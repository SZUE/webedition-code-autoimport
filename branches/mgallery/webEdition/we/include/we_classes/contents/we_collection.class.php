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

	protected $Collection = '';
	public $remTable;
	protected $jsFormCollection = '';

	/** Constructor
	 * @return we_collection
	 * @desc Constructor for we_collection
	 */
	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Collection');
		$this->remTable = FILE_TABLE;

		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_INFO);
			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}
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
		we_html_multiIconBox::getHTML('weOtherDocProp', '100%', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => 140))
			, 20);
	}

	function formCollection(){t_e("col", $this->Collection);

		// one $yuiSuggest instance for all rows
		$yuiSuggest = &weSuggest::getInstance();

		//prepare items array: nonexisting IDs are thrown out by id_to_path. reenter empty rows (id = -1) after setting paths
		$tmpItems = id_to_path($this->Collection, FILE_TABLE, $this->DB_WE, false, true);
		$items = array();
		foreach(explode(',', trim($this->Collection, ',')) as $id){
			$items[] = array("id" => intval($id), "path" => isset($tmpItems[$id]) ? $tmpItems[$id] : '');
		}

		$index = 0;
		$rows = '';
		foreach($items as $item){
			$index++;
			$rows .= $this->getCollectionRow($item, $index, $yuiSuggest, count($items));
		}
		
		// write "blank" collection row to js var
		$this->jsFormCollection .= "
weCollectionEdit.maxIndex = " . count($items) . "; 
weCollectionEdit.blankRow = '" . str_replace(array("'"), "\'", str_replace(array("\n\r", "\r\n", "\r", "\n"), "", $this->getCollectionRow(array("id" => -1, "path" => '/'), 'XX', $yuiSuggest, 1, true))) . "';";

		return we_html_element::jsElement($this->jsFormCollection) . we_html_element::htmlDiv(array('id' => 'content_table', 'style' => 'width:806px;border:1px solid #afb0af;padding:20px;margin:20px;background-color:white;'), $rows);
	}

	function getCollectionRow($item, $index, &$yuiSuggest, $itemsNum = 0, $noAcAutoInit = false){
		$textname = 'we_' . $this->Name . '_ItemName_' . $index;
		$idname = 'we_' . $this->Name . '_ItemID_' . $index;

		$wecmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmd2 = "document.we_form.elements['" . $textname . "'].value";
		$wecmd3 = "opener._EditorFrame.setEditorIsHot(true);opener.weCollectionEdit.repaintAndRetrieveCsv();";
		$this->jsFormCollection .= $noAcAutoInit ? 'weCollectionEdit.selectorCmds = ["' . $wecmd1 . '","' . $wecmd2 . '","' . $wecmd3 . '"];' : ''; 

		//$this->jsFormCollection .= $noAcAutoInit ? 'weCollectionEdit.selectorCmd_1 = "' . $wecmd1 . '";' : '';


		$wecmdenc1 = we_base_request::encCmd($wecmd1);
		$wecmdenc2 = we_base_request::encCmd($wecmd2);
		$wecmdenc3 = we_base_request::encCmd($wecmd3);

		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['" . $idname . "'].value,'" . $this->remTable . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','',1)", true, 0, 0, '', '', false, false, '_' . $index);
		$openbutton = we_html_button::create_button("image:edit_edit", "javascript:if(document.we_form.elements['" . $idname . "'].value){top.doClickDirect(document.we_form.elements['" . $idname . "'].value,'" . ($this->remTable === FILE_TABLE ? we_base_ContentTypes::TEMPLATE : we_base_ContentTypes::OBJECT_FILE) . "','" . $this->remTable . "'); }");
		$trashButton = we_html_button::create_button("image:btn_function_trash", "javascript:document.we_form.elements['" . $idname . "'].value='-1';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputItem_" . $index ."');_EditorFrame.setEditorIsHot(true);weCollectionEdit.repaintAndRetrieveCsv();", true, 27, 22);
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setContentType('folder,text/weTmpl');
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setAcId('Item_' . $index, true);
		$yuiSuggest->setNoAutoInit($noAcAutoInit);
		$yuiSuggest->setInput($textname, $item['path'], array("onmouseover" => "document.getElementById('drag_" . $index . "').draggable=false", "onmouseout" => "document.getElementById('drag_" . $index . "').draggable=true"));
		$yuiSuggest->setResult($idname, $item['id']);
		$yuiSuggest->setWidth(210);
		$yuiSuggest->setMaxResults(10);//$yuiSuggest->setOpenButton
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setTrashButton($trashButton);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setOpenButton($openbutton);
		$yuiSuggest->setDoOnItemSelect("weCollectionEdit.repaintAndRetrieveCsv();");

		$rowControllsArr = array();
		$rowControllsArr[] = we_html_button::create_button('image:btn_add_listelement', "javascript:_EditorFrame.setEditorIsHot(true);weCollectionEdit.addRows(this);//top.we_cmd('switch_edit_page',1,we_transaction);", true, 100, 22);
		$rowControllsArr[] = we_html_tools::htmlSelect('numselect_' . $index, array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10), 1, '', false, array('id' => 'numselect_' . $index));
		$rowControllsArr[] = we_html_button::create_button('image:btn_direction_up', 'javascript:weCollectionEdit.moveUp(this);', true, 0, 0, '', '', ($index === 1 ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('image:btn_direction_down', 'javascript:weCollectionEdit.moveDown(this);', true, 0, 0, '', '', ($index === $itemsNum ? true : false), false, '_' . $index);
		$rowControllsArr[] = we_html_button::create_button('image:btn_function_trash', 'javascript:weCollectionEdit.deleteRow(this)');
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
				'ondragstart' => 'weCollectionEdit.drag(event)', 
				'ondrop' => 'weCollectionEdit.drop(event)', 
				'ondragover' => 'weCollectionEdit.allowDrop(event)', 
				'ondragenter' => 'weCollectionEdit.enterDrag(event)'
			), $rowHtml);
	}

	function i_filenameDouble(){//
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->Text) . " AND Text='" . escape_sql_query($this->Filename) . "' AND ID != " . intval($this->ID), "", $this->DB_WE);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		$this->ContentType = $this->IsFolder ? 'folder' : we_base_ContentTypes::COLLECTION;
		$this->Filename = $this->Text;
	}

	//FIXME: maybe add column Filename to db to avoid setting Text = Filename and Filename = Text when initializing or saving we_doc!
	function getText(){
		return $this->Filename;
	}

}