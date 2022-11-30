<?php
if (!isset($gCms)) exit;

$newparams = isset($params["levelname"])?array("active_tab"=>$params["levelname"]):array();

if (!isset($params["move"]) || !isset($params["tablename"]) || !isset($params["prefix"]) || !isset($params["currentorder"]) )
$this->Redirect($id, "defaultadmin", $returnid, $newparams);

$db =& $this->GetDb();
$dbtable = cms_db_prefix()."module_".$params["tablename"];
$order = $params["currentorder"];
$itemid = $params[$params["prefix"]."id"];
$wparent = isset($params["parent"])?" AND parent='".$params["parent"]."'":"";

switch($params["move"]){
	
	case "delete":
		if(isset($params["sharechildren"]) && $params["sharechildren"]){
			// this level share its children... we delete the item's links to children
			if($childlevel = $this->get_nextlevel($params["levelname"])){
				$linktable = $dbtable."_has_".$childlevel;
				$query = "DELETE FROM $linktable WHERE ".$params["levelname"]."_id=?";
				$db->Execute($query, array($itemid));
			}
		}elseif(isset($params["child"]) && $params["child"] != ""){
			// this level doesn't share its children... we delete the item's children
			$childtable = cms_db_prefix()."module_".$params["child"];
			$query = "DELETE FROM $childtable WHERE parent = ?";
			$db->Execute($query, array($itemid));
		}
		if(isset($params["sharedbyparents"]) && $params["sharedbyparents"]){
			// this item is shared by its parents... we delete links from parents to this child
			if($parentlevel = $this->get_nextlevel($params["levelname"], false)){
				$linktable = cms_db_prefix()."module_youtubeplayer_".$parentlevel."_has_".$params["levelname"];
				$query = "DELETE FROM $linktable WHERE ".$params["levelname"]."_id=?";
				$db->Execute($query, array($itemid));
			}
		}
		
		if(isset($params["files"]) && $params["files"] != "" && $this->GetPreference("delete_files",false) ){
			$filefields = explode(",",$params["files"]);
			$query = "SELECT * FROM $dbtable WHERE id = ?";
			$dbresult = $db->Execute($query, array($itemid));
			if($dbresult && $row = $dbresult->FetchRow()){
				foreach($filefields as $field){
					if($row[$field] != "" && $row[$field] != "/"){
						$filepath = $gCms->config["uploads_path"].(substr($row["filepath"],0,1)=="/"?"":"/").$row[$field];
						$tmpexp = explode("/",$row[$field]);
						$filename = $tmpexp[count($tmpexp) -1];
						$thumbpath = str_replace($filename,"plthumb_".$filename,$filepath);
						if(file_exists($thumbpath))	unlink($thumbpath);
						if(file_exists($filepath))	unlink($filepath);
					}
				}
			}
		}
		// if the level has fields of the type "Undefined amount of files", we delete the files
		if(isset($params["addfiles"]) && $params["addfiles"] != ""){
			$fields = explode(",",$params["addfiles"]);
			if(count($fields) > 0){
				$where = "";
				$values = array();
				foreach($fields as $field){
					$where .= ($where == ""?"":" OR ")." fieldname=?";
					$values[] = $field;
				}
				$addfilestable = cms_db_prefix()."module_youtubeplayer_multiplefilesfields";

				if($this->GetPreference("delete_files",false)){
					$query = "SELECT * FROM $addfilestable WHERE ($where) AND itemid=".$itemid;
					$dbresult = $db->Execute($query, $values);
					while($dbresult && $row = $dbresult->FetchRow()){
						$filepath = $gCms->config["upload_path"].(substr($row["filepath"],0,1)=="/"?"":"/").$row["filepath"];
						if(file_exists($filepath))	unlink($filepath);
					}
				}
				$query = "DELETE FROM $addfilestable WHERE ($where) AND itemid=".$itemid;
				$db->Execute($query, $values);
			}
		}
			
		
		$query = "DELETE FROM $dbtable WHERE id = ? LIMIT 1";
		$db->Execute($query, array($itemid));

		// UPDATE THE ORDER OF THE ITEMS
		$query = "UPDATE $dbtable SET item_order=(item_order-1) WHERE item_order > ? ".$wparent;
		$db->Execute($query, array($order));

		if(isset($params["levelname"]))	$this->SendEvent("youtubeplayer_deleted", array("what"=>$params["levelname"]));
		$newparams["module_message"] = $this->lang("message_deleted");
		break;	

	case "up":
		if ($order != 1){
			$query = "UPDATE $dbtable SET item_order=(item_order+1) WHERE item_order = ? $wparent LIMIT 1;";
			$db->Execute($query, array($order-1));
			$query = "UPDATE $dbtable SET item_order=(item_order-1) WHERE id = ? LIMIT 1;";
			$db->Execute($query, array($itemid));
		}
		break;
		
	case "down":
		$query = "UPDATE $dbtable SET item_order=(item_order-1) WHERE item_order = ? $wparent LIMIT 1;";
		if( $db->Execute($query, array($order+1)) ){
			$query = "UPDATE $dbtable SET item_order=(item_order+1) WHERE id = ? LIMIT 1;";
			$db->Execute($query, array($itemid));
		}
		break;
}

$this->Redirect($id, "defaultadmin", $returnid, $newparams);
?>