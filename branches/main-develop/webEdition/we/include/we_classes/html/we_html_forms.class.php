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
	static function checkbox($value, $checked, $name, $text, $uniqid = false, $class = 'defaultfont', $onClick = '', $disabled = false, $description = '', $type = we_html_tools::TYPE_NONE, $width = 0, $html = '', $style = '', $title = ''){
		// Check if we have to create a uniqe id
		$id = ($uniqid ? $name . '_' . md5(uniqid(__FUNCTION__, true)) : $name);

		// Create HTML tags
		return '
			<span class="default checkbox" style="' . $style . '">
						<input type="checkbox" name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . ($checked ? ' checked="checked"' : '') . ($onClick ? ' onclick="' . $onClick . '"' : '') . ($disabled ? ' disabled="disabled"' : "") . ' />
					<div class="elementText ' . $class . '"><label id="label_' . $id . '" for="' . $id . '" class="' . ($disabled ? 'disabled ' : '') . ($title ? ' showhelp' : '') . '"' . ($title ? ' title="' . $title . '"' : '') . '>' . $text . '</label>' . (false && $title ? we_html_tools::htmlAlertAttentionBox($title, we_html_tools::TYPE_HELP) : '') . ($html ?: "") . '</div>' . ($description ? we_html_tools::htmlAlertAttentionBox($description, $type, $width) : '') .
			'</span>';
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
	static function checkboxWithHidden($checked, $name, $text, $uniqid = false, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = we_html_tools::TYPE_NONE, $width = 0, $html = '', $style = '', $title = ''){
		$onClick = "this.form.elements['" . $name . "'].value=this.checked ? 1 : 0;" . $onClick;
		return we_html_element::htmlHidden($name, ($checked ? 1 : 0)) . self::checkbox(1, $checked, 'check_' . $name, $text, $uniqid, $class, $onClick, $disabled, $description, $type, $width, $html, $style, $title);
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
	static function radiobutton($value, $checked, $name, $text, $uniqid = true, $class = "defaultfont", $onClick = '', $disabled = false, $description = '', $type = we_html_tools::TYPE_NONE, $width = 0, $onMouseUp = '', $extra_content = '', $title = ''){
		// Check if we have to create a uniqe id
		$id = $name . ($uniqid ? '_' . md5(uniqid(__FUNCTION__, true)) : '');

		// Create HTML tags
		return '
<div class="radiobuttonContainer">
<div class="radiobutton"><input type="radio" name="' . $name . '" id="' . $id . '" value="' . $value . '" ' . ($checked ? ' checked="checked"' : '') . ($onMouseUp ? ' onmouseup="' . $onMouseUp . '"' : '') . ($onClick ? ' onclick="' . $onClick . '"' : "") . ($disabled ? ' disabled="disabled"' : '') . ' />
<label id="label_' . $id . '" for="' . $id . '" class="elementText ' . ($disabled ? 'disabled ' : '') . $class . ($title ? ' showhelp' : '') . '" ' . ($onMouseUp ? ' onmouseup="' . str_replace('this.', "document.getElementById('" . $id . "').", $onMouseUp) . '"' : '') . ($title ? ' title="' . $title . '"' : '') . '>' . $text . '</label></div>' .
			($description ? '<div class="extra">' . we_html_tools::htmlAlertAttentionBox($description, $type, $width) . '</div>' : "") .
			($extra_content ? '<div class="extra">' . $extra_content . '</div>' : "") . '
</div>';
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
	static function weTextarea($name, $value, $attribs, $autobr, $autobrName, $showAutobr = true, $hidestylemenu = false, $forceinwebedition = false, $xml = false, $removeFirstParagraph = true, $charset = "", $showSpell = true, $isFrontendEdit = false, $origName = ""){
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
		$hideautobr = weTag_getAttribute('hideautobr', $attribs, false, we_base_request::BOOL);
		$class = weTag_getAttribute('class', $attribs, '', we_base_request::STRING);
		$style = weTag_getAttribute('style', $attribs, '', we_base_request::STRING);
		$id = weTag_getAttribute('id', $attribs, weTag_getAttribute('pass_id', $attribs, '', we_base_request::STRING), we_base_request::STRING);
		$oldHtmlspecialchars = weTag_getAttribute('htmlspecialchars', $attribs, false, we_base_request::BOOL);
		$ignoredocumentcss = weTag_getAttribute('ignoredocumentcss', $attribs, false, we_base_request::BOOL);
		$buttonpos = weTag_getAttribute('buttonpos', $attribs, '', we_base_request::STRING);
		$cssClasses = weTag_getAttribute('classes', $attribs, '', we_base_request::STRING);
		$buttonTop = false;
		$buttonBottom = false;
		$editorcss = weTag_getAttribute('editorcss', $attribs, [], we_base_request::INTLISTA);
		$imagestartid = weTag_getAttribute('imagestartid', $attribs, 0, we_base_request::INT);
		$galleryTemplates = weTag_getAttribute('gallerytemplates', $attribs, 0, we_base_request::INTLIST);

		//first prepare stylesheets from textarea-attribute editorcss (templates) or class-css (classes): csv of ids. then (if document) get document-css, defined by we:css

		$contentCss = array_filter((isset($GLOBALS['we_doc']) && is_object($GLOBALS['we_doc']) && !$ignoredocumentcss ? $GLOBALS['we_doc']->getDocumentCss() : []));
		if($editorcss){
			$contentCss = $contentCss + $GLOBALS['DB_WE']->getAllq('SELECT CONCAT(Path,"?",Published) FROM ' . FILE_TABLE . ' WHERE Published>0 AND ID IN (' . implode(',', $editorcss) . ')', true);
		}

		$contentCss = implode(',', $contentCss);

		if($buttonpos){
			$foo = explode(',', $buttonpos);
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
		$style = ($style ? explode(';', trim(preg_replace(['/width:[^;"]+[;"]?/i', '/height:[^;"]+[;"]?/i'], '', $style), ' ;')) : []);

		$fontnames = weTag_getAttribute('fontnames', $attribs, '', we_base_request::STRING);
		$showmenues = (
			isset($attribs['showMenues']) ?
			weTag_getAttribute('showMenues', $attribs, true, we_base_request::BOOL) :
			(isset($attribs['showmenues']) ?
			weTag_getAttribute('showmenues', $attribs, true, we_base_request::BOOL) :
			weTag_getAttribute('showmenus', $attribs, true, we_base_request::BOOL))
			);
		$importrtf = weTag_getAttribute('importrtf', $attribs, false, we_base_request::BOOL);
		$doc = (!empty($GLOBALS['we_doc']) && ($GLOBALS['we_doc'] instanceof we_objectFile) ? 'we_doc' : 'WE_MAIN_DOC');
		$inwebedition = ($forceinwebedition ?: !empty($GLOBALS[$doc]->InWebEdition));
		$inlineedit = // we are in frontend, where default is inlineedit = true
			weTag_getAttribute('inlineedit', $attribs, ($inwebedition ? INLINEEDIT_DEFAULT : true), we_base_request::BOOL);
		$value = self::removeBrokenInternalLinksAndImages($value);
		$width = is_numeric($width) ? max($width ?: intval($cols) * 5.5, we_wysiwyg_editor::MIN_WIDTH_INLINE) : $width;
		$height = is_numeric($height) ? max($height ?: intval($rows) * 8, we_wysiwyg_editor::MIN_HEIGHT_INLINE) : $height;

		if($wysiwyg){
			$commands = ($showmenues ? $commands : str_replace(['formatblock,', 'fontname,', 'fontsize,',], '', $commands ?: implode(',', we_wysiwyg_editor::getAllCmds())));
			$commands = ($hidestylemenu ? str_replace('applystyle,', '', $commands ?: implode(',', we_wysiwyg_editor::getAllCmds())) : $commands);
			$out = $isFrontendEdit ? we_wysiwyg_editor::getHTMLHeader($isFrontendEdit) : ''; // obsolet?
			$lang = (isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->Language)) ? $GLOBALS['we_doc']->Language : WE_LANGUAGE;
			$buttonpos = $buttonpos ?: 'top';
			$tinyParams = weTag_getAttribute('tinyparams', $attribs, '', we_base_request::RAW);
			$templates = weTag_getAttribute('templates', $attribs, '', we_base_request::STRING);
			$formats = weTag_getAttribute('formats', $attribs, '', we_base_request::STRING);
			$fontsizes = weTag_getAttribute('fontsizes', $attribs, '', we_base_request::STRING);

			if($inlineedit){
				$e = new we_wysiwyg_editor([], we_wysiwyg_editor::TYPE_INLINE_TRUE, $name, $width, $height, $value, $commands, $bgcolor, $class, $fontnames, $xml, $removeFirstParagraph, $inlineedit, '', $charset, $cssClasses, $lang, $showSpell, $isFrontendEdit, $buttonpos, $oldHtmlspecialchars, $contentCss, $origName, $tinyParams, $contextmenu, $templates, $formats, $imagestartid, $galleryTemplates, $fontsizes);
				return $out . $e->getHTML();
			}
			$e = new we_wysiwyg_editor([], we_wysiwyg_editor::TYPE_EDITBUTTON, $name, $width, $height, '', $commands, $bgcolor, $class, $fontnames, $xml, $removeFirstParagraph, $inlineedit, '', $charset, $cssClasses, $lang, $showSpell, $isFrontendEdit, $buttonpos, $oldHtmlspecialchars, $contentCss, $origName, $tinyParams, $contextmenu, $templates, $formats, $imagestartid, $galleryTemplates, $fontsizes);

			if(stripos($name, "we_ui") === false){//we are in backend
				$hiddenTextareaContent = strtr(we_wysiwyg_editor::parseInternalImageSrc($value), ["##|r##" => "\r", "##|n##" => "\n"]);
				$previewDivContent = strtr((
					isset($GLOBALS['we_doc']) && !($GLOBALS['we_doc'] instanceof we_objectFile) && !($GLOBALS['we_doc'] instanceof we_object) ?
					we_wysiwyg_editor::parseInternalImageSrc($GLOBALS['we_doc']->getField($attribs)) :
					we_document::parseInternalLinks($value, 0)
					), ["##|r##" => "\r", "##|n##" => "\n"]
				);
			} else {//we are in frontend
				$previewDivContent = $hiddenTextareaContent = strtr(we_document::parseInternalLinks($value, 0), ['##|r##' => "\r", '##|n##' => "\n"]);
			}

			$bgcolor = preg_match('/^[a-f0-9]{6}$/i', $bgcolor) ? '#' . $bgcolor : $bgcolor;
			$bgStyle = $bgcolor ? ' style="background-color: ' . $bgcolor . ' !important; background-image: none !important;"' : '';

			return $out .
				we_html_element::htmlTextArea(['name' => $name, 'id' => $name, 'onchange' => '_EditorFrame.setEditorIsHot(true);', 'style' => 'display: none', 'class' => 'wetextarea'], $hiddenTextareaContent) .
				($buttonTop ? '<div class="tbButtonWysiwygBorder" style="border-bottom:0px;">' . $e->getHTML() . '</div>' : '') . '<div class="wysiwygPreview tbButtonWysiwygBorder ' . ($class ?: "") . ' wetextarea tiny-wetextarea wetextarea-' . $origName . '" ' . $bgStyle . ' id="div_wysiwyg_' . $name . '">' . $previewDivContent . '</div>' . ($buttonBottom ? '<div class="tbButtonWysiwygBorder" style="border-top:0px;">' . $e->getHTML() . '</div>' : '');
		}

		if($width){
			$style[] = 'width:' . $width . (is_numeric($width) ? 'px' : '');
		}
		if($height){
			$style[] = 'height:' . $height . (is_numeric($height) ? 'px' : '');
		}

		$atts = removeAttribs($attribs, ['wysiwyg',
			'commands',
			'_name_orig',
			'pure',
			'type',
			'value',
			'checked',
			'autobr',
			'name',
			'values',
			'hidden',
			'editable',
			'format',
			'property',
			'size',
			'maxlength',
			'width',
			'height',
			'rows',
			'cols',
			'fontnames',
			'bgcolor',
			'editorcss',
			'ignoredocumentcss',
			'buttonpos',
			'importrtf'
		]);
		$atts['style'] = $style ? implode(';', $style) : '';
		$atts['class'] = ($class ? $class . ' ' : '') . 'wetextarea wetextarea-' . $origName;

		if($showAutobr || $importrtf){
			$clearval = $value;
			$value = strtr($value, [
				'<?' => '##|lt;?##',
				'<script' => '<##scr#ipt##',
				'</script' => '</##scr#ipt##',
				'\\' => '\\\\',
				"\n" => '\n',
				"\r" => '\r',
				'"' => '\\"'
			]);
			$attr = makeHTMLTagAtts($atts);
			$out .= we_html_element::jsElement('new we_textarea("' . $name . '","' . $value . '","' . $autobr . '","' . $autobrName . '",' . ($showAutobr && !$hideautobr ? "true" : "false") . ',\'' . $attr . '\',' . ($xml ? "true" : "false") . ');') .
				'<noscript><textarea name="' . $name . '" ' . $attr . '>' . oldHtmlspecialchars($clearval) . '</textarea></noscript>';
		} else {
			$atts['name'] = $name;
			$out .= getHtmlTag('textarea', $atts, oldHtmlspecialchars($value), true);
		}

		return $out;
	}

	static function removeBrokenInternalLinksAndImages(&$text){
		$DB_WE = new DB_WE();
		$regs = [];
		if(preg_match_all('/(href|src)="' . we_base_link::TYPE_INT_PREFIX . '(\\d+)([" \?#])/i', $text, $regs, PREG_SET_ORDER)){
			foreach($regs as $reg){
				if(!f('SELECT 1 FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]), '', $DB_WE)){
					$text = preg_replace(['|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '[^>]*>([^<]+)</a>|i',
						'|<a [^>]*href="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '[^>]*>|i',
						//'|<img [^>]*src="' . we_base_link::TYPE_INT_PREFIX . $reg[2] . $reg[3] . '"[^>]*>|i',
						], ['${1}'], $text);
				}
			}
		}
		/*
		  if(preg_match_all('/src="' . we_base_link::TYPE_THUMB_PREFIX . '(\\d+)[" ]/i', $text, $regs, PREG_SET_ORDER)){
		  foreach($regs as $reg){
		  list($imgID, $thumbID) = explode(',', $reg[1]);
		  $thumbObj = new we_thumbnail();
		  if(!$thumbObj->initByImageIDAndThumbID(intval($imgID), intval($thumbID))){
		  $text = preg_replace('|<img[^>]+src="' . we_base_link::TYPE_THUMB_PREFIX . $reg[1] . '[^>]+>|i', '', $text);
		  }
		  }
		  }
		 */

		if(defined('OBJECT_TABLE')){
			if(preg_match_all('/href="' . we_base_link::TYPE_OBJ_PREFIX . '(\\d+)[^" \?#]+\??/i', $text, $regs, PREG_SET_ORDER)){
				foreach($regs as $reg){
					if(!id_to_path($reg[1], OBJECT_FILES_TABLE)){ // if object doesn't exists, remove the link
						$text = preg_replace(['|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>([^<]+)</a>|i',
							'|<a [^>]*href="' . we_base_link::TYPE_OBJ_PREFIX . $reg[1] . '"[^>]*>|i',
							], ['${1}'], $text);
					}
				}
			}
		}

		return $text;
	}

}
