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
class we_docTypes extends we_class{
	/* The Text that will be shown in the tree-menue */
	var $DocType = 'New DocType';
	var $Extension = DEFAULT_STATIC_EXT;
	var $ParentID = 0;
	var $ParentPath = '';
	var $TemplateID = 0;
	var $ContentTable = '';
	var $IsDynamic = false;
	var $IsSearchable = false;
	var $Templates = '';
	var $SubDir = self::SUB_DIR_NO;
	var $Category = '';
	var $Language = '';

	public function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Category', 'DocType', 'Extension', 'ParentID', 'ParentPath', 'TemplateID', 'ContentTable', 'IsDynamic', 'IsSearchable', 'SubDir', 'Templates', 'Language');
		$this->Table = DOC_TYPES_TABLE;
	}

	public function we_save($resave = false){
		$idArr = makeArrayFromCSV($this->Templates);
		$newIdArr = array();
		foreach($idArr as $id){
			$path = id_to_path($id, TEMPLATES_TABLE);
			if($id && $path){
				$newIdArr[] = $id;
			}
		}
		$this->Templates = implode(',', $newIdArr);

		if(LANGLINK_SUPPORT){
			if(($llink = we_base_request::_(we_base_request::RAW, "we_" . $this->Name . "_LangDocType"))){
				$this->setLanguageLink($llink, 'tblDocTypes');
			}
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, false);
		}

		return we_class::we_save($resave);
	}

	public function we_save_exim(){
		return parent::we_save(false);
	}

	function saveInSession(&$save){
		$save = array(array());
		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}
	}

	public function we_initSessDat($sessDat){
		we_class::we_initSessDat($sessDat);
		if(is_array($sessDat)){
			foreach($this->persistent_slots as $cur){
				if(isset($sessDat[0][$cur])){
					$this->{$cur} = $sessDat[0][$cur];
				}
			}
		}
		$this->i_setElementsFromHTTP();

		if(!$this->Language){
			$this->initLanguageFromParent();
		}
	}

	private function initLanguageFromParent(){
		$ParentID = $this->ParentID;
		$i = 0;
		while(!$this->Language){
			if($ParentID == 0 || $i > 20){
				we_loadLanguageConfig();
				$this->Language = ($GLOBALS['weDefaultFrontendLanguage'] ? : 'de_DE');
			} elseif(($h = getHash('SELECT Language,ParentID FROM ' . $this->DB_WE->escape($this->Table) . ' WHERE ID=' . intval($ParentID), $this->DB_WE))){
				$this->Language = $h['Language'];
				$ParentID = $h['ParentID'];
			}
			$i++;
		}
	}

	private function formLanguage(){
		we_loadLanguageConfig();

		$value = ($this->Language ? : $GLOBALS['weDefaultFrontendLanguage']);
		$inputName = 'we_' . $this->Name . '_Language';
		$_languages = getWeFrontendLanguagesForBackend();

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($_languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND DID=' . $this->ID . ' AND Locale="' . $langkey . '"', '', $this->DB_WE);
				$htmlzw.= $this->formDocTypes3($lang, $langkey, ($LDID ? : 0));
				$langkeys[] = $langkey;
			}
			return we_html_tools::htmlFormElementTable($this->htmlSelect($inputName, $_languages, 1, $value, false, array('onchange' => 'dieWerte=\'' . implode(',', $langkeys) . '\'; disableLangDefault(\'we_' . $this->Name . '_LangDocType\',dieWerte,this.options[this.selectedIndex].value);'), "value", 521), g_l('weClass', '[language]'), "left", "defaultfont") .
				we_html_element::htmlBr() . we_html_tools::htmlFormElementTable($htmlzw, g_l('weClass', '[languageLinksDefaults]'), 'left', 'defaultfont');
		}
		return we_html_tools::htmlFormElementTable($this->htmlSelect($inputName, $_languages, 1, $value, false, array(), "value", 521), g_l('weClass', '[language]'), "left", "defaultfont");
	}

	private function formCategory(){
		$addbut = we_html_button::create_button("add", "javascript:we_cmd('openCatselector', -1, '" . CATEGORY_TABLE . "', '', '', 'fillIDs();opener.we_cmd(\\'dt_add_cat\\', top.allIDs);')", false, 92, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));

		$cats = new we_chooser_multiDir(521, $this->Category, "dt_delete_cat", $addbut, "", "Icon,Path", CATEGORY_TABLE);
		return we_html_tools::htmlFormElementTable($cats->get(), g_l('weClass', '[category]'));
	}

	public function addCat(array $ids){
		$cats = makeArrayFromCSV($this->Category);
		foreach($ids as $id){
			if($id && (!in_array($id, $cats))){
				$cats[] = $id;
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	public function delCat($id){
		$cats = makeArrayFromCSV($this->Category);
		if(in_array($id, $cats)){
			$pos = array_search($id, $cats);
			if($pos !== false || $pos == '0'){
				array_splice($cats, $pos, 1);
			}
		}
		$this->Category = makeCSVFromArray($cats, true);
	}

	/*
	 * Form functions for generating the html of the input fields
	 */

	public function formDocTypeHeader(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td>' . $this->formDocTypes2() . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . $this->formNewDocType() . we_html_tools::getPixel(2, 10) . $this->formDeleteDocType() . '</td>
	</tr>
</table>';
	}

	public function formName(){
		return $this->formInputField('', 'DocType', '', 24, 520, 32);
	}

	public function formDocTypeTemplates(){
		$wecmdenc3 = we_base_request::encCmd("fillIDs();opener.we_cmd('add_dt_template', top.allIDs);");
		$addbut = we_html_button::create_button("add", "javascript:we_cmd('openDocselector', 0, '" . TEMPLATES_TABLE . "','','','" . $wecmdenc3 . "', '', '', '" . we_base_ContentTypes::TEMPLATE . "', 1,1)");
		$templ = new we_chooser_multiDir(521, $this->Templates, "delete_dt_template", $addbut, "", "Icon,Path", TEMPLATES_TABLE);
		return $templ->get();
	}

	public function formDocTypeDefaults(){
		return '
<table border="0" cellpadding="0" cellspacing="0">
	<tr><td colspan="3">' . $this->formDirChooser(we_base_browserDetect::isIE() ? 403 : 409) . '</td></tr>
	<tr><td>' . we_html_tools::getPixel(300, 5) . '</td>
		<td>' . we_html_tools::getPixel(20, 5) . '</td>
		<td>' . we_html_tools::getPixel(200, 5) . '</td>
	</tr>
	<tr>
		<td>' . $this->formSubDir(300) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . $this->formExtension(200) . '</td>
	</tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formTemplatePopup(521) . '</td></tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr>
		<td>' . $this->formIsDynamic() . '</td>
		<td></td>
		<td>' . $this->formIsSearchable() . '</td>
	</tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formLanguage(521) . '</td></tr>
	<tr><td colspan="3">' . we_html_tools::getPixel(2, 5) . '</td></tr>
	<tr><td colspan="3">' . $this->formCategory(521) . '</td></tr>
</table>';
	}

	/**
	 * @return string
	 * @param  array $arrHide
	 * @desc   returns HTML-Code for a doctype select-box without doctypes given in $array
	 * @return string
	 */
	private function formDocTypes2($arrHide = array()){
		$vals = array();
		$dtq = we_docTypes::getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN tblFile dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);

		while($this->DB_WE->next_record()){
			$v = $this->DB_WE->f('ID');
			$t = $this->DB_WE->f('DocType');
			if(in_array($t, $arrHide)){
				continue;
			}
			$vals[$v] = $t;
		}
		return $this->htmlSelect("DocTypes", $vals, 8, $this->ID, false, array('style' => "width:328px", 'onchange' => 'we_cmd(\'change_docType\',this.options[this.selectedIndex].value)'));
	}

	private function formDocTypes3($headline, $langkey, $derDT = 0){
		$dtq = we_docTypes::getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN tblFile dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE dt.Language="' . $langkey . '" AND ' . $dtq['where']);
		$vals = array(0 => g_l('weClass', '[nodoctype]'));
		foreach($this->DB_WE->getAllFirst(false) as $k => $v){
			$vals[$k] = $v;
		}

		return we_html_tools::htmlFormElementTable($this->htmlSelect('we_' . $this->Name . "_LangDocType[" . $langkey . "]", $vals, 1, $derDT, false, array(($langkey == $this->Language ? 'disabled' : null) => "disabled", 'width' => 328, 'onchange' => '')), $headline, "left", "defaultfont");
	}

	private function formDirChooser($width = 100){
		$yuiSuggest = & weSuggest::getInstance();

		$textname = 'we_' . $this->Name . '_ParentPath';
		$idname = 'we_' . $this->Name . '_ParentID';

		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['" . $textname . "'].value");
		$button = we_html_button::create_button("select", "javascript:we_cmd('openDirselector', document.forms['we_form'].elements['" . $idname . "'].value, '" . FILE_TABLE . "', '" . $wecmdenc1 . "', '" . $wecmdenc2 . "', '', '')");
		$yuiSuggest->setAcId("Path");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput($textname, $this->ParentPath);
		$yuiSuggest->setLabel(g_l('weClass', '[dir]'));
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult($idname, $this->ParentID);
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth($width - (we_base_browserDetect::isIE() ? 0 : 10));
		$yuiSuggest->setSelectButton($button);

		return $yuiSuggest->getHTML();
	}

	private function formExtension($width = 100){
		$exts = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT);
		return we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup('we_' . $this->Name . '_Extension', $this->Extension, $exts, $width), g_l('weClass', '[extension]'));
	}

	/* creates the Template PopupMenue */

	private function formTemplatePopup($width = 100){
		$tlist = array();
		if($this->TemplateID){
			$tlist[] = $this->TemplateID;
		}
		if($this->Templates){
			$tlist = array_merge($tlist, explode(',', $this->Templates));
		}
		$tlist = array_filter(array_unique($tlist));
		$sqlTeil = 'IsFolder=0 ' . ($tlist ? 'AND ID IN(' . implode(',', $tlist) . ')' : ' AND false' );
		return $this->formSelect2($width, 'TemplateID', TEMPLATES_TABLE, 'ID', 'Path', g_l('weClass', '[standard_template]'), $sqlTeil, 1, $this->TemplateID, false, '', array(), 'left', 'defaultfont', '', '', array(0, g_l('weClass', '[none]')));
	}

	private function formIsDynamic(){
		$n = 'we_' . $this->Name . '_IsDynamic';

		return we_html_forms::checkbox(1, $this->IsDynamic, "check_" . $n, g_l('weClass', '[IsDynamic]'), true, "defaultfont", "this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0'); switchExt();") . $this->htmlHidden($n, ($this->IsDynamic ? 1 : 0)) .
			we_html_element::jsElement('
function switchExt(){
	var a=document.we_form.elements;' .
				($this->ID ?
					'if(confirm("' . g_l('weClass', '[confirm_ext_change]') . '")){' : '') .
				'if(a["we_' . $this->Name . '_IsDynamic"].value==1) {var changeto="' . DEFAULT_DYNAMIC_EXT . '";} else {var changeto="' . DEFAULT_STATIC_EXT . '";}
	a["we_' . $this->Name . '_Extension"].value=changeto;' .
				($this->ID ? '}' : '') . '
}');
	}

	private function formIsSearchable(){
		$n = 'we_' . $this->Name . '_IsSearchable';
		return we_html_forms::checkbox(1, $this->IsSearchable, 'check_' . $n, g_l('weClass', '[IsSearchable]'), false, 'defaultfont', "this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0');") . $this->htmlHidden($n, ($this->IsSearchable ? 1 : 0));
	}

	private function formSubDir($width = 100){
		return we_html_tools::htmlFormElementTable($this->htmlSelect('we_' . $this->Name . '_SubDir', g_l('weClass', '[subdir]'), 1, $this->SubDir, false, array(), 'value', $width), g_l('weClass', '[subdirectory]'));
	}

	public function formNewDocType(){
		return we_html_button::create_button('new_doctype', "javascript:we_cmd('newDocType')");
	}

	private function formDeleteDocType(){
		return we_html_button::create_button('delete_doctype', "javascript:we_cmd('deleteDocType', '" . $this->ID . "')");
	}

	/**
	 * Returns "where query" for Doctypes depending on which workspace the user have
	 *
	 * @param	object	$db
	 *
	 *
	 * @return         string
	 */
	public static function getDoctypeQuery(we_database_base $db = null){
		$db = $db ? : new DB_WE();

		$paths = array();
		$ws = get_ws(FILE_TABLE, false, true);
		if(!$ws){
			return array(
				'join' => '',
				'where' => '1 ORDER BY dt.DocType'
			);
		}
		if(WE_DOCTYPE_WORKSPACE_BEHAVIOR){
			return array(
				'join' => 'LEFT JOIN ' . FILE_TABLE . ' f ON CONCAT(f.Path,"/") LIKE CONCAT(dtf.Path,"/%")',
				'where' => 'ISNULL(dtf.ID) OR (f.ID IN(' . implode(',', $ws) . ') AND f.IsFolder=1) ORDER BY dt.DocType'
			);
		}

		$db->query('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $ws) . ')');
		while($db->next_record()){
			$paths[] = 'dtf.Path LIKE "' . $db->escape($db->f('Path')) . '/%"';
		}
		return ($paths ?
				array(
				'join' => '',
				'where' => '(dt.ParentID IN(' . implode(',', $ws) . ') OR ' . implode(' OR ', $paths) . ' OR ISNULL(dtf.ID)) ORDER BY dt.DocType'
				) : array(
				'join' => '',
				'where' => '1 ORDER BY dt.DocType'
		));
	}

}
