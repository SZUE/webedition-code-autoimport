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
class we_export_tree extends we_tree_base{

	public function __construct(we_base_jsCmd $jsCmd, $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		parent::__construct($jsCmd, $topFrame, $treeFrame, $cmdFrame);
		$this->autoload = false;
	}

	protected function customJSFile(){
		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_tree.js', 'initTree();');
	}

	function getHTMLMultiExplorer($width = 500, $height = 250, $useSelector = true, $selected = FILE_TABLE, $exportType = we_exim_ExIm::TYPE_WE){
		$js = $this->getJSTreeCode() . we_html_element::cssLink(CSS_DIR . 'tree.css');

		if($useSelector){
			$selectorAttribs = ['name' => 'headerSwitch',
				'onchange' => "we_cmd('setTreeHead', this.value)",
				'style' => 'width:' . $width . 'px;',
				'disabled' => ($exportType === we_exim_ExIm::TYPE_CSV ? 'disabled' : false)];
			$selector = new we_html_select(array_filter($selectorAttribs));

			if(we_base_permission::hasPerm('CAN_SEE_DOCUMENTS')){
				$selector->addOption(FILE_TABLE, g_l('export', '[documents]'));
			}
			if(we_base_permission::hasPerm('CAN_SEE_TEMPLATES')){
				$selector->addOption(TEMPLATES_TABLE, g_l('export', '[templates]'), ($exportType === we_exim_ExIm::TYPE_XML ? ['disabled' => 'disabled'] : []));
			}
			if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES")){
				$selector->addOption(OBJECT_FILES_TABLE, g_l('export', '[objects]'));
			}
			if(defined('OBJECT_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTS")){
				$selector->addOption(OBJECT_TABLE, g_l('export', '[classes]'), ($exportType === we_exim_ExIm::TYPE_XML ? ['disabled' => 'disabled'] : []));
			}
			if(we_base_moduleInfo::isActive(we_base_moduleInfo::COLLECTION) && we_base_permission::hasPerm("CAN_SEE_COLLECTIONS")){
				$selector->addOption(VFILE_TABLE, g_l('export', '[collections]'), ($exportType === we_exim_ExIm::TYPE_XML ? ['disabled' => 'disabled'] : []));
			}
			$selector->selectOption($selected);

			$header = we_html_element::htmlDiv(['style' => 'margin:5px 0px;'], $selector->getHtml());
		} else {
			$header = '';
		}
		return $js . $header . we_html_element::htmlDiv(['id' => 'treetable', 'class' => 'blockWrapper', 'style' => 'position: relative;width: ' . $width . 'px; height: ' . $height . 'px; border:1px #dce6f2 solid;'], '');
	}

	private static function getQueryParents($path){
		$out = [];
		while($path != '/' && $path){
			$out[] = 'Path="' . $path . '"';
			$path = dirname($path);
		}
		return $out ? implode(' OR ', $out) : '';
	}

	public static function getItems($ParentID, $offset = 0, $segment = 500, $sort = false){
		//unused
	}

	public static function getTreeItems($table, $ParentID, array &$treeItems, $of, we_database_base $DB_WE){
		static $openFolders = -1;
		if($openFolders == -1){
			$openFolders = $of;
		}

		$elem = 'ID,ParentID,Path,Text,IsFolder,ModDate,ContentType';

		switch($table){
			case FILE_TABLE :
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selDocs) : [];
				$elem .= ',Published,0 AS IsClassFolder';
				break;
			case (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE'):
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selTempl) : [];
				$elem .= ',ModDate AS Published,0 AS IsClassFolder';
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$elem .= ',Published,IsClassFolder';
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selObjs) : [];
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$selDocs = isset($_SESSION['weS']['export_session']) ? explode(',', $_SESSION['weS']['export_session']->selClasses) : [];
				$elem .= ',ModDate AS Published,0 AS IsClassFolder';
				break;
		}

		$DB_WE->query('SELECT ' . $elem . ' FROM ' . $DB_WE->escape($table) . ' WHERE ParentID=' . intval($ParentID) . ' AND((1' . we_users_util::makeOwnersSql() . ')' . $GLOBALS['wsQuery'] . ') ORDER BY IsFolder DESC,(text REGEXP "^[0-9]") DESC,ABS(text),Text');

		$entries = $DB_WE->getAll();

		foreach($entries as $entry){
			$ID = $entry['ID'];
			$OpenCloseStatus = in_array($ID, $openFolders);

			$treeItems[] = ['id' => $ID,
				'parentid' => $entry['ParentID'],
				'text' => $entry['Text'],
				'contenttype' => $entry['ContentType'],
				'isclassfolder' => $entry['IsClassFolder'],
				'table' => $table,
				'checked' => (!empty($selDocs) && in_array($ID, $selDocs)),
				'typ' => $entry['IsFolder'] ? 'group' : 'item',
				'open' => $OpenCloseStatus,
				'published' => ($entry['Published'] && ($entry['Published'] < $entry['ModDate'])) ? -1 : $entry['Published'],
				'disabled' => in_array($entry['Path'], $GLOBALS['parentpaths']),
				'tooltip' => $ID
			];

			if($entry['IsFolder'] && $OpenCloseStatus){
				static::getTreeItems($table, $ID, $treeItems, $of, $DB_WE);
			}
		}
	}

	public function loadHTML($table, $parentFolder, $openFolders){
		$GLOBALS['parentpaths'] = $wsQuery = [];

		if(($ws = get_ws($table, true))){
			$wsPathArray = id_to_path($ws, $table, $GLOBALS['DB_WE'], true);
			foreach($wsPathArray as $path){
				$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%" OR ' . self::getQueryParents($path);
				while($path != '/' && $path){
					$GLOBALS['parentpaths'][] = $path;
					$path = dirname($path);
				}
			}
		}

		switch($table){
			case FILE_TABLE:
				if(!we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
					return 0;
				}
				break;
			case TEMPLATES_TABLE:
				if(!we_base_permission::hasPerm("CAN_SEE_TEMPLATES")){
					return 0;
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(!we_base_permission::hasPerm("CAN_SEE_OBJECTFILES")){
					return 0;
				}
				if(!we_base_permission::hasPerm("ADMINISTRATOR")){
					$ac = we_users_util::getAllowedClasses($GLOBALS['DB_WE']);
					foreach($ac as $cid){
						$path = id_to_path($cid, OBJECT_TABLE);
						$wsQuery[] = 'Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%"';
						$wsQuery[] = 'Path="' . $GLOBALS['DB_WE']->escape($path) . '"';
					}
				}
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				if(!we_base_permission::hasPerm("CAN_SEE_OBJECTS")){
					return 0;
				}
				break;
			case VFILE_TABLE:
				if(!we_base_permission::hasPerm("CAN_SEE_COLLECTIONS")){
					return 0;
				}
				break;
			default:
				return 0;
		}

		$GLOBALS['wsQuery'] = ' ' . ($wsQuery ? ' OR (' . implode(' OR ', $wsQuery) . ')' : '');

		$treeItems = [];
		self::getTreeItems($table, $parentFolder, $treeItems, $openFolders, new DB_WE());

		$dynVars = [
			'parentFolder' => $parentFolder,
			'clear' => !$parentFolder,
			'treeItems' => $treeItems
		];

		echo we_html_tools::getHtmlTop('', '', '', we_html_element::jsScript(WE_JS_MODULES_DIR . 'export/export_cmd_loadTree.js', '', ['id' => 'loadVarExport_cmd_loadTree', 'data-cmdDynVars' => setDynamicVar($dynVars)]) . 
				$this->jsCmd->getCmds(), we_html_element::htmlBody());
	}

	public static function loadTree(){
		$topFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', "top", 4);
//added for export module.
		$treeFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', $topFrame . '.body', 5);
		$cmdFrame = we_base_request::_(we_base_request::STRING, 'we_cmd', $topFrame . '.cmd', 6);
		$jsCmd = new we_base_jsCmd();
		$tree = new we_export_tree($jsCmd);

		$table = we_base_request::_(we_base_request::TABLE, 'we_cmd', FILE_TABLE, 1);

		if($table === FILE_TABLE && !we_base_permission::hasPerm("CAN_SEE_DOCUMENTS")){
			if(we_base_permission::hasPerm("CAN_SEE_TEMPLATES")){
				$table = TEMPLATES_TABLE;
			} else if(defined('OBJECT_FILES_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTFILES")){
				$table = OBJECT_FILES_TABLE;
			} else if(defined('OBJECT_TABLE') && we_base_permission::hasPerm("CAN_SEE_OBJECTS")){
				$table = OBJECT_TABLE;
			}
		}

		$parentFolder = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 2);
		$openFolders = array_filter(we_base_request::_(we_base_request::INTLISTA, 'we_cmd', [], 3));

		$tree->loadHTML($table, $parentFolder, $openFolders);
	}

}
