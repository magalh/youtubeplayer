<?php
if (!isset($gCms)) exit;
$admintheme = $gCms->variables["admintheme"];
$active_tab = isset($params["active_tab"])?$params["active_tab"]:"category";

$has_advanced_perm = $this->CheckPermission("youtubeplayer_advanced");

$filter = $this->GetPreference("display_filter",false);
$instantsearch = $this->GetPreference("display_instantsearch",false);
$instantsort = $this->GetPreference("display_instantsort",false);

if($instantsort || $instantsearch){
	echo '
	<script type="text/javascript">
';
	if($instantsearch)	echo $this->getFileContent("instantsearch.js");
	if($instantsort)	echo $this->getFileContent("instantsort.js");
	echo "
	</script>
";
}

echo $this->StartTabHeaders();
	if( $has_advanced_perm || $this->GetPreference("tabdisplay_category",false) || $this->CheckPermission("youtubeplayer_manage_category") ) {
		echo $this->SetTabHeader("category", $this->Lang("category_plural"), "category" == $active_tab ? true : false);
	}
	if( $has_advanced_perm || $this->GetPreference("tabdisplay_videos",false) || $this->CheckPermission("youtubeplayer_manage_videos") ) {
		echo $this->SetTabHeader("videos", $this->Lang("videos_plural"), "videos" == $active_tab ? true : false);
	}
	if( $has_advanced_perm || $this->CheckPermission("Modify Templates") || $this->GetPreference("tabdisplay_templates",false) ) {
		echo $this->SetTabHeader("templates", $this->Lang("templates"), "templates" == $active_tab ? true : false);		
	}
	if( $has_advanced_perm || $this->GetPreference("tabdisplay_queries",false) ) {
		echo $this->SetTabHeader("queries", $this->Lang("queries"), "queries" == $active_tab ? true : false);		
	}
	if( $has_advanced_perm ) {
		echo $this->SetTabHeader("preferences", $this->Lang("preferences"), "preferences" == $active_tab ? true : false);		
	}
echo $this->EndTabHeaders();



echo $this->StartTabContent();


if( $has_advanced_perm || $this->GetPreference("tabdisplay_category",false) || $this->CheckPermission("youtubeplayer_manage_category") ) {
	echo $this->StartTab("category");

		$whereclause = array();
		$filteroutput = false;

		$this->smarty->assign("filter", $filteroutput);
		$this->smarty->assign("instantsearch", $instantsearch?$this->Lang("searchthistable")." ".$this->CreateInputText($id, "searchtable_category", "", 10, 64, ' onkeyup="ctlmm_search(this.value,\'category_table\');"'):false);
			
		$this->smarty->assign("addnew", $this->CreateLink($id, "editA", $returnid, $admintheme->DisplayImage("icons/system/newobject.gif", "","","","systemicon")." ".$this->Lang("add_category")));
		$reorder_btn = false;if($has_advanced_perm || !$this->GetPreference("restrict_permissions",false) || $this->CheckPermission("youtubeplayer_manage_category"))	$reorder_btn = $this->CreateLink($id, "reorder", $returnid, $admintheme->DisplayImage("icons/system/reorder.gif", "","","","systemicon")." ".$this->Lang("reorder"));
			
		$this->smarty->assign("reorder", $reorder_btn);
		
			$itemlist = $this->get_level_category(isset($whereclause)?$whereclause:array(),true, $id, $returnid);
			$this->smarty->assign("tableid", "category_table");
			$this->smarty->assign("itemlist", $itemlist);
			$adminshow = array(
				array($this->Lang("name"),"editlink",false),
				array($this->Lang("active"),"toggleactive",true),
				array($this->Lang("reorder"),"movelinks",true),
				array($this->Lang("Actions"),"deletelink",true)		
				);
			if($instantsort && count($itemlist)>1){
				$i = 0;
				while($i<count($adminshow)){
					if(!$adminshow[$i][2])	$adminshow[$i][0] = '<div style="float:left;"><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'category_table\','.$i.');"><img src="themes/default/images/icons/system/sort_up.gif" alt="^"/></a><br/><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'category_table\','.$i.',true);"><img src="themes/default/images/icons/system/sort_down.gif" alt="v"/></a></div><div style="line-height: 24px;"> &nbsp;'.$adminshow[$i][0]."</div>";
					$i++;
				}
			}
			$this->smarty->assign("adminshow", $adminshow);
			echo $this->ProcessTemplate("adminpanel.tpl");

	echo $this->EndTab();

}

if( $has_advanced_perm || $this->GetPreference("tabdisplay_videos",false) || $this->CheckPermission("youtubeplayer_manage_videos") ) {
	echo $this->StartTab("videos");

	if($this->countsomething("category") > 0){

		$whereclause = array();
		$filteroutput = false;

			if($filter){
				if(!isset($params["videos_showonly"]) || $params["videos_showonly"] == ""){
					if($this->GetPreference("use_hierarchy",false)){
						$filteroptions = $this->get_admin_hierarchyoptions("videos",true);
					}else{
						$filteroptions = $this->get_options("category");
					}
					$filteroutput = $this->CreateFormStart($id, "defaultadmin", $returnid);
					$filteroutput .= $this->Lang("filterby_category")." : ";
					$filteroutput .= $this->CreateInputDropdown($id, "videos_showonly", $filteroptions, -1);
					$filteroutput .= $this->CreateInputHidden($id, "active_tab", "videos");
					$filteroutput .= " ".$this->CreateInputSubmit($id, "submit", lang("submit"));
					$filteroutput .= $this->CreateFormEnd();
				}else{
					$filteroutput = $this->CreateLink($id, "defaultadmin", $returnid, $this->Lang("showall"), array("active_tab" => "videos"));
					$whereclause = array("parent_id"=>$params["videos_showonly"]);
				}
			}
		$this->smarty->assign("filter", $filteroutput);
		$this->smarty->assign("instantsearch", $instantsearch?$this->Lang("searchthistable")." ".$this->CreateInputText($id, "searchtable_videos", "", 10, 64, ' onkeyup="ctlmm_search(this.value,\'videos_table\');"'):false);
			
		$this->smarty->assign("addnew", $this->CreateLink($id, "editB", $returnid, $admintheme->DisplayImage("icons/system/newobject.gif", "","","","systemicon")." ".$this->Lang("add_videos")));
		$reorder_btn = false;if($has_advanced_perm || !$this->GetPreference("restrict_permissions",false) || $this->CheckPermission("youtubeplayer_manage_videos"))	$reorder_btn = $this->CreateLink($id, "reorder", $returnid, $admintheme->DisplayImage("icons/system/reorder.gif", "","","","systemicon")." ".$this->Lang("reorder"));
			
		$this->smarty->assign("reorder", $reorder_btn);
		
			$itemlist = $this->get_level_videos(isset($whereclause)?$whereclause:array(),true, $id, $returnid);
			$this->smarty->assign("tableid", "videos_table");
			$this->smarty->assign("itemlist", $itemlist);
			$adminshow = array(
				array($this->Lang("name"),"editlink",false),
				array($this->Lang("active"),"toggleactive",true),
				array($this->Lang("isdefault"),"toggledefault",true),
				array($this->Lang("reorder"),"movelinks",true),
				array($this->Lang("Actions"),"deletelink",true)		
				);
			if($instantsort && count($itemlist)>1){
				$i = 0;
				while($i<count($adminshow)){
					if(!$adminshow[$i][2])	$adminshow[$i][0] = '<div style="float:left;"><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'videos_table\','.$i.');"><img src="themes/default/images/icons/system/sort_up.gif" alt="^"/></a><br/><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'videos_table\','.$i.',true);"><img src="themes/default/images/icons/system/sort_down.gif" alt="v"/></a></div><div style="line-height: 24px;"> &nbsp;'.$adminshow[$i][0]."</div>";
					$i++;
				}
			}
			$this->smarty->assign("adminshow", $adminshow);
			echo $this->ProcessTemplate("adminpanel.tpl");

	}else{
		echo "<p>".$this->Lang("error_noparent")."</p>";
	}

	echo $this->EndTab();

}
	$this->smarty->assign("filter", false);
	$this->smarty->assign("reorder", false);
	$this->smarty->assign("instantsearch", false);
	$this->smarty->assign("tableid", false);

