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
require_once (WE_INCLUDES_PATH . 'we_addToCollection_fn.inc.php');

we_html_tools::protect();
$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);
$targetCollection = we_base_request::_(we_base_request::INT, 'we_cmd', '', 3);
$targetCollectionPath = we_base_request::_(we_base_request::URL, 'we_cmd', '', 4);
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
		if(($transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_targetTransaction', '')) && isset($_SESSION['weS']['we_data'][$transaction])){
			$collection->we_initSessDat(($sess = $_SESSION['weS']['we_data'][$transaction]));
		} else if(($collectionID = we_base_request::_(we_base_request::INT, 'we_target', 0))){
			$collection->initByID($collectionID);
		}

		if($collection->getRemTable() !== stripTblPrefix(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2))){
			$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall('wrong table for this collection', we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			$collBefore = $collection->getCollection();
			if(($items = $collection->getVerifiedRemObjectsFromIDs($sel, false))){
				$result = $collection->addItemsToCollection($items);
				if(isset($sess)){
					$collection->saveInSession($sess);
				} else {
					$collection->save();
				}
				$collection->saveInSession($_SESSION['weS']['we_data'][$transaction]);
				$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall('inserted: ' . implode(',', $result[0]) . '. as doublettes rejected: '. implode(',', $result[1]) .'. \nother items may not have matched the collection\'s content types.', we_message_reporting::WE_MESSAGE_ERROR);
			} else {
				$script .= 'top.toggleBusy(0);' . we_message_reporting::getShowMessageCall("none of the items selected do not matches the collection's content types", we_message_reporting::WE_MESSAGE_INFO);
			}
		}
	}
	$script = we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsElement($script);
}

echo we_html_tools::getHtmlTop() . STYLESHEET .
 $script .
 weSuggest::getYuiFiles();
?>
<script type="text/javascript"><!--
	top.treeData.setstate(top.treeData.tree_states["select"]);
	if (top.treeData.table != "<?php echo $table; ?>") {
		top.treeData.table = "<?php echo $table; ?>";
		we_cmd("load", "<?php echo $table; ?>");
	} else {
		we_cmd("load", "<?php echo $table; ?>");
		top.drawTree();
	}

	function press_ok_add() {
		var sel = "";
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked == 1) {
				sel += (top.treeData[i].id + ",");
			}
		}
		if (!sel) {
			top.toggleBusy(0);
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
			return;
		}

		// check if selected target exists
		var acStatus = '';
		var invalidAcFields = false;
		acStatus = YAHOO.autocoml.checkACFields();
		acStatusType = typeof acStatus;
		if (acStatusType.toLowerCase() == 'object') {
			if (acStatus.running) {
				setTimeout(press_ok_move, 100);
				return;
			} else if (!acStatus.valid) {
<?php echo we_message_reporting::getShowMessageCall(g_l('weClass', '[notValidFolder]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
				return;
			}
		}

		// check if collection is open
		var _usedEditors = top.weEditorFrameController.getEditorsInUse(),
			_collID = document.getElementById('yuiAcResultDir').value,
			_isOpen = false,
			_isEditorCollActive = false,
			_frameId,
			_transaction,
			_editor,
			_contentEditor;

		_collID = _collID ? _collID : 0;
		for (_frameId in _usedEditors) {
			_editor = _usedEditors[_frameId];
			if (_editor.getEditorEditorTable() == '<?php echo VFILE_TABLE; ?>' && _editor.getEditorDocumentId() == _collID) {
				_isOpen = true;
				_transaction = _editor.getEditorTransaction();
				if(_editor.getEditorEditPageNr() == 1){
					_isEditorCollActive = true;
					_contentEditor = _editor.getContentEditor();
				} else {
					_editor.setEditorIsHot(true);
				}
			}
		}

		if(_isOpen){
			if(_isEditorCollActive){
				var contentTable = _contentEditor.document.getElementById('content_table'), 
					index,
					acResult, 
					lastIndexNotEmpty = contentTable.firstChild.id.substr(5);

				for(var i = 0; i < contentTable.childNodes.length; i++){
					index = contentTable.childNodes[i].id.substr(5);
					lastIndexNotEmpty = _contentEditor.document.getElementById('yuiAcResultItem_' + index).value != -1 ? contentTable.childNodes[i].nextSibling.id.substr(5) : lastIndexNotEmpty;
				}

				_contentEditor.weCollectionEdit.callForVerifiedItemsAndInsert(lastIndexNotEmpty, sel, true);
				_editor.setEditorIsHot(true);

				for (var i = 1; i <= top.treeData.len; i++) {
					if(top.treeData[i].constructor.name === 'node' && top.treeData[i].checked){
						top.checkNode('img_' + top.treeData[i].id);
					}
				}
				return;
			}
			document.we_form.we_targetTransaction.value = _transaction;
		}
		we_cmd('do_addToCollection', '', '<?php echo $table; ?>');
	}

	function we_submitForm(target, url) {
		var f = self.document.we_form;
		var sel = "";
		for (var i = 1; i <= top.treeData.len; i++) {
			if (top.treeData[i].checked == 1)
				sel += (top.treeData[i].id + ",");
		}
		if (!sel) {
			top.toggleBusy(0);
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
			return;
		}

		sel = sel.substring(0, sel.length - 1);
		f.sel.value = sel;
		f.target = target;
		f.action = url;
		f.method = "post";
		f.submit();
	}

	function we_cmd() {
		var args = "";
		for (var i = 0; i < arguments.length; i++) {
			args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
		}
		eval('parent.we_cmd(' + args + ')');
	}
//-->
</script>
<?php
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
$yuiSuggest->setContainerWidth(360);
$wecmdenc1 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $idname . '.value');
$wecmdenc2 = we_base_request::encCmd('top.treeheader.document.we_form.elements.' . $textname . '.value');
$yuiSuggest->setSelectButton(we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . VFILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0)"), 10);
$yuiSuggest->setOpenButton(we_html_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . we_base_ContentTypes::COLLECTION . "','" . VFILE_TABLE . "'); }"));


$weAcSelector = $yuiSuggest->getHTML();

$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button("ok", "javascript:press_ok_add();"), "", we_html_button::create_button("quit_move", "javascript:we_cmd('exit_move','','" . $table . "')"), 10, "left");

echo
'</head><body class="weTreeHeaderMove">
<form name="we_form" method="post" onsubmit="return false">' .
we_html_element::htmlHidden(array('name' => 'we_targetTransaction')) . '
<div style="width:460px;">
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(
		g_l('newFile', '[title_move]')) . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">addToCollectionText</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p></p>
<div>' . $_buttons . '</div></div>' . we_html_tools::hidden("sel", "") .
 '</form>' .
 $yuiSuggest->getYuiJs() .
 '</body>
</html>';
