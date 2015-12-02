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

class we_search_model extends we_tool_model{
	/**
	 * @var integer: default ParentId in which own searches are saved
	 */
	public $ParentID = 8;

	/**
	 * @var string: classname
	 */
	public $ModelClassName = __CLASS__;

	/**
	 * @var string: toolname
	 */
	public $toolName = 'weSearch';

	/**
	 * @var tinyint: flag if the search ist predefined or not
	 */
	public $predefined;

	/**
	 * for each search there are seperate variables
	 *
	 * @var integer: position to start the search
	 */
	public $searchstartDocSearch = 0;
	public $searchstartTmplSearch = 0;
	public $searchstartMediaSearch = 0;
	public $searchstartAdvSearch = 0;

	/**
	 * @var array: includes the text to search for
	 */
	public $searchDocSearch = array();
	public $searchTmplSearch = array();
	public $searchMediaSearch = array();
	public $searchAdvSearch = array();

	/**
	 * @var array: includes the operators
	 */
	public $locationDocSearch = array();
	public $locationTmplSearch = array();
	public $locationMediaSearch = array();
	public $locationAdvSearch = array();

	/**
	 * @var tinyint: flag that shows what you are searching for in the docsearch
	 */
	public $searchForTextDocSearch = 1;
	public $searchForTitleDocSearch = 0;
	public $searchForContentDocSearch = 0;

	/**
	 * @var tinyint: flag that shows what you are searching for in the tmplsearch
	 */
	public $searchForTextTmplSearch = 1;
	public $searchForContentTmplSearch = 0;

	/**
	 * @var tinyint: flag that shows what you are searching for in the mediaearch
	 */
	public $searchForTextMediaSearch = 1;
	public $searchForTitleMediaSearch = 0;
	public $searchForMetaMediaSearch = 0;
	public $searchForImageMediaSearch = 1;
	public $searchForVideoMediaSearch = 0;
	public $searchForAudioMediaSearch = 0;
	public $searchForOtherMediaSearch = 0;
	public $search_contentTypes_mediaSearch = array();

	/**
	 * @var array: shows which tables you have to search in in the advsearch
	 */
	public $search_tables_advSearch = array();

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	public $folderIDDoc;
	public $folderIDTmpl;
	public $folderIDMedia;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	public $setViewDocSearch = 0;
	public $setViewTmplSearch = 0;
	public $setViewMediaSearch = 0;
	public $setViewAdvSearch = 0;

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	public $anzahlDocSearch = 10;
	public $anzahlTmplSearch = 10;
	public $anzahlMediaSearch = 10;
	public $anzahlAdvSearch = 10;

	/**
	 * @var string: gives the order
	 */
	public $OrderDocSearch = "Text";
	public $OrderTmplSearch = "Text";
	public $OrderMediaSearch = "Text";
	public $OrderAdvSearch = "Text";

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	public $searchFieldsDocSearch = array();
	public $searchFieldsTmplSearch = array();
	public $searchFieldsMediaSearch = array();
	public $searchFieldsAdvSearch = array();
	public $activTab = 1;

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($weSearchID = 0){

		parent::__construct(SUCHE_TABLE);

		if($weSearchID){
			$this->ID = $weSearchID;
			$this->load($weSearchID);
		} else {
			$this->ID = 0;
		}
	}

	function load($id = 0, $isAdvanced = false){
		parent::load($id);
		$array = get_object_vars($this);
		foreach($array as $key => $cur){
			if(is_string($cur) && substr($cur, 0, 2) === 'a:'){
				$this->{$key} = we_unserialize($cur);
			}
		}
	}

	function setIsFolder($value){
		$this->IsFolder = $value;
	}

	function filenameNotValid($text){
		return preg_match('|[^a-z0-9._-]|i', $text);
	}

	static function getLangText($path, $text){
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
