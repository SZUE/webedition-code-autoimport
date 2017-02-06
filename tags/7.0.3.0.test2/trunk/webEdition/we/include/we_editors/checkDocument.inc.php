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

if(($we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'))){ //  initialise Document
	$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

	$GLOBALS['we_doc']->InWebEdition = false;

	$content = $GLOBALS['we_doc']->getDocument();

	$host = we_base_request::_(we_base_request::STRING, 'host');
	if($host != 'validator.w3.org' && !f('SELECT 1 FROM ' . VALIDATION_SERVICES_TABLE . ' WHERE host="' . $GLOBALS['DB_WE']->escape($host) . '" LIMIT 1')){
		exit($host . ' not in allowed hosts!');
	}

	$path = we_base_request::_(we_base_request::FILE, 'path');
	$s_method = we_base_request::_(we_base_request::STRING, 's_method');
	$varname = we_base_request::_(we_base_request::STRING, 'varname');
	$contentType = we_base_request::_(we_base_request::STRING, 'ctype');

	$http_request = new we_http_request($path, $host, $s_method);
	$http_request->addHeader('User-Agent', $_SERVER['HTTP_USER_AGENT']);

	//  add additional parameters to the request
	if(($add = we_base_request::_(we_base_request::STRING, 'additionalVars'))){
		$args = explode('&', $add);
		foreach($args as $pair){
			$keyValue = explode('=', $pair);
			$http_request->addVar($keyValue[0], $keyValue[1]);
		}
	}

	//  generate name of file.  - must be .html because of <?xml and short-open tags
	$extension = $GLOBALS['we_doc']->Extension;
	$filename = '/' . $we_transaction . $extension;

	//  check what should happen with document
	if(we_base_request::_(we_base_request::STRING, 'checkvia') === 'fileupload'){ //  submit via fileupload
		$http_request->addFileByContent($varname, $content, $contentType, $filename);
	} else { //  submit via onlinecheck - site must be available online
		// when it is a dynamic document, remove <?xml when short_open_tags are allowed.
		if(ini_get("short_open_tag") == 1 && $GLOBALS['we_doc']->IsDynamic && $contentType === 'text/html'){
			$content = str_replace("<?xml", '<?php echo "<?xml"; ?>', $content);
		}

		//  save file - submit URL to service
		$tmpFile = $_SERVER['DOCUMENT_ROOT'] . $filename;
		we_base_file::save($tmpFile, $content);
		we_base_file::insertIntoCleanUp($tmpFile, 0);

		$url = getServerUrl() . $filename;
		$http_request->addVar($varname, $url);
	}

	$http_request->executeHttpRequest();

	//  check if all worked well..
	if(!$http_request->error){

		$http_response = new we_http_response($http_request->getHttpResponseStr());

		echo ($http_response->getHttp_answer('code') == 200 ?
			//  change base href -> css of included page is loaded correctly
			str_replace('<head>', '<head><base href="http://' . $host . '" />', $http_response->http_body) :
//  no correct answer
			we_html_tools::getHtmlTop(g_l('validation', '[connection_problems]'), '', '', STYLESHEET, '<body>' .
				we_html_tools::htmlAlertAttentionBox(sprintf(g_l('validation', '[connection_problems]'), $http_response->getHttp_answer()), we_html_tools::TYPE_ALERT, 0, false) .
				'</body>')
		);
	} else {
		echo $http_request->errno . ": " . $http_request->errstr . '<br/>';
	}
} else {
	echo ' &hellip; ';
}
