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
	var $ParentID = 8;

	/**
	 * @var string: classname
	 */
	var $ModelClassName = __CLASS__;

	/**
	 * @var string: name of the icon in the tree
	 */
	var $Icon = 'Suche.gif';

	/**
	 * @var string: toolname
	 */
	var $toolName = 'weSearch';

	/**
	 * @var tinyint: flag if the search ist predefined or not
	 */
	var $predefined;

	/**
	 * for each search there are seperate variables
	 *
	 * @var integer: position to start the search
	 */
	var $searchstartDocSearch = 0;
	var $searchstartTmplSearch = 0;
	var $searchstartAdvSearch = 0;

	/**
	 * @var array: includes the text to search for
	 */
	var $searchDocSearch = array();
	var $searchTmplSearch = array();
	var $searchAdvSearch = array();

	/**
	 * @var array: includes the operators
	 */
	var $locationDocSearch = array();
	var $locationTmplSearch = array();
	var $locationAdvSearch = array();

	/**
	 * @var tinyint: flag that shows what you are searching for in the docsearch
	 */
	var $searchForTextDocSearch = 1;
	var $searchForTitleDocSearch = 0;
	var $searchForContentDocSearch = 0;

	/**
	 * @var tinyint: flag that shows what you are searching for in the tmplsearch
	 */
	var $searchForTextTmplSearch = 1;
	var $searchForContentTmplSearch = 0;

	/**
	 * @var array: shows which tables you have to search in in the advsearch
	 */
	var $search_tables_advSearch = array();

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	var $folderIDDoc;
	var $folderIDTmpl;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	var $setViewDocSearch = 0;
	var $setViewTmplSearch = 0;
	var $setViewAdvSearch = 0;

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	var $anzahlDocSearch = 10;
	var $anzahlTmplSearch = 10;
	var $anzahlAdvSearch = 10;

	/**
	 * @var string: gives the order
	 */
	var $OrderDocSearch = "Text";
	var $OrderTmplSearch = "Text";
	var $OrderAdvSearch = "Text";

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	var $searchFieldsDocSearch = array();
	var $searchFieldsTmplSearch = array();
	var $searchFieldsAdvSearch = array();
	var $activTab = 1;

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	function __construct($weSearchID = 0){

		parent::__construct(SUCHE_TABLE);

		if($weSearchID){
			$this->ID = $weSearchID;
			$this->load($weSearchID);
		}
	}

	function load($id = 0){
		parent::load($id);
		foreach(get_object_vars($this) as $key => &$cur){
			if(is_string($cur) && substr($cur, 0, 2) == 'a:'){
				$this->{$key} = unserialize($cur);
			}
		}
	}

	function setIsFolder($value){
		$this->IsFolder = $value;
		$this->Icon = ($value ? we_base_ContentTypes::FOLDER_ICON : 'Suche.gif');
	}

	function filenameNotValid($text){
		return preg_match('|[^a-z0-9._-]|i', $text);
	}

	static function getLangText($path, $text){
		switch($path){
			case '/Vordefinierte Suchanfragen' :
				return g_l('searchtool', '[vordefinierteSuchanfragen]');
			case '/Vordefinierte Suchanfragen/Dokumente' :
				return g_l('searchtool', '[dokumente]');
			case '/Vordefinierte Suchanfragen/Objekte' :
				return g_l('searchtool', '[objekte]');
			case substr($path, 0, 43) == '/Vordefinierte Suchanfragen/Dokumente/Unver' :
				return g_l('searchtool', '[unveroeffentlicheDokumente]');
			case '/Vordefinierte Suchanfragen/Dokumente/Statische Dokumente' :
				return g_l('searchtool', '[statischeDokumente]');
			case '/Vordefinierte Suchanfragen/Dokumente/Dynamische Dokumente' :
				return g_l('searchtool', '[dynamischeDokumente]');
			case substr($path, 0, 41) == '/Vordefinierte Suchanfragen/Objekte/Unver' :
				return g_l('searchtool', '[unveroeffentlicheObjekte]');
			case '/Eigene Suchanfragen' :
				return g_l('searchtool', '[eigeneSuchanfragen]');
			case '/Versionen' :
				return g_l('searchtool', '[versionen]');
			case '/Versionen/Dokumente' :
				return g_l('searchtool', '[dokumente]');
			case '/Versionen/Objekte' :
				return g_l('searchtool', '[objekte]');
			case substr($path, 0, 24) == '/Versionen/Dokumente/gel' :
				return g_l('searchtool', '[geloeschteDokumente]');
			case substr($path, 0, 22) == '/Versionen/Objekte/gel' :
				return g_l('searchtool', '[geloeschteObjekte]');
			default:
				return $text;
		}
	}

}
