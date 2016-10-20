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
class we_search_model extends we_search_modelBase{
	/**
	 * @var integer: default ParentId in which own searches are saved
	 */
	public $ParentID = 8;

	/**
	 * @var tinyint: flag if the search ist predefined or not
	 */
	public $predefined;
	public $activTab = 1;
	public $mode = 0;

	/**
	 * for each search there are seperate variables
	 *
	 * @var integer: position to start the search
	 */
	protected $searchstartDocSearch = 0;
	protected $searchstartTmplSearch = 0;
	protected $searchstartMediaSearch = 0;
	protected $searchstartAdvSearch = 0;

	/**
	 * @var array: includes the text to search for
	 */
	protected $searchDocSearch = [];
	protected $searchTmplSearch = [];
	protected $searchMediaSearch = [];
	protected $searchAdvSearch = [];

	/**
	 * @var array: includes the operators
	 */
	protected $locationDocSearch = [];
	protected $locationTmplSearch = [];
	protected $locationMediaSearch = [];
	protected $locationAdvSearch = [];

	/**
	 * @var tinyint: flag that shows what you are searching for in the docsearch
	 */
	protected $searchForTextDocSearch = 1;
	protected $searchForTitleDocSearch = 0;
	protected $searchForContentDocSearch = 0;

	/**
	 * @var tinyint: flag that shows what you are searching for in the tmplsearch
	 */
	protected $searchForTextTmplSearch = 1;
	protected $searchForContentTmplSearch = 0;

	/**
	 * @var tinyint: flag that shows what you are searching for in the mediaearch
	 */
	protected $searchForTextMediaSearch = 1;
	protected $searchForTitleMediaSearch = 0;
	protected $searchForMetaMediaSearch = 0;
	protected $searchForImageMediaSearch = 1;
	protected $searchForVideoMediaSearch = 0;
	protected $searchForAudioMediaSearch = 0;
	protected $searchForOtherMediaSearch = 0;

	/**
	 * @var array: shows which tables you have to search in in the advsearch
	 */
	protected $search_tables_advSearch = [];

	/**
	 * @var integer: folder-ids of the docsearch and the tmplsearch
	 */
	protected $folderIDDoc;
	protected $folderIDTmpl;
	protected $folderIDMedia;

	/**
	 * @var tinyint: flag that shows what view is set in each search
	 */
	protected $setViewDocSearch = 0;
	protected $setViewTmplSearch = 0;
	protected $setViewMediaSearch = 0;
	protected $setViewAdvSearch = 0;

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	protected $anzahlDocSearch = 10;
	protected $anzahlTmplSearch = 10;
	protected $anzahlMediaSearch = 10;
	protected $anzahlAdvSearch = 10;

	/**
	 * @var int: gives the number of entries in each search for one page
	 */
	protected $anzahlMedialinksDocSearch = 0;
	protected $anzahlMedialinksTmplSearch = 0;
	protected $anzahlMedialinksMediaSearch = 10;
	protected $anzahlMedialinksAdvSearch = 0;

	/**
	 * @var string: gives the order
	 */
	protected $OrderDocSearch = "Text";
	protected $OrderTmplSearch = "Text";
	protected $OrderMediaSearch = "Text";
	protected $OrderAdvSearch = "Text";

	/**
	 * @var array: includes the searchfiels which you are searching in
	 */
	protected $searchFieldsDocSearch = [];
	protected $searchFieldsTmplSearch = [];
	protected $searchFieldsMediaSearch = [];
	protected $searchFieldsAdvSearch = [];
	protected $searchTablesDocSearch = [];
	protected $searchTablesTmplSearch = [];
	protected $searchTablesMediaSearch = [];
	protected $searchTablesAdvSearch = [];
	protected $searchForFieldTmplSearch = ['text' => 0,
		'title' => 0,
		'content' => 0,
		'meta' => 0,
	];
	protected $searchForFieldDocSearch = ['text' => 0,
		'title' => 0,
		'content' => 0,
		'meta' => 0,
	];
	protected $searchForFieldMediaSearch = ['text' => 0,
		'title' => 0,
		'content' => 0,
		'meta' => 0,
	];
	protected $searchForContentTypeMediaSearch = ['image' => 0,
		'audio' => 0,
		'video' => 0,
		'other' => 0,
	];

	/**
	 * Default Constructor
	 * Can load or create new searchtool object depends of parameter
	 */
	public function __construct($weSearchID = 0){
		parent::__construct(SUCHE_TABLE);
		if($weSearchID){
			$this->ID = $weSearchID;
			$this->load($weSearchID);
		} else {
			$this->ID = 0;
		}
	}

	public function load($id = 0, $isAdvanced = false){
		parent::load($id);
		$array = get_object_vars($this);
		foreach($array as $key => $cur){
			if(is_string($cur) && substr($cur, 0, 2) === 'a:'){
				$this->{$key} = we_unserialize($cur);
			}
		}
	}

	public function initByHttp($whichSearch = '', $isWeCmd = true){
		// prepare searchFields, location and search(text) to read as array
		if($isWeCmd){
			$request = we_base_request::_(we_base_request::STRING, 'we_cmd');
			foreach($request as $k => $v){
				if(stristr($k, 'searchFields' . $whichSearch . '[') && !stristr($k, 'hidden_')){
					$_REQUEST['we_cmd']['searchFields' . $whichSearch][] = $v;
				}
				if(stristr($k, 'location' . $whichSearch . '[')){
					$_REQUEST['we_cmd']['location' . $whichSearch][] = $v;
				}
				if(stristr($k, 'search' . $whichSearch . '[')){
					$_REQUEST['we_cmd']['search' . $whichSearch][] = $v;
				}
				if($v == 1 || $v == 0){
					switch($k){
						case 'search_tables_advSearch[' . FILE_TABLE:
							$_REQUEST['we_cmd']['search_tables_advSearch'][FILE_TABLE] = $v;
							break;
						case 'search_tables_advSearch[' . (defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE'):
							$_REQUEST['we_cmd']['search_tables_advSearch'][TEMPLATES_TABLE] = $v;
							break;
						case 'search_tables_advSearch[' . (defined('VERSIONS_TABLE') ? VERSIONS_TABLE : 'VERSIONS_TABLE'):
							$_REQUEST['we_cmd']['search_tables_advSearch'][VERSIONS_TABLE] = $v;
							break;
						case 'search_tables_advSearch[' . (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
							$_REQUEST['we_cmd']['search_tables_advSearch'][OBJECT_FILES_TABLE] = $v;
							break;
						case 'search_tables_advSearch[' . (defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE'):
							$_REQUEST['we_cmd']['search_tables_advSearch'][OBJECT_TABLE] = $v;
							break;
					}
				}
			}
		} else {
			$modelVars = array_merge([// some vars are not persistent in db but must be written to session anyway: must COMPLETE!!
				'searchstartDocSearch',
				'searchstartTmplSearch',
				'searchstartMediaSearch',
				'searchstartAdvSearch'], (is_array($this->persistent_slots) ? $this->persistent_slots : []));

			/* was nice before 7.0, but it's not typed!
			  foreach($modelVars as $val){
			  if(($tmp = we_base_request::_(we_base_request::STRING, $val, we_base_request::NOT_VALID)) !== we_base_request::NOT_VALID){
			  $this->Model->$val = $tmp;
			  }
			  }
			 *
			 */

			foreach($modelVars as $v){
				if(isset($_REQUEST[$v])){
					$_REQUEST['we_cmd'][$v] = $_REQUEST[$v];
				}
			}
		}

		if(!$isWeCmd || $whichSearch === we_search_view::SEARCH_DOCS){
			$this->folderIDDoc = we_base_request::_(we_base_request::INT, 'we_cmd', $this->folderIDDoc, 'folderIDDoc');
			$this->searchDocSearch = we_base_request::_(we_base_request::RAW, 'we_cmd', $this->searchDocSearch, 'searchDocSearch');
			$this->searchForTextDocSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchForTextDocSearch, 'searchForTextDocSearch');
			$this->searchForTitleDocSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchForTitleDocSearch, 'searchForTitleDocSearch');
			$this->searchForContentDocSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchForContentDocSearch, 'searchForContentDocSearch');
			$this->currentAnzahl = $this->anzahlDocSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlDocSearch, 'anzahlDocSearch');
			$this->currentSetView = $this->setViewDocSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setViewDocSearch, 'setViewDocSearch');
			$this->OrderDocSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderDocSearch, 'OrderDocSearch');
			$this->currentSearchstart = $this->searchstartDocSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartDocSearch, 'searchstartDocSearch');
		}
		if(!$isWeCmd || $whichSearch === we_search_view::SEARCH_TMPL){
			$this->folderIDTmpl = we_base_request::_(we_base_request::INT, 'we_cmd', $this->folderIDDoc, 'folderIDDoc');
			$this->searchTmplSearch = we_base_request::_(we_base_request::RAW, 'we_cmd', $this->searchTmplSearch, 'searchTmplSearch');
			$this->searchForTextTmplSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchForTextTmplSearch, 'searchForTextTmplSearch');
			$this->searchForContentTmplSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchForContentTmplSearch, 'searchForContentTmplSearch');
			$this->anzahlTmplSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlTmplSearch, 'anzahlTmplSearch');
			$this->setViewTmplSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setViewTmplSearch, 'setViewTmplSearch');
			$this->OrderTmplSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderTmplSearch, 'OrderTmplSearch');
			$this->searchstartTmplSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartTmplSearch, 'searchstartTmplSearch');
		}
		if(!$isWeCmd || $whichSearch === we_search_view::SEARCH_MEDIA){
			$this->folderIDMedia = we_base_request::_(we_base_request::INT, 'we_cmd', $this->folderIDMedia, 'folderIDMedia');
			$this->searchFieldsMediaSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->searchFieldsMediaSearch, 'searchFieldsMediaSearch');
			$this->searchForTitleMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForTitleMediaSearch, 'searchForTitleMediaSearch');
			$this->searchForTextMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForTextMediaSearch, 'searchForTextMediaSearch');
			$this->searchForMetaMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForMetaMediaSearch, 'searchForMetaMediaSearch');
			$this->searchForImageMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForImageMediaSearch, 'searchForImageMediaSearch');
			$this->searchForAudioMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForAudioMediaSearch, 'searchForAudioMediaSearch');
			$this->searchForVideoMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForVideoMediaSearch, 'searchForVideoMediaSearch');
			$this->searchForOtherMediaSearch = we_base_request::_(we_base_request::BOOL, 'we_cmd', $this->searchForOtherMediaSearch, 'searchForOtherMediaSearch');
			$this->searchMediaSearch = we_base_request::_(we_base_request::RAW, 'we_cmd', $this->searchMediaSearch, 'searchMediaSearch');
			$this->locationMediaSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->locationMediaSearch, 'locationMediaSearch');
			$this->OrderMediaSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderMediaSearch, 'OrderMediaSearch');
			$this->setViewMediaSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setViewMediaSearch, 'setViewMediaSearch');
			$this->anzahlMediaSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlMediaSearch, 'anzahlMediaSearch');
			$this->anzahlMedialinksMediaSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlMedialinksMediaSearch, 'anzahlMedialinksMediaSearch');
			$this->searchstartMediaSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartMediaSearch, 'searchstartMediaSearch');
			$this->searchMediaSearch = array_merge(is_array($this->searchMediaSearch) ? $this->searchMediaSearch : []);
			$this->locationMediaSearch = array_merge(is_array($this->locationMediaSearch) ? $this->locationMediaSearch : []);
			$this->searchFieldsMediaSearch = array_merge(is_array($this->searchFieldsMediaSearch) ? $this->searchFieldsMediaSearch : []);
		}
		if(!$isWeCmd || $whichSearch === we_search_view::SEARCH_ADV){
			$this->anzahlAdvSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->anzahlAdvSearch, 'anzahlAdvSearch');
			$this->setViewAdvSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->setViewAdvSearch, 'setViewAdvSearch');
			$this->OrderAdvSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->OrderAdvSearch, 'OrderAdvSearch');
			$this->searchAdvSearch = we_base_request::_(we_base_request::RAW, 'we_cmd', $this->searchAdvSearch, 'searchAdvSearch');
			$this->locationAdvSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->locationAdvSearch, 'locationAdvSearch');
			$this->searchFieldsAdvSearch = we_base_request::_(we_base_request::STRING, 'we_cmd', $this->searchFieldsAdvSearch, 'searchFieldsAdvSearch');
			$this->search_tables_advSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->search_tables_advSearch, 'search_tables_advSearch');
			$this->searchstartAdvSearch = we_base_request::_(we_base_request::INT, 'we_cmd', $this->searchstartAdvSearch, 'searchstartAdvSearch');
			$this->searchAdvSearch = array_merge(is_array($this->searchAdvSearch) ? $this->searchAdvSearch : []);
			$this->locationAdvSearch = array_merge(is_array($this->locationAdvSearch) ? $this->locationAdvSearch : []);
			$this->searchFieldsAdvSearch = array_merge(is_array($this->searchFieldsAdvSearch) ? $this->searchFieldsAdvSearch : []);
		}

		$this->prepareModelForSearch($whichSearch);
	}

	public function prepareModelForSearch($whichSearch = ''){
		if(!$whichSearch){
			switch($this->activTab){
				case 1:
					$whichSearch = we_search_view::SEARCH_DOCS;
					break;
				case 2:
					$whichSearch = we_search_view::SEARCH_TMPL;
					break;
				case 3:
					$whichSearch = we_search_view::SEARCH_ADV;
					break;
				case 5:
					$whichSearch = we_search_view::SEARCH_MEDIA;
			}
		}

		switch($whichSearch){
			case we_search_view::SEARCH_DOCS:
				// process some SEARCH_DOCS specialties
				if(count(($tmpSearch = $this->searchDocSearch))){
					$this->searchFieldsDocSearch = $this->locationDocSearch = $this->searchDocSearch = [];
					foreach(['Text' => $this->searchForTextDocSearch, 'Title' => $this->searchForTitleDocSearch, 'Content' => $this->searchForContentDocSearch] as $field => $val){
						if($val){
							$this->searchFieldsDocSearch[] = $field;
							$this->locationDocSearch[] = 'CONTAIN';
							$this->searchDocSearch[] = $tmpSearch[0];
						}
					}
				}
				$this->searchForFieldDocSearch = ['text' => $this->searchForTextDocSearch,
					'title' => $this->searchForTitleDocSearch,
					'content' => $this->searchForContentDocSearch,
					'meta' => false,
				];

				// write current set
				$this->currentSearchTables = $this->searchTablesDocSearch = [FILE_TABLE];
				$this->currentSearchFields = $this->searchFieldsDocSearch;
				$this->currentSearchForField = $this->searchForFieldDocSearch;
				$this->currentLocation = $this->locationDocSearch;
				$this->currentSearch = $this->searchDocSearch;
				$this->currentFolderID = $this->folderIDDoc;
				$this->currentOrder = $this->OrderDocSearch;
				$this->currentSetView = $this->setViewDocSearch;
				$this->currentSearchstart = $this->searchstartDocSearch;
				$this->currentAnzahl = $this->anzahlDocSearch;
				break;
			case we_search_view::SEARCH_TMPL:
				if(count(($tmpSearch = $this->searchTmplSearch))){
					$this->searchFieldsTmplSearch = $this->locationTmplSearch = $this->searchTmplSearch = [];
					foreach(['Text' => $this->searchForTextTmplSearch, 'Content' => $this->searchForContentTmplSearch] as $field => $val){
						if($val){
							$this->searchFieldsTmplSearch[] = $field;
							$this->locationTmplSearch[] = 'CONTAIN';
							$this->searchTmplSearch[] = $tmpSearch[0];
						}
					}
				}
				$this->searchForFieldTmplSearch = ['text' => $this->searchForTextTmplSearch,
					'title' => false,
					'content' => $this->searchForContentTmplSearch,
					'meta' => false,
				];

				// write current set
				$this->currentSearchTables = $this->searchTablesTmplSearch = [TEMPLATES_TABLE];
				$this->currentSearchFields = $this->searchFieldsTmplSearch;
				$this->currentSearchForField = $this->searchForFieldTmplSearch;
				$this->currentLocation = $this->locationTmplSearch;
				$this->currentSearch = $this->searchTmplSearch;
				$this->currentFolderID = $this->folderIDTmpl;
				$this->currentOrder = $this->OrderTmplSearch;
				$this->currentSetView = $this->setViewTmplSearch;
				$this->currentSearchstart = $this->searchstartTmplSearch;
				$this->currentAnzahl = $this->anzahlTmplSearch;
				break;
			case we_search_view::SEARCH_MEDIA:
				// process some SEARCH_MEDIA specialties
				$this->searchForFieldMediaSearch = ['text' => $this->searchForTextMediaSearch,
					'title' => $this->searchForTitleMediaSearch,
					'content' => false,
					'meta' => $this->searchForMetaMediaSearch,
				];
				$this->searchForContentTypeMediaSearch = ['image' => $this->searchForImageMediaSearch,
					'audio' => $this->searchForAudioMediaSearch,
					'video' => $this->searchForVideoMediaSearch,
					'other' => $this->searchForOtherMediaSearch,
				];

				// write current set
				$this->currentSearchTables = $this->searchTablesMediaSearch = [FILE_TABLE];
				$this->currentSearchFields = $this->searchFieldsMediaSearch;
				$this->currentLocation = $this->locationMediaSearch;
				$this->currentSearch = $this->searchMediaSearch;
				$this->currentFolderID = $this->folderIDMedia;
				$this->currentOrder = $this->OrderMediaSearch;
				$this->currentSetView = $this->setViewMediaSearch;
				$this->currentSearchstart = $this->searchstartMediaSearch;
				$this->currentAnzahl = $this->anzahlMediaSearch;
				$this->currentAnzahlMedialinks = $this->anzahlMedialinksMediaSearch;
				$this->currentSearchForField = $this->searchForFieldMediaSearch;
				$this->currentSearchForContentType = $this->searchForContentTypeMediaSearch;
				break;
			case we_search_view::SEARCH_ADV:
				// process some SEARCH_ADV specialties
				$tmp = [// default db entry
					FILE_TABLE => 1,
					addTblPrefix('tblTemplates') => 0,
					addTblPrefix('tblObjectFiles') => 1,
					addTblPrefix('tblObject') => 0,
					addTblPrefix('tblversions') => 0,
				];

				$this->searchTablesAdvSearch = [];
				foreach($this->search_tables_advSearch as $k => $v){
					switch($k){
						case FILE_TABLE:
						case stripTblPrefix(FILE_TABLE): // in older predefined searches we have hardcoded tables without prefix)
							$tmp[FILE_TABLE] = $v;
							if($v && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
								$this->searchTablesAdvSearch[] = FILE_TABLE; // this is used in search: so check perms!
							}
							break;
						case defined('TEMPLATES_TABLE') ? TEMPLATES_TABLE : 'TEMPLATES_TABLE':
						case defined('TEMPLATES_TABLE') ? stripTblPrefix(FILE_TABLE) : 'TEMPLATES_TABLE':
							$tmp[TEMPLATES_TABLE] = $v;
							if($v && permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] !== we_base_constants::MODE_SEE){
								$this->searchTablesAdvSearch[] = TEMPLATES_TABLE;
							}
							break;
						case defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE':
						case defined('OBJECT_FILES_TABLE') ? stripTblPrefix(OBJECT_FILES_TABLE) : 'OBJECT_FILES_TABLE':
							$tmp[OBJECT_FILES_TABLE] = $v;
							if($v && defined('OBJECT_FILES_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
								$this->searchTablesAdvSearch[] = OBJECT_FILES_TABLE;
							}
							break;
						case defined('OBJECT_TABLE') ? OBJECT_TABLE : 'OBJECT_TABLE':
						case defined('OBJECT_TABLE') ? stripTblPrefix(OBJECT_TABLE) : 'OBJECT_TABLE':
							$tmp[OBJECT_TABLE] = $v;
							if($v && defined('OBJECT_TABLE') && permissionhandler::hasPerm('CAN_SEE_OBJECTS') && $_SESSION['weS']['we_mode'] !== we_base_constants::MODE_SEE){
								$this->searchTablesAdvSearch[] = OBJECT_TABLE;
							}
							break;
						case VERSIONS_TABLE:
						case stripTblPrefix(VERSIONS_TABLE):
							$tmp[VERSIONS_TABLE] = $v;
							if($v && permissionhandler::hasPerm('SEE_VERSIONS')){
								$this->searchTablesAdvSearch[] = VERSIONS_TABLE;
							}
							break;
					}
				}
				$this->search_tables_advSearch = $tmp;

				// write current set
				$this->currentSearchTables = $this->searchTablesAdvSearch;
				$this->currentSearchFields = $this->searchFieldsAdvSearch;
				$this->currentLocation = $this->locationAdvSearch;
				$this->currentSearch = $this->searchAdvSearch;
				$this->currentOrder = $this->OrderAdvSearch;
				$this->currentSetView = $this->setViewAdvSearch;
				$this->currentSearchstart = $this->searchstartAdvSearch;
				$this->currentAnzahl = $this->anzahlAdvSearch;
		}
	}

	public function setPredefinedSearch($tab = 3, $keyword = '', $tables = 0){
		// set activTab
		$this->activTab = $tab;

		// set SEARCH_ADV tables
		$this->search_tables_advSearch = [FILE_TABLE => 0,
			TEMPLATES_TABLE => 0,
			addTblPrefix('tblObjectFiles') => 0,
			addTblPrefix('tblObject') => 0,
			VERSIONS_TABLE => 0,
		];

		switch($tables){
			case 1:
				$this->search_tables_advSearch[FILE_TABLE] = 1;
				break;
			case 2:
				$this->search_tables_advSearch[TEMPLATES_TABLE] = 1;
				break;
			case 3:
				$this->search_tables_advSearch[addTblPrefix('tblObjectFiles')] = 1;
				break;
			case 4:
				$this->search_tables_advSearch[addTblPrefix('tblObject')] = 1;
				break;
			case 5:
				$this->search_tables_advSearch[VERSIONS_TABLE] = 1;
				break;
			default:
				$this->search_tables_advSearch[FILE_TABLE] = 1;
				$this->search_tables_advSearch[addTblPrefix('tblObjectFiles')] = 1;
		}

		// set searchfields
		switch($tables){ // FIXME: make fn tabToWhichsearch()
			case 1://Doc
				$this->searchForTextDocSearch = $this->searchForTitleDocSearch = $this->searchForContentDocSearch = 1; // FIXME: make this default

				if($keyword){
					$this->searchDocSearch = [$keyword, $keyword, $keyword];
					$this->searchFieldsDocSearch = ['Text', 'Title', 'Content'];
					$this->locationDocSearch = ['CONTAIN', 'CONTAIN', 'CONTAIN'];

					$this->searchAdvSearch = [$keyword];
					$this->searchFieldsAdvSearch = ['Content'];
					$this->locationAdvSearch = ['CONTAIN'];

					$this->searchMediaSearch[0] = $keyword;
					$this->locationMediaSearch[0] = 'CONTAIN';
					$this->searchFieldsMediaSearch[0] = 'keyword';
				}

				// FIXME: make this default
				$this->searchForTextMediaSearch = $this->searchForTitleMediaSearch = $this->searchForMetaMediaSearch = 1;
				$this->searchForImageMediaSearch = $this->searchForAudioMediaSearch = $this->searchForVideoMediaSearch = $this->searchForOtherMediaSearch = 1;
				break;
			case 2://Templ
				$this->searchForTextTmplSearch = $this->searchForContentTmplSearch = 1;

				if($keyword){
					$this->searchTmplSearch = $this->searchAdvSearch = [$keyword, $keyword];
					$this->searchFieldsTmplSearch = $this->searchFieldsAdvSearch = ['Text', 'Content'];
					$this->locationTmplSearch = $this->locationAdvSearch = ['CONTAIN', 'CONTAIN'];
				}
				break;
			case 3://Adv
			case 4:
			case 4:
				if($keyword){
					$this->searchAdvSearch = [$keyword];
					$this->searchFieldsAdvSearch = ['Text'];
					$this->locationAdvSearch = ['CONTAIN'];
				}
				break;
		}
	}

}
