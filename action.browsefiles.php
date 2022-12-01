<?php
## action.Browsefiles.php
## By: Pierre-Luc Germain
## PARAMS:
## $params['mode'] : images || files
## $params['extensions'] : array of shown file extensions
## $params['startdir'] : the folder to start at ( uploads/ [startdir] / )
## $params['tablename'] : the name of the db table
## $params['prefix'] : the variable prefix
## $params[prefix.'id'] : the id of the item to which the image will be assigned (value is not used, but returned at the end)
## $params['field'] : only a pass-through to the assign action

###################################################
# RETRIEVING PARAMS AND CONFIGS...
#
if (!isset($params['mode']) || !isset($params['field']) || !isset($params['prefix']) || !isset($params['field']) || !isset($params['tablename']) ) exit;

$hidethumbs = true; // will hide Image Manager thumbnails, but also hide all files that start with "thumb_"...
$theadmintheme = $gCms->variables["admintheme"];

## defining the allowed extensions:
$mediaext = array('swf','flv','jpeg','jpg','png','gif');
$docext = array('doc','docx','xls','xlsx','rtf','txt','pdf');

if($params['mode'] == 'images' || $params['mode'] == 'image') {
	$mode = 'image';
	$allowedext = $mediaext;
}else{
	$mode = 'other';
	$allowedext = array_merge($mediaext,$docext);
}
if(isset($params['extensions']) && $params['extensions'] && $params['extensions'] != "")	$allowedext = explode(",",$params['extensions']);
if($mode == 'image' && !$this->GetPreference("showthumbnails",true))	$mode = 'other';

$addfiles = ( isset($params['addfiles']) && $params['addfiles'] );



###################################################
# SOME FUNCTIONS...
#

function strlimit($string){
	// function to limit the length of the filenames
	if (substr($string,0,21) == $string){
		return $string;
	}else{
		return substr($string,0,19).'...';
	}
}
function get_thumbnail($baseurl, $filename, $dir, $returnarray=false){
	$thumbpath = $dir.DIRECTORY_SEPARATOR.$filename;
	$thumbname = $filename;
	if (file_exists($dir.DIRECTORY_SEPARATOR.'thumb_'.$filename)) {
		$thumbname = 'thumb_'.$filename;
	}elseif(file_exists($dir.DIRECTORY_SEPARATOR.'plthumb_'.$filename)){
		$thumbname = 'plthumb_'.$filename;
	}
	$imginf = getimagesize($dir.DIRECTORY_SEPARATOR.$thumbname);
	if(!$imginf || !isset($imginf[1]))	return false;
	list($width, $height) = $imginf;
	$thumbnailratio = max($width / 106,$height / 106);
	if ($thumbnailratio < 1) $thumbnailratio = 1;
	$theight = floor($height / $thumbnailratio);
	$twidth = floor($width / $thumbnailratio);
	if($returnarray){
		return array($baseurl.'/'.$thumbname, $twidth, $theight);
	}else{
		return '<img border=0 src="'.$baseurl.'/'.$thumbname.'" width="'.$twidth.'" height="'.$theight.'" />';
	}
}
function imagemode_buildlinkcontent($baseurl, $filename, $dir=''){
	// creates the content of the link in image mode
	$extension = strtolower(substr(strrchr($filename, "."), 1));
	if(in_array($extension, array('jpeg','jpg','png','gif'))){
		$thethumb = get_thumbnail($baseurl, $filename, $dir);
		return '<div style="text-align: center;">'.$thethumb.'</div>'.strlimit($filename);
	}
	global $gCms;
	$theadmintheme = $gCms->variables["admintheme"];
	if(in_array($extension, array('flv','swf','wmv','mov'))){
		$pic = $theadmintheme->DisplayImage("icons/filetypes/fmedia.gif");
	}elseif(in_array($extension, array('jpeg','jpg','png','gif'))){
		$pic = $theadmintheme->DisplayImage("icons/filetypes/fpaint.gif");
	}else{
		$pic = $theadmintheme->DisplayImage("icons/filetypes/fdoc.gif");
	}
	return '<div style="text-align: center;">'.$pic.'</div>'.strlimit($filename);
}

