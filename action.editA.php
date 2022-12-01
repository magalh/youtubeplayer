<?php
if( !defined('CMS_VERSION') ) exit;

$gCms = cmsms();
$admintheme = cms_utils::get_theme_object();

if(isset($params["cancel"]) || ($this->GetPreference("restrict_permissions",false) && !$this->CheckPermission("youtubeplayer_advanced") && !$this->CheckPermission("youtubeplayer_manage_category")) ){
	$newparams = array("active_tab" => "category");
	if(!isset($params["cancel"]))	$newparams["module_message"] = $this->Lang("error_denied");
	$this->Redirect($id, "defaultadmin", $returnid, $newparams);
}

$db = $this->GetDb();


if(isset($params["Aid"])) {
	// if we are working on an item that exists, we load it. We must do this even when the form is submitted, otherwise we won't have the file fields
	$items = $this->get_level_category(array("id"=>$params["Aid"]));
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
	if(isset($params["Aitem_order"])) $item->item_order = $params["Aitem_order"];
	$item->description = $params["Adescription"];
		$item->name = $params["Aname"];
		
	if($this->GetPreference("editable_aliases",false)){
		if(isset($params["Aalias"])){
			$tmpalias = $this->plcreatealias(trim($params["Aalias"]));
			$item->alias = $tmpalias == ""?$this->plcreatealias($item->name):$tmpalias;
		}else{
			$item->alias = $this->plcreatealias($item->name);
		}
	}else{
		$item->alias = $this->plcreatealias($item->name);
	}
	
	$autoincrementalias = $this->GetPreference("autoincrement_alias",false);

	// CHECK IF THE NEEDED VALUES ARE THERE
	if(	!isset($params["Aname"]) || $params["Aname"] == ""
		 )
	{
		echo $this->ShowErrors($this->Lang("error_missginvalue"));
	}elseif(!$autoincrementalias && false == $this->checkalias("module_youtubeplayer_category", $item->alias, isset($params["Aid"])?$params["Aid"]:false)){
		echo $this->ShowErrors($this->Lang("error_alreadyexists"));
	}else{
		############ DOING THE UPDATE

		if($autoincrementalias){
			$basealias = $item->alias;
			$tmpalias = $item->alias;
			$i = 1;
			while(!$this->checkalias("module_youtubeplayer_category", $tmpalias, isset($params["Aid"])?$params["Aid"]:false)){
				$tmpalias = $basealias."_".$i;
				$i++;
			}
			$item->alias = $tmpalias;
		}

		// FIELDS TO UPDATE
		$query = (isset($item->id)?"UPDATE ":"INSERT INTO ").cms_db_prefix()."module_youtubeplayer_category SET 
			description=?,
			name=?,
		alias=?,
		date_modified=?,
		active=".(isset($item->active)?$item->active:1).",
		isdefault=".(isset($item->isdefault)?$item->isdefault:0)."";
			
		// VALUES
		$values = array(addslashes($item->description),
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
			$item->id = $db->GenID(cms_db_prefix()."module_youtubeplayer_category_seq");
			if($this->GetPreference("newitemsfirst_category",false)){
				// new items get to the top - so we must put all other items down from one step, and then set this item's order to 1
				$query2 = "UPDATE ".cms_db_prefix()."module_youtubeplayer_category SET item_order=(item_order+1)";
				$db->Execute($query2);
				$query .= ",item_order=1, id=".$item->id;
			}else{
				// new items get to the bottom - so we must set the item_order to the number of items + 1
				$item_order = $this->countsomething("category") + 1;
				$query .= ",item_order=".$item_order.", id=".$item->id;
			}
		}
		$db->Execute($query, $values);

		$redirect = true;
		//if(mysql_affected_rows()){	// mysql-only
		if($db->Affected_Rows()){
			if($this->GetPreference("searchmodule_index_category",false)){
				// IF ANYTHING WAS MODIFIED, WE MUST UPDATE THE SEARCH INDEX AND SEND AN EVENT...
				if(isset($event))	$this->SendEvent($event, array("what"=>"category", "itemid" => $item->id, "alias"=>$item->alias));
				debug_buffer("SEARHC INDEX WAS UPDATED ".__LINE__);
				$module =& $this->GetModuleInstance("Search");
				if($module) {
					$text = "$item->name";
					$module->AddWords($this->GetName(), $item->id, "category", $text, NULL);
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
				$params = array("module_message" => $this->Lang("message_modified"), "active_tab"=>"category");
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

$this->smarty->assign("description_label", $this->Lang("category_description"));
$this->smarty->assign("description_input", $this->CreateInputText($id,"Adescription",isset($item)?$item->description:"",50,255));
$this->smarty->assign("name_label", $this->Lang("name"));
$this->smarty->assign("name_input", $this->CreateInputText($id,"Aname",isset($item)?$item->name:"",50,64));
if($this->GetPreference("editable_aliases",false)){
	$this->smarty->assign("alias_label", $this->Lang("alias"));
	$this->smarty->assign("alias_input", $this->CreateInputText($id,"Aalias",isset($item)?$item->alias:"",50,64));
	$this->smarty->assign("itemalias",false);
}else{
	$this->smarty->assign("itemalias",isset($item->alias)?"(alias : ".$item->alias.")":false);
	$this->smarty->assign("alias_input", false);
}

$this->smarty->assign("edittitle", $this->Lang("edit_category"));

$this->smarty->assign("submit", $this->CreateInputSubmit($id, "submit", lang("submit")));
$this->smarty->assign("apply", (isset($item) && isset($item->id))?$this->CreateInputSubmit($id, "apply", lang("apply")):"");
$this->smarty->assign("cancel", $this->CreateInputSubmit($id, "cancel", lang("cancel")));


// DISPLAYING
if(isset($item) && isset($item->id)){
		echo $this->CreateFormStart($id, "editA", $returnid);
		echo $this->ProcessTemplate("editA.tpl");
		echo $this->CreateInputHidden($id, "Aid", $item->id);
		if(isset($item) && isset($item->parent)) echo $this->CreateInputHidden($id, "oldparent", $item->parent);
		echo $this->CreateInputHidden($id, "Aitem_order", $item->item_order);
		echo $this->CreateFormEnd();
	

}else{
	echo $this->CreateFormStart($id, "editA", $returnid);
	echo $this->ProcessTemplate("editA.tpl");
	echo $this->CreateFormEnd();
}
?>