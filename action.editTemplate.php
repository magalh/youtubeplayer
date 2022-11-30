<?php
if (!isset($gCms)) exit;

if( isset($params['cancel']) )	$this->Redirect($id, 'defaultadmin', $returnid, array('active_tab'=>'templates') );

$tplcontent = '';
if( ( isset($params['submit']) || isset($params['apply']) ) && isset($params['tplname']) ){
	if($params['tplname'] != ''){
		if(isset($params['isnew']))	$params['tplname'] = munge_string_to_url($params['tplname'], false);
		$tplcontent = $params['tplcontent'];
		if($this->SetTemplate($params['tplname'],$params['tplcontent'],$this->GetName()));
		if(isset($params['submit'])){
			$this->Redirect($id, 'defaultadmin', $returnid, array("module_message" => $this->Lang("message_modified"), 'active_tab'=>'templates') );
		}else{
			echo $this->ShowMessage($this->Lang("message_modified"));
		}
	}else{
		echo '<div class="pageerrorcontainer">'.$this->Lang('error_missginvalue').'</div>';
	}
}elseif( isset($params['tplname']) ){
	$tplcontent = $this->GetTemplate( $params['tplname'], $this->GetName() );
}

echo $this->StartTabHeaders();
	echo $this->SetTabHeader("template", $this->Lang("template"), true);
	echo $this->SetTabHeader("helptab", $this->Lang("templatevars"), false);		
echo $this->EndTabHeaders();

echo $this->StartTabContent();

	echo $this->StartTab("template");
		echo '<h1>'.$this->Lang("edittemplate").'</h1>';
		echo $this->CreateFormStart($id, "editTemplate", $returnid);
		//echo '<p>'.$this->CreateInputSubmit($id, "submit", lang("submit")).' '.$this->CreateInputSubmit($id, "apply", lang("apply")).' '.$this->CreateInputSubmit($id, "cancel", lang("cancel")).'</p><br/><br/>';
		echo '	<div class="pageoverflow">
				<p class="pagetext">'.$this->Lang('name').' :</p>
				<p class="pageinput">';
		if(isset($params['tplname']) && $params['tplname'] != ''){
		    echo $params['tplname'].$this->CreateInputHidden($id, "tplname", $params['tplname']);
		}else{
		    echo $this->CreateInputText($id,'tplname','',30,64);
		}
		echo '</p>
		    </div>
		';
		echo '	<div class="pageoverflow">
				<p class="pagetext">'.$this->Lang('template').' :</p>
				<p class="pageinput">'.$this->CreateTextArea(false,$id,$tplcontent,'tplcontent').'</p>
		    </div>
		';
		echo '<p>'.$this->CreateInputSubmit($id, "submit", lang("submit")).' '.$this->CreateInputSubmit($id, "apply", lang("apply")).' '.$this->CreateInputSubmit($id, "cancel", lang("cancel")).'</p>';
		if(!isset($params["tplname"]) || $params["tplname"] == '')	echo $this->CreateInputHidden($id, "isnew", "yes");
		echo $this->CreateFormEnd();
	echo $this->EndTab();

	echo $this->StartTab("helptab");
		echo '<div id="templatehelp">'.$this->Lang('templatehelp').'</div>';
		echo '
<script type="text/javascript">
	function ctlmm_displaytoggle(ele){
		if(!ele || !ele.parentNode)	return false;
		var thediv = ele.parentNode.getElementsByTagName("div")[0];
		if(!thediv)	return false;
		if(thediv.style.display == "none"){
			thediv.style.display = "block";
			ele.innerHTML = "V" + ele.innerHTML.substr(4);
		}else{
			thediv.style.display = "none";
			ele.innerHTML = "&gt;" + ele.innerHTML.substr(1);
		}
	}
	var thedivs = document.getElementById("templatehelp").getElementsByTagName("div");
	for(i=0;i<thedivs.length;i++){
		if(thedivs[i].className == "tplvars_hide")	thedivs[i].style.display = "none";
	}
</script>
';
	echo $this->EndTab();

echo $this->EndTabContent();

?>
