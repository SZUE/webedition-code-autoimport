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
$errorMessage = (isset($Response) ? str_replace("</body></html>", "", stristr($Response, "<body>")) : '') .
	"<div id=\"contentHeadlineDiv\" style=\"height: 30px; margin-top:30px; \">
			<b>" . g_l('liveUpdate', '[connect][connectionInfo]') . "<hr /></b>
			</div><br />
	<li>" . g_l('liveUpdate', '[connect][availableConnectionTypes]') . ":
	<ul>" .
	(ini_get("allow_url_fopen") == "1" ?
		"<li>fopen</li>" : '') .
	(is_callable("curl_exec") ?
		"<li>curl</li>" : '') .
	"</ul>
	<li>" . g_l('liveUpdate', '[connect][connectionType]') . ": ";
if(isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use'] == 1){
	$errorMessage .= "Proxy (fsockopen)" .
		"<ul>" .
		"<li>" . g_l('liveUpdate', '[connect][proxyHost]') . ": " . $_SESSION["le_proxy_host"] . "</li>" .
		"<li>" . g_l('liveUpdate', '[connect][proxyPort]') . ": " . $_SESSION["le_proxy_port"] . "</li>";
	if(is_callable("gethostbynamel") && is_callable("gethostbyaddr")){
		if(preg_match("/(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/", $_SESSION["le_proxy_host"])){
			$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][ipResolutionTest]') . " (IPv4 only): ";
			$hostName = gethostbyaddr((string) $_SESSION["le_proxy_host"]);
			if($hostName != $_SESSION["le_proxy_host"]){
				$errorMessage .= "" . g_l('liveUpdate', '[connect][succeeded]') . ".</li>" .
					"<li>" . g_l('liveUpdate', '[connect][hostName]') . ": " . $hostName . "</li>";
			} else {
				$errorMessage .= "" . g_l('liveUpdate', '[connect][failed]') . ".</li>";
			}
		}
		// gethostbyaddr currently does not support ipv6 address resolution
		/*
		  else if(preg_match("/^([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4}$/",$_SESSION["le_proxy_host"])) {
		  $errorMessage .= "<li>".$GLOBALS['l_liveUpdate']['connect']["ipResolutionTest"]." (IPv6): ";
		  $hostName = gethostbyaddr($_SESSION["le_proxy_host"],DNS_AAAA);
		  if($hostName != $_SESSION["le_proxy_host"]) {
		  $errorMessage .= "".$GLOBALS['l_liveUpdate']['connect']["succeeded"].".</li>".
		  "<li>".$GLOBALS['l_liveUpdate']['connect']["ipAddresses"].": ".$hostName."</li>";
		  } else {
		  $errorMessage .= "".$GLOBALS['l_liveUpdate']['connect']["failed"].".</li>";
		  }
		  }
		 */ else {
			$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][dnsResolutionTest]') . ": ";
			if($ipAddr = gethostbynamel($_SESSION["le_proxy_host"])){
				$errorMessage .= "" . g_l('liveUpdate', '[connect][succeeded]') . ".</li>" .
					"<li>" . g_l('liveUpdate', '[connect][ipAddresses]') . ": " . implode(",", $ipAddr) . "</li>";
			} else {
				$errorMessage .= "" . g_l('liveUpdate', '[connect][failed]') . ".</li>";
			}
		}
	}
	$errorMessage .= "</ul>";
} else {
	$errorMessage .= liveUpdateHttp::getHttpOption();
}
$errorMessage .= "</li>" .
	"<li>" . g_l('liveUpdate', '[connect][addressResolution]') . " " . g_l('liveUpdate', '[connect][updateServer]') . ":</li>" .
	"<ul>" .
	"<li>" . g_l('liveUpdate', '[connect][hostName]') . ": " . LIVEUPDATE_SERVER . "</li>";
if(is_callable("gethostbynamel")){
	$errorMessage .= "<li>" . g_l('liveUpdate', '[connect][dnsResolutionTest]') . ": " .
		(($ipAddr = gethostbynamel(LIVEUPDATE_SERVER)) ?
			g_l('liveUpdate', '[connect][succeeded]') . '.</li><li>' . g_l('liveUpdate', '[connect][ipAddresses]') . ": " . implode(",", $ipAddr) . "</li>" :
			g_l('liveUpdate', '[connect][failed]') . ".</li>"
		) . "</ul>";
}

$content = '
<div class="defaultfont">
	' . g_l('liveUpdate', '[connect][connectionError]') . '
</div>' . we_html_element::jsElement('alert("' . g_l('liveUpdate', '[connect][connectionErrorJs]') . '");') . $errorMessage;

echo liveUpdateTemplates::getHtml(g_l('liveUpdate', '[connect][headline]'), $content);
