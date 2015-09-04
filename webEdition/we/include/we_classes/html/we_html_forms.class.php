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
/**
 * Class we_forms
 *
 * Provides functions for creating html tags used in forms.
 */
require_once (WE_INCLUDES_PATH . 'we_tag.inc.php');

abstract class we_html_forms{

	/**
	 * @param      $value                                  string
	 * @param      $checked                                bool
	 * @param      $name                                   string
	 * @param      $text                                   string
	 * @param      $uniqid                                 bool                (optional)
	 * @param      $class                                  string              (optional)
	 * @param      $onClick                                string              (optional)
	 * @param      $disabled                               bool                (optional)
	 *
	 * @return     string
	 */
	static function checkbox($value, $checked, $name, $text, $uniqid = false, $class = 'defaultfont', $onClick = '', $disabled = false, $description = '', $type = we_html_tools::TYPE_NONE, $width = 0, $html = '', $style = ''){
		// Check if we have to create a uniqe id
		$_id = ($uniqid ? $name . '_' . md5(uniqid(__FUNCTION__, true)) : $name);

		// Create HTML tags
		return '
			<table cellpadding="0" style="border-spacing: 0px;border-style:none;' . $style . '">
				<tr>
					<td' . ($description ? ' valign="top"' : '') . '>
						<input type="checkbox" name="' . $name . '" id="' . $_id . '" value="' . $value . '" style="cursor: pointer; outline: 0px;" ' . ($checked ? " checked=\"checked\"" : "") . ($onClick ? " onclick=\"$onClick\"" : "") . ($disabled ? " disabled=\"disabled\"" : "") . ' /></td>
					<td>' . we_html_tools::getPixel(4, 2) . '</td>
					<td class="' . $class . '" style="white-space:nowrap;"><label id="label_' . $_id . '" for="' . $_id . '" style="' . ($disabled ? 'color: grey; ' : 'cursor: pointer;') . 'outline: 0px;">' . $text . '</label>' . ($description ? "<br/>" . we_html_tools::getPixel(1, 3) . "<br/>" . we_html_tools::htmlAlertAttentionBox($description, $type, $width) : "") . ($html ? : "") . '</td>
				</tr>
			</table>';
	}

	/**
	 * @param      $checked                                bool
	 * @param      $name                                   string
	 * @param      $text                                   string
	 * @param      $uniqid                                 bool                (optional)
	 * @param      $class                                  string              (optional)
	 * @param      $onClick                                string              (optional)
	 * @param      $disabled                               bool                (optional)
	 *
	 * @return     string
	 */
	static function checkboxWithHidden($checked, $name, $text, $uniqid = false, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = we_html_tools::TYPE_NONE, $width = 0){
		$onClick = "this.form.elements['" . $name . "'].value=this.checked ? 1 : 0;" . $onClick;
		return '<input type="hidden" name="' . $name . '" value="' . ($checked ? 1 : 0) . '" />' . self::checkbox(1, $checked, 'check_' . $name, $text, $uniqid, $class, $onClick, $disabled, $description, $type, $width);
	}

	/**
	 * @param      $value                                  string
	 * @param      $checked                                bool
	 * @param      $name                                   string
	 * @param      $text                                   string
	 * @param      $uniqid                                 bool                (optional)
	 * @param      $class                                  string              (optional)
	 * @param      $onClick                                string              (optional)
	 * @param      $disabled                               bool                (optional)
	 *
	 * @return     string
	 */
	static function radiobutton($value, $checked, $name, $text, $uniqid = true, $class = "defaultfont", $onClick = '', $disabled = false, $description = '', $type = we_html_tools::TYPE_NONE, $width = 0, $onMouseUp = '', $extra_content = ''){
		// Check if we have to create a uniqe id
		$_id = $name . ($uniqid ? '_' . md5(uniqid(__FUNCTION__, true)) : '');

		// Create HTML tags
		return '
			<table style="border-spacing: 0px;border-style:none" cellpadding="0">
				<tr>
					<td class="weEditmodeStyle"' . ($description ? ' valign="top"' : '') . '><input type="radio" name="' . $name . '" id="' . $_id . '" value="' . $value . '" style="cursor: pointer;outline: 0px;" ' . ($checked ? ' checked="checked"' : '') . ($onMouseUp ? ' onmouseup="' . $onMouseUp . '"' : '') . ($onClick ? ' onclick="' . $onClick . '"' : "") . ($disabled ? ' disabled="disabled"' : '') . ' /></td>
					<td class="weEditmodeStyle">' . we_html_tools::getPixel(4, 2) . '</td>
					<td class="weEditmodeStyle ' . $class . '" style="white-space:nowrap;"><label id="label_' . $_id . '" for="' . $_id . '" style="' . ($disabled ? 'color: grey; ' : 'cursor: pointer;') . 'outline: 0px;" ' . ($onMouseUp ? ' onmouseup="' . str_replace('this.', "document.getElementById('" . $_id . "').", $onMouseUp) . '"' : '') . '>' . $text . '</label>' . ($description ? we_html_element::htmlBr() . we_html_tools::getPixel(1, 3) . we_html_element::htmlBr() . we_html_tools::htmlAlertAttentionBox($description, $type, $width) : "") .
			($extra_content ? (we_html_element::htmlBr() . we_html_tools::getPixel(1, 3) . we_html_element::htmlBr() . $extra_content) : "") . '</td>
				</tr>
			</table>';
	}

	/**
	 * returns the HTML Code for a webEdition Textarea (we:textarea we:sessionfield ...)
	 *
	 * @return string
	 * @param string $name
	 * @param string $value
	 * @param array $attribs
	 * @param string $autobr
	 * @param string $autobrName
	 * @param boolean $showAutobr
	 * @param string $path
	 * @param boolean $hidestylemenu
	 * @param boolean $forceinwebedition
	 * @param boolean $xml
	 * @param boolean $removeFirstParagraph
	 * @param string $charset
	 *
	 */
	static function weTextarea($name, $value, $attribs, $autobr, $autobrName, $showAutobr = true, $path = "", $hidestylemenu = false, $forceinwebedition = false, $xml = false, $removeFirstParagraph = true, $charset = "", $showSpell = true, $isFrontendEdit = false, $origName = ""){
		if(!$charset){
			if(isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->getElement('Charset')){
				$charset = $GLOBALS['we_doc']->getElement('Charset');
			}
		}
		$out = '';
		$dhtmledit = weTag_getAttribute('dhtmledit', $attribs, false, we_base_request::BOOL); //4614
		$wysiwyg = $dhtmledit || weTag_getAttribute('wysiwyg', $attribs, false, we_base_request::BOOL);

		$cols = weTag_getAttribute('cols', $attribs, '', we_base_request::STRING);
		$rows = weTag_getAttribute('rows', $attribs, '', we_base_request::UNIT);
		$width = weTag_getAttribute('width', $attribs, '', we_base_request::UNIT);
		$height = weTag_getAttribute('height', $attribs, '', we_base_request::UNIT);
		$commands = preg_replace('/ *, */', ',', weTag_getAttribute('commands', $attribs, defined('COMMANDS_DEFAULT') ? COMMANDS_DEFAULT : '', we_base_request::STRING));
		$contextmenu = preg_replace('/ *, */', ',', weTag_getAttribute('contextmenu', $attribs, '', we_base_request::STRING));
		$bgcolor = weTag_getAttribute('bgcolor', $attribs, '', we_base_request::STRING);
		$wrap = weTag_getAttribute('wrap', $attribs, false, we_base_request::BOOL);
		$hideautobr = weTag_getAttribute('hideautobr', $attribs, false, we_base_request::BOOL);
		$class = weTag_getAttribute('class', $attribs, '', we_base_request::STRING);
		$style = weTag_getAttribute('style', $attribs, '', we_base_request::STRING);
		$id = weTag_getAttribute('id', $attribs, weTag_getAttribute('pass_id', $attribs, '', we_base_request::STRING), we_base_request::STRING);
		$tabindex = weTag_getAttribute('tabindex', $attribs, '', we_base_request::STRING);
		$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL);
		$ignoredocumentcss = weTag_getAttribute('ignoredocumentcss', $attribs, false, we_base_request::BOOL);
		$buttonpos = weTag_getAttribute('buttonpos', $attribs, '', we_base_request::STRING);
		$cssClasses = weTag_getAttribute('classes', $attribs, '', we_base_request::STRING);
		$buttonTop = false;
		$buttonBottom = false;
		$editorcss = weTag_getAttribute('editorcss', $attribs, '', we_base_request::STRING);
		$editorcss = $editorcss ? id_to_path($editorcss, FILE_TABLE, null, false, true) : array();
		//first prepare stylesheets from textarea-attribute editorcss (templates) or class-css (classes): csv of ids. then (if document) get document-css, defined by we:css

		$contentCss = array_filter(array_merge((isset($GLOBALS['we_doc']) && is_object($GLOBALS['we_doc']) && !$ignoredocumentcss ? $GLOBALS['we_doc']->getDocumentCss() : array()), $editorcss));
		$contentCss = ($contentCss ? implode('?' . time() . ',', $contentCss) . '?' . time() : '');

		if($buttonpos){
			$foo = makeArrayFromCSV($buttonpos);
			foreach($foo as $p){
				switch($p){
					case 'top':
						$buttonTop = true;
						break;
					case 'bottom':
						$buttonBottom = true;
						break;
				}
			}
		}
		if($buttonTop == false && $buttonBottom == false){
			$buttonBottom = true;
		}

		//FIXME: don't remove style width/height in no wysiwyg if no width is given!
		$style = ($style ? explode(';', trim(preg_replace(array('/width:[^;"]+[;"]?/i', '/height:[^;"]+[;"]?/i'), '', $style), ' ;')) : array());

		$fontnames = weTag_getAttribute('fontnames', $attribs, '', we_base_request::STRING);
		$showmenues = (
			isset($attribs['showMenues']) ?
				weTag_getAttribute('showMenues', $attribs, true, we_base_request::BOOL) :
				(isset($attribs['showmenues']) ?
					weTag_getAttribute('showmenues', $attribs, true, we_base_request::BOOL) :
					weTag_getAttribute('showmenus', $attribs, true, we_base_request::BOOL)));

		$importrtf = weTag_getAttribute('importrtf', $attribs, false, we_base_request::BOOL);
		$doc = (isset($GLOBALS['we_doc']) && $GLOBALS['we_doc'] && ($GLOBALS['we_doc'] instanceof we_objectFile) ? 'we_doc' : 'WE_MAIN_DOC');
		$inwebedition = ($forceinwebedition ? : (isset($GLOBALS[$doc]->InWebEdition) && $GLOBALS[$doc]->InWebEdition));

		$inlineedit = // we are in frontend, where default is inlineedit = true
			weTag_getAttribute('inlineedit', $attribs, ($inwebedition ? INLINEEDIT_DEFAULT : true), we_base_request::BOOL);


		$value = self::removeBrokenInternalLinksAndImages($value);

		$width = max($width ? : intval($cols) * 5.5, 520);
		$height = max($height ? : intval($rows) * 8, 400);

		if($wysiwyg){
			if(!$showmenues){
				$commands = str_replace(array('formatblock,', 'fontname,', 'fontsize,',), '', $commands ? : implode(',', we_wysiwyg_editor::getAllCmds()));
			}
			if($hidestylemenu){
				$commands = str_replace('applystyle,', '', $commands ? : implode(',', we_wysiwyg_editor::getAllCmds()));
			}

			$out = we_wysiwyg_editor::getHeaderHTML(!$inwebedition);

			$_lang = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->Language)) ? $GLOBALS['we_doc']->Language : WE_LANGUAGE;
			$buttonpos = $buttonpos ? : 'top';
			$tinyParams = weTag_getAttribute('tinyparams', $attribs, '', we_base_request::RAW);
			$templates = weTag_getAttribute('templates', $attribs, '', we_base_request::STRING);
			$formats = weTag_getAttribute('formats', $attribs, '', we_base_request::STRING);
			$fontsizes = weTag_getAttribute('fontsizes', $attribs, '', we_base_request::STRING);

			if($inlineedit){
				$e = new we_wysiwyg_editor($name, $width, $height, $value, $commands, $bgcolor, '', $class, $fontnames, (!$inwebedition), $xml, $removeFirstParagraph, $inlineedit, '', $charset, $cssClasses, $_lang, '', $showSpell, $isFrontendEdit, $buttonpos, $oldHtmlspecialchars, $contentCss, $origName, $tinyParams, $contextmenu, false, $templates, $formats, $fontsizes);
				return $out . $e->getHTML();
			}

			$e = new we_wysiwyg_editor($name, $width, $height, '', $commands, $bgcolor, '', $class, $fontnames, (!$inwebedition), $xml, $removeFirstParagraph, $inlineedit, '', $charset, $cssClasses, $_lang, '', $showSpell, $isFrontendEdit, $buttonpos, $oldHtmlspecialchars, $contentCss, $origName, $tinyParams, $contextmenu, false, $templates, $formats, $fontsizes);

			if(stripos($name, "we_ui") === false){//we are in backend
				$hiddenTextareaContent = str_replace(array("##|r##", "##|n##"), array("\r", "\n"), $e->parseInternalImageSrc($value));
				$previewDivContent = str_replace(array("##|r##", "##|n##"), array("\r", "\n"), (
					isset($GLOBALS['we_doc']) && $GLOBALS['we_doc']->ClassName != 'we_objectFile' && $GLOBALS['we_doc']->ClassName != 'we_object' ?
						$e->parseInternalImageSrc($GLOBALS['we_doc']->getField($attribs)) :
						we_document::parseInternalLinks($value, 0)
					)
				);
			} else {//we are in frontend
				$previewDivContent = $hiddenTextareaContent = strtr(we_document::parseInternalLinks($value, 0), array('##|r##' => "\r", '##|n##' => "\n"));
			}

			$fieldName = preg_match('|^.+\[.+\]$|i', $name) ? preg_replace('/^.+\[(.+)\]$/', '$1', $name) : '';

			return $out .
				we_html_element::htmlTextArea(array('name' => $name, 'id' => $name, 'onchange' => '_EditorFrame.setEditorIsHot(true);', 'style' => 'display: none', 'class' => 'wetextarea'), $hiddenTextareaContent) .
				($fieldName ? we_html_element::jsElement('tinyEditors["' . $fieldName . '"] = "' . $name . '";') : '') .
				($buttonTop ? '<div class="tbButtonWysiwygBorder" style="width:25px;border-bottom:0px;background-image: url(' . IMAGE_DIR . 'backgrounds/aquaBackground.gif);">' . $e->getHTML() . '</div>' : '') . '<div class="tbButtonWysiwygBorder ' . (empty($class) ? "" : $class . " ") . 'wetextarea tiny-wetextarea wetextarea-' . $origName . '" id="div_wysiwyg_' . $name . '">' . $previewDivContent . '</div>' . ($buttonBottom ? '<div class="tbButtonWysiwygBorder" style="width:25px;border-top:0px;background-image: url(' . IMAGE_DIR . 'backgrounds/aquaBackground.gif);">' . $e->getHTML() . '</div>' : '');
		}
		if($width){
			$style[] = 'width:' . $width;
		}
		if($height){
			$style[] = 'height:' . $height;
		}

		if($showAutobr || $showSpell){
			$clearval = $value;
			$value = str_replace(array('<?', '<script', '</script', '\\', "\n", "\r", '"'), array('##|lt;?##', '<##scr#ipt##', '</##scr#ipt##', '\\\\', '\n', '\r',
				'\\"'), $value);
			$out .= we_html_element::jsElement('new we_textarea("' . $name . '","' . $value . '","' . $cols . '","' . $rows . '","","","' . $autobr . '","' . $autobrName . '",' . ($showAutobr ? ($hideautobr ? "false" : "true") : "false") . ',' . ($importrtf ? "true" : "false") . ',"' . $GLOBALS["WE_LANGUAGE"] . '","' . $class . '","' . implode(';', $style) . '","' . $wrap . '","onkeydown","' . ($xml ? "true" : "false") . '","' . $id . '",' . ((defined('SPELLCHECKER') && $showSpell) ? "true" : "false") . ',"' . $origName . '");') .
				'<noscript><textarea name="' . $name . '"' . ($tabindex ? ' tabindex="' . $tabindex . '"' : '') . ($style ? ' style="' . implode(';', $style) . '"' : '') . ' class="' . ($class ? $class . " " : "") . 'wetextarea wetextarea-' . $origName . '"' . ($id ? ' id="' . $id . '"' : '') . '>' . oldHtmlspecialchars($clearval) . '</textarea></noscript>';
		} else {
			$out .= '<textarea name="' . $name . '"' . ($tabindex ? ' tabindex="' . $tabindex . '"' : '') . ($style ? ' style="' . implode(';', $style) . '"' : '') . ' class="' . ($class ? $class . " " : "") . 'wetextarea wetextarea-' . $origName . '"' . ($id ? ' id="' . $id . '"' : '') . '>' . oldHtmlspecialchars($value) . '</textarea>';
		}

		return $out;
	}

	static function removeBrokenInternalLinksAndImages(&$text){
		$DB_WE = new DB_WE();
		$regs = array();
		if(preg_match_all('/(href|src)="' . we_base_link::TYPE_INT_PREFIX . '(\\d+)([" \?#])/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				if(!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]), '', $DB_WE)){
					$text = preg_replace(array(
						'|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '"[^>]*>([^<]+)</a>|i',
						'|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '"[^>]*>|i',
						'|<img [^>]*src="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '"[^>]*>|i',
						), array('$1'), $text);
				}
			}
		}
		if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '(\\d+)[" ]/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				list($imgID, $thumbID) = explode(',', $reg[1]);
				$thumbObj = new we_thumbnail();
				if(!$thumbObj->initByImageIDAndThumbID(intval($imgID), intval($thumbID))){
					$text = preg_replace('|<img[^>]+src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '[^>]+>|i', '', $text);
				}
			}
		}
		if(defined('OBJECT_TABLE')){
			if(preg_match_all('/href="' . we_base_link::TYPE_OBJ_PREFIX . '(\\d+)[^" \?#]+\??/i', $text, $regs, PREG_SET_ORDER)){
				foreach($regs as $reg){
					if(!id_to_path($reg[1], OBJECT_FILES_TABLE)){ // if object doesn't exists, remove the link
						$text = preg_replace(array(
							'|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>([^<]+)</a>|i',
							'|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>|i',
							), array('$1'), $text);
					}
				}
			}
		}

		return $text;
	}

}
