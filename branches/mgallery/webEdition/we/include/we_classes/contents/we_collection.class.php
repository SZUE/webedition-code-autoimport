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
	public $remTable;
	public $remCT;
	public $remClass;
	public $IsDuplicates;
	public $InsertRecursive;
	protected $fileCollection = '';
	protected $objectCollection = '';
	protected $jsFormCollection = '';
	protected $insertPrefs;
	private $tmpFoldersDone = array();
	private $gridItemSize = 180;

	/** Constructor
	 * @return we_collection
	 * @desc Constructor for we_collection
	 */
	function __construct(){
		parent::__construct();
		$this->Table = VFILE_TABLE;
		array_push($this->persistent_slots, 'fileCollection', 'objectCollection', 'remTable', 'remCT', 'remClass', 'insertPrefs', 'IsDuplicates', 'InsertRecursive', 'ContentType');

		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_INFO);
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}
	}

	public function getRemTable(){
		return $this->remTable;
	}

	public function getRemCT(){
		return $this->remCT;
	}

	public function getRemClass(){
		return $this->remClass;
	}

	public function getCollection($asArray = false){
		$coll = $this->remTable === stripTblPrefix(FILE_TABLE) ? $this->fileCollection : $this->objectCollection;

		return $asArray ? explode(',', trim($coll, ',')) : $coll;
	}

	// verify collection against remTable, remCT, remClass and ID
	public function getCollectionVerified($skipEmpty = true, $full = false, $updateCollection = false){
		if($this->remTable == stripTblPrefix(FILE_TABLE)){
			$activeCollectionName = 'fileCollection';
			$fields = 'ID,Path,ContentType,Extension';
			$table = FILE_TABLE;
			$prop = 'remCT';
			$cField = 'ContentType';
		} else {
			$activeCollectionName = 'objectCollection';
			$fields = 'ID,Path,TableID';
			$table = OBJECT_FILES_TABLE;
			$prop = 'remClass';
			$cField = 'TableID';
		}
		$this->$activeCollectionName = !trim($this->$activeCollectionName, ',') ? ',-1,' : $this->$activeCollectionName;

		$this->DB_WE->query('SELECT ' . $fields . ' FROM ' . $table . ' WHERE ID IN (' . trim($this->$activeCollectionName, ',') . ') AND NOT IsFolder');
		$verifiedItems = array();
		while($this->DB_WE->next_record()){
			if(!trim($this->$prop, ',') || in_array($this->DB_WE->f($cField), makeArrayFromCSV($this->$prop))){
				$verifiedItems[$this->DB_WE->f('ID')] = $full ? array('id' => $this->DB_WE->f('ID'), 'path' => $this->DB_WE->f('Path'), 'type' => $this->DB_WE->f($cField)) : $this->DB_WE->f('ID');
				if($full && $this->remTable == stripTblPrefix(FILE_TABLE)){
					$verifiedItems[$this->DB_WE->f('ID')]['ext'] = $this->DB_WE->f('Extension');
				}
			}
		}

		$ret = array();
		$tempCollection = ',';
		$emptyItem = array('id' => -1, 'path' => '', 'type' => '');

		$ac = explode(',', trim($this->$activeCollectionName, ','));
		$ac = $this->IsDuplicates ? $ac : array_unique($ac);
		foreach($ac as $id){
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
		$collectionName = $this->remTable === stripTblPrefix(FILE_TABLE) ? 'fileCollection' : 'objectCollection';
		$this->$collectionName = ',' . implode(',', $coll) . ',';
	}

	//FIXME: maybe add column Filename to db to avoid setting Text = Filename and Filename = Text when initializing or saving we_doc!
	public function getText(){
		return $this->Filename;
	}

	public function getEditorBodyAttributes($editor = 0){
		switch($editor){
			case self::EDITOR_HEADER:
				return ' ondragenter="we_editor_header.dragEnter();" ondragleave="we_editor_header.dragLeave();"';
			case self::EDITOR_FOOTER:
				return ' ondragenter="we_editor_footer.dragEnter();" ondragleave="we_editor_footer.dragLeave();"';
			default:
				return '';
		}

	}

	public function initByID($ID){
		parent::initByID($ID, VFILE_TABLE);
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return 'we_editors/we_editor_content_collection.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_editors/we_editor_info.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return 'we_editors/we_editor_properties.inc.php';
		}
	}

	public function getPropertyPage(){
		echo we_html_multiIconBox::getJS() .
		we_html_element::jsScript(JS_DIR . 'we_editor_collectionContent.js') .
		we_html_multiIconBox::getHTML('weOtherDocProp', '100%', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'cache.gif', 'headline' => 'Inhalt', 'html' => $this->formContent(), 'space' => 140),
			array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => 140))
			, 20);
	}

	function formContent($fixedRemTable = false){
		$valsRemTable = array(
			'tblFile' => g_l('navigation', '[documents]')
		);
		if(defined('OBJECT_TABLE')){
			$valsRemTable['tblObjectFiles'] = g_l('navigation', '[objects]');
		}

		$allMime = we_base_ContentTypes::inst()->getContentTypes(FILE_TABLE, true);
		$remCtArr = makeArrayFromCSV($this->remCT);
		$tmpRemCT = ',';
		$selectedMime = $unselectedMime = array();
		foreach($allMime as $mime){
			if(in_array($mime, $remCtArr)){
				$selectedMime[$mime] = g_l('contentTypes', '[' . $mime . ']');
				$tmpRemCT .= $mime . ',';
			} else {
				$unselectedMime[$mime] = g_l('contentTypes', '[' . $mime . ']');
			}
		}
		$this->remCT = $tmpRemCT;
		$mimeListFrom = we_html_tools::htmlSelect(
				'mimeListFrom', $unselectedMime, 13, '', true, array("id" => "mimeListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListFrom'],this.form['mimeListTo'],true, 'document');"), 'value', 184
		);
		$mimeListTo = we_html_tools::htmlSelect(
				'mimeListTo', $selectedMime, 13, '', true, array("id" => "mimeListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListTo'],this.form['mimeListFrom'],true, 'document');"), 'value', 184
		);
		$mimeTable = new we_html_table(array("border" => 0, "width" => 388, "cellpadding" => 0, "cellspacing" => 0, "style" => "margin-top:10px"), 1, 3);
		$mimeTable->setCol(0, 0, null, $mimeListFrom);
		$mimeTable->setCol(
			0, 1, array(
			"align" => "center", "valign" => "middle"
			), we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('mimeListFrom'),document.getElementById('mimeListTo'),true, 'document');return false;"
				), we_html_element::htmlImg(array(
					"src" => IMAGE_DIR . "pd/arrow_right.gif", "border" => 0
			))) . we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('mimeListTo'),document.getElementById('mimeListFrom'),true, 'document');return false;"
				), we_html_element::htmlImg(array(
					"src" => IMAGE_DIR . "pd/arrow_left.gif", "border" => 0
		))));
		$mimeTable->setCol(0, 2, null, $mimeListTo);

		$selectedClasses = array();
		$unselectedClasses = array();
		$allClasses = array();
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
		$classListFrom = we_html_tools::htmlSelect(
				'classListFrom', $unselectedClasses, max(count($allClasses), 5), '', true, array("id" => "classListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListFrom'],this.form['classListTo'],true, 'object');"), 'value', 184
		);
		$classListTo = we_html_tools::htmlSelect(
				'classListTo', $selectedClasses, max(count($allClasses), 5), '', true, array("id" => "classListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListTo'],this.form['classListFrom'],true, 'object');"), 'value', 184
		);

		$classTable = new we_html_table(array("border" => 0, "width" => 388, "cellpadding" => 0, "cellspacing" => 0, "style" => "margin-top:10px"), 1, 3);
		$classTable->setCol(0, 0, null, $classListFrom);
		$classTable->setCol(
			0, 1, array(
			"align" => "center", "valign" => "middle"
			), we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('classListFrom'),document.getElementById('classListTo'),true, 'object');return false;"
				), we_html_element::htmlImg(array(
					"src" => IMAGE_DIR . "pd/arrow_right.gif", "border" => 0
			))) . we_html_element::htmlBr() . we_html_element::htmlBr() .
			we_html_element::htmlA(array(
				"href" => "#",
				"onclick" => "wePropertiesEdit.moveSelectedOptions(document.getElementById('classListTo'),document.getElementById('classListFrom'),true, 'object');return false;"
				), we_html_element::htmlImg(array(
					"src" => IMAGE_DIR . "pd/arrow_left.gif", "border" => 0
		))));
		$classTable->setCol(0, 2, null, $classListTo);

		$selRemTable = $fixedRemTable && $this->remTable ? we_html_element::htmlHidden('we_' . $this->Name . '_remTable', $this->remTable) . we_html_element::htmlInput(array('disabled' => 1, 'name' => 'disabledField', 'value' => $valsRemTable[$this->remTable], 'width' => 382)) :
			we_html_tools::htmlSelect('we_' . $this->Name . '_remTable', $valsRemTable, 1, $this->remTable, false, array('onchange' => 'document.getElementById(\'mimetype\').style.display=(this.value===\'tblFile\'?\'block\':\'none\');document.getElementById(\'classname\').style.display=(this.value===\'tblFile\'?\'none\':\'block\');', 'style' => 'width: 388px; margin-top: 5px;'), 'value', 388);


		$dublettes = we_html_forms::checkboxWithHidden($this->IsDuplicates, 'we_' . $this->Name . '_IsDuplicates', 'Dubletten sind erlaubt');

		$html = $selRemTable .
			'<div id="mimetype" style="' . ($this->remTable === 'tblObjectFiles' ? 'display:none' : 'display:block') . '; width:388px;margin-top:5px;">' .
			'<br/>Erlaubte Dokumente auf folgende Typen einschränken:<br>' .
			we_html_element::htmlHidden('we_' . $this->Name . '_remCT', $this->remCT, 'we_remCT') .
			$mimeTable->getHTML() .
			'</div>
		<div id="classname" style="' . ($this->remTable === 'tblObjectFiles' ? 'display:block' : 'display:none') . '; width: 380px;margin-top:5px;">' .
			(defined('OBJECT_TABLE') ? '<br/>Erlaubte Objekte auf folgende Klassen einschränken:<br/>' .
				we_html_element::htmlHidden('we_' . $this->Name . '_remClass', $this->remClass, 'we_remClass') .
				$classTable->getHTML() : '') .
			'</div>' .
			we_html_element::htmlDiv(array('style' => 'width:388px;margin:20px 0 10px 0;'), $dublettes);

		return $html;
	}

	function formCollection(){
		$recursive = we_html_forms::checkboxWithHidden($this->InsertRecursive, 'we_' . $GLOBALS['we_doc']->Name . '_InsertRecursive', 'Verzeichnisse rekursiv einfügen') .
			we_html_element::htmlHidden('check_we_' . $GLOBALS['we_doc']->Name . '_IsDuplicates', $this->IsDuplicates);

		$items = $this->getCollectionVerified(false, true, true);
		if($items[count($items) - 1]['id'] !== -1){
			$items[] = array('id' => -1, 'path' => '', 'type' => '');
		}

		$yuiSuggest = &weSuggest::getInstance();
		$index = 0;
		$rows = $divs = '';
		foreach($items as $item){
			$index++;
			$rows .= $this->getListItem($item, $index, $yuiSuggest, count($items), true);
			$divs .= $this->getGridItem($item, $index, count($items));
		}

		// write "blank" collection row to js
		$this->jsFormCollection .= "
weCollectionEdit.gridItemSize = " . $this->gridItemSize . ";
weCollectionEdit.maxIndex = " . count($items) . ";
weCollectionEdit.blankListItem = '" . str_replace(array("'"), "\'", str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $this->getListItem(array("id" => -1, "path" => '/'), 'XX', $yuiSuggest, 1, true, true))) . "';
weCollectionEdit.blankGridItem = '" . str_replace(array("'"), "\'", str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $this->getGridItem(array("id" => '##ID##', "path" => '/'), '##INDEX##'))) . "';";

		return we_html_element::jsElement($this->jsFormCollection) .
			we_html_element::htmlHiddens(array(
				'we_' . $this->Name . '_fileCollection' => $this->fileCollection,
				'we_' . $this->Name . '_objectCollection' => $this->objectCollection)) .
			we_html_element::htmlDiv(array('class' => 'weMultiIconBoxHeadline', 'style' => 'width:806px;margin:20px 0 0 20px;'), 'Inhalt der Sammlung') .
			we_html_element::htmlDiv(array('class' => 'weMultiIconBoxHeadline', 'style' => 'width:806px;margin:20px 0 0 20px;color:red;font-size:20px'), 'Hinweis: Die Sammlungen sind z.Zt. komplett unbenutzbar<br>(auch nicht zu Testzwecken)!') .
			we_html_element::htmlDiv(array('class' => '', 'style' => 'width:806px;margin:20px 0 0 20px;'), we_html_tools::htmlAlertAttentionBox('Ausführlich zu Drag&Drop, Seletoren etc (zum Aufklappen)', we_html_tools::TYPE_INFO, 680)) .
			we_html_element::htmlDiv(array('style' => 'width:806px;padding:10px 0 0 20px;margin-left:20px;'), $recursive) .
			we_html_element::htmlDiv(array('id' => 'content_table_list', 'class' => 'content_table', 'style' => 'width:806px;border:1px solid #afb0af;padding:20px;margin:20px;background-color:white;min-height:200px;display:block'), $rows) .

			// TODO: make "header" width range and toggle view
			we_html_element::htmlDiv(array('style' => 'padding:30px 0 0 30px;'), '<input type="range" style="width:120px;height:20px;" name="zoom" min="120" step="20" max="240" value="' . $this->gridItemSize . '" onchange="weCollectionEdit.doZoomGrid(this.value);"/>') .

			we_html_element::htmlDiv(array('id' => 'content_table_grid', 'class' => 'content_table', 'style' => 'width:806px;border:1px solid #afb0af;padding:20px;margin:20px 0 0 20px;background-color:white;display:inline-block;min-height:200px'), $divs);
	}

	function getListItem($item, $index, &$yuiSuggest, $itemsNum = 0, $noAcAutoInit = false, $noSelectorAutoInit = false){
		$textname = 'we_' . $this->Name . '_ItemName_' . $index;
		$idname = 'we_' . $this->Name . '_ItemID_' . $index;

		$wecmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmd2 = "document.we_form.elements['" . $textname . "'].value";
		$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);opener.weCollectionEdit.repaintAndRetrieveCsv();";

		if($noSelectorAutoInit){
			$this->jsFormCollection .= 'weCollectionEdit.selectorCmds = ["' . $wecmd1 . '","' . $wecmd2 . '"];';
			$wecmdenc1 = 'CMD1';
			$wecmdenc2 = 'CMD2';
		} else {
			$wecmdenc1 = we_base_request::encCmd($wecmd1);
			$wecmdenc2 = we_base_request::encCmd($wecmd2);
		}
		$wecmdenc3 = we_base_request::encCmd($wecmd3);

		$button = we_html_button::create_button("fa:btn_add_file,fa-plus,fa-lg fa-file-o", "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . addTblPrefix($this->remTable) . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . trim($this->remCT, ',') . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")", true, 44, 0, '', '', false, false, '_' . $index);
		$addFromTreeButton = we_html_button::create_button("fa:btn_import_files,fa-lg fa-upload", "javascript:weCollectionEdit.doClickAddItems(this);", true, 44, 22);
		$openbutton = we_html_button::create_button("fa:btn_edit_edit,fa-lg fa-pencil", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . (addTblPrefix($this->remTable) === FILE_TABLE ? we_base_ContentTypes::TEMPLATE : we_base_ContentTypes::OBJECT_FILE) . "','" . addTblPrefix($this->remTable) . "'); }", true, 27, 22, '', '', $item['id'] === -1, false, '_' . $index);
		//$trashButton = we_html_button::create_button("fa:btn_function_trash,fa-lg fa-trash-o", "javascript:document.we_form.elements['" . $idname . "'].value='-1';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputItem_" . $index . "');_EditorFrame.setEditorIsHot(true);weCollectionEdit.repaintAndRetrieveCsv();", true, 27, 22, '', '', $item['id'] === -1, false, '_' . $index);
		$yuiSuggest->setTable(addTblPrefix($this->remTable));
		$yuiSuggest->setContentType('folder,' . trim($this->remCT, ','));
		$yuiSuggest->setCheckFieldValue(false);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setAcId('Item_' . $index);
		$yuiSuggest->setNoAutoInit($noAcAutoInit);
		$yuiSuggest->setInput($textname, $item['path'], array("onmouseover" => "document.getElementById('list_item_" . $index . "').draggable=false", "onmouseout" => "document.getElementById('list_item_" . $index . "').draggable=true"));
		$yuiSuggest->setResult($idname, $item['id']);
		$yuiSuggest->setWidth(240);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setSelectButton($button, 4);
		$yuiSuggest->setAdditionalButton($addFromTreeButton, 6);
		$yuiSuggest->setOpenButton($openbutton, 4);
		$yuiSuggest->setDoOnItemSelect("weCollectionEdit.repaintAndRetrieveCsv();");

		$rowControllsArr = array();
		$rowControllsArr[] = we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:_EditorFrame.setEditorIsHot(true);weCollectionEdit.doClickAdd(this);//top.we_cmd('switch_edit_page',1,we_transaction);", true, 100, 22);
		$rowControllsArr[] = we_html_tools::htmlSelect('numselect_' . $index, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 1, '', false, array('id' => 'numselect_' . $index));
		$rowControllsArr[] = we_html_button::create_button('fa:btn_direction_up,fa-lg fa-caret-up', 'javascript:weCollectionEdit.doClickUp(this);', true, 0, 0, '', '', ($index === 1 ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('fa:btn_direction_down,fa-lg fa-caret-down', 'javascript:weCollectionEdit.doClickDown(this);', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('fa:btn_function_trash,fa-lg fa-trash-o', 'javascript:weCollectionEdit.doClickDelete(this)', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);

		$rowControlls = we_html_button::create_button_table($rowControllsArr, 5);

		//FIXME: use we_html_table
		$rowHtml = '<table cellspacing="0" draggable="false">
				<tr style="background-color:#f5f5f5;cursor:move;" height="34px">
					<td width="70px" style="padding:0 0 0 20px;" class="weMultiIconBoxHeadline">Nr. <span id="label_' . $index . '">' . $index . '</span></td>
					<td width="220px" style="padding:4px 40px 0 0;">' . $yuiSuggest->getHTML() . '</td>
					<td width="" style="padding:4px 40px 0 20px;">' . $rowControlls . '</td>
				</tr>
			</table>';

		return we_html_element::htmlDiv(array(
				'style' => 'margin-top:4px;border:1px solid #006db8;background-color:#f5f5f5;cursor:move;',
				'id' => 'list_item_' . $index,
				'class' => 'drop_reference',
				'draggable' => 'true',
				'ondragstart' => 'weCollectionEdit.startMoveItem(event, \'list\')',
				'ondrop' => 'weCollectionEdit.dropOnItem(\'item\',\'list\',event, this)',
				'ondragover' => 'weCollectionEdit.allowDrop(event)',
				'ondragenter' => 'weCollectionEdit.enterDrag(\'item\',\'list\',event, this)',
				'ondragend' => 'weCollectionEdit.dragEnd(event)'
				), $rowHtml);
	}

	function getGridItem($item, $index){
		$iconHTML = $item['id'] === '##ID##' ? array('url' => '##URL##') : array();
		if(is_numeric($item['id']) && $item['id'] !== -1){
			$file = array('docID' => $item['id'], 'Path' => $item['path'], 'ContentType' => isset($item['type']) ? $item['type'] : 'text/*', 'Extension' => $item['ext']);
			$file['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) : 0;
			$file['fileSize'] = we_base_file::getHumanFileSize($file['size']);
			$iconHTML = we_search_view::getHtmlIconThmubnail($file, 200, 200);
		}
		$trashButton = we_html_button::create_button("fa:btn_function_trash,fa-lg fa-trash-o", "javascript:weCollectionEdit.deleteItem('grid', this);", true, 27, 22);

		return we_html_element::htmlDiv(array(
			'style' => 'position:relative;width:' . $this->gridItemSize . 'px;height:' . $this->gridItemSize . 'px;float:left;dislpay:block;',
			'id' => 'grid_item_' . $index,
			'class' => 'drop_reference'
			), we_html_element::htmlDiv(array(
					'style' => 'position:absolute;left:0;top:0;bottom:14px;right:14px;border:1px solid #006db8;float:left;dislpay:block;' . ($iconHTML ? "background:url('" . $iconHTML['url'] . "') no-repeat center center;background-size:contain" : 'background-color:white'),
					'draggable' => 'true',
				), we_html_element::htmlDiv(array(
					'style' => 'position:absolute;bottom:0;width:100%;height:30px;text-align:right;padding-top:6px;background-color:#f5f5f5;opacity:0.6;display:none;',
					), $trashButton)) .
				we_html_element::htmlDiv(array(
					'style' => 'position:absolute;top:0;right:0;bottom:14px;width:12px;border:1px solid white;float:left;dislpay:block;',
					'id' => 'grid_space_' . $index,
				), '') . we_html_element::htmlHidden('collectionItem_we_id', $item['id'])
			);
	}

	function i_filenameDouble(){
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->ParentID) . " AND Text='" . escape_sql_query($this->Text) . "' AND ID != " . intval($this->ID), "", $this->DB_WE);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		//FIXME: remove this switch after 6.6
		$this->ContentType = $this->ContentType ? : ($this->IsFolder ? we_base_ContentTypes::FOLDER : we_base_ContentTypes::COLLECTION);
		$this->Filename = $this->Text;
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';

		if(!$skipHook){// TODO: integrate hooks?
		}

		$collection = $this->getCollectionVerified();
		$ret = parent::we_save($resave) && $this->writeCollectionToDB($collection) && !$this->errMsg;

		if($ret){
			$this->unregisterMediaLinks();
			$this->registerMediaLinks($collection);
		}

		return $ret;
	}

	protected function i_getContentData(){
		//!! parent::i_getContentData();

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

	function writeCollectionToDB($collection){// FIXME: is there a standard function called by some parent to save non-persistent data?
		$ret = $this->DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" AND (type="collection" OR type="archive")');

		$i = 0;
		foreach(($this->IsDuplicates ? $collection : array_unique($collection)) as $remObj){
			$ret &= $this->DB_WE->query('INSERT INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix(VFILE_TABLE),
					'type' => 'collection',
					'remObj' => $remObj,
					'remTable' => $this->remTable,
					'position' => $i++,
			)));
		}

		return $ret;
	}

	function registerMediaLinks($collection = array()){
		$this->MediaLinks = $collection ? : $this->getCollectionVerified();

		parent::registerMediaLinks(false, true);
	}

	public function addItemsToCollection($items, $pos = -1){ // FIXME: when pos=-1 do append at the end od collection not replacing last element!
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
		$this->setCollection(array_merge($newColl, $tmpColl, array(-1)));

		return $result;
	}

	public function getVerifiedRemObjectsFromIDs($IDs = array(), $returnFull = false, $recursive = -1, $table = '', $recursion = 0, $foldersDone = array(), $checkWs = true, $wspaces = array()){
		$IDs = is_array($IDs) ? $IDs : array($IDs);
		if(empty($IDs)){
			return -1;
		}
		if($table && $table !== stripTblPrefix($this->remTable)){
			return -2;
		}

		$recursive = $recursive === -1 ? $this->InsertRecursive : $recursive;

		if($checkWs && (empty($wspaces))){
			if(($ws = get_ws($this->remTable))){
				$wsPathArray = id_to_path($ws, $this->remTable, $this->DB_WE, false, true);
				foreach($wsPathArray as $path){
					$wspaces[] = " Path LIKE '" . $this->DB_WE->escape($path) . "/%' OR " . getQueryParents($path);
					while($path != '/' && $path != '\\' && $path){
						$parentpaths[] = $path;
						$path = dirname($path);
					}
				}
			} elseif(defined('OBJECT_FILES_TABLE') && $this->remTable == OBJECT_FILES_TABLE && (!permissionhandler::hasPerm("ADMINISTRATOR"))){
				$ac = we_users_util::getAllowedClasses($this->DB_WE);
				foreach($ac as $cid){
					$path = id_to_path($cid, OBJECT_TABLE);
					$wspaces[] = " Path LIKE '" . $this->DB_WE->escape($path) . "/%' OR Path='" . $this->DB_WE->escape($path) . "'";
				}
			}
			$wspaces = empty($wspaces) ? array(false) : $wspaces;
		}
		$wsQuery = ($checkWs && $wspaces[0] !== false ? ' AND (' . implode(' OR ', $wspaces) . ') ' : ' OR RestrictOwners=0 ' );

		$result = $resultRoot = $todo = array();

		if($this->remTable === stripTblPrefix(OBJECT_FILES_TABLE)){
			$typeField = 'TableID';
			$typeProp = 'remClass';
			$whereType = $this->remClass ? 'AND TableID IN (' . trim($this->remClass, ',') . ',0) ' : '';
			$classField = ',TableID';
		} else {
			$typeField = 'ContentType';
			$typeProp = 'remCT';
			$whereType = trim($this->remCT, "',") ? 'AND ContentType IN ("' . (str_replace(',', '","', trim($this->remCT, ','))) . '","folder") ' : '';
			$classField = '';
		}

		$this->DB_WE->query('SELECT ID,ParentID,Path,ContentType,Extension' . $classField . ' FROM ' . addTblPrefix($this->remTable) . ' WHERE ' . ($recursion === 0 ? 'ID' : 'ParentID') . ' IN (' . implode(',', $IDs) . ') ' . $whereType . 'AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ') ORDER BY Path ASC');
		while($this->DB_WE->next_record()){
			$data = $this->DB_WE->getRecord();
			if(($recursive || $recursion === 0) && $data['ContentType'] === 'folder' && !isset($foldersDone[$data['ID']])){
				$todo[] = $data['ID'];
				$foldersDone[] = $data['ID'];
			}
			if($data['ContentType'] !== 'folder'){
				//if((!$this->$typeProp || in_array($data[$typeField], explode(',', $this->$typeProp))) && $data['ContentType'] !== 'folder'){

				//IMI:TEST
				if($data['ID'] !== -1){
					$file = array('docID' => $data['ID'], 'Path' => $data['Path'], 'ContentType' => isset($data[$typeField]) ? $data[$typeField] : 'text/*', 'Extension' => $data['Extension']);
					$file['size'] = file_exists($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $file["Path"]) : 0;
					$file['fileSize'] = we_base_file::getHumanFileSize($file['size']);
					$iconHTML = we_search_view::getHtmlIconThmubnail($file, 200, 200);
				}
				//END
				if($data['ParentID'] == 0){
					$resultRoot[$data['ID']] = $returnFull ? array('id' => $data['ID'], 'path' => $data['Path'], 'ct' => $data[$typeField], 'iconSrc' => $iconHTML['url']) : $data['ID'];
				} else {
					$result[$data['Path']] = array('id' => $data['ID'], 'path' => $data['Path'], 'ct' => $data[$typeField], 'iconSrc' => $iconHTML['url']);
				}
			}
		}

		if(!empty($todo)){
			$result = array_merge($result, $this->getVerifiedRemObjectsFromIDs($todo, $returnFull, $recursive, '', $recursion + 1, $foldersDone, true, $wspaces));
		}

		if($recursion++ === 0){ // when finishing the initial call, sort complete result (on root level folders first, then items)
			ksort($result);
			$tmpResult = array();
			foreach($result as $res){
				$tmpResult[$res['id']] = $returnFull ? $res : $res['id'];
			}
			$result = array_merge($tmpResult, $resultRoot);
		}

		return $result;
	}

}
