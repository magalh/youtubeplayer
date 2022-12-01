<?php
if( !defined('CMS_VERSION') ) exit;
$admintheme = $gCms->variables["admintheme"];
if(isset($params["cancel"]) || ($this->GetPreference("restrict_permissions",false) && !$this->CheckPermission("youtubeplayer_advanced") && !$this->CheckPermission("youtubeplayer_manage_videos")) ){
	$newparams = array("active_tab" => "videos");
	if(!isset($params["cancel"]))	$newparams["module_message"] = $this->Lang("error_denied");
	$this->Redirect($id, "defaultadmin", $returnid, $newparams);
}

$db =& $this->GetDb();

if($this->GetPreference("use_hierarchy",false)){
	$parentoptions = $this->get_admin_hierarchyoptions("videos",false);
}else{
	$parentoptions = $this->get_options("category");
}

if(isset($params["Bid"])) {
	// if we are working on an item that exists, we load it. We must do this even when the form is submitted, otherwise we won't have the file fields
	$items = $this->get_level_videos(array("id"=>$params["Bid"]));
	$item = $items[0];
}

// CHECK IF THE FORM IS BEING SUBMITTED :
// (we must detect all kinds of submit buttons, including files, since information must be saved before we go to file submission)
if (isset($params["submit"]) || 
	isset($params["apply"]) 
	)
{
	debug_buffer("Edit Form has been submitted".__LINE__);

	// RETRIEVING THE FORM VALUES (and escaping it, if needed)
	if(!isset($item)) $item = new stdClass();
	if(isset($params["Bitem_order"])) $item->item_order = $params["Bitem_order"];
	$item->videoid = $params["Bvideoid"];
		$item->description = $params["Bdescription"];
		$item->parent = $params["Bparent"];
		$item->name = $params["Bname"];
		
	if($this->GetPreference("editable_aliases",false)){
		if(isset($params["Balias"])){
			$tmpalias = $this->plcreatealias(trim($params["Balias"]));
			$item->alias = $tmpalias == ""?$this->plcreatealias($item->name):$tmpalias;
		}else{
			$item->alias = $this->plcreatealias($item->name);
		}
	}else{
		$item->alias = $this->plcreatealias($item->name);
	}
	
	$autoincrementalias = $this->GetPreference("autoincrement_alias",false);

	// CHECK IF THE NEEDED VALUES ARE THERE
	if(	!isset($params["Bvideoid"]) || $params["Bvideoid"] == ""
		 || !isset($params["Bdescription"]) || $params["Bdescription"] == ""
		 || !isset($params["Bname"]) || $params["Bname"] == ""
		 )
	{
		echo $this->ShowErrors($this->Lang("error_missginvalue"));
	}elseif(!$autoincrementalias && false == $this->checkalias("module_youtubeplayer_videos", $item->alias, isset($params["Bid"])?$params["Bid"]:false)){
		echo $this->ShowErrors($this->Lang("error_alreadyexists"));
	}else{
		############ DOING THE UPDATE

		if($autoincrementalias){
			$basealias = $item->alias;
			$tmpalias = $item->alias;
			$i = 1;
			while(!$this->checkalias("module_youtubeplayer_videos", $tmpalias, isset($params["Bid"])?$params["Bid"]:false)){
				$tmpalias = $basealias."_".$i;
				$i++;
			}
			$item->alias = $tmpalias;
		}

		// FIELDS TO UPDATE
		$query = (isset($item->id)?"UPDATE ":"INSERT INTO ").cms_db_prefix()."module_youtubeplayer_videos SET 
			videoid=?,
			description=?,
			parent=?,
			name=?,
		alias=?,
		date_modified=?,
		active=".(isset($item->active)?$item->active:1).",
		isdefault=".(isset($item->isdefault)?$item->isdefault:0)."";
			
		// VALUES
		$values = array(addslashes($item->videoid),
			addslashes($item->description),
			$item->parent,
			addslashes($item->name),$item->alias,str_replace("'","",$db->DBTimeStamp(time())));

		if(isset($item->id)){
			$event = "youtubeplayer_modified";
			$query .= " WHERE id=?;";
			array_push($values,$item->id);
		}else{
			// NEW ITEM
			$query .= ", date_created=?";
			$values[] = str_replace("'","",$db->DBTimeStamp(time()));
			$event = "youtubeplayer_added";
			// get a new id from the sequence table
			$item->id = $db->GenID(cms_db_prefix()."module_youtubeplayer_videos_seq");
			if($this->GetPreference("newitemsfirst_videos",false)){
				// new items get to the top - so we must put all other items down from one step, and then set this item's order to 1
				$query2 = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=(item_order+1) WHERE parent=?";
				$db->Execute($query2, array($item->parent));
				$query .= ",item_order=1, id=".$item->id;
			}else{
				// new items get to the bottom - so we must set the item_order to the number of items + 1
				$item_order = $this->countsomething("videos","id",array("parent"=>$item->parent)) + 1;
				$query .= ",item_order=".$item_order.", id=".$item->id;
			}
		}
		$db->Execute($query, $values);

	if(isset($params["oldparent"]) && $params["oldparent"] != $item->parent){
		// the item is changing parent, and we're ordering by parents
		
		if($this->GetPreference("newitemsfirst_videos",false)){
			// new items get to the top
			
			// UPDATE THE ORDER OF THE ITEMS WITH THE OLD PARENT
			$query = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=(item_order-1) WHERE item_order > ? AND parent=?";
			$db->Execute($query, array($item->item_order, $params["oldparent"]));
			// GET NEW ITEM ORDER
			$item->item_order = $this->countsomething("videos","id",array("parent"=>$item->parent)) + 1;
			$query = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=? WHERE id=?";
			$db->Execute($query, array($item->item_order, $item->id));

		}else{

			// UPDATE THE ORDER OF THE ITEMS WITH THE OLD PARENT
			$query = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=(item_order-1) WHERE item_order > ? AND parent=?";
			$db->Execute($query, array($item->item_order, $params["oldparent"]));
			// UPDATE NEW PARENT
			$query = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=(item_order+1) WHERE parent=?";
			$db->Execute($query, array($item->parent));
			// GET NEW ITEM ORDER
			$item->item_order = 1;
			$query = "UPDATE ".cms_db_prefix()."module_youtubeplayer_videos SET item_order=? WHERE id=?";
			$db->Execute($query, array($item->item_order, $item->id));
		
		}
	}

		$redirect = true;
		//if(mysql_affected_rows()){	// mysql-only
		if($db->Affected_Rows()){
			if($this->GetPreference("searchmodule_index_videos",false)){
				// IF ANYTHING WAS MODIFIED, WE MUST UPDATE THE SEARCH INDEX AND SEND AN EVENT...
				if(isset($event))	$this->SendEvent($event, array("what"=>"videos", "itemid" => $item->id, "alias"=>$item->alias));
				debug_buffer("SEARHC INDEX WAS UPDATED ".__LINE__);
				$module =& $this->GetModuleInstance("Search");
				if($module) {
					$text = "$item->name";
					$module->AddWords($this->GetName(), $item->id, "videos", $text, NULL);
				}
			}
		}elseif(mysql_error()){
			// do not redirect :
			$redirect = false;
			echo $this->ShowErrors(mysql_error());
		}

		// REDIRECTING...
			if($redirect == false){
			}elseif(isset($params["apply"])){
				echo $this->ShowMessage($this->Lang("message_modified"));
			}else{
				$params = array("module_message" => $this->Lang("message_modified"), "active_tab"=>"videos");
				$this->Redirect($id, "defaultadmin", $returnid, $params);	
			}
	}
	// END OF FORM SUBMISSION
}



/* ## PREPARING SMARTY ELEMENTS
CreateInputText : (id,name,value,size,maxlength)
CreateTextArea : (wysiwyg,id,text,name)
CreateInputSelectList : (id,name,items,selecteditems,size)
CreateInputDropdown : (id,name,items,sindex,svalue)
*/

$this->smarty->assign("videoid_label", $this->Lang("videos_videoid"));
$this->smarty->assign("videoid_input", $this->CreateInputText($id,"Bvideoid",isset($item)?$item->videoid:"",50,255));
$this->smarty->assign("description_label", $this->Lang("videos_description"));
$this->smarty->assign("description_input", $this->CreateInputText($id,"Bdescription",isset($item)?$item->description:"",50,255));
$this->smarty->assign("name_label", $this->Lang("name"));
$this->smarty->assign("name_input", $this->CreateInputText($id,"Bname",isset($item)?$item->name:"",50,64));
$this->smarty->assign("parent_label", $this->Lang("category"));
$this->smarty->assign("parent_input", $this->CreateInputDropdown($id,"Bparent",$parentoptions,-1,isset($item)?$item->parent:0));if(isset($item)) $this->CreateInputHidden($id, "oldparent", $item->parent);
if($this->GetPreference("editable_aliases",false)){
	$this->smarty->assign("alias_label", $this->Lang("alias"));
	$this->smarty->assign("alias_input", $this->CreateInputText($id,"Balias",isset($item)?$item->alias:"",50,64));
	$this->smarty->assign("itemalias",false);
}else{
	$this->smarty->assign("itemalias",isset($item->alias)?"(alias : ".$item->alias.")":false);
	$this->smarty->assign("alias_input", false);
}

$this->smarty->assign("edittitle", $this->Lang("edit_videos"));

$this->smarty->assign("submit", $this->CreateInputSubmit($id, "submit", lang("submit")));
$this->smarty->assign("apply", (isset($item) && isset($item->id))?$this->CreateInputSubmit($id, "apply", lang("apply")):"");
$this->smarty->assign("cancel", $this->CreateInputSubmit($id, "cancel", lang("cancel")));


// DISPLAYING
if(isset($item) && isset($item->id)){
		echo $this->CreateFormStart($id, "editB", $returnid);
		echo $this->ProcessTemplate("editB.tpl");
		echo $this->CreateInputHidden($id, "Bid", $item->id);
		if(isset($item) && isset($item->parent)) echo $this->CreateInputHidden($id, "oldparent", $item->parent);
		echo $this->CreateInputHidden($id, "Bitem_order", $item->item_order);
		echo $this->CreateFormEnd();
	

}else{
	echo $this->CreateFormStart($id, "editB", $returnid);
	echo $this->ProcessTemplate("editB.tpl");
	echo $this->CreateFormEnd();
}
?>