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
class we_class_folder extends we_folder{
	var $ClassPath = ''; //#4076
	var $RootfolderID = ''; //#4076
	var $searchclass;
	var $GreenOnly = 0;
	var $Order = 'Path';
	private $searchView = 'properties';
	var $Search = '';
	var $SearchField = '';
	var $SearchStart = 0;
	var $TriggerID = 0;
	var $TableID = 0;

	public function __construct(){
		parent::__construct();
		$this->IsClassFolder = 1;
		array_push($this->persistent_slots, 'searchclass', 'TriggerID', 'TableID');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_FIELDS, we_base_constants::WE_EDITPAGE_INFO);
		}
		$this->ContentType = we_base_ContentTypes::FOLDER;
	}

	public function makeSameNew(array $keep = []){
		parent::makeSameNew(array_merge($keep, ['TableID', 'TriggerID', 'ClassPath', 'RootfolderID', 'ParentID', 'Table']));
	}

	private function setClassProp(){
		$sp = explode('/', $this->Path);
		$this->ClassPath = '/' . $sp[1];

		list($this->RootfolderID, $this->TableID) = (getHash('SELECT IFNULL(of.ID,0),o.ID FROM ' . OBJECT_TABLE . ' o LEFT JOIN ' . OBJECT_FILES_TABLE . ' of ON (of.TableID=o.ID AND of.IsClassFolder=1 AND of.Path=o.Path) WHERE o.Path="' . $this->DB_WE->escape($this->ClassPath) . '"', $this->DB_WE, MYSQL_NUM) ?: [
			0, 0]);
	}

	public function we_rewrite(){
		$this->ClassName = __CLASS__;
		return $this->we_save(false, true);
	}

	function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		if(is_object($this->searchclass)){
			//
		} else if(isset($_SESSION['weS']['we_objectSearch'][$this->ID])){
			$temp = $_SESSION['weS']['we_objectSearch'][$this->ID];
			$this->searchclass = $temp['Searchclass'];
			$this->SearchStart = $temp['SearchStart'];
			$this->GreenOnly = $temp['GreenOnly'];
			$this->EditPageNr = $temp['EditPageNr'];
			$this->Order = $temp['Order'];
		}

		if(!is_object($this->searchclass)){
			$this->searchclass = new we_object_search();
		}

		if(empty($this->EditPageNr)){
			$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
		}
		$this->setClassProp();
	}

	public function we_save($resave = false, $skipHook = false){
		$sp = explode('/', $this->Path);
		if(!empty($sp[2])){
			$this->IsClassFolder = 0;
		}
		parent::we_save($resave, $skipHook);
		return true;
	}

	public function initByPath($path, $tblName = OBJECT_FILES_TABLE, $skipHook = false){
		if(($id = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $path . '" AND IsFolder=1', '', $this->DB_WE))){
			$this->initByID($id, $tblName);
			return true;
		}
		## Folder does not exist, so we have to create it (if user has permissons to create folders)
		$spl = explode('/', $path);
		$folderName = array_pop($spl);
		$p = [];
		$anz = count($spl);
		$last_pid = 0;
		for($i = 0; $i < $anz; $i++){
			$p[] = array_shift($spl);
			$pa = $this->DB_WE->escape(implode('/', $p));
			if($pa){
				if(($pid = f('SELECT ID FROM ' . $this->DB_WE->escape($tblName) . ' WHERE Path="' . $pa . '"', '', $this->DB_WE))){
					$last_pid = $pid;
				} else {
					$folder = new self();
					$folder->init();
					$folder->Table = $tblName;
					$folder->ParentID = $last_pid;
					$folder->Text = $p[$i];
					$folder->Filename = $p[$i];
					$folder->IsClassFolder = ($i === 0);
					$folder->Path = $pa;
					$folder->save($skipHook);
					$last_pid = $folder->ID;
				}
			}
		}
		$this->init();
		$this->Table = $tblName;
		$this->ClassName = __CLASS__;
		$this->IsClassFolder = $last_pid == 0;
		$this->ParentID = $last_pid;
		$this->Text = $folderName;
		$this->Filename = $folderName;
		$this->Path = $path;
		//#4076
		$this->setClassProp();

		return $this->save(0, $skipHook);
	}

	protected function i_canSaveDirinDir(){
		if($this->ParentID == 0){
			return $this->IsClassFolder ? true : false;
		}
		$this->IsClassFolder = 0;
		return true;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		$this->ContentType = we_base_ContentTypes::FOLDER;

		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			//no break
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_FIELDS:
				return 'we_modules/object/we_classFolder_fields.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
		}
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = [0, $this->ID];
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$this->setClassProp();
		if($this->ID){
			$disabled = false;
			$disabledNote = '';
		} else {
			$disabled = true;
			$disabledNote = ' ' . g_l('weClass', '[availableAfterSave]');
		}

		$but = we_html_button::create_button(we_html_button::SELECT, $this->ID ? "javascript:we_cmd('we_selector_directory', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "', '" . $idname . "', '', ' 'copyFolderCheck," . $this->ID . "," . $this->Table . "," . implode(',', $parents) . "',''," . $this->RootfolderID . ");" : "javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folders_no_id]'), we_message_reporting::WE_MESSAGE_ERROR), '', 0, 0, "", "", $disabled);

		return '<table class="default" style="margin-bottom:2px;"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[copy_owners_expl]') . $disabledNote, we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
			we_html_element::htmlHidden($idname, $this->CopyID) . $but . '</td></tr></table>';
	}

	private function setDefaultWorkspaces(){
		$userWSArray = get_ws(FILE_TABLE, true);
		$userDefaultWsID = empty($userWSArray) ? 0 : $userWSArray[0];
		$userDefaultWsPath = $userDefaultWsID ? id_to_path($userDefaultWsID, FILE_TABLE, $GLOBALS['DB_WE']) : '/';
		$this->WorkspacePath = ($this->WorkspacePath ? $this->WorkspacePath : $userDefaultWsPath);
		$this->WorkspaceID = ($this->WorkspaceID != '') ? $this->WorkspaceID : $userDefaultWsID;
		return $userWSArray;
	}

	public function getSearch(){
		return ($this->searchView == 'properties' ? $this->searchProperties() : $this->searchFields());
	}

	function searchProperties(){
		$userWSArray = $this->setDefaultWorkspaces();
		$where = (isset($this->searchclass->searchname) ?
			$this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $this->TableID, -1, 0, "", 0) . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID) :
			$this->searchclass->greenOnly($this, $this->WorkspaceID, $this->TableID));
		$whereRestrictOwners = ' AND (of.RestrictOwners=0 OR of.CreatorID=' . intval($_SESSION['user']['ID']) . ' OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',of.Owners)) ';

		$this->searchclass->settable(OBJECT_X_TABLE . $this->TableID . ' obx JOIN ' . OBJECT_FILES_TABLE . ' of ON obx.OF_ID=of.ID');
		$this->searchclass->setwhere(($where ? $where . ' AND ' : '') . ' of.ID!=0 AND of.Path LIKE "' . $this->Path . '/%" AND of.IsFolder=0 ' . $whereRestrictOwners);
		$this->searchclass->searchquery('', 'obx.*,of.ID,of.Text,of.Path,of.ParentID,of.Workspaces,of.Published,of.IsSearchable,of.ModDate,of.Language,of.Url,of.TriggerID, of.ModDate, of.WebUserID, of.IsFolder');

		$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, "", $this->DB_WE));

		$ok = empty($DefaultValues['WorkspaceFlag']) ? '' : $DefaultValues['WorkspaceFlag'];

		$javascriptAll = "";
		$headline = [
				['dat' => ''],
				['dat' => g_l('modules_objectClassfoldersearch', '[zeige]')],
				['dat' => ''],
				['dat' => '<span onclick="setOrder(\'Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . $this->getSortImage('Path') . '</span>'],
				['dat' => '<span onclick="setOrder(\'ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . $this->getSortImage('ID') . '</span>'],
				['dat' => g_l('modules_objectClassfoldersearch', '[Arbeitsbereiche]')],
				['dat' => g_l('modules_objectClassfoldersearch', '[xtraArbeitsbereiche]')],
				['dat' => '<span onclick="setOrder(\'Published\');">' . g_l('modules_objectClassfoldersearch', '[Veroeffentlicht]') . $this->getSortImage('Published') . '</span>'],
			['dat' => '<span onclick="setOrder(\'ModDate\');">' . g_l('modules_objectClassfoldersearch', '[geaendert]') . $this->getSortImage('ModDate') . '</span>'],
				['dat' => '<span onclick="setOrder(\'Url\');">' . g_l('modules_objectClassfoldersearch', '[url]') . $this->getSortImage('Url') . '</span>'],
				['dat' => '<span onclick="setOrder(\'TriggerID\');">' . g_l('modules_objectClassfoldersearch', '[triggerid]') . $this->getSortImage('TriggerID') . '</span>'],
			['dat' => g_l('modules_objectClassfoldersearch', '[charset]')],
				['dat' => g_l('modules_objectClassfoldersearch', '[language]')],
				['dat' => '<span onclick="setOrder(\'WebUserID\');">' . g_l('modules_objectClassfoldersearch', '[WebUser]') . $this->getSortImage('WebUserID') . '</span>'],
		];

		$content = [];

		while($this->searchclass->next_record()){
			$stateclass = !$this->searchclass->f("Published") ? 'notpublished' : ($this->searchclass->f("ModDate") > $this->searchclass->f("Published") ? 'changed' : '');
			$content[] = [
					["align" => "center",
					'dat' => ((permissionhandler::hasPerm(["DELETE_OBJECTFILE", "NEW_OBJECTFILE"])) && permissionhandler::checkIfRestrictUserIsAllowed($this->searchclass->f("ID"), OBJECT_FILES_TABLE, $this->DB_WE) ?
					'<input type="checkbox" name="weg[' . $this->searchclass->f("ID") . ']" />' :
					'<i class="fa fa-square-o wecheckIcon disabled"></i>')],
					["align" => "center",
					"height" => 35,
					'dat' => (((we_users_util::in_workspace($this->WorkspaceID, $this->searchclass->f("Workspaces")) && $this->searchclass->f("Workspaces") != "") || ($this->searchclass->f("Workspaces") === "" && $ok)) ?
					'<i class="fa fa-lg fa-circle" style="color:#006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[visible_in_ws]') . '"></i>' :
					'<i class="fa fa-lg fa-circle" style="color:#E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[not_visible_in_ws]') . '"></i>')],
					['dat' => ($this->searchclass->f("IsSearchable") ?
					'<i class="fa fa-lg fa-circle" style="color:#006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') . '"></i>' :
					'<i class="fa fa-lg fa-circle" style="color:#E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]') . '"></i>')],
					['dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("ID") . ',\'objectFile\');" class="middlefont' . ($stateclass ? ' ' . $stateclass : '') . '" title="' . $this->searchclass->f("Path") . '">' . we_base_util::shortenPath($this->searchclass->f("Text"), 32) . '</a>'],
				['dat' => $this->searchclass->f("ID")],
					['dat' => $this->searchclass->getWorkspaces(makeArrayFromCSV($this->searchclass->f("Workspaces")), 32)],
					['dat' => '<nobr>' . ($this->searchclass->f("Published") ? date(g_l('date', '[format][default]'), $this->searchclass->f("Published")) : "-") . '</nobr>'],
					['dat' => '<nobr>' . ($this->searchclass->f("ModDate") ? date(g_l('date', '[format][default]'), $this->searchclass->f("ModDate")) : "-") . '</nobr>'],
					['dat' => $this->searchclass->f("Url")],
					['dat' => $this->searchclass->f("TriggerID") ? id_to_path($this->searchclass->f("TriggerID")) : ''],
					['dat' => $this->searchclass->f("Charset")],
					['dat' => $this->searchclass->f("Language")],
					['dat' => $this->searchclass->f("WebUserID")],
			];

			$javascriptAll .= "var flo=document.we_form.elements['weg[" . $this->searchclass->f("ID") . "]'].checked=true;";
		}

		return $this->getSearchresult($content, $headline, $javascriptAll);
	}

	function searchFields(){
		$this->setDefaultWorkspaces();

		if(we_base_request::_(we_base_request::STRING, 'do') === 'delete'){
			$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', []));
			foreach(array_keys($weg) as $ofid){//FIXME: this is not save
				if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
					we_base_delete::deleteEntry($ofid, OBJECT_FILES_TABLE, $this->DB_WE);
				}
			}
		}

		$where = (isset($this->searchclass->searchname) ?
			$this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $this->TableID, -1, 0, "", 0) . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID) :
			$this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID));
		$whereRestrictOwners = ' AND (of.RestrictOwners=0 OR of.CreatorID=' . intval($_SESSION['user']['ID']) . ' OR FIND_IN_SET(' . intval($_SESSION['user']["ID"]) . ',of.Owners)) ';

		$this->searchclass->settable(OBJECT_X_TABLE . $this->TableID . ' obx JOIN ' . OBJECT_FILES_TABLE . ' of ON obx.OF_ID=of.ID');
		$this->searchclass->setwhere(($where ? $where . ' AND ' : '') . 'of.Path LIKE "' . $this->Path . '/%" AND of.ID!=0 AND of.IsFolder=0 ' . $whereRestrictOwners);
		$this->searchclass->searchquery('', 'obx.*,of.ID,of.Text,of.Path,of.ParentID,of.Workspaces,of.Published,of.IsSearchable,of.Charset,of.Language,of.Url,of.TriggerID,of.ModDate,of.WebUserID');

		$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE));
		$ok = empty($DefaultValues["WorkspaceFlag"]) ? '' : $DefaultValues["WorkspaceFlag"];

		$javascriptAll = "";
		$headline = [['dat' => ''],
				['dat' => g_l('modules_objectClassfoldersearch', '[zeige]')],
				['dat' => ''],
				['dat' => '<span onclick="setOrder(\'Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . $this->getSortImage('Path') . '</span>'],
				['dat' => '<span onclick="setOrder(\'ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . $this->getSortImage('ID') . '</span>'],
		];

		$content = $head = $type = [];
		$f = 0;
		while($this->searchclass->next_record()){
			$stateclass = !$this->searchclass->f("Published") ? 'notpublished' : ($this->searchclass->f("ModDate") > $this->searchclass->f("Published") ? 'changed' : '');
			if($f == 0){
				$i = 5;
				$regs = [];
				foreach(array_keys($this->searchclass->getRecord()) as $key){
					if(preg_match('/(.+?)_(.*)/', $key, $regs)){
						switch($regs[1]){
							case "object":
								$type[$i] = $regs[1];
								$head[$i] = $regs[2];
								$headline[$i]['dat'] = f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($regs[2]), "", $this->DB_WE);
								$i++;
								break;
							case we_objectFile::TYPE_MULTIOBJECT:
								$type[$i] = $regs[1];
								$head[$i] = $regs[2];
								$headline[$i]['dat'] = $regs[2];
								$i++;
								break;
							default:
								if($regs[1] != 'OF'){
									$type[$i] = $regs[1];
									$head[$i] = $regs[2];
									$headline[$i]['dat'] = '<span onclick="setOrder(\'' . $key . '\');">' . $regs[2] . $this->getSortImage($key) . '</span>';
									$i++;
								}
						}
					}
				}
				$count = $i;
			}

			$javascriptAll .= "var flo=document.we_form.elements['weg[" . $this->searchclass->f("ID") . "]'].checked=true;";
			$content[$f] = [["align" => "center",
				'dat' => (permissionhandler::hasPerm("DELETE_OBJECTFILE") ?
				'<input type="checkbox" name="weg[' . $this->searchclass->f("ID") . ']" />' :
				'<i class="fa fa-square-o wecheckIcon disabled"></i>'
				)],
					["align" => "center",
					'dat' => '<i class="fa fa-lg fa-circle" style="color:#' . (((we_users_util::in_workspace($this->WorkspaceID, $this->searchclass->f("Workspaces")) && $this->searchclass->f("Workspaces") != "") || ($this->searchclass->f("Workspaces") === "" && $ok)) ?
					'006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[visible_in_ws]') . '"' : //blue
					'E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[not_visible_in_ws]') . '"'//grey
					) . '></i>'//FIXME: add text as in others shown
				],
					['dat' => '<i class="fa fa-lg fa-circle" style="color:' . ($this->searchclass->f("IsSearchable") ?
					'#006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') :
					'#E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]')) .
					'"></i>'
				],
					['dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("ID") . ',\'objectFile\');" class="middlefont' . ($stateclass ? ' ' . $stateclass : '') . '" title="' . $this->searchclass->f("Path") . '">' . we_base_util::shortenPath($this->searchclass->f("Text"), 32) . '</a>'],
				['dat' => $this->searchclass->f("ID")],
			];
			for($i = 5; $i < $count; $i++){
				switch($type[$i]){
					case 'date':
						$content[$f][$i]['dat'] = date(g_l('date', '[format][default]'), $this->searchclass->f($type[$i] . '_' . $head[$i]));
						break;
					case 'object':
						$content[$f][$i]['dat'] = '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f($type[$i] . '_' . $head[$i]) . ',\'objectFile\');" ' . ($this->searchclass->f('Published') ? '' : 'color:red;') . '" class="defaultfont" title="' . $this->searchclass->f('Path') . '">' . we_base_util::shortenPath($this->searchclass->f('Path'), 32) . '</a>';
						break;
					case we_objectFile::TYPE_MULTIOBJECT:
						$temp = we_unserialize($this->searchclass->f($type[$i] . '_' . $head[$i]));
						$objects = array_filter(isset($temp['objects']) ? $temp['objects'] : $temp);
						if($objects){
							$content[$f][$i]['dat'] = '<ul>';
							foreach($objects as $id){
								$path = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), '', $this->DB_WE);
								$content[$f][$i]['dat'] .= '<li><a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $id . ',\'objectFile\');" class="defaultfont" title="' . $path . '">' . we_base_util::shortenPath($path, 32) . '.</a></li>';
							}
							$content[$f][$i]['dat'] .= '</ul>';
						} else {
							$content[$f][$i]['dat'] = '-';
						}
						break;
					case 'checkbox':
						$text = $this->searchclass->f($type[$i] . '_' . $head[$i]);
						$content[$f][$i]['dat'] = g_l('global', ($text == '1' ? '[yes]' : '[no]'));
						break;
					case 'meta':
						if($this->searchclass->f($type[$i] . '_' . $head[$i]) != '' && isset($DefaultValues[$type[$i] . '_' . $head[$i]]["meta"][$this->searchclass->f($type[$i] . '_' . $head[$i])])){
							$text = $DefaultValues[$type[$i] . '_' . $head[$i]]['meta'][$this->searchclass->f($type[$i] . '_' . $head[$i])];
							$content[$f][$i]['dat'] = (strlen($text) > 20) ? substr($text, 0, 20) . " &hellip;" : $text;
						} else {
							$content[$f][$i]['dat'] = '&nbsp;';
						}
						break;
					case 'link':
						$text = $this->searchclass->f($type[$i] . '_' . $head[$i]);
						$content[$f][$i]['dat'] = we_document::getFieldLink($text, $GLOBALS['DB_WE']);
						break;
					case 'href':
						$text = $this->searchclass->f($type[$i] . '_' . $head[$i]);
						$content[$f][$i]['dat'] = we_document::getHrefByArray(we_unserialize($text));
						break;
					default:
						$text = strip_tags($this->searchclass->f($type[$i] . '_' . $head[$i]));
						$content[$f][$i]['dat'] = (strlen($text) > 20) ? substr($text, 0, 20) . ' &hellip;' : $text;
						break;
				}
			}

			$f++;
		}

		return $this->getSearchresult($content, $headline, $javascriptAll);
	}

	function getSearchDialog(){
		//#4076
		$this->setClassProp();
		$this->searchView = we_base_request::_(we_base_request::STRING, 'searchView', $this->searchView);

		$out = '
<table style="width:510px">
<form name="we_form_search" action="" onsubmit="sub();return false;" method="GET">
' . we_class::hiddenTrans() . '
<input type="hidden" name="todo" />
<input type="hidden" name="position" />';

		for($i = 0; $i <= $this->searchclass->height; $i++){
			$button = ($i == 0 ? '' : we_html_button::create_button(we_html_button::TRASH, "javascript:del(" . $i . ");", '', 0, 0, "", "", false) );

			if(isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) && (substr($this->searchclass->objsearchField[$i], 0, 4) === "meta" || substr($this->searchclass->objsearchField[$i], 0, 8) === "checkbox")){
				$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE));

				$values = (substr($this->searchclass->objsearchField[$i], 0, 4) === "meta" ?
					$DefaultValues[$this->searchclass->objsearchField[$i]]["meta"] :
					[0 => g_l('global', '[no]'),
					1 => g_l('global', '[yes]'),
					]
					);

				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', '[search]') . '</td>
	<td style="width:50px;"></td>'
					. '<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, $this->searchclass->objsearchField[$i], $this->ClassPath) . '</td>
	<td style="width:10px;"></td>
	<td style="width:50px;">' . we_search_base::getLocationMeta("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td style="width:10px;"></td>
	<td>' . we_html_tools::htmlSelect('objsearch[' . $i . ']', $values, 1, $this->searchclass->objsearch[$i]) . '</td>
	<td style="width:10px;"></td>
	<td style="text-align:right">' . $button . '</td>
