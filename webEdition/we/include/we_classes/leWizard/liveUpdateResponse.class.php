<?php
/**
 * webEdition CMS
 *
 * LICENSETEXT_CMS
 *
 *
 * @category   webEdition
 * @package    webEdition_base
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.living-e.de/licence     LICENSETEXT_CMS  TODO insert license type and url
 */

class liveUpdateResponse {

	var $Type;
	var $Headline;
	var $Content;
	var $Header;
	var $Code;
	var $EncodedCode;
	var $Encoding = false;

	function liveUpdateResponse() {
	}

	function initByArray($respArray) {

		foreach ($respArray as $key => $value) {

			$this->$key = $value;
		}

		if ($this->Encoding && $this->EncodedCode) {
			$this->Code = base64_decode($this->EncodedCode);
		}
	}

	/**
	 * init the object with the response from the update-server
	 *
	 * @param string $response
	 * @return boolean
	 */
	function initByHttpResponse($response) {

		if ($respArr = liveUpdateResponse::responseToArray($response)) {

			$this->initByArray($respArr);
			return true;
		} else {
			return false;
		}
	}

	function isError() {

		if ($this->Type == 'state' && $this->State == 'error') {
			return true;
		}
		return false;
	}

	function getField($fieldname) {
		if (isset($this->$fieldname)) {
			return $this->$fieldname;
		}
		return '';
	}

	function responseToArray($response) {

		$respArray = unserialize($response);

		if (is_array($respArray)) {
			return $respArray;
		} else {
			return false;
		}
	}

	function getOutput() {

		switch ($this->Type) {

			case 'template':
				return liveUpdateTemplates::getHtml(
					$this->Headline,
					$this->Content,
					$this->Header
				);
				break;

			case 'eval':
				return eval('?>' . $this->Code);
				break;

			case 'state':
				return liveUpdateFrames::htmlStateMessage();
				break;

			default:
				return $this->Type . ' is not implemented yet';
				break;
		}
	}
}


?>