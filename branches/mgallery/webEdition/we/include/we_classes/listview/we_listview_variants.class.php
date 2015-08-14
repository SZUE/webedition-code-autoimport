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

/**
 * @desc    class for tag <we:listview type="shopVariants">
 *
 */
class we_listview_variants extends we_listview_base{
	var $Record = array();
	var $VariantData = array();
	var $Position = 0;
	//old code compatibility
	var $Id = 0;
	public $id = 0;
	var $ObjectId = 0;
	var $DefaultName = 'default';
	var $Model = null;
	var $IsObjectFile = false;
	var $hidedirindex = false;
	var $objectseourls = false;

	function __construct($name, $rows, $defaultname, $documentid, $objectid, $offset, $hidedirindex, $objectseourls, $triggerID){
		parent::__construct($name, $rows, $offset);

		$this->DefaultName = $defaultname;
		$this->hidedirindex = $hidedirindex;
		$this->objectseourls = $objectseourls;
		$this->triggerID = $triggerID;

		// we have to init a new document and look for the given field
		// get id of given document and check if it is a document or an objectfile
		if($documentid){
			$this->id = $documentid;

			$this->Model = new we_webEditionDocument();
			$this->Model->initByID($this->id);
		} else if($objectid && defined('OBJECT_TABLE')){
			$this->IsObjectFile = true;
			$this->id = $objectid;
			$this->Model = new we_objectFile();
			$this->Model->initByID($this->id, OBJECT_FILES_TABLE);
			// check if its a document or a objectFile
		} elseif($GLOBALS['we_doc'] instanceof we_objectFile){ // is an objectFile can this happen??!
			$this->id = $GLOBALS['we_doc']->ID;
			$this->IsObjectFile = true;
			$this->Model = $GLOBALS['we_doc'];
		} elseif(isset($GLOBALS['we_obj'])){
			$this->id = $GLOBALS['we_obj']->ID;
			$this->IsObjectFile = true;
			$this->Model = $GLOBALS['we_obj'];
		} else {
			$this->id = $GLOBALS['we_doc']->ID;
			$this->Model = $GLOBALS['we_doc'];
		}
		$this->id = $this->id;

		// store model in listview object
		$this->VariantData['Record'] = we_base_variants::getVariantData($this->Model, $this->DefaultName);
		$this->anz_all = count($this->VariantData['Record']);
		$this->anz = min($this->rows, $this->anz_all);
	}

	function next_record(){
		$this->Position = ($this->count + $this->start);
		if(isset($this->VariantData['Record'][$this->Position])){
			$ret = $this->VariantData['Record'][$this->Position];

			list($key, $vardata) = each($ret);
			foreach($vardata as $name => $value){

				$ret[$name] = (isset($value['type']) && $value['type'] === 'img' ?
						// there is a difference between objects and webEdition Documents
						isset($value['bdid']) ? $value['bdid'] : $value['dat'] :
						(isset($value['dat']) ? $value['dat'] : '')
					);
			}

			$ret['WE_VARIANT_NAME'] = $key;

			if($key != $this->DefaultName){
				$varUrl = we_base_constants::WE_VARIANT_REQUEST . '=' . $key;
				$ret['WE_VARIANT'] = $key;
			} else {
				$varUrl = $ret['WE_VARIANT'] = '';
			}

			$ret['WE_ID'] = $this->id;
			$path_parts = pathinfo($this->IsObjectFile ? $GLOBALS['we_doc']->Path : $this->Model->Path);
			if($this->IsObjectFile){ // objectFile
				if($this->objectseourls && show_SeoLinks()){
					$Url = f('SELECT Url FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $this->id, '', $this->DB_WE);
					if($Url != ''){
						$ret['WE_PATH'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') .
							( show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								'' : '/' . $path_parts['filename']
							) . '/' . $Url . ($varUrl ? "?$varUrl" : '');
					} else {
						$ret['WE_PATH'] = (show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES))) ?
								($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . "?we_objectID=" . $this->id . ($varUrl ? "&amp;$varUrl" : '') :
								$GLOBALS['we_doc']->Path . '?we_objectID=' . $this->id . ($varUrl ? '&amp;' . $varUrl : '')
							);
					}
				} elseif(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$ret['WE_PATH'] = $GLOBALS['we_doc']->Path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . ($varUrl ? "?$varUrl" : '');
				} else {
					$ret['WE_PATH'] = $GLOBALS['we_doc']->Path . '?we_objectID=' . $this->id . ($varUrl ? "&amp;$varUrl" : '');
				}
			} else // webEdition Document

			if(show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && $this->hidedirindex && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
				$ret['WE_PATH'] = $this->Model->Path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/' . ($varUrl ? '?' . $varUrl : '');
			} else {
				$ret['WE_PATH'] = $this->Model->Path . ($varUrl ? '?' . $varUrl : '');
			}

			$this->Record = $ret;
			$this->count++;
			return true;
		}
		return false;
	}

}