function makeDirObj($filename,$dir,$module,$params,$curdir,$parentdir,$id,$returnid){
	global $gCms;
	$theadmintheme = $gCms->variables["admintheme"];
	$parent = ($filename == '..');
	$obj = new StdClass();
	$obj->folder = true;
	$empty = array("ext","size","imagesize","deletelink","modified","owner","permissions");
	foreach($empty as $field)	$obj->$field = '';
	$obj->filename = $filename;
	$obj->pic = $theadmintheme->DisplayImage("icons/filetypes/".($parent?"up":"")."folder.gif");
	if($parent){
		$newparams = array_merge($params, array('curdir' => $parentdir));
		$obj->thelink = $module->CreateLink($id, 'browsefiles', $returnid, '..', $newparams);
	}else{
		$newparams = array_merge($params, array('curdir' => $curdir.'/'.$filename, 'parentdir' => $curdir));
		$obj->thelink = '<a style="display:none;">.'.$filename.'</a>'.$module->CreateLink($id, 'browsefiles', $returnid, $filename, $newparams);
	}
	return $obj;
}
function makeFileObj($filename,$dir,$module,$params,$curdir,$id,$returnid,$baseurl){
	global $gCms;
	$theadmintheme = $gCms->variables["admintheme"];
	$info = $module->plGetFileInfo($dir.DIRECTORY_SEPARATOR.$filename);
	$extension = $info['ext'];
	$obj = new StdClass();
	$obj->folder = false;
	$obj->filename = $filename;
	$obj->ext = $extension;
	$obj->size = '<a style="display:none;">'.$info["size"].'</a>'.$info["size_wformat"];
	$obj->pic = $theadmintheme->DisplayImage($info["fileicon"]);
	$obj->imagesize = '';
	$obj->viewlink = '';
	if($info["imagesize"]){
		$obj->imagesize = '<a style="display:none;">'.$info["width"].'</a>'.$info["imagesize"];
		$thethumb = get_thumbnail($baseurl, $filename, $dir, true);
		if($thethumb)	$obj->viewlink = ' <a href="'.$thethumb[0].'" title="'.$module->Lang("instantpreview").'" style="cursor: pointer;" onclick="return displaythumb(this,\''.$thethumb[0].'\','.$thethumb[1].','.$thethumb[2].');">'.$theadmintheme->DisplayImage("icons/system/view.gif").'</a>';
	}
	$obj->deletelink = $module->CreateLink($id, 'browsefiles', $returnid, $theadmintheme->DisplayImage("icons/system/delete.gif"),array_merge($params, array('deletefile'=>$filename))).$obj->viewlink;
	$newparams = array_merge($params, array('filename'=>$filename, 'filepath' => $curdir.'/'.$filename));
	$obj->thelink = $module->CreateLink($id, 'assignfile', $returnid, $filename, $newparams);
	$obj->modified = '<a style="display:none;">'.$info["filemtime"].'</a>'.$info["modified"];
	$obj->owner = $info["owner"];
	$obj->permissions = $info["permissions"];
	return $obj;
}






###################################################
# RETRIEVING CURRENT PATH...
#

$startdir = isset($params['startdir'])?$params['startdir']:'';
if($startdir != '' && substr($startdir,0,1) != '/' && substr($startdir,0,1) != '\\') $startdir = DIRECTORY_SEPARATOR.$startdir;
if(!file_exists($gCms->config['uploads_path'].$startdir)) @mkdir($gCms->config['uploads_path'].$startdir);

$parentdir = (isset($params['parentdir']) ? $params['parentdir'] : '');
$curdir = (isset($params['curdir']) ? $params['curdir'] : '');
$dir = $gCms->config['uploads_path'].$startdir.$curdir;
if(!is_dir($dir)){
	$dir = $gCms->config["uploads_path"];
	$curdir = '';
	$parentdir = '';
}
$baseurl = $gCms->config['uploads_url'].$startdir.$curdir;

