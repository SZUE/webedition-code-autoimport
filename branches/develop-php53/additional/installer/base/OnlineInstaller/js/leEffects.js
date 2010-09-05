function leEffect() {}

//
// ---> Public Static Methods
//

leEffect.switchTheme = function(startColor, endColor, Path, Title) {
	leEffect._startSwitchTheme(startColor, endColor, Path, Title);

}


leEffect.resizeWide = function(endWidth) {
	leEffect._startResizeWide(endWidth);

}


leEffect.resizeSmall = function(endWidth) {
	leEffect._startResizeSmall(endWidth);

}


//
// ---> Private Static Methods
//

//
// ---> Swicth Theme
//

leEffect._startSwitchTheme = function(startColor, endColor, Path, Title) {


	// Don't do this at the moment becaus of problems with safari and internet explorer
	//document.getElementById('leCSSForm').href = Path + '/css/leForm.css.php';
	//document.getElementById('leCSSContent').href = Path + '/css/leContent.css.php';
	
	preLoad = new Array();
	preLoad[0] = new Image();
	preLoad[0].src = Path + "/img/leLayout/logo.gif";
	//preLoad[1] = new Image();
	//preLoad[1].src = Path + "/img/leLayout/emoticon.gif";
	preLoad[1] = new Image();
	preLoad[1].src = Path + "/img/leLayout/product.gif";

	new Effect.Fade(document.getElementById('leTitle'), { duration: 0.5 });
	new Effect.Fade(document.getElementById('leProduct'), { duration: 0.4 });
	//new Effect.Fade(document.getElementById('leEmoticon'), { duration: 0.3 });
	new Effect.Fade(document.getElementById('leLogo'), { duration: 0.2 });
	leEffect._runSwitchTheme(startColor, endColor, Path, Title, 10);

}


leEffect._runSwitchTheme = function(startColor, endColor, Path, Title, Steps) {

	if(Steps >= 3) {

		redA = startColor.charAt(0) + startColor.charAt(1);
		red_valA = parseInt(redA,'16');
		redB = endColor.charAt(0) + endColor.charAt(1);
		red_valB = parseInt(redB,'16');
		red_int = ((red_valB - red_valA) / Steps) * -1;
		red = red_valA;
		red -= red_int;
		red_hex = leEffect._Dec2Hex(red);

		grnA = startColor.charAt(2) + startColor.charAt(3);
		grn_valA = parseInt(grnA,'16');
		grnB = endColor.charAt(2) + endColor.charAt(3);
		grn_valB = parseInt(grnB,'16');
		grn_int = ((grn_valB - grn_valA) / Steps) * -1;
		grn = grn_valA;
		grn -= grn_int;
		grn_hex = leEffect._Dec2Hex(grn);

		bluA = startColor.charAt(4) + startColor.charAt(5);
		blu_valA = parseInt(bluA,'16');
		bluB = endColor.charAt(4) + endColor.charAt(5);
		blu_valB = parseInt(bluB,'16');
		blu_int = ((blu_valB - blu_valA) / Steps) * -1;
		blu = blu_valA;
		blu -= blu_int;
		blu_hex = leEffect._Dec2Hex(blu);

		startColor = red_hex + grn_hex + blu_hex;
		document.getElementsByTagName('BODY')[0].style.backgroundColor = '#' + startColor;

		window.setTimeout(function() { leEffect._runSwitchTheme(startColor, endColor, Path, Title, --Steps); }, 1);

	} else {

		leEffect._finishSwitchTheme(startColor, endColor, Path, Title);

		new Effect.Appear(document.getElementById('leTitle'), { duration: 0.5 });
		window.setTimeout(function() { document.getElementById('leTitle').style.display = "block" }, 510);

		new Effect.Appear(document.getElementById('leProduct'), { duration: 0.4 });
		window.setTimeout(function() { document.getElementById('leProduct').style.display = "block" }, 410);

		//new Effect.Appear(document.getElementById('leEmoticon'), { duration: 0.5 });
		//window.setTimeout(function() { document.getElementById('leEmoticon').style.display = "block" }, 310);

		new Effect.Appear(document.getElementById('leLogo'), { duration: 0.2 });
		window.setTimeout(function() { document.getElementById('leLogo').style.display = "block" }, 210);

  	}


}


leEffect._finishSwitchTheme = function(startColor, endColor, Path, Title) {

	document.getElementById('leTitle').innerHTML = Title;
	document.getElementById('leLogoImg').src = Path + "/img/leLayout/logo.gif";
	//document.getElementById('leEmoticonImg').src = Path + "/img/leLayout/emoticon.gif";
	document.getElementById('leProductImg').src = Path + "/img/leLayout/product.gif";
	document.getElementsByTagName('BODY')[0].style.backgroundColor = '#' + endColor;

}

leEffect._Dec2Hex = function(Dec) {
	var a = Dec % 16;
	var b = (Dec - a)/16;
	var hexChars = "0123456789ABCDEF";
	hex = "" + hexChars.charAt(b) + hexChars.charAt(a);
	return hex;

}


//
// ---> Resize wide
//

leEffect._startResizeWide = function(endWidth) {

	leButton.disable('back');
	leButton.disable('next');

	window.setTimeout(function() { leEffect._runResizeWide(endWidth); }, 500);
	new Effect.Fade(document.getElementById('leStatus'), { duration: 0.3 });
	new Effect.Fade(document.getElementById('leProduct'), { duration: 0.3 });

	new Effect.Appear(document.getElementById('leCategories'), { duration: 1.5 });
	window.setTimeout(function() { document.getElementById('leCategories').style.display = "block" }, 310);

}


leEffect._runResizeWide = function(endWidth) {
	id = 'leContent';
	width = document.getElementById(id).offsetWidth - 22;
		if(width < endWidth) {
			resizePixel = 10;
			if(width - resizePixel > endWidth) {
				resizePixel = endWidth - width;

			}
			document.getElementById(id).style.width = (width + resizePixel) + 'px';

			window.setTimeout(function() { leEffect._runResizeWide(endWidth); }, 1);

		} else {
			leEffect._finishResizeWide();

		}
	return;
}


leEffect._finishResizeWide = function() {

	leButton.enable('back');
	leButton.enable('next');

}


//
// ---> Resize small
//

leEffect._startResizeSmall = function(endWidth) {

	leButton.disable('back');
	leButton.disable('next');

	new Effect.Fade(document.getElementById('leCategories'), { duration: 0.8 });

	leEffect._runResizeSmall(endWidth);
}


leEffect._runResizeSmall = function(endWidth) {
	id = 'leContent';
	width = document.getElementById(id).offsetWidth - 22;


		if(width > endWidth) {
			resizePixel = 10;
			if(width - resizePixel < endWidth) {
				resizePixel = width - endWidth;

			}
			document.getElementById(id).style.width = width - resizePixel + 'px';

			window.setTimeout(function() { leEffect._runResizeSmall(endWidth); }, 1);

		} else {
			leEffect._finishResizeSmall();

		}


}


leEffect._finishResizeSmall = function() {

	leButton.enable('back');
	leButton.enable('next');

	new Effect.Appear(document.getElementById('leStatus'), { duration: 0.3 });
	window.setTimeout(function() { document.getElementById('leStatus').style.display = "block" }, 310);

	new Effect.Appear(document.getElementById('leProduct'), { duration: 0.3 });
	window.setTimeout(function() { document.getElementById('leProduct').style.display = "block" }, 310);

}