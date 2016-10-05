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
class we_backup_object extends we_object{
	private $_ObjectBaseElements = ['ID', 'OF_ID', 'OF_ParentID', 'OF_Text', 'OF_Path', 'OF_Url', 'OF_TriggerID', 'OF_Workspaces', 'OF_ExtraWorkspaces', 'OF_ExtraWorkspacesSelected',
		'OF_Templates', 'OF_Category', 'OF_Published', 'OF_IsSearchable', 'OF_Charset', 'OF_WebUserID', 'OF_Language', 'variant_weInternVariantElement'
	];
	protected $isAddFieldNoSave = false;
	protected $isModifyFieldNoSave = false;
	protected $isDropFieldNoSave = false;
	protected $isForceDropOnSave = false;

	public function __construct(){
		parent::__construct();
		$this->ClassName = 'we_object'; //for we_backup_object, otherwise ist will save its own classname, or needs its own constructor
	}

	function saveToDB(){
		$this->wasUpdate = $this->ID > 0;

		$this->i_savePersistentSlotsToDB();
		$ctable = OBJECT_X_TABLE . intval($this->ID);

		if(!$this->wasUpdate){
			$qarr = ['OF_ID' => 'INT unsigned NOT NULL',
			];

			$indexe = ['PRIMARY KEY (OF_ID)',
			];

			$this->SerializedArray = we_unserialize($this->DefaultValues);
			$this->SerializedArray = is_array($this->SerializedArray) ? $this->SerializedArray : [];

			$noFields = ['WorkspaceFlag', 'elements', 'WE_CSS_FOR_CLASS'];
			foreach($this->SerializedArray as $key => $value){
				if(!in_array($key, $noFields)){
					$arr = explode('_', $key);
					$len = isset($value['length']) ? $value['length'] : $this->getElement($key . 'length', "dat");
					$type = $this->switchtypes2($arr[0], $len);
					if(!empty($type)){
						$qarr[$key] = $type;
//add index for complex queries
						if($arr[0] === 'object'){
							$indexe[] = 'KEY ' . $key . ' (' . $key . ')';
						}
					}
				}
			}

			$this->DB_WE->delTable($ctable);
			$this->DB_WE->addTable($ctable, $qarr, $indexe);


// folder in object schreiben
			if(!($this->OldPath && ($this->OldPath != $this->Path))){
				$fold = new we_class_folder();
				$fold->initByPath($this->getPath(), OBJECT_FILES_TABLE);
			}
		} else {
			$this->SerializedArray = we_unserialize($this->DefaultValues);
			$this->SerializedArray = is_array($this->SerializedArray) ? $this->SerializedArray : [];

			$noFields = ['WorkspaceFlag', 'elements', 'WE_CSS_FOR_CLASS'];
			$tableInfo = $this->DB_WE->metadata($ctable, we_database_base::META_FULL);

			$add = $drop = $alter = $addKey = [];

			foreach($this->SerializedArray as $fieldname => $value){
				$arr = explode('_', $fieldname);
				if(!isset($arr[1])){
					continue;
				}

				$fieldtype = $this->getFieldType($arr[0]);
				$len = (isset($value['length']) ? $value['length'] : 0);
				$type = $this->switchtypes2($arr[0], $len);
				$isObject = ($arr[0] === 'object');

				if(isset($tableInfo['meta'][$fieldname])){
					$props = $tableInfo[$tableInfo['meta'][$fieldname]];
// the field exists
					if(!empty($fieldtype) && (strtolower($fieldtype) == strtolower($props['type'])) && $len != $props['len']){
						$alter[$fieldname] = $type;
					}
				} elseif(!empty($type)){
					$add[$fieldname] = $type;
					if($isObject){
						$addKey[$fieldname . '_key'] = ' INDEX (`' . $fieldname . '`)';
					}
				}
			}

			if(isset($tableInfo['meta'])){
				foreach($tableInfo['meta'] as $key => $value){
					if(!isset($this->SerializedArray[$key]) && substr($key, 0, 3) != 'OF_' && $key != 'ID'){
						$drop[$key] = $key;
					}
				}
			}

//FIXME: deactivated for #9899 - some elements are not present (e.g. object-references) & will be deleted therefore
//With $this->isForceDropOnSave drops can be activated
			if($this->isForceDropOnSave){
				foreach($drop as $key => $value){
					$this->DB_WE->delCol($ctable, $value);
				}
			}

			foreach($alter as $key => $value){
				$this->DB_WE->changeColType($ctable, $key, $value);
			}

			foreach($add as $key => $value){
				$this->DB_WE->addCol($ctable, $key, $value);
			}
			foreach($addKey as $key => $value){
				$this->DB_WE->addKey($ctable, $value);
			}
		}

		unset($this->elements);
		$this->i_getContentData();
	}

