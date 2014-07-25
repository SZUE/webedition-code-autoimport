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
	var $schedArr = array();
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
	var $CSS = array();
	/* this array is used, to store document specific data for a page */
	protected $editorSaves = array();

	function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Extension', 'IsDynamic', 'Published', 'Category', 'IsSearchable', 'InGlossar', 'Language', 'schedArr', 'parseFile', 'editorSaves');
		$this->Table = FILE_TABLE;
		if(defined('WE_SIDEBAR')){
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
		return strlen($this->elements['data']['dat']);
	}

	// returns the whole document Alias - don't remove
	function getDocument($we_editmode = 0, $baseHref = 0, $we_transaction = ''){
		return $this->i_getDocument();
	}

	function initLanguageFromParent(){
		$ParentID = $this->ParentID;
		$i = 0;
		while(empty($this->Language)){
			if($ParentID == 0 || $i > 20){
				we_loadLanguageConfig();
				$this->Language = self::getDefaultLanguage();
				if(empty($this->Language)){
					$this->Language = 'de_DE';
				}
			} else {
				$this->DB_WE->query('SELECT Language, ParentID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($ParentID));

				while($this->DB_WE->next_record()){
					$ParentID = $this->DB_WE->f('ParentID');
					$this->Language = $this->DB_WE->f('Language');
				}
			}
			$i++;
		}
	}

	function getDefaultLanguage(){
		// get interface language of user
		list($_userLanguage) = explode('_', isset($_SESSION['prefs']['Language']) ? $_SESSION['prefs']['Language'] : '');

		// trying to get locale string out of interface language
		$_key = array_search($_userLanguage, getWELangs());

		$_defLang = $GLOBALS['weDefaultFrontendLanguage'];

		// if default language is not equal with frontend language
		if(substr($_defLang, 0, strlen($_key)) !== $_key){
			// get first language that fits
			foreach(getWeFrontendLanguagesForBackend() as $_k => $_v){
				$_parts = explode('_', $_k);
				if($_parts[0] === $_key){
					$_defLang = $_k;
				}
			}
		}
		return $_defLang;
	}

	/*
	 * Form Functions
	 */

	function formLanguage($withHeadline = true){

		we_loadLanguageConfig();

		$_defLang = self::getDefaultLanguage();
		$value = ($this->Language ? $this->Language : $_defLang);
		$inputName = 'we_' . $this->Name . '_Language';
		$_languages = getWeFrontendLanguagesForBackend();
		$_headline = ($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[language]') . '</td></tr>' : '');

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($_languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . " WHERE DocumentTable='tblFile' AND DID=" . $this->ID . ' AND Locale="' . $langkey . '"', 'LDID', $this->DB_WE);
				if(!$LDID){
					$LDID = 0;
				}
				$divname = 'we_' . $this->Name . '_LanguageDocDiv[' . $langkey . ']';
				$htmlzw.= '<div id="' . $divname . '" ' . ($this->Language == $langkey ? ' style="display:none" ' : '') . '>' . $this->formLanguageDocument($lang, $langkey, $LDID) . '</div>';
				$langkeys[] = $langkey;
			}
			return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	' . $_headline . '
	<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "dieWerte='" . implode(',', $langkeys) . "';showhideLangLink('we_" . $this->Name . "_LanguageDocDiv',dieWerte,this.options[this.selectedIndex].value);_EditorFrame.setEditorIsHot(true);"), "value", 508) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 20) . '</td></tr>
	<tr><td class="defaultfont" align="left">' . g_l('weClass', '[languageLinks]') . '</td></tr>