if (isset($params['deletefile'])){
	// We're deleting a file
	if(file_exists($dir.'/'.$params['deletefile']) && unlink($dir.'/'.$params['deletefile'])){
		echo $this->ShowMessage($this->Lang("message_deleted"));
	}
}


###################################################
# PREPARING HIDDEN FIELDS FOR UPLOAD
#

$hidden = $this->CreateInputHidden($id,'curdir',$curdir).$this->CreateInputHidden($id,'field',$params['field']).$this->CreateInputHidden($id,'tablename',$params['tablename']).$this->CreateInputHidden($id,'mode',$params['mode']).$this->CreateInputHidden($id,'prefix',$params['prefix']).$this->CreateInputHidden($id,$params['prefix'].'id',$params[$params['prefix'].'id']);
if(isset($params['extensions'])) $hidden .= $this->CreateInputHidden($id,'extensions',$params['extensions']);
if(isset($params['startdir'])) $hidden .= $this->CreateInputHidden($id,'startdir',$params['startdir']);
if(isset($params['size'])) $hidden .= $this->CreateInputHidden($id,'size',$params['size']);
if(isset($params['thumb'])) $hidden .= $this->CreateInputHidden($id,'thumb',$params['thumb']);
if(isset($params['crop'])) $hidden .= $this->CreateInputHidden($id,'crop',$params['crop']);
if(isset($params['cropthumb'])) $hidden .= $this->CreateInputHidden($id,'cropthumb',$params['cropthumb']);
if(isset($params['addfiles'])) $hidden .= $this->CreateInputHidden($id,'addfiles',$params['addfiles']);
if(isset($params['active_tab'])) $hidden .= $this->CreateInputHidden($id,'active_tab',$params['active_tab']);



###################################################
# RETRIEVING FILES & FOLDERS
#

$dh  = opendir($dir);
$folders = array();
$files = array();
$upfolder = false;
while ($filename = readdir($dh)){
	if(is_dir($dir.DIRECTORY_SEPARATOR.$filename) && $filename!='.'){
			if ($filename=='..') {
				if ($curdir != '')		$upfolder = true;
			}else{
				$folders[] = $filename;
			}
	}else{
		$extension = strtolower(substr(strrchr($filename, "."), 1));
		if(in_array($extension, $allowedext) && (!$hidethumbs || substr($filename,0,6) != 'thumb_') && substr($filename,0,8) != 'plthumb_'){
			$files[] = $filename;
		}
	}
}
if (isset($dh))	closedir($dh);
sort($folders);
sort($files);




###################################################
# DISPLAYING...
#

echo '<div id="ctlmm_submitmessage" class="pagemcontainer" style="display: none;">'.$this->Lang("submitting_file").'</div>';

