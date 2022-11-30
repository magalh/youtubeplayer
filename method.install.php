<?php
if( !defined('CMS_VERSION') ) exit;

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

$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_youtubeplayer_category", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix()."module_youtubeplayer_category_seq");


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

$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_youtubeplayer_videos", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix()."module_youtubeplayer_videos_seq");



// Creates the queries table
$flds = "
    id I,
	name C(64),
	what C(32),
	whereclause C(255),
	wherevalues C(255),
	queryorder C(32)
	";

$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_youtubeplayer_saved_queries", $flds, $taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$db->CreateSequence(cms_db_prefix()."module_youtubeplayer_saved_queries_seq");


// INSERTING DEFAULT TEMPLATES
	$template = '<h2>{$leveltitle}</h2>
<ul>
{foreach from=$itemlist item="item"}
<li {if $item->is_selected}class="active"{/if}>{$item->detaillink}
<br />
<a href="{$item->detailurl}">
<img src="http://img.youtube.com/vi/{$item->videoid}/1.jpg" width="130" height="97" border="0" alt="{$item->name}" title="{$item->name}"> 
</a>
<br /><br />
</li>
{/foreach}
</ul>
{if $page_pagenumbers}
<div id="pagemenu" style="text-align: center;">
{$page_previous}&nbsp; {$page_showing}/{$page_totalitems} &nbsp;{$page_next}<br/>
{$page_pagenumbers}
</div>
{/if}
';
$this->SetTemplate("list_default",$template,$this->GetName());
    $this->SetPreference("listtemplate_category","list_default");
    $this->SetPreference("listtemplate_videos","list_default");

$template = '<h3>{$item->name}</h3>
<p>{$labels->videoid}: {$item->videoid}</p>
<p>{$labels->description}: {$item->description}</p>
<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/{$item->videoid}?rel=0&fs=1&loop=0"></param><param name="wmode" value="transparent"></param><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/{$item->videoid}?rel=0&fs=1&loop=0" allowfullscreen="true" type="application/x-shockwave-flash" wmode="transparent" width="425" height="344"></embed></object>
';

$this->SetTemplate("final_default",$template,$this->GetName());
$this->SetPreference("finaltemplate","final_default");


// permissions
$this->CreatePermission("youtubeplayer_normaluser", "youtubeplayer: Normal user");
$this->CreatePermission("youtubeplayer_advanced", "youtubeplayer: Advanced");
	$this->CreatePermission("youtubeplayer_manage_category", "youtubeplayer: Manage category");
	$this->CreatePermission("youtubeplayer_manage_videos", "youtubeplayer: Manage videos");
// activating default preferences
	$defprefs = array("tabdisplay_category","searchmodule_index_category","newitemsfirst_category","tabdisplay_videos","searchmodule_index_videos","newitemsfirst_videos","restrict_permissions","orderbyname","display_filter","display_instantsearch","display_instantsort","showthumbnails");
	foreach($defprefs as $onepref)	$this->SetPreference($onepref,true);

// events
	$this->CreateEvent("youtubeplayer_added");
	$this->CreateEvent("youtubeplayer_modified");
	$this->CreateEvent("youtubeplayer_deleted");
	
// prepare information for an eventual upgrade
	$this->SetPreference("makerversion","1.8.8");

// put mention into the admin log
	$this->Audit( 0, $this->Lang("friendlyname"), $this->Lang("installed",$this->GetVersion()));

?>
