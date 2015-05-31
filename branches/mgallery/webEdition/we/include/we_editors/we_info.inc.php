<?php
/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
$isInfoScreen = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === "info";

//	build table for login screen.
$_widthTotal = 432;
$_space = 15;
$_middlePart = ($_widthTotal - (2 * $_space));

// widths of loginTable
$_logoPart = 140;
$_leftPart = $_middlePart - $_logoPart;

$_credits = '<br /><span style="line-height:160%">' .
	g_l('global', '[developed_further_by]') . ': <a href="http://www.webedition.org/" target="_blank" ><strong>webEdition e.V.</strong></a><br/>' .
	g_l('global', '[with]') . ' <b><a href="http://credits.webedition.org/?language=' . $GLOBALS["WE_LANGUAGE"] . '" target="_blank" >' . g_l('global', '[credits_team]') . '</a></b></span><br/>';

$we_version = '';
if(!isset($GLOBALS['loginpage'])){
	$we_version .= ((defined('WE_VERSION_NAME') && WE_VERSION_NAME != '') ? WE_VERSION_NAME : WE_VERSION) . ' (' . WE_VERSION .
		((defined('WE_SVNREV') && WE_SVNREV != '0000') ? ', SVN-Revision: ' . WE_SVNREV : '') . ')' .
		((defined('WE_VERSION_SUPP') && WE_VERSION_SUPP != '') ? ' ' . g_l('global', '[' . WE_VERSION_SUPP . ']') : '') .
		((defined('WE_VERSION_SUPP_VERSION') && WE_VERSION_SUPP_VERSION != 0) ? WE_VERSION_SUPP_VERSION : '');
}

if(isset($GLOBALS['loginpage']) && WE_LOGIN_HIDEWESTATUS){

} else {
	switch(strtolower(WE_VERSION_SUPP)){
		case "rc":
			$extra = "RC";
			break;
		case "alpha":
			$extra = "ALPHA";
			break;
		case "beta":
			$extra = "BETA";
			break;
		case "nightly":
		case "weekly":
		case "nightly-build":
			$extra = "NIGHTLY";
			break;
		case "preview":
		case "dp":
			$extra = "PREVIEW";
			break;
		case "trunk":
		case "svn":
			$extra = "SVN";
			break;
	}
}

$_table = new we_html_table(array(
	"style" => "border-style:none; padding:0px;border-spacing:0px;background-color:#EBEBEB;width:" . $_widthTotal . 'px;' . (isset($GLOBALS['loginpage']) ? 'margin-left: auto; margin-right: auto;text-align:left;box-shadow: 5px 5px 5px #555555;border-radius:10px;' : '')), 8, 3);
$_actRow = 0;
//	First row with background
$_table->setCol($_actRow++, 0, array("colspan" => 3, "style" => 'width: ' . $_widthTotal . 'px;height:100px;',), '<a href="http://www.webedition.org" target="_blank"  title="www.webedition.org"><p style="text-align:center;height:100px;width:' . $_widthTotal . 'px"><img src="' . IMAGE_DIR . 'webedition.svg"/></p></a>' . (isset($extra) ? '<div style="position:relative;top:-60px;left:300px;text-align:left;font-weight:bolder;color:red;transform: rotate(-45deg);transform-origin: 0% 0%;">' . $extra . '</div>' : ''));

$_table->addRow(2);
if($we_version){
//	spaceholder
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 5));

//	3rd Version
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 1));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), "Version: " . $we_version);
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));
}

//	4th row with spaceholder
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 5));


//	5th credits
$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "defaultfont small"), $_credits);
$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));


//	7th agency
if(is_readable($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'agency.php')){
	include_once($_SERVER['DOCUMENT_ROOT'] . WEBEDITION_DIR . 'agency.php');
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 10));
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "defaultfont small"), $_agency);
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));
}

//	8th row
$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 10));