</tr>';
			} elseif(isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) && substr($this->searchclass->objsearchField[$i], 0, 4) === "date"){
				$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), 'DefaultValues', $this->DB_WE));

				$month = ['' => ''];
				for($j = 1; $j <= 12; $j++){
					$month[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$day = ['' => ''];
				for($j = 1; $j <= 31; $j++){
					$day[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$hour = ['' => ''];
				for($j = 0; $j <= 23; $j++){
					$hour[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$minute = ['' => ''];
				for($j = 0; $j <= 59; $j++){
					$minute[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', '[search]') . '</td>
	<td style="width:5px;"></td>
	<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, $this->searchclass->objsearchField[$i], $this->ClassPath) . '</td>
	<td style="width:10px;"></td>
	<td>' . we_search_base::getLocationDate("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td style="width:10px;"></td>
	<td>' . we_html_tools::htmlTextInput('objsearch[' . $i . '][year]', 4, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['year']) ? $this->searchclass->objsearch[$i]['year'] : date("Y")), 4) . ' - ' .
					we_html_tools::htmlSelect('objsearch[' . $i . '][month]', $month, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['month']) ? $this->searchclass->objsearch[$i]['month'] : date("m"))) . ' - ' .
					we_html_tools::htmlSelect('objsearch[' . $i . '][day]', $day, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['day']) ? $this->searchclass->objsearch[$i]['day'] : date("d"))) . ' &nbsp;' .
					we_html_tools::htmlSelect('objsearch[' . $i . '][hour]', $hour, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['hour']) ? $this->searchclass->objsearch[$i]['hour'] : date("H"))) . ' : ' .
					we_html_tools::htmlSelect('objsearch[' . $i . '][minute]', $minute, 1, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]['minute']) ? $this->searchclass->objsearch[$i]['minute'] : date("i"))) .
					'</td>
	<td style="width:10px;"></td>
	<td style="text-align:right">' . $button . '</td>
