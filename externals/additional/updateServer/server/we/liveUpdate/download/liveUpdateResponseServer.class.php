<?php

/**
 * $Id$
 */
class liveUpdateResponseServer extends liveUpdateResponse{

	protected function getErrorMsg($msg){
		return '<script><!--
	top.leWizardContent.appendErrorText("' . $msg . '");
	alert("' . strip_tags($msg) . '");
//-->
</script>';
	}

	protected function getProgress($msg){
		return '<script>
			top.frames.updatecontent.decreaseSpeed = false;
			top.frames.updatecontent.nextUrl = "' . $this->Next['nextUrl'] . '"+(top.frames.updatecontent.param?top.frames.updatecontent.param:"");
			top.frames.updatecontent.setProgressBar("' . $this->Next['progress'] . '");
			var msg="' . str_replace(["\r", "\n"], '', $msg . $this->Next['message']) . '\n";
			top.frames.updatecontent.appendMessageLog(msg);' .
			($this->Next['nextStep'] ? 'top.frames.updatecontent.finishLiInstallerStep("' . $this->Next['nextStep'][0] . '");
			top.frames.updatecontent.activateLiInstallerStep("' . $this->Next['nextStep'][1] . '");' : '') . '
			window.setTimeout("top.frames.updatecontent.proceedUrl();", 20);
		</script>';
	}

	protected function executePatches(){
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

	protected function saveFiles(){
		global $liveUpdateFnc;
		$files = $this->Files;
		$successFiles = array(); // successfully saved files

		foreach($files as $path => $content){

			$testPath = ltrim($path, '/');
			$testPath = strpos($testPath, 'tmp/files') === 0 ? '/' . ltrim(str_replace('tmp/files', '', $testPath), '/') : false;

			if($liveUpdateFnc->isPhpFile($path)){
				$content = $liveUpdateFnc->preparePhpCode($content);
			}

			if(!$liveUpdateFnc->filePutContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . $path, $content)){
				return str_replace('__PATH__', $path, $this->Error['path']);
			} else if($testPath && method_exists($liveUpdateFnc, "checkMakeFileWritable") && !$liveUpdateFnc->checkMakeFileWritable($testPath)){
				return str_replace('__PATH__', $testPath, $this->Error['write']);
			} else {
				$successFiles[] = $path;
			}
		}

		if($this->Parts){//multipart files to merge
			$content = '';
			for($i = 0; $i <= $this->Parts['Count']; $i++){
				$content .= $liveUpdateFnc->getFileContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . $this->Parts['Name'] . "part" . $i);
				$liveUpdateFnc->deleteFile(LIVEUPDATE_CLIENT_DOCUMENT_DIR . $this->Parts['Name'] . "part" . $i);
			}
			
			if($liveUpdateFnc->isPhpFile($this->Parts['Name'])){
				$content = $liveUpdateFnc->preparePhpCode($content);
			}

			if($liveUpdateFnc->filePutContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . $this->Parts['Name'], $content)){
				$successFiles[] = $path;
			}
		}


		$message = $this->SuccessText . "<br/>";
		/* foreach ($successFiles as $path) {

		  $text = basename($path);
		  $text = substr($text, -40);

		  $message .= "<div>&hellip;$text</div>";
		  } */
		return $this->getProgress($message);
	}

	function getOutput(){
		if(defined('LIVEUPDATE_DIR') && is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php')){
			require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateFunctionsServer.class.php');
		} elseif(!class_exists('liveUpdateFunctionsServer', false)){
			class_alias('liveUpdateFunctions', 'liveUpdateFunctionsServer');
		}

		if(defined('LIVEUPDATE_DIR') && is_readable(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php')){
			require_once(LIVEUPDATE_DIR . 'updateClient/liveUpdateServer.class.php');
		}

		$GLOBALS['liveUpdateFnc'] = new liveUpdateFunctionsServer();
		set_error_handler(array('liveUpdateFunctionsServer', 'liveUpdateErrorHandler'));

		switch($this->Type){
			case 'SaveFiles':
				return $this->saveFiles();
			case 'ExecutePatches':
				t_e($this);
				return $this->executePatches();
			default:
				return parent::getOutput();
		}
	}

}