	function getFieldType($type){
		switch($type){
			case 'country':
			case 'language':
			case 'meta':
			case 'link':
				return 'meta';
			case 'href':
			case 'input':
				return 'string';
			case 'float':
				return 'real';
			case 'img':
			case 'flashmovie':
			case 'binary':
			case 'object':
			case 'date':
			case 'checkbox':
			case 'int':
				return 'int';
			case 'text':
				return 'blob';
		}
		return '';
	}

	private function switchtypes2($type, $len = 0){
		switch($type){
			case "meta":
				return " VARCHAR(" . (($len > 0 && ($len < 256)) ? $len : 255) . ") NOT NULL ";
			case "date":
				return " INT unsigned NOT NULL ";
			case "input":
				return " VARCHAR(" . (($len > 0 && ($len < 4096)) ? $len : 255) . ") NOT NULL ";
			case "country":
			case "language":
				return " CHAR(2) NOT NULL ";
			case "link":
			case "href":
				return " TEXT NOT NULL ";
			case "text":
				return " LONGTEXT NOT NULL ";
			case "img":
			case "flashmovie":
			case "binary":
				return " INT unsigned DEFAULT '0' NOT NULL ";
			case "checkbox":
				return " tinyint unsigned DEFAULT '0' NOT NULL";
			case "int":
				return " INT(" . (($len > 0 && ($len < 256)) ? $len : "11") . ") DEFAULT NULL ";
			case "float":
				return " DOUBLE DEFAULT NULL ";
			case "object":
				return " INT unsigned DEFAULT '0' NOT NULL ";
			case we_objectFile::TYPE_MULTIOBJECT:
				return " TEXT NOT NULL ";
			case 'shopVat':
				return ' TEXT NOT NULL';
		}
		return '';
	}

	function isFieldExists($name, $type = ''){
		$this->SerializedArray = we_unserialize($this->DefaultValues);
//		$noFields = ['WorkspaceFlag', 'elements', 'WE_CSS_FOR_CLASS'];
		foreach(array_keys($this->SerializedArray) as $fieldname){
			$arr = explode('_', $fieldname);
			if(!isset($arr[0])){
				continue;
			}
			$fieldtype = $arr[0];
			unset($arr[0]);
			$fieldname = implode('_', $arr);
			if($type === ''){
				if($fieldname == $name){
					return true;
				}
			} elseif($fieldname == $name && $fieldtype == $type){
				return true;
			}
		}
		return false;
	}

	function getFieldPrefix($name){
		$this->SerializedArray = we_unserialize($this->DefaultValues);
//$noFields = array('WorkspaceFlag', 'elements', 'WE_CSS_FOR_CLASS');
		foreach(array_keys($this->SerializedArray) as $fieldname){
			$arr = explode('_', $fieldname);
			if(!isset($arr[1])){
				continue;
			}
			$fieldtype = $arr[0];
			unset($arr[0]);
			$fieldname = implode('_', $arr);
			if($fieldname == $name){
				return $fieldtype;
			}
			return false;
		}
	}

