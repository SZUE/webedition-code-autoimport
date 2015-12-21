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
class we_dialog_Hyperlink extends we_dialog_base{
	var $ClassName = __CLASS__;
	var $changeableArgs = array(
		'type', 'extHref', 'fileID', 'href', 'fileHref', 'objID', 'objHref', 'mailHref', 'target', 'class',
		'param', 'anchor', 'lang', 'hreflang', 'title', 'accesskey', 'tabindex', 'rel', 'rev'
	);

	function __construct($href = '', $target = '', $fileID = 0, $objID = 0, $noInternals = false){
		parent::__construct();
		$this->dialogTitle = g_l('wysiwyg', '[edit_hyperlink]');
		$this->noInternals = $noInternals;
	}

	function getDialogButtons(){
		if($this->pageNr == $this->numPages && $this->JsOnly == false){
			$okBut = ($this->getBackBut() != "") ? we_html_button::create_button_table(array($this->getBackBut(), we_html_button::create_button("ok", "javascript:weCheckAcFields()"))) : we_html_button::create_button("ok", "javascript:weCheckAcFields()");
		} else if($this->pageNr < $this->numPages){
			$back = $this->getBackBut();
			$next = $this->getNextBut();
			$okBut = $back && $next ?
				we_html_button::create_button_table(array($back, $next)) :
				($back ? : $next );
		} else {
			$back = $this->getBackBut();
			$ok = $this->getOkBut();
			$okBut = $back && $ok ? we_html_button::create_button_table(array($back, $ok)) : ($back ? : $ok);
		}

		return we_html_button::position_yes_no_cancel($okBut, '', we_html_button::create_button('cancel', 'javascript:top.close();'));
	}

	function initByHref($href, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($href){
			$this->args['href'] = $href;
			$href = explode(':', $this->args['href']);
			if(count($href) == 2){
				list($type, $ref) = $href;
				$type.=':';
			} else {
				$ref = '';
				$type = we_base_link::TYPE_EXT;
			}

			// Object Links and internal links are not possible when outside webEdition
			// for exmaple in the wysiwyg (Mantis Bug #138)
			if(($this->noInternals || (isset($this->args['outsideWE']) && $this->args['outsideWE'] == 1)) && (
				$type == we_base_link::TYPE_OBJ_PREFIX || $type == we_base_link::TYPE_INT_PREFIX
				)
			){
				$this->args['href'] = $type = $ref = '';
			}

			$this->args['mailsubject'] = $this->args['mailcc'] = $this->args['mailbcc'] = '';
			$this->args['param'] = str_replace('&amp;', '&', $param);
			switch($type){
				case we_base_link::TYPE_OBJ_PREFIX:
					$this->args['type'] = we_base_link::TYPE_OBJ;
					$this->args['extHref'] = '';
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['mailHref'] = '';
					$this->args['objID'] = trim($ref, '/?#');
					$this->args['objHref'] = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->args['objID']), 'Path', $this->db);
					break;
				case we_base_link::TYPE_INT_PREFIX:
					$this->args['type'] = we_base_link::TYPE_INT;
					$this->args['extHref'] = '';
					$this->args['fileID'] = trim($ref, '/?#');
					$this->args['fileHref'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), 'Path', $this->db);
					$this->args['mailHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					break;
				case we_base_link::TYPE_MAIL_PREFIX:
					$this->args['type'] = we_base_link::TYPE_MAIL;
					$match = array();
					preg_match('|^([^\?#]+).*$|', $ref, $match);
					$this->args['mailHref'] = trim($match[1], '/');
					$this->args['extHref'] = '';
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					$match = array();
					preg_match('|(subject=([^&]*)&?)?(cc=([^&]*)&?)?(bcc=([^&]*)&?)?|', $this->args['param'], $match);
					$this->args['mailsubject'] = isset($match[2]) ? urldecode($match[2]) : '';
					$this->args['mailcc'] = isset($match[4]) ? $match[4] : '';
					$this->args['mailbcc'] = isset($match[6]) ? $match[6] : '';
					break;
				default:
					$this->args['type'] = we_base_link::TYPE_EXT;
					$this->args['extHref'] = preg_replace(
						array(
						'|^' . WEBEDITION_DIR . 'we_cmd.php[^"\'#]+(#.*)$|',
						'|^' . WEBEDITION_DIR . '|',
						'|^([^\?#]+).*$|'
						), array('$1', '', '$1')
						, $this->args["href"]);
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['mailHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
			}
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByFileID($fileID, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($fileID){
			$this->args['href'] = we_base_link::TYPE_INT_PREFIX . $fileID;
			$this->args['type'] = we_base_link::TYPE_INT;
			$this->args['extHref'] = '';
			$this->args['fileID'] = $fileID;
			$this->args['fileHref'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), 'Path', $this->db);
			$this->args['objID'] = '';
			$this->args['mailHref'] = '';
			$this->args['objHref'] = '';
		}
		$this->args['mailsubject'] = $this->args['mailcc'] = $this->args['mailbcc'] = '';
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = str_replace('&amp;', '&', $param);
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByObjectID($objID, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($objID){
			$this->args['href'] = we_base_link::TYPE_OBJ_PREFIX . $objID;
			$this->args['type'] = we_base_link::TYPE_OBJ;
			$this->args['extHref'] = '';
			$this->args['fileID'] = '';
			$this->args['fileHref'] = '';
			$this->args['mailHref'] = '';
			$this->args['objID'] = $objID;
			$this->args['objHref'] = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->args['objID']), 'Path', $this->db);
		}
		$this->args['mailsubject'] = $this->args['mailcc'] = $this->args['mailbcc'] = '';
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = str_replace('&amp;', '&', $param);
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
	}

	function initByMailHref($mailHref, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($mailHref){
			$this->args['href'] = we_base_link::TYPE_MAIL_PREFIX . $mailHref;
			$this->args['type'] = we_base_link::TYPE_MAIL;
			$this->args['extHref'] = '';
			$this->args['fileID'] = '';
			$this->args['fileHref'] = '';
			$this->args['mailHref'] = $mailHref;
			$this->args['objID'] = '';
			$this->args['objHref'] = '';
		}
		$this->args['target'] = $target;
		$this->args['class'] = $class;
		$this->args['param'] = str_replace('&amp;', '&', $param);
		$this->args['anchor'] = $anchor;
		$this->args['lang'] = $lang;
		$this->args['hreflang'] = $hreflang;
		$this->args['title'] = $title;
		$this->args['accesskey'] = $accesskey;
		$this->args['tabindex'] = $tabindex;
		$this->args['rel'] = $rel;
		$this->args['rev'] = $rev;
		$match = array();
		preg_match('|(subject=([^&]*)&?)?(cc=([^&]*)&?)?(bcc=([^&]*)&?)?|', $this->args['param'], $match);
		$this->args['mailsubject'] = isset($match[2]) ? urldecode($match[2]) : '';
		$this->args['mailcc'] = isset($match[4]) ? $match[4] : '';
		$this->args['mailbcc'] = isset($match[6]) ? $match[6] : '';
	}

	function glue_url($parsed){
		if(!is_array($parsed)){
			return false;
		}
		return ($parsed['scheme'] ? $parsed['scheme'] . ':' . ((strtolower($parsed['scheme']) === 'mailto') ? '' : '//') : '') .
			($parsed['user'] ? $parsed['user'] . ($parsed['pass'] ? ':' . $parsed['pass'] : '') . '@' : '') .
			($parsed['host'] ? : '') .
			($parsed['port'] ? ':' . $parsed['port'] : '') .
			($parsed['path'] ? : '') .
			($parsed['query'] ? '?' . $parsed['query'] : '') .
			($parsed['fragment'] ? '#' . $parsed['fragment'] : '');
	}

	function initByHttp(){
		parent::initByHttp();
		$href = $this->getHttpVar(we_base_request::URL, 'href');
		$target = $this->getHttpVar(we_base_request::STRING, 'target');
		$param = $this->getHttpVar(we_base_request::STRING, 'param');
		$anchor = $this->getHttpVar(we_base_request::STRING, 'anchor');
		$lang = $this->getHttpVar(we_base_request::STRING, 'lang');
		$hreflang = $this->getHttpVar(we_base_request::STRING, 'hreflang');
		$title = $this->getHttpVar(we_base_request::STRING, 'title');
		$accesskey = $this->getHttpVar(we_base_request::STRING, 'accesskey');
		$tabindex = $this->getHttpVar(we_base_request::INT, 'tabindex');
		$rel = $this->getHttpVar(we_base_request::STRING, 'rel');
		$rev = $this->getHttpVar(we_base_request::STRING, 'rev');

		if($href && (strpos($href, "?") !== false || strpos($href, "#") !== false)){
			$urlparts = parse_url($href);

			if((!$param) && !empty($urlparts["query"])){
				$param = $urlparts["query"];
			}
			if((!$anchor) && !empty($urlparts["fragment"])){
				$anchor = $urlparts["fragment"];
			}
		}

		$class = $this->getHttpVar(we_base_request::STRING, 'class');
		$type = $this->getHttpVar(we_base_request::STRING, 'type');
		if($href){
			$this->initByHref($href, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
		} else if($type){
			$fileID = $this->getHttpVar(we_base_request::INT, "fileID", 0);
			$objID = $this->getHttpVar(we_base_request::INT, "objID", 0);
			switch($type){
				case we_base_link::TYPE_EXT:
					$extHref = $this->getHttpVar(we_base_request::URL, 'extHref', '#');
					$this->initByHref($extHref, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case we_base_link::TYPE_INT:
					$this->initByFileID($fileID, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case we_base_link::TYPE_OBJ:
					$this->initByObjectID($objID, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
				case we_base_link::TYPE_MAIL:
					$mailhref = $this->getHttpVar(we_base_request::STRING, 'mailHref'); //FIXME mail?
					$this->initByMailHref($mailhref, $target, $class, $param, $anchor, $lang, $hreflang, $title, $accesskey, $tabindex, $rel, $rev);
					break;
			}
		} else {
			$this->defaultInit();
		}
	}

	function defaultInit(){
		$this->args = array_merge($this->args, array(
			'href' => we_base_link::TYPE_INT_PREFIX,
			'type' => we_base_link::TYPE_INT,
			'extHref' => '',
			'fileID' => '',
			'fileHref' => '',
			'objID' => '',
			'objHref' => '',
			'mailHref' => '',
			'target' => '',
			'class' => '',
			'param' => '',
			'anchor' => '',
			'lang' => '',
			'hreflang' => '',
			'title' => '',
			'accesskey' => '',
			'tabindex' => '',
			'rel' => '',
			'rev' => '',
			'mailsubject' => '',
			'mailcc' => '',
			'mailbcc' => '',
		));
	}

	function getDialogContentHTML(){
		// Initialize we_button class
		$yuiSuggest = &weSuggest::getInstance();

		$extHref = (!$this->args['extHref'] ? we_base_link::EMPTY_EXT : ((substr($this->args['extHref'], 0, 1) === '#') ? '' : $this->args['extHref']));
		if($this->noInternals || (isset($this->args['outsideWE']) && $this->args['outsideWE'] == 1)){
			$_select_type = '<option value="' . we_base_link::TYPE_EXT . '"' . (($this->args["type"] == we_base_link::TYPE_EXT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[external_link]') . '</option>
<option value="' . we_base_link::TYPE_MAIL . '"' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', '[emaillink]') . '</option>';

			$_external_link = we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref, '', '', 'url', 300);
			// E-MAIL LINK
			$_email_link = we_html_tools::htmlTextInput("we_dialog_args[mailHref]", 30, $this->args["mailHref"], "", '', "email", 300);
		} else {
			$_select_type = '<option value="' . we_base_link::TYPE_EXT . '"' . (($this->args["type"] == we_base_link::TYPE_EXT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[external_link]') . '</option>
<option value="' . we_base_link::TYPE_INT . '"' . (($this->args["type"] == we_base_link::TYPE_INT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[internal_link]') . '</option>
<option value="' . we_base_link::TYPE_MAIL . '"' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', '[emaillink]') . '</option>' .
				((defined('OBJECT_TABLE') && ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL || permissionhandler::hasPerm("CAN_SEE_OBJECTFILES"))) ?
					'<option value="' . we_base_link::TYPE_OBJ . '"' . (($this->args["type"] == we_base_link::TYPE_OBJ) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[objectFile]') . '</option>' :
					''
				);

			// EXTERNAL LINK
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[extHref]'].value");
			$_external_select_button = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.elements['we_dialog_args[extHref]'].value, '')") : "";

			$_external_link = "<div style='margin-top:1px'>" . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref, '', 'onfocus="this.value = this.value === \'\' ? we_const.EMPTY_EXT : this.value;" onblur="checkMakeEmptyHrefExt();" onchange="
if(this.value === \'\' || this.value === we_const.EMPTY_EXT){
	checkMakeEmptyHrefExt();
}else{
	var x=this.value.match(/(.*:\/\/[^#?]*)(\?([^?#]*))?(#([^?#]*))?/);
	this.value=x[1];
	if(x[3]!=undefined){
		document.getElementsByName(\'we_dialog_args[param]\')[0].value=x[3];
	}
	if(x[5]!=undefined){
		document.getElementsByName(\'we_dialog_args[anchor]\')[0].value=x[5];
	}}"', "url", 300), "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_external_select_button, '', '', '', 0) . '</div>';


			// INTERNAL LINK
			$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileID]'].value");
			$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileHref]'].value");
			$_internal_select_button = we_html_button::create_button("select", "javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[fileID]'].value, '" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0, '', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

			$yuiSuggest->setAcId("Path");
			$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::JS, we_base_ContentTypes::CSS, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::QUICKTIME)));
			$yuiSuggest->setInput("we_dialog_args[fileHref]", $this->args["fileHref"]);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("we_dialog_args[fileID]", ($this->args["fileID"] == 0 ? "" : $this->args["fileID"]));
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($_internal_select_button, 10);

			$_internal_link = $yuiSuggest->getHTML();
			// E-MAIL LINK

			$_email_link = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[mailHref]", 30, $this->args["mailHref"], "", '', "email", 300), "", "left", "defaultfont", "", "", "", "", "", 0);

			// OBJECT LINK
			if(defined('OBJECT_TABLE') && ($_SESSION['weS']['we_mode'] == we_base_constants::MODE_NORMAL || permissionhandler::hasPerm("CAN_SEE_OBJECTFILES"))){
				$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[objID]'].value");
				$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[objHref]'].value");
				$wecmdenc3 = we_base_request::encCmd("top.opener._EditorFrame.setEditorIsHot(true);");
				$_object_select_button = we_html_button::create_button("select", "javascript:we_cmd('openDocselector', document.we_form.elements['we_dialog_args[objID]'].value, '" . OBJECT_FILES_TABLE . "', '" . $wecmdenc1 . "','" . $wecmdenc2 . "', '', '', '', 'objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");", false, 100, 22, "", "", !permissionhandler::hasPerm("CAN_SEE_OBJECTFILES"));

				$yuiSuggest->setAcId("Obj");
				$yuiSuggest->setContentType("folder,objectFile");
				$yuiSuggest->setInput("we_dialog_args[objHref]", $this->args["objHref"]);
				$yuiSuggest->setMaxResults(20);
				$yuiSuggest->setMayBeEmpty(0);
				$yuiSuggest->setResult('we_dialog_args[objID]', ($this->args["objID"] == 0 ? "" : $this->args["objID"]));
				$yuiSuggest->setSelector(weSuggest::DocSelector);
				$yuiSuggest->setTable(OBJECT_FILES_TABLE);
				$yuiSuggest->setWidth(300);
				$yuiSuggest->setSelectButton($_object_select_button, 10);

				$_object_link = $yuiSuggest->getHTML();
				/*
				  $_object_link = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[objHref]",30,$this->args["objHref"],"",' readonly="readonly"',"text",300, 0, "", !permissionhandler::hasPerm("CAN_SEE_OBJECTFILES")) .
				  '<input type="hidden" name="we_dialog_args[objID]" value="'.$this->args["objID"].'" />', "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_object_select_button, "", "", "", 0);
				 */
			}
		}

		$_anchorSel = (isset($this->args["editor"]) && $this->args["editor"] === 'tinyMce') ? '<div id="anchorlistcontainer"></div>' : we_html_element::jsElement('showanchors("anchors","","this.form.elements[\'we_dialog_args[anchor]\'].value=this.options[this.selectedIndex].value;this.selectedIndex=0;")');
		$_anchorInput = we_html_tools::htmlTextInput("we_dialog_args[anchor]", 30, $this->args["anchor"], "", 'onkeyup="checkMakeEmptyHrefExt()" onblur="checkMakeEmptyHrefExt(); checkAnchor(this)"', "text", 300);

		$_anchor = we_html_tools::htmlFormElementTable($_anchorInput, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_anchorSel, "", "", "", 0);

		$_param = we_html_tools::htmlTextInput("we_dialog_args[param]", 30, htmlspecialchars(urldecode(utf8_decode($this->args["param"]))), '', 'onkeyup="checkMakeEmptyHrefExt()" onblur="checkMakeEmptyHrefExt();"', 'text', 300);

		// CSS STYLE
		$classSelect = $this->args["editor"] === 'tinyMce' ? $this->getClassSelect() : we_html_element::jsElement('showclasss("we_dialog_args[class]", "' . $this->args["class"] . '", "");');


		// lang
		$_lang = $this->getLangField("lang", g_l('wysiwyg', '[link_lang]'), 145);
		$_hreflang = $this->getLangField("hreflang", g_l('wysiwyg', '[href_lang]'), 145);

		$_title = we_html_tools::htmlTextInput("we_dialog_args[title]", 30, $this->args["title"], "", "", "text", 300);


		$_accesskey = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[accesskey]", 30, $this->args["accesskey"], "", "", "text", 145), "accesskey");
		$_tabindex = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[tabindex]", 30, $this->args["tabindex"], "", ' onkeypress="return IsDigit(event);"', "text", 145), "tabindex");


		$_rev = we_html_tools::htmlFormElementTable($this->getRevRelSelect("rev"), "rev");
		$_rel = we_html_tools::htmlFormElementTable($this->getRevRelSelect("rel"), "rel");

		$show_accessible_class = (permissionhandler::hasPerm("CAN_SEE_ACCESSIBLE_PARAMETERS") ? '' : ' class="weHide"');

		return array(
			array(
				'html' =>
				// Create table output
				'<div style="position:relative; top:15px"><table cellpadding="0" cellspacing="0" border="0" height="65">
	<tr>
		<td class="defaultgray" valign="top" width="100" height="20">' . g_l('weClass', '[linkType]') . '</td>
		<td valign="top"><select name="we_dialog_args[type]" class="defaultfont" id="weDialogType" size="1" style="margin-bottom:5px;width:300px;" onchange="changeTypeSelect(this);">' . $_select_type . '</select></td>
	</tr>
	<tr class="we_change ' . we_base_link::TYPE_EXT . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_EXT) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('linklistEdit', '[external_link]') . '</td><td valign="top" >' . $_external_link . '</td>
	</tr>' .
				(isset($_internal_link) ? '
	<tr class="we_change ' . we_base_link::TYPE_INT . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_INT) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100"> ' . g_l('weClass', '[document]') . '</td>
		<td valign="top"> ' . $_internal_link . we_html_element::jsElement('document.we_form.onsubmit = weonsubmit;
function weonsubmit() {
	return false;
}') . '</td>
	</tr>' : '') . '
	<tr class="we_change ' . we_base_link::TYPE_MAIL . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', '[emaillink]') . '</td>
		<td valign="top">
			' . $_email_link . '</td>
	</tr>' .
				(defined('OBJECT_TABLE') && isset($_object_link) ? '
	<tr class="we_change ' . we_base_link::TYPE_OBJ . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_OBJ) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100" height="0">' . g_l('contentTypes', '[objectFile]') . '</td>
		<td valign="top">
			' . $_object_link . '</td>
	</tr>' : '') . '
</table></div>' .
				weSuggest::getYuiFiles() .
				$yuiSuggest->getYuiCss() .
				$yuiSuggest->getYuiJs()
			),
			array('html' => '<table cellpadding="0" cellspacing="0" border="0">
	<tr class="we_change ' . we_base_link::TYPE_INT . ' ' . we_base_link::TYPE_EXT . ' ' . we_base_link::TYPE_OBJ . '" style="display:' . (($this->args["type"] != we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', '[anchor]') . '</td>
		<td>' . $_anchor . '</td>
	</tr>
	<tr class="we_change ' . we_base_link::TYPE_MAIL . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('modules_messaging', '[subject]') . '</td>
		<td>' . we_html_tools::htmlTextInput('we_dialog_args[mail_subject]', 30, $this->args["mailsubject"], "", "", "text", 300) . '</td>
	</tr>

	<tr><td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td></tr>
	<tr class="we_change ' . we_base_link::TYPE_INT . ' ' . we_base_link::TYPE_EXT . ' ' . we_base_link::TYPE_OBJ . '" style="display:' . (($this->args["type"] != we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('linklistEdit', '[link_params]') . '</td>
		<td>' . $_param . '</td>
	</tr>
	<tr class="we_change ' . we_base_link::TYPE_MAIL . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">CC</td>
		<td>' . we_html_tools::htmlTextInput("we_dialog_args[mail_cc]", 30, $this->args["mailcc"], "", "", "text", 300) . '</td>
	</tr>
	<tr><td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td></tr>
	<tr class="we_change ' . we_base_link::TYPE_INT . ' ' . we_base_link::TYPE_EXT . ' ' . we_base_link::TYPE_OBJ . '" style="display:' . (($this->args["type"] != we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">' . g_l('linklistEdit', '[link_target]') . '</td>
		<td>' . we_html_tools::targetBox('we_dialog_args[target]', 29, 300, 'we_dialog_args[target]', $this->args['target'], '', 10, 100) . '</td>
	</tr>
	<tr class="we_change ' . we_base_link::TYPE_MAIL . '" style="display:' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td class="defaultgray" valign="top" width="100">BCC</td>
		<td>' . we_html_tools::htmlTextInput("we_dialog_args[mail_bcc]", 30, $this->args['mailbcc'], '', '', 'text', 300) . '</td>
	</tr>
	<tr><td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td></tr>
	<tr>
		<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', '[css_style]') . '</td>
		<td>' . $classSelect . '</td>
	</tr>
</table>'),
			array('html' => '<table cellpadding="0" cellspacing="0" border="0">
	<tr' . $show_accessible_class . '>
		<td class="defaultgray" valign="top" width="100">
			' . g_l('wysiwyg', '[language]') . '</td>
		<td>
			<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_lang . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_hreflang . '</td></tr></table></td>
	</tr>
	<tr' . $show_accessible_class . '>
		<td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td>
	</tr>
	<tr>
		<td class="defaultgray" valign="top" width="100">' . g_l('wysiwyg', '[title]') . '</td>
		<td>' . $_title . '</td>
	</tr>
	<tr' . $show_accessible_class . '>
		<td colspan="2">' . we_html_tools::getPixel(110, 5) . '</td>
	</tr>
	<tr' . $show_accessible_class . '>
		<td class="defaultgray" valign="top">' . g_l('wysiwyg', '[keyboard]') . '</td>
		<td><table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_accesskey . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_tabindex . '</td></tr></table></td>
	</tr>
	<tr' . $show_accessible_class . '>
		<td colspan="2">' . we_html_tools::getPixel(110, 5) . '</td>
	</tr>
	<tr' . $show_accessible_class . '>
		<td class="defaultgray" valign="top">' . g_l('wysiwyg', '[relation]') . '</td>
		<td><table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $_rel . '</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' . $_rev . '</td></tr></table></td>
	</tr>
	<tr><td colspan="2">' . we_html_tools::getPixel(110, 10) . '</td></tr>
</table>'
			)
		);
	}

	function getRevRelSelect($type){
		return '<input type="text" class="wetextinput" name="we_dialog_args[' . $type . ']" value="' . oldHtmlspecialchars($this->args["$type"]) . '" style="width:70px;" /><select class="defaultfont" name="' . $type . '_sel" size="1" style="width:75px;" onchange="this.form.elements[\'we_dialog_args[' . $type . ']\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
	<option></option>
	<option>contents</option>
	<option>chapter</option>
	<option>section</option>
	<option>subsection</option>
	<option>index</option>
	<option>glossary</option>
	<option>appendix</option>
	<option>copyright</option>
	<option>next</option>
	<option>prev</option>
	<option>start</option>
	<option>help</option>
	<option>bookmark</option>
	<option>alternate</option>
	<option>nofollow</option>
</select>';
	}

	public static function getTinyMceJS(){
		return parent::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/welink/js/welink_init.js');
	}

	function getJs(){
		return parent::getJs() . we_html_element::jsElement('
var we_const = { //to be declared oninit when js is extracted
	EMPTY_EXT : "' . we_base_link::EMPTY_EXT . '",
	TYPE_INT : "' . we_base_link::TYPE_INT . '"
};

var weAcCheckLoop = 0;
var weFocusedField;
function setFocusedField(elem){
	weFocusedField = elem;
}

function weCheckAcFields(){
	if(!!weFocusedField) weFocusedField.blur();
	if(document.getElementById("weDialogType").value === we_const.TYPE_INT){
		setTimeout("weDoCheckAcFields()",100);
	} else {
		document.forms["we_form"].submit();
	}
}

function weDoCheckAcFields(){
	acStatus = YAHOO.autocoml.checkACFields();
	acStatusType = typeof acStatus;
	if (weAcCheckLoop > 10) {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		weAcCheckLoop = 0;
	} else if(acStatusType.toLowerCase() == "object") {
		if(acStatus.running) {
			weAcCheckLoop++;
			setTimeout("weDoCheckAcFields",100);
		} else if(!acStatus.valid) {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			weAcCheckLoop=0;
		} else {
			weAcCheckLoop=0;
			document.forms["we_form"].submit();
		}
	} else {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}

function changeTypeSelect(s){
var elem=document.getElementsByClassName("we_change");
	for(var i=0; i< elem.length; i++){
		elem[i].style.display = (elem[i].className.match(s.value)?"":"none");
	}
}

function checkAnchor(el){
	if(el.value && !new RegExp(\'#?[a-z]+[a-z,0-9,_,:,.,-]*$\',\'i\').test(el.value)){
		alert(\'' . g_l('linklistEdit', '[anchor_invalid]') . ' \');
		setTimeout(function(){el.focus()}, 10);
		return false;
	}
}

function checkMakeEmptyHrefExt(){
	var f = document.we_form,
		hrefField = f.elements["we_dialog_args[extHref]"],
		anchor = f.elements["we_dialog_args[anchor]"].value,
		params = f.elements["we_dialog_args[param]"].value;

	if((anchor || params) && hrefField.value === we_const.EMPTY_EXT){
			hrefField.value = "";
	} else if(!(anchor || params) && !hrefField.value ){
		hrefField.value = we_const.EMPTY_EXT;
	}

}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]) {
		case "openImgselector":
		case "openDocselector":
			new jsWindow(url,"we_docselector",-1,-1,' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . ',true,false,true,true);
			break;

		case "browse_server":
			new jsWindow(url,"browse_server",-1,-1,800,400,true,false,true);
			break;
	}
}

function showclasss(name, val, onCh) {' .
				(isset($this->args["cssClasses"]) && $this->args["cssClasses"] ? '
	var classCSV = "' . $this->args['cssClasses'] . '";
	classNames = classCSV.split(/,/);' : ($this->args["editor"] === 'tinyMce' ? '
	classNames = top.opener.weclassNames_tinyMce;' : '
	classNames = top.opener.we_classNames;')) . '
	document.writeln(\'<select class="defaultfont" style="width:300px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onchange="\'+onCh+\'"\' : \'\')+\'>\');
	document.writeln(\'<option value="">' . g_l('wysiwyg', '[none]') . '\');
	if(typeof(classNames) != "undefined"){
		for (var i = 0; i < classNames.length; i++) {
			var foo = classNames[i].substring(0,1) == "." ?
				classNames[i].substring(1,classNames[i].length) :
				classNames[i];
			document.writeln(\'<option value="\'+foo+\'"\'+((val==foo) ? \' selected\' : \'\')+\'>.\'+foo);
		}
	}
	document.writeln(\'</select>\');
}' .
				(isset($this->args["editname"]) ? '

function showanchors(name, val, onCh) {
	var pageAnchors = top.opener.document.getElementsByTagName("A");
	var objAnchors = top.opener.weWysiwygObject_' . $this->args["editname"] . '.eDocument.getElementsByTagName("A");
	var allAnchors = new Array();

	for(var i = 0; i < pageAnchors.length; i++) {
		if (!pageAnchors[i].href && pageAnchors[i].name != "") {
			allAnchors.push(pageAnchors[i].name);
		}
	}

	for (var i = 0; i < objAnchors.length; i++) {
		if(!objAnchors[i].href && objAnchors[i].name != "") {
			allAnchors.push(objAnchors[i].name);
		}
	}
	if(allAnchors.length){
		document.writeln(\'<select class="defaultfont" style="width:100px" name="\'+name+\'" id="\'+name+\'" size="1"\'+(onCh ? \' onchange="\'+onCh+\'"\' : \'\')+\'>\');
		document.writeln(\'<option value="">\');

		for (var i = 0; i < allAnchors.length; i++) {
			document.writeln(\'<option value="\'+allAnchors[i]+\'"\'+((val==allAnchors[i]) ? \' selected\' : \'\')+\'>\'+allAnchors[i]);
		}

		document.writeln(\'</select>\');
	}
}' : '')
		);
	}

}
