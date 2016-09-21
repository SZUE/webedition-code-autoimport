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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');

/* the parent class for documents */

class we_document extends we_root{
//Extension of the document
	var $Extension = '';
//Array of possible filename extensions for the document
	var $Extensions;
	var $Published = 0;
	var $Language = '';
//If the file should only be saved in the db
	var $IsDynamic = 0;
	var $schedArr = [];
//Categories of the document
	var $Category = '';
	protected $oldCategory = '';
	var $IsSearchable = 0;
	var $InGlossar = 0;
//var $NavigationItems = '';
	private $DocStream = '';
	public $parseFile = 1;
// persistent in we_object*, since saved in class
// temporary in document, holds Paths to stylesheets from we:css-tags that are user by tinyMCE
	var $CSS = [];
	/* this array is used, to store document specific data for a page */
	protected $editorSaves = [];
	public $versionsModel; // FIXME: set protected and make getter

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Extension', 'IsDynamic', 'Published', 'Category', 'IsSearchable', 'InGlossar', 'Language', 'schedArr', 'parseFile', 'editorSaves');
		$this->Table = FILE_TABLE;
		if(isWE() || defined('WE_SIDEBAR')){
			$this->InWebEdition = true;
		}
	}

	function copyDoc($id){
		if($id){
			$tmp = $this->ClassName;
			$doc = new $tmp();
			$doc->InitByID($id, $this->Table);
			$parentIDMerk = $doc->ParentID;
			if($this->ID == 0){
				foreach($this->persistent_slots as $name){
					if($name != 'elements' && in_array($name, array_keys(get_object_vars($doc)))){
						$this->{$name} = $doc->{$name};
					}
				}
				$this->Published = 0;
				if(isset($doc->Category)){
					$this->Category = $doc->Category;
				}
				$this->CreationDate = time();
				$this->CreatorID = (isset($_SESSION['user']) ? $_SESSION['user']['ID'] : 0);

				$this->ID = 0;
				$this->OldPath = '';
				$this->Filename .= '_copy';
				$this->Text = $this->Filename . $this->Extension;
				$this->setParentID($parentIDMerk);
				$this->Path = $this->ParentPath . $this->Text;
				$this->OldPath = $this->Path;
			}
			$this->elements = $doc->elements;
			foreach($this->elements as $n => $e){
				$this->elements[$n]['cid'] = 0;
			}
			$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
			$this->InWebEdition = true;
			if(isset($this->documentCustomerFilter)){
				$this->documentCustomerFilter = $doc->documentCustomerFilter;
			}
		}
	}

	/** gets the filesize of the document */
	function getFilesize(){
		return strlen($this->getElement('data'));
	}

// returns the whole document Alias - don't remove
	function getDocument($we_editmode = 0, $baseHref = 0, $we_transaction = ''){
		return $this->i_getDocument();
	}

	private function initLanguageFromParent(){
		$ParentID = $this->ParentID;
		$i = 0;
		while(!$this->Language){
			if($ParentID == 0 || $i > 20){
				$this->Language = self::getDefaultLanguage()? : 'de_DE';
			} else {
				$this->DB_WE->query('SELECT Language,ParentID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($ParentID));

				while($this->DB_WE->next_record()){
					$ParentID = $this->DB_WE->f('ParentID');
					$this->Language = $this->DB_WE->f('Language');
				}
			}
			$i++;
		}
	}

	/*
	 * Form Functions
	 */

	function formInGlossar(){
		return (we_base_moduleInfo::isActive(we_base_moduleInfo::GLOSSARY) ?
				we_html_forms::checkboxWithHidden((bool) $this->InGlossar, 'we_' . $this->Name . '_InGlossar', g_l('weClass', '[InGlossar]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);') :
				'');
	}

	function formIsSearchable(){
		return we_html_forms::checkboxWithHidden((bool) $this->IsSearchable, 'we_' . $this->Name . '_IsSearchable', g_l('weClass', '[IsSearchable]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

	protected function formExtension2(){
		$doctype = isset($this->DocType) ? $this->DocType : '';

		if(!$this->ID && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'load_editor' && $doctype == ''){ //	Neues Dokument oder Dokument ohne DocType
			switch($this->ContentType){
				case we_base_ContentTypes::HTML: //	is HTML-File
					$selected = DEFAULT_HTML_EXT;
					break;
				case we_base_ContentTypes::WEDOCUMENT: //	webEdition Document
					$selected = ($this->IsDynamic == 1 ? DEFAULT_DYNAMIC_EXT : DEFAULT_STATIC_EXT);
					break;
				default: //	no webEdition Document
					$selected = $this->Extension;
					break;
			}
		} else { //	bestehendes Dokument oder Dokument mit DocType
			$selected = $this->Extension;
		}
		return $this->Extensions ?
			we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup('we_' . $this->Name . '_Extension', $selected, $this->Extensions, 100, 'onselect="_EditorFrame.setEditorIsHot(true);"', permissionhandler::hasPerm('EDIT_DOCEXTENSION')), g_l('weClass', '[extension]')) :
			we_html_element::htmlHidden('we_' . $this->Name . '_Extension', $selected);
	}

	function formMetaInfos(){
		return '
<table class="default">
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Title') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Description') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Keywords') . '</td></tr>
</table>' .
			($this->ContentType == we_base_ContentTypes::IMAGE ? $this->formCharset(true) : '');
	}

	function formCategory(){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('delete_all_cats')", true, 0, 0, '', '', $this->Category ? false : true);
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category',-1,'" . CATEGORY_TABLE . "','','','opener.setScrollTo();opener.top.we_cmd(\\'add_cat\\',top.fileSelect.data.allIDs.join(\\',\\'));')");
		$cats = new we_chooser_multiDir(508, $this->Category, 'delete_cat', $delallbut . $addbut, '', '"we/category"', CATEGORY_TABLE);
		$cats->extraDelFn = 'setScrollTo();';
		return $cats->get();
	}

	function formNavigation(){
		$isSee = $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE;
		$navItems = $this->getNavigationItems();
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('module_navigation_edit_navi',0)", true, 100, 22, '', '', (permissionhandler::hasPerm('EDIT_NAVIGATION') && $this->ID && $this->Published) ? false : true, false);

		if(permissionhandler::hasPerm('EDIT_NAVIGATION') && $isSee){
			$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:if(confirm('" . g_l('navigation', '[dellall_question]') . "')) we_cmd('delete_all_navi')", true, 0, 0, '', '', (permissionhandler::hasPerm('EDIT_NAVIGATION') && $navItems) ? false : true);
		} else {
			$delallbut = '';
		}
		$navis = new we_chooser_multiFile(508, $navItems, 'delete_navi', $delallbut . $addbut, 'module_navigation_edit_navi', 'folder', 'we/navigation');
		$navis->extraDelFn = 'setScrollTo();';
		$NoDelNavis = $navItems;
		foreach($NoDelNavis as $path){
			$id = path_to_id($path, NAVIGATION_TABLE, $GLOBALS['DB_WE']);
			$naviItem = new we_navigation_navigation($id);
			if(!$naviItem->hasAnyChilds()){
				if(($pos = array_search($path, $NoDelNavis)) === false){
					continue;
				}
				unset($NoDelNavis[$pos]);
			}
		}
		$navis->setDisabledDelItems($NoDelNavis, g_l('navigation', '[NoDeleteFromDocument]'));

		if(!$isSee || !permissionhandler::hasPerm('EDIT_NAVIGATION')){
			$navis->isEditable = false;
			$navis->CanDelete = false;
		}

		return $navis->get();
	}

	function addCat(array $ids){
		$this->Category = implode(',', array_unique(array_merge(array_filter(explode(',', $this->Category)), $ids), SORT_NUMERIC));
	}

	function delCat($id){
		$cats = array_filter(explode(',', $this->Category));
		if(($pos = array_search($id, $cats, false)) === false){
			return;
		}

		unset($cats[$pos]);
		$this->Category = implode(',', $cats);
	}

	function addNavi($id, $text, $parentid, $ordn){
		$text = urldecode($text); //Bug #3769
		if($this->ID){
			if(is_numeric($ordn)){
				$ordn--;
			}
			$ord = ($ordn === 'end' ? -1 : (is_numeric($ordn) && $ordn > 0 ? $ordn : 0));

			$new_path = rtrim(id_to_path($parentid, NAVIGATION_TABLE), '/') . '/' . $text;
			$id = $id? : path_to_id($new_path, NAVIGATION_TABLE, $GLOBALS['DB_WE']);

			$naviItem = new we_navigation_navigation($id);

			$naviItem->Ordn = f('SELECT MAX(Ordn) FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($parentid));
			$naviItem->ParentID = $parentid;
			$naviItem->LinkID = $this->ID;
			$naviItem->Text = $text;
			$naviItem->Path = $new_path;
			if(NAVIGATION_ENTRIES_FROM_DOCUMENT){
				$naviItem->Selection = we_navigation_navigation::SELECTION_STATIC;
				$naviItem->SelectionType = we_navigation_navigation::STYPE_DOCLINK;
			} else {
				$naviItem->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$naviItem->SelectionType = we_navigation_navigation::STYPE_DOCTYPE;
				$naviItem->IsFolder = 1;
				$charset = $naviItem->findCharset($naviItem->ParentID);
				$naviItem->Charset = ($charset ? : (DEFAULT_CHARSET ? : $GLOBALS['WE_BACKENDCHARSET']));
			}

			$naviItem->save();
			$naviItem->reorderAbs($ord);
		}
	}

	function delNavi($path){
		$path = urldecode($path); //Bug #3816
		$navis = $this->getNavigationItems();
		if(($pos = array_search($path, $navis)) === false){
			return;
		}
		$id = path_to_id($path, NAVIGATION_TABLE, $GLOBALS['DB_WE']);
		$naviItem = new we_navigation_navigation($id);
		if(!$naviItem->hasAnyChilds()){
			$naviItem->delete();
			unset($navis[$pos]);
		}
	}

	function delAllNavi(){
		$navis = $this->getNavigationItems();
		foreach($navis as $path){
			$id = path_to_id($path, NAVIGATION_TABLE, $GLOBALS['DB_WE']);
			$naviItem = new we_navigation_navigation($id);
			if(!$naviItem->hasAnyChilds()){
				$naviItem->delete();
				if(($pos = array_search($path, $navis)) === false){
					continue;
				}
				unset($navis[$pos]);
			}
		}
	}

	/*
	 * internal functions
	 */

	protected function getParentIDFromParentPath(){
		$f = new we_folder();
		return ($f->initByPath($this->ParentPath) ? $f->ID : -1);
	}

	function addEntryToList($name, $number = 1){
		$list = $this->getElement($name);

		$listarray = we_unserialize($list);

		for($f = 0; $f < $number; $f++){
			$content = $this->getElement($name, 'content');
			$new_nr = $this->getMaxListArrayNr($listarray) + 1;

// clear value
			$names = $this->getNamesFromContent($content);

			foreach($names as $curname){
				$this->setElement($curname . '_' . $new_nr, '');
			}

			$listarray[] = '_' . $new_nr;
		}
		$this->setElement($name, we_serialize(array_values($listarray), SERIALIZE_JSON, true, 0, true), 'block');
	}

	function getMaxListArrayNr(array $la){
		$maxnr = 0;
		foreach($la as $val){
			$nr = intval(str_replace('_', '', $val));
			$maxnr = max($maxnr, $nr);
		}
		return $maxnr;
	}

	function insertEntryAtList($name, $nr, $number = 1){
		$listarray = we_unserialize($this->getElement($name));

		for($f = 0; $f < $number; $f++){
			$content = $this->getElement($name, 'content');
			$new_nr = $this->getMaxListArrayNr($listarray) + 1;
// clear value
			$names = $this->getNamesFromContent($content);
			foreach($names as $cur){
				$this->setElement($cur . '_' . $new_nr, '');
			}

			for($i = count($listarray); $i > $nr; $i--){
				$listarray[$i] = $listarray[$i - 1];
			}

			$listarray[$nr] = '_' . $new_nr;
		}

		$this->setElement($name, we_serialize($listarray, SERIALIZE_JSON, true, 0, true), 'block');
	}

	function upEntryAtList($name, $nr, $number = 1){
		$list = $this->getElement($name);
		if(!$list){
			t_e('failed');
			return;
		}
		$listarray = we_unserialize($list);
		$newPos = max($nr - $number, 0);
		$temp = $listarray[$newPos];
		$listarray[$newPos] = $listarray[$nr];
		$listarray[$nr] = $temp;

		$this->setElement($name, we_serialize($listarray, SERIALIZE_JSON, true, 0, true), 'block');
	}

	function downEntryAtList($name, $nr, $number = 1){
		$list = $this->getElement($name);
		if(!$list){
			return;
		}
		$listarray = we_unserialize($list);
		$newPos = min($nr + $number, count($listarray) - 1);
		$temp = $listarray[$newPos];
		$listarray[$newPos] = $listarray[$nr];
		$listarray[$nr] = $temp;
		$this->setElement($name, we_serialize($listarray, SERIALIZE_JSON, true, 0, true), 'block');
	}

	function removeEntryFromList($name, $nr, $names){
		$list = $this->getElement($name);
		$listarray = we_unserialize($list);
		if(is_array($listarray)){
			foreach(array_keys($this->elements) as $key){
				if(preg_match('/' . $names . '(__.*)*$/', $key)){// # Bug 6904
					unset($this->elements[$key]);
				}
			}
			unset($listarray[$nr]);
		} else {
			$listarray = [];
		}
		$this->setElement($name, we_serialize($listarray, SERIALIZE_JSON, true, 0, true), 'block');
	}

	function addLinkToLinklist($name){
		$ll = new we_base_linklist(we_unserialize($this->getElement($name)));
		$ll->addLink();
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function upEntryAtLinklist($name, $nr){
		$ll = new we_base_linklist(we_unserialize($this->getElement($name)));
		$ll->upLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function downEntryAtLinklist($name, $nr){
		$ll = new we_base_linklist(we_unserialize($this->getElement($name)));
		$ll->downLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function insertLinkAtLinklist($name, $nr){
		$ll = new we_base_linklist(we_unserialize($this->getElement($name)));
		$ll->insertLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function removeLinkFromLinklist($name, $nr, $names = ''){
		$ll = new we_base_linklist(we_unserialize($this->getElement($name)));
		$ll->removeLink($nr, $names, $name);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function changeLink($name){//FIXME: can we store info in bdid? add info on type, if it is object or file?
		if(!isset($_SESSION['weS']['WE_LINK'])){
			return;
		}
		$this->setElement($name, we_serialize($_SESSION['weS']['WE_LINK'], SERIALIZE_JSON), 'link');
		unset($_SESSION['weS']['WE_LINK']);
	}

	function changeLinklist($name){
		if(!isset($_SESSION['weS']['WE_LINKLIST'])){
			return;
		}
		$this->setElement($name, $_SESSION['weS']['WE_LINKLIST'], 'linklist');
		unset($_SESSION['weS']['WE_LINKLIST']);
	}

	function getNamesFromContent($content){
		$arr = $result = [];
		preg_match_all('/< ?we:[^>]+name="([^"]+)"[^>]*>/i', $content, $result, PREG_SET_ORDER);
		foreach($result as $val){
			$arr[] = $val[1];
		}
		return $arr;
	}

	function remove_image($name){
		unset($this->elements[$name]);
		unset($this->elements[$name . we_imageDocument::ALT_FIELD]);
		unset($this->elements[$name . we_imageDocument::TITLE_FIELD]);
	}

	/*
	 * public
	 */

	public function we_new(){
		parent::we_new();
		$this->i_setExtensions();
		if(is_array($this->Extensions) && $this->Extensions){
			$this->Extension = $this->Extensions[0];
		}
		switch($this->Table){
			case FILE_TABLE:
			case TEMPLATES_TABLE:
				if(!isset($GLOBALS['WE_IS_DYN'])){
					if(!$this->ParentID && ($ws = get_ws($this->Table, true))){
						$this->setParentID(intval(reset($ws)));
					}
				}
		}
	}

	private function i_setExtensions(){
		if($this->ContentType){
			$exts = we_base_ContentTypes::inst()->getExtension($this->ContentType);
			$this->Extensions = is_array($exts) && $exts ? $exts : [];
			$this->Extension = $this->Extension? : (!is_array($exts) ? $exts : '');
		}
	}

	private function isVersioned(){
		switch($this->ContentType){
			case we_base_ContentTypes::AUDIO:
				return VERSIONING_AUDIO;
			case we_base_ContentTypes::VIDEO:
				return VERSIONING_VIDEO;
			case we_base_ContentTypes::FLASH://FIXME: remove flash, replace by video
				return VERSIONING_FLASH;
			case we_base_ContentTypes::IMAGE:
				return VERSIONING_IMAGE;
			case we_base_ContentTypes::TEMPLATE:
				return VERSIONING_TEXT_WETMPL;
			case we_base_ContentTypes::JS:
				return VERSIONING_TEXT_JS;
			case we_base_ContentTypes::CSS:
				return VERSIONING_TEXT_CSS;
			case we_base_ContentTypes::TEXT:
				return VERSIONING_TEXT_PLAIN;
			case we_base_ContentTypes::XML:
				return VERSIONING_TEXT_XML;
			default:
			case we_base_ContentTypes::APPLICATION:
				return VERSIONING_SONSTIGE;
		}
	}

	public function we_save($resave = false, $skipHook = false){
		$this->errMsg = '';
		$this->i_setText();

		if(!$skipHook){
			$hook = new weHook('preSave', '', [$this, 'resave' => $resave]);
//check if doc should be saved
			if($hook->executeHook() === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		if(!parent::we_save($resave)){
			return false;
		}

		$ret = $this->i_writeDocument();
		if(!$ret || ($this->errMsg != '')){
			return false;
		}

		$this->OldPath = $this->Path;

		if(!$resave){ // NO rebuild!
			$this->resaveWeDocumentCustomerFilter();
		}

		if($this->isVersioned()){
			$version = new we_versions_version();
			$version->save($this);
		}

		/* hook */
		if(!$skipHook){
			$hook = new weHook('save', '', [$this, 'resave' => $resave]);
//check if doc should be saved
			if($hook->executeHook() === false){
				$this->errMsg = $hook->getErrorString();
				return false;
			}
		}

		return $ret;
	}

	protected function i_writeMetaValues(){
		foreach($this->DB_WE->getAllq('SELECT tag,type,importFrom,mode,csv FROM ' . METADATA_TABLE) as $meta){
			if($meta['mode'] === 'auto' && $meta['type'] === 'textfield' && ($value = $this->getElement($meta['tag']))){
				$values = $meta['csv'] ? array_map('trim', explode(',', $value)) : [$value];
				foreach($values as $v){
					$this->DB_WE->query('INSERT INTO ' . METAVALUES_TABLE . ' SET ' . we_database_base::arraySetter(['tag' => $meta['tag'],
							'value' => $v
					]));
				}
			}
		}
	}

	function resaveWeDocumentCustomerFilter(){
		if(!empty($this->documentCustomerFilter)){
			we_customer_documentFilter::saveForModel($this);
		}
	}

	function registerMediaLinks($temp = false, $linksReady = false){
		if(!$linksReady){
			switch($this->ContentType){
				case we_base_ContentTypes::CSS:
				case we_base_ContentTypes::JS:
					$this->replaceWEIDs('', true);
					return parent::registerMediaLinks(true);
				case we_base_ContentTypes::WEDOCUMENT:
				case we_base_ContentTypes::OBJECT_FILE:
					if(!$linksReady){//FIXME: maybe move this part do we_webEditionDocument
						$c = 0;
						foreach($this->elements as $k => $v){
							$element = $v['type'] . '[name=' . ($k ? : 'NN' . ++$c) . ']';
							switch(isset($v['type']) ? $v['type'] : ''){
								case 'audio':
								case 'binary':
								case 'flashmovie':
								case 'href':
								case 'img':
								case 'video':
									if(!empty($v['bdid']) && is_numeric($v['bdid'])){
										$this->MediaLinks[$element] = $v['bdid'];
									} elseif(!empty($v['dat']) && is_numeric($v['dat'])){
										$this->MediaLinks[$element] = $v['dat'];
									}
									break;
								case 'link':
									/*
									 * documents: when no link is set but there is a default id in template $v['dat'] is serialized twice:
									 * we do not register such links, they belong to the template and are they are not stored in tblContent!
									 *
									 * objectfiles: here the default defined in class is stored in tblObject_X:
									 * it is no "dynamic" default and belongs to the object: so we register it as medialink of the object (and class)
									 */
									if(isset($v['dat']) && ($link = we_unserialize($v['dat'], [], true)) && is_array($link)){
										if(isset($link['type']) && isset($link['id']) && isset($link['img_id'])){
											if($link['type'] === 'int' && $link['id']){
												$this->MediaLinks[$element] = $link['id'];
											}
											if($link['img_id']){
												$this->MediaLinks[$element] = $link['img_id'];
											}
										}
									}
									break;
								default:
									if(!empty($v['bdid'])){
										$this->MediaLinks[$element] = $v['bdid'];
									}
							}
						}
					}
					break;
				default:
				//
			}
		}

		return parent::registerMediaLinks($temp);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		// Navigation items
		$this->i_setExtensions();
	}

	/**
	 * inits weDocumentCustomerFilter from db regarding the modelId
	 * is called from "we_textContentDocument::we_load"
	 * @see we_textContentDocument::we_load
	 */
	protected function initWeDocumentCustomerFilterFromDB(){
		$this->documentCustomerFilter = we_customer_documentFilter::getFilterOfDocument($this);
	}

// reverse function to saveInSession !
	public function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		/* this is bad old code
		 *
		 * if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
		  if(
		  ($day = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_day')) && ($month = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_month')) && ($year = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_year')) && ($hour = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_hour')) !== false && ($min = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_minute')) !== false){
		  $this->From = mktime($hour, $min, 0, $month, $day, $year);
		  }
		  if(
		  ($day = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_day')) && ($month = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_month')) && ($year = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_year')) && ($hour = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_hour')) !== false && ($min = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_minute')) !== false){
		  $this->To = mktime($hour, $min, 0, $month, $day, $year);
		  }
		  } */

		if(we_base_request::_(we_base_request::INT, 'wecf_mode') !== false){
			$this->documentCustomerFilter = we_customer_documentFilter::getCustomerFilterFromRequest($this->ID, $this->ContentType, $this->Table);
		} else if(isset($sessDat[3])){ // init webUser from session - unserialize is only needed for old temporary docs
			$this->documentCustomerFilter = we_unserialize($sessDat[3]);
		}

		$this->i_setExtensions();

		if(!($this->Language) && $this->Table != TEMPLATES_TABLE){
			$this->initLanguageFromParent();
		}

		if(!is_object($this->versionsModel)){
			$this->versionsModel = new we_versions_model($GLOBALS["we_transaction"]);
		}
		$this->versionsModel->initByHttp();
	}

	function we_rewrite(){
		$this->RebuildDate = time();
		return $this->i_writeDocument();
	}

	/*
	 * private
	 */

	protected function i_setText(){
		$this->Text = $this->Filename . $this->Extension;
	}

	protected function i_writeSiteDir($doc){
		if($this->isMoved()){
			we_base_file::deleteLocalFile($this->getSitePath(true));
		}
		return we_base_file::checkAndMakeFolder(dirname($this->getSitePath()), true) && we_base_file::save($this->getSitePath(), $doc);
	}

	protected function i_writeMainDir($doc){
		if($this->isMoved()){
			we_base_file::deleteLocalFile($this->getRealPath(true));
		}
		return we_base_file::save($this->getRealPath(), $doc);
	}

	protected function i_writeDocument(){
		$update = $this->isMoved();
		$doc = $this->i_getDocumentToSave();
		if(!$this->i_writeSiteDir($doc) || !$this->i_writeMainDir($doc)){
			return false;
		}

		if($update){
			$this->rewriteNavigation();
		}
		$this->update_filehash();
		return true;
	}

	protected function i_getDocumentToSave(){
		$this->DocStream = $this->DocStream ? : $this->i_getDocument();
		return $this->DocStream;
	}

	function i_getDocument($includepath = ''){
		return $this->getElement('data');
	}

	function i_setDocument($value){
		$this->setElement('data', $value);
	}

	protected function i_filenameDouble(){
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->ParentID) . ' AND Filename="' . escape_sql_query($this->Filename) . '" AND Extension="' . escape_sql_query($this->Extension) . '" AND ID!=' . intval($this->ID), "", $this->DB_WE);
	}

	public static function getFieldLink($val, we_database_base $db, array $attribs = [], $pathOnly = false, $parentID = 0, $path = ''){
		$link = we_unserialize($val);

		$only = weTag_getAttribute('only', $attribs, '', we_base_request::STRING);

		$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
		$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, we_base_request::BOOL);

		if($pathOnly || $only === 'href'){
			$return = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls);

			if(!empty($GLOBALS['we_link_not_published'])){
				unset($GLOBALS['we_link_not_published']);
				return '';
			}
			return $return;
		}

		if(is_array($link)){
			$img = new we_imageDocument();
//	set name of image for rollover ...

			if(isset($attribs['name'])){ //	here we must change the name for a rollover-image
				$useName = $attribs['name'] . '_img';
				$img->setElement('name', $useName, 'dat');
			} else {
				$useName = '';
			}

			$xml = weTag_getAttribute('xml', $attribs, (XHTML_DEFAULT), we_base_request::BOOL);
			$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, true, we_base_request::BOOL);
			if($only){
				return ($only === 'content' ?
						self::getLinkContent($link, $parentID, $path, $db, $img, $xml, $useName, $oldHtmlspecialchars, $hidedirindex, $objectseourls) :
						isset($link[$only]) ? $link[$only] : ''); // #3636
			}

			if(($content = self::getLinkContent($link, $parentID, $path, $db, $img, $xml, $useName, $oldHtmlspecialchars, $hidedirindex, $objectseourls))){
				if(($startTag = self::getLinkStartTag($link, $attribs, $parentID, $path, $db, $img, $useName, $hidedirindex, $objectseourls))){
					return $startTag . $content . '</a>';
				}
				return $content;
			}
		}
		return '';
	}

//FIXME: parameter $attrib should be: array $attribs=array()
//FIXME: check if we can rid of this function, since it causes problems every change of tags since it also uses the given attribs array!
	public function getFieldByVal($val, $type, $attribs = '', $pathOnly = false, $parentID = 0, $path = '', we_database_base $db = null, $classID = '', $fn = 'this'){
		$attribs = is_array($attribs) ? $attribs : [];
		if(isset($attribs['_name_orig'])){
			unset($attribs['_name_orig']);
		}
		$db = ($db ? : new DB_WE());
		if((!$attribs) || (!is_array($attribs))){
			$attribs = [];
		}
		switch($type){
			case 'img':
				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}

				$img = new we_imageDocument(false);

				if(isset($attribs['name'])){
					$img->Name = $attribs['name'];
				}

				$img->initByID($val, FILE_TABLE);

				$altField = $img->Name . we_imageDocument::ALT_FIELD;
				$titleField = $img->Name . we_imageDocument::TITLE_FIELD;

				if(isset($GLOBALS['lv'])){
					switch(get_class($GLOBALS['lv'])){
						case 'we_listview_variants':
							$altField = (we_base_constants::WE_VARIANTS_PREFIX . $GLOBALS['lv']->Position . '_' . $altField);
							$titleField = (we_base_constants::WE_VARIANTS_PREFIX . $GLOBALS['lv']->Position . '_' . $titleField);
							break;
						case 'we_listview_document':
							$alt = $GLOBALS['lv']->f($altField) ? : '';
							$title = $GLOBALS['lv']->f($titleField) ? : '';
						case 'we_listview_object':
						case 'we_listview_multiobject':
							$attribs['alt'] = !empty($alt) ? $alt : ($img->getElement('alt') ? : (isset($attribs['alt']) ? $attribs['alt'] : ''));
							$attribs['title'] = !empty($title) ? $title : ($img->getElement('title') ? : (isset($attribs['title']) ? $attribs['title'] : ''));
							break;
					}
				}
				if(isset($attribs['alt'])){
					$attribs['alt'] = oldHtmlspecialchars($attribs['alt']);
				}
				if(isset($attribs['title'])){
					$attribs['title'] = oldHtmlspecialchars($attribs['title']);
				}
				if(!(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) === 'reload_editpage' && ($img->Name === we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1)) && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) === 'change_image') && isset($GLOBALS['we_doc']->elements[$altField])){
					if(!isset($GLOBALS['lv'])){
						$attribs['alt'] = oldHtmlspecialchars($GLOBALS['we_doc']->getElement($altField));
						$attribs['title'] = oldHtmlspecialchars($GLOBALS['we_doc']->getElement($titleField));
					}
				}

//	when width or height are given, then let the browser adjust the image
				if(isset($attribs['width'])){
					unset($img->elements['width']);
				}
				if(isset($attribs['height'])){
					unset($img->elements['height']);
				}
				if($attribs){
					$img->initByAttribs(removeAttribs($attribs, ['hyperlink', 'target']));
				}
				if(isset($GLOBALS['lv'])){
					if(isset($GLOBALS['lv']->count)){
						$img->setElement('name', $img->getElement('name') . '_' . $GLOBALS['lv']->count, 'attrib');
						$img->Name = $img->Name . '_' . $GLOBALS['lv']->count;
					} else {
						$img->setElement('name', $img->getElement('name'), 'attrib');
					}
				}

				switch($pathOnly ? 'path' : (isset($attribs['only']) ? $attribs['only'] : '')){
					case 'src': //TODO: make separate case for multi domain project to devide between path and src
					case 'path':
						return (empty($attribs['thumbnail']) ? $img->Path : $img->getHtml(false, true, true) );
					case 'id':
						return $img->ID;
					case 'parentpath':
						return $img->ParentPath;
					case 'filename':
						return $img->Filename;
					case 'extension':
						return $img->Extension;
					case 'filesize':
						return $img->getFilesize();
				}

				return $img->getHtml(false, true);
			case 'binary':
				$bin = new we_otherDocument();
				if(isset($attribs['name'])){
					$bin->Name = $attribs['name'];
				}
				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}
				$bin->initByID($val, FILE_TABLE);
				return [$bin->Text, $bin->Path, $bin->ParentPath, $bin->Filename, $bin->Extension, $bin->getFilesize()];
			case 'video':
				$video = new we_document_video();
				if(isset($attribs['name'])){
					$video->Name = $attribs['name'];
				}
				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}
				$video->initByID($val, FILE_TABLE);
				if(!empty($attribs)){
					$video->initByAttribs($attribs);
				}
				return $pathOnly ? $video->Path : $video->getHtml(false, $GLOBALS['we_editmode']);
			case 'flashmovie':
				$fl = new we_flashDocument();
				if(isset($attribs['name'])){
					$fl->Name = $attribs['name'];
				}
				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}
				$fl->initByID($val, FILE_TABLE);
				if(!empty($attribs)){
					$fl->initByAttribs($attribs);
				}
				return $pathOnly ? $fl->Path : $fl->getHtml();
			case 'link':
				return self::getFieldLink($val, $db, $attribs, $pathOnly, $parentID, $path);
			case 'date':
				$val = $val ? : time();
				$format = !empty($attribs['format']) ? $attribs['format'] : g_l('date', '[format][default]');
				$langcode = (isset($GLOBALS['WE_MAIN_DOC']) && strlen($GLOBALS['WE_MAIN_DOC']->Language) == 5 ? $GLOBALS['WE_MAIN_DOC']->Language : $GLOBALS['weDefaultFrontendLanguage']);

				$date = (is_numeric($val) ? new DateTime('@' . $val) : new DateTime($val));
				//we need to set it explicitly
				$date->setTimezone(new DateTimeZone(date_default_timezone_get()));

				return CheckAndConvertISOfrontend(we_base_country::dateformat($langcode, $date, $format));

			case 'select':
				if(defined('OBJECT_TABLE')){
					if(strlen($val) == 0){
						return '';
					}
					if($classID){
						$defVals = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classID), '', $db);
						if($defVals){
							$arr = we_unserialize($defVals);
							return isset($arr['meta_' . $attribs['name']]['meta'][$val]) ? $arr['meta_' . $attribs['name']]['meta'][$val] : '';
						}
					}
				}
				$f = __FUNCTION__;
				return $this->{$f}($val, 'text', $attribs, $pathOnly, $parentID, $path, $db, $classID, $fn);
			case 'href':
				return $this->getHref($attribs, $db, $fn);
			default:
				self::parseInternalLinks($val, $parentID);
				$retval = preg_replace('/<\?xml[^>]+>/i', '', $val);

				if(!weTag_getAttribute('html', $attribs, true, we_base_request::BOOL) && !weTag_getAttribute('wysiwyg', $attribs, false, we_base_request::BOOL)){
					$retval = strip_tags($retval, '<br/>,<p>');
				}

				$htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL);
				$wysiwyg = weTag_getAttribute('wysiwyg', $attribs, false, we_base_request::BOOL);

				if($htmlspecialchars && (!$wysiwyg)){
					$retval = preg_replace('/#we##br([^#]*)#we##/', '<br${1}>', oldHtmlspecialchars(preg_replace('/<br([^>]*)>/i', '#we##br${1}#we##', $retval), ENT_QUOTES));
				}
				if(!weTag_getAttribute('php', $attribs, (defined('WE_PHP_DEFAULT') && WE_PHP_DEFAULT), we_base_request::BOOL)){
					$retval = we_base_util::rmPhp($retval);
				}
				$xml = weTag_getAttribute('xml', $attribs, (XHTML_DEFAULT), we_base_request::BOOL);
				$retval = preg_replace('-<(br|hr)([^/>]*)/? *>-i', ($xml ? '<${1}${2}/>' : '<${1}${2}>'), $retval);

				if(preg_match('/^[\d.,]+$/', trim($retval))){
					$precision = isset($attribs['precision']) ? abs($attribs['precision']) : 2;
					if(($num = weTag_getAttribute('num_format', $attribs, '', we_base_request::STRING))){
						$retval = we_base_util::formatNumber(we_base_util::std_numberformat($retval), $num, $precision);
					}
				}
				if(weTag_getAttribute('win2iso', $attribs, false, we_base_request::BOOL)){

					$charset = ( isset($GLOBALS['WE_MAIN_DOC']) && isset($GLOBALS['WE_MAIN_DOC']->elements['Charset']['dat'])) ? $GLOBALS['WE_MAIN_DOC']->elements['Charset']['dat'] : '';
					if(trim(strtolower(substr($charset, 0, 3))) === 'iso' || $charset === ''){
						$retval = strtr($retval, [chr(128) => '&#8364;',
							chr(130) => '&#8218;',
							chr(131) => '&#402;',
							chr(132) => '&#8222;',
							chr(133) => '&#8230;',
							chr(134) => '&#8224;',
							chr(135) => '&#8225;',
							chr(136) => '&#710;',
							chr(137) => '&#8240;',
							chr(138) => '&#352;',
							chr(139) => '&#8249;',
							chr(140) => '&#338;',
							chr(142) => '&#381;',
							chr(145) => '&#8216;',
							chr(146) => '&#8217;',
							chr(147) => '&#8220;',
							chr(148) => '&#8221;',
							chr(149) => '&#8226;',
							chr(150) => '&#8211;',
							chr(151) => '&#8212;',
							chr(152) => '&#732;',
							chr(153) => '&#8482;',
							chr(154) => '&#353;',
							chr(155) => '&#8250;',
							chr(156) => '&#339;',
							chr(158) => '&#382;',
							chr(159) => '&#376;']);
					}
				}
				return str_replace(["##|n##", "##|r##"], ["\n", "\r"], $retval);
		}
	}

	function getField($attribs, $type = 'txt', $pathOnly = false){
		if(is_array($attribs) && isset($attribs['_name_orig'])){
			unset($attribs['_name_orig']);
		}

		$val = '';
		switch($type){
			case 'img':
			case 'flashmovie':
			case 'video':
				if(isset($attribs['showcontrol']) && !$attribs['showcontrol'] && isset($attribs['id']) && $attribs['id']){//bug 6433: siehe korrespondierende Änderung in we_tag_img
					unset($attribs['showcontrol']);
					$val = $attribs['id'];
					break;
				}
				$val = $this->getElement($attribs['name']);
				break;
			case 'href':
				if(!isset($attribs['name'])){
					return;
				}
				$val = $this->getElement($attribs['name']);
				if(isset($this->TableID) || (is_string($val) && $val && ($val{0} == 'a' || $val{0} == '{'))){// we can not use '$this instanceof we_objectFile' to identify objectFile, we have to use 'isset($this->TableID)' instead
					return self::getHrefByArray(we_unserialize($val));
				}
				break;
			default:
//check bdid first
				$val = $this->getElement($attribs['name']);
		}

		return $this->getFieldByVal($val, $type, $attribs, $pathOnly, isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC']->ParentID : $this->ParentID, isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC']->Path : $this->Path, $this->DB_WE, (isset($attribs['classid']) && isset($attribs['type']) && $attribs['type'] === 'select') ? $attribs['classid'] : (isset($this->TableID) ? $this->TableID : '')); //not instance due to we_showObject
	}

	private function getValFromSrc($fn, $name, $key = 'dat'){
		switch($fn){
			default:
			case 'this':
				return $this->getElement($name, $key);
			case 'listview':
				return $GLOBALS['lv']->f($name);
		}
	}

	function getHref($attribs, we_database_base $db = null, $fn = 'this'){
		$db = $db ? : new_DB_WE();
		$n = $attribs['name'];
		if($this->getValFromSrc($fn, $n . we_base_link::MAGIC_INT_LINK, 'bdid')){
			$intID = $this->getValFromSrc($fn, $n . we_base_link::MAGIC_INT_LINK_ID, 'bdid'); //try bdid first
			$intID = $intID ? : $this->getValFromSrc($fn, $n . we_base_link::MAGIC_INT_LINK_ID);
			return f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID), '', $db);
		}
		return $this->getValFromSrc($fn, $n);
	}

	static function getHrefByArray(array $hrefArr){
		return ($hrefArr['int'] ?
				(empty($hrefArr['intID']) ? '' : id_to_path($hrefArr['intID'])) :
				(empty($hrefArr['extPath']) ? '' : $hrefArr['extPath'])
			);
	}

	function getLinkHref($link, $parentID, $path, we_database_base $db = null, $hidedirindex = false, $objectseourls = false){
		$db = ($db ? : new DB_WE());

// Bug Fix 8170&& 8166
		if(isset($link['href']) && strpos($link['href'], we_base_link::TYPE_MAIL_PREFIX) === 0){
			$link['type'] = we_base_link::TYPE_MAIL;

//added for #7269
			if(!empty($link['subject'])){
				$link['href'] = $link['href'] . "?subject=" . $link['subject'];
			}
			if(!empty($link['cc'])){
				$link['href'] = $link['href'] . "&cc=" . $link['cc'];
			}
			if(!empty($link['bcc'])){
				$link['href'] = $link['href'] . "&bcc=" . $link['bcc'];
			}
		}
		if(!isset($link['type'])){
			return '';
		}
		switch($link['type']){
			case we_base_link::TYPE_INT:
				if(empty($link['id'])){
					return '';
				}
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($link['id']), '', $db);
				$path_parts = pathinfo($path);
				if($hidedirindex && seoIndexHide($path_parts['basename'])){
					$path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
				if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition || f('SELECT Published FROM ' . FILE_TABLE . ' WHERE ID=' . intval($link['id']), '', $db)){
					return $path;
				}
				$GLOBALS['we_link_not_published'] = 1;
				return '';

			case we_base_link::TYPE_OBJ:
				return we_objectFile::getObjectHref($link['obj_id'], $parentID, $path, $db, $hidedirindex, $objectseourls);
			default:
				return ($link['href'] == we_base_link::EMPTY_EXT ? '' : $link['href']);
		}
	}

	function getLinkContent($link, $parentID = 0, $path = '', we_database_base $db = null, $img = '', $xml = '', $useName = '', $htmlspecialchars = false, $hidedirindex = false, $objectseourls = false){
		//$l_href = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls);

		if(!empty($GLOBALS['we_link_not_published'])){
			unset($GLOBALS['we_link_not_published']);
			return '';
		}

		switch(isset($link['ctype']) ? $link['ctype'] : ''){
			case we_base_link::CONTENT_INT:
				$img = ($img ? : new we_imageDocument());
				$img->initByID($link['img_id']);

				$img_attribs = ['width' => $link['width'], 'height' => $link['height'], 'border' => $link['border'], 'hspace' => $link['hspace'], 'vspace' => $link['vspace'], 'align' => $link['align'], 'alt' => $link['alt'], 'title' => (isset($link['img_title']) ? $link['img_title'] : '')];

				if($useName){ //	rollover with links ...
					$img_attribs['name'] = $useName;
					$img->elements['name']['dat'] = $useName;
				}

				if($xml){
					$img_attribs['xml'] = 'true';
				}

				$img->initByAttribs($img_attribs);

				return $img->getHtml(false, false);
			case we_base_link::CONTENT_EXT:
//  set default atts
				$img_attribs = ['src' => $link['img_src'],
					'alt' => '',
					'xml' => $xml
				];
				if(isset($link['img_title'])){
					$img_attribs['title'] = $link['img_title'];
				}
//  deal with all remaining attribs
				$img_attList = ['width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'name'];
				foreach($img_attList as $k){
					if(!empty($link[$k])){
						$img_attribs[$k] = $link[$k];
					}
				}
				return getHtmlTag('img', $img_attribs);
			case we_base_link::CONTENT_TEXT:
// Workarround => We have to find another solution
				return (($xml || $htmlspecialchars) ?
						oldHtmlspecialchars(html_entity_decode($link['text'])) : $link['text']);
		}
	}

	function getLinkStartTag($link, $attribs, $parentID = 0, $path = '', we_database_base $db = null, $img = '', $useName = '', $hidedirindex = false, $objectseourls = false){
		if(($l_href = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls))){
//    define some arrays to order the attribs to image, link or js-window ...
			$popUpAtts = ['jswin', 'jscenter', 'jswidth', 'jsheight', 'jsposx', 'jsposy', 'jsstatus', 'jsscrollbars', 'jsmenubar', 'jstoolbar', 'jsresizable', 'jslocation'];

//    attribs only for image - these are already handled
			$imgAtts = ['img_id', 'width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'img_title'];

//    these are handled separately
			$dontUse = ['img_id', 'obj_id', 'ctype', 'anchor', 'params', 'attribs', 'img_src', 'text', 'type', 'only'];

//    these are already handled dont get them in output
			$we_linkAtts = ['id'];

			$linkAttribs = [];

// define image-if necessary - handle with image-attribs
			$img = ($img ? : new we_imageDocument());

//   image attribs
			foreach($imgAtts as $att){ //  take all attribs belonging to image inside content
				$img_attribs[$att] = isset($link[$att]) ? $link[$att] : '';
			}

			$img->initByID($img_attribs['img_id']);
			$img->initByAttribs($img_attribs);


			if($link['ctype'] == we_base_link::TYPE_INT){
//	set name of image dynamically
				if($useName){ //	we must set the name of the image -> rollover
					$img->setElement('name', $useName, 'dat');
				}
				$rollOverScript = $img->getRollOverScript();
				$rollOverAttribsArr = $img->getRollOverAttribsArr();
			} else {
				$rollOverScript = '';
				$rollOverAttribsArr = [];
			}

// Link-Attribs
//   1st attribs-string from link dialog ! These are already used in content ...
			if(isset($link['attribs'])){
				$linkAttribs = array_merge(we_tag_tagParser::makeArrayFromAttribs($link['attribs']), $linkAttribs);
			}

//   2nd take all atts given in link-array - from function we_tag_link()
			foreach($link as $k => $v){ //   define all attribs - later we can remove/overwrite them
				if($v != '' && !in_array($k, $we_linkAtts) && !in_array($k, $imgAtts) && !in_array($k, $popUpAtts) && !in_array($k, $dontUse)){
					$linkAttribs[$k] = $v;
				}
			}

//   3rd we take attribs given from we:link,
			foreach($attribs as $k => $v){ //   define all attribs - later we can remove/overwrite them
				if($v != '' && !in_array($k, $imgAtts) && !in_array($k, $popUpAtts) && !in_array($k, $dontUse)){
					$linkAttribs[$k] = $v;
				}
			}

//   4th use Rollover attributes
			foreach($rollOverAttribsArr as $n => $v){
				$linkAttribs[$n] = $v;
			}
//   override the href at last important !

			$linkAdds = (isset($link['params']) ? $link['params'] : '' ) . (isset($link['anchor']) ? $link['anchor'] : '' );
			if(strpos($linkAdds, '?') === false && strpos($linkAdds, '&') !== false && strpos($linkAdds, '&') == 0){//Bug #5478
				$linkAdds = substr_replace($linkAdds, '?', 0, 1);
			}
			$linkAttribs['href'] = $l_href . str_replace('&', '&amp;', $linkAdds);

// The pop-up-window                              */
			$popUpCtrl = [];
			foreach($popUpAtts as $n){
				if(isset($link[$n])){
					$popUpCtrl[$n] = $link[$n];
				}
			}


			if(!empty($popUpCtrl['jswin'])){ //  add attribs for popUp-window
				$js = 'var we_winOpts = \'\';';
				if(!empty($popUpCtrl["jscenter"]) && !empty($popUpCtrl["jswidth"]) && !empty($popUpCtrl["jsheight"])){
					$js .= 'if (window.screen) {var w = ' . $popUpCtrl["jswidth"] . ';var h = ' . $popUpCtrl["jsheight"] . ';var screen_height = screen.availHeight - 70;var screen_width = screen.availWidth-10;var w = Math.min(screen_width,w);var h = Math.min(screen_height,h);var x = (screen_width - w) / 2;var y = (screen_height - h) / 2;we_winOpts = \'left=\'+x+\',top=\'+y;}else{we_winOpts=\'\';};';
				} else if(!empty($popUpCtrl["jsposx"]) || !empty($popUpCtrl["jsposy"])){
					if($popUpCtrl["jsposx"] != ''){
						$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'left=' . $popUpCtrl["jsposx"] . '\';';
					}
					if($popUpCtrl["jsposy"] != ''){
						$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'top=' . $popUpCtrl["jsposy"] . '\';';
					}
				}
				$js.=
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'status=' . (!empty($popUpCtrl["jsstatus"]) ? 'yes' : 'no') .
					',scrollbars=' . (!empty($popUpCtrl["jsscrollbars"]) ? 'yes' : 'no') .
					',menubar=' . (!empty($popUpCtrl["jsmenubar"]) ? 'yes' : 'no') .
					',resizable=' . (!empty($popUpCtrl["jsresizable"]) ? 'yes' : 'no') .
					',location=' . (!empty($popUpCtrl["jslocation"]) ? 'yes' : 'no') .
					',toolbar=' . (!empty($popUpCtrl["jstoolbar"]) ? 'yes' : 'no') .
					(empty($popUpCtrl["jswidth"]) ? '' : ',width=' . $popUpCtrl["jswidth"] ) .
					(empty($popUpCtrl["jsheight"]) ? '' : ',height=' . $popUpCtrl["jsheight"] ) .
					'\';';
				$foo = $js . "var we_win = window.open('','we_" . (isset($attribs['name']) ? $attribs['name'] : "") . "',we_winOpts);";

				$linkAttribs['target'] = 'we_' . (isset($attribs['name']) ? $attribs['name'] : "");
				$linkAttribs['onclick'] = $foo;
			}
			return $rollOverScript . getHtmlTag('a', removeAttribs($linkAttribs, ['hidedirindex', 'objectseourls']), '', false, true);
		}
		if(!empty($GLOBALS['we_link_not_published'])){
			unset($GLOBALS['we_link_not_published']);
		}
	}

	/*
	 * functions for scheduler pro
	 */

	function add_schedule(){
		$this->schedArr[] = we_schedpro::getEmptyEntry();
	}

	function del_schedule($nr){
		unset($this->schedArr[$nr]);
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		if($_REQUEST){
			$dates = $regs = [];
			foreach($_REQUEST as $n => $v){
				if(preg_match('/^we_schedule_([^\[]+)$/', $n, $regs)){//make this an array
					$rest = $regs[1];
					$sw = explode('_', $rest);
					$nr = end($sw);
					if(!isset($this->schedArr[$nr])){
						$this->schedArr[$nr] = [];
					}
					switch($sw[0]){
						case 'task':
							$this->schedArr[$nr]['task'] = $v;
							break;
						case 'type':
							$this->schedArr[$nr]['type'] = $v;
							break;
						case 'active':
							$this->schedArr[$nr]['active'] = $v;
							break;
						case 'doctype':
							$this->schedArr[$nr]['DoctypeID'] = $v;
							break;
						case 'doctypeAll':
							$this->schedArr[$nr]['doctypeAll'] = $v;
							break;
						case 'parentid':
							$this->schedArr[$nr]['ParentID'] = $v;
							break;
						case 'time':
							if((!isset($dates[$nr]) || !is_array($dates[$nr]))){
								$dates[$nr] = [];
							}
							$dates[$nr][$sw[1]] = $v;
							break;
						default:
							if(substr($sw[0], 0, 5) === 'month'){
								$d = intval(substr($sw[0], 5));
								$this->schedArr[$nr]['months'][$d - 1] = $v;
							} else if(substr($sw[0], 0, 3) === 'day'){
								$d = intval(substr($sw[0], 3));
								$this->schedArr[$nr]['days'][$d - 1] = $v;
							} else if(substr($sw[0], 0, 4) === 'wday'){
								$d = intval(substr($sw[0], 4));
								$this->schedArr[$nr]['weekdays'][$d - 1] = $v;
							}
					}
				}
			}
			foreach($dates as $nr => $v){
				$this->schedArr[$nr]['time'] = mktime($dates[$nr]['hour'], $dates[$nr]['minute'], 0, $dates[$nr]['month'], $dates[$nr]['day'], $dates[$nr]['year']);
			}
		}
		$this->Path = $this->getPath();
	}

	function add_schedcat($id, $nr){
		$cats = array_filter(explode(',', $this->schedArr[$nr]['CategoryIDs']));
		if(in_array($id, $cats)){
			return;
		}
		$cats[] = $id;
		$this->schedArr[$nr]['CategoryIDs'] = implode(',', $cats);
	}

	function delete_schedcat($id, $nr){
		$cats = array_filter(explode(',', $this->schedArr[$nr]['CategoryIDs']));
		if(($pos = array_search($id, $cats, false)) === false){
			return;
		}
		unset($cats[$pos]);
		$this->schedArr[$nr]['CategoryIDs'] = implode(',', $cats);
	}

// returns the next date when the document gets published
	function getNextPublishDate(){
		$times = [];
		foreach($this->schedArr as $s){
			if($s['task'] == we_schedpro::SCHEDULE_FROM && $s['active']){
				$times[] = we_schedpro::getNextTimestamp($s, time());
			}
		}
		if(!$times){
			return 0;
		}
		sort($times);
		return $times[0];
	}

	function loadSchedule(){
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			$this->DB_WE->query('SELECT * FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND ClassName="' . $this->DB_WE->escape($this->ClassName) . '"');
			if($this->DB_WE->num_rows()){
				$this->schedArr = [];
			}
			while($this->DB_WE->next_record()){
				$s = we_unserialize($this->DB_WE->f('Schedpro'));
				if(is_array($s)){
					$s['active'] = $this->DB_WE->f('Active');
					$this->schedArr[] = $s;
				}
			}
		}
	}

	protected function formMetaField($field){
		$props = we_metadata_metaData::getMetaDataField($field);
		if(empty($props) || $props['mode'] === 'none' /* || !$values */ || $props['type'] !== 'textfield'){
			$name = in_array($field, explode(',', we_metadata_metaData::STANDARD_FIELDS)) ? g_l('weClass', '[' . $field . ']') : $field;

			return $this->formInputField('txt', $field, $name, 40, 508, '', 'onchange="_EditorFrame.setEditorIsHot(true);"');
		}

		$leading = $props['csv'] ? '-- ' . g_l('buttons_global', '[add][value]') . ' -- ' : '-- ' . g_l('buttons_global', '[select][value]') . ' --';
		$values = we_metadata_metaData::getDefinedMetaValues(true, $leading, $field, $props['closed'], ($props['closed'] && $props['csv']));
		$inputName = 'we_' . $this->Name . '_txt[' . $field . ']';

		$onchange = "metaFieldSelectProposal(this, '" . $inputName . "', " . ($props['csv'] ? "true" : "false") . ");";
		$mouseover = ['onmouseover' => "this.parentNode.getElementsByClassName('meta_icons')[0].style.display='inline-block';",
			'onmouseout' => "this.parentNode.getElementsByClassName('meta_icons')[0].style.display='none';",
			//'onclick' => "this.parentNode.getElementsByClassName('meta_icons')[0].style.display='none';",
		];

		$input = we_html_tools::htmlTextInput($inputName, 23, ($this->getElement($field) ? : (isset($GLOBALS['meta'][$field]) ? $GLOBALS['meta'][$field]['default'] : '')), '', '', 'txt', 308, 0, '', false, $props['closed']);
		$sel = $this->htmlSelect('we_tmp_' . $this->Name . '_select[' . $field . ']', $values, 1, '', false, ['onchange' => $onchange], "value", 200);

		// FIXME: if we want the icons make icon-css and better js
		$csvText = g_l('metadata', '[txtIconCsv]');
		$closedText = g_l('metadata', '[txtIconClosed]');
		$autoText = g_l('metadata', '[txtIconAuto]');

		$inlineCss = 'display:inline-block;background-color:#cccccc;border:1px solid black;height:1.2em;border-radius:1em;font-weight:normal;'; // FIXME: add class
		$iconCsv = we_html_element::htmlDiv(['title' => $csvText, 'style' => $inlineCss], '&nbsp;c,s,v&nbsp;');
		$iconClosed = we_html_element::htmlDiv(['title' => $closedText, 'style' => $inlineCss], '&nbsp;<i class="fa fa-key"></i>&nbsp;');
		$iconAuto = we_html_element::htmlDiv(['title' => $autoText, 'style' => $inlineCss], '&nbsp;&nbsp;<i class="fa fa-sign-in"></i>&nbsp;&nbsp;');
		$icons = we_html_element::htmlDiv(['style' => 'display:none;', 'class' => 'meta_icons'], ($props['closed'] ? $iconClosed . '&nbsp;' : '') . ($props['csv'] ? $iconCsv . '&nbsp;' : '') . (($props['mode'] === 'auto') && !$props['closed'] ? $iconAuto : ''));

		return we_html_element::htmlDiv($mouseover, we_html_tools::htmlFormElementTable($input, (g_l('weClass', '[' . $field . ']', true)? : $field), '', '', $sel . $icons));
	}

	/**
	 * returns	a select menu within a html table. to ATTENTION this function is also used in classes object and objectFile !
	 * 			when $withHeadline is true, a table with headline is returned, default is false
	 * @return	select menue to determine charset
	 * @param	boolean
	 */
	function formCharset($withHeadline = false){
		$value = $this->getElement('Charset');

		$charsetHandler = new we_base_charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		$charsets[''] = '';
		asort($charsets);

		$name = 'Charset';

		$inputName = 'we_' . $this->Name . "_txt[$name]";


		return '<table class="default">' .
			($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[Charset]') . '</td></tr>' : '') .
			'<tr><td>' . we_html_tools::htmlTextInput($inputName, 24, $value, '', '', 'text', '14em') . '</td><td></td><td>' . $this->htmlSelect('we_tmp_' . $this->Name . '_select[' . $name . ']', $charsets, 1, $value, false, ["onblur" => "_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd('reload_editpage');", "onchange" => "_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd('reload_editpage');"], "value", 330) . '</td></tr>' .
			'</table>';
	}

	/**
	 * returns if document can have variants the function returns true otherwise
	 * false
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @param boolean $checkFields
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false){
// overwrite
		return false;
	}

	/**
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
// overwrite
		return [];
	}

	/**
	 * @desc	the function modifies document EditPageNrs set
	 */
	function checkTabs(){
		if(!$this->canHaveVariants(true)){

			$ind = array_search(we_base_constants::WE_EDITPAGE_VARIANTS, $this->EditPageNrs);
			if(!empty($ind)){
				unset($this->EditPageNrs[$ind]);
			}
		}
	}

	/**
	 * get styles for textarea or object
	 * @return type
	 */
	public function getDocumentCss(){
		return $this->CSS;
	}

	/**
	 * get styles for textarea or object
	 * @return type
	 */
	public function addDocumentCss($stylesheet){
		$this->CSS[] = $stylesheet;
	}

	protected function update_filehash(){
		switch($this->Table){
			default:
				return;
			case TEMPLATES_TABLE:
			case FILE_TABLE:
		}
		$this->wasUpdate = $this->ID > 0;
		$usepath = ($this->Table == TEMPLATES_TABLE ?
				(strpos($this->Path, '.tmpl') === false ? TEMPLATES_PATH . $this->Path : TEMPLATES_PATH . substr_replace($this->Path, '.php', -5)) :
				$_SERVER['DOCUMENT_ROOT'] . $this->Path
			);

		$this->Filehash = (file_exists($usepath) && is_file($usepath) ? sha1_file($usepath) : '');
		$this->i_savePersistentSlotsToDB('Filehash,RebuildDate');
	}

	public static function parseInternalLinks(&$text, $pid, $path = '', $returnAllFileIDs = false){
		$DB_WE = new DB_WE();
		$regs = [];
		if(preg_match_all('/(href|src)="(' . we_base_link::TYPE_INT_PREFIX . '|\?id=)(\\d+)(&amp;|&)?("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
			$allIds = [];
			foreach($regs as $reg){
				$allIds[] = intval($reg[3]);
			}
			if($returnAllFileIDs){
				return $allIds;
			}

			$DB_WE->query('SELECT ID,Path,Published,IsDynamic  FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $allIds) . ')' . (!empty($GLOBALS['we_doc']->InWebEdition) ? '' : ' AND Published>0'));
			$allDocs = $DB_WE->getAllFirst(true, MYSQL_ASSOC);
			foreach($regs as $reg){
				$foo = isset($allDocs[$reg[3]]) ? $allDocs[$reg[3]] : '';
				if($foo && $foo['Path']){
					$path_parts = pathinfo($foo['Path']);
					if(WYSIWYGLINKS_DIRECTORYINDEX_HIDE && seoIndexHide($path_parts['basename'])){
						$foo['Path'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
					}
					$text = str_replace($reg[1] . '="' . $reg[2] . $reg[3] . $reg[4] . $reg[5], $reg[1] . '="' . $foo['Path'] . (!$foo['IsDynamic'] ? '?m=' . $foo['Published'] . $reg[4] : ($reg[4] ? '?' : '')) . $reg[5], $text);
				} else {
					$text = preg_replace(['-<(a|img) [^>]*' . $reg[1] . '="' . $reg[2] . $reg[3] . '("|&|&amp;|\?)[^>]*>(.*)</a>-Ui',
						'-<(a|img) [^>]*' . $reg[1] . '="' . $reg[2] . $reg[3] . '(\?|&|&amp;|")[^>]*>-Ui',
						], ['${3}',
						''
						], $text);
				}
			}
		}
		if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '(\d+),(\d+)"/i', $text, $regs, PREG_SET_ORDER)){
			$text = preg_replace('/(="' . we_base_link::TYPE_THUMB_PREFIX . '[^>]* )width="[^"]*"([^>]* )height="[^"]*"([^>]*>)/U', '$1$2$3', $text);
			foreach($regs as $reg){
				list(, $imgID, $thumbID) = $reg;
				$thumbObj = new we_thumbnail();
				if($thumbObj->initByImageIDAndThumbID($imgID, $thumbID)){
					$text = str_replace('src="' . we_base_link::TYPE_THUMB_PREFIX . $imgID . ',' . $thumbID . '"', 'src="' . $thumbObj->getOutputPath(false, true) . '"', $text);
				} else {
					$text = preg_replace('|<img[^>]+src="' . we_base_link::TYPE_THUMB_PREFIX . $imgID . ',' . $thumbID . '"[^>]+>|Ui', '', $text);
				}
			}
		}
		if(defined('OBJECT_TABLE')){
			if(preg_match_all('/href="' . we_base_link::TYPE_OBJ_PREFIX . '(\d+)(\??)("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
				foreach($regs as $reg){
					$href = we_objectFile::getObjectHref($reg[1], $pid, $path, $DB_WE, WYSIWYGLINKS_DIRECTORYINDEX_HIDE, WYSIWYGLINKS_OBJECTSEOURLS);
					if(isset($GLOBALS['we_link_not_published'])){
						unset($GLOBALS['we_link_not_published']);
					}
					if($href){
						$text = ($reg[2] === '?' ?
								str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '?', 'href="' . $href . '&amp;', $text) :
								str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . $reg[2] . $reg[3], 'href="' . $href . $reg[2] . $reg[3], $text));
					} else {
						$text = preg_replace(['-<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '("|&|&amp;|\?)[^>]*>(.*)</a>-Ui',
							'-<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '("|&|&amp;|\?)[^>]*>-Ui',
							], ['${2}',
							''
							], $text);
					}
				}
			}
		}

		return preg_replace('/\<a>(.*)\<\/a>/siU', '${1}', $text);
	}

	private function getNavigationItems(){
		if($this->Table == FILE_TABLE && $this->ID && $this->InWebEdition){
			$this->DB_WE->query('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STYPE_DOCLINK . '") OR (IsFolder=1 AND SelectionType="' . we_navigation_navigation::STYPE_DOCLINK . '")) AND LinkID=' . intval($this->ID));
			return $this->DB_WE->getAll(true);
		}
		return [];
	}

	/**
	 * Get data which was saved persistent in this session by the editor
	 * @param string $name name of the variable to save
	 * @return mixed false if not found, data if found
	 */
	public function getEditorPersistent($name){
		return isset($this->editorSaves[$name]) ? $this->editorSaves[$name] : false;
	}

	/**
	 * Save data in editor persistent in session
	 * @param string $name name of the variable to save
	 * @param mixed $value the data to save. Don't save objects! Just data & arrays.
	 */
	public function setEditorPersistent($name, $value){
		$this->editorSaves[$name] = $value;
	}

	protected static function makeBlockName($block, $field){
		$block = str_replace('[0-9]+', '####BLOCKNR####', $block);
		$field = str_replace('[0-9]+', '####BLOCKNR####', $field);
		return str_replace('####BLOCKNR####', '[0-9]+', preg_quote($field . 'blk_' . $block . '__') . '[0-9]+');
	}

	protected static function makeLinklistName($block, $field){
		$block = str_replace('[0-9]+', '####BLOCKNR####', $block);
		$field = str_replace('[0-9]+', '####BLOCKNR####', $field);
		return str_replace('####BLOCKNR####', '[0-9]+', preg_quote($field . $block . '_TAGS_') . '[0-9]+');
	}

	public static function initDoc($we_dt, $we_ContentType = '', $we_ID = 0, $we_Table = '', $dontMakeGlobal = false){
		if(isset($GLOBALS['we_ContentType']) && empty($we_ContentType)){
			$we_ContentType = $GLOBALS['we_ContentType'];
		}

		if(empty($we_ContentType)){
			if(!empty($we_dt) && !empty($we_dt[0]['ContentType'])){
				$we_ContentType = $we_dt[0]['ContentType'];
			} else if((empty($we_dt) || empty($we_dt[0]['ClassName'])) && $we_ID && $we_Table){
				$we_ContentType = f('SELECT ContentType FROM ' . $GLOBALS['DB_WE']->escape($we_Table) . ' WHERE ID=' . intval($we_ID));
			}
		}

		switch(empty($we_ContentType) ? '' : $we_ContentType){
			/*
			  case we_base_ContentTypes::WEDOCUMENT:
			  $showDoc = !empty($GLOBALS['FROM_WE_SHOW_DOC']);
			  $we_doc = new we_webEditionDocument(); //($showDoc ? new we_webEditionDocument() : new we_view_webEditionDocument());
			  break;
			 *
			 */
			case we_base_ContentTypes::FOLDER:
				if($we_dt){
					$we_doc = new $we_dt[0]['ClassName'];
					break;
				}
				$we_doc = new we_folder();
				break;
			case 'nested_class_folder':
				$we_doc = new we_class_folder();
				$we_doc->IsClassFolder = 0;
				$we_ContentType = 'folder';
				break;
			case '':
				$we_doc = (!empty($we_dt[0]['ClassName']) && ($classname = $we_dt[0]['ClassName']) ?
						new $classname() :
						new we_webEditionDocument());
				break;
			default:
				$we_doc = we_base_ContentTypes::inst()->getObject($we_ContentType);
		}
		if(!$we_doc){
			exit(1);
		}

		if($we_ID){
			$we_doc->initByID($we_ID, $we_Table, ( (!empty($GLOBALS['FROM_WE_SHOW_DOC'])) || (isset($GLOBALS['WE_RESAVE']) && $GLOBALS['WE_RESAVE']) ) ? we_class::LOAD_MAID_DB : we_class::LOAD_TEMP_DB);
		} else if(!empty($we_dt)){
			$we_doc->we_initSessDat($we_dt);

//	in some templates we must disable some EDIT_PAGES and disable some buttons
			$we_doc->executeDocumentControlElements();
		} else {
			$we_doc->ContentType = $we_ContentType;
			$we_doc->Table = (!empty($we_Table) ? $we_Table : FILE_TABLE);
			$we_doc->we_new();
		}

		if($dontMakeGlobal){
//FIXME: remove this clone => where do we need this?!
			$GLOBALS['we_doc'] = clone($we_doc);
		}

//if document opens get initial object for versioning if no versions exist
		if(in_array(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0), ['load_edit_footer', 'switch_edit_page']) && $we_doc->Table !== VFILE_TABLE){
			$version = new we_versions_version();
			$version->setInitialDocObject($we_doc);
		}
		return $we_doc;
	}

}
