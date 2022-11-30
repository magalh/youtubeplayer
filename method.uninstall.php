<?php
if(!isset($gCms)) exit;

// Typical Database Initialization
$db =& $this->GetDb();


$dict = NewDataDictionary($db);



	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_youtubeplayer_category");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(cms_db_prefix()."module_youtubeplayer_category_seq");
	

	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_youtubeplayer_videos");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(cms_db_prefix()."module_youtubeplayer_videos_seq");
	

	//$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_youtubeplayer_templates");
	//$dict->ExecuteSQLArray($sqlarray);
	$this->DeleteTemplate("",$this->GetName());
	
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_youtubeplayer_saved_queries");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence(cms_db_prefix()."module_youtubeplayer_saved_queries_seq");

// permissions
	$this->RemovePermission("youtubeplayer_manage_category");
	$this->RemovePermission("youtubeplayer_manage_videos");
	$this->RemovePermission("youtubeplayer_advanced");
	$this->RemovePreference();
	
// events
	$this->RemoveEvent("youtubeplayer_added");
	$this->RemoveEvent("youtubeplayer_modified");
	$this->RemoveEvent("youtubeplayer_deleted");

// put mention into the admin log
	$this->Audit( 0, $this->Lang("friendlyname"), $this->Lang("uninstalled"));

?>
