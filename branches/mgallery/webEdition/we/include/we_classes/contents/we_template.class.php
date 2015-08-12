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

class we_template extends we_document{
	var $MasterTemplateID = 0;
	var $TagWizardCode; // bugfix 1502
	var $TagWizardSelection; // bugfix 1502
	var $IncludedTemplates = '';
	var $doUpdateCode = true; // will be protected in later WE Versions

	const NO_TEMPLATE_INC = 'we_noTmpl.inc.php';

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->Table = TEMPLATES_TABLE;

		array_push($this->persistent_slots, 'MasterTemplateID', 'IncludedTemplates', 'TagWizardCode', 'TagWizardSelection');
		$this->setElement('Charset', DEFAULT_CHARSET, 'attrib');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, we_base_constants::WE_EDITPAGE_VARIANTS, we_base_constants::WE_EDITPAGE_VERSIONS);
		}
		$this->Published = 1;
		$this->InWebEdition = true;
		$this->ContentType = we_base_ContentTypes::TEMPLATE;
		$this->Extension = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::TEMPLATE);
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
		$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
		return we_html_element::jsElement('
var _currentEditorRootFrame = top.weEditorFrameController.getActiveDocumentReference();
_currentEditorRootFrame.frames[2].reloadContent = true;');
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			case we_base_constants::WE_EDITPAGE_PROPERTIES:
				return "we_editors/we_editor_properties.inc.php";
			case we_base_constants::WE_EDITPAGE_INFO:
				return "we_editors/we_editor_info.inc.php";
			case we_base_constants::WE_EDITPAGE_CONTENT:
				$GLOBALS["we_editmode"] = true;
				return "we_editors/we_srcTmpl.inc.php";
			case we_base_constants::WE_EDITPAGE_PREVIEW:
				$GLOBALS["we_editmode"] = true;
				$GLOBALS["we_file_to_delete_after_include"] = TEMP_PATH . we_base_file::getUniqueId();
				we_base_file::save($GLOBALS["we_file_to_delete_after_include"], $this->i_getDocument());
				return $GLOBALS["we_file_to_delete_after_include"];
			case we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE:
				$GLOBALS["we_editmode"] = false;
				$GLOBALS["we_file_to_delete_after_include"] = TEMP_PATH . we_base_file::getUniqueId();
				we_base_file::save($GLOBALS["we_file_to_delete_after_include"], $this->i_getDocument());
				return $GLOBALS["we_file_to_delete_after_include"];
			case we_base_constants::WE_EDITPAGE_VARIANTS:
				$GLOBALS["we_editmode"] = true;
				return 'we_editors/we_editor_variants.inc.php';
			case we_base_constants::WE_EDITPAGE_VERSIONS:
				return "we_editors/we_editor_versions.inc.php";
			default:
				$this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
				$_SESSION['weS']['EditPageNr'] = we_base_constants::WE_EDITPAGE_PROPERTIES;
				return "we_editors/we_editor_properties.inc.php";
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
				if($regs[1] === '/'){
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
		if($GLOBALS['we']['errorhandler']['shutdown'] === 'template'){
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
		$code = str_replace("<?xml", '<?php echo "<?xml"; ?>', $this->getTemplateCode(true));
		//$code = preg_replace('/(< *\/? *we:[^>]+>\n)/i','$1'."\n",$code);
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
				for($ln = max(0, $error['line'] - 2); $ln <= $error['line'] + 2 && isset($tmp[$ln]); $ln++){
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
		$pre_code = '<?php /*Generated by WE ' . WE_VERSION . ', SVN ' . WE_SVNREV . ', ' . date('Y-m-d, H:i') . '*/
require_once($_SERVER[\'DOCUMENT_ROOT\'].\'/webEdition/we/include/we_global.inc.php\');
we_templateInit();?>';

		if($this->hasStartAndEndTag('html', $code) && $this->hasStartAndEndTag('head', $code) && $this->hasStartAndEndTag('body', $code)){
			$pre_code .= '<?php $GLOBALS[\'WE_HTML_HEAD_BODY\']=true; ?>';
			$repl = array(
				'?>' => '__WE_?__WE__',
				'=>' => '__WE_=__WE__',
				'->' => '__WE_-__WE__'
			);
			$code = str_replace(array_keys($repl), $repl, $code);
			//#### parse base href
			$code = preg_replace(array(
				'%(<body[^>]*)>%i',
				'%(<head[^>]*>)%i',
				'%(</body[^>]*>)%i',
				), array(
				'${1}<?php echo (!empty($GLOBALS[\'we_editmode\']) ? \' onload="doScrollTo();" onunload="doUnload()">\':\'>\'); we_templatePreContent(true);?>',
				'${1}<?php we_templateHead();?>',
				'<?php we_templatePostContent(true);?>${1}'
				), $code);

			$code = str_replace($repl, array_keys($repl), $code);
		} else if(!$this->hasStartAndEndTag('html', $code) && !$this->hasStartAndEndTag('head', $code) && !$this->hasStartAndEndTag('body', $code)){
			$code = '<?php we_templateHead(true);?>' . $code . '<?php we_templatePostContent(false,true);?>';
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
		switch($Name){
			case "data":
			case "Charset":
			case "completeData":
			case "allVariants":
				return true;
			default:
				return (substr($Name, 0, 8) === 'variant_');
		}
	}

	protected function i_setElementsFromHTTP(){
		parent::i_setElementsFromHTTP();
		//get clean variants
		$regs = array();
		foreach($_REQUEST as $n => $v){
			if(is_array($v) && preg_match('|^we_' . $this->Name . '_variant|', $n, $regs)){
				foreach($v as $n2 => $v2){
					if($this->getElement($n2, 'type') === 'variant' && $v2 == 0){
						$this->delElement($n2);
					}
				}
			}
		}
	}

	function i_getDocument(){
		$this->_updateCompleteCode();
		/* remove unwanted/-needed start/stop parser tags (?><php) */
		return preg_replace(array("/(:|;|{|})(\r|\n| |\t)*\?>(\r|\n|\t)*<\?= ?/si", "/(:|;|{|})(\r|\n| |\t)*\?>(\r|\n|\t)*<\?php ?/si"), array('${1}' . "\n" . '${2} echo ', '${1}' . "\n" . '${2}'), $this->parseTemplate());
	}

	protected function i_writeSiteDir(){
		return true;
	}

	protected function i_writeMainDir($doc){
		if($this->isMoved()){
			we_base_file::deleteLocalFile($this->getRealPath(true));
		}
		return we_base_file::save($this->getRealPath(), $doc);
	}

	protected function i_filenameNotAllowed(){
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
		return true;
	}

	/**
	 * @desc 	the function returns the array with selected variant field names and field attributes/types
	 * @return	array with the selected filed names and attributes
	 * @param	none
	 */
	function getVariantFields(){
		$ret = array();
		$fields = $this->getAllVariantFields();
		if(!$fields){
			return array();
		}
		foreach(array_keys($fields)as $name){
			if($this->getElement('variant_' . $name)){
				$ret[$name] = $fields[$name];
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
		return ($this->getElement('allVariants') ? : array());
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
								$name = self::makeBlockName($blockname, $name);
								break;
							case 'linklist':
								$name = self::makeLinklistName($blockname, $name);
								break;
						}
					}

					$att = we_tag_tagParser::makeArrayFromAttribs(str_ireplace('<we:' . $tagname, '', $tag));

					if(in_array($tagname, $variant_tags)){
						if($tagname === 'input' && isset($att['type']) && $att['type'] === 'date' && !$includedatefield){
							// do nothing
						} else {
							$out[$name] = array(
								'type' => $tagname,
								'attributes' => $att
							);
						}
						//additional parsing for selects
						if($tagname === 'select'){
							$spacer = '[ |\n|\t|\r]*';
							$selregs = array();
							//FIXME: this regex is not correct [^name] will not match any of those chars
							if(preg_match('-(<we:select [^name]*name' . $spacer . '[\=\"|\=\'|\=\\\\|\=]*' . $spacer . preg_quote($att['name'], '-') . '[\'\"]*[^>]*>)(.*)<' . $spacer . '/' . $spacer . 'we:select' . $spacer . '>-i', $templateCode, $selregs)){
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
		$myid = $this->MasterTemplateID ? : '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), "", $this->DB_WE);
		$alerttext = str_replace('\'', "\\\\\\'", g_l('weClass', '[same_master_template]'));
		$cmd1 = "document.we_form.elements['" . $idname . "'].value";
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $textname . "'].value");
		$wecmdenc3 = we_base_request::encCmd("opener._EditorFrame.setEditorIsHot(true);if(currentID==$this->ID){" . we_message_reporting::getShowMessageCall($alerttext, we_message_reporting::WE_MESSAGE_ERROR) . "opener.document.we_form.elements['" . $idname . "'].value='';opener.document.we_form.elements['" . $textname . "'].value='';}");

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . $table . "','" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)");
		$openButton = we_html_button::create_button(we_html_button::EDIT, 'javascript:goTemplate(document.we_form.elements[\'we_' . $GLOBALS['we_doc']->Name . '_MasterTemplateID\'].value)');
		$trashButton = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';YAHOO.autocoml.selectorSetValid('yuiAcInputMasterTemplate');_EditorFrame.setEditorIsHot(true);", true, 27, 22);

		$yuiSuggest->setAcId('MasterTemplate');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$yuiSuggest->setInput($textname, $path);
		$yuiSuggest->setLabel('');
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult($idname, $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
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
		$this->DB_WE->query('SELECT ID, CONCAT(Path," (ID: ",ID,")"),IF(Published=0,"notpublished",IF(ModDate>Published,"changed","published")) FROM ' . FILE_TABLE . ' WHERE temp_template_id=' . intval($this->ID) . ' OR (temp_template_id=0 AND TemplateID=' . intval($this->ID) . ') ORDER BY Path');
		return $this->DB_WE->getAllFirst(true);
	}

	function formTemplateDocuments(){
		if($this->ID == 0){
			return array(0, g_l('weClass', '[no_documents]'));
		}
		if(!($elems = $this->isUsedByDocuments())){
			return array(0, g_l('weClass', '[no_documents]'));
		}
		$path = $elemAttribs = array();
		foreach($elems as $id => $data){
			$path[$id] = $data[0];
			$elemAttribs[$id] = $data[1];
		}

		return array(count($elems), we_html_tools::htmlFormElementTable($this->htmlSelect('TemplateDocuments', $path, 1, '', false, array('style' => 'margin-right: 20px;'), 'value', 388, $elemAttribs), '', 'left', 'defaultfont', '', we_html_button::create_button(we_html_button::EDIT, "javascript:top.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.we_form.elements['TemplateDocuments'].value, '" . we_base_ContentTypes::WEDOCUMENT . "');")));
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
		$hash = getHash('SELECT IncludedTemplates, MasterTemplateID FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($id));
		$_tmplCSV = ($hash ? $hash['IncludedTemplates'] : '');
		$_masterTemplateID = ($hash ? $hash['MasterTemplateID'] : 0);

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
				if(isset($att['type']) && $att['type'] === 'template'){

					// if path is set - look for the id of the template
					if(!empty($att['path'])){
						// get id of template
						$templId = path_to_id($att['path'], TEMPLATES_TABLE, $GLOBALS['DB_WE']);
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
		$this->IncludedTemplates.= ($this->IncludedTemplates ? ',' : '');

		$this->setElement('completeData', $code);
		--$cnt;
	}

	public function we_save($resave = false, $updateCode = true){
		$this->Extension = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::TEMPLATE);
		if($updateCode){
			$this->_updateCompleteCode(true);
			$this->setElement('allVariants', we_serialize($this->readAllVariantFields($this->getElement('completeData'))), 'variants');
		} else {
			$this->doUpdateCode = false;
		}
		$_ret = parent::we_save($resave);

		if($_ret){
			$tmplPathWithTmplExt = parent::getRealPath();
			if(file_exists($tmplPathWithTmplExt)){
				unlink($tmplPathWithTmplExt);
			}

			$this->unregisterMediaLinks();
			$_ret = $this->registerMediaLinks();
		} else {
			t_e('save template failed', $this->Path);
		}

		$this->setElement('allVariants', we_unserialize($this->getElement('allVariants')), 'variants');

		return $_ret;
	}

	public function we_publish(){
		if(VERSIONS_CREATE_TMPL){
			$version = new we_versions_version();
			$version->save($this, 'published');
		}
		return true;
	}

	public function we_load($from = we_class::LOAD_MAID_DB){
		parent::we_load($from);
		$this->Extension = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::TEMPLATE);
		$this->_updateCompleteCode();
		if(($tmp = $this->getElement('allVariants'))){
			$tmp = we_unserialize($tmp, '');
			$this->setElement('allVariants', (is_array($tmp) ?
					$tmp :
					$this->readAllVariantFields($this->getElement('completeData'))
				), 'variants');
		}
	}

	function registerMediaLinks(){
		$tp = new we_tag_tagParser($this->getTemplateCode());
		foreach($tp->getTagsWithAttributes() as $tag){
			switch($tag['name']){
				case 'icon':
				case 'img':
				case 'flash':
				case 'quicktime':
				case 'video':
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[] = intval($tag['attribs']['id']);
					}
					break;
				case 'url':
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'document' &&
						isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[] = intval($tag['attribs']['id']);
					}
					break;
				case 'link':
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[] = intval($tag['attribs']['id']);
					}
					if(isset($tag['attribs']['imageid']) && is_numeric($tag['attribs']['imageid'])){
						$this->MediaLinks[] = intval($tag['attribs']['imageid']);
					}
					break;
				case 'sessionfield':
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'img' &&
						isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[] = intval($tag['attribs']['id']);
					}
					break;

				// the following cases are not meant to link media files: we check them anyway
				case 'a': // selector: text/webEdition only
				case 'linkToSeeMode':// selector: text/webEdition only
				case 'metadata':// selector: text/webEdition only
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[] = intval($tag['attribs']['id']);
					}
					break;
				case 'include': //nur type=document: id, path
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'document'){
						if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
							$this->MediaLinks[] = intval($tag['attribs']['id']); // selector: text/webEdition only
						}
						if(isset($tag['attribs']['path']) && $tag['attribs']['path'] && ($id = path_to_id($tag['attribs']['path'], FILE_TABLE, $this->db))){
							$this->MediaLinks[] = intval($tag['attribs']['id']); // selector: text/webEdition only
						}
					}
					break;
				case 'listview':
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'document' && !empty($tag['attribs']['id'])){
						$ids = explode(',', $tag['attribs']['id']);
						foreach($ids as $id){
							$id = trim($id);
							if(is_numenric($id)){
								$this->MediaLinks[] = intval($id);
							}
						}
					}
					break;
				default:
				//
			}
		}

		if(!empty($this->MediaLinks)){
			return parent::registerMediaLinks(false, true);
		}
		return true;
	}

	// .tmpl mod

	function getRealPath($old = false){
		return preg_replace('/.tmpl$/i', '.php', parent::getRealPath($old));
	}

	public function getPropertyPage(){
		list($cnt, $select) = $this->formTemplateDocuments();
		echo we_html_multiIconBox::getHTML('', '100%', array(
			array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => 140),
			array('icon' => 'mastertemplate.gif', 'headline' => g_l('weClass', '[master_template]'), 'html' => $this->formMasterTemplate(), 'space' => 140),
			array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[documents]') . ($cnt ? ' (' . $cnt . ')' : ''), 'html' => $select, 'space' => 140),
			array('icon' => 'charset.gif', 'headline' => g_l('weClass', '[Charset]'), 'html' => $this->formCharset(), 'space' => 140),
			array('icon' => 'copy.gif', 'headline' => g_l('weClass', '[copyTemplate]'), 'html' => $this->formCopyDocument(), 'space' => 140)
			)
			, 20);
	}

	public static function we_getCodeMirror2Tags($css, $setting, $weTags = true){
		$ret = '';
		$allTags = array();
		if($weTags && ($css || $setting['WE'])){
			$allWeTags = we_wizard_tag::getExistingWeTags($css); //only load deprecated tags if css is requested
			foreach($allWeTags as $tagName){
				if(($weTag = weTagData::getTagData($tagName))){
					if($css){
						$ret.='.cm-weTag_' . $tagName . ':hover:after {content: "' . strtr(html_entity_decode($weTag->getDescription(), null, $GLOBALS['WE_BACKENDCHARSET']), array('"' => '\'', "\n" => ' ')) . '";}' . "\n";
					} else {
						$allTags['we:' . $tagName] = array('we' => $weTag->getAttributesForCM());
					}
				}
			}
		}
		if($css){
			return $ret;
		}

		$all = include(WE_INCLUDES_PATH . 'accessibility/htmlTags.inc.php');
		$allTags = array_merge($allTags, ($setting['htmlTag'] ? $all['html'] : array()), ($setting['html5Tag'] ? $all['html5'] : array()));
		if(!$allTags){
			return '';
		}
		//keep we tags in front of ordinal html tags
		$ret.='CodeMirror.weHints["<"] = ["' . implode('","', array_keys($allTags)) . '"];' . "\n";

		ksort($allTags);
		foreach($allTags as $tagName => $cur){
			$attribs = array();
			foreach($cur as $type => $attribList){
				switch($type){
					case 'we':
						$ok = true;
						break;
					case 'default':
						$ok = (!empty($setting['htmlDefAttr']));
						break;
					case 'js':
						$ok = (!empty($setting['htmlJSAttr']));
						break;
					case 'norm':
						$ok = (!empty($setting['htmlAttr']));
						break;
					case 'default_html5':
						$ok = (!empty($setting['html5Tag']) && !empty($setting['htmlDefAttr']));
						break;
					case 'html5':
						$ok = (!empty($setting['html5Tag']) && !empty($setting['html5Attr']));
						break;
					default:
						$ok = false;
				}
				if($ok){
					foreach($attribList as $attr){
						$attribs[] = '\'' . $attr . (strstr($attr, '"') === false ? '=""' : '') . '\'';
					}
				}
			}
			if($attribs){
				$attribs = array_unique($attribs);
				sort($attribs);
				$ret.='CodeMirror.weHints["<' . $tagName . ' "] = [' . implode(',', $attribs) . '];' . "\n";
			}
		}
		return $ret;
	}

}
