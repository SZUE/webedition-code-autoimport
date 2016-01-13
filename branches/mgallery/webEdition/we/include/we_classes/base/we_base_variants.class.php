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
/*
  data of variations have the following format in document

  we_doc->elements[WE_VARIANTS_ELEMENT_NAME] = array(
  [0] => array(
  'VARIATIONNAME1' => array(
  'fieldName1' => array(
  'type' = 'txt',
  'dat' = 'Text'
  ),
  'fieldName2' => array(
  'type' = 'img',
  'dat' = 152
  )
  ),
  [1] => array(
  'VARIATIONNAME2' => array(
  'fieldName1' => array(
  'type' = 'txt',
  'dat' = 'CU'
  ),
  'fieldName2' => array(
  'type' = 'img',
  'dat' = 155
  )
  )
  )
  )
  =====>>

  in editmode available in document
  we_doc->elements[WE_VARIANTS_PREFIX . '0'] = array('type' = 'txt', 'dat' = 'VARIATIONNAME1');
  we_doc->elements[WE_VARIANTS_PREFIX . '0' . '_' . fieldName1] = array('type' = 'txt', 'dat' = 'Text');
  we_doc->elements[WE_VARIANTS_PREFIX . '0' . '_' . fieldName2] = array('type' = 'img', 'dat' = 152);

  we_doc->elements[WE_VARIANTS_PREFIX . '1'] = array('type' = 'txt', 'dat' = 'VARIATIONNAME2');
  we_doc->elements[WE_VARIANTS_PREFIX . '1' . '_' . fieldName1] = array('type' = 'txt', 'dat' = 'CU');
  ...
 */

abstract class we_base_variants{

	/**
	 * Searchs all elements of document/object
	 * fetches all variation-data in one single field
	 * and deletes all other fields
	 * when not save, the field is resettet for the editor
	 *
	 * @param object $model
	 * @param boolean $save
	 */
	public static function correctModelFields(&$model, $save = true){

		$elements = $model->elements;

		// all variant fields must be stored in one single field of the content table
		// store variationfields in one array
		$variationElements = array();

		foreach($elements as $element => $elemArr){
			if(strpos($element, we_base_constants::WE_VARIANTS_PREFIX) !== false){
				//since delete might have deleted this instance, check if this id is still set
				list($pos) = explode('_', substr($element, strlen(we_base_constants::WE_VARIANTS_PREFIX), 2));
				if(isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$pos])){
					//only add elements which are not deleted
					$variationElements[$element] = $elemArr;
				}
				if($save){
					$model->elements[$element] = null;
					unset($model->elements[$element]);
				}
			}
		}
		// :ATTENTION: if nr of variants is > 10 a ksort of the elements is not
		// enough to build blocks of data of a single variant.
		ksort($variationElements);

		$variationElement = array();
		$nameOfPosition = array();

		// :ATTENTION: if nr of variants is > 10 a ksort of the elements is not
		// enough to build blocks of data of a single variant.
		foreach($variationElements as $element => $data){

			$elemNr = self::getNrFromElemName($element);

			if(!isset($nameOfPosition["nameof_$elemNr"])){
				$nameOfPosition["nameof_$elemNr"] = $data['dat'];
				$variationElement[$elemNr][$nameOfPosition["nameof_$elemNr"]] = array();
			} else {
				$fieldName = self::getFieldNameFromElemName($element);
				$variationElement[$elemNr][$nameOfPosition["nameof_$elemNr"]][$fieldName] = $data;
			}
		}

		// now create element for the model
		// just overwrite new values ...
		$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['type'] = 'variant';
		$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'] = ($save ? we_serialize($variationElement) : $variationElement);
	}

	/**
	 * this function is reverse function to correctModelFields
	 * initialises variant data in the model and stores them in special fields
	 * @param object $model
	 * @param boolean $unserialize
	 */
	public static function setVariantDataForModel(&$model, $unserialize = false){

		// set variation data from array and
		if(!isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME])){
			return;
		}

		if($unserialize){
			$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'] = is_array($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']) ?
				$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'] :
				we_unserialize($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']);
		}

		$elements = $model->elements;

		$variations = isset($elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']) ? $elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'] : ''; //Fix #9349
		if(!$variations || !is_array($variations)){
			return;
		}
		foreach($variations as $i => $variation){
			if(is_array($variation)){

				foreach($variation as $name => $varArr){
					$model->elements[we_base_constants::WE_VARIANTS_PREFIX . $i] = array(
						'type' => 'txt',
						'dat' => $name
					);

					foreach($varArr as $name => $datArr){
						$model->elements[we_base_constants::WE_VARIANTS_PREFIX . $i . '_' . $name] = $datArr;
					}
				}
			}
		}
	}

	private static function getNrFromElemName($elemName){
		return preg_replace('/_(.*)/', '', substr($elemName, strlen(we_base_constants::WE_VARIANTS_PREFIX)));
	}

	private static function getFieldNameFromElemName($elemName){

		$fieldNameTmp = substr($elemName, strlen(we_base_constants::WE_VARIANTS_PREFIX));
		$fieldName = preg_replace('/(\d+_*)/', '', $fieldNameTmp, 1);

		return ($fieldNameTmp == $fieldName ? '' : $fieldName);
	}

	public static function getNumberOfVariants(&$model){
		return (isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]) && is_array($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']) ?
				count($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']) :
				0);
	}

	private static function insertVariant(&$model, $position){
		$amount = we_base_variants::getNumberOfVariants($model);

		// init model->elements if neccessary

		if(!isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]) || !isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat']) || !is_array($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'])){
			$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME] = array();
			$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'] = array();
		}

		// add new element at end of array, move it when neccesary
		$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][] = self::createNewVariantElement($model);

		// now move element, it is actually at last position
		if($amount > $position){ // move all elements
			$newElemPos = $amount;
			while($position < $newElemPos){
				self::changeVariantPosition($newElemPos, --$newElemPos, $model);
			}
		}
	}

	private static function createNewVariantElement(&$model){

		// :TODO: improve me
		return array();
	}

	public static function getAllVariationFields($model, $pos = false){
		$elements = $model->elements;
		$variationElements = array();

		foreach($elements as $element => $elemArr){
			if(strpos($element, we_base_constants::WE_VARIANTS_PREFIX) !== false){
				$variationElements[$element] = $elemArr;
			}
		}
		ksort($variationElements);

		if($pos === false){
			return $variationElements;
		}
		foreach($variationElements as $name => $value){
			if(self::getNrFromElemName($name) != $pos){
				unset($variationElements[$name]);
			}
		}
		return $variationElements;
	}

	private static function moveVariant(&$model, $pos, $direction){
		// check if a move is possible
		self::changeVariantPosition($pos, ($pos + ($direction === 'up' ? -1 : 1)), $model);
	}

	/**
	 * @param integer $pos1
	 * @param integer $pos2
	 * @param array $model
	 */
	private static function changeVariantPosition($pos1, $pos2, &$model){
		// first move all fields in the $modell
		$tmp = $model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$pos1];
		$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$pos1] = $model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$pos2];
		$model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$pos2] = $tmp;
		// move elements for editmode
		$variationElements_1 = we_base_variants::getAllVariationFields($model, $pos1);
		$variationElements_2 = we_base_variants::getAllVariationFields($model, $pos2);

		// backup pos 1
		$tmp = array();
		foreach($variationElements_1 as $name => $arr){
			$tmp[$name] = $arr;
			unset($model->elements[$name]);
		}

		// overwrite pos 1 with pos 2
		foreach($variationElements_2 as $name => $arr){
			$model->elements[self::getNameForPosition($name, $pos1)] = $model->elements[$name];
			unset($model->elements[$name]);
		}

		// restore pos 1 to pos2
		foreach($tmp as $name => $arr){
			$model->elements[self::getNameForPosition($name, $pos2)] = $tmp[$name];
		}
		// delete backup
		unset($tmp);
	}

	private static function getNameForPosition($name, $pos){
		return we_base_constants::WE_VARIANTS_PREFIX . $pos .
			(($fieldName = self::getFieldNameFromElemName($name)) ? '_' . self::getFieldNameFromElemName($name) : '');
	}

	private static function removeVariant(&$model, $delPos){
		$total = we_base_variants::getNumberOfVariants($model);

		$lastPos = $total - 1;
		// move at last position, then remove it
		while($delPos < $lastPos){
			self::moveVariant($model, $delPos++, 'down');
		}
		// first remove all fields from doc
		$variationFields = we_base_variants::getAllVariationFields($model, $delPos);
		foreach(array_keys($variationFields) as $name){
			unset($model->elements[$name]);
		}
		if(is_array($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$delPos])){
			unset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'][$delPos]);
		}
	}

	public static function getVariantsEditorMultiBoxArrayObjectFile($model){
		$variantFields = $model->getVariantFields();

		$count = we_base_variants::getNumberOfVariants($model);

		$parts = $regs = array();

		if($count > 0){
			for($i = 0; $i < $count; $i++){
				$plusBut = we_html_button::create_button("fa:btn_add_field,fa-plus,fa-lg fa-square-o", "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_insert_variant','" . ($i) . "');", true, 40);
				$upbut = ($i == 0 ? we_html_button::create_button(we_html_button::DIRUP, "", true, 21, 22, "", "", true) : we_html_button::create_button(we_html_button::DIRUP, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_move_variant_up','" . ($i) . "');"));
				$downbut = ($i == ($count - 1) ? we_html_button::create_button(we_html_button::DIRDOWN, "", true, 21, 22, "", "", true) : we_html_button::create_button(we_html_button::DIRDOWN, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_move_variant_down','" . ($i) . "');"));
				$trashbut = we_html_button::create_button(we_html_button::TRASH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_remove_variant','" . ($i) . "');", true, 30);

				$content = '<table class="defaultfont lowContrast" style="width:700px">
<tr>
		<td style="width:200px"><span class="defaultfont"><b>Name</b></span></td>
</tr>
<tr>
		<td>' . $model->getFieldHTML(we_base_constants::WE_VARIANTS_PREFIX . $i, 'input', array(), true, true) . '</td>
		<td>
			<table class="defaultfont lowContrast" style="text-align:right;width:120px">
				<tr>
					<td>' . $plusBut . '</td>
					<td>' . $upbut . '</td>
					<td>' . $downbut . '</td>
					<td>' . $trashbut . '</td>
				</tr>
			</table>
		</td>
	</tr>';

				foreach($variantFields as $realName => $attributes){

					$fieldInfo = explode('_', $realName); // Verursacht Bug #4682
					$type = $fieldInfo[0];
					$realname = $fieldInfo[1];
					if(preg_match('/(.+?)_(.*)/', $realName, $regs)){//und hier der fix #4682
						$type = $regs[1];
						$realname = $regs[2];
					}
					$name = we_base_constants::WE_VARIANTS_PREFIX . $i . '_' . $realname;
					//$name = ''; //#6924
					$content .= '
<tr><td><span class="defaultfont"><b>' . $realname . '</b></span><div class="objectDescription">' . (isset($model->DefArray[$type . '_' . $realname]['editdescription']) ? str_replace("\n", we_html_element::htmlBr(), $model->DefArray[$type . '_' . $realname]['editdescription']) : '') . '</div></td></tr>
<tr><td style="padding-bottom:8px;">' . $model->getFieldHTML($name, $type, $attributes, true, true) . '</td></tr>';
				}
				$content .= '</table>';
				$parts[] = array(
					'headline' => '',
					'html' => $content,
				);
			}
		} else {
			$i = 0;
		}

		$parts[] = array(
			'headline' => '',
			'html' => we_html_button::create_button("fa:btn_add_field,fa-plus,fa-lg fa-square-o", "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_insert_variant','" . ($i) . "');"),
		);
		return $parts;
	}

	public static function getVariantsEditorMultiBoxArray($model){
		$variationFields = $model->getVariantFields();

		$count = we_base_variants::getNumberOfVariants($model);

		$i = 0;
		$parts = array();

		if($count > 0){

			for($i = 0; $i < $count; $i++){
				$plusBut = we_html_button::create_button("fa:btn_add_field,fa-plus,fa-lg fa-square-o", "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_insert_variant','" . ($i) . "');", true, 40);
				$upbut = ($i == 0 ? we_html_button::create_button(we_html_button::DIRUP, '', true, 21, 22, '', '', true) : we_html_button::create_button(we_html_button::DIRUP, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_move_variant_up','" . ($i) . "');"));
				$downbut = ($i == ($count - 1) ? we_html_button::create_button(we_html_button::DIRDOWN, "", true, 21, 22, "", "", true) : we_html_button::create_button(we_html_button::DIRDOWN, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_move_variant_down','" . ($i) . "');"));
				$trashbut = we_html_button::create_button(we_html_button::TRASH, "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_remove_variant','" . ($i) . "');", true, 30);
				$previewBut = we_html_button::create_button(we_html_button::VIEW, "javascript:we_cmd('shop_preview_variant','" . $GLOBALS['we_transaction'] . "','" . ($model->getElement(we_base_constants::WE_VARIANTS_PREFIX . $i)) . "');", true, 30);

				$content = '<table class="defaultfont lowContrast" style="width:700px;">
<tr>
	<td style="width:200px" class="defaultfont bold">Name</td>
</tr>
<tr>
	<td>' . $model->formTextInput('input', we_base_constants::WE_VARIANTS_PREFIX . $i, '') . '</td>
		<td>
			<table class="defaultfont lowContrast" style="text-align:right">
				<tr>
					<td>' . $previewBut . '</td>
					<td>&nbsp;&nbsp;</td>
					<td>' . $plusBut . '</td>
					<td>' . $upbut . '</td>
					<td>' . $downbut . '</td>
					<td>' . $trashbut . '</td>
				</tr>
			</table>
		</td>
	</tr>';

				foreach($variationFields as $name => $fieldInformation){

					$fieldInformation['attributes']['name'] = we_base_constants::WE_VARIANTS_PREFIX . $i . '_' . $fieldInformation['attributes']['name'];
					$content .= '
	<tr>
		<td class="defaultfont"><b>' . $name . '</b></td>
		</tr>
		<tr>
		<td>' . we_tag($fieldInformation['type'], $fieldInformation['attributes'], (isset($fieldInformation['content']) ? $fieldInformation['content'] : '')) . '</td>
	<tr>';
				}
				$content .= '</table>';

				$parts[] = array(
					'headline' => '',
					'html' => $content,
				);
			}
		}
		$plusBut = we_html_button::create_button("fa:btn_add_field,fa-plus,fa-lg fa-square-o", "javascript:WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);we_cmd('shop_insert_variant','" . ($i) . "');");
		$content = $plusBut;

		$parts[] = array(
			'headline' => '',
			'html' => $content,
		);
		return $parts;
	}

	public static function useVariant(&$model, $name){
		$variantDatArray = $model->getElement(we_base_constants::WE_VARIANTS_ELEMENT_NAME);

		$model->Variant = $name;
		if(!is_array($variantDatArray)){
			return;
		}

		foreach($variantDatArray as $variant){
			if(!is_array($variant)){
				continue;
			}
			if(isset($variant[$name])){//can we break if one is found??
				$variantData = $variant[$name];
				foreach($variantData as $elementName => $elementData){
					$model->elements[$elementName] = $elementData;
				}
			}
		}
	}

	/**
	 * This function sets variant data for serialised document in the shopping basket
	 * different function, due to performance reasons and the shop itself
	 *
	 * @param array $record
	 * @param string $name
	 */
	public static function useVariantForShop(&$record, $name){
		if(!isset($record[we_base_constants::WE_VARIANTS_ELEMENT_NAME])){
			return;
		}
		$variantDatArray = we_unserialize($record[we_base_constants::WE_VARIANTS_ELEMENT_NAME]);

		foreach($variantDatArray as $variant){
			foreach($variant as $variantName => $variantData){
				if($variantName == $name){
					foreach($variantData as $elementName => $elementData){
						$record[$elementName] = ($elementData['type'] === 'img' ? $elementData['bdid'] : $elementData['dat']);
					}
				}
			}
		}
	}

	/**
	 * This function sets variant data for serialised object in the shopping basket
	 * different function, due to performance reasons and the shop itself
	 *
	 * @param array $record
	 * @param string $name
	 * @param we_objectFile $model
	 */
	public static function useVariantForShopObject(&$record, $name, $model){
		if(!isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME])){
			return;
		}
		$variantDatArray = $model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'];

		if(!is_array($variantDatArray)){
			return;
		}

		foreach($variantDatArray as $variant){
			foreach($variant as $variantName => $variantData){
				if($variantName == $name){
					foreach($variantData as $elementName => $elementData){
						// fields have the prefix we_
						$record['we_' . $elementName] = ($elementData['type'] === 'img' ? (isset($elementData['bdid']) ? $elementData['bdid'] : '') : (isset($elementData['dat']) ? $elementData['dat'] : ''));
					}
				}
			}
		}
	}

	public static function getVariantData($model, $defaultname){
		if(!isset($model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME])){
			return array();
		}

		// add default data to listview
		$elements = $model->elements[we_base_constants::WE_VARIANTS_ELEMENT_NAME]['dat'];
		//this elemets contains only the variant fields, not the non-variant fields of the object
		if(!is_array($elements) && $elements{0} == 'a'){
			$elements = we_unserialize($elements);
		}

		$newPos = count($elements);

		if($newPos > 0){

			$elemdata = $elements[0];
			if(is_array($elemdata) && $defaultname != ''){
				$noFirst = (strpos($defaultname, 'FIRST') === false);
				foreach($elemdata as $name => $varArr){
					foreach($varArr as $key => $fieldArr){
						if(isset($model->elements[$key])){
							if($noFirst){
								$elements[$newPos][$defaultname][$key] = $model->elements[$key];
							} else {
								$elementF[$defaultname][$key] = $model->elements[$key];
							}
						}
					}
				}
				if(!$noFirst){
					array_unshift($elements, $elementF);
				}
			}
		}
		// attemot to add the other fields
		$modelelemets = $model->elements; //get a copy of the non variant fields
		unset($modelelemets[we_base_constants::WE_VARIANTS_ELEMENT_NAME]); // get rid of some keys
		foreach(array_keys($modelelemets) as $key){
			if(strpos($key, we_base_constants::WE_VARIANTS_PREFIX) !== false && strpos($key, we_base_constants::WE_VARIANTS_PREFIX) == 0){
				unset($modelelemets[$key]);
			}
		}
		if($newPos > 0 && $elements){ //Fix #6883 - not sure if this has an impact
			foreach($elements as $name => &$varArr){//now add the elements
				foreach($varArr as $key => &$fieldArr){
					$fieldArr = array_merge($modelelemets, $fieldArr);
				}
			}
			unset($varArr, $fieldArr);
		}
		//
		return $elements;
	}

	public static function edit($isObject, $command, $we_doc){
		switch($command){
			case 'shop_insert_variant':
				self::insertVariant($we_doc, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'shop_move_variant_up':
				self::moveVariant($we_doc, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), 'up');
				break;
			case 'shop_move_variant_down':
				self::moveVariant($we_doc, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), 'down');
				break;
			case 'shop_remove_variant':
				self::removeVariant($we_doc, we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1));
				break;
			case 'shop_preview_variant':
				self::correctModelFields($we_doc, false);
				self::useVariant($we_doc, we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 2));

				echo $we_doc->getDocument();
				exit;
		}
		$GLOBALS['we_editmode'] = true;

		echo we_html_multiIconBox::getHTML('', ($isObject ? self::getVariantsEditorMultiBoxArrayObjectFile($we_doc) : self::getVariantsEditorMultiBoxArray($we_doc)), 30, '', -1, '', '', false);
	}

}
