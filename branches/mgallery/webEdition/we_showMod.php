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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$mod = we_base_request::_(we_base_request::STRING, 'mod');

if(we_base_moduleInfo::isActive($mod) && file_exists(WE_MODULES_PATH . $mod . '/edit_' . $mod . '_frameset.php')){
	require_once(WE_MODULES_PATH . $mod . '/edit_' . $mod . '_frameset.php');
}

if(strpos($mod, '?')){
	//compatibility code for ?mod=xxx?pnt=yy
	list($mod, $other) = explode('?', $mod);
	$_REQUEST['mod'] = $mod;
	list($k, $v) = explode('=', $other);
	$_REQUEST[$k] = $v;
}

if(!we_base_moduleInfo::isActive($mod)){
	return;
}

$what = we_base_request::_(we_base_request::STRING, "pnt", "frameset");
$mode = we_base_request::_(we_base_request::INT, "art", 0);
$step = we_base_request::_(we_base_request::INT, 'step', 0);


if($what === 'show_frameset'){ //old call to show_frameset.php
	echo we_html_tools::getHtmlTop() .
	STYLESHEET .
	we_tabs::getHeader() .
	we_html_element::jsElement('
var makeNewEntryCheck = 0;
var publishWhenSave = 0;
var weModuleWindow = true;

function we_cmd() {
	top.content.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}
');
	?>
	</head>
	<body id="weMainBody" onload="weTabs.setFrameSize()" onresize="weTabs.setFrameSize()">
		<?php
		$_REQUEST['mod'] = $mod = (isset($mod) ? $mod : we_base_request::_(we_base_request::STRING, 'mod'));

		//TODO: we should loop through all we_cmd and process them in respective we_module_frames.class only
		$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', false, 1); //to be used only for IDs or integer constants!
		$sid = we_base_request::_(we_base_request::RAW, 'sid');
		$bid = $mod === 'shop' && $cmd1 !== false ? $cmd1 : we_base_request::_(we_base_request::RAW, 'bid');

		echo we_html_element::htmlExIFrame('navi', WE_MODULES_PATH . 'navi.inc.php', 'background-color:white;position:absolute;top:0px;height:21px;left:0px;right:0px;overflow: hidden;') .
		we_html_element::htmlIFrame('content', WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod . ($cmd1 === false ? '' : '&msg_param=' . $cmd1) . ($sid !== false ? '&sid=' . $sid : '') . ($bid !== false ? '&bid=' . $bid : ''), 'position:absolute;top:21px;bottom:0px;left:0px;right:0px;overflow: hidden;', '', '', false)
		;
		?></body></html><?php
	return;
}

switch($mod){
	case 'workflow':
		$override = ($what === 'log');
	default:
		$protect = we_base_moduleInfo::isActive($mod) && (we_users_util::canEditModule($mod) || !empty($override)) ? null : array(false);
		we_html_tools::protect($protect);
}

switch($mod){
	case 'banner':
		$weFrame = new we_banner_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		break;
	case 'shop':
		$weFrame = new we_shop_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->View->processCommands();
		break;
	case 'customer':
		switch($what){
			case 'export':
			case 'eibody':
			case 'eifooter':
			case 'eiload':
			case 'import':
			case 'eiupload':
				$weFrame = new we_customer_EIWizard(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
				break;
			default:
				$weFrame = new we_customer_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
				$weFrame->process();
		}
		break;
	case 'users':
		$weFrame = new we_users_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		break;

	case 'export':
		$weFrame = new we_export_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		break;

	case 'glossary':
		$weFrame = new we_glossary_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		break;

	case 'voting':
		$weFrame = new we_voting_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		break;

	case 'navigation':
		switch($what){
			case 'ruleCmd':
			case 'ruleContent':
			case 'ruleFrameset':
				we_html_tools::protect(array('EDIT_NAVIAGTION_RULES'));

				$weFrame = new we_navigation_ruleFrames();
				ob_start();
				$weFrame->Controller->processVariables();
				$weFrame->Controller->processCommands();
				$GLOBALS['extraJS'] = ob_get_clean();
				break;
			default:
				$weFrame = new we_navigation_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
				$weFrame->process();
				break;
		}
		break;

	case 'workflow':
		$type = we_base_request::_(we_base_request::INTLIST, 'type', 0);
		$weFrame = new we_workflow_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		$weFrame->process();
		echo $weFrame->getHTML($what, $mode, $type);
		return;
	case 'messaging':

		if(!isset($we_transaction)){//FIXME: can this ever be set except register globals???
			$we_transaction = 0;
		}
		$transaction = $what === 'frameset' ? $we_transaction : we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 'no_request'); //FIXME: is $transaction used anywhere?

		$weFrame = new we_messaging_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod, we_base_request::_(we_base_request::STRING, 'viewclass', 'message'), we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 'no_request'), $we_transaction);
		$weFrame->process();
		break;

	case 'newsletter':
		$ncmd = we_base_request::_(we_base_request::STRING, 'ncmd', '');

		$weFrame = new we_newsletter_frames(WEBEDITION_DIR . 'we_showMod.php?mod=' . $mod);
		switch($what){
			case 'edit_file':
				$mode = we_base_request::_(we_base_request::FILE, 'art');
				break;
			default:
				$mode = we_base_request::_(we_base_request::INT, 'art', 0);
				break;
		}

		switch($ncmd){
			case 'do_upload_csv':
			case 'do_upload_black':
				break;
			default:
				echo $weFrame->getHTMLDocumentHeader($what, $mode);
				return;
		}

		if(($id = we_base_request::_(we_base_request::INT, 'inid')) !== false){
			$weFrame->View->newsletter = new we_newsletter_newsletter($id);
		} else {
			switch($what){
				case 'export_csv_mes':
				case 'newsletter_settings':
				case 'qsend':
				case 'eedit':
				case 'black_list':
				case 'upload_csv':
					break;
				default:
					$weFrame->View->processVariables();
			}
		}

		switch($what){
			case 'export_csv_mes':
			case 'preview':
			case 'domain_check':
			case 'newsletter_settings':
			case 'show_log':
			case 'print_lists':
			case 'qsend':
			case 'eedit':
			case 'black_list':
				break;
			default:
				$mode = isset($mode) ? $mode : we_base_request::_(we_base_request::INT, 'art', 0);
				$weFrame->View->processCommands();
		}

		if($weFrame->View->isJsonOnly()){
			return;
		}
		break;
	default:
		echo 'no module';
		return;
}

//FIXME: process will generate js output without doctype
echo $weFrame->getHTML($what, $mode, $step);
