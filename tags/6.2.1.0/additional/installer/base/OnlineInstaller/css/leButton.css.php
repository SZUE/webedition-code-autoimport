<?php

	include("../includes/constants.inc.php");
	header("Content-type: text/css");

?>
.leBtn,
.leBtnLeft,
.leBtnMiddle,
.leBtnRight,
.leBtnClicked,
.leBtnLeftClicked,
.leBtnMiddleClicked,
.leBtnRightClicked,
.leBtnDisabled,
.leBtnLeftDisabled,
.leBtnMiddleDisabled,
.leBtnRightDisabled {
	height				: 22px ! important;
	-moz-user-select	: none ! important;
}

.leBtn,
.leBtnDisabled,
.leBtnClicked {
	width				: auto;
	background-color	: transparent ! important;
}

.leBtnLeft,
.leBtnLeftDisabled,
.leBtnLeftClicked {
	width				: 11px ! important;
	background-position	: top left;
}

.leBtnMiddle,
.leBtnMiddleDisabled,
.leBtnMiddleClicked {
	width				: auto;
	background-position	: top;
}

.leBtnRight,
.leBtnRightDisabled,
.leBtnRightClicked {
	width				: 12px ! important;
	background-position	: top right;
}

.leBtn {
	cursor				: pointer;
}

.leBtnLeft {
	background-image	: url('../img/leButton/btn_normal_left.gif');
}

.leBtnImage {
	border				: 0;
}

.leBtnMiddle,
.leBtnMiddleClicked,
.leBtnMiddleDisabled {
	font-family			: verdana, arial, helvetica;
	background-repeat	: repeat-x;
	font-size			: 10px ! important;
	text-align			: center;
	padding-top			: 1px ! important;
}

.leBtnMiddle {
	background-image	: url('../img/leButton/btn_normal_middle.gif');
	color				: black ! important;
}

.leBtnRight {
	background-image	: url('../img/leButton/btn_normal_right.gif');
}

.leBtnLeftClicked {
	background-image	: url('../img/leButton/btn_clicked_left.gif');
}

.leBtnMiddleClicked {
	background-image	: url('../img/leButton/btn_clicked_middle.gif');
	color: black ! important;
}

.leBtnRightClicked {
	background-image	: url('../img/leButton/btn_clicked_right.gif');
}

.leBtnLeftDisabled {
	background-image	: url('../img/leButton/btn_disabled_left.gif');
}

.leBtnMiddleDisabled {
	background-image	: url('../img/leButton/btn_disabled_middle.gif');
	color				: gray ! important;
}

.leBtnRightDisabled {
	background-image	: url('../img/leButton/btn_disabled_right.gif');
}

.leBtnDisabled {
	cursor				: default ! important;
}

