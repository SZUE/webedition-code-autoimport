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
	if(($targetCollection = we_base_request::_(we_base_request::INT, 'we_target', 0)) === 0){
		$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[move_no_dir]'), we_message_reporting::WE_MESSAGE_ERROR);
	} elseif(!($sel = we_base_request::_(we_base_request::INTLISTA, 'sel', array()))){
		$script .= 'top.we_showMessage(WE().consts.g_l.main.nothing_to_move, WE().consts.message.WE_MESSAGE_ERROR, window);';
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
			$script .= we_message_reporting::getShowMessageCall(g_l('weClass','[collection][wrongTable]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$collBefore = $collection->getCollection();
			if(($items = $collection->getValidItemsFromIDs($sel, false, $recursive = we_base_request::_(we_base_request::BOOL, 'InsertRecursive', false)))){
				$result = $collection->addItemsToCollection($items, $isSession ? $insertPos : -1);
				if($isSession){
					$collection->saveInSession($_SESSION['weS']['we_data'][$transaction]);
				} else {
					$collection->save();
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('weClass','[collection][insertedAndDuplicates]'),implode(',', $result[0]),implode(',', $result[1])), we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$script .= we_message_reporting::getShowMessageCall(g_l('weClass','[collection][contentDoesntMatch]'), we_message_reporting::WE_MESSAGE_INFO);
			}
		}
	}
	$script = we_html_element::jsElement($script);
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
		});') .
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
$yuiSuggest->setWidth(273);
$yuiSuggest->setContainerWidth(300);
$wecmdenc1 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $idname . '.value');
$wecmdenc2 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $textname . '.value');
$yuiSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:weAddToCollection.we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . VFILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0)"), 6);
//$yuiSuggest->setOpenButton(we_html_button::create_button(we_html_button::EDIT, "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . we_base_ContentTypes::COLLECTION . "','" . VFILE_TABLE . "'); return false}"));

$yuiSuggest->setAdditionalButton(we_html_button::create_button('fa:btn_add_collection,fa-plus,fa-lg fa-archive', "javascript:top.we_cmd('edit_new_collection','" . $wecmdenc1 . "','" . $wecmdenc2 . "',-1,'" . stripTblPrefix($table) . "');", true, 0, 0, "", "", false, false), 0);
$weAcSelector = $yuiSuggest->getHTML();
$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:weAddToCollection.press_ok_add();"), "", we_html_button::create_button('quit_addToCollection', "javascript:weAddToCollection.we_cmd('exit_addToCollection','','" . $table . "')"), 10, "left");

$recursive = we_html_forms::checkboxWithHidden(1, 'InsertRecursive', g_l('weClass','[collection][insertRecursive]'));

echo
'</head><body class="weTreeHeaderAddToCollection">
<form name="we_form" method="post" onsubmit="return false">' .
 we_html_element::htmlHiddens(array(
	'we_targetTransaction' => '',
	'we_targetInsertPos' => $insertPos,
	'sel' => '')) . '
<div style="width:440px;">
<h1 class="big" style="padding:0px;margin:0px;">' . g_l('weClass', '[collection][add]') . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">'.g_l('weClass', '[collection][add_help]').'</span>
<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . $recursive . '</p></p>
<div>' . $_buttons . '</div></div>
 </form>' .
 $yuiSuggest->getYuiJs() .
 '</body>
</html>';