if($mode == 'image'){
	// image mode (with thumbnails) - no template
	echo '<h2>'.$this->Lang('browsefilestitle').'</h2>';
	echo '<p>'.$this->Lang('showingdir').' : '.$dir.'</p>';
	echo '<div id="fileselect" class="pageoverflow" style="padding: 4px; margin-bottom: 10px; border: 1px solid #666666; background: #FFFFFF; overflow: auto;"><div style="clear: both;">';
	$divoption = '<div style="text-align: center; float: left; width: 165px; height: 180px;"><br/>';
	$closediv = '</div>
';
	if($upfolder){
		echo $divoption;
		$newparams = array_merge($params, array('curdir' => $parentdir));
		echo $this->CreateLink($id, 'browsefiles', $returnid, $theadmintheme->DisplayImage("icons/filetypes/upfolder.gif").'<br/>'.$this->Lang('parentdir'), $newparams);
		echo $closediv;
	}
	foreach($folders as $filename){
		echo $divoption;
		$newparams = array_merge($params, array('curdir' => $curdir.'/'.$filename, 'parentdir' => $curdir));
		echo $this->CreateLink($id, 'browsefiles', $returnid, '<div style="text-align: center;"><img border=0 src="../lib/filemanager/ImageManager/img/folder.gif" /></div>'.$filename, $newparams);
		echo $closediv;
	}
	foreach($files as $filename){
		echo $divoption;
		$newparams = array_merge($params, array('filename'=>$filename, 'filepath' => $curdir.'/'.$filename));
		echo $this->CreateLink($id, 'assignfile', $returnid, imagemode_buildlinkcontent($baseurl, $filename, $dir), $newparams).'<br/>';
		$imginf = getimagesize($dir.DIRECTORY_SEPARATOR.$filename);
		if($imginf && isset($imginf[1]))	echo '('.$imginf[0].'x'.$imginf[1].') ';
		$newparams = array_merge($params, array('deletefile'=>$filename));
		echo $this->CreateLink($id, 'browsefiles', $returnid, $theadmintheme->DisplayImage("icons/system/delete.gif"),$newparams);
		echo $closediv;
	}
	echo '</div></div>';
	
	echo $this->CreateFormStart($id, 'addfiles', $returnid, 'post', 'multipart/form-data');
	echo '<h3>'.lang('uploadfile').':</h3><div>';
	if($addfiles){
		echo '<fieldset style="float: right;"><legend><b>'.$this->Lang("uploadzipfile").'</b></legend>';
		echo '<p>'.$this->Lang("zipfilenotice").'</p>';
		echo '<p>'.$this->CreateFileUploadInput($id,'uploadzipfile').' '.$this->CreateInputSubmit($id,'submitzipfile',lang('send')).'</p></fieldset>';
	}
	echo '<p>'.$this->Lang('postmaxsize').ini_get('post_max_size').'</p>';
	if(isset($params["size"]))	echo '<p>('.$this->Lang('browsefilesresize').')</p>';
	echo '<div id="fileinputs"><p>'.$this->CreateFileUploadInput($id,'uploadfile').'</p></div>';
	if($addfiles)	echo '<p><a href="javascript:addfileinput(\'fileinputs\',this);" style="cursor: pointer;">'.$this->Lang("addfileinput").'</a></p>';
	echo '<p>'.$this->CreateInputSubmit($id,'submit',lang('send')).'</p>';
	echo $hidden;
	echo '</div>';		
	echo $this->CreateFormEnd();
	
}else{
	// list mode (no thumbnails) - uses a template
	
	$instantsearch = $this->GetPreference("display_instantsearch",false);
	$instantsort = $this->GetPreference("display_instantsort",false);
	
	$this->smarty->assign("browsetitle",$this->Lang('browsefilestitle'));
	$this->smarty->assign("uploadtitle",lang('uploadfile'));
	$this->smarty->assign("showingdir",$this->Lang('showingdir').' : '.$dir);
	$this->smarty->assign("formstart",$this->CreateFormStart($id, 'addfiles', $returnid, 'post', 'multipart/form-data'));
	$this->smarty->assign("formend",$hidden.$this->CreateFormEnd());
	$this->smarty->assign("resizenotice",isset($params["size"])?$this->Lang('browsefilesresize'):false);
	$this->smarty->assign("postmaxsize",$this->Lang('postmaxsize').ini_get('post_max_size'));
	$this->smarty->assign("submit",$this->CreateInputSubmit($id,'submit',lang('send')));
	$this->smarty->assign("instantsearch", $instantsearch?$this->Lang("searchthistable")." ".$this->CreateInputText($id, "searchfiletable", "", 10, 64, ' onkeyup="ctlmm_search(this.value,\'filelist_table\');"'):false);

	$this->smarty->assign("ziptitle", $this->Lang("uploadzipfile"));
	$this->smarty->assign("zipnotice", $this->Lang("zipfilenotice"));
	$this->smarty->assign("zipinput", $this->CreateFileUploadInput($id,'uploadzipfile'));
	$this->smarty->assign("zipsubmit", $this->CreateInputSubmit($id,'submitzipfile',lang('send')));

	if($addfiles){
		$this->smarty->assign("fileinput",'<div id="fileinputs"><p>'.$this->CreateFileUploadInput($id,'uploadfile').'</p></div>');
		$this->smarty->assign("addfileinput",'<a href="javascript:addfileinput(\'fileinputs\',this);" style="cursor: pointer;">'.$this->Lang("addfileinput").'</a>');
	}else{
		$this->smarty->assign("fileinput",'<p>'.$this->CreateFileUploadInput($id,'uploadfile').'</p>');
		$this->smarty->assign("addfileinput",false);
	}
	
	$sortlinks = array();
	$numeric_sortlinks = array();
	$i = 0;
	while($i < 10){
		if($instantsort){
			$sortlinks[$i] = '<div style="float:left;"><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'filelist_table\','.$i.');"><img src="themes/default/images/icons/system/sort_up.gif" alt="^"/></a><br/><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'filelist_table\','.$i.',true);"><img src="themes/default/images/icons/system/sort_down.gif" alt="v"/></a></div> &nbsp;';
			$numeric_sortlinks[$i] = '<div style="float:left;"><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'filelist_table\','.$i.',false,true);"><img src="themes/default/images/icons/system/sort_up.gif" alt="^"/></a><br/><a style="cursor: pointer;" onclick="ctlmm_sortRows(\'filelist_table\','.$i.',true,true);"><img src="themes/default/images/icons/system/sort_down.gif" alt="v"/></a></div> &nbsp;';
		}else{
			$sortlinks[$i] = '';
			$numeric_sortlinks[$i] = '';
		}
		$i++;
	}
	$this->smarty->assign("sortlinks",$sortlinks);
	$this->smarty->assign("numeric_sortlinks",$numeric_sortlinks);
	
	$obj = new StdClass();
	$obj->folder = "";
	$obj->filename = $this->Lang("filename");
	$obj->ext = $this->Lang("fileext");
	$obj->size = $this->Lang("filesize");
	$obj->pic = "";
	$obj->imagesize = $this->Lang("imagesize");
	$obj->deletelink = $this->Lang("Actions");
	$obj->modified = $this->Lang("lastmod");
	$obj->owner = $this->Lang("fileowner");
	$obj->permissions = $this->Lang("fileperms");
	$this->smarty->assign("headers",$obj);
	
	$itemlist = array();
	if($upfolder)	$itemlist[] = makeDirObj("..",$dir,$this,$params,$curdir,$parentdir,$id,$returnid);
	foreach($folders as $folder)	$itemlist[] = makeDirObj($folder,$dir,$this,$params,$curdir,$parentdir,$id,$returnid);
	foreach($files as $filename)	$itemlist[] = makeFileObj($filename,$dir,$this,$params,$curdir,$id,$returnid,$baseurl);
	$this->smarty->assign("itemlist",$itemlist);
	
	echo '
	<script type="text/javascript">
';
	if($instantsearch)	echo $this->getFileContent("instantsearch.js");
	if($instantsort)	echo $this->getFileContent("instantsort.js");
	echo '
	</script>
';

	echo $this->ProcessTemplate("browsefiles.tpl");
}

echo '<script type="text/javascript">
var ctlmm_form = document.getElementById("'.$id.'moduleform_1");
if(ctlmm_form)	ctlmm_form.onsubmit = function() {
	document.getElementById("ctlmm_submitmessage").style.display = "block";
}
function displaythumb(ele,thumbpath,width,height){
	ele.style.display = "none";
	var theimg = new Image();
	theimg.width = width;
	theimg.height = height;
	theimg.src = thumbpath;
	theimg.style.cursor = "pointer";
	theimg.onclick = function() { hidethumb(this); };
	ele.parentNode.appendChild(theimg);
	return false;
}
function hidethumb(ele,thumbpath,width,height){
	var parent = ele.parentNode;
	parent.removeChild(ele);
	var theas = parent.getElementsByTagName("a");
	theas[theas.length-1].style.display = "inline";
}

var nbfileinput = 1;

function addfileinput(containerid, btn){
	var container = document.getElementById(containerid);
	if(!container) return false;
	nbfileinput++;
	var tmpinput = document.createElement("input");
	tmpinput.type = "file";
	tmpinput.name = "'.$id.'uploadfile"+nbfileinput;
	var thep = document.createElement("p");
	thep.appendChild(tmpinput);
	container.appendChild(thep);
	if(nbfileinput == 12)	btn.style.display = false;
}
</script>';
?>
