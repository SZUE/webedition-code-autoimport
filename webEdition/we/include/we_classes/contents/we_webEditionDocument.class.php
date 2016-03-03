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
class we_webEditionDocument extends we_textContentDocument{
	// ID of the templates that is used from the document
	var $TemplateID = 0;
	// ID of the template that is used from the parked document (Bug Fix #6615)
	var $temp_template_id = 0;
	// Categories of the parked document (Bug Fix #6615)
	var $temp_category = '';
	// Path from the template
	var $TemplatePath = '';
	var $hasVariants = null;
	protected $usedElementNames = array();

	/**
	 * @var we_customer_documentFilter
	 */
	var $documentCustomerFilter = ''; // DON'T SET TO NULL !

	public function __construct(){
		parent::__construct();
		if(isWE()){
			//if(defined('SHOP_TABLE')){not needed for global variants
			$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_VARIANTS;
			//}

			if(defined('CUSTOMER_TABLE') && (permissionhandler::hasPerm('CAN_EDIT_CUSTOMERFILTER') || permissionhandler::hasPerm('CAN_CHANGE_DOCS_CUSTOMER'))){
				$this->EditPageNrs[] = we_base_constants::WE_EDITPAGE_WEBUSER;
			}
		}
		if(isset($_SESSION['prefs']['DefaultTemplateID'])){
			$this->TemplateID = $_SESSION['prefs']['DefaultTemplateID'];
		}
		array_push($this->persistent_slots, 'TemplateID', 'TemplatePath', 'hidePages', 'controlElement', 'temp_template_id', 'temp_category', 'usedElementNames');
		$this->ContentType = we_base_ContentTypes::WEDOCUMENT;
	}

	public function initByObj(we_objectFile $obj){
		$this->elements = $obj->elements;
		$this->Templates = $obj->Templates;
		$this->ExtraTemplates = $obj->ExtraTemplates;
		$this->TableID = $obj->TableID;
		$this->CreatorID = $obj->CreatorID;
		$this->ModifierID = $obj->ModifierID;
		$this->RestrictOwners = $obj->RestrictOwners;
		$this->Owners = $obj->Owners;
		$this->OwnersReadOnly = $obj->OwnersReadOnly;
		$this->Category = $obj->Category;
		$this->OF_ID = $obj->ID;
		$this->Charset = $obj->Charset;
		$this->Language = $obj->Language;
		$this->Url = $obj->Url;
		$this->TriggerID = $obj->TriggerID;
		$this->elements['Charset']['dat'] = $obj->Charset; // for charset-tag
	}

	public static function initDocument($formname = 'we_global_form', $tid = 0, $doctype = '', $categories = '', $docID = 0, $wewrite = false){
		//  check if a <we:sessionStart> Tag was before
		$session = !empty($GLOBALS['WE_SESSION_START']);

		if(!(isset($GLOBALS['we_document']) && is_array($GLOBALS['we_document']))){
			$GLOBALS['we_document'] = array();
		}
		$GLOBALS['we_document'][$formname] = new we_webEditionDocument();
		if((!$session) || (!isset($_SESSION['weS']['we_document_session_' . $formname])) || $wewrite){
			if($session){
				$_SESSION['weS']['we_document_session_' . $formname] = array();
			}
			$GLOBALS['we_document'][$formname]->we_new();
			if($docID){
				$GLOBALS['we_document'][$formname]->initByID($docID, FILE_TABLE);
			} else {
				$dt = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $GLOBALS['we_document'][$formname]->DB_WE->escape($doctype) . '"', '', $GLOBALS['we_document'][$formname]->DB_WE);
				$GLOBALS['we_document'][$formname]->changeDoctype($dt);
				if($tid){
					$GLOBALS['we_document'][$formname]->setTemplateID($tid);
				}
			}
			if((!$docID || $wewrite) && $categories){
				$GLOBALS['we_document'][$formname]->Category = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
			}
			if($session){
				$GLOBALS['we_document'][$formname]->saveInSession($_SESSION['weS']['we_document_session_' . $formname]);
			}
		} else {
			if($docID){
				$GLOBALS['we_document'][$formname]->initByID($docID, FILE_TABLE);
			} elseif($session){
				$GLOBALS['we_document'][$formname]->we_initSessDat($_SESSION['weS']['we_document_session_' . $formname]);
			}
			if($categories){
				$GLOBALS['we_document'][$formname]->Category = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
			}
		}

