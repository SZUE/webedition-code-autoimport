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
 * @package    webEdition_class
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_webEditionDocument extends we_textContentDocument{

	// ID of the templates that is used from the document
	var $TemplateID = 0;
	// ID of the template that is used from the parked document (Bug Fix #6615)
	var $temp_template_id = 0;
	// Categories of the parked document (Bug Fix #6615)
	var $temp_category = '';
	// Doc-Type of the parked document (Bug Fix #6615)
	var $temp_doc_type = '';
	// Path from the template
	var $TemplatePath = '';
	var $hasVariants = null;
	// Paths to stylesheets from we:css-tags that are user by tinyMCE
	private $DocumentCss = '';
	protected $usedElementNames = array();

	/**
	 * @var we_customer_documentFilter
	 */
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !!!!

	function __construct(){
		parent::__construct();
		if(defined('SHOP_TABLE')){
			$this->EditPageNrs[] = WE_EDITPAGE_VARIANTS;
		}

		if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
			$this->EditPageNrs[] = WE_EDITPAGE_WEBUSER;
		}

		if(isset($_SESSION['prefs']['DefaultTemplateID'])){
			$this->TemplateID = $_SESSION['prefs']['DefaultTemplateID'];
		}
		array_push($this->persistent_slots, 'TemplateID', 'TemplatePath', 'hidePages', 'controlElement', 'temp_template_id', 'temp_doc_type', 'temp_category', 'usedElementNames');
		$this->Icon = 'we_dokument.gif';
		$this->ContentType = we_base_ContentTypes::WEDOCUMENT;
	}

	public static function initDocument($formname = 'we_global_form', $tid = 0, $doctype = '', $categories = '', $wewrite = false){
		//  check if a <we:sessionStart> Tag was before
		$session = isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START'];

		if(!(isset($GLOBALS['we_document']) && is_array($GLOBALS['we_document']))){
			$GLOBALS['we_document'] = array();
		}
		$GLOBALS['we_document'][$formname] = new we_webEditionDocument();
		if((!$session) || (!isset($_SESSION['weS']['we_document_session_' . $formname])) || $wewrite){
			if($session){
				$_SESSION['weS']['we_document_session_' . $formname] = array();
			}
			$GLOBALS['we_document'][$formname]->we_new();
			if(($id = weRequest('int', 'we_editDocument_ID', 0))){
				$GLOBALS['we_document'][$formname]->initByID($id, FILE_TABLE);
			} else {
				$dt = f('SELECT ID FROM ' . DOC_TYPES_TABLE . " WHERE DocType LIKE '" . $GLOBALS['we_document'][$formname]->DB_WE->escape($doctype) . "'", '', $GLOBALS['we_document'][$formname]->DB_WE);
				$GLOBALS['we_document'][$formname]->changeDoctype($dt);
				if($tid){
					$GLOBALS['we_document'][$formname]->setTemplateID($tid);
				}
				if(strlen($categories)){
					$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
					$GLOBALS['we_document'][$formname]->Category = $categories;
				}
			}
			if($session){
				$GLOBALS['we_document'][$formname]->saveInSession($_SESSION['weS']['we_document_session_' . $formname]);
			}
		} else {
			if(($id = weRequest('string', 'we_editDocument_ID'))){
				$GLOBALS['we_document'][$formname]->initByID($id, FILE_TABLE);
			} elseif($session){
				$GLOBALS['we_document'][$formname]->we_initSessDat($_SESSION['weS']['we_document_session_' . $formname]);
			}
			if(strlen($categories)){
				$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
				$GLOBALS['we_document'][$formname]->Category = $categories;
			}
		}

		if(($ret = weRequest('string', 'we_returnpage'))){
			$GLOBALS['we_document'][$formname]->setElement('we_returnpage', $ret);
		}
		if(isset($_REQUEST['we_ui_' . $formname]) && is_array($_REQUEST['we_ui_' . $formname])){
			we_base_util::convertDateInRequest($_REQUEST['we_ui_' . $formname], true);
			foreach($_REQUEST['we_ui_' . $formname] as $n => $v){
				$v = we_base_util::rmPhp($v);
				$GLOBALS['we_document'][$formname]->setElement($n, $v);
			}
		}

		if(isset($_REQUEST['we_ui_' . $formname . '_categories'])){
			$cats = $_REQUEST['we_ui_' . $formname . '_categories'];
			// Bug Fix #750
			if(is_array($cats)){
				$cats = implode(',', $cats);
			}
			$cats = makeIDsFromPathCVS($cats, CATEGORY_TABLE);
			$GLOBALS['we_document'][$formname]->Category = $cats;
		}
		if(isset($_REQUEST['we_ui_' . $formname . '_Category'])){
			$_REQUEST['we_ui_' . $formname . '_Category'] = (is_array($_REQUEST['we_ui_' . $formname . '_Category']) ?
					makeCSVFromArray($_REQUEST['we_ui_' . $formname . '_Category'], true) :
					makeCSVFromArray(makeArrayFromCSV($_REQUEST['we_ui_' . $formname . '_Category']), true));
		}
		foreach($GLOBALS['we_document'][$formname]->persistent_slots as $slotname){
			if($slotname != 'categories' && isset($_REQUEST['we_ui_' . $formname . '_' . $slotname])){
				$GLOBALS["we_document"][$formname]->$slotname = $_REQUEST["we_ui_" . $formname . "_" . $slotname];
			}
		}

		we_imageDocument::checkAndPrepare($formname, 'we_document');
		we_flashDocument::checkAndPrepare($formname, 'we_document');
		we_quicktimeDocument::checkAndPrepare($formname, 'we_document');
		we_otherDocument::checkAndPrepare($formname, 'we_document');

		if($session){
			$GLOBALS['we_document'][$formname]->saveInSession($_SESSION['weS']['we_document_session_' . $formname]);
		}
		return $GLOBALS['we_document'][$formname];
	}

	function makeSameNew(){
		$TemplateID = $this->TemplateID;
		$TemplatePath = $this->TemplatePath;
		$IsDynamic = $this->IsDynamic;
		parent::makeSameNew();
		$this->TemplateID = $TemplateID;
		$this->TemplatePath = $TemplatePath;
		$this->IsDynamic = $IsDynamic;
	}

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return 'we_templates/we_editor_properties.inc.php';
			case WE_EDITPAGE_INFO:
				if(isset($GLOBALS['WE_MAIN_DOC'])){
					$GLOBALS["WE_MAIN_DOC"]->InWebEdition = true; //Bug 3417
				}
				return 'we_templates/we_editor_info.inc.php';

			case WE_EDITPAGE_CONTENT:
				$GLOBALS['we_editmode'] = true;
				break;
			case WE_EDITPAGE_PREVIEW:
				$GLOBALS['we_editmode'] = false;
				break;
			case WE_EDITPAGE_VALIDATION:
				return 'we_templates/validateDocument.inc.php';
			case WE_EDITPAGE_VARIANTS:
				return 'we_templates/we_editor_variants.inc.php';
			case WE_EDITPAGE_WEBUSER:
				return 'we_modules/customer/editor_weDocumentCustomerFilter.inc.php';
			default:
				return parent::editor();
		}
		return preg_replace('/.tmpl$/i', '.php', $this->TemplatePath); // .tmpl mod
	}

	/*
	 * Form functions for generating the html of the input fields
	 */

	function formIsDynamic($disabled = false){
		$v = $this->IsDynamic;
		if(!$disabled){
			$n = "we_" . $this->Name . "_IsDynamic";
			return we_html_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass', "[IsDynamic]"), false, "defaultfont", "_EditorFrame.setEditorIsHot(true);switchExt();") . we_html_element::jsElement(
					'function switchExt() {' .
					(!$this->Published ?
						'var a=document.we_form.elements;' .
						($this->ID ? 'if(confirm("' . g_l('weClass', "[confirm_ext_change]") . '")){' : '') . '
					if(a["we_' . $this->Name . '_IsDynamic"].value==1){ var changeto="' . DEFAULT_DYNAMIC_EXT . '"; }else{ var changeto="' . DEFAULT_STATIC_EXT . '";}
					a["we_' . $this->Name . '_Extension"].value=changeto;' .
						($this->ID ? '}' : '') : '') .
					'}'
			);
		} else {
			return we_html_forms::checkboxWithHidden($v ? true : false, '', g_l('weClass', "[IsDynamic]"), false, "defaultfont", "", true);
		}
	}

	function formDocTypeTempl(){
		$disable = (permissionhandler::hasPerm('EDIT_DOCEXTENSION') ?
				(($this->ContentType == we_base_ContentTypes::HTML || $this->ContentType == we_base_ContentTypes::WEDOCUMENT) && $this->Published) :
				true);

		return '
<table style="border-spacing: 0px;border-style:none;" cellpadding="0">
	<tr>
		<td colspan="3" class="defaultfont" align="left">
			' . $this->formDocType2(388, ($this->Published > 0)) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 4) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . we_html_tools::getPixel(100, 2) . '</td>
	</tr>
	<tr>
		<td colspan="3" class="defaultfont" align="left">' . $this->formTemplatePopup(388, ($this->Published > 0)) . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(20, 4) . '</td>
		<td>' . we_html_tools::getPixel(20, 2) . '</td>
		<td>' . we_html_tools::getPixel(100, 2) . '</td>
	</tr>
	<tr>
		<td colspan="3">
			<table style="border-spacing: 0px;border-style:none" cellpadding="0">
				<tr>
					<td>' . $this->formIsDynamic($disable) . '</td>
					<td class="defaultfont">&nbsp;</td>
					<td>' . $this->formIsSearchable() . '</td>
				</tr>
				<tr>
					<td>' . $this->formInGlossar(100) . '</td>
				</tr>
			</table></td>
	</tr></table>';
	}

	function formTemplateWindow(){
		$yuiSuggest = & weSuggest::getInstance();
		$table = TEMPLATES_TABLE;
		$textname = 'we_' . $this->Name . '_TemplateName';
		$idname = 'we_' . $this->Name . '_TemplateID';
		$ueberschrift = g_l('weClass', '[template]');

		if($this->TemplateID > 0){
			$styleTemplateLabel = 'display:none';
			$styleTemplateLabelLink = 'display:inline';
		} else {
			$styleTemplateLabel = 'display:inline';
			$styleTemplateLabelLink = 'display:none';
		}
		$myid = $this->TemplateID ? $this->TemplateID : '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE);
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('reload_editpage');");

		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','" . we_base_ContentTypes::TEMPLATE . "',1)");
		$yuiSuggest->setAcId('Template');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel("<span id='TemplateLabel' style='" . $styleTemplateLabel . "'>" . $ueberschrift . "</span><span id='TemplateLabelLink' style='" . $styleTemplateLabelLink . "'>" . $ueberschrift . "</span>");
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		$yuiSuggest->setDoOnTextfieldBlur("if(yuiAcFields[yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')) top.we_cmd('reload_editpage')");

		return $yuiSuggest->getHTML();
	}

	// creates the Template PopupMenue
	function formTemplatePopup($leftsize, $disable){
		if($this->DocType){
			$templateFromDoctype = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($this->DocType) . ' LIMIT 1', 'Templates', $this->DB_WE);
		}
		if($disable){
			$myid = intval($this->TemplateID ? $this->TemplateID : 0);
			$path = ($myid ? f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . $myid, '', $this->DB_WE) : '');

			$ueberschrift = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
					'<a href="javascript:goTemplate(' . $myid . ')">' . g_l('weClass', '[template]') . '</a>' :
					g_l('weClass', '[template]'));

			if($this->DocType){
				return (empty($templateFromDoctype) ?
						we_html_tools::htmlFormElementTable($path, g_l('weClass', '[template]'), 'left', 'defaultfont') :
						$this->xformTemplatePopup(388));
			}
			$pop = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
					'<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $path . '</td><td>' . we_html_tools::getPixel(20, 2) . '</td><td>' .
					we_html_button::create_button('edit', 'javascript:goTemplate(' . $myid . ')') .
					'</td></tr></table>' :
					$path);

			return we_html_tools::htmlFormElementTable($pop, g_l('weClass', '[template]'), 'left', 'defaultfont');
		}

		if($this->DocType){

			// if a Doctype is set and this Doctype has defined some templates, just show a select box
			return (empty($templateFromDoctype) ?
					$this->formTemplateWindow() :
					$this->xformTemplatePopup(388));
		}
		return $this->formTemplateWindow();
	}

	function xformTemplatePopup($width = 50){
		$ws = get_ws(TEMPLATES_TABLE);

		$fieldname = 'we_' . $this->Name . '_TemplateID';

		list($TID, $Templates) = getHash('SELECT TemplateID,Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID =' . intval($this->DocType), $this->DB_WE, MYSQL_NUM);
		$tlist = '';
		if($TID != ''){
			$tlist = $TID;
		}
		if($Templates != ''){
			$tlist.=',' . $Templates;
		}
		if($tlist){
			$temps = explode(',', $tlist);
			if(in_array($this->TemplateID, $temps)){
				$TID = $this->TemplateID;
			}
			$tlist = implode(',', array_unique($temps));
		} else {
			$foo = array();
			$wsArray = makeArrayFromCSV($ws);
			foreach($wsArray as $wid){
				pushChilds($foo, $wid, TEMPLATES_TABLE, 0);
			}
			$tlist = makeCSVFromArray($foo);
		}
		if($this->TemplateID){
			$tlist = $tlist ? ($tlist . ',' . $this->TemplateID) : $this->TemplateID;
			$TID = $this->TemplateID;
		}

		$openButton = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ? we_html_button::create_button('edit', 'javascript:goTemplate(document.we_form.elements[\'' . $fieldname . '\'].options[document.we_form.elements[\'' . $fieldname . '\'].selectedIndex].value)') : '');

		if(!empty($tlist)){
			$foo = array();
			$arr = makeArrayFromCSV($tlist);
			foreach($arr as $tid){
				if(($tid == $this->TemplateID) || in_workspace($tid, $ws, TEMPLATES_TABLE)){
					$foo[] = $tid;
				}
			}
			$tlist = $foo ? implode(',', $foo) : -1;
			return $this->formSelect4($width, 'TemplateID', TEMPLATES_TABLE, 'ID', 'Path', g_l('weClass', '[template]'), ' WHERE ID IN (' . $tlist . ') AND IsFolder=0 ORDER BY Path', 1, $TID, false, "we_cmd('template_changed');_EditorFrame.setEditorIsHot(true);", array(), 'left', 'defaultfont', '', $openButton, array(0, ''));
		}
		return $this->formSelect2($width, 'TemplateID', TEMPLATES_TABLE, 'ID', 'Path', g_l('weClass', '[template]'), 'WHERE IsFolder=0 ORDER BY Path ', 1, $this->TemplateID, false, '_EditorFrame.setEditorIsHot(true);', array(), 'left', 'defaultfont', '', $openButton);
	}

	/**
	 * @return string
	 * @desc Returns the metainfos for the selected file.
	 */
	function formMetaInfos(){
		//	Collect data from meta-tags
		$_code = $this->getTemplateCode();

		we_tag_tagParser::getMetaTags($_code);

		//	if a meta-tag is set all information are in array $GLOBALS["meta"]
		return '
<table style="border-spacing: 0px;border-style:none" cellpadding="0">
	<tr>
		<td colspan="2">' . $this->formInputField("txt", "Title", g_l('weClass', "[Title]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(2, 4) . '</td>
	</tr>
	<tr>
		<td colspan="2">' .
			$this->formInputField("txt", "Description", g_l('weClass', "[Description]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::getPixel(2, 4) . '</td>
	</tr>
	<tr>
		<td colspan="2">' . $this->formInputField("txt", "Keywords", g_l('weClass', "[Keywords]"), 40, 508, "", "onchange=\"_EditorFrame.setEditorIsHot(true);\"") . '</td>
	</tr>' .
			$this->getCharsetSelect() .
			$this->formLanguage(true) .
			'</table>';
	}

	/**
	 * This function returns the selector of the charset.
	 * @return string
	 */
	function getCharsetSelect(){
		$_charsetHandler = new we_base_charsetHandler();

		if(isset($GLOBALS["meta"]["Charset"])){ //	charset-tag available
			$name = 'Charset';

			//	This is the input field for the charset
			$inputName = 'we_' . $this->Name . "_txt[$name]";

			$chars = explode(',', $GLOBALS["meta"]["Charset"]["defined"]);

			//	input field - check value
			$value = ($this->getElement($name) ?
					$this->getElement($name) :
					(isset($GLOBALS["meta"][$name]) ?
						$GLOBALS["meta"][$name]["default"] :
						''));

			$retInput = $this->htmlTextInput($inputName, 40, $value, '', ' readonly ', 'text', 254);

			//	menu for all possible charsets

			$_defaultInChars = false;
			foreach($chars as $set){ //	check if default value is already in array
				if(strtolower($set) == strtolower(DEFAULT_CHARSET)){
					$_defaultInChars = true;
				}
			}
			if(!$_defaultInChars){
				$chars[] = DEFAULT_CHARSET;
			}

			//	Last step: get Information about the charsets
			$retSelect = $this->htmlSelect('we_tmp_' . $name, $_charsetHandler->getCharsetsByArray($chars), 1, $value, false, array('onblur' => '_EditorFrame.setEditorIsHot(true);document.forms[0].elements[\'' . $inputName . '\'].value=this.options[this.selectedIndex].value;', 'onchange' => '_EditorFrame.setEditorIsHot(true);document.forms[0].elements[\'' . $inputName . '\'].value=this.options[this.selectedIndex].value;'), 'value', 254);

			return '<tr><td colspan="2">' . we_html_tools::getPixel(2, 4) . '</td></tr>
<tr><td>
	<table style="border-spacing: 0px;border-style:none" cellpadding="0">
		<tr><td colspan="2" class="defaultfont">' . g_l('weClass', "[Charset]") . '</td>
		<tr><td>' . $retInput . '</td><td>' . $retSelect . '</td></tr>
	</table>
</td></tr>';
		} else { //	charset-tag NOT available
			//getCharsets
			return '<tr><td colspan="2">' . we_html_tools::getPixel(2, 4) . '</td></tr>
<tr><td>
	<table style="border-spacing: 0px;border-style:none" cellpadding="0">
		<tr><td colspan="2" class="defaultfont">' . g_l('weClass', "[Charset]") . '</td>
		<tr><td>' . $this->htmlTextInput("dummi", 40, g_l('charset', "[error][no_charset_tag]"), "", " readonly disabled", "text", 254) . '</td><td>' . $this->htmlSelect("dummi2", array(g_l('charset', "[error][no_charset_available]")), 1, DEFAULT_CHARSET, false, array("disabled" => 'disabled'), "value", 254) . '</td></tr>
	</table>
</td></tr>';
		}
	}

	// for internal use
	private function setTemplatePath(){
		$this->TemplatePath = $this->TemplateID ?
			TEMPLATES_PATH . f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($this->TemplateID), 'Path', $this->DB_WE) :
			WE_INCLUDES_PATH . 'we_templates/' . we_template::NO_TEMPLATE_INC;
	}

	function setTemplateID($templID){
		$this->TemplateID = $templID;
		$this->setTemplatePath();
	}

	public function we_new(){
		parent::we_new();
		$this->setTemplatePath();
	}

	private static function getFieldType($tagname, $tag, $useTextarea){
		switch($tagname){
			case 'list':
				return 'block';
			case 'textarea':
				if(!$useTextarea){
					return 'txt';
				}
			//no break;
			case 'formfield':
			case 'img':
			case 'linklist':
			case 'block':
				return $tagname;
			case 'input':
				return (strpos($tag, 'type="date"') !== false) ?
					'date' : 'txt';
			default:
				return 'txt';
		}
	}

	function makeBlockName($block, $field){
		$block = str_replace('[0-9]+', '####BLOCKNR####', $block);
		$field = str_replace('[0-9]+', '####BLOCKNR####', $field);
		$out = preg_quote($field . 'blk_' . $block . '__') . '[0-9]+';
		return str_replace('####BLOCKNR####', '[0-9]+', $out);
	}

	function makeLinklistName($block, $field){
		$block = str_replace('[0-9]+', '####BLOCKNR####', $block);
		$field = str_replace('[0-9]+', '####BLOCKNR####', $field);
		$out = preg_quote($field . $block . '_TAGS_') . '[0-9]+';
		return str_replace('####BLOCKNR####', '[0-9]+', $out);
	}

	/**
	 * @return string
	 * @desc this function returns the code of the template this document bases on
	 */
	function getTemplateCode($completeCode = true){
		return f('SELECT ' . CONTENT_TABLE . '.Dat FROM ' . CONTENT_TABLE . ',' . LINK_TABLE . ' WHERE ' . LINK_TABLE . '.CID=' . CONTENT_TABLE . '.ID AND ' . LINK_TABLE . '.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND ' . LINK_TABLE . '.DID=' . intval($this->TemplateID) . ' AND ' . LINK_TABLE . '.Name="' . ($completeCode ? 'completeData' : 'data') . '"', '', $this->DB_WE);
	}

	function getFieldTypes($templateCode, $useTextarea = false){
		$tp = new we_tag_tagParser($templateCode, $this->getPath());
		$tags = $tp->getAllTags();
		$blocks = array();
		$fieldTypes = array();
		$regs = array();
		//$xmlInputs = array();
		foreach($tags as $tag){
			if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){ // starttag found
				$tagname = $regs[1];
				if(($tagname != 'var') && ($tagname != 'field') && preg_match('|name="([^"]+)"|i', $tag, $regs)){ // name found
					$name = str_replace(array('[', ']'), array('\[', '\]'), $regs[1]);
					if(!empty($blocks)){
						$foo = end($blocks);
						$blockname = $foo['name'];
						$blocktype = $foo['type'];
						switch($blocktype){
							case 'list':
							case 'block':
								$name = self::makeBlockName($blockname, $name);
								break;
							case 'linklist':
								$name = self::makeLinklistName($blockname, $name);
								break;
						}
					}
					$fieldTypes[$name] = self::getFieldType($tagname, $tag, $useTextarea);
					switch($tagname){
						case 'list':
							$tagname = 'block';
						case 'block':
						case 'linklist':
							$foo = array(
								'name' => $name,
								'type' => $tagname
							);
							$blocks[] = $foo;
							break;
					}
				}
			} else if(preg_match('|</we:([^> ]+)|i', $tag, $regs)){ // endtag found
				$tagname = $regs[1];
				switch($tagname){
					case 'block':
					case 'list':
					case 'linklist':
						if(!empty($blocks)){
							array_pop($blocks);
						}
						break;
				}
			}
		}
		return $fieldTypes;
	}

	function correctFields(){
		// this is new for shop-variants
		$this->correctVariantFields();
		$regs = array();
		$allElements = $this->getUsedElements();
		if(isset($allElements['textarea'])){
			foreach($allElements['textarea'] as $name){
				//Bugfix for buggy tiny implementation where internal links looked like href="/img.gif?id=123" #7210
				$value = $this->getElement($name);
				if(preg_match_all('|src="/[^">]+\\?id=(\\d+)"|i', $value, $regs, PREG_SET_ORDER)){
					foreach($regs as $reg){
						$value = str_replace($reg[0], 'src="' . we_base_link::TYPE_INT_PREFIX . $reg[1] . '"', $value);
					}
				}
				if(preg_match_all('|src="/[^">]+\\?thumb=(\\d+,\\d+)"|i', $value, $regs, PREG_SET_ORDER)){
					foreach($regs as $reg){
						$value = str_replace($reg[0], 'src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '"', $value);
					}
				}

				$this->setElement($name, $value);
			}
		}
		//FIXME: it is better to use $this->getUsedElements - only we:input type="date" is not handled... => this will call the TP which is not desired since this method is called on save in frontend
		$types = self::getFieldTypes($this->getTemplateCode());

		foreach($this->elements as $k => $v){
			switch(isset($v['type']) ? $v['type'] : ''){
				case 'block':
				case 'list':
					$this->elements[$k]['type'] = 'block';
					break;
				case 'txt':
				case 'attrib':
				case 'variant':
				case 'formfield':
				case 'date':
				case 'image':
				case 'linklist':
				case 'img':
					if(isset($types[$k])){
						$this->elements[$k]['type'] = $types[$k];
					}
					break;
				default:
					$this->elements[$k]['type'] = 'txt';
					break;
			}
		}
	}

	public function we_save($resave = 0, $skipHook = 0){
		// First off correct corupted fields
		$this->correctFields();
//FIXME: maybe use $this->getUsedElements() to unset unused elements?! add setting to do this? check rebuild!!!
		// Bug Fix #6615
		$this->temp_template_id = $this->TemplateID;
		$this->temp_doc_type = $this->DocType;
		$this->temp_category = $this->Category;

		// Last step is to save the webEdition document
		$out = parent::we_save($resave, $skipHook);
		if(LANGLINK_SUPPORT && ($docID = weRequest('int', 'we_' . $this->Name . '_LanguageDocID'))){
			$this->setLanguageLink($docID, 'tblFile', false, false); // response deactivated
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, false);
		}

		if($resave == 0){
			$hy = unserialize(getPref('History'));
			$hy['doc'][$this->ID] = array('Table' => $this->Table, 'ModDate' => $this->ModDate);
			setUserPref('History', serialize($hy));
		}
		return $out;
	}

	protected function i_writeDocument(){
		$this->setTemplatePath();
		return parent::i_writeDocument();
	}

	public function we_publish($DoNotMark = false, $saveinMainDB = true, $skipHook = 0){
		return parent::we_publish($DoNotMark, $saveinMainDB, $skipHook);
	}

	public function we_unpublish($skipHook = 0){
		return ($this->ID ? parent::we_unpublish($skipHook) : false);
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		switch($from){
			case we_class::LOAD_SCHEDULE_DB:
				if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
					$sessDat = f('SELECT SerializedData FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($this->ID) . " AND ClassName='" . $this->ClassName . "' AND Was=" . we_schedpro::SCHEDULE_FROM, '', $this->DB_WE);

					if($sessDat &&
						$this->i_initSerializedDat(unserialize(substr_compare($sessDat, 'a:', 0, 2) == 0 ? $sessDat : gzuncompress($sessDat)))){
						$this->i_getPersistentSlotsFromDB('Path,Text,Filename,Extension,ParentID,Published,ModDate,CreatorID,ModifierID,Owners,RestrictOwners');
						break;
					}
				}
				$from = we_class::LOAD_TEMP_DB;
			//no break;
			default:
				parent::we_load($from);
				$this->setTemplatePath();
		}
	}

	function i_getDocument($includepath = ''){
		$glob = array();
		foreach(array_keys($GLOBALS) as $k){
			if((!preg_match('|^[0-9]|', $k)) && (!preg_match('|[^a-z0-9_]|i', $k)) && $k != '_SESSION' && $k != '_GET' && $k != '_POST' && $k != '_REQUEST' && $k != '_SERVER' && $k != '_FILES' && $k != '_SESSION' && $k != '_ENV' && $k != '_COOKIE'){
				$glob[] = '$' . $k;
			}
		}
		eval('global ' . implode(',', $glob) . ';'); // globalen Namensraum herstellen.
		$editpageSave = $this->EditPageNr;
		$inWebEditonSave = $this->InWebEdition;
		$this->InWebEdition = false;
		$this->EditPageNr = WE_EDITPAGE_PREVIEW;
		$we_include = $includepath ? $includepath : $this->editor();
		ob_start();
		if(is_file($we_include)){
			include($we_include);
		} else {
			t_e('File ' . $we_include . ' not found!');
		}
		$contents = ob_get_contents();
		ob_end_clean();
		$this->EditPageNr = $editpageSave;
		$this->InWebEdition = $inWebEditonSave;

		if((version_compare(phpversion(), '5.0') >= 0) && isset($we_EDITOR) && $we_EDITOR){ //  fix for php5, in editor we_doc was replaced by $GLOBALS['we_doc'] from we:include tags
			$GLOBALS['we_doc'] = $this;
		}

		return $contents;
	}

	function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		$this->setTemplatePath();
	}

	function i_scheduleToBeforeNow(){
		return false;
//FIXME: check
		//return (defined('SCHEDULE_TABLE') && ($this->To < time() && $this->ToOk));
	}

	function i_areVariantNamesValid(){
		if(defined('SHOP_TABLE')){
			$variationFields = we_shop_variants::getAllVariationFields($this);

			if(!empty($variationFields)){
				$i = 0;
				while(isset($this->elements[WE_SHOP_VARIANTS_PREFIX . $i])){
					if(!trim($this->elements[WE_SHOP_VARIANTS_PREFIX . $i++]['dat'])){
						return false;
					}
				}
			}
		}
		return true;
	}

	function i_publInScheduleTable(){
		return (we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER) ?
				we_schedpro::publInScheduleTable($this, $this->DB_WE) :
				false);
	}

	// returns the filesize of the document
	function getFilesize(){
		/* dies führt bei manchen dokumenten zum absturz in i_getDocument, und zwar dort beim include innerhalb von ob_start
		  $filename = TEMP_PATH."/".md5(uniqid(rand()));
		  saveFile($filename,$this->i_getDocument($includepath));
		  $fs = filesize($filename);
		  unlink($filename);
		  return $fs;
		 */
		return (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->Path) ?
				filesize($_SERVER['DOCUMENT_ROOT'] . $this->Path) : //das ist ungenau
				0);
	}

	protected function i_getDocumentToSave(){
		if($this->IsDynamic){
			$data = array();

			if(defined('SHOP_TABLE')){
				we_shop_variants::setVariantDataForModel($this, true);
			}
			$this->saveInSession($data);

			if(defined('SHOP_TABLE')){
				we_shop_variants::correctModelFields($this, true);
			}

			$data[0]['InWebEdition'] = false;
//FIXME: check if we can remove pv_id, used by we:printversion
			return '<?php
if(!defined(\'NO_SESS\')){define(\'NO_SESS\',1);}
$GLOBALS[\'WE_IS_DYN\'] = 1;
$GLOBALS[\'we_transaction\'] = 0;
$GLOBALS[\'we_ContentType\'] = \'' . we_base_ContentTypes::WEDOCUMENT . '\';

if (isset($_REQUEST[\'pv_id\']) && isset($_REQUEST[\'pv_tid\'])) {
	$_REQUEST[\'we_cmd\'] = array(
		1 => intval($_REQUEST[\'pv_id\']),
		4 => intval($_REQUEST[\'pv_tid\']),
	);
} else {
	$_REQUEST[\'we_cmd\'] = array(1 => ' . $this->ID . ');
}

$FROM_WE_SHOW_DOC = true;

if (!isset($GLOBALS[\'WE_MAIN_DOC\']) && isset($_REQUEST[\'we_objectID\'])) {
	include($_SERVER[\'DOCUMENT_ROOT\'] . \'' . WE_MODULES_DIR . 'object/we_object_showDocument.inc.php\');
} else {
	include($_SERVER[\'DOCUMENT_ROOT\'] . \'' . WE_INCLUDES_DIR . 'we_showDocument.inc.php\');
}';
		}
		static $cache = array();
		if(isset($cache[$this->ID])){
			return $cache[$this->ID];
		}
		$doc = $this->i_getDocument();
		$urlReplace = we_folder::getUrlReplacements($GLOBALS['DB_WE']);
// --> Glossary Replacement
		$useGlossary = ((defined('GLOSSARY_TABLE') && (!isset($GLOBALS['WE_MAIN_ID']) || $GLOBALS['WE_MAIN_ID'] == $GLOBALS['we_doc']->ID)) && (isset($GLOBALS['we_doc']->InGlossar) && $GLOBALS['we_doc']->InGlossar == 0) && we_glossary_replace::useAutomatic());

		// --> Glossary Replacement
		$doc = ($useGlossary ? we_glossary_replace::doReplace($doc, $this->Language) : $doc);
		$doc = ($urlReplace ? preg_replace($urlReplace, array_keys($urlReplace), $doc) : $doc);

		$cache[$this->ID] = $doc;

		return $doc;
	}

	/**
	 * @return void
	 * @desc This function sets special fields in the document to control i.e. the existing EDIT_PAGES or the available buttons
	 * 		for this document, use with tags we:hidePages and we:controlElement
	 */
	function setDocumentControlElements(){
		//	get code of the matching template
		$_templateCode = $this->getTemplateCode();

		//	First set hidePages from document ...
		$this->setHidePages($_templateCode);

		//	now set information about buttons of document
		$this->setControlElements($_templateCode);
	}

	function executeDocumentControlElements(){
		// here we must check, if setDocumentControlElements() already worked
		if(!isset($this->controlElement) || !is_array($this->controlElement)){
			$this->setDocumentControlElements();
		}
		//	disable hidePages
		$this->disableHidePages();
	}

	/**
	 * @return void
	 * @param string $templatecode
	 * @desc	if tag we:controlElement exists in template, this function sets the given control-elements in persistent_slot
	 * 		they are disabled in document later
	 *
	 */
	function setControlElements($templatecode){
		if(strpos($templatecode, '<we:controlElement') !== false){ // tag we:control exists
			$_tags = we_tag_tagParser::itemize_we_tag('we:controlElement', $templatecode);
			//	we need all given tags ...

			if($_tags[0]){

				if(!in_array('controlElement', $this->persistent_slots)){
					$this->persistent_slots[] = 'controlElement';
				} else {
					unset($this->controlElement);
				}

				$_ctrlArray = array();

				foreach($_tags[2] as $cur){ //	go through all matches
					$_tagAttribs = we_tag_tagParser::makeArrayFromAttribs($cur);

					$_type = weTag_getAttribute('type', $_tagAttribs);
					$_name = weTag_getAttribute('name', $_tagAttribs);
					$_hide = weTag_getAttribute('hide', $_tagAttribs, false, true);

					if($_type && $_name){
						switch($_type){
							case 'button': //	only look, if the button shall be hidden or not
								$_ctrlArray['button'][$_name] = array('hide' => ( $_hide ? 1 : 0 ));
								break;
							case 'checkbox':
								$_ctrlArray['checkbox'][$_name] = array(
									'hide' => ( $_hide ? 1 : 0 ),
									'readonly' => ( weTag_getAttribute('readonly', $_tagAttribs, true, true) ? 1 : 0 ),
									'checked' => ( weTag_getAttribute('checked', $_tagAttribs, false, true) ? 1 : 0 )
								);
								break;
						}
					}
				}
			}
			$this->controlElement = $_ctrlArray;
		}
	}

	/**
	 * @return void
	 * @param string $templatecode
	 * @desc	if tag we:hidePages exists in template, this function sets the given pages in persistent_slot
	 *
	 */
	function setHidePages($templatecode){
		if($this->InWebEdition){
			//	delete exisiting hidePages ...
			if(in_array('hidePages', $this->persistent_slots)){
				unset($this->hidePages);
			}

			if(strpos($templatecode, '<we:hidePages') !== false){ //	tag hidePages exists
				$_tags = we_tag_tagParser::itemize_we_tag('we:hidePages', $templatecode);

				// here we only take the FIRST tag
				$_tagAttribs = we_tag_tagParser::makeArrayFromAttribs($_tags[2][0]);

				$_pages = weTag_getAttribute('pages', $_tagAttribs);

				if(!in_array('hidePages', $this->persistent_slots)){
					$this->persistent_slots[] = 'hidePages';
				} else {
					unset($this->hidePages);
				}
				$this->hidePages = $_pages;

				$this->disableHidePages();
			}
		}
	}

	/**
	 * @return void
	 * @desc disables the editpages saved in persistent_slot hidePages inside webEdition
	 */
	function disableHidePages(){
		$MNEMONIC_EDITPAGES = array(
			WE_EDITPAGE_PROPERTIES => 'properties', WE_EDITPAGE_CONTENT => 'edit', WE_EDITPAGE_INFO => 'information', WE_EDITPAGE_PREVIEW => 'preview', WE_EDITPAGE_SCHEDULER => 'schedpro', WE_EDITPAGE_VALIDATION => 'validation', WE_EDITPAGE_VERSIONS => 'versions'
		);
		if(we_base_moduleInfo::isActive('shop')){
			$MNEMONIC_EDITPAGES[WE_EDITPAGE_VARIANTS] = 'variants';
		}
		if(we_base_moduleInfo::isActive('customer')){
			$MNEMONIC_EDITPAGES[WE_EDITPAGE_WEBUSER] = 'customer';
		}

		if(isset($this->hidePages) && $this->InWebEdition){
			$_hidePagesArr = explode(',', $this->hidePages); //	get pages which shall be disabled

			if(in_array('all', $_hidePagesArr)){
				$this->EditPageNrs = array();
			} else {
				foreach($this->EditPageNrs as $key => $editPage){
					if(array_key_exists($editPage, $MNEMONIC_EDITPAGES) && in_array($MNEMONIC_EDITPAGES[$editPage], $_hidePagesArr)){
						unset($this->EditPageNrs[$key]);
					}
				}
			}
		}
	}

	function changeTemplate(){
		// reload hidePages, controlElements
		$this->setDocumentControlElements();
	}

	/**
	 * called when document is initialized from inside webEdition
	 * @param mixed $sessDat
	 */
	protected function i_initSerializedDat($sessDat){
		if(!is_array($sessDat)){
			return false;
		}
		parent::i_initSerializedDat($sessDat);
		if(defined('SHOP_TABLE')){
			if($this->canHaveVariants()){
				$this->initVariantDataFromDb();
			}
		}
		return true;
	}

	/**
	 * called when document is initialized from outside webEdition
	 * @param mixed $loadBinary
	 */
	protected function i_getContentData($loadBinary = 0){
		parent::i_getContentData($loadBinary);
		if(defined('SHOP_TABLE')){
			if($this->canHaveVariants()){ // article variants
				$this->initVariantDataFromDb();
			}
		}
	}

	/**
	 * checks if this document is allowed to have variants
	 * and if it has some fields defined for variants.
	 *
	 * if paramter checkField is true, this function checks also, if there are
	 * already fields selected for the variants.
	 *
	 * @param boolean $checkFields
	 * @return boolean
	 */
	function canHaveVariants($checkFields = false){
		if(!defined('SHOP_TABLE') || ($this->TemplateID == 0)){
			return false;
		}

		if($this->hasVariants != null){
			return $this->hasVariants;
		}

		if($this->InWebEdition){
			$this->hasVariants = (f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE DID=' . intval($this->TemplateID) . ' AND DocumentTable="tblTemplates" AND Name LIKE ("variant_%") LIMIT 1', '', $this->DB_WE));
		} else {
			if(isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']) && is_array($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'])){
				$this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'] = serialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']);
			}
			if(isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]) && substr($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'], 0, 2) == 'a:'){
				$_vars = unserialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']);
				$this->hasVariants = (is_array($_vars) && $_vars);
			} else {
				$this->hasVariants = false;
			}
		}

		return $this->hasVariants;
	}

	function correctVariantFields(){
		if($this->canHaveVariants()){
			we_shop_variants::correctModelFields($this);
		}
	}

	function initVariantDataFromDb(){
		if(isset($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]) && $this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']){

			// unserialize the variant data when loading the model
			//if(!is_array($model->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'])) {
			$this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat'] = unserialize($this->elements[WE_SHOP_VARIANTS_ELEMENT_NAME]['dat']);
			//}
			// now register variant fields in document
			we_shop_variants::setVariantDataForModel($this);
		}
	}

	function getVariantFields(){
		if($this->TemplateID == 0){
			return array();
		}
		$template = new we_template();
		$template->initByID($this->TemplateID, TEMPLATES_TABLE);
		return $template->getVariantFields();
	}

	protected function updateRemoteLang($db, $id, $lang, $type){
		$oldLang = f('SELECT Language FROM ' . $this->Table . ' WHERE ID=' . $id, '', $db);
		if($oldLang == $lang){
			return;
		}
		//update Lang of doc
		$db->query('UPDATE ' . $this->Table . ' SET Language="' . $lang . '" WHERE ID=' . $id);
		//update LangLink:
		$db->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $lang . '" WHERE DID=' . $id . ' AND DocumentTable="' . $type . '"');
		//drop invalid entries => is this safe???
		$db->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . $id . ' AND DocumentTable="' . $type . '" AND DLocale!="' . $lang . '"');
	}

	public function addDocumentCss($stylesheet = ''){
		$this->DocumentCss .= ($this->DocumentCss ? ',' : '') . $stylesheet;
	}

	public function getDocumentCss(){
		return $this->DocumentCss;
	}

	public function resetUsedElements(){
		$this->usedElementNames = array();
	}

	public function addUsedElement($type, $name){
		if(!isset($this->usedElementNames[$type])){
			$this->usedElementNames[$type] = array($name);
		} else {
			$this->usedElementNames[$type][] = $name;
		}
	}

	public function getUsedElements($txtNamesOnly = false){
		if($txtNamesOnly){
			$ret = array();
			foreach($this->usedElementNames as $tag => $val){
				if(self::getFieldType($tag, '', false) == 'txt'){
					$ret = array_merge($ret, $val);
				}
			}
			return array_unique($ret);
		}
		return $this->usedElementNames;
	}

}
