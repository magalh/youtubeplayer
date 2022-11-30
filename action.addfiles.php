<?php
if(!isset($gCms)) exit;
if(!isset($_FILES) || !isset($params['field']) || !isset($params['prefix']) || !isset($params[$params['prefix'].'id']) || !isset($params['tablename'])){
	$this->Redirect($id, 'defaultadmin', $returnid, array());
}

$db = $this->GetDb();

$resize = isset($params['size'])?$params['size']:false;
$thumbsize = isset($params['thumb'])?$params['thumb']:false;
$itemid = $params[$params['prefix'].'id'];

$curdir = (isset($params['curdir']) ? $params['curdir'].'/' : '');
$startdir = (isset($params['startdir']) ? $params['startdir'].'/' : '');
$destination = str_replace('//','/',$startdir.$curdir);

$allowedext = false;
if(isset($params['extensions']) && $params['extensions'] && $params['extensions'] != "")	$allowedext = explode(",",$params['extensions']);

$errors = "";
$zipfield = $id.'uploadzipfile';

if(isset($params["submitzipfile"])){
	
	$dir = $gCms->config["uploads_path"].str_replace("//","/","/".$destination."/");
	if($resize && !is_array($resize))	$resize = explode("x",$resize);
	
	if(isset($_FILES[$zipfield]) && isset($_FILES[$zipfield]['name']) && $_FILES[$zipfield]['name'] != ""){
		$extension = strtolower(strrchr($_FILES[$zipfield]['name'], "."));
		$za = new ZipArchive();
		if(strtolower($extension) == '.zip' && $za->open($_FILES[$zipfield]['tmp_name'])) {
			$i = 0;
			while($i < $za->numFiles) {
				$filestats = $za->statIndex($i);
				$filename = $filestats['name'];
				$extension = strtolower(substr(strrchr($filename, "."),1));
				if( $extension != "" && ( !$allowedext || in_array($extension, $allowedext) ) ){
					$filename = $this->upload_checkfilename($filename,$dir);
					$fp = fopen($dir.$filename, "w");
					if(fwrite($fp,$za->getFromIndex($i))){
						fclose($fp);
						chmod($dir.$filename, 0777);
						if($resize && count($resize) == 2) $this->plResize($dir.$filename, '', $resize[0], $resize[1],true,isset($params['crop']));
						$this->plAssignFile(str_replace("//","/","/".$destination."/".$filename), $params['tablename'], $itemid, $params['field'], $thumbsize, isset($params['cropthumb']));
					}else{
						fclose($fp);
						$errors = '<li>'.$filename.': '.lang('filenotuploaded').'</li>';
					}
				}else{
					$errors = '<li>'.$filename.': '.$this->Lang('error_wrongfiletype').'</li>';
				}
				$i++;
			}
		}else{
			$errors = '<li>'.$_FILES[$zipfield]['name'].': '.$this->Lang('error_invalidarchive').'</li>';
		}
	}
	
}else{
	
	foreach($_FILES as $fieldname=>$file){
		if($fieldname != $zipfield && isset($file['name']) && $file['name'] != ""){
			$extension = strtolower(substr(strrchr($file['name'], "."),1));
			if( !$allowedext || in_array($extension, $allowedext) ){
				if( $filepath = $this->plUploadFile($file, $destination, $resize, isset($params['crop'])) ){
					$this->plAssignFile($filepath, $params['tablename'], $itemid, $params['field'], $thumbsize, isset($params['cropthumb']));
				}else{
					$errors .= "<li>".$file['name'].': '.lang('filenotuploaded')."</li>";
				}
			}else{
				$errors = '<li>'.$file['name'].': '.$this->Lang('error_wrongfiletype').'</li>';
			}
		}
	}
	
}

$newparams = array( $params['prefix'].'id' => $itemid );
if(isset($params['active_tab'])) $newparams['active_tab'] = $params['active_tab'];
if($errors != "")	$newparams['module_message'] = '<ul>'.$errors.'</ul>';
$this->Redirect($id, 'edit'.$params['prefix'], $returnid, $newparams );		

?>
