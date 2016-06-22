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
		we_base_ContentTypes::FLASH => "NEW_FLASH",
		we_base_ContentTypes::VIDEO => "NEW_VIDEO",
		we_base_ContentTypes::COLLECTION => "NEW_COLLECTION"
	);
	protected $ctb = array(
		"" => "btn_add_file",
		we_base_ContentTypes::IMAGE => 'fa:btn_add_image,fa-upload,fa-lg fa-file-image-o',
		we_base_ContentTypes::FLASH => 'fa:btn_add_flash,fa-upload,fa-lg fa-flash',
		we_base_ContentTypes::VIDEO => 'fa:btn_add_video,fa-upload,fa-lg fa-file-video-o',
		we_base_ContentTypes::COLLECTION => 'fa:btn_add_collection,fa-plus,fa-lg fa-archive',
	);

	public function __construct($id, $table = '', $JSIDName = '', $JSTextName = '', $JSCommand = '', $order = '', $sessionID = '', $we_editDirID = '', $FolderText = '', $filter = '', $rootDirID = 0, $open_doc = false, $multiple = false, $canSelectDir = false, $startID = 0, $lang = ''){
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
		$this->language = $lang;

		$this->title = g_l('fileselector', '[docSelector][title]');
		$this->userCanMakeNewFile = $this->_userCanMakeNewFile();
		$this->open_doc = $open_doc;
	}

	protected function printCmdHTML($morejs = ''){
		$isWS = we_users_util::in_workspace(intval($this->dir), get_ws($this->table, true), $this->table, $this->db);
		parent::printCmdHTML(($isWS && $this->userCanMakeNewFile ? 'top.enableNewFileBut();' : 'top.disableNewFileBut();') . $morejs);
	}

	function query(){
		$filterQuery = '';
		if($this->filter){
			$contentTypes = explode(',', $this->filter);
			$filterQuery .= ' AND (  ' . ($contentTypes ? 'ContentType IN ("' . implode('","', $contentTypes) . '") OR ' : '') . ' isFolder=1)';
		}
		if($this->language){
			$filterQuery .=' AND (Language="' . $this->language . '" OR isFolder=1)';
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
					$wsQueryA[] = ' Path LIKE "' . $this->db->escape($path) . '/%" OR Path="' . $this->db->escape($path) . '"';
				}
				$wsQuery = ($wsQueryA ? ' AND (' . implode(' OR ', $wsQueryA) . ')' : '');
			}
			$wsQuery = $wsQuery? : ' OR RestrictOwners=0 ';
		}

		switch($this->table){
			case FILE_TABLE:
				$this->db->query('SELECT f.ID, c.Dat FROM (' . FILE_TABLE . ' f JOIN ' . LINK_TABLE . ' l ON (f.ID=l.DID)) JOIN ' . CONTENT_TABLE . ' c ON (l.CID=c.ID) WHERE l.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND f.ParentID=' . intval($this->dir) . ' AND l.nHash=x\'' . md5("Title") . '\'');
				$this->titles = $this->db->getAllFirst(false);
				break;
			case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
				$path = $this->path;
				while($path !== '' && dirname($path) != '\\' && dirname($path) != '/'){
					$path = dirname($path);
				}

				$hash = getHash('SELECT o.DefaultTitle,o.ID FROM ' . OBJECT_TABLE . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON o.ID=of.TableID WHERE of.ID=' . intval($this->dir), $this->db);

				$this->titleName = ($hash ? $hash['DefaultTitle'] : '');
				if($this->titleName && strpos($this->titleName, '_')){
					$this->db->query('SELECT OF_ID, o.' . $this->titleName . ' FROM ' . OBJECT_X_TABLE . $hash['ID'] . ' o JOIN ' . OBJECT_FILES_TABLE . ' of ON of.ID=o.OF_ID WHERE of.ParentID=' . intval($this->dir));
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
		if(top.opener.' . $frameRef . 'YAHOO!==undefined && top.opener.' . $frameRef . 'YAHOO.autocoml!==undefined) {  top.opener.' . $frameRef . 'YAHOO.autocoml.selectorSetValid(top.opener.' . str_replace('.value', '.id', $this->JSTextName) . '); }
		' : '') .
				($this->JSCommand ?
					$this->JSCommand . ';' : '') . '
	}
	self.close();
}');
	}

	protected function setDefaultDirAndID($setLastDir){
		$ws = get_ws($this->table, true);
		$rootDirID = ($ws ? reset($ws) : 0);

		$this->dir = $this->startID ? : ($setLastDir && isset($_SESSION['weS']['we_fs_lastDir'][$this->table]) ? intval($_SESSION['weS']['we_fs_lastDir'][$this->table]) : $rootDirID);
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
		return $_SERVER['SCRIPT_NAME'] . 'what=' . $what . '&rootDirID=' . $this->rootDirID . '&table=' . $this->table . '&id=' . $this->id . '&order=' . $this->order . '&startID=' . $this->startID . '&filter=' . $this->filter . '&open_doc=' . $this->open_doc . '&lang=' . $this->language;
	}

	protected function printCmdAddEntriesHTML(){
		$ret = '';
		$this->query();
		while($this->db->next_record()){

			$title = strip_tags(str_replace(array('"', "\n\r", "\n", "\\", '°',), array('\"', ' ', ' ', "\\\\", '&deg;'), (isset($this->titles[$this->db->f("ID")]) ? oldHtmlspecialchars($this->titles[$this->db->f("ID")]) : '-')));

			$ret .= 'top.addEntry(' . $this->db->f("ID") . ',"' . $this->db->f("Filename") . '","' . $this->db->f("Extension") . '",' . $this->db->f('IsFolder') . ',"' . $this->db->f("Path") . '","' . date(g_l('date', '[format][default]'), $this->db->f("ModDate")) . '","' . $this->db->f("ContentType") . '","' . $this->db->f("Published") . '","' . $title . '");';
		}
		$ret .=' function startFrameset(){';
		switch($this->filter){
			case we_base_ContentTypes::TEMPLATE:
			case we_base_ContentTypes::OBJECT:
			case we_base_ContentTypes::OBJECT_FILE:
			case we_base_ContentTypes::WEDOCUMENT:
				break;
			default:
				$tmp = ((we_users_util::in_workspace($this->dir, get_ws($this->table, true))) && $this->userCanMakeNewFile) ? 'enable' : 'disable';
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
		switch($this->table){
			case FILE_TABLE:
			case VFILE_TABLE:
				return parent::printHeaderJSDef() . '
var newFileState = ' . ($this->userCanMakeNewFile ? 1 : 0) . ';';
			default:
				return parent::printHeaderJSDef();
		}
	}

	protected function printHeaderTable($extra = '', $append = true){
		switch($this->table){
			case FILE_TABLE:
				$extra = '<td>' .
					($this->filter && isset($this->ctb[$this->filter]) ?
						we_html_button::create_button($this->ctb[$this->filter], "javascript:top.newFile();", true, 0, 0, "", "", !$this->userCanMakeNewFile, false, '', false, '', '', 'btn_add_file') :
						(permissionhandler::hasPerm('NEW_GRAFIK') || permissionhandler::hasPerm('NEW_SONSTIGE') ? we_html_button::create_button('fa:btn_add_file,fa-plus,fa-lg fa-file-o', "javascript:top.newFile();", true, 0, 0, "", "", !$this->userCanMakeNewFile, false, '', false, '', '', 'btn_add_file') :
							'')
					) .
					'</td>' .
					$extra;
				break;

			case VFILE_TABLE:
				$extra = '<td>' . we_html_button::create_button($this->ctb[we_base_ContentTypes::COLLECTION], "javascript:top.newCollection();", true, 0, 0, "", "", !$this->userCanMakeNewFile, false, '', false, '', '', 'btn_add_file') . '</td>';
				break;
		}
		return parent::printHeaderTable($extra, true);
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

	protected function printSetDirHTML($morejs = ''){
		$isWS = we_users_util::in_workspace(intval($this->dir), get_ws($this->table, true), $this->table, $this->db);

		parent::printSetDirHTML(($isWS && $this->userCanMakeNewFile ? 'top.enableNewFileBut();' : 'top.disableNewFileBut();') . $morejs);
	}

	protected function printFooterTable($more = null){
		$ret = '
<table id="footer">';
		if(!$this->filter){
			$ret.= '
	<tr>
		<td class="defaultfont description">' . g_l('fileselector', '[type]') . '</td>
		<td class="defaultfont">
			<select name="filter" class="weSelect" onchange="top.setFilter(this.options[this.selectedIndex].value)" class="defaultfont" style="width:100%">
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

	protected function getFrameset($withPreview = false){
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
function weWriteBreadCrumb(BreadCrumb){
	if(top.document.getElementById("fspath")){
		top.document.getElementById("fspath").innerHTML = BreadCrumb;
	}
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
						$fieldnames = getHash('SELECT DefaultDesc,DefaultTitle,DefaultKeywords FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($result['TableID']), $this->db, MYSQL_ASSOC);
						$selFields = array();
						foreach($fieldnames as $key => $val){
							if(!$val || $val === '_'){ // bug #4657
								continue;
							}
							switch($key){
								case "DefaultDesc":
									$selFields[] = $val . ' AS Description';
									break;
								case "DefaultTitle":
									$selFields[] = $val . ' AS Title';
									break;
								case "DefaultKeywords":
									$selFields[] = $val . ' AS Keywords';
									break;
							}
						}
						if($selFields){
							$metainfos = getHash('SELECT ' . implode(',', $selFields) . ' FROM ' . OBJECT_X_TABLE . intval($result['TableID']) . ' WHERE OF_ID=' . intval($result["ID"]), $this->db);
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

			$filesize = we_base_file::getHumanFileSize($fs);

			if($result['ContentType'] == we_base_ContentTypes::IMAGE && file_exists($_SERVER['DOCUMENT_ROOT'] . $result['Path'])){
				if($fs === 0){
					$imagesize = array(0, 0);
					$thumbpath = ICON_DIR . 'no_image.gif';
					$imagepreview = '<img src="' . $thumbpath . '" id="previewpic"><p>' . g_l('fileselector', '[image_not_uploaded]') . '</p>';
				} else {
					$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $result['Path']);
					$thumbpath = WEBEDITION_DIR . 'thumbnail.php?' . http_build_query(array(
							'id' => $this->id,
							'size' => array(
								'width' => 150,
								'height' => 200,
							),
							'path' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $result['Path']),
							'extension' => $result['Extension'],
					));
					$imagepreview = "<a href='" . $result['Path'] . "' target='_blank'><img src='" . $thumbpath . "' border='0' id='previewpic'></a>";
				}
			}

			$previewFields = array(
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
								"<a href='" . $result['Path'] . "' target='_blank' style='color:black'><i style='margin-right:4px' class='fa fa-external-link fa-lg'></i>" . $result['Text'] . "</a>" :
								$result['Text']
							)
						),
						array(
							"caption" => "ID",
							"content" => "<a href='javascript:WE().layout.openToEdit(\"" . $this->table . "\",\"" . $this->id . "\",\"" . $result['ContentType'] . "\")' style='color:black'><i style='margin-right:4px' class='fa fa-edit fa-lg'></i>" . $this->id . "</a>"
						)
					)),
			);
			if($result['CreationDate']){
				$previewFields["properies"]["data"][] = array(
					'caption' => g_l('fileselector', '[created]'),
					'content' => is_numeric($result['CreationDate']) ? date(g_l('date', '[format][default]'), $result['CreationDate']) : $result['CreationDate']
				);
			}

			if($result['ModDate']){
				$previewFields["properies"]["data"][] = array(
					"caption" => g_l('fileselector', '[modified]'),
					"content" => is_numeric($result['ModDate']) ? date(g_l('date', '[format][default]'), $result['ModDate']) : $result['ModDate']
				);
			}

			$previewFields["properies"]["data"][] = array(
				"caption" => g_l('fileselector', '[type]'),
				"content" => ((g_l('contentTypes', '[' . $result['ContentType'] . ']') !== false) ? g_l('contentTypes', '[' . $result['ContentType'] . ']') : $result['ContentType'])
			);

			if(isset($result['Language'])){
				$langs = getWeFrontendLanguagesForBackend();
				$previewFields['properies']['data'][] = array(
					'caption' => g_l('weClass', '[language]'),
					'content' => $result['Language'] ? $langs[$result['Language']] : '-'
				);
			}


			if(isset($imagesize)){
				$previewFields["properies"]["data"][] = array(
					"caption" => g_l('weClass', '[width]') . " x " . g_l('weClass', '[height]'),
					"content" => $imagesize[0] . " x " . $imagesize[1] . " px "
				);
			}

			switch($result['ContentType']){
				case we_base_ContentTypes::FOLDER:
				case we_base_ContentTypes::TEMPLATE:
				case we_base_ContentTypes::OBJECT:
				case we_base_ContentTypes::OBJECT_FILE:
					break;
				default:
					$previewFields["properies"]["data"][] = array(
						"caption" => g_l('fileselector', '[filesize]'),
						"content" => $filesize
					);
			}


			if(isset($metainfos['Title'])){
				$previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', '[Title]'),
					"content" => $metainfos['Title']
				);
			}

			if(isset($metainfos['Description'])){
				$previewFields["metainfos"]["data"][] = array(
					"caption" => g_l('weClass', '[Description]'),
					"content" => $metainfos['Description']
				);
			}

			if(isset($metainfos['Keywords'])){
				$previewFields["metainfos"]["data"][] = array(
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
						$previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[Title]'),
							"content" => oldHtmlspecialchars($Title)
						);
					}
					if($name){
						$previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[name]'),
							"content" => $name
						);
					}
					if($alt){
						$previewFields["attributes"]["data"][] = array(
							"caption" => g_l('weClass', '[alt]'),
							"content" => oldHtmlspecialchars($alt)
						);
					}
				//no break!
				case we_base_ContentTypes::FLASH:
				case we_base_ContentTypes::APPLICATION:
					// only binary data have additional metadata
					$metaDataFields = we_metadata_metaData::getDefinedMetaDataFields();
					foreach($metaDataFields as $md){
						if($md['tag'] != "Title" && $md['tag'] != "Description" && $md['tag'] != "Keywords"){
							if(isset($metainfos[$md['tag']])){
								$previewFields["metainfos"]["data"][] = array(
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
							$previewFields["folders"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					if(isset($folderFiles) && is_array($folderFiles) && count($folderFiles)){
						foreach($folderFiles as $fId => $fxVal){
							$previewFields["files"]["data"][] = array(
								"caption" => $fId,
								"content" => $fxVal
							);
						}
					}
					break;

				case we_base_ContentTypes::TEMPLATE:
					if(isset($result['MasterTemplateID']) && !empty($result['MasterTemplateID'])){
						$mastertemppath = f("SELECT Text, Path FROM " . $this->db->escape($this->table) . " WHERE ID=" . intval($result['MasterTemplateID']), "Path", $this->db);
						$previewFields["masterTemplate"]["data"][] = array(
							"caption" => "ID",
							"content" => $result['MasterTemplateID']
						);
						$previewFields["masterTemplate"]["data"][] = array(
							"caption" => g_l('weClass', '[path]'),
							"content" => $mastertemppath
						);
					}
					break;
			}

			$out .= '<table class="default" style="width:100%">';
			if(!empty($imagepreview)){
				$out .= "<tr><td colspan='2' class='image'>" . $imagepreview . "</td></tr>";
			}

			foreach($previewFields as $part){
				if(!empty($part["data"])){
					$out .= "<tr><td colspan='2' class='headline'>" . $part["headline"] . "</td></tr>";
					foreach($part["data"] as $z => $row){
						$class = (($z % 2) == 0) ? "odd" : "even";
						$out .= "<tr class='" . $class . "'><td>" . $row['caption'] . ": </td><td>" . $row['content'] . "</td></tr>";
					}
				}
			}
			$out .= '</table></div></td></tr></table>';
		}
		$out .= '</body></html>';
		echo $out;
	}

	protected function getFramesetJavaScriptDef(){
		/* $ctypes = array();
		  $ct = we_base_ContentTypes::inst();
		  foreach($ct->getContentTypes() as $ctype){
		  if(g_l('contentTypes', '[' . $ctype . ']') !== false){
		  $ctypes[] = '"' . $ctype . '" : "' . g_l('contentTypes', '[' . $ctype . ']') . '"';
		  }
		  } */

		return parent::getFramesetJavaScriptDef() . we_html_element::jsElement('
options.canSelectDir=' . intval($this->canSelectDir) . ';
options.useID=' . intval($this->useID) . ';
');
		//var contentTypes = {' . implode(',', $ctypes) . '};
	}

}
