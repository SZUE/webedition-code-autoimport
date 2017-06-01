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
/* a class for handling templates */
class we_object extends we_document{
	const ELEMENT_LENGHT = 'length';
	const ELEMENT_TYPE = 'dtype';
	const ELEMENT_WIDTH = 'width';
	const ELEMENT_HEIGHT = 'height';
	const ELEMENT_CLASS = 'class';
	const ELEMENT_MAX = 'max';
	const ELEMENT_DEFAULT = 'default';
	const QUERY_PREFIX = 'object_';

	var $Users = ''; // Default Owners
	var $UsersReadOnly = ''; // For DefaultOwners
	var $RestrictUsers = '';
	var $Workspaces = '';
	var $DefaultWorkspaces = '';
	var $WorkspaceFlag = 1;
	var $Templates = '';
	var $SerializedArray = []; // #3931
	var $DefaultValues = '';
	protected $isInApp = false; // runtime variable which allows to construct Classes from within Apps
	protected static $urlFields = ['urlfield1' => 64,
		'urlfield2' => 64,
		'urlfield3' => 64,
	];
	protected static $urlUnique = ['urlunique' => 16,
	];

	public function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'WorkspaceFlag', 'RestrictUsers', 'UsersReadOnly', 'Text', 'SerializedArray', 'Templates', 'Workspaces', 'DefaultWorkspaces', 'ID', 'Users', 'Category', 'DefaultCategory', 'DefaultText', 'DefaultValues', 'DefaultTitle', 'DefaultKeywords', 'DefaultUrl', 'DefaultUrlfield0', 'DefaultUrlfield1', 'DefaultUrlfield2', 'DefaultUrlfield3', 'DefaultTriggerID', 'DefaultDesc', 'CSS');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_WORKSPACE, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT); // ,we_base_constants::WE_EDITPAGE_PREVIEW
		}
		$this->setElement('Charset', DEFAULT_CHARSET, 'attrib');
		$this->Table = OBJECT_TABLE;
		$this->Published = PHP_INT_MAX;
		$this->CSS = '';
	}

// setter for runtime variable isInApp which allows to construct Classes from within Apps
// do not access this variable directly, in later WE Versions, it will be protected

	function setIsInApp($isInApp){
		$this->isInApp = $isInApp;
	}

