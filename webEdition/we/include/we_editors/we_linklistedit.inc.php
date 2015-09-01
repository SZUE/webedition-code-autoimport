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
we_html_tools::protect();

//todo: make int/ext/mail/obj consts of some class
function getLangField($name, $value, $title, $width){
	//FIXME: these values should be obtained from global settings
	$input = we_html_tools::htmlTextInput($name, 15, $value, '', '', 'text', $width - 50);
	//FIXME: remove this fixed list by global lang settings
	$select = '<select style="width:50px;" class="defaultfont" name="' . $name . '_select" size="1" onchange="this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].value;this.selectedIndex=-1;">
						<option value=""></option>
						<option value="en">en</option>
						<option value="de">de</option>
						<option value="es">es</option>
						<option value="fi">fi</option>
						<option value="ru">ru</option>
						<option value="fr">fr</option>
						<option value="nl">nl</option>
						<option value="pl">pl</option>
					</select>';
	return we_html_tools::htmlFormElementTable($input, $title, 'left', 'small', $select);
}

function getRevRelSelect($type, $value){
	$input = we_html_tools::htmlTextInput($type, 15, $value, '', '', 'text', 70);
	$select = '<select name="' . $type . '_sel" class="defaultfont" size="1" style="width:70px;" onchange="this.form.elements[\'' . $type . '\'].value=this.options[this.selectedIndex].text;this.selectedIndex=0;">
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
	return we_html_tools::htmlFormElementTable($input, $type, "left", "small", $select);
}

// init document
$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

if(($charset = $we_doc->getElement('Charset'))){ //	send charset which might be determined in template
	we_html_tools::headerCtCharset('text/html', $charset);
}

$name = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
$nr = we_base_request::_(we_base_request::INT, 'we_cmd', '-1', 2);

if(we_base_request::_(we_base_request::BOOL, 'ok')){
	$alt = we_base_request::_(we_base_request::STRING, 'alt', '');
	$img_title = we_base_request::_(we_base_request::STRING, 'img_title', '');
	$text = we_base_request::_(we_base_request::HTML, 'text', '');
	$attribs = we_base_request::_(we_base_request::RAW, 'attribs', '');
	$href = we_base_request::_(we_base_request::URL, 'href', '');
	$anchor = trim(we_base_request::_(we_base_request::STRING, 'anchor', ''));
	$tabindex = we_base_request::_(we_base_request::INT, 'tabindex', '');
	$accesskey = we_base_request::_(we_base_request::STRING, 'accesskey', '');
	$lang = we_base_request::_(we_base_request::STRING, 'lang', '');
	$rel = we_base_request::_(we_base_request::STRING, 'rel', '');
	$rev = we_base_request::_(we_base_request::STRING, 'rev', '');
	$hreflang = we_base_request::_(we_base_request::STRING, 'hreflang', '');
	$params = we_base_request::_(we_base_request::STRING, 'params');
	$title = we_base_request::_(we_base_request::STRING, 'title', '');
	$type = we_base_request::_(we_base_request::STRING, 'type');

	//	accept anchor with or without '#', when saving the link
	$anchor = (!$anchor || $anchor{0} === '#' ? $anchor : '#' . $anchor);

	if($params){ //	accept parameters with or without '?', when saving the link
		//	when type=object we need a '&'
		switch(we_base_request::_(we_base_request::STRING, 'type')){
			case we_base_link::TYPE_OBJ:
				if(substr($params, 0, 1) != '&'){
					$params = (substr($params, 0, 1) === '?' ? '&' . substr($params, 1) : '&' . $params);
				}
				break;
			default:
				if(substr($params, 0, 1) != '?'){
					$params = (substr($params, 0, 1) === '&' ? '?' . substr($params, 1) : '?' . $params);
				}
		}
	}
	$link = array(
		'id' => we_base_request::_(we_base_request::INT, 'id', 0),
		'obj_id' => defined('OBJECT_TABLE') ? we_base_request::_(we_base_request::INT, 'obj_id', 0) : 0,
		'anchor' => $anchor,
		'accesskey' => $accesskey,
		'tabindex' => $tabindex,
		'lang' => $lang,
		'rel' => $rel,
		'rev' => $rev,
		'hreflang' => $hreflang,
		'params' => $params,
		'title' => $title,
		'bcc' => we_base_request::_(we_base_request::EMAIL, 'bcc', ''),
		'cc' => we_base_request::_(we_base_request::EMAIL, 'cc', ''),
		'subject' => we_base_request::_(we_base_request::STRING, 'subject', ''),
		'href' => ($type == we_base_link::TYPE_MAIL ? we_base_link::TYPE_MAIL_PREFIX . we_base_request::_(we_base_request::EMAIL, 'emaillink', '') : we_base_request::_(we_base_request::URL, 'href', '')),
		'attribs' => $attribs,
		'target' => we_base_request::_(we_base_request::STRING, 'target', ''),
		'jswin' => we_base_request::_(we_base_request::BOOL, 'jswin'),
		'jscenter' => we_base_request::_(we_base_request::BOOL, 'jscenter'),
		'jsposx' => we_base_request::_(we_base_request::UNIT, 'jsposx', ''),
		'jsposy' => we_base_request::_(we_base_request::UNIT, 'jsposy', ''),
		'jswidth' => we_base_request::_(we_base_request::UNIT, 'jswidth', ''),
		'jsheight' => we_base_request::_(we_base_request::UNIT, 'jsheight', ''),
		'jsstatus' => we_base_request::_(we_base_request::BOOL, 'jsstatus'),
		'jsscrollbars' => we_base_request::_(we_base_request::BOOL, 'jsscrollbars'),
		'jsmenubar' => we_base_request::_(we_base_request::BOOL, 'jsmenubar'),
		'jstoolbar' => we_base_request::_(we_base_request::BOOL, 'jstoolbar'),
		'jsresizable' => we_base_request::_(we_base_request::BOOL, 'jsresizable'),
		'jslocation' => we_base_request::_(we_base_request::BOOL, 'jslocation'),
		'img_id' => we_base_request::_(we_base_request::INT, 'img_id', 0),
		'img_src' => we_base_request::_(we_base_request::URL, 'img_src', ''),
		'text' => we_base_request::_(we_base_request::STRING, 'text'),
		'type' => isset($type) ? $type: we_base_link::TYPE_INT,
		'ctype' => we_base_request::_(we_base_request::STRING, 'ctype'),
		'width' => we_base_request::_(we_base_request::UNIT, 'width', ''),
		'height' => we_base_request::_(we_base_request::UNIT, 'height', ''),
		'border' => we_base_request::_(we_base_request::INT, 'border'),
		'hspace' => we_base_request::_(we_base_request::INT, 'hspace'),
		'vspace' => we_base_request::_(we_base_request::INT, 'vspace'),
		'align' => we_base_request::_(we_base_request::STRING, 'align'),
		'alt' => $alt,
		'img_title' => we_base_request::_(we_base_request::STRING, 'img_title'),
	);

	if(($linklist = we_base_request::_(we_base_request::SERIALIZED_KEEP, 'linklist')) !== false){
		//  set $nr to global, because it is used everywhere;
		$nr = we_base_request::_(we_base_request::INT, 'nr', 0);
		$ll = new we_base_linklist($linklist);
		$ll->setID($nr, $link['id']);
		$ll->setObjID($nr, $link['obj_id']);
		$ll->setHref($nr, $link['href']);
		$ll->setAnchor($nr, $link['anchor']);
		$ll->setAccesskey($nr, $link['accesskey']);
		$ll->setTabindex($nr, $link['tabindex']);
		$ll->setLang($nr, $link['lang']);
		$ll->setRel($nr, $link['rel']);
		$ll->setRev($nr, $link['rev']);
		$ll->setHreflang($nr, $link['hreflang']);
		$ll->setParams($nr, $link['params']);
		$ll->setAttribs($nr, $link['attribs']);
		$ll->setTarget($nr, $link['target']);
		$ll->setTitle($nr, $link['title']);
		$ll->setBcc($nr, $link['bcc']);
		$ll->setCc($nr, $link['cc']);
		$ll->setSubject($nr, $link['subject']);
		$ll->setJsWinAttrib($nr, 'jswin', $link['jswin']);
		$ll->setJsWinAttrib($nr, 'jscenter', $link['jscenter']);
		$ll->setJsWinAttrib($nr, 'jsposx', $link['jsposx']);
		$ll->setJsWinAttrib($nr, 'jsposy', $link['jsposy']);
		$ll->setJsWinAttrib($nr, 'jswidth', $link['jswidth']);
		$ll->setJsWinAttrib($nr, 'jsheight', $link['jsheight']);
		$ll->setJsWinAttrib($nr, 'jsstatus', $link['jsstatus']);
		$ll->setJsWinAttrib($nr, 'jsscrollbars', $link['jsscrollbars']);
		$ll->setJsWinAttrib($nr, 'jsmenubar', $link['jsmenubar']);
		$ll->setJsWinAttrib($nr, 'jstoolbar', $link['jstoolbar']);
		$ll->setJsWinAttrib($nr, 'jsresizable', $link['jsresizable']);
		$ll->setJsWinAttrib($nr, 'jslocation', $link['jslocation']);
		$ll->setImageID($nr, $link['img_id']);
		$ll->setImageSrc($nr, $link['img_src']);
		$ll->setText($nr, $link['text']);
		$ll->setType($nr, $link['type']);
		$ll->setCType($nr, $link['ctype']);
		$ll->setImageAttrib($nr, 'width', $link['width']);
		$ll->setImageAttrib($nr, 'height', $link['height']);
		$ll->setImageAttrib($nr, 'border', $link['border']);
		$ll->setImageAttrib($nr, 'hspace', $link['hspace']);
		$ll->setImageAttrib($nr, 'vspace', $link['vspace']);
		$ll->setImageAttrib($nr, 'align', $link['align']);
		$ll->setImageAttrib($nr, 'alt', $link['alt']);

		$linklist = $ll->getString();
	} /* else {
	  $link['nr'] = 0;
	  $linklist = serialize(array($link));
	  } */
} elseif($nr > -1){
	$ll = new we_base_linklist($we_doc->getElement($name));
	$href = $ll->getHref($nr);
	if($href && strpos($href, we_base_link::TYPE_MAIL_PREFIX) === 0){
		$emaillink = substr($href, strlen(we_base_link::TYPE_MAIL_PREFIX));
		$href = '';
		$type = we_base_link::TYPE_MAIL;
	} else {
		$type = $ll->getType($nr);
		$type = $type ? : we_base_link::TYPE_INT;
		$emaillink = '';
	}
	$anchor = $ll->getAnchor($nr);
	$accesskey = $ll->getAccesskey($nr);
	$lang = $ll->getLang($nr);
	$rel = $ll->getRel($nr);
	$rev = $ll->getRev($nr);
	$hreflang = $ll->getHrefLang($nr);
	$tabindex = $ll->getTabindex($nr);
	$params = $ll->getParams($nr);
	$title = $ll->getTitle($nr);
	$attribs = $ll->getAttribs($nr);
	$text = $ll->getText($nr);
	$target = $ll->getTarget($nr);
	$jswin = $ll->getJsWinAttrib($nr, 'jswin');
	$jscenter = $ll->getJsWinAttrib($nr, 'jscenter');
	$jsposx = $ll->getJsWinAttrib($nr, 'jsposx');
	$jsposy = $ll->getJsWinAttrib($nr, 'jsposy');
	$jswidth = $ll->getJsWinAttrib($nr, 'jswidth');
	$jsheight = $ll->getJsWinAttrib($nr, 'jsheight');
	$jsstatus = $ll->getJsWinAttrib($nr, 'jsstatus');
	$jsscrollbars = $ll->getJsWinAttrib($nr, 'jsscrollbars');
	$jsmenubar = $ll->getJsWinAttrib($nr, 'jsmenubar');
	$jstoolbar = $ll->getJsWinAttrib($nr, 'jstoolbar');
	$jsresizable = $ll->getJsWinAttrib($nr, 'jsresizable');
	$jslocation = $ll->getJsWinAttrib($nr, 'jslocation');

	//added for #7269
	$bcc = $ll->getBcc($nr);
	$cc = $ll->getCc($nr);
	$subject = $ll->getSubject($nr);

	$id = $ll->getID($nr);
	$obj_id = $ll->getObjID($nr);
	$href_obj = $ll->getHrefObj($nr);
	$img_id = $ll->getImageID($nr);
	$img_src = $ll->getImageSrc($nr);
	$width = $ll->getImageAttrib($nr, 'width');
	$height = $ll->getImageAttrib($nr, 'height');
	$border = $ll->getImageAttrib($nr, 'border');
	$hspace = $ll->getImageAttrib($nr, 'hspace');
	$vspace = $ll->getImageAttrib($nr, 'vspace');
	$align = $ll->getImageAttrib($nr, 'align');
	$alt = $ll->getImageAttrib($nr, 'alt');
	$img_title = $ll->getImageAttrib($nr, 'img_title');
	$href_int = $ll->getHrefInt($nr);
	$src_int = $ll->getImageSrcInt($nr);
	$ctype = $ll->getCType($nr);
} else {
	$link = $we_doc->getElement($name) ? unserialize($we_doc->getElement($name)) : array();
	$link = ($link ? : array('ctype' => we_base_link::CONTENT_TEXT, 'type' => we_base_link::TYPE_INT, 'href' => we_base_link::EMPTY_EXT, 'text' => g_l('global', '[new_link]')));
	$href = isset($link['href']) ? $link['href'] : '';
	if($href && strpos($href, we_base_link::TYPE_MAIL_PREFIX) === 0){
		$emaillink = substr($href, strlen(we_base_link::TYPE_MAIL_PREFIX));
		$href = '';
		$type = we_base_link::TYPE_MAIL;
	} else {
		$type = isset($link['type']) ? $link['type'] : we_base_link::TYPE_INT;
		$emaillink = '';
	}
	$attribs = isset($link['attribs']) ? $link['attribs'] : '';
	$text = isset($link['text']) ? $link['text'] : '';
	$anchor = isset($link['anchor']) ? $link['anchor'] : '';
	$accesskey = isset($link['accesskey']) ? $link['accesskey'] : '';
	$lang = isset($link['lang']) ? $link['lang'] : '';
	$rel = isset($link['rel']) ? $link['rel'] : '';
	$rev = isset($link['rev']) ? $link['rev'] : '';
	$hreflang = isset($link['hreflang']) ? $link['hreflang'] : '';
	$tabindex = isset($link['tabindex']) ? $link['tabindex'] : '';
	$params = isset($link['params']) ? $link['params'] : '';
	$title = isset($link['title']) ? $link['title'] : '';
	$target = isset($link['target']) ? $link['target'] : '';

	//added for #7269
	$bcc = isset($link['bcc']) ? $link['bcc'] : '';
	$cc = isset($link['cc']) ? $link['cc'] : '';
	$subject = isset($link['subject']) ? $link['subject'] : '';

	$jswin = isset($link['jswin']) && $link['jswin'] ? : '';
	$jscenter = isset($link['jscenter']) ? $link['jscenter'] : '';
	$jsposx = isset($link['jsposx']) ? $link['jsposx'] : '';
	$jsposy = isset($link['jsposy']) ? $link['jsposy'] : '';
	$jswidth = isset($link['jswidth']) ? $link['jswidth'] : '';
	$jsheight = isset($link['jsheight']) ? $link['jsheight'] : '';
	$jsstatus = isset($link['jsstatus']) ? $link['jsstatus'] : '';
	$jsscrollbars = isset($link['jsscrollbars']) ? $link['jsscrollbars'] : '';
	$jsmenubar = isset($link['jsmenubar']) ? $link['jsmenubar'] : '';
	$jstoolbar = isset($link['jstoolbar']) ? $link['jstoolbar'] : '';
	$jsresizable = isset($link['jsresizable']) ? $link['jsresizable'] : '';
	$jslocation = isset($link['jslocation']) ? $link['jslocation'] : '';

	$id = isset($link['id']) ? $link['id'] : '';
	$img_id = isset($link['img_id']) ? $link['img_id'] : '';
	$img_src = isset($link['img_src']) ? $link['img_src'] : '';
	$width = isset($link['width']) ? $link['width'] : '';
	$height = isset($link['height']) ? $link['height'] : '';
	$border = isset($link['border']) ? $link['border'] : '';
	$hspace = isset($link['hspace']) ? $link['hspace'] : '';
	$vspace = isset($link['vspace']) ? $link['vspace'] : '';
	$align = isset($link['align']) ? $link['align'] : '';
	$alt = isset($link['alt']) ? $link['alt'] : '';
	$img_title = isset($link['img_title']) ? $link['img_title'] : '';
	$href_int = (isset($id) && $id) ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id)) : '';
	if(defined('OBJECT_TABLE')){
		$obj_id = isset($link['obj_id']) ? $link['obj_id'] : '';
		$href_obj = (isset($obj_id) && $obj_id) ? f('SELECT Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($obj_id)) : '';
	}
	$src_int = $img_id ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($img_id)) : '';
	$ctype = isset($link['ctype']) ? $link['ctype'] : '';
}

echo we_html_tools::getHtmlTop(g_l('linklistEdit', '[edit_link]'), $we_doc->getElement('Charset'));
$yuiSuggest = & weSuggest::getInstance();
echo $yuiSuggest->getYuiCssFiles() .
 $yuiSuggest->getYuiJsFiles() .
 we_html_element::jsScript(JS_DIR . 'keyListener.js') .
 we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--

	function closeOnEscape() {
		return true;

	}

	function applyOnEnter(evt) {

		_elemName = "target";
		if (typeof (evt["srcElement"]) != "undefined") { // IE
			_elemName = "srcElement";
		}

		if (!(evt[_elemName].tagName == "SELECT" ||
						(evt[_elemName].tagName == "INPUT" && (evt[_elemName].name == "href_int" || evt[_elemName].name == "href_obj" || evt[_elemName].name == "src_int"))
						)) {
			document.forms['we_form'].submit();
			return true;

		}
	}


	function changeTypeSelect(s) {
		for (var i = 0; i < s.options.length; i++) {
			var trObj = document.getElementById(s.options[i].value + "_tr");
			if (i != s.selectedIndex) {
				trObj.style.display = "none";
			} else {
				trObj.style.display = "";
			}
		}
		//added for #7269
		var emailTable = document.getElementById("emailOptions");
		if (emailTable) {
			if (s.value == "<?php echo we_base_link::TYPE_MAIL; ?>") {
				emailTable.style.display = "block";
			} else {
				emailTable.style.display = "none";
			}
		}
	}
	function changeCTypeSelect(s) {
		for (var i = 0; i < s.options.length; i++) {
			var trObj = document.getElementById("c" + s.options[i].value + "_tr");
			var imgPropsObj = document.getElementById("cimgprops_tr");
			if (i != s.selectedIndex) {
				trObj.style.display = "none";
			} else {
				trObj.style.display = "";
			}
		}
		if (s.options[s.selectedIndex].value == "<?php echo we_base_link::CONTENT_TEXT; ?>") {
			imgPropsObj.style.display = "none";
		} else {
			imgPropsObj.style.display = "";
		}
	}

	function IsDigit(e) {
		var key;

		if (e && e.charCode) {
			key = e.charCode;
		} else {
			key = event.keyCode;
		}

		return (((key >= 48) && (key <= 57)) || (key == 0) || (key == 13));
	}

	function IsDigitPercent(e) {
		var key;

		if (e && e.charCode) {
			key = e.charCode;
		} else {
			key = event.keyCode;
		}

		return (((key >= 48) && (key <= 57)) || (key == 37) || (key == 0) || (key == 13));
	}

	function doUnload() {
		if (jsWindow_count) {
			for (i = 0; i < jsWindow_count; i++) {
				eval("jsWindow" + i + "Object.close()");
			}
		}
	}
<?php
$trans = we_base_request::_(we_base_request::TRANSACTION, "we_transaction", 0);

$ok = we_base_request::_(we_base_request::BOOL, "ok");
$cmd = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0);
$name = we_base_request::_(we_base_request::STRING, 'name', $name);

if($ok && $cmd === "edit_link_at_class"){
	$_SESSION['weS']['WE_LINK'] = $link;
	//FIXME: we_field XSS
	?>
		opener.setScrollTo();
		opener.we_cmd("object_change_link_at_class", "<?php echo $trans; ?>", "<?php echo we_base_request::_(we_base_request::STRING, "we_field"); ?>", "<?php echo $name; ?>");
		top.close();
	<?php
} else if($ok && $cmd === "edit_link_at_object"){
	$_SESSION['weS']['WE_LINK'] = $link;
	?>
		opener.setScrollTo();
		opener.we_cmd("object_change_link_at_object", "<?php echo $trans; ?>", "link_<?php echo $name; ?>");
		top.close();
	<?php
} else if($ok && isset($linklist) && $linklist){
	$_SESSION['weS']["WE_LINKLIST"] = $linklist;
	?>
		opener.setScrollTo();
		opener.we_cmd("change_linklist", "<?php echo $name; ?>", "");
	<?php
} else if($ok && isset($link) && $link){
	$_SESSION['weS']['WE_LINK'] = $link;
	?>
		opener.setScrollTo();
		opener.we_cmd("change_link", "<?php echo $name; ?>", "");
	<?php
} else {
	?>
		function we_cmd() {
			var args = "";
			var url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?";

			for (var i = 0; i < arguments.length; i++) {
				url += "we_cmd[" + i + "]=" + encodeURI(arguments[i]);
				if (i < (arguments.length - 1)) {
					url += "&";
				}
			}

			switch (arguments[0]) {
				case "openImgselector":
				case "openDocselector":
					new jsWindow(url, "we_fileselector", -1, -1,<?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>, true, true, true, true);
					break;

				case "browse_server":
					new jsWindow(url, "browse_server", -1, -1, 840, 400, true, false, true);
					break;

				default:
					for (var i = 0; i < arguments.length; i++) {
						args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
					}
					eval('opener.parent.we_cmd(' + args + ')');
			}
		}

		self.focus();
	<?php
}
?>
//-->
</script>

<?php echo STYLESHEET; ?>

</head>

<body class="weDialogBody" style="overflow:hidden;">
	<?php
	if(!we_base_request::_(we_base_request::BOOL, "ok")){

		$_select_type = '<select name="type" size="1" style="margin-bottom:5px;width:300px;" onchange="changeTypeSelect(this);" class="big">
<option value="' . we_base_link::TYPE_EXT . '"' . (($type == we_base_link::TYPE_EXT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[external_link]') . '</option>
<option value="' . we_base_link::TYPE_INT . '"' . (($type == we_base_link::TYPE_INT) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[internal_link]') . '</option>
<option value="' . we_base_link::TYPE_MAIL . '"' . (($type == we_base_link::TYPE_MAIL) ? ' selected="selected"' : '') . '>' . g_l('wysiwyg', '[emaillink]') . '</option>
' . (defined('OBJECT_TABLE') ? '
<option value="' . we_base_link::TYPE_OBJ . '"' . (($type == we_base_link::TYPE_OBJ) ? ' selected="selected"' : '') . '>' . g_l('linklistEdit', '[objectFile]') . '</option>' : '') . '
</select>';



		$wecmdenc1 = we_base_request::encCmd('document.we_form.href.value');
		$but = permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? we_html_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.href.value, '')") : "";
		$butspace = (we_base_browserDetect::isSafari() ? 8 : 10);
		$extLink = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("href", 30, $href, '', 'placeholder="http://www.example.com"', "url", 300), "", "left", "defaultfont", we_html_tools::getPixel($butspace, 20), $but, "", "", "", 0);
		$emailLink = we_html_tools::htmlTextInput("emaillink", 30, $emaillink, "", 'placeholder="user@example.com"', "text", 300);

		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['id'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['href_int'].value");

		$but = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].id.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',0,''," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

		$yuiSuggest->setAcId("Doc");
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::WEDOCUMENT . ',' . we_base_ContentTypes::HTML);
		$yuiSuggest->setInput("href_int", $href_int);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult("id", $id);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($but, 10);

		$intLink = $yuiSuggest->getHTML();
		if(defined('OBJECT_TABLE')){
			$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['obj_id'].value");
			$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['href_obj'].value");
			$but = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].obj_id.value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','','objectFile'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1) . ");");

			$yuiSuggest->setAcId("Obj");
			$yuiSuggest->setContentType("folder,objectFile");
			$yuiSuggest->setInput("href_obj", $href_obj);
			$yuiSuggest->setMaxResults(20);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult("obj_id", $obj_id);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($but, 10);

			$objLink = $yuiSuggest->getHTML();
		}

		//added for #7269
		$bcc = we_html_tools::htmlTextInput('bcc', 30, $bcc, '', '', 'text', 300);
		$cc = we_html_tools::htmlTextInput('cc', 30, $cc, '', '', 'text', 300);
		$subject = we_html_tools::htmlTextInput('subject', 30, $subject, '', '', 'text', 300);

		$anchor = we_html_tools::htmlTextInput('anchor', 30, $anchor, '', 'onblur="if(this.value&&!new RegExp(\'#?[a-z]+[a-z,0-9,_,:,.,-]*$\',\'i\').test(this.value)){alert(\'' . g_l('linklistEdit', '[anchor_invalid]') . '\');this.focus();}"', 'text', 300);
		$accesskey = we_html_tools::htmlTextInput('accesskey', 30, $accesskey, '', '', 'text', 140);
		$tabindex = we_html_tools::htmlTextInput('tabindex', 30, $tabindex, '', '', 'text', 140);
		$lang = getLangField('lang', $lang, g_l('linklistEdit', '[link_language]'), 140);
		$relfield = getRevRelSelect('rel', $rel);
		$revfield = getRevRelSelect('rev', $rev);
		$hreflang = getLangField('hreflang', $hreflang, g_l('linklistEdit', '[href_language]'), 140);
		$params = we_html_tools::htmlTextInput('params', 30, $params, '', '', 'text', 300);
		$title = we_html_tools::htmlTextInput('title', 30, $title, '', '', 'text', 300);
		$ctarget = we_html_tools::targetBox('target', 30, 300, '', $target);
		$cattribs = we_html_tools::htmlTextInput('attribs', 30, $attribs, '', '', 'text', 300);
		$jsWinProps = '
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td class="small">' . g_l('global', '[posx]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[posy]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[width]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[height]') . '</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>' . we_html_tools::htmlTextInput('jsposx', 4, $jsposx, '', '', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput('jsposy', 4, $jsposy, '', "", "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("jswidth", 4, $jswidth, '', ' onchange="if(this.form.jscenter.checked && this.value==\'\'){this.value=100}"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("jsheight", 4, $jsheight, "", ' onchange="if(this.form.jscenter.checked && this.value==\'\'){this.value=100}"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
	</tr>
	<tr>
		<td colspan="9">' . we_html_tools::getPixel(2, 2) . '</td>
	</tr>
	<tr>
		<td>' . we_html_forms::checkbox(1, $jsstatus, "jsstatus", g_l('global', '[status]'), true, "small") . '</td>
		<td></td>
		<td>' . we_html_forms::checkbox(1, $jsscrollbars, "jsscrollbars", g_l('global', '[scrollbars]'), true, "small") . '</td>
		<td></td>
		<td>' . we_html_forms::checkbox(1, $jsmenubar, "jsmenubar", g_l('global', '[menubar]'), true, "small") . '</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>' . we_html_forms::checkbox(1, $jsresizable, "jsresizable", g_l('global', '[resizable]'), true, "small") . '</td>
		<td></td>
		<td>' . we_html_forms::checkbox(1, $jslocation, "jslocation", g_l('global', '[location]'), true, "small") . '</td>
		<td></td>
		<td>' . we_html_forms::checkbox(1, $jstoolbar, "jstoolbar", g_l('global', '[toolbar]'), true, "small") . '</td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
	</tr>
</table>';
		$foo = '
<table border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td>' . we_html_forms::checkbox(1, $jswin, "jswin", g_l('global', '[open]')) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_forms::checkbox(1, $jscenter, "jscenter", g_l('global', '[center]'), true, "defaultfont", "if(this.checked){if(this.form.jswidth.value==''){this.form.jswidth.value='100';};if(this.form.jsheight.value==''){this.form.jsheight.value='100';};}") . '</td>
	</tr>
</table>';
		$jswinonoff = we_html_tools::htmlFormElementTable($jsWinProps, $foo, "left", "defaultfont", we_html_tools::getPixel(10, 2), "", "", "", "", 0);


		$_content_select = '<select name="ctype" size="1" style="margin-bottom:5px;width:300px;" onchange="changeCTypeSelect(this);" class="big">
<option value="' . we_base_link::CONTENT_TEXT . '"' . (($ctype == we_base_link::CONTENT_TEXT) ? ' selected="selected"' : '') . '>' . oldHtmlspecialchars(g_l('linklistEdit', '[text]')) . '</option>
<option value="' . we_base_link::CONTENT_EXT . '"' . (($ctype == we_base_link::CONTENT_EXT) ? ' selected="selected"' : '') . '>' . oldHtmlspecialchars(g_l('linklistEdit', '[external_image]')) . '</option>
<option value="' . we_base_link::CONTENT_INT . '"' . (($ctype == we_base_link::CONTENT_INT) ? ' selected="selected"' : '') . '>' . oldHtmlspecialchars(g_l('linklistEdit', '[internal_image]')) . '</option>
</select>';


		$ctext = we_html_tools::htmlTextInput("text", 30, $text, "", "", "text", 300);

		$wecmdenc1 = we_base_request::encCmd("document.we_form.img_src.value");
		$but = permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES") ? we_html_button::create_button("select", "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.img_src.value, '')") : "";
		$extImg = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("img_src", 30, $img_src, "", "", "text", 300), "", "left", "defaultfont", we_html_tools::getPixel(10, 2), $but, "", "", "", 0);

		$wecmdenc1 = we_base_request::encCmd("document.forms['we_form'].elements['img_id'].value");
		$wecmdenc2 = we_base_request::encCmd("document.forms['we_form'].elements['src_int'].value");
		$but = we_html_button::create_button("select", "javascript:we_cmd('openDocselector',document.forms[0].img_id.value,'" . FILE_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','','" . we_base_ContentTypes::IMAGE . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ");");

		$yuiSuggest->setAcId("Image");
		$yuiSuggest->setContentType("folder," . we_base_ContentTypes::IMAGE);
		$yuiSuggest->setInput("src_int", $src_int);
		$yuiSuggest->setMaxResults(20);
		$yuiSuggest->setMayBeEmpty(true);
		$yuiSuggest->setResult('img_id', $img_id);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($but, 10);

		$intImg = $yuiSuggest->getHTML();
		$imgProps = '
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr>
		<td class="small">' . g_l('global', '[width]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[height]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[border]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[hspace]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[vspace]') . '</td>
		<td></td>
		<td class="small">' . g_l('global', '[align]') . '</td>
	</tr>
	<tr>
		<td>' . we_html_tools::htmlTextInput("width", 4, $width, "", ' onkeypress="return IsDigitPercent(event);"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("height", 4, $height, "", ' onkeypress="return IsDigitPercent(event);"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("border", 4, $border, "", ' onkeypress="return IsDigit(event);"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("hspace", 4, $hspace, "", ' onkeypress="return IsDigit(event);"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>' . we_html_tools::htmlTextInput("vspace", 4, $vspace, "", ' onkeypress="return IsDigit(event);"', "text", 40) . '</td>
		<td>' . we_html_tools::getPixel(10, 2) . '</td>
		<td>
			<select class="defaultfont" name="align" size="1">
			<option value="">Default</option>
			<option value="top"' . (($align === "top") ? "selected" : "") . '>Top</option>
			<option value="middle"' . (($align === "middle") ? "selected" : "") . '>Middle</option>
			<option value="bottom"' . (($align === "bottom") ? "selected" : "") . '>Bottom</option>
			<option value="left"' . (($align === "left") ? "selected" : "") . '>Left</option>
			<option value="right"' . (($align === "right") ? "selected" : "") . '>Right</option>
			<option value="texttop"' . (($align === "texttop") ? "selected" : "") . '>Text Top</option>
			<option value="absmiddle"' . (($align === "absmiddle") ? "selected" : "") . '>Abs Middle</option>
			<option value="baseline"' . (($align === "baseline") ? "selected" : "") . '>Baseline</option>
			<option value="absbottom"' . (($align === "absbottom") ? "selected" : "") . '>Abs Bottom</option>
		</select></td>
	</tr>
	<tr><td colspan="12">' . we_html_tools::getPixel(2, 2) . '</td></tr>
	<tr><td colspan="12" class="small">' . g_l('linklistEdit', '[alt_text]') . '</td></tr>
	<tr><td colspan="12">' . we_html_tools::htmlTextInput("alt", 20, $alt, "", '', "text", 300) . '</td></tr>
	<tr><td colspan="12" class="small">' . g_l('linklistEdit', '[title]') . '</td></tr>
	<tr><td colspan="12">' . we_html_tools::htmlTextInput("img_title", 20, $img_title, "", '', "text", 300) . '</td></tr>
</table>';
		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button("save", "javascript:document.forms['we_form'].submit()"), null, we_html_button::create_button("cancel", "javascript:self.close()"));

		$_parts = array(
			array('headline' => 'URL',
				'html' => '
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>' . $_select_type . '</td>
	</tr>
	<tr id="ext_tr" style="display:' . (($type == we_base_link::TYPE_EXT) ? "table-row" : "none") . ';">
		<td height="35" valign="top"><div style="margin-top:1px;">' . $extLink . '</div></td>
	</tr>
	<tr id="int_tr" style="display:' . (($type == we_base_link::TYPE_INT) ? "table-row" : "none") . ';">
		<td height="35" valign="top">' . $intLink . '</td>
	</tr>
	<tr id="mail_tr" style="display:' . (($type == we_base_link::TYPE_MAIL) ? "table-row" : "none") . ';">
		<td height="35" valign="top"><div style="margin-top:2px;">' . $emailLink . '</div></td>
	</tr>
' . (defined('OBJECT_TABLE') ? '
	<tr id="obj_tr" style="display:' . (($type == we_base_link::TYPE_OBJ) ? "table-row" : "none") . ';">
		<td height="35" valign="top">' . $objLink . '</td>
	</tr>
' : '') . '
</table>',
				'space' => 150,
				'noline' => 1),
			array('headline' => g_l('global', '[content]'),
				'html' => '
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>' . $_content_select . '</td>
	</tr>
	<tr id="ctext_tr" style="display:' . (($ctype == we_base_link::CONTENT_TEXT) ? "table-row" : "none") . ';">
		<td>' . $ctext . '</td>
	</tr>
	<tr id="cext_tr" style="display:' . (($ctype == we_base_link::CONTENT_EXT) ? "table-row" : "none") . ';">
		<td>' . $extImg . '</td>
	</tr>
	<tr id="cint_tr" style="display:' . (($ctype == we_base_link::CONTENT_INT) ? "table-row" : "none") . ';">
		<td>' . $intImg . '</td>
	</tr>
	<tr id="cimgprops_tr" style="display:' . (($ctype == we_base_link::CONTENT_TEXT) ? "none" : "table-row") . ';">
		<td>' . we_html_tools::getPixel(10, 3) . "<br/>" . $imgProps . '</td>
	</tr>
</table><div></div>',
				'space' => 150),
			array('headline' => g_l('linklistEdit', '[link_anchor]'),
				'html' => $anchor,
				'space' => 150,
				'noline' => 1),
			array('headline' => g_l('linklistEdit', '[link_params]'),
				'html' => $params,
				'space' => 150,
				'noline' => 1),
			array('headline' => g_l('linklistEdit', '[link_target]'),
				'html' => $ctarget,
				'space' => 150)
		);


		if(permissionhandler::hasPerm("CAN_SEE_ACCESSIBLE_PARAMETERS")){
			//   start of accessible parameters
			$_parts[] = array('headline' => g_l('linklistEdit', '[language]'),
				'html' => '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . $lang . '</td>
		<td>' . we_html_tools::getPixel(20, 5) . '</td>
		<td>' . $hreflang . '</td>
	</tr>
</table>',
				'space' => 150,
				'noline' => 1);

			$_parts[] = array('headline' => g_l('linklistEdit', '[title]'),
				'html' => $title,
				'space' => 150,
				'noline' => 1);

			$_parts[] = array('headline' => g_l('linklistEdit', '[keyboard]'),
				'html' => '
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="small">' . g_l('linklistEdit', '[accesskey]') . '</td>
		<td>' . we_html_tools::getPixel(20, 5) . '</td>
		<td class="small">' . g_l('linklistEdit', '[tabindex]') . '</td>
	</tr>
	<tr>
		<td>' . $accesskey . '</td>
		<td>' . we_html_tools::getPixel(20, 5) . '</td>
		<td>' . $tabindex . '</td>
	</tr>
</table>',
				'space' => 150,
				'noline' => 1);

			$_parts[] = array('headline' => g_l('wysiwyg', '[relation]'),
				'html' => '<table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>' . $relfield . '</td>
                                <td>' . we_html_tools::getPixel(20, 5) . '</td>
                                <td>' . $revfield . '</td>
                            </tr>
			                 </table>',
				'space' => 150,
				'noline' => 1);

			$_parts[] = array('headline' => g_l('linklistEdit', '[link_attr]'),
				'html' => $cattribs,
				'space' => 150);
		}


		//   Pop-Up
		$_parts[] = array('headline' => g_l('global', '[jswin]'),
			'html' => $jswinonoff,
			'space' => 150);
		?>
		<form name="we_form" action="<?php echo WEBEDITION_DIR; ?>we_cmd.php" method="post" onsubmit="return false">
			<input type="hidden" name="we_cmd[0]" value="<?php echo we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0); ?>" />
			<?php
			if(isset($ll) && $ll){
				?>
				<input type="hidden" name="linklist" value="<?php echo oldHtmlspecialchars($ll->getString()); ?>" />
				<?php
			}
			?>
			<input type="hidden" name="name" value="<?php echo $name; ?>" />
			<input type="hidden" name="nr" value="<?php echo we_base_request::_(we_base_request::INT, "nr", $nr); ?>" />
			<input type="hidden" name="ok" value="1" />
			<input type="hidden" name="we_transaction" value="<?php echo $we_transaction; ?>" />
			<input type="hidden" name="we_field" value="<?php echo we_base_request::_(we_base_request::STRING, 'we_cmd', '', 3); ?>" />
			<?php
			echo we_html_multiIconBox::getHTML('', '100%', $_parts, 30, $buttons, -1, '', '', false, g_l('linklistEdit', '[edit_link]')) .
			$yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJs();
			?>
		</form>
		<?php
	}
	?>
</body>

</html>
<?php
if(!we_base_request::_(we_base_request::BOOL, 'ok')){
	$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
}