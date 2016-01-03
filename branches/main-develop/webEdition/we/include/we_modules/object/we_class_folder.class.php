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
	var $Order = 'OF_Path';
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

	private function setClassProp(){
		$sp = explode('/', $this->Path);
		$this->ClassPath = '/' . $sp[1];

		list($this->RootfolderID, $this->TableID) = (getHash('SELECT IFNULL(of.ID,0),o.ID FROM ' . OBJECT_TABLE . ' o LEFT JOIN ' . OBJECT_FILES_TABLE . ' of ON (of.TableID=o.ID AND of.IsClassFolder=1 AND of.Path=o.Path) WHERE o.Path="' . $this->DB_WE->escape($this->ClassPath) . '"', $this->DB_WE, MYSQL_NUM)? : array(0, 0));
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
		} else {
			## Folder does not exist, so we have to create it (if user has permissons to create folders)
			$spl = explode('/', $path);
			$folderName = array_pop($spl);
			$p = array();
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
						$folder->IsClassFolder = $i == 0;

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

			$this->save(0, $skipHook);
		}
		return true;
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
/*			case we_base_constants::WE_EDITPAGE_CFWORKSPACE:
				return 'we_modules/object/we_classFolder_properties.inc.php';*/
			case we_base_constants::WE_EDITPAGE_FIELDS:
				return 'we_modules/object/we_classFolder_fields.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
		}
	}

	function getUserDefaultWsPath(){
		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = empty($userWSArray) ? 0 : $userWSArray[0];
		return (intval($userDefaultWsID) ?
						id_to_path($userDefaultWsID, FILE_TABLE, $GLOBALS['DB_WE']) :
						'/');
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$parents = array(0, $this->ID);
		we_getParentIDs(FILE_TABLE, $this->ID, $parents);
		$this->setClassProp();
		if($this->ID){
			$_disabled = false;
			$_disabledNote = '';
		} else {
			$_disabled = true;
			$_disabledNote = ' ' . g_l('weClass', '[availableAfterSave]');
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("var parents =[" . implode(',', $parents) . "];if(parents.indexOf(currentID) > -1){
			WE().util.showMessage(WE().consts.g_l.main.copy_folder_not_valid, WE().consts.message.WE_MESSAGE_ERROR, window);}else{opener.top.we_cmd('copyFolder', currentID," . $this->ID . ",1,'" . $this->Table . "');};");
		$but = we_html_button::create_button(we_html_button::SELECT, $this->ID ? "javascript:we_cmd('we_selector_directory', document.forms[0].elements['" . $idname . "'].value, '" . $this->Table . "', '" . $wecmdenc1 . "', '', '" . $wecmdenc3 . "',''," . $this->RootfolderID . ");" : "javascript:" . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folders_no_id]'), we_message_reporting::WE_MESSAGE_ERROR), true, 100, 22, "", "", $_disabled);

		return '<table class="default" style="margin-bottom:2px;"><tr><td>' . we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[copy_owners_expl]') . $_disabledNote, we_html_tools::TYPE_INFO, 388, false) . '</td><td>' .
				we_html_element::htmlHidden($idname, $this->CopyID) . $but . '</td></tr></table>';
	}

	function searchProperties(){
		$this->searchclass->Order = we_base_request::_(we_base_request::STRING, 'Order', (isset($this->Order) ? $this->Order : 'ModDate DESC'));
		$this->Order = we_base_request::_(we_base_request::STRING, 'Order');
		$this->searchclass->searchstart = we_base_request::_(we_base_request::INT, 'SearchStart', $this->searchclass->searchstart);
		$this->searchclass->anzahl = we_base_request::_(we_base_request::INT, 'Anzahl', $this->searchclass->anzahl);
		$we_obectPathLength = 32;

		$we_wsLength = $we_extraWsLength = 26;

		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = $userWSArray ? $userWSArray[0] : 0;
		//$userDefaultWsPath = (intval($userDefaultWsID) ? id_to_path($userDefaultWsID, FILE_TABLE, $this->DB_WE) : '/');
		//#4076
		$this->setClassProp();

		$userDefaultWsPath = $this->getUserDefaultWsPath();
		$this->WorkspacePath = ($this->WorkspacePath != '') ? $this->WorkspacePath : $userDefaultWsPath;
		$this->WorkspaceID = ($this->WorkspaceID != '') ? $this->WorkspaceID : $userDefaultWsID;

		$where = (isset($this->searchclass->searchname) ?
						'1 ' . $this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $this->TableID, -1, 0, "", 0) . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID) :
						'1' . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID));

		$this->searchclass->settable(OBJECT_X_TABLE . $this->TableID . ' JOIN ' . OBJECT_FILES_TABLE . ' ON ' . OBJECT_X_TABLE . $this->TableID . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID');
		$this->searchclass->setwhere($where . ' AND ' . OBJECT_X_TABLE . $this->TableID . '.OF_PATH LIKE "' . $this->Path . '/%" AND ' . OBJECT_X_TABLE . $this->TableID . '.OF_ID!=0');

		$foundItems = $this->searchclass->countitems();


		$this->searchclass->searchquery($where . ' AND ' . OBJECT_X_TABLE . $this->TableID . '.OF_PATH LIKE "' . $this->Path . '/%" AND ' . OBJECT_X_TABLE . $this->TableID . '.OF_ID!=0 AND ' . OBJECT_X_TABLE . $this->TableID . '.OF_ID = ' . OBJECT_FILES_TABLE . '.ID', OBJECT_X_TABLE . $this->TableID . '.OF_ID, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Text, ' . OBJECT_X_TABLE . $this->TableID . '.OF_ID, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Path, ' . OBJECT_X_TABLE . $this->TableID . '.OF_ParentID, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Workspaces, ' . OBJECT_X_TABLE . $this->TableID . '.OF_ExtraWorkspaces, ' . OBJECT_X_TABLE . $this->TableID . '.OF_ExtraWorkspacesSelected, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Published, ' . OBJECT_X_TABLE . $this->TableID . '.OF_IsSearchable, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Charset, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Language, ' . OBJECT_X_TABLE . $this->TableID . '.OF_Url, ' . OBJECT_X_TABLE . $this->TableID . '.OF_TriggerID, ' . OBJECT_FILES_TABLE . '.ModDate');


		$content = array();
		$foo = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, "", $this->DB_WE));

		$ok = isset($foo["WorkspaceFlag"]) ? $foo["WorkspaceFlag"] : "";

		$javascriptAll = "";
		if($foundItems){

			while($this->searchclass->next_record()){
				$content[] = array(
					array(
						"align" => "center",
						'dat' => ((permissionhandler::hasPerm("DELETE_OBJECTFILE") || permissionhandler::hasPerm("NEW_OBJECTFILE")) && permissionhandler::checkIfRestrictUserIsAllowed($this->searchclass->f("OF_ID"), OBJECT_FILES_TABLE, $this->DB_WE) ?
								'<input type="checkbox" name="weg[' . $this->searchclass->f("OF_ID") . ']" />' :
								'<i class="fa fa-square-o wecheckIcon disabled"></i>')),
					array(
						"align" => "center",
						"height" => 35,
						'dat' => ($this->searchclass->f("OF_Published") && (((in_workspace($this->WorkspaceID, $this->searchclass->f("OF_Workspaces")) && $this->searchclass->f("OF_Workspaces") != "") || (in_workspace($this->WorkspaceID, $this->searchclass->f("OF_ExtraWorkspacesSelected")) && $this->searchclass->f("OF_ExtraWorkspacesSelected") != "" ) ) || ($this->searchclass->f("OF_Workspaces") === "" && $ok)) ?
								'<i class="fa fa-lg fa-circle" style="color:#006DB8;"></i>' :
								'<i class="fa fa-lg fa-circle" style="color:#E7E7E7;"></i>')),
					array('dat' => ($this->searchclass->f("OF_IsSearchable") ?
								'<i class="fa fa-lg fa-circle" style="color:#006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') . '"></i>' :
								'<i class="fa fa-lg fa-circle" style="color:#E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]') . '"></i>')),
					array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . $this->searchclass->f("OF_ID") . '</a>'),
					array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . we_base_util::shortenPath($this->searchclass->f("OF_Text"), $we_obectPathLength) . '</a>'),
					array('dat' => $this->searchclass->getWorkspaces(makeArrayFromCSV($this->searchclass->f("OF_Workspaces")), $we_wsLength)),
					array('dat' => $this->searchclass->getExtraWorkspace(makeArrayFromCSV($this->searchclass->f("OF_ExtraWorkspaces")), $we_extraWsLength, $this->TableID, $userWSArray)),
					array('dat' => '<nobr>' . ($this->searchclass->f("OF_Published") ? date(g_l('date', '[format][default]'), $this->searchclass->f("OF_Published")) : "-") . '</nobr>'),
					array('dat' => '<nobr>' . ($this->searchclass->f("ModDate") ? date(g_l('date', '[format][default]'), $this->searchclass->f("ModDate")) : "-") . '</nobr>'),
					array('dat' => $this->searchclass->f("OF_Url")),
					array('dat' => $this->searchclass->f("OF_TriggerID") ? id_to_path($this->searchclass->f("OF_TriggerID")) : ''),
					array('dat' => $this->searchclass->f("OF_Charset")),
					array('dat' => $this->searchclass->f("OF_Language")),
				);

				$javascriptAll .= "var flo=document.we_form.elements['weg[" . $this->searchclass->f("OF_ID") . "]'].checked=true;";
			}
		} else {
			//echo "Leider nichts gefunden!";
		}

		$headline = array(
			array('dat' => ""),
			array('dat' => g_l('modules_objectClassfoldersearch', '[zeige]')),
			array('dat' => ""),
			array('dat' => '<a href="javascript:setOrder(\'OF_ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . '</a> ' . $this->getSortImage('OF_ID')),
			array('dat' => '<a href="javascript:setOrder(\'OF_Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . '</a> ' . $this->getSortImage('OF_Path')),
			array('dat' => g_l('modules_objectClassfoldersearch', '[Arbeitsbereiche]')),
			array('dat' => g_l('modules_objectClassfoldersearch', '[xtraArbeitsbereiche]')),
			array('dat' => '<a href="javascript:setOrder(\'OF_Published\');">' . g_l('modules_objectClassfoldersearch', '[Veroeffentlicht]') . '</a> ' . $this->getSortImage('OF_Published')),
			array('dat' => '<a href="javascript:setOrder(\'ModDate\');">' . g_l('modules_objectClassfoldersearch', '[geaendert]') . '</a> ' . $this->getSortImage('ModDate')),
			array('dat' => '<a href="javascript:setOrder(\'OF_Url\');">' . g_l('modules_objectClassfoldersearch', '[url]') . '</a> ' . $this->getSortImage('OF_Url')),
			array('dat' => '<a href="javascript:setOrder(\'OF_TriggerID\');">' . g_l('modules_objectClassfoldersearch', '[triggerid]') . '</a> ' . $this->getSortImage('OF_TriggerID')),
			array('dat' => g_l('modules_objectClassfoldersearch', '[charset]')),
			array('dat' => g_l('modules_objectClassfoldersearch', '[language]')),
		);

		return $this->getSearchresult($content, $headline, $foundItems, $javascriptAll);
	}

	function searchFields(){
		$order = we_base_request::_(we_base_request::STRING, 'Order', (isset($this->Order) ? $this->Order : 'OF_PATH'));
		if(stripos($order, "ModDate") === 0 || stripos($order, "OF_Published") === 0){
			$order = 'OF_PATH';
		}
		$this->searchclass->Order = $order;
		$this->Order = $order;

		$this->searchclass->searchstart = we_base_request::_(we_base_request::INT, "SearchStart", $this->searchclass->searchstart);
		$this->searchclass->anzahl = we_base_request::_(we_base_request::INT, 'Anzahl', $this->searchclass->anzahl);

		//$this->searchclass->setlimit(1);
		$we_obectPathLength = 32;
		$values = array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, 10000 => 10000, 50000 => 50000, 100000 => 100000);
		$strlen = 20;

		//#4076
		$this->setClassProp();

		if(we_base_request::_(we_base_request::STRING, 'do') === 'delete'){
			$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));
			foreach(array_keys($weg) as $ofid){//FIXME: this is not save
				if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
					we_base_delete::deleteEntry($ofid, OBJECT_FILES_TABLE, $this->DB_WE);
				}
			}
		}

		$userWSArray = makeArrayFromCSV(get_ws());

		$userDefaultWsID = !empty($userWSArray) ? $userWSArray[0] : 0;
		//$userDefaultWsPath = (intval($userDefaultWsID) ? id_to_path($userDefaultWsID, FILE_TABLE, $this->DB_WE) : '/');

		$fields = '*';

		$userDefaultWsPath = $this->getUserDefaultWsPath();
		$this->WorkspacePath = ($this->WorkspacePath != "") ? $this->WorkspacePath : $userDefaultWsPath;
		$this->WorkspaceID = ($this->WorkspaceID != "") ? $this->WorkspaceID : $userDefaultWsID;

		if(isset($this->searchclass->searchname)){
			$where = '1' . $this->searchclass->searchfor($this->searchclass->searchname, $this->searchclass->searchfield, $this->searchclass->searchlocation, OBJECT_X_TABLE . $this->TableID, $rows = -1, $start = 0, $order = "", $desc = 0) .
					$this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID);
		} else {
			$where = '1' . $this->searchclass->greenOnly($this->GreenOnly, $this->WorkspaceID, $this->TableID);
		}

		$this->searchclass->settable(OBJECT_X_TABLE . $this->TableID);
		//$this->searchclass->setwhere($where." AND OF_ID !=0 "); #4076 orig
		$this->searchclass->setwhere($where . ' AND OF_PATH LIKE "' . $this->Path . '/%" AND OF_ID!=0 ');


		$foundItems = $this->searchclass->countitems();

		//$this->searchclass->setorder($z);
		//$this->searchclass->setstart(1);
		//$this->searchclass->searchquery($where." AND OF_ID !=0 ",$fields); #4076 orig
		$this->searchclass->searchquery($where . ' AND OF_PATH LIKE "' . $this->Path . '/%" AND OF_ID !=0 ', $fields);

		$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE));

		$content = array();
		$foo = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, '', $this->DB_WE));

		$ok = isset($foo["WorkspaceFlag"]) ? $foo["WorkspaceFlag"] : "";

		$javascriptAll = "";
		$headline = array(
			array('dat' => ""),
			array('dat' => '<table class="default defaultfont"><tr><td>' . g_l('modules_objectClassfoldersearch', '[zeige]') . '</td><td></td></tr></table>'),
			array('dat' => ''),
			array('dat' => '<table class="default defaultfont"><tr><td><a href="javascript:setOrder(\'OF_ID\');">' . g_l('modules_objectClassfoldersearch', '[ID]') . '</a></td><td> ' . $this->getSortImage('OF_ID') . '</td></tr></table>'),
			array('dat' => '<table class="default defaultfont"><tr><td><a href="javascript:setOrder(\'OF_Path\');">' . g_l('modules_objectClassfoldersearch', '[Objekt]') . '</a></td><td> ' . $this->getSortImage('OF_Path') . '</td></tr></table>'),
		);

		if($foundItems){

			$f = 0;
			while($this->searchclass->next_record()){
				if($f == 0){
					$i = 0;
					$regs = array();
					foreach($this->searchclass->getRecord() as $key => $val){
						if(preg_match('/(.+?)_(.*)/', $key, $regs)){
							switch($regs[1]){
								case "object":
									$object[$i + 5] = $regs[2];
									$headline[$i + 5]['dat'] = '<table class="default defaultfont"><tr><td>' . f('SELECT Text FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($regs[2]), "Text", $this->DB_WE) . '</td><td></td></tr></table>';
									$type[$i + 5] = $regs[1];
									$i++;
									break;
								case we_objectFile::TYPE_MULTIOBJECT:
									$headline[$i + 5]['dat'] = '<table class="default defaultfont"><tr><td>' . $regs[2] . '</td><td></td></tr></table>';
									$head[$i + 5]['dat'] = $regs[2];
									$type[$i + 5] = $regs[1];
									$i++;
									break;
								default:
									if($regs[1] != "OF"){
										$headline[$i + 5]['dat'] = '<table class="default defaultfont"><tr><td><a href="javascript:setOrder(\'' . $key . '\');">' . $regs[2] . '</a></td><td> ' . $this->getSortImage($key) . '</td></tr></table>';
										$head[$i + 5]['dat'] = $regs[2];
										$type[$i + 5] = $regs[1];
										$i++;
									}
							}
						}
					}

					$count = $i;
				}

				$javascriptAll .= "var flo=document.we_form.elements['weg[" . $this->searchclass->f("OF_ID") . "]'].checked=true;";
				$content[$f] = array(
					array(
						"height" => 35,
						"align" => "center",
						'dat' => (permissionhandler::hasPerm("DELETE_OBJECTFILE") ?
								'<input type="checkbox" name="weg[' . $this->searchclass->f("OF_ID") . ']" />' :
								'<i class="fa fa-square-o wecheckIcon disabled"></i>'
						)),
					array(
						"align" => "center",
						'dat' => '<i class="fa fa-lg fa-circle" style="color:#' . ($this->searchclass->f("OF_Published") && (((in_workspace($this->WorkspaceID, $this->searchclass->f("OF_Workspaces")) && $this->searchclass->f("OF_Workspaces") != "") || (in_workspace($this->WorkspaceID, $this->searchclass->f("OF_ExtraWorkspacesSelected")) && $this->searchclass->f("OF_ExtraWorkspacesSelected") != "" ) ) || ($this->searchclass->f("OF_Workspaces") === "" && $ok)) ?
								'006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[Veroeffentlicht]') . '"' : //blue
								'E7E7E7;" title="' . g_l('searchtool', '[geparkt]') . '"'//grey
						) . '></i>'//FIXME: add text as in others shown
					),
					array(
						'dat' => '<i class="fa fa-lg fa-circle" style="color:' . ($this->searchclass->f("OF_IsSearchable") ?
								'#006DB8;" title="' . g_l('modules_objectClassfoldersearch', '[issearchable]') :
								'#E7E7E7;" title="' . g_l('modules_objectClassfoldersearch', '[isnotsearchable]')) .
						'"></i>'
					),
					array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" class="middlefont" title="' . $this->searchclass->f("OF_Path") . '">' . $this->searchclass->f("OF_ID") . '</a>'),
					array('dat' => '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f("OF_ID") . ',\'objectFile\');" class="defaultfont" title="' . $this->searchclass->f("OF_Path") . '">' . we_base_util::shortenPath($this->searchclass->f("OF_Text"), $we_obectPathLength) . '</a>'),
				);
				for($i = 0; $i < $count; $i++){
					switch($type[$i + 5]){
						case "date":
							$content[$f][$i + 5]['dat'] = date(g_l('date', '[format][default]'), $this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]['dat']));
							break;
						case "object":
							$tmp = f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $object[$i + 5] . ' WHERE OF_ID="' . $this->searchclass->f($type[$i + 5] . '_' . $object[$i + 5]) . '"', '', $this->DB_WE);
							if($tmp != ""){
								$publ = f("SELECT Published FROM " . OBJECT_FILES_TABLE . ' WHERE ID="' . $this->searchclass->f($type[$i + 5] . '_' . $object[$i + 5]) . '"', '', $this->DB_WE);
								$obj = '<a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $this->searchclass->f($type[$i + 5] . "_" . $object[$i + 5]) . ',\'objectFile\');" ' . ($publ ? '' : 'color:red;') . '" class="defaultfont" title="' . $tmp . '">' . we_base_util::shortenPath(f("SELECT OF_Path FROM " . OBJECT_X_TABLE . $object[$i + 5] . ' WHERE OF_ID="' . $this->searchclass->f($type[$i + 5] . '_' . $object[$i + 5]) . '"', '', $this->DB_WE), $we_obectPathLength) . '</a>';
							} else {
								$obj = "&nbsp;";
							}
							$content[$f][$i + 5]['dat'] = $obj;
							break;
						case we_objectFile::TYPE_MULTIOBJECT:
							$temp = we_unserialize($this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']));
							$objects = array_filter(isset($temp['objects']) ? $temp['objects'] : $temp);
							if($objects){
								$content[$f][$i + 5]['dat'] = '<ul>';
								foreach($objects as $id){
									$content[$f][$i + 5]['dat'] .= '<li><a href="javascript:WE().layout.weEditorFrameController.openDocument(\'' . OBJECT_FILES_TABLE . '\',' . $id . ',\'objectFile\');" class="defaultfont" title="' . f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $id, '', $this->DB_WE) . '">' . we_base_util::shortenPath(f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id), '', $this->DB_WE), $we_obectPathLength) . '.</a></li>';
								}
								$content[$f][$i + 5]['dat'] .= '</ul>';
							} else {
								$content[$f][$i + 5]['dat'] = '-';
							}
							break;
						case 'checkbox':
							$text = $this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']);
							$content[$f][$i + 5]['dat'] = g_l('global', ($text == '1' ? '[yes]' : '[no]'));
							break;
						case 'meta':
							if($this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']) != '' && isset($DefaultValues[$type[$i + 5] . '_' . $head[$i + 5]['dat']]["meta"][$this->searchclass->f($type[$i + 5] . "_" . $head[$i + 5]['dat'])])){
								$text = $DefaultValues[$type[$i + 5] . '_' . $head[$i + 5]['dat']]['meta'][$this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat'])];
								$content[$f][$i + 5]['dat'] = (strlen($text) > $strlen) ? substr($text, 0, $strlen) . " ..." : $text;
							} else {
								$content[$f][$i + 5]['dat'] = '&nbsp;';
							}
							break;
						case 'link':
							$text = $this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']);
							//FIXME: this is not php compliant getFieldByVal is a dynamic method - and must be
							$content[$f][$i + 5]['dat'] = we_document::getFieldByVal($text, "link");
							break;
						case 'href':
							$text = $this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']);
							$content[$f][$i + 5]['dat'] = we_document::getHrefByArray(we_unserialize($text));
							//$text = $DefaultValues[$type[$i+3]."_".$head[$i+3]['dat']]["meta"][$this->searchclass->f($type[$i+3]."_".$head[$i+3]['dat'])];
							//$content[$f][$i+3]['dat'] = "TEST";
							break;
						default:
							$text = strip_tags($this->searchclass->f($type[$i + 5] . '_' . $head[$i + 5]['dat']));
							$content[$f][$i + 5]['dat'] = (strlen($text) > $strlen) ? substr($text, 0, $strlen) . ' ...' : $text;
							break;
					}
				}

				$f++;
			}
			//} else{
			//$out .= "Leider nichts gefunden!";
		}
		return $this->getSearchresult($content, $headline, $foundItems, $javascriptAll);
	}

	function getSearchDialog(){
		//#4076
		$this->setClassProp();

		$out = '
<table style="width:510px">
<form name="we_form_search" action="" onsubmit="sub();return false;" methode="GET">
' . we_class::hiddenTrans() . '
<input type="hidden" name="todo" />
<input type="hidden" name="position" />';

		for($i = 0; $i <= $this->searchclass->height; $i++){

			$button = ($i == 0 ?
							'' :
							we_html_button::create_button(we_html_button::TRASH, "javascript:del(" . $i . ");", true, 26, 22, "", "", false)
					);


			if(isset($this->searchclass->objsearchField) && is_array($this->searchclass->objsearchField) && isset($this->searchclass->objsearchField[$i]) && (substr($this->searchclass->objsearchField[$i], 0, 4) === "meta" || substr($this->searchclass->objsearchField[$i], 0, 8) === "checkbox")){
				$DefaultValues = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE));

				$values = (substr($this->searchclass->objsearchField[$i], 0, 4) === "meta" ?
								$DefaultValues[$this->searchclass->objsearchField[$i]]["meta"] :
								array(
							0 => g_l('global', '[no]'),
							1 => g_l('global', '[yes]'),
								)
						);

				$out .= '
<tr>
	<td class="defaultfont">' . g_l('global', '[search]') . '</td>
	<td width="50"></td>'
						//<td>'.$this->searchclass->getFields("objsearchField[".$i."]",1,$this->searchclass->objsearchField[$i],$this->Path).'</td> #4076 orig
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

				$month = array('' => '');
				for($j = 1; $j <= 12; $j++){
					$month[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$day = array('' => '');
				for($j = 1; $j <= 31; $j++){
					$day[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$hour = array('' => '');
				for($j = 0; $j <= 23; $j++){
					$hour[$j] = ($j < 10 ? '0' : '') . $j;
				}

				$minute = array('' => '');
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

		return $out;
	}

	function getSearchresult($content, $headline, $foundItems, $javascriptAll){
		$yuiSuggest = & weSuggest::getInstance();

		$values = array(10 => 10, 25 => 25, 50 => 50, 100 => 100, 500 => 500, 1000 => 1000, 5000 => 5000, 10000 => 10000, 50000 => 50000, 100000 => 100000);

		// JS einbinden
		return $this->searchclass->getJSinWEsearchobj($this->Name) . '
<form name="we_form" method="post">
' . we_class::hiddenTrans() . '
<table class="default" style="margin-bottom:20px;">
	<tr>
		<td class="defaultfont lowContrast" style="margin-bottom:12px;">' . g_l('modules_objectClassfoldersearch', '[Verzeichnis]') . '</td>
		<td colspan="3">' . $this->formDirChooser(388, 0, FILE_TABLE, "WorkspacePath", "WorkspaceID", "opener.we_cmd('reload_editpage');", false) . '</td>
	</tr>
	<tr>
		<td class="defaultfont lowContrast" style="width:128px;">' . g_l('modules_objectClassfoldersearch', '[Ansicht]') . '</td>
		<td style="width:40px;">' . we_html_tools::htmlSelect("Anzahl", $values, 1, $this->searchclass->anzahl, "", array('onchange' => 'this.form.elements.SearchStart.value=0;we_cmd(\'reload_editpage\');')) .
				we_html_tools::hidden("Order", $this->searchclass->Order) .
				we_html_tools::hidden("do", "") .
				'</td>
		<td style="width:10px;">&nbsp;</td>
		<td style="width:350px;">' . we_html_forms::checkboxWithHidden($this->GreenOnly == 1 ? true : false, "we_" . $this->Name . "_GreenOnly", g_l('modules_objectClassfoldersearch', '[sicht]'), false, "defaultfont", "toggleShowVisible(document.getElementById('_we_" . $this->Name . "_GreenOnly'));") . '</td>
	</tr>
	</table>
	<table class="default" style="margin-bottom:12px;">
	<tr>
		<td class="defaultfont lowContrast" style="width:200px">' . (isset($this->searchclass->searchname) ? g_l('modules_objectClassfoldersearch', '[teilsuche]') : '') . '</td>
		<td style="text-align:right">' . $this->searchclass->getNextPrev($foundItems) . '</td>
	</tr>
	</table>' .
				we_html_tools::htmlDialogBorder3(900, 0, $content, $headline) . '
	<table class="default" style="margin:12px 0px;">
	<tr>
		<td style="width:200px;">' . (permissionhandler::hasPerm("DELETE_OBJECTFILE") || permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("selectAllObjects", "javascript: " . $javascriptAll) : "") . '</td>
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
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fa:btn_function_publish,fa-lg fa-sun-o", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichveroeffentlichen]') . "'))document.we_form.elements.do.value='publish';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[veroeffentlichen]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fa:btn_function_unpublish,fa-lg fa-moon-o", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichparken]') . "'))document.we_form.elements.do.value='unpublish';we_cmd('reload_editpage');") . '</td>
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
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fas:btn_function_unsearchable,fa-ban,fa-search", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichunsearchable]') . "'))document.we_form.elements.do.value='unsearchable';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[unsearchable]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fa:btn_function_copy,fa-lg fa-copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopychar]') . "'))document.we_form.elements.do.value='copychar';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[copychar]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="margin-bottom:12px;">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fa:btn_function_copy,fa-lg fa-copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopyws]') . "'))document.we_form.elements.do.value='copyws';we_cmd('reload_editpage');") . '</td>
				<td class="small" style="padding-left:1em;">' . g_l('modules_objectClassfoldersearch', '[copyws]') : "") . '</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<table class="default">
			<tr>
				<td class="small">' . (permissionhandler::hasPerm("NEW_OBJECTFILE") ? we_html_button::create_button("fa:btn_function_copy,fa-lg fa-copy", "javascript: if(confirm('" . g_l('modules_objectClassfoldersearch', '[wirklichcopytid]') . "'))document.we_form.elements.do.value='copytid';we_cmd('reload_editpage');") . '</td>
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
		$modulepath = WE_OBJECT_MODULE_DIR;

		$ret = <<<EOF
function sub(){
	document.we_form_search.target="load";
	document.getElementsByName("SearchStart")[0].value=0;
	document.we_form_search.action="{$modulepath}search_submit.php";
	document.we_form_search.todo.value="search";
	document.we_form_search.submit();


}

function newinput(){
	document.we_form_search.target='load';
	document.we_form_search.action='{$modulepath}search_submit.php';
	document.we_form_search.todo.value="add";
	document.we_form_search.submit();
}

function del(pos){
	document.we_form_search.target='load';
	document.we_form_search.action='{$modulepath}search_submit.php';
	document.we_form_search.todo.value="delete";
	document.we_form_search.position.value=pos;
	document.we_form_search.submit();
}

function changeit(f){
EOF;
		$objID = f('SELECT ID FROM ' . OBJECT_TABLE . ' WHERE Path="' . $this->DB_WE->escape($this->Path) . '"', '', $this->DB_WE);
		if($objID){
			$tableInfo = $this->DB_WE->metadata(OBJECT_X_TABLE . $objID);

			for($i = 0; $i < count($tableInfo); $i++){
//fixme: explode?
				$type = explode('_', $tableInfo[$i]["name"]);
				switch($type[0]){
					case 'meta':

						$ret.= "
if(f=='" . $tableInfo[$i]["name"] . "'){
	document.we_form_search.target='load';
	document.we_form_search.action='{$modulepath}search_submit.php';
	document.we_form_search.todo.value='changemeta';
	document.we_form_search.submit();
}";
						break;
					case 'date':
						$ret.= "
if(f=='" . $tableInfo[$i]["name"] . "'){
	document.we_form_search.target='load';
	document.we_form_search.action='{$modulepath}search_submit.php';
	document.we_form_search.todo.value='changedate';
	document.we_form_search.submit();
}";
						break;
					case 'checkbox':
						$ret.= "
if(f=='" . $tableInfo[$i]["name"] . "'){
	document.we_form_search.target='load';
	document.we_form_search.action='{$modulepath}search_submit.php';
	document.we_form_search.todo.value='changecheckbox';
	document.we_form_search.submit();
}";
						break;
				}
			}
		}
		$ret .= <<<EOF

		}

function changeitanyway(f){
		document.we_form_search.target='load';
		document.we_form_search.action='{$modulepath}search_submit.php';
		document.we_form_search.todo.value="changemeta";
		document.we_form_search.submit();
}
EOF;

		return we_html_element::jsElement($ret);
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

	function saveInSession(&$save){
		parent::saveInSession($save);

		if(!isset($_SESSION['weS']['we_objectSearch'])){
			$_SESSION['weS']['we_objectSearch'] = array();
		}
		$_SESSION['weS']['we_objectSearch'][$this->ID] = array(
			'Searchclass' => $this->searchclass,
			'SearchStart' => $this->SearchStart,
			'GreenOnly' => $this->GreenOnly,
			'Order' => $this->Order,
			'EditPageNr' => $this->EditPageNr,
		);
	}

	public function deleteObjects(){
		$this->setClassProp(); //4076
		$javascript = '';
		$deletedItems = array();

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));
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

	function copyWSfromClass(){
		$this->setClassProp(); //4076
		$foo = getHash('SELECT Workspaces,Templates FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), $this->DB_WE);

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));
		foreach(array_keys($weg) as $ofid){
			if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
				$obj = new we_objectFile();
				$obj->initByID($ofid, OBJECT_FILES_TABLE);
				$obj->getContentDataFromTemporaryDocs($ofid);
				$obj->Workspaces = $foo["Workspaces"];
				$obj->Templates = $foo["Templates"];
				$obj->ExtraTemplates = "";
				$obj->ExtraWorkspaces = "";
				$obj->ExtraWorkspacesSelected = "";
				$oldModDate = $obj->ModDate;
				$obj->we_save(false, true);
				if($obj->Published != 0 && $obj->Published == $oldModDate){
					$obj->we_publish(false, true, true);
				}
			}
		}
		return 'top.drawTree();';
	}

	function copyCharsetfromClass(){
		$this->setClassProp();
		$fooo = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->TableID, '', $this->DB_WE));
		$Charset = (isset($fooo["elements"]["Charset"]['dat']) ? $fooo["elements"]["Charset"]['dat'] : DEFAULT_CHARSET );

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));

		foreach(array_keys($weg) as $ofid){
			if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
				$obj = new we_objectFile();
				$obj->initByID($ofid, OBJECT_FILES_TABLE);
				$obj->getContentDataFromTemporaryDocs($ofid);
				$obj->Charset = $Charset;
				$oldModDate = $obj->ModDate;
				$obj->we_save(false, true);
				if($obj->Published != 0 && $obj->Published == $oldModDate){
					$obj->we_publish(false, true, true);
				}
			}
		}
		return '';
	}

	function copyTIDfromClass(){
		$this->setClassProp();
		$DefaultTriggerID = f('SELECT DefaultTriggerID FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($this->TableID), '', $this->DB_WE);

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));
		foreach(array_keys($weg) as $ofid){
			if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
				$obj = new we_objectFile();
				$obj->initByID($ofid, OBJECT_FILES_TABLE);
				$obj->getContentDataFromTemporaryDocs($ofid);
				$obj->TriggerID = $DefaultTriggerID;
				$oldModDate = $obj->ModDate;
				$obj->we_save(false, true);
				if($obj->Published != 0 && $obj->Published == $oldModDate){
					$obj->we_publish(false, true, true);
				}
			}
		}
		return '';
	}

	function searchableObjects($searchable = true){
		$this->setClassProp();

		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));
		foreach(array_keys($weg) as $ofid){

			if(permissionhandler::checkIfRestrictUserIsAllowed($ofid, OBJECT_FILES_TABLE, $this->DB_WE)){
				$obj = new we_objectFile();
				$obj->initByID($ofid, OBJECT_FILES_TABLE);
				$obj->getContentDataFromTemporaryDocs($ofid);
				$obj->IsSearchable = ($searchable != true ? 0 : 1);
				$oldModDate = $obj->ModDate;
				$obj->we_save(false, true);
				if($obj->Published != 0 && $obj->Published == $oldModDate){
					$obj->we_publish(false, true, true);
				}
			}
		}

		return '';
	}

	function publishObjects($publish = true){
		$this->setClassProp();
		$javascript = "";
		$weg = array_filter(we_base_request::_(we_base_request::BOOL, 'weg', array()));

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
						//.	"_EditorFrame.setEditorDocumentId(".$obj->ID.");\n"
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
							//.	"_EditorFrame.setEditorDocumentId(".$obj->ID.");\n"
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
