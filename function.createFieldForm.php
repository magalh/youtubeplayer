
<?php
		$comparechoices = array($this->Lang("contains") =>1, $this->Lang("isexactly") =>0, $this->Lang("isnot") =>2, $this->Lang("ishigherthan") =>3, $this->Lang("islowerthan") =>4);
		$datecomparechoices = array($this->Lang("queryuse") => "NA", $this->Lang("isafter") =>3, $this->Lang("isbefore") =>4, $this->Lang("isbetween") =>5);
		$orderchoices = array("ASC"=>"ASC","DESC"=>"DESC");

		$output = array();

		switch($what){
			case "category":
				$output["description"] = array($this->Lang("category_description"), $this->CreateInputDropdown($id,"compare_description",$comparechoices,-1,1)." ".$this->CreateInputText($id,"field_description","",30));
				$output["name"] = array($this->Lang("name"), $this->CreateInputDropdown($id,"compare_name",$comparechoices,-1,1)." ".$this->CreateInputText($id,"field_name","",30));
				
				$this->smarty->assign("tmptime", time());
				$this->smarty->assign("s_and", $this->Lang("thisandthis"));
				$this->smarty->assign("tmp_prefix", $id."date_date_modified_");
				$this->smarty->assign("tmp_prefix2", $id."date_date_modified_part2_");
				$tmptemplate = '{html_select_date prefix=$tmp_prefix time=$tmptime start_year="-10" end_year="+10"} {html_select_time prefix=$tmp_prefix time=$tmptime }<div id="'.$id.'date_modified_part2" style="display: none;"> {$s_and} {html_select_date prefix=$tmp_prefix2 time=$tmptime start_year="-10" end_year="+10"} {html_select_time prefix=$tmp_prefix2 time=$tmptime }</div>';
				$tmpout = $this->CreateInputDropdown($id,"compare_date_modified",$datecomparechoices,-1,"NA"," onchange=\"dateinput_changecompare('".$id."date_modified', this.value);\"")." ".$this->ProcessTemplateFromData($tmptemplate);
				$tmpout .= $this->CreateInputHidden($id,"field_date_modified", "__date_field");
				$output["date_modified"] = array($this->Lang("date_modified"), $tmpout);
				$orderfieldoptions = array(""=>"","description"=>"description","id"=>"id","name"=>"name","alias"=>"alias","item_order"=>"item_order","date_modified"=>"date_modified");
				$output["orderby"] = array($this->Lang("orderbyfield"), $this->CreateInputDropdown($id,"order",$orderfieldoptions)." ".$this->CreateInputDropdown($id,"order_type",$orderchoices));
				break;
			case "videos":
				$output["videoid"] = array($this->Lang("videos_videoid"), $this->CreateInputDropdown($id,"compare_videoid",$comparechoices,-1,1)." ".$this->CreateInputText($id,"field_videoid","",30));
				$output["description"] = array($this->Lang("videos_description"), $this->CreateInputDropdown($id,"compare_description",$comparechoices,-1,1)." ".$this->CreateInputText($id,"field_description","",30));
				$output["parent"] = array($this->Lang("parent"));
				$parentoptions = $this->get_hierarchyoptions("videos");
				$output["parent"][] = $this->CreateInputDropdown($id,"field_parent",$parentoptions);
				$output["name"] = array($this->Lang("name"), $this->CreateInputDropdown($id,"compare_name",$comparechoices,-1,1)." ".$this->CreateInputText($id,"field_name","",30));
				
				$this->smarty->assign("tmptime", time());
				$this->smarty->assign("s_and", $this->Lang("thisandthis"));
				$this->smarty->assign("tmp_prefix", $id."date_date_modified_");
				$this->smarty->assign("tmp_prefix2", $id."date_date_modified_part2_");
				$tmptemplate = '{html_select_date prefix=$tmp_prefix time=$tmptime start_year="-10" end_year="+10"} {html_select_time prefix=$tmp_prefix time=$tmptime }<div id="'.$id.'date_modified_part2" style="display: none;"> {$s_and} {html_select_date prefix=$tmp_prefix2 time=$tmptime start_year="-10" end_year="+10"} {html_select_time prefix=$tmp_prefix2 time=$tmptime }</div>';
				$tmpout = $this->CreateInputDropdown($id,"compare_date_modified",$datecomparechoices,-1,"NA"," onchange=\"dateinput_changecompare('".$id."date_modified', this.value);\"")." ".$this->ProcessTemplateFromData($tmptemplate);
				$tmpout .= $this->CreateInputHidden($id,"field_date_modified", "__date_field");
				$output["date_modified"] = array($this->Lang("date_modified"), $tmpout);
				$orderfieldoptions = array(""=>"","videoid"=>"videoid","description"=>"description","parent"=>"parent","id"=>"id","name"=>"name","alias"=>"alias","item_order"=>"item_order","date_modified"=>"date_modified");
				$output["orderby"] = array($this->Lang("orderbyfield"), $this->CreateInputDropdown($id,"order",$orderfieldoptions)." ".$this->CreateInputDropdown($id,"order_type",$orderchoices));
				break;
			
		}
		echo '<script type="text/javascript">
	function dateinput_changecompare(fieldname, newvalue){
		if(newvalue == 5){
			var newstate = "inline";
		}else{
			var newstate = "none";
		}
		if(document.getElementById(fieldname+"_part2"))	document.getElementById(fieldname+"_part2").style.display = newstate;
		return true;
	}
</script>
';
		if($assign){
			foreach($output as $key=>$value){
				$this->smarty->assign($key."_label", $value[0]);
				$this->smarty->assign($key."_input", $value[1]);
			}
		}else{
			echo "<table style=\"border-left: 1px solid LightGray; padding-left: 10px;\">";
			foreach($output as $key=>$value){
				if($key != "orderby")	echo "
				<tr><td>".$value[0]."</td><td>".$value[1]."</td></tr>";
			}
				echo "
			</table><br/>";
			echo "<p>".$output["orderby"][0].": ".$output["orderby"][1]."</p>";
		}
?>