<?php

//version6201
//code aus 6201
class liveUpdateResponseServer extends liveUpdateResponse{

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

		return parent::getOutput();
	}

}
