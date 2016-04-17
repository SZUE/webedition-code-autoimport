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
 * class representing the model data of the search
 */
require_once (WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

class we_search_modelBase extends we_tool_model{
	/**
	 * @var string: classname
	 */
	public $ModelClassName = __CLASS__;

	/**
	 * @var string: toolname
	 */
	public $toolName = 'weSearch';
	public $mode = 0;
	// in these variables we hold data of actual search (= whichSearch) after initByHttp();
	protected $currentSearchstart = 0;
	protected $currentSearch = array();
	protected $currentLocation = array();
	protected $currentSearchTables = array();
	protected $currentSearchFields = array();
	protected $currentOrder = 'Text';
	protected $currentAnzahl = 10;
	protected $currentAnzahlMedialinks = 0;
	protected $currentSetView = 0;
	protected $currentFolderID = 0;
	protected $currentSearchForField = array(
		'text' => 0,
		'title' => 0,
		'meta' => 0,
	);
	protected $currentSearchForContentType = array(
		'image' => 0,
		'audio' => 0,
		'video' => 0,
		'other' => 0,
	);

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	public function __construct($table = ''){
		parent::__construct($table);
	}

	public function load($id = 0, $isAdvanced = false){
		parent::load($id);
	}

	public function initByHttp($whichSearch = '', $isWeCmd = true){

	}

	public function prepareModelForSearch(){

	}

	public function getProperty($property = ''){
		switch($property){
			case 'currentSearchstart':
				return $this->currentSearchstart;
			case 'currentSearch':
				return $this->currentSearch;
			case 'currentLocation':
				return $this->currentLocation;
			case 'currentSearchTables':
				return $this->currentSearchTables;
			case 'currentSearchFields':
				return $this->currentSearchFields;
			case 'currentOrder':
				return $this->currentOrder;
			case 'currentAnzahl':
				return $this->currentAnzahl;
			case 'currentAnzahlMedialinks':
				return $this->currentAnzahlMedialinks;
			case 'currentSetView':
				return $this->currentSetView;
			case 'currentFolderID':
				return $this->currentFolderID;
			case 'currentSearchForField':
				return $this->currentSearchForField;
			case 'currentSearchForContentType':
				return $this->currentSearchForContentType;
		}
	}

	public function setIsFolder($value){
		$this->IsFolder = $value;
	}

	public function filenameNotValid($text = ''){
		return preg_match('|[^a-z0-9._-]|i', $text);
	}

	public static function getLangText($path, $text){
		switch($path){
			case '/_PREDEF_':
				return g_l('searchtool', '[vordefinierteSuchanfragen]');
			case '/_PREDEF_/document':
				return g_l('searchtool', '[dokumente]');
			case '/_PREDEF_/document/unpublished':
				return g_l('searchtool', '[unveroeffentlicheDokumente]');
			case '/_PREDEF_/document/static':
				return g_l('searchtool', '[statischeDokumente]');
			case '/_PREDEF_/document/dynamic':
				return g_l('searchtool', '[dynamischeDokumente]');

			case '/_PREDEF_/object':
				return g_l('searchtool', '[objekte]');
			case '/_PREDEF_/object/unpublished':
				return g_l('searchtool', '[unveroeffentlicheObjekte]');
			case '/_CUSTOM_':
				return g_l('searchtool', '[eigeneSuchanfragen]');
			case '/_VERSION_':
				return g_l('searchtool', '[versionen]');
			case '/_VERSION_/document':
				return g_l('searchtool', '[dokumente]');
			case '/_VERSION_/document/deleted':
				return g_l('searchtool', '[geloeschteDokumente]');

			case '/_VERSION_/object':
				return g_l('searchtool', '[objekte]');
			case '/_VERSION_/object/deleted':
				return g_l('searchtool', '[geloeschteObjekte]');
			default:
				return $text;
		}
	}

}
