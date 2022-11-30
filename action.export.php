<?php
if(!isset($gCms))	exit;

if(isset($params['doexport'])){
	
	$db =& $this->GetDb();
	
	$output = '';
	foreach($params['selectedlevels'] as $level){
		$output .= '
<table name="'.$level.'">
	';
		
		$query = "SELECT * FROM ".cms_db_prefix()."module_".$this->GetName()."_".$level;
		$dbresult = $db->Execute($query);
		while ( $dbresult && $row = $dbresult->FetchRow() ) {
			$output .= "<item>
		";
			foreach($row as $field=>$value){
				$output .= '<'.$field.'><![CDATA['.stripslashes($value).']]></'.$field.'>
		';
			}
			$output .= "
	</item>
	";
		}
		$output .= "
</table>

";
	}
	
	if(isset($params['export_templates']) && $params['export_templates']){
		foreach($this->ListTemplates() as $tplname){
			$output .= '
<template name="'.$tplname.'"><![CDATA['.$this->GetTemplate($tplname).']]></template>
';
		}
	}
	
	if($output == ''){
		$this->Redirect($id, 'export', $returnid);
	}else{
		$output = "<root>".$output."</root>";
		
		$handlers = ob_list_handlers(); 
		for ($cnt = 0; $cnt < sizeof($handlers); $cnt++) { ob_end_clean(); }		

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private',false);
		header('Content-Description: File Transfer');
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="'.$this->GetName().'.xml"' );
		header('Content-Length: ' . strlen($output));
	
		print($output);
	
		@flush(); @ob_flush(); @ob_flush();
	}	

}else{
	
	echo $this->CreateFormStart($id, "export", $returnid);
	echo '<h3>'.$this->Lang('export_title').'</h3>';
	echo '<fieldset><legend><b>'.$this->Lang('exportwhichlevels').'</b></legend>';
	$choices = array();
	foreach($this->get_levelarray() as $level)	$choices[$level] = $level;
	echo $this->DoCheckboxes($id, 'selectedlevels', $choices, $choices).'</fieldset><br/>';
	echo '<p>'.$this->CreateInputCheckbox($id,"export_templates",1,0).' '.$this->Lang('export_templates').'</p>';
	echo '<p>'.$this->CreateInputSubmit($id, "doexport", lang("submit")).'</p>';
	echo $this->CreateFormEnd();	
	
}
