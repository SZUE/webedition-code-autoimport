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
if($cmd0 === 'do_move' || $cmd0 === 'move_single_document'){
	$db = new DB_WE();
	if(($targetDirectroy = we_base_request::_(we_base_request::INT, 'we_target')) === false){
		$script .= 'top.toggleBusy(0);' .
			we_message_reporting::getShowMessageCall(g_l('alert', '[move_no_dir]'), we_message_reporting::WE_MESSAGE_ERROR);
	} elseif(($selectedItems = we_base_request::_(we_base_request::INTLISTA, 'sel', array()))){

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
			$script .= 'top.toggleBusy(0);' .
				we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[noRightsToMove]'), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
		} elseif($retVal){ //	move files !
			$notMovedItems = array();
			moveItems($targetDirectroy, $selectedItems, $table, $notMovedItems);

			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	only update tree when in normal mode
				$script .= moveTreeEntries($table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'));
			}

			$script .= 'top.toggleBusy(0);';
			if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	different messages in normal or seeMode
				if($notMovedItems){
					$_SESSION['weS']['move_files_nok'] = array();
					foreach($notMovedItems as $item){
						$_SESSION['weS']['move_files_nok'][] = array(
							"ContentType" => $item['ContentType'],
							"path" => $item['Path']
						);
					}
					$script .= 'new (WE().util.jsWindow)(top.window, "' . WEBEDITION_DIR . 'moveInfo.php","we_moveinfo",-1,-1,550,550,true,true,true);' . "\n";
				} else {
					$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[move_ok]'), we_message_reporting::WE_MESSAGE_NOTICE);
				}
			}
		} else {
			$script .= 'top.toggleBusy(0);' .
				we_message_reporting::getShowMessageCall($message, we_message_reporting::WE_MESSAGE_ERROR);
		}
	} else {
		$script .= 'top.toggleBusy(0);' .
			we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_move]'), we_message_reporting::WE_MESSAGE_ERROR);
	}
	$script = we_html_element::jsElement($script);
}

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
		$_type = g_l('global', '[templates]');
		break;
	case defined('OBJECT_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
		$_type = g_l('global', '[objects]');
		break;
	default:
		$_type = g_l('global', '[documents]');
		break;
}

echo we_html_tools::getHtmlTop() . STYLESHEET .
 $script .
 weSuggest::getYuiFiles();
?>
<script><!--
	top.treeData.setstate(top.treeData.tree_states["select"]);
	if (top.treeData.table != "<?php echo $table; ?>") {
		top.treeData.table = "<?php echo $table; ?>";
		we_cmd("load", "<?php echo $table; ?>");
	} else {
		we_cmd("load", "<?php echo $table; ?>");
		top.drawTree();
	}

	function press_ok_move() {
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
			}
			if (!acStatus.valid) {
<?php echo we_message_reporting::getShowMessageCall(g_l('weClass', '[notValidFolder]'), we_message_reporting::WE_MESSAGE_ERROR) ?>
				return;
			}
		}

		// close all documents before moving.


		// no open document can be moved
		// close all Editors with deleted documents
		var _usedEditors = top.weEditorFrameController.getEditorsInUse();

		var _move_table = "<?php
echo $table;
?>";
		var _move_ids = "," + sel;

		var _open_move_editors = [];

		for (frameId in _usedEditors) {
			if (_move_table == _usedEditors[frameId].getEditorEditorTable()) {
				_open_move_editors.push(_usedEditors[frameId]);
			}
		}
		if (_open_move_editors.length) {
			_openDocs_Str = "";

			for (i = 0; i < _open_move_editors.length; i++) {
				_openDocs_Str += "- " + _open_move_editors[i].getEditorDocumentPath() + "\n";

			}
			if (confirm("<?php
printf(g_l('alert', '[move_exit_open_docs_question]'), $_type, $_type);
?>" + _openDocs_Str + "\n<?php
echo g_l('alert', '[move_exit_open_docs_continue]');
?>")) {

				for (i = 0; i < _open_move_editors.length; i++) {
					_open_move_editors[i].setEditorIsHot(false);
					top.weEditorFrameController.closeDocument(_open_move_editors[i].getFrameId());

				}
				we_cmd('do_move', '', '<?php
echo $table;
?>');
			}

		} else {

			if (confirm('<?php
echo g_l('alert', '[move]');
?>')) {
				we_cmd('do_move', '', '<?php
echo $table;
?>');
			}
		}
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
		var args = [];
		for (var i = 0; i < arguments.length; i++) {
			args.push(arguments[i]);
		}
		parent.we_cmd.apply(this, args);
	}
//-->
</script>
<?php
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

$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, 'javascript:press_ok_move();'), '', we_html_button::create_button('quit_move', "javascript:we_cmd('exit_move','','" . $table . "')"), 10, "left");

echo
'</head><body class="weTreeHeaderMove">
<form name="we_form" method="post" onsubmit="return false">
<div style="width:460px;">
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(
	g_l('newFile', '[title_move]')) . '</h1>
<p class="small"><span class="middlefont" style="padding-right:5px;padding-bottom:10px;">' . g_l('newFile', '[move_text]') . '</span>
			<p style="margin:0px 0px 10px 0px;padding:0px;">' . $weAcSelector . '</p></p>
<div>' . $_buttons . '</div></div>' . we_html_tools::hidden("sel", "") .
 '</form>' .
 $yuiSuggest->getYuiJs() .
 '</body>
</html>';
