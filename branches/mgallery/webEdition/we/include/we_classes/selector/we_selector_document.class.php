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
class we_selector_document extends we_selector_directory{
	protected $userCanMakeNewFile = true;
	protected $titles = array();
	protected $titleName = '';
	protected $startPath;
	protected $ctp = array(//FIXME: add audio button
		we_base_ContentTypes::IMAGE => "NEW_GRAFIK",
		we_base_ContentTypes::QUICKTIME => "NEW_QUICKTIME", //FIXME: remove quicktime
		we_base_ContentTypes::FLASH => "NEW_FLASH",
		we_base_ContentTypes::VIDEO => "NEW_VIDEO",
		we_base_ContentTypes::COLLECTION => "NEW_COLLECTION"
	);
	protected $ctb = array(
		"" => "btn_add_file",
		we_base_ContentTypes::IMAGE => 'fa:btn_add_image,fa-plus,fa-lg fa-file-image-o',
		we_base_ContentTypes::QUICKTIME => 'fa:btn_add_quicktime,fa-plus,fa-lg fa-fire',
		we_base_ContentTypes::FLASH => 'fa:btn_add_flash,fa-plus,fa-lg fa-flash',
		we_base_ContentTypes::VIDEO => 'fa:btn_add_video,fa-plus,fa-lg fa-file-video-o',
		we_base_ContentTypes::COLLECTION => 'fa:btn_add_collection,fa-plus,fa-lg fa-suitcase',
	);

	public function __construct($id, $table = '', $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $we_editDirID = '', $FolderText = '', $filter = '', $rootDirID = 0, $open_doc = false, $multiple = false, $canSelectDir = false, $startID = 0){
		parent::__construct($id, $table, $JSIDName, $JSTextName, $JSCommand, $order, 0, $we_editDirID, $FolderText, $rootDirID, $multiple, $filter, $startID);
		$this->fields .= ',RestrictOwners,Owners,OwnersReadOnly,CreatorID';
		switch($this->table){
			case FILE_TABLE:
				$this->fields .= ',Filename,Extension,ModDate,Published,ContentType';
				break;
			case VFILE_TABLE:
				$this->fields .= ',UNIX_TIMESTAMP(ModDate) AS ModDate,Text AS Filename,ContentType';
				break;
			case (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
				$this->fields .= ',Text AS Filename,ModDate,ContentType';
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$this->fields .= ',Text AS Filename,ModDate,Published,ContentType';
				break;
			default:
				$this->fields .= ',Filename,Extension,ModDate,1 AS Published,ContentType';
		}

		$this->canSelectDir = $canSelectDir;

		$this->title = g_l('fileselector', '[docSelector][title]');
		$this->userCanMakeNewFile = $this->_userCanMakeNewFile();
		$this->open_doc = $open_doc;
	}

	function query(){
		$filterQuery = '';
		if($this->filter){
			if(strpos($this->filter, ',')){
				$contentTypes = explode(',', $this->filter);
				$filterQuery .= ' AND (  ';
				foreach($contentTypes as $ct){
					$filterQuery .= ' ContentType="' . $this->db->escape($ct) . '" OR ';
				}
				$filterQuery .= ' isFolder=1)';
			} else {
				$filterQuery = ' AND (ContentType="' . $this->db->escape($this->filter) . '" OR IsFolder=1 ) ';
			}
		}

		// deal with workspaces
		$wsQuery = '';
		if(permissionhandler::hasPerm('ADMINISTRATOR') || ($this->table == FILE_TABLE && permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES')) || (defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE && permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_FILES'))){

		} else {
			if(get_ws($this->table)){
				$wsQuery = getWsQueryForSelector($this->table);
			} else if(defined('OBJECT_FILES_TABLE') && $this->table == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
				$ac = we_users_util::getAllowedClasses($this->db);
				$wsQueryA = array();
				foreach($ac as $cid){
					$path = id_to_path($cid, OBJECT_TABLE);
					$wsQueryA[] = " Path LIKE '" . $this->db->escape($path) . "/%' OR Path='" . $this->db->escape($path) . "'";
				}
				$wsQuery = ($wsQueryA ? ' AND (' . implode(' OR ', $wsQueryA) . ')' : '');
			}
			$wsQuery = $wsQuery? : ' OR RestrictOwners=0 ';
		}

		switch($this->table){
			case FILE_TABLE:
				$this->db->query('SELECT f.ID, c.Dat FROM (' . FILE_TABLE . ' f JOIN ' . LINK_TABLE . ' l ON (f.ID=l.DID)) JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND f.ParentID=' . intval($this->dir) . ' AND l.Name="Title"');
				$this->titles = $this->db->getAllFirst(false);
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$_path = $this->path;
				while($_path !== '' && dirname($_path) != '\\' && dirname($_path) != '/'){
					$_path = dirname($_path);
				}

				$hash = getHash('SELECT o.DefaultTitle,o.ID FROM ' . OBJECT_TABLE . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON o.ID=of.TableID WHERE of.ID=' . intval($this->dir), $this->db);

				$this->titleName = ($hash ? $hash['DefaultTitle'] : '');
				if($this->titleName && strpos($this->titleName, '_')){
					$this->db->query('SELECT OF_ID, ' . $this->titleName . ' FROM ' . OBJECT_X_TABLE . $hash['ID'] . ' WHERE OF_ParentID=' . intval($this->dir));
					$this->titles = $this->db->getAllFirst(false);
				}
				break;
		}
		$this->db->query('SELECT ' . $this->fields . ' FROM ' . $this->db->escape($this->table) . ' WHERE ParentID=' . intval($this->dir) . ' AND((1 ' .
			we_users_util::makeOwnersSql() . ')' .
			$wsQuery . ')' .
			$filterQuery . //$publ_q.
			($this->order ? (' ORDER BY IsFolder DESC,' . $this->order) : '')
		);
	}

	protected function getFramsetJSFile(){
		return parent::getFramsetJSFile() . we_html_element::jsScript(JS_DIR . 'selectors/document_selector.js');
	}

	protected function getExitOpen(){
		$frameRef = $this->JSTextName && strpos($this->JSTextName, ".document.") > 0 ?
			substr($this->JSTextName, 0, strpos($this->JSTextName, ".document.") + 1) :
			'';
		return we_html_element::jsElement('
function exit_open() {
	if(top.currentID) {' . ($this->JSIDName ?
					'top.opener.' . $this->JSIDName . '= top.currentID ? top.currentID : "";' : '') .
				($this->JSTextName ?
					'top.opener.' . $this->JSTextName . '= top.currentID ? top.currentPath : "";
		if(!!top.opener.' . $frameRef . 'YAHOO && !!top.opener.' . $frameRef . 'YAHOO.autocoml) {  top.opener.' . $frameRef . 'YAHOO.autocoml.selectorSetValid(top.opener.' . str_replace('.value', '.id', $this->JSTextName) . '); }
		' : '') .
				($this->JSCommand ?
					$this->JSCommand . ';' : '') . '
	}
	self.close();
}');
	}

	protected function setDefaultDirAndID($setLastDir){
		$this->dir = $this->startID ? : ($setLastDir && isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : 0);
		if($this->rootDirID){
			if(!in_parentID($this->dir, $this->rootDirID, $this->table, $this->db)){
				$this->dir = $this->rootDirID;
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
			'CreatorID' => 0,
			'ContentType' => '');
		$this->id = '';
	}

	protected function getFsQueryString($what){
		return $_SERVER["SCRIPT_NAME"] . "what=$what&rootDirID=" . $this->rootDirID . "&table=" . $this->table . "&id=" . $this->id . "&order=" . $this->order . "&startID=" . $this->startID . "&filter=" . $this->filter . "&open_doc=" . $this->open_doc;
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){

			$title = strip_tags(str_replace(array('"', "\n\r", "\n", "\\", '°',), array('\"', ' ', ' ', "\\\\", '&deg;'), (isset($this->titles[$this->db->f("ID")]) ? oldHtmlspecialchars($this->titles[$this->db->f("ID")]) : '-')));

			$ret .= 'top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Filename") . '","' . $this->db->f("Extension") . '",' . $this->db->f("IsFolder") . ',"' . $this->db->f("Path") . '","' . date(g_l('date', '[format][default]'), $this->db->f("ModDate")) . '","' . $this->db->f("ContentType") . '","' . $this->db->f("Published") . '","' . $title . '");';
		}
		$ret .=' function startFrameset(){';
		switch($this->filter){
			case we_base_ContentTypes::TEMPLATE:
			case we_base_ContentTypes::OBJECT:
			case we_base_ContentTypes::OBJECT_FILE:
			case we_base_ContentTypes::WEDOCUMENT:
				break;
			default:
				$tmp = ((in_workspace($this->dir, get_ws($this->table, true))) && $this->userCanMakeNewFile) ? 'enable' : 'disable';
				$ret.= 'if(top.' . $tmp . 'NewFileBut){top.' . $tmp . 'NewFileBut();}';
		}


		$tmp = ($this->userCanMakeNewDir() ? 'enable' : 'disable');
		$ret.='top.' . $tmp . 'NewFolderBut();}';
		return $ret;
	}

	function printHeaderHeadlines(){
		return '
<table class="headerLines">
	<tr>' . $this->tableHeadlines . '</tr>
</table>';
	}

	protected function printHeaderJSDef(){
		switch($this->filter){
			case we_base_ContentTypes::TEMPLATE:
			case we_base_ContentTypes::OBJECT:
			case we_base_ContentTypes::OBJECT_FILE:
			case we_base_ContentTypes::WEDOCUMENT:
				return parent::printHeaderJSDef();
			default:
				$btn = ($this->filter && isset($this->ctb[$this->filter]) ? $this->ctb[$this->filter] : 'btn_add_file');
				return parent::printHeaderJSDef() . '
var newFileState = ' . ($this->userCanMakeNewFile ? 1 : 0) . ';
function disableNewFileBut() {
	WE().layout.button.switch_button_state(document, "' . $btn . '",  "disabled");
	newFileState = 0;
}

function enableNewFileBut() {
	WE().layout.button.switch_button_state(document, "' . $btn . '",  "enabled");
	newFileState = 1;
}';
		}
	}

	protected function printHeaderTable($extra = ''){
		if($this->table !== VFILE_TABLE){
			return parent::printHeaderTable($extra);
		}

		$newFileState = $this->userCanMakeNewFile ? 1 : 0;
		return parent::printHeaderTable(
				'<td>' .
				we_html_element::jsElement('newFileState=' . $newFileState . ';') .
				we_html_button::create_button($this->ctb[we_base_ContentTypes::COLLECTION], "javascript:top.newCollection();", true, 0, 0, "", "", !$newFileState, false) .
				'</td>', true);
	}

	protected function _userCanMakeNewFile(){
		if(permissionhandler::hasPerm("ADMINISTRATOR")){
			return true;
		}
		if(!$this->userCanSeeDir()){
			return false;
		}
		if($this->filter && isset($this->ctp[$this->filter])){
			if(!permissionhandler::hasPerm($this->ctp[$this->filter])){
				return false;
			}
		} elseif(!
			(
			permissionhandler::hasPerm("NEW_GRAFIK") ||
			permissionhandler::hasPerm("NEW_QUICKTIME") ||
			permissionhandler::hasPerm("NEW_HTML") ||
			permissionhandler::hasPerm("NEW_JS") ||
			permissionhandler::hasPerm("NEW_CSS") ||
			permissionhandler::hasPerm("NEW_TEXT") ||
			permissionhandler::hasPerm("NEW_HTACCESS") ||
			permissionhandler::hasPerm("NEW_FLASH") ||
			permissionhandler::hasPerm("NEW_SONSTIGE") ||
			permissionhandler::hasPerm('FILE_IMPORT')
			)
		){
			return false;
		}

		return true;
	}

	function printSetDirHTML(){
		echo we_html_element::jsElement('
top.clearEntries();' .
			$this->printCmdAddEntriesHTML() .
			$this->printCMDWriteAndFillSelectorHTML() . '
top.' . (intval($this->dir) == 0 ? 'disable' : 'enable') . 'RootDirButs();
top.currentDir = "' . $this->dir . '";
top.parentID = "' . $this->values["ParentID"] . '";');
		$_SESSION['weS']['we_fs_lastDir'][$this->table] = $this->dir;
	}

	function printNewDocumentHTML(){
		echo '<script><!--
top.clearEntries();
top.makeNewDocument = true;' .
		$this->printCmdAddEntriesHTML() .
		$this->printCMDWriteAndFillSelectorHTML() . '
//-->
</script>';
	}

	protected function printFooterTable($more = null){
		$ret = '
<table id="footer">';
		if(!$this->filter){
			$ret.= '
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[type]') . '</td>
		<td class="defaultfont">
			<select name="filter" class="weSelect" size="1" onchange="top.setFilter(this.options[this.selectedIndex].value)" class="defaultfont" style="width:100%">
				<option value="">' . g_l('fileselector', '[all_Types]') . '</option>';
			foreach(we_base_ContentTypes::inst()->getWETypes() as $ctype){
				$ret.= '<option value="' . oldHtmlspecialchars($ctype) . '">' . g_l('contentTypes', '[' . $ctype . ']') . '</option>';
			}
			$ret.= '
			</select></td>
	</tr>';
		}

		$ret.= '
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[name]') . '</td>
		<td class="defaultfont">' . we_html_tools::htmlTextInput("fname", 24, ($this->values["Text"] === '/' ? '' : $this->values["Text"]), "", 'style="width:100%" readonly="readonly"') . '</td>
	</tr>
</table><div id="footerButtons">' . we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::OK, "javascript:press_ok_button();"), we_html_button::create_button(we_html_button::CANCEL, "javascript:top.exit_close();"), $more) . '</div>';
		return $ret;
	}

	protected function getFrameset(){
		$is_object = defined('OBJECT_TABLE') && $this->table === OBJECT_TABLE;
		return STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'selectors.css') .
			$this->getFramsetJSFile() .
			'<body class="selector" onload="startFrameset()">' .
			we_html_element::htmlDiv(array('id' => 'fsheader'), $this->printHeaderHTML()) .
			we_html_element::htmlIFrame('fsbody', $this->getFsQueryString(we_selector_file::BODY), '', '', '', true, 'preview' . ($is_object ? ' object' : '')) .
			we_html_element::htmlIFrame('fspreview', $this->getFsQueryString(we_selector_file::PREVIEW), '', '', '', false, ($is_object ? 'object' : '')) .
			we_html_element::htmlDiv(array('id' => 'fsfooter'), $this->printFooterTable()) .
			we_html_element::htmlDiv(array('id' => 'fspath', 'class' => 'radient'), we_html_element::jsElement('document.write( (top.startPath === undefined || top.startPath === "") ? "/" : top.startPath);')) .
			we_html_element::htmlIFrame('fscmd', 'about:blank', '', '', '', false) .
			'</body>
</html>';
	}

	function printPreviewHTML(){
		if(!$this->id){
			return;
		}

		$result = getHash('SELECT * FROM ' . $this->table . ' WHERE ID=' . intval($this->id), $this->db);
		$path = $result ? $result['Path'] : '';
		$out = we_html_tools::getHtmlTop() .
			STYLESHEET .
			we_html_element::cssLink(CSS_DIR . 'we_selector_preview.css') .
			we_html_element::jsElement('
	function setInfoSize() {
		infoSize = document.body.clientHeight;
		if(infoElem=document.getElementById("info")) {
			infoElem.style.height = document.body.clientHeight - (prieviewpic = document.getElementById("previewpic") ? 160 : 0 )+"px";
		}
	}
	function openToEdit(tab,id,contentType){
		WE().layout.weEditorFrameController.openDocument(tab,id,contentType);
	}
	var weCountWriteBC = 0;
	function weWriteBreadCrumb(BreadCrumb){
		//FIXME: this function should not need a timeout - check
		if(top.document.getElementById("fspath")){
			top.document.getElementById("fspath").innerHTML = BreadCrumb;
		}else if(weCountWriteBC<10){
			setTimeout(function(){weWriteBreadCrumb("' . $path . '")},100);
		}
		weCountWriteBC++;
	}') . '
</head>
<body class="defaultfont" onresize="setInfoSize()" onload="setInfoSize();weWriteBreadCrumb(\'' . $path . '\');">';
		if((isset($result['ContentType']) && !empty($result['ContentType'])) || ($this->table == VFILE_TABLE )){//FIXME: this check should be obsolete, remove in 6.6
			if((isset($result['ContentType']) && $result['ContentType'] === we_base_ContentTypes::FOLDER) || ($this->table == VFILE_TABLE && $result['IsFolder'])){
				$this->db->query('SELECT ID,Text FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=1 AND ParentID=' . intval($this->id));
				$folderFolders = $this->db->getAllFirst(false);
				$this->db->query('SELECT ID,Text FROM ' . $this->db->escape($this->table) . ' WHERE IsFolder=0 AND ParentID=' . intval($this->id));
				$folderFiles = $this->db->getAllFirst(false);
			} else {
				switch($this->table){
					case VFILE_TABLE:
						$this->db->query('SELECT f.ID,f.Text FROM ' . FILELINK_TABLE . ' fl JOIN ' . FILE_TABLE . ' f ON (f.ID=fl.remObj AND fl.remTable="tblFile") WHERE fl.DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" AND fl.type="collection" AND fl.ID=' . intval($this->id));
						$folderFiles = $this->db->getAllFirst(false);
						$result['ContentType'] = we_base_ContentTypes::COLLECTION;
						break;
					case FILE_TABLE:
						$this->db->query('SELECT l.Name, c.Dat FROM ' . LINK_TABLE . ' l LEFT JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DID=' . intval($this->id) . ' AND l.DocumentTable!="tblTemplates"');
						$metainfos = $this->db->getAllFirst(false);
						break;
					case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
						$_fieldnames = getHash('SELECT DefaultDesc,DefaultTitle,DefaultKeywords FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($result['TableID']), $this->db, MYSQL_ASSOC);
						$_selFields = array();
						foreach($_fieldnames as $_key => $_val){
							if(!$_val || $_val === '_'){ // bug #4657
								continue;
							}
							switch($_key){
								case "DefaultDesc":
									$_selFields[] = $_val . ' AS Description';
									break;
								case "DefaultTitle":
									$_selFields[] = $_val . ' AS Title';
									break;
								case "DefaultKeywords":
									$_selFields[] = $_val . ' AS Keywords';
									break;
							}
						}
						if($_selFields){
							$metainfos = getHash('SELECT ' . implode(',', $_selFields) . ' FROM ' . OBJECT_X_TABLE . intval($result['TableID']) . ' WHERE OF_ID=' . intval($result["ID"]), $this->db);
						}
				}
			}
			switch($result['ContentType']){
				case we_base_ContentTypes::IMAGE:
				case we_base_ContentTypes::WEDOCUMENT:
				case we_base_ContentTypes::HTML:
				case we_base_ContentTypes::APPLICATION:
					$showPreview = $result['Published'] > 0;
					break;

				default:
					$showPreview = false;
					break;
			}

			$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path']) ? filesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']) : 0;

			$_filesize = we_base_file::getHumanFileSize($fs);

			if($result['ContentType'] == we_base_ContentTypes::IMAGE && file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
				if($fs === 0){
					$_imagesize = array(0, 0);
					$_thumbpath = ICON_DIR . 'no_image.gif';
					$_imagepreview = "<img src='" . $_thumbpath . "' id='previewpic'><p>" . g_l('fileselector', '[image_not_uploaded]') . "</p>";
				} else {
					$_imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
					$_thumbpath = WEBEDITION_DIR . 'thumbnail.php?' . http_build_query(array(
							'id' => $this->id,
							'size' => 150,
							'path' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $result['Path']),
							'extension' => $result['Extension'],
							'size2' => 200));
					$_imagepreview = "<a href='" . $result['Path'] . "' target='_blank' style='text-align:center'><img src='" . $_thumbpath . "' border='0' id='previewpic'></a>";
				}
			}

			$_previewFields = array(
				"metainfos" => array("headline" => g_l('weClass', '[metainfo]'), "data" => array()),
				"attributes" => array("headline" => g_l('weClass', '[attribs]'), "data" => array()),
				"folders" => array("headline" => g_l('fileselector', '[folders]'), "data" => array()),
				"files" => array("headline" => g_l('fileselector', '[files]'), "data" => array()),
				"masterTemplate" => array("headline" => g_l('weClass', '[master_template]'), "data" => array()),
				"properies" => array("headline" => g_l('weClass', '[tab_properties]'), "data" => array(
						array(
							"caption" => g_l('fileselector', '[name]'),
							"content" => (
							$showPreview ?
								"<div style='float:left; vertical-align:baseline; margin-right:4px;'><a href='" . $result['Path'] . "' target='_blank' style='color:black'><i class='fa fa-external-link fa-lg'></i></a></div>" :
								""
							) . "<div style='margin-right:14px'>" .
							($showPreview ?
								"<a href='" . $result['Path'] . "' target='_blank' style='color:black'>" . $result['Text'] . "</a>" :
								$result['Text']
							) . "</div>"
						),
						array(
							"caption" => "ID",
							"content" => "<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'>
					<div style='float:left; vertical-align:baseline; margin-right:4px;'>
					<i class='fa fa-edit fa-lg'></i>
					</div></a>
					<a href='javascript:openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'>
						<div>" . $this->id . "</div>
					</a>"
						)
					)),
			);
			if($result['CreationDate']){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', '[created]'),
					"content" => is_int($result['CreationDate']) ? date(g_l('date', '[format][default]'), $result['CreationDate']) : $result['CreationDate']
				);
			}

			if($result['ModDate']){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', '[modified]'),
					"content" => is_int($result['ModDate']) ? date(g_l('date', '[format][default]'), $result['ModDate']) : $result['ModDate']
				);
			}

			$_previewFields["properies"]["data"][] = array(
				"caption" => g_l('fileselector', '[type]'),
				"content" => ((g_l('contentTypes', '[' . $result['ContentType'] . ']') !== false) ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType'])
			);


			if(isset($_imagesize)){
				$_previewFields["properies"]["data"][] = array(
					"caption" => g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]'),
					"content" => $_imagesize[0] . " x " . $_imagesize[1] . " px "
				);
			}

			switch($result['ContentType']){
				case we_base_ContentTypes::FOLDER:
				case we_base_ContentTypes::TEMPLATE:
				case we_base_ContentTypes::OBJECT:
				case we_base_ContentTypes::OBJECT_FILE:
					break;
				default:
					$_previewFields["properies"]["data"][] = array(
						"caption" => g_l('fileselector', '[filesize]'),
						"content" => $_filesize
					);
			}


			if(isset($metainfos['Title'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', '[Title]'),
					"content" => $metainfos['Title']
				);
			}

			if(isset($metainfos['Description'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', '[Description]'),
					"content" => $metainfos['Description']
				);
			}

			if(isset($metainfos['Keywords'])){
				$_previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', '[Keywords]'),
					"content" => $metainfos['Keywords']
				);
			}
			switch($result['ContentType']){
				case we_base_ContentTypes::IMAGE:
					$Title = (isset($metainfos['title']) ? $metainfos['title'] : ((isset($metainfos['Title']) && !empty($metainfos['useMetaTitle'])) ? $metainfos['Title'] : ''));
					$name = (isset($metainfos['name']) ? $metainfos['name'] : '');
					$alt = (isset($metainfos['alt']) ? $metainfos['alt'] : '');
					if($Title !== ""){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[Title]'),
							"content" => oldHtmlspecialchars($Title)
						);
					}
					if($name){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[name]'),
							"content" => $name
						);
					}
					if($alt){
						$_previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[alt]'),
							"content" => oldHtmlspecialchars($alt)
						);
					}
				//no break!
				case we_base_ContentTypes::FLASH:
				case we_base_ContentTypes::QUICKTIME:
				case we_base_ContentTypes::APPLICATION:
					// only binary data have additional metadata
					$metaDataFields = we_metadata_metaData::getDefinedMetaDataFields();
					foreach($metaDataFields as $md){
						if($md['tag'] != "Title" && $md['tag'] != "Description" && $md['tag'] != "Keywords"){
							if(isset($metainfos[$md['tag']])){
								$_previewFields["metainfos"]["data"][] = array(
									"caption" => $md['tag'],
									"content" => $metainfos[$md['tag']]
								);
							}
						}
					}
					break;

