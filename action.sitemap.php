<?php
if(!isset($gCms)) exit;

$levelarray = $this->get_levelarray();
$mode = (isset($params["mode"]) && in_array($params["mode"],array("google","urllist","frontend")))?$params["mode"]:"frontend";
$what = isset($params["what"])?explode('|',$params["what"]):$levelarray;
$linkreturnid = (isset($params["detailpage"]))?$this->get_pageid($params["detailpage"]):$returnid;
$inline = (isset($params["inline"]) && $params["inline"])?true:false;

$db = $this->GetDb();
$i = 0;
$select = "";
$tables = "";
$order = "";
while($i < count($levelarray)){
	$level = $levelarray[$i];
	$i++;
	$select .= ($select==""?"":", ")."table".$i.".alias table".$i."_alias, table".$i.".name table".$i."_name";
	if($i == 1){
		$tables = cms_db_prefix()."module_".$this->GetName()."_".$level." table".$i;
	}else{
		$tables .= " LEFT JOIN ".cms_db_prefix()."module_".$this->GetName()."_".$level." table".$i. " ON table".($i-1).".id=table".$i.".parent";
	}
	$order .= ($order==""?"":", ")."table".$i.".item_order";
}
$query = "SELECT $select FROM $tables ORDER BY $order";
$dbresult = $db->Execute($query);

$last = array();
$currentlevel = 1;
if($mode == 'frontend')	echo '<ul id="'.$this->GetName().'_map">';
while($dbresult && $row = $dbresult->FetchRow()){
	$parselevel = 1;
	while($parselevel <= $i){
		if(in_array($levelarray[$parselevel-1],$what) && (!isset($last[$parselevel]) || $row["table".$parselevel."_alias"] != $last[$parselevel]) ){
			$wantedwhat = ($parselevel==$i)?$levelarray[$i-1]:$levelarray[$parselevel];
			$wantedparam = ($parselevel==$i)?"alias":"parent";
			$newparams = array("what"=>$wantedwhat, $wantedparam=>$row["table".$parselevel."_alias"]);
			$prettyurl = $this->BuildPrettyUrls($newparams, $linkreturnid);
			if($mode == 'frontend'){
				if($currentlevel == $parselevel){
					if($last[$currentlevel])	echo "</li>";
				}elseif($currentlevel > $parselevel){
					while($currentlevel > $parselevel){
						echo "</li></ul></li>";
						$currentlevel--;
					}
				}elseif($currentlevel < $parselevel){
					$currentlevel++;
					echo '<ul>';
				}
				echo '<li>'.$this->CreateLink($id, "default", $linkreturnid, $row["table".$parselevel."_name"], $newparams, "", false, $inline, "", false, $prettyurl);
			}elseif($mode == 'urllist'){
				echo $this->CreateLink($id, "default", $linkreturnid, "", $newparams, "", true, $inline, "", false, $prettyurl).'
';
			}elseif($mode == 'google'){
				$priority = 9 - $parselevel;
				if($priority < 0)	$priority = 1;
				$priority = "0.".$priority;
				echo '
	<url>
		<loc>'.$this->CreateLink($id, "default", $linkreturnid, "", $newparams, "", true, $inline, "", false, $prettyurl).'</loc>
		<priority>'.$priority.'</priority>
	</url>';
			}
			
			$last[$parselevel] = $row["table".$parselevel."_alias"];
		}
		$parselevel++;
	}
}
if($mode == 'frontend'){
	echo "</li>";
	while($currentlevel > 1){
		echo "</ul></li>";
		$currentlevel--;
	}
	echo "</ul>";
}
