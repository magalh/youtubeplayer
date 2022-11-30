<?php
// function plResize($fullpath, $newpath, $newwidth, $newheight=false, $transparency=false, $crop=false) {
	$name = str_replace('//','/',$fullpath);
	if(!file_exists($name))	return false;
	$image_infos = getimagesize($name);
	if($image_infos[0] != $newwidth || ($newheight != false && $image_infos[1] != $newheight)){
		$newname = ($newpath == ''?$fullpath:$newpath);
		$arr = split("\.",$name);
		$ext = strtolower($arr[count($arr)-1]);

		if($ext=="jpeg" || $ext=="jpg"){
			$img = @imagecreatefromjpeg($name);
		} elseif($ext=="png"){
			$img = @imagecreatefrompng($name);
		} elseif($ext=="gif") {
			$img = @imagecreatefromgif($name);
		}
		if(!$img)   return false;

		$old_x = imageSX($img);
		$old_y = imageSY($img);

		if($newheight && $crop){
			// find the wanted ratio
			$wantedratio = $newwidth/$newheight;
			$currentratio = $old_x/$old_y;
			if($currentratio > $wantedratio){
				// width is too large
				$crop_x = (int) ($old_y * $wantedratio);
				$crop_y = $old_y;
				$int_w = (int) (($old_x - $crop_x) / 2);
				$int_h = 0;
			}elseif($currentratio < $wantedratio){
				// height is too large
				$crop_x = $old_x;
				$crop_y = (int) ($old_x / $wantedratio);
				$int_h = (int) (($old_y - $crop_y) / 2);
				$int_w = 0;
			}
			if($currentratio != $wantedratio){
				// we first crop
				$dimg = ImageCreateTrueColor($crop_x, $crop_y);
				if(imagecopyresampled($dimg,$img,0,0,$int_w,$int_h,$crop_x,$crop_y,$crop_x,$crop_y)){
					imagedestroy($img);
					$img = $dimg;
				}
			}
			$old_x = $crop_x;
			$old_y = $crop_y;
			$thumb_h = $newheight;
			$thumb_w = $newwidth;
		}else{
			$ratio = $newheight?min($newwidth / $image_infos[0], $newheight / $image_infos[1]):($newwidth / $image_infos[0]);
			$thumb_h = $image_infos[1] * $ratio;
			$thumb_w = $image_infos[0] * $ratio;	
		}

		$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	   
		if($transparency) {
			if($ext=="png") {
				imagealphablending($new_img, false);
				$colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
				imagefill($new_img, 0, 0, $colorTransparent);
				imagesavealpha($new_img, true);
			} elseif($ext=="gif") {
				$trnprt_indx = imagecolortransparent($img);
				if ($trnprt_indx >= 0) {
					//its transparent
					$trnprt_color = imagecolorsforindex($img, $trnprt_indx);
					$trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
					imagefill($new_img, 0, 0, $trnprt_indx);
					imagecolortransparent($new_img, $trnprt_indx);
				}
			}
		} else {
			Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		}
		imagecopyresampled($new_img, $img, 0,0,0,0, $thumb_w, $thumb_h, $old_x, $old_y);

		if(file_exists($newname))	@unlink($newname);

		if($ext=="jpeg" || $ext=="jpg"){
			imagejpeg($new_img, $newname);
			$return = true;
		} elseif($ext=="png"){
			imagepng($new_img, $newname);
			$return = true;
		} elseif($ext=="gif") {
			imagegif($new_img, $newname);
			$return = true;
		}
		imagedestroy($new_img);
		imagedestroy($img);
		if($return) $return = $newname;
	}else{
		$return = false;
	}

?>
