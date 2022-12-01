<?php
if(!isset($gCms)) exit;

echo "<h2>".$this->Lang("createquery")."</h2><br/>";

if(!isset($params["what"])){
	
	$levelarray = $this->get_levelarray();
	$leveloptions = array();
	foreach($levelarray as $one)	$leveloptions[$one] = $one;
	echo "<p>".$this->Lang("prompt_query");
	echo $this->CreateFormStart($id, "adminquery", $returnid);
	echo $this->CreateInputDropdown($id,"what",$leveloptions);
	echo $this->CreateInputSubmit($id, "submit", lang("submit"))."</p>";
	echo $this->CreateFormEnd();
	
}elseif(isset($params["submitquery"])){
	$db =& $this->GetDb();
	$what = $params["what"];
	$multiplelistfields = array();


	// we parse the keywords
	$where = $this->getWhereFromParams($params);
	
	list($whereclause, $wherevalues) = $this->buildWhere($where, $what);
	$customorder = (isset($params["order"]) && $params["order"] != "")?$params["order"]." ".$params["order_type"]:"";
	
	$tmpid = $db->GenID(cms_db_prefix()."module_youtubeplayersaved_queries_seq");
	$name = isset($params["queryname"])?addslashes($params["queryname"]):"";
	
	$query = "INSERT INTO ".cms_db_prefix()."module_youtubeplayer_saved_queries SET id=?, name=?, what=?, whereclause=?, wherevalues=?, queryorder=?";
	$db->Execute($query, array($tmpid, $name, $what, $whereclause, serialize($wherevalues), $customorder));
	$this->Redirect($id, "defaultadmin", $returnid, array("active_tab"=>"queries","module_message"=>$this->Lang("message_modified")));

}else{
	$what = $params["what"];
	echo $this->CreateFormStart($id, "adminquery", $returnid);
	echo $this->CreateInputHidden($id, "what", $what);
	echo "<p>".$this->Lang("queryname").": ".$this->CreateInputText($id,"queryname","",30)."</p>";
	$this->createFieldForm($what, $id, false);
	echo "<p>".$this->CreateInputSubmit($id, "submitquery", lang("submit"))."</p>";
	echo $this->CreateFormEnd();
			
}
?>