<?php

	include("../includes/constants.inc.php");
	header("Content-type: text/css");

?>
#leProgress {
	margin				: 4px 28px 4px 0px;
	float				: left;
	width				: 270px;
	display				: none;
}

#leProgressBackground {
	background			: url('../img/leProgressBar/background.gif');
	background-repeat	: repeat-x;
	width				: 230px;
}

#leProgressBar {
	margin-top			: 2px;
	background			: url('../img/leProgressBar/bar.gif');
	background-repeat	: repeat-x;
	width				: 0px;
	overflow			: hidden;
}

#leProgressPercent {
	width				: 40px;
	text-align			: right;
	font-size			: 9px;
	font-family			: verdana, arial, helvetica, sans-serif;
	font-weight			: bold;
}