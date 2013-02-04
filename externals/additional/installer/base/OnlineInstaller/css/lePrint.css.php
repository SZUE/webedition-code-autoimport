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
	
	$BROWSER = "";
	if(stristr($_SERVER['HTTP_USER_AGENT'], 'safari')) {
		$BROWSER = "SAFARI";
	}
	

?>
body {
	background-color	: #ffffff ! important;
	font-size			: 11pt;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	width				: 95%;
}

#leInstaller {
}

#leLogo {
	display				: none !important;
}

#leCategories {
	display				: none !important;
}

#leLeft {
	display				: none;
}

#leRight {
	display				: none;
}

#leCenter {
	background-color	: #ffffff;
	display				: block;
}

#leHead {
	display				: none;
}

#leTitle {
	display				: none !important;
}

#leEmoticon {
	display				: none !important;
}

#leMain {
	background-color	: #ffffff;
	display				: block;
}

#leStatus {
	display				: none !important;
	width				: 0px;
	height				: 0px;
	overflow			: hidden;
}

#leProduct {
	display				: none !important;
	width				: 0px;
	height				: 0px;
	overflow			: hidden;
}

#leContent {
	background-color	: #ffffff;
<?php if($BROWSER == "SAFARI") { ?>
	position			: absolute;
<?php } else { ?>
	position			: relative;
<?php } ?>
	top					: 0pt;
	left				: 0pt;
	display				: block;
}

#leFoot {
	display				: none;
}

#next_table {
	display				: none;
}

#back_table {
	display				: none;
}

#reload_table {
	display				: none;
}



/**
 *	Fonts
 */
.defaultfont {
	color				: #000000;
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}

.defaultfont a {
	color				: #000000;
}

.defaultfont a:visited {
	color				: #000000;
}

.defaultfont a:active {
	color				: #006DB8;
}

#leContent h1 {
	font-size			: <?php print ($System == "MAC") ? "10pt" : (($System == "X11") ? "12pt" : "11pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	font-weight			: bold;
	margin-top			: 3pt;
}

#leContent h1.error {
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	color				: #ff0000;
	font-weight			: normal;
	margin-top			: 3pt;
}

#leContent label, p {
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 15pt;
}

#leContent p.message {
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 15pt;
	color				: #ff0000;
}


table#requirementsLog{
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 13pt;
	width				: 470pt;
	border				: 1pt solid #cccccc;
	margin				: 0pt;
	overflow			: auto;
	background			: #DFDFDF;
	padding				: 5pt;
}

table#leSummary {
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 13pt;
	width				: 474pt;
	border				: 1pt solid #cccccc;
	margin				: 0pt;
	overflow			: auto;
	background			: #ffffff;
	padding				: 3pt;
}

table#leSummary td.left {
	width				: 140pt;
	font-weight			: bold;
}

table#leSummary td.middle {
	width				: 5pt;
}

table#leSummary input.right {
	font-size			: <?php print ($System == "MAC") ? "8pt" : (($System == "X11") ? "10pt" : "9pt"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	width				: 325pt;
	background			: #efefef;
	border				: 0pt solid #DFDFDF;
}

div#licenceAgreementDiv {
	overflow			: visible;
}