</tr>';
			} else {
				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', '[search]') . '</td>
	<td></td>
	<td>' . $this->searchclass->getFields("objsearchField[" . $i . "]", 1, (isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) ? $this->searchclass->objsearchField[$i] : ""), $this->ClassPath) . '</td>
	<td></td>
	<td>' . we_search_base::getLocation("objlocation[" . $i . "]", (isset($this->searchclass->objlocation[$i]) ? $this->searchclass->objlocation[$i] : '')) . '</td>
	<td></td>
	<td>' . we_html_tools::htmlTextInput("objsearch[" . $i . "]", 30, (isset($this->searchclass->objsearch) && is_array($this->searchclass->objsearch) && isset($this->searchclass->objsearch[$i]) ? $this->searchclass->objsearch[$i] : ''), "", "", "text", 200) . '</td>
	<td></td>
	<td style="text-align:right">' . $button . '</td>
</tr>';
			}
		}

		$out .= '
<tr>
	<td colspan="9"><br/></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="3">' . we_html_button::create_button(we_html_button::ADD, "javascript:newinput();") . '</td>
	<td colspan="4" style="text-align:right">' . we_html_button::create_button(we_html_button::SEARCH, "javascript:sub();") . '</td>
</tr>
</form>
</table>';


		$this->searchclass->Order = we_base_request::_(we_base_request::STRING, 'Order', (empty($this->Order) ? 'Path' : $this->Order));
		$this->Order = $this->searchclass->Order;
		$this->searchclass->searchstart = we_base_request::_(we_base_request::INT, 'SearchStart', $this->searchclass->searchstart);
		$this->searchclass->anzahl = we_base_request::_(we_base_request::INT, 'Anzahl', $this->searchclass->anzahl);

		return $out;
	}

	function getSearchresult($content, $headline, $javascriptAll){
		$foundItems = $this->searchclass->maxItems;
		$yuiSuggest = & weSuggest::getInstance();

		$values = [10 => 10, 25 => 25, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, 10000 => 10000, 50000 => 50000, 100000 => 100000];

		// JS einbinden
		return $this->searchclass->getJSinWEsearchobj($this->Name) . '
<form name="we_form" method="post">' . we_class::hiddenTrans() .
			we_html_element::htmlHiddens(["Order" => $this->searchclass->Order,
				"do" => ''
				]) . '
<table class="default" style="margin-bottom:20px;">
	<tr>
		<td class="defaultfont lowContrast" style="margin-bottom:12px;">' . g_l('modules_objectClassfoldersearch', '[Verzeichnis]') . '</td>
		<td colspan="3">' . $this->formDirChooser(388, 0, FILE_TABLE, "WorkspacePath", "WorkspaceID", "opener.we_cmd('reload_editpage');", false) . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast" style="width:128px;">' . g_l('modules_objectClassfoldersearch', '[Ansicht]') . '</td>
		<td style="width:40px;">' . we_html_tools::htmlSelect("Anzahl", $values, 1, $this->searchclass->anzahl, "", ['onchange' => 'this.form.elements.SearchStart.value=0;we_cmd(\'reload_editpage\');']) .
			'</td>
		<td style="width:10px;">&nbsp;</td>
		<td style="width:350px;">' . we_html_forms::checkboxWithHidden($this->GreenOnly == 1 ? true : false, "we_" . $this->Name . "_GreenOnly", g_l('modules_objectClassfoldersearch', '[sicht]'), false, "defaultfont", "toggleShowVisible(document.getElementById('_we_" . $this->Name . "_GreenOnly'));") . '</td>	</tr>
<tr>
	<td class="defaultfont lowContrast" style="width:128px;">' . g_l('modules_objectClassfoldersearch', '[anzeige]') . '</td>
	<td colspan="3">' . we_html_tools::htmlSelect('searchView', ['properties' => g_l('weClass', '[properties]'), 'fields' => g_l('modules_objectClassfoldersearch', '[FELDER]')], 1, $this->searchView, "", [
				'onchange' => 'this.form.elements.SearchStart.value=0;submit();']) . '</td>
</tr>
</table>
	<table class="default" style="margin-bottom:12px;">
	<tr>
		<td class="defaultfont lowContrast" style="width:200px">' . (permissionhandler::hasPerm(["DELETE_OBJECTFILE", "NEW_OBJECTFILE"]) ? we_html_button::create_button(we_html_button::TOGGLE, "javascript: " . $javascriptAll) : "") .
			//(isset($this->searchclass->searchname) ? g_l('modules_objectClassfoldersearch', '[teilsuche]') : '') .
			'</td>
		<td style="text-align:right">' . $this->searchclass->getNextPrev($foundItems) . '</td>
	</tr>
	</table>
	<div id="scrollContent_DoclistSearch">' . we_html_tools::htmlDialogBorder3(900, $content, $headline) . '</div>
	<table class="default" style="margin:12px 0px;">
	<tr>
		<td style="width:200px;">' . (permissionhandler::hasPerm(["DELETE_OBJECTFILE", "NEW_OBJECTFILE"]) ? we_html_button::create_button(we_html_button::TOGGLE, "javascript: " . $javascriptAll) : "") . '</td>
		<td style="text-align:right">' . $this->searchclass->getNextPrev($foundItems) . '</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("DELETE_OBJECTFILE") ? we_html_button::create_button(we_html_button::TRASH, "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichloeschen]') . "'))document.we_form.elements.do.value='delete';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[loesch]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fa:btn_function_publish,fa-lg fa-globe', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichveroeffentlichen]') . "'))document.we_form.elements.do.value='publish';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[veroeffentlichen]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fa:btn_function_unpublish,fa-lg fa-moon-o', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichparken]') . "'))document.we_form.elements.do.value='unpublish';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[parken]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button(we_html_button::SEARCH, "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichsearchable]') . "'))document.we_form.elements.do.value='searchable';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[searchable]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fas:btn_function_unsearchable,fa-ban,fa-search', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichunsearchable]') . "'))document.we_form.elements.do.value='unsearchable';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[unsearchable]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fa:btn_function_copy,fa-lg fa-copy', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopychar]') . "'))document.we_form.elements.do.value='copychar';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[copychar]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fa:btn_function_copy,fa-lg fa-copy', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopyws]') . "')){document.we_form.elements.do.value='copyws';we_cmd('reload_editpage');}") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[copyws]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button('fa:btn_function_copy,fa-lg fa-copy', "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopytid]') . "'))document.we_form.elements.do.value='copytid';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[copytid]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
