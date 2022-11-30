<?php
/* PARAMS :
what* : 'active' || 'default'
tablename*
prefix
prefix.id
newval : new value for active ( 0 || 1 )
*/

if (!isset($gCms)) exit;

$newparams = isset($params['levelname'])?array('active_tab'=>$params['levelname']):array();

if( !isset($params['what']) || !isset($params['tablename']) )	$this->Redirect($id, 'defaultadmin', $returnid, $newparams);

$db =& $this->GetDb();
$dbtable = cms_db_prefix().'module_'.$params['tablename'];
$field = $params['what'];
$itemid = isset($params['prefix'])?$params[$params['prefix'].'id']:false;
$value = isset($params['newval'])?$params['newval']:1;

if($field == 'active'){
	$query = "UPDATE $dbtable SET active=? WHERE id=? LIMIT 1";
	$db->Execute($query,array($value, $itemid));
}elseif($field == 'default'){
	if(isset($params["parent"]) && isset($params["parentdefault"]) && $params["parentdefault"]) {
		$query = "UPDATE $dbtable SET isdefault=0 WHERE parent=?";
		$db->Execute($query,array($params["parent"]));
	}else{
		$query = "UPDATE $dbtable SET isdefault=0";
		$db->Execute($query);
	}
	if($itemid){
		$query = "UPDATE $dbtable SET isdefault=1 WHERE id=?";
		$db->Execute($query,array($itemid));
	}
}


$this->Redirect($id, 'defaultadmin', $returnid, $newparams);
?>
