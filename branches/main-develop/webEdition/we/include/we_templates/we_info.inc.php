<?php
/**
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */


include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_htmlTable.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_forms.inc.php");


//	build table for login screen.
$_widthTotal = 432;
$_space      = 15;
$_middlePart = ($_widthTotal - (2 * $_space));

// widths of loginTable
$_logoPart = 140;
$_leftPart = $_middlePart - $_logoPart;

$_credits = '<br /><span style="line-height:160%">'
			.g_l('global','[developed_further_by]').': <a href="http://www.webedition.org/" target="_blank" ><strong>webEdition e.V.</strong></a><br/>'
			.g_l('global','[with]').' <b><a href="http://credits.webedition.org/?version='.str_replace(".","",WE_VERSION).'&language='.$GLOBALS["WE_LANGUAGE"].'" target="_blank" >'.g_l('global','[credits_team]').'</a></b></span><br/>';


$we_version = preg_replace('/\.0$/','', WE_VERSION);
if(defined("WE_SVNREV") && WE_SVNREV!='0000' && !isset($GLOBALS["loginpage"])) $we_version .= " (SVN-Revision: ".WE_SVNREV.")";
if(defined("WE_VERSION_SUPP") && WE_VERSION_SUPP!='' && !isset($GLOBALS["loginpage"]) ) $we_version .= ' '.g_l('global','['.WE_VERSION_SUPP.']');
if(defined("WE_VERSION_SUPP_VERSION") && WE_VERSION_SUPP_VERSION!='0' && !isset($GLOBALS["loginpage"]) ) $we_version .= WE_VERSION_SUPP_VERSION;

$_logo = "info.jpg";
if(defined("WE_VERSION_SUPP")) {
	switch(strtolower(WE_VERSION_SUPP)) {
		case "rc":
			$_logo = "info_rc.jpg";
			break;
		case "alpha":
			$_logo = "info_alpha.jpg";
			break;
		case "beta":
			$_logo = "info_beta.jpg";
			break;
		case "nightly":
		case "weekly":
		case "nightly-build":
			$_logo = "info_nightly.jpg";
			break;
		case "preview":
		case "dp":
			$_logo = "info_preview.jpg";
			break;
		case "trunk":
		case "svn":
			$_logo = "info_svn.jpg";
			break;
		default:
			$_logo = "info.jpg";
			break;
	}
}

$_table = new we_htmlTable( array(	"border"      => 0,
									"cellpadding" => 0,
									"cellspacing" => 0,
									"style"  => "background-image:url(" . IMAGE_DIR . "info/".$_logo."?we=".str_replace(".","",WE_VERSION).");background-repeat: no-repeat;background-color:#EBEBEB" ),
									8,
									3);
$_actRow = 0;
//	First row with background
$_table->setCol($_actRow++,0, array(	"colspan" => 3,
							"width"   => $_widthTotal,
							"height"  => 110), '<a href="http://www.webedition.org" target="_blank"  title="www.webedition.org">'.getPixel($_widthTotal,110,0).'</a><br /><div class="defaultfont small" style="text-align:center;">Open Source Content Management</div>');

$_table->addRow(2);
//	spaceholder
$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
							"colspan" => 3), getPixel($_widthTotal,25));

//	3rd Version
$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,1));

$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
							"class" => "small"), "Version: " . $we_version);
$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

//	4th row with spaceholder
$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
							"colspan" => 3), getPixel($_widthTotal,5));


//	5th credits
$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
							"class" => "defaultfont small"), $_credits);
$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

//	6th row
$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
							"colspan" => 3), getPixel($_widthTotal,10));

//	7th agency
if (is_readable($_SERVER["DOCUMENT_ROOT"].WEBEDITION_DIR.'agency.php') ){
	include_once($_SERVER["DOCUMENT_ROOT"].WEBEDITION_DIR.'agency.php');
	$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
							"colspan" => 3), getPixel($_widthTotal,10));

	$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
	$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
							"class" => "defaultfont small"), $_agency);
	$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));
}

//	8th row
$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
							"colspan" => 3), getPixel($_widthTotal,10));


