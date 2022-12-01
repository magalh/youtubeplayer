<?php
if( !defined('CMS_VERSION') ) exit;
$this->CreatePermission(YouTubePlayer::PERM_ADVANCED,'Manage '.$this->Lang("friendlyname"));

// Typical Database Initialization
$db = $this->GetDb();
$dict = NewDataDictionary($db);
		
// mysql-specific, but ignored by other database
$taboptarray = array("mysql" => "TYPE=MyISAM");
		
// Creates the category table
$flds = "
	description C(255),
	id I KEY,
	name C(64),
	alias C(64),
	item_order I,
	active L,
	isdefault L,
    date_modified ".CMS_ADODB_DT.",
	date_created ".CMS_ADODB_DT."
	";

$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_youtubeplayer_category", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX."module_youtubeplayer_category_seq");


// Creates the videos table
$flds = "
	videoid C(255),
	description C(255),
	parent I,
	id I KEY,
	name C(64),
	alias C(64),
	item_order I,
	active L,
	isdefault L,
    date_modified ".CMS_ADODB_DT.",
	date_created ".CMS_ADODB_DT."
	";

$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_youtubeplayer_videos", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX."module_youtubeplayer_videos_seq");

// Creates the queries table
$flds = "
    id I,
	name C(64),
	what C(32),
	whereclause C(255),
	wherevalues C(255),
	queryorder C(32)
	";

$sqlarray = $dict->CreateTableSQL(CMS_DB_PREFIX."module_youtubeplayer_saved_queries", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(CMS_DB_PREFIX."module_youtubeplayer_saved_queries_seq");

// INSERTING DEFAULT TEMPLATES
$list_default = $this::create_template_type('List', $this);
$fn = __DIR__.'/templates/orig_youtube_list.tpl';
if ( is_file($fn) ) $this::create_template_of_type($list_default, $this->Lang("friendlyname").' Default List', file_get_contents($fn), true);

$final_default = $this::create_template_type('Final', $this);
$fn = __DIR__.'/templates/orig_youtube_final.tpl';
if ( is_file($fn) ) $this::create_template_of_type($final_default, $this->Lang("friendlyname").' Default Final', file_get_contents($fn), true);

$this->SetPreference("listtemplate_category",'List');
$this->SetPreference("listtemplate_videos",'List');
$this->SetPreference("finaltemplate",'Final');


// permissions
$this->CreatePermission(YouTubePlayer::PERM_NORMAL, "YouTubePlayer: Normal user");
$this->CreatePermission(YouTubePlayer::PERM_ADVANCED, "YouTubePlayer: Advanced");
$this->CreatePermission(YouTubePlayer::PERM_CAT, "YouTubePlayer: Manage category");
$this->CreatePermission(YouTubePlayer::PERM_VID, "YouTubePlayer: Manage videos");

// activating default preferences
$defprefs = array("tabdisplay_category","searchmodule_index_category","newitemsfirst_category","tabdisplay_videos","searchmodule_index_videos","newitemsfirst_videos","restrict_permissions","orderbyname","display_filter","display_instantsearch","display_instantsort","showthumbnails");
foreach($defprefs as $onepref)	$this->SetPreference($onepref,true);


?>
