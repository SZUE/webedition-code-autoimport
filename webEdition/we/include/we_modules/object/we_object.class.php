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

	var $ObjectID = 0;
	var $Users = ''; // Default Owners
	var $UsersReadOnly = ''; // For DefaultOwners
	var $RestrictUsers = '';
	var $Workspaces = '';
	var $DefaultWorkspaces = '';
	var $WorkspaceFlag = 1;
	var $Templates = '';
	var $SerializedArray = array(); // #3931

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'WorkspaceFlag', 'RestrictUsers', 'UsersReadOnly', 'Text', 'SerializedArray', 'Templates', 'Workspaces', 'DefaultWorkspaces', 'ID', 'Users', 'strOrder', 'Category', 'DefaultCategory', 'DefaultText', 'DefaultValues', 'DefaultTitle', 'DefaultKeywords', 'DefaultUrl', 'DefaultUrlfield0', 'DefaultUrlfield1', 'DefaultUrlfield2', 'DefaultUrlfield3', 'DefaultTriggerID', 'DefaultDesc', 'CSS');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_WORKSPACE, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT); // ,we_base_constants::WE_EDITPAGE_PREVIEW
		}
		$this->elements['Charset']['dat'] = DEFAULT_CHARSET;
		$this->Icon = 'object.gif';
		$this->Table = OBJECT_TABLE;
		$this->Published = 1;
		$this->ClassName = 'we_object'; //for we_object_Eximport, otherwise ist will save its own classname, or needs its own constructor
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

	function save(){
		if(!$this->checkIfPathOk()){
			return false;
		}

		$this->ModDate = time();
		$this->ModifierID = !isset($GLOBALS['we']['Scheduler_active']) && isset($_SESSION['user']['ID']) ? $_SESSION['user']['ID'] : 0;

		$this->saveToDB();

		$GLOBALS['we_responseText'] = g_l('weClass', '[response_save_ok]');
		$GLOBALS['we_responseTextType'] = we_message_reporting::WE_MESSAGE_NOTICE;

		if($this->OldPath && ($this->OldPath != $this->Path)){
			$fID = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . " WHERE Path='" . $this->DB_WE->escape($this->OldPath) . "'", 'ID', $this->DB_WE);
			$pID = intval(f('SELECT ID FROM ' . OBJECT_FILES_TABLE . " WHERE Path='" . str_replace("\\", "/", dirname($this->Path)) . "'", 'ID', $this->DB_WE));
			$cf = new we_class_folder();
			$cf->initByID($fID, OBJECT_FILES_TABLE);
			$cf->Text = $this->Text;
			$cf->Filename = $this->Text;
			$cf->setParentID($pID);
			$cf->Path = $cf->getPath();
			$cf->we_save(1);
			$cf->modifyChildrenPath();
		}

		$this->OldPath = $this->Path; // reset oldPath
		if(!(isset($this->isInApp) && $this->isInApp)){// allows to save Classes from within WE-Apps
			$GLOBALS['we_JavaScript'] = "top.we_cmd('reload_editpage');_EditorFrame.setEditorDocumentId(" . $this->ID . ");" .
				$this->getUpdateTreeScript() .
				we_main_headermenu::getMenuReloadCode('top.');
		}
	}

	function saveToDB(){
		$arrt = array(
			'WorkspaceFlag' => $this->WorkspaceFlag,
			//	Save charsets in defaultvalues
			//	charset must be in other namespace -> for header !!!
			'elements' => array('Charset' => array('dat' => $this->elements['Charset']['dat'])),
		);

		$this->wasUpdate = $this->ID > 0;
		if(isset($this->elements['Defaultanzahl']['dat'])){
			$this->DefaultText = '';

			for($i = 0; $i <= $this->elements['Defaultanzahl']['dat']; $i++){
				$was = 'DefaultText_' . $i;
				if($this->elements[$was]['dat'] != ''){ //&& in_array($this->elements[$was]["dat"],$var_flip)
					if(stristr($this->elements[$was]['dat'], 'unique')){
						$this->elements[$was]['dat'] = '%' . str_replace('%', '', $this->elements[$was]['dat']) . (($this->elements['unique_' . $i]['dat'] > 0) ? $this->elements['unique_' . $i]['dat']
									: '16') . '%';
						//echo $this->elements[$was]['dat'];
					}
					$this->DefaultText .= $this->elements[$was]['dat'];
				}
			}
		}
		if(isset($this->elements['DefaultanzahlUrl']['dat'])){
			$this->DefaultUrl = '';

			for($i = 0; $i <= $this->elements['DefaultanzahlUrl']['dat']; $i++){
				$was = 'DefaultUrl_' . $i;
				if($this->elements[$was]['dat'] != ''){ //&& in_array($this->elements[$was]['dat'],$var_flip)
					if(stristr($this->elements[$was]['dat'], 'urlunique')){
						$this->elements[$was]['dat'] = '%' . str_replace('%', '', $this->elements[$was]['dat']) . (($this->elements['urlunique_' . $i]['dat'] > 0) ? $this->elements['urlunique_' . $i]['dat']
									: '16') . '%';
					}
					if(stristr($this->elements[$was]['dat'], 'urlfield1')){
						$this->elements[$was]['dat'] = '%' . str_replace('%', '', $this->elements[$was]['dat']) . (($this->elements['urlfield1_' . $i]['dat'] > 0) ? $this->elements['urlfield1_' . $i]['dat']
									: '64') . '%';
					}
					if(stristr($this->elements[$was]['dat'], 'urlfield2')){
						$this->elements[$was]['dat'] = '%' . str_replace('%', '', $this->elements[$was]['dat']) . (($this->elements['urlfield2_' . $i]['dat'] > 0) ? $this->elements['urlfield2_' . $i]['dat']
									: '64') . '%';
					}
					if(stristr($this->elements[$was]['dat'], 'urlfield3')){
						$this->elements[$was]['dat'] = '%' . str_replace('%', '', $this->elements[$was]['dat']) . (($this->elements['urlfield3_' . $i]['dat'] > 0) ? $this->elements['urlfield3_' . $i]['dat']
									: '64') . '%';
					}
					$this->DefaultUrl .= $this->elements[$was]['dat'];
				}
			}
		}

		if(!$this->wasUpdate){
			$q = array(
				'ID BIGINT NOT NULL AUTO_INCREMENT',
				'OF_ID BIGINT NOT NULL',
				'OF_ParentID BIGINT NOT NULL',
				'OF_Text VARCHAR(255) NOT NULL',
				'OF_Path VARCHAR(255) NOT NULL',
				'OF_Url VARCHAR(255) NOT NULL',
				'OF_TriggerID  BIGINT NOT NULL  default "0"',
				'OF_Workspaces VARCHAR(255) NOT NULL',
				'OF_ExtraWorkspaces VARCHAR(255) NOT NULL',
				'OF_ExtraWorkspacesSelected VARCHAR(255) NOT NULL',
				'OF_Templates VARCHAR(255) NOT NULL',
				'OF_ExtraTemplates VARCHAR(255) NOT NULL',
				'OF_Category VARCHAR(255) NOT NULL',
				'OF_Published int(11) NOT NULL',
				'OF_IsSearchable tinyint(1) NOT NULL default "1"',
				'OF_Charset VARCHAR(64) NOT NULL',
				'OF_WebUserID BIGINT NOT NULL',
				'OF_Language VARCHAR(5) default "NULL"'
			);

			$indexe = array(
				'PRIMARY KEY (ID)',
				'UNIQUE KEY OF_ID (OF_ID)',
				'KEY (OF_WebUserID)',
				'KEY `published` (`OF_ID`,`OF_Published`,`OF_IsSearchable`)',
				'KEY (`OF_IsSearchable`)',
			);

			if(isset($this->elements['neuefelder']['dat'])){

				$neu = explode(',', $this->elements['neuefelder']['dat']);
				foreach($neu as $cur){
					if($cur){
						$name = $this->getElement($cur . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($cur, 'dat');
						$arrt[$name] = array(
							'default' => isset($this->elements[$cur . 'default']['dat']) ? $this->elements[$cur . 'default']['dat'] : '',
							'defaultThumb' => isset($this->elements[$cur . 'defaultThumb']['dat']) ? $this->elements[$cur . 'defaultThumb']['dat'] : '',
							'defaultdir' => isset($this->elements[$cur . 'defaultdir']['dat']) ? $this->elements[$cur . 'defaultdir']['dat'] : '',
							'rootdir' => isset($this->elements[$cur . 'rootdir']['dat']) ? $this->elements[$cur . 'rootdir']['dat'] : '',
							'autobr' => isset($this->elements[$cur . 'autobr']['dat']) ? $this->elements[$cur . 'autobr']['dat'] : '',
							'dhtmledit' => isset($this->elements[$cur . 'dhtmledit']['dat']) ? $this->elements[$cur . 'dhtmledit']['dat'] : '',
							'commands' => isset($this->elements[$cur . 'commands']['dat']) ? $this->elements[$cur . 'commands']['dat'] : '',
							'contextmenu' => isset($this->elements[$cur . 'contextmenu']['dat']) ? $this->elements[$cur . 'contextmenu']['dat'] : '',
							'height' => isset($this->elements[$cur . 'height']['dat']) ? $this->elements[$cur . 'height']['dat'] : '',
							self::ELEMENT_WIDTH => isset($this->elements[$cur . self::ELEMENT_WIDTH]['dat']) ? $this->elements[$cur . self::ELEMENT_WIDTH]['dat'] : '',
							'bgcolor' => isset($this->elements[$cur . 'bgcolor']['dat']) ? $this->elements[$cur . 'bgcolor']['dat'] : '',
							'class' => isset($this->elements[$cur . 'class']['dat']) ? $this->elements[$cur . 'class']['dat'] : '',
							'max' => isset($this->elements[$cur . 'max']['dat']) ? $this->elements[$cur . 'max']['dat'] : '',
							'cssClasses' => isset($this->elements[$cur . 'cssClasses']['dat']) ? $this->elements[$cur . 'cssClasses']['dat'] : '',
							'tinyparams' => isset($this->elements[$cur . 'tinyparams']['dat']) ? $this->elements[$cur . 'tinyparams']['dat'] : '',
							'templates' => isset($this->elements[$cur . 'templates']['dat']) ? $this->elements[$cur . 'templates']['dat'] : '',
							'xml' => isset($this->elements[$cur . 'xml']['dat']) ? $this->elements[$cur . 'xml']['dat'] : '',
							'removefirstparagraph' => isset($this->elements[$cur . 'removefirstparagraph']['dat']) ? $this->elements[$cur . 'removefirstparagraph']['dat'] : '',
							'showmenus' => isset($this->elements[$cur . 'showmenus']['dat']) ? $this->elements[$cur . 'showmenus']['dat'] : 'off',
							'forbidhtml' => isset($this->elements[$cur . 'forbidhtml']['dat']) ? $this->elements[$cur . 'forbidhtml']['dat'] : 'off',
							'forbidphp' => isset($this->elements[$cur . 'forbidphp']['dat']) ? $this->elements[$cur . 'forbidphp']['dat'] : 'off',
							'inlineedit' => isset($this->elements[$cur . 'inlineedit']['dat']) ? $this->elements[$cur . 'inlineedit']['dat'] : '',
							'users' => isset($this->elements[$cur . 'users']['dat']) ? $this->elements[$cur . 'users']['dat'] : '',
							'required' => isset($this->elements[$cur . 'required']['dat']) ? $this->elements[$cur . 'required']['dat'] : '',
							'editdescription' => isset($this->elements[$cur . 'editdescription']['dat']) ? $this->elements[$cur . 'editdescription']['dat'] : '',
							'int' => isset($this->elements[$cur . 'int']['dat']) ? $this->elements[$cur . 'int']['dat'] : '',
							'intID' => isset($this->elements[$cur . 'intID']['dat']) ? $this->elements[$cur . 'intID']['dat'] : '',
							'intPath' => isset($this->elements[$cur . 'intPath']['dat']) ? $this->elements[$cur . 'intPath']['dat'] : '',
							'hreftype' => isset($this->elements[$cur . 'hreftype']['dat']) ? $this->elements[$cur . 'hreftype']['dat'] : '',
							'hrefdirectory' => isset($this->elements[$cur . 'hrefdirectory']['dat']) ? $this->elements[$cur . 'hrefdirectory']['dat'] : 'false',
							'hreffile' => isset($this->elements[$cur . 'hreffile']['dat']) ? $this->elements[$cur . 'hreffile']['dat'] : 'true',
							'uniqueID' => md5(uniqid(__FILE__, true)),
						);

						if($this->isVariantField($cur) && isset($this->elements[$cur . 'variant']['dat']) && $this->elements[$cur . 'variant']['dat'] == 1){
							$arrt[$name]['variant'] = $this->elements[$cur . 'variant']['dat'];
						} else if(isset($this->elements[$cur . 'variant'])){
							unset($this->elements[$cur . 'variant']);
						}

						if((!isset($arrt[$name]['meta']) ) || (!is_array($arrt[$name]['meta']))){
							$arrt[$name]['meta'] = array();
						}

						//  First time a field is added
						for($f = 0; $f <= ( isset($this->elements[$cur . 'count']['dat']) ? $this->elements[$cur . 'count']['dat'] : 0 ); $f++){
							$_val = (isset($this->elements[$cur . 'defaultvalue' . $f]['dat']) && $this->elements[$cur . 'defaultvalue' . $f]['dat'] != $cur . 'defaultvalue' . $f) ? $this->elements[$cur . 'defaultvalue' . $f]['dat']
									: '';
							if(substr($name, 0, 12) == we_objectFile::TYPE_MULTIOBJECT . '_'){
								$arrt[$name]['meta'][] = $_val;
							} else {
								if(isset($this->elements[$cur . 'defaultkey' . $f]['dat'])){
									$arrt[$name]['meta'][$this->elements[$cur . 'defaultkey' . $f]['dat']] = $_val;
								}
							}
						}
						$q[] = '`' . $name . '` ' . $this->switchtypes($cur);
						t_e($this);
						//add index for complex queries
						if($this->getElement($cur . self::ELEMENT_TYPE, 'dat') == we_objectFile::TYPE_OBJECT){
							$indexe[] = 'KEY (`' . $name . '`)';
						}
					}
				}
			}

			$arrt['WE_CSS_FOR_CLASS'] = $this->CSS;
			$this->DefaultValues = serialize($arrt);

			$this->DefaultTitle = isset($this->elements['title']['dat']) ? $this->getElement($this->elements['title']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['title']['dat'], 'dat')
					: '_';
			$this->DefaultDesc = isset($this->elements['desc']['dat']) ? $this->getElement($this->elements['desc']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['desc']['dat'], 'dat')
					: '_';
			$this->DefaultKeywords = isset($this->elements['keywords']['dat']) ? $this->getElement($this->elements['keywords']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['keywords']['dat'], 'dat')
					: '_';


			$this->DefaultUrlfield0 = isset($this->elements['urlfield0']['dat']) ? $this->getElement($this->elements['urlfield0']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield0']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield1 = isset($this->elements['urlfield1']['dat']) ? $this->getElement($this->elements['urlfield1']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield1']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield2 = isset($this->elements['urlfield2']['dat']) ? $this->getElement($this->elements['urlfield2']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield2']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield3 = isset($this->elements['urlfield3']['dat']) ? $this->getElement($this->elements['urlfield3']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield3']['dat'], 'dat')
					: '_';
			$this->DefaultTriggerID = isset($this->elements['triggerid']['dat']) ? $this->getElement($this->elements['triggerid']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['triggerid']['dat'], 'dat')
					: '0';


			$this->strOrder = implode(',', $this->getElement('we_sort'));

			$this->DefaultCategory = $this->Category;
			$this->i_savePersistentSlotsToDB();

			$ctable = OBJECT_X_TABLE . intval($this->ID);

			$this->DB_WE->delTable($ctable);
			$this->DB_WE->query('CREATE TABLE ' . $ctable . ' (' . implode(',', $q) . ', ' . implode(',', $indexe) . ') ENGINE = MYISAM ' . we_database_base::getCharsetCollation());

			//dummy eintrag schreiben
			$this->DB_WE->query('INSERT INTO ' . $ctable . ' SET OF_ID=0');

			// folder in object schreiben
			if(!($this->OldPath && ($this->OldPath != $this->Path))){
				$fold = new we_class_folder();
				$fold->initByPath($this->getPath(), OBJECT_FILES_TABLE, 1, 0, 1);
			}
		} else {
			$ctable = OBJECT_X_TABLE . intval($this->ID);
			$tableInfo = $this->DB_WE->metadata($ctable);
			$q = $regs = array();
			$fieldsToDelete = isset($this->elements['felderloeschen']['dat']) ? explode(',', $this->elements['felderloeschen']['dat']) : array();
			foreach($tableInfo as $info){
				if(preg_match('/(.+?)_(.*)/', $info['name'], $regs)){

					if($regs[1] != 'OF' && $regs[1] != 'variant'){
						if(in_array($info['name'], $fieldsToDelete)){
							$q [] = ' DROP `' . $info['name'] . '` ';
						} else {

							$nam = $this->getElement($info['name'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($info['name'], 'dat');
							//change from object is indexed to unindexed
							if((strpos($info['name'], self::QUERY_PREFIX) === 0) && (strpos($nam, self::QUERY_PREFIX) !== 0)){
								$q[] = ' DROP KEY `' . $info['name'] . '` ';
							}

							$q[] = ' CHANGE `' . $info['name'] . '` `' . $nam . '` ' . $this->switchtypes($info['name']) .
								((strpos($info['name'], self::QUERY_PREFIX) !== 0) && (strpos($nam, self::QUERY_PREFIX) === 0) ?
									', ADD INDEX (`' . $nam . '`) ' : '');

							$arrt[$nam] = array(
								'default' => (strpos($info['name'], 'date_') === 0 ?
									($this->elements[$info['name'] . 'defaultThumb']['dat'] ? '' : $this->elements[$info['name'] . 'default']['dat']) :
									$this->elements[$info['name'] . 'default']['dat']),
								'defaultThumb' => $this->elements[$info['name'] . 'defaultThumb']['dat'],
								'autobr' => $this->elements[$info['name'] . 'autobr']['dat'],
								'defaultdir' => $this->elements[$info['name'] . 'defaultdir']['dat'],
								'rootdir' => $this->elements[$info['name'] . 'rootdir']['dat'],
								'dhtmledit' => $this->elements[$info['name'] . 'dhtmledit']['dat'],
								'showmenus' => $this->elements[$info['name'] . 'showmenus']['dat'],
								'commands' => $this->elements[$info['name'] . 'commands']['dat'],
								'contextmenu' => $this->elements[$info['name'] . 'contextmenu']['dat'],
								'height' => $this->elements[$info['name'] . 'height']['dat'],
								self::ELEMENT_WIDTH => $this->elements[$info['name'] . self::ELEMENT_WIDTH]['dat'],
								'bgcolor' => $this->elements[$info['name'] . 'bgcolor']['dat'],
								'class' => $this->elements[$info['name'] . 'class']['dat'],
								'max' => $this->elements[$info['name'] . 'max']['dat'],
								'cssClasses' => $this->elements[$info['name'] . 'cssClasses']['dat'],
								'tinyparams' => $this->elements[$info['name'] . 'tinyparams']['dat'],
								'templates' => $this->elements[$info['name'] . 'templates']['dat'],
								'xml' => $this->elements[$info['name'] . 'xml']['dat'],
								'removefirstparagraph' => $this->elements[$info['name'] . 'removefirstparagraph']['dat'],
								'forbidhtml' => $this->elements[$info['name'] . 'forbidhtml']['dat'],
								'forbidphp' => $this->elements[$info['name'] . 'forbidphp']['dat'],
								'inlineedit' => $this->elements[$info['name'] . 'inlineedit']['dat'],
								'users' => $this->elements[$info['name'] . 'users']['dat'],
								'required' => $this->elements[$info['name'] . 'required']['dat'],
								'editdescription' => $this->elements[$info['name'] . 'editdescription']['dat'],
								'int' => $this->elements[$info['name'] . 'int']['dat'],
								'intID' => $this->elements[$info['name'] . 'intID']['dat'],
								'intPath' => $this->elements[$info['name'] . 'intPath']['dat'],
								'hreftype' => $this->elements[$info['name'] . 'hreftype']['dat'],
								'hrefdirectory' => $this->elements[$info['name'] . 'hrefdirectory']['dat'],
								'hreffile' => $this->elements[$info['name'] . 'hreffile']['dat'],
								'uniqueID' => $this->SerializedArray[$info['name']]['uniqueID'] ? $this->SerializedArray[$info['name']]['uniqueID'] : md5(uniqid(__FILE__, true)),
							);
							if($this->isVariantField($info['name']) && isset($this->elements[$info['name'] . 'variant']['dat']) && $this->elements[$info['name'] . 'variant']['dat'] == 1){
								$arrt[$nam]['variant'] = $this->elements[$info['name'] . 'variant']['dat'];
							} else if(isset($this->elements[$info['name'] . 'variant'])){
								unset($this->elements[$info['name'] . 'variant']);
							}
							if(isset($this->elements[$info['name'] . 'count'])){
								for($f = 0; $f <= $this->elements[$info['name'] . 'count']['dat']; ++$f){

									if(isset($this->elements[$info['name'] . 'defaultkey' . $f])){
										if((!isset($arrt[$nam]['meta'])) || (!is_array($arrt[$nam]['meta']))){
											$arrt[$nam]['meta'] = array();
										}
										$_val = (isset($this->elements[$info['name'] . 'defaultvalue' . $f]) && $this->elements[$info['name'] . 'defaultvalue' . $f] != $info['name'] . 'defaultvalue' . $f )
												? $this->elements[$info['name'] . 'defaultvalue' . $f]['dat'] : '';
										if(substr($nam, 0, 12) == we_objectFile::TYPE_MULTIOBJECT . '_'){
											$arrt[$nam]['meta'][] = $_val;
										} else {
											$arrt[$nam]['meta'][$this->elements[$info['name'] . 'defaultkey' . $f]['dat']] = $_val;
										}
									}
								}
							}
						}
					}
				}
			}

			$neu = explode(',', (isset($this->elements['neuefelder']['dat']) ? $this->elements['neuefelder']['dat'] : ''));

			foreach($neu as $cur){
				if(isset($cur) && $cur != ''){
					$nam = $this->getElement($cur . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($cur, 'dat');
					$arrt[$nam] = array(
						'default' => isset($this->elements[$cur . 'default']['dat']) ? $this->elements[$cur . 'default']['dat'] : '',
						'defaultThumb' => isset($this->elements[$cur . 'defaultThumb']['dat']) ? $this->elements[$cur . 'defaultThumb']['dat'] : '',
						'defaultdir' => isset($this->elements[$cur . 'defaultdir']['dat']) ? $this->elements[$cur . 'defaultdir']['dat'] : '',
						'rootdir' => isset($this->elements[$cur . 'rootdir']['dat']) ? $this->elements[$cur . 'rootdir']['dat'] : '',
						'autobr' => isset($this->elements[$cur . 'autobr']['dat']) ? $this->elements[$cur . 'autobr']['dat'] : '',
						'dhtmledit' => isset($this->elements[$cur . 'dhtmledit']['dat']) ? $this->elements[$cur . 'dhtmledit']['dat'] : '',
						'showmenues' => isset($this->elements[$cur . 'showmenues']['dat']) ? $this->elements[$cur . 'showmenues']['dat'] : '',
						'commands' => isset($this->elements[$cur . 'commands']['dat']) ? $this->elements[$cur . 'commands']['dat'] : '',
						'contextmenu' => isset($this->elements[$cur . 'contextmenu']['dat']) ? $this->elements[$cur . 'contextmenu']['dat'] : '',
						'height' => isset($this->elements[$cur . 'height']['dat']) ? $this->elements[$cur . 'height']['dat'] : 200,
						self::ELEMENT_WIDTH => isset($this->elements[$cur . self::ELEMENT_WIDTH]['dat']) ? $this->elements[$cur . self::ELEMENT_WIDTH]['dat'] : 618,
						'bgcolor' => isset($this->elements[$cur . 'bgcolor']['dat']) ? $this->elements[$cur . 'bgcolor']['dat'] : '',
						'class' => isset($this->elements[$cur . 'class']['dat']) ? $this->elements[$cur . 'class']['dat'] : '',
						'max' => isset($this->elements[$cur . 'max']['dat']) ? $this->elements[$cur . 'max']['dat'] : '',
						'cssClasses' => isset($this->elements[$cur . 'cssClasses']['dat']) ? $this->elements[$cur . 'cssClasses']['dat'] : '',
						'tinyparams' => isset($this->elements[$cur . 'tinyparams']['dat']) ? $this->elements[$cur . 'tinyparams']['dat'] : '',
						'templates' => isset($this->elements[$cur . 'templates']['dat']) ? $this->elements[$cur . 'templates']['dat'] : '',
						'xml' => isset($this->elements[$cur . 'xml']['dat']) ? $this->elements[$cur . 'xml']['dat'] : '',
						'removefirstparagraph' => isset($this->elements[$cur . 'removefirstparagraph']['dat']) ? $this->elements[$cur . 'removefirstparagraph']['dat'] : '',
						'forbidhtml' => isset($this->elements[$cur . 'forbidhtml']['dat']) ? $this->elements[$cur . 'forbidhtml']['dat'] : '',
						'forbidphp' => isset($this->elements[$cur . 'forbidphp']['dat']) ? $this->elements[$cur . 'forbidphp']['dat'] : '',
						'inlineedit' => isset($this->elements[$cur . 'inlineedit']['dat']) ? $this->elements[$cur . 'inlineedit']['dat'] : '',
						'users' => isset($this->elements[$cur . 'users']['dat']) ? $this->elements[$cur . 'users']['dat'] : '',
						'required' => isset($this->elements[$cur . 'required']['dat']) ? $this->elements[$cur . 'required']['dat'] : '',
						'editdescription' => isset($this->elements[$cur . 'editdescription']['dat']) ? $this->elements[$cur . 'editdescription']['dat'] : '',
						'int' => isset($this->elements[$cur . 'int']['dat']) ? $this->elements[$cur . 'int']['dat'] : '',
						'intID' => isset($this->elements[$cur . 'intID']['dat']) ? $this->elements[$cur . 'intID']['dat'] : '',
						'intPath' => isset($this->elements[$cur . 'intPath']['dat']) ? $this->elements[$cur . 'intPath']['dat'] : '',
						'hreftype' => isset($this->elements[$cur . 'hreftype']['dat']) ? $this->elements[$cur . 'hreftype']['dat'] : '',
						'hrefdirectory' => isset($this->elements[$cur . 'hrefdirectory']['dat']) ? $this->elements[$cur . 'hrefdirectory']['dat'] : 'false',
						'hreffile' => isset($this->elements[$cur . 'hreffile']['dat']) ? $this->elements[$cur . 'hreffile']['dat'] : 'true',
						'uniqueID' => md5(uniqid(__FILE__, true)),
					);
//					$arrt[$nam]['variant'] = (isset($this->elements[$cur.'variant']['dat']) && $this->elements[$cur.'variant']['dat']==1) ? $this->elements[$cur.'variant']['dat'] : '';
					if($this->isVariantField($cur) && isset($this->elements[$cur . 'variant']['dat']) && $this->elements[$cur . 'variant']['dat'] == 1){
						$arrt[$nam]['variant'] = $this->elements[$cur . 'variant']['dat'];
					} else if(isset($this->elements[$cur . 'variant'])){
						unset($this->elements[$cur . 'variant']);
					}

					for($f = 0; $f <= (isset($this->elements[$cur . 'count']['dat']) ? $this->elements[$cur . 'count']['dat'] : 0); $f++){
						$_val = isset($this->elements[$cur . 'defaultvalue' . $f]['dat']) ? $this->elements[$cur . 'defaultvalue' . $f]['dat'] : '';
						if((!isset($arrt[$nam]['meta'])) || (!is_array($arrt[$nam]['meta']))){
							$arrt[$nam]['meta'] = array();
						}
						if(substr($nam, 0, 12) == we_objectFile::TYPE_MULTIOBJECT . '_'){
							$arrt[$nam]['meta'][] = $_val;
						} else {
							if(isset($this->elements[$cur . 'defaultkey' . $f]['dat'])){
								$arrt[$nam]['meta'][$this->elements[$cur . 'defaultkey' . $f]['dat']] = $_val;
							} else {
								$arrt[$nam]['meta'][$nam . 'defaultkey' . $f] = $_val;
							}
						}
					}

					$q [] = ' ADD `' . $nam . '` ' . $this->switchtypes($cur);
					//add index for complex queries
					if($this->getElement($cur . self::ELEMENT_TYPE, 'dat') == we_objectFile::TYPE_OBJECT){
						$q [] = ' ADD INDEX (`' . $nam . '`)';
					}
				}
			}

			$this->DefaultCategory = $this->Category;

			$this->DefaultTitle = $this->getElement($this->elements['title']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['title']['dat'], 'dat');
			$this->DefaultDesc = $this->getElement($this->elements['desc']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['desc']['dat'], 'dat');
			$this->DefaultKeywords = $this->getElement($this->elements['keywords']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['keywords']['dat'], 'dat');


			$this->DefaultUrlfield0 = isset($this->elements['urlfield0']['dat']) ? $this->getElement($this->elements['urlfield0']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield0']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield1 = isset($this->elements['urlfield1']['dat']) ? $this->getElement($this->elements['urlfield1']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield1']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield2 = isset($this->elements['urlfield2']['dat']) ? $this->getElement($this->elements['urlfield2']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield2']['dat'], 'dat')
					: '_';
			$this->DefaultUrlfield3 = isset($this->elements['urlfield3']['dat']) ? $this->getElement($this->elements['urlfield3']['dat'] . self::ELEMENT_TYPE, 'dat') . '_' . $this->getElement($this->elements['urlfield3']['dat'], 'dat')
					: '_';
			//$this->DefaultTriggerID = isset($this->elements['triggerid']['dat']) ? $this->getElement($this->elements['triggerid']['dat'].'dtype','dat').'_'.$this->getElement($this->elements['urlfield3']['dat'],'dat') : '0';

			$arrt['WE_CSS_FOR_CLASS'] = $this->CSS;

			$this->DefaultValues = serialize($arrt);

			if(defined('SHOP_TABLE')){
				$variant_field = 'variant_' . WE_SHOP_VARIANTS_ELEMENT_NAME;
				$exists = false;
				$this->DB_WE->query('SHOW COLUMNS FROM ' . $ctable . ' LIKE "' . $variant_field . '"');
				if($this->DB_WE->next_record()){
					$exists = true;
				}

				if($this->hasVariantFields() > 0){
					if(!$exists){
						$this->DB_WE->query('ALTER TABLE ' . $ctable . ' ADD `' . $variant_field . '` TEXT NOT NULL');
					}
				} else {
					if($exists){
						$this->DB_WE->delCol($ctable, $variant_field);
					}
				}
			}

			foreach($q as $v){
				if($v){
					$this->DB_WE->query('ALTER TABLE ' . $ctable . ' ' . $v);
				}
			}

			$this->strOrder = implode(',', $this->getElement("we_sort"));
			$this->i_savePersistentSlotsToDB();
		}

		////// resave the line O to O.....
		$this->DB_WE->query('REPLACE INTO ' . $ctable . ' SET OF_ID=0');
		////// resave the line O to O.....

		unset($this->elements);
		$this->i_getContentData();
		//$this->initByID($this->ID,$this->Table);
	}

	function switchtypes($name){
		switch($this->getElement($name . self::ELEMENT_TYPE, 'dat')){
			case we_objectFile::TYPE_META:
				return ' VARCHAR(' . (($this->getElement($name . 'length', 'dat') > 0 && ($this->getElement($name . 'length', 'dat') < 255)) ? $this->getElement($name . 'length', 'dat')
							: 255) . ') NOT NULL ';
			case we_objectFile::TYPE_DATE:
				return ' INT(11) NOT NULL ';
			case we_objectFile::TYPE_INPUT:
				return ' VARCHAR(' . (($this->getElement($name . 'length', 'dat') > 0 && ($this->getElement($name . 'length', 'dat') < 4096)) ? $this->getElement($name . 'length', 'dat')
							: 255) . ') NOT NULL ';
			case we_objectFile::TYPE_COUNTRY:
			case we_objectFile::TYPE_LANGUAGE:
				return ' VARCHAR(2) NOT NULL ';
			case we_objectFile::TYPE_LINK:
			case we_objectFile::TYPE_HREF:
				return ' TEXT NOT NULL ';
			case we_objectFile::TYPE_TEXT:
				return ' LONGTEXT NOT NULL ';
				break;
			case we_objectFile::TYPE_IMG:
			case we_objectFile::TYPE_FLASHMOVIE:
			case we_objectFile::TYPE_QUICKTIME:
			case we_objectFile::TYPE_BINARY:
				return ' INT(11) DEFAULT "0" NOT NULL ';
			case we_objectFile::TYPE_CHECKBOX:
				return ' INT(1) DEFAULT "' . ($this->getElement($name . 'default', 'dat') == 1 ? '1' : '0') . '" NOT NULL ';
			case we_objectFile::TYPE_INT:
				return ' INT(' . (($this->getElement($name . 'length', 'dat') > 0 && ($this->getElement($name . 'length', 'dat') < 256)) ? $this->getElement($name . 'length', 'dat')
							: '11') . ') DEFAULT NULL ';
			case we_objectFile::TYPE_FLOAT:
				return ' DOUBLE DEFAULT NULL ';
			case we_objectFile::TYPE_OBJECT:
				return ' BIGINT(20) DEFAULT "0" NOT NULL ';
			case we_objectFile::TYPE_MULTIOBJECT:
				return ' TEXT NOT NULL ';
			case we_objectFile::TYPE_SHOPVAT:
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
		if(!$this->issetElement('we_sort')){
			$t = we_objectFile::getSortArray($this->ID, $this->DB_WE);
			$sort = array();
			foreach($t as $v){
				if($v < 0){
					$v = 0;
				}
				$sort[str_replace('.', '', uniqid(__FUNCTION__, true))] = $v;
			}
			$this->setElement('we_sort', $sort);
		}
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) == "save_document"){
			$GLOBALS['we_JavaScript'] = '';
			$this->save();
			$GLOBALS['we_responseText'] = sprintf(g_l('weClass', "[response_save_ok]"), $this->Path);
			$GLOBALS['we_responseTextType'] = we_message_reporting::WE_MESSAGE_NOTICE;
			return 'we_templates/we_editor_save.inc.php';
		}
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
			case we_base_constants::WE_EDITPAGE_WORKSPACE:
				return 'we_templates/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				return 'we_modules/object/we_editor_info_object.inc.php';
			case we_base_constants::WE_EDITPAGE_CONTENT:
				return 'we_modules/object/we_editor_contentobject.inc.php';
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return 'we_templates/we_editor_properties.inc.php';
		}
	}

	function getSortIndex($nr){
		$sort = $this->getElement("we_sort");

		$i = 0;
		foreach($sort as $k => $v){
			if($i == $nr){
				return $k;
			}
			$i++;
		}
	}

	function getSortIndexByValue($value){
		$sort = $this->getElement("we_sort");

		$i = 0;
		foreach($sort as $k => $v){
			if($v == $value){
				return $k;
			}
			$i++;
		}
	}

	function downEntryAtClass($identifier){
		$sort = $this->getElement("we_sort");
		$pos = $sort[$identifier];

		$t = array();
		$i = 0;
		$position = count($sort);
		foreach($sort as $ident => $identpos){
			if($ident == $identifier && $i < count($sort) - 1){
				$position = $i;
			}
			if($i == $position + 1){
				$t[$ident] = $identpos;
				$t[$identifier] = $pos;
			} elseif($i != $position){
				$t[$ident] = $sort[$ident];
			}
			$i++;
		}

		$sort = $t;
		$this->setElement("we_sort", $sort);
	}

	function upEntryAtClass($identifier){
		$sort = $this->getElement("we_sort");
		$pos = $sort[$identifier];
		$reversed = array_reverse($sort, true);

		$t = array();
		$i = 0;
		$position = count($reversed);
		foreach($reversed as $ident => $identpos){
			if($ident == $identifier && $i < count($reversed) - 1){
				$position = $i;
			}
			if($i == $position + 1){
				$t[$ident] = $identpos;
				$t[$identifier] = $sort[$identifier];
			} elseif($i != $position){
				$t[$ident] = $reversed[$ident];
			}
			$i++;
		}

		$sort = array_reverse($t, true);
		$this->setElement("we_sort", $sort);
	}

	function addEntryToClass($identifier, $after = false){
		$sort = $this->getElement("we_sort");
		$uid = uniqid();

		$gesamt = $this->getElement("Sortgesamt");

		$this->elements["Sortgesamt"]["dat"] = (empty($sort) ? 0 : ++$gesamt);
		$this->elements[$uid]["dat"] = '';
		$this->elements[$uid . self::ELEMENT_LENGHT]["dat"] = "";
		$this->elements[$uid . self::ELEMENT_TYPE]["dat"] = "";
		$this->elements[$uid . self::ELEMENT_WIDTH]["dat"] = 618;
		$this->elements[$uid . self::ELEMENT_HEIGHT]["dat"] = 200;
		$this->elements[$uid . self::ELEMENT_CLASS]["dat"] = "";
		$this->elements[$uid . self::ELEMENT_MAX]["dat"] = "";
		$this->elements["wholename" . $identifier]["dat"] = $uid;

		if(!isset($this->elements["neuefelder"]["dat"])){
			$this->elements["neuefelder"]["dat"] = "";
		}

		$this->elements["neuefelder"]["dat"] .= "," . $uid;

		if(isset($after) && in_array($after, array_keys($sort))){
			$pos = $sort[$after];
			$t = array();
			foreach($sort as $ident => $identpos){
				if($pos > $identpos){
					$t[$ident] = $identpos;
				} elseif($pos == $identpos){
					$t[$ident] = $identpos;
					$t[$identifier] = count($sort);
				} elseif($pos < $identpos){
					$t[$ident] = $identpos;
				}
			}
			$sort = $t;
		} else {
			$sort[$identifier] = count($sort);
		}
		$this->setElement("we_sort", $sort);
	}

	function removeEntryFromClass($identifier){

		$sort = $this->getElement("we_sort");
		$max = $this->getElement("Sortgesamt");

		$uid = $this->elements["wholename" . $identifier]["dat"];

		if(stristr((isset($this->elements['neuefelder']['dat']) ? $this->elements['neuefelder']['dat'] : ''), ',' . $uid)){
			$this->elements["neuefelder"]["dat"] = str_replace("," . $uid, "", $this->elements["neuefelder"]["dat"]);
		} else {
			if(!isset($this->elements["felderloeschen"]["dat"])){
				$this->elements["felderloeschen"]["dat"] = "";
			}
			$this->elements["felderloeschen"]["dat"] .= "," . $uid;
		}

		unset($this->elements["wholename" . $identifier]["dat"]);
		unset($this->elements[$uid]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_LENGHT]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_TYPE]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_HEIGHT]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_WIDTH]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_DEFAULT]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_CLASS]["dat"]);
		unset($this->elements[$uid . self::ELEMENT_MAX]["dat"]);


		### move elements ####
		$pos = $sort[$identifier];
		foreach($sort as $ident => $identpos){
			if($identpos == $pos){
				unset($sort[$ident]);
			} elseif($identpos > $pos){
				$sort[$ident] --;
			}
		}
		$this->elements["Sortgesamt"]["dat"] = count($sort);
		### end move elements ####

		$this->setElement("we_sort", ($sort ? $sort : array()));
	}

	function downMetaAtClass($name, $i){
		$temp = $this->elements[$name . "defaultkey" . ($i + 1)]["dat"];
		$this->elements[$name . "defaultkey" . ($i + 1)]["dat"] = $this->elements[$name . "defaultkey" . ($i)]["dat"];
		$this->elements[$name . "defaultkey" . ($i)]["dat"] = $temp;

		$temp = $this->elements[$name . "defaultvalue" . ($i + 1)]["dat"];
		$this->elements[$name . "defaultvalue" . ($i + 1)]["dat"] = $this->elements[$name . "defaultvalue" . ($i)]["dat"];
		$this->elements[$name . "defaultvalue" . ($i)]["dat"] = $temp;
	}

	function upMetaAtClass($name, $i){
		$temp = $this->elements[$name . "defaultkey" . ($i - 1)]["dat"];
		$this->elements[$name . "defaultkey" . ($i - 1)]["dat"] = $this->elements[$name . "defaultkey" . ($i)]["dat"];
		$this->elements[$name . "defaultkey" . ($i)]["dat"] = $temp;

		$temp = $this->elements[$name . "defaultvalue" . ($i - 1)]["dat"];
		$this->elements[$name . "defaultvalue" . ($i - 1)]["dat"] = $this->elements[$name . "defaultvalue" . ($i)]["dat"];
		$this->elements[$name . "defaultvalue" . ($i)]["dat"] = $temp;
	}

	function addMetaToClass($name, $pos){
		// get from request
		$amount = we_base_request::_(we_base_request::INT, "amount_insert_meta_at_class_" . $name . $pos, 1);

		// set new amount
		$this->elements[$name . "count"]["dat"] += $amount;

		// move elements - add new elements
		for($i = $this->elements[$name . "count"]["dat"]; 0 <= $i; $i--){

			if(($pos + $amount) < $i){// move existing fields
				$this->elements[$name . "defaultkey" . ($i)]["dat"] = ($this->getElement($name . "defaultkey" . ($i - $amount), "dat"));
				$this->elements[$name . "defaultvalue" . ($i)]["dat"] = ($this->getElement($name . "defaultvalue" . ($i - $amount), "dat"));
			} else if($pos < $i && $i <= ($pos + $amount)){ // add new fields
				$this->elements[$name . "defaultkey" . $i]["dat"] = "";
				$this->elements[$name . "defaultvalue" . $i]["dat"] = "";
			}
		}
	}

	function removeMetaFromClass($name, $nr){

		### move elements ####

		for($i = 0; $i < $this->elements[$name . "count"]["dat"]; $i++){
			if($i >= $nr){
				$this->elements[$name . "defaultkey" . $i]["dat"] = ($this->getElement($name . "defaultkey" . ($i + 1), "dat"));
				$this->elements[$name . "defaultvalue" . $i]["dat"] = ($this->getElement($name . "defaultvalue" . ($i + 1), "dat"));
			}
		}
		$this->elements[$name . "defaultkey" . $i]["dat"] = "";
		$this->elements[$name . "defaultvalue" . $i]["dat"] = "";
		### end move elements ####

		$this->elements[$name . "count"]["dat"] = ($this->elements[$name . "count"]["dat"] > 0) ? $this->elements[$name . "count"]["dat"] - 1 : 0;
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

	function getFieldHTML($name, $identifier){
		$type = $this->getElement($name . self::ELEMENT_TYPE, "dat") ? $this->getElement($name . self::ELEMENT_TYPE, "dat") : we_objectFile::TYPE_INPUT;
		$content = '<tr>
			<td  width="100" class="weMultiIconBoxHeadline" valign="top" >' . g_l('weClass', "[name]") . '</td>
			<td  width="170" class="defaultfont" valign="top">';

		if($type == we_objectFile::TYPE_OBJECT){
			$regs = $vals = array();
			$all = $this->DB_WE->table_names(OBJECT_X_TABLE . '%');
			$count = 0;
			while($count < count($all)){
				if($all[$count]["table_name"] != OBJECT_FILES_TABLE && $all[$count]["table_name"] != OBJECT_FILES_TABLE){
					if(preg_match('/^(.+)_(\d+)$/', $all[$count]["table_name"], $regs)){
						if($this->ID != $regs[2]){
							$path = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $regs[2], '', $this->DB_WE);
							if($path !== ''){
								$vals[$regs[2]] = $path;
							}
						}
					}
				}
				$count++;
			}
			asort($vals);
			$content .= $this->htmlSelect("we_" . $this->Name . "_input[$name]", $vals, 1, $this->getElement($name, "dat"), "", array('onchange' => 'if(this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\']){this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'object_reload_entry_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . $identifier . '\')'), "value", 388);
		} else {

			$foo = $this->getElement($name, "dat");
			if($type == we_objectFile::TYPE_SHOPVAT){
				$foo = WE_SHOP_VAT_FIELD_NAME;
				$content .= we_html_tools::hidden("we_" . $this->Name . "_input[$name]", $foo) .
					$this->htmlTextInput("tmp" . WE_SHOP_VAT_FIELD_NAME, 40, $foo, 52, ' readonly="readonly" disabled="disabled"', "text", 388);
			} else {
				$foo = $foo ? $foo : g_l('modules_object', '[new_field]');
				$content .= $this->htmlTextInput("we_" . $this->Name . "_input[$name]", 40, $foo, 52, ' oldValue="' . $foo . '" onBlur="we_checkObjFieldname(this);" onchange="_EditorFrame.setEditorIsHot(true);"', "text", 388);
			}
		}


		$content .= '</td></tr>' .
			'<tr><td class="weMultiIconBoxHeadlineThin" valign="top">' . g_l('global', "[description]") . '</td><td>' .
			$this->htmlTextArea("we_" . $this->Name . "_input[" . $name . "editdescription]", 3, 40, $this->getElement($name . "editdescription"), array('onchange' => '_EditorFrame.setEditorIsHot(true)', 'style' => 'width: 388px;')) .
			'</td></tr>' .
			//type
			'<tr><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">' . g_l('modules_object', '[type]') . '</td>
		<td width="170" class="defaultfont"  valign="top">';

		$val = array(
			we_objectFile::TYPE_INPUT => g_l('modules_object', '[input_field]'),
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
			we_objectFile::TYPE_QUICKTIME => g_l('modules_object', '[quicktime_field]'),
			we_objectFile::TYPE_COUNTRY => g_l('modules_object', '[country_field]'),
			we_objectFile::TYPE_LANGUAGE => g_l('modules_object', '[language_field]'),
			we_objectFile::TYPE_OBJECT => g_l('modules_object', '[objectFile_field]'),
			we_objectFile::TYPE_MULTIOBJECT => g_l('modules_object', '[multiObjectFile_field]'),
		);
		if(defined('SHOP_TABLE')){
			$val[we_objectFile::TYPE_SHOPVAT] = g_l('modules_object', '[shopVat_field]');
		}
		$content .= $this->htmlSelect("we_" . $this->Name . "_input[" . $name . self::ELEMENT_TYPE . ']', $val, 1, $type, "", array('onchange' => 'if(this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\']){this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'object_reload_entry_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . $identifier . '\');'), "value", 388) .
			'</td></tr>';

		switch($type){
			case we_objectFile::TYPE_SHOPVAT:
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
				break;
			default:
				// Length
				$maxLengthVal = $type == we_objectFile::TYPE_INT ? 10 : 1023;
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">' . g_l('modules_object', '[length]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					$this->htmlTextInput("we_" . $this->Name . "_input[" . $name . self::ELEMENT_LENGHT . ']', 10, ($this->getElement($name . "length", "dat") > 0 && ($this->getElement($name . "length", "dat") < ($maxLengthVal + 1))
								? $this->getElement($name . "length", "dat") : $maxLengthVal), ($type == we_objectFile::TYPE_INT ? 2 : 4), 'onchange="_EditorFrame.setEditorIsHot(true);" weType="weObject_' . $type . '_length"', "text", 388) .
					'</td></tr>';
		}

		switch($type){
			case we_objectFile::TYPE_MULTIOBJECT:
				$content .= '<tr>' .
					'<td  width="100" class="weMultiIconBoxHeadlineThin" valign="top" >' . g_l('contentTypes', '[object]') . '</td>' .
					'<td  width="170" class="defaultfont" valign="top">';
				$vals = array();
				$all = $this->DB_WE->table_names(OBJECT_X_TABLE . "%");
				$count = 0;
				while($count < count($all)){
					if($all[$count]["table_name"] != OBJECT_FILES_TABLE && $all[$count]["table_name"] != OBJECT_FILES_TABLE){
						if(preg_match('/^(.+)_(\d+)$/', $all[$count]["table_name"], $regs)){
							$this->DB_WE->query('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID = ' . intval($regs[2]));
							$this->DB_WE->next_record();
							if($this->DB_WE->f("Path") !== ''){
								$vals[$regs[2]] = $this->DB_WE->f("Path");
							}
						}
					}
					$count++;
				}
				asort($vals);
				if(!isset($this->elements[$name . "class"]["dat"]) || $this->elements[$name . "class"]["dat"] == ""){
					$this->setElement($name . "class", array_shift(array_flip($vals)));
				}
				$content .= $this->htmlSelect("we_" . $this->Name . '_' . we_objectFile::TYPE_MULTIOBJECT . '[' . $name . "class]", $vals, 1, $this->getElement($name . 'class', "dat"), "", array('onchange' => 'if(this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\']){this.form.elements[\'' . 'we_' . $this->Name . '_input[' . $name . 'default]' . '\'].value=\'\' };_EditorFrame.setEditorIsHot(true);we_cmd(\'object_change_multiobject_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . $identifier . '\',\'' . $name . '\')'), "value", 388) .
					'</td></tr>
						<tr valign="top">
						<td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[max_objects]') . '</td>
						<td class="defaultfont"><nobr>' . $this->htmlTextInput("we_" . $this->Name . '_' . we_objectFile::TYPE_MULTIOBJECT . '[' . $name . "max]", 5, $this->getElement($name . "max", "dat"), 3, 'onchange="_EditorFrame.setEditorIsHot(true);we_cmd(\'object_reload_entry_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . ($identifier) . '\');"', "text", 50) . ' (' . g_l('modules_object', '[no_maximum]') . ')</nobr></td>
					</tr>
						<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>
						<td width="170" class="defaultfont"><table border="0">';
				if(!isset($this->elements[$name . "count"]["dat"])){
					$this->elements[$name . "count"]["dat"] = 0;
				}
				for($f = 0; $f <= $this->elements[$name . "count"]["dat"]; $f++){
					$content .= $this->getMultiObjectFieldHTML($name, $identifier, $f);
				}

				$content .= '</tr></table></td></tr>';
				break;

			case we_objectFile::TYPE_HREF:
				$typeVal = $this->getElement($name . 'hreftype', 'dat');
				$typeSelect = '<select class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hreftype]" name="we_' . $this->Name . '_input[' . $name . 'hreftype]" onchange="_EditorFrame.setEditorIsHot(true);we_cmd(\'object_reload_entry_at_class\',\'' . $GLOBALS['we_transaction'] . '\',\'' . $identifier . '\');">
			<option' . (($typeVal == we_base_link::TYPE_ALL || $typeVal == "") ? " selected" : "") . ' value="' . we_base_link::TYPE_ALL . '">all
			<option' . (($typeVal == we_base_link::TYPE_INT) ? " selected" : "") . ' value="' . we_base_link::TYPE_INT . '">int
			<option' . (($typeVal == we_base_link::TYPE_EXT) ? " selected" : "") . ' value="' . we_base_link::TYPE_EXT . '">ext
			</select>';
				$fileVal = $this->getElement($name . "hreffile", "dat");
				$fileVal = $fileVal ? $fileVal : "true";
				$fileSelect = '<select class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hreffile]" name="we_' . $this->Name . '_input[' . $name . 'hreffile]">
			<option' . (($fileVal == "true") ? " selected" : "") . ' value="true">true
			<option' . (($fileVal == "false") ? " selected" : "") . ' value="false">false
			</select>';
				$dirVal = $this->getElement($name . "hrefdirectory", "dat");
				$dirVal = $dirVal ? $dirVal : "false"; // options anzeige umgedreht wegen 4363
				$dirSelect = '<select class="weSelect" id="we_' . $this->Name . '_input[' . $name . 'hrefdirectory]" name="we_' . $this->Name . '_input[' . $name . 'hrefdirectory]">
			<option' . (($dirVal == "true") ? " selected" : "") . ' value="true">false
			<option' . (($dirVal == "false") ? " selected" : "") . ' value="false">true
			</select>';
				$content .= '<tr valign="top"><td  width="100" class="defaultfont"  valign="top"></td>' .
					'<td class="defaultfont">type' . we_html_tools::getPixel(8, 2) .
					$typeSelect . we_html_tools::getPixel(30, 2) . "file" . we_html_tools::getPixel(8, 2) .
					$fileSelect . we_html_tools::getPixel(30, 2) . "directory" . we_html_tools::getPixel(8, 2) .
					$dirSelect .
					'</td></tr>
					<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>
						<td width="170" class="defaultfont">' .
					$this->htmlHref($name) .
					'</td></tr>';
				break;


			// default
			/*
			  if($_REQUEST['we_cmd'][0] == "reload_editpage" && $_REQUEST['we_cmd'][2] == $identifier){
			  $this->setElement($name."default","");
			  }
			 */
			case we_objectFile::TYPE_CHECKBOX:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					we_html_forms::checkbox(1, $this->getElement($name . "default", "dat"), "we_" . $this->Name . "_input[" . $name . "default1]", g_l('modules_object', '[checked]'), true, "defaultfont", "if(this.checked){document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "default]" . "'].value=1;}else{ document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "default]" . "'].value=0;}") .
					'<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "default]" . '" value="' . $this->getElement($name . "default", "dat") . '" />' .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_IMG:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", "", $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", "", $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->getImageHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_FLASHMOVIE:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", "", $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", "", $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->getFlashmovieHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_QUICKTIME:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", "", $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", "", $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin" valign="top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->getQuicktimeHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_BINARY:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[rootdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "ParentPath", "input[" . $name . "rootdir]", "", $this->getElement($name . "rootdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[defaultdir]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->formDirChooser(267, 0, FILE_TABLE, "StartPath", "input[" . $name . "defaultdir]", "", $this->getElement($name . "defaultdir", "dat"), $identifier) .
					'</td></tr>' .
					'<tr><td  width="100" valign="top" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class= "defaultfont"  valign="top">' .
					$this->getBinaryHTML($name . "default", $this->getElement($name . "default", "dat"), $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_DATE:

				$d = abs($this->getElement($name . "default", "dat"));
				$dd = abs($this->getElement($name . "defaultThumb", "dat"));
				$content .= '<tr valign="top"><td  width="100" class="defaultfont">Default</td>' .
					'<td width="170" class="defaultfont">' .
					we_html_forms::checkboxWithHidden(($dd == '1' ? true : false), "we_" . $this->Name . "_xdate[" . $name . "defaultThumb]", 'Creation Date', false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);') .
					we_html_tools::getDateInput2('we_' . $this->Name . '_date[' . $name . 'default]', ($d ? $d : time()), true) .
					'</td></tr>';

				break;
			case we_objectFile::TYPE_TEXT:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->dhtmledit($name, $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_OBJECT:
				$content .= '<tr><td  width="100" class="weMultiIconBoxHeadlineThin"  valign="top">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"  valign="top">' .
					$this->getObjectFieldHTML($name, isset($attribs) ? $attribs : "") .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_META:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont"><table border="0"><tr><td class="defaultfont">Key</td><td class="defaultfont">Value</td><td></td></tr>';
				if(!isset($this->elements[$name . "count"]["dat"])){
					$this->elements[$name . "count"]["dat"] = 0;
				}

				$addArray = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);

				for($f = 0; $f <= $this->elements[$name . "count"]["dat"]; $f++){
					$content .= '<tr><td>' . $this->htmlTextInput("we_" . $this->Name . "_input[" . $name . "defaultkey" . $f . "]", 40, $this->getElement($name . "defaultkey" . $f, "dat"), 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 105) .
						'</td><td>' . $this->htmlTextInput("we_" . $this->Name . "_input[" . $name . "defaultvalue" . $f . "]", 40, $this->getElement($name . "defaultvalue" . $f, "dat"), 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 105);

					$upbut = we_html_button::create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')");
					$upbutDis = we_html_button::create_button("image:btn_direction_up", "#", true, 0, 0, "", "", true);
					$downbut = we_html_button::create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')");
					$downbutDis = we_html_button::create_button("image:btn_direction_down", "#", true, 0, 0, "", "", true);

					$plusAmount = $this->htmlSelect("amount_insert_meta_at_class_" . $name . $f, $addArray);
					$plusbut = we_html_button::create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')");
					$trashbut = we_html_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_meta_class','" . $GLOBALS['we_transaction'] . "','" . ($identifier) . "','" . $name . "','" . ($f) . "')");

					$content .= "</td><td>" .
						we_html_button::create_button_table(array($plusAmount,
							$plusbut,
							(($f > 0) ? $upbut : $upbutDis ),
							(($f < ($this->elements[$name . "count"]["dat"])) ? $downbut : $downbutDis),
							$trashbut
							), 5
						) .
						'</td></tr>';
					//$content.="test<br/>test<input type='text'>".$upbut."test<br/>";
				}
				$content .= '</table></td></tr>';
				break;
			case we_objectFile::TYPE_COUNTRY:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					$this->htmlTextInput("we_" . $this->Name . "_country[" . $name . "default]", 40, $this->getElement($name . "default", "dat"), 10, 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_LANGUAGE:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					$this->htmlTextInput("we_" . $this->Name . "_language[" . $name . "default]", 40, $this->getElement($name . "default", "dat"), 15, 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_LINK:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					$this->htmlLinkInput($name, $identifier) .
					'</td></tr>';
				break;
			case we_objectFile::TYPE_SHOPVAT:
				$values = array();
				if(defined('SHOP_TABLE')){
					$allVats = we_shop_vats::getAllShopVATs();
					foreach($allVats as $id => $shopVat){
						$values[$id] = $shopVat->vat . ' - ' . $shopVat->text;
						/* 						if($shopVat->standard){
						  $standardId = $id;
						  $standardVal = $shopVat->vat;
						  } */
					}
				}
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					we_class::htmlSelect("we_" . $this->Name . "_shopVat[" . $name . "default]", $values, 1, $this->getElement($name . "default", "dat")) .
					'</td></tr>';
				break;
			default: // default for input, int and float

				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('modules_object', '[default]') . '</td>' .
					'<td width="170" class="defaultfont">' .
					$this->htmlTextInput("we_" . $this->Name . "_input[" . $name . "default]", 40, $this->getElement($name . "default", "dat"), ($type == we_objectFile::TYPE_INT
								? 10 : ($type == we_objectFile::TYPE_FLOAT ? 19 : 255)), 'onchange="_EditorFrame.setEditorIsHot(true);" weType="' . $type . '"', "text", 388) .
					'</td></tr>';
				break;
		}

		switch($type){
			case we_objectFile::TYPE_TEXT:
			case we_objectFile::TYPE_INPUT:
			case we_objectFile::TYPE_META:
			case we_objectFile::TYPE_LINK:
			case we_objectFile::TYPE_HREF:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td>' .
					'<td width="170" class="defaultfont">' .
					// title
					we_html_forms::radiobutton($name, ($this->getElement("title", "dat") == $name), "we_" . $this->Name . "_input[title]", g_l('global', "[title]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[title]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
					// description
					we_html_forms::radiobutton($name, ($this->getElement("desc", "dat") == $name), "we_" . $this->Name . "_input[desc]", g_l('global', "[description]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[desc]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
					// keywords
					we_html_forms::radiobutton($name, ($this->getElement("keywords", "dat") == $name), "we_" . $this->Name . "_input[keywords]", g_l('weClass', "[Keywords]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[keywords]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
					'</td></tr>';
				break;
			default:
		}

		switch($type){
			case we_objectFile::TYPE_TEXT:
			case we_objectFile::TYPE_INPUT:
			case we_objectFile::TYPE_DATE:
				$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin"></td>' .
					'<td width="170" class="defaultfont">';
				if($type == we_objectFile::TYPE_DATE){
					$content .= we_html_forms::radiobutton($name, ($this->getElement("urlfield0", "dat") == $name), "we_" . $this->Name . "_input[urlfield0]", g_l('weClass', "[urlfield0]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[urlfield0]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}");
				} else {
					$content .= we_html_forms::radiobutton($name, ($this->getElement("urlfield1", "dat") == $name), "we_" . $this->Name . "_input[urlfield1]", g_l('weClass', "[urlfield1]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[urlfield1]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
						we_html_forms::radiobutton($name, ($this->getElement("urlfield2", "dat") == $name), "we_" . $this->Name . "_input[urlfield2]", g_l('weClass', "[urlfield2]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[urlfield2]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}") .
						we_html_forms::radiobutton($name, ($this->getElement("urlfield3", "dat") == $name), "we_" . $this->Name . "_input[urlfield3]", g_l('weClass', "[urlfield3]"), true, "defaultfont", "if(this.waschecked){document.getElementById('empty_" . $this->Name . "_input[urlfield3]').checked=true;this.waschecked=false;}_EditorFrame.setEditorIsHot(true);", false, "", 0, 0, "if(this.checked){this.waschecked=true}");
				}
				$content .= '</td></tr>';
				break;
			default:
		}


		if($type != we_objectFile::TYPE_CHECKBOX){
			//Pflichtfeld
			$content .= '<tr valign="top"><td  width="100" class="defaultfont"></td>' .
				'<td width="170" class="defaultfont">' .
				we_html_forms::checkbox(1, $this->getElement($name . "required", "dat"), "we_" . $this->Name . "_input[" . $name . "required1]", g_l('global', "[required_field]"), true, "defaultfont", "if(this.checked){document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "required]" . "'].value=1;}else{ document.we_form.elements['" . "we_" . $this->Name . "_input[" . $name . "required]" . "'].value=0;}");

			if(defined('SHOP_TABLE') && $this->canHaveVariants() && $this->isVariantField($name)){
				$content .= we_html_forms::checkboxWithHidden($this->getElement($name . "variant", "dat"), "we_" . $this->Name . "_variant[" . $name . "variant]", g_l('global', "[variant_field]"), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
			}
			$content .= '<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="' . $this->getElement($name . "required", "dat") . '" />' .
				'</td></tr>';
			// description for editmode.
		} else if(defined('SHOP_TABLE')){
			//Pflichtfeld
			$content .= '<tr valign="top"><td  width="100" class="defaultfont"></td>' .
				'<td width="170" class="defaultfont">';
			if($this->canHaveVariants() && $this->isVariantField($name)){
				$content .= we_html_forms::checkboxWithHidden($this->getElement($name . "variant", "dat"), "we_" . $this->Name . "_variant[" . $name . "variant]", g_l('global', "[variant_field]"), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
			}
			$content .= '<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="0" />' .
				'</td></tr>';
			// description for editmode.
		} else {
			$content .= '<input type=hidden name="' . "we_" . $this->Name . "_input[" . $name . "required]" . '" value="0" />';
		}


		$content .= '<tr valign="top"><td  width="100" class="weMultiIconBoxHeadlineThin">' . g_l('weClass', "[fieldusers]") . '</td>
			<td width="170" class="defaultfont" >' .
			$this->formUsers1($name, $identifier) .
			'</td></tr>';

		return $content;
	}

	function htmlHref($n){
		$type = isset($this->elements[$n . "hreftype"]["dat"]) ?
			$this->elements[$n . "hreftype"]["dat"] :
			"";

		$n .= 'default';
		$hrefArr = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(!is_array($hrefArr)){
			$hrefArr = array();
		}

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
		$intPath_elem_Name = 'we_' . $this->Name . '_href[' . $nintPath . ']';
		$intID_elem_Name = 'we_' . $this->Name . '_href[' . $nintID . ']';
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
		return '<table border="0" cellpadding="0" cellspacing="0">' . $out . '</table>';
	}

	private function htmlLinkInput($n, $i){
		$n .= 'default';

		$attribs = array(
			'name' => $n
		);
		$link = $this->getElement($n) ? unserialize($this->getElement($n)) : array();
		if(!is_array($link)){
			$link = array();
		}

		$link = $link ? $link : array("ctype" => "text", "type" => we_base_link::TYPE_EXT, "href" => "#", "text" => g_l('global', "[new_link]"));

		$img = new we_imageDocument();
		$content = parent::getLinkContent($link, $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);

		$startTag = $this->getLinkStartTag($link, $attribs, $this->ParentID, $this->Path, $GLOBALS['DB_WE'], $img);
		$editbut = we_html_button::create_button("edit", "javascript:we_cmd('edit_link_at_class','" . $n . "','','" . $i . "');");
		$delbut = we_html_button::create_button("image:btn_function_trash", "javascript:setScrollTo();we_cmd('object_delete_link_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $n . "')");
		if(!$content){
			$content = g_l('global', "[new_link]");
		}
		return "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
					<tr><td class=\"defaultfont\">" . ($startTag ? $startTag : '') . $content . "</a></td>
						<td width=\"5\"></td><td>" . we_html_button::create_button_table(array($editbut, $delbut), 5) . "</td>
					</tr>
					</table>";
	}

	private function getObjectFieldHTML($ObjectID, $attribs, $editable = true){
		$pid = intval($this->getElement($ObjectID, "dat"));
		if(!$editable){
			return '';
		}
		$db = new DB_WE();
		$classPath = f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $pid, 'Path', $db);
		$textname = 'we_' . $this->Name . '_txt[' . $pid . '_path]';
		$idname = 'we_' . $this->Name . "_input[" . $ObjectID . "default]";
		$myid = $this->getElement($ObjectID . "default", "dat");
		$DoubleNames = $this->includedObjectHasDoubbleFieldNames($pid);
		$path = $this->getElement("we_object_" . $pid . "_path");
		$path = $path ? $path : ($myid ? f("SELECT Path FROM " . OBJECT_FILES_TABLE . " WHERE ID=$myid", "Path", $db) : '');
		$rootDir = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . ' WHERE Path="' . $db->escape($classPath) . '"', "ID", $db);
		$table = OBJECT_FILES_TABLE;
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("top.opener._EditorFrame.setEditorIsHot(true);");
		$button = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDir . "','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS")
						? 0 : 1) . ")");
		$delbutton = we_html_button::create_button("image:btn_function_trash", "javascript:document.forms['we_form'].elements['" . $idname . "'].value='';document.forms['we_form'].elements['" . $textname . "'].value=''");
		/*
		  DAMD: der Autocompleter funktioniert hier nicht. Der HTML-Cokde wird dynamisch erzeugt das
		  $yuiSuggest =& weSuggest::getInstance();
		  $yuiSuggest->setAcId("TypeObject");
		  $yuiSuggest->setContentType("folder,objectFile");
		  $yuiSuggest->setInput($textname,$path);
		  $yuiSuggest->setMaxResults(20);
		  $yuiSuggest->setMayBeEmpty(false);
		  $yuiSuggest->setResult($idname,$myid);
		  $yuiSuggest->setSelector(weSuggest::DocSelector);
		  $yuiSuggest->setTable($table);
		  $yuiSuggest->setWidth(246);
		  $yuiSuggest->setSelectButton($button,10);
		  $yuiSuggest->setTrashButton($delbutton,5);
		  $yuiSuggest->setAddJS("YAHOO.autocoml.init;");

		  return weSuggest::getYuiFiles().$yuiSuggest->getHTML().$yuiSuggest->getYuiCode();
		 */
		return we_html_tools::htmlFormElementTable($this->htmlTextInput($textname, 30, $path, "", ' readonly', "text", 246, 0), "", "left", "defaultfont", $this->htmlHidden($idname, $myid), we_html_tools::getPixel(10, 4), $button, we_html_tools::getPixel(5, 4), $delbutton) . ($DoubleNames
					? '<span style="color:red" >' . sprintf(g_l('modules_object', '[incObject_sameFieldname]'), implode(', ', $DoubleNames)) . '</span>' : '');
	}

	private function getMultiObjectFieldHTML($name, $i, $f){
		$pid = $this->getElement($name . "class", "dat");

		$db = new DB_WE();
		$classPath = f('SELECT Path FROM ' . OBJECT_TABLE . " WHERE ID=" . intval($pid), "", $db);
		$textname = 'we_' . $this->Name . '_txt[' . $name . '_path' . $f . ']';
		$idname = 'we_' . $this->Name . "_input[" . $name . "defaultvalue" . $f . "]";
		$myid = $this->getElement($name . "defaultvalue" . $f, "dat");

		$path = $this->getElement("we_object_" . $name . "_path");
		$path = ($path ? $path : ($myid ? f("SELECT Path FROM " . OBJECT_FILES_TABLE . " WHERE ID=$myid", "Path", $db) : ''));
		$rootDir = f('SELECT ID FROM ' . OBJECT_FILES_TABLE . " WHERE Path='" . $classPath . "'", '', $db);

		$table = OBJECT_FILES_TABLE;
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("top.opener._EditorFrame.setEditorIsHot(true);");

		$selectObject = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms['we_form'].elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDir . "','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS")
						? 0 : 1) . ")");

		return '<tr>' .
			'<td>' . $this->htmlTextInput($textname, 30, $path, 255, 'onchange="_EditorFrame.setEditorIsHot(true);" readonly ', "text", 146) . '</td>' .
			'<td>' . we_html_button::create_button_table(
				array(
				$selectObject,
				$this->htmlHidden($idname, $myid),
				(($this->elements[$name . "count"]["dat"] + 1 < $this->getElement($name . "max") || $this->getElement($name . "max") == "") ?
					we_html_button::create_button("image:btn_add_listelement", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_insert_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')", true, 40, 22)
						:
					we_html_button::create_button("image:btn_add_listelement", "#", true, 21, 22, "", "", true)
				),
				(($f > 0) ?
					we_html_button::create_button("image:btn_direction_up", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_up_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')", true, 21, 22)
						:
					we_html_button::create_button("image:btn_direction_up", "#", true, 21, 22, "", "", true)
				),
				(($f < ($this->elements[$name . "count"]["dat"])) ?
					we_html_button::create_button("image:btn_direction_down", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_down_meta_at_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')", true, 21, 22)
						:
					we_html_button::create_button("image:btn_direction_down", "#", true, 21, 22, "", "", true)
				),
				($this->elements[$name . "count"]["dat"] >= 1 ?
					we_html_button::create_button("image:btn_function_trash", "javascript:_EditorFrame.setEditorIsHot(true);we_cmd('object_delete_meta_class','" . $GLOBALS['we_transaction'] . "','" . ($i) . "','" . $name . "','" . ($f) . "')", true, 27, 22)
						:
					we_html_button::create_button("image:btn_function_trash", "#", true, 27, 22, "", "", true)
				)
				), 5
			) .
			'</td></tr>';
	}

	function dhtmledit($name, $i = 0){
		return we_html_button::create_button("attributes", "javascript:setScrollTo();we_cmd('object_editObjectTextArea','" . $i . "','" . $name . "','" . $GLOBALS["we_transaction"] . "');") .
			$this->getWysiwygArea($name);
	}

	function getWysiwygArea($name){
		$rmfp = isset($this->elements[$name . "removefirstparagraph"]["dat"]) &&
			($this->elements[$name . "removefirstparagraph"]["dat"] == 'on' || $this->elements[$name . "removefirstparagraph"]["dat"] == 'off') ? ($this->elements[$name . "removefirstparagraph"]["dat"] == 'on'
					? true : false) :
			(defined('REMOVEFIRSTPARAGRAPH_DEFAULT') ? REMOVEFIRSTPARAGRAPH_DEFAULT : true);
		$commands = isset($this->elements[$name . "commands"]["dat"]) && $this->elements[$name . "commands"]["dat"] ? $this->elements[$name . "commands"]["dat"] :
			(defined('COMMANDS_DEFAULT') ? COMMANDS_DEFAULT : '');
		$attribs = array(
			"removefirstparagraph" => $rmfp,
			"xml" => isset($this->elements[$name . "xml"]["dat"]) ? $this->elements[$name . "xml"]["dat"] : "",
			"dhtmledit" => isset($this->elements[$name . "dhtmledit"]["dat"]) ? $this->elements[$name . "dhtmledit"]["dat"] : "",
			"wysiwyg" => isset($this->elements[$name . "dhtmledit"]["dat"]) ? $this->elements[$name . "dhtmledit"]["dat"] : "",
			"showmenus" => isset($this->elements[$name . "showmenus"]["dat"]) ? $this->elements[$name . "showmenus"]["dat"] : "off",
			"commands" => $commands,
			"contextmenu" => isset($this->elements[$name . "contextmenu"]["dat"]) ? $this->elements[$name . "contextmenu"]["dat"] : "",
			"classes" => isset($this->elements[$name . "cssClasses"]["dat"]) ? $this->elements[$name . "cssClasses"]["dat"] : "",
			"width" => 386, //isset($this->elements[$name."width"]["dat"]) ? $this->elements[$name."width"]["dat"] : 618,
			"height" => 52, //isset($this->elements[$name."height"]["dat"]) ? $this->elements[$name."height"]["dat"] : 200,
			"rows" => 3,
			"bgcolor" => isset($this->elements[$name . "bgcolor"]["dat"]) ? $this->elements[$name . "bgcolor"]["dat"] : (WYSIWYG_TYPE == 'tinyMCE' ? '' : 'white'),
			"tinyparams" => isset($this->elements[$name . "tinyparams"]["dat"]) ? $this->elements[$name . "tinyparams"]["dat"] : "",
			"templates" => isset($this->elements[$name . "templates"]["dat"]) ? $this->elements[$name . "templates"]["dat"] : "",
			"class" => isset($this->elements[$name . "class"]["dat"]) ? $this->elements[$name . "class"]["dat"] : "",
			"cols" => 30,
			"inlineedit" => isset($this->elements[$name . "inlineedit"]["dat"]) ? $this->elements[$name . "inlineedit"]["dat"] : "",
			"stylesheets" => $this->CSS,
			"spellchecker" => true,
		);

		$autobr = isset($this->elements[$name . "autobr"]["dat"]) ? $this->elements[$name . "autobr"]["dat"] : "";
		$autobrName = 'we_' . $this->Name . '_input[' . $name . 'autobr]';

		$value = $this->getElement($name . "default", "dat");
		return we_html_forms::weTextarea("we_" . $this->Name . "_input[" . $name . "default]", $value, $attribs, $autobr, $autobrName, true, "", (($this->CSS || $attribs["classes"])
						? false : true), false, false, $rmfp, "");
	}

	function add_user_to_field($id, $name){
		$users = makeArrayFromCSV($this->getElement($name . "users", "dat"));
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id, $users))){
				$users[] = $id;
			}
		}
		$this->elements[$name . "users"]["dat"] = makeCSVFromArray($users, true);
	}

	function del_user_from_field($id, $name){
		$csv = str_replace($id . ',', '', $this->getElement($name . "users", "dat"));
		$this->elements[$name . "users"]["dat"] = ($csv == ',' ? '' : $csv);
	}

	function formUsers1($name, $nr = 0){
		$users = $this->getElement($name . "users", "dat") ? explode(",", $this->getElement($name . "users", "dat")) : array();
		$content = '<table border="0" cellpadding="0" cellspacing="0" width="388">' .
			'<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(324, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr>';
		if(empty($users)){
			$content .= '<tr><td><img src="' . ICON_DIR . 'usergroup.gif" width="16" height="18" /></td><td class="defaultfont">' . g_l('weClass', "[everybody]") . '</td><td>' . we_html_tools::getPixel(26, 18) . '</td></tr>';
		} else {
			for($i = 1; $i < (count($users) - 1); $i++){
				$foo = getHash('SELECT Path,Icon FROM ' . USER_TABLE . ' WHERE ID=' . intval($users[$i]), $this->DB_WE);
				$content .= '<tr><td>' . (empty($foo) ? '' : '<img src="' . ICON_DIR . $foo["Icon"] . '" width="16" height="18" />') . '</td><td class="defaultfont">' . (empty($foo)
							? 'Unknown' : $foo["Path"]) . '</td><td>' . we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('object_del_user_from_field','" . $GLOBALS['we_transaction'] . "','" . $nr . "'," . $users[$i] . ",'" . $name . "');") . '</td></tr>';
			}
		}
		$content .= '<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(324, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr></table>';

		$textname = "we_" . $this->Name . "_input[" . $name . "usertext]";
		$idname = "we_" . $this->Name . "_input[" . $name . "userid]";
		$delallbut = we_html_button::create_button("delete_all", "javascript:we_cmd('object_del_all_users','" . $GLOBALS['we_transaction'] . "','" . $nr . "','" . $name . "')", true, 0, 0, "", "", count($users)
						? false : true);
		$addbut = $this->htmlHidden($idname, 0) . $this->htmlHidden($textname, "") . we_html_button::create_button("add", "javascript:we_cmd('browse_users','document.forms[\\'we_form\\'].elements[\\'" . $idname . "\\'].value','document.forms[\\'we_form\\'].elements[\\'" . $textname . "\\'].value','',document.forms['we_form'].elements['" . $idname . "'].value,'fillIDs();opener.we_cmd(\\'object_add_user_to_field\\',\\'" . $GLOBALS['we_transaction'] . "\\',\\'" . $nr . "\\', top.allIDs,\\'" . $name . "\\')','','',1)");

		return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' .
			'<div style="width:388px;" class="multichooser">' . $content . '</div></td></tr><tr><td align="right">' . we_html_tools::getPixel(2, 4) . we_html_button::create_button_table(array($delallbut, $addbut)) . '</td></tr></table>';
	}

	function formUsers($canChange = true){
		$users = makeArrayFromCSV($this->Users);
		$usersReadOnly = $this->UsersReadOnly ? unserialize($this->UsersReadOnly) : array();

		$content = '<table border="0" cellpadding="0" cellspacing="0" width="388">' .
			'<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(333, 2) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(80, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr>';

		if($users){
			$this->DB_WE->query('SELECT ID,Path,Icon FROM ' . USER_TABLE . ' WHERE ID IN(' . implode(',', $users) . ')');
			$allUsers = $this->DB_WE->getAllFirst();
			foreach($users as $user){
				$content .= '<tr><td>' . (isset($allUsers[$user]) ? '<img src="' . ICON_DIR . $allUsers[$user]["Icon"] . '" width="16" height="18" />' : '') . '</td><td class="defaultfont">' . (isset($allUsers[$user])
							? $allUsers[$user]["Path"] : 'Unknown' ) . '</td><td>' .
					($canChange ?
						$this->htmlHidden('we_users_read_only[' . $user . ']', (isset($usersReadOnly[$user]) && $usersReadOnly[$user]) ? $usersReadOnly[$user] : "" ) . '<input type="checkbox" value="1" name="wetmp_users_read_only[' . $user . ']"' . ( (isset($usersReadOnly[$user]) && $usersReadOnly[$user] )
								? ' checked' : '') . ' onclick="this.form.elements[\'we_users_read_only[' . $user . ']\'].value=(this.checked ? 1 : 0);_EditorFrame.setEditorIsHot(true);" />'
							:
						'<img src="' . TREE_IMAGE_DIR . ($usersReadOnly[$user] ? 'check1_disabled.gif' : 'check0_disabled.gif') . '" />') . '</td><td class="defaultfont">' . g_l('weClass', "[readOnly]") . '</td><td>' . ($canChange
							? we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('users_del_user','" . $user . "');_EditorFrame.setEditorIsHot(true);") : "") . '</td></tr>';
			}
		} else {
			$content .= '<tr><td><img src="' . ICON_DIR . 'user.gif" width="16" height="18" /></td><td class="defaultfont">' . g_l('weClass', "[onlyOwner]") . '</td><td></td></tr>';
		}
		$content .= '<tr><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(333, 2) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(80, 2) . '</td><td>' . we_html_tools::getPixel(26, 2) . '</td></tr></table>';

		$textname = 'userNameTmp';
		$idname = 'userIDTmp';
		$delallbut = we_html_button::create_button("delete_all", "javascript:we_cmd('object_del_all_users','')", true, 0, 0, "", "", $this->Users ? false : true);
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$wecmdenc5 = we_base_request::encCmd("fillIDs();opener.we_cmd('users_add_user',top.allIDs)");
		$addbut = $canChange ?
			$this->htmlHidden($idname, "") . $this->htmlHidden($textname, "") . we_html_button::create_button("add", "javascript:we_cmd('browse_users','" . $wecmdenc1 . "','" . $wecmdenc2 . "','',document.forms[0].elements['" . $idname . "'].value,'" . $wecmdenc5 . "','','',1)")
				: '';

		$content = '<table border="0" cellpadding="0" cellspacing="0">
<tr><td><div style="width:506px;" class="multichooser">' . $content . '</div></td></tr>' .
			($canChange ? '<tr><td align="right">' . we_html_tools::getPixel(2, 6) . '<br/>' . we_html_button::create_button_table(array($delallbut, $addbut)) . '</td></tr>'
					: "") . '</table>';

		return we_html_tools::htmlFormElementTable($content, g_l('weClass', "[otherowners]"), "left", "defaultfont");
	}

	function del_all_users($name){
		if($name == ''){
			$this->Users = '';
		} else {
			$this->elements[$name . "users"]["dat"] = '';
		}
	}

	function add_user($id){
		$users = makeArrayFromCSV($this->Users);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id, $users))){
				$users[] = $id;
			}
		}
		$this->Users = makeCSVFromArray($users, true);
	}

	function del_user($id){
		$users = makeArrayFromCSV($this->Users);
		if(in_array($id, $users)){
			$pos = array_search($id, $users);
			if($pos !== false || $pos == '0'){
				array_splice($users, $pos, 1);
			}
		}
		$this->Users = makeCSVFromArray($users, true);
	}

	function add_css($id){
		$this->CSS = implode(',', array_filter(
				array_unique(
					array_merge(
						explode(',', $this->CSS), explode(',', $id))
			))
		);
	}

	function del_css($id){
		$css = makeArrayFromCSV($this->CSS);
		if(($pos = array_search($id, $css)) !== false){
			unset($css[$pos]);
		}
		$this->CSS = makeCSVFromArray($css, true);
	}

	private function getImageHTML($name, $defaultname, $i = 0){
		$img = new we_imageDocument();
		$id = $defaultname; //$this->getElement($defaultname);
		if($id){
			$img->initByID($id, FILE_TABLE, false);
		} else {
			$img->we_new();
		}

		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.top.we_cmd('object_reload_entry_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "');opener._EditorFrame.setEditorIsHot(true);");

		$content = '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button_table(array(
				we_html_button::create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "',0,'" . we_base_ContentTypes::IMAGE . "')"),
				we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "')")
				)
			) .
			'<br/>' . $img->getHtml();

		// gets thumbnails and shows a select field, if there are any:
		$thumbdb = new DB_WE();
		$thumbdb->query('SELECT Name FROM ' . THUMBNAILS_TABLE);
		$thumbList = $thumbdb->getAll(true);
		if(!empty($thumbList)){
			$content .= "<br />" . g_l('modules_object', '[use_thumbnail_preview]') . ":<br />";
			array_unshift($thumbList, '-');
			$currentSelection = (isset($this->elements["" . $name . "Thumb"]) && isset($this->elements["" . $name . "Thumb"]["dat"]) && isset($thumbList[$this->elements["" . $name . "Thumb"]["dat"]])
						?
					$this->elements["" . $name . "Thumb"]["dat"] :
					'');

			$content .= $this->htmlSelect("we_" . $this->Name . "_input[" . $name . "Thumb]", $thumbList, 1, $currentSelection, "", array('onchange' => "_EditorFrame.setEditorIsHot(true);"), "value", 388);
		}
		return $content;
	}

	private function getFlashmovieHTML($name, $defaultname, $i = 0){
		$img = new we_flashDocument();
		$id = $defaultname; //$this->getElement($defaultname);
		if($id){
			$img->initByID($id, FILE_TABLE, false);
		} else {
			$img->we_new();
		}

		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.top.we_cmd('object_reload_entry_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "');opener._EditorFrame.setEditorIsHot(true);");

		return '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button_table(array(
				we_html_button::create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "',0,'" . we_base_ContentTypes::FLASH . "')"),
				we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "')")
				)
			) .
			'<br/>' . $img->getHtml();
	}

	private function getQuicktimeHTML($name, $defaultname, $i = 0){
		$img = new we_quicktimeDocument();
		$id = $defaultname; //$this->getElement($defaultname);
		if($id){
			$img->initByID($id, FILE_TABLE, false);
		} else {
			$img->we_new();
		}

		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.top.we_cmd('object_reload_entry_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "');opener._EditorFrame.setEditorIsHot(true);");

		return '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button_table(array(
				we_html_button::create_button("edit", "javascript:we_cmd('openDocselector','" . $id . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "',0,'" . we_base_ContentTypes::QUICKTIME . "')"),
				we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "')")
				)
			) .
			'<br/>' . $img->getHtml();
	}

	private function getBinaryHTML($name, $defaultname, $i = 0){
		$other = new we_otherDocument();
		$id = $defaultname; //$this->getElement($defaultname);
		$other->initByID($id, FILE_TABLE, false);
		$fname = 'we_' . $this->Name . '_input[' . $name . ']';
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $fname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener.top.we_cmd('object_reload_entry_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "');opener._EditorFrame.setEditorIsHot(true);");

		return '<input type=hidden name="' . $fname . '" value="' . $defaultname . '" />' .
			we_html_button::create_button_table(array(
				we_html_button::create_button("select", "javascript:we_cmd('openDocselector','" . $id . "','" . FILE_TABLE . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "',0,'" . we_base_ContentTypes::APPLICATION . "')"),
				we_html_button::create_button("image:btn_function_trash", "javascript:we_cmd('object_remove_image_at_class','" . $GLOBALS['we_transaction'] . "','" . $i . "','" . $name . "');")
				)
			) .
			'<br/>' . $other->getHtml();
	}

	function formDefault(){
		$select = '';
		if(isset($this->elements["Defaultanzahl"]["dat"])){
			$this->DefaultText = '';

			for($i = 0; $i <= $this->elements["Defaultanzahl"]["dat"]; $i++){
				$was = "DefaultText_" . $i;
				if($this->elements[$was]["dat"] != ""){
					if(stristr($this->elements[$was]["dat"], 'unique')){
						$this->elements[$was]["dat"] = "%" . str_replace("%", "", $this->elements[$was]["dat"]) . (( isset($this->elements["unique_" . $i]["dat"]) && $this->elements["unique_" . $i]["dat"] > 0 )
									? $this->elements["unique_" . $i]["dat"] : 16) . "%";
					}
					$this->DefaultText .= $this->elements[$was]["dat"];
				}
			}
		}

		$all = $this->DefaultText;
		$text1 = 0;
		$zahl = 0;
		$regs = array();

		while(!empty($all)){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all, strlen($regs[1]) + 2);
				$key = $regs[1];
				if(preg_match('/unique([^%]*)/', $key, $regs)){
					$anz = (!$regs[1] ? 16 : abs($regs[1]));
					$unique = substr(md5(uniqid(__FUNCTION__, true)), 0, min($anz, 32));
					$text = preg_replace('/%unique[^%]*%/', $unique, (isset($text) ? $text : ""));
					$select .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultText_" . $zahl . "]", g_l('modules_object', '[value]'), 1, "%unique%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
						$this->htmlTextInput("we_" . $this->Name . "_input[unique_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
				} else {
					$select .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultText_" . $zahl . "]", g_l('modules_object', '[value]'), 1, "%" . $key . "%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;";
				}
			} else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all, strlen($regs[1]));
				$key = $regs[1];
				$select .= $this->htmlSelect("textwert_" . $zahl, g_l('modules_object', '[value]'), 1, "Text", "", array('onchange' => '_EditorFrame.setEditorIsHot(true); document.we_form.elements[\'we_' . $this->Name . '_input[DefaultText_' . $zahl . ']\'].value = this.options[this.selectedIndex].value; we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
					$this->htmlTextInput("we_" . $this->Name . "_input[DefaultText_" . $zahl . "]", 40, $key, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
			}

			$select .= we_html_element::htmlBr();
			$zahl++;
		}

		$select .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultText_" . $zahl . "]", g_l('modules_object', '[value]'), 1, "", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
			'<input type = "hidden" name="we_' . $this->Name . '_input[Defaultanzahl]" value="' . $zahl . '" />';

		$var_flip = array_flip(g_l('modules_object', '[url]'));

		$select2 = "";
		if(isset($this->elements["DefaultanzahlUrl"]["dat"])){
			$this->DefaultUrl = "";

			for($i = 0; $i <= $this->elements["DefaultanzahlUrl"]["dat"]; $i++){
				$was = "DefaultUrl_" . $i;
				if($this->elements[$was]["dat"] != ""){ //&& in_array($this->elements[$was]["dat"],$var_flip)
					if(stristr($this->elements[$was]["dat"], 'urlunique')){
						$this->elements[$was]["dat"] = "%" . str_replace("%", "", $this->elements[$was]["dat"]) . (( isset($this->elements["urlunique_" . $i]["dat"]) && $this->elements["urlunique_" . $i]["dat"] > 0 )
									? $this->elements["urlunique_" . $i]["dat"] : 16) . "%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield1')){
						$this->elements[$was]["dat"] = "%" . str_replace("%", "", $this->elements[$was]["dat"]) . (( isset($this->elements["urlfield1_" . $i]["dat"]) && $this->elements["urlfield1_" . $i]["dat"] > 0 )
									? $this->elements["urlfield1_" . $i]["dat"] : 64) . "%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield2')){
						$this->elements[$was]["dat"] = "%" . str_replace("%", "", $this->elements[$was]["dat"]) . (( isset($this->elements["urlfield2_" . $i]["dat"]) && $this->elements["urlfield2_" . $i]["dat"] > 0 )
									? $this->elements["urlfield2_" . $i]["dat"] : 64) . "%";
					}
					if(stristr($this->elements[$was]["dat"], 'urlfield3')){
						$this->elements[$was]["dat"] = "%" . str_replace("%", "", $this->elements[$was]["dat"]) . (( isset($this->elements["urlfield3_" . $i]["dat"]) && $this->elements["urlfield3_" . $i]["dat"] > 0 )
									? $this->elements["urlfield3_" . $i]["dat"] : 64) . "%";
					}
					$this->DefaultUrl .= $this->elements[$was]["dat"];
				}
			}
		}

		$all = $this->DefaultUrl;
		$text1 = 0;
		$zahl = 0;

		while(!empty($all)){
			if(preg_match('/^%([^%]+)%/', $all, $regs)){
				$all = substr($all, strlen($regs[1]) + 2);
				$key = $regs[1];
				if(preg_match('/urlunique([^%]*)/', $key, $regs)){
					$anz = (!$regs[1] ? 16 : abs($regs[1]));
					$unique = substr(md5(uniqid(__FUNCTION__, true)), 0, min($anz, 32));
					$text = preg_replace('/%urlunique[^%]*%/', $unique, (isset($text) ? $text : ""));
					$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "%urlunique%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
						$this->htmlTextInput("we_" . $this->Name . "_input[urlunique_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
				} else {
					if(preg_match('/urlfield1([^%]*)/', $key, $regs)){
						$anz = (!$regs[1] ? 64 : abs($regs[1]));
						$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "%urlfield1%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
							$this->htmlTextInput("we_" . $this->Name . "_input[urlfield1_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
					} elseif(preg_match('/urlfield2([^%]*)/', $key, $regs)){
						$anz = (!$regs[1] ? 64 : abs($regs[1]));
						$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "%urlfield2%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
							$this->htmlTextInput("we_" . $this->Name . "_input[urlfield2_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
					} elseif(preg_match('/urlfield3([^%]*)/', $key, $regs)){
						$anz = (!$regs[1] ? 64 : abs($regs[1]));
						$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "%urlfield3%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
							$this->htmlTextInput("we_" . $this->Name . "_input[urlfield3_" . $zahl . "]", 40, $anz, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
					} else {
						$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "%" . $key . "%", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;";
					}
				}
			} else if(preg_match('/^([^%]+)/', $all, $regs)){
				$all = substr($all, strlen($regs[1]));
				$key = $regs[1];
				$select2 .= $this->htmlSelect("textwert_" . $zahl, g_l('modules_object', '[url]'), 1, "Text", "", array('onchange' => '_EditorFrame.setEditorIsHot(true); document.we_form.elements[\'we_' . $this->Name . '_input[DefaultUrl_' . $zahl . ']\'].value = this.options[this.selectedIndex].value; we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
					$this->htmlTextInput("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", 40, $key, 255, 'onchange="_EditorFrame.setEditorIsHot(true);"', "text", 140);
			}

			$select2 .= we_html_element::htmlBr();
			$zahl++;
		}

		$select2 .= $this->htmlSelect("we_" . $this->Name . "_input[DefaultUrl_" . $zahl . "]", g_l('modules_object', '[url]'), 1, "", "", array('onchange' => '_EditorFrame.setEditorIsHot(true);we_cmd(\'reload_editpage\');'), "value", 140) . "&nbsp;" .
			'<input type = "hidden" name="we_' . $this->Name . '_input[DefaultanzahlUrl]" value="' . $zahl . '" />';

		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="2" class="defaultfont" valign=top>' . g_l('modules_object', '[name]') . '</td><td>' . we_html_tools::getPixel(20, 20) . '</td></tr>
	<tr><td colspan="3" >' . $select . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 16) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="2" class="defaultfont" valign=top>' . g_l('modules_object', '[seourl]') . '</td><td>' . we_html_tools::getPixel(20, 20) . '</td></tr>
	<tr><td colspan="3" >' . $select2 . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 16) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" >' . $this->formTriggerDocument(true) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 16) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td class="defaultfont" valign=top>' . g_l('global', "[categorys]") . '</td><td>' . we_html_tools::getPixel(20, 20) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" >' . $this->formCategory() . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(20, 16) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" >' . $this->formRestrictUsers() . '</td></tr>' .
			($this->RestrictUsers ?
				'<tr><td>' . we_html_tools::getPixel(20, 10) . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' . we_html_tools::getPixel(100, 2) . '</td></tr>
	<tr><td colspan="3" >' . $this->formUsers() . '</td></tr>' :
				'') .
			'</table>';
	}

	function formRestrictUsers($canChange = true){
		if($canChange){
			$hiddenname = 'we_' . $this->Name . '_RestrictUsers';
			$tmpname = 'tmpwe_' . $this->Name . '_RestrictUsers';
			$hidden = $this->htmlHidden($hiddenname, abs($this->RestrictUsers));
			$check = we_html_forms::checkbox(1, $this->RestrictUsers ? true : false, $tmpname, g_l('weClass', "[limitedAccess]"), true, "defaultfont", "_EditorFrame.setEditorIsHot(true);this.form.elements['" . $hiddenname . "'].value=(this.checked ? '1' : '0');we_cmd('reload_editpage');");
			return $hidden . $check;
		} else {
			return '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="' . TREE_IMAGE_DIR . ($this->RestrictUsers ? 'check1_disabled.gif' : 'check0_disabled.gif') . '" /></td><td class="defaultfont">&nbsp;' . g_l('weClass', "[limitedAccess]") . '</td></tr></table>';
		}
	}

	function formPath(){
		return '<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . $this->formInputField('', 'Text', g_l('modules_object', '[classname]'), 30, 508, 255, 'onchange="_EditorFrame.setEditorIsHot(true);pathOfDocumentChanged();"') . '</td><td></td><td></td></tr>
</table>';
	}

	function formWorkspaces(){
		//remove not existing workspaces - deal with templates as well
		$arr = makeArrayFromCSV($this->Workspaces);
		$newArr = array();

		$_defaultArr = makeArrayFromCSV($this->DefaultWorkspaces);
		$_newDefaultArr = array();

		$_tmplArr = makeArrayFromCSV($this->Templates);
		$_newTmplArr = array();

		//    check if workspace exists - correct templates if neccessary !!
		for($i = 0; $i < count($arr); $i++){
			if(we_base_file::isWeFile($arr[$i], FILE_TABLE, $this->DB_WE)){
				$newArr[] = $arr[$i];
				if(in_array($arr[$i], $_defaultArr)){
					$_newDefaultArr[] = $arr[$i];
				}
				$_newTmplArr[] = (isset($_tmplArr[$i]) ? $_tmplArr[$i] : '');
			}
		}

		$this->Workspaces = makeCSVFromArray($newArr, true);
		$this->Templates = makeCSVFromArray($_newTmplArr, true);
		$this->DefaultWorkspaces = makeCSVFromArray($_newDefaultArr, true);

		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);fillIDs();opener.we_cmd('object_add_workspace',top.allIDs);");
		$button = we_html_button::create_button("add", "javascript:we_cmd('openDirselector','','" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','',1)");

		$addbut = $button;

		$obj = new MultiDirTemplateAndDefaultChooser(450, $this->Workspaces, "object_del_workspace", $addbut, get_ws(FILE_TABLE), $this->Templates, "we_" . $this->Name . "_Templates", "", get_ws(TEMPLATES_TABLE), "we_" . $this->Name . "_DefaultWorkspaces", $this->DefaultWorkspaces);
		$obj->CanDelete = true;
		$obj->create = 1;
		$content = $obj->get();

		if(isset($GLOBALS['WE_DEL_WORKSPACE_ERROR']) && $GLOBALS['WE_DEL_WORKSPACE_ERROR']){
			unset($GLOBALS['WE_DEL_WORKSPACE_ERROR']);
			$content .= we_html_element::jsElement(we_message_reporting::getShowMessageCall(addslashes(g_l('weClass', '[we_del_workspace_error]')), we_message_reporting::WE_MESSAGE_ERROR));
		}
		return $content;
	}

	function formWorkspacesFlag(){
		return '<div style="margin-bottom:8px;">' . we_html_forms::radiobutton(1, $this->WorkspaceFlag == 1, "we_" . $this->Name . "_WorkspaceFlag", g_l('modules_object', '[behaviour_all]')) . '</div><div>' .
			we_html_forms::radiobutton(0, $this->WorkspaceFlag != 1, "we_" . $this->Name . "_WorkspaceFlag", g_l('modules_object', '[behaviour_no]')) . '</div>';
	}

	function formCSS(){
		require_once(WE_INCLUDES_PATH . 'we_tools/MultiDirChooser.inc.php');
		$wecmdenc3 = we_base_request::encCmd("fillIDs();opener.we_cmd('object_add_css', top.allIDs);");

		$addbut = we_html_button::create_button("add", "javascript:we_cmd('openDocselector', 0, '" . FILE_TABLE . "','','','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::CSS . "', 1,1)");
		$css = new MultiDirChooser(510, $this->CSS, "object_del_css", $addbut, "", "Icon,Path", FILE_TABLE);
		return $css->get();
	}

	function formCopyDocument(){
		$idname = 'we_' . $this->Name . '_CopyID';
		$rootDIrID = 0;
		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('copyDocument',currentID);");

		$but = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].elements['" . $idname . "'].value,'" . $this->Table . "','" . $wecmdenc1 . "','','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDIrID . "','" . $this->ContentType . "');");
		return $this->htmlHidden($idname, $this->CopyID) . $but;
	}

	function copyDoc($id){
		if($id){
			$doc = new we_object();
			$doc->InitByID($id, $this->Table, we_class::LOAD_TEMP_DB);
			if($this->ID == 0){
				foreach($this->persistent_slots as $cur){
					$this->{$cur} = isset($doc->{$cur}) ? $doc->{$cur} : '';
				}
				$this->ObjectID = 0;
				$this->CreationDate = time();
				$this->CreatorID = $_SESSION["user"]["ID"];
				$this->ID = 0;
				$this->OldPath = "";
				$this->Published = 1;
				$this->Text .= "_copy";
				$this->Path = $this->ParentPath . $this->Text;
				$this->OldPath = $this->Path;
			}
			$this->elements = $doc->elements;
			foreach($this->elements as $n => $e){
				if(strtolower(substr($n, 0, 9)) == 'wholename'){
					if(isset($this->elements['neuefelder']) && is_array($this->elements['neuefelder'])){
						$this->elements['neuefelder']['dat'] .= "," . $e['dat'];
					} else {
						$this->elements['neuefelder']['dat'] = "," . $e['dat'];
					}
				}
			}
			$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			$this->Category = $doc->Category;
		}
	}

	function changeTempl_ob($nr, $id){
		$arr = makeArrayFromCSV($this->Templates);
		$arr[$nr] = $id;

		$this->Templates = makeCSVFromArray($arr, true);
	}

	function add_workspace($id){
		$workspaces = makeArrayFromCSV($this->Workspaces);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if(strlen($id) && (!in_array($id, $workspaces))){
				$workspaces[] = $id;
			}
		}
		$this->Workspaces = makeCSVFromArray($workspaces, true);
	}

	function del_workspace($id){
		if(f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=' . intval($this->ID) . " AND (Workspaces LIKE '," . intval($id) . ",' OR ExtraWorkspaces LIKE '," . intval($id) . ",') LIMIT 1", '', $this->DB_WE)){
			$GLOBALS['WE_DEL_WORKSPACE_ERROR'] = true;
			return;
		}

		$workspaces = makeArrayFromCSV($this->Workspaces);
		$defaultWorkspaces = makeArrayFromCSV($this->DefaultWorkspaces);
		$Templates = makeArrayFromCSV($this->Templates);
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
		$tempArr = array();

		foreach($workspaces as $ws){
			$tempArr[] = $ws;
		}

		$this->Workspaces = makeCSVFromArray($tempArr, true);

		$tempArr = array();

		foreach($defaultWorkspaces as $t){
			$tempArr[] = $t;
		}

		$this->DefaultWorkspaces = makeCSVFromArray($tempArr, true);

		$tempArr = array();

		foreach($Templates as $t){
			$tempArr[] = $t;
		}

		$this->Templates = makeCSVFromArray($tempArr, true);
	}

	function we_initSessDat($sessDat){
		//	charset must be in other namespace -> for header !!!
		$this->elements["Charset"]["dat"] = (isset($sessDat["0"]["SerializedArray"]["elements"]["Charset"]) ? $sessDat["0"]["SerializedArray"]["elements"]["Charset"]["dat"]
					: "");
		parent::we_initSessDat($sessDat);
		$this->setSort();
	}

	protected function i_getContentData(){
		$f = 0;

		if($this->ID){
			$rec = getHash('SELECT strOrder,DefaultCategory,DefaultValues,DefaultText,DefaultDesc,DefaultTitle,DefaultUrl,DefaultUrlfield0,DefaultUrlfield1,DefaultUrlfield2,DefaultUrlfield3,DefaultTriggerID,DefaultKeywords,DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $this->ID, $this->DB_WE);

			$this->strOrder = $rec["strOrder"];
			$this->setSort();

			$this->DefaultValues = $rec["DefaultValues"];

			$vals = unserialize($this->DefaultValues);
			$names = (is_array($vals) ? array_keys($vals) : array());

			foreach($names as $name){
				if($name == "WE_CSS_FOR_CLASS"){
					$this->CSS = $vals[$name];
				}
				if(isset($vals[$name]) && is_array($vals[$name])){
					$this->elements[$name . "count"]["dat"] = (( isset($vals[$name]["meta"]) && $vals[$name]["meta"]) ? (count($vals[$name]["meta"]) - 1) : 0);
					if(isset($vals[$name]["meta"]) && is_array($vals[$name]["meta"])){
						$keynames = array_keys($vals[$name]["meta"]);

						for($ll = 0; $ll <= count($vals[$name]["meta"]); $ll++){
							$this->elements[$name . "defaultkey" . $ll]["dat"] = isset($keynames[$ll]) ? $keynames[$ll] : "";
							$this->elements[$name . "defaultvalue" . $ll]["dat"] = isset($keynames[$ll]) ? $vals[$name]["meta"][$keynames[$ll]] : "";
						}
					}
				}
			}

			$this->DefaultCategory = $rec["DefaultCategory"];
			$this->Category = $this->DefaultCategory;
			$this->SerializedArray = unserialize($rec["DefaultValues"]);

			//	charset must be in other namespace -> for header !!!
			$this->elements["Charset"]["dat"] = (isset($this->SerializedArray["elements"]["Charset"]["dat"]) ? $this->SerializedArray["elements"]["Charset"]["dat"] : "");

			$this->WorkspaceFlag = isset($this->SerializedArray["WorkspaceFlag"]) ? $this->SerializedArray["WorkspaceFlag"] : "";
			$this->elements["title"]["dat"] = $rec["DefaultTitle"];
			$this->elements["desc"]["dat"] = $rec["DefaultDesc"];
			$this->elements["keywords"]["dat"] = $rec["DefaultKeywords"];

			$this->DefaultText = $rec["DefaultText"];
			$this->DefaultUrl = $rec["DefaultUrl"];

			$this->elements["urlfield0"]["dat"] = $rec["DefaultUrlfield0"];
			$this->elements["urlfield1"]["dat"] = $rec["DefaultUrlfield1"];
			$this->elements["urlfield2"]["dat"] = $rec["DefaultUrlfield2"];
			$this->elements["urlfield3"]["dat"] = $rec["DefaultUrlfield3"];
			$this->elements["triggerid"]["dat"] = $rec["DefaultTriggerID"];
			$this->DefaultTriggerID = $rec["DefaultTriggerID"];

			$ctable = OBJECT_X_TABLE . intval($this->ID);
			$tableInfo = $this->DB_WE->metadata($ctable);
			$fields = array(
				'max' => '',
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
				'intPath' => '',
			);
			foreach($tableInfo as $info){
				$type = $name = '';
				@list($type, $name) = explode('_', $info["name"], 2);
				if($name && $type != 'OF' && $type != 'variant'){

					$this->elements[$info["name"]]["dat"] = $name;
					$this->elements["wholename" . $this->getSortIndexByValue($f)]["dat"] = $info["name"];
					$this->elements[$info["name"] . self::ELEMENT_LENGHT]["dat"] = $info["len"];
					$this->elements[$info["name"] . self::ELEMENT_TYPE]["dat"] = $type;
					if(isset($vals[$info["name"]]["variant"])){
						$this->elements[$info["name"] . "variant"]["dat"] = $vals[$info["name"]]["variant"];
					}
					foreach($fields as $field => $def){
						$this->elements[$info["name"] . $field]['dat'] = isset($vals[$info["name"]][$field]) ? $vals[$info["name"]][$field] : $def;
					}

					$f++;
				}
			}
			$this->elements["Sortgesamt"]["dat"] = ($f - 1);
		}
	}

	protected function i_set_PersistentSlot($name, $value){
		if(in_array($name, $this->persistent_slots)){
			$this->$name = $value;
		} elseif($name == "Templates_0"){
			$this->Templates = "";
			$cnt = count(makeArrayFromCSV($this->Workspaces));
			for($i = 0; $i < $cnt; $i++){
				$this->Templates .= $_REQUEST["we_" . $this->Name . "_Templates_" . $i] . ",";
			}
			if($this->Templates){
				$this->Templates = ',' . $this->Templates;
			}
			$this->DefaultWorkspaces = '';
			$wsp = makeArrayFromCSV($this->Workspaces);
			for($i = 0; $i < count($wsp); $i++){
				if(isset($_REQUEST["we_" . $this->Name . "_DefaultWorkspaces_" . $i])){
					$this->DefaultWorkspaces .= $wsp[$i] . ',';
				}
			}
			if($this->DefaultWorkspaces){
				$this->DefaultWorkspaces = ',' . $this->DefaultWorkspaces;
			}
		}
	}

	protected function i_setText(){
		// do nothing here!
	}

	function i_filenameEmpty(){
		return ($this->Text == '');
	}

	function i_filenameNotValid(){
		$defTextValid = false;
		foreach($this->elements as $k => $v){
			if(is_string($k) && substr($k, 0, 12) == 'DefaultText_'){
				$end = substr($k, 12, strlen($k));
				if(isset($_REQUEST['textwert_' . $end]) && isset($v['dat']) && $v['dat'] != '' && preg_match('/[^\w\-.]/', $v['dat'])){
					$defTextValid = true;
					break;
				}
			}
		}
		return (preg_match('/[^\w\-.]/', $this->Text) || $defTextValid);
	}

	function i_filenameNotAllowed(){
		return false;
	}

	function i_filenameDouble(){
		return f('SELECT 1 FROM ' . $this->Table . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Text="' . $this->DB_WE->escape($this->Text) . '" AND ID!=' . intval($this->ID), '', $this->DB_WE);
	}

	function i_checkPathDiffAndCreate(){
		return true;
	}

	function i_hasDoubbleFieldNames(){
		$sort = $this->getElement('we_sort');
		$count = $this->getElement('Sortgesamt');
		$usedNames = array();
		if(is_array($sort)){
			for($i = 0; $i <= $count && !empty($sort); $i++){
				$foo = $this->getElement($this->getElement('wholename' . $this->getSortIndex($i)), 'dat');
				if(!in_array($foo, $usedNames)){
					$usedNames[] = $foo;
				} else {
					switch($this->getElement($this->getElement('wholename' . $this->getSortIndex($i)) . 'dtype', 'dat')){
						case we_objectFile::TYPE_OBJECT:
							return f('SELECT Path FROM ' . OBJECT_TABLE . ' WHERE ID=' . $foo, 'Path', $this->DB_WE);
						default:
							return $foo;
					}
				}
			}
		}
		return false;
	}

	function includedObjectHasDoubbleFieldNames($incClass){
		$sort = $this->getElement('we_sort');
		$count = $this->getElement('Sortgesamt');
		$usedNames = array();
		$doubleNames = array();
		if(is_array($sort)){
			for($i = 0; $i <= $count && !empty($sort); $i++){
				$foo = $this->getElement($this->getElement('wholename' . $this->getSortIndex($i)), 'dat');
				$usedNames[] = $foo;
			}
		}
		$incclassobj = new we_object();
		$incclassobj->initByID($incClass, $this->Table);
		$isort = $incclassobj->getElement('we_sort');
		$icount = $incclassobj->getElement('Sortgesamt');
		if(is_array($isort) && !empty($isort)){
			for($i = 0; $i <= $icount; $i++){
				$foo = $incclassobj->getElement($incclassobj->getElement('wholename' . $incclassobj->getSortIndex($i)), 'dat');
				if(in_array($foo, $usedNames)){
					$doubleNames[] = $foo;
				}
			}
		}
		return ($doubleNames ? $doubleNames : false);
	}

	protected function i_writeDocument(){
		return true; // we don't have to write!
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		if($_REQUEST){
			$regs = array();
			$hrefFields = false;

			foreach(array_keys($_REQUEST) as $n){
				if(preg_match('/^we_' . $this->Name . '_(' . we_objectFile::TYPE_HREF . ')$/', $n, $regs)){
					${$regs[1] . 'Fields'}|=true;
				}
			}

			if($hrefFields){
				$empty = array('int' => 1, 'intID' => '', 'intPath' => '', 'extPath' => '');
				$hrefs = $match = array();
				foreach($_REQUEST['we_' . $this->Name . '_' . we_objectFile::TYPE_HREF] as $k => $val){
					if(preg_match('|^(.+)' . we_base_link::MAGIC_INFIX . '(.+)$|', $k, $match)){
						$hrefs[$match[1]][$match[2]] = $val;
					}
				}
				foreach($hrefs as $k => $v){
					$href = array_merge($empty, $v);
					$this->setElement($k, serialize($href), we_objectFile::TYPE_HREF);
				}
			}
		}
	}

	public function we_save($resave = 0, $skipHook = 0){
		$this->save();
		if($resave == 0){
			we_history::insertIntoHistory($this);
		}
		/* hook */
		if(!$skipHook){
			$hook = new weHook('save', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}
		return true;
	}

	/**
	 * @return	if the field can have variants the function returns true otherwise false
	 * @param	$field - the name of the field
	 */
	function isVariantField($field){
		$types = array(we_objectFile::TYPE_INPUT, we_objectFile::TYPE_LINK, we_objectFile::TYPE_TEXT, we_objectFile::TYPE_IMG, we_objectFile::TYPE_INT, we_objectFile::TYPE_FLOAT, we_objectFile::TYPE_META, we_objectFile::TYPE_DATE, we_objectFile::TYPE_HREF); // #6924
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
		if(!defined('SHOP_TABLE')){
			return false;
		}
		$fields = $this->getAllVariantFields();
		$fieldnamesarr = array_keys($fields);
		$fieldnames = implode(',', $fieldnamesarr) . ',';
		return stristr($fieldnames, '_' . WE_SHOP_TITLE_FIELD_NAME . ',') && stristr($fieldnames, '_' . WE_SHOP_DESCRIPTION_FIELD_NAME . ',');
	}

	/**
	 * @desc 	the function returns the array with all object field names
	 * @return	array with the filed names and attributes
	 */
	function getAllVariantFields(){
		$return = array();
		$fields = unserialize($this->DefaultValues);
		if(is_array($fields)){
			foreach($fields as $name => $field){
				if($this->isVariantField($name)){
					$return[$name] = $field;
				}
			}
		}
		return $return;
	}

	/**
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
		$return = array();
		$fields = $this->getAllVariantFields();
		foreach($fields as $name => $field){
			if(isset($field['variant']) && $field['variant'] == 1){
				$return[$name] = $field;
			}
		}
		return $return;
	}

	/* creates the DirectoryChoooser field with the 'browse'-Button. Clicking on the Button opens the fileselector */

	function formDirChooser($width = '', $rootDirID = 0, $table = '', $Pathname = 'ParentPath', $IDName = 'ParentID', $cmd = '', $pathID = 0, $identifier = ''){
		$path = id_to_path($pathID);

		if(!$table){
			$table = $this->Table;
		}
		$textname = 'we_' . $this->Name . '_' . $Pathname . ($identifier ? '_' . $identifier : '');
		$idname = 'we_' . $this->Name . '_' . $IDName;
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);opener.pathOfDocumentChanged();" . str_replace('\\', '', $cmd));
		$button = we_html_button::create_button("select", "javascript:we_cmd('openDirselector',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','" . $rootDirID . "')");
		return we_html_tools::htmlFormElementTable($this->htmlTextInput($textname, 30, $path, "", ' readonly', "text", $width, 0), "", "left", "defaultfont", $this->htmlHidden($idname, $pathID), we_html_tools::getPixel(20, 4), $button);
	}

	function userCanSave(){
		if(permissionhandler::hasPerm('ADMINISTRATOR')){
			return true;
		}
		$ownersReadOnly = $this->UsersReadOnly ? unserialize($this->UsersReadOnly) : array();
		$readers = array();
		foreach(array_keys($ownersReadOnly) as $key){
			if(isset($ownersReadOnly[$key]) && $ownersReadOnly[$key] == 1){
				$readers[] = $key;
			}
		}
		return parent::userCanSave() && !we_users_util::isUserInUsers($_SESSION['user']['ID'], $readers);
	}

	static function isUsedByObjectFile($id){
		return $id && f('SELECT 1 FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=' . intval($id) . ' LIMIT 1');
	}

	public function getDocumentCss(){
		return array();
	}

}
