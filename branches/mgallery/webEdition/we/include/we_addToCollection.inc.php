<?php
/**
 * webEdition CMS
 *
 * $Rev: 9440 $
 * $Author: mokraemer $
 * $Date: 2015-03-01 01:29:07 +0100 (So, 01 Mrz 2015) $
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
we_html_tools::protect();
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);
$targetCollection = we_base_request::_(we_base_request::INT, 'we_cmd', '', 3);
$targetCollectionPath = we_base_request::_(we_base_request::URL, 'we_cmd', '', 4);
$insertIndex = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 5);
$insertPos = ($pos = we_base_request::_(we_base_request::INT, 'we_cmd', -1, 6)) !== -1 ? $pos : (we_base_request::_(we_base_request::INT, 'we_targetInsertPos', -1));
$script = '';

/* FIXME: adapt when collection perms are implemented
 *
  if(($table == TEMPLATES_TABLE && !permissionhandler::hasPerm("MOVE_TEMPLATE")) ||
  ($table == FILE_TABLE && !permissionhandler::hasPerm("MOVE_DOCUMENT")) ||
  (defined('OBJECT_TABLE') && $table == OBJECT_TABLE && !permissionhandler::hasPerm("MOVE_OBJECTFILES"))){
  require_once (WE_USERS_MODULE_PATH . 'we_users_permmessage.inc.php');
  exit();
  }
 *
 */
$yuiSuggest = & weSuggest::getInstance();
$cmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);

if($cmd0 === 'do_addToCollection'){
	$db = new DB_WE();
	if(($targetCollection = we_base_request::_(we_base_request::INT, 'we_target', 0)) === 0){
		$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall(g_l('alert', '[move_no_dir]'), we_message_reporting::WE_MESSAGE_ERROR);
	} elseif(!($sel = we_base_request::_(we_base_request::INTLISTA, 'sel', array()))){
		$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR);
	} else {
		$collection = new we_collection();
		$isSession = false;
		if(($transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_targetTransaction', '')) && isset($_SESSION['weS']['we_data'][$transaction])){
			$isSession = true;
			$collection->we_initSessDat($_SESSION['weS']['we_data'][$transaction]);
		} else if(($collectionID = we_base_request::_(we_base_request::INT, 'we_target', 0))){
			$collection->initByID($collectionID);
		}

		if($collection->getRemTable() !== stripTblPrefix(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2))){
			$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall('wrong table for this collection', we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$collBefore = $collection->getCollection();
			if(($items = $collection->getVerifiedRemObjectsFromIDs($sel, false))){
				$result = $collection->addItemsToCollection($items, $isSession ? $insertPos : -1);
				if($isSession){
					$collection->saveInSession($_SESSION['weS']['we_data'][$transaction]);
				} else {
					$collection->save();
				}
				$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall('Inserted: ' . implode(',', $result[0]) . '\nAs duplicates rejected: ' . implode(',', $result[1]) . '. \n\nOthers items may have been rejecected because of inapropriate class/mime type.', we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall("none of the items selected do not matches the collection's content types", we_message_reporting::WE_MESSAGE_INFO);
			}
		}
	}
	$script = we_html_element::jsScript(JS_DIR . 'windows.js') .
		we_html_element::jsElement($script);
}

echo we_html_tools::getHtmlTop() .
 STYLESHEET .
 $script .
 we_html_element::jsScript(JS_DIR . 'weAddToCollection.js') .
 we_html_element::jsElement('
	weAddToCollection.init({
			table: "' . $table . '",
			targetInsertIndex: ' . $insertIndex . ',
			targetInsertPos: ' . $insertPos . '
		},{
			nothingToMove: "' . we_message_reporting::prepareMsgForJS(g_l('alert', '[nothing_to_move]')) . '",
			notValidFolder: "' . we_message_reporting::prepareMsgForJS(g_l('weClass', '[notValidFolder]')) . '"
		},{
			VFILE_TABLE: "' . VFILE_TABLE . '"
		}
	);') .
 weSuggest::getYuiFiles();

if($cmd0 === "do_addToCollection"){
	echo "</head><body></body></html>";
	exit();
}

$ws_Id = get_def_ws($table);
if($ws_Id){
	$ws_path = id_to_path($ws_Id, $table);
} else {
	$ws_Id = 0;
	$ws_path = '/';
}

$textname = 'we_targetname';
$idname = 'we_target';

$yuiSuggest->setAcId('Dir');
$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER . ',' . we_base_ContentTypes::COLLECTION);
$yuiSuggest->setInput($textname, $targetCollectionPath);
$yuiSuggest->setMaxResults(4);
$yuiSuggest->setMayBeEmpty(false);
$yuiSuggest->setResult($idname, $targetCollection);
$yuiSuggest->setSelector(weSuggest::DocSelector);
$yuiSuggest->setTable(VFILE_TABLE);
$yuiSuggest->setWidth(250);
$yuiSuggest->setContainerWidth(370);
$wecmdenc1 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $idname . '.value');
$wecmdenc2 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $textname . '.value');
$yuiSuggest->setSelectButton(we_html_button::create_button("select", "javascript:weAddToCollection.we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . VFILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0)"), 10);
//$yuiSuggest->setOpenButton(we_html_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . we_base_ContentTypes::COLLECTION . "','" . VFILE_TABLE . "'); return false}"));
$weAcSelector = $yuiSuggest->getHTML();

$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button("ok", "javascript:weAddToCollection.press_ok_add();"), "", we_html_button::create_button("quit_move", "javascript:weAddToCollection.we_cmd('exit_move','','" . $table . "')"), 10, "left");

echo
'</head><body class="weTreeHeaderMove">
<form name="we_form" method="post" onsubmit="return false">' .
 we_html_element::htmlHiddens(array(
	'we_targetTransaction' => '',
	'we_targetInsertPos' => $insertPos,
	'name' => 'sel')) . '
<div style="width:370px;">
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(
	g_l('newFile', '[title_move]')) . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">addToCollectionText</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p></p>
<div>' . $_buttons . '</div></div>
 </form>' .
 $yuiSuggest->getYuiJs() .
 '</body>
</html>';
