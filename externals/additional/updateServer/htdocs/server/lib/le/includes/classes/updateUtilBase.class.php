<?php

class updateUtilBase {


	/**
	 * Checks if given Ip is within range of Wirtualna Polska ips
	 *
	 * @param string $ip
	 * @return boolean
	 */
    function isWpolskaIp($ip) {
		global $rootDir, $wpolskas_ips;

		// include local file with known wpolska ips
		include($rootDir . "/extras/registration/wpolska-ips.inc.php");

		// calculate and correct ip
		$ip = updateUtil::correctIp($ip);
	    $checkIp = ip2long($ip);

	    // check ip with given ips from file
	    for ($i = 0; $i < sizeof($wpolskas_ips); $i++) {

	        $start = ip2long($wpolskas_ips[$i]["start"]);
	        $stop  = ip2long($wpolskas_ips[$i]["stop"]);

	        if (($start <= $checkIp) && ($checkIp <=$stop)) {
	            return true;
	        }
	    }
		return false;

    }


	/**
	 * Checks if given Ip is within range of strato-ips
	 *
	 * @param string $ip
	 * @return boolean
	 */
	function isStratoIp($ip) {
	    global $rootDir, $strato_ips;

	    $ip = updateUtil::correctIp($ip);
	    $checkIp = ip2long($ip);

	    // include local file with known strato ips
	    include($rootDir . "/extras/registration/strato-ips.inc.php");

	    // check ip with given ips from file
	    for ($i = 0; $i < sizeof($strato_ips); $i++) {
	        $start = ip2long($strato_ips[$i]["start"]);
	        $stop  = ip2long($strato_ips[$i]["stop"]);

	        if (($start <= $checkIp) && ($checkIp <=$stop)) {
	            return true;

	        }

	    }

	    // ip does not match with known ips so read file on strato server
	    if (fopen("http://www.strato.de/shop/iparea.txt", "r")) {
	    	$fileContent = file("http://www.strato.de/shop/iparea.txt");

            $strato_ips_extern = array();

            // get given ips and tsave them in array
            foreach ($fileContent as $ip) {

                if (strpos($ip, "#") !== 0) {
                    $strato_ips_extern[] = trim($ip);

                }

            }

            // now check if ip is in these arrays
    	    for ($i = 0; $i < sizeof($strato_ips_extern); $i++) {
    	        $iprange = updateUtil::getIpRange($strato_ips_extern[$i]);

    	        $start = ip2long($iprange["start"]);
    	        $stop  = ip2long($iprange["stop"]);

    	        if (($start <= $checkIp) && ($checkIp <=$stop)) {
    	            return true;

    	        }

    	    }

	    }
	    return false;

	}


	/**
	 * returns if domain is localhost
	 *
	 * @param string $domain
	 * @return boolean
	 */
	function isLocalhost($domain) {

		if( strtolower($domain) == "localhost" || $domain == "127.0.0.1" ){
			return true;

		}
		return false;

	}


	/**
	 * return serialized version of the response
	 *
	 * @param array $responseArray
	 * @return string
	 */
	function serializeResponseArray($responseArray) {

		return base64_encode(serialize($responseArray));

	}


	/**
	 * generates the response string from a given array. Occassionaly renames
	 * parameters of the given array. returns serialized version of the
	 * response-array
	 *
	 * @param array $array
	 * @return string
	 */
	static function getResponseString($array) {

		$responseArray = array();

		foreach ($array as $key => $value) {
			$responseArray[ucfirst($key)] = $value;

		}
		return updateUtil::serializeResponseArray($responseArray);

	}


	/**
	 * This function makes it easy to add php-code to a template.
	 *
	 * @param string $code
	 * @return string
	 */
	function addPhpCodeToTemplate($code) {

		return $code;

	}


	/**
	 * includes given response file and returns included array
	 *
	 * @param string $file
	 * @return array
	 */
	function getLiveUpdateResponseArrayFromFile($file) {
		global $updateServerTemplateData;

		$liveUpdateResponse = array();
		if (file_exists($file)) {

			include_once($file);

		} else {
			$liveUpdateResponse['Type'] = 'template';
			$liveUpdateResponse['headline'] = 'Error';
			$liveUpdateResponse['Content'] = '
			<div class="errorDiv">
				 Can\'t find template
			</div>';
		}
		if(isset($_SESSION["displayAnnouncement"]) && $_SESSION["displayAnnouncement"] === true) {
			$liveUpdateResponse["Code"] = str_replace('$content = \'','$content = \'<div class="messageDiv">'.$GLOBALS["lang"]['notification']['importantAnnouncement'].'</div>',$liveUpdateResponse["Code"]);
		}
		return $liveUpdateResponse;

	}


	function getEncodedLiveUpdateResponseArrayFromFile($file) {

		return updateUtil::encodeCode( updateUtil::getLiveUpdateResponseArrayFromFile($file) );

	}


	/**
	 * executes given file and returns the output.
	 *
	 * @param string $file
	 * @return string
	 */
	function getTemplateContentForResponse($file) {
		global $updateServerTemplateData;

		$content = '';

		if (file_exists($file)) {
			ob_start();
			include($file);
			$content = ob_get_contents();
			ob_end_clean();

		}
		return $content;

	}

	/**
	 * this function is used to use response inside a response, this is used
	 * whenever a template is used inside a template
	 *
	 * @param string $file
	 * @return string
	 */
	function getEncodedTemplateContentForResponse($file) {
		global $updateServerTemplateData;

		return updateUtil::encodeCode((updateUtil::getTemplateContentForResponse($file)));

	}


	/**
	 * returns always needed formular fields
	 *
	 * @param string $update_cmd
	 * @param string $detail
	 * @param string $liveUpdateSession
	 * @return string
	 */
	function getCommonFormFields($update_cmd, $detail, $liveUpdateSession=false) {
		global $clientRequestVars;

		return "
			<input type=\"hidden\" name=\"update_cmd\" value=\"$update_cmd\" />
			<input type=\"hidden\" name=\"detail\" value=\"$detail\" />
			<input type=\"hidden\" name=\"liveUpdateSession\" value=\"" . ($liveUpdateSession ? $liveUpdateSession : session_id()) . "\" />
		";

	}

	/**
	 * splits a version (string) to a number. If this number has less than 4
	 * numbers, it gets some ending "0"s
	 *
	 * @param string $number
	 * @param integer $length
	 * @return integer
	 */
	function version2number($version, $length=VERSIONNUMBER_LENGTH) {

		$numberStr = str_replace('.', '', $version);
		$number = (int) $numberStr;

		return $number;

	}


	/**
	 * this function converts a versionnumber (integer) to the number as string
	 * each number separated with ".". Parameter length determines how many
	 * numbers the version contains - all missing are filled with "0".
	 *
	 * @param integer $number
	 * @param integer $length
	 * @return string
	 */
	function number2version($number, $length=VERSIONNUMBER_LENGTH) {

		$number = "$number";
		$numbers = array();

		for ($i=0;$i<$length;$i++) {
			if (isset($number[$i])) {
				$numbers[] = $number[$i];

			} else {
				$numbers[] = 0;

			}

		}
		return implode('.', $numbers);

	}


	/**
	 * returns array of ip-range array(start=>IP,stop=>IP)
	 *
	 * @param string $ip
	 * @return array
	 */
    function getIpRange($ip) {
        // correct format of given ip-address
        $ip = updateUtil::correctIp($ip);

        $startIp = $ip;
        $stopIp  = $ip;

        // determine
        $iparr = explode(".", $ip);

        switch (sizeof($iparr)) {
	        case 1:
	            $startIp .= ".0";
	            $stopIp  .= ".255";

	        case 2:
	            $startIp .= ".0";
	            $stopIp  .= ".255";

	        case 3:
	            $startIp .= ".0";
	            $stopIp  .= ".255";

	        case 4:
	            return array('start' => $startIp, 'stop' => $stopIp);
				break;

	        default:
	            return array('start' => "0.0.0.0", 'stop' => "0.0.0.0");
				break;

        }

    }


	/**
	 * checks and corrects given ip-address to the form
	 * for example 180.005.80 -> 180.5.80.
	 * Attention: only corrects as many numbers as given !!!
	 *
	 * @param string $ip
	 * @return string
	 */
	function correctIp($ip) {

		$iparr = explode(".", $ip);

		for ($i=0; $i < sizeof($iparr); $i++) {
			$iparr[$i] = abs($iparr[$i]);

		}
		return implode(".", $iparr);

	}

	/**
	 * returns nearest, smaller key from array compared to given version
	 *
	 * @param array $array
	 * @param integer $version
	 * @return string
	 */
	function getNearestVersion($array, $version){

		if ( sizeof($array) == 1 ) {
			return key($array);

		}

		if (isset($array[$version])) {
			return $version;

		}

		krsort($array);
		foreach ($array as $key => $value) {
			if (intval($key) <= $version) {
				return $key;

			}

		}

	}


	/**
	 * returns parameter base_64 encoded
	 *
	 * @param string $string
	 * @return string
	 */
	static function encodeCode($string) {
		return base64_encode($string);

	}


	// output of standard templates

	/**
	 * complete needed output for using an internal response object
	 *
	 * @param string $templatePath
	 * @return string
	 */
	function getInternalResponse($templatePath) {

		$liveUpdateResponse = updateUtil::getLiveUpdateResponseArrayFromFile($templatePath);

		return '
			$newResponse = new liveUpdateResponse();
			$newResponse->initByHttpResponse("' . addslashes(updateUtil::getResponseString($liveUpdateResponse)) . '");
			print $newResponse->getOutput();
		';

	}


	function replaceExtensionInContent($content) {
		$content = str_replace('.php', $_SESSION['clientExtension'], $content);
		return $content;

	}


	/**
	 * reads given file and returns it already encoded
	 *
	 * @param string $filePath
	 * @return string
	 */
	function getFileContentEncoded($filePath, $replaceExtension=false) {
		$content = updateUtil::getFileContent($filePath);

		if ($replaceExtension) {
			$content = updateUtil::replaceExtensionInContent($content);

		}
		return updateUtil::encodeCode($content);

	}


	/**
	 * Reads filecontent in a string and returns it
	 *
	 * @param string $filePath
	 * @return string
	 */
	static function getFileContent($filePath) {

		$content = '';
		if (file_exists($filePath)) {
			if ($fh = fopen($filePath, 'rb')) {
				if (filesize($filePath)) {
					$content = fread($fh, filesize($filePath));

				}

			}

		}
		return $content;

	}

	/**
	 * @param string $dir
	 * @param array $files
	 */
	function getFilesOfDir($dir, & $files) {

		if (file_exists($dir)) {

			$dh = opendir($dir);
			while( $entry = readdir($dh) ){

				if( $entry != "" && $entry != "." && $entry != ".." ){

					$_entry = $dir . "/" . $entry;

		            if( !is_dir( $_entry ) ){
		                $files[] = $_entry;
		            }

					if(is_dir( $_entry )){
						updateUtilBase::getFilesOfDir($_entry, $files);
					}
				}
			}
			closedir($dh);
		}
	}


	/**
	 * returns string for the liveUpdate functions to overwrite functions, which
	 * could be broken.
	 *
	 * @return string
	 */
	function getOverwriteClassesCode() {

		$retString = '

			if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . \'updateClient/liveUpdateFunctionsServer.class.php\')) {
				require_once(LIVEUPDATE_DIR . \'updateClient/liveUpdateFunctionsServer.class.php\');
			}
			if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . \'updateClient/liveUpdateResponseServer.class.php\')) {
				require_once(LIVEUPDATE_DIR . \'updateClient/liveUpdateResponseServer.class.php\');
			}
			if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . \'updateClient/liveUpdateServer.class.php\')) {
				require_once(LIVEUPDATE_DIR . \'updateClient/liveUpdateServer.class.php\');
			}
			if (! @class_exists("liveUpdateFunctionsServer")) {
				class liveUpdateFunctionsServer extends liveUpdateFunctions {}
				class liveUpdateResponseServer extends liveUpdateResponse {}
			}

			$liveUpdateFnc = new liveUpdateFunctionsServer();
			$liveUpdateRsp = new liveUpdateResponseServer();

			function liveUpdateErrorHandler($errno, $errstr , $errfile , $errline, $errcontext) {
				liveUpdateFunctionsServer::liveUpdateErrorHandler($errno, $errstr , $errfile , $errline, $errcontext);
			}
			set_error_handler("liveUpdateErrorHandler");

		';
		return $retString;

	}

}
?>