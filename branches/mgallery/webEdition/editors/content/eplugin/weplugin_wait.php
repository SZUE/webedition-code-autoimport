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
we_html_tools::protect();

$_callback = we_base_request::_(we_base_request::JS, 'callback');

echo we_html_element::htmlDocType() . we_html_element::htmlHtml(
	we_html_element::htmlHead(
		STYLESHEET .
		we_html_element::jsElement('
var g_l={
"no_java":"' . g_l('eplugin', '[no_java]') . '"
};
var callBack="' . $_callback . '";') .
		we_html_element::jsScript(JS_DIR . 'weplugin.js')
	) .
	we_html_element::htmlBody(array("style" => "background-color:#ffffff;margin:20px;", "onload" => "initPlugin();"), we_html_element::htmlForm(array("name" => "we_form"), we_html_element::htmlCenter(
				'<i class="fa fa-2x fa-spinner fa-pulse"></i>' .
				we_html_element::htmlBr() .
				we_html_element::htmlBr() .
				we_html_element::htmlDiv(array("class" => "header_small"), g_l('eplugin', '[initialisation]'))
			)
		)
	)
);
