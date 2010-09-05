<?php

	include("../includes/constants.inc.php");
	header("Content-type: text/css");

?>
ul {
	margin				: 2px 0px 2px 0px;
	padding-top			: 0px;
	padding-left		: 18px;
	line-height			: 14px;
	list-style			: none;
	font-size			: 11px;
}

li {
}

ul#leStatusBar {
	margin				: 0px;
	padding				: 0px;
}

ul#leStatusBar li {
	line-height			: 14px;
	padding				: 2px 2px 2px 0px;
	margin				: 0px;
}

li.leStatusUpcomingStep {
	color				: #aaaaaa;
}

ul li.leStatusUpcomingStep ul {
	display				: none;
}

ul li ul li.leStatusUpcomingStep {
	font-weight			: normal;
	list-style-image	: url("../img/leStatus/upcoming.gif");
}

li.leStatusFinishedStep {
	color				: black;
}

ul li.leStatusFinishedStep ul {
	display				: none;
}

ul li ul li.leStatusFinishedStep {
	font-weight			: normal;
	list-style-image	: url("../img/leStatus/finished.gif");
}

li.leStatusActiveStep {
	font-weight			: bold;
	display				: list-item;
}

ul li.leStatusActiveStep ul {
	display				: list-item;
}

ul li ul li.leStatusActiveStep {
	list-style-image	: url("../img/leStatus/active.gif") ! important;
}