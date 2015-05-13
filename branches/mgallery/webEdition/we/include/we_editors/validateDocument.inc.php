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
//  This page gives the possibility to check a document via a known web-Service
//  supports w3c (xhtml) and css validation via fileupload.
//  There is also the possibility to check a file via url, this is only possible,
//  when the server is accessible via web

we_html_tools::protect();
echo we_html_tools::getHtmlTop();

//  for predefined services include properties file, depending on content-Type
//  and depending on fileending.

if($we_doc->ContentType == we_base_ContentTypes::CSS || $we_doc->Extension === '.css'){
	require_once(WE_INCLUDES_PATH . 'accessibility/services_css.inc.php');
} else {
	require_once(WE_INCLUDES_PATH . 'accessibility/services_html.inc.php');
}

$services = array();
$js = '';

foreach($validationService as $_service){
	$services[$_service->art][$_service->category][] = $_service;
}

//  get custom services from database ..
$customServices = validation::getValidationServices('use');

if(!empty($customServices)){
	foreach($customServices as $_cService){
		$services['custom'][$_cService->category][] = $_cService;
	}
}

//  Generate Select-Menu with optgroups
krsort($services);

$_select = '';
$_lastArt = '';
$_lastCat = '';
$_hiddens = '';
$_js = '';
if($services){
	$_select = '<select name="service" class="weSelect" style="width:350px;" onchange="switchPredefinedService(this.options[this.selectedIndex].value);">';
	foreach($services as $art => $arr){
		foreach($arr as $cat => $arrServices){
			foreach($arrServices as $service){

				if($_lastArt != $art){
					if($_lastArt != ''){
						$_select .= '</optgroup>';
						$_lastCat = '1';
					}
					$_lastArt = $art;
					$_select .= '<optgroup class="lvl1" label="' . g_l('validation', '[art_' . $art . ']') . '">';
				}
				if($_lastCat != $cat){
					if($_lastCat != ''){
						$_select .= '</optgroup>';
					}
					$_lastCat = $cat;

					$_select .= '<optgroup class="lvl2" label="-- ' . g_l('validation', '[category_' . $cat . ']') . '">';
				}
				$_select .= '<option value="' . $service->getName() . '">' . oldHtmlspecialchars($service->name) . '</option>';
				$js .= '				host["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->host) . '";
                        path["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->path) . '";
                        s_method["' . $service->getName() . '"] = "' . $service->method . '";
                        varname["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->varname) . '";
                        checkvia["' . $service->getName() . '"] = "' . $service->checkvia . '";
                        ctype["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->ctype) . '";
                        additionalVars["' . $service->getName() . '"] = "' . oldHtmlspecialchars($service->additionalVars) . '";';
			}
		}
	}
	$_select .= '</optgroup></optgroup></select>';
	$selectedService = $validationService[0];
	$_hiddens = we_html_tools::hidden('host', $selectedService->host) .
		we_html_tools::hidden('path', $selectedService->path) .
		we_html_tools::hidden('ctype', $selectedService->ctype) .
		we_html_tools::hidden('s_method', $selectedService->method) .
		we_html_tools::hidden('checkvia', $selectedService->checkvia) .
		we_html_tools::hidden('varname', $selectedService->varname) .
		we_html_tools::hidden('additionalVars', $selectedService->additionalVars);
} else {
	$_select = g_l('validation', '[no_services_available]');
}

//  generate Body of page
$parts = array(
	array('html' => g_l('validation', '[description]'), 'space' => 0),
	array('headline' => g_l('validation', '[service]'),
		'html' =>
		'<table style="border:0px;padding:0px;" cellspacing="0">
                                 <tr><td class="defaultfont">' .
		$_select .
		$_hiddens .
		'</td><td>' . we_html_tools::getPixel(20, 5) . '</td><td>' .
		we_html_button::create_button('fa:edit,fa-lg fa-pencil', 'javascript:we_cmd(\'customValidationService\')', true, 100, 22, "", "", !permissionhandler::hasPerm("CAN_EDIT_VALIDATION"))
		. '</td><td>' . we_html_tools::getPixel(20, 5) . '</td><td>' .
		we_html_button::create_button(we_html_button::OK, 'javascript:we_cmd(\'checkDocument\')', true, 100, 22, '', '', (empty($services)))
		. '</td></tr></table>'
		, 'space' => 95),
	array('html' => g_l('validation', '[result]'), 'noline' => 1, 'space' => 0),
	array('html' => '<iframe name="validation" id="validation" src="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=checkDocument" width="680" height="400"></iframe>', 'space' => 5),
);

//  css for webSite
echo STYLESHEET . we_html_element::jsElement('
	host = {};
	path = {};
	varname = {};
	checkvia = {};
	ctype = {};
	s_method = {};
	additionalVars = {};' .
	$js) .
 we_html_element::jsScript(JS_DIR . 'validateDocument.js') .
 '</head>' .
 we_html_element::htmlBody(array('class' => 'weEditorBody', 'onload' => 'setIFrameSize()', 'onresize' => 'setIFrameSize()'), '<form name="we_form">'
	. we_html_tools::hidden('we_transaction', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0))
	. we_html_multiIconBox::getHTML('weDocValidation', "100%", $parts, 20, '', -1, '', '', false) .
	'</form>') .
 '</html>';
