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

.leContentTable {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 16px;
	width				: 295px;
	margin				: 0px;
	overflow			: auto;
	padding				: 5px;
}

table#requirementsLog {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 16px;
	width				: 295px;
	border				: 1px solid #cccccc;
	margin				: 0px;
	overflow			: auto;
	background			: #DFDFDF;
	padding				: 5px;
}

table#leSummary {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	line-height			: 16px;
	width				: 295px;
	border				: 1px solid #cccccc;
	margin				: 0px;
	overflow			: auto;
	background			: #DFDFDF;
	padding				: 5px;
}

table#leSummary td.left {
	width				: 145px;
	font-weight			: bold;
}

table#leSummary td.middle {
	width				: 5px;
}

table#leSummary input.right {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	width				: 145px;
	background			: #DFDFDF;
	border				: 0px solid #DFDFDF;
}


div#licenceAgreementDiv {
	font-size			: <?php print ($System == "MAC") ? "9px" : (($System == "X11") ? "11px" : "10px"); ?>;
	font-family			: Verdana, Arial, Helvetica, sans-serif;
	border				: 1px solid #cccccc;
	background			: white;
	width				: 293px;
	height				: 265px;
	padding				: 2px;
	overflow			: auto;
}