</table>
</form>
		' .
			weSuggest::getYuiFiles() .
			$yuiSuggest->getYuiJs();
	}

	function getSearchJS(){

		$ret = <<<EOF
function changeit(f){
EOF;
		$objID = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="' . $this->DB_WE->escape($this->Path) . '"', '', $this->DB_WE);
		if($objID){
			$tableInfo = $this->DB_WE->metadata(OBJECT_X_TABLE . $objID, we_database_base::META_NAME);

			foreach($tableInfo as $infoName){
//fixme: explode?
				$type = explode('_', $infoName);
				switch($type[0]){
					case 'meta':
						$ret .= "
if(f=='" . $infoName . "'){
	document.we_form_search.target='load';
	document.we_form_search.action=WE().consts.dirs.WE_MODULES_DIR+'object/search_submit.php';
	document.we_form_search.todo.value='changemeta';
	document.we_form_search.submit();
}";
						break;
					case 'date':
						$ret .= "
if(f=='" . $infoName . "'){
	document.we_form_search.target='load';
	document.we_form_search.action=WE().consts.dirs.WE_MODULES_DIR+'object/search_submit.php';
	document.we_form_search.todo.value='changedate';
	document.we_form_search.submit();
}";
						break;
					case 'checkbox':
						$ret .= "
if(f=='" . $infoName . "'){
	document.we_form_search.target='load';
	document.we_form_search.action=WE().consts.dirs.WE_MODULES_DIR+'object/search_submit.php';
	document.we_form_search.todo.value='changecheckbox';
	document.we_form_search.submit();
}";
						break;
				}
			}
		}
		$ret .= <<<EOF

		}
EOF;

		return we_html_element::jsElement($ret) . we_html_element::jsScript(WE_JS_MODULES_DIR . 'object/we_search.js');
	}

	function getSortImage($for){
		$ord = we_base_request::_(we_base_request::STRING, 'Order', '');
		if(strpos($ord, $for) === 0){
			if(strpos($ord, 'DESC')){
				return '<i class="fa fa-sort-desc fa-lg"></i>';
			}
			return '<i class="fa fa-sort-asc fa-lg"></i>';
		}
		return '<i class="fa fa-sort fa-lg"></i>';
	}

	function saveInSession(&$save, $toFile = false){
		parent::saveInSession($save, $toFile);

		if(!isset($_SESSION['weS']['we_objectSearch'])){
			$_SESSION['weS']['we_objectSearch'] = [];
		}
		$_SESSION['weS']['we_objectSearch'][$this->ID] = [
			'Searchclass' => $this->searchclass,
			'SearchStart' => $this->SearchStart,
			'GreenOnly' => $this->GreenOnly,
			'Order' => $this->Order,
			'EditPageNr' => $this->EditPageNr,
		];
	}

	public function deleteObjects(){
		$this->setClassProp(); //4076
		$javascript = '';
		$deletedItems = [];

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', []));
		foreach(array_keys($weg) as $tid){
			if(permissionhandler::checkIfRestrictUserIsAllowed($tid, OBJECT_FILES_TABLE, $this->DB_WE)){
				we_base_delete::deleteEntry($tid, OBJECT_FILES_TABLE, $this->DB_WE);
				$javascript .= 'top.treeData.deleteEntry(' . $tid . ');';
				$deletedItems[] = $tid;
			}
		}

		return $javascript . "
top.drawTree();

// close all Editors with deleted documents
var _usedEditors =  WE().layout.weEditorFrameController.getEditorsInUse();

var _delete_table = '" . OBJECT_FILES_TABLE . "';
var _delete_Ids = '," . implode(",", $deletedItems) . ",';

for ( frameId in _usedEditors ) {

	if ( _delete_table == _usedEditors[frameId].getEditorEditorTable() && (_delete_Ids.indexOf( ',' + _usedEditors[frameId].getEditorDocumentId() + ',' ) != -1) ) {
		_usedEditors[frameId].setEditorIsHot(false);
		WE().layout.weEditorFrameController.closeDocument(frameId);
	}
}";
	}

	function setObjectProperty($property = '', $value = false){
		$this->setClassProp();
		$IDs = array_map('intval', array_keys(array_filter(we_base_request::_(we_base_request::BOOL, 'weg', []))));

		if(!$IDs){
			return '';
		}
		switch($property){
			case 'IsSearchable':
				$value = intval($value);
				$set = [$property => $value];
				break;
			case 'TriggerID':
				$value = intval(f('SELECT DefaultTriggerID FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE));
				$set = [$property => $value];
				break;
			case 'Charset':
				$class = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, '', $this->DB_WE));
				$value = (isset($class['elements']['Charset']['dat']) ? $class["elements"]["Charset"]['dat'] : DEFAULT_CHARSET );
				$set = [$property => $value];
				break;
			case 'Workspaces':
				$class = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);
				$set = ['Workspaces' => $class['Workspaces'],
					'Templates' => $class['Templates'],
					];
				break;
			default:
				return '';
		}

		$whereRestrictOwners = ' AND (of.RestrictOwners=0 OR of.CreatorID=' . intval($_SESSION['user']['ID']) . ' OR FIND_IN_SET(' . intval($_SESSION['user']['ID']) . ',of.Owners))';
		$this->DB_WE->query('UPDATE ' . OBJECT_FILES_TABLE . ' of SET ' . we_database_base::arraySetter($set) . ' WHERE of.ID IN(' . implode(',', $IDs) . ') AND of.IsFolder=0' . $whereRestrictOwners);

		//change tblIndex
		switch($property){
			case 'IsSearchable':
				$value = intval($value);
				if($value){
					//FIXME: we have to add new entries
				} else {
					$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=' . $this->TableID . ' AND ID IN(' . implode(',', $IDs) . ') AND IsFolder=0' . $whereRestrictOwners);
				}
				return;
			case 'Workspaces':
				//FIXME: we have to add entries for all workspaces - in general we have to call we_objectFile::insertAtIndex - but this will be slow....
				//delete all unneeded workspaces
				if($class['Workspaces']){
					$this->DB_WE->query('DELETE FROM ' . INDEX_TABLE . ' WHERE ClassID=' . $this->TableID . ' AND ID IN(' . implode(',', $IDs) . ') AND IsFolder=0 AND WorkspaceID NOT IN(' . $class['Workspaces'] . ')' . $whereRestrictOwners);
				}
				return;
		}

		return '';
	}

	function publishObjects($publish = true){
		$this->setClassProp();
		$javascript = "";
		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', []));

		foreach(array_keys($weg) as $ofid){//FIXME: this is not save
			if(!permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
				continue;
			}
			$obj = new we_objectFile();
			$obj->initByID($ofid, OBJECT_FILES_TABLE);

			if($publish){
				$obj->getContentDataFromTemporaryDocs($ofid);
				$update = $obj->we_publish();
			} else {
				$update = $obj->we_unpublish();
			}

			if($update){
				$javascript .= "_EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();" .
					$obj->getUpdateTreeScript(false) . "
if(top.treeData.table!='" . OBJECT_FILES_TABLE . "') {
	 top.we_cmd('loadVTab', '" . OBJECT_FILES_TABLE . "', 0);
}
weWindow.treeData.selectNode(" . $GLOBALS['we_doc']->ID . ");";
			} else {

				$obj = new we_objectFile();
				$obj->initByID($ofid, OBJECT_FILES_TABLE);

				$obj->getContentDataFromTemporaryDocs($ofid);

				if($obj->we_publish()){
					$javascript .= "_EditorFrame = WE().layout.weEditorFrameController.getActiveEditorFrame();" .
						$obj->getUpdateTreeScript(false) . "
if(top.treeData.table!='" . OBJECT_FILES_TABLE . "') {
	top.we_cmd('loadVTab', '" . OBJECT_FILES_TABLE . "', 0);
}
weWindow.treeData.selectNode(" . $GLOBALS['we_doc']->ID . ");";
				}
			}
		}

		return $javascript;
	}

	protected function i_pathNotValid(){
		return $this->IsClassFolder ? false : (parent::i_pathNotValid() || $this->ParentID == 0 || $this->ParentPath === '/');
	}

}
