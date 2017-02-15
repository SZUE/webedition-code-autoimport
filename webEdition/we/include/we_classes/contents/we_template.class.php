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
	var $Display = '';

	const NO_TEMPLATE_INC = 'we_noTmpl.inc.php';

	/* Constructor */

	function __construct(){
		parent::__construct();
		$this->Table = TEMPLATES_TABLE;

		array_push($this->persistent_slots, 'MasterTemplateID', 'IncludedTemplates', 'TagWizardCode', 'TagWizardSelection', 'Display');
		$this->setElement('Charset', DEFAULT_CHARSET, 'attrib');
		if(isWE()){
			array_push($this->EditPageNrs, we_base_constants::WE_EDITPAGE_PROPERTIES, we_base_constants::WE_EDITPAGE_INFO, we_base_constants::WE_EDITPAGE_CONTENT, we_base_constants::WE_EDITPAGE_PREVIEW, we_base_constants::WE_EDITPAGE_PREVIEW_TEMPLATE, we_base_constants::WE_EDITPAGE_VARIANTS, we_base_constants::WE_EDITPAGE_VERSIONS, we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS);
		}
		$this->Published = 1;
		$this->InWebEdition = true;
		$this->ContentType = we_base_ContentTypes::TEMPLATE;
		$this->Extension = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::TEMPLATE);
	}

	function copyDoc($id){
		if(!$id){
			return false;
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
		return true;
	}

	/* must be called from the editor-script. Returns a filename which has to be included from the global-Script */

	function editor(){
		switch($this->EditPageNr){
			default:
				$_SESSION['weS']['EditPageNr'] = $this->EditPageNr = we_base_constants::WE_EDITPAGE_PROPERTIES;
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
			case we_base_constants::WE_EDITPAGE_TEMPLATE_UNUSEDELEMENTS:
				return 'we_editors/we_editor_unusedElements.inc.php';
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
	  $out = [];
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
		$foo = [];
		$regs = [];
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
		$foo = [];
		$regs = [];
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
				$errCode .= $ln . ': ' . $tmp[$ln] . "\n";
			}

			//FIXME: this->Path ist bei rebuild nicht gesetzt
			t_e('error', 'Error in template:' . $this->Path, $error, 'Code: ' . $errCode);
		}
	}

	private function parseTemplate(){
		$code = str_replace("<?xml", '<?= "<?xml"; ?>', $this->getTemplateCode(true));
		//$code = preg_replace('/(< *\/? *we:[^>]+>\n)/i','${1}'."\n",$code);
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

		if($this->doUpdateCode){
			$GLOBALS['we']['errorhandler']['shutdown'] = 'template';
			register_shutdown_function([$this, 'handleShutdown'], $code);

			//remove "use" since this is not allowed inside functions
			$var = create_function('', '?>' . preg_replace('|use [\w,\s\\\\]*;|', '', $code) . '<?php ');
			if(empty($var) && ( $error = error_get_last() )){
				$tmp = explode("\n", $code);
				if(!is_array($tmp)){
					$tmp = explode("\r", $code);
				}
				$errCode = "\n";
				for($ln = max(0, $error['line'] - 2); $ln <= $error['line'] + 2 && isset($tmp[$ln]); $ln++){
					$errCode .= $ln . ': ' . $tmp[$ln] . "\n";
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
			$repl = ['?>' => '__WE_?__WE__',
				'=>' => '__WE_=__WE__',
				'->' => '__WE_-__WE__'
			];
			$code = str_replace(array_keys($repl), $repl, $code);
			//#### parse base href
			$code = preg_replace(['%(<body[^>]*)>%i',
				'%(<head[^>]*>)%i',
				'%(</body[^>]*>)%i',
				], ['${1}<?= (!empty($GLOBALS[\'we_editmode\']) ? \' onload="doScrollTo();" onunload="doUnload()">\':\'>\'); we_templatePreContent(true);?>',
				'${1}<?php we_templateHead();?>',
				'<?php we_templatePostContent(true);?>${1}'
				], $code);

			$code = str_replace($repl, array_keys($repl), $code);
		} else if(!$this->hasStartAndEndTag('html', $code) && !$this->hasStartAndEndTag('head', $code) && !$this->hasStartAndEndTag('body', $code)){
			$code = '<?php we_templateHead(true);?>' . $code . '<?php we_templatePostContent(false,true);?>';
		} else {
			return parseError(g_l('parser', '[html_tags]')) . '<?php exit();?><!-- current parsed template code for debugging -->' . $code;
		}
		$code = strtr($code, [
			'exit(' => 'we_TemplateExit(',
			'die(' => 'we_TemplateExit(',
			'exit;' => 'we_TemplateExit();'
		]);
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
		$regs = [];
		foreach($_REQUEST as $n => $v){
			if(is_array($v) && preg_match('|^we_' . preg_quote($this->Name, '|') . '_variant|', $n, $regs)){
				foreach($v as $n2 => $v2){
					if($this->getElement($n2, 'type') === 'variant' && empty($v2)){
						$this->delElement($n2);
					}
				}
			}
		}
	}

	function i_getDocument($includepath = ''){
		$this->_updateCompleteCode();
		/* remove unwanted/-needed start/stop parser tags (?><php) */
		return preg_replace(["/(:|;|{|})(\r|\n| |\t)*\?>(\r|\n|\t)*<\?= ?/si", "/(:|;|{|})(\r|\n| |\t)*\?>(\r|\n|\t)*<\?php ?/si"], ['${1}' . "\n" . '${2} echo ', '${1}' . "\n" . '${2}'], $this->parseTemplate());
	}

	protected function i_writeSiteDir($doc){
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
		$ret = [];
		$fields = $this->getAllVariantFields();
		if(!$fields){
			return [];
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
		return (is_array($fields) ? array_keys($fields) : []);
	}

	/**
	 * @desc 	the function returns the array with all template field names and field attributes/types;
	 * 			if there is no fields in the elements, the template code will be parsed
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function getAllVariantFields(){
		return ($this->getElement('allVariants') ?: []);
	}

	/**
	 * @desc 	the function parses the template code and returns all template field names and field attributes/types
	 * @return	array with the filed names and attributes
	 * @param	none
	 */
	function readAllVariantFields($includedatefield = false){
		$variant_tags = ['input', 'link', 'textarea', 'img', 'select'];
		$templateCode = $this->getTemplateCode();
		$tp = new we_tag_tagParser($templateCode, $this->getPath());
		$tags = $tp->getAllTags();

		$blocks = $out = $regs = [];

		foreach($tags as $tag){
			if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){ // starttag found
				$tagname = $regs[1];
				if(preg_match('|\sname="([^"]+)"|i', $tag, $regs) && ($tagname != 'var') && ($tagname != 'field')){ // name found
					$name = $regs[1];

					if(!empty($blocks)){
						$foo = end($blocks);
						$blockname = $foo['name'];
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


					if(in_array($tagname, $variant_tags)){
						$att = we_tag_tagParser::makeArrayFromAttribs(str_ireplace('<we:' . $tagname, '', $tag));
						if($tagname === 'input' && isset($att['type']) && $att['type'] === 'date' && !$includedatefield){
							// do nothing
						} else {
							$out[$name] = ['type' => $tagname,
								'attributes' => $att
							];
						}
						//additional parsing for selects
						if($tagname === 'select'){
							$spacer = '\s*';
							$selregs = [];
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
							$blocks[] = ['name' => $name,
								'type' => $tagname
							];
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
		$weSuggest = & weSuggest::getInstance();
		$table = TEMPLATES_TABLE;
		$textname = 'MasterTemplateNameDummy';
		$idname = 'we_' . $this->Name . '_MasterTemplateID';
		$myid = $this->MasterTemplateID ?: '';
		$path = f('SELECT Path FROM ' . $this->DB_WE->escape($table) . ' WHERE ID=' . intval($myid), "", $this->DB_WE);

		$weSuggest->setAcId('MasterTemplate');
		$weSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$weSuggest->setInput($textname, $path);
		$weSuggest->setLabel('');
		$weSuggest->setResult($idname, $myid);
		$weSuggest->setSelector(weSuggest::DocSelector);
		$weSuggest->setTable($table);
		$weSuggest->setWidth(0);
		$weSuggest->setSelectButton(we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $idname . "'].value,'" . $table . "','" . $idname . "','" . $textname . "','checkSameMaster," . $this->ID . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)"));
		$weSuggest->setTrashButton(we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.elements['" . $idname . "'].value='';document.we_form.elements['" . $textname . "'].value='';WE().layout.weSuggest.checkRequired(window,'yuiAcInputMasterTemplate');_EditorFrame.setEditorIsHot(true);"));
		$weSuggest->setOpenButton(we_html_button::create_button(we_html_button::EDIT, 'javascript:goTemplate(document.we_form.elements.we_' . $GLOBALS['we_doc']->Name . '_MasterTemplateID.value)'));
		return $weSuggest->getHTML();
	}

	private function isUsedByDocuments(){
		if($this->ID == 0){
			return [0, []];
		}
		$where = ' WHERE temp_template_id=' . intval($this->ID) . ' OR TemplateID=' . intval($this->ID);
		$cnt = f('SELECT COUNT(1) FROM ' . FILE_TABLE . $where);
		$this->DB_WE->query('SELECT ID,SUBSTRING_INDEX(Path,Text,1),CONCAT(Text," (ID: ",ID,")"),IF(Published=0,"notpublished",IF(ModDate>Published,"changed","published")) FROM ' . FILE_TABLE . $where . ' ORDER BY Path LIMIT 100');
		return [$cnt, $this->DB_WE->getAllFirst(true)];
	}

	function formTemplateDocuments(){
		if($this->ID == 0){
			return [0, g_l('weClass', '[no_documents]')];
		}
		list($count, $elems) = $this->isUsedByDocuments();
		if(!$count){
			return [0, g_l('weClass', '[no_documents]')];
		}
		$path = $elemAttribs = [];
		$oldpath = '';
		foreach($elems as $id => $data){
			if($oldpath != $data[0]){
				$path[$data[0]] = we_html_tools::OPTGROUP;
				$oldpath = $data[0];
			}
			$path[$id] = $data[1];
			$elemAttribs[$id] = $data[2];
		}

		return [$count, we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('TemplateDocuments', $path, 1, '', false, ['style' => 'margin-right: 20px;'], 'value', 0, $elemAttribs), '', 'left', 'defaultfont', '', we_html_button::create_button(we_html_button::EDIT, "javascript:WE().layout.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.we_form.elements['TemplateDocuments'].value, '" . we_base_ContentTypes::WEDOCUMENT . "');") .
				we_html_button::create_button(we_html_button::VIEW, "javascript:WE().layout.openBrowser(document.we_form.elements['TemplateDocuments'].value);")
		)];
	}

	function formTemplatesUsed(){
		if($this->ID == 0 || empty($this->IncludedTemplates)){
			return [0, g_l('weClass', '[no_documents]')];
		}
		$this->DB_WE->query('SELECT ID,SUBSTRING_INDEX(Path,Text,1),CONCAT(Text," (ID: ",ID,")") FROM ' . TEMPLATES_TABLE . ' WHERE ID IN (' . trim($this->IncludedTemplates, ',') . ') ORDER BY Path');

		$elems = $this->DB_WE->getAllFirst(true);

		if(empty($elems)){
			return [0, g_l('weClass', '[no_documents]')];
		}
		$path = [];
		$oldpath = '';
		foreach($elems as $id => $data){
			if($oldpath != $data[0]){
				$path[$data[0]] = we_html_tools::OPTGROUP;
				$oldpath = $data[0];
			}
			$path[$id] = $data[1];
		}

		return [count($elems), we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('TemplateUsedTemplates', $path, 1, '', false, ['style' => 'margin-right: 20px;']), '', 'left', 'defaultfont', '', we_html_button::create_button(we_html_button::EDIT, "javascript:WE().layout.weEditorFrameController.openDocument('" . FILE_TABLE . "', document.we_form.elements['TemplateUsedTemplates'].value, '" . we_base_ContentTypes::WEDOCUMENT . "');") .
				we_html_button::create_button(we_html_button::VIEW, "javascript:WE().layout.openBrowser(document.we_form.elements['TemplateUsedTemplates'].value);")
		)];
	}

	function formTemplateUsedByTemplate(){
		if($this->ID == 0){
			return [0, g_l('weClass', '[no_documents]')];
		}
		$this->DB_WE->query('SELECT ID,SUBSTRING_INDEX(Path,Text,1),CONCAT(Text," (ID: ",ID,")") FROM ' . TEMPLATES_TABLE . ' WHERE IsFolder=0 AND FIND_IN_SET(' . $this->ID . ',IncludedTemplates) ORDER BY Path');

		$elems = $this->DB_WE->getAllFirst(true);

		if(empty($elems)){
			return [0, g_l('weClass', '[no_documents]')];
		}
		$path = [];
		$oldpath = '';
		foreach($elems as $id => $data){
			if($oldpath != $data[0]){
				$path[$data[0]] = we_html_tools::OPTGROUP;
				$oldpath = $data[0];
			}
			$path[$id] = $data[1];
		}

		return [count($elems), we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect('TemplateUsedByTemplates', $path, 1, '', false, ['style' => 'margin-right: 20px;']), '', 'left', 'defaultfont', '', we_html_button::create_button(we_html_button::EDIT, "javascript:WE().layout.weEditorFrameController.openDocument('" . TEMPLATES_TABLE . "', document.we_form.elements['TemplateUsedByTemplates'].value, '" . we_base_ContentTypes::TEMPLATE . "');") .
				we_html_button::create_button(we_html_button::VIEW, "javascript:WE().layout.openBrowser(document.we_form.elements['TemplateUsedByTemplates'].value);")
		)];
	}

	/**
	 * @desc 	this function returns the code of the unparsed template
	 * @return	array with the filed names and attributes
	 * @param	boolean $completeCode if true then the function returns the code of the complete template (with master template)
	 */
	function getTemplateCode($completeCode = true, $fillIncluded = false){
		if($fillIncluded){
			$regs = $codes = [];
			$max = 100;
			$db = $GLOBALS['DB_WE'];
			$code = $this->getElement('completeData');
			while(( --$max) > 0 && preg_match('|<we:include ([^>]*type="template"[^>]*)/>|', $code, $regs)){
				$parse = we_tag_tagParser::parseAttribs($regs[1], true);
				if(!empty($parse['path'])){
					$parse['id'] = path_to_id($parse['path'], TEMPLATES_TABLE, $db);
				}
				if(!empty($parse['id'])){
					$id = intval($parse['id']);
					if(!isset($codes[$id])){
						$codes[$id] = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID WHERE DocumentTable="tblTemplates" AND nHash=x\'' . md5('completeData') . '\' AND l.DID=' . $id);
					}
					$code = str_replace($regs[0], $codes[$id], $code);
				}
			}
			return $code;
		}
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
		$tmplCSV = ($hash ? $hash['IncludedTemplates'] : '');
		$masterTemplateID = ($hash ? $hash['MasterTemplateID'] : 0);

		$tmpArr = makeArrayFromCSV($tmplCSV);
		foreach($tmpArr as $tid){
			if(!in_array($tid, $arr) && $tid != $id){
				$arr[] = $tid;
			}
		}
		foreach($tmpArr as $tid){
			if($id != $tid){
				self::getUsedTemplatesOfTemplate($tid, $arr);
			}
		}

		$tmpArr = makeArrayFromCSV($tmplCSV);
		foreach($tmpArr as $tid){
			if(!in_array($tid, $arr) && $tid != $id){
				$arr[] = $tid;
			}
		}
		if($masterTemplateID && !in_array($masterTemplateID, $arr)){
			if($masterTemplateID != $id){
				self::getUsedTemplatesOfTemplate($masterTemplateID, $arr);
			}
		}

		foreach($tmpArr as $tid){
			if($id != $tid){
				self::getUsedTemplatesOfTemplate($tid, $arr);
			}
		}
	}

	function _updateCompleteCode(){
		if(!$this->doUpdateCode || defined('IMPORT_RUNNING')){
			return true;
		}
		static $cnt = 0;
		static $recursiveTemplates;
		if($cnt == 0){
			$recursiveTemplates = [];
		}
		if(empty($recursiveTemplates)){
			$recursiveTemplates[] = $this->ID;
		}
		++$cnt;
		$code = $this->getTemplateCode(false);

		// find all we:master Tags
		$masterTags = $regs = [];

		preg_match_all('|(<we:master([^>+]*)>)\n?([\\s\\S]*?)</we:master>\n?|', $code, $regs, PREG_SET_ORDER);


		foreach($regs as $reg){
			$attribs = we_tag_tagParser::parseAttribs(isset($reg[2]) ? $reg[2] : '', true);
			if(!empty($attribs['name'])){
				$masterTags[$attribs['name']] = ['content' => isset($reg[3]) ? $reg[3] : '',];
				$code = str_replace($reg[0], '', $code);
			}
		}

		if($this->MasterTemplateID){
			$templates = [];
			self::getUsedTemplatesOfTemplate($this->MasterTemplateID, $templates);
			if(in_array($this->ID, $templates) || $this->ID == $this->MasterTemplateID || in_array($this->MasterTemplateID, $recursiveTemplates)){
				$code = g_l('parser', '[template_recursion_error]');
				t_e(g_l('parser', '[template_recursion_error]'), 'Template ' . $this->ID, 'Mastertemplate: ' . $this->MasterTemplateID, 'Templates of Master: ' . implode(',', $templates), 'already processed: ' . implode(',', $recursiveTemplates));
			} else {
				// we have a master template. => surround current template with it
				// first get template code
				$recursiveTemplates[] = $this->MasterTemplateID;
				$templObj = new we_template();
				$templObj->initByID($this->MasterTemplateID, TEMPLATES_TABLE);
				$masterTemplateCode = $templObj->getTemplateCode(true);
				array_pop($recursiveTemplates);

				$contentTags = [];
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
		$regs = [];
		foreach($tags as $tag){
			// search for include tag
			if(preg_match('|^<we:include ([^>]+)>$|mi', $tag, $regs)){ // include found
				// get attributes of tag
				$att = we_tag_tagParser::parseAttribs($regs[1], true);
				// if type-attribute is equal to "template"
				if(!empty($att['type']) && $att['type'] === 'template'){

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
						$templates = [];
						self::getUsedTemplatesOfTemplate($att['id'], $templates);
						if(in_array($this->ID, $templates) || $att['id'] == $this->ID || in_array($att['id'], $recursiveTemplates)){
							$code = str_replace($tag, g_l('parser', '[template_recursion_error]'), $code);
							t_e(g_l('parser', '[template_recursion_error]'), 'Template ' . $this->ID, 'Included Template: ' . $att['id'], 'Templates of include: ' . implode(',', $templates), 'already processed: ' . implode(',', $recursiveTemplates));
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
		$this->IncludedTemplates .= ($this->IncludedTemplates ? ',' : '');

		$this->setElement('completeData', $code);
		--$cnt;
	}

	public function we_save($resave = false, $updateCode = true){
		$this->Extension = we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::TEMPLATE);
		if($updateCode){
			$this->_updateCompleteCode(true);
			$this->setElement('allVariants', we_serialize($this->readAllVariantFields($this->getElement('completeData')), SERIALIZE_JSON), 'variants');
		} else {
			$this->doUpdateCode = false;
		}
		$ret = parent::we_save($resave);
		if($ret){
			$tmplPathWithTmplExt = parent::getRealPath();
			if(file_exists($tmplPathWithTmplExt)){
				unlink($tmplPathWithTmplExt);
			}
			$this->unregisterMediaLinks();
			$ret = $this->registerMediaLinks();
		} else {
			t_e('save template failed', $this->Path);
		}

		$this->setElement('allVariants', we_unserialize($this->getElement('allVariants')), 'variants');
		return $ret;
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

	function registerMediaLinks($temp = false, $linksReady = false){
		$tp = new we_tag_tagParser($this->getTemplateCode());
		$c = 0;
		foreach($tp->getTagsWithAttributes() as $tag){
			$element = $tag['name'] . '[name=' . (isset($tag['attribs']['name']) ? $tag['attribs']['name'] : 'NN' . ++$c) . ']';
			switch($tag['name']){
				case 'icon':
				case 'img':
				case 'flashmovie':
				case 'video':
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['id']);
					}
					break;
				case 'url':
					if((empty($tag['attribs']['type']) || $tag['attribs']['type'] === 'document') &&
						isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['id']);
					}
					break;
				case 'include': //nur type=document: id, path
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'document'){
						if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
							$this->MediaLinks[$element] = intval($tag['attribs']['id']); // selector: text/webEdition only
						}
						if(isset($tag['attribs']['path']) && $tag['attribs']['path'] && ($id = path_to_id($tag['attribs']['path'], FILE_TABLE, $this->DB_WE))){
							$this->MediaLinks[$element] = intval($id); // selector: text/webEdition only
						}
					}
					break;
				case 'link':
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['id']);
					}
					if(isset($tag['attribs']['imageid']) && is_numeric($tag['attribs']['imageid'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['imageid']);
					}
					break;
				case 'sessionfield':
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'img' &&
						isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['id']);
					}
					break;

				// the following cases are not meant to link media files: we check them anyway
				case 'a': // selector: text/webEdition only
				case 'linkToSeeMode':// selector: text/webEdition only
				case 'metadata':// selector: text/webEdition only
					if(isset($tag['attribs']['id']) && is_numeric($tag['attribs']['id'])){
						$this->MediaLinks[$element] = intval($tag['attribs']['id']);
					}
					break;
				case 'listview':
					if(isset($tag['attribs']['type']) && $tag['attribs']['type'] === 'document' && !empty($tag['attribs']['id'])){
						$ids = explode(',', $tag['attribs']['id']);
						foreach($ids as $id){
							$id = trim($id);
							if(is_numeric($id)){
								$this->MediaLinks[$element] = intval($id);
							}
						}
					}
					break;
				default:
				//
			}
		}
		return (empty($this->MediaLinks) ?
			true :
			parent::registerMediaLinks(false, true));
	}

	// .tmpl mod

	function getRealPath($old = false){
		return preg_replace('/.tmpl$/i', '.php', parent::getRealPath($old));
	}

	public function getPropertyPage(we_base_jsCmd $jsCmd){
		return we_html_multiIconBox::getHTML('PropertyPage', [['icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $this->formPath(), 'space' => we_html_multiIconBox::SPACE_MED2],
				['icon' => 'mastertemplate.gif', 'headline' => g_l('weClass', '[master_template]'), 'html' => $this->formMasterTemplate(), 'space' => we_html_multiIconBox::SPACE_MED2],
				['icon' => 'charset.gif', 'headline' => g_l('weClass', '[Charset]'), 'html' => $this->formCharset(), 'space' => we_html_multiIconBox::SPACE_MED2],
				['icon' => 'copy.gif', 'headline' => g_l('weClass', '[copyTemplate]'), 'html' => $this->formCopyDocument(), 'space' => we_html_multiIconBox::SPACE_MED2]
				]
		);
	}

	public function formPath($disablePath = false, $notSetHot = false, $extra = ''){
		$extra = '<tr><td colspan="3" style="padding-bottom:4px;">' .
			$this->formInputField('', 'Display', g_l('navigation', '[display]'), 30, 0, 255, 'onchange="' . ($notSetHot ? '' : "we_cmd('setHot'); ") . '"')
			. '</td></tr>';
		return parent::formPath($disablePath, $notSetHot, $extra);
	}

	public static function we_getCodeMirror2Tags($css, $setting, $weTags = true){
		$ret = [];
		$allTags = [];
		if($weTags && ($css || $setting['WE'])){
			$allWeTags = we_wizard_tag::getExistingWeTags($css); //only load deprecated tags if css is requested
			foreach($allWeTags as $tagName){
				if(($weTag = weTagData::getTagData($tagName))){
					if($css){
						$ret[] = '.cm-weTag_' . $tagName . ':hover:after {content: "' . strtr(html_entity_decode($weTag->getDescription(), null, $GLOBALS['WE_BACKENDCHARSET']), [
								'"' => '\'',
								"\n" => ' ']) . '";}';
					} else {
						$allTags['we:' . $tagName] = ['we' => $weTag->getAttributesForCM()];
					}
				}
			}
		}
		if($css){
			return implode('', $ret);
		}

		$all = include(WE_INCLUDES_PATH . 'accessibility/htmlTags.inc.php');
		$allTags = array_merge($allTags, ($setting['htmlTag'] ? $all['html'] : []), ($setting['html5Tag'] ? $all['html5'] : []));
		if(!$allTags){
			return '';
		}
		//keep we tags in front of ordinal html tags
		$ret[] = '"<":["' . implode('","', array_keys($allTags)) . '"]';

		ksort($allTags);
		foreach($allTags as $tagName => $cur){
			$attribs = [];
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
				$ret[] = '"<' . $tagName . ' ":[' . implode(',', $attribs) . ']';
			}
		}
		return 'WE().layout.editors.CodeMirror={weHints:{' . implode(',', $ret) . '}};';
	}

	public static function getJSLangConsts(){
		return 'WE().consts.g_l.tagWizzard={
	fill_required_fields:"' . g_l('taged', '[fill_required_fields]') . '",
	no_type_selected:"' . g_l('taged', '[no_type_selected]') . '",
};
';
	}

	public static function getJSTWConsts(){
		// Code Wizard
		$allWeTags = we_wizard_tag::getExistingWeTags();
		$tagGroups = we_wizard_tag::getWeTagGroups($allWeTags);
		$groupJs = '';
		foreach($tagGroups as $tagGroupName => $tags){
			switch($tagGroupName){
				case 'custom_tags':
					$tagGroupName = 'custom';
			}
			$groupJs .= $tagGroupName . ": ['" . implode("', '", $tags) . "'],";
		}

		return 'WE().consts.tagWizzard={
  groups:{' . $groupJs . '},
};';
	}

}
