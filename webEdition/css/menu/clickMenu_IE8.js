/* ================================================================
This copyright notice must be untouched at all times.

The original version of this scriptt and the associated (x)html
is available at http://www.stunicholls.com/menu/simple.html
Copyright (c) 2005-2007 Stu Nicholls. All rights reserved.
This script and the associated (x)html may be modified in any
way to fit your requirements.
=================================================================== */

var menuActive = false;

function topMenuClick(elem){
	menuActive = (menuActive) ? false : true;
	if(menuActive){ 
		elem.className = "top_div click";
	} else{
		elem.className = "top_div";
	}
}

function topMenuHover(elem){
	var left = elem.getElementsByTagName("DIV")[0].getElementsByTagName("UL")[0].offsetLeft;
	if(left < -1000) {
		var elemsDivTop = document.getElementById("nav").getElementsByTagName("DIV");
		for (var x = 0; x < elemsDivTop.length; x++){
			elemsDivTop[x].className = "top_div";
		}
		menuActive = false;
	}
}
