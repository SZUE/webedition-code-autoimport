/* ================================================================
This copyright notice must be untouched at all times.

The original version of this scriptt and the associated (x)html
is available at http://www.stunicholls.com/menu/simple.html
Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This script and the associated (x)html may be modified in any
way to fit your requirements.
=================================================================== */

var menuActive = false;


function topMenuClick(){
	var getAgn = document.getElementById("nav").getElementsByTagName("DIV"); //
	/*
			// Variant 1: Mouseclick to change between main-menues
			for (var x=0; x<getAgn.length; x++) {
				getAgn[x].className=getAgn[x].className.replace("top_div unclick", "top_div");
				getAgn[x].className=getAgn[x].className.replace("top_div click", "top_div unclick");
			}
			if (this.className == 'top_div unclick') {
				this.className= 'top_div';
			}
			else {
				this.className = "top_div click";
			}
			*/

	// Variant 2: Change between main-menues without mouseclick
	var itemsState = (menuActive) ? "top_div" : "top_div click";
	menuActive = (menuActive) ? false : true;
	for (var x=0; x < getAgn.length; x++){
		getAgn[x].className = itemsState;
	}

}

function topMenuHover(){
	var left = document.getElementsByTagName("DIV")[0].getElementsByTagName("UL")[0].offsetLeft;
	if(left < -1000) {
		var getElsDivTop = document.getElementById("nav").getElementsByTagName("DIV");
		for (var x = 0; x < getElsDivTop.length; x++){
			getElsDivTop[x].className = "top_div";
		}
		menuActive = false;
	}

}
