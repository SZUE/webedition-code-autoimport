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
class liveUpdateHttp{

	function getServerProtocol($addslashes = true){
		return getServerProtocol($addslashes);
	}

	function connectFopen($server, $url, $parameters = array()){

		// try fopen first
		$parameterStr = '';
		foreach($parameters as $key => $value){
			$parameterStr .= "$key=" . urlencode($value) . "&";
		}

		$address = 'https://' . $server . $url . ($parameterStr ? "?$parameterStr" : '');
		$response = false;

		$fh = @fopen($address, "rb");
		if($fh){
			$response = "";
			while(!feof($fh)){
				$response .= fgets($fh, 1024);
			}
			fclose($fh);
		}
		return $response;
	}

	function connectProxy($server, $url, $parameters){
		$proxyhost = defined("WE_PROXYHOST") ? WE_PROXYHOST : "";
		$proxyport = (defined("WE_PROXYPORT") && WE_PROXYPORT) ? WE_PROXYPORT : "80";
		$proxy_user = defined("WE_PROXYUSER") ? WE_PROXYUSER : "";
		$proxy_pass = defined("WE_PROXYPASSWORD") ? WE_PROXYPASSWORD : "";

		$response = @fsockopen($proxyhost, $proxyport, $errno, $errstr, 20);

		if(!$response){

			return false;
		} else {

			$parameterStr = '';
			foreach($parameters as $key => $value){
				$parameterStr .= "$key=" . urlencode($value) . "&";
			}

			$address = 'https://' . $server . $url . ($parameterStr ? "?$parameterStr" : '');

			$realm = base64_encode($proxy_user . ":" . $proxy_pass);

			// send headers
			fputs($response, "GET $address HTTP/1.0\r\n");
			//fputs($response, "Proxy-Connection: Keep-Alive\r\n");
			fputs($response, 'User-Agent: PHP ' . phpversion() . "\r\n");
			fputs($response, "Pragma: no-cache\r\n");
			if($proxy_user != ""){
				fputs($response, "Proxy-authorization: Basic $realm\r\n");
			}
			fputs($response, "\r\n");

			$zeile = "";
			while(!feof($response)){
				$zeile = $zeile . fread($response, 4096);
			}
			fclose($response);

			return substr($zeile, strpos($zeile, "\r\n\r\n") + 4);
		}
	}

	function getCurlHttpResponse($server, $url, $parameters){

		$_address = 'https://' . $server . $url;

		$_parameters = '';
		foreach($parameters as $key => $value){
			$_parameters .= "$key=" . urlencode($value) . "&";
		}

		$session = curl_init();
		curl_setopt($session, CURLOPT_URL, $_address);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);

		if($_parameters != ''){
			curl_setopt($session, CURLOPT_POST, 1);
			curl_setopt($session, CURLOPT_POSTFIELDS, $_parameters);
		}

		if(defined('WE_PROXYHOST') && WE_PROXYHOST != ''){

			$_proxyhost = defined('WE_PROXYHOST') ? WE_PROXYHOST : '';
			$_proxyport = (defined('WE_PROXYPORT') && WE_PROXYPORT) ? WE_PROXYPORT : 80;
			$_proxy_user = defined('WE_PROXYUSER') ? WE_PROXYUSER : '';
			$_proxy_pass = defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : '';

			if($_proxyhost != ''){
				curl_setopt($session, CURLOPT_PROXY, $_proxyhost . ":" . $_proxyport);
				if($_proxy_user != ''){
					curl_setopt($session, CURLOPT_PROXYUSERPWD, $_proxy_user . ':' . $_proxy_pass);
				}
				curl_setopt($session, CURLOPT_SSL_VERIFYPEER, FALSE);
			}
		}

		$_data = curl_exec($session);

		curl_close($session);

		return $_data;
	}

	function getHttpOption(){
		if(ini_get('allow_url_fopen') != 1 && strtolower(ini_get('allow_url_fopen')) != "on"){
			@ini_set('allow_url_fopen', 1);
			if(ini_get('allow_url_fopen') != 1 && strtolower(ini_get('allow_url_fopen')) != "on"){
				if(function_exists('curl_init')){
					return 'curl';
				} else {
					return 'none';
				}
			}
		}
		return 'fopen';
	}

	function getHttpResponse($server, $url, $parameters = array()){
		switch(liveUpdateHttp::getHttpOption()){
			case 'fopen':
				return liveUpdateHttp::getFopenHttpResponse($server, $url, $parameters);
			case 'curl':
				return liveUpdateHttp::getCurlHttpResponse($server, $url, $parameters);
			default:
				return null; // return null otherwise php error
				//'Server error: Unable to open URL (php configuration directive allow_url_fopen=Off)';
		}
	}

	function getFopenHttpResponse($server, $url, $parameters = array()){
		return (defined("WE_PROXYHOST") && WE_PROXYHOST ?
				liveUpdateHttp::connectProxy($server, $url, $parameters) :
				liveUpdateHttp::connectFopen($server, $url, $parameters)
			);
	}

	/**
	 * returns html page with formular to init session on the server
	 *
	 * @return unknown
	 */
	function getServerSessionForm(){
		$params = '';
		foreach($GLOBALS['LU_Variables'] as $LU_name => $LU_value){
			$params .= '<input type="hidden" name="' . $LU_name . '" value="' . urlencode((is_array($LU_value) ? base64_encode(serialize($LU_value)) : $LU_value)) . '" />';
		}

		return we_html_tools::headerCtCharset('text/html', $GLOBALS['WE_BACKENDCHARSET']) . we_html_element::htmlDocType() . '<html>
<head>
	' . LIVEUPDATE_CSS . '
<head>
<body onload="document.getElementById(\'liveUpdateForm\').submit();">
<form id="liveUpdateForm" action="https://' . LIVEUPDATE_SERVER . LIVEUPDATE_SERVER_SCRIPT . '" method="post">
	<input type="hidden" name="update_cmd" value="startSession" /><br />
	<input type="hidden" name="next_cmd" value="' . we_base_request::_(we_base_request::STRING, 'update_cmd') . '" />
	<input type="hidden" name="detail" value="' . we_base_request::_(we_base_request::STRING, 'detail') . '" />
	' . $params . '
</form>
</body>
</html>';
	}

}
