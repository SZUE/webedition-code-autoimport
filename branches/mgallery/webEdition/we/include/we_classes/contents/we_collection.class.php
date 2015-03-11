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
	protected $fileCollection = '';
	protected $objectCollection = '';

	protected $remTable;
	protected $remCT;
	protected $remClass;
	protected $jsFormCollection = '';

	protected $insertPrefs;
	protected $insertRecursive;
	protected $useEmpty;
	protected $doubleOk;
	

	private $tmpFoldersDone = array();

	/** Constructor
	 * @return we_collection
	 * @desc Constructor for we_collection
	 */
	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'fileCollection', 'objectCollection', 'remTable', 'remCT', 'remClass', 'insertPrefs', 'insertRecursive', 'useEmpty', 'doubleOk');

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
			$fields = 'ID,Path,ContentType';
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
			}
		}

		$ret = array();
		$tempCollection = ',';
		$emptyItem = array('id' => -1, 'path' => '', 'type' => '');
		foreach(explode(',', trim($this->$activeCollectionName, ',')) as $id){
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

	function formContent(){
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
			'mimeListFrom',
			$unselectedMime,
			13,
			'',
			true,
			array("id" => "mimeListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListFrom'],this.form['mimeListTo'],true, 'document');"),
			'value',
			184
			);
		$mimeListTo = we_html_tools::htmlSelect(
			'mimeListTo',
			$selectedMime,
			13,
			'',
			true,
			array("id" => "mimeListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['mimeListTo'],this.form['mimeListFrom'],true, 'document');"),
			'value',
			184
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
			'classListFrom',
			$unselectedClasses,
			max(count($allClasses), 5),
			'',
			true,
			array("id" => "classListFrom", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListFrom'],this.form['classListTo'],true, 'object');"),
			'value',
			184
			);
		$classListTo = we_html_tools::htmlSelect(
			'classListTo',
			$selectedClasses,
			max(count($allClasses), 5),
			'',
			true,
			array("id" => "classListTo", "onDblClick" => "wePropertiesEdit.moveSelectedOptions(this.form['classListTo'],this.form['classListFrom'],true, 'object');"),
			'value',
			184
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

		$html = we_html_tools::htmlSelect('we_' . $this->Name . '_remTable', $valsRemTable, 1, $this->remTable, false, array('onchange' => 'document.getElementById(\'mimetype\').style.display=(this.value===\'tblFile\'?\'block\':\'none\');document.getElementById(\'classname\').style.display=(this.value===\'tblFile\'?\'none\':\'block\');', 'style' => 'width: 388px; margin-top: 5px;'), 'value', 388) .
		'<div id="mimetype" style="' . ($this->remTable === 'tblObjectFiles' ? 'display:none' : 'display:block') . '; width:388px;margin-top:5px;">' .
			'<br/>Erlaubte Dokumente auf folgende Typen einschränken:<br>' .
			we_html_element::htmlHidden(array('id' => 'we_remCT', 'name' => 'we_' . $this->Name . '_remCT', 'value' => $this->remCT)) .
			$mimeTable->getHTML() .
		'</div>
		<div id="classname" style="' . ($this->remTable === 'tblObjectFiles' ? 'display:block' : 'display:none') . '; width: 380px;margin-top:5px;">' .
			(defined('OBJECT_TABLE') ? '<br/>Erlaubte Objekte auf folgende Klassen einschränken:<br/>' . 
				we_html_element::htmlHidden(array('id' => 'we_remClass', 'name' => 'we_' . $this->Name . '_remClass', 'value' => $this->remClass)) .
				$classTable->getHTML() : '') .
		'</div>';

		return $html;
	}

	function formCollection(){
		$checkboxes = we_html_forms::checkboxWithHidden($this->insertRecursive, 'we_' . $GLOBALS['we_doc']->Name . '_insertRecursive', 'Verzeichnisse rekursiv einfügen') .
			we_html_forms::checkboxWithHidden($this->useEmpty, 'we_' . $GLOBALS['we_doc']->Name . '_useEmpty', 'Leere Felder im Anschluss an die Einfügeposition auffüllen') .
			we_html_forms::checkboxWithHidden($this->doubleOk, 'we_' . $GLOBALS['we_doc']->Name . '_doubleOk', 'Doubletten zulassen');

		$items = $this->getCollectionVerified(false, true, true);
		if($items[count($items) - 1]['id'] !== -1){
			$items[] = array('id' => -1, 'path' => '', 'type' => '');
		}

		$yuiSuggest = &weSuggest::getInstance();
		$index = 0;
		$rows = '';
		foreach($items as $item){
			$index++;
			$rows .= $this->getCollectionRow($item, $index, $yuiSuggest, count($items), true);
		}

		// write "blank" collection row to js var
		$this->jsFormCollection .= "
weCollectionEdit.maxIndex = " . count($items) . "; 
weCollectionEdit.blankRow = '" . str_replace(array("'"), "\'", str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $this->getCollectionRow(array("id" => -1, "path" => '/'), 'XX', $yuiSuggest, 1, true, true))) . "';";

		return we_html_element::jsElement($this->jsFormCollection) .
			we_html_element::htmlDiv(array('class' => 'weMultiIconBoxHeadline', 'style' => 'width:806px;margin:20px 0 0 20px;'), 'Einfügen ganzer Verzeichnisse mit Drag and Drop:') .
			we_html_element::htmlDiv(array('style' => 'width:806px;padding:10px 0 0 20px;margin-left:20px;'), $checkboxes) .
			we_html_element::htmlDiv(array('style' => 'width:806px;margin:20px 0 0 20px;padding:10px;'), 
					we_html_element::htmlDiv(array(), $GLOBALS['we_doc']->formInputField('', 'fileCollection', 'fileCollection', 40, 410, '')) .
					we_html_element::htmlDiv(array(), $GLOBALS['we_doc']->formInputField('', 'objectCollection', 'objectCollection', 40, 410, ''))) .
			we_html_element::htmlDiv(array('id' => 'content_table', 'style' => 'width:806px;border:1px solid #afb0af;padding:20px;margin:20px;background-color:white;min-height:200px'), $rows);
	}

	function getCollectionRow($item, $index, &$yuiSuggest, $itemsNum = 0, $noAcAutoInit = false, $noSelectorAutoInit = false){
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

		$button = we_html_button::create_button("image:add_file", "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . addTblPrefix($this->remTable) . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . trim($this->remCT, ',') . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")", true, 44, 0, '', '', false, false, '_' . $index);
		$addFromTreeButton = we_html_button::create_button("image:import_files", "javascript:weCollectionEdit.doClickAddItems(this);", true, 44, 22);
		$openbutton = we_html_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . (addTblPrefix($this->remTable) === FILE_TABLE ? we_base_ContentTypes::TEMPLATE : we_base_ContentTypes::OBJECT_FILE) . "','" . addTblPrefix($this->remTable) . "'); }");
		$trashButton = we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $idname . "'].value='-1';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputItem_" . $index ."');_EditorFrame.setEditorIsHot(true);weCollectionEdit.repaintAndRetrieveCsv();", true, 27, 22);
		$yuiSuggest->setTable(addTblPrefix($this->remTable));
		$yuiSuggest->setContentType('folder,' . trim($this->remCT, ','));
		$yuiSuggest->setCheckFieldValue(false);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setAcId('Item_' . $index);
		$yuiSuggest->setNoAutoInit($noAcAutoInit);
		$yuiSuggest->setInput($textname, $item['path'], array("onmouseover" => "document.getElementById('drag_" . $index . "').draggable=false", "onmouseout" => "document.getElementById('drag_" . $index . "').draggable=true"));
		$yuiSuggest->setResult($idname, $item['id']);
		$yuiSuggest->setWidth(240);
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setTrashButton($trashButton, 4);
		$yuiSuggest->setSelectButton($button, 4);
		$yuiSuggest->setAdditionalButton($addFromTreeButton, 6);
		$yuiSuggest->setOpenButton($openbutton, 4);
		$yuiSuggest->setDoOnItemSelect("weCollectionEdit.repaintAndRetrieveCsv();");

		$rowControllsArr = array();
		$rowControllsArr[] = we_html_button::create_button('image:btn_add_listelement', "javascript:_EditorFrame.setEditorIsHot(true);weCollectionEdit.doClickAdd(this);//top.we_cmd('switch_edit_page',1,we_transaction);", true, 100, 22);
		$rowControllsArr[] = we_html_tools::htmlSelect('numselect_' . $index, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 1, '', false, array('id' => 'numselect_' . $index));
		$rowControllsArr[] = we_html_button::create_button('image:btn_direction_up', 'javascript:weCollectionEdit.doClickUp(this);', true, 0, 0, '', '', ($index === 1 ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('image:btn_direction_down', 'javascript:weCollectionEdit.doClickDown(this);', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('image:btn_function_trash', 'javascript:weCollectionEdit.doClickDelete(this)');

		$rowControlls =  we_html_button::create_button_table($rowControllsArr, 5);

		//FIXME: use we_html_table
		$rowHtml = '<table cellspacing="0" draggable="false">
				<tr style="background-color:#f5f5f5;" height="34px">
					<td width="60px" style="padding:0 0 0 20px;" class="weMultiIconBoxHeadline">Nr. <span id="label_' . $index . '">' . $index . '</span></td>
					<td width="200px" style="padding:4px 40px 0 0;">' . $yuiSuggest->getHTML() . '</td>
					<td width="" style="padding:4px 40px 0 0;">' . $rowControlls . '</td>
				</tr>
			</table>';

		return we_html_element::htmlDiv(array(
				'style' => 'margin-top:4px;border:1px solid #006db8', 
				'id' => 'drag_' . $index, 
				'class' => 'drop_reference', 
				'draggable' => 'true', 
				'ondragstart' => 'weCollectionEdit.startDragRow(event)', 
				'ondrop' => 'weCollectionEdit.dropOnRow(event)', 
				'ondragover' => 'weCollectionEdit.allowDrop(event)', 
				'ondragenter' => 'weCollectionEdit.enterDrag(event)',
			), $rowHtml);
	}

	function i_filenameDouble(){//
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->Text) . " AND Text='" . escape_sql_query($this->Filename) . "' AND ID != " . intval($this->ID), "", $this->DB_WE);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		$this->ContentType = $this->IsFolder ? 'folder' : we_base_ContentTypes::COLLECTION;
		$this->Filename = $this->Text;
		$this->insertRecursive = $this->insertPrefs[0];
		$this->useEmpty = $this->insertPrefs[1];
		$this->doubleOk = $this->insertPrefs[2];
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';

		if(!$skipHook){// TODO: integrate hooks?

		}

		$this->insertPrefs = strval($this->insertRecursive . $this->useEmpty . $this->doubleOk);

		if(!parent::we_save($resave)){
			return false;
		}

		$ret = $this->i_writeFileLinks();// FIXME: is there a standard function called by some parent to save non-persistent data?
		if(!$ret || ($this->errMsg != '')){
			return false;
		}

		return $ret;
	}

	protected function i_getContentData(){
		//!! parent::i_getContentData();

		$this->DB_WE->query('SELECT remObj,remTable FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '" ORDER BY position ASC');

		$this->fileCollection = ',';
		$this->ObjectCollection = ',';
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

	function i_writeFileLinks(){
		$ret = $this->DB_WE->query('DELETE FROM ' . FILELINK_TABLE . ' WHERE ID=' . intval($this->ID) . ' AND DocumentTable="' . stripTblPrefix(VFILE_TABLE) . '"');

		$i = 0;
		foreach($this->getCollectionVerified() as $remObj){
			$ret &= $this->DB_WE->query('INSERT INTO ' . FILELINK_TABLE . ' SET ' . we_database_base::arraySetter(array(
					'ID' => $this->ID,
					'DocumentTable' => stripTblPrefix(VFILE_TABLE),
					'type' => 'archive',
					'remObj' => $remObj,
					'remTable' => $this->remTable,
					'position' => $i++,
			)));
		}

		return $ret;
	}

	public function addItemsToCollection($items){
		$coll = $this->getCollection(true);
		array_pop($coll);// FIXME: maybe abandon the ending -1 inserted on we_load()?

		$last = count($coll) - 1;
		if($this->useEmpty){
			// find last collection item not empty
			for($i = 0; $i < count($coll); $i++){
				if($coll[$i] != -1){
					$last = $i;
				}
			}
		}

		$result = array(array(), array());
		foreach($items as $item){
			if($this->doubleOk || !in_array($item, $coll)){
				$coll[++$last] = $result[0][] = $item;
			} else {
				$result[1][] = $item;
			}
		}

		$coll[] = -1;
		$this->setCollection($coll);

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

		$recursive = $recursive === -1 ? $this->insertRecursive : $recursive;

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

		$this->DB_WE->query('SELECT ID,ParentID,Path,ContentType' . $classField . ' FROM ' . addTblPrefix($this->remTable) . ' WHERE ' . ($recursion === 0 ? 'ID' : 'ParentID') . ' IN (' . implode(',', $IDs) . ') ' . $whereType . 'AND ((1' . we_users_util::makeOwnersSql() . ') ' . $wsQuery . ') ORDER BY Path ASC');
		while($this->DB_WE->next_record()){
			$data = $this->DB_WE->getRecord();
			if(($recursive || $recursion === 0) && $data['ContentType'] === 'folder' && !isset($foldersDone[$data['ID']])){
				$todo[] = $data['ID'];
				$foldersDone[] = $data['ID'];
			}
			if($data['ContentType'] !== 'folder'){
			//if((!$this->$typeProp || in_array($data[$typeField], explode(',', $this->$typeProp))) && $data['ContentType'] !== 'folder'){
				if($data['ParentID'] == 0){
					$resultRoot[$data['ID']] = $returnFull ? array('id' => $data['ID'], 'path' => $data['Path'], 'ct' => $data[$typeField]) : $data['ID'];
				} else {
					$result[$data['Path']] = array('id' => $data['ID'], 'path' => $data['Path'], 'ct' => $data[$typeField]);
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