if (isset($GLOBALS["loginpage"]) && $GLOBALS["loginpage"]){

	$loginRow = 0;

	$_loginTable = new we_htmlTable(
		array(	"border"      => 0,
				"cellpadding" => 0,
				"cellspacing" => 0
			),
		7,
		2
	);

	$_loginTable->setCol($loginRow, 0, array("width" => $_leftPart, "class" => "small"), we_baseElement::getHtmlCode(new we_baseElement("label",true,array("for"=>"username"),g_l('global','[username]'))));

	$_loginTable->setCol($loginRow++, 1, array('width'=> $_logoPart, 'rowspan' => '5', 'valign' => 'bottom'), '<img src="' . IMAGE_DIR . 'info/partnerLogo.gif" width="140" height="60" />');

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), htmlTextInput("username", 25, "", 100, "id=\"username\" style=\"width: 250px;\" ", "text", 0, 0));

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), getPixel(5,5));

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart, "class" => "small"), we_baseElement::getHtmlCode(new we_baseElement("label",true,array("for"=>"password"),g_l('global','[password]'))));

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart), htmlTextInput("password", 25, "", 100, "id=\"password\" style=\"width: 250px;\" ", "password", 0, 0));

	$_loginTable->setCol($loginRow++, 0, array("width" => $_leftPart + $_logoPart, 'colspan'=>2), getPixel(5,5));


	$_table->addRow(4);
	$_table->setCol($_actRow,0,array("width" => $_space), getPixel($_space,5));
	$_table->setCol($_actRow,1,array(), $_loginTable->getHtmlCode());
	$_table->setCol($_actRow++,2,array(), getPixel($_space,5));


	//	mode-table
	$we_button = new we_button();
	$_modetable = new we_htmlTable(	array(	"border"      => "0",
											"cellpadding" => "0",
											"cellspacing" => "0",
											"width"       => $_middlePart),
											1,
											3);


	if (defined("WE_SEEM") && !WE_SEEM) {	//	deactivate See-Mode

		$_modetable->setCol(0,1, array(	"align"   => "right",
									"valign"  => "bottom",
									"rowspan" => "2"), we_htmlElement::htmlHidden(array("name" => "mode", "value" => "normal")) . $we_button->create_button("login", "javascript:document.loginForm.submit();"));
	} else {	//	normal login

		//	15th Mode
		$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
		$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
									"class" => "small"), ((defined("WE_SEEM") && !WE_SEEM) ? "" : g_l('SEEM',"[start_mode]")));
		$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

		// if button is between these radio boces, they can not be reachable with <tab>
		$_modetable->setCol(0,0, array(), '<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td>' . we_forms::radiobutton("normal", getValueLoginMode("normal"), "mode", g_l('SEEM',"[start_mode_normal]"), true, "small") . '</td>
		</tr>
		<tr>
			<td>' . we_forms::radiobutton("seem", getValueLoginMode("seem"), "mode", g_l('SEEM',"[start_mode_seem]"), true, "small") . '</td>
		</tr>
		</table>');
		$_modetable->setCol(0,1, array(	"align"   => "right",
									"valign"  => "bottom",
									"rowspan" => "2"),$we_button->create_button("login", "javascript:document.loginForm.submit();"));
	}

	//	16th
	$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
	$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
										"class" => "small"), $_modetable->getHtmlCode() );
	$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

	//	17th row
	$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
										"colspan" => 3), getPixel($_widthTotal,15));


} else if (isset($GLOBALS["loginpage"]) && !$GLOBALS["loginpage"]) {

	$we_button = new we_button();
	srand ((double) microtime() * 1000000);
	$r = rand();

	$loginRow = 0;

	$_content = "";
	if($_SESSION["user"]["Username"] && $_POST["password"] && $_POST["username"]){
		$_content = g_l('global','[loginok]');
	}

	$_content = g_l('global','[loginok]');


	$_loginTable = new we_htmlTable(
		array(	"border"      => 0,
				"cellpadding" => 0,
				"cellspacing" => 0
			),
		2,
		2
	);

	$_loginTable->setCol($loginRow, 0, array("width" => $_leftPart, "class" => "small"), $_content);
	$_loginTable->setCol($loginRow++, 1, array('width'=> $_logoPart, 'rowspan' => '5', 'height' => 60),  '<img src="' . IMAGE_DIR . 'info/partnerLogo.gif" width="140" height="60" />');

	$_table->addRow(4);

	//	9th Login ok
	$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
	$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
								"class" => "small"), $_loginTable->getHtmlCode());
	$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

	//	10th row
	$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
								"colspan" => 3), getPixel($_widthTotal,5));
	//	11th back button
	$_table->setCol($_actRow,0,array(	"width" => $_space), getPixel($_space,5));
	$_table->setCol($_actRow,1,array(	"width" => $_middlePart,
								"class" => "small",
								"align" => "right"), $we_button->create_button("back_to_login", "/webEdition/index.php?r=$r"));
	$_table->setCol($_actRow++,2,array(	"width" => $_space), getPixel($_space,1));

	//	12th row
	$_table->setCol($_actRow++,0,array(	"width"   => $_widthTotal,
								"colspan" => 3), getPixel($_widthTotal,15));


}else if(isset($_REQUEST["we_cmd"][0]) && $_REQUEST["we_cmd"][0] == "info"){
	$_table->addRow();
	$_table->setCol($_actRow++,0,array("colspan"=>"3"), getPixel(2,50));
}

if(isset($_REQUEST["we_cmd"][0]) && $_REQUEST["we_cmd"][0] == "info"){
	print $_table->getHtmlCode();
} else {
	$_loginTable = $_table->getHtmlCode() . '<input type="image" width="1" height="1" src="/webEdition/images/pixel.gif"/>';
}
?>