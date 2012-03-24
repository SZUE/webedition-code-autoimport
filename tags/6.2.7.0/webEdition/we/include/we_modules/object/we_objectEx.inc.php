<?php
/**
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

	include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/object/we_object.inc.php");

	class we_objectEx extends we_object{

		function we_objectEx() {
			$this->we_object();
		}

		function saveToDB(){

			$this->wasUpdate = $this->ID ? true : false;

			$this->i_savePersistentSlotsToDB();
			$ctable = OBJECT_X_TABLE.abs($this->ID);

			if(!$this->wasUpdate){
				$q = " ID BIGINT NOT NULL AUTO_INCREMENT, ";
				$q .= " OF_ID BIGINT NOT NULL, ";
				$q .= " OF_ParentID BIGINT NOT NULL, ";
				$q .= " OF_Text VARCHAR(255) NOT NULL, ";
				$q .= " OF_Path VARCHAR(255) NOT NULL, ";
				$q .= " OF_Url VARCHAR(255) NOT NULL, ";
				$q .= " OF_TriggerID  BIGINT NOT NULL  default '0', ";
				$q .= " OF_Workspaces VARCHAR(255) NOT NULL, ";
				$q .= " OF_ExtraWorkspaces VARCHAR(255) NOT NULL, ";
				$q .= " OF_ExtraWorkspacesSelected VARCHAR(255) NOT NULL, ";
				$q .= " OF_Templates VARCHAR(255) NOT NULL, ";
				$q .= " OF_ExtraTemplates VARCHAR(255) NOT NULL, ";
				$q .= " OF_Category VARCHAR(255) NOT NULL,";
				$q .= " OF_Published int(11) NOT NULL,";
				$q .= " OF_IsSearchable tinyint(1) NOT NULL default '1',";
				$q .= " OF_Charset VARCHAR(64) NOT NULL, ";
				$q .= " OF_WebUserID BIGINT NOT NULL, ";
				$q .= " OF_Language VARCHAR(5) default 'NULL', ";

				$indexe = ', KEY OF_WebUserID (OF_WebUserID), KEY `published` (`OF_ID`,`OF_Published`,`OF_IsSearchable`),KEY `OF_IsSearchable` (`OF_IsSearchable`)';

				$this->SerializedArray = unserialize($this->DefaultValues);

				$qarr = array();
				$noFields = array('WorkspaceFlag','elements','WE_CSS_FOR_CLASS');
				foreach ($this->SerializedArray as $key=>$value) {
					if(!in_array($key,$noFields)){
						$arr = explode('_',$key);
						$len = isset($value['length']) ? $value['length'] : $this->getElement($key."length","dat");
						$type = $this->switchtypes2($arr[0],$len);
						if(!empty($type)){
							$qarr[] = $key . $type;
							//add index for complex queries
							if($arr[0]=='object'){
								$indexe .= ', KEY '.$key.' ('.$key.')';
							}
						}
					}
				}

				$q .= implode(',',$qarr);

				// Charset and Collation
				$charset_collation = "";
				if (defined("DB_CHARSET") && DB_CHARSET != "" && defined("DB_COLLATION") && DB_COLLATION != "") {
					$Charset = DB_CHARSET;
					$Collation = DB_COLLATION;
					$charset_collation = " CHARACTER SET " . $Charset . " COLLATE " . $Collation;

				}

				$this->DB_WE->query("DROP TABLE IF EXISTS $ctable");
				$this->DB_WE->query("CREATE TABLE $ctable ($q, PRIMARY KEY (ID)$indexe)$charset_collation");

				//dummy eintrag schreiben
				$this->DB_WE->query("INSERT INTO $ctable (OF_ID) VALUES (0)");


				// folder in object schreiben
				if(!($this->OldPath && ($this->OldPath != $this->Path))){
					$fold = new we_class_folder();
					$fold -> initByPath($this->getPath(),OBJECT_FILES_TABLE,1,0);
				}

				////// resave the line O to O.....
		    	$this->DB_WE->query("DELETE FROM $ctable where OF_ID=0 OR ID=0");
		    	$this->DB_WE->query("INSERT INTO $ctable (OF_ID) VALUES(0)");
				////// resave the line O to O.....
			}else {
				$this->SerializedArray = unserialize($this->DefaultValues);

				$noFields = array('WorkspaceFlag','elements','WE_CSS_FOR_CLASS');
				$tableInfo = $this->DB_WE->metadata($ctable,true);
				$size = count($tableInfo);

				$add = array();
				$drop = array();
				$alter = array();

				foreach($this->SerializedArray as $fieldname=>$value){

					$arr = explode('_',$fieldname);
					if(!isset($arr[0])) continue;

					$fieldtype = $this->getFieldType($arr[0]);
					if(isset($value['length'])){
						$len = ($fieldtype == 'string') ? ($value['length']>255 ? 255 : $value['length']) : $value['length'];
					}else{
						$len=0;
					}
					$type = $this->switchtypes2($arr[0],$len);
					$isObject = ($arr[0]=='object');

					if(isset($tableInfo['meta'][$fieldname])){
						$props = $tableInfo[$tableInfo['meta'][$fieldname]];
						// the field exists
						if(!empty($fieldtype) && (strtolower($fieldtype) == strtolower($props['type']))){
							if($len!=$props['len']){
								$alter[$fieldname] = $fieldname . $type;
							}
						}
					} else {
						if(!empty($type)){
							$add[$fieldname] = $fieldname . $type;
							if($isObject){
								$add[$fieldname.'_key'] = ' INDEX ('.$fieldname.')';
							}
						}
					}

				}

				if (isset($tableInfo['meta'])) {

					foreach($tableInfo['meta'] as $key=>$value) {
						if(!isset($this->SerializedArray[$key]) && substr($key,0,3)!='OF_' && $key!='ID') {
							$drop[$key] = $key;
						}
					}
				}

				foreach($drop as $key=>$value) {
					$this->DB_WE->query("ALTER TABLE $ctable DROP $value;");
				}

				foreach($alter as $key=>$value) {
					$this->DB_WE->query("ALTER TABLE $ctable CHANGE $key $value;");
				}

				foreach($add as $key=>$value) {
					$this->DB_WE->query("ALTER TABLE $ctable ADD $value;");
				}

			}
			
			unset($this->elements);
			$this->i_getContentData();

		}


		function getFieldType($type) {
			switch($type){
				case "country":
				case "language":
				case "meta":
				case "input":
				case "link":
				case "href":
					return "string";
				case "float":
					return "real";
				case "img":
				case "flashmovie":
				case "quicktime":
				case "binary":
				case "object":
				case "date":
				case "checkbox":
				case "int":
					return  "int";
				case "text":
					return "blob";
			}
			return '';
		}

		function switchtypes2($type,$len){
			switch($type){
				case "meta":
					return " VARCHAR(".(($len>0 && ($len < 256))?$len:"255").") NOT NULL ";
				case "date":
					return " INT(11) NOT NULL ";
				case "input":
					return  " VARCHAR(".(($len>0 && ($len < 256))?$len:"255").") NOT NULL ";
				case "country":
				case "language":
				return " VARCHAR(2) NOT NULL ";
				case "link":
				case "href":
					return " TEXT NOT NULL ";
				case "text":
					return " LONGTEXT NOT NULL ";
				case "img":
				case "flashmovie":
				case "quicktime":
				case "binary":
					return  " INT(11) DEFAULT '0' NOT NULL ";
				case "checkbox":
					return " INT(1) DEFAULT '0' NOT NULL";
				case "int":
					return " INT(".(($len>0  && ($len < 256))?$len:"11").") DEFAULT NULL ";
				case "float":
					return " DOUBLE DEFAULT NULL ";
				case "object":
					return " BIGINT(20) DEFAULT '0' NOT NULL ";
				case "multiobject":
					return " TEXT NOT NULL ";
				case 'shopVat':
					return ' TEXT NOT NULL';
			}
			return '';
		}
		
		function isFieldExists($name,$type=''){
			$this->SerializedArray = unserialize($this->DefaultValues);
			$noFields = array('WorkspaceFlag','elements','WE_CSS_FOR_CLASS');
			foreach($this->SerializedArray as $fieldname=>$value){
				$arr = explode('_',$fieldname);
				if(!isset($arr[0])) continue;
				$fieldtype = $arr[0];
				unset($arr[0]);
				$fieldname=implode('_',$arr);
				if($type==''){
					if($fieldname==$name){
						return true;	
					}
				} else {
					if($fieldname==$name && $fieldtype==$type){
						return true;	
					}
				}
			}
			return false;
		}
		
		function getFieldPrefix($name){
			$this->SerializedArray = unserialize($this->DefaultValues);
			$noFields = array('WorkspaceFlag','elements','WE_CSS_FOR_CLASS');
			foreach($this->SerializedArray as $fieldname=>$value){
				$arr = explode('_',$fieldname);
				if(!isset($arr[0])) continue;
				$fieldtype = $arr[0];
				unset($arr[0]);
				$fieldname=implode('_',$arr);
				if($fieldname==$name){
					return $fieldtype;	
				}
			}
			return false;
		}
		function addField($name,$type='',$default=''){
			
			$defaultArr=array();
			$defaultArr['default'] ='';
			$defaultArr['defaultThumb'] = '';
			$defaultArr['defaultdir'] = '';
			$defaultArr['rootdir'] = '';
			$defaultArr['autobr'] = '';
			$defaultArr['dhtmledit'] = '';
			$defaultArr['commands'] = '';
			$defaultArr['height'] = '200';
			$defaultArr['width'] = '618';
			$defaultArr['class'] = '';
			$defaultArr['max'] = '';
			$defaultArr['cssClasses'] = '';
			$defaultArr['xml'] = '';
			$defaultArr['removefirstparagraph'] = '';
			$defaultArr['showmenus'] = '';
			$defaultArr['forbidhtml'] = '';
			$defaultArr['forbidphp'] = '';
			$defaultArr['inlineedit'] = '';
			$defaultArr['users'] = '';
			$defaultArr['required'] = '';
			$defaultArr['editdescription'] = '';
			$defaultArr['int'] = '';
			$defaultArr['intID'] = '';
			$defaultArr['intPath'] = '';
			$defaultArr['hreftype'] = '';
			$defaultArr['hrefdirectory'] = '';
			$defaultArr['hreffile'] = '';
			$defaultArr['uniqueID'] = md5(uniqid(rand(),1));
			switch ($type){
				case 'text':
				case 'input':
				case 'int':
					$defaultArr['meta']=array( $type.'_'.$name.'defaultkey0' =>'');
					break;
				case 'multiobject':	
					$defaultArr['meta']=array('');
					break;
			}
				
			if($default!='' && is_array($default)){
				foreach($default as $k => $v){
					$defaultArr[$k]=$v; 
				}	
			}
			$this->SerializedArray = unserialize($this->DefaultValues);
			$this->SerializedArray[$type.'_'.$name]=$defaultArr;
			$this->DefaultValues=serialize($this->SerializedArray);
			$arrOrder=explode(',',$this->strOrder);
			$arrOrder[]=max($arrOrder)+1;
			$this->strOrder=implode(',',$arrOrder);
			return $this->saveToDB(true);
			
		}
		function dropField($name,$type=''){
			$this->SerializedArray = unserialize($this->DefaultValues);
			$isfound=false;
			foreach($this->SerializedArray as $field=>$value){
				$arr = explode('_',$field);
				if(!isset($arr[0])) continue;
				$fieldtype = $arr[0];
				unset($arr[0]);
				$fieldname=implode('_',$arr);
				if($type==''){
					if($fieldname==$name){
						unset($this->SerializedArray[$field]);
						$isfound=true;
						break;
					}
				} else {p_r($fieldname);
					if($fieldname==$name && $fieldtype==$type){
						unset($this->SerializedArray[$field])	;
						$isfound=true;
						break;
					}
				}
			}
			if($isfound){
				$this->DefaultValues=serialize($this->SerializedArray);
				$arrOrder=explode(',',$this->strOrder);
				
				unset($arrOrder[array_search(max($arrOrder),$arrOrder)]);
				
				$this->strOrder=implode(',',$arrOrder);
				return $this->saveToDB(true);
				
			}
			
			return false;
		}
		function modifyField($name,$newtype,$type,$default='',$delete=''){
			$this->SerializedArray = unserialize($this->DefaultValues);
			$defaultArr = $this->SerializedArray[$type.'_'.$name];
			if($newtype==$type){
				if($default!='' && is_array($default)){
					foreach($default as $k => $v){
						$defaultArr[$k]=$v; 
					}
					if($delete!='' && is_array($delete)){
						foreach($delete as $delkey){
							unset($defaultArr[$delkey]);
						}
					}
					$this->SerializedArray[$type.'_'.$name]	= $defaultArr; 
				}
			} else {
				unset($this->SerializedArray[$type.'_'.$name]);
				if($default!='' && is_array($default)){
					foreach($default as $k => $v){
						$defaultArr[$k]=$v; 
					}
					if($delete!='' && is_array($delete)){
						foreach($delete as $delkey){
							unset($defaultArr[$delkey]);
						}
					}
					$this->SerializedArray[$newtype.'_'.$name]	= $defaultArr; 
				}
			}
			$this->DefaultValues=serialize($this->SerializedArray);
			return $this->saveToDB(true);
			
		}
		
	}
