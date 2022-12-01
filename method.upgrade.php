<?php
if( !defined('CMS_VERSION') ) exit;

$db =& $this->GetDb();
$dict = NewDataDictionary($db);
$tabopt = array("mysql" => "TYPE=MyISAM");

// PART 1: This does the upgrade for the CTLModuleMaker part
// Unfortunately, this is only backward compatible to CTLModuleMaker 1.6.3. I couldn't go any further behind because at that time the maker version wasn't saved anywhere

$oldmaker = $this->GetPreference("makerversion", "veryold");

switch($oldmaker){
	// BEGIN SWITCH($oldmaker)

	case "veryold":
		// the module was created with version 1.6.3 or prior.
		$this->CreateEvent("youtubeplayer_added");
		$this->CreateEvent("youtubeplayer_modified");
		$this->CreateEvent("youtubeplayer_deleted");
		break;
	case "1.6.4":
	case "1.7":
	case "1.7.1":
	case "1.7.2":
	case "1.7.3":
	case "1.7.4":
		;
	case "1.8.1":
	case "1.8.2":
	case "1.8.2.2":
	case "1.8.3":
	case "1.8.3.1":
		// Creates the queries table
		$flds = "
			id I,
			name C(64),
			what C(32),
			whereclause C(255),
			wherevalues C(255),
			queryorder C(32)
			";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_youtubeplayer_saved_queries", $flds, $tabopt);
		$dict->ExecuteSQLArray($sqlarray);
		$db->CreateSequence(cms_db_prefix()."module_youtubeplayer_saved_queries_seq");
		$this->CreatePermission("youtubeplayer_advanced", "youtubeplayer: Advanced");
	case "1.8.4":
	case "1.8.4.1":
	case "1.8.5":
	case "1.8.5.1":
		$this->CreatePermission("youtubeplayer_normaluser", "youtubeplayer: Normal user");
		// activating default preferences
		$defprefs = array("tabdisplay_category","searchmodule_index_category","newitemsfirst_category","tabdisplay_videos","searchmodule_index_videos","newitemsfirst_videos","restrict_permissions","display_filter","display_instantsearch");
		foreach($defprefs as $onepref)	$this->SetPreference($onepref,true);
	case "1.8.6":
	case "1.8.6.1":
	case "1.8.7":
	case "1.8.7.1":
		if($db->dbtype == "mysql" || $db->dbtype == "mysqli"){
			// msyql
			$queries = array();
			$queries[] = "ALTER TABLE ".cms_db_prefix()."module_youtubeplayer_category ADD COLUMN date_created DATETIME";
			$queries[] = "UPDATE ".cms_db_prefix()."module_youtubeplayer_category SET date_created=date_modified";
			$queries[] = "ALTER TABLE ".cms_db_prefix()."module_youtubeplayer_videos ADD COLUMN date_created DATETIME";
			$queries[] = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET date_created=date_modified";
			foreach($queries as $query)		mysql_query($query);

		}else{
			// non-mysql
			$dict->AddColumnSQL(cms_db_prefix()."module_youtubeplayer_category", "date_created ".CMS_ADODB_DT);
			$dict->AddColumnSQL(cms_db_prefix()."module_youtubeplayer_videos", "date_created ".CMS_ADODB_DT);
			
		}
	// END SWITCH($oldmaker)
}

$this->SetPreference("makerversion", "1.8.8");
?>