<?php

	include("../includes/constants.inc.php");
	header("Content-type: text/css");


	if(eregi("X11",$_SERVER["HTTP_USER_AGENT"])) {

		$System = "X11";
	} else if(eregi("Win",$_SERVER["HTTP_USER_AGENT"])) {
		$System = "WIN";

	} else if(eregi("Mac",$_SERVER["HTTP_USER_AGENT"])) {
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