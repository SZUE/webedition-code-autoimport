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
/*  a class for handling flashDocuments. */

class we_collection extends we_root{
	/*
	 * FIXME: maybe abandon file- and objectCollection and make one $collection only?
	 * we have both collections for not immediately deleting existing collections when changing remTable without saving collection:
	 * they exist in collection objects only: the remObjects of the matching one are written to tblFileLink when saving
	 */
	public $remTable = 'fileTable'; //TODO: set getters for all public props
	public $remCT;
	public $remClass;
	public $DefaultDir = IMAGESTARTID_DEFAULT;
	protected $DefaultPath = '';
	public $IsDuplicates;
	public $InsertRecursive;
	protected $fileCollection = '';
	protected $objectCollection = '';
	protected $jsFormCollection = '';
	protected $insertPrefs;
	//private $tmpFoldersDone = [];
	protected $view = 'grid';
	private $gridItemDimensions = [
		2 => ['item' => 400, 'icon' => 56, 'font' => 30, 'btnFontsize' => 30, 'btnHeight' => 60],
		3 => ['item' => 264, 'icon' => 42, 'font' => 24, 'btnFontsize' => 22, 'btnHeight' => 44],
		4 => ['item' => 200, 'icon' => 32, 'font' => 18, 'btnFontsize' => 16, 'btnHeight' => 31],
		5 => ['item' => 159, 'icon' => 24, 'font' => 14, 'btnFontsize' => 13, 'btnHeight' => 25],
		6 => ['item' => 134, 'icon' => 20, 'font' => 11, 'btnFontsize' => 13, 'btnHeight' => 25]
	];
	protected $itemsPerRow = 4;

	const CLASS_YES = 'we-state-green';
	const CLASS_NO = 'we-state-red';
	const CLASS_NONE = 'we-state-none';

	/** Constructor
	 * @return we_collection
	 * @desc Constructor for we_collection
	 */
	function __construct(){
		parent::__construct();

		$this->ModDate = $this->ModDate ? $this->ModDate : $this->CreationDate;
		$this->Published = $this->ModDate;

		$this->Table = VFILE_TABLE;
		array_push($this->persistent_slots, 'fileCollection', 'objectCollection', 'remTable', 'remCT', 'remClass', 'DefaultDir', 'insertPrefs', 'IsDuplicates', 'InsertRecursive', 'ContentType', 'view', 'viewSub', 'itemsPerRow');

		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_INFO);
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm(['CAN_EDIT_CUSTOMERFILTER', 'CAN_CHANGE_DOCS_CUSTOMER']))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}

		$this->remTable = 'tblFile'; // FIXME: remove when objects are implemented
	}

	public function getRemTable(){
		return 'tblFile'; // FIXME: make dynamic when objects are implemented
	}

	public function getRemCT(){
		//FIXME: do not alloud to write ',' to db!
		return !$this->remCT || $this->remCT === ',' ? ',' . implode(',', we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true)) . ',' : $this->remCT;
	}

	public function getRealRemCT(){
		$remCT = !$this->remCT || $this->remCT === ',' ? we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true) : explode(',', trim($this->remCT, ' ,'));
		foreach($remCT as $ct){
			$remCT = array_merge($remCT, we_base_ContentTypes::inst()->getRealContentTypes($ct));
		}

		return ',' . implode(',', $remCT) . ',';
	}

	public function getRemClass(){
		return $this->remClass;
	}

	public function getCollection($asArray = false){
		$coll = $this->getRemTable() === stripTblPrefix(FILE_TABLE) ? $this->fileCollection : $this->objectCollection;

		return $asArray ? explode(',', trim($coll, ',')) : $coll;
	}

	// verify collection against remTable, remCT, remClass and ID
	public function getValidCollection($skipEmpty = true, $full = false, $updateCollection = false){
		if($this->getRemTable() === stripTblPrefix(FILE_TABLE)){
			$activeCollectionName = 'fileCollection';
			$fields = 'ID,Path,ContentType,Extension,Filename';
			$table = FILE_TABLE;
			$prop = 'remCT';
			$cField = 'ContentType';
		} else {
			$activeCollectionName = 'objectCollection';
			$fields = 'ID,Path,TableID,Text';
			$table = OBJECT_FILES_TABLE;
			$prop = 'remClass';
			$cField = 'TableID';
		}
		$this->$activeCollectionName = !trim($this->$activeCollectionName, ',') ? ',-1,' : $this->$activeCollectionName;

		$this->DB_WE->query('SELECT ' . $fields . ' FROM ' . $table . ' WHERE ID IN (' . trim($this->$activeCollectionName, ',') . ') AND NOT IsFolder');
		$verifiedItems = [];
		while($this->DB_WE->next_record()){
			if(!trim($this->$prop, ',') || in_array($this->DB_WE->f($cField), makeArrayFromCSV($this->$prop))){
				if($full){
					$verifiedItems[$this->DB_WE->f('ID')] = array_merge($this->getEmptyItem(), ['id' => $this->DB_WE->f('ID'), 'path' => $this->DB_WE->f('Path'), 'ct' => $this->DB_WE->f($cField), 'name' => $this->DB_WE->f('Filename'), 'ext' => $this->DB_WE->f('Extension')]);
				} else {
					$verifiedItems[$this->DB_WE->f('ID')] = $this->DB_WE->f('ID');
				}
			}
		}

		if($full && $this->getRemTable() === stripTblPrefix(FILE_TABLE)){
			$verifiedItems = $this->setItemElements($verifiedItems);
		}

		$ret = [];
		$tempCollection = ',';
		$emptyItem = ['id' => -1, 'path' => '', 'type' => '', 'name' => '', 'ext' => '', 'elements' => ['attrib_title' => ['Dat' => '', 'state' => self::CLASS_NONE], 'attrib_alt' => ['Dat' => '', 'state' => self::CLASS_NONE], 'meta_title' => ['Dat' => '', 'state' => self::CLASS_NONE], 'meta_description' => ['Dat' => '', 'state' => self::CLASS_NONE], 'custom' => ['type' => '', 'Dat' => '', 'BDID' => 0]], 'icon' => ['imageView' => '', 'imageViewPopup' => '', 'sizeX' => 0, 'sizeY' => 0, 'url' => '', 'urlPopup' => '']];

		$activeCollection = explode(',', trim($this->$activeCollectionName, ','));
		$activeCollection = $this->IsDuplicates ? $activeCollection : array_unique($activeCollection);
		foreach($activeCollection as $id){
			$id = intval($id);
			if(isset($verifiedItems[$id])){
				$ret[] = $verifiedItems[$id];
			}
			if(!$skipEmpty && $id === -1){
				$ret[] = $full ? $emptyItem : -1;
			}
			$tempCollection .= $id . ',';
		}

		if($updateCollection){
			$this->$activeCollectionName = $tempCollection;
		}

		return $ret;
	}

	public function setCollection($coll){
		$collectionName = $this->getRemTable() === stripTblPrefix(FILE_TABLE) ? 'fileCollection' : 'objectCollection';
		$this->$collectionName = ',' . implode(',', $coll) . ',';
	}

	public function getText(){
		return $this->Filename;
	}

	public function getEditorBodyAttributes($editor = 0){
		switch($editor){
			case self::EDITOR_HEADER:
				return [
					'ondragenter' => "we_editor_header.dragEnter();",
					'ondragleave' => "we_editor_header.dragLeave();"
				];
			case self::EDITOR_FOOTER:
				return [
					'ondragenter' => "we_editor_footer.dragEnter();",
					'ondragleave' => "we_editor_footer.dragLeave();"
				];
			default:
				return [];
		}
	}

	public function initByID($ID, $Table = VFILE_TABLE, $from = we_class::LOAD_MAID_DB){
		parent::initByID($ID, VFILE_TABLE);
	}

	function editor(){
		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return 'we_editors/we_editor_content_collection.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
		}
	}

	public function getPropertyPage(){
		return we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
			we_html_multiIconBox::getHTML('PropertyPage', [
				['icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(!permissionhandler::hasPerm('MOVE_COLLECTION')), 'space' => we_html_multiIconBox::SPACE_MED2],
				['icon' => 'cache.gif', 'headline' => 'Inhalt', 'html' => $this->formContent(), 'space' => we_html_multiIconBox::SPACE_MED2],
				['icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => we_html_multiIconBox::SPACE_MED2]]
		);
	}

	function formContent($fixedRemTable = false){
		$fixedRemTable = true || !permissionhandler::hasPerm('NEW_COLLECTION');
		$this->remTable = stripTblPrefix(FILE_TABLE); // FIXME: remove this line when object collections are implemented

		$valsRemTable = [
			'tblFile' => g_l('navigation', '[documents]')
		];
		if(defined('OBJECT_TABLE')){
			$valsRemTable['tblObjectFiles'] = g_l('navigation', '[objects]');
		}

		$allMime = we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true);
		$remCtArr = makeArrayFromCSV($this->remCT);
		$tmpRemCT = ',';
		$selectedMime = $unselectedMime = [];
		foreach($allMime as $mime){
			if(in_array($mime, $remCtArr)){
				$selectedMime[$mime] = g_l('contentTypes', '[' . $mime . ']');
				$tmpRemCT .= $mime . ',';
			} else {
				$unselectedMime[$mime] = g_l('contentTypes', '[' . $mime . ']');
			}
		}
		$this->remCT = $tmpRemCT;
		$attribsFrom = $attribsTo = !permissionhandler::hasPerm('NEW_COLLECTION') ? ['disabled' => 'disabled'] : [];
		$mimeListFrom = we_html_tools::htmlSelect('mimeListFrom', $unselectedMime, 13, '', true, array_merge($attribsFrom, array("id" => "mimeListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListFrom'],this.form['mimeListTo'],true, 'document');")), 'value', 175);
		$mimeListTo = we_html_tools::htmlSelect('mimeListTo', $selectedMime, 13, '', true, array_merge($attribsTo, array("id" => "mimeListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListTo'],this.form['mimeListFrom'],true, 'document');")), 'value', 175);
		$mimeTable = new we_html_table(array('class' => 'collection_props-mime default'), 1, 3);
		$mimeTable->setCol(0, 0, [], $mimeListFrom);
		$mimeTable->setCol(0, 1, array('style' => 'text-align:center;vertical-align:middle'), we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('mimeListFrom'),document.getElementById('mimeListTo'),true, 'document');return false;"
				), '<i class="fa fa-lg fa-caret-right"></i>') . we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('mimeListTo'),document.getElementById('mimeListFrom'),true, 'document');return false;"
				), '<i class="fa fa-lg fa-caret-left"></i>'));
		$mimeTable->setCol(0, 2, [], $mimeListTo);

		$selectedClasses = [];
		$unselectedClasses = [];
		$allClasses = [];
		$allowedClasses = we_users_util::getAllowedClasses($this->DB_WE);
		if(defined('OBJECT_TABLE')){
			$this->DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE);
			while($this->DB_WE->next_record()){
				if(in_array($this->DB_WE->f('ID'), $allowedClasses)){
					if(in_array($this->DB_WE->f('ID'), makeArrayFromCSV($this->remClass))){
						$selectedClasses[$this->DB_WE->f('ID')] = $this->DB_WE->f('Text');
					} else {
						$unselectedClasses[$this->DB_WE->f('ID')] = $this->DB_WE->f('Text');
					}
				}
			}
		}
		$classListFrom = we_html_tools::htmlSelect('classListFrom', $unselectedClasses, max(count($allClasses), 5), '', true, array_merge($attribsFrom, ["id" => "classListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListFrom'],this.form['classListTo'],true, 'object');"]), 'value', 184);
		$classListTo = we_html_tools::htmlSelect('classListTo', $selectedClasses, max(count($allClasses), 5), '', true, array_merge($attribsTo, ["id" => "classListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListTo'],this.form['classListFrom'],true, 'object');"]), 'value', 184);

		$classTable = new we_html_table(['class' => 'collection_props-classes default'], 1, 3);
		$classTable->setCol(0, 0, null, $classListFrom);
		//FIXME: why a tags, if onclick is used????

		$classTable->setCol(0, 1, array('style' => 'text-align:center;vertical-align:middle'
			), we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('classListFrom'),document.getElementById('classListTo'),true, 'object');return false;"
				), '<i class="fa fa-lg fa-caret-right"></i>') . we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('classListTo'),document.getElementById('classListFrom'),true, 'object');return false;"
				), '<i class="fa fa-lg fa-caret-left"></i>'));
		$classTable->setCol(0, 2, null, $classListTo);

		$selRemTable = ($fixedRemTable && $this->getRemTable() ? we_html_element::htmlHidden('we_' . $this->Name . '_remTable', $this->getRemTable()) . we_html_element::htmlInput(array('disabled' => 1, 'name' => 'disabledField', 'value' => $valsRemTable[$this->getRemTable()], 'width' => 356)) :
				we_html_tools::htmlSelect('we_' . $this->Name . '_remTable', $valsRemTable, 1, $this->getRemTable(), false, array('onchange' => 'document.getElementById(\'mimetype\').style.display=(this.value===\'tblFile\'?\'block\':\'none\');document.getElementById(\'classname\').style.display=(this.value===\'tblFile\'?\'none\':\'block\');', 'style' => 'margin-top: 5px;'), 'value')) .
			we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[collection][selector_remTable]'), we_html_tools::TYPE_HELP, false);


		$dublettes = we_html_forms::checkboxWithHidden($this->IsDuplicates, 'we_' . $this->Name . '_IsDuplicates', g_l('weClass', '[collection][allowDuplicates]'), false, 'defaultfont', '', !permissionhandler::hasPerm('NEW_COLLECTION'));

		$this->DefaultDir = $this->DefaultDir ? : (IMAGESTARTID_DEFAULT ? : 0);
		$this->DefaultPath = $this->DefaultDir ? id_to_path($this->DefaultDir, FILE_TABLE) : '';
		$defDir = $this->formDirChooser(360, 0, FILE_TABLE, 'DefaultPath', 'DefaultDir', '', g_l('weClass', '[collection][label_defaultDir]'), !permissionhandler::hasPerm('NEW_COLLECTION'));

		$html = $selRemTable .
			'<div id="collection_props-mime_class">
	<div id="mimetype" class="collection_props-mime" style="' . ($this->getRemTable() === 'tblObjectFiles' ? 'display:none' : 'display:block') . ';">' .
			'<br/>' . g_l('weClass', '[collection][filter_contenttype]') . ':<br/>' .
			we_html_element::htmlHidden('we_' . $this->Name . '_remCT', $this->remCT, 'we_remCT') .
			$mimeTable->getHTML() .
			'</div>
	<div id="classname" class="collection_props-classes" style="' . ($this->getRemTable() === 'tblObjectFiles' ? 'display:block' : 'display:none') . ';">' .
			(defined('OBJECT_TABLE') ? '<br/>' . g_l('weClass', '[collection][filter_class]') . ':<br/>' .
				we_html_element::htmlHidden('we_' . $this->Name . '_remClass', $this->remClass, 'we_remClass') .
				$classTable->getHTML() : '') .
			'</div>