if(isset($GLOBALS["loginpage"]) && $GLOBALS["loginpage"]){

	$loginRow = 0;

	$_loginTable = new we_html_table(
		array("style" => "border-style:none; padding:0px;border-spacing:0px;"
		), 7, 2
	);

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart, "class" => "small"), we_html_baseElement::getHtmlCode(new we_html_baseElement("label", true, array("for" => "username"), g_l('global', '[username]'))));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::htmlTextInput('WE_LOGIN_username', 25, '', 255, 'id="username" style="width: 250px;" ', 'text', 0, 0));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::getPixel(5, 5));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart, "class" => "small"), we_html_baseElement::getHtmlCode(new we_html_baseElement("label", true, array("for" => 'password'), g_l('global', '[password]'))));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), we_html_tools::htmlTextInput('WE_LOGIN_password', 25, '', 255, 'id="password" style="width: 250px;" ', 'password', 0, 0));
	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart + $_logoPart, 'colspan' => 2), we_html_tools::getPixel(5, 5));


	$_table->addRow(4);
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array(), $_loginTable->getHtml());
	$_table->setCol($_actRow++, 2, array(), we_html_tools::getPixel($_space, 5));


	//	mode-table
	$_modetable = new we_html_table(array("style" => 'border-style:none; padding:0px;border-spacing:0px;', "width" => $_middlePart), 1, 3);

	$loginButton = we_html_button::create_button("fat:login,fa-lg fa-sign-in", "form:loginForm");
	if(!WE_SEEM){ //	deactivate See-Mode
		if(WE_LOGIN_WEWINDOW){
			$_modetable->setCol(0, 0, array(), '');
			$_modetable->setCol(0, 1, array("align" => "right", "valign" => "bottom", "rowspan" => 2), (WE_LOGIN_WEWINDOW == 1 ? '<input type="hidden" name="popup" value="popup"/>' : '') . $loginButton);
		} else {
			$_modetable->setCol(0, 0, array(), we_html_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]')));
			$_modetable->setCol(0, 1, array("align" => "right", "valign" => "bottom", "rowspan" => 2), we_html_element::htmlHidden("mode", "normal") . $loginButton);
		}
	} else { //	normal login
		//	15th Mode
		$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
		$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), (WE_SEEM ? g_l('SEEM', '[start_mode]') : ''));
		$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

		switch(WE_LOGIN_WEWINDOW){
			case 0:
				$we_login_type = we_html_forms::checkbox('popup', getValueLoginMode('popup'), 'popup', g_l('SEEM', '[popup]'));
				break;
			case 1:
				$we_login_type = '<input type="hidden" name="popup" value="popup"/>';
				break;
			default:
				$we_login_type = '';
		}

		// if button is between these radio boces, they can not be reachable with <tab>
		$_modetable->setCol(0, 0, array(), '<table style="border:0px;padding:0px;" cellspacing="0">
		<tr><td>' . $we_login_type . '</td></tr>' .
			'<tr><td>' .
			we_html_forms::radiobutton(we_base_constants::MODE_NORMAL, getValueLoginMode(we_base_constants::MODE_NORMAL), 'mode', g_l('SEEM', '[start_mode_normal]'), true, 'small') .
			'</td></tr>
		<tr><td>' . we_html_forms::radiobutton(we_base_constants::MODE_SEE, getValueLoginMode(we_base_constants::MODE_SEE), 'mode', '<abbr title="' . g_l('SEEM', '[start_mode_seem_acronym]') . '">' . g_l('SEEM', '[start_mode_seem]') . '</abbr>', true, "small") .
			'</td></tr>
		</table>');
		$_modetable->setCol(0, 1, array(
			'align' => 'right',
			'valign' => 'bottom',
			'rowspan' => 3), $loginButton);
	}

	//	16th
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), $_modetable->getHtml());
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	17th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 15));
} else if(isset($GLOBALS["loginpage"]) && !$GLOBALS["loginpage"]){

	srand((double) microtime() * 1000000);
	$r = rand();

	$loginRow = 0;

	$_content = g_l('global', '[loginok]');

	$_loginTable = new we_html_table(
		array("style" => 'border-style:none; padding:0px;border-spacing:0px;',
		), 2, 2
	);

	$_loginTable->setCol($loginRow, 0, array("width" => $_leftPart, "class" => "small"), $_content);

	$_table->addRow(4);

	//	9th Login ok
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small"), $_loginTable->getHtml());
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	10th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 5));
	//	11th back button
	$_table->setCol($_actRow, 0, array("width" => $_space), we_html_tools::getPixel($_space, 5));
	$_table->setCol($_actRow, 1, array("width" => $_middlePart, "class" => "small", "align" => "right"), we_html_button::create_button("back_to_login", WEBEDITION_DIR . "index.php?r=$r"));
	$_table->setCol($_actRow++, 2, array("width" => $_space), we_html_tools::getPixel($_space, 1));

	//	12th row
	$_table->setCol($_actRow++, 0, array("width" => $_widthTotal, "colspan" => 3), we_html_tools::getPixel($_widthTotal, 15));
} else if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === "info"){
	$_table->addRow();
	$_table->setCol($_actRow++, 0, array("colspan" => 3), we_html_tools::getPixel(2, 50));
}

if($isInfoScreen){
	echo $_table->getHtml();
} else {
	$_loginTable = $_table->getHtml();
}