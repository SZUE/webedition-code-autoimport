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
/**
 * Form Elements
 */
.textinput {
	color				: #000000;
	border				: #AAAAAA solid 1px;
	height				: 14px;
	font-size			: <?php print ($System == "MAC") ? "11px" : (($System == "X11") ? "13px" : "12px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}

.textinput[disabled] {
	background-color	: #EEEEEE;
}

.textinputselected {
	color				: black;
	border				: #888888 solid 1px;
	background-color	: #DFDFDF;
	height				: 14px;
	font-size			: <?php print ($System == "MAC") ? "11px" : (($System == "X11") ? "13px" : "12px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}

.textarea {
	color				: black;
	border				: #AAAAAA solid 1px;
	height				: 80px;
	font-size			: <?php print ($System == "MAC") ? "11px" : (($System == "X11") ? "13px" : "12px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}

.textareaselected {
	color				: black;
	border				: #888888 solid 1px;
	background-color	: #DFDFDF;
	height				: 80px;
	font-size			: <?php print ($System == "MAC") ? "11px" : (($System == "X11") ? "13px" : "12px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
}