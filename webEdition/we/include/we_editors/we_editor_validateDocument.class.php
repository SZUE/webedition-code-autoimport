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

class we_editor_validateDocument extends we_editor_base{

	private function getCSSServices(){
		$i = 0;
		return [
			new we_validation_service($i++, 'default', 'css', g_l('validation', '[service_css_upload]'), 'jigsaw.w3.org', '/css-validator/validator', 'post', 'file', 'fileupload', 'text/css', 'usermedium=all&submit=check', '.css', 1),
			new we_validation_service($i++, 'default', 'css', g_l('validation', '[service_css_url]'), 'jigsaw.w3.org', '/css-validator/validator', 'get', 'uri', 'url', 'text/css', 'usermedium=all', '.css', 1)
		];
	}

	private function getHTMLServices(){
		$i = 0;

//  first xhtml from W3C
		return [
			new we_validation_service($i++, 'default', 'xhtml', g_l('validation', '[service_xhtml_upload]'), 'validator.w3.org', '/check', 'post', 'uploaded_file', 'fileupload', 'text/html', '', '.html,.htm,.php', 1),
			new we_validation_service($i++, 'default', 'xhtml', g_l('validation', '[service_xhtml_url]'), 'validator.w3.org', '/check', 'get', 'uri', 'url', 'text/html', '', '.html,.htm,.php', 1)
		];
	}

	public function show(){

//  for predefined services include properties file, depending on content-Type
//  and depending on fileending.
		if($this->we_doc->ContentType == we_base_ContentTypes::CSS || $this->we_doc->Extension === '.css'){
			$validationService = $this->getCSSServices();
		} else {
			$validationService = $this->getHTMLServices();
		}

		$services = [];
		$json = [];

		foreach($validationService as $service){
			$services[$service->art][$service->category][] = $service;
		}

//  get custom services from database ..
		$customServices = we_validation_base::getValidationServices('use');

		if(!empty($customServices)){
			foreach($customServices as $cService){
				$services['custom'][$cService->category][] = $cService;
			}
		}

//  Generate Select-Menu with optgroups
		krsort($services);

		$select = $lastArt = $lastCat = $hiddens = '';
		if($services){
			$select = '<select name="service" class="weSelect" style="width:350px;" onchange="switchPredefinedService(this.options[this.selectedIndex].value);">';
			foreach($services as $art => $arr){
				foreach($arr as $cat => $arrServices){
					foreach($arrServices as $service){

						if($lastArt != $art){
							if($lastArt != ''){
								$select .= '</optgroup>';
								$lastCat = '1';
							}
							$lastArt = $art;
							$select .= '<optgroup class="lvl1" label="' . g_l('validation', '[art_' . $art . ']') . '">';
						}
						if($lastCat != $cat){
							if($lastCat != ''){
								$select .= '</optgroup>';
							}
							$lastCat = $cat;

							$select .= '<optgroup class="lvl2" label="-- ' . g_l('validation', '[category_' . $cat . ']') . '">';
						}
						$select .= '<option value="' . $service->getName() . '">' . oldHtmlspecialchars($service->name) . '</option>';
						$json[$service->getName()] = [
							'host' => $service->host,
							'path' => $service->path,
							's_method' => $service->method,
							'varname' => $service->varname,
							'checkvia' => $service->checkvia,
							'ctype' => $service->ctype,
							'additionalVars' => $service->additionalVars,
						];
					}
				}
			}
			$select .= '</optgroup></optgroup></select>';
			$selectedService = $validationService[0];
			$hiddens = we_html_element::htmlHiddens([
					'host' => $selectedService->host,
					'path' => $selectedService->path,
					'ctype' => $selectedService->ctype,
					's_method' => $selectedService->method,
					'checkvia' => $selectedService->checkvia,
					'varname' => $selectedService->varname,
					'additionalVars' => $selectedService->additionalVars
			]);
		} else {
			$select = g_l('validation', '[no_services_available]');
		}

//  generate Body of page
		$parts = [
			['html' => g_l('validation', '[description]'),],
			['headline' => g_l('validation', '[service]'),
				'html' =>
				'<table class="default">
<tr><td class="defaultfont" style="padding-right:20px;">' .
				$select .
				$hiddens .
				'</td><td style="padding-right:20px;">' .
				we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('customValidationService')", '', 0, 0, "", "", !we_base_permission::hasPerm("CAN_EDIT_VALIDATION"))
				. '</td><td>' .
				we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('checkDocument')", '', 0, 0, '', '', (empty($services)))
				. '</td></tr></table>'
				, 'space' => we_html_multiIconBox::SPACE_MED],
			['html' => g_l('validation', '[result]'), 'noline' => 1,],
			['html' => '<iframe name="validation" id="validation" src="' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=checkDocument" style="width:680px;height:400px;"></iframe>',
				'space' => we_html_multiIconBox::SPACE_SMALL],
		];


		return $this->getPage(we_html_multiIconBox::getHTML('weDocValidation', $parts, 20), we_html_element::jsScript(JS_DIR . 'validateDocument.js', '', ['id' => 'loadVarValidateDocument',
					'data-validate' => setDynamicVar($json)]), [
				'onload' => 'setIFrameSize()',
				'onresize' => 'setIFrameSize()'
		]);
	}

}