				case "folder":
					if(isset($folderFolders) && is_array($folderFolders) && count($folderFolders)){
						foreach($folderFolders as $fId => $fxVal){
							$_previewFields["folders"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
						foreach($folderFiles as $fId => $fxVal){
							$_previewFields["files"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					break;

				case we_base_ContentTypes::TEMPLATE:
					if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
						$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
						$_previewFields["masterTemplate"]["data"][] = array(
							"caption" => "ID",
							"content" => $result['MasterTemplateID']
						);
						$_previewFields["masterTemplate"]["data"][] = array(
							"caption" => g_l('weClass', '[path]'),
							"content" => $mastertemppath
						);
					}
					break;
			}

			$out .= '<table class="default" width="100%">';
			if(!empty($_imagepreview)){
				$out .= "<tr><td colspan='2' style='vertical-align:middle;text-align:center;' class='image' height='160' bgcolor='#EDEEED'>" . $_imagepreview . "</td></tr>";
			}

			foreach($_previewFields as $_part){
				if(!empty($_part["data"])){
					$out .= "<tr><td colspan='2' class='headline'>" . $_part["headline"] . "</td></tr>";
					foreach($_part["data"] as $z => $_row){
						$_class = (($z % 2) == 0) ? "odd" : "even";
						$out .= "<tr class='" . $_class . "'><td>" . $_row['caption'] . ": </td><td>" . $_row['content'] . "</td></tr>";
					}
				}
			}
			$out .= '</table></div></td></tr></table>';
		}
		$out .= '</body></html>';
		echo $out;
	}

	protected function getFramesetJavaScriptDef(){
		$ctypes = array();
		$ct = we_base_ContentTypes::inst();
		foreach($ct->getContentTypes() as $ctype){
			if(g_l('contentTypes', '[' . $ctype . ']') !== false){
				$ctypes[] = '"' . $ctype . '" : "' . g_l('contentTypes', '[' . $ctype . ']') . '"';
			}
		}

		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
options.canSelectDir=' . intval($this->canSelectDir) . ';
options.col2js="' . $this->col2js . '";
var contentTypes = {' . implode(',', $ctypes) . '};
');
	}

}
