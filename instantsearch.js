function ctlmm_search(what,inwhat){
	var destination = document.getElementById(inwhat);
	if(!destination) return;
	var even = false;
	var therows = destination.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
	for(i=0;i<therows.length;i++){
		var theclass = (even?"row2":"row1");
		var newstyle = "none";
		var thecolumns = therows[i].getElementsByTagName("td");
		for(j=0;j<thecolumns.length;j++){
			if(thecolumns[j].className != "ctlmm_nosearch"){
				var tmpa = thecolumns[j].getElementsByTagName("a")[0];
				if(tmpa && tmpa.innerHTML.toLowerCase().indexOf(what.toLowerCase()) >= 0)	newstyle = "table-row";
				if(!tmpa && thecolumns[j].innerHTML.toLowerCase().indexOf(what.toLowerCase()) >= 0)		newstyle = "table-row";
			}
		}
		therows[i].style.display = newstyle;
		if(newstyle != "none"){
			therows[i].className = theclass;
			therows[i].onmouseover = function(){ this.className += "hover"; };
			therows[i].onmouseout = function(){ this.className = this.className.substr(0,4); };
			even = !even;
		}
	}
}
