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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* a class for handling templates */

class we_template extends we_document{

	var $MasterTemplateID = 0;
	var $TagWizardCode; // bugfix 1502
	var $TagWizardSelection; // bugfix 1502
	var $IncludedTemplates = '';
	var $doUpdateCode = true; // will be protected in later WE Versions

	const TemplateHead = '<?php we_templateHead();?>';
	const TemplatePreContent = '<?php we_templatePreContent();?>';
	const TemplatePostContent = '<?php we_templatePostContent();?>';
	const NO_TEMPLATE_INC = 'we_noTmpl.inc.php';

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->Icon = 'we_template.gif';
		$this->Table = TEMPLATES_TABLE;

		array_push($this->persistent_slots, 'MasterTemplateID', 'IncludedTemplates', 'TagWizardCode', 'TagWizardSelection');
		$this->elements["Charset"]["dat"] = DEFAULT_CHARSET;
		array_push($this->EditPageNrs, WE_EDITPAGE_PROPERTIES, WE_EDITPAGE_INFO, WE_EDITPAGE_CONTENT, WE_EDITPAGE_PREVIEW, WE_EDITPAGE_PREVIEW_TEMPLATE, WE_EDITPAGE_VARIANTS, WE_EDITPAGE_VERSIONS);
		$this->Published = 1;
		$this->InWebEdition = true;
		$this->ContentType = 'text/weTmpl';
	}

	function copyDoc($id){
		if(!$id){
			return;
		}
		$temp = new we_template();
		$temp->InitByID($id, TEMPLATES_TABLE);
		//$parentIDMerk = $this->ParentID;
		if($this->ID == 0){
			foreach($this->persistent_slots as $cur){
				if($cur != 'elements'){
					$this->{$cur} = $temp->{$cur};
				}
			}
			$this->CreationDate = time();
			$this->ID = 0;
			$this->OldPath = '';
			$this->Filename .= '_copy';
			$this->Text = $this->Filename . $this->Extension;
			//$this->setParentID($parentIDMerk);
			$this->Path = $this->ParentPath . $this->Text;
			$this->OldPath = $this->Path;
		}
		$temp->resetElements();
		$k = $v = '';
		while((list($k, $v) = $temp->nextElement('txt'))){
			$this->setElement($k, $temp->getElement($k), 'txt');
		}
		$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
		echo we_html_element::jsElement('
var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
_currentEditorRootFrame.frames[2].reloadContent = true;');
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case WE_EDITPAGE_PROPERTIES:
				return "we_templates/we_editor_properties.inc.php";
			case WE_EDITPAGE_INFO:
				return "we_templates/we_editor_info.inc.php";
			case WE_EDITPAGE_CONTENT:
				$GLOBALS["we_editmode"] = true;
				return "we_templates/we_srcTmpl.inc.php";
			case WE_EDITPAGE_PREVIEW:
				$GLOBALS["we_editmode"] = true;
				$GLOBALS["we_file_to_delete_after_include"] = TEMP_PATH . '/' . we_base_file::getUniqueId();
				we_util_File::saveFile($GLOBALS["we_file_to_delete_after_include"], $this->i_getDocument());
				return $GLOBALS["we_file_to_delete_after_include"];
			case WE_EDITPAGE_PREVIEW_TEMPLATE:
				$GLOBALS["we_editmode"] = false;
				$GLOBALS["we_file_to_delete_after_include"] = TEMP_PATH . '/' . we_base_file::getUniqueId();
				we_util_File::saveFile($GLOBALS["we_file_to_delete_after_include"], $this->i_getDocument());
				return $GLOBALS["we_file_to_delete_after_include"];
			case WE_EDITPAGE_VARIANTS:
				$GLOBALS["we_editmode"] = true;
				return 'we_templates/we_editor_variants.inc.php';
			case WE_EDITPAGE_VERSIONS:
				return "we_versions/we_editor_versions.inc.php";
			default:
				$this->EditPageNr = WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = WE_EDITPAGE_PROPERTIES;
				return "we_templates/we_editor_properties.inc.php";
		}
	}

	/* 	private static function checkEndtags($tagname, $eq, $tags){
	  $start = 0;
	  $end = 0;
	  foreach($tags as $tag){
	  if(strpos($tag, 'ifNoJavaScript') === false){
	  if($eq){
	  if(preg_match('|<we:' . $tagname . '[> ]|', $tag))
	  $start++;
	  if(preg_match('|</we:' . $tagname . '[> ]|', $tag))
	  $end++;
	  }else{
	  if(strpos($tag, '<we:' . $tagname) !== false)
	  $start++;
	  if(strpos($tag, '</we:' . $tagname) !== false)
	  $end++;
	  }
	  }
	  }
	  if($start != $end){
	  return parseError(sprintf($this->Text . ': ' . g_l('parser', '[start_endtag_missing]'), $tagname . ((!$eq) ? "..." : "")));
	  }
	  return '';
	  }

	  private static function removeDoppel($tags){
	  $out = array();
	  foreach($tags as $tag){
	  if(!in_array($tag, $out))
	  $out[] = $tag;
	  }
	  return $out;
	  } */

	private static function findIfStart($tags, $nr){
		if($nr == 0){
			return -1;
		}
		$foo = array();
		$regs = array();
		for($i = $nr; $i >= 0; $i--){
			if(preg_match('%<(/?)we:if([[:alpha:]]+)( *[[:alpha:]]+ *= *"[^"]*")* */?>?%i', $tags[$i], $regs)){
				if($regs[1] == '/'){
					$foo[$regs[2]] = isset($foo[$regs[2]]) ? $foo[$regs[2]] + 1 : 1;
				} else {
					if(empty($foo)){
						return $i;
					} else if(isset($foo[$regs[2]]) && intval($foo[$regs[2]])){
						$foo[$regs[2]] = intval($foo[$regs[2]]) - 1;
					} else {
						return $i;
					}
				}
			}
		}
		return -1;
	}

	private static function findIfEnd($tags, $nr){
		if($nr == count($tags)){
			return -1;
		}
		$foo = array();
		$regs = array();
		for($i = $nr; $i < count($tags); $i++){
			if(preg_match('%<(/?)we:if([[:alpha:]]+)( *[[:alpha:]]+ *= *"[^"]*")* */?>?%i', $tags[$i], $regs)){
				if($regs[1] != '/'){
					$foo[$regs[2]] = isset($foo[$regs[2]]) ? $foo[$regs[2]] + 1 : 1;
				} else {
					if(empty($foo)){
						return $i;
					} else if(isset($foo[$regs[2]]) && intval($foo[$regs[2]])){
						$foo[$regs[2]] = intval($foo[$regs[2]]) - 1;
					} else {
						return $i;
					}
				}
			}
		}
		return -1;
	}

	private static function checkElsetags($tags){
		for($i = 0; $i < count($tags); $i++){
			if(strpos($tags[$i], '<we:else') !== false){
				$ifStart = self::findIfStart($tags, $i);
				if($ifStart == -1){
					return parseError(g_l('parser', '[else_start]'));
				}
				if(self::findIfEnd($tags, $i) == -1){
					return parseError(g_l('parser', '[else_end]'));
				}
			}
		}
		return '';
	}

	public function handleShutdown($code){
		if($GLOBALS['we']['errorhandler']['shutdown'] == 'template'){
			$error = error_get_last();
			$tmp = explode("\n", $code);
			$errCode = "\n";
			for($ln = $error['line'] - 2; $ln <= $error['line'] + 2; $ln++){
				$errCode.=$ln . ': ' . $tmp[$ln] . "\n";
			}

			//FIXME: this->Path ist bei rebuild nicht gesetzt
			t_e('error', 'Error in template:' . $this->Path, $error, 'Code: ' . $errCode);
		}
	}

	private function parseTemplate(){
		$code = str_replace("<?xml", '<?php print "<?xml"; ?>', $this->getTemplateCode(true));
		//$code = preg_replace('/(< *\/? *we:[^>]+>\n)/i','\1'."\n",$code);
		$tp = new we_tag_tagParser($code, $this->getPath());
		$tags = $tp->getAllTags();
		if(($foo = self::checkElsetags($tags))){
			$this->errMsg = $foo;
			return $foo;
		}
		/* if(($foo = self::checkEndtags('if',0,$tags))){
		  return $foo;
		  } */

		if(($foo = $tp->parseTags($code)) !== true){
			$this->errMsg = str_replace('<we>', '<we:', strip_tags(str_replace('<we:', '<we>', html_entity_decode($foo, ENT_QUOTES, $GLOBALS['WE_BACKENDCHARSET'])), '<we>'));
			return $foo;
		}

		if(!DISABLE_TEMPLATE_CODE_CHECK && $this->doUpdateCode){
			$GLOBALS['we']['errorhandler']['shutdown'] = 'template';
			register_shutdown_function(array($this, 'handleShutdown'), $code);

			//remove "use" since this is not allowed inside functions
			$var = create_function('', '?>' . preg_replace('|use [\w,\s\\\\]*;|', '', $code) . '<?php ');
			if(empty($var) && ( $error = error_get_last() )){
				$tmp = explode("\n", $code);
				if(!is_array($tmp)){
					$tmp = explode("\r", $code);
				}
				$errCode = "\n";
				for($ln = max(0,$error['line'] - 2); $ln <= $error['line'] + 2 && isset($tmp[$ln]); $ln++){
					$errCode.=$ln . ': ' . $tmp[$ln] . "\n";
				}

				$this->errMsg = "Error: " . $error['message'] . "\nLine: " . $error['line'] . "\nCode: " . $errCode;
				//type error will stop we
				t_e('warning', "Error in template: " . we_tag_tagParser::$curFile, $error['message'], 'Line: ' . $error['line'], 'Code: ' . $errCode);
			}
			$GLOBALS['we']['errorhandler']['shutdown'] = 'we';
		}


		// Code must be executed every time a template is included,
		// so it must be executed during the caching process when a cacheable document
		// is called for the first time and every time the document come from the cache
		// Because of this reason the following code must be putted out directly and(!)
		// echoed in templates with CacheType = document
		$pre_code = '<?php
	// Activate the webEdition error handler
	require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/webEdition/we/include/we_error_handler.inc.php\');
	we_error_handler(false);

	require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/webEdition/we/include/we_global.inc.php\');
	require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/webEdition/we/include/we_tag.inc.php\');
	we_templateInit();?>';


		if($this->hasStartAndEndTag('html', $code) && $this->hasStartAndEndTag('head', $code) && $this->hasStartAndEndTag('body', $code)){
			$pre_code = '<?php $GLOBALS[\'WE_HTML_HEAD_BODY\'] = true; ?>' . $pre_code;

			//#### parse base href
			$code = str_replace(array('?>', '=>'), array('__WE_?__WE__', '__WE_=__WE__'), $code);

			$code = preg_replace('%(<body[^>]*)(>)%i', '\\1<?php if(isset($GLOBALS[\'we_editmode\']) && $GLOBALS[\'we_editmode\']) print \' onunload="doUnload()"\'; ?>\\2' . self::TemplatePreContent, $code);

			$code = str_replace(array('__WE_?__WE__', '__WE_=__WE__'), array('?>', '=>'), $code);
			$code = str_ireplace(array('</head>', '</body>'), array(self::TemplateHead . '</head>', self::TemplatePostContent . '</body>'), $code);
		} else if(!$this->hasStartAndEndTag('html', $code) && !$this->hasStartAndEndTag('head', $code) && !$this->hasStartAndEndTag('body', $code)){
			$code = '<?php if((isset($GLOBALS[\'we_editmode\']) && $GLOBALS[\'we_editmode\']) && (!isset($GLOBALS[\'WE_HTML_HEAD_BODY\']) || !$GLOBALS[\'WE_HTML_HEAD_BODY\'] )){  $GLOBALS["WE_HTML_HEAD_BODY"] = true; ?>' . we_html_element::htmlDocType() . '<html><head><title>WE</title>' . self::TemplateHead . '</head>
<body <?php if(isset($GLOBALS[\'we_editmode\']) && $GLOBALS[\'we_editmode\']) print \' onunload="doUnload()"\'; ?>><?php } ?>
' . self::TemplatePreContent . $code . self::TemplatePostContent . '<?php if((isset($GLOBALS[\'we_editmode\']) && $GLOBALS[\'we_editmode\'])&&($GLOBALS[\'we_templatePreContent\']==0) ){ ?>' . '
</body></html><?php }?>';
		} else {
			return parseError(g_l('parser', '[html_tags]')) . '<?php exit();?><!-- current parsed template code for debugging -->' . $code;
		}
		$code = str_replace(array('exit(', 'die(', 'exit;'), array('we_TemplateExit(', 'we_TemplateExit(', 'we_TemplateExit();'), $code);
		return $pre_code . $code . '<?php we_templatePost();';
	}

	private function hasStartAndEndTag($tagname, $code){
		return preg_match('%< ?/ ?' . $tagname . '[^>]*>%i', $code) && preg_match('%< ?' . $tagname . '[^>]*>%i', $code) && preg_match('%< ?' . $tagname . '[ >]%i', $code) && preg_match('%< ?/ ?' . $tagname . '[ >]%i', $code);
	}

### NEU###

	protected function i_isElement($Name){
		return (substr($Name, 0, 8) == "variant_" || $Name == "data" || $Name == "Charset" || $Name == "completeData" || $Name == "allVariants");
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		//get clean variants
		$regs = array();
		foreach($_REQUEST as $n => $v){
			if(preg_match('|^we_' . $this->Name . '_variant|', $n, $regs)){
				if(is_array($v)){
					foreach($v as $n2 => $v2){
						if(isset($this->elements[$n2]) && $this->elements[$n2]['type'] == 'variant' && $v2 == 0){
							unset($this->elements[$n2]);
						}
					}
				}
			}
		}
	}

	function i_getDocument(){
		$this->_updateCompleteCode();
		/* remove unwanted/-needed start/stop parser tags (?><php) */
		return preg_replace("/(;|{|})(\r|\n| |\t)*\?>(\r|\n|\t)*<\?php ?/si", "\\1\\2\n", $this->parseTemplate());
	}

	protected function i_writeSiteDir(){
		return true;
	}

	protected function i_writeMainDir($doc){
		if($this->isMoved()){
			we_util_File::deleteLocalFile($this->getRealPath(true));
		}
		return we_util_File::saveFile($this->getRealPath(), $doc);
	}

	function i_filenameNotAllowed(){
		return false;
	}

	/**
	 * returns if this template contains fields required for a shop-document.
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
		$fieldnames = $this->getVariantFieldNames();
		return in_array(WE_SHOP_TITLE_FIELD_NAME, $fieldnames) && in_array(WE_SHOP_DESCRIPTION_FIELD_NAME, $fieldnames);
	}

	/**
	 * @desc 	the function returns the array with selected variant field names and field attributes/types
	 * @return	array with the selected filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
		$ret = array();
		$fields = $this->getAllVariantFields();
		if(empty($fields)){
			return $fields;
		}
		$element_names = array();
		$names = array_keys($this->elements);
		foreach($names as $name){
			if(substr($name, 0, 8) == 'variant_'){
				$element_names[] = substr($name, 8);
			}
		}
		foreach($fields as $name => $value){
			if(in_array($name, $element_names)){
				$ret[$name] = $value;
			}
		}
		return $ret;
	}

	/**
	 * @desc 	the function returns the array with all variant field names
	 * @return	array with the varinat filed names
	 * @param	none
	 */
	function getVariantFieldNames(){
		if(!defined('SHOP_TABLE')){
			return array();
		}
		$fields = $this->getAllVariantFields();
		return (is_array($fields) ? array_keys($fields) : array());
	}

	/**
	 * @desc 	the function returns the array with all template field names and field attributes/types;
	 * 			if there is no fields in the elements, the template code will be parsed
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getAllVariantFields(){
		return (isset($this->elements['allVariants']) ? $this->elements['allVariants']['dat'] : array());
	}

	/**
	 * @desc 	the function parses the template code and returns all template field names and field attributes/types
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function readAllVariantFields($includedatefield = false){
		$variant_tags = array('input', 'link', 'textarea', 'img', 'select');
		$templateCode = $this->getTemplateCode();
		$tp = new we_tag_tagParser($templateCode, $this->getPath());
		$tags = $tp->getAllTags();

		$blocks = array();
		$out = array();
		$regs = array();

		foreach($tags as $tag){
			if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){ // starttag found
				$tagname = $regs[1];
				if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != 'var') && ($tagname != 'field')){ // name found
					$name = $regs[1];

					if(!empty($blocks)){
						$foo = end($blocks);
						$blockname = $foo["name"];
						switch($foo['type']){
							case 'list':
							case 'block':
								$name = we_webEditionDocument::makeBlockName($blockname, $name);
								break;
							case 'linklist':
								$name = we_webEditionDocument::makeLinklistName($blockname, $name);
								break;
						}
					}

					$att = we_tag_tagParser::makeArrayFromAttribs(str_ireplace('<we:' . $tagname, '', $tag));

					if(in_array($tagname, $variant_tags)){
						if($tagname == 'input' && isset($att['type']) && $att['type'] == 'date' && !$includedatefield){
							// do nothing
						} else {
							$out[$name] = array(
								'type' => $tagname,
								'attributes' => $att
							);
						}
						//additional parsing for selects
						if($tagname == 'select'){
							$spacer = "[\040|\n|\t|\r]*";
							$selregs = array();
							//FIXME: this regex is not correct [^name] will not match any of those chars
							if(preg_match('-(<we:select [^name]*name' . $spacer . '[\=\"|\=\'|\=\\\\|\=]*' . $spacer . $att['name'] . '[\'\"]*[^>]*>)(.*)<' . $spacer . '/' . $spacer . 'we:select' . $spacer . '>-i', $templateCode, $selregs)){
								$out[$name]['content'] = $selregs[2];
							}
						}
					}

					switch($tagname){
						case 'list':
							$tagname = 'block';
						case 'block':
						case 'linklist':
							$blocks[] = array(
								'name' => $name,
								'type' => $tagname
							);
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
		ksort($out);
		return $out;
	}

	function formMasterTemplate(){
		$yuiSuggest = & weSuggest::getInstance();
		$table = TEMPLATES_TABLE;
		$textname = 'MasterTemplateNameDummy';
		$idname = 'we_' . $this->Name . '_MasterTemplateID';
		$myid = $this->MasterTemplateID ? $this->MasterTemplateID : '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), "Path", $this->DB_WE);
		$alerttext = str_replace('\'', "\\\\\\'", g_l('weClass', '[same_master_template]'));
		//javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','document.we_form.elements[\\'$idname\\'].value','document.we_form.elements[\\'$textname\\'].value','opener._EditorFrame.setEditorIsHot(true);if(currentID==$this->ID){" . we_message_reporting::getShowMessageCall($alerttext, we_message_reporting::WE_MESSAGE_ERROR) . "opener.document.we_form.elements[\\'$idname\\'].value=\'\';opener.document.we_form.elements[\\'$textname\\'].value=\\'\\';}','".session_id()."','','text/weTmpl',1)"
		$wecmdenc1 = we_cmd_enc("document.we_form.elements['$idname'].value");
		$wecmdenc2 = we_cmd_enc("document.we_form.elements['$textname'].value");
		$wecmdenc3 = we_cmd_enc("opener._EditorFrame.setEditorIsHot(true);if(currentID==$this->ID){" . we_message_reporting::getShowMessageCall($alerttext, we_message_reporting::WE_MESSAGE_ERROR) . "opener.document.we_form.elements['$idname'].value='';opener.document.we_form.elements['$textname'].value='';}");

		$button = we_html_button::create_button('select', "javascript:we_cmd('openDocselector',document.we_form.elements['$idname'].value,'$table','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','" . session_id() . "','','text/weTmpl',1)");
		$openButton = we_html_button::create_button('edit', 'javascript:goTemplate(document.we_form.elements[\'we_' . $GLOBALS['we_doc']->Name . '_MasterTemplateID\'].value)');
		$trashButton = we_html_button::create_button(we_html_button::WE_IMAGE_BUTTON_IDENTIFY . 'btn_function_trash', "javascript:document.we_form.elements['$idname'].value='';document.we_form.elements['$textname'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputMasterTemplate');_EditorFrame.setEditorIsHot(true);", true, 27, 22);

		$yuiSuggest->setAcId('MasterTemplate');
		$yuiSuggest->setContentType('folder,text/weTmpl');
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel('');
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector('Docselector');
		$yuiSuggest->setTable($table);
		$yuiSuggest->setWidth(388);
		$yuiSuggest->setSelectButton($button);
		$yuiSuggest->setTrashButton($trashButton);
		$yuiSuggest->setOpenButton($openButton);
		return $yuiSuggest->getHTML();
	}

	private function isUsedByDocuments(){
		if($this->ID == 0){
			return array();
		}
		$this->DB_WE->query('SELECT ID, CONCAT(Path," (ID: ",ID,")") FROM ' . FILE_TABLE . ' WHERE temp_template_id=' . intval($this->ID) . ' OR (temp_template_id=0 AND TemplateID=' . intval($this->ID) . ') ORDER BY Path');
		return $this->DB_WE->getAllFirst(false);
	}

	function formTemplateDocuments(){
		if($this->ID == 0){
			return g_l('weClass', '[no_documents]');
		}
		$textname = 'TemplateDocuments';

		$path = $this->isUsedByDocuments();

		if(empty($path)){
			return g_l('weClass', "[no_documents]");
		}

		$button = we_html_button::create_button('open', "javascript:top.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.we_form.elements['TemplateDocuments'].value, 'text/webedition');");
		$foo = $this->htmlSelect($textname, $path, 1, '', false, '', 'value', 388);
		return we_html_tools::htmlFormElementTable($foo, '', 'left', 'defaultfont', '', we_html_tools::getPixel(20, 4), $button);
	}

	/**
	 * @desc 	this function returns the code of the unparsed template
	 * @return	array with the filed names and attributes
	 * @param	boolean $completeCode if true then the function returns the code of the complete template (with master template and included templates)
	 */
	function getTemplateCode($completeCode = true){
		return $completeCode ? $this->getElement('completeData') : $this->getElement('data');
	}

	/* setter for runtime variable doUpdateCode which allows save a class without rebuilding everything -> for later rebuild
	  do not access this variable directly, in later WE Versions, it will be protected */

	public function setDoUpdateCode($doUpdateCode = true){
		$this->doUpdateCode = $doUpdateCode;
	}

	/* getter for runtime variable doUpdateCode which allows save a class without rebuilding everything -> for later rebuild
	  do not access this variable directly, in later WE Versions, it will be protected */

	public function getDoUpdateCode(){
		return $this->doUpdateCode;
	}

	static function getUsedTemplatesOfTemplate($id, &$arr){
		$hash = getHash('SELECT IncludedTemplates, MasterTemplateID FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($id), $GLOBALS['DB_WE']);
		list($_tmplCSV, $_masterTemplateID) = ($hash ? $hash : array('', 0));

		$_tmpArr = makeArrayFromCSV($_tmplCSV);
		foreach($_tmpArr as $_tid){
			if(!in_array($_tid, $arr) && $_tid != $id){
				$arr[] = $_tid;
			}
		}
		foreach($_tmpArr as $_tid){
			if($id != $_tid){
				self::getUsedTemplatesOfTemplate($_tid, $arr);
			}
		}

		$_tmpArr = makeArrayFromCSV($_tmplCSV);
		foreach($_tmpArr as $_tid){
			if(!in_array($_tid, $arr) && $_tid != $id){
				$arr[] = $_tid;
			}
		}
		if($_masterTemplateID && !in_array($_masterTemplateID, $arr)){
			if($_masterTemplateID != $id){
				self::getUsedTemplatesOfTemplate($_masterTemplateID, $arr);
			}
		}

		foreach($_tmpArr as $_tid){
			if($id != $_tid){
				self::getUsedTemplatesOfTemplate($_tid, $arr);
			}
		}
	}

	function _updateCompleteCode(){
		if(!$this->doUpdateCode){
			return true;
		}
		static $cnt = 0;
		static $recursiveTemplates;
		if($cnt == 0){
			$recursiveTemplates = array();
		}
		if(empty($recursiveTemplates)){
			$recursiveTemplates[] = $this->ID;
		}
		++$cnt;
		$code = $this->getTemplateCode(false);

		// find all we:master Tags
		$masterTags = $regs = array();

		preg_match_all('|(<we:master([^>+]*)>)\n?([\\s\\S]*?)</we:master>\n?|', $code, $regs, PREG_SET_ORDER);


		foreach($regs as $reg){
			$attribs = we_tag_tagParser::parseAttribs(isset($reg[2]) ? $reg[2] : '', true);
			$name = isset($attribs['name']) ? $attribs['name'] : '';
			if($name){
				$masterTags[$name] = array(
					//'all' => $reg[0],
					//'startTag' => $reg[1],
					'content' => isset($reg[3]) ? $reg[3] : '',
				);
				$code = str_replace($reg[0], '', $code);
			}
		}

		if($this->MasterTemplateID != 0){

			$_templates = array();
			self::getUsedTemplatesOfTemplate($this->MasterTemplateID, $_templates);
			if(in_array($this->ID, $_templates) || $this->ID == $this->MasterTemplateID || in_array($this->MasterTemplateID, $recursiveTemplates)){
				$code = g_l('parser', '[template_recursion_error]');
				t_e(g_l('parser', '[template_recursion_error]'), 'Template ' . $this->ID, 'Mastertemplate: ' . $this->MasterTemplateID, 'Templates of Master: ' . implode(',', $_templates), 'already processed: ' . implode(',', $recursiveTemplates));
			} else {
				// we have a master template. => surround current template with it
				// first get template code
				$recursiveTemplates[] = $this->MasterTemplateID;
				$templObj = new we_template();
				$templObj->initByID($this->MasterTemplateID, TEMPLATES_TABLE);
				$masterTemplateCode = $templObj->getTemplateCode(true);
				array_pop($recursiveTemplates);

				$contentTags = array();
				preg_match_all('|<we:content ?([^>+]*)/?>\n?|', $masterTemplateCode, $contentTags, PREG_SET_ORDER);

				foreach($contentTags as $reg){
					$all = $reg[0];
					$attribs = we_tag_tagParser::parseAttribs($reg[1], true);
					$name = isset($attribs['name']) ? $attribs['name'] : '';
					$masterTemplateCode = str_replace($all, ($name ?
							(isset($masterTags[$name]['content']) ?
								$masterTags[$name]['content'] :
								'') :
							$code), $masterTemplateCode);
				}

				$code = str_replace('</we:content>', '', $masterTemplateCode);
			}
		}
		$this->IncludedTemplates = '';
		// look for included templates (<we:include type="template" id="99">)
		$tp = new we_tag_tagParser($code, $this->getPath());
		$tags = $tp->getAllTags();
		// go through all tags
		$regs = array();
		foreach($tags as $tag){
			// search for include tag
			if(preg_match('|^<we:include ([^>]+)>$|mi', $tag, $regs)){ // include found
				// get attributes of tag
				$att = we_tag_tagParser::parseAttribs($regs[1], true);
				// if type-attribute is equal to "template"
				if(isset($att['type']) && $att['type'] == 'template'){

					// if path is set - look for the id of the template
					if(isset($att['path']) && $att['path']){
						// get id of template
						$templId = path_to_id($att['path'], TEMPLATES_TABLE);
						if($templId){
							$att['id'] = $templId;
						} elseif(!isset($att['id'])){
							continue;
						}
					}

					// if id attribute is set and greater 0
					if(isset($att['id']) && intval($att['id']) != 0){
						$_templates = array();
						self::getUsedTemplatesOfTemplate($att['id'], $_templates);
						if(in_array($this->ID, $_templates) || $att['id'] == $this->ID || in_array($att['id'], $recursiveTemplates)){
							$code = str_replace($tag, g_l('parser', '[template_recursion_error]'), $code);
							t_e(g_l('parser', '[template_recursion_error]'), 'Template ' . $this->ID, 'Included Template: ' . $att['id'], 'Templates of include: ' . implode(',', $_templates), 'already processed: ' . implode(',', $recursiveTemplates));
						} else {
							// get code of template
							$recursiveTemplates[] = $att['id'];
							$templObj = new we_template();
							$templObj->initByID($att['id'], TEMPLATES_TABLE);
							//$completeCode = (!(isset($att['included']) && ($att['included'] == 'false' || $att["included"] === '0' || $att['included'] == "off")));
							$includedTemplateCode = $templObj->getTemplateCode(true);
							array_pop($recursiveTemplates);
							// replace include tag with template code
							if(strpos($includedTemplateCode, 'we:content') !== false || strpos($includedTemplateCode, 'we:master') !== false){
								//only insert code if really needed!
								$code = str_replace($tag, $includedTemplateCode, $code);
							}
							$this->IncludedTemplates .= ',' . intval($att['id']);
						}
					}
				}
			}
		}
		if(strlen($this->IncludedTemplates) > 0){
			$this->IncludedTemplates .= ',';
		}
		$this->setElement('completeData', $code);
		--$cnt;
	}

	public function we_save($resave = 0, $updateCode = 1){
		$this->Extension = we_base_ContentTypes::inst()->getExtension('text/weTmpl');
		if($updateCode){
			$this->_updateCompleteCode(true);
			if(defined('SHOP_TABLE')){
				$this->elements['allVariants'] = array(
					'type' => 'variants',
					'dat' => serialize($this->readAllVariantFields($this->elements['completeData']['dat']))
				);
			}
		} else {
			$this->doUpdateCode = false;
		}
		$_ret = parent::we_save($resave);
		if($_ret){
			$tmplPathWithTmplExt = parent::getRealPath();
			if(file_exists($tmplPathWithTmplExt)){
				unlink($tmplPathWithTmplExt);
			}
		}
		if(defined('SHOP_TABLE')){
			$this->elements['allVariants']['dat'] = unserialize($this->elements['allVariants']['dat']);
		}
		return $_ret;
	}

	public function we_publish(){
		if(VERSIONS_CREATE_TMPL){
			$version = new weVersions();
			$version->save($this, "published");
		}
		return true;
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		$this->Extension = we_base_ContentTypes::inst()->getExtension('text/weTmpl');
		$this->_updateCompleteCode();
		if(defined('SHOP_TABLE') && isset($this->elements['allVariants'])){
			$this->elements['allVariants']['dat'] = @unserialize($this->elements['allVariants']['dat']);
			if(!is_array($this->elements['allVariants']['dat'])){
				$this->elements['allVariants']['dat'] = $this->readAllVariantFields($this->elements['completeData']['dat']);
			}
		}
	}

	// .tmpl mod

	function getRealPath($old = false){
		return preg_replace('/.tmpl$/i', '.php', parent::getRealPath($old));
	}

}
