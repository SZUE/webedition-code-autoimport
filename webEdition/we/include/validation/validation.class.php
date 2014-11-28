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
abstract class validation{

	static function getAllCategories(){
		$cats = array(
			'xhtml' => g_l('validation', '[category_xhtml]'),
			'links' => g_l('validation', '[category_links]'),
			'css' => g_l('validation', '[category_css]'),
			'accessibility' => g_l('validation', '[category_accessibility]')
		);
		return $cats;
	}

	static function saveService($validationService){
		// before saving check if another validationservice has this name
		$exist = f('SELECT 1 FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE name="' . $GLOBALS['DB_WE']->escape($validationService->name) . '"
					AND PK_tblvalidationservices != ' . intval($validationService->id) . ' LIMIT 1');

		if($exist === '1'){
			$GLOBALS['errorMessage'] = g_l('validation', '[edit_service][servicename_already_exists]');
			return false;
		}

		$qSet = we_database_base::arraySetter(array(
				'category' => $validationService->category,
				'name' => $validationService->name,
				'host' => $validationService->host,
				'path' => $validationService->path,
				'method' => $validationService->method,
				'varname' => $validationService->varname,
				'checkvia' => $validationService->checkvia,
				'additionalVars' => $validationService->additionalVars,
				'ctype' => $validationService->ctype,
				'fileEndings' => $validationService->fileEndings,
				'active' => $validationService->active
		));
		if($validationService->id != 0){
			$query = 'UPDATE ' . VALIDATION_SERVICES_TABLE . ' SET ' . $qSet .
				' WHERE PK_tblvalidationservices = ' . intval($validationService->id);
		} else {
			$query = 'INSERT INTO ' . VALIDATION_SERVICES_TABLE . ' SET ' . $qSet;
		}

		if($GLOBALS['DB_WE']->query($query)){
			if($validationService->id == 0){
				$validationService->id = $GLOBALS['DB_WE']->getInsertId();
			}
			return $validationService;
		} else {
			return false;
		}
	}

	static function deleteService($validationService){
		if($validationService->id != 0){

			if($GLOBALS['DB_WE']->query('DELETE FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE PK_tblvalidationservices = ' . intval($validationService->id))){
				return true;
			}
		} else {
			//  not saved entry - must not be deleted from db
			return true;
		}
		return false;
	}

	static function getValidationServices($mode = 'edit'){
		$_ret = array();

		switch($mode){
			case 'edit':
				$query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE;
				break;
			case 'use':
				$query = 'SELECT * FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE fileEndings LIKE "%' . $GLOBALS['DB_WE']->escape($GLOBALS['we_doc']->Extension) . '%" AND active=1';
				break;
		}

		$GLOBALS['DB_WE']->query($query);
		while($GLOBALS['DB_WE']->next_record()){
			$_ret[] = new validationService($GLOBALS['DB_WE']->f('PK_tblvalidationservices'), 'custom', $GLOBALS['DB_WE']->f('category'), $GLOBALS['DB_WE']->f('name'), $GLOBALS['DB_WE']->f('host'), $GLOBALS['DB_WE']->f('path'), $GLOBALS['DB_WE']->f('method'), $GLOBALS['DB_WE']->f('varname'), $GLOBALS['DB_WE']->f('checkvia'), $GLOBALS['DB_WE']->f('ctype'), $GLOBALS['DB_WE']->f('additionalVars'), $GLOBALS['DB_WE']->f('fileEndings'), $GLOBALS['DB_WE']->f('active'));
		}
		return $_ret;
	}

	/**
	 * @return  void
	 * @param   string $element
	 * @param   array $attribs
	 * @desc    This function checks if given attribs are elements of a certain xhtml-element
	  changes attribs if removeWrong is true

	 */
	public static function validateXhtmlAttribs($element, &$attribs, $xhtmlType, $showWrong, $removeWrong){

		if($xhtmlType === 'transitional'){ //	use xml-transitional
			include(WE_INCLUDES_PATH . 'validation/xhtml_10_transitional.inc.php');
			//   the array $_validAtts and $_reqAtts are set inside this include-file
		} else {		//	use xml-strict
			include(WE_INCLUDES_PATH . 'validation/xhtml_10_strict.inc.php');
			//   the array $_validAtts and $_reqAtts are set inside this include-file
		}

		if(isset($_validAtts[$element])){ //	element exists
			//	check if all parameters are allowed.
			foreach($attribs as $k => $v){
				if(!in_array($k, $_validAtts[$element]) && !in_array(str_replace('pass_', '', $k), $_validAtts[$element])){

					$removeText = '';

					if($removeWrong){
						$removeText = ' ' . g_l('xhtmlDebug', '[removed_element][text]');
						unset($attribs[$k]);
					}

					if($showWrong){
						if(isset($_SESSION['prefs']['xhtml_show_wrong_text']) && $_SESSION['prefs']['xhtml_show_wrong_text']){
							echo '<p>' . sprintf(g_l('xhtmlDebug', '[wrong_attribute][text]') . $removeText, $k, $element) . '</p>';
						}
						if(isset($_SESSION['prefs']['xhtml_show_wrong_js']) && $_SESSION['prefs][xhtml_show_wrong_js']){
							echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(sprintf(g_l('xhtmlDebug', '[wrong_attribute][error_log]'), $k, $element) . $removeText), we_message_reporting::WE_MESSAGE_ERROR));
						}
						if(isset($_SESSION['prefs']['xhtml_show_wrong_error_log']) && $_SESSION['prefs']['xhtml_show_wrong_error_log']){
							t_e(sprintf(g_l('xhtmlDebug', '[wrong_attribute][error_log]'), $k, $element) . $removeText);
						}
					}
				}
			}

			//	check if all required parameters are there.
			if(array_key_exists($element, $_reqAtts)){
				foreach($_reqAtts[$element] as $required){

					if(!array_key_exists($required, $attribs)){

						if($showWrong){
							if(isset($_SESSION['prefs']['xhtml_show_wrong_text']) && $_SESSION['prefs']['xhtml_show_wrong_text']){
								echo '<p>' . sprintf(g_l('xhtmlDebug', '[missing_attribute][text]'), $required, $element) . '</p>';
							}
							if(isset($_SESSION['prefs']['xhtml_show_wrong_js']) && $_SESSION['prefs']['xhtml_show_wrong_js']){
								echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('xhtmlDebug', '[missing_attribute][error_log]'), $required, $element), we_message_reporting::WE_MESSAGE_ERROR));
							}
							if(isset($_SESSION['prefs']['xhtml_show_wrong_error_log']) && $_SESSION['prefs']['xhtml_show_wrong_error_log']){
								error_log(sprintf(g_l('xhtmlDebug', '[missing_attribute][error_log]'), $required, $element));
							}
						}
					}
				}
			}
		} else { //	element does not exist
			if($showWrong){
				if(isset($_SESSION['prefs']['xhtml_show_wrong_text']) && $_SESSION['prefs']['xhtml_show_wrong_text']){
					echo '<p>' . sprintf(g_l('xhtmlDebug', '[wrong_element][text]'), $element) . '</p>';
				}
				if(isset($_SESSION['prefs']['xhtml_show_wrong_js']) && $_SESSION['prefs']['xhtml_show_wrong_js']){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(sprintf(g_l('xhtmlDebug', '[wrong_element][error_log]'), $element), we_message_reporting::WE_MESSAGE_ERROR));
				}
				if(isset($_SESSION['prefs']['xhtml_show_wrong_error_log']) && $_SESSION['prefs']['xhtml_show_wrong_error_log']){
					error_log(sprintf(g_l('xhtmlDebug', '[wrong_element][error_log]'), $element));
				}
			}
			if($removeWrong){
				//  nothing
			}
		}
	}

}
