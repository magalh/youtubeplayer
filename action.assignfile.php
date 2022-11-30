<?php
if(!isset($gCms)) exit;

if(!isset($params['field']) || !isset($params['prefix']) || !isset($params[$params['prefix'].'id']) || !isset($params['tablename']))	$this->Redirect($id, 'defaultadmin', $returnid, array());

$filepath = isset($params['filepath'])?$params['filepath']:'';
if($filepath != '' && isset($params['startdir']) && $params['startdir'] != '/')	$filepath = $params['startdir'].'/'.$filepath;
$filepath = str_replace('//','/',$filepath);
$itemid = $params[$params['prefix'].'id'];
$thumbsize = isset($params['thumb'])?$params['thumb']:false;

$this->plAssignFile($filepath, $params['tablename'], $itemid, $params['field'], $thumbsize, isset($params['cropthumb']));

$newparams = array( $params['prefix'].'id' => $itemid );
if(isset($params['active_tab'])) $newparams['active_tab'] = $params['active_tab'];
$this->Redirect($id, 'edit'.$params['prefix'], $returnid, $newparams );		

?>