		if(($ret = we_base_request::_(we_base_request::STRING, 'we_returnpage'))){
			$GLOBALS['we_document'][$formname]->setElement('we_returnpage', $ret);
		}
		if(isset($_REQUEST['we_ui_' . $formname]) && is_array($_REQUEST['we_ui_' . $formname])){
			we_base_util::convertDateInRequest($_REQUEST['we_ui_' . $formname], true); //FIXME: this can't be we_base_request at the moment
			foreach($_REQUEST['we_ui_' . $formname] as $n => $v){
				$v = we_base_util::rmPhp($v);
				$GLOBALS['we_document'][$formname]->setElement($n, $v);
			}
		}

		if(($cats = we_base_request::_(we_base_request::STRING_LIST, 'we_ui_' . $formname . '_categories')) !== false){
			$GLOBALS['we_document'][$formname]->Category = makeIDsFromPathCVS($cats, CATEGORY_TABLE);
		}
		if(($cats = we_base_request::_(we_base_request::STRING, 'we_ui_' . $formname . '_Category')) !== false){
			$_REQUEST['we_ui_' . $formname . '_Category'] = implode(',', $cats);
		}
		foreach($GLOBALS['we_document'][$formname]->persistent_slots as $slotname){
			switch($slotname){
				case 'categories':
					break;
				default:
					if(($slot = we_base_request::_(we_base_request::STRING, 'we_ui_' . $formname . '_' . $slotname)) !== false){
						$GLOBALS["we_document"][$formname]->$slotname = $slot;
					}
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

	public function makeSameNew(array $keep = array()){
		parent::makeSameNew(array_merge($keep, array('TemplateID', 'TemplatePath', 'IsDynamic')));
	}

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return 'we_editors/we_editor_properties.inc.php';
			case we_base_constants::WE_EDITPAGE_INFO:
				if(isset($GLOBALS['WE_MAIN_DOC'])){
					$GLOBALS["WE_MAIN_DOC"]->InWebEdition = true; //Bug 3417
				}
				return 'we_editors/we_editor_info.inc.php';

			case we_base_constants::WE_EDITPAGE_CONTENT:
				$GLOBALS['we_editmode'] = true;
				break;
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				$GLOBALS['we_editmode'] = false;
				break;
			case we_base_constants::WE_EDITPAGE_VALIDATION:
				return 'we_editors/validateDocument.inc.php';
			case we_base_constants::WE_EDITPAGE_VARIANTS:
				return 'we_editors/we_editor_variants.inc.php';
			case we_base_constants::WE_EDITPAGE_WEBUSER:
				return 'we_editors/editor_weDocumentCustomerFilter.inc.php';
			default:
				return parent::editor();
		}

		return preg_replace('/.tmpl$/i', '.php', $this->TemplatePath); // .tmpl mod
	}

	/*
	 * Form functions for generating the html of the input fields
	 */

	private function formIsDynamic($disabled = false){
		$v = $this->IsDynamic;
		if(!$disabled){
			$n = 'we_' . $this->Name . '_IsDynamic';
			return we_html_forms::checkboxWithHidden($v ? true : false, $n, g_l('weClass', '[IsDynamic]'), false, "defaultfont", "WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);switchExt();") . we_html_element::jsElement(
					'function switchExt() {' .
					($this->Published ?
						'' :
						'var a=document.we_form.elements;' .
						($this->ID ? 'if(confirm("' . g_l('weClass', '[confirm_ext_change]') . '")){' : '') . '
					a["we_' . $this->Name . '_Extension"].value=(a["we_' . $this->Name . '_IsDynamic"].value==1?"' . DEFAULT_DYNAMIC_EXT . '":"' . DEFAULT_STATIC_EXT . '");' .
						($this->ID ? '}' : '')
					) .
					'}'
			);
		}
		return we_html_forms::checkboxWithHidden($v ? true : false, '', g_l('weClass', '[IsDynamic]'), false, "defaultfont", "", true);
	}

	function formDocTypeTempl(){
		$disable = (permissionhandler::hasPerm('EDIT_DOCEXTENSION') ?
				(($this->ContentType == we_base_ContentTypes::HTML || $this->ContentType == we_base_ContentTypes::WEDOCUMENT) && $this->Published) :
				true);

		return '
<table class="default">
	<tr><td colspan="3" class="defaultfont" style="text-align:left;padding-bottom:4px;">' . $this->formDocType(($this->Published > 0)) . '</td></tr>
	<tr><td colspan="3" class="defaultfont" style="text-align:left;padding-bottom:4px;">' . $this->formTemplatePopup(($this->Published > 0)) . '</td></tr>
	<tr><td colspan="3">
			<table class="default">
				<tr>
					<td>' . $this->formIsDynamic($disable) . '</td>
					<td class="defaultfont">&nbsp;</td>
					<td>' . $this->formIsSearchable() . '</td>
				</tr>
				<tr><td>' . $this->formInGlossar(100) . '</td></tr>
			</table></td>
	</tr></table>';
	}

	private function formTemplateWindow(){
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
		$myid = $this->TemplateID ? : '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), '', $this->DB_WE);
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $idname . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);opener.top.we_cmd('reload_editpage');");

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)");
		$yuiSuggest->setAcId('Template');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel("<span id='TemplateLabel' style='" . $styleTemplateLabel . "'>" . $ueberschrift . "</span><span id='TemplateLabelLink' style='" . $styleTemplateLabelLink . "'>" . $ueberschrift . "</span>");
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(0);
		$yuiSuggest->setSelectButton($button);
		//$yuiSuggest->setDoOnTextfieldBlur("if(document.getElementById('yuiAcResultTemplate').value == '' || document.getElementById('yuiAcResultTemplate').value == 0) { document.getElementById('TemplateLabel').style.display = 'inline'; document.getElementById('TemplateLabelLink').style.display = 'none'; } else { document.getElementById('TemplateLabel').style.display = 'none'; document.getElementById('TemplateLabelLink').style.display = 'inline'; }");
		$yuiSuggest->setDoOnTextfieldBlur("if(YAHOO.autocoml.yuiAcFields[YAHOO.autocoml.yuiAcFieldsById['yuiAcInputTemplate'].set].changed && YAHOO.autocoml.isValidById('yuiAcInputTemplate')){ top.we_cmd('reload_editpage')}");

		return $yuiSuggest->getHTML();
	}

	// creates the Template PopupMenue
	private function formTemplatePopup($disable){
		if($this->DocType){
			$templateFromDoctype = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($this->DocType) . ' LIMIT 1', '', $this->DB_WE);
		}
		if($disable){
			$myid = intval($this->TemplateID ? : 0);
			$path = ($myid ? f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($myid), '', $this->DB_WE) : '');

			/* $ueberschrift = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
			  '<a href="javascript:goTemplate(' . $myid . ')">' . g_l('weClass', '[template]') . '</a>' :
			  g_l('weClass', '[template]')); */

			if($this->DocType){
				return ($templateFromDoctype ?
						$this->xformTemplatePopup(0) :
						we_html_tools::htmlFormElementTable($path, g_l('weClass', '[template]'), 'left', 'defaultfont')
					);
			}
			$pop = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ?
					'<table class="default"><tr><td>' . $path . '</td><td>' . we_html_button::create_button(we_html_button::EDIT, 'javascript:goTemplate(' . $myid . ')') .
					'</td></tr></table>' :
					$path);

			return we_html_tools::htmlFormElementTable($pop, g_l('weClass', '[template]'), 'left', 'defaultfont');
		}

		if($this->DocType){

			// if a Doctype is set and this Doctype has defined some templates, just show a select box
			return ($templateFromDoctype ?
					$this->xformTemplatePopup(388) :
					$this->formTemplateWindow() );
		}
		return $this->formTemplateWindow();
	}

	private function xformTemplatePopup($width = 50){
		$ws = get_ws(TEMPLATES_TABLE, true);

		$hash = getHash('SELECT TemplateID,Templates FROM ' . DOC_TYPES_TABLE . ' WHERE ID =' . intval($this->DocType), $this->DB_WE);
		$TID = $hash['TemplateID'];
		$Templates = $hash['Templates'];
		$tlist = ($TID? : '') . ($Templates ? ',' . $Templates : '');

		if($tlist){
			$temps = array_filter(explode(',', $tlist));
		} else {
			$temps = array();
			foreach($ws as $wid){
				pushChilds($temps, $wid, TEMPLATES_TABLE, 0, $this->DB_WE);
			}
		}
		if($this->TemplateID){
			$temps[] = $this->TemplateID;
			$TID = $this->TemplateID;
		}
		$tlist = array_unique($temps);

		$fieldname = 'we_' . $this->Name . '_TemplateID';
		$openButton = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES') && $_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL ? we_html_button::create_button(we_html_button::EDIT, 'javascript:goTemplate(document.we_form.elements[\'' . $fieldname . '\'].options[document.we_form.elements[\'' . $fieldname . '\'].selectedIndex].value)') : '');

		if($tlist){
			$foo = array();
			foreach($tlist as $tid){
				if(($tid == $this->TemplateID) || in_workspace($tid, $ws, TEMPLATES_TABLE)){
					$foo[] = $tid;
				}
			}


			return $this->formSelect4($width, 'TemplateID', TEMPLATES_TABLE, 'ID', 'Path', g_l('weClass', '[template]'), ' WHERE ID IN (' . ($foo ? implode(',', $foo) : -1) . ') AND IsFolder=0 ORDER BY Path', 1, $TID, false, "we_cmd('template_changed');WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);", array(), 'left', 'defaultfont', '', $openButton, array(0, ''));
		}
		return $this->formSelect2($width, 'TemplateID', TEMPLATES_TABLE, 'ID', 'Path', g_l('weClass', '[template]'), '', 'IsFolder=0 ORDER BY Path ', 1, $this->TemplateID, false, 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);', array(), 'left', 'defaultfont', '', $openButton);
	}

	/**
	 * @return string
	 * @desc Returns the metainfos for the selected file.
	 */
	function formMetaInfos(){
		//	Collect data from meta-tags
		//will evaluate the tags => we get meta data set
		$oldEdit = $this->EditPageNr; //FIXME: cache data
		$this->EditPageNr = we_base_constants::WE_EDITPAGE_CONTENT;
		weSuggest::setStaticInstance(false); //avoid modification on suggestor
		$include = $this->editor();
		$this->EditPageNr = $oldEdit;
		if($include && $include != WE_INCLUDES_PATH . 'we_editors/' . we_template::NO_TEMPLATE_INC){
			ob_start();
			//FIX for old code
			$DB_WE = $GLOBALS['DB_WE'];
			//$we_doc = $this;
			include($include);
			$ret = ob_end_clean();
		}

		weSuggest::setStaticInstance(true);

		//	if a meta-tag is set all information are in array $GLOBALS["meta"]
		return '
<table class="default">
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Title') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Description') . '</td></tr>
	<tr><td style="padding-bottom:2px;">' . $this->formMetaField('Keywords') . '</td></tr>' .
			$this->getCharsetSelect() .
			$this->formLangLinks(true) .
			'</table>';
	}

	/**
	 * This function returns the selector of the charset.
	 * @return string
	 */
	private function getCharsetSelect(){
		$_charsetHandler = new we_base_charsetHandler();

		if(isset($GLOBALS['meta']['Charset'])){ //	charset-tag available
			$name = 'Charset';

			//	This is the input field for the charset
			$inputName = 'we_' . $this->Name . '_attrib[' . $name . ']';
			$chars = explode(',', $GLOBALS['meta']['Charset']['defined']);

			//	input field - check value
			$value = ($this->getElement($name) ?
					$this->getElement($name) :
					(isset($GLOBALS["meta"][$name]) ?
						$GLOBALS["meta"][$name]["default"] :
						''));

			$retInput = we_html_tools::htmlTextInput($inputName, 40, $value, '', ' readonly ', 'text', 254);

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
			$retSelect = $this->htmlSelect('we_tmp_' . $name, $_charsetHandler->getCharsetsByArray($chars), 1, $value, false, array('onblur' => 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);document.forms[0].elements[\'' . $inputName . '\'].value=this.options[this.selectedIndex].value;', 'onchange' => 'WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);document.forms[0].elements[\'' . $inputName . '\'].value=this.options[this.selectedIndex].value;'), 'value', 254);
		} else {
			//	charset-tag NOT available
			$retInput = we_html_tools::htmlTextInput("dummi", 40, g_l('charset', '[error][no_charset_tag]'), '', ' readonly disabled', 'text', 254);
			$retSelect = $this->htmlSelect("dummi2", array(g_l('charset', '[error][no_charset_available]')), 1, DEFAULT_CHARSET, false, array('disabled' => 'disabled'), 'value', 254);
		}
		//getCharsets
		return '
<tr><td style="padding-top:2px;">
	<table class="default">
		<tr><td colspan="2" class="defaultfont">' . g_l('weClass', '[Charset]') . '</td>
		<tr><td>' . $retInput . '</td><td>' . $retSelect . '</td></tr>
	</table>
</td></tr>';
	}

	// for internal use
	private function setTemplatePath(){
		$path = $this->TemplatePath = $this->TemplateID ? f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($this->TemplateID), '', $this->DB_WE) : '';
		$this->TemplatePath = $path ?
			TEMPLATES_PATH . $path : WE_INCLUDES_PATH . 'we_editors/' . we_template::NO_TEMPLATE_INC;
	}

	public function setTemplateID($templID){
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
			case 'vars'://internal data which is never saved
			case 'formfield':
			case 'img':
			case 'linklist':
			case 'block':
			case 'link':
			case 'object':
			case 'href':
			case 'customer':
				return $tagname;
			case 'input':
				return (strpos($tag, 'type="date"') !== false) ?
					'date' : 'txt';
			default:
				return 'txt';
		}
	}

	public function insertAtIndex(array $only = null, array $fieldTypes = array()){
		if($this->ContentType == we_base_ContentTypes::WEDOCUMENT){
			$only = $this->getUsedElements(true);
			if($only){//FIXME:needed for rebuild, since tags are unintialized
				$only = array_merge(array('Title', 'Description', 'Keywords'), $only);
			}
		}
		return parent::insertAtIndex($only, $fieldTypes);
	}

	/**
	 * @return string
	 * @desc this function returns the code of the template this document bases on
	 */
	function getTemplateCode($completeCode = true){
		return f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON c.ID=l.CID WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND l.DID=' . intval($this->TemplateID) . ' AND l.nHash=x\'' . md5($completeCode ? 'completeData' : 'data') . '\'', '', $this->DB_WE);
	}

	protected function getFieldTypes($templateCode, $useTextarea = false){
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
					if($blocks){
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
						if($blocks){
							array_pop($blocks);
						}
						break;
				}
			}
		}
		return $fieldTypes;
	}

	public function correctFields(){
		// this is new for shop-variants
		$this->correctVariantFields();
		$regs = array();
		$allElements = $this->getUsedElements();
		if(isset($allElements['textarea'])){
			foreach($allElements['textarea'] as $name){
				//Bugfix for buggy tiny implementation where internal links looked like href="/img.gif?id=123" #7210
				$value = $this->getElement($name);
				if(preg_match_all('@src="/[^">]+\\?id=(\\d+)([&][^">]+["]|["])@i', $value, $regs, PREG_SET_ORDER)){
					foreach($regs as $reg){
						$value = str_replace($reg[0], 'src="' . we_base_link::TYPE_INT_PREFIX . $reg[1] . '"', $value);
					}
				}
				if(preg_match_all('@src="/[^">]+\?thumb=(\d+,\d+)([&][^">]+["]|["])@i', $value, $regs, PREG_SET_ORDER)){
					foreach($regs as $reg){
						$value = str_replace($reg[0], 'src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '"', $value);
					}
				}

				$this->setElement($name, $value);
			}
		}
		//FIXME: it is better to use $this->getUsedElements - only we:input type="date" is not handled... => this will call the TP which is not desired since this method is called on save in frontend
		$types = $this->getFieldTypes($this->getTemplateCode());

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
				case 'link':
				case 'img':
				case 'href':
				case 'object':
				case 'customer':
					if(isset($types[$k])){
						$this->elements[$k]['type'] = $types[$k];
					}
					break;
				case 'vars'://internal data which is never saved
					break;
				default:
					switch(isset($types[$k]) ? $types[$k] : ''){
						case 'link':
							// FIXME: make sure fixed types are written to tblFile too!
							$this->elements[$k]['type'] = $types[$k];
							break;
						default:
							$this->elements[$k]['type'] = 'txt';
					}
			}
		}
	}

	/*
	 * this function is used to replace to prepare wysiwyg img sources for db
	 * it also writes img sources and hrefs to $this->MediaLinks
	 *
	 * when $isRebuildMediaLinks it only writes $this->MediaLinks (img sources come from db and must not be vhanged)
	 */

	function parseTextareaFields($rebuildMode = false){
		if($rebuildMode === false){
			$allElements = $this->getUsedElements();
			if(isset($allElements['textarea'])){
				foreach($allElements['textarea'] as $name){
					$value = $this->getElement($name);
					$this->MediaLinks = array_merge($this->MediaLinks, we_wysiwyg_editor::reparseInternalLinks($value, true)); //true: replace internal file paths
					$this->setElement($name, $value);
				}
			}

			return;
		}

		//FIXME: implement textarea as element-type for textareas!
		if($rebuildMode === 'main'){
			foreach($this->elements as $name => $elem){
				if($elem['type'] === 'txt' && (strpos($elem['dat'], 'src="' . we_base_link::TYPE_INT_PREFIX) !== false || strpos($elem['dat'], 'href="' . we_base_link::TYPE_INT_PREFIX) !== false)){
					$this->MediaLinks = array_merge($this->MediaLinks, we_document::parseInternalLinks($elem['dat'], 0, '', true));
				}
			}
		} else {//rebuilding from tblTemporaryDoc
			foreach($this->elements as $name => $elem){
				if($elem['type'] === 'txt' && (strpos($elem['dat'], 'src="') !== false || strpos($elem['dat'], 'href="' . we_base_link::TYPE_INT_PREFIX) !== false)){
					$this->MediaLinks = array_merge($this->MediaLinks, we_wysiwyg_editor::reparseInternalLinks($elem['dat'], true));
				}
			}
		}
	}

	public function we_save($resave = false, $skipHook = false){
		// First off correct corupted fields
		$this->correctFields();

//FIXME: maybe use $this->getUsedElements() to unset unused elements?! add setting to do this? check rebuild!
		// Bug Fix #6615
		$this->temp_template_id = $this->TemplateID;
		$this->temp_category = $this->Category;

		// Last step is to save the webEdition document
		$out = parent::we_save($resave, $skipHook);
		if($out){
			$this->parseTextareaFields();
			$this->unregisterMediaLinks(false, true);
			$out = $this->registerMediaLinks(true);
		}

		if(LANGLINK_SUPPORT){
			$this->setLanguageLink($this->LangLinks, 'tblFile', false, false); // response deactivated
		} else {
			//if language changed, we must delete eventually existing entries in tblLangLink, even if !LANGLINK_SUPPORT!
			$this->checkRemoteLanguage($this->Table, false);
		}

		$this->i_writeMetaValues();

		/* if(!$resave){
		  $hy = we_unserialize(we_base_preferences::getUserPref('History'));
		  $hy['doc'][$this->ID] = array('Table' => $this->Table, 'ModDate' => $this->ModDate);
		  we_base_preferences::setUserPref('History', we_serialize($hy));
		  } */
		return $out;
	}

	protected function i_writeDocument(){
		$this->setTemplatePath();
		return parent::i_writeDocument();
	}

	public function we_publish($DoNotMark = false, $saveinMainDB = true, $skipHook = false){
		$this->temp_template_id = $this->TemplateID;
		$this->temp_category = $this->Category;
		$out = parent::we_publish($DoNotMark, $saveinMainDB, $skipHook);
		if($out){
			if($DoNotMark){
				// when called directly by rebuild we must prepare elements as is normally done in we_save
				$this->correctFields();
				$this->parseTextareaFields('main');
				// TODO: we should try to throw out obsolete elements from temporary! but this affects static docs only!
				// TODO: when doing rebuild media link test all elements against template!
				$this->unregisterMediaLinks(true, false);
				$out = $this->registerMediaLinks(); // last param: when rebuilding static docs do not delete temp entries!
			} else {
				$this->unregisterMediaLinks();
				$out = $this->registerMediaLinks(false, true);
			}
		}

		return $out;
	}

	public function we_unpublish($skipHook = 0){
		$oldPublished = $this->Published;
		$ret = ($this->ID ? parent::we_unpublish($skipHook) : false);

		// if document was modified before unpublishing, the actual version is in tblTemporaryDoc: we unregister temp=0
		// otherwise we have nothing to do
		if($ret && $oldPublished && ($this->ModDate > $oldPublished)){
			$this->unregisterMediaLinks(true, false);
		}

		return $ret;
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		switch($from){
			case we_class::LOAD_SCHEDULE_DB:
				if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
					$sessDat = f('SELECT SerializedData FROM ' . SCHEDULE_TABLE . ' WHERE DID=' . intval($this->ID) . ' AND ClassName="' . $this->DB_WE->escape($this->ClassName) . '" AND task="' . we_schedpro::SCHEDULE_FROM . '"', '', $this->DB_WE);

					if($sessDat && $this->i_initSerializedDat(we_unserialize($sessDat))){
						$this->i_getPersistentSlotsFromDB(self::primaryDBFiels);
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
		extract($GLOBALS, EXTR_SKIP); // globalen Namensraum herstellen.

		$editpageSave = $this->EditPageNr;
		$inWebEditonSave = $this->InWebEdition;
		$this->InWebEdition = false;
		$this->EditPageNr = we_base_constants::WE_EDITPAGE_PREVIEW;
		$we_include = $includepath ? : $this->editor();
		if(is_file($we_include)){
			ob_start();
			include($we_include);
			$contents = ob_get_clean();
		} else {
			t_e('File ' . $we_include . ' not found!');
			$contents = '';
		}
		$this->EditPageNr = $editpageSave;
		$this->InWebEdition = $inWebEditonSave;

		if(!empty($we_EDITOR)){ //  fix for php5, in editor we_doc was replaced by $GLOBALS['we_doc'] from we:include tags
			$GLOBALS['we_doc'] = $this;
		}

		return $contents;
	}

	public function we_initSessDat($sessDat){
		parent::we_initSessDat($sessDat);
		$this->setTemplatePath();
	}

	protected function i_scheduleToBeforeNow(){
		return false;
//FIXME: check
		//return (defined('SCHEDULE_TABLE') && ($this->To < time() && $this->ToOk));
	}

	protected function i_areVariantNamesValid(){
		$variationFields = we_base_variants::getAllVariationFields($this);

		if(!empty($variationFields)){
			$i = 0;
			while($this->issetElement(we_base_constants::WE_VARIANTS_PREFIX . $i)){
				if(!trim($this->getElement(we_base_constants::WE_VARIANTS_PREFIX . $i++))){
					return false;
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
		return (file_exists($_SERVER['DOCUMENT_ROOT'] . $this->Path) ?
				filesize($_SERVER['DOCUMENT_ROOT'] . $this->Path) : //das ist ungenau
				0);
	}

	protected function i_getDocumentToSave(){
		static $cache = array();
		if($this->IsDynamic){
			$data = array();

			we_base_variants::setVariantDataForModel($this, true);

			$this->saveInSession($data);

			we_base_variants::correctModelFields($this, true);

			$data[0]['InWebEdition'] = false;
//FIXME: check if we can remove pv_id, used by we:printversion
			return '<?php /*Generated by WE ' . WE_VERSION . ', SVN ' . WE_SVNREV . ', ' . date('Y-m-d, H:i') . '*/
if(!defined(\'NO_SESS\')){define(\'NO_SESS\',1);}
$GLOBALS[\'WE_IS_DYN\'] = 1;
$GLOBALS[\'we_transaction\'] = 0;
$GLOBALS[\'we_ContentType\'] = \'' . we_base_ContentTypes::WEDOCUMENT . '\';

if(isset($_REQUEST[\'pv_id\']) && isset($_REQUEST[\'pv_tid\'])) {
		$_REQUEST[\'we_cmd\'] = array(
				1 => intval($_REQUEST[\'pv_id\']),
				4 => intval($_REQUEST[\'pv_tid\']),
		);
} else {
		$_REQUEST[\'we_cmd\'] = array(1 => ' . $this->ID . ');
}

$FROM_WE_SHOW_DOC = true;

if(!isset($GLOBALS[\'WE_MAIN_DOC\']) && isset($_REQUEST[\'we_objectID\'])) {
		include($_SERVER[\'DOCUMENT_ROOT\'] . \'' . WE_MODULES_DIR . 'object/we_object_showDocument.inc.php\');
} else {
		include($_SERVER[\'DOCUMENT_ROOT\'] . \'' . WE_INCLUDES_DIR . 'we_showDocument.inc.php\');
}';
		}

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
	public function setDocumentControlElements(){
		////FIXME: use Tagparser & save this to DB
		//	get code of the matching template
		$_templateCode = $this->getTemplateCode();

		//	First set hidePages from document ...
		$this->setHidePages($_templateCode);

		//	now set information about buttons of document
		$this->setControlElements($_templateCode);
	}

	public function executeDocumentControlElements(){
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

					$_type = weTag_getAttribute('type', $_tagAttribs, '', we_base_request::STRING);
					$_name = weTag_getAttribute('name', $_tagAttribs, '', we_base_request::STRING);
					$_hide = weTag_getAttribute('hide', $_tagAttribs, false, we_base_request::BOOL);

					if($_type && $_name){
						switch($_type){
							case 'button': //	only look, if the button shall be hidden or not
								$_ctrlArray['button'][$_name] = array('hide' => ( $_hide ? 1 : 0 ));
								break;
							case 'checkbox':
								$_ctrlArray['checkbox'][$_name] = array(
									'hide' => ( $_hide ? 1 : 0 ),
									'readonly' => ( weTag_getAttribute('readonly', $_tagAttribs, true, we_base_request::BOOL) ? 1 : 0 ),
									'checked' => ( weTag_getAttribute('checked', $_tagAttribs, false, we_base_request::BOOL) ? 1 : 0 )
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
	private function setHidePages($templatecode){
		if($this->InWebEdition){
			//	delete exisiting hidePages ...
			if(in_array('hidePages', $this->persistent_slots)){
				unset($this->hidePages);
			}

			if(strpos($templatecode, '<we:hidePages') !== false){ //	tag hidePages exists
				$_tags = we_tag_tagParser::itemize_we_tag('we:hidePages', $templatecode);

				// here we only take the FIRST tag
				$_tagAttribs = we_tag_tagParser::makeArrayFromAttribs($_tags[2][0]);

				$_pages = weTag_getAttribute('pages', $_tagAttribs, '', we_base_request::STRING);

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
	private function disableHidePages(){
		if(!(isset($this->hidePages) && $this->InWebEdition)){
			return;
		}
		$MNEMONIC_EDITPAGES = array(
			we_base_constants::WE_EDITPAGE_PROPERTIES => 'properties',
			we_base_constants::WE_EDITPAGE_CONTENT => 'edit',
			we_base_constants::WE_EDITPAGE_INFO => 'information',
			we_base_constants::WE_EDITPAGE_PREVIEW => 'preview',
			we_base_constants::WE_EDITPAGE_SCHEDULER => 'schedpro',
			we_base_constants::WE_EDITPAGE_VALIDATION => 'validation',
			we_base_constants::WE_EDITPAGE_VERSIONS => 'versions',
			we_base_constants::WE_EDITPAGE_VARIANTS => 'variants',
			we_base_constants::WE_EDITPAGE_WEBUSER => 'customer',
		);

		$_hidePagesArr = explode(',', $this->hidePages); //	get pages which shall be disabled

		if(in_array('all', $_hidePagesArr)){
			$this->EditPageNrs = array();
			return;
		}
		foreach($this->EditPageNrs as $key => $editPage){
			if(array_key_exists($editPage, $MNEMONIC_EDITPAGES) && in_array($MNEMONIC_EDITPAGES[$editPage], $_hidePagesArr)){
				unset($this->EditPageNrs[$key]);
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
		$ret = parent::i_initSerializedDat($sessDat);
		if($ret && $this->canHaveVariants()){
			$this->initVariantDataFromDb();
		}

		return $ret;
	}

	/**
	 * called when document is initialized from outside webEdition
	 * @param mixed $loadBinary
	 */
	protected function i_getContentData(){
		parent::i_getContentData();
		if($this->canHaveVariants()){ // article variants
			$this->initVariantDataFromDb();
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
		if(($this->TemplateID == 0)){
			return false;
		}

		if($this->hasVariants != null){
			return $this->hasVariants;
		}

		if($this->InWebEdition){
			return ($this->hasVariants = (f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE DID=' . intval($this->TemplateID) . ' AND DocumentTable="tblTemplates" AND Name LIKE ("variant_%") LIMIT 1', '', $this->DB_WE)));
		}
		$tmp = $this->getElement(we_base_constants::WE_VARIANTS_ELEMENT_NAME);
		if(is_array($tmp)){
			$this->setElement(we_base_constants::WE_VARIANTS_ELEMENT_NAME, we_serialize($tmp), 'variant');
			return ($this->hasVariants = !empty($tmp));
		}
		$_vars = we_unserialize($tmp);
		return ($this->hasVariants = (is_array($_vars) && $_vars));
	}

	function correctVariantFields(){
		if($this->canHaveVariants()){
			we_base_variants::correctModelFields($this);
		}
	}

	function initVariantDataFromDb(){
		if(($tmp = $this->getElement(we_base_constants::WE_VARIANTS_ELEMENT_NAME))){

			// unserialize the variant data when loading the model
			$this->setElement(we_base_constants::WE_VARIANTS_ELEMENT_NAME, we_unserialize($tmp), 'variant');

			// now register variant fields in document
			we_base_variants::setVariantDataForModel($this);
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
		$oldLang = f('SELECT Language FROM ' . $db->escape($this->Table) . ' WHERE ID=' . intval($id), '', $db);
		if($oldLang == $lang){
			return;
		}
		//update Lang of doc
		$db->query('UPDATE ' . $db->escape($this->Table) . ' SET Language="' . $db->escape($lang) . '" WHERE ID=' . intval($id));
		//update LangLink:
		$db->query('UPDATE ' . LANGLINK_TABLE . ' SET DLocale="' . $db->escape($lang) . '" WHERE DID=' . intval($id) . ' AND DocumentTable="' . $db->escape($type) . '"');
		//drop invalid entries => is this safe???
		$db->query('DELETE FROM ' . LANGLINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $db->escape($type) . '" AND DLocale!="' . $db->escape($lang) . '"');
	}

	public function resetUsedElements(){
		$this->usedElementNames = array();
	}

	public function addUsedElement($type, $name){
		$type = self::getFieldType($type, '', true);
		if(!isset($this->usedElementNames[$type])){
			$this->usedElementNames[$type] = array($name);
		} elseif(array_search($name, $this->usedElementNames[$type]) === false){
			$this->usedElementNames[$type][] = $name;
		}
	}

	public function getUsedElements($txtNamesOnly = false){
		if($txtNamesOnly){
			return array_unique(array_merge((isset($this->usedElementNames['txt']) ? $this->usedElementNames['txt'] : array()), isset($this->usedElementNames['textarea']) ? $this->usedElementNames['textarea'] : array()));
		}
		return $this->usedElementNames;
	}

	public function getPropertyPage(){
		$wepos = weGetCookieVariable('but_weDocProp');

		return we_html_multiIconBox::getHTML('PropertyPage', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[document]'), 'html' => $this->formDocTypeTempl(), 'space' => 140),
			array('icon' => 'meta.gif', 'headline' => g_l('weClass', '[metainfo]'), 'html' => $this->formMetaInfos(), 'space' => 140),
			array('icon' => 'cat.gif', 'headline' => g_l('global', '[categorys]'), 'html' => $this->formCategory(), 'space' => 140),
			array('icon' => 'navi.gif', 'headline' => g_l('global', '[navigation]'), 'html' => $this->formNavigation(), 'space' => 140),
			array('icon' => 'copy.gif', 'headline' => g_l('weClass', '[copyWeDoc]'), 'html' => $this->formCopyDocument(), 'space' => 140),
			array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $this->formCreatorOwners(), 'space' => 140)
			), 0, '', -1, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'));
	}

}
