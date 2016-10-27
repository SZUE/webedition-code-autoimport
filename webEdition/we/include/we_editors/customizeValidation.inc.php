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
echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', we_html_element::jsScript(JS_DIR . 'customizeValidation.js'));
?>
<body class="weDialogBody" style="overflow:hidden;">
	<?php
	switch(we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1)){
		case 'saveService':
			$service = new validationService(we_base_request::_(we_base_request::INT, 'id'), 'custom', we_base_request::_(we_base_request::STRING, 'category'), we_base_request::_(we_base_request::STRING, 'name'), we_base_request::_(we_base_request::STRING, 'host'), we_base_request::_(we_base_request::FILE, 'path'), we_base_request::_(we_base_request::STRING, 's_method'), we_base_request::_(we_base_request::STRING, 'varname'), we_base_request::_(we_base_request::STRING, 'checkvia'), we_base_request::_(we_base_request::STRING, 'ctype'), we_base_request::_(we_base_request::STRING, 'additionalVars'), we_base_request::_(we_base_request::STRING, 'fileEndings'), we_base_request::_(we_base_request::BOOL, 'active'));
			if(($selectedService = validation::saveService($service))){
				echo we_message_reporting::jsMessagePush(g_l('validation', '[edit_service][saved_success]'), we_message_reporting::WE_MESSAGE_NOTICE);
			} else {
				$selectedService = $service;
				echo we_message_reporting::jsMessagePush(g_l('validation', '[edit_service][saved_failure]') . (isset($GLOBALS['errorMessage']) ? '\n' . $GLOBALS['errorMessage'] : ''), we_message_reporting::WE_MESSAGE_ERROR);
			}
			break;
		case 'deleteService':
			$service = new validationService(we_base_request::_(we_base_request::INT, 'id'), 'custom', we_base_request::_(we_base_request::STRING, 'category'), we_base_request::_(we_base_request::STRING, 'name'), we_base_request::_(we_base_request::STRING, 'host'), we_base_request::_(we_base_request::FILE, 'path'), we_base_request::_(we_base_request::STRING, 's_method'), we_base_request::_(we_base_request::STRING, 'varname'), we_base_request::_(we_base_request::STRING, 'checkvia'), we_base_request::_(we_base_request::STRING, 'ctype'), we_base_request::_(we_base_request::STRING, 'additionalVars'), we_base_request::_(we_base_request::STRING, 'fileEndings'), we_base_request::_(we_base_request::BOOL, 'active'));
			echo (validation::deleteService($service) ?
				we_message_reporting::jsMessagePush(g_l('validation', '[edit_service][delete_success]'), we_message_reporting::WE_MESSAGE_NOTICE) :
				we_message_reporting::jsMessagePush(g_l('validation', '[edit_service][delete_failure]'), we_message_reporting::WE_MESSAGE_ERR)
			);
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
		$hiddenFields = we_html_element::htmlHiddens(['id' => $selectedService->id,
			'art' => 'custom'
				]);
} else {
		$hiddenFields = we_html_element::htmlHidden('art', 'custom');
		$selectArr = [];
	}


	//  table with new and delete
	$table = '<table>
    <tr><td style="padding-right:10px;">' . we_html_tools::htmlSelect('validationService', $selectArr, 5, (isset($selectedService) ? $selectedService->getName() : ''), false, [
		'onchange' => 'we_cmd(\'customValidationService\',\'selectService\');'], "value", 320) . '</td>
        <td style="vertical-align:top">' . we_html_button::create_button('new_service', "javascript:we_cmd('customValidationService','newService');")
		. '<div style="height:10px;"></div>'
		. we_html_button::create_button(we_html_button::DELETE, "javascript:we_cmd('customValidationService','deleteService');", '', 0, 0, '', '', (empty($services))) . '
        </td>
    </tr>
    </table>' .
		$hiddenFields;

	$parts = [
		['headline' => g_l('validation', '[available_services]'), 'html' => $table, 'space' => we_html_multiIconBox::SPACE_MED2]
	];

	if($services){
		$parts[] = ['headline' => g_l('validation', '[category]'), 'html' => we_html_tools::htmlSelect('category', validation::getAllCategories(), 1, $selectedService->category), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[service_name]'), 'html' => we_html_tools::htmlTextInput('name', 50, $selectedService->name), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[host]'), 'html' => we_html_tools::htmlTextInput('host', 50, $selectedService->host), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[path]'), 'html' => we_html_tools::htmlTextInput('path', 50, $selectedService->path), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[ctype]'), 'html' => we_html_tools::htmlTextInput('ctype', 50, $selectedService->ctype) . '<br /><span class="small">' . g_l('validation', '[desc][ctype]') . '</span>', 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[fileEndings]'), 'html' => we_html_tools::htmlTextInput('fileEndings', 50, $selectedService->fileEndings) . '<br /><span class="small">' . g_l('validation', '[desc][fileEndings]') . '</span>', 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[method]'), 'html' => we_html_tools::htmlSelect('s_method', ['post' => 'post', 'get' => 'get'], 1, $selectedService->method, false), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[checkvia]'), 'html' => we_html_tools::htmlSelect('checkvia', ['url' => g_l('validation', '[checkvia_url]'), 'fileupload' => g_l('validation', '[checkvia_upload]')], 1, $selectedService->checkvia, false), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[varname]'), 'html' => we_html_tools::htmlTextInput('varname', 50, $selectedService->varname) . '<br /><span class="small">' . g_l('validation', '[desc][varname]') . '</span>', 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1];
		$parts[] = ['headline' => g_l('validation', '[additionalVars]'), 'html' => we_html_tools::htmlTextInput('additionalVars', 50, $selectedService->additionalVars) . '<br /><span class="small">' . g_l('validation', '[desc][additionalVars]') . '</span>', 'space' => we_html_multiIconBox::SPACE_MED2];
		$parts[] = ['headline' => g_l('validation', '[active]'), 'html' => we_html_tools::htmlSelect('active', [0 => 'false', 1 => 'true'], 1, $selectedService->active) . '<br /><span class="small">' . g_l('validation', '[desc][active]') . '</span>', 'space' => we_html_multiIconBox::SPACE_MED2];
	}

	echo '<form name="we_form" onsubmit="return false;">' .
	we_html_multiIconBox::getHTML('weDocValidation', $parts, 30, we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('customValidationService','saveService');", '', 0, 0, '', '', (empty($services))), we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('close');")), -1, '', '', false, g_l('validation', '[adjust_service]'))
	. '</form>' .
	'</body></html>';
