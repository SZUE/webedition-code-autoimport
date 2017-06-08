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

// FIXME: reduce all onchange JS to single function calls 

class we_docTypes extends we_contents_base{
	/* The Text that will be shown in the tree-menue */
	var $DocType = 'New DocType';
	var $Extension = DEFAULT_STATIC_EXT;
	var $ParentID = 0;
	var $ParentPath = '';
	var $TemplateID = 0;
	var $IsDynamic = false;
	var $IsSearchable = false;
	var $Templates = '';
	var $SubDir = self::SUB_DIR_NO;
	var $Category = '';
	var $Language = '';
	protected $IsHot = 0;

	public function __construct(){
		parent::__construct();
		array_push($this->persistent_slots, 'Category', 'DocType', 'Extension', 'ParentID', 'ParentPath', 'TemplateID', 'IsDynamic', 'IsSearchable', 'SubDir', 'Templates', 'Language', 'IsHot');
		$this->Table = DOC_TYPES_TABLE;
	}

	public function we_save($resave = false){
		$idArr = makeArrayFromCSV($this->Templates);
		$newIdArr = [];
		foreach($idArr as $id){
			$path = id_to_path($id, TEMPLATES_TABLE);
			if($id && $path){
				$newIdArr[] = $id;
			}
		}
		$this->Templates = implode(',', $newIdArr);

		if(LANGLINK_SUPPORT){
			if(($llink = we_base_request::_(we_base_request::STRING, 'we_' . $this->Name . '_LangDocType'))){
				$this->setLanguageLink($llink, 'tblDocTypes');
			}
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage(DOC_TYPES_TABLE, false);
		}

		return parent::we_save($resave);
	}

	public function we_save_exim(){
		return parent::we_save(false);
	}

	function saveInSession(&$save, $toFile = false){
		$save = [[]];
		foreach($this->persistent_slots as $cur){
			$save[0][$cur] = $this->{$cur};
		}
	}

	public function we_initSessDat($sessDat){
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
				$this->Language = ($GLOBALS['weDefaultFrontendLanguage'] ?: 'de_DE');
			} elseif(($h = getHash('SELECT Language,ParentID FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($ParentID), $this->DB_WE))){
				$this->Language = $h['Language'];
				$ParentID = $h['ParentID'];
			}
			$i++;
		}
	}