</table>
<br/>' . $htmlzw; //.we_html_tools::htmlFormElementTable($htmlzw,g_l('weClass','[languageLinksDefaults]'),"left",	"defaultfont");	dieWerte=\''.implode(',',$langkeys).'\'; disableLangDefault(\'we_'.$this->Name.'_LangDocType\',dieWerte,this.options[this.selectedIndex].value);"
		} else {
			return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	' . $_headline . '
	<tr><td>' . $this->htmlSelect($inputName, $_languages, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);", 'onchange' => "_EditorFrame.setEditorIsHot(true);"), "value", 508) . '</td></tr>
</table>';
		}
	}

	function formInGlossar(){
		return (we_base_moduleInfo::we_getModuleNameByContentType('glossary') == 'glossary' ?
				we_html_forms::checkboxWithHidden((bool) $this->InGlossar, 'we_' . $this->Name . '_InGlossar', g_l('weClass', '[InGlossar]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);')
					:
				'');
	}

	function formIsSearchable(){
		return we_html_forms::checkboxWithHidden((bool) $this->IsSearchable, 'we_' . $this->Name . '_IsSearchable', g_l('weClass', '[IsSearchable]'), false, 'defaultfont', '_EditorFrame.setEditorIsHot(true);');
	}

	function formExtension2(){
		$doctype = isset($this->DocType) ? $this->DocType : '';

		if($this->ID == 0 && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) == 'load_editor' && $doctype == ''){ //	Neues Dokument oder Dokument ohne DocType
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
		return we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup('we_' . $this->Name . '_Extension', $selected, $this->Extensions, 100, 'onselect="_EditorFrame.setEditorIsHot(true);"', permissionhandler::hasPerm('EDIT_DOCEXTENSION')), g_l('weClass', "[extension]"));
	}

	function formPath(){
		$disable = ( ($this->ContentType == we_base_ContentTypes::HTML || $this->ContentType == we_base_ContentTypes::WEDOCUMENT) && $this->Published);
		if($this->ContentType == we_base_ContentTypes::HTACESS){
			$this->Filename = '.htaccess';
			$filenameinput = 'disabled="disabled" ';
		} else {
			$filenameinput = '';
		}
		return $disable ? ('<span class="defaultfont">' . $this->Path . '</span>') : '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>' . $this->formInputField('', 'Filename', g_l('weClass', '[filename]'), 30, 388, 255, $filenameinput . 'onchange="_EditorFrame.setEditorIsHot(true);if(self.pathOfDocumentChanged){pathOfDocumentChanged();}"') . '</td>
		<td></td>
		<td>' . $this->formExtension2() . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 4) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . we_html_tools::getPixel(100, 2) . '</td>
	</tr>
	<tr><td colspan="3">' . $this->formDirChooser(388) . '</td></tr>
</table>';
	}

	function formMetaInfos(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="2">' . $this->formInputField("txt", "Title", g_l('weClass', "[Title]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	<tr><td colspan="2">' . $this->formInputField("txt", "Description", g_l('weClass', "[Description]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(2, 4) . '</td></tr>
	<tr><td colspan="2">' . $this->formInputField("txt", "Keywords", g_l('weClass', "[Keywords]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td></tr>
</table>' .
			($this->ContentType == we_base_ContentTypes::IMAGE ? $this->formCharset(true) : '');
	}

	function formCategory(){
		$delallbut = we_html_button::create_button('delete_all', "javascript:we_cmd('delete_all_cats')", true, 0, 0, '', '', $this->Category ? false : true);
		$addbut = we_html_button::create_button('add', "javascript:we_cmd('openCatselector',0,'" . CATEGORY_TABLE . "','','','opener.setScrollTo();fillIDs();opener.top.we_cmd(\\'add_cat\\',top.allIDs);')");
		$cats = new MultiDirChooser(508, $this->Category, 'delete_cat', we_html_button::create_button_table(array($delallbut, $addbut)), '', 'Icon,Path', CATEGORY_TABLE);
		$cats->extraDelFn = 'setScrollTo();';
		return $cats->get();
	}

	function formNavigation(){
		$isSee = $_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE;
		$navItems = $this->getNavigationItems();
		$addbut = we_html_button::create_button('add', "javascript:we_cmd('module_navigation_edit_navi',0)", true, 100, 22, '', '', (permissionhandler::hasPerm('EDIT_NAVIGATION') && $this->ID && $this->Published)
						? false : true, false);

		if(permissionhandler::hasPerm('EDIT_NAVIGATION') && $isSee){
			$delallbut = we_html_button::create_button('delete_all', "javascript:if(confirm('" . g_l('navigation', '[dellall_question]') . "')) we_cmd('delete_all_navi')", true, 0, 0, "", "", (permissionhandler::hasPerm('EDIT_NAVIGATION') && $navItems)
							? false : true);
		} else {
			$delallbut = '';
		}
		$navis = new MultiFileChooser(508, $navItems, 'delete_navi', we_html_button::create_button_table(array($delallbut, $addbut)), "module_navigation_edit_navi", "Icon,Path", NAVIGATION_TABLE);
		$navis->extraDelFn = 'setScrollTo();';
		$NoDelNavis = $navItems;
		foreach($NoDelNavis as $_path){
			$_id = path_to_id($_path, NAVIGATION_TABLE);
			$_naviItem = new we_navigation_navigation($_id);
			if(!$_naviItem->hasAnyChilds()){

				if(in_array($_path, $NoDelNavis)){
					$pos = array_search($_path, $NoDelNavis);
					array_splice($NoDelNavis, $pos, 1);
				}
			}
		}
		$navis->setDisabledDelItems($NoDelNavis, g_l('navigation', '[NoDeleteFromDocument]'));

		if(!$isSee || !permissionhandler::hasPerm('EDIT_NAVIGATION')){
			$navis->isEditable = false;
			$navis->CanDelete = false;
		}

		return we_html_button::create_state_changer() . $navis->get();
	}

	function addCat($id){
		$cats = makeArrayFromCSV($this->Category);
		$ids = makeArrayFromCSV($id);
		foreach($ids as $id){
			if($id && (!in_array($id, $cats))){
				$cats[] = $id;
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	function delCat($id){
		$cats = makeArrayFromCSV($this->Category);
		if(in_array($id, $cats)){
			$pos = array_search($id, $cats);
			if($pos !== false || $pos == '0'){
				array_splice($cats, $pos, 1);
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	function addNavi($id, $text, $parentid, $ordn){
		$text = urldecode($text); //Bug #3769
		if($this->ID){
			//$navis = $this->getNavigationItems();

			if(is_numeric($ordn)){
				$ordn--;
			}
			$_ord = ($ordn == 'end' ? 10000 : (is_numeric($ordn) && $ordn > 0 ? $ordn : 0));

			$_ppath = id_to_path($parentid, NAVIGATION_TABLE);
			$_new_path = rtrim($_ppath, '/') . '/' . $text;

			$rename = false;
			if(empty($id)){
				$id = path_to_id($_new_path, NAVIGATION_TABLE);
				if($id){
					$rename = true;
				}
			}

			$_naviItem = new we_navigation_navigation($id);
			$_old_path = ($id ? $_naviItem->Path : '');

			$_naviItem->Ordn = $_ord;
			$_naviItem->ParentID = $parentid;
			$_naviItem->LinkID = $this->ID;
			$_naviItem->Text = $text;
			$_naviItem->Path = $_new_path;
			if(NAVIGATION_ENTRIES_FROM_DOCUMENT == 0){
				$_naviItem->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$_naviItem->SelectionType = we_navigation_navigation::STPYE_DOCTYPE;
				$_naviItem->IsFolder = 1;
				$charset = $_naviItem->findCharset($_naviItem->ParentID);
				$_naviItem->Charset = ($charset ? $charset : (DEFAULT_CHARSET ? DEFAULT_CHARSET : $GLOBALS['WE_BACKENDCHARSET']));
			} else {
				$_naviItem->Selection = we_navigation_navigation::SELECTION_STATIC;
				$_naviItem->SelectionType = we_navigation_navigation::STPYE_DOCLINK;
			}

			$_naviItem->save();
			$_naviItem->setOrdn($_ord);
			// replace or set new item in the multi selector
			/* if($id && !$rename){
			  foreach($navis as $_k => $_v){
			  if($_old_path == $_v){
			  $navis[$_k] = $_new_path;
			  }
			  }
			  } else {
			  $navis[] = $_new_path;
			  }

			  $this->NavigationItems = makeCSVFromArray($navis, true); */
		}
	}

	function delNavi($path){
		$path = urldecode($path); //Bug #3816
		$navis = $this->getNavigationItems();
		if(in_array($path, $navis)){
			$pos = array_search($path, $navis);
			if($pos !== false || $pos == '0'){
				$_id = path_to_id($path, NAVIGATION_TABLE);
				$_naviItem = new we_navigation_navigation($_id);
				if(!$_naviItem->hasAnyChilds()){
					$_naviItem->delete();
					array_splice($navis, $pos, 1);
				}
			}
		}
		//$this->NavigationItems = makeCSVFromArray($navis, true);
	}

	function delAllNavi(){
//		$navis = makeArrayFromCSV($this->NavigationItems);
		foreach($navis as $_path){
			$_id = path_to_id($_path, NAVIGATION_TABLE);
			$_naviItem = new we_navigation_navigation($_id);
			if(!$_naviItem->hasAnyChilds()){
				$_naviItem->delete();
				if(in_array($_path, $navis)){
					$pos = array_search($_path, $navis);
					array_splice($navis, $pos, 1);
				}
			}
		}

		//	$this->NavigationItems = makeCSVFromArray($navis, true);
	}

	/*
	 * internal functions
	 */

	function getParentIDFromParentPath(){
		$f = new we_folder();
		return ($f->initByPath($this->ParentPath) ? $f->ID : -1);
	}

	function addEntryToList($name, $number = 1){
		$list = $this->getElement($name);

		$listarray = $list ? unserialize($list) : array();
		$listarray = is_array($listarray) ? $listarray : array();

		for($f = 0; $f < $number; $f++){
			$content = $this->getElement($name, 'content');
			$new_nr = $this->getMaxListArrayNr($listarray) + 1;

			// clear value
			$names = $this->getNamesFromContent($content);

			foreach($names as $_name){
				$this->setElement($_name . '_' . $new_nr, '');
			}

			$listarray[] = '_' . $new_nr;
		}
		$this->setElement($name, serialize(array_values($listarray)), 'block');
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
		$list = $this->getElement($name);

		$listarray = $list ? unserialize($list) : array();
		$listarray = is_array($listarray) ? $listarray : array();

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

		$this->setElement($name, serialize(array_values($listarray)), 'block');
	}

	function upEntryAtList($name, $nr, $number = 1){
		$list = $this->getElement($name);
		if(!$list){
			t_e('failed');
			return;
		}
		$listarray = unserialize($list);
		$newPos = $nr - $number;
		if($newPos < 0){
			$newPos = 0;
		}
		$temp = $listarray[$newPos];
		$listarray[$newPos] = $listarray[$nr];
		$listarray[$nr] = $temp;

		$this->setElement($name, serialize($listarray), 'block');
	}

	function downEntryAtList($name, $nr, $number = 1){
		$list = $this->getElement($name);
		if(!$list){
			return;
		}
		$listarray = unserialize($list);
		$newPos = $nr + $number;
		if($newPos > count($listarray) - 1){
			$newPos = count($listarray) - 1;
		}
		$temp = $listarray[$newPos];
		$listarray[$newPos] = $listarray[$nr];
		$listarray[$nr] = $temp;
		$this->setElement($name, serialize($listarray), 'block');
	}

	function removeEntryFromList($name, $nr, $names = '', $isBlock = false){
		$list = $this->getElement($name);
		$listarray = $list ? unserialize($list) : array();
		if(is_array($listarray)){
			if($isBlock){
				foreach(array_keys($this->elements) as $key){
					if(preg_match('/' . $names . '(__.*)*$/', $key)){// # Bug 6904
						unset($this->elements[$key]);
					}
				}
			} else {
				$namesArray = $names ? explode(',', $names) : array($names);
				foreach($namesArray as $element){
					unset($this->elements[$element . $listarray[$nr]]);
				}
			}
			if(is_array($listarray)){// Bug #4079
				unset($listarray[$nr]);
			}
		} else {
			$listarray = array();
		}

		$this->setElement($name, serialize(array_values($listarray)), 'block');
	}

	function addLinkToLinklist($name){
		$ll = new we_base_linklist($this->getElement($name));
		$ll->addLink();
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function upEntryAtLinklist($name, $nr){
		$ll = new we_base_linklist($this->getElement($name));
		$ll->upLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function downEntryAtLinklist($name, $nr){
		$ll = new we_base_linklist($this->getElement($name));
		$ll->downLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function insertLinkAtLinklist($name, $nr){
		$ll = new we_base_linklist($this->getElement($name));
		$ll->insertLink($nr);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function removeLinkFromLinklist($name, $nr, $names = ''){
		$ll = new we_base_linklist($this->getElement($name));
		$ll->removeLink($nr, $names, $name);
		$this->setElement($name, $ll->getString(), 'linklist');
	}

	function changeLink($name){
		$this->setElement($name, serialize($_SESSION['weS']['WE_LINK']));
	}

	function changeLinklist($name){
		$this->setElement($name, $_SESSION['weS']['WE_LINKLIST']);
	}

	function getNamesFromContent($content){
		$result = array();
		preg_match_all('/< ?we:[^>]+name="([^"]+)"[^>]*>/i', $content, $result, PREG_SET_ORDER);
		$arr = array();
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
		if(is_array($this->Extensions) && !empty($this->Extensions)){
			$this->Extension = $this->Extensions[0];
		}
		if(!isset($GLOBALS['WE_IS_DYN']) && ($this->Table == FILE_TABLE || $this->Table == TEMPLATES_TABLE)){
			if(($ws = get_ws($this->Table))){
				$foo = makeArrayFromCSV($ws);
				if($foo){
					$this->setParentID(intval($foo[0]));
				}
			}
		}
	}

	private function i_setExtensions(){
		if($this->ContentType){
			$exts = we_base_ContentTypes::inst()->getExtension($this->ContentType);
			$this->Extensions = is_array($exts) ? $exts : array($exts);
		}
	}

	private function isVersioned(){
		switch($this->ContentType){
			case we_base_ContentTypes::FLASH:
				return VERSIONING_FLASH;
			case we_base_ContentTypes::IMAGE:
				return VERSIONING_IMAGE;
			case we_base_ContentTypes::TEMPLATE:
				return VERSIONING_TEXT_WETMPL;
			case we_base_ContentTypes::QUICKTIME:
				return VERSIONING_QUICKTIME;
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

	public function we_save($resave = 0, $skipHook = 0){
		$this->errMsg = '';
		$this->i_setText();

		if(!$skipHook){
			$hook = new weHook('preSave', '', array($this, 'resave' => $resave));
			$ret = $hook->executeHook();
			//check if doc should be saved
			if($ret === false){
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

		if($resave == 0){ // NO rebuild!!!
			$this->resaveWeDocumentCustomerFilter();
		}

		if($this->isVersioned()){
			$version = new weVersions();
			$version->save($this);
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
		return $ret;
	}

	function resaveWeDocumentCustomerFilter(){
		if(isset($this->documentCustomerFilter) && $this->documentCustomerFilter){
			we_customer_documentFilter::saveForModel($this);
		}
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

	// reverse function to we_init_sessDat
	/* function saveInSession(&$save){
	  parent::saveInSession($save);
	  //$save[2] = $this->NavigationItems;
	  } */

	// reverse function to saveInSession !!!
	function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
			if(
				($day = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_day')) && ($month = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_month')) && ($year = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_year')) && ($hour = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_hour')) !== false && ($min = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_From_minute')) !== false){
				$this->From = mktime($hour, $min, 0, $month, $day, $year);
			}
			if(
				($day = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_day')) && ($month = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_month')) && ($year = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_year')) && ($hour = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_hour')) !== false && ($min = we_base_request::_(we_base_request::INT, 'we_' . $this->Name . '_To_minute')) !== false){
				$this->To = mktime($hour, $min, 0, $month, $day, $year);
			}
		}
		/* if(isset($sessDat[2])){
		  $this->NavigationItems = $sessDat[2];
		  } else {
		  $this->i_loadNavigationItems();
		  } */


		if(we_base_request::_(we_base_request::INT, 'wecf_mode') !== false){
			$this->documentCustomerFilter = we_customer_documentFilter::getCustomerFilterFromRequest($this);
		} else if(isset($sessDat[3])){ // init webUser from session
			$this->documentCustomerFilter = unserialize($sessDat[3]);
		}


		$this->i_setExtensions();

		if(!($this->Language) && $this->Table != TEMPLATES_TABLE){
			$this->initLanguageFromParent();
		}
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
			we_util_File::deleteLocalFile($this->getSitePath(true));
		}
		return we_base_file::save($this->getSitePath(), $doc);
	}

	protected function i_writeMainDir($doc){
		if($this->isMoved()){
			we_util_File::deleteLocalFile($this->getRealPath(true));
		}
		return we_base_file::save($this->getRealPath(), $doc);
	}

	protected function i_writeDocument(){
		$update = $this->isMoved();
		$doc = $this->i_getDocumentToSave();
		if(!($doc || !$doc)){
			return false;
		}
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
		$this->DocStream = $this->DocStream ? $this->DocStream : $this->i_getDocument();
		return $this->DocStream;
	}

	function i_getDocument($includepath = ''){
		return isset($this->elements['data']['dat']) ? $this->elements['data']['dat'] : '';
	}

	function i_setDocument($value){
		$this->elements['data']['dat'] = $value;
	}

	function i_filenameDouble(){
		return f('SELECT 1 FROM ' . escape_sql_query($this->Table) . ' WHERE ParentID=' . intval($this->ParentID) . " AND Filename='" . escape_sql_query($this->Filename) . "' AND Extension='" . escape_sql_query($this->Extension) . "' AND ID != " . intval($this->ID), "", $this->DB_WE);
	}

//FIXME: parameter $attribt should be: array $attribs=array()
	//FIXME: check if we can rid of this function, since it causes problems every change of tags since it also uses the given attribs array!
	public function getFieldByVal($val, $type, $attribs = '', $pathOnly = false, $parentID = 0, $path = '', we_database_base $db = null, $classID = '', $fn = 'this'){
		$attribs = is_array($attribs) ? $attribs : array();
		if(isset($attribs['_name_orig'])){
			unset($attribs['_name_orig']);
		}
		$db = ($db ? $db : new DB_WE());
		if((!$attribs) || (!is_array($attribs))){
			$attribs = array();
		}
		switch($type){
			case 'img':
				$img = new we_imageDocument(false);

				if(isset($attribs['name'])){
					$img->Name = $attribs['name'];
				}

				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}

				$img->initByID($val, FILE_TABLE);

				$altField = $img->Name . we_imageDocument::ALT_FIELD;
				$titleField = $img->Name . we_imageDocument::TITLE_FIELD;

				if(isset($GLOBALS['lv'])){
					switch(get_class($GLOBALS['lv'])){
						case 'we_shop_listviewShopVariants':
							$altField = (WE_SHOP_VARIANTS_PREFIX . $GLOBALS['lv']->Position . '_' . $altField);
							$titleField = (WE_SHOP_VARIANTS_PREFIX . $GLOBALS['lv']->Position . '_' . $titleField);
							break;
						case 'we_listview':
							$alt = $GLOBALS['lv']->f($altField) ? $GLOBALS['lv']->f($altField) : '';
							$title = $GLOBALS['lv']->f($titleField) ? $GLOBALS['lv']->f($titleField) : '';
						case 'we_object_listview':
						case 'we_object_listviewMultiobject':
							$attribs['alt'] = isset($alt) && $alt ? $alt : ($img->getElement('alt') ? $img->getElement('alt') : (isset($attribs['alt']) ? $attribs['alt'] : ''));
							$attribs['title'] = isset($title) && $title ? $title : ($img->getElement('title') ? $img->getElement('title') : (isset($attribs['title']) ? $attribs['title']
											: ''));
							break;
					}
				}
				if(isset($attribs['alt'])){
					$attribs['alt'] = oldHtmlspecialchars($attribs['alt']);
				}
				if(isset($attribs['title'])){
					$attribs['title'] = oldHtmlspecialchars($attribs['title']);
				}
				if(!(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0) == 'reload_editpage' && ($img->Name === we_base_request::_(we_base_request::STRING, 'we_cmd', false, 1)) && we_base_request::_(we_base_request::STRING, 'we_cmd', '', 2) == 'change_image') && isset($GLOBALS['we_doc']->elements[$altField])){
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
					$img->initByAttribs(removeAttribs($attribs, array('hyperlink', 'target')));
				}
				if(isset($GLOBALS['lv'])){
					if(isset($GLOBALS['lv']->count)){
						$img->setElement('name', $img->getElement('name') . '_' . $GLOBALS['lv']->count, 'attrib');
						$img->Name = $img->Name . '_' . $GLOBALS['lv']->count;
					} else {
						$img->setElement('name', $img->getElement('name'), 'attrib');
					}
				}
				return $img->getHtml(false, true, $pathOnly);
			case 'binary':
				$bin = new we_otherDocument();
				if(isset($attribs['name'])){
					$bin->Name = $attribs['name'];
				}
				if(!$val && isset($attribs['id'])){
					$val = $attribs['id'];
				}
				$bin->initByID($val, FILE_TABLE);
				return array($bin->Text, $bin->Path, $bin->ParentPath, $bin->Filename, $bin->Extension, (isset($bin->elements['filesize']) ? $bin->elements['filesize']['dat']
							: ''));
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
			case 'quicktime':
				$fl = new we_quicktimeDocument();
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
				$link = $val ? unserialize($val) : array();
				$only = weTag_getAttribute('only', $attribs);

				$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, true);
				$objectseourls = weTag_getAttribute('objectseourls', $attribs, TAGLINKS_OBJECTSEOURLS, true);

				if($pathOnly || $only == 'href'){
					$return = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls);

					if((isset($GLOBALS['we_link_not_published'])) && ($GLOBALS['we_link_not_published'])){
						unset($GLOBALS['we_link_not_published']);
						return '';
					}
					return $return;
				}

				if(is_array($link)){
					$img = new we_imageDocument();
					//	set name of image for rollover ...
					$_useName = '';

					if(isset($attribs['name'])){ //	here we must change the name for a rollover-image
						$_useName = $attribs['name'] . '_img';
						$img->setElement('name', $_useName, 'dat');
					}

					$xml = weTag_getAttribute('xml', $attribs, (XHTML_DEFAULT), true, false);
					$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, true, true);
					if($only){
						return ($only == 'content' ?
								self::getLinkContent($link, $parentID, $path, $db, $img, $xml, $_useName, $oldHtmlspecialchars, $hidedirindex, $objectseourls) :
								isset($link[$only]) ? $link[$only] : ''); // #3636
					}

					if(($content = self::getLinkContent($link, $parentID, $path, $db, $img, $xml, $_useName, $oldHtmlspecialchars, $hidedirindex, $objectseourls))){
						if(($startTag = self::getLinkStartTag($link, $attribs, $parentID, $path, $db, $img, $_useName, $hidedirindex, $objectseourls))){
							return $startTag . $content . '</a>';
						}
						return $content;
					}
				}
				return '';
			case 'date':
				// it is a date field from the customer module
				//2010-12-12 00:00:00
				if($val && !is_numeric($val)){
					$len = strlen($val);
					if($len == 19 || $len == 10){

						$_y = substr($val, 0, 4);
						$_m = substr($val, 5, 2);
						$_d = substr($val, 8, 2);
						if($len == 19){
							$_h = substr($val, 11, 2);
							$_min = substr($val, 14, 2);
							$_s = substr($val, 17, 2);
							$val = mktime($_h, $_min, $_s, $_m, $_d, $_y);
						} else {
							$val = mktime(0, 0, 0, $_m, $_d, $_y);
						}
					}
				}

				$val = $val ? $val : time();

				$format = isset($attribs['format']) ? $attribs['format'] : g_l('date', '[format][default]');
				//FIXME: zend part doesn't use correctDateFormat & won't work on new Dates
				if(isset($GLOBALS['WE_MAIN_DOC']) && $GLOBALS['WE_MAIN_DOC']->Language != 'de_DE' && is_numeric($val)){
					$zdate = new Zend_Date($val, Zend_Date::TIMESTAMP);
					return $zdate->toString($format, 'php', $GLOBALS['WE_MAIN_DOC']->Language);
				}
				require_once(WE_INCLUDES_PATH . 'we_tags/we_tag_date.inc.php');
				$dt = new DateTime((is_numeric($val) ? '@' : '') . $val);
				$dt->setTimeZone(new DateTimeZone(@date_default_timezone_get())); //Bug #6335
				return $dt->format(correctDateFormat($format, $dt));
			case 'select':
				if(defined('OBJECT_TABLE')){
					if(strlen($val) == 0){
						return '';
					}
					if($classID){
						$defVals = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classID), '', $db);
						if($defVals){
							$arr = unserialize($defVals);
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

				if(!weTag_getAttribute('html', $attribs, true, true) && !weTag_getAttribute('wysiwyg', $attribs, false, true)){
					$retval = strip_tags($retval, '<br/>,<p>');
				}

				$_htmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, true);
				$_wysiwyg = weTag_getAttribute('wysiwyg', $attribs, false, true);

				if($_htmlspecialchars && (!$_wysiwyg)){
					$retval = preg_replace('/#we##br([^#]*)#we##/', '<br\1>', oldHtmlspecialchars(preg_replace('/<br([^>]*)>/i', '#we##br\1#we##', $retval), ENT_QUOTES));
				}
				if(!weTag_getAttribute('php', $attribs, (defined('WE_PHP_DEFAULT') && WE_PHP_DEFAULT), true)){
					$retval = we_base_util::rmPhp($retval);
				}
				$xml = weTag_getAttribute('xml', $attribs, (XHTML_DEFAULT), true);
				$retval = preg_replace('-<(br|hr)([^/>]*)/? *>-i', ($xml ? '<\\1\\2/>' : '<\\1\\2>'), $retval);

				if(preg_match('/^[\d.,]+$/', trim($retval))){
					$precision = isset($attribs['precision']) ? abs($attribs['precision']) : 2;
					if(($num = weTag_getAttribute('num_format', $attribs))){
						$retval = we_util_Strings::formatNumber(we_base_util::std_numberformat($retval), $num, $precision);
					}
				}
				if(weTag_getAttribute('win2iso', $attribs, false, true)){

					$charset = ( isset($GLOBALS['WE_MAIN_DOC']) && isset($GLOBALS['WE_MAIN_DOC']->elements['Charset']['dat'])) ? $GLOBALS['WE_MAIN_DOC']->elements['Charset']['dat']
							: '';
					if(trim(strtolower(substr($charset, 0, 3))) == 'iso' || $charset == ''){
						$retval = strtr($retval, array(
							chr(128) => '&#8364;',
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
							chr(159) => '&#376;'));
					}
				}
				return str_replace(array("##|n##", "##|r##"), array("\n", "\r"), $retval);
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
			case 'quicktime':
				if(isset($attribs['showcontrol']) && !$attribs['showcontrol'] && isset($attribs['id']) && $attribs['id']){//bug 6433: siehe korrespondierende Änderung in we_tag_img
					unset($attribs['showcontrol']);
					$val = $attribs['id'];
					break;
				}
				$val = $this->getElement($attribs['name'], 'bdid');
				if(!$val){
					$val = $this->getElement(isset($attribs['name']) ? $attribs['name'] : '');
				}
				break;
			case 'href':
				$val = $this->getElement(isset($attribs['name']) ? $attribs['name'] : '');
				if((isset($this->TableID) && $this->TableID) || ($this instanceof we_objectFile)){
					$hrefArr = $val ? unserialize($val) : array();
					return (is_array($hrefArr) ? self::getHrefByArray($hrefArr) : '');
				}
				break;
			default:
				$val = $this->getElement(isset($attribs['name']) ? $attribs['name'] : '');
		}

		return $this->getFieldByVal($val, $type, $attribs, $pathOnly, isset($GLOBALS['WE_MAIN_DOC']) ? $GLOBALS['WE_MAIN_DOC']->ParentID : $this->ParentID, isset($GLOBALS['WE_MAIN_DOC'])
						? $GLOBALS['WE_MAIN_DOC']->Path : $this->Path, $this->DB_WE, (isset($attribs['classid']) && isset($attribs['type']) && $attribs['type'] == 'select') ? $attribs['classid']
						: (isset($this->TableID) ? $this->TableID : ''));
	}

	private function getValFromSrc($fn, $name){
		switch($fn){
			default:
			case 'this':
				return $this->getElement($name);
			case 'listview':
				return $GLOBALS['lv']->f($name);
		}
	}

	function getHref($attribs, we_database_base $db = null, $fn = 'this'){
		$db = $db ? $db : new_DB_WE();
		$n = $attribs['name'];
		$nint = $n . we_base_link::MAGIC_INT_LINK;
		if($this->getValFromSrc($fn, $nint)){
			$intID = $this->getValFromSrc($fn, $n . we_base_link::MAGIC_INT_LINK_ID);
			return f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($intID), '', $db);
		}
		return $this->getValFromSrc($fn, $n);
	}

	static function getHrefByArray($hrefArr){
		if(isset($hrefArr['int']) && $hrefArr['int']){
			$intID = isset($hrefArr['intID']) ? $hrefArr['intID'] : 0;
			return $intID ? id_to_path($intID) : '';
		}
		return isset($hrefArr['extPath']) ? $hrefArr['extPath'] : '';
	}

	function getLinkHref($link, $parentID, $path, we_database_base $db = null, $hidedirindex = false, $objectseourls = false){
		$db = ($db ? $db : new DB_WE());

		// Bug Fix 8170&& 8166
		if(isset($link['href']) && strpos($link['href'], we_base_link::TYPE_MAIL_PREFIX) === 0){
			$link['type'] = we_base_link::TYPE_MAIL;

			//added for #7269
			if(isset($link['subject']) && $link['subject'] != ''){
				$link['href'] = $link['href'] . "?subject=" . $link['subject'];
			}
			if(isset($link['cc']) && $link['cc'] != ''){
				$link['href'] = $link['href'] . "&cc=" . $link['cc'];
			}
			if(isset($link['bcc']) && $link['bcc'] != ''){
				$link['href'] = $link['href'] . "&bcc=" . $link['bcc'];
			}
		}
		if(!isset($link['type'])){
			return '';
		}
		switch($link['type']){
			case we_base_link::TYPE_INT:
				$id = $link['id'];
				if(empty($id)){
					return '';
				}
				$path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), '', $db);
				$path_parts = pathinfo($path);
				if($hidedirindex && show_SeoLinks() && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
					$path = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
				}
				if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition || f('SELECT Published FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), '', $db)){
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

	function getLinkContent($link, $parentID = 0, $path = '', we_database_base $db = null, $img = '', $xml = '', $_useName = '', $htmlspecialchars = false, $hidedirindex = false, $objectseourls = false){
		$l_href = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls);

		if(isset($GLOBALS['we_link_not_published']) && $GLOBALS['we_link_not_published']){
			unset($GLOBALS['we_link_not_published']);
			return '';
		}

		switch(isset($link['ctype']) ? $link['ctype'] : ''){
			case we_base_link::CONTENT_INT:
				$img = ($img ? $img : new we_imageDocument());
				$img->initByID($link['img_id']);

				$img_attribs = array('width' => $link['width'], 'height' => $link['height'], 'border' => $link['border'], 'hspace' => $link['hspace'], 'vspace' => $link['vspace'], 'align' => $link['align'], 'alt' => $link['alt'], 'title' => (isset($link['img_title'])
							? $link['img_title'] : ''));

				if($_useName){ //	rollover with links ...
					$img_attribs['name'] = $_useName;
					$img->elements['name']['dat'] = $_useName;
				}

				if($xml){
					$img_attribs['xml'] = 'true';
				}

				$img->initByAttribs($img_attribs);

				return $img->getHtml(false, false);
			case we_base_link::CONTENT_EXT:
				//  set default atts
				$img_attribs = array('src' => $link['img_src'],
					'alt' => '',
					'xml' => $xml
				);
				if(isset($link['img_title'])){
					$img_attribs['title'] = $link['img_title'];
				}
				//  deal with all remaining attribs
				$img_attList = array('width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'name');
				foreach($img_attList as $k){
					if(isset($link[$k]) && $link[$k] != ''){
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

	function getLinkStartTag($link, $attribs, $parentID = 0, $path = '', we_database_base $db = null, $img = '', $_useName = '', $hidedirindex = false, $objectseourls = false){
		if(($l_href = self::getLinkHref($link, $parentID, $path, $db, $hidedirindex, $objectseourls))){
			//    define some arrays to order the attribs to image, link or js-window ...
			$_popUpAtts = array('jswin', 'jscenter', 'jswidth', 'jsheight', 'jsposx', 'jsposy', 'jsstatus', 'jsscrollbars', 'jsmenubar', 'jstoolbar', 'jsresizable', 'jslocation');

			//    attribs only for image - these are already handled
			$_imgAtts = array('img_id', 'width', 'height', 'border', 'hspace', 'vspace', 'align', 'alt', 'img_title');

			//    these are handled separately
			$_dontUse = array('img_id', 'obj_id', 'ctype', 'anchor', 'params', 'attribs', 'img_src', 'text', 'type', 'only');

			//    these are already handled dont get them in output
			$_we_linkAtts = array('id');

			$_linkAttribs = array();

			// define image-if necessary - handle with image-attribs
			$img = ($img ? $img : new we_imageDocument());

			//   image attribs
			foreach($_imgAtts as $att){ //  take all attribs belonging to image inside content
				$img_attribs[$att] = isset($link[$att]) ? $link[$att] : '';
			}

			$img->initByID($img_attribs['img_id']);
			$img->initByAttribs($img_attribs);


			if($link['ctype'] == we_base_link::TYPE_INT){
				//	set name of image dynamically
				if($_useName){ //	we must set the name of the image -> rollover
					$img->setElement('name', $_useName, 'dat');
				}
				$rollOverScript = $img->getRollOverScript();
				$rollOverAttribsArr = $img->getRollOverAttribsArr();
			} else {
				$rollOverScript = '';
				$rollOverAttribsArr = array();
			}

			// Link-Attribs
			//   1st attribs-string from link dialog ! These are already used in content ...
			if(isset($link['attribs'])){
				$_linkAttribs = array_merge(we_tag_tagParser::makeArrayFromAttribs($link['attribs']), $_linkAttribs);
			}

			//   2nd take all atts given in link-array - from function we_tag_link()
			foreach($link as $k => $v){ //   define all attribs - later we can remove/overwrite them
				if($v != '' && !in_array($k, $_we_linkAtts) && !in_array($k, $_imgAtts) && !in_array($k, $_popUpAtts) && !in_array($k, $_dontUse)){
					$_linkAttribs[$k] = $v;
				}
			}

			//   3rd we take attribs given from we:link,
			foreach($attribs as $k => $v){ //   define all attribs - later we can remove/overwrite them
				if($v != '' && !in_array($k, $_imgAtts) && !in_array($k, $_popUpAtts) && !in_array($k, $_dontUse)){
					$_linkAttribs[$k] = $v;
				}
			}

			//   4th use Rollover attributes
			foreach($rollOverAttribsArr as $n => $v){
				$_linkAttribs[$n] = $v;
			}
			//   override the href at last important !!

			$linkAdds = (isset($link['params']) ? $link['params'] : '' ) . (isset($link['anchor']) ? $link['anchor'] : '' );
			if(strpos($linkAdds, '?') === false && strpos($linkAdds, '&') !== false && strpos($linkAdds, '&') == 0){//Bug #5478
				$linkAdds = substr_replace($linkAdds, '?', 0, 1);
			}
			$_linkAttribs['href'] = $l_href . str_replace('&', '&amp;', $linkAdds);

			// The pop-up-window                              */
			$_popUpCtrl = array();
			foreach($_popUpAtts as $n){
				if(isset($link[$n])){
					$_popUpCtrl[$n] = $link[$n];
				}
			}


			if(isset($_popUpCtrl['jswin']) && $_popUpCtrl['jswin']){ //  add attribs for popUp-window
				$js = 'var we_winOpts = \'\';';
				if(isset($_popUpCtrl["jscenter"]) && $_popUpCtrl["jscenter"] && isset($_popUpCtrl["jswidth"]) && $_popUpCtrl["jswidth"] && isset($_popUpCtrl["jsheight"]) && $_popUpCtrl["jsheight"]){
					$js .= 'if (window.screen) {var w = ' . $_popUpCtrl["jswidth"] . ';var h = ' . $_popUpCtrl["jsheight"] . ';var screen_height = screen.availHeight - 70;var screen_width = screen.availWidth-10;var w = Math.min(screen_width,w);var h = Math.min(screen_height,h);var x = (screen_width - w) / 2;var y = (screen_height - h) / 2;we_winOpts = \'left=\'+x+\',top=\'+y;}else{we_winOpts=\'\';};';
				} else if((isset($_popUpCtrl["jsposx"]) && $_popUpCtrl["jsposx"] != "") || (isset($_popUpCtrl["jsposy"]) && $_popUpCtrl["jsposy"] != "")){
					if($_popUpCtrl["jsposx"] != ''){
						$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'left=' . $_popUpCtrl["jsposx"] . '\';';
					}
					if($_popUpCtrl["jsposy"] != ''){
						$js .= 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'top=' . $_popUpCtrl["jsposy"] . '\';';
					}
				}
				$js.=
					(isset($_popUpCtrl["jswidth"]) && $_popUpCtrl["jswidth"] ?
						'we_winOpts += (we_winOpts ? \',\' : \'\')+\'width=' . $_popUpCtrl["jswidth"] . '\';' : '') .
					(isset($_popUpCtrl["jsheight"]) && $_popUpCtrl["jsheight"] ?
						'we_winOpts += (we_winOpts ? \',\' : \'\')+\'height=' . $_popUpCtrl["jsheight"] . '\';' : '') . 'we_winOpts += (we_winOpts ? \',\' : \'\')+\'status=' . (isset($_popUpCtrl["jsstatus"]) && $_popUpCtrl["jsstatus"]
							? 'yes' : 'no') . '\';' .
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'scrollbars=' . (isset($_popUpCtrl["jsscrollbars"]) && $_popUpCtrl["jsscrollbars"] ? 'yes' : 'no') . '\';' .
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'menubar=' . (isset($_popUpCtrl["jsmenubar"]) && $_popUpCtrl["jsmenubar"] ? 'yes' : 'no') . '\';' .
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'resizable=' . (isset($_popUpCtrl["jsresizable"]) && $_popUpCtrl["jsresizable"] ? 'yes' : 'no') . '\';' .
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'location=' . (isset($_popUpCtrl["jslocation"]) && $_popUpCtrl["jslocation"] ? 'yes' : 'no') . '\';' .
					'we_winOpts += (we_winOpts ? \',\' : \'\')+\'toolbar=' . (isset($_popUpCtrl["jstoolbar"]) && $_popUpCtrl["jstoolbar"] ? 'yes' : 'no') . '\';';
				$foo = $js . "var we_win = window.open('','we_" . (isset($attribs["name"]) ? $attribs["name"] : "") . "',we_winOpts);";

				$_linkAttribs['target'] = 'we_' . (isset($attribs["name"]) ? $attribs["name"] : "");
				$_linkAttribs['onclick'] = $foo;
			}
			$_linkAttribs = removeAttribs($_linkAttribs, array('hidedirindex', 'objectseourls'));
			return $rollOverScript . getHtmlTag('a', $_linkAttribs, '', false, true);
		} else {
			if((isset($GLOBALS['we_link_not_published'])) && ($GLOBALS['we_link_not_published'])){
				unset($GLOBALS['we_link_not_published']);
			}
		}
	}

	/*
	 * functions for scheduler pro
	 */

	function add_schedule(){
		$this->schedArr[] = we_schedpro::getEmptyEntry();
	}

	function del_schedule($nr){
		array_splice($this->schedArr, $nr, 1);
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		if($_REQUEST){
			$dates = $regs = array();
			foreach($_REQUEST as $n => $v){
				if(preg_match('/^we_schedule_([^\[]+)$/', $n, $regs)){
					$rest = $regs[1];
					$sw = explode('_', $rest);
					$nr = end($sw);
					if(!isset($this->schedArr[$nr])){
						$this->schedArr[$nr] = array();
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
								$dates[$nr] = array();
							}
							$dates[$nr][$sw[1]] = $v;
							break;
						default:
							if(substr($sw[0], 0, 5) == 'month'){
								$d = intval(substr($sw[0], 5));
								$this->schedArr[$nr]['months'][$d - 1] = $v;
							} else if(substr($sw[0], 0, 3) == 'day'){
								$d = intval(substr($sw[0], 3));
								$this->schedArr[$nr]['days'][$d - 1] = $v;
							} else if(substr($sw[0], 0, 4) == 'wday'){
								$d = intval(substr($sw[0], 4));
								$this->schedArr[$nr]['weekdays'][$d - 1] = $v;
							}
					}
				}
			}
			foreach($dates as $nr => $v){
				$this->schedArr[$nr]['time'] = mktime(
					$dates[$nr]['hour'], $dates[$nr]['minute'], 0, $dates[$nr]['month'], $dates[$nr]['day'], $dates[$nr]['year']);
			}
		}
		$this->Path = $this->getPath();
	}

	function add_schedcat($id, $nr){
		$cats = makeArrayFromCSV($this->schedArr[$nr]['CategoryIDs']);
		if(!in_array($id, $cats)){
			$cats[] = $id;
		}
		$this->schedArr[$nr]['CategoryIDs'] = makeCSVFromArray($cats, true);
	}

	function delete_schedcat($id, $nr){
		$cats = makeArrayFromCSV($this->schedArr[$nr]['CategoryIDs']);
		if(in_array($id, $cats)){
			$pos = array_search($id, $cats);
			if($pos !== false || $pos == '0'){
				array_splice($cats, $pos, 1);
			}
		}
		$this->schedArr[$nr]['CategoryIDs'] = makeCSVFromArray($cats, true);
	}

	// returns the next date when the document gets published
	function getNextPublishDate(){
		$times = array();
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
				$this->schedArr = array();
			}
			while($this->DB_WE->next_record()){
				$s = unserialize($this->DB_WE->f('Schedpro'));
				if(is_array($s)){
					$s['active'] = $this->DB_WE->f('Active');
					$this->schedArr[] = $s;
				}
			}
		}
	}

	/**
	 * returns	a select menu within a html table. to ATTENTION this function is also used in classes object and objectFile !!!!
	 * 			when $withHeadline is true, a table with headline is returned, default is false
	 * @return	select menue to determine charset
	 * @param	boolean
	 */
	function formCharset($withHeadline = false){
		$value = (isset($this->elements['Charset']['dat']) ? $this->elements['Charset']['dat'] : '');

		$_charsetHandler = new we_base_charsetHandler();

		$_charsets = $_charsetHandler->getCharsetsForTagWizzard();
		$_charsets[''] = '';
		asort($_charsets);
		reset($_charsets);

		$name = 'Charset';

		$inputName = 'we_' . $this->Name . "_txt[$name]";


		return '<table border="0" cellpadding="0" cellspacing="0">' .
			($withHeadline ? '<tr><td class="defaultfont">' . g_l('weClass', '[Charset]') . '</td></tr>' : '') .
			'<tr><td>' . $this->htmlTextInput($inputName, 24, $value) . '</td><td></td><td>' . $this->htmlSelect('we_tmp_' . $this->Name . '_select[' . $name . ']', $_charsets, 1, $value, false, array("onblur" => "_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd('reload_editpage');", "onchange" => "_EditorFrame.setEditorIsHot(true);document.forms[0].elements['" . $inputName . "'].value=this.options[this.selectedIndex].value;top.we_cmd('reload_editpage');"), "value", 330) . '</td></tr>' .
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
		return array();
	}

	/**
	 * @desc	the function modifies document EditPageNrs set
	 */
	function checkTabs(){
		if(!$this->canHaveVariants(true)){

			$ind = array_search(we_base_constants::WE_EDITPAGE_VARIANTS, $this->EditPageNrs);
			if(!empty($ind)){
				array_splice($this->EditPageNrs, $ind, 1);
			}
		}
	}

	private function i_deleteNavigation(){
		$this->DB_WE->query('DELETE FROM ' . NAVIGATION_TABLE . ' WHERE ' . we_navigation_navigation::getNavCondition($this->ID, $this->Table));
		return true;
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

	public static function parseInternalLinks(&$text, $pid, $path = '', $doBaseReplace = true){
		$doBaseReplace&=we_isHttps();
		$DB_WE = new DB_WE();
		$regs = array();
		if(preg_match_all('/(href|src)="' . we_base_link::TYPE_INT_PREFIX . '(\\d+)(&amp;|&)?("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				$foo = getHash('SELECT Path,(ContentType="' . we_base_ContentTypes::IMAGE . '") AS isImage  FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]) . (isset($GLOBALS['we_doc']->InWebEdition) && $GLOBALS['we_doc']->InWebEdition
							? '' : ' AND Published>0'), $DB_WE);

				if($foo && $foo['Path']){
					$path_parts = pathinfo($foo['Path']);
					if(show_SeoLinks() && WYSIWYGLINKS_DIRECTORYINDEX_HIDE && NAVIGATION_DIRECTORYINDEX_NAMES && in_array($path_parts['basename'], array_map('trim', explode(',', NAVIGATION_DIRECTORYINDEX_NAMES)))){
						$foo['Path'] = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
					}
					$text = str_replace($reg[1] . '="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . $reg[4], $reg[1] . '="' . ($doBaseReplace && $foo['isImage'] ? BASE_IMG
								: '') . $foo['Path'] . ($reg[3] ? '?' : '') . $reg[4], $text);
				} else {
					$text = preg_replace(array(
						'|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>(.*)</a>|Ui', '|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>|Ui', '|<img [^>]*src="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . '"[^>]*>|Ui'), array(
						'\1', '', ''), $text);
				}
			}
		}
		if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '([^" ]+)"/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(',', $reg[1]);
				$thumbObj = new we_thumbnail();
				if($thumbObj->initByImageIDAndThumbID($imgID, $thumbID)){
					$text = str_replace('src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '"', 'src="' . ($doBaseReplace ? BASE_IMG : '') . $thumbObj->getOutputPath(false, true) . '"', $text);
				} else {
					$text = preg_replace('|<img[^>]+src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '[^>]+>|Ui', '', $text);
				}
			}
		}
		if(defined('OBJECT_TABLE')){
			if(preg_match_all('/href="' . we_base_link::TYPE_OBJ_PREFIX . '(\d+)(\??)("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)){
				foreach($regs as $reg){
					$href = we_objectFile::getObjectHref($reg[1], $pid, $path, null, WYSIWYGLINKS_DIRECTORYINDEX_HIDE, WYSIWYGLINKS_OBJECTSEOURLS);
					if(isset($GLOBALS['we_link_not_published'])){
						unset($GLOBALS['we_link_not_published']);
					}
					if($href){
						$text = ($reg[2] == '?' ?
								str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '?', 'href="' . $href . '&amp;', $text) :
								str_replace('href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . $reg[2] . $reg[3], 'href="' . $href . $reg[2] . $reg[3], $text));
					} else {
						$text = preg_replace(array('|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>(.*)</a>|Ui',
							'|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>|Ui',), array('\1'), $text);
					}
				}
			}
		}

		return preg_replace('/\<a>(.*)\<\/a>/siU', '\1', $text);
	}

	private function getNavigationItems(){
		if($this->Table == FILE_TABLE && $this->ID && $this->InWebEdition){
			$this->DB_WE->query('SELECT Path FROM ' . NAVIGATION_TABLE . ' WHERE ((Selection="' . we_navigation_navigation::SELECTION_STATIC . '" AND SelectionType="' . we_navigation_navigation::STPYE_DOCLINK . '") OR (IsFolder=1)) AND LinkID=' . intval($this->ID));
			return $this->DB_WE->getAll(true);
		}
		return array();
	}

	public function showLockedWarning($userID){
		echo we_html_tools::getHtmlTop() . STYLESHEET .
		we_html_element::jsElement('top.toggleBusy(0);') .
		'</head>' . we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_tools::htmlDialogLayout('<p class="defaultfont">' . sprintf(g_l('alert', "[temporaere_no_access_text]"), $this->Text, f('SELECT username FROM ' . USER_TABLE . ' WHERE ID=' . intval($userID))) . '</p>', g_l('alert', "[temporaere_no_access]")) .
//	For SEEM-Mode
			($_SESSION['weS']['we_mode'] == we_base_constants::MODE_SEE ?
				'<a href="javascript://" style="text-decoration:none" onclick="top.weNavigationHistory.navigateReload()" >' . g_l('SEEM', "[try_doc_again]") . '</a>' : '')
		) .
		'</html>';
		exit();
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

}
