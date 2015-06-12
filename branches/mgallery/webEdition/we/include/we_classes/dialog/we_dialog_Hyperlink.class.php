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
			$back = $this->getBackBut();
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:weCheckAcFields()");
			$okBut = $back ? we_html_button::create_button_table(array($back, $ok)) : $ok;
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

		return we_html_button::position_yes_no_cancel($okBut, '', we_html_button::create_button(we_html_button::CANCEL, 'javascript:top.close();'));
	}

	function initByHref($href, $target = '', $class = '', $param = '', $anchor = '', $lang = '', $hreflang = '', $title = '', $accesskey = '', $tabindex = '', $rel = '', $rev = ''){
		if($href){
			$this->args["href"] = $href;
			list($type, $ref) = explode(':', $this->args['href']);
			$type.=':';

			// Object Links and internal links are not possible when outside webEdition
			// for exmaple in the wysiwyg (Mantis Bug #138)
			if(($this->noInternals || (isset($this->args["outsideWE"]) && $this->args["outsideWE"] == 1)) && (
				$type == we_base_link::TYPE_OBJ_PREFIX || $type == we_base_link::TYPE_INT_PREFIX
				)
			){
				$this->args["href"] = $type = $ref = '';
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
					$this->args['objID'] = $ref;
					$this->args['objHref'] = f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($this->args['objID']), 'Path', $this->db);
					break;
				case we_base_link::TYPE_INT_PREFIX:
					$this->args['type'] = we_base_link::TYPE_INT;
					$this->args['extHref'] = '';
					$this->args['fileID'] = $ref;
					$this->args['fileHref'] = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($this->args['fileID']), 'Path', $this->db);
					$this->args['mailHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					break;
				case we_base_link::TYPE_MAIL_PREFIX:
					$this->args['type'] = we_base_link::TYPE_MAIL;
					$this->args['mailHref'] = preg_replace('|^([^\?#]+).*$|', '$1', $ref);
					$this->args['extHref'] = '';
					$this->args['fileID'] = '';
					$this->args['fileHref'] = '';
					$this->args['objID'] = '';
					$this->args['objHref'] = '';
					$match = array();
					preg_match('|(subject=([^&]*)&?)?(cc=([^&]*)&?)?(bcc=([^&]*)&?)?|', $this->args['param'], $match);
					$this->args['mailsubject'] = isset($match[2]) ? $match[2] : '';
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
		$this->args['mailsubject'] = isset($match[2]) ? $match[2] : '';
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

			if((!$param) && isset($urlparts["query"]) && $urlparts["query"]){
				$param = $urlparts["query"];
			}
			if((!$anchor) && isset($urlparts["fragment"]) && $urlparts["fragment"]){
				$anchor = $urlparts["fragment"];
			}
		}

		$class = $this->getHttpVar(we_base_request::STRING, "class");
		$type = $this->getHttpVar(we_base_request::STRING, "type");
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

		$extHref = (!$this->args['extHref'] ? 'http://' : (utf8_decode(($this->args['extHref']{0} === '#') ? '' : $this->args['extHref'])));
		if($this->noInternals || (isset($this->args['outsideWE']) && $this->args['outsideWE'] == 1)){
			$_select_type = '<option value="' . we_base_link::TYPE_EXT . '"' . (($this->args["type"] == we_base_link::TYPE_EXT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[external_link]') . '</option>
<option value="' . we_base_link::TYPE_MAIL . '"' . (($this->args["type"] == we_base_link::TYPE_MAIL) ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', '[emaillink]') . '</option>';

			$_external_link = we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref ? : we_base_link::EMPTY_EXT, '', '', 'url', 300);
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
			$cmd1 = "document.we_form.elements['we_dialog_args[extHref]'].value";
			$_external_select_button = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server', '" . we_base_request::encCmd($cmd1) . "', '', " . $cmd1 . ", '')") : "";

			$_external_link = "<div style='margin-top:1px'>" . we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("we_dialog_args[extHref]", 30, $extHref ? : we_base_link::EMPTY_EXT, '', 'onchange="if(this.value==\'\'){
					this.value=\'http://\';
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
			$cmd1 = "document.we_form.elements['we_dialog_args[fileID]'].value";
			$_internal_select_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', " . $cmd1 . ", '" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("document.we_form.elements['we_dialog_args[fileHref]'].value") . "','','',0, '', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

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
				$cmd1 = "document.we_form.elements['we_dialog_args[objID]'].value";
				$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['we_dialog_args[objHref]'].value");
				//$wecmdenc3 = we_base_request::encCmd("top.opener._EditorFrame.setEditorIsHot(true);");
				$_object_select_button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document', " . $cmd1 . ", '" . OBJECT_FILES_TABLE . "', '" . we_base_request::encCmd($cmd1) . "','" . $wecmdenc2 . "', '', '', '', 'objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");", false, 100, 22, "", "", !permissionhandler::hasPerm("CAN_SEE_OBJECTFILES"));

				$yuiSuggest->setAcId("Obj");
				$yuiSuggest->setContentType("folder," . we_base_ContentTypes::OBJECT_FILE);
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

		$_anchorSel = '<div id="anchorlistcontainer"></div>';
		$_anchorInput = we_html_tools::htmlTextInput("we_dialog_args[anchor]", 30, $this->args["anchor"], "", 'onblur="checkAnchor(this)"', "text", 300);

		$_anchor = we_html_tools::htmlFormElementTable($_anchorInput, "", "left", "defaultfont", we_html_tools::getPixel(10, 1), $_anchorSel, "", "", "", 0);

		$_param = we_html_tools::htmlTextInput("we_dialog_args[param]", 30, htmlspecialchars(urldecode(utf8_decode($this->args["param"]))), '', '', 'text', 300);

		// CSS STYLE
		$classSelect = $this->getClassSelect();

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
		<td valign="top"> ' . $_internal_link . we_html_element::jsElement('document.we_form.onsubmit = function() {return false;}') . '</td>
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
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/welink/js/welink_init.js', 'preinit();tinyMCEPopup.onInit.add(init);');
	}

	function getJs(){
		return parent::getJs() . we_html_element::jsElement('
var weAcCheckLoop = 0;
var editname="' . (isset($this->args["editname"]) ? $this->args["editname"] : '') . '";
var classNames = ' . (isset($this->args["cssClasses"]) && $this->args["cssClasses"] ? '"' . $this->args['cssClasses'] . '".split(/,/)' : 'top.opener.weclassNames_tinyMce;') . ';

var g_l={
	anchor_invalid:"' . g_l('linklistEdit', '[anchor_invalid]') . '",
	wysiwyg_none:"' . g_l('wysiwyg', '[none]') . '"
};
var consts={
	TYPE_INT:"' . TYPE_INT . '"
};
var dirs = {
	WEBEDITION_DIR:"' . WEBEDITION_DIR . '"
};

var size = {
	docSelect: {
		width:' . we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',
		height:' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT . '
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
			setTimeout(weDoCheckAcFields,100);
		} else if(!acStatus.valid) {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			weAcCheckLoop=0;
		} else {
			weAcCheckLoop=0;
			document.we_form.submit();
		}
	} else {' .
				we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}'
			) . we_html_element::jsScript(JS_DIR . 'dialogs/we_dialog_hyperlink.js');
	}

	function cmdFunction(array $args){
		if((!isset($args['href'])) || $args['href'] == we_base_link::EMPTY_EXT){
			$args['href'] = '';
		}
		$param = trim($args['param'], '?& ');
		$anchor = trim($args['anchor'], '# ');
		if(!empty($param)){
			$tmp = array();
			parse_str($param, $tmp);
			$param = '?' . http_build_query($tmp, null, '&');
		}
		// TODO: $args['href'] comes from weHyperlinkDialog with params and anchor: strip these elements there, not here!
		$href = (strpos($args['href'], '?') !== false ? substr($args['href'], 0, strpos($args['href'], '?')) :
				(strpos($args['href'], '#') === false ? $args['href'] : substr($args['href'], 0, strpos($args['href'], '#')))) . $param . ($anchor ? '#' . $anchor : '');

		if(strpos($href, we_base_link::TYPE_MAIL_PREFIX) === 0){
			$query = array();
			if(!empty($args['mail_subject'])){
				$query['subject'] = $args['mail_subject'];
			}
			if(!empty($args['mail_cc'])){
				$query['cc'] = $args['mail_cc'];
			}
			if(!empty($args['mail_bcc'])){
				$query['bcc'] = $args['mail_bcc'];
			}

			$href = $args['href'] . (empty($query) ? '' : '?' . http_build_query($query));

			$tmpClass = $args['class'];
			foreach($args as &$val){
				$val = '';
			}
			$args['class'] = $tmpClass;
		}

		return we_dialog_base::getTinyMceJS() .
			we_html_element::jsScript(WE_JS_TINYMCE_DIR . 'plugins/welink/js/welink_insert.js') .
			'<form name="tiny_form">' . we_html_element::htmlHiddens(array(
				"href" => $href,
				"target" => $args["target"],
				"class" => $args["cssclass"],
				"lang" => $args["lang"],
				"hreflang" => $args["hreflang"],
				"title" => $args["title"],
				"accesskey" => $args["accesskey"],
				"tabindex" => $args["tabindex"],
				"rel" => $args["rel"],
				"rev" => $args["rev"])) . '</form>';
	}

}