	private function formLangLinks(){
		$value = ($this->Language ?: $GLOBALS['weDefaultFrontendLanguage']);
		$inputName = 'we_' . $this->Name . '_Language';
		$languages = getWeFrontendLanguagesForBackend();

		if(LANGLINK_SUPPORT){
			$htmlzw = '';
			foreach($languages as $langkey => $lang){
				$LDID = f('SELECT LDID FROM ' . LANGLINK_TABLE . ' WHERE DocumentTable="tblDocTypes" AND DID=' . $this->ID . ' AND Locale="' . $langkey . '"', '', $this->DB_WE);
				$htmlzw .= $this->formDocTypes3($lang, $langkey, ($LDID ?: 0));
				$langkeys[] = $langkey;
			}
			return we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect($inputName, $languages, 1, $value, false, ['onchange' => 'we_cmd(\'setHot\');dieWerte=\'' . implode(',', $langkeys) . '\'; disableLangDefault(\'we_' . $this->Name . '_LangDocType\',dieWerte,this.options[this.selectedIndex].value);'], "value", 521), g_l('weClass', '[language]'), "left", "defaultfont") .
				we_html_element::htmlBr() . we_html_tools::htmlFormElementTable($htmlzw, g_l('weClass', '[languageLinksDefaults]'), 'left', 'defaultfont');
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect($inputName, $languages, 1, $value, false, ['onchange' => "we_cmd('setHot')"], "value", 521), g_l('weClass', '[language]'), "left", "defaultfont");
	}

	private function formCategory(we_base_jsCmd $jsCmd){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_category', -1, '" . CATEGORY_TABLE . "', '', '', 'dt_add_cat')", '', 0, 0, "", "", (!we_base_permission::hasPerm("EDIT_KATEGORIE")));

		$cats = new we_chooser_multiDir(521, $this->Category, "dt_delete_cat", $addbut, "", '"we/category"', CATEGORY_TABLE);
		return we_html_tools::htmlFormElementTable($cats->get($jsCmd), g_l('weClass', '[category]'));
	}

	public function addCat(array $ids){
		$this->Category = implode(',', array_unique(array_filter(array_map(intval, array_merge(explode(',', $this->Category), $ids))), SORT_NUMERIC));
	}

	public function delCat($id){
		$cats = array_filter(explode(',', $this->Category));
		if(($pos = array_search($id, $cats, false)) === false){
			return;
		}

		unset($cats[$pos]);
		$this->Category = implode(',', $cats);
	}

	/*
	 * Form functions for generating the html of the input fields
	 */

	public function formDocTypeHeader(){
		return $this->hiddenIsHot() .
'<table class="default">
	<tr>
		<td style="padding-right:20px;">' . $this->formDocTypes2() . '</td>
		<td><div style="padding-bottom:10px;">' . we_html_button::create_button('new_doctype', "javascript:we_cmd('newDocType')") . '</div>' . we_html_button::create_button('delete_doctype', "javascript:we_cmd('deleteDocType', '" . $this->ID . "')") . '</td>
	</tr>
</table>';
	}

	public function formName(){
		return $this->formInputField('', 'DocType', '', 24, 520, 32, 'onchange="we_cmd(\'setHot\')"');
	}

	public function formDocTypeTemplates(we_base_jsCmd $jsCmd){
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_selector_document', 0, '" . TEMPLATES_TABLE . "','','','add_dt_template', '', '', '" . we_base_ContentTypes::TEMPLATE . "', 1,1)");
		$templ = new we_chooser_multiDir(521, $this->Templates, "delete_dt_template", $addbut, "", 'ContentType', TEMPLATES_TABLE);
		return $templ->get($jsCmd);
	}

	public function formDocTypeDefaults(we_base_jsCmd $jsCmd){
		return '
<table class="default">
	<tr><td colspan="3" style="padding-bottom:5px;">' . $this->formDirChooser(409) . '</td></tr>
	<tr>
		<td style="padding-right:20px;">' . $this->formSubDir(300) . '</td>
		<td>' . $this->formExtension(200) . '</td>
	</tr>
	<tr><td colspan="3" style="padding:5px 0px;">' . $this->formTemplatePopup() . '</td></tr>
	<tr>
		<td>' . $this->formIsDynamic() . '</td>
		<td>' . $this->formIsSearchable() . '</td>
	</tr>
	<tr><td colspan="3" style="padding:5px 0px;">' . $this->formLangLinks(521) . '</td></tr>
	<tr><td colspan="3">' . $this->formCategory($jsCmd) . '</td></tr>
</table>';
	}

	/**
	 * @return string
	 * @param  array $arrHide
	 * @desc   returns HTML-Code for a doctype select-box without doctypes given in $array
	 * @return string
	 */
	private function formDocTypes2($arrHide = []){
		$vals = [];
		$dtq = we_docTypes::getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE ' . $dtq['where']);

		while($this->DB_WE->next_record()){
			$v = $this->DB_WE->f('ID');
			$t = $this->DB_WE->f('DocType');
			if(in_array($t, $arrHide)){
				continue;
			}
			$vals[$v] = $t;
		}
		return we_html_tools::htmlSelect("DocTypes", $vals, 8, $this->ID, false, ['style' => "width:328px", 'onchange' => 'we_cmd(\'change_docType\',this.options[this.selectedIndex].value)']);
	}

	private function formDocTypes3($headline, $langkey, $derDT = 0){
		$dtq = we_docTypes::getDoctypeQuery($this->DB_WE);
		$this->DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt LEFT JOIN ' . FILE_TABLE . ' dtf ON dt.ParentID=dtf.ID ' . $dtq['join'] . ' WHERE dt.Language="' . $langkey . '" AND ' . $dtq['where']);
		$vals = [0 => g_l('weClass', '[nodoctype]')];
		foreach($this->DB_WE->getAllFirst(false) as $k => $v){
			$vals[$k] = $v;
		}

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('we_' . $this->Name . "_LangDocType[" . $langkey . "]", $vals, 1, $derDT, false, [($langkey == $this->Language ? 'disabled' : null) => "disabled",
					'width' => 328, 'onchange' => "we_cmd('setHot')"]), $headline, "left", "defaultfont");
	}

	private function formDirChooser($width = 100){
		$weSuggest = & we_gui_suggest::getInstance();

		$textname = 'we_' . $this->Name . '_ParentPath';
		$idname = 'we_' . $this->Name . '_ParentID';
		$weSuggest->setAcId("Path");
		$weSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$weSuggest->setInput($textname, $this->ParentPath);
		$weSuggest->setLabel(g_l('weClass', '[dir]'));
		$weSuggest->setResult($idname, $this->ParentID);
		$weSuggest->setSelector(we_gui_suggest::DirSelector);
		$weSuggest->setWidth($width - 10);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory', document.we_form.elements['" . $idname . "'].value, '" . FILE_TABLE . "', '" . $idname . "', '" . $textname . "', 'setHot', '')"));

		return $weSuggest->getHTML();
	}

	private function formExtension($width = 100){
		$exts = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT);
		return we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup('we_' . $this->Name . '_Extension', $this->Extension, $exts, $width, 'onchange="we_cmd(\'setHot\')"'), g_l('weClass', '[extension]'));
	}

	/* creates the Template PopupMenue */

	private function formTemplatePopup(){
		$tlist = [];
		if($this->TemplateID){
			$tlist[] = $this->TemplateID;
		}
		if($this->Templates){
			$tlist = array_merge($tlist, explode(',', $this->Templates));
		}
		$tlist = array_filter(array_unique($tlist));
		$sqlTail = 'IsFolder=0 ' . ($tlist ? 'AND ID IN(' . implode(',', $tlist) . ')' : ' AND false' );
		return $this->formSelect2(0, 'TemplateID', TEMPLATES_TABLE, 'ID,Path', g_l('weClass', '[standard_template]'), $sqlTail, 1, $this->TemplateID, false, '', ['onchange' => "we_cmd('setHot')"], 'left', 'defaultfont', '', '', [
				0, g_l('weClass', '[none]')]);
	}

	private function formIsDynamic(){
		$n = 'we_' . $this->Name . '_IsDynamic';
		return we_html_forms::checkbox(1, $this->IsDynamic, "check_" . $n, g_l('weClass', '[IsDynamic]'), true, "defaultfont", "we_cmd('setHot');this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0');WE().layout.propEdit.switchExt(document," . intval($this->ID) . ",'" . $this->Name . "');");
	}

	private function hiddenIsHot(){
		return we_html_element::htmlHidden('isHotName', 'we_' . $this->Name . '_IsHot') . // FIXME: fast and dirty: handle Name over to editor js
				we_html_element::htmlHidden('we_' . $this->Name . '_IsHot', $this->IsHot);
	}

	private function formIsSearchable(){
		$n = 'we_' . $this->Name . '_IsSearchable';
		return we_html_forms::checkbox(1, $this->IsSearchable, 'check_' . $n, g_l('weClass', '[IsSearchable]'), false, 'defaultfont', "we_cmd('setHot');this.form.elements['" . $n . "'].value = (this.checked ? '1' : '0');") . we_html_element::htmlHidden($n, ($this->IsSearchable ? 1 : 0));
	}

	private function formSubDir($width = 100){
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('we_' . $this->Name . '_SubDir', g_l('weClass', '[subdir]'), 1, $this->SubDir, false, ['onchange' => "we_cmd('setHot')"], 'value', $width), g_l('weClass', '[subdirectory]'));
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
		$db = $db ?: new DB_WE();

		$paths = [];
		$ws = get_ws(FILE_TABLE, true);
		if(!$ws){
			return ['join' => '',
				'where' => '1 ORDER BY dt.DocType'
			];
		}
		if(WE_DOCTYPE_WORKSPACE_BEHAVIOR){
			return ['join' => 'LEFT JOIN ' . FILE_TABLE . ' f ON (CONCAT(f.Path,"/") LIKE CONCAT(dtf.Path,"/%") AND f.ID IN(' . implode(',', $ws) . ') AND f.IsFolder=1 )',
				'where' => 'ISNULL(dtf.ID) OR !ISNULL(f.ID) ORDER BY dt.DocType'
			];
		}

		$db->query('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID IN(' . implode(',', $ws) . ')');
		while($db->next_record()){
			$paths[] = 'dtf.Path LIKE "' . $db->escape($db->f('Path')) . '/%"';
		}
		return ($paths ?
			['join' => '',
			'where' => '(dt.ParentID IN(' . implode(',', $ws) . ') OR ' . implode(' OR ', $paths) . ' OR ISNULL(dtf.ID)) ORDER BY dt.DocType'
			] : ['join' => '',
			'where' => '1 ORDER BY dt.DocType'
		]);
	}

	public static function getJSLangConsts(){
		return '
	WE().consts.g_l.doctypeEdit=JSON.parse("' . we_base_util::setLangString([
				'doctype_empty' => (g_l('alert', '[doctype_empty]')),
				'doctype_exists' => (g_l('alert', '[doctype_exists]')),
				'doctype_hochkomma' => (g_l('alert', '[doctype_hochkomma]')),
				'newDocTypeName' => g_l('weClass', '[newDocTypeName]'),
				]) . '");';
	}

}
