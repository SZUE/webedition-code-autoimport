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
require_once (WE_INCLUDES_PATH . 'we_move_fn.inc.php');

we_html_tools::protect();
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);

$script = '';

if(($table == TEMPLATES_TABLE && !permissionhandler::hasPerm("MOVE_TEMPLATE")) ||
	($table == FILE_TABLE && !permissionhandler::hasPerm("MOVE_DOCUMENT")) ||
	(defined('OBJECT_TABLE') && $table == OBJECT_TABLE && !permissionhandler::hasPerm("MOVE_OBJECTFILES"))){
	require_once (WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
	exit();
}

$yuiSuggest = & weSuggest::getInstance();
$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$script = '';
switch($cmd0){
	case 'do_move':
	case 'move_single_document':
		$db = new DB_WE();
		if(($targetDirectroy = we_base_request::_(we_base_request::INT, 'we_target')) === false){
			$script = we_message_reporting::getShowMessageCall(g_l('alert', '[move_no_dir]'), we_message_reporting::WE_MESSAGE_ERROR);
			break;
		}
		if(!($selectedItems = we_base_request::_(we_base_request::INTLISTA, 'sel', array()))){
			$script = 'top.we_showMessage(WE().consts.g_l.main.nothing_to_move, WE().consts.message.WE_MESSAGE_ERROR, window);';
			break;
		}

		// list of all item names which should be moved
		$items2move = array();

		// list of the selected items
		$retVal = 1;
		foreach($selectedItems as $selectedItem){

			// check if user is allowed to move this item
			if(!permissionhandler::checkIfRestrictUserIsAllowed($selectedItem, $table, $db)){
				$retVal = -1;
				break;
			}

			// check if item could be moved to the target directory
			$check = checkMoveItem($db, $targetDirectroy, $selectedItem, $table, $items2move);
			switch($check){
				default :
				case 1 :
					break;
				case -1 :
					$message = g_l('alert', '[move_nofolder]');
					$retVal = 0;
					break;
				case -2 :
					$message = g_l('alert', '[move_duplicate]');
					$retVal = 0;
					break;
				case -3 :
					$message = g_l('alert', '[move_onlysametype]');
					$retVal = 0;
					break;
			}
			if(!$check){
				break;
			}
		}

		if($retVal == -1){ //	not allowed to move document
			$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[noRightsToMove]'), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif($retVal){ //	move files !
			$notMovedItems = array();
			moveItems($targetDirectroy, $selectedItems, $table, $notMovedItems);

			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	only update tree when in normal mode
				$script .= moveTreeEntries($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'));
			}

			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	different messages in normal or seeMode
				if($notMovedItems){
					$_SESSION['weS']['move_files_nok'] = array();
					foreach($notMovedItems as $item){
						$_SESSION['weS']['move_files_nok'][] = array(
							"ContentType" => $item['ContentType'],
							"path" => $item['Path']
						);
					}
					$script .= 'new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[0]=moveInfo","we_moveinfo",-1,-1,550,550,true,true,true);' . "\n";
				} else {
					$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[move_ok]'), we_message_reporting::WE_MESSAGE_NOTICE);
				}
			}
		} else {
			$script .= we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_ERROR);
		}
}
$script = $script ? we_html_element::jsElement($script) : '';

//	in seeMode return to startDocument ...


if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
	$js = ($retVal ? //	document moved -> go to seeMode startPage
			we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][return_to_start]'), we_message_reporting::WE_MESSAGE_NOTICE) . ";top.we_cmd('start_multi_editor');" :
			we_message_reporting::getShowMessageCall(g_l('alert', '[move_single][no_delete]'), we_message_reporting::WE_MESSAGE_ERROR));

	echo we_html_tools::getHtmlTop('', '', '', $script . we_html_element::jsElement($js));
	exit();
}

switch($table){
	case TEMPLATES_TABLE:
		$type = g_l('global', '[templates]');
		break;
	case defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
		$type = g_l('global', '[objects]');
		break;
	default:
		$type = g_l('global', '[documents]');
		break;
}

echo we_html_tools::getHtmlTop() . STYLESHEET .
 $script .
 weSuggest::getYuiFiles() .
 we_html_element::jsScript(JS_DIR . 'move.js', "initMove('" . $table . "');");

if($cmd0 === 'do_move'){
	echo '</head><body></body></html>';
	exit();
}


$ws_Id = get_def_ws($table)? : 0;
$ws_path = ($ws_Id ? id_to_path($ws_Id, $table) : '/');
$textname = 'we_targetname';
$idname = 'we_target';

$yuiSuggest->setAcId('Dir');
$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
$yuiSuggest->setInput($textname, $ws_path);
$yuiSuggest->setMaxResults(4);
$yuiSuggest->setMayBeEmpty(false);
$yuiSuggest->setResult(trim($idname), $ws_Id);
$yuiSuggest->setSelector(weSuggest::DirSelector);
$yuiSuggest->setTable($table);
$yuiSuggest->setWidth(250);
$yuiSuggest->setContainerWidth(360);
$cmd1 = 'top.treeheader.document.we_form.elements.' . $idname . '.value';
$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $textname . '.value') . "','','',0)"), 10);

$weAcSelector = $yuiSuggest->getHTML();

$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:press_ok_move('" . $type . "');"), '', we_html_button::create_button('quit_move', "javascript:we_cmd('exit_move','','" . $table . "')"), 10, "left");

echo
'</head><body class="weTreeHeaderMove">
<form name="we_form" method="post" onsubmit="return false">
<div>
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(
	g_l('newFile', '[title_move]')) . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">' . g_l('newFile', '[move_text]') . '</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p></p>
<div>' . $buttons . '</div></div>' . we_html_element::htmlHidden("sel", "") .
 '</form>' .
 $yuiSuggest->getYuiJs() .
 '</body>
</html>';