if( $has_advanced_perm || $this->CheckPermission("Modify Templates") || $this->GetPreference("tabdisplay_templates",false) ) {
	echo $this->StartTab("templates");
	
    echo "<fieldset style=\"width: 600px;\"><legend><b>".$this->Lang("defaulttemplates")."</b></legend>";
    echo $this->CreateFormStart($id, "changedeftemplates", $returnid);
    $templatelist = $this->ListTemplates($this->GetName());
    $deftpls = $this->getDefaultTemplates();
    $tploptions = array();
    $itemlist = array();
    foreach($templatelist as $onetpl){
	   $tploptions[$onetpl] = $onetpl;
	   $tpl = new stdClass();
	   $tpl->editlink = $this->CreateLink( $id, "editTemplate", $returnid, $onetpl, array("tplname"=>$onetpl) );
	   $tpl->deletelink = in_array($onetpl, $deftpls)?"":$this->CreateLink( $id, "deletetpl", $returnid, $admintheme->DisplayImage("icons/system/delete.gif", $this->Lang("delete"), "", "", "systemicon"), array("tplname"=>$onetpl) );
	   array_push($itemlist, $tpl);
    }

	   echo "	<div class=\"pageoverflow\">
			 <p class=\"pagetext\">".$this->Lang("deftemplatefor")." \"category\":</p>
			 <p class=\"pageinput\">".$this->CreateInputDropdown($id,"listtemplate_category",$tploptions,-1,$this->GetPreference("listtemplate_category"))."</p>
		</div>
    ";
	   echo "	<div class=\"pageoverflow\">
			 <p class=\"pagetext\">".$this->Lang("deftemplatefor")." \"videos\":</p>
			 <p class=\"pageinput\">".$this->CreateInputDropdown($id,"listtemplate_videos",$tploptions,-1,$this->GetPreference("listtemplate_videos"))."</p>
		</div>
    ";
    echo "	<div class=\"pageoverflow\">
			 <p class=\"pagetext\">".$this->Lang("defdetailtemplate").":</p>
			 <p class=\"pageinput\">".$this->CreateInputDropdown($id,"finaltemplate",$tploptions,-1,$this->GetPreference("finaltemplate"))."</p>
		</div>
	";
	$tploptions[$this->Lang("uselevellisttpl")] = "**";
	echo "	<div class=\"pageoverflow\">
			 <p class=\"pagetext\">".$this->Lang("defsearchresultstemplate").":</p>
			 <p class=\"pageinput\">".$this->CreateInputDropdown($id,"searchresultstemplate",$tploptions,-1,$this->GetPreference("searchresultstemplate",0))."</p>
		</div>
    <p>".$this->CreateInputSubmit($id, "submit", lang("submit"))."</p>";
    echo $this->CreateFormEnd();

    echo "</fieldset><br/><br/>";
    $this->smarty->assign("itemlist", $itemlist);
	$this->smarty->assign("addnew", $this->CreateLink($id, "editTemplate", $returnid, $admintheme->DisplayImage("icons/system/newobject.gif", "","","","systemicon")." ".$this->Lang("addtemplate")));
    $adminshow = array(	array($this->Lang("template"), "editlink"), array($this->Lang("Actions"), "deletelink")	);
    $this->smarty->assign("adminshow", $adminshow);
    echo $this->ProcessTemplate("adminpanel.tpl");
	echo $this->EndTab();
}

if( $has_advanced_perm || $this->GetPreference("tabdisplay_queries",false) ) {
	echo $this->StartTab("queries");

		$itemlist = $this->get_queries($where=array(), true, $id, $returnid);
		$this->smarty->assign("itemlist", $itemlist?$itemlist:array());
		$adminshow = array( array("id","id"),
							array($this->Lang("name"), "name"),
							array($this->Lang("Actions"), "actions")
							);
		$this->smarty->assign("adminshow", $adminshow);
		$this->smarty->assign("addnew", $this->CreateLink($id, "adminquery", $returnid, $admintheme->DisplayImage("icons/system/newobject.gif", "","","","systemicon")." ".$this->Lang("createquery")));
		echo $this->ProcessTemplate("adminpanel.tpl");
		
	echo $this->EndTab();
}

if( $has_advanced_perm ) {
	echo $this->StartTab("preferences");
	echo $this->CreateFormStart($id, "changepreferences", $returnid);
	echo "<fieldset style=\"width: 465px;\"><legend><b>".$this->Lang("pref_tabdisplay").":</b></legend><ul>
	";
	$tabdisplay = array("category","videos","templates","queries");
	foreach($tabdisplay as $onepref){
		echo "<li>".$this->CreateInputCheckbox($id, "tabdisplay_".$onepref, true, $this->GetPreference("tabdisplay_".$onepref,false)).$this->Lang($onepref)."</li>
	";
	}
	echo "</ul><br/><p>".$this->Lang("help_tabdisplay")."</p>
</fieldset><br/>
";
	echo "<fieldset style=\"width: 465px;\"><legend><b>".$this->Lang("pref_searchmodule_index").":</b></legend><ul>
	";
	foreach($this->get_levelarray() as $onepref){
		echo "<li>".$this->CreateInputCheckbox($id, "searchmodule_index_".$onepref, true, $this->GetPreference("searchmodule_index_".$onepref,false)).$this->Lang($onepref)."</li>
	";
	}
	echo "</ul><br/><p>".$this->Lang("help_searchmodule_index")."</p>
</fieldset><br/>
";
	echo "<fieldset style=\"width: 465px;\"><legend><b>".$this->Lang("pref_newitemsfirst").":</b></legend><ul>
	";
	foreach($this->get_levelarray() as $onepref){
		echo "<li>".$this->CreateInputCheckbox($id, "newitemsfirst_".$onepref, true, $this->GetPreference("newitemsfirst_".$onepref,false)).$this->Lang($onepref)."</li>
	";
	}
	echo "</ul><br/><p>".$this->Lang("help_newitemsfirst")."</p>
</fieldset><br/>
";

	echo "<fieldset style=\"width: 600px;\"><legend><b>".$this->Lang("pref_frontend").":</b></legend>
	";
	$checkboxprefs = array("fe_wysiwyg","fe_decodeentities","fe_allowfiles","fe_allownamechange","fe_allowaddnew","fe_usecaptcha");
	foreach($checkboxprefs as $onepref){
		echo "<p>".$this->CreateInputCheckbox($id, $onepref, true, $this->GetPreference($onepref,false)).$this->Lang("pref_".$onepref)."</p>
	";
	}
	echo "<p>".$this->Lang("pref_fe_maxfilesize").":".$this->CreateInputText($id, "fe_maxfilesize", $this->GetPreference("fe_maxfilesize",""), 12, 12)."</p>
	";
	$cntoperations = $gCms->getContentOperations();
	$cur_redirect = $this->GetPreference("fe_aftersubmit",-1);
	echo "<p>".$this->Lang("pref_fe_aftersubmit").$cntoperations->CreateHierarchyDropdown("",$cur_redirect,$id."fe_aftersubmit")."</p>
	";
	echo "<br/><p>".$this->Lang("help_frontend")."</p>
</fieldset><br/>
";

	echo "<fieldset style=\"width: 600px;\"><legend><b>".$this->Lang("preferences").":</b></legend>
	";
	$checkboxprefs = array("restrict_permissions","use_hierarchy","orderbyname","display_filter","display_instantsearch","display_instantsort","editable_aliases","autoincrement_alias","allow_sql","force_list","showthumbnails","delete_files","allow_complex_order");
	foreach($checkboxprefs as $onepref){
		echo "<p>".$this->CreateInputCheckbox($id, $onepref, true, $this->GetPreference($onepref,false))." ".$this->Lang("pref_".$onepref)."</p>
	";
	}
	echo "<p>".$this->Lang("pref_maxshownpages").$this->CreateInputText($id, "maxshownpages", $this->GetPreference("maxshownpages",7), 12, 2)."</p>
	";
	echo "</fieldset><br/><p>".$this->CreateInputSubmit($id, "submit", lang("submit"))."</p>";
    echo $this->CreateFormEnd();
	echo "<br/><p>".$admintheme->DisplayImage("icons/system/import.gif", "","","","systemicon")." ";
	echo $this->CreateLink($id, "export", $returnid, $this->Lang("export_title"))." | ".$this->CreateLink($id, "import", $returnid, $this->Lang("import_title"))."</p>";
	echo $this->EndTab();
}

echo $this->EndTabContent();

?>