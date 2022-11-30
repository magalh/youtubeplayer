<?php
if(!isset($gCms)) exit;

// we need to know which level is the final level
$levelarray = $this->get_levelarray();
$finallevel = "videos";

// we check what level we're watching... if none is specified, we use the final level
$what = (isset($params["what"]) && in_array($params["what"],$levelarray))?$params["what"]:$finallevel;
// we give it back to the params for pretty urls and page view
if(!isset($params["what"]) || $params["what"] == "")	$params["what"] = $what;
$parentlevel = $this->get_nextlevel($what, false);

// we retrieve some other parameters
if($what != $finallevel){
	$forcelist = true;		// Always display as list when we're not on the final level
}elseif(isset($params["alias"])){
	$forcelist = false;		// never display a list when the alias is specified
}else{
	$forcelist = isset($params["forcelist"])?$params["forcelist"]:$this->GetPreference("force_list",false);
}
$orderby = isset($params["orderby"])?$params["orderby"]:false;
$limit = isset($params["limit"])?$params["limit"]:false;
$inline = (isset($params["inline"]) && $params["inline"])?true:false;

// we build the query. First, we check if we're using a saved query :
if(isset($params["query"])){
	$queryid = (int) $params["query"];
	if($query = $this->get_queries(array("id"=>$queryid))){
		// we retrieve the name of the function that will do the query
		$query = $query[0];
		$what = $query->what;
		$getfunction = "get_level_".$what;
		$parentlevel = $this->get_nextlevel($what, false);
		$itemlist = $this->$getfunction(array(), false, "", "", $orderby, $limit, $query->whereclause, $query->wherevalues, ($query->queryorder == ""?false:$query->queryorder));
	}elseif($this->GetPreference("allow_sql",false)){
		$getfunction = "get_level_".$what;
		$parentlevel = $this->get_nextlevel($what, false);
		$query = $params["query"]; // query should be sanitized...
		$itemlist = $this->$getfunction(array(), false, "", "", $orderby, $limit, $query);
	}else{
		$message = $this->Lang("error_wrongquery");
		if($queryid != $query && $msg = mysql_error())		$message .= "<br/>".$this->Lang("givenerror").$msg;
		echo $this->ShowErrors($message);
	}
}else{
	// we're not using a saved query, so we parse the parameters
	// we retrieve the name of the function that will do the query
	$getfunction = "get_level_".$what;
	// The $where holds elements of the WHERE clause of the query, in the form field_name=>field_value
	if(isset($params["alias"]) && $params["alias"] != "") {
		$where = array("alias"=>$params["alias"]);
	}elseif(isset($params["showdefault"]) && $params["showdefault"]) {
		$where = array("isdefault"=>1);
	}else{
		$where = array();
	}
	if(isset($params["parent"]) && $params["parent"] != "") $where["parent"] = $params["parent"];

	$where["active"] = 1;
	
	$itemlist = $this->$getfunction($where, false, "", "", isset($params["orderby"])?$params["orderby"]:0, isset($params["limit"])?$params["limit"]:0);
}

// Integration would require some changes to CartMadeSimple
// $itemlist = $this->addCartUrls($itemlist,$id,$returnid);

//  START PROCESSING

if(count($itemlist) == 1 && !$forcelist){
	
	// ################# WE ARE DISPLAYING AN ITEM IN DETAIL VIEW
	
	$item=$itemlist[0];

	// if a template has been specified, we try to retrieve it	
	$template = false;
	if(isset($params["finaltemplate"]) && $params["finaltemplate"] != ""){
		$template = $this->GetTemplate($params["finaltemplate"]);
	}

	// if no template has been specified, we retrieve the default final template
	if(!$template || $template == ""){
		$templatename = $this->GetPreference("finaltemplate");
		$template = $this->GetTemplate($templatename, $this->GetName());
	}
	// we retrieve the parent tree:
	$parenttree = $this->get_objtree( $item->parent_id, $this->get_nextlevel($what, false) );
	$item->parent_object = $parenttree;

	
	// if the item has parents, we assign links to that parent
	if(isset($item->parent_alias)){
		$prettyurl = $this->BuildPrettyUrls(array("parent"=>$item->parent_alias, "what"=>$what), $returnid);
		$item->parentlink = $this->CreateLink($id, "default", $returnid, $item->parent_name, array("parent"=>$item->parent_alias), "", false, $inline, "", false, $prettyurl);
		$item->parenturl = $this->CreateLink($id, "default", $returnid, "", array("parent"=>$item->parent_alias), "", true, $inline, "", false, $prettyurl);
	}
	
	// we retrieve a label for each of the item's field and assign it to smarty
	$labels = new StdClass();
	foreach($item as $key=>$value){
		$labels->$key = $this->Lang($what."_".$key);		
	}
	$this->smarty->assign("labels", $labels);
	
	$this->smarty->assign("item",$item);
	$this->smarty->assign("leveltitle",$this->Lang($what."_plural"));
	// we process the template
	echo $this->ProcessTemplateFromData($template);
	
}elseif(count($itemlist) > 0){
	
	// ################# WE ARE DISPLAYING A LIST VIEW
	$parentobj = false;
	// if we are watching items from a specific parents, we want to have the informations of this parent available
	// in the template (for example, if we want to display a category page, we might want to show the category description)
	// if it is the case, we retrieve the parent and give it to smarty
	if(isset($params["parent"]) && $params["parent"] != ""){
		$parentobj = $this->get_objtree($params["parent"], $this->get_nextlevel($what,false), "alias");
	}
	$this->smarty->assign("parentobj",$parentobj);
	
	$selectedalias = false;
	// we check if the current page has other instances of the module which are in action
	$glob = $this->get_moduleGetVars();
	if($what == $finallevel && isset($glob["alias"])){
		$selectedalias = $glob["alias"];
	}elseif(isset($glob["parent"]) && isset($glob["what"]) && $glob["what"] == $this->get_nextlevel($what)){
		$selectedalias = $glob["parent"];
	}
	if(isset($params["nbperpage"]) && isset($glob["pageindex"]) && !isset($params["pageindex"])) $params["pageindex"] = $glob["pageindex"];
	if(!isset($this->plcurrent[$what]))	$this->buildGlobalTree();
	if(isset($this->plcurrent[$what]))	$selectedalias = $this->plcurrent[$what];
	if(isset($params["nbperpage"]) && !isset($params["pageindex"])) $params["pageindex"] = $this->currentpageindex;
	$linkreturnid = (isset($params["detailpage"]))?$this->get_pageid($params["detailpage"]):$returnid;

	// we retrieve the template
	if(isset($params["listtemplate"]) && $params["listtemplate"] != "" && $customtpl = $this->GetTemplate($params["listtemplate"], $this->GetName())){
		$template = $customtpl;
	}else{
		$templatename = $this->GetPreference("listtemplate_".$what);
		$template = $this->GetTemplate($templatename, $this->GetName());
	}

	// if RANDOM option is set, we randomly selected only a number of the items retrieved
	if( isset($params["random"]) && $params["random"] > 0 && $params["random"] < count($itemlist) ){
		$newlist = array();
		$i = 1;
		$selected = array();
		$currand = rand(0,(count($itemlist)-1));
		while($i <= $params["random"]){
			while(in_array($currand, $selected))	$currand = rand(0,(count($itemlist)-1));
			$newlist[] = $itemlist[$currand];
			$selected[] = $currand;
			$i++;
		}
		$itemlist = $newlist;
	}
	
	// check if we must separate into pages
	$itemlist = $this->split_into_pages($itemlist, $id, $returnid, $params);
	
		
	// final processing - we create the detaillinks for each item
	$newlist = array();
	foreach($itemlist as $item){
		$item = $this->addfrontendurls($item,$params,$id,$linkreturnid);
		$item->is_selected = ($item->alias == $selectedalias)?true:false;
		array_push($newlist, $item);
	}
	$itemlist = $newlist;
	
	// we give everything to smarty and process the template
	$this->smarty->assign("itemlist",$itemlist);
	$this->smarty->assign("leveltitle",$this->Lang($what."_plural"));
	echo $this->ProcessTemplateFromData($template);
	
}else{
	
	// ################# WE AREN'T DISPLAYING ANYTHING AT ALL
	$this->smarty->assign("error_msg",(isset($params["alias"])?$this->Lang("error_notfound"):$this->Lang("error_noitemfound")));
	$this->smarty->assign("backlink",false);
	echo $this->ProcessTemplate("noresult.tpl");
}

?>
