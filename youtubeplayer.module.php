<?php
#-------------------------------------------------------------------------
# Module: youtubeplayer
# Version: 1.2, 
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2008 by Ted Kulp (wishy@cmsmadesimple.org)
# This project"s homepage is: http://www.cmsmadesimple.org
#
# This module was created with CTLModuleMaker 1.8.8
# CTLModuleMaker was created by Pierre-Luc Germain and is released under GNU
# http://dev.cmsmadesimple.org/projects/ctlmodulemaker
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

class YouTubePlayer extends CMSModule
{

	const PERM_NORMAL = 'youtubeplayer_normaluser';
	const PERM_ADVANCED = 'youtubeplayer_advanced';
	const PERM_CAT = 'youtubeplayer_manage_category';
	const PERM_VID = 'youtubeplayer_manage_videos';

	var $currenttree = false;
	var $currentpageindex = 1;
	var $plcurrent = array();

	function GetFriendlyName(){return $this->Lang("friendlyname");}
	function GetVersion(){ return "2.0"; }
	function GetHelp(){return $this->Lang("help");}
	public function GetAuthor() { return 'Magal Hezi'; }
	public function GetAuthorEmail() { return 'h_magal@hotmail.com'; }
	public function IsPluginModule() { return TRUE; }
	public function HasAdmin() { return TRUE; }
	public function GetAdminSection() { return 'content'; }
	public function GetAdminDescription() { return $this->Lang('admindescription'); }
	public function VisibleToAdminUser(){return ($this->CheckPermission(self::PERM_NORMAL) || $this->CheckPermission(self::PERM_ADVANCED));}
	
	/*---------------------------------------------------------
	   Module Constructor 
	---------------------------------------------------------*/

	public function __construct() {
		parent::__construct();
  
		$smarty = \CmsApp::get_instance()->GetSmarty();
		if( !$smarty ) return;

		$smarty->register_function("youtubeplayer_breadcrumbs", array($this,"function_modbreadcrumbs"));
		$smarty->register_function("youtubeplayer_get_levelitem", array($this,"function_get_levelitem"));
  
	 }

	public function InitializeFrontend() {
		$this->RegisterModulePlugin();
		$this->SetParameterType("what",CLEAN_STRING);
		$this->SetParameterType("alias",CLEAN_STRING);
		$this->SetParameterType("showdefault",CLEAN_INT);
		$this->SetParameterType("parent",CLEAN_STRING);
		$this->SetParameterType("limit",CLEAN_INT);
		$this->SetParameterType("nbperpage",CLEAN_STRING);
		$this->SetParameterType("orderby",CLEAN_STRING);
		$this->SetParameterType("detailpage",CLEAN_STRING);
		$this->SetParameterType("random",CLEAN_INT);
		$this->SetParameterType("listtemplate",CLEAN_STRING);
		$this->SetParameterType("finaltemplate",CLEAN_STRING);
		$this->SetParameterType("forcelist",CLEAN_STRING);
		$this->SetParameterType("inline",CLEAN_STRING);
		$this->SetParameterType("searchmode",CLEAN_STRING);
		$this->SetParameterType("query",CLEAN_STRING);
		$this->SetParameterType("toaction",CLEAN_STRING);
		$this->SetParameterType("pageindex",CLEAN_INT);
		// for the search form (trick from ikulis) :
			$this->SetParameterType(CLEAN_REGEXP."/date_.*/",CLEAN_STRING);
			$this->SetParameterType(CLEAN_REGEXP."/field_.*/",CLEAN_STRING);
			$this->SetParameterType(CLEAN_REGEXP."/compare_.*/",CLEAN_STRING);
			$this->SetParameterType("submitsearch",CLEAN_STRING);
			$this->SetParameterType("searchfield",CLEAN_STRING);
			
			// for the frontend add action
			$this->SetParameterType(CLEAN_REGEXP."/feadd.*/",CLEAN_STRING);
			$this->SetParameterType(CLEAN_REGEXP."/fefile.*/",CLEAN_STRING);
			$this->SetParameterType("captcha_input", CLEAN_STRING);
	}

	public function InitializeAdmin() {
		$this->SetParameters();
	}

	function SetParameters()
	{
		$defact = array("action"=>"default");
		$this->RegisterRoute("/[yY]outubeplayer\/([Qq]uery)\/(?P<query>[0-9]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/([Qq]uery)\/(?P<query>[0-9]+)\/(?P<pageindex>[0-9]+)\/(?P<nbperpage>[0-9]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/([Dd]etail)\/(?P<alias>[^\/]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/(?P<what>[^\/]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/(?P<what>[^\/]+)\/(?P<parent>[^\/]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/(?P<what>[^\/]+)\/(?P<pageindex>[0-9]+)\/(?P<nbperpage>[0-9]+)\/(?P<returnid>[0-9]+)$/", $defact);
		$this->RegisterRoute("/[yY]outubeplayer\/(?P<what>[^\/]+)\/(?P<parent>[^\/]+)\/(?P<pageindex>[0-9]+)\/(?P<nbperpage>[0-9]+)\/(?P<returnid>[0-9]+)$/", $defact);
	
		$this->RestrictUnknownParams();
		
		$this->CreateParameter("action", "default", $this->Lang("phelp_action"));
		$this->CreateParameter("what", "", $this->Lang("phelp_what"));
		$this->CreateParameter("alias", "", $this->Lang("phelp_alias"));
		$this->CreateParameter("showdefault", false, $this->Lang("phelp_showdefault"));
		$this->CreateParameter("parent", "", $this->Lang("phelp_parent"));
		$this->CreateParameter("limit", 0, $this->Lang("phelp_limit"));
		$this->CreateParameter("nbperpage", 0, $this->Lang("phelp_nbperpage"));
		$this->CreateParameter("orderby", 0, $this->Lang("phelp_orderby"));
		$this->CreateParameter("detailpage", "", $this->Lang("phelp_detailpage"));
		$this->CreateParameter("random", 0, $this->Lang("phelp_random"));
		$this->CreateParameter("listtemplate", "", $this->lang("phelp_listtemplate"));
		$this->CreateParameter("finaltemplate", "", $this->lang("phelp_finaltemplate"));
		$this->CreateParameter("forcelist", "0", $this->lang("phelp_forcelist"));
		$this->CreateParameter("inline", 0, $this->lang("phelp_inline"));
		$this->CreateParameter("searchmode", "advanced", $this->lang("phelp_searchmode"));
		$this->CreateParameter("query", 0, $this->lang("phelp_query"));
		$this->CreateParameter("toaction", "", $this->Lang("phelp_toaction"));

	}

	public function UninstallPreMessage() { return $this->Lang('really_uninstall'); }

    function SearchResult($returnid, $itemid, $level = "")
    {
		$result = array();
		$wantedparam = false;
		$newparams = array();
		if($level == "videos"){
			// we seek an element of the last level, and will display the detail view
			$wantedparam = "alias";
		}else{
			if($newparams["what"] = $this->get_nextlevel($level)){
			// we seek an element of another level, and will display the list view of its children
				$wantedparam = "parent";
			}
		}
		if ($wantedparam){
			$tablename = cms_db_prefix()."module_youtubeplayer_".$level;
			$db =& $this->GetDb();
			$query = "SELECT name, alias FROM $tablename WHERE id = ?";
			$dbresult = $db->Execute( $query, array( $itemid ) );
			if ($dbresult){
				$row = $dbresult->FetchRow();
				$newparams[$wantedparam] = $row["alias"];

				//0 position is the prefix displayed in the list results.
				$result[0] = $this->GetFriendlyName();

				//1 position is the title
				$result[1] = $row["name"];
		
				//2 position is the URL to the title.
				$result[2] = $this->CreateLink($id, "default", $returnid, "", $newparams, "", true, false, "", false, $this->BuildPrettyUrls($newparams, $returnid));
			}
		}

		return $result;
	}
	
	function SearchReindex(&$module)
    {
		$db =& $this->GetDb();
		if($this->GetPreference("searchmodule_index_category",false)){
			$itemlist = $this->get_level_category();
			foreach($itemlist as $item){
				$text = "$item->name";
				$module->AddWords($this->GetName(), $item->id, "category", $text, NULL);
			}
		}
		if($this->GetPreference("searchmodule_index_videos",false)){
			$itemlist = $this->get_level_videos();
			foreach($itemlist as $item){
				$text = "$item->name";
				$module->AddWords($this->GetName(), $item->id, "videos", $text, NULL);
			}
		}
		
    }

	public static function create_template_type($type_name, $mod) {
		if ( !is_object($mod) ) return false;
		try {  
			$module_name = $mod->GetName();
			$tpl_type = new CmsLayoutTemplateType();
			$tpl_type->set_originator($module_name);
			$tpl_type->set_name($type_name);
			$tpl_type->set_dflt_flag(TRUE);
			$tpl_type->set_lang_callback($module_name.'::page_type_lang_callback');
			$tpl_type->set_content_callback($module_name.'::reset_page_type_defaults');
			$tpl_type->reset_content_to_factory();
			$tpl_type->save();
		} catch( \CmsException $e ) {
			self::log_exception($e);
			audit('', $module_name, 'Install error: '.$e->GetMessage());
		}
	
		$tpl_type = \CmsLayoutTemplateType::load($module_name.'::'.$type_name);
		return $tpl_type;
	}

	public static function create_template_of_type( $type_ob, $name, $contents, $dflt = false ) 
    {
        $ob = new CmsLayoutTemplate();
        $ob->set_type( $type_ob );
        $ob->set_content( $contents );
        $ob->set_owner( get_userid() );
        $ob->set_type_dflt( $dflt );
        $new_name = $ob->generate_unique_name( $name );
        $ob->set_name( $new_name );
        $ob->save();
    }

	static public function log_exception(\Exception $e)
    {
        $out = '-- EXCEPTION DUMP --'."\n";
        $out .= "TYPE: ".get_class($e)."\n";
        $out .= "MESSAGE: ".$e->getMessage()."\n";
        $out .= "FILE: ".$e->getFile().':'.$e->GetLine()."\n";
        $out .= "TREACE:\n";
        $out .= $e->getTraceAsString();
        debug_to_log($out,'-- '.__METHOD__.' --');
    }

	public static function reset_page_type_defaults(CmsLayoutTemplateType $type)
    {
        $mod = cms_utils::get_module('YouTubePlayer');
        if( $type->get_originator() != $mod->GetName() ) throw new CmsLogicException('Cannot reset contents for this template type');

        $fn = null;
        switch( $type->get_name() ) {
        case 'YouTube List':
            $fn = 'orig_youtube_list.tpl';
            break;
        case 'YouTube Final':
            $fn = 'orig_youtube_final.tpl';
            break;
        }

        if( !$fn ) return;
        $fn = __DIR__.'/templates/'.$fn;
        if( file_exists($fn) ) return @file_get_contents($fn);
    }

}
?>