</div>' .
			we_html_element::htmlDiv(array('class' => 'collection_props-dublettes'), $dublettes) .
			we_html_element::htmlDiv([], $defDir);

		return $html;
	}

	function formCollection(){
		$recursive = we_html_forms::checkboxWithHidden($this->InsertRecursive, 'we_' . $GLOBALS['we_doc']->Name . '_InsertRecursive', g_l('weClass', '[collection][insertRecursive]')) .
			we_html_element::htmlHidden('check_we_' . $GLOBALS['we_doc']->Name . '_IsDuplicates', $this->IsDuplicates);
		$slider = '<div id="sliderDiv" style="display:' . ($this->view === 'grid' ? 'block' : 'none') . '"><input type="range" class="collection-Slider" name="zoom" min="1" step="1" max="5" value="' . (7 - $this->itemsPerRow) . '" onchange="weCollectionEdit.doZoomGrid(this.value);"/></div>';
		$btnGridview = we_html_button::create_button('fa:iconview,fa-lg fa-th', "javascript:weCollectionEdit.setView('grid');", true, 40, "", "", "", false);
		$btnListview = we_html_button::create_button('fa:listview,fa-lg fa-th-list', "javascript:weCollectionEdit.setView('list');", true, 40, "", "", "", false);
		$btnListviewMinimal = we_html_button::create_button('fa:listview_minimal,fa-lg fa-align-justify', "javascript:weCollectionEdit.setView('list', 'minimal');", true, 40, "", "", "", false);

		$callback = we_base_request::encCmd("doFileUploader('" . VFILE_TABLE . "', " . $this->ID . ");");
		$btnImport = we_fileupload_ui_importer::getBtnImportFiles($this->DefaultDir, $callback, 'btn_import_files_and_insert');
		$addFromTreeButton = we_html_button::create_button('fa:btn_select_files, fa-lg fa-sitemap, fa-lg fa-angle-right, fa-lg fa-copy', "javascript:weCollectionEdit.doClickAddItems();", true, 62, 22, '', '', false, false, '', false, '', 'btn_addFromTree');

		//TODO: use tables and some padding
		$toolbar = new we_html_table([], 1, 8);
		$toolbar->setCol(0, 0, ['class' => 'toolbarRecursive'], $recursive);
		$toolbar->setCol(0, 1, ['class' => 'toolbarSlider'], $slider);
		$toolbar->setCol(0, 2, ['class' => 'toolbarView'], $btnGridview);
		$toolbar->setCol(0, 3, ['class' => 'toolbarView'], $btnListview);
		$toolbar->setCol(0, 4, ['class' => 'toolbarView'], $btnListviewMinimal);
		$toolbar->setCol(0, 5, ['class' => 'toolbarAdd'], $addFromTreeButton);
		$toolbar->setCol(0, 6, ['class' => 'toolbarImport'], $btnImport);
		$toolbar->setCol(0, 7, ['class' => 'toolbarNum weMultiIconBoxHeadline'], g_l('weClass', '[collection][number]') . ': <span id="numSpan"><i class="fa fa-2x fa-spinner fa-pulse"></i></span>');

		$items = $this->getValidCollection(false, true, true);

		$yuiSuggest = &weSuggest::getInstance();
		$index = 0;
		$jsItemsArr = $rows = $divs = '';
		$jsStorageItems = "\n";

		foreach($items as $item){
			//FIXME: set icon in getValidCollection() and make only what's really needed
			if(is_numeric($item['id']) && $item['id'] !== -1 && $item['ct'] === 'image/*'){
				$file = array('docID' => $item['id'], 'Path' => $item['path'], 'ContentType' => isset($item['ct']) ? $item['ct'] : 'text/*', 'Extension' => $item['ext']);
				$file['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) : 0;
				$file['fileSize'] = we_base_file::getHumanFileSize($file['size']);
				// FIXME: look for biggest icon size in grid view dynamically
				$item['icon'] = self::getHtmlIconThmubnail($file, 400, 400);
			}

			/*
			  $index++;
			  if($this->view === 'grid'){
			  $divs .= $this->makeGridItem($item, $index, count($items));
			  } else {
			  $rows .= $this->makeListItem($item, $index, $yuiSuggest, count($items), true);
			  }
			 *
			 */
			$jsStorageItems .= $this->getJsStrorageItem($item, ++$index);
			$jsItemsArr .= $item['id'] . ',';
		}

		// write "blank" collection row to js
		$placeholders = [
			'id' => '##ID##',
			'path' => '##PATH##',
			'type' => '##CT##',
			'class' => '##CLASS##',
			'icon' => ['url' => '##ICONURL##', 'sizeX' => 200, 'sizeY' => 200],
			'elements' => [
				'attrib_title' => ['Dat' => '##ATTRIB_TITLE##', 'state' => '##S_ATTRIB_TITLE##', 'write' => '##W_ATTRIB_TITLE##'],
				'attrib_alt' => ['Dat' => '##ATTRIB_ALT##', 'state' => '##S_ATTRIB_ALT##', 'write' => '##W_ATTRIB_ALT##'],
				'meta_title' => ['Dat' => '##META_TITLE##', 'state' => '##S_META_TITLE##', 'write' => '##W_META_TITLE##'],
				'meta_description' => ['Dat' => '##META_DESC##', 'state' => '##S_META_DESC##', 'write' => '##W_META_DESC##'],
				'custom' => ['Dat' => '##CUSTOM##']
			]
		];

		$this->jsFormCollection .= "
weCollectionEdit.isDragAndDrop = " . (self::isDragAndDrop() ? 1 : 0) . ";
weCollectionEdit.gridItemDimension = " . json_encode($this->gridItemDimensions[$this->itemsPerRow]) . ";
weCollectionEdit.maxIndex = " . count($items) . ";
weCollectionEdit.blankItem.list = '" . strtr($this->makeListItem($placeholders, '##INDEX##', $yuiSuggest, 1, true, true), ["'" => "\'", "\r" => '', "\n" => '']) . "';
weCollectionEdit.blankItem.listMinimal = '" . strtr($this->makeListItemMinimal($placeholders, '##INDEX##', $yuiSuggest, 1, true, true), ["'" => "\'", "\r" => '', "\n" => '']) . "';
weCollectionEdit.blankItem.grid = '" . strtr($this->makeGridItem($placeholders, '##INDEX##'), ["'" => "\'", "\r" => '', "\n" => '']) . "';
weCollectionEdit.collectionArr = [" . rtrim($jsItemsArr, ',') . "];
weCollectionEdit.view = '" . $this->view . "';
weCollectionEdit.viewSub = '" . ($this->viewSub === 'minimal' ? 'minimal' : 'broad') . "';
weCollectionEdit.gridItemDimensions = " . json_encode($this->gridItemDimensions) . ";
weCollectionEdit.storage = {};
weCollectionEdit.storage['item_-1'] = " . json_encode($this->getEmptyItem()) . ";
" .
			$jsStorageItems;

		$longtext = g_l('weClass', '[collection][long_description]');
		$ddtext = self::isDragAndDrop() ? g_l('weClass', '[collection][dd_ok]') : (we_base_browserDetect::isOpera() ? 'Drag n\' drop is not yet optimized for Opera 12: temporarily disabled!' : g_l('weClass', '[collection][dd_nok]'));


		return we_html_element::jsElement($this->jsFormCollection) .
			we_html_element::htmlHiddens(['we_' . $this->Name . '_view' => $this->view,
				'we_' . $this->Name . '_viewSub' => $this->viewSub,
				'we_' . $this->Name . '_itemsPerRow' => $this->itemsPerRow,
				'we_' . $this->Name . '_fileCollection' => $this->fileCollection,
				'we_' . $this->Name . '_objectCollection' => $this->objectCollection]) .
			we_html_element::htmlDiv(['class' => 'weMultiIconBoxHeadline collection-head'], g_l('weClass', '[collection][collectionTitle]')) .
			we_html_element::htmlDiv(['class' => 'collection-head'], we_html_tools::htmlAlertAttentionBox($longtext . $ddtext, we_html_tools::TYPE_INFO, 850, false, 29)) .
			we_html_element::htmlDiv(['class' => 'collection-toolbar'], $toolbar->getHtml()) .
			we_html_element::htmlDiv(['id' => 'content_div_list', 'class' => 'collection-content', 'style' => 'display:' . ($this->view === 'grid' ? 'none' : 'block')], $rows) .
			we_html_element::htmlDiv(['id' => 'content_div_grid', 'class' => 'collection-content', 'style' => 'display:' . ($this->view === 'grid' ? 'inline-block' : 'none')]);
	}

	private function makeListItem($item, $index, &$yuiSuggest, $itemsNum = 0, $noAcAutoInit = false, $noSelectorAutoInit = false){
		$textname = 'we_' . $this->Name . '_ItemName_' . $index;
		$idname = 'we_' . $this->Name . '_ItemID_' . $index;
		$wecmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmd2 = "document.we_form.elements['" . $textname . "'].value";
		//$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);opener.weCollectionEdit.reindexAndRetrieveCollection();";
		$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);try{opener._EditorFrame.getContentEditor().weCollectionEdit.callForValidItemsAndInsert(" . $index . ", opener._EditorFrame.getContentEditor().document.we_form.elements['" . $idname . "'].value);} catch(e){}";


		if($noSelectorAutoInit){
			$this->jsFormCollection .= 'weCollectionEdit.selectorCmds = ["' . $wecmd1 . '","' . $wecmd2 . '","' . $wecmd3 . '"];';
			$wecmdenc1 = '##CMD1##';
			$wecmdenc2 = '##CMD2##';
			$wecmdenc3 = '##CMD3##';
		} else {
			$wecmdenc1 = we_base_request::encCmd($wecmd1);
			$wecmdenc2 = we_base_request::encCmd($wecmd2);
			$wecmdenc3 = we_base_request::encCmd($wecmd3);
		}

		$selectButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',(document.we_form.elements['" . $idname . "'].value != -1 ? document.we_form.elements['" . $idname . "'].value : " . $this->DefaultDir . "),'" . addTblPrefix($this->getRemTable()) . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . trim($this->remCT, ',') . "'," . (permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') ? 0 : 1) . ")", true, 52, 0, '', '', false, false, '_' . $index);
		$addFromTreeButton = we_html_button::create_button('fa:btn_select_files, fa-lg fa-sitemap, fa-lg fa-angle-right, fa-lg fa-copy', "javascript:weCollectionEdit.doClickAddItems(this);", true, 62, 22, '', '', false, false, '', false, '', 'btn_addFromTree');
		$editButton = we_html_button::create_button(we_html_button::EDIT, "javascript:weCollectionEdit.doClickOpenToEdit(" . $item['id'] . ", '" . $item['type'] . "');", true, 27, 22, '', '', ($item['id'] === -1), false, '', false, '', 'btn_edit');

		$yuiSuggest->setTable(addTblPrefix($this->getRemTable()));
		$yuiSuggest->setContentType('folder,' . trim($this->remCT, ','));
		$yuiSuggest->setCheckFieldValue(false);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setAcId('Item_' . $index);
		$yuiSuggest->setNoAutoInit($noAcAutoInit);
		$yuiSuggest->setInput($textname, $item['path'], ['title' => $item['path'] . ' (ID: ' . $item['id'] . ')']);
		$yuiSuggest->setResult($idname, $item['id']);
		$yuiSuggest->setWidth(240);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setSelectButton($editButton, 0);
		$yuiSuggest->setAdditionalButton($addFromTreeButton, 0);
		//$yuiSuggest->setOpenButton($editButton, 4);
		$yuiSuggest->setDoOnItemSelect("weCollectionEdit.repaintAndRetrieveCsv();");

		$rowControlls = we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:_EditorFrame.setEditorIsHot(true);weCollectionEdit.doClickAdd(this);", true, 50, 22) .
			//$rowControllsArr[] = we_html_tools::htmlSelect('numselect_' . $index, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 1, '', false, array('id' => 'numselect_' . $index));
			we_html_button::create_button(we_html_button::DIRUP, 'javascript:weCollectionEdit.doClickUp(this);', true, 0, 0, '', '', ($index === 1 ? true : false), false, '_' . $index, false, '', 'btn_up') .
			we_html_button::create_button(we_html_button::DIRDOWN, 'javascript:weCollectionEdit.doClickDown(this);', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index, false, '', 'btn_down') .
			we_html_button::create_button('fa:btn_remove_from_collection,fa-lg fa-trash-o', 'javascript:weCollectionEdit.doClickDelete(this)', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);

		$rowHtml = new we_html_table(['class' => $item['class'], 'draggable' => 'false'], 1, 4);
		$imgDiv = we_html_element::htmlDiv(['id' => 'previweDiv_' . $index,
				'class' => 'previewDiv',
				'style' => "background-image:url('" . $item['icon']['url'] . "');",
				'title' => $item['path'] . ' (ID: ' . $item['id'] . ')'
				], we_html_element::htmlDiv(['class' => 'divBtnSelect'], $selectButton));
		$rowHtml->setCol(0, 0, ['class' => 'colNum weMultiIconBoxHeadline'], '<span class="list_label" id="label_' . $index . '">' . $index . '</span>');
		$rowHtml->setCol(0, 1, ['class' => 'colPreview'], $imgDiv);

		$rowInnerTable = new we_html_table(['draggable' => 'false'], 2, 1);
		$rowInnerTable->setCol(0, 0, ['colspan' => 1], $yuiSuggest->getHTML());

		$attrTitle = we_html_element::htmlDiv(['class' => 'innerDiv defaultfont' . ($item['elements']['attrib_title']['Dat'] ? ' div_' . $item['elements']['attrib_title']['state'] : ''),
				'title' => ($item['elements']['attrib_title']['Dat'])
				], '<i class="fa fa-lg fa-circle ' . $item['elements']['attrib_title']['state'] . '"></i> ' . $item['elements']['attrib_title']['write']);
		$attrAlt = we_html_element::htmlDiv(['class' => 'innerDiv defaultfont' . ($item['elements']['attrib_alt']['Dat'] ? ' div_' . $item['elements']['attrib_alt']['state'] : ''),
				'title' => ($item['elements']['attrib_alt']['Dat'])
				], '<i class="fa fa-lg fa-circle ' . $item['elements']['attrib_alt']['state'] . '"></i> ' . $item['elements']['attrib_alt']['write']);
		$metaTitle = we_html_element::htmlDiv(['class' => 'innerDiv defaultfont' . ($item['elements']['meta_title']['Dat'] ? ' div_' . $item['elements']['meta_title']['state'] : ''),
				'title' => ($item['elements']['meta_title']['Dat'])
				], '<i class="fa fa-lg fa-dot-circle-o ' . $item['elements']['meta_title']['state'] . '"></i> ' . $item['elements']['meta_title']['write']);
		$metaDesc = we_html_element::htmlDiv(['class' => 'innerDiv defaultfont' . ($item['elements']['meta_description']['Dat'] ? ' div_' . $item['elements']['meta_description']['state'] : ''),
				'title' => ($item['elements']['meta_description']['Dat'])
				], '<i class="fa fa-lg fa-dot-circle-o ' . $item['elements']['meta_description']['state'] . '"></i> ' . $item['elements']['meta_description']['write']);

		$rowInnerTable->setCol(1, 0, [], $attrTitle . $attrAlt . $metaTitle . $metaDesc);
		$rowInnerTable->setRowAttributes(1, ['class' => 'rowAttribsMeta']);

		$rowHtml->setCol(0, 2, ['class' => 'colContent'], $rowInnerTable->getHtml());
		$rowHtml->setCol(0, 3, ['class' => 'colControls weMultiIconBoxHeadline'], $rowControlls);

		return we_html_element::htmlDiv(['id' => 'list_item_' . $index, 'class' => 'listItem', 'draggable' => 'false'], $rowHtml->getHtml());
	}

	private function makeListItemMinimal($item, $index, &$yuiSuggest, $itemsNum = 0, $noAcAutoInit = false, $noSelectorAutoInit = false){
		$textname = 'we_' . $this->Name . '_ItemName_' . $index;
		$idname = 'we_' . $this->Name . '_ItemID_' . $index;
		$wecmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmd2 = "document.we_form.elements['" . $textname . "'].value";
		//$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);opener.weCollectionEdit.reindexAndRetrieveCollection();";
		$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);try{opener._EditorFrame.getContentEditor().weCollectionEdit.callForValidItemsAndInsert(" . $index . ", opener._EditorFrame.getContentEditor().document.we_form.elements['" . $idname . "'].value);} catch(e){}";

		if($noSelectorAutoInit){
			$this->jsFormCollection .= 'weCollectionEdit.selectorCmds = ["' . $wecmd1 . '","' . $wecmd2 . '","' . $wecmd3 . '"];';
			$wecmdenc1 = '##CMD1##';
			$wecmdenc2 = '##CMD2##';
			$wecmdenc3 = '##CMD3##';
		} else {
			$wecmdenc1 = we_base_request::encCmd($wecmd1);
			$wecmdenc2 = we_base_request::encCmd($wecmd2);
			$wecmdenc3 = we_base_request::encCmd($wecmd3);
		}

		$selectButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',(document.we_form.elements['" . $idname . "'].value != -1 ? document.we_form.elements['" . $idname . "'].value : " . $this->DefaultDir . "),'" . addTblPrefix($this->getRemTable()) . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . trim($this->remCT, ',') . "'," . (permissionhandler::hasPerm('CAN_SELECT_OTHER_USERS_OBJECTS') ? 0 : 1) . ")", true, 52, 0, '', '', false, false, '_' . $index);
		$editButton = we_html_button::create_button(we_html_button::EDIT, "javascript:weCollectionEdit.doClickOpenToEdit(" . $item['id'] . ", '" . $item['type'] . "');", true, 27, 22, '', '', ($item['id'] === -1), false, '', false, '', 'btn_edit');

		$yuiSuggest->setTable(addTblPrefix($this->getRemTable()));
		$yuiSuggest->setContentType('folder,' . trim($this->remCT, ','));
		$yuiSuggest->setCheckFieldValue(false);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setAcId('Item_' . $index);
		$yuiSuggest->setNoAutoInit($noAcAutoInit);
		$yuiSuggest->setInput($textname, $item['path'], ['title' => $item['path'] . ' (ID: ' . $item['id'] . ')']);
		$yuiSuggest->setResult($idname, $item['id']);
		$yuiSuggest->setWidth(500);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setSelectButton(null, 0);
		$yuiSuggest->setDoOnItemSelect("weCollectionEdit.repaintAndRetrieveCsv();");
		$yuiSuggest->setAdditionalButton('', 0);
		$divRowContent = we_html_element::htmlDiv(['class' => 'divContent'], we_html_element::htmlDiv(['class' => 'colContentInput'], $yuiSuggest->getHTML()) .
				we_html_element::htmlDiv(['class' => 'colContentTextOnly'])
		);

		$rowControlls = we_html_element::htmlDiv(['class' => 'divBtnEditTextOnly'], $editButton) . we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:_EditorFrame.setEditorIsHot(true);weCollectionEdit.doClickAdd(this);", true, 50, 22) .
			we_html_button::create_button('fa:btn_remove_from_collection,fa-lg fa-trash-o', 'javascript:weCollectionEdit.doClickDelete(this)', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);

		$rowHtml = new we_html_table(['class' => $item['class'], 'draggable' => 'false'], 1, 4);
		$imgDiv = we_html_element::htmlDiv(['id' => 'previweDiv_' . $index,
				'class' => 'previewDiv',
				'style' => "background-image:url('" . $item['icon']['url'] . "');",
				'title' => $item['path'] . ' (ID: ' . $item['id'] . ')'
				], we_html_element::htmlDiv(['class' => 'divBtnSelect'], $selectButton));
		$rowHtml->setCol(0, 0, ['class' => 'colNum weMultiIconBoxHeadline'], '<span class="list_label" id="label_' . $index . '">' . $index . '</span>');
		$rowHtml->setCol(0, 1, ['class' => 'colPreview'], $imgDiv);
		$rowHtml->setCol(0, 2, ['class' => 'colContent'], $divRowContent);
		$rowHtml->setCol(0, 3, ['class' => 'colControls weMultiIconBoxHeadline'], $rowControlls);

		return we_html_element::htmlDiv(['id' => 'list_item_' . $index, 'class' => 'listItem', 'draggable' => 'false'], $rowHtml->getHtml());
	}

	private function makeGridItem($item, $index){ // TODO: maybe write only blank item and let JS render items oninit from storage?
		$idname = 'collectionItem_we_id_' . $index;
		//$wecmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmd1 = "WE().layout.weEditorFrameController.getVisibleEditorFrame().document.we_form.elements['" . $idname . "'].value";
		$wecmd2 = "";
		//$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);try{var ce = opener._EditorFrame.getContentEditor();ce.weCollectionEdit.callForValidItemsAndInsert(ce.weCollectionEdit.getItemId(ce.document.we_form.elements['collectionItem_we_id_" . $index . "']), top.fileSelect.data.currentID);} catch(e){}";
		$wecmd3 = "try{var ce = top.opener.WE().layout.weEditorFrameController.getVisibleEditorFrame();ce.weCollectionEdit.callForValidItemsAndInsert(ce.weCollectionEdit.getItemId(ce.document.we_form.elements['collectionItem_we_id_" . $index . "']), top.fileSelect.data.currentID);} catch(e){}";

		switch($item['id']){
			case '##ID##':
				$this->jsFormCollection .= "\n" . 'weCollectionEdit.gridBtnCmds = ["' . $wecmd1 . '","' . $wecmd2 . '","' . $wecmd3 . '"];';
				$wecmdenc1 = '##CMD1##';
				$wecmdenc2 = '';
				$wecmdenc3 = '##CMD3##';
				break;
			case -1:
				$wecmdenc1 = we_base_request::encCmd($wecmd1);
				$wecmdenc2 = '';
				$wecmdenc3 = we_base_request::encCmd($wecmd3);
				break;
			default:
				$wecmdenc1 = $wecmdenc2 = $wecmdenc3 = '';
		}

		$trashButton = we_html_button::create_button('fa:btn_remove_from_collection,fa-lg fa-trash-o', "javascript:weCollectionEdit.doClickDelete(this);", true, 27, 22);
		$editButton = we_html_button::create_button(we_html_button::EDIT, "javascript:weCollectionEdit.doClickOpenToEdit(" . $item['id'] . ", '" . $item['type'] . "');", true, 27, 22);
		$selectButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',(document.we_form.elements['" . $idname . "'].value != -1 ? document.we_form.elements['" . $idname . "'].value : " . $this->DefaultDir . "),'" . addTblPrefix($this->getRemTable()) . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . trim($this->remCT, ',') . "',1)", true, 52, 0, '', '', false, false, '_' . $index);

		// TODO: make fn for attribs: same structure as in list
		$toolbar = we_html_element::htmlDiv(array('class' => 'toolbarLeft weMultiIconBoxHeadline'), '<span class="grid_label" id="label_' . $index . '">' . $index . '</span>') .
			we_html_element::htmlDiv(array(
				'class' => 'toolbarAttribs',
				'style' => 'display:' . ($this->itemsPerRow > 5 ? 'none' : 'block')
				), we_html_element::htmlDiv(array(
					'class' => 'toolbarAttr',
					'title' => $item['elements']['attrib_title']['Dat']
					), '<i class="fa fa-lg fa-circle ' . $item['elements']['attrib_title']['state'] . '"></i>') .
				we_html_element::htmlDiv(array(
					'class' => 'toolbarAttr',
					'title' => $item['elements']['attrib_alt']['Dat'],
					), '<i class="fa fa-lg fa-circle ' . $item['elements']['attrib_alt']['state'] . '"></i>') .
				we_html_element::htmlDiv(array(
					'class' => 'toolbarAttr',
					'title' => $item['elements']['meta_title']['Dat'],
					), '<i class="fa fa-lg fa-dot-circle-o ' . $item['elements']['meta_title']['state'] . '"></i>') .
				we_html_element::htmlDiv(array(
					'class' => 'toolbarAttr',
					'title' => $item['elements']['meta_description']['Dat']
					), '<i class="fa fa-lg fa-dot-circle-o ' . $item['elements']['meta_description']['state'] . '"></i>')
			) . we_html_element::htmlDiv(array(
				'class' => 'toolbarBtns',
				), $editButton . $trashButton);
		//);

		$displayBtnEdit = $item['id'] === -1 ? 'block' : ($item['id'] === '##ID##' ? '##SHOWBTN##' : 'none');

		return we_html_element::htmlDiv(array(
				//TODO: set dimensions by JS
				'style' => 'width:' . $this->gridItemDimensions[$this->itemsPerRow]['item'] . 'px;height:' . $this->gridItemDimensions[$this->itemsPerRow]['item'] . 'px;',
				'id' => 'grid_item_' . $index,
				'class' => 'gridItem'
				), we_html_element::htmlDiv(array(
					'title' => $item['path'] . ' (ID: ' . $item['id'] . ')',
					'class' => 'divContent',
					'style' => ($item['icon'] ? "background-image:url('" . $item['icon']['url'] . "');" : '') . (max($item['icon']['sizeX'], $item['icon']['sizeY']) < $this->gridItemDimensions[$this->itemsPerRow]['item'] ? 'background-size:auto;' : ''),
					'draggable' => 'false',
					), we_html_element::htmlDiv(array(
						'class' => 'divInner',
						'style' => 'display:' . $displayBtnEdit
						), $selectButton) .
					we_html_element::htmlDiv(array(
						'class' => 'divToolbar',
						'draggable' => false
						), $toolbar)) .
				we_html_element::htmlDiv(array(
					'class' => 'divSpace',
					'id' => 'grid_space_' . $index,
					'title' => g_l('weClass', '[collection][dblClick_to_insert]')
					), '') . we_html_element::htmlHidden('collectionItem_we_id', $item['id']) . we_html_element::htmlHidden('collectionItem_we_id_' . $index, $item['id'])
		);
		//TODO: use indexed input field only
	}

	private function getJsStrorageItem($item){
		return $item['id'] == -1 ? '' : "weCollectionEdit.storage['item_" . $item['id'] . "'] = " . json_encode($item) . ";\n";
	}

	private function getEmptyItem(){
		return ['id' => -1,
			'path' => '',
			'ct' => '',
			'name' => '',
			'ext' => '',
			'elements' => [
				'attrib_title' => ['Dat' => '',
					'state' => self::CLASS_NONE,
					'write' => ''
				],
				'attrib_alt' => ['Dat' => '',
					'state' => self::CLASS_NONE,
					'write' => ''
				], 'meta_title' => ['Dat' => '',
					'state' => self::CLASS_NONE,
					'write' => ''
				],
				'meta_description' => ['Dat' => '',
					'state' => self::CLASS_NONE,
					'write' => ''
				],
				'custom' => ['type' => '',
					'Dat' => '',
					'BDID' => 0]
			],
			'icon' => ['imageView' => '',
				'imageViewPopup' => '',
				'sizeX' => 0,
				'sizeY' => 0,
				'url' => '',
				'urlPopup' => ''
			]
		];
	}

	private function setItemElements($items){
		if(empty($items)){
			return $items;
		}

		$itemsCsv = implode(',', array_keys($items));
		$orCustomElement = ' OR (l.nHash=x\'' . md5('elemIMG') . '\' AND (c.Dat!="" OR c.BDID != 0) )';
		if($this->getRemTable() === stripTblPrefix(FILE_TABLE)){
			$this->DB_WE->query('SELECT l.DID, l.Name, l.type, c.Dat, c.BDID FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID = c.ID
				WHERE l.DocumentTable="tblFile" AND l.DID IN (' . rtrim($itemsCsv, ',') . ') AND ((l.type="attrib" AND l.nHash IN (x\'' . md5('title') . '\',x\'' . md5('alt') . '\') ) OR (l.type="txt" AND l.nHash IN (x\'' . md5('Title') . '\',x\'' . md5('Description') . '\') ) ' . $orCustomElement . ')'
			);

			while($this->DB_WE->next_record()){
				switch($this->DB_WE->f('Name')){
					case 'title':
					case 'alt':
						$fieldname = 'attrib_' . $this->DB_WE->f('Name');
						break;
					case 'Title':
					case 'Description':
						$fieldname = 'meta_' . strtolower($this->DB_WE->f('Name'));
						break;
					default:
						$fieldname = 'custom';
				}
				$items[$this->DB_WE->f('DID')]['elements'][$fieldname] = $fieldname === 'custom' ? ['type' => $this->DB_WE->f('type'), 'Dat' => $this->DB_WE->f('Dat'), 'BDID' => $this->DB_WE->f('BDID')] : ['Dat' => $this->DB_WE->f('Dat'), 'state' => self::CLASS_NONE];
			}

			// mark the first 2 set elements of each item in an ordered way
			$elements = ['attrib_title', 'attrib_alt', 'meta_title', 'meta_description'];
			$hasMeta = ['application/*', 'application/x-shockwave-flash', 'audio/*', 'image/*', 'text/webedition', 'video/*'];
			foreach($items as $k => $v){
				$c = 0;
				foreach($elements as $name){
					$items[$k]['elements'][$name]['write'] = $items[$k]['elements'][$name]['Dat'] && in_array($items[$k]['ct'], $hasMeta) && $c++ < 2 ? $items[$k]['elements'][$name]['Dat'] : '';
					switch($name){
						case 'attrib_title':
							$items[$k]['elements'][$name]['state'] = $items[$k]['ct'] !== 'image/*' ? self::CLASS_NONE : ($items[$k]['elements'][$name]['Dat'] ? self::CLASS_YES : self::CLASS_NO);
							$items[$k]['elements'][$name]['Dat'] = $items[$k]['elements'][$name]['Dat'] ? g_l('weClass', '[collection][attr_title]') . ': ' . $items[$k]['elements'][$name]['Dat'] : ($items[$k]['elements'][$name]['state'] === self::CLASS_NONE ? '' : g_l('weClass', '[collection][attr_title]') . ': ' . g_l('weClass', '[collection][notSet]'));
							break;
						case 'attrib_alt':
							$items[$k]['elements'][$name]['state'] = $items[$k]['ct'] !== 'image/*' ? self::CLASS_NONE : ($items[$k]['elements'][$name]['Dat'] ? self::CLASS_YES : self::CLASS_NO);
							$items[$k]['elements'][$name]['Dat'] = $items[$k]['elements'][$name]['Dat'] ? g_l('weClass', '[collection][attr_alt]') . ': ' . $items[$k]['elements'][$name]['Dat'] : ($items[$k]['elements'][$name]['state'] === self::CLASS_NONE ? '' : g_l('weClass', '[collection][attr_alt]') . ': ' . g_l('weClass', '[collection][notSet]'));
							break;
						case 'meta_title':
							$items[$k]['elements'][$name]['state'] = !in_array($items[$k]['ct'], $hasMeta) ? self::CLASS_NONE : ($items[$k]['elements'][$name]['Dat'] ? self::CLASS_YES : self::CLASS_NO);
							$items[$k]['elements'][$name]['Dat'] = $items[$k]['elements'][$name]['Dat'] && in_array($items[$k]['ct'], $hasMeta) ? g_l('weClass', '[Title]') . ': ' . $items[$k]['elements'][$name]['Dat'] : ($items[$k]['elements'][$name]['state'] === self::CLASS_NONE ? '' : g_l('weClass', '[Title]') . ': ' . g_l('weClass', '[collection][notSet]'));
							break;
						case 'meta_description':
							$items[$k]['elements'][$name]['state'] = !in_array($items[$k]['ct'], $hasMeta) ? self::CLASS_NONE : ($items[$k]['elements'][$name]['Dat'] ? self::CLASS_YES : self::CLASS_NO);
							$items[$k]['elements'][$name]['Dat'] = $items[$k]['elements'][$name]['Dat'] && in_array($items[$k]['ct'], $hasMeta) ? g_l('weClass', '[Description]') . ': ' . $items[$k]['elements'][$name]['Dat'] : ($items[$k]['elements'][$name]['state'] === self::CLASS_NONE ? '' : g_l('weClass', '[Description]') . ': ' . g_l('weClass', '[collection][notSet]')); //
							break;
						default:
							$fieldname = 'custom';
					}
				}
			}
		}

		return $items;
	}

	protected function i_filenameDouble(){
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Text="' . escape_sql_query($this->Text) . '" AND ID!=' . intval($this->ID), '', $this->DB_WE);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		//FIXME: remove this switch after 6.6
		$this->ContentType = $this->ContentType ? : ($this->IsFolder ? we_base_ContentTypes::FOLDER : we_base_ContentTypes::COLLECTION);
		$this->Filename = $this->Text;
	}

	function userCanSave($ctConditionOk = false){
		return permissionhandler::hasPerm('SAVE_COLLECTION') && parent::userCanSave(true);
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';

		if(!$skipHook){// TODO: integrate hooks?
		}

		$collection = $this->getValidCollection();
		$this->remCT = $this->remCT === ',' ? '' : $this->remCT;

		$ret = parent::we_save($resave) && $this->writeCollectionToDB($collection) && !$this->errMsg;

		if($ret){
			$this->unregisterMediaLinks();
			$this->registerMediaLinks($collection);
		}

		return $ret;
	}

	protected function i_getContentData(){
		//! parent::i_getContentData();

		$this->DB_WE->query('SELECT remObj,remTable FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" AND (type="collection" OR type="archive") ORDER BY position ASC');

		$this->fileCollection = ',';
		$this->objectCollection = ',';
		while($this->DB_WE->next_record()){
			if($this->DB_WE->f('remTable') == stripTblPrefix(FILE_TABLE)){
				$this->fileCollection .= $this->DB_WE->f('remObj') . ',';
			} else {
				$this->objectCollection .= $this->DB_WE->f('remObj') . ',';
			}
		}
		$this->fileCollection .= '-1,';
		$this->objectCollection .= '-1,';
	}

	protected function i_getPersistentSlotsFromDB($felder = '*'){ // FIXME: throw out when CreationDate and ModDate are migrated to MySQL timestamp in all tables
		parent::i_getPersistentSlotsFromDB($felder);

		$this->CreationDate = strtotime($this->CreationDate);
		$this->ModDate = strtotime($this->ModDate);
	}

	protected function i_savePersistentSlotsToDB($felder = ''){ // FIXME: throw out when CreationDate and ModDate are migrated to MySQL timestamp in all tables
		if(($key = array_search('CreationDate', $this->persistent_slots)) !== false){
			unset($this->persistent_slots[$key]);
		}
		$modDateTmp = $this->ModDate;
		$this->ModDate = date('Y-m-d H:i:s', $this->ModDate);
		$ret = parent::i_savePersistentSlotsToDB();

		$this->ModDate = $modDateTmp;
		$this->persistent_slots[] = 'CreationDate';

		return $ret;
	}

	protected function i_setElementsFromHTTP(){
		if(!permissionhandler::hasPerm('NEW_COLLECTION')){
			unset($_REQUEST['we_' . $this->Name . '_remTable'], $_REQUEST['we_' . $this->Name . '_remCT'], $_REQUEST['we_' . $this->Name . '_remClass'], $_REQUEST['we_' . $this->Name . '_IsDuplicates'], $_REQUEST['we_' . $this->Name . '_DefaultDir'], $_REQUEST['we_' . $this->Name . '_DefaultPath']);
		}

		if(!permissionhandler::hasPerm('MOVE_COLLECTION')){
			//unset($_REQUEST['we_' . $this->Name . '_Filename']);
			unset($_REQUEST['we_' . $this->Name . '_ParentID'], $_REQUEST['we_' . $this->Name . '_ParentPath']);
		}

		parent::i_setElementsFromHTTP();
	}

	function writeCollectionToDB($collection){// FIXME: is there a standard function called by some parent to save non-persistent data?
		$ret = $this->DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" AND (type="collection" OR type="archive")');

		$i = 0;
		foreach(($this->IsDuplicates ? $collection : array_unique($collection)) as $remObj){
			$ret &= $this->DB_WE->query('INSERT INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter([
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix(VFILE_TABLE),
					'type' => 'collection',
					'remObj' => $remObj,
					'remTable' => $this->getRemTable(),
					'position' => $i++,
			]));
		}

		return $ret;
	}

	function registerMediaLinks(array $collection = []){
		$this->MediaLinks = $collection ? : $this->getValidCollection();

		parent::registerMediaLinks(false, true);
	}

	public function addItemsToCollection($items, $pos = -1){ // FIXME: when pos=-1 do append at the end of collection not replacing last element!
		$coll = $this->getCollection(true);
		array_pop($coll); // FIXME: maybe abandon the ending -1 inserted on we_load()?

		$useEmpty = true;
		if($pos === -1){
			$pos = count($coll);
			if($useEmpty){
				// find last collection item not empty
				for($i = 0; $i < count($coll); $i++){
					if($coll[$i] != -1){
						$pos = $i;
					}
				}
				$pos++;
			}
		}
		$tmpColl = array_slice($coll, $pos);
		$newColl = array_slice($coll, 0, $pos);
		$result = [[], []];
		$isFirstSet = false;
		foreach($items as $item){
			if($this->IsDuplicates || !in_array($item, $coll)){
				$newColl[] = $result[0][] = $item;
				if(!$isFirstSet || ($useEmpty && isset($tmpColl[0]) && $tmpColl[0] == -1)){
					array_shift($tmpColl);
					$isFirstSet = true;
				}
			} else {
				$result[1][] = $item;
			}
		}
		$this->setCollection(array_merge($newColl, $tmpColl, [-1]));

		return $result;
	}

	public function getValidItemsFromIDs($IDs = [], $returnFull = false, $recursive = -1, $table = '', $recursion = 0, $foldersDone = [], $checkWs = true, $wspaces = []){
		$IDs = is_array($IDs) ? $IDs : [$IDs];
		if(empty($IDs)){
			return -1;
		}
		if($table && $table !== stripTblPrefix($this->getRemTable())){
			return -2;
		}

		$recursive = $recursive === -1 ? $this->InsertRecursive : $recursive;

		if($checkWs && (empty($wspaces))){
			if(($ws = get_ws($this->getRemTable(), true))){
				$wsPathArray = id_to_path($ws, $this->getRemTable(), $this->DB_WE, true);
				foreach($wsPathArray as $path){
					$wspaces[] = ' Path LIKE "' . $this->DB_WE->escape($path) . '/%" OR ' . we_tool_treeDataSource::getQueryParents($path);
					while($path != '/' && $path != '\\' && $path){
						$parentpaths[] = $path;
						$path = dirname($path);
					}
				}
			} elseif(defined('OBJECT_FILES_TABLE') && $this->getRemTable() === stripTblPrefix(OBJECT_FILES_TABLE) && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
				$ac = we_users_util::getAllowedClasses($this->DB_WE);
				foreach($ac as $cid){
					$path = id_to_path($cid, OBJECT_TABLE);
					$wspaces[] = ' Path LIKE "' . $this->DB_WE->escape($path) . '/%" OR Path="' . $this->DB_WE->escape($path) . '"';
				}
			}
			$wspaces = empty($wspaces) ? array(false) : $wspaces;
		}
		$wsQuery = ($checkWs && $wspaces[0] !== false ? ' AND (' . implode(' OR ', $wspaces) . ') ' : ' OR RestrictOwners=0 ' );

		$result = $resultRoot = $todo = [];

		if($this->getRemTable() === stripTblPrefix(OBJECT_FILES_TABLE)){
			$typeField = 'TableID';
			$typeProp = 'remClass';
			$whereType = $this->remClass ? 'AND TableID IN (' . trim($this->remClass, ',') . ',0) ' : '';
			$classField = ',TableID';
			$nameField = 'Text';
		} else {
			$typeField = 'ContentType';
			$typeProp = 'remCT';
			$whereType = trim($this->remCT, "',") ? 'AND ContentType IN ("' . (str_replace(',', '","', trim($this->remCT, ','))) . '","folder") ' : '';
			$classField = '';
			$nameField = 'Filename';
		}

		$resultIDsCsv = '';

		$this->DB_WE->query('SELECT ID,ParentID,Path,ContentType,Extension' . $classField . ',' . $nameField . ' FROM ' . addTblPrefix($this->getRemTable()) . ' WHERE ' . ($recursion === 0 ? 'ID' : 'ParentID') . ' IN (' . implode(',', $IDs) . ') ' . $whereType . 'AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ') ORDER BY Path ASC');
		while($this->DB_WE->next_record()){
			$data = $this->DB_WE->getRecord();
			if(($recursive || $recursion === 0) && $data['ContentType'] === 'folder' && !isset($foldersDone[$data['ID']])){
				$todo[] = $data['ID'];
				$foldersDone[] = $data['ID'];
			}

			if($data['ContentType'] !== 'folder'){
				//if((!$this->$typeProp || in_array($data[$typeField], explode(',', $this->$typeProp))) && $data['ContentType'] !== 'folder'){
				//IMI:TEST ==> get icon from some fn!
				if($data['ID'] !== -1 && $data[$typeField] === 'image/*'){
					$file = ['docID' => $data['ID'], 'Path' => $data['Path'], 'ContentType' => isset($data[$typeField]) ? $data[$typeField] : 'text/*', 'Extension' => $data['Extension']];
					$file['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) : 0;
					$file['fileSize'] = we_base_file::getHumanFileSize($file['size']);
					// FIXME: look for biggest icon size in grid view dynamically
					$iconHTML = self::getHtmlIconThmubnail($file, 400, 400);
				}
				//END
				$resultIDsCsv .= $data['ID'] . ',';
				if($data['ParentID'] == 0){
					if($returnFull){
						$resultRoot[$data['ID']] = array_merge($this->getEmptyItem(), ['id' => $data['ID'], 'path' => $data['Path'], 'ext' => $data['Extension'], 'ct' => $data[$typeField], 'name' => $data[$nameField]]);
						$resultRoot[$data['ID']]['icon'] = $iconHTML;
					} else {
						$resultRoot[] = $data['ID'];
					}
				} else {
					$result[$data['Path']] = array_merge($this->getEmptyItem(), ['id' => $data['ID'], 'path' => $data['Path'], 'ext' => $data['Extension'], 'ct' => $data[$typeField], 'name' => $data[$nameField]]);
					$result[$data['Path']]['icon'] = $iconHTML;
				}
			}
		}

		if($result && $returnFull){
			//$result = $this->setItemElements($result);
		}

		if(!empty($todo)){
			$result = array_merge($result, $this->getValidItemsFromIDs($todo, $returnFull, $recursive, '', $recursion + 1, $foldersDone, true, $wspaces));
		}

		if($recursion++ === 0){ // when finishing the initial call, sort complete result (on root level folders first, then items and set items' elements)
			ksort($result);
			$tmpResult = [];
			foreach($result as $res){
				$tmpResult[$res['id']] = $returnFull ? $res : $res['id'];
			}
			$result = $returnFull ? array_merge($this->setItemElements(($tmpResult + $resultRoot))) : $tmpResult + $resultRoot;
		}

		return $result;
	}

	public static function getHtmlIconThmubnail($file, $smallSize = 64, $bigSize = 140){ // FIXME: move this as static fn to some image utilities class (from we_search_view too)
		$urlPopup = $url = '';
		if($file["ContentType"] == we_base_ContentTypes::IMAGE){
			if($file["size"] > 0){
				$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]);
				$url = WEBEDITION_DIR . 'thumbnail.php?id=' . $file["docID"] . "&size[width]=" . $smallSize . "&path=" . urlencode($file["Path"]) . "&extension=" . $file["Extension"];
				$imageView = '<img src="' . $url . '" /></a>';

				$urlPopup = WEBEDITION_DIR . "thumbnail.php?id=" . $file["docID"] . "&size[width]=" . $bigSize . "&path=" . $file["Path"] . "&extension=" . $file["Extension"];
				$imageViewPopup = '<img src="' . $urlPopup . '" /></a>';
			} else {
				$imagesize = array(0, 0);
				$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $file["ContentType"] . '" data-extension="' . $file['Extension'] . '"></span>';
			}
		} else {
			$imagesize = array(0, 0);
			$imageView = $imageViewPopup = '<span class="resultIcon" data-contenttype="' . $file["ContentType"] . '" data-extension="' . $file['Extension'] . '"></span>';
		}

		return ['imageView' => $imageView, 'imageViewPopup' => $imageViewPopup, 'sizeX' => $imagesize[0], 'sizeY' => $imagesize[1], 'url' => $url, 'urlPopup' => $urlPopup];
	}

	private static function isDragAndDrop(){
		return !((we_base_browserDetect::isIE() && we_base_browserDetect::getIEVersion() < 10) ||
			(we_base_browserDetect::isSafari() && intval(we_base_browserDetect::getBrowserVersion()) < 7) ||
			(we_base_browserDetect::isOpera()));
	}

}