	function getDefaultArray($name, $type = '', $default = ''){
		$defaultArr = ['default' => '',
			'defaultThumb' => '',
			'defaultdir' => '',
			'rootdir' => '',
			'autobr' => '',
			'dhtmledit' => '',
			'commands' => '',
			'contextmenu' => '',
			'height' => 200,
			'width' => 618,
			'class' => '',
			'max' => '',
			'cssClasses' => '',
			'fontnames' => '',
			'fontsizes' => '',
			'formats' => '',
			'tinyparams' => '',
			'templates' => '',
			'xml' => '',
			'removefirstparagraph' => '',
			'showmenus' => '',
			'forbidhtml' => '',
			'forbidphp' => '',
			'inlineedit' => '',
			'users' => '',
			'required' => '',
			'editdescription' => '',
			'int' => '',
			'intID' => '',
//			'intPath' => '',
			'hreftype' => '',
			'hrefdirectory' => '',
			'hreffile' => '',
			'shopcatField' => '',
			'shopcatShowPath' => 'true',
			'shopcatRootdir' => '',
			'shopcatLimitChoice' => 0,
			'uniqueID' => md5(uniqid(__FUNCTION__, true)),
		];
		switch($type){
			case 'text':
			case 'input':
			case 'int':
				$defaultArr['meta'] = [$type . '_' . $name . 'defaultkey0' => ''];
				break;
			case we_objectFile::TYPE_MULTIOBJECT:
				$defaultArr['meta'] = [''];
				break;
		}

		if($default != '' && is_array($default)){
			foreach($default as $k => $v){
				$defaultArr[$k] = $v;
			}
		}
		return $defaultArr;
	}

	function renameField($name, $newname){
		$ctable = OBJECT_X_TABLE . intval($this->ID);
		$this->wasUpdate = true;
		$this->SerializedArray = we_unserialize($this->DefaultValues);
		$type = $this->getFieldPrefix($name);
		$this->SerializedArray[$type . '_' . $newname] = $this->SerializedArray[$type . '_' . $name];
		unset($this->SerializedArray[$type . '_' . $name]);
		$this->DefaultValues = we_serialize($this->SerializedArray);
		$this->DB_WE->renameCol($ctable, $type . '_' . $name, $type . '_' . $newname);
		$this->DB_WE->changeColType($ctable, $type . '_' . $newname, $this->switchtypes2($type));
		unset($this->elements);
		$this->i_savePersistentSlotsToDB();
		$this->i_getContentData();
	}

	function addField($name, $type = '', $default = ''){
		$defaultArr = $this->getDefaultArray($name, $type, $default);
		$this->SerializedArray = we_unserialize($this->DefaultValues);
		$this->SerializedArray[$type . '_' . $name] = $defaultArr;
		$this->DefaultValues = we_serialize($this->SerializedArray);
		$ord = $this->getElement('we_sort', 'dat', []);
		$ord[] = max($ord) + 1;
		$this->setElement('we_sort', $ord);
		if($this->isAddFieldNoSave){
			return true;
		}
		return $this->saveToDB(true);
	}

	function dropField($name, $type = ''){
		$this->SerializedArray = we_unserialize($this->DefaultValues);
		$isfound = false;
		foreach($this->SerializedArray as $field => $value){
			$arr = explode('_', $field);
			if(!isset($arr[1])){
				continue;
			}
			$fieldtype = $arr[0];
			unset($arr[0]);
			$fieldname = implode('_', $arr);
			if($type === ''){
				if($fieldname == $name){
					unset($this->SerializedArray[$field]);
					$isfound = true;
					break;
				}
			} elseif($fieldname == $name && $fieldtype == $type){
				unset($this->SerializedArray[$field]);
				$isfound = true;
				break;
			}
		}
		if($isfound){
			$this->DefaultValues = we_serialize($this->SerializedArray);
			$ord = $this->getElement('we_sort');

			unset($ord[array_search(max($ord), $ord)]);

			$this->setElement('we_sort', $ord);
			if($this->isDropFieldNoSave){
				return true;
			}
			return $this->saveToDB(true);
		}

		return false;
	}

	function modifyField($name, $newtype, $type, $default = '', $delete = ''){
		$this->SerializedArray = we_unserialize($this->DefaultValues);
		$defaultArr = $this->SerializedArray[$type . '_' . $name];
		if($newtype == $type){
			if($default != '' && is_array($default)){
				foreach($default as $k => $v){
					$defaultArr[$k] = $v;
				}
				if($delete != '' && is_array($delete)){
					foreach($delete as $delkey){
						unset($defaultArr[$delkey]);
					}
				}
			} else {
				$defaultArr = $this->getDefaultArray($name, $newtype, $default);
			}
			$this->SerializedArray[$type . '_' . $name] = $defaultArr;
		} else {
			unset($this->SerializedArray[$type . '_' . $name]);
			if($default != '' && is_array($default)){
				foreach($default as $k => $v){
					$defaultArr[$k] = $v;
				}
				if($delete != '' && is_array($delete)){
					foreach($delete as $delkey){
						unset($defaultArr[$delkey]);
					}
				}
			} else {
				$defaultArr = $this->getDefaultArray($newtype, $default);
			}
			$this->SerializedArray[$newtype . '_' . $name] = $defaultArr;
		}
		$this->DefaultValues = we_serialize($this->SerializedArray);

		return ($this->isModifyFieldNoSave ?
			true :
			$this->saveToDB(true));
	}

	private function resetOrder(){
		unset($this->elements['we_sort']);
		$this->setSort();
	}

	function checkFields($fields){
		$ctable = OBJECT_X_TABLE . intval($this->ID);
		$metadata = $this->DB_WE->metadata($ctable, we_database_base::META_FULL);
		$metas = array_keys($metadata['meta']);
		$consider = array_diff($metas, $this->_ObjectBaseElements);
		$this->resetOrder();
		$theKeys = $this->getElement('we_sort');

		if(count($theKeys) == count($consider)){
			$consider = array_combine($theKeys, $consider);
			$isOK = true;
			foreach($fields as $field){
				if(!in_array($field, $consider)){
					t_e('warning', __METHOD__ . ' ' . $ctable . ' (' . $this->Text . ')  Field ' . $field . ' not found');
					$isOK = false;
				}
			}
			return $isOK;
		}
		t_e('warning', __METHOD__ . ' ' . $ctable . ' (' . $this->Text . ') different field count - not recoverable bei resetOrder');
	}

	/* setter for for property isAddFieldNoSave which allows to construct Classes from within Apps */

	function setIsAddFieldNoSave($isAddFieldNoSave){
		$this->isAddFieldNoSave = $isAddFieldNoSave;
	}

	/* getter for for property isAddFieldNoSave which allows to construct Classes from within Apps */

	function getIsAddFieldNoSave(){
		return $this->isAddFieldNoSave;
	}

	/* setter for for property isModifyFieldNoSave which allows to construct Classes from within Apps */

	function setIsModifyFieldNoSave($isModifyFieldNoSave){
		$this->isModifyFieldNoSave = $isModifyFieldNoSave;
	}

	/* getter for property isModifyFieldNoSave which allows to construct Classes from within Apps */

	function getIsModifyFieldNoSave(){
		return $this->isModifyFieldNoSave;
	}

	/* setter for property isDropFieldNoSave which allows to construct Classes from within Apps */

	function setIsDropFieldNoSave($isDropFieldNoSave){
		$this->isDropFieldNoSave = $isDropFieldNoSave;
	}

	/* getter for property isDropFieldNoSave which allows to construct Classes from within Apps */

	function getIsDropFieldNoSave(){
		return $this->isDropFieldNoSave;
	}

	/* setter for property isForceDropOnSave which allows to construct Classes from within Apps */

	function setIsForceDropOnSave($isForceDropOnSave){
		$this->isForceDropOnSave = $isForceDropOnSave;
	}

	/* getter for property isForceDropOnSave which allows to construct Classes from within Apps */

	function getIsForceDropOnSave(){
		return $this->isForceDropOnSave;
	}

}
