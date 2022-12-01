function ctlmm_sortRows(tableid,colindex,desc,numeric){
	// tableid is the id of the table to sort
	// colindex is against which column to sort (0-> first, 1-> second...)
	// desc makes the sort descending
	// numeric makes the sort numeric
	if(!document.getElementById(tableid))	return false;
	var therows = document.getElementById(tableid).getElementsByTagName("tbody")[0].getElementsByTagName("tr");
	if(!therows[0] || !therows[0].cells[colindex])	return false;
	
	var sorted = new Array();
	for(i=0; i<therows.length; i++){
		var theas = therows[i].cells[colindex].getElementsByTagName("a");
		if(theas[0]){
			var sortdata = theas[0].innerHTML;
		}else{
			var sortdata = therows[i].cells[colindex].innerHTML;
		}
		sorted.push([sortdata, therows[i]]);
		therows[i].parentNode.removeChild(therows[i]);
		i--;
	}
	if(numeric){
		if(desc){
			sorted.sort(function(a,b){ return b[0]-a[0]; });
		}else{
			sorted.sort(function(a,b){ return a[0]-b[0]; });
		}
	}else{
		if(desc){
			sorted.sort(function(a,b){ return a[0].toLowerCase() < b[0].toLowerCase(); });
		}else{
			sorted.sort(function(a,b){ return a[0].toLowerCase() > b[0].toLowerCase(); });
		}
	}

	var tablebody = document.getElementById(tableid).getElementsByTagName("tbody")[0];
	var even = true;
	for(i=0; i<sorted.length; i++){
		even = !even;
		var theclass = (even?"row2":"row1");
		sorted[i][1].className = theclass;
		sorted[i][1].onmouseover = function(){ this.className += "hover"; };
		sorted[i][1].onmouseout = function(){ this.className = this.className.substr(0,4); };
		tablebody.appendChild(sorted[i][1]);
	}
}
