<?php
if(!isset($gCms))	exit;

echo '<h3>'.$this->Lang('import_title').'</h3>';

if(isset($_FILES) && isset($_FILES[$id.'xmlfile']) && isset($_FILES[$id.'xmlfile']['name']) && $_FILES[$id.'xmlfile']['name'] != '') {
	
	$xml = file_get_contents( $_FILES[$id.'xmlfile']['tmp_name'] );
	$parser = xml_parser_create('');
	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');
	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

	$xmldata = array();
	xml_parse_into_struct($parser, trim($xml), $xmldata);
	xml_parser_free($parser);
	if (!$xmldata)	$this->Redirect($id, 'import', $returnid, array('module_message'=>$this->Lang('error_invalidxml')) );

	$tables = array();
	$templates = array();
	$entries = 0;
	$currenttable = false;
	$currentitem = false;

	$existing_tables = $this->get_levelarray();
	
	
	foreach ($xmldata as $data) {
		switch($data['type']) {
			case 'open':
				if($data['level'] == 2 && $data['tag'] == 'table' && isset($data['attributes']) && isset($data['attributes']['name']) ){
					$tablename = $data['attributes']['name'];
					if(in_array($tablename, $existing_tables)){
						$tables[$tablename] = array();
						$currenttable = $tablename;
					}else{
						$currenttable = false;
					}
				}elseif($data['level'] == 3 && $data['tag'] == 'item'){
					$currentitem = array();
				}
				break;
			case 'close':
				if($currenttable && $currentitem != false && $data['level'] == 3 && $data['tag'] == 'item'){
					if(count($currentitem) > 0){
						$tables[$currenttable][] = $currentitem;
						$entries++;
					}
				}
				break;
			case 'complete':
				if($data['level'] == 4){
					$currentitem[$data['tag']] = $data['value'];
				}elseif($data['level'] == 1 && $data['tag'] == 'template' && isset($data['attributes']) && isset($data['attributes']['name']) ){
					$templates[$data['attributes']['name']] = $data['value'];
				}
				break;
		}
	}

	if(count($tables) == 0)	$this->Redirect($id, 'import', $returnid, array('module_message'=>$this->Lang('error_invalidxml')) );
	
	$_SESSION['ctlmm_moduleimport'] = array();
	$_SESSION['ctlmm_moduleimport']['tables'] = $tables;
	$_SESSION['ctlmm_moduleimport']['templates'] = $templates;
	
	echo $this->CreateFormStart($id, "import", $returnid);
	echo '<p>'.$this->Lang('import_entries',$entries).'</p>';
	echo '<fieldset><legend><b>'.$this->Lang('importwhichlevels').'</b></legend>';
	$choices = array();
	foreach($tables as $key=>$value){
		if(count($value) >0)	$choices[$key.' ('.count($value).')'] = $key;
	}
	echo $this->DoCheckboxes($id, 'selectedlevels', $choices, $choices).'</fieldset><br/>';
	if(count($templates) > 0)	echo '<p>'.$this->CreateInputCheckbox($id,"import_templates",1,0).' '.$this->Lang('import_templates').'</p>';
	echo '<p>'.$this->CreateInputCheckbox($id,"import_delete",1,0).' '.$this->Lang('import_delete').'</p>';
	echo '<p>'.$this->CreateInputSubmit($id, "doimport", lang("submit")).'</p>';
	echo $this->CreateFormEnd();

}elseif(isset($params['doimport']) && isset($_SESSION['ctlmm_moduleimport']) && isset($params['selectedlevels'])){

	$db =& $this->GetDb();
	$tableprefix = cms_db_prefix()."module_".$this->GetName()."_";
	$errors = '';
	$added = 0;
	
	if(isset($params['import_templates']) && $params['import_templates'] && isset($_SESSION['ctlmm_moduleimport']['templates']) ){
		foreach($_SESSION['ctlmm_moduleimport']['templates'] as $tplname=>$template){
			$this->SetTemplate($tplname, $template);
		}
	}
	
	$delete = (isset($params['import_delete']) && $params['import_delete'] );
	
	foreach($params['selectedlevels'] as $tablename){
		
		$existing_fields = array();
		$columns = $db->MetaColumnNames($tableprefix.$tablename);
		foreach($columns as $col)	$existing_fields[] = strtolower($col);
		
		$proceed = true;
		$highestid = 0;
		if(isset($_SESSION['ctlmm_moduleimport']['tables'][$tablename])){
			if($delete)	$db->Execute("TRUNCATE TABLE ".$tableprefix.$tablename);
			$table = $_SESSION['ctlmm_moduleimport']['tables'][$tablename];
			$i = 0;
			while($proceed && $i < count($table)){
				$values = array();
				$query = '';
				foreach($table[$i] as $key=>$value){
					if( in_array($key,$existing_fields) ){
						$query .= ($query==''?'':', ')."`".$key."`=?";
						$values[] = ($value===NULL?'':$value);
					}
				}
				$query = "INSERT INTO ".$tableprefix.$tablename." SET ".$query;
				if($db->Execute($query,$values)){
					if(isset($table[$i]['id']) && $table[$i]['id'] > $highestid)	$highestid = $table[$i]['id'];
					$added++;
				}else{
					$proceed = false;
					$errors .= ($errors==''?'':', ').$tablename;
				}	
				$i++;
			}
		}
		$currentid = 0;
		while($currentid < $highestid){
			$currentid = $db->GenID($tableprefix.$tablename.'_seq');
		}
	}
	
	unset($_SESSION['ctlmm_moduleimport']);
	
	$message = $this->Lang('import_done',$added);
	if($errors != '') $message .= '<br/>'.$this->Lang('couldnotimport').$errors;
	$this->Redirect($id, 'defaultadmin', $returnid, array('module_message'=>$message) );

}else{

	echo '<p>'.$this->Lang('import_fileprompt').'</p>';
	echo $this->CreateFormStart($id, 'import', $returnid, 'post', 'multipart/form-data');
	echo $this->CreateFileUploadInput($id,'xmlfile').' '.$this->CreateInputSubmit($id,'submit',lang('send'));
	echo $this->CreateFormEnd();	
	
}

?>
