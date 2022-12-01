<?php
	if(!isset($filepath))	exit;
	
	$info = array();
	
	$info["filename"] = basename($filepath);
	
	$size = filesize($filepath);
	$info["size"] = $size;
	if($size > 1073741824){
		$size = round(($size/1073741824),2).' GB';
	}elseif($size > 1048576){
		$size = round(($size/1048576),2).' MB';
	}elseif($size > 1024){
		$size = round(($size/1024),2).' KB';
	}else{
		$size .= ' bytes';
	}
	$info["size_wformat"] = $size;
	
	$extension = strtolower(substr(strrchr($filepath, "."), 1));
	$info["ext"] = $extension;
	if(in_array($extension, array('flv','swf','wmv','mov'))){
		$info["fileicon"] = "icons/filetypes/fmedia.gif";
	}elseif(in_array($extension, array('jpeg','jpg','png','gif'))){
		$info["fileicon"] = "icons/filetypes/fpaint.gif";
	}else{
		$info["fileicon"] = "icons/filetypes/fdoc.gif";
	}
	
	$info["imagesize"] = false;
	$imginf = getimagesize($filepath);
	if(isset($imginf[1])){
		$info["width"] = $imginf[0];
		$info["height"] = $imginf[1];
		$info["imagesize"] = $imginf[0].'x'.$imginf[1];
		if(isset($imginf["mime"]))	$info["mime"] = $imginf["mime"];
	}
	
	$info["owner"] = fileowner($filepath);
	$info["permissions"] = fileperms($filepath);
	
	$info["filemtime"] = filemtime($filepath);
	$info["modified"] = date("Y-m-d G:i", $info["filemtime"]);
	
?>
