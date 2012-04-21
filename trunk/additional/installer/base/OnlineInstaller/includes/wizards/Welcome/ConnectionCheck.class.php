<?php
class ConnectionCheck extends leStep {

	function execute(&$Template) {

		/*
		 * For command checkConnection, it is not needed to create a session on the
		 * server. Therefore treat this command in a special way.
		 */

		$Parameters = array(
			"update_cmd" => "checkConnection"
		);

		$RetValue = LE_STEP_NEXT;

		$Response = liveUpdateHttp::getHttpResponse($GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateServer'], $GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateScript'], $Parameters);
		$LiveUpdateResponse = new liveUpdateResponse();

		if ($LiveUpdateResponse->initByHttpResponse($Response)) {
			if ($LiveUpdateResponse->isError()) {
				$Template->addError($this->Language["connectionWithError"] . "<br />" . $LiveUpdateResponse->getField('Message'));
				$RetValue = LE_STEP_FATAL_ERROR;

			} else {
				$this->setContent($this->Language['connectionReady']);
				$this->AutoContinue = 10;

			}

		} else {
			$errorMessage = $this->Language["noConnection"];
			if(isset($Response)) {
				$errorMessage .= str_replace("</body></html>","",stristr($Response,"<body>"));
			}
			$errorMessage .= "<hr /><h1>".$this->Language["connectionInfo"].":</h1>";
			$errorMessage .= "<li>".$this->Language["availableConnectionTypes"].": ";
				$errorMessage .= "<ul>";
				if(ini_get("allow_url_fopen") == "1") {
					$errorMessage .= "<li>fopen</li>";
				}
				if(is_callable("curl_exec")) {
					$errorMessage .= "<li>curl</li>";
				}
				$errorMessage .= "</ul>";
			$errorMessage .= "<li>".$this->Language["connectionType"].": ";
			if (isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use']=="1") {
				$errorMessage .= "Proxy (fsockopen)".
				"<ul>".
				"<li>".$this->Language["proxyHost"].": ".$_SESSION["le_proxy_host"]."</li>".
				"<li>".$this->Language["proxyPort"].": ".$_SESSION["le_proxy_port"]."</li>";
				if(is_callable("gethostbynamel") && is_callable("gethostbyaddr")) {
					if(preg_match("/(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)/",$_SESSION["le_proxy_host"])) {
						$errorMessage .= "<li>".$this->Language["ipResolutionTest"]." (IPv4 only): ";
						$hostName = gethostbyaddr((string)$_SESSION["le_proxy_host"]);
						if($hostName != $_SESSION["le_proxy_host"]) {
							$errorMessage .= "".$this->Language["succeeded"].".</li>".
							"<li>".$this->Language["hostName"].": ".$hostName."</li>";
						} else {
							$errorMessage .= "".$this->Language["failed"].".</li>";
						}
					}
					// gethostbyaddr currently does not support ipv6 address resolution
					/*
					else if(preg_match("/^([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4}$/",$_SESSION["le_proxy_host"])) {
						$errorMessage .= "<li>".$this->Language["ipResolutionTest"]." (IPv6): ";
						$hostName = gethostbyaddr($_SESSION["le_proxy_host"],DNS_AAAA);
						if($hostName != $_SESSION["le_proxy_host"]) {
							$errorMessage .= "".$this->Language["succeeded"].".</li>".
							"<li>".$this->Language["ipAddresses"].": ".$hostName."</li>";
						} else {
							$errorMessage .= "".$this->Language["failed"].".</li>";
						}
					}
					*/
					else {
						$errorMessage .= "<li>".$this->Language["dnsResolutionTest"].": ";
						if($ipAddr = gethostbynamel($_SESSION["le_proxy_host"])) {
							$errorMessage .= "".$this->Language["succeeded"].".</li>".
							"<li>".$this->Language["ipAddresses"].": ".implode(",",$ipAddr)."</li>";
						} else {
							$errorMessage .= "".$this->Language["failed"].".</li>";
						}
					}
				}
				$errorMessage .= "</ul>";
			} else {
				$errorMessage .= liveUpdateHttp::getHttpOption();
			}
			$errorMessage .= "</li>";
			$errorMessage .= "<li>".$this->Language["addressResolution"]." ".$this->Language["updateServer"].":</li>";
			$errorMessage .= "<ul>";
			$errorMessage .= "<li>".$this->Language["hostName"].": ".$GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateServer']."</li>";
			if(is_callable("gethostbynamel")) {
				$errorMessage .= "<li>".$this->Language["dnsResolutionTest"].": ";
				if($ipAddr = gethostbynamel($GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateServer'])) {
					$errorMessage .= "".$this->Language["succeeded"].".</li>".
					"<li>".$this->Language["ipAddresses"].": ".implode(",",$ipAddr)."</li>";
				} else {
					$errorMessage .= "".$this->Language["failed"].".</li>";
				}
				$errorMessage .= "</ul>";
			}
			$Template->addError($errorMessage);
			$RetValue = LE_STEP_FATAL_ERROR;

		}


		$this->setHeadline($this->Language['headline']);

		return $RetValue;

	}

}