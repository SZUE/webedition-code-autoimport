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
echo we_html_tools::getHtmlTop() .
 STYLESHEET;

function getObjectsForDocWorkspace($id, we_database_base $db){
	$ids = (is_array($id)) ? $id : array($id);

	if(!defined('OBJECT_FILES_TABLE')){
		return array();
	}

	$where = array();
	foreach($ids as $id){
		$where[] = 'FIND_IN_SET(' . $id . ',Workspaces)';
		$where[] = 'FIND_IN_SET(' . $id . ',ExtraWorkspaces)';
	}

	$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . implode(' OR ', $where));
	return $db->getAllFirst(false);
}

$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', '', 2);
$wfchk = defined('WORKFLOW_TABLE') && ($table == FILE_TABLE || (defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE)) ?
	we_base_request::_(we_base_request::BOOL, 'we_cmd', 0, 3) :
	1;
$wecmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$wfchk_html = '';
$script = '';

if(!$wfchk){
	if(($selectedItems = we_base_request::_(we_base_request::INTLISTA, 'sel', array()))){
		$found = false;
		foreach($selectedItems as $selectedItem){
			if(we_workflow_utility::inWorkflow($selectedItem, $table)){
				$found = true;
				break;
			}
		}
		$wfchk_html .= we_html_element::jsElement('
function confirmDel(){' .
				($found ? 'if(confirm("' . g_l('alert', '[found_in_workflow]') . '")){' : '') .
				'we_cmd("' . we_base_request::_(we_base_request::RAW, 'we_cmd', '', 0) . '","","' . $table . '",1);' .
				($found ? '}' : '') .
				'}');
	} else {
		$script = we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_WARNING);
		$wfchk_html .= we_html_element::jsElement('function confirmDel(){}');
	}
	$wfchk_html .= '</head><body onload="confirmDel()"><form name="we_form" method="post">' .
		we_html_tools::hidden("sel", implode(',', $selectedItems)) . "</form>";
} elseif(in_array($wecmd0, array("do_delete", 'delete_single_document'))){
	if(($selectedItems = we_base_request::_(we_base_request::INTLISTA, "sel", array()))){
		//	look which documents must be deleted.
		$retVal = 1;
		$idInfos = array(
			'IsFolder' => 0,
			'Path' => '',
			'hasFiles' => 0
		);
		if($selectedItems && ($table == FILE_TABLE || $table == TEMPLATES_TABLE)){
			$idInfos = getHash('SELECT IsFolder, Path FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ID=' . intval($selectedItems[0]));
			if(!$idInfos){
				t_e('ID ' . $selectedItems[0] . ' not present in table ' . $table);
				return;
			} elseif($idInfos['IsFolder']){
				$idInfos['hasFiles'] = f('SELECT ID FROM ' . $GLOBALS['DB_WE']->escape($table) . ' WHERE ParentID=' . intval($selectedItems[0]) . ' AND IsFolder = 0 AND Path LIKE "' . $GLOBALS['DB_WE']->escape($idInfos['Path']) . '%"') > 0 ? 1 : 0;
			}
		}

		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			$hasPerm = true;
		} else {
			switch($table){
				case FILE_TABLE:
					$hasPerm = (
						($idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_DOC_FOLDER') && !$idInfos['hasFiles']) ||
						(!$idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_DOCUMENT')) ||
						($idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_DOC_FOLDER') && $idInfos['hasFiles'] && permissionhandler::hasPerm('DELETE_DOCUMENT'))
						);
					break;
				case TEMPLATES_TABLE:
					$hasPerm = (
						($idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_TEMP_FOLDER') && !$idInfos['hasFiles']) ||
						(!$idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_TEMPLATE')) ||
						($idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_TEMP_FOLDER') && $idInfos['hasFiles'] && permissionhandler::hasPerm('DELETE_TEMPLATE'))
						);
					break;
				case OBJECT_FILES_TABLE:
					$hasPerm = (permissionhandler::hasPerm('DELETE_OBJECTFILE'));
					break;
				case OBJECT_TABLE:
					$hasPerm = ($idInfos['IsFolder'] && permissionhandler::hasPerm('DELETE_OBJECT'));
					break;
				default:
					$hasPerm = false;
			}
		}
		unset($idInfos);

		if(!$hasPerm){
			$retVal = -6;
		} else {
			foreach($selectedItems as $selectedItem){
				if(!permissionhandler::checkIfRestrictUserIsAllowed($selectedItem, $table, $GLOBALS['DB_WE'])){
					$retVal = -1;
					break;
				}

				if(!we_base_delete::checkDeleteEntry($selectedItem, $table)){
					$retVal = 0;
					break;
				}
			}
		}

		if($retVal == 1){ // only if no error occurs
			foreach($selectedItems as $selectedItem){

				if($table == FILE_TABLE){
					$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem);
					if($users){
						$retVal = -2;
						break;
					}

					// check if childrenfolders are workspaces
					$childs = array();

					pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
					$users = array();
					foreach($childs as $ch){
						$users = array_merge($users, we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs));
					}
					$users = array_unique($users);

					if($users){
						$retVal = -4;
						break;
					}
				}

				if($table == TEMPLATES_TABLE){
					$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem, "workSpaceTmp");
					if($users){
						$retVal = -2;
						break;
					}

					// check if childrenfolders are workspaces
					$childs = array();

					pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
					$users = array();
					foreach($childs as $ch){
						$users = array_merge($users, we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs, "workSpaceTmp"));
					}
					$users = array_unique($users);

					if($users){
						$retVal = -4;
						break;
					}
				}

				if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE){

					$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $selectedItem, "workSpaceObj");
					if($users){
						$retVal = -2;
						break;
					}

					$childs = array();

					pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
					$users = we_users_util::getUsersForDocWorkspace($GLOBALS['DB_WE'], $childs, "workSpaceObj");

					if($users){
						$retVal = -4;
						break;
					}
				}
				if(defined('OBJECT_FILES_TABLE') && $table == FILE_TABLE){
					$objects = getObjectsForDocWorkspace($selectedItem, $GLOBALS['DB_WE']);
					if($objects){
						$retVal = -3;
						break;
					}

					$childs = array();

					pushChilds($childs, $selectedItem, $table, 1, $GLOBALS['DB_WE']);
					$objects = getObjectsForDocWorkspace($childs, $GLOBALS['DB_WE']);

					if($objects){
						$retVal = -5;
						break;
					}
				}
			}
		}

		switch($retVal){
			case -6:
				$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms_action]'), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -5: //	not allowed to delete workspace
				$objList = '';
				foreach($objects as $val){
					$objList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_object_r]'), id_to_path($selectedItem, $table), $objList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -4: //	not allowed to delete workspace
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_user_r]'), id_to_path($selectedItem, $table), $usrList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -3: //	not allowed to delete workspace
				$objList = '';
				foreach($objects as $val){
					$objList .= "- " . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_object]'), id_to_path($selectedItem, $table), $objList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -2: //	not allowed to delete workspace
				$usrList = '';
				foreach($users as $val){
					$usrList .= '- ' . $val . '\n';
				}
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[delete_workspace_user]'), id_to_path($selectedItem, $table), $usrList), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			case -1: //	not allowed to delete document
				$script .= we_message_reporting::getShowMessageCall(sprintf(g_l('alert', '[noRightsToDelete]'), id_to_path($selectedItem, $table)), we_message_reporting::WE_MESSAGE_ERROR);
				break;
			default:
				if($retVal){ //	user may delete -> delete files !
					$GLOBALS["we_folder_not_del"] = array();

					$deletedItems = array();

					foreach($selectedItems as $sel){
						we_base_delete::deleteEntry($sel, $table, true, false, $GLOBALS['DB_WE']);
					}

					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	only update tree when in normal mode
						$script .= we_tree_base::deleteTreeEntries(defined('OBJECT_FILES_TABLE') && $table == OBJECT_FILES_TABLE);
					}

					if(!empty($deletedItems)){
						$class_condition = '';
						$deleted_objects = array();

						if(defined('OBJECT_TABLE') && $table == OBJECT_TABLE){ // close all open objects, if a class is deleted
							$_deletedItems = array();

							// if its deleted and not selected, it must be an object
							foreach($deletedItems as $cur){
								if(in_array($cur, $selectedItems)){
									$_deletedItems[] = $cur;
								} else {
									$deleted_objects[] = $cur; // deleted objects when classes are deleted
								}
							}
							$deletedItems = $_deletedItems;
							$class_condition = ' || (_usedEditors[frameId].getEditorEditorTable() == "' . OBJECT_FILES_TABLE . '" && (_delete_objects.indexOf( "," + _usedEditors[frameId].getEditorDocumentId() + "," ) != -1) ) ';
						}

						if(defined('CUSTOMER_TABLE')){ // delete the customerfilters
							we_customer_documentFilter::deleteModel($deletedItems, $table);
							if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_TABLE){
								if(!empty($deleted_objects)){
									we_customer_documentFilter::deleteModel($deleted_objects, OBJECT_FILES_TABLE);
								}
							}
						}

						we_history::deleteFromHistory(
							$deletedItems, $table);
						if(defined('OBJECT_FILES_TABLE') && $table == OBJECT_TABLE){
							if(!empty($deleted_objects)){
								we_history::deleteFromHistory(
									$deleted_objects, OBJECT_FILES_TABLE);
							}
						}

						$script .= '
// close all Editors with deleted documents
var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();

// if a class is deleted, close all open objects of this class
var _delete_table = "' . $table . '";
var _delete_Ids = ",' . implode(",", $deletedItems) . ',";
var _delete_objects = ",' . implode(",", $deleted_objects) . ',";

for ( frameId in _usedEditors ) {

	if ( _delete_table == _usedEditors[frameId].getEditorEditorTable() && (_delete_Ids.indexOf( "," + _usedEditors[frameId].getEditorDocumentId() + "," ) != -1)
		' . $class_condition . '
		) {
		_usedEditors[frameId].setEditorIsHot(false);
		WE().layout.weEditorFrameController.closeDocument(frameId);
	}
}';
					}


					if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL){ //	different messages in normal or seeMode
						if(!empty($GLOBALS['we_folder_not_del'])){
							$_SESSION['weS']['delete_files_nok'] = array();
							$_SESSION['weS']['delete_files_info'] = str_replace('\n', '', sprintf(g_l('alert', '[folder_not_empty]'), ''));
							foreach($GLOBALS["we_folder_not_del"] as $datafile){
								$_SESSION['weS']['delete_files_nok'][] = array(
									'ContentType' => 'folder',
									"path" => $datafile
								);
							}
							$script .= 'new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR+"delInfo.php","we_delinfo",-1,-1,550,550,true,true,true);';
						} else {
							$delete_ok = g_l('alert', '[delete_ok]');
							$script .= we_message_reporting::getShowMessageCall($delete_ok, we_message_reporting::WE_MESSAGE_NOTICE);
						}
					}
				} else {
					switch($table){
						case TEMPLATES_TABLE:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[deleteTempl_notok_used]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						case OBJECT_TABLE:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[deleteClass_notok_used]'), we_message_reporting::WE_MESSAGE_ERROR);
							break;
						default:
							$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[delete_notok]'), we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
		}
	} else {
		$script .= we_message_reporting::getShowMessageCall(g_l('alert', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_WARNING);
	}
	echo we_html_element::jsElement($script);

	//exit;
}

//	in seeMode return to startDocument ...


if($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE){
	echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::jsElement(
			($retVal ? //	document deleted -> go to seeMode startPage
				we_message_reporting::getShowMessageCall(g_l('alert', '[delete_single][return_to_start]'), we_message_reporting::WE_MESSAGE_NOTICE) . "top.we_cmd('start_multi_editor');" :
				we_message_reporting::getShowMessageCall(g_l('alert', '[delete_single][no_delete]'), we_message_reporting::WE_MESSAGE_ERROR))
		), we_html_element::htmlBody());
	exit();
}
?>
<script><!--
<?php
if($wecmd0 != "delete_single_document"){ // no select mode in delete_single_document
	switch($table){
		case FILE_TABLE:
			if(permissionhandler::hasPerm("DELETE_DOC_FOLDER") && permissionhandler::hasPerm("DELETE_DOCUMENT")){
				echo 'top.treeData.setState(top.treeData.tree_states["select"]);';
			} elseif(permissionhandler::hasPerm("DELETE_DOCUMENT")){
				echo 'top.treeData.setState(top.treeData.tree_states["selectitem"]);';
			}
			break;
		case TEMPLATES_TABLE:
			if(permissionhandler::hasPerm("DELETE_TEMP_FOLDER") && permissionhandler::hasPerm("DELETE_TEMPLATE")){
				echo 'top.treeData.setState(top.treeData.tree_states["select"]);';
			} elseif(permissionhandler::hasPerm("DELETE_TEMPLATE")){
				echo 'top.treeData.setState(top.treeData.tree_states["selectitem"]);';
			}
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 1):
			if(permissionhandler::hasPerm("DELETE_OBJECTFILE")){
				echo 'top.treeData.setState(top.treeData.tree_states["select"]);';
			}
			break;
		case VFILE_TABLE:
			// FIXME: implement prefs for collections
			//if(permissionhandler::hasPerm("DELETE_DOC_FOLDER") && permissionhandler::hasPerm("DELETE_DOCUMENT")){
			echo 'top.treeData.setState(top.treeData.tree_states["select"]);';
			/*
			  } elseif(permissionhandler::hasPerm("DELETE_DOCUMENT")){
			  echo 'top.treeData.setState(top.treeData.tree_states["selectitem"]);';
			  }
			 *
			 */
			break;
		default:
			echo 'top.treeData.setState(top.treeData.tree_states["selectitem"]);';
	}
}
?>
if (top.treeData.table != "<?php echo $table; ?>") {
	top.treeData.table = "<?php echo $table; ?>";
	we_cmd("load", "<?php echo $table; ?>");
} else {
	top.drawTree();
}

function we_submitForm(target, url) {
	var f = self.document.we_form;
		if (!f.checkValidity()) {
		top.we_showMessage(WE().consts.g_l.main.save_error_fields_value_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);
		return false;
	}

	var sel = "";
	for (var i = 1; i <= top.treeData.len; i++) {
		if (top.treeData[i].checked == 1) {
			sel += (top.treeData[i].id + ",");
		}
	}
	if (!sel) {
		top.we_showMessage(WE().consts.g_l.main.nothing_to_delete, WE().consts.message.WE_MESSAGE_ERROR, window);
		return;
	}

	sel = sel.substring(0, sel.length - 1);

	f.sel.value = sel;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
	return true;
}
function we_cmd() {
	if (top.we_cmd) {
		top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}
//-->
</script>
<?php
if(!$wfchk && $wecmd0 != "delete"){
	echo $wfchk_html;
	exit();
}
if($wecmd0 === "do_delete"){
	echo '</head><body></body></html>';
	exit();
}

$delete_text = g_l('newFile', '[delete_text]');
$delete_confirm = g_l('alert', '[delete]');

$content = '<span class="middlefont">' . $delete_text . '</span>';

$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:if(confirm('" . $delete_confirm . "')) we_cmd('do_delete','','" . $table . "')"), "", we_html_button::create_button('quit_delete', "javascript:we_cmd('exit_delete','','" . $table . "')"), 10, "left");

$form = '<form name="we_form" method="post">' . we_html_tools::hidden('sel', '') . '</form>';

echo '</head><body class="weTreeHeader">
<div>
<h1 class="big" style="padding:0px;margin:0px;">' . oldHtmlspecialchars(g_l('newFile', '[title_delete]')) . '</h1>
<p class="small">' . $content . '</p>
<div>' . $_buttons . '</div></div>' . $form . '
</body>
</html>';
