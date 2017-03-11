<?php

//version6300
//code aus 6300
class liveUpdateResponseServer extends liveUpdateResponse{

	function executePatches(){
		global $liveUpdateFnc;
		if(method_exists($liveUpdateFnc, "weUpdaterDoUpdate")){
			$redo = $liveUpdateFnc::weUpdaterDoUpdate((empty($_REQUEST["progress"]) ? "" : $_REQUEST["progress"]["what"]), (empty($_REQUEST["progress"]) ? array() : $_REQUEST["progress"]));
		}
		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/patches/");

		$success = true;
		$message = "";

		foreach($allFiles as $file){
			$message .= basename($file) . "<br />";
			$success = $liveUpdateFnc->executePatch($file);
			if(!$success){
				$errorFile .= " " . basename($file);
			}
			unlink($file);
		}

		if($success){
			$message = "";
			if($allFiles){
				$message = "<div>" . $this->RepeatText . '</div>';
			}

			if(is_array($redo)){
				$message .= "<div>Update " . $redo["text"] . "</div>";
				unset($redo["text"]);
				return "<script>top.frames.updatecontent.param=\"&" . http_build_query(array("progress" => $redo)) . "\";</script>" .
						$this->Repeat;
			}

			$delDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/includes/";
			if(is_file($delDir . "del.files") && method_exists($liveUpdateFnc, "removeObsoleteFiles")){
				if(is_file($delDir . "deleted.files")){
					unlink($delDir . "deleted.files");
				}
				$liveUpdateFnc->removeObsoleteFiles($delDir);
			}
			if(method_exists($liveUpdateFnc, "removeDirOnlineInstaller")){
				$liveUpdateFnc->removeDirOnlineInstaller();
			}

			return $this->Next;
		}
		return $this->Error;
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
			case 'ExecutePatches':
				t_e($this);
				return $this->executePatches();
			default:
				return parent::getOutput();
		}
	}

}
