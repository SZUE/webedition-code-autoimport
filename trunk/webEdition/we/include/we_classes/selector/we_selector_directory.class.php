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
class we_selector_directory extends we_selector_file{
	protected $userCanMakeNewFolder = true;
	protected $userCanRenameFolder = true;
	protected $we_editDirID = "";
	protected $FolderText = '';

	function __construct($id, $table = "", $JSIDName = "", $JSTextName = "", $JSCommand = "", $order = "", $sessionID = "", $we_editDirID = 0, $FolderText = "", $rootDirID = 0, $multiple = 0, $filter = '', $startID = 0){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, $rootDirID, $multiple, $filter, $startID);
		switch($this->table){
			case FILE_TABLE:
			case TEMPLATES_TABLE:
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->fields.= ',ModDate,RestrictOwners,Owners,OwnersReadOnly,CreatorID';
				break;
			default:
		}
		$this->title = g_l('fileselector', '[dirSelector][title]');
		$this->userCanMakeNewFolder = $this->userCanMakeNewDir();
		$this->userCanRenameFolder = $this->userCanRenameFolder();
		$this->we_editDirID = $we_editDirID;
		$this->FolderText = $FolderText;
	}

	function printHTML($what = we_selector_file::FRAMESET, $withPreview = true){
		switch($what){
			case self::SETDIR:
				$this->printSetDirHTML();
				break;
			case self::NEWFOLDER:
				$this->printNewFolderHTML();
				break;
			case self::CREATEFOLDER:
				$this->printCreateFolderHTML();
				break;
			case self::RENAMEFOLDER:
				$this->printRenameFolderHTML();
				break;
			case self::DORENAMEFOLDER:
				$this->printDoRenameFolderHTML();
				break;
			case self::PREVIEW:
				$this->printPreviewHTML();
				break;
			default:
				parent::printHTML($what, $withPreview);
		}
	}

	protected function printCmdHTML($morejs = ''){
		parent::printCmdHTML(
			($this->userCanMakeNewFolder ? 'top.enableNewFolderBut();' : 'top.disableNewFolderBut();') .
			$morejs
		);
	}

	function query(){
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->dir) . ' AND((1' . we_users_util::makeOwnersSql() . ') ' .
			getWsQueryForSelector($this->table) . ')' . ($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : ''));
	}

	protected function setDefaultDirAndID($setLastDir){
		$this->dir = $this->startID ? : ($setLastDir ? (isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0 ) : 0);
		$ws = get_ws($this->table, true);
		if($ws && in_array($this->dir, $ws)){
			$this->dir = "";
		}
		$this->id = $this->dir;
		if($this->rootDirID){
			if(!in_parentID($this->dir, $this->rootDirID, $this->table, $this->db)){
				$this->dir = $this->rootDirID;
				$this->id = $this->rootDirID;
			}
		}
		$this->path = '';

		$this->values = array(
			'ParentID' => 0,
			'Text' => '/',
			'Path' => '/',
			'IsFolder' => 1,
			'ModDate' => 0,
			'RestrictOwners' => 0,
			'Owners' => '',
			'OwnersReadOnly' => '',
			'CreatorID' => 0);
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "what=$what&rootDirID=" . $this->rootDirID . "&table=" . $this->table . "&id=" . $this->id . "&startID=" . $this->startID . "&order=" . $this->order . "&open_doc=" . $this->open_doc;
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() .
			we_html_element::jsScript(JS_DIR . 'selectors/directory_selector.js');
	}

	protected function printCmdAddEntriesHTML(){
		$this->query();
		$ret = '';
		while($this->db->next_record()){
			$ret.='top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Text") . '",' . $this->db->f("IsFolder") . ',"' . $this->db->f("Path") . '","' . date(g_l('date', '[format][default]'), (is_numeric($this->db->f("ModDate")) ? $this->db->f("ModDate") : 0)) . '","folder");';
		}
		$ret.=' function startFrameset(){
top.' . ($this->userCanMakeNewDir() ? 'enable' : 'disable') . 'NewFolderBut();}';
		return $ret;
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines">
	<tr>
		<th class="selector treeIcon"></th>
		<th class="selector directory"><a href="#" onclick="javascript:top.orderIt(\'Text\');">' . g_l('fileselector', '[filename]') . '</a></th>
		<th class="selector moddate"><a href="#" onclick="javascript:top.orderIt(\'ModDate\');">' . g_l('fileselector', '[modified]') . '</a></th>
		<th class="selector remain"></th>
	</tr>
</table>';
	}

	protected function printHeaderJSDef(){
		return parent::printHeaderJSDef() .
			'var makefolderState = ' . intval($this->userCanMakeNewFolder) . ';';
	}

	protected function userCanSeeDir($showAll = false){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$showAll){
			if(!we_users_util::in_workspace(intval($this->dir), get_ws($this->table, true), $this->table, $this->db)){
				return false;
			}
		}
		return we_users_util::userIsOwnerCreatorOfParentDir($this->dir, $this->table);
	}

	protected function userCanRenameFolder(){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$this->userHasRenameFolderPerms()){
			return false;
		}
		return true;
	}

	protected function userCanMakeNewDir(){
		if(defined('OBJECT_FILES_TABLE') && ($this->table == OBJECT_FILES_TABLE) && (!$this->dir)){
			return false;
		}
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		if(!$this->userCanSeeDir() || !$this->userHasFolderPerms()){
			return false;
		}
		return true;
	}

	protected function userHasRenameFolderPerms(){
		switch($this->table){
			case FILE_TABLE:
				if(!permissionhandler::hasPerm("CHANGE_DOC_FOLDER_PATH")){
					return false;
				}
				break;
		}
		return true;
	}

	protected function userHasFolderPerms(){

		switch($this->table){
			case FILE_TABLE:
				if(!permissionhandler::hasPerm("NEW_DOC_FOLDER")){
					return false;
				}
				break;
			case TEMPLATES_TABLE:
				if(!permissionhandler::hasPerm("NEW_TEMP_FOLDER")){
					return false;
				}
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				if(!permissionhandler::hasPerm("NEW_OBJECTFILE_FOLDER")){
					return false;
				}
				break;
		}
		return true;
	}

	protected function printCMDWriteAndFillSelectorHTML($withWrite = true){
		$pid = $this->dir;
		$out = '';
		$c = 0;
		while($pid != 0){
			$c++;
			$this->db->query('SELECT ID,Text,ParentID FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($pid));
			if($this->db->next_record()){
				$out = 'top.addOption("' . $this->db->f('Text') . '",' . $this->db->f('ID') . ');' . $out;
			}
			$pid = $this->db->f("ParentID");
			if($c > 500 || ($this->rootDirID && $this->db->f('ID') == $this->rootDirID)){
				$pid = 0;
			}
		}
		return ($withWrite ? 'top.writeBody(top.fsbody.document.body);' : '') . '
top.clearOptions();
if(!top.options.rootDirID){
	top.addOption("/",0);
}' .
			$out . '
top.selectIt();';
	}

	protected function printHeaderTable($extra = '', $append = false){
		return '
<table class="selectorHeaderTable">
	<tr style="vertical-align:middle">
		<td class="defaultfont lookinText">' . g_l('fileselector', '[lookin]') . '</td>
		<td class="lookin"><select name="lookin" id="lookin" class="weSelect" size="1" onchange="top.setDir(this.options[this.selectedIndex].value);" class="defaultfont" style="width:100%"></select>' .
			((!defined('OBJECT_TABLE')) || $this->table != OBJECT_TABLE ? '
		</td>
		<td>' . we_html_button::create_button('root_dir', "javascript:if(rootDirButsState){top.setRootDir();}", true, 0, 0, "", "", $this->dir == intval($this->rootDirID), false) . '</td>
		<td>' . we_html_button::create_button('fa:btn_fs_back,fa-lg fa-level-up,fa-lg fa-folder', "javascript:if(rootDirButsState){top.goBackDir();}", true, 0, 0, "", "", $this->dir == intval($this->rootDirID), false) . '</td>' .
				($append || !$extra ? '<td>' . we_html_button::create_button('fa:btn_new_dir,fa-plus,fa-lg fa-folder', "javascript:top.drawNewFolder();", true, 0, 0, '', '', !$this->userCanMakeNewDir(), false, '', false, '', '', 'btn_new_dir') . '</td>' : '') .
				($extra? : '') :
				''
			) . '
	</tr>
</table>';
	}

	protected function printSetDirHTML($morejs = ''){
		$isWS = we_users_util::in_workspace(intval($this->dir), get_ws($this->table, true), $this->table, $this->db);
		if(!$morejs && $isWS && $this->id == 0){
			$this->path = '/';
		}

		echo we_html_element::jsElement('
top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			($this->userCanMakeNewFolder ? 'top.enableNewFolderBut();' : 'top.disableNewFolderBut();') .
			$morejs .
			($isWS ?
				($morejs ? '' :
					'top.currentPath="' . $this->path . '";
top.currentID="' . $this->id . '";'

				) .
				'top.unselectAllFiles();
top.' . (intval($this->dir) == intval($this->rootDirID) ? 'disable' : 'enable') . 'RootDirButs();
top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values['ParentID'] . '";' :
				'')
		);
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	function printNewFolderHTML(){
		echo we_html_element::jsElement('
top.clearEntries();
top.makeNewFolder = true;' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() . '
');
	}

	function printCreateFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;
		$folder = (defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE ? //4076
				new we_class_folder() :
				new we_folder());

		$folder->we_new($this->table, $this->dir, $txt);
		if(!($msg = $folder->checkFieldsOnSave())){
			$folder->we_save();
		}

		echo we_html_tools::getHtmlTop() .
		we_html_element::jsElement('
top.clearEntries();' .
			($msg ? we_message_reporting::getShowMessageCall($msg, we_message_reporting::WE_MESSAGE_ERROR) :
				'var ref;
if(top.opener.top.treeData){
	ref = top.opener.top;
}else if(top.opener.top.opener.top.treeData){
	ref = top.opener.top.opener.top;
}
if(ref){
	ref.treeData.makeNewEntry({id:' . $folder->ID . ',parentid:' . $folder->ParentID . ',text:"' . $txt . '",open:1,contenttype:"' . $folder->ContentType . '",table:"' . $this->table . '"});
}' .
				($this->canSelectDir ? '
top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";' : '')
			) .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() .
			'top.makeNewFolder = false;
top.selectFile(top.currentID);') . '
</head><body></body></html>';
	}

	protected function getFrameset($withPreview = true){
		return STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'selectors.css') .
			$this->getFramsetJSFile() .
			'<body class="selector" onload="startFrameset();">' .
			we_html_element::htmlDiv(array('id' => 'fsheader'), $this->printHeaderHTML()) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, $withPreview ? 'preview' : '') .
			($withPreview ? we_html_element::htmlIFrame('fspreview', $this->getFsQueryString(we_selector_file::PREVIEW), '', '', '', false) : '') .
			we_html_element::htmlDiv(array('id' => 'fsfooter'), $this->printFooterTable()) .
			we_html_element::htmlDiv(array('id' => 'fspath', 'class' => 'radient'), we_html_element::jsElement('document.write( (top.startPath === undefined || top.startPath === "") ? "/" : top.startPath);')) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>
</html>';
	}

	protected function getFramesetJavaScriptDef(){
		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
var makeNewFolder=0;
var we_editDirID="";
var old=0;
options.userCanRenameFolder=' . intval($this->userCanRenameFolder) . ';
options.userCanMakeNewFolder=' . intval($this->userCanMakeNewFolder) . ';
');
	}

	function printRenameFolderHTML(){
		if(we_users_util::userIsOwnerCreatorOfParentDir($this->we_editDirID, $this->table) && we_users_util::in_workspace($this->we_editDirID, get_ws($this->table, true), $this->table, $this->db)){
			echo we_html_element::jsElement('
top.clearEntries();
top.we_editDirID=' . $this->we_editDirID . ';' .
				$this->printCmdAddEntriesHTML() .
				$this->printCMDWriteAndFillSelectorHTML());
		}
	}

	function printDoRenameFolderHTML(){
		$this->FolderText = rawurldecode($this->FolderText);
		$txt = $this->FolderText;

		$folder = (defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE ? //4076
				new we_class_folder() :
				new we_folder());

		$folder->initByID($this->we_editDirID, $this->table);
		$folder->Text = $txt;
		$folder->ModDate = time();
		$folder->Filename = $txt;
		$folder->Published = time();
		$folder->Path = $folder->getPath();
		$folder->ModifierID = isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : '';

		echo we_html_tools::getHtmlTop() .
		'<script><!--
top.clearEntries();';
				if(($msg = $folder->checkFieldsOnSave())){
			echo we_message_reporting::getShowMessageCall($msg, we_message_reporting::WE_MESSAGE_ERROR);
		} elseif(we_users_util::in_workspace($this->we_editDirID, get_ws($this->table, true), $this->table, $this->db)){
			if(f('SELECT Text FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->we_editDirID), 'Text', $this->db) != $txt){
				$folder->we_save();
				echo 'var ref;
if(top.opener.top.treeData){
	ref = top.opener.top;
}else if(top.opener.top.opener.top.treeData){
	ref = top.opener.top.opener.top;
}
if(ref){
	ref.treeData.updateEntry({id:' . $folder->ID . ',text:"' . $txt . '",parentid:"' . $folder->ParentID . '",table:"' . $this->table . '"});
}' .
				($this->canSelectDir ? '
top.currentPath = "' . $folder->Path . '";
top.currentID = "' . $folder->ID . '";
top.document.getElementsByName("fname")[0].value = "' . $folder->Text . '";
' : '');
			}
		}

		echo
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() . '
top.makeNewFolder = 0;
top.selectFile(top.currentID);
//-->
</script>
</head><body></body></html>';
	}

	function printPreviewHTML(){
		if(!$this->id){
			return;
		}
		$data = getHash('SELECT * FROM ' . $this->db->escape($this->table) . ' WHERE ID=' . intval($this->id), $this->db);
		if($data){
			$result = array(
				'Text' => $data['Text'],
				'Path' => $data['Path'],
				'ContentType' => isset($data['ContentType']) ? $data['ContentType'] : '',
				'Type' => isset($data['Type']) ? $data['Type'] : '',
				'CreationDate' => isset($data['CreationDate']) ? $data['CreationDate'] : '',
				'ModDate' => isset($data['ModDate']) ? $data['ModDate'] : '',
				'Filename' => isset($data['Filename']) ? $data['Filename'] : '',
				'Extension' => isset($data['Extension']) ? $data['Extension'] : '',
				'MasterTemplateID' => isset($data['MasterTemplateID']) ? $data['MasterTemplateID'] : '',
				'IncludedTemplates' => isset($data['IncludedTemplates']) ? $data['IncludedTemplates'] : '',
				'ClassName' => isset($data['ClassName']) ? $data['ClassName'] : '',
				'Templates' => isset($data['Templates']) ? $data['Templates'] : '',
			);
		}
		$path = $data ? $data['Path'] : '';
		$out = we_html_tools::getHtmlTop() .
			STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'we_selector_preview.css') .
			we_html_element::jsScript(JS_DIR . 'selectors/preview.js') .
			'</head>
<body class="defaultfont" onresize="setInfoSize()" onload="setInfoSize();weWriteBreadCrumb(\'' . $path . '\');">';
		if(!empty($result['ContentType'])){
			switch($this->table){
				case FILE_TABLE:
				case VFILE_TABLE:
					if($result['ContentType'] == we_base_ContentTypes::FOLDER){
						$query = $this->db->query('SELECT ID,Text,IsFolder FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->id));
						$folderFolders = $folderFiles = array();
						while($this->db->next_record()){
							if($this->db->f('IsFolder')){
								$folderFolders[$this->db->f('ID')] = $this->db->f('Text');
							} else {
								$folderFiles[$this->db->f('ID')] = $this->db->f('Text');
							}
						}
					} else {
						$query = $this->db->query('SELECT l.Name,c.Dat FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($this->id) . ' AND l.DocumentTable!="tblTemplates"');
						$metainfos = $this->db->getAllFirst(false);
					}
			}

			$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']) : 0;

			$filesize = we_base_file::getHumanFileSize($fs);
			$next = 0;
			$previewDefauts = "
<tr><td class='info' width='100%'>
	<div style='overflow:auto; height:100%' id='info'>
	<table class='default' width='100%'>
		<tr><td colspan='2' class='headline'>" . g_l('weClass', '[tab_properties]') . "</td></tr>
		<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td title=\"" . $result['Path'] . "\" width='10'>" . g_l('fileselector', '[name]') . ": </td><td>
			<div style='margin-right:14px'>" . $result['Text'] . "</div></td></tr>
		<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td width='10'>ID: </td><td>
			<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div style='float:left; vertical-align:baseline; margin-right:4px;'><i class='fa fa-lg fa-edit'></i></div></a>
			<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><div>" . $this->id . "</div></a>
		</td></tr>";
			if($result['CreationDate']){
				$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[created]') . ": </td><td>" . date(g_l('date', '[format][default]'), $result['CreationDate']) . "</td></tr>";
			}
			if($result['ModDate']){
				$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[modified]') . ": </td><td>" . date(g_l('date', '[format][default]'), $result['ModDate']) . "</td></tr>";
			}
			$previewDefauts .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[type]') . ": </td><td>" . (g_l('contentTypes', '[' . $result['ContentType'] . ']', true) !== false ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType']) . "</td></tr>";

			$out .= '<table class="default" style="height:100%;width:100%">';
			switch($result['ContentType']){
				case we_base_ContentTypes::IMAGE:
					if(file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
						if($imagesize[0] > 150 || $imagesize[1] > 150){
							$extension = substr($result['Extension'], 1);
							$thumbpath = WE_THUMBNAIL_DIRECTORY . '/' . $this->id . '.' . $extension;
							$created = filemtime($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
							if(file_exists($_SERVER['DOCUMENT_ROOT'] . $thumbpath) && ($created > filemtime($_SERVER['DOCUMENT_ROOT'] . $thumbpath))){
								//remove old thumb
								we_base_file::delete($_SERVER['DOCUMENT_ROOT'] . $thumbpath);
							}
							$thumbpath = WEBEDITION_DIR . 'thumbnail.php?' . http_build_query(array(
									'id' => $this->id,
									'size' => array(
										'width' => 150,
										'height' => 200
									),
									'path' => $result['Path'],
									'extension' => $extension,
							));
						} else {
							$thumbpath = $result['Path'];
						}

						$out .= "<tr><td class='image'><a href='" . $result['Path'] . "' target='_blank'><img src='" . $thumbpath . "' id='previewpic'></a></td></tr>" .
							$previewDefauts . "
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]') . ": </td><td>" . $imagesize[0] . " x " . $imagesize[1] . " px </td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[filesize]') . ": </td><td>" . $filesize . "</td></tr>";

						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[metainfo]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Description]') . ": </td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Keywords]') . ": </td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>";

						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[attribs]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ": </td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[name]') . ": </td><td>" . (isset($metainfos['name']) ? $metainfos['name'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[alt]') . ": </td><td>" . (isset($metainfos['alt']) ? $metainfos['alt'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]') . ": </td><td>" . (isset($metainfos['width']) ? $metainfos['width'] : '') . " x " . (isset($metainfos['height']) ? $metainfos['height'] : '') . " px </td></tr>";
					}
					break;
				case "folder":
					$out .= $previewDefauts;
					if(isset($folderFolders) && is_array($folderFolders) && count($folderFolders)){
						$next = 0;
						$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', '[folders]') . "</td></tr>";
						foreach($folderFolders as $fId => $fxVal){
							$out .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
						}
					}
					if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
						$next = 0;
						$out .= "<tr><td colspan='2' class='headline'>" . g_l('fileselector', '[files]') . "</td></tr>";
						foreach($folderFiles as $fId => $fxVal){
							$out .= "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . $fId . ": </td><td>" . $fxVal . "</td></tr>";
						}
					}
					break;
				case we_base_ContentTypes::TEMPLATE:
					$out .= $previewDefauts;
					if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
						$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
						$next = 0;
						$out .= "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[master_template]') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>ID:</td><td>" . $result['MasterTemplateID'] . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[path]') . ":</td><td>" . $mastertemppath . "</td></tr>";
					}
					break;
				case we_base_ContentTypes::WEDOCUMENT:
					$out .= $previewDefauts . "
<tr><td colspan='2' class='headline'>" . g_l('weClass', '[metainfo]') . "</td></tr>";
					$next = 0;
					$out .= "
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Title]') . ":</td><td>" . (isset($metainfos['Title']) ? $metainfos['Title'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Charset]') . ":</td><td>" . (isset($metainfos['Charset']) ? $metainfos['Charset'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Keywords]') . ":</td><td>" . (isset($metainfos['Keywords']) ? $metainfos['Keywords'] : '') . "</td></tr>
<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('weClass', '[Description]') . ":</td><td>" . (isset($metainfos['Description']) ? $metainfos['Description'] : '') . "</td></tr>";
					break;
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::CSS:
				case we_base_ContentTypes::JS:
				case we_base_ContentTypes::APPLICATION:
					$out .= $previewDefauts . "<tr class='" . ( ++$next % 2 == 0 ? 'even' : 'odd') . "'><td>" . g_l('fileselector', '[filesize]') . ":</td><td>" . $filesize . "</td></tr>";
					break;
				case we_base_ContentTypes::OBJECT:
				case we_base_ContentTypes::OBJECT_FILE:
				default:
					$out .= $previewDefauts;
					break;
			}
			$out .= '</table></div></td></tr></table>';
		}

		echo $out . '</body></html>';
	}

}
