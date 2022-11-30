<?php
if(!isset($gCms)) exit;

$levelarray = $this->get_levelarray();
$finallevel = "videos";

$searchmode = (isset($params["searchmode"]) && in_array($params["searchmode"], array("simple","advanced")))?$params["searchmode"]:"advanced";

if(isset($params["submitsearch"])){
	// #####################################################################################
	// # SEARCH RESULTS
	$inline = (isset($params["inline"]) && $params["inline"])?true:false;
	$linkreturnid = (isset($params["detailpage"]))?$this->get_pageid($params["detailpage"]):false;
	if(!$linkreturnid)	$linkreturnid = $returnid;
	$what = $params["what"];
	$multiplelistfields = array();
	
	if($searchmode == "advanced"){
		// ADVANCED SEARCH
		
		$db = $this->GetDb();
		
		// we parse the keywords
		$where = $this->getWhereFromParams($params, true);
		list($whereclause, $wherevalues) = $this->buildWhere($where, $what);
		
		$getfunction = "get_level_".$what;
		// we do the query
		$itemlist = $this->$getfunction(array(), false, "", "", isset($params["orderby"])?$params["orderby"]:0, isset($params["limit"])?$params["limit"]:0, $whereclause, $wherevalues);
		
		if(count($itemlist) > 0){
			$foundsomething = true;
			
			// check if we must separate into pages
			$itemlist = $this->split_into_pages($itemlist, $id, $returnid, $params, "search", false);
			
			// we must build a detail link for each result element
			$newlist = array();
			foreach($itemlist as $item){
				$item = $this->addfrontendurls($item,$params,$id,$linkreturnid);
				$item->is_selected = false;
				array_push($newlist, $item);
			}
			
			// we retrieve the display template
			if(isset($params["listtemplate"]))		$template = $this->GetTemplate($params["listtemplate"], $this->GetName());
			if(!isset($template) || !$template){
				$templatename = $this->GetPreference("searchresultstemplate","**");
				if($templatename == "**")	$templatename = $this->GetPreference("listtemplate_".$what);
				$template = $this->GetTemplate($templatename, $this->GetName());
			}

			$this->smarty->assign("itemlist",$newlist);
			$this->smarty->assign("leveltitle",$this->Lang($what."_plural"));
			$this->smarty->assign("breadcrumbs",false);
			$this->smarty->assign("parentobj",false);
			echo $this->ProcessTemplateFromData($template);
			
		}else{
			$foundsomething = false;
		}
	}else{
		// SIMPLE SEARCH
		
		$foundsomething = false;
		$template = false;
		$levels = ($what == "-all")?$levelarray:array($what);
		
		// we retrieve the display template
		// if the user has asked for a specific display template, we will use it for every category :
		if(isset($params["listtemplate"]))		$template = $this->GetTemplate($params["listtemplate"], $this->GetName());
		if(!isset($template) || !$template){
			$templatename = $this->GetPreference("searchresultstemplate","**");
			if($templatename == "**"){
				if(count($levels) == 1){
					$templatename = $this->GetPreference("listtemplate_".$levels[0]);
					$template = $this->GetTemplate($templatename, $this->GetName());
				}else{
					$template = false;
				}
			}else{
				$template = $this->GetTemplate($templatename, $this->GetName());
			}
		}

		
		foreach($levels as $level){
			$whereclause = "";
			$fields = $this->get_levelsearchfields($level);
			$keywords = $this->parsekeywords(html_entity_decode($params["searchfield"]));
			foreach($fields as $field){
				foreach($keywords as $keyword){
					$whereclause .= ($whereclause == ""?"":" OR ")."A.".$field." LIKE '%".addslashes($keyword)."%'";
				}
			}
			$whereclause = "A.active=1".($whereclause == ""?"":" AND (".$whereclause.")");
			$getfunction = "get_level_".$level;
			$itemlist = $this->$getfunction(array(), false, "", "", isset($params["orderby"])?$params["orderby"]:0, isset($params["limit"])?$params["limit"]:0, $whereclause);
			
			if(count($itemlist) > 0){

				// check if we must separate into pages
				$itemlist = $this->split_into_pages($itemlist, $id, $returnid, $params, "search", false);
				
				$foundsomething = true;
				// we must build a detail link for each result element
				$newlist = array();
				foreach($itemlist as $item){
					$item = $this->addfrontendurls($item,$params,$id,$linkreturnid);
					$item->is_selected = false;
					array_push($newlist, $item);
				}
				$this->smarty->assign("itemlist",$newlist);
				$this->smarty->assign("leveltitle",$this->Lang($level."_plural"));
				$this->smarty->assign("breadcrumbs",false);
				$this->smarty->assign("parentobj",false);
				
				if($template == FALSE){
					echo $this->ProcessTemplate("search_generalresults.tpl");
				}else{
					echo $this->ProcessTemplateFromData($template);				
				}
				
			}
		}
		
	}
	
	// we display the "do another search" link, keeping the search parameters
	$newparams = array("what"=>$what, "searchmode"=>$searchmode);
	if(isset($params["listtemplate"]))	$newparams["listtemplate"] = $params["listtemplate"];
	if(isset($params["limit"]))			$newparams["limit"] = $params["limit"];
	if(isset($params["inline"]))		$newparams["inline"] = $params["inline"];
	if(isset($params["orderby"]))		$newparams["orderby"] = $params["orderby"];
	if(isset($params["nbperpage"]))		$newparams["nbperpage"] = $params["nbperpage"];
	if(isset($params["detailpage"]))	$newparams["detailpage"] = $params["detailpage"];

	$backlink = $this->CreateLink($id, "search", $returnid, $this->Lang("searchagain"), $newparams, "", false, $inline, "", false, false);
	if($foundsomething){
		echo "<p>".$backlink."</p>";
	}else{
		$this->smarty->assign("error_msg",(isset($params["alias"])?$this->Lang("error_notfound"):$this->Lang("error_noitemfound")));
		$this->smarty->assign("backlink",false);
		echo $this->ProcessTemplate("noresult.tpl");
	}

	// # END SEARCH RESULTS
	// #####################################################################################

}else{
	
	// #####################################################################################
	// # SEARCH FORM	
	
	$what = (isset($params["what"]) && in_array($params["what"],$levelarray))?$params["what"]:"-all";
	if($searchmode == "advanced" && $what == "-all")		$what = $finallevel;

	echo $this->CreateFormStart($id, "search", $returnid);
	echo $this->CreateInputHidden($id, "what", $what);
	echo $this->CreateInputHidden($id, "searchmode", $searchmode);
	if(isset($params["listtemplate"]))	echo $this->CreateInputHidden($id, "listtemplate", $params["listtemplate"]);
	if(isset($params["limit"]))	echo $this->CreateInputHidden($id, "limit", $params["limit"]);
	if(isset($params["inline"]))	echo $this->CreateInputHidden($id, "inline", $params["inline"]);
	if(isset($params["orderby"]))	echo $this->CreateInputHidden($id, "orderby", $params["orderby"]);
	if(isset($params["nbperpage"]))		echo $this->CreateInputHidden($id, "nbperpage", $params["nbperpage"]);
	if(isset($params["detailpage"]))	echo $this->CreateInputHidden($id, "detailpage", $params["detailpage"]);
	$this->smarty->assign("searchtitle", $this->Lang("searchtitle"));
	$this->smarty->assign("what", ($what == "-all")?false:$this->Lang($what));
	$this->smarty->assign("submit", $this->CreateInputSubmit($id, "submitsearch", $this->Lang("searchbtn")));

	if($searchmode == "simple"){
		// WE ARE DOING A SIMPLE SEARCH
		$this->smarty->assign("searchfield", $this->CreateInputText($id,"searchfield","",50));
		echo $this->ProcessTemplate("search.tpl");
		
	}else{
		// WE ARE DOING AN ADVANCED SEARCH

		$this->createFieldForm($what, $id, true);

		echo $this->ProcessTemplate("search_".$what.".tpl");
		
	}
	
	echo $this->CreateFormEnd();
	
	// # END SEARCH FORM
	// #####################################################################################	
	
}
?>