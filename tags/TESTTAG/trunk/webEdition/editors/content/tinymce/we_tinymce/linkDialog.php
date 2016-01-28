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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');

$noInternals = false;
if(!(
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'outsideWE') ||
	we_base_request::_(we_base_request::BOOL, 'we_dialog_args', false, 'isFrontend')
	)){
	we_html_tools::protect();
} else {
	$noInternals = true;
}
$noInternals = $noInternals || !isset($_SESSION['user']) || !isset($_SESSION['user']['Username']) || $_SESSION['user']['Username'] == '';

$dialog = new we_dialog_Hyperlink('', '', 0, 0, $noInternals);
$dialog->initByHttp();
$dialog->registerCmdFn('weDoLinkCmd');
echo $dialog->getHTML();

function weDoLinkCmd($args){
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

	if(we_base_request::_(we_base_request::STRING, 'we_dialog_args', 'tinyMce', 'editor') != "tinyMce"){
		return we_html_element::jsElement(
				'top.opener.weWysiwygObject_' . $args['editname'] . '.createLink("' . $href . '","' . $args['target'] . '","' . $args['class'] . '","' . $args['lang'] . '","' . $args['hreflang'] . '","' . $args['title'] . '","' . $args['accesskey'] . '","' . $args['tabindex'] . '","' . $args['rel'] . '","' . $args['rev'] . '");
top.close();
');
	} else {
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

			foreach($args as $k => &$val){
				switch($k){
					case 'class':
					case 'title':
						break;
					default: 
						$val = '';
				}
			}
		}

		return we_dialog_base::getTinyMceJS() .
			we_html_element::jsScript(TINYMCE_JS_DIR . 'plugins/welink/js/welink_insert.js') .
			'<form name="tiny_form">
			<input type="hidden" name="href" value="' . $href . '">
			<input type="hidden" name="target" value="' . $args["target"] . '">
			<input type="hidden" name="class" value="' . $args["class"] . '">
			<input type="hidden" name="lang" value="' . $args["lang"] . '">
			<input type="hidden" name="hreflang" value="' . $args["hreflang"] . '">
			<input type="hidden" name="title" value="' . $args["title"] . '">
			<input type="hidden" name="accesskey" value="' . $args["accesskey"] . '">
			<input type="hidden" name="tabindex" value="' . $args["tabindex"] . '">
			<input type="hidden" name="rel" value="' . $args["rel"] . '">
			<input type="hidden" name="rev" value="' . $args["rev"] . '">
			</form>';
	}
}
