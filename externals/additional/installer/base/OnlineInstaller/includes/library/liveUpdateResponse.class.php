<?php
/**
 * $Id: liveUpdateResponse.class.php 13539 2017-03-12 11:39:19Z mokraemer $
 */

class liveUpdateResponse{
	var $Type;
	var $Headline;
	var $Content;
	var $Header;
	var $Code;
	var $EncodedCode;
	var $Encoding = false;

	function initByArray($respArray){

		foreach($respArray as $key => $value){

			$this->$key = $value;
		}

		if($this->Encoding && $this->EncodedCode){
			$this->Code = base64_decode($this->EncodedCode);
		}
	}

	/**
	 * init the object with the response from the update-server
	 *
	 * @param string $response
	 * @return boolean
	 */
	function initByHttpResponse($response){

		if($respArr = liveUpdateResponse::responseToArray($response)){

			$this->initByArray($respArr);
			return true;
		} else {
			return false;
		}
	}

	function isError(){

		if($this->Type == 'state' && $this->State == 'error'){
			return true;
		}
		return false;
	}

	function getField($fieldname){
		if(isset($this->$fieldname)){
			return $this->$fieldname;
		}
		return '';
	}

	function responseToArray($response){

		$respArray = @unserialize(base64_decode($response));
		if(!is_array($respArray)){
			$respArray = @unserialize($response);
		}
		return (is_array($respArray) ? $respArray : false);
	}

	function getOutput(){

		if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . "updateClient/liveUpdateFunctionsServer.class.php")){
			require_once(LIVEUPDATE_DIR . "updateClient/liveUpdateFunctionsServer.class.php");
		} elseif(!class_exists("liveUpdateFunctionsServer", false)){
			class_alias("liveUpdateFunctions", "liveUpdateFunctionsServer");
		}

		if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . "updateClient/liveUpdateResponseServer.class.php")){
			require_once(LIVEUPDATE_DIR . "updateClient/liveUpdateResponseServer.class.php");
		} elseif(!class_exists("liveUpdateResponseServer", false)){
			class_alias("liveUpdateResponse", "liveUpdateResponseServer");
		}

		if(defined("LIVEUPDATE_DIR") && is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php')){
			require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php');
		}

		if(!function_exists("liveUpdateErrorHandler")){
			function liveUpdateErrorHandler($errno, $errstr, $errfile, $errline, $errcontext){
				liveUpdateFunctionsServer::liveUpdateErrorHandler($errno, $errstr, $errfile, $errline, $errcontext);
			}
			set_error_handler("liveUpdateErrorHandler");
		}

		switch($this->Type){
			case 'template':
				return liveUpdateTemplates::getHtml($this->Headline, $this->Content, $this->Header);
			case 'eval':
				return eval('?>' . $this->Code);
			case 'state':
				return liveUpdateFrames::htmlStateMessage();
			default:
				return $this->Type . ' is not implemented yet';
		}
	}

}
