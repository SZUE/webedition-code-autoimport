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
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', STYLESHEET .
	we_html_element::jsScript(JS_DIR . 'customizeValidation.js'));
?>
<body class="weDialogBody" style="overflow:hidden;">
	<?php
	switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
		case 'saveService':
			$_service = new validationService(we_base_request::_(we_base_request::INT, 'id'), 'custom', we_base_request::_(we_base_request::STRING, 'category'), we_base_request::_(we_base_request::STRING, 'name'), we_base_request::_(we_base_request::STRING, 'host'), we_base_request::_(we_base_request::FILE, 'path'), we_base_request::_(we_base_request::STRING, 's_method'), we_base_request::_(we_base_request::STRING, 'varname'), we_base_request::_(we_base_request::STRING, 'checkvia'), we_base_request::_(we_base_request::STRING, 'ctype'), we_base_request::_(we_base_request::STRING, 'additionalVars'), we_base_request::_(we_base_request::STRING, 'fileEndings'), we_base_request::_(we_base_request::BOOL, 'active'));
			if(($selectedService = validation::saveService($_service))){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][saved_success]'), we_message_reporting::WE_MESSAGE_NOTICE));
			} else {
				$selectedService = $_service;
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][saved_failure]') . (isset($GLOBALS['errorMessage']) ? '\n' . $GLOBALS['errorMessage'] : ''), we_message_reporting::WE_MESSAGE_ERROR));
			}
			break;
		case 'deleteService':
			$_service = new validationService(we_base_request::_(we_base_request::INT, 'id'), 'custom', we_base_request::_(we_base_request::STRING, 'category'), we_base_request::_(we_base_request::STRING, 'name'), we_base_request::_(we_base_request::STRING, 'host'), we_base_request::_(we_base_request::FILE, 'path'), we_base_request::_(we_base_request::STRING, 's_method'), we_base_request::_(we_base_request::STRING, 'varname'), we_base_request::_(we_base_request::STRING, 'checkvia'), we_base_request::_(we_base_request::STRING, 'ctype'), we_base_request::_(we_base_request::STRING, 'additionalVars'), we_base_request::_(we_base_request::STRING, 'fileEndings'), we_base_request::_(we_base_request::BOOL, 'active'));
			if(validation::deleteService($_service)){
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][delete_success]'), we_message_reporting::WE_MESSAGE_NOTICE));
			} else {
				echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('validation', '[edit_service][delete_failure]'), WE() . consts . message . WE_MESSAGE_ERR)
				);
			}
			break;
		case 'selectService';
			$selectedName = we_base_request::_(we_base_request::STRING, 'validationService');
			break;
		case 'newService':
			$selectedService = new validationService(0, 'custom', 'accessible', g_l('validation', '[edit_service][new]'), 'www.example', '/path', 'get', 'varname', 'url', 'text/html', '', '.html', 1);
			break;
	}

	//  get all custom services from the database - new service select it
	$services = validation::getValidationServices('edit');
	if(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1) === 'newService' && $selectedService){
		$services[] = $selectedService;
	}

	if($services){
		foreach($services as $service){

			$selectArr[$service->getName()] = htmlentities($service->name);

			if(!isset($selectedService)){
				$selectedService = $service;
			}

			if(isset($selectedName) && $service->getName() == $selectedName){
				$selectedService = $service;
			}
		}
		$hiddenFields = we_html_element::htmlHiddens(array(
				'id' => $selectedService->id,
				'art' => 'custom'
		));
	} else {
		$hiddenFields = we_html_element::htmlHidden('art', 'custom');
		$selectArr = array();
	}




	//  table with new and delete
	$_table = '<table>
    <tr><td style="padding-right:10px;">' . we_html_tools::htmlSelect('validationService', $selectArr, 5, (isset($selectedService) ? $selectedService->getName() : ''), false, array('onchange' => 'we_cmd(\'customValidationService\',\'selectService\');'), "value", 320) . '</td>
        <td style="vertical-align:top">' . we_html_button::create_button('new_service', 'javascript:we_cmd(\'customValidationService\',\'newService\');')
		. '<div style="height:10px;"></div>'
		. we_html_button::create_button(we_html_button::DELETE, 'javascript:we_cmd(\'customValidationService\',\'deleteService\');', true, 100, 22, '', '', (empty($services))) . '
        </td>
    </tr>
    </table>' .
		$hiddenFields;

	$parts = array(
		array('headline' => g_l('validation', '[available_services]'), 'html' => $_table, 'space' => 150)
	);

	if($services){
		$parts[] = array('headline' => g_l('validation', '[category]'), 'html' => we_html_tools::htmlSelect('category', validation::getAllCategories(), 1, $selectedService->category), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[service_name]'), 'html' => we_html_tools::htmlTextInput('name', 50, $selectedService->name), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[host]'), 'html' => we_html_tools::htmlTextInput('host', 50, $selectedService->host), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[path]'), 'html' => we_html_tools::htmlTextInput('path', 50, $selectedService->path), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[ctype]'), 'html' => we_html_tools::htmlTextInput('ctype', 50, $selectedService->ctype) . '<br /><span class="small">' . g_l('validation', '[desc][ctype]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[fileEndings]'), 'html' => we_html_tools::htmlTextInput('fileEndings', 50, $selectedService->fileEndings) . '<br /><span class="small">' . g_l('validation', '[desc][fileEndings]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[method]'), 'html' => we_html_tools::htmlSelect('s_method', array('post' => 'post', 'get' => 'get'), 1, $selectedService->method, false), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[checkvia]'), 'html' => we_html_tools::htmlSelect('checkvia', array('url' => g_l('validation', '[checkvia_url]'), 'fileupload' => g_l('validation', '[checkvia_upload]')), 1, $selectedService->checkvia, false), 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[varname]'), 'html' => we_html_tools::htmlTextInput('varname', 50, $selectedService->varname) . '<br /><span class="small">' . g_l('validation', '[desc][varname]') . '</span>', 'space' => 150, 'noline' => 1);
		$parts[] = array('headline' => g_l('validation', '[additionalVars]'), 'html' => we_html_tools::htmlTextInput('additionalVars', 50, $selectedService->additionalVars) . '<br /><span class="small">' . g_l('validation', '[desc][additionalVars]') . '</span>', 'space' => 150);
		$parts[] = array('headline' => g_l('validation', '[active]'), 'html' => we_html_tools::htmlSelect('active', array(0 => 'false', 1 => 'true'), 1, $selectedService->active) . '<br /><span class="small">' . g_l('validation', '[desc][active]') . '</span>', 'space' => 150);
	}

	echo '<form name="we_form" onsubmit="return false;">' .
	we_html_multiIconBox::getHTML('weDocValidation', $parts, 30, we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('customValidationService','saveService');", true, 100, 22, '', '', (empty($services))), we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('close');")), -1, '', '', false, g_l('validation', '[adjust_service]'))
	. '</form>' .
	'</body></html>';
