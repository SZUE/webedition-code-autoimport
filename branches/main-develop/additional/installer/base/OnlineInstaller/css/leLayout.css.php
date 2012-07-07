<?php

	include("../includes/constants.inc.php");
	header("Content-type: text/css");


	if(stristr($_SERVER['HTTP_USER_AGENT'], 'X11')) {
		$System = "X11";
	} else if(stristr($_SERVER['HTTP_USER_AGENT'], 'Win')) {
		$System = "WIN";
	} else if(stristr($_SERVER['HTTP_USER_AGENT'], 'Mac')) {
		$System = "MAC";
	} else {
		$System = "UNKNOWN";
	}
	

?>
body {
	background-color	: #007abd;
	padding				: 0px;
	margin				: 0px;
	font-size			: 12px;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}

#debug {
	font-family			: courier,monospace;
	font-size			: 8pt;
	position			: absolute;
	height				: 100px;
	width				: 100%;
	margin				: 0px;
	top					: 24px;
	left				: 0px;
	overflow			: hidden;
	display				: block;
}

#leToolbar {
	position			: absolute;
	height				: 24px;
	width				: 100%;
	margin				: 0px;
	top					: 0px;
	left				: 0px;
	overflow			: hidden;
	display				: block;
	background			: #ffffff  url('<?php print LE_INSTALLER_URL; ?>/../img/leLayout/toolbar.gif') top left repeat-x;
}

#leInstaller {
	position			: absolute;
	height				: 532px;
	width				: 640px;
	margin				: -239px 0px 0px -320px;
	top					: 50%;
	left				: 50%;
	overflow			: hidden;
}

#phpinfo {
	position			: absolute;
	height				: 460px;
	width				: 640px;
	margin				: -174px 0px 0px -260px;
	top					: 50%;
	left				: 50%;
	overflow			: hidden;
	z-index				: 1000;
}

#leLogo {
	position			: absolute;
	height				: 50px;
	width				: 120px;
	margin				: -294px 0px 0px -379px;
	top					: 50%;
	left				: 50%;
	overflow			: hidden;
	display				: block;
}

#leCategories {
	filter				: alpha(opacity=0);
	-moz-opacity		: 0.0;
	overflow			: hidden;
	position			: absolute;
	height				: 532px;
	width				: 110px;
	margin				: -239px 0px 0px -379px;
	top					: 50%;
	left				: 50%;
	text-align			: center;
	display				: none;
}

#leCategories img {
	padding				: 20px 0px 0px 0px;
}

#leLeft {
	float 				: left;
	width				: 9px;
	height				: 534px;
	background-image	: url('<?php print LE_INSTALLER_URL; ?>/../img/leLayout/left.gif');
}

#leRight {
	float 				: right;
	width				: 9px;
	height				: 532px;
	background-image	: url('<?php print LE_INSTALLER_URL; ?>/../img/leLayout/right.gif');
}

#leCenter {
	padding				: 10px 22px 10px 22px;
	float 				: right;
	width				: 578px;
	height				: 532px;
	background-image	: url('<?php print LE_INSTALLER_URL; ?>/../img/leLayout/bg.gif');
}

#leHead {
	margin				: 0px;
	padding				: 0px;
	width				: 578px;
	height				: 51px;
	line-height			: 51px;
	font-weight			: bold;
	vertical-align		: bottom;
	overflow			: hidden;
}

#leTitle {
	float				: left;
	width				: 500px;
	height				: 51px;
	overflow			: hidden;
	display				: block;
}

#leEmoticon {
	float				: right;
	width				: 75px;
	height				: 51px;
	text-align			: right;
	overflow			: hidden;
	display				: block;
}

#leMain {
	margin				: 0px 0px 12px 0px;
	padding				: 12px 0px 0px 0px;
	float 				: right;
	width				: 578px;
	height				: 397px;
	border-width		: 0px 0px 0px 0px;
	border-style		: solid none none none;
	border-color		: #000000;
}

#leStatus {
	float				: left;
	margin				: 0px;
	padding				: 0px;
	width				: 240px;
	height				: 330px;
	overflow			: hidden;
}

#leProduct {
	float				: right;
	margin				: 0px;
	padding				: 0px;
	width				: 240px;
	height				: 67px;
	overflow			: hidden;
	text-align			: left;
	overflow			: hidden;
}

#leContent {
	float				: right;
	margin				: 0px;
	padding				: 10px;
	width				: 316px;
	height				: 375px;
	background-color	: #efefef;
	border-width		: 1px;
	border-style		: solid;
	border-color		: #cccccc;
	overflow			: auto;
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
}

#leFoot {
	margin				: 0px 0px 0px 0px;
	padding				: 0px;
	float 				: right;
	width				: 578px;
}

#next_table {
	float				: left;
	margin				: 0px 20px 0px 0px;
}

#back_table {
	float				: left;
	margin				: 0px 20px 0px 0px;
}

#reload_table {
	padding				: 0px;
	margin				: 0px;
	float				: right;
}

#print_table {
	padding				: 0px;
	margin				: 0px;
	float				: right;
}



/**
 *	Fonts
 */
.defaultfont {
	color				: #000000;
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
}

#leContent a,
.defaultfont a {
	color				: #000000;
}

#leContent  a:visited,
.defaultfont a:visited {
	color				: #000000;
}

#leContent  a:active,
.defaultfont a:active {
	color				: #006DB8;
}

#leContent h1 {
	font-size			: <?php print ($System == "MAC") ? "11px" : (($System == "X11") ? "13px" : "12px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	font-weight			: bold;
	line-height			: <?php print ($System == "MAC") ? "17px" : (($System == "X11") ? "19px" : "18px"); ?>;
	margin-top			: 4px;
}

#leContent ul {
	margin-top			: 4px;
	margin-bottom		: 4px;
	list-style-type		: circle;
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
}

#leContent li {

}


#leContent h1.error {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	color				: #ff0000;
	font-weight			: normal;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
	margin-top			: 4px;
}

#leContent h1.notice {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	color				: #ffb55f;
	font-weight			: normal;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
	margin-top			: 4px;
}

#leContent label {
	vertical-align		: top;
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
}

#leContent p {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
}

#leContent p.message {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: <?php print ($System == "MAC") ? "15px" : (($System == "X11") ? "17px" : "16px"); ?>;
	color				: #ff0000;
}