// getter for runtime variable isInApp which allows to construct Classes from within Apps
// do not access this variable directly, in later WE Versions, it will be protected

	function getIsInApp(){
		return $this->isInApp;
	}

	function save($resave = 0, $skipHook = 0){
		if(!$this->checkIfPathOk()){
			return false;
		}

		$this->ModDate = time();
		$this->ModifierID = !isset($GLOBALS['we']['Scheduler_active']) && isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;

		$this->saveToDB();

		$GLOBALS['we_responseText'] = g_l('weClass', '[response_save_ok]');
		$GLOBALS['we_responseTextType'] = we_base_util::WE_MESSAGE_NOTICE;

		if(!$this->OldPath || ($this->OldPath != $this->Path)){
			$fID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $this->DB_WE->escape($this->OldPath) . '"', '', $this->DB_WE);
			$pID = intval(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . str_replace("\\", "/", dirname($this->Path)) . '"', '', $this->DB_WE));
			$cf = new we_class_folder();
			$cf->initByID($fID, OBJECT_FILES_TABLE);
			$cf->Text = $this->Text;
			$cf->Filename = $this->Text;
			$cf->setParentID($pID);
			$cf->Path = $cf->getPath();
			$cf->TableID = $this->ID;
			$cf->we_save(true);
			$cf->modifyChildrenPath();
		}

		$this->OldPath = $this->Path; // reset oldPath
		if(empty($this->isInApp)){// allows to save Classes from within WE-Apps
			$GLOBALS['we_JavaScript'][] = ['reload_editpage'];
			$GLOBALS['we_JavaScript'][] = ['setEditorDocumentId', $this->ID];
			$GLOBALS['we_JavaScript'][] = $this->getUpdateTreeScript(true, null, true);
			$GLOBALS['we_JavaScript'][] = we_main_headermenu::getMenuReloadCode('', true);
		}
	}

	function saveToDB(){
		$arrt = ['WorkspaceFlag' => $this->WorkspaceFlag,
			//	Save charsets in defaultvalues
//	charset must be in other namespace -> for header !!!
			'elements' => ['Charset' => ['dat' => $this->getElement('Charset')]],
		];

		$this->wasUpdate = $this->ID > 0;

		if(($def = $this->getElement('Defaultanzahl'))){
			$this->DefaultText = '';

			for($i = 0; $i <= $def; $i++){
				$was = 'DefaultText_' . $i;
				if(($dat = $this->getElement($was))){
					foreach(self::$urlUnique as $key => $len){
						if(stristr($dat, $key)){
							$unique = $this->getElement($key . '_' . $i);
							$dat = '%' . str_replace('%', '', $dat) . (($unique > 0) ? $unique : $len) . '%';
							$this->setElement($was, $dat, 'defaultText');
						}
						$this->DefaultText .= $dat;
					}
				}
			}
		}
		if(($def = $this->getElement('DefaultanzahlUrl'))){
//FIXME: make this json - this format is totally nonsense
			$this->DefaultUrl = '';
			$allFields = array_merge(self::$urlUnique, self::$urlFields);
			for($i = 0; $i <= $def; $i++){
				$was = 'DefaultUrl_' . $i;

				if(($dat = $this->getElement($was))){
					foreach($allFields as $key => $len){
						if(stristr($dat, $key)){
							$unique = $this->getElement($key . '_' . $i);
							$dat = '%' . str_replace('%', '', $dat) . (($unique > 0) ? $unique : $len) . '%';
						}
					}
					$this->setElement($was, $dat);
					$this->DefaultUrl .= $dat;
				}
			}
		}


		if(!$this->wasUpdate){//create table, add element to tblObject
			$q = ['OF_ID' => 'INT unsigned NOT NULL',];

			$indexe = ['PRIMARY KEY (OF_ID)'];

			$arrt['WE_CSS_FOR_CLASS'] = $this->CSS;
			$this->DefaultValues = we_serialize($arrt, SERIALIZE_JSON);
			$this->DefaultTitle = ($tmp = $this->getElement('title')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultDesc = ($tmp = $this->getElement('desc')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultKeywords = ($tmp = $this->getElement('keywords')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultUrlfield0 = ($tmp = $this->getElement('urlfield0')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultUrlfield1 = ($tmp = $this->getElement('urlfield1')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($this->getElement('urlfield1')) : '_';
			$this->DefaultUrlfield2 = ($tmp = $this->getElement('urlfield2')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultUrlfield3 = ($tmp = $this->getElement('urlfield3')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
			$this->DefaultTriggerID = ($tmp = $this->getElement('triggerid')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '0';


			$this->DefaultCategory = $this->Category;
			$this->i_savePersistentSlotsToDB();

			$ctable = OBJECT_X_TABLE . intval($this->ID);

			$this->DB_WE->delTable($ctable);
			$this->DB_WE->addTable($ctable, $q, $indexe);

			$q = $indexe = [];
			$this->wasUpdate = true;
		}

		$we_sort = $this->getElement('we_sort');
		asort($we_sort, SORT_NUMERIC);
		$this->setElement('we_sort', $we_sort);

		$ctable = OBJECT_X_TABLE . intval($this->ID);
		$tableInfo = $this->DB_WE->metadata($ctable, we_database_base::META_NAME);
		$q = $regs = [];
		$fieldsToDelete = array_intersect($this->getElement('felderloeschen', 'dat', []), $tableInfo);
		foreach($fieldsToDelete as $cur){
			$q[] = ' DROP `' . $cur . '` ';
		}

		$neu = $this->getElement('neuefelder', 'dat', []);

		$lastElement = 'OF_ID';
		foreach(array_keys($we_sort) as $id){
			$cur = $this->getElement('wholename' . $id, 'dat');
			$newType = $this->getElement($cur . self::ELEMENT_TYPE);
			$nam = $newType . '_' . $this->getElement($cur);

			if(in_array($cur, $neu)){
				$q[] = ' ADD `' . $nam . '` ' . $this->switchtypes($cur) . ' AFTER `' . $lastElement . '`';
			} else {
				//change from object is indexed to unindexed
				if((strpos($cur, self::QUERY_PREFIX) === 0) && ($newType . '_' != self::QUERY_PREFIX)){
					$q[] = ' DROP KEY `' . $cur . '` ';
				}

				$q[] = ' CHANGE `' . $cur . '` `' . $nam . '` ' . $this->switchtypes($cur) . ' AFTER `' . $lastElement . '`';
			}
			//add index for complex queries
			if((strpos($cur, self::QUERY_PREFIX) !== 0) && ($newType . '_' == self::QUERY_PREFIX)){
				$q[] = 'ADD INDEX (`' . $nam . '`) ';
			}
			$lastElement = $nam;

			$arrt[$nam] = array_filter([
				'default' => $this->getElement($cur . 'default'),
				'defaultThumb' => $this->getElement($cur . 'defaultThumb'),
				'autobr' => $this->getElement($cur . 'autobr'),
				'defaultdir' => $this->getElement($cur . 'defaultdir'),
				'rootdir' => $this->getElement($cur . 'rootdir'),
				'dhtmledit' => $this->getElement($cur . 'dhtmledit'),
				'showmenus' => $this->getElement($cur . 'showmenus'),
				'commands' => $this->getElement($cur . 'commands'),
				'contextmenu' => $this->getElement($cur . 'contextmenu'),
				self::ELEMENT_HEIGHT => $this->getElement($cur . 'height', 'dat', 200),
				self::ELEMENT_WIDTH => $this->getElement($cur . self::ELEMENT_WIDTH, 'dat', 618),
				'bgcolor' => $this->getElement($cur . 'bgcolor'),
				self::ELEMENT_CLASS => $this->getElement($cur . 'class'),
				'max' => $this->getElement($cur . 'max'),
				'cssClasses' => $this->getElement($cur . 'cssClasses'),
				'fontnames' => $this->getElement($cur . 'fontnames'),
				'fontsizes' => $this->getElement($cur . 'fontsizes'),
				'formats' => $this->getElement($cur . 'formats'),
				'tinyparams' => $this->getElement($cur . 'tinyparams'),
				'templates' => $this->getElement($cur . 'templates'),
				'xml' => $this->getElement($cur . 'xml'),
				'removefirstparagraph' => $this->getElement($cur . 'removefirstparagraph'),
				'forbidhtml' => $this->getElement($cur . 'forbidhtml'),
				'forbidphp' => $this->getElement($cur . 'forbidphp'),
				'inlineedit' => $this->getElement($cur . 'inlineedit'),
				'users' => $this->getElement($cur . 'users'),
				'required' => $this->getElement($cur . 'required'),
				'editdescription' => $this->getElement($cur . 'editdescription'),
				'int' => $this->getElement($cur . 'int'),
				'intID' => $this->getElement($cur . 'intID'),
				'hreftype' => $this->getElement($cur . 'hreftype'),
				'hrefdirectory' => $this->getElement($cur . 'hrefdirectory', 'dat', 'false'),
				'hreffile' => $this->getElement($cur . 'hreffile', 'dat', 'true'),
				'shopcatField' => $this->getElement($cur . 'shopcatField'),
				'shopcatShowPath' => $this->getElement($cur . 'shopcatShowPath'),
				'shopcatRootdir' => $this->getElement($cur . 'shopcatRootdir'),
				'shopcatLimitChoice' => $this->getElement($cur . 'shopcatLimitChoice'),
				'uniqueID' => $this->SerializedArray[$cur]['uniqueID'] ?: md5(uniqid(__FILE__, true)),
			]);
			if($this->isVariantField($cur) && $this->getElement($cur . 'variant') == 1){
				$arrt[$nam]['variant'] = 1;
			} else if($this->issetElement($cur . 'variant')){
				$this->delElement($cur . 'variant');
			}

			for($f = 0; $f <= $this->getElement($cur . 'count', 'dat', 0); ++$f){
				if($this->issetElement($cur . 'defaultkey' . $f)){
					if((!isset($arrt[$nam]['meta'])) || (!is_array($arrt[$nam]['meta']))){
						$arrt[$nam]['meta'] = [];
					}

					$val = $this->getElement($cur . 'defaultvalue' . $f);
					$val = ($val != $cur . 'defaultvalue' . $f ? $val : '');
					if($newType == we_objectFile::TYPE_MULTIOBJECT){
						$arrt[$nam]['meta'][] = $val;
					} else {
						$arrt[$nam]['meta'][$this->getElement($cur . 'defaultkey' . $f)] = $val;
					}
				}
			}
		}

		$this->DefaultCategory = $this->Category;

		$this->DefaultTitle = $this->getElement(($tmp = $this->getElement('title')) . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp);
		$this->DefaultDesc = $this->getElement(($tmp = $this->getElement('desc')) . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp);
		$this->DefaultKeywords = $this->getElement(($tmp = $this->getElement('keywords')) . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp);


		$this->DefaultUrlfield0 = ($tmp = $this->getElement('urlfield0')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
		$this->DefaultUrlfield1 = ($tmp = $this->getElement('urlfield1')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
		$this->DefaultUrlfield2 = ($tmp = $this->getElement('urlfield2')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
		$this->DefaultUrlfield3 = ($tmp = $this->getElement('urlfield3')) ? $this->getElement($tmp . self::ELEMENT_TYPE) . '_' . $this->getElement($tmp) : '_';
//$this->DefaultTriggerID = ($tmp = $this->getElement('triggerid')) ? $this->getElement($tmp.'dtype').'_'.$this->getElement($tmp) : '0';

		$arrt['WE_CSS_FOR_CLASS'] = $this->CSS;

		$this->DefaultValues = we_serialize($arrt, SERIALIZE_JSON);

		$variant_field = 'variant_' . we_base_constants::WE_VARIANTS_ELEMENT_NAME;

		$exists = $this->DB_WE->isColExist($ctable, $variant_field);

		if($this->hasVariantFields()){
			if(!$exists){
				$this->DB_WE->addCol($ctable, $variant_field, 'TEXT NOT NULL');
			}
		} else {
			if($exists){
				$this->DB_WE->delCol($ctable, $variant_field);
			}
		}

		$q = array_filter($q);
		if($q){
			$this->DB_WE->query('ALTER TABLE ' . $ctable . ' ' . implode(',', $q));
		}

		$this->i_savePersistentSlotsToDB();

		unset($this->elements);
		$this->i_getContentData();
//$this->initByID($this->ID,$this->Table);
	}

	private function switchtypes($name){
		switch($this->getElement($name . self::ELEMENT_TYPE, 'dat')){
			case we_objectFile::TYPE_META:
				return ' VARCHAR(' . (($this->getElement($name . 'length', 'dat') > 0 && ($this->getElement($name . 'length', 'dat') < 255)) ? $this->getElement($name . 'length', 'dat') : 255) . ') NOT NULL ';
			case we_objectFile::TYPE_DATE:
				return ' INT unsigned NOT NULL ';
			case we_objectFile::TYPE_INPUT:
				return ' VARCHAR(' . (($this->getElement($name . 'length', 'dat') > 0 && ($this->getElement($name . 'length', 'dat') < 4096)) ? $this->getElement($name . 'length', 'dat') : 255) . ') NOT NULL ';
			case we_objectFile::TYPE_COUNTRY:
			case we_objectFile::TYPE_LANGUAGE:
				return ' CHAR(2) NOT NULL ';
			case we_objectFile::TYPE_LINK:
			case we_objectFile::TYPE_HREF:
				return ' TINYTEXT NOT NULL ';
			case we_objectFile::TYPE_TEXT:
				return ' LONGTEXT NOT NULL ';
			case we_objectFile::TYPE_IMG:
			case we_objectFile::TYPE_FLASHMOVIE:
			case we_objectFile::TYPE_QUICKTIME:
			case we_objectFile::TYPE_BINARY:
			case we_objectFile::TYPE_COLLECTION:
				return ' INT unsigned DEFAULT "0" NOT NULL ';
			case we_objectFile::TYPE_CHECKBOX:
				return ' TINYINT unsigned DEFAULT "' . ($this->getElement($name . 'default', 'dat') == 1 ? '1' : '0') . '" NOT NULL ';
			case we_objectFile::TYPE_INT:
				return ' INT DEFAULT NULL ';
			case we_objectFile::TYPE_FLOAT:
				return ' DOUBLE DEFAULT NULL ';
			case we_objectFile::TYPE_OBJECT:
				return ' INT unsigned DEFAULT "0" NOT NULL ';
			case we_objectFile::TYPE_MULTIOBJECT:
				return ' TEXT NOT NULL ';
			case we_objectFile::TYPE_SHOPVAT:
				return ' decimal(4,2) default NOT NULL';
			case we_objectFile::TYPE_SHOPCATEGORY:
				return ' TEXT NOT NULL';
			default:
				return '';
		}
	}

	function getPath(){
		return rtrim($this->getParentPath(), '/') . '/' . $this->Text;
	}

	function ModifyPathInformation($parentID){
		$this->setParentID($parentID);
		$this->Path = $this->getPath();
		$this->wasUpdate = true;
		$this->i_savePersistentSlotsToDB('Text,Path,ParentID');
	}

	function setSort(){
		if($this->issetElement('we_sort')){
			return;
		}
		$ctable = OBJECT_X_TABLE . intval($this->ID);
		$tableInfo = $this->DB_WE->metadata($ctable, we_database_base::META_NAME);
		$sort = [];
		$i = 0;
		foreach($tableInfo as $name){
			list($type, $name) = explode('_', $name, 2);
			switch($type){
				case 'OF':
				case 'variant':
					break;
				default:
					$sort[str_replace('.', '', uniqid($name))] = $i++;
			}
		}
		$this->setElement('we_sort', $sort);
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === "save_document"){
			$this->save();
			$GLOBALS['we_JavaScript'] = '';
			$GLOBALS['we_responseText'] = sprintf(g_l('weClass', '[response_save_ok]'), $this->Path);
			$GLOBALS['we_responseTextType'] = we_base_util::WE_MESSAGE_NOTICE;
			return new we_editor_save($this);
		}
		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
			case we_base_constants::WE_EDITPAGE_WORKSPACE:
				return new we_editor_properties($this);
			case we_base_constants::WE_EDITPAGE_INFO:
				return new we_editor_info_object($this);
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return new we_editor_contentobject($this);
		}
	}

	private function moveEntryAtClass($identifier, $move){
		$sort = $this->getElement('we_sort');
		$pos = $sort[$identifier];

		$newPos = array_search($pos + $move, $sort);
		if($newPos !== false){
			$sort[$identifier] = $sort[$newPos];
			$sort[$newPos] = $pos;
			asort($sort, SORT_NUMERIC);
			$this->setElement('we_sort', $sort);
		}
	}

	function downEntryAtClass($identifier){
		$this->moveEntryAtClass($identifier, 1);
	}

	function upEntryAtClass($identifier){
		$this->moveEntryAtClass($identifier, -1);
	}

	function addEntryToClass($identifier, $after = false){
		$sort = $this->getElement('we_sort');
		$uid = uniqid();

		$this->setElement($uid, '');
		$this->setElement($uid . self::ELEMENT_LENGHT, '');
		$this->setElement($uid . self::ELEMENT_TYPE, '');
		$this->setElement($uid . self::ELEMENT_WIDTH, 618);
		$this->setElement($uid . self::ELEMENT_HEIGHT, 200);
		$this->setElement($uid . self::ELEMENT_CLASS, '');
		$this->setElement($uid . self::ELEMENT_MAX, '');
		$this->setElement('wholename' . $identifier, $uid);
		$nf = $this->getElement('neuefelder', 'dat', []);
		$nf[] = $uid;
		$this->setElement('neuefelder', $nf);

		if($after && isset($sort[$after])){
			$pos = $sort[$after];

			foreach($sort as &$identpos){
				if($identpos > $pos){
					$identpos = $identpos + 1;
				}
			}
			$sort[$identifier] = $pos + 1;
		} else {
			$sort[$identifier] = count($sort);
		}
		asort($sort, SORT_NUMERIC);
		$this->setElement('we_sort', $sort);
	}

	function removeEntryFromClass($identifier){
		$sort = $this->getElement('we_sort', 'dat');

		$uid = $this->getElement('wholename' . $identifier);

		if(($pos = array_search($uid, ($nf = $this->getElement('neuefelder', 'dat', [])))) !== false){
			unset($nf[$pos]);
			$this->setElement('neuefelder', $nf);
		} else {
			$er = $this->getElement('felderloeschen', 'dat', []);
			$er[] = $uid;
			$this->setElement('felderloeschen', $er);
		}

		$this->delElement('wholename' . $identifier);
		$this->delElement($uid);
		$this->delElement($uid . self::ELEMENT_LENGHT);
		$this->delElement($uid . self::ELEMENT_TYPE);
		$this->delElement($uid . self::ELEMENT_HEIGHT);
		$this->delElement($uid . self::ELEMENT_WIDTH);
		$this->delElement($uid . self::ELEMENT_DEFAULT);
		$this->delElement($uid . self::ELEMENT_CLASS);
		$this->delElement($uid . self::ELEMENT_MAX);


### move elements ####
		$pos = $sort[$identifier];
		unset($sort[$identifier]);
		foreach($sort as &$identpos){
			if($identpos > $pos){
				$identpos = $identpos - 1;
			}
		}
### end move elements ####
		asort($sort, SORT_NUMERIC);
		$this->setElement('we_sort', ($sort ?: []));
	}

	function downMetaAtClass($name, $i){
		$temp = $this->getElement($name . 'defaultkey' . ($i + 1));
		$this->elements[$name . 'defaultkey' . ($i + 1)]['dat'] = $this->elements[$name . 'defaultkey' . ($i)]['dat'];
		$this->elements[$name . 'defaultkey' . ($i)]['dat'] = $temp;

		$temp = $this->elements[$name . 'defaultvalue' . ($i + 1)]['dat'];
		$this->elements[$name . "defaultvalue" . ($i + 1)]["dat"] = $this->elements[$name . "defaultvalue" . ($i)]['dat'];
		$this->elements[$name . "defaultvalue" . ($i)]["dat"] = $temp;
	}

	function upMetaAtClass($name, $i){
		$temp = $this->elements[$name . 'defaultkey' . ($i - 1)]['dat'];
		$this->elements[$name . "defaultkey" . ($i - 1)]["dat"] = $this->elements[$name . "defaultkey" . ($i)]["dat"];
		$this->elements[$name . "defaultkey" . ($i)]["dat"] = $temp;

		$temp = $this->elements[$name . 'defaultvalue' . ($i - 1)]['dat'];
		$this->elements[$name . "defaultvalue" . ($i - 1)]["dat"] = $this->elements[$name . "defaultvalue" . ($i)]["dat"];
		$this->elements[$name . "defaultvalue" . ($i)]["dat"] = $temp;
	}

	function addMetaToClass($name, $pos){
// get from request
		$amount = we_base_request::_(we_base_request::INT, "amount_insert_meta_at_class_" . $name . $pos, 1);

// set new amount
		$cnt = $this->getElement($name . "count") + $amount;
		$this->setElement($name . "count", $cnt);

// move elements - add new elements
		for($i = $cnt; 0 <= $i; $i--){

			if(($pos + $amount) < $i){// move existing fields
				$this->setElement($name . "defaultkey" . $i, ($this->getElement($name . "defaultkey" . ($i - $amount))));
				$this->setElement($name . "defaultvalue" . $i, ($this->getElement($name . "defaultvalue" . ($i - $amount))));
			} else if($pos < $i && $i <= ($pos + $amount)){ // add new fields
				$this->setElement($name . "defaultkey" . $i, "");
				$this->setElement($name . "defaultvalue" . $i, "");
			}
		}
	}

	function removeMetaFromClass($name, $nr){
### move elements ####
		$cnt = $this->getElement($name . "count");
		for($i = 0; $i < $cnt; $i++){
			if($i >= $nr){
				$this->setElement($name . "defaultkey" . $i, ($this->getElement($name . "defaultkey" . ($i + 1))));
				$this->setElement($name . "defaultvalue" . $i, ($this->getElement($name . "defaultvalue" . ($i + 1))));
			}
		}
		$this->setElement($name . "defaultkey" . $i, "");
		$this->setElement($name . "defaultvalue" . $i, "");
### end move elements ####

		$this->setElement($name . "count", max($cnt - 1, 0));
	}

	function getEmptyDefaultFields(){
		return '<div style="display:none">' .
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[title]" id="empty_' . $this->Name . '_input[title]"/>' .
// description
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[desc]" id="empty_' . $this->Name . '_input[desc]"/>' .
// keywords
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[keywords]" id="empty_' . $this->Name . '_input[keywords]"/>' .
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[urlfield0]" id="empty_' . $this->Name . '_input[urlfield0]"/>' .
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[urlfield1]" id="empty_' . $this->Name . '_input[urlfield1]"/>' .
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[urlfield2]" id="empty_' . $this->Name . '_input[urlfield2]"/>' .
			'<input type="radio" value="0" name="we_' . $this->Name . '_input[urlfield3]" id="empty_' . $this->Name . '_input[urlfield3]"/>' .
			'</div>';
	}

	function getFieldHTML(we_base_jsCmd $jsCmd, $name, $identifier){
		$type = $this->getElement($name . self::ELEMENT_TYPE, "dat") ?: we_objectFile::TYPE_INPUT;
		$content = '<tr>
			<td  class="weMultiIconBoxHeadline" style="width:100px;vertical-align:top" >' . g_l('weClass', '[name]') . '</td>
			<td  class="defaultfont" style="vertical-align:top">';

		switch($type){
			case we_objectFile::TYPE_OBJECT:
				$regs = $vals = [];
				$all = $this->DB_WE->table_names(OBJECT_X_TABLE . '%');
				$count = 0;
				while($count < count($all)){
					if($all[$count]["table_name"] != OBJECT_FILES_TABLE && $all[$count]["table_name"] != OBJECT_FILES_TABLE){
						if(preg_match('/^(.+)_(\d+)$/', $all[$count]["table_name"], $regs)){
							if($this->ID != $regs[2]){
								if(($path = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $regs[2], '', $this->DB_WE))){
									$vals[$regs[2]] = $path;
								}
							}
						}
					}
					$count++;
				}
				asort($vals);
				$content .= we_html_tools::htmlSelect("we_" . $this->Name . "_input[$name]", $vals, 1, $this->getElement($name, "dat"), "", ['onchange' => 'if(this.form.elements[\'we_' . $this->Name . '_input[' . $name . 'default]' . '\']){this.form.elements[\'we_' . $this->Name . '_input[' . $name . 'default]' . '\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'object_change_entry_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . $identifier . '\')'], "value", 388);
				break;
			case we_objectFile::TYPE_SHOPVAT:
			case we_objectFile::TYPE_SHOPCATEGORY:
				$foo = $type == we_objectFile::TYPE_SHOPCATEGORY ? WE_SHOP_CATEGORY_FIELD_NAME : WE_SHOP_VAT_FIELD_NAME;
				$content .= we_html_element::htmlHidden("we_" . $this->Name . "_input[$name]", $foo) .
					we_html_tools::htmlTextInput("tmp" . $foo, 40, $foo, 52, ' readonly="readonly" disabled="disabled"', "text", 388);
				break;
			default:
				$foo = $this->getElement($name, "dat") ?: g_l('modules_object', '[new_field]');
				$content .= we_html_tools::htmlTextInput("we_" . $this->Name . "_input[$name]", 40, $foo, 52, ' oldValue="' . $foo . '" onBlur="we_checkObjFieldname(this);" onchange="_EditorFrame.setEditorIsHot(true);"', "text", 388);
		}

		$content .= '</td></tr>' .
			'<tr><td class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('global', '[description]') . '</td><td>' .
			$this->htmlTextArea("we_" . $this->Name . "_input[" . $name . "editdescription]", 3, 40, $this->getElement($name . "editdescription"), ['onchange' => '_EditorFrame.setEditorIsHot(true)',
				'style' => 'width: 388px;']) .
			'</td></tr>' .
//type
			'<tr><td class="weMultiIconBoxHeadlineThin"  style="width:100px;vertical-align:top">' . g_l('modules_object', '[type]') . '</td>
		<td class="defaultfont"  style="vertical-align:top">';

		$val = [we_objectFile::TYPE_INPUT => g_l('modules_object', '[input_field]'),
			we_objectFile::TYPE_TEXT => g_l('modules_object', '[textarea_field]'),
			we_objectFile::TYPE_DATE => g_l('modules_object', '[date_field]'),
			we_objectFile::TYPE_IMG => g_l('modules_object', '[img_field]'),
			we_objectFile::TYPE_CHECKBOX => g_l('modules_object', '[checkbox_field]'),
			we_objectFile::TYPE_INT => g_l('modules_object', '[int_field]'),
			we_objectFile::TYPE_FLOAT => g_l('modules_object', '[float_field]'),
			we_objectFile::TYPE_META => g_l('modules_object', '[meta_field]'),
			we_objectFile::TYPE_LINK => g_l('modules_object', '[link_field]'),
			we_objectFile::TYPE_HREF => g_l('modules_object', '[href_field]'),
			we_objectFile::TYPE_BINARY => g_l('modules_object', '[binary_field]'),
			we_objectFile::TYPE_FLASHMOVIE => g_l('modules_object', '[flashmovie_field]'),
			we_objectFile::TYPE_COUNTRY => g_l('modules_object', '[country_field]'),
			we_objectFile::TYPE_LANGUAGE => g_l('modules_object', '[language_field]'),
			we_objectFile::TYPE_OBJECT => g_l('modules_object', '[objectFile_field]'),
			we_objectFile::TYPE_MULTIOBJECT => g_l('modules_object', '[multiObjectFile_field]'),
			we_objectFile::TYPE_COLLECTION => g_l('modules_object', '[collection_field]'),
		];
		if(defined('SHOP_ORDER_TABLE')){
			$val[we_objectFile::TYPE_SHOPVAT] = g_l('modules_object', '[shopVat_field]');
			$val[we_objectFile::TYPE_SHOPCATEGORY] = g_l('modules_object', '[shopCat_field]');
		}
		$content .= we_html_tools::htmlSelect("we_" . $this->Name . "_input[" . $name . self::ELEMENT_TYPE . ']', $val, 1, $type, "", ['onchange' => "if(this.form.elements['we_" . $this->Name . '_input[' . $name . 'default]' . "']){this.form.elements['we_" . $this->Name . '_input[' . $name . 'default]' . "'].value='' };_EditorFrame.setEditorIsHot(true);we_cmd('object_change_entry_at_class','" . $GLOBALS['we_transaction'] . "','" . $identifier . "');"], "value", 388) .
			'</td></tr>';

		switch($type){
			case we_objectFile::TYPE_SHOPVAT:
			case we_objectFile::TYPE_SHOPCATEGORY:
			case we_objectFile::TYPE_FLOAT:
			case we_objectFile::TYPE_TEXT:
			case we_objectFile::TYPE_COUNTRY:
			case we_objectFile::TYPE_LANGUAGE:
			case we_objectFile::TYPE_IMG:
			case we_objectFile::TYPE_BINARY:
			case we_objectFile::TYPE_FLASHMOVIE:
			case we_objectFile::TYPE_QUICKTIME:
			case we_objectFile::TYPE_DATE:
			case we_objectFile::TYPE_META:
			case we_objectFile::TYPE_OBJECT:
			case we_objectFile::TYPE_LINK:
			case we_objectFile::TYPE_HREF:
			case we_objectFile::TYPE_CHECKBOX:
			case we_objectFile::TYPE_MULTIOBJECT:
			case we_objectFile::TYPE_COLLECTION:
				break;
			case we_objectFile::TYPE_INT:
				$values = [
					g_l('modules_object', '[int][signed]') => we_html_tools::OPTGROUP,
					'TINYINT' => '[-128 .. 127] (tiny)',
					'SMALLINT' => '[-32.768 .. 32.767] (small)',
					'MEDIUMINT' => '[-8.388.608 .. 8.388.607] (medium)',
					'INT' => '[-2.147.483.648 .. 2.147.483.647] (int)',
					'BIGINT' => '[-9.223.372.036.854.775.808 .. 9.223.372.036.854.775.807] (big)',
					g_l('modules_object', '[int][unsigned]') => we_html_tools::OPTGROUP,
					'TINYINT_U' => '[0 .. 255] (tiny)',
					'SMALLINT_U' => '[0 .. 65.535] (small)',
					'MEDIUMINT_U' => '[0 .. 16.777.215] (medium)',
					'INT_U' => '[0 .. 4.294.967.295] (int)',
					'BIGINT_U' => '[0 .. 18.446.744.073.709.551.615] (big)',
				];
				$sel = $this->getElement($name . "typeLen", "dat");
				$content .= '<tr style="vertical-align:top"><td style="width:100px;" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[range][int]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlSelect('we_' . $this->Name . '_input[' . $name . 'typeLen]', $values, 1, $sel ? $sel : 'INT', false, ['onchange' => "_EditorFrame.setEditorIsHot(true);",
						'width' => '388px']) .
					'</td></tr>';
				break;
			default:
// Length
				$maxLengthVal = 255;
				$content .= '<tr style="vertical-align:top"><td class="weMultiIconBoxHeadlineThin" style="width:100px;vertical-align:top">' . g_l('modules_object', '[length]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlTextInput('we_' . $this->Name . "_input[" . $name . self::ELEMENT_LENGHT . ']', 10, ($this->getElement($name . "length", "dat") > 0 && ($this->getElement($name . "length", "dat") < ($maxLengthVal + 1)) ? $this->getElement($name . "length", "dat") : $maxLengthVal), ($type == we_objectFile::TYPE_INT ? 2 : 4), 'onchange="_EditorFrame.setEditorIsHot(true);" weType="weObject_' . $type . '_length"', "text", 388) .
					'</td></tr>';
		}

		switch($type){
			case we_objectFile::TYPE_MULTIOBJECT:
				$content .= '<tr><td class="weMultiIconBoxHeadlineThin" style="width:100px;vertical-align:top" >' . g_l('contentTypes', '[object]') . '</td><td class="defaultfont" style="vertical-align:top">';
				$vals = [];
				$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
				$count = 0;
				while($count < count($all)){
					if($all[$count]['table_name'] != OBJECT_FILES_TABLE && $all[$count]['table_name'] != OBJECT_FILES_TABLE){
						if(preg_match('/^(.+)_(\d+)$/', $all[$count]['table_name'], $regs)){
							if(($path = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($regs[2]), '', $this->DB_WE))){
								$vals[$regs[2]] = $path;
							}
						}
					}
					$count++;
				}
				asort($vals);
				if($this->getElement($name . 'class') === ''){
					$this->setElement($name . 'class', array_shift(array_flip($vals)));
				}
				$content .= we_html_tools::htmlSelect('we_' . $this->Name . '_' . we_objectFile::TYPE_MULTIOBJECT . '[' . $name . 'class]', $vals, 1, $this->getElement($name . 'class', 'dat'), '', [
						'onchange' => "if(this.form.elements['we_" . $this->Name . '_input[' . $name . 'default]' . "']){this.form.elements['we_" . $this->Name . '_input[' . $name . 'default]' . "'].value='';};_EditorFrame.setEditorIsHot(true);we_cmd('object_change_multiobject_at_class','" . $GLOBALS['we_transaction'] . "','" . $identifier . "','" . $name . "')"], "value", 388) .
					'</td></tr>
<tr style="vertical-align:top">
	<td style="width:100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[max_objects]') . '</td>
	<td class="defaultfont"><nobr>' . we_html_tools::htmlTextInput("we_" . $this->Name . '_' . we_objectFile::TYPE_MULTIOBJECT . '[' . $name . "max]", 5, $this->getElement($name . 'max', 'dat'), 3, "onchange=\"we_cmd('object_reload_entry_at_class','','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "');\"", "text", 50) . ' (' . g_l('modules_object', '[no_maximum]') . ')</nobr></td>
</tr>
<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td><td class="defaultfont"><table>';

				if(!$this->issetElement($name . 'count')){
					$this->setElement($name . 'count', 0);
				}
				for($f = 0; $f <= $this->getElement($name . 'count', 'dat', 0); $f++){
					$content .= $this->getMultiObjectFieldHTML($name, $identifier, $f);
				}

				$content .= '</tr></table></td></tr>';
				break;
			case we_objectFile::TYPE_HREF:
				$typeVal = $this->getElement($name . 'hreftype', 'dat');
				// FIXME: use we_html_select
				$typeSelect = '<select style="margin-right:4em;" class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hreftype]" name="we_' . $this->Name . '_input[' . $name . 'hreftype]" onchange="' . "we_cmd('object_reload_entry_at_class','','" . $GLOBALS['we_transaction'] . "','" . $identifier . "');" . '">
			<option' . (($typeVal == we_base_link::TYPE_ALL || !$typeVal) ? " selected" : "") . ' value="' . we_base_link::TYPE_ALL . '">all
			<option' . (($typeVal == we_base_link::TYPE_INT) ? " selected" : "") . ' value="' . we_base_link::TYPE_INT . '">int
			<option' . (($typeVal == we_base_link::TYPE_EXT) ? " selected" : "") . ' value="' . we_base_link::TYPE_EXT . '">ext
			</select>';
				$fileVal = $this->getElement($name . "hreffile") ?: "true";
				$fileSelect = '<select style="margin-right:4em;" class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hreffile]" name="we_' . $this->Name . '_input[' . $name . 'hreffile]">
			<option' . (($fileVal === "true") ? " selected" : "") . ' value="true">true
			<option' . (($fileVal === "false") ? " selected" : "") . ' value="false">false
			</select>';
				$dirVal = $this->getElement($name . "hrefdirectory") ?: "false"; // options anzeige umgedreht wegen 4363
				$dirSelect = '<select class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hrefdirectory]" name="we_' . $this->Name . '_input[' . $name . 'hrefdirectory]">
			<option' . (($dirVal === "true") ? " selected" : "") . ' value="true">false
			<option' . (($dirVal === "false") ? " selected" : "") . ' value="false">true
			</select>';
				$content .= '<tr style="vertical-align:top"><td  width="100" class="defaultfont" style="vertical-align:top"></td>' .
					'<td class="defaultfont">type ' .
					$typeSelect . 'file ' .
					$fileSelect . 'directory ' .
					$dirSelect .
					'</td></tr>
					<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>
						<td class="defaultfont">' .
					$this->htmlHref($name) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_CHECKBOX:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_forms::checkbox(1, $this->getElement($name . "default", "dat"), "we_" . $this->Name . "_input[" . $name . "default1]", g_l('modules_object', '[checked]'), true, "defaultfont", "if(this.checked){document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "default]" . "'].value=1;}else{ document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "default]" . "'].value=0;}") .
					'<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "default]" . '" value="' . $this->getElement($name . "default", "dat") . '" />' .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_IMG:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", '', $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", '', $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->getImageHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_FLASHMOVIE:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", '', $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", '', $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->getFlashmovieHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_QUICKTIME:
			case we_objectFile::TYPE_BINARY:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", '', $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", '', $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" style="vertical-align:top" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class= "defaultfont"  style="vertical-align:top">' .
					$this->getBinaryHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_DATE:
				$d = abs($this->getElement($name . "default", "dat"));
				$dd = abs($this->getElement($name . "defaultThumb", "dat"));
				$content .= '<tr style="vertical-align:top"><td  width="100" class="defaultfont">Default</td>' .
					'<td class="defaultfont">' .
					we_html_forms::checkboxWithHidden(($dd == '1' ? true : false), "we_" . $this->Name . "_xdate[" . $name . "defaultThumb]", 'Creation Date', false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);') .
					we_html_tools::getDateInput('we_' . $this->Name . '_date[' . $name . 'default]', ($d ?: time()), true) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_TEXT:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->dhtmledit($name, $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_OBJECT:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->getObjectFieldHTML($name, isset($attribs) ? $attribs : "") .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_COLLECTION:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin" style="vertical-align:top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont" style="vertical-align:top">' .
					$this->getCollectionFieldHTML($name . "default", $this->getElement($name . "default", "dat")) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_META:
				$content .= '<tr style="vertical-align:top"><td width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont"><table><tr><td class="defaultfont">Key</td><td class="defaultfont">Value</td><td></td></tr>';
				if(!$this->issetElement($name . "count")){
					$this->setElement($name . "count", 0);
				}

				$addArray = [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10];

				for($f = 0; $f <= $this->getElement($name . 'count', 'dat', 0); $f++){
					$content .= '<tr><td>' . we_html_tools::htmlTextInput('we_' . $this->Name . '_input[' . $name . 'defaultkey' . $f . ']', 40, $this->getElement($name . "defaultkey" . $f), 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 105) .
						'</td><td>' . we_html_tools::htmlTextInput("we_" . $this->Name . "_input[" . $name . "defaultvalue" . $f . "]", 40, $this->getElement($name . "defaultvalue" . $f), 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 105);

					$content .= "</td><td>" .
						we_html_tools::htmlSelect("amount_insert_meta_at_class_" . $name . $f, $addArray) .
						we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')") .
						(($f > 0) ?
						we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')") :
						we_html_button::create_button(we_html_button::DIRUP, "#", '', 0, 0, "", "", true)
						) .
						(($f < ($this->getElement($name . 'count', 'dat', 0))) ?
						we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')") :
						we_html_button::create_button(we_html_button::DIRDOWN, "#", '', 0, 0, "", "", true)
						) .
						we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_meta_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')") .
						'</td></tr>';
				}
				$content .= '</table></td></tr>';
				break;
			case we_objectFile::TYPE_COUNTRY:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlTextInput("we_" . $this->Name . "_country[" . $name . "default]", 40, $this->getElement($name . "default", "dat"), 10, 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_LANGUAGE:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlTextInput("we_" . $this->Name . "_language[" . $name . "default]", 40, $this->getElement($name . "default", "dat"), 15, 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_LINK:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					$this->htmlLinkInput($name, $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_SHOPVAT:
				$values = [];
				if(defined('SHOP_ORDER_TABLE')){
					$allVats = we_shop_vats::getAllShopVATs();
					foreach($allVats as $id => $shopVat){
						$values[$id] = $shopVat->vat . ' - ' . $shopVat->getNaturalizedText() . ' (' . $shopVat->territory . ')';
						/* 						if($shopVat->standard){
						  $standardId = $id;
						  $standardVal = $shopVat->vat;
						  } */
					}
				}
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlSelect("we_" . $this->Name . "_shopVat[" . $name . "default]", $values, 1, $this->getElement($name . "default", "dat")) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_SHOPCATEGORY:
				if(defined('SHOP_ORDER_TABLE')){
					$values = ['0' => '', 'ID' => 'ID', 'Category' => 'Category', 'Path' => 'Path', 'Title' => 'Title', 'Description' => 'Description', 'destPrinciple' => 'destPrinciple'];
					$selectField = self::htmlSelect('we_' . $this->Name . '_input[' . $name . 'shopcatField]', $values, 1, $this->getElement($name . 'shopcatField', 'dat'));

					$values = ['true' => 'true', 'false' => 'false'];
					$selectShopPath = self::htmlSelect('we_' . $this->Name . '_input[' . $name . 'shopcatShowPath]', $values, 1, $this->getElement($name . 'shopcatShowPath', 'dat'));
					$textRootdir = self::htmlTextInput('we_' . $this->Name . '_input[' . $name . 'shopcatRootdir]', 24, $value = $this->getElement($name . 'shopcatRootdir', 'dat'));

					$values = ['0' => ' '] + we_shop_category::getShopCatFieldsFromDir('Path', true); //Fix #9355 don't use array_merge() because numeric keys will be renumbered!
					$selectCategories = we_html_tools::htmlSelect('we_' . $this->Name . '_shopCategory[' . $name . 'default]', $values, 1, $this->getElement($name . 'default', 'dat'), false, [
							], 'value', 388);
					$selectLimitChoice = we_html_forms::checkboxWithHidden((abs($this->getElement($name . 'shopcatLimitChoice', 'dat')) == '1' ? true : false), 'we_' . $this->Name . '_input[' . $name . 'shopcatLimitChoice]', 'use default only', false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');

					$content .= '<tr style="vertical-align:top"><td  width="100" class="defaultfont" style="vertical-align:top"></td><td class="defaultfont">' .
						'field ' . $selectField . ' showpath ' . $selectShopPath . '</td>
						</tr>
						<tr style="vertical-align:top"><td  width="100" class="defaultfont" style="vertical-align:top"></td><td class="defaultfont">' .
						'rootdir ' . $textRootdir . '</td>
						</tr>
						<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td><td class="defaultfont">' .
						$selectCategories . '<br/>' . $selectLimitChoice . '</td>
						</tr>';
				}
				break;
			default: // default for input, int and float
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td class="defaultfont">' .
					we_html_tools::htmlTextInput("we_" . $this->Name . '_input[' . $name . 'default]', 40, $this->getElement($name . "default", "dat"), ($type == we_objectFile::TYPE_INT ? 9 : ($type == we_objectFile::TYPE_FLOAT ? 19 : 255)), 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
		}

		switch($type){
			case we_objectFile::TYPE_TEXT:
			case we_objectFile::TYPE_INPUT:
			case we_objectFile::TYPE_META:
			case we_objectFile::TYPE_LINK:
			case we_objectFile::TYPE_HREF:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td><td class="defaultfont">' .
// title
					we_html_forms::radiobutton($name, ($this->getElement('title', "dat") == $name), "we_" . $this->Name . "_input[title]", g_l('global', '[title]'), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[title]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
// description
					we_html_forms::radiobutton($name, ($this->getElement('desc', "dat") == $name), "we_" . $this->Name . "_input[desc]", g_l('global', '[description]'), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[desc]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
// keywords
					we_html_forms::radiobutton($name, ($this->getElement('keywords', "dat") == $name), "we_" . $this->Name . "_input[keywords]", g_l('weClass', '[Keywords]'), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[keywords]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
					'</td></tr>';
				break;
			default:
		}

		switch($type){
			case we_objectFile::TYPE_TEXT:
			case we_objectFile::TYPE_INPUT:
			case we_objectFile::TYPE_DATE:
				$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td><td class="defaultfont">';
				if($type == we_objectFile::TYPE_DATE){
					$content .= we_html_forms::radiobutton($name, ($this->getElement('urlfield0', "dat") == $name), "we_" . $this->Name . "_input[urlfield0]", g_l('weClass', '[urlfield0]'), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[urlfield0]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}");
				} else {
					foreach(array_keys(self::$urlFields) as $key){
						$content .= we_html_forms::radiobutton($name, ($this->getElement($key) == $name), "we_" . $this->Name . "_input[" . $key . "]", g_l('weClass', '[' . $key . ']'), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[" . $key . "]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}");
					}
				}
				$content .= '</td></tr>';
				break;
			default:
		}


		if($type != we_objectFile::TYPE_CHECKBOX){
//Pflichtfeld
			$content .= '<tr style="vertical-align:top"><td  width="100" class="defaultfont"></td><td class="defaultfont">' .
				we_html_forms::checkbox(1, $this->getElement($name . "required", "dat"), "we_" . $this->Name . "_input[" . $name . "required1]", g_l('global', '[required_field]'), true, "defaultfont", "if(this.checked){document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "required]" . "'].value=1;}else{ document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "required]" . "'].value=0;}") .
				($this->canHaveVariants() && $this->isVariantField($name) ?
				we_html_forms::checkboxWithHidden($this->getElement($name . "variant", "dat"), "we_" . $this->Name . "_variant[" . $name . "variant]", g_l('global', '[variant_field]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);') :
				'') .
				'<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="' . $this->getElement($name . "required", "dat") . '" />' .
				'</td></tr>';
// description for editmode.
		} else {
			$content .= (defined('SHOP_ORDER_TABLE') ?
//Pflichtfeld
				'<tr style="vertical-align:top"><td  width="100" class="defaultfont"></td><td class="defaultfont">' .
				($this->canHaveVariants() && $this->isVariantField($name) ?
				we_html_forms::checkboxWithHidden($this->getElement($name . "variant", "dat"), "we_" . $this->Name . "_variant[" . $name . "variant]", g_l('global', '[variant_field]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);') :
				'') .
				'<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="0" />' .
				'</td></tr>' :
				'<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="0" />' );
		}


		$content .= '<tr style="vertical-align:top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('weClass', '[fieldusers]') . '</td>
			<td class="defaultfont" >' .
			$this->formUsers1($jsCmd, $name, $identifier) .
			'</td></tr>';

		return $content;
	}

	function htmlHref($n){
		$type = $this->getElement($n . "hreftype");

		$n .= 'default';
		$hrefArr = we_unserialize($this->getElement($n));

		$nint = $n . we_base_link::MAGIC_INT_LINK;
		$nintID = $n . we_base_link::MAGIC_INT_LINK_ID;
		$nintPath = $n . we_base_link::MAGIC_INT_LINK_PATH;
		$nextPath = $n . we_base_link::MAGIC_INT_LINK_EXTPATH;

		$attr = ' size="20" ';


		$int = isset($hrefArr["int"]) ? $hrefArr["int"] : false;
		$intID = isset($hrefArr["intID"]) ? $hrefArr["intID"] : 0;
		$intPath = $intID ? id_to_path($intID) : "";
		$extPath = isset($hrefArr["extPath"]) ? $hrefArr["extPath"] : "";
		$int_elem_Name = 'we_' . $this->Name . '_href[' . $nint . ']';
		$intPath_elem_Name = 'we_' . $this->Name . '_vars[' . $nintPath . ']';
		$intID_elem_Name = 'we_' . $this->Name . '_href[' . $nintID . ']'; //TODO: should we use #bdid?
		$ext_elem_Name = 'we_' . $this->Name . '_href[' . $nextPath . ']';

		switch($type){
			case we_base_link::TYPE_INT:
				$out = we_objectFile::hrefRow($intID_elem_Name, $intID, $intPath_elem_Name, $intPath, $attr, $int_elem_Name);
				break;
			case we_base_link::TYPE_EXT:
				$out = we_objectFile::hrefRow('', '', $ext_elem_Name, $extPath, $attr, $int_elem_Name);
				break;
			default:
				$out = we_objectFile::hrefRow($intID_elem_Name, $intID, $intPath_elem_Name, $intPath, $attr, $int_elem_Name, true, $int) .
					we_objectFile::hrefRow('', '', $ext_elem_Name, $extPath, $attr, $int_elem_Name, true, $int);
		}
		return '<table class="default">' . $out . '</table>';
	}

	private function htmlLinkInput($n, $i){
		$n .= 'default';

		$attribs = ['name' => $n
		];
		$elem = $this->getElement($n);
		$link = ($elem ? (is_array($elem) ? $elem : we_unserialize($elem)) : []) ?:
			["ctype" => "text", "type" => we_base_link::TYPE_EXT, "href" => "#", "text" => g_l('global', '[new_link]')];

		$img = new we_imageDocument();
		$content = self::getLinkContent($link, $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);

		$startTag = self::getLinkStartTag($link, $attribs, $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);
		$editbut = we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('edit_link_at_class','" . $n . "','','" . $i . "');");
		$delbut = we_html_button::create_button(we_html_button::TRASH, "javascript:setScrollTo();we_cmd('object_delete_link_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $n . "')");
		if(!$content){
			$content = g_l('global', '[new_link]');
		}
		return "<table class=\"default\">
					<tr><td class=\"defaultfont\">" . ($startTag ?: '') . $content . "</a></td>
						<td width=\"5\"></td><td>" . $editbut . $delbut . "</td>
					</tr>
					</table>";
	}

	private function getObjectFieldHTML($ObjectID, $attribs, $editable = true){
		$pid = intval($this->getElement($ObjectID));
		if(!$editable){
			return '';
		}
		$db = new DB_WE();
		$classPath = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $pid, 'Path', $db);
		$textname = 'we_' . $this->Name . '_txt[' . $pid . '_path]';
		$idname = 'we_' . $this->Name . '_input[' . $ObjectID . 'default]';
		$myid = $this->getElement($ObjectID . 'default', "dat");
		$DoubleNames = $this->includedObjectHasDoubbleFieldNames($pid);
		$path = $this->getElement('we_object_' . $pid . '_path');
		$path = $path ?: ($myid ? f("SELECT Path FROM " . OBJECT_FILES_TABLE . ' WHERE ID=' . $myid, '', $db) : '');
		$rootDir = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $db->escape($classPath) . '"', '', $db);
		$table = OBJECT_FILES_TABLE;
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $idname . "','" . $textname . "','setHot','','" . $rootDir . "','objectFile'," . (we_base_permission::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");
		$delbutton = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value=''");
		/*
		  DAMD: der Autocompleter funktioniert hier nicht. Der HTML-Cokde wird dynamisch erzeugt das
		  $weSuggest =& weSuggest::getInstance();
		  $weSuggest->setAcId("TypeObject");
		  $weSuggest->setContentType("folder,objectFile");
		  $weSuggest->setInput($textname,$path);
		  $weSuggest->setMaxResults(20);
		  $weSuggest->setRequired(true);
		  $weSuggest->setResult($idname,$myid);
		  $weSuggest->setSelector(weSuggest::DocSelector);
		  $weSuggest->setTable($table);
		  $weSuggest->setWidth(246);
		  $weSuggest->setSelectButton($button);
		  $weSuggest->setTrashButton($delbutton);

		  return $weSuggest->getHTML();
		 */
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($textname, 30, $path, "", ' readonly', "text", 246, 0), "", "left", "defaultfont", we_html_element::htmlHidden($idname, $myid), $button, $delbutton) . ($DoubleNames ? '<span style="color:red" >' . sprintf(g_l('modules_object', '[incObject_sameFieldname]'), implode(', ', $DoubleNames)) . '</span>' : '');
	}

	private function getCollectionFieldHTML($name, $defaultID, $editable = true){
		if(!$editable){
			return '';
		}

		$textname = 'we_' . $this->Name . '_txt[' . $name . '_path]';
		$idname = 'we_' . $this->Name . "_input[" . $name . "]";
		$path = id_to_path($defaultID, VFILE_TABLE);
		$rootDir = 0;

		$btnSelect = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . VFILE_TABLE . "','" . $idname . "','" . $textname . "','setHot','','" . $rootDir . "','" . we_base_ContentTypes::COLLECTION . "'," . (we_base_permission::hasPerm("CAN_SEE_COLLECTIONS") ? 0 : 1) . ")");

		$btnDelete = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value=''");

		$btnNewCollection = we_html_button::create_button('fa:btn_add_collection,fa-plus,fa-lg fa-archive', "javascript:top.we_cmd('edit_new_collection','write_back_to_opener," . $idname . "," . $textname . "','',-1,'" . stripTblPrefix(VFILE_TABLE) . "');", '', 0, 0, "", "", false, false);

		/*
		  DAMD: der Autocompleter funktioniert hier nicht...
		  $weSuggest =& weSuggest::getInstance();
		  $weSuggest->setAcId("TypeObject");
		  $weSuggest->setContentType("folder,objectFile");
		  $weSuggest->setInput($textname,$path);
		  $weSuggest->setMaxResults(20);
		  $weSuggest->setRequired(true);
		  $weSuggest->setResult($idname,$myid);
		  $weSuggest->setSelector(weSuggest::DocSelector);
		  $weSuggest->setTable($table);
		  $weSuggest->setWidth(246);
		  $weSuggest->setSelectButton($btnSelect);
		  $weSuggest->setTrashButton($btnDelete);

		  return $weSuggest->getHTML();
		 */
		$buttons = $btnSelect . (we_base_permission::hasPerm('NEW_COLLECTION') ? $btnNewCollection : '') . $btnDelete;

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($textname, 30, $path, "", ' readonly', "text", 216, 0), "", "left", "defaultfont", we_html_element::htmlHidden($idname, $collectionID), $buttons);
	}

	private function getMultiObjectFieldHTML($name, $i, $f){
		$pid = $this->getElement($name . "class");

		$db = new DB_WE();
		$classPath = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($pid), "", $db);
		$textname = 'we_' . $this->Name . '_txt[' . $name . '_path' . $f . ']';
		$idname = 'we_' . $this->Name . "_input[" . $name . "defaultvalue" . $f . "]";
		$myid = $this->getElement($name . "defaultvalue" . $f, "dat");

		$path = (
			$this->getElement('we_object_' . $name . "_path") ?:
			($myid ?
			f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . $myid, "", $db) :
			''
			) );
		$rootDir = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $db->escape($classPath) . '"', '', $db);

		$selectObject = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . OBJECT_FILES_TABLE . "','" . $idname . "','" . $textname . "','setHot','','" . $rootDir . "','objectFile'," . (we_base_permission::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ")");
		$delbutton = we_html_button::create_button(we_html_button::TRASH, "javascript:" . $cmd1 . "='';document.we_form.elements['" . $textname . "'].value=''");

		$count = $this->getElement($name . "count", 'dat', 0);

		return '<tr>' .
			'<td>' . we_html_tools::htmlTextInput($textname, 30, $path, 255, 'onchange="_EditorFrame.setEditorIsHot(true);" readonly ', "text", 146) . '</td>' .
			'<td>' .
			$selectObject .
			we_html_element::htmlHidden($idname, $myid) .
			(($count + 1 < $this->getElement($name . "max") || $this->getElement($name . "max") == "") ?
			we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')", '', 0, 22) :
			we_html_button::create_button('fa:btn_add_listelement,fa-plus,fa-lg fa-list-ul', "#", '', 0, 0, "", "", true)
			) .
			(($f > 0) ?
			we_html_button::create_button(we_html_button::DIRUP, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')") :
			we_html_button::create_button(we_html_button::DIRUP, "#", '', 0, 0, "", "", true)
			) .
			(($f < ($count)) ?
			we_html_button::create_button(we_html_button::DIRDOWN, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')") :
			we_html_button::create_button(we_html_button::DIRDOWN, "#", '', 0, 0, "", "", true)
			) .
			($count >= 1 ?
			we_html_button::create_button(we_html_button::TRASH, "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_meta_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')") :
			$delbutton
			) .
			'</td></tr>';
	}

	function dhtmledit($name, $i = 0){
		return we_html_button::create_button('attributes', "javascript:setScrollTo();we_cmd('object_editObjectTextArea','" . $i . "','" . $name . "','" . $GLOBALS["we_transaction"] . "');") .
			$this->getWysiwygArea($name);
	}

	function getWysiwygArea($name){
		$rmfp = $this->getElement($name . "removefirstparagraph");
		$commands = $this->getElement($name . "commands");
		$attribs = ["removefirstparagraph" => $rmfp ? $rmfp === 'on' : REMOVEFIRSTPARAGRAPH_DEFAULT,
			"xml" => $this->getElement($name . "xml"),
			"dhtmledit" => $this->getElement($name . "dhtmledit"),
			"wysiwyg" => $this->getElement($name . "dhtmledit"),
			"showmenus" => $this->getElement($name . "showmenus", "dat", "off"),
			"commands" => preg_replace('/ *, */', ',', $commands ?: COMMANDS_DEFAULT),
			"contextmenu" => $this->getElement($name . "contextmenu"),
			"classes" => $this->getElement($name . "cssClasses"),
			"fontnames" => $this->getElement($name . "fontnames"),
			"fontsizes" => $this->getElement($name . "fontsizes"),
			"formats" => $this->getElement($name . "formats"),
			"width" => 386, //$this->getElement($name."width","dat",618),
			"height" => 52, //$this->getElement($name."height","dat",200),
			"rows" => 3,
			"bgcolor" => $this->getElement($name . "bgcolor", "dat", ''),
			"tinyparams" => $this->getElement($name . "tinyparams"),
			"templates" => $this->getElement($name . "templates"),
			"class" => $this->getElement($name . "class"),
			"cols" => 30,
			"inlineedit" => $this->getElement($name . "inlineedit"),
			"stylesheets" => $this->CSS,
			"spellchecker" => true,
		];

		$autobr = $this->getElement($name . "autobr");
		$autobrName = 'we_' . $this->Name . '_input[' . $name . 'autobr]';

		$value = $this->getElement($name . "default", "dat");
		return we_html_forms::weTextarea('we_' . $this->Name . '_input[' . $name . 'default]', $value, $attribs, $autobr, $autobrName, true, (($this->CSS || $attribs['classes']) ? false : true), false, false, ($rmfp ? $rmfp === 'on' : REMOVEFIRSTPARAGRAPH_DEFAULT), "");
	}

	function add_user_to_field(array $ids, $name){
		$users = makeArrayFromCSV($this->getElement($name . 'users', 'dat'));
		foreach($ids as $id){
			if($id && (!in_array($id, $users))){
				$users[] = $id;
			}
		}
		$this->setElement($name . 'users', implode(',', $users));
	}

	function del_user_from_field($id, $name){
		$users = makeArrayFromCSV($this->getElement($name . 'users', 'dat'));
		$pos = array_search($id, $users);
		if($pos !== false){
			unset($users[$pos]);
		}
		$this->setElement($name . 'users', implode(',', $users));
	}

	private function formUsers1(we_base_jsCmd $jsCmd, $name, $nr = 0){
		$users = $this->getElement($name . 'users') ? makeArrayFromCSV($this->getElement($name . 'users')) : [];
		$content = '<table class="default" style="width:388px;margin:5px;" >';
		if($users){
			$this->DB_WE->query('SELECT ID,Path,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType FROM ' . USER_TABLE . ' WHERE ID IN (' . implode(',', $users) . ')');
			$allUsers = array_flip($users);
			while($this->DB_WE->next_record(MYSQL_ASSOC)){
				$content .= '<tr><td style="width:20px;" class="userIcon" data-contenttype="' . $this->DB_WE->f('ContentType') . '"></td><td class="defaultfont" style="width:324px;">' . $this->DB_WE->f('Path') . '</td><td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_del_user_from_field','" . $GLOBALS['we_transaction'] . "','" . $nr . "'," . $this->DB_WE->f('ID') . ",'" . $name . "');") . '</td></tr>';
				unset($allUsers[$this->DB_WE->f('ID')]);
			}
//all non-existing users
			foreach(array_keys($allUsers) as $user){
				$content .= '<tr><td style="width:20px;"></td><td class="defaultfont">Unknown</td><td>' . we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_del_user_from_field','" . $GLOBALS['we_transaction'] . "','" . $nr . "'," . $user . ",'" . $name . "');") . '</td></tr>';
			}
		} else {
			$content .= '<tr><td style="width:20px;" class="userIcon" data-contenttype="we/userGroup"></td><td class="defaultfont" style="width:324px;">' . g_l('weClass', '[everybody]') . '</td><td></td></tr>';
		}

		$content .= '</table>';

		$textname = 'we_' . $this->Name . '_input[' . $name . 'usertext]';
		$idname = 'we_' . $this->Name . '_input[' . $name . 'userid]';
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('object_del_all_users','" . $GLOBALS['we_transaction'] . "','" . $nr . "','" . $name . "')", '', 0, 0, "", "", ($users ? false : true));

		$cmd = "object_add_user_to_field," . $GLOBALS['we_transaction'] . "," . $nr . ",," . $name;
		$addbut = we_html_element::htmlHiddens([$idname => 0, $textname => ""]) . we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_users_selector',document.we_form.elements['" . $idname . "'].value,'" . $textname . "','','" . $idname . "','" . $cmd . "','','',1)");

		$jsCmd->addCmd('setIconOfDocClass', 'userIcon');
		return '<table class="default"><tr><td>' .
			'<div class="multichooser">' . $content . '</div></td></tr><tr><td style="text-align:right">' . $delallbut . $addbut . '</td></tr></table>';
	}

	private function formUsers(we_base_jsCmd $jsCmd, $canChange = true){
		$users = makeArrayFromCSV($this->Users);
		$usersReadOnly = makeArrayFromCSV($this->UsersReadOnly);
		$content = '<table class="default" style="width:388px;margin:5px;">';

		if($users){
			$this->DB_WE->query('SELECT ID,Path,(IF(IsFolder,"we/userGroup",(IF(Alias>0,"we/alias","we/user")))) AS ContentType FROM ' . USER_TABLE . ' WHERE ID IN(' . implode(',', $users) . ')');
			$allUsers = $this->DB_WE->getAllFirst(true, MYSQL_ASSOC);
			foreach($allUsers as $user => $data){
				$content .= '<tr><td style="width:20px;" class="userIcon" data-contenttype="' . $data['ContentType'] . '"></td><td  style="width:333px;" class="defaultfont">' . $data["Path"] . '</td><td style="width:20px;">' .
					($canChange ?
					we_html_element::htmlHidden('we_users_read_only[' . $user . ']', (!empty($usersReadOnly[$user])) ? $usersReadOnly[$user] : "") .
					'<input type="checkbox" value="1" name="wetmp_users_read_only[' . $user . ']"' . ( (!empty($usersReadOnly[$user])) ? ' checked' : '') . ' onclick="this.form.elements[\'we_users_read_only[' . $user . ']\'].value=(this.checked ? 1 : 0);_EditorFrame.setEditorIsHot(true);" />' :
					'<i class="fa fa-' . ($usersReadOnly[$user] ? 'check-' : '') . 'square-o wecheckIcon disabled"></i>'
					) . '</td><td style="width:80px;" class="defaultfont">' . g_l('weClass', '[readOnly]') . '</td><td>' .
					($canChange ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('users_del_user','" . $user . "');_EditorFrame.setEditorIsHot(true);") :
					''
					) . '</td></tr>';
			}
		} else {
			$content .= '<tr><td style="width:20px;" class="userIcon" data-contenttype="we/user"></td><td class="defaultfont">' . g_l('weClass', '[onlyOwner]') . '</td><td></td></tr>';
		}
		$content .= '</table>';

		$textname = 'userNameTmp';
		$idname = 'userIDTmp';
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('object_del_all_users','')", '', 0, 0, "", "", $this->Users ? false : true);
//FIXME: bad encoded field!

		$addbut = $canChange ?
			we_html_element::htmlHiddens([$idname => "", $textname => ""]) . we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_users_selector',document.we_form.elements['" . $idname . "'].value,'" . $textname . "','',document.we_form.elements['" . $idname . "'].value,'users_add_user','','',1)") : '';

		$content = '<table class="default">
<tr><td><div style="width:506px;" class="multichooser">' . $content . '</div></td></tr>' .
			($canChange ? '<tr><td style="text-align:right;padding-top:1em;">' . $delallbut . $addbut . '</td></tr>' : "") . '</table>';
		$jsCmd->addCmd('setIconOfDocClass', 'userIcon');
		return we_html_tools::htmlFormElementTable($content, g_l('weClass', '[otherowners]'), "left", "defaultfont");
	}

	function del_all_users($name){
		if($name === ''){
			$this->Users = '';
			return;
		}
		$this->setElement($name . "users", '');
	}

	function add_user($id){
		$ids = is_array($id) ? $id : explode(',', $id);
		$this->Users = implode(',', array_unique(array_filter(explode(',', $this->Users)) + $ids, SORT_NUMERIC));
	}

	function del_user($id){
		$users = array_filter(explode(',', $this->Users));
		if(($pos = array_search($id, $users, false)) == false){
			return;
		}
		unset($users[$pos]);
		$this->Users = implode(',', $users);
	}

	function add_css(array $id){
		$this->CSS = implode(',', array_filter(
				array_unique(
					array_merge(explode(',', $this->CSS), $id)
			))
		);
	}

	function del_css($id){
		$css = explode(',', $this->CSS);
		if(($pos = array_search($id, $css, false)) === false){
			return;
		}
		unset($css[$pos]);
		$this->CSS = implode(',', $css);
	}

	private function getImageHTML($name, $defaultname, $i = 0){
		$img = new we_imageDocument();
		$id = $defaultname;
		if($id){
			$img->initByID($id, FILE_TABLE, false);
		} else {
			$img->we_new();
		}

		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$cmd = 'object_reload_entry_at_class,' . $GLOBALS['we_transaction'] . ',' . $i . ');';
		$content = '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('we_selector_image','" . $id . "','" . FILE_TABLE . "','" . $fname . "','','" . $cmd . "','',0,'" . we_base_ContentTypes::IMAGE . "')") .
			we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "')") .
			'<br/>' . $img->getHtml();

// gets thumbnails and shows a select field, if there are any:
		$thumbdb = new DB_WE();
		$thumbdb->query('SELECT Name FROM ' . THUMBNAILS_TABLE);
		$thumbList = $thumbdb->getAll(true);
		if($thumbList){
			$content .= '<br />' . g_l('modules_object', '[use_thumbnail_preview]') . ":<br />";
			array_unshift($thumbList, '-');
			$tmp = $this->getElement($name . "Thumb");
			$currentSelection = ($tmp && isset($thumbList[$tmp]) ? $tmp : '');

			$content .= we_html_tools::htmlSelect("we_" . $this->Name . "_input[" . $name . "Thumb]", $thumbList, 1, $currentSelection, "", ['onchange' => "_EditorFrame.setEditorIsHot(true);"], "value", 388);
		}
		return $content;
	}

	private function getFlashmovieHTML($name, $defaultname, $i = 0){
		$img = new we_flashDocument();
		$id = $defaultname;
		if($id){
			$img->initByID($id, FILE_TABLE, false);
		} else {
			$img->we_new();
		}

		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$cmd = 'object_reload_entry_at_class,' . $GLOBALS['we_transaction'] . ',' . $i . ');';

		return '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('we_selector_document','" . $id . "','" . FILE_TABLE . "','" . $fname . "','','" . $cmd . "','',0,'" . we_base_ContentTypes::FLASH . "')") .
			we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "')") .
			'<br/>' . $img->getHtml();
	}

	private function getBinaryHTML($name, $defaultname, $i = 0){
		$other = new we_otherDocument();
		$id = $defaultname; //$this->getElement($defaultname);
		$other->initByID($id, FILE_TABLE, false);
		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$cmd = 'object_reload_entry_at_class,0,' . $GLOBALS['we_transaction'] . ',' . $i . ');';

		return '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document','" . $id . "','" . FILE_TABLE . "','" . $fname . "','','" . $cmd . "','',0,'" . we_base_ContentTypes::APPLICATION . "')") .
			we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "');") .
			'<br/>' . $other->getHtml();
	}

	private function formDefault(we_base_jsCmd $jsCmd){
		$select2 = $select = '';

		if(($anz = $this->getElement('Defaultanzahl', 'dat', -1)) >= 0){
			$this->DefaultText = '';
			for($i = 0; $i <= $anz; $i++){
				$was = "DefaultText_" . $i;
				if(($dat = $this->getElement($was)) != ""){
					if(stristr($dat, 'unique')){
						$unique = $this->getElement('unique_' . $i);
						$dat = "%" . str_replace("%", "", $dat) . ( $unique > 0 ? $unique : 16) . "%";
						$this->setElement($was, $dat, 'defaultText');
					}
					$this->DefaultText .= $dat;
				}
			}
		}

		$all = $this->DefaultText;
		$zahl = 0;
		$regs = [];

		while($all){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all, strlen($regs[1]) + 2);
				$key = $regs[1];
				if(preg_match('/unique([^%]*)/', $key, $regs)){
					$anz = (!$regs[1] ? 16 : abs($regs[1]));
					$unique = substr(md5(uniqid('', true)), 0, min($anz, 32));
					$text = preg_replace('/%unique[^%]*%/', $unique, (isset($text) ? $text : ""));
					$select .= we_html_tools::htmlSelect('we_' . $this->Name . '_input[DefaultText_' . $zahl . "]", g_l('modules_object', '[value]'), 1, "%unique%", "", ['onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'], "value", 140) . "&nbsp;" .
						we_html_tools::htmlTextInput('we_' . $this->Name . "_input[unique_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
				} else {
					$select .= we_html_tools::htmlSelect('we_' . $this->Name . "_input[DefaultText_" . $zahl . "]", g_l('modules_object', '[value]'), 1, "%" . $key . "%", "", [
							'onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'], "value", 140) . "&nbsp;";
				}
			} else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all, strlen($regs[1]));
				$key = $regs[1];
				$select .= we_html_tools::htmlSelect('textwert_' . $zahl, g_l('modules_object', '[value]'), 1, "Text", "", ['onchange' => "_EditorFrame.setEditorIsHot(true); document.we_form.elements['we_" . $this->Name . '_input[DefaultText_' . $zahl . "]'].value = this.options[this.selectedIndex].value; we_cmd('reload_editpage');"], "value", 140) . "&nbsp;" .
					we_html_tools::htmlTextInput('we_' . $this->Name . '_input[DefaultText_' . $zahl . "]", 40, $key, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
			}

			$select .= we_html_element::htmlBr();
			$zahl++;
		}

		$select .= we_html_tools::htmlSelect('we_' . $this->Name . '_input[DefaultText_' . $zahl . ']', g_l('modules_object', '[value]'), 1, "", "", ['onchange' => "_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');"], 'value', 140) . '&nbsp;' .
			'<input type = "hidden" name="we_' . $this->Name . '_input[Defaultanzahl]" value="' . $zahl . '" />';


		if(($anz = $this->getElement('DefaultanzahlUrl', 'dat', -1)) >= 0){ //Fix #9964 0 is an corret value!
			$allFields = array_merge(self::$urlUnique, self::$urlFields);
			$this->DefaultUrl = '';

			for($i = 0; $i <= $anz; $i++){
				$was = 'DefaultUrl_' . $i;
				if(($curDat = $this->getElement($was))){
					foreach($allFields as $key => $len){
						if(stristr($curDat, $key)){
							$curDat = '%' . str_replace('%', '', $curDat) . ($this->getElement($key . '_' . $i) ?: $len) . '%';
						}
					}
					$this->setElement($was, $curDat);
					$this->DefaultUrl .= $curDat;
				}
			}
		}

		$all = $this->DefaultUrl;
		$zahl = 0;

		while($all){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all, strlen($regs[1]) + 2);
				$data = $regs[1];
				$found = false;
				foreach(self::$urlUnique as $key => $len){
					if(preg_match('/' . $key . '([^%]*)/', $data, $regs)){
						$anz = (!$regs[1] ? $len : abs($regs[1]));
						$unique = substr(md5(uniqid('', true)), 0, min($anz, 32));
						$text = preg_replace('/%' . $key . '[^%]*%/', $unique, (isset($text) ? $text : ""));
						$select2 .= we_html_tools::htmlSelect('we_' . $this->Name . '_input[DefaultUrl_' . $zahl . ']', g_l('modules_object', '[url]'), 1, '%' . $key . '%', '', [
								'onchange' => "_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');"], "value", 140) . "&nbsp;" .
							we_html_tools::htmlTextInput('we_' . $this->Name . '_input[' . $key . '_' . $zahl . ']', 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
						$found = true;
						break;
					}
				}
				if(!$found){
					foreach(self::$urlFields as $key => $len){
						if(preg_match('/' . $key . '([^%]*)/', $data, $regs)){
							$anz = (!$regs[1] ? $len : abs($regs[1]));
							$select2 .= we_html_tools::htmlSelect('we_' . $this->Name . '_input[DefaultUrl_' . $zahl . ']', g_l('modules_object', '[url]'), 1, '%' . $key . '%', '', [
									'onchange' => "_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');"], "value", 140) . "&nbsp;" .
								we_html_tools::htmlTextInput('we_' . $this->Name . '_input[' . $key . '_' . $zahl . ']', 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
							$found = true;
							break;
						}
					}
					if(!$found){
						$select2 .= we_html_tools::htmlSelect('we_' . $this->Name . '_input[DefaultUrl_' . $zahl . ']', g_l('modules_object', '[url]'), 1, '%' . $data . '%', '', [
								'onchange' => "_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');"], "value", 140) . "&nbsp;";
					}
				}
			} else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all, strlen($regs[1]));
				$data = $regs[1];
				$select2 .= we_html_tools::htmlSelect('textwert_' . $zahl, g_l('modules_object', '[url]'), 1, 'Text', '', ['onchange' => "_EditorFrame.setEditorIsHot(true); document.we_form.elements['we_" . $this->Name . '_input[DefaultUrl_' . $zahl . "]'].value = this.options[this.selectedIndex].value; we_cmd('reload_editpage');"], "value", 140) . "&nbsp;" .
					we_html_tools::htmlTextInput("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", 40, $data, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
			}

			$select2 .= we_html_element::htmlBr();
			$zahl++;
		}

		$select2 .= we_html_tools::htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "", "", ['onchange' => "_EditorFrame.setEditorIsHot(true);we_cmd('reload_editpage');"], "value", 140) . '&nbsp;' .
			'<input type = "hidden" name="we_' . $this->Name . '_input[DefaultanzahlUrl]" value="' . $zahl . '" />';

		return '<table class="default">
	<tr><td colspan="3" class="defaultfont" style="vertical-align:top">' . g_l('modules_object', '[name]') . '</td></tr>
	<tr><td colspan="3" class="withBigSpace" >' . $select . '</td></tr>
	<tr><td colspan="3" class="defaultfont" style="vertical-align:top">' . g_l('modules_object', '[seourl]') . '</td></tr>
	<tr><td colspan="3" class="withBigSpace">' . $select2 . '</td></tr>
	<tr><td colspan="3" class="withBigSpace">' . $this->formTriggerDocument(true) . '</td></tr>
	<tr><td class="defaultfont" style="vertical-align:top" colspan="3">' . g_l('global', '[categorys]') . '</td></tr>
	<tr><td colspan="3" class="withBigSpace">' . $this->formCategory($jsCmd) . '</td></tr>
	<tr><td colspan="3" ' . ($this->RestrictUsers ? 'class="withBigSpace"' : '') . '>' . $this->formRestrictUsers() . '</td></tr>' .
			($this->RestrictUsers ?
			'<tr><td colspan="3" >' . $this->formUsers($jsCmd) . '</td></tr>' :
			'') .
			'</table>';
	}

	function formRestrictUsers($canChange = true){
		if($canChange){
			$hiddenname = 'we_' . $this->Name . '_RestrictUsers';
			$hidden = we_html_element::htmlHidden($hiddenname, abs($this->RestrictUsers));
			$check = we_html_forms::checkbox(1, $this->RestrictUsers ? true : false, 'tmp' . $hiddenname, g_l('weClass', '[limitedAccess]'), true, "defaultfont", "_EditorFrame.setEditorIsHot(true);this.form.elements['" . $hiddenname . "'].value=(this.checked ? '1' : '0');we_cmd('reload_editpage');");
			return $hidden . $check;
		}
		return '<table class="default"><tr><td><i class="fa fa-' . ($this->RestrictUsers ? 'check-' : '') . 'square-o wecheckIcon disabled"></i></td><td class="defaultfont">&nbsp;' . g_l('weClass', '[limitedAccess]') . '</td></tr></table>';
	}

	public function formPath($disablePath = false, $notSetHot = false, $extra = ''){
		return '<table class="default">
	<tr><td>' . $this->formInputField('', 'Text', g_l('modules_object', '[classname]'), 30, 0, 255, 'onchange="pathOfDocumentChanged(true);"') . '</td><td></td><td></td></tr>
</table>';
	}

	private function formWorkspaces(we_base_jsCmd $jsCmd){
//remove not existing workspaces - deal with templates as well
		$arr = makeArrayFromCSV($this->Workspaces);
		$defaultArr = makeArrayFromCSV($this->DefaultWorkspaces);
		$tmplArr = makeArrayFromCSV($this->Templates);
		$newTmplArr = $newDefaultArr = $newArr = [];

//    check if workspace exists - correct templates if neccessary !!
		for($i = 0; $i < count($arr); $i++){
			if(we_base_file::isWeFile($arr[$i], FILE_TABLE, $this->DB_WE)){
				$newArr[] = $arr[$i];
				if(in_array($arr[$i], $defaultArr)){
					$newDefaultArr[] = $arr[$i];
				}
				$newTmplArr[] = (isset($tmplArr[$i]) ? $tmplArr[$i] : '');
			}
		}

		$this->Workspaces = implode(',', $newArr);
		$this->Templates = implode(',', $newTmplArr);
		$this->DefaultWorkspaces = implode(',', $newDefaultArr);

		$button = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_directory','','" . FILE_TABLE . "','','','object_add_workspace','','','',1)");

		$addbut = $button;

		$obj = new we_chooser_multiDirTemplateAndDefault(450, $this->Workspaces, "object_del_workspace", $addbut, get_ws(FILE_TABLE), $this->Templates, 'we_' . $this->Name . '_Templates', "", get_ws(TEMPLATES_TABLE), 'we_' . $this->Name . '_DefaultWorkspaces', $this->DefaultWorkspaces);
		$obj->CanDelete = true;
		$obj->create = 1;
		$content = $obj->get($jsCmd);

		if(!empty($GLOBALS['WE_DEL_WORKSPACE_ERROR'])){
			unset($GLOBALS['WE_DEL_WORKSPACE_ERROR']);
			$jsCmd->addMsg(g_l('weClass', '[we_del_workspace_error]'), we_base_util::WE_MESSAGE_ERROR);
		}
		return $content;
	}

	private function formWorkspacesFlag(){
		return '<div style="margin-bottom:8px;">' . we_html_forms::radiobutton(1, $this->WorkspaceFlag == 1, "we_" . $this->Name . "_WorkspaceFlag", g_l('modules_object', '[behaviour_all]')) . '</div><div>' .
			we_html_forms::radiobutton(0, $this->WorkspaceFlag != 1, "we_" . $this->Name . "_WorkspaceFlag", g_l('modules_object', '[behaviour_no]')) . '</div>';
	}

	private function formCSS(we_base_jsCmd $jsCmd){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_document', 0, '" . FILE_TABLE . "','','','object_add_css','','','" . we_base_ContentTypes::CSS . "', 1,1)");
		$css = new we_chooser_multiDir(510, $this->CSS, "object_del_css", $addbut, "", "ContentType", FILE_TABLE);
		return $css->get($jsCmd);
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$rootDIrID = 0;

		$but = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . $this->Table . "','" . $idname . "','','copyDocumentSelect','','" . $rootDIrID . "','" . $this->ContentType . "');");
		return we_html_element::htmlHidden($idname, $this->CopyID) . $but;
	}

	function copyDoc($id){
		if(!$id){
			return false;
		}
		$doc = new we_object();
		$doc->InitByID($id, $this->Table, self::LOAD_TEMP_DB);
		if($this->ID == 0){
			foreach($this->persistent_slots as $name){
				$this->{$name} = isset($doc->{$name}) ? $doc->{$name} : '';
			}
			$this->CreationDate = time();
			$this->CreatorID = $_SESSION['user']["ID"];
			$this->ID = 0;
			$this->OldPath = '';
			$this->Published = 1;
			$this->Text .= '_copy';
			$this->Path = $this->ParentPath . $this->Text;
			$this->OldPath = $this->Path;
		}
		$this->elements = $doc->elements;
		foreach($this->elements as $n => $e){
			if(strtolower(substr($n, 0, 9)) === 'wholename'){
				$this->setElement('neuefelder', array_merge($this->getElement('neuefelder', 'dat', []), $e['dat']));
			}
		}
		$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
		$this->Category = $doc->Category;
		return true;
	}

	function changeTempl_ob($nr, $id){
		$arr = explode(',', $this->Templates);
		$arr[$nr] = $id;

		$this->Templates = implode(',', $arr);
	}

	function add_workspace(array $ids){
		$wsp = trim($this->Workspaces, ',');
		$this->Workspaces = implode(',', ($wsp !== '' ? array_unique(array_merge(explode(',', $wsp), $ids), SORT_NUMERIC) : $ids));
	}

	function del_workspace($id){
		if(f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=0 AND TableID=' . intval($this->ID) . ' AND FIND_IN_SET(' . intval($id) . ',Workspaces) LIMIT 1', '', $this->DB_WE)){
			$GLOBALS['WE_DEL_WORKSPACE_ERROR'] = true;
			return;
		}

		$workspaces = explode(',', trim($this->Workspaces, ','));
		$defaultWorkspaces = explode(',', trim($this->DefaultWorkspaces, ','));
		$Templates = explode(',', trim($this->Templates, ','));
		for($i = 0; $i < count($workspaces); $i++){
			if($workspaces[$i] == $id){
				unset($workspaces[$i]);
				if(in_array($id, $defaultWorkspaces)){
					unset($defaultWorkspaces[array_search($id, $defaultWorkspaces)]);
				}
				unset($Templates[$i]);
				break;
			}
		}

		$this->Workspaces = implode(',', $workspaces);
		$this->DefaultWorkspaces = implode(',', $defaultWorkspaces);
		$this->Templates = implode(',', $Templates);
	}

	public function we_initSessDat($sessDat){
//	charset must be in other namespace -> for header !!!
		$this->setElement('Charset', (isset($sessDat[0]['SerializedArray']['elements']['Charset']) ? $sessDat[0]['SerializedArray']['elements']['Charset']["dat"] : ""), 'attrib');
		parent::we_initSessDat($sessDat);
		$this->setSort();
	}

	protected function i_getContentData(){
		if(!$this->ID){
			return;
		}
		$f = 0;
		$rec = getHash('SELECT DefaultCategory,DefaultText,DefaultDesc,DefaultTitle,DefaultUrl,DefaultUrlfield0,DefaultUrlfield1,DefaultUrlfield2,DefaultUrlfield3,DefaultTriggerID,DefaultKeywords,DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->ID, $this->DB_WE);

		$this->setSort();

		$this->DefaultValues = $rec['DefaultValues'];

		$vals = we_unserialize($this->DefaultValues);
		$names = array_keys($vals);

		foreach($names as $name){
			if($name === 'WE_CSS_FOR_CLASS'){
				$this->CSS = $vals[$name];
			}
			if(isset($vals[$name]) && is_array($vals[$name])){
				$this->setElement($name . 'count', ((!empty($vals[$name]['meta'])) ? (count($vals[$name]['meta']) - 1) : 0));
				if(isset($vals[$name]['meta']) && is_array($vals[$name]['meta'])){
					$keynames = array_keys($vals[$name]['meta']);
					for($ll = 0; $ll <= count($vals[$name]['meta']); $ll++){
						$this->setElement($name . 'defaultkey' . $ll, (isset($keynames[$ll]) ? $keynames[$ll] : ""));
						$this->setElement($name . 'defaultvalue' . $ll, (isset($keynames[$ll]) ? $vals[$name]["meta"][$keynames[$ll]] : ""));
					}
				}
			}
		}

		$this->DefaultCategory = $rec['DefaultCategory'];
		$this->Category = $this->DefaultCategory;
		$this->SerializedArray = we_unserialize($rec['DefaultValues']);

//	charset must be in other namespace -> for header !!!
		$this->setElement('Charset', (empty($this->SerializedArray['elements']['Charset']['dat']) ? '' : $this->SerializedArray['elements']['Charset']['dat']));

		$this->WorkspaceFlag = empty($this->SerializedArray['WorkspaceFlag']) ? '' : $this->SerializedArray['WorkspaceFlag'];
		$this->setElement('title', $rec['DefaultTitle']);
		$this->setElement('desc', $rec['DefaultDesc']);
		$this->setElement('keywords', $rec['DefaultKeywords']);

		$this->DefaultText = $rec['DefaultText'];
		$this->DefaultUrl = $rec['DefaultUrl'];

		$this->setElement('urlfield0', $rec['DefaultUrlfield0']);
		$this->setElement('urlfield1', $rec['DefaultUrlfield1']);
		$this->setElement('urlfield2', $rec['DefaultUrlfield2']);
		$this->setElement('urlfield3', $rec['DefaultUrlfield3']);
		$this->setElement('triggerid', $rec['DefaultTriggerID']);
		$this->DefaultTriggerID = $rec['DefaultTriggerID'];

		$ctable = OBJECT_X_TABLE . intval($this->ID);
		$tableInfo = $this->DB_WE->metadata($ctable);
		$fields = ['max' => '',
			'default' => '',
			'defaultThumb' => '',
			'autobr' => '',
			'rootdir' => '',
			'defaultdir' => '',
			'dhtmledit' => 'off',
			'showmenus' => 'off',
			'commands' => '',
			'contextmenu' => '',
			'height' => 50,
			self::ELEMENT_WIDTH => 200,
			'bgcolor' => '',
			'class' => '',
			'cssClasses' => '',
			'fontnames' => '',
			'fontsizes' => '',
			'formats' => '',
			'tinyparams' => '',
			'templates' => '',
			'xml' => '',
			'removefirstparagraph' => '',
			'forbidhtml' => 'off',
			'forbidphp' => 'off',
			'inlineedit' => '',
			'users' => '',
			'required' => '',
			'editdescription' => '',
			'int' => '',
			'intID' => '',
			'hreftype' => '',
			'hreffile' => '',
			'hrefdirectory' => 'false',
			'shopcatField' => '',
			'shopcatShowPath' => 'true',
			'shopcatRootdir' => '',
			'shopcatLimitChoice' => 0,
			'intPath' => '',
		];
		$sort = $this->getElement('we_sort');

		foreach($tableInfo as $info){
			list($type, $name) = explode('_', $info['name'], 2);
			if($name && $type != 'OF' && $type != 'variant'){

				$this->setElement($info['name'], $name, 'dat');
				$this->setElement('wholename' . array_search($f, $sort), $info['name'], 'dat');
				$this->setElement($info['name'] . self::ELEMENT_LENGHT, $info["len"], 'dat');
				$this->setElement($info['name'] . self::ELEMENT_TYPE, $type, 'dat');
				$typeLen = strtoupper($info['type']) . (strpos($info['flags'], 'unsigned') !== false || (defined('MYSQLI_UNSIGNED_FLAG') && (($info['flags'] & MYSQLI_UNSIGNED_FLAG) > 0)) ? '_U' : '');
				$this->setElement($info['name'] . 'typeLen', $typeLen, 'dat');

				if(isset($vals[$info['name']]['variant'])){
					$this->setElement($info['name'] . 'variant', $vals[$info['name']]['variant']);
				}
				foreach($fields as $field => $def){
					$this->setElement($info['name'] . $field, isset($vals[$info['name']][$field]) ? $vals[$info['name']][$field] : $def);
				}

				$f++;
			}
		}
	}

	protected function i_set_PersistentSlot($name, $value){
		if(in_array($name, $this->persistent_slots)){
			$this->$name = $value;
			return;
		}
		if($name === 'Templates_0'){
			$tmp = $dfWs = [];
			$wsp = makeArrayFromCSV($this->Workspaces);
			foreach($wsp as $i => $cur){
				$tmp[] = we_base_request::_(we_base_request::INT, "we_" . $this->Name . "_Templates_" . $i);
				if(we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_DefaultWorkspaces_' . $i) !== false){
					$dfWs[] = $cur;
				}
			}
			$this->Templates = implode(',', $tmp);
			$this->DefaultWorkspaces = implode(',', $dfWs);
		}
	}

	protected function i_setText(){
// do nothing here!
	}

	protected function i_filenameEmpty(){
		return ($this->Text === '');
	}

	protected function i_filenameNotValid(){
		static $allowedReplace = [
			'%ID%',
			'%d%',
			'%j%',
			'%m%',
			'%y%',
			'%Y%',
			'%n%',
			'%h%',
			'%H%',
			'%g%',
			'%G%',
			'%unique%'
		];

		$this->resetElements();
		while((list($k, $v) = $this->nextElement('defaultText'))){
			if(substr($k, 0, 12) === 'DefaultText_'){
				$end = substr($k, 12, strlen($k));
				if(isset($_REQUEST['textwert_' . $end]) && !empty($v['dat']) && !in_array($v['dat'], $allowedReplace) && preg_match('/[^\w\-.]/', $v['dat'])){
					return true;
				}
			}
		}
		return (preg_match('/[^\w\-.]/', $this->Text));
	}

	protected function i_filenameNotAllowed(){
		return false;
	}

	protected function i_filenameDouble(){
		return f('SELECT 1 FROM ' . $this->Table . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Text="' . $this->DB_WE->escape($this->Text) . '" AND ID!=' . intval($this->ID), '', $this->DB_WE);
	}

	function i_checkPathDiffAndCreate(){
		return true;
	}

	protected function i_hasDoubbleFieldNames(){
		$sort = $this->getElement('we_sort');
		if(empty($sort)){
			return false;
		}
		$usedNames = [];
		foreach(array_keys($sort) as $elem){
			$name = $this->getElement('wholename' . $elem);
			$foo = $this->getElement($name, 'dat');
			if(empty($foo)){ //we don't allow field names evaluating to false, report them as double ;-)
				return false;
			}
			if(!in_array($foo, $usedNames)){
				$usedNames[] = $foo;
				continue;
			}
			switch($this->getElement($name . 'dtype', 'dat')){
				case we_objectFile::TYPE_OBJECT:
					return f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $foo, '', $this->DB_WE);
				default:
					return $foo;
			}
		}
		return false;
	}

	protected function i_triggerIdNotValdid(){
		return $this->DefaultUrl && (!$this->DefaultTriggerID || !f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->DefaultTriggerID) . ' AND IsFolder=0', '', $this->DB_WE));
	}

	protected function i_triggerDocNotDynamic(){
		return $this->DefaultUrl && (!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->DefaultTriggerID) . ' AND IsFolder=0 AND IsDynamic=1', '', $this->DB_WE));
	}

	function includedObjectHasDoubbleFieldNames($incClass){
		$sort = $this->getElement('we_sort');
		$usedNames = $doubleNames = [];
		if(is_array($sort)){
			foreach(array_keys($sort) as $elem){
				$foo = $this->getElement($this->getElement('wholename' . $elem), 'dat');
				$usedNames[] = $foo;
			}
		}
		$incclassobj = new we_object();
		$incclassobj->initByID($incClass, $this->Table);
		$isort = $incclassobj->getElement('we_sort');
		if(is_array($isort) && $isort){
			foreach(array_keys($isort) as $elem){
				$foo = $incclassobj->getElement($incclassobj->getElement('wholename' . $elem), 'dat');
				if(in_array($foo, $usedNames)){
					$doubleNames[] = $foo;
				}
			}
		}
		return ($doubleNames ?: false);
	}

	protected function i_writeDocument(){
		return true; // we don't have to write!
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		if(!$_REQUEST){
			return;
		}
		$regs = [];
		$hrefFields = false;

		foreach(array_keys($_REQUEST) as $n){
			if(preg_match('/^we_' . $this->Name . '_(' . we_objectFile::TYPE_HREF . ')$/', $n, $regs)){
				${$regs[1] . 'Fields'} |= true;
			}
		}

		if($hrefFields){
			$empty = ['int' => 1, 'intID' => '', 'intPath' => '', 'extPath' => ''];
			$hrefs = $match = [];
			foreach($_REQUEST['we_' . $this->Name . '_' . we_objectFile::TYPE_HREF] as $k => $val){
				if(preg_match('|^(.+)' . we_base_link::MAGIC_INFIX . '(.+)$|', $k, $match)){
					$hrefs[$match[1]][$match[2]] = $val;
				}
			}
			foreach($hrefs as $k => $v){
				$href = array_merge($empty, $v);
				$this->setElement($k, we_serialize($href, SERIALIZE_JSON), we_objectFile::TYPE_HREF);
			}
		}
	}

	public function we_save($resave = false, $skipHook = false){
		$this->save();
		if(!$resave){
			we_base_history::insertIntoHistory($this);
		}
		/* hook */
		$this->unregisterMediaLinks();
		$ret = $this->registerMediaLinks(true, false);
		if($skipHook){
			return true;
		}
		$hook = new we_hook_base('save', '', [$this, 'resave' => $resave]);
//check if doc should be saved
		if($hook->executeHook() === false){
			$this->errMsg = $hook->getErrorString();
			return false;
		}
		return true;
	}

	function registerMediaLinks($temp = false, $linksReady = false){// FIXME: publish is obsolete for classes
		$serializedArray = is_array($this->SerializedArray) ? $this->SerializedArray : [];
		foreach($serializedArray as $k => $v){
			if(count(($typeName = explode('_', $k, 2))) > 1){
				switch($typeName[0]){
					case 'binary':
					case 'flashmovie':
					case 'img':
						if($v['default']){
							$this->MediaLinks[$typeName[0] . '[name=' . $typeName[1] . ']'] = $v['default'];
						}
						break;
					case 'link':
						$default = we_unserialize($v['default'], ['type' => 0, 'ctype' => 0]);
						if($default['type'] === 'int' && $default['id']){
							$this->MediaLinks[$typeName[0] . '[name=' . $typeName[1] . ']'] = $default['id'];
						}
						if($default['ctype'] === 'int' && $default['img_id']){
							$this->MediaLinks[$typeName[0] . '[name=' . $typeName[1] . ']'] = $default['img_id'];
						}
						break;
					case 'text':
						if($v['dhtmledit'] == 'on' || $v['dhtmledit'] === true){//FIXME: make bool!
							$this->MediaLinks = array_merge($this->MediaLinks, we_wysiwyg_editor::reparseInternalLinks($v['default'], false, $typeName[1]));
						}
						break;
					default:
					//
				}
			}
		}

		return parent::registerMediaLinks(false, true);
	}

	/**
	 * @return	if the field can have variants the function returns true otherwise false
	 * @param	$field - the name of the field
	 */
	function isVariantField($field){
		$types = [we_objectFile::TYPE_INPUT, we_objectFile::TYPE_LINK, we_objectFile::TYPE_TEXT, we_objectFile::TYPE_IMG, we_objectFile::TYPE_INT, we_objectFile::TYPE_FLOAT,
			we_objectFile::TYPE_META, we_objectFile::TYPE_DATE, we_objectFile::TYPE_HREF]; // #6924
		$type = ($this->getElement($field . self::ELEMENT_TYPE, 'dat') != '') ? $this->getElement($field . self::ELEMENT_TYPE, 'dat') : '';
		return in_array($type, $types);
	}

	/**
	 * @return	the function returns the number of variant fields
	 */
	function hasVariantFields(){
		$tmp = $this->getVariantFields();
		return !empty($tmp);
	}

	/**
	 * if document can have variants the function returns true, otherwise false
	 *
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @param boolean $checkFields
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false){
		return true;
	}

	private function loadDefaultsValues(){
		if($this->DefaultValues){
			return;
		}
		$this->DefaultValues = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->ID, $this->DB_WE);
	}

	/**
	 * @desc 	the function returns the array with all object field names
	 * @return	array with the filed names and attributes
	 */
	function getAllVariantFields(){
		$return = [];
		$this->loadDefaultsValues();
		$fields = we_unserialize($this->DefaultValues);
		foreach($fields as $name => $field){
			if($this->isVariantField($name)){
				$return[$name] = $field;
			}
		}
		return $return;
	}

	/**
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
		$return = [];
		$fields = $this->getAllVariantFields();
		foreach($fields as $name => $field){
			if(isset($field['variant']) && $field['variant'] == 1){
				$return[$name] = $field;
			}
		}
		return $return;
	}

	/* creates the DirectoryChoooser field with the 'browse'-Button. Clicking on the Button opens the fileselector */

	protected function formDirChooser($width = '', $rootDirID = 0, $table = '', $Pathname = 'ParentPath', $IDName = 'ParentID', $cmd = '', $pathID = 0, $identifier = ''){
		$path = id_to_path($pathID);

		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname . ($identifier ? '_' . $identifier : '');
		$idname = 'we_' . $this->Name . '_' . $IDName;

		// Important: custom callback commands must call we_cmd('pathOfDocumentChanged') at the beginning
		$cmd = $cmd ?: 'pathOfDocumentChanged';
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $idname . "','" . $textname . "','" . $cmd . "','','" . $rootDirID . "')");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($textname, 30, $path, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden($idname, $pathID), $button);
	}

	function userCanSave($ctConditionOk = false){
		if(we_base_permission::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$ownersReadOnly = makeArrayFromCSV($this->UsersReadOnly);
		$readers = [];
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1){
				$readers[] = $key;
			}
		}
		return parent::userCanSave() && !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	static function isUsedByObjectFile($id){
		return $id && f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE IsClassFolder=0 AND TableID=' . intval($id) . ' LIMIT 1');
	}

	public function getDocumentCss(){
		return id_to_path($this->CSS, FILE_TABLE, null, true);
	}

	public function getPropertyPage(we_base_jsCmd $jsCmd){
		if($this->EditPageNr != we_base_constants::WE_EDITPAGE_WORKSPACE){
			$parts = [['headline' => g_l('weClass', '[path]'),
				'html' => $this->formPath(),
				'space' => we_html_multiIconBox::SPACE_ICON,
				'icon' => we_html_multiIconBox::PROP_PATH
				],
				['headline' => g_l('modules_object', '[default]'),
					'html' => $this->formDefault($jsCmd),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_ATTRIB
				],
				['headline' => g_l('weClass', '[Charset]'),
					'html' => $this->formCharset(),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_CHARSET
				],
				['headline' => g_l('weClass', '[CSS]'),
					'html' => $this->formCSS($jsCmd),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_OTHER
				],
				['headline' => g_l('modules_object', '[copyClass]'),
					'html' => $this->formCopyDocument(),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_COPY
				]
			];
		} else {
			$parts = [
				['headline' => g_l('weClass', '[workspaces]'),
					'html' => $this->formWorkspaces($jsCmd),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_WORKSPACE
				],
				['headline' => g_l('modules_object', '[behaviour]'),
					'html' => $this->formWorkspacesFlag(),
					'space' => we_html_multiIconBox::SPACE_ICON,
					'icon' => we_html_multiIconBox::PROP_DISPLAY
				]
			];
		}
		return we_html_multiIconBox::getHTML('PropertyPage', $parts);
	}

}
