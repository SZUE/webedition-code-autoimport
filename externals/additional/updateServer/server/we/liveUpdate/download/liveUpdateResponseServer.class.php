<?php

/**
 * $Id$
 */
class liveUpdateResponseServer extends liveUpdateResponse{
	/*Make sure all code is as compatible as possible
	 * NOTE: everthing that was packed on server side is available here
	 */

	protected function getErrorMsg(){
		$msg = "<div class='errorDiv'>" .
			$this->Error['headline'] . '<br />' .
			$this->Error['message'] .
			($GLOBALS["liveUpdateError"]["errorString"] ? $this->Error['lang']['errorMessage'] . ': <code class=\'errorText\'>' . $GLOBALS["liveUpdateError"]["errorString"] . "</code><br />" .
			$this->Error['lang']['errorIn'] . ': <code class=\'errorText\'>' . $GLOBALS["liveUpdateError"]["errorFile"] . "</code><br />" .
			$this->Error['lang']['errorLine'] . ': <code class=\'errorText\'>' . $GLOBALS["liveUpdateError"]["errorLine"] . "</code>" : "") .
			"</div>";

		return '<script><!--
	top.leWizardContent.appendErrorText("' . $msg . '");
	alert("' . strip_tags($msg) . '");
//-->
</script>';
	}

	protected function getProgress($msg, array $urls){
		return '<script>
			top.frames.updatecontent.decreaseSpeed = false;
			top.frames.updatecontent.nextUrl = "' . $urls['nextUrl'] . '"+(top.frames.updatecontent.param?top.frames.updatecontent.param:"");
			top.frames.updatecontent.setProgressBar("' . $urls['progress'] . '");
			var msg="' . str_replace(["\r", "\n"], '', $msg . $urls['message']) . '\n";
			top.frames.updatecontent.appendMessageLog(msg);' .
			($urls['nextStep'] ? 'top.frames.updatecontent.finishLiInstallerStep("' . $urls['nextStep'][0] . '");
			top.frames.updatecontent.activateLiInstallerStep("' . $urls['nextStep'][1] . '");' : '') . '
			window.setTimeout("top.frames.updatecontent.proceedUrl();", 20);
		</script>';
	}

	protected function executePatches(){//FIXME must be changed!
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
				$message = "<div>" . $this->Message . '</div>';
			}

			if(is_array($redo)){
				$message .= "<div>Update " . $redo["text"] . "</div>";
				unset($redo["text"]);
				return "<script>top.frames.updatecontent.param=\"&" . http_build_query(array("progress" => $redo)) . "\";</script>" .
					$this->getProgress($message, $this->Repeat);
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

			return $this->getProgress($message, $this->Next);
		}

		return strtr($this->getErrorMsg(), array(
			'__HEAD__' => $errorFile,
			'__MSG__' => $GLOBALS["errorDetail"]
		));
	}

	protected function saveFiles(){
		global $liveUpdateFnc;
		$successFiles = array(); // successfully saved files

		foreach($this->Files as $path => $content){

			$testPath = ltrim($path, '/');
			$testPath = strpos($testPath, 'tmp/files') === 0 ? '/' . ltrim(str_replace('tmp/files', '', $testPath), '/') : false;

			if($liveUpdateFnc->isPhpFile($path)){
				$content = $liveUpdateFnc->preparePhpCode($content);
			}

			if(!$liveUpdateFnc->filePutContent(LIVEUPDATE_CLIENT_DOCUMENT_DIR . $path, $content)){
				return str_replace('__PATH__', $path, $this->Error['path']);
			}
			if($testPath && method_exists($liveUpdateFnc, "checkMakeFileWritable") && !$liveUpdateFnc->checkMakeFileWritable($testPath)){
				return str_replace('__PATH__', $testPath, $this->Error['write']);
			}
			$successFiles[] = $path;
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
		return $this->getProgress($message, $this->Next);
	}

	protected function dBUpdate(){
		global $liveUpdateFnc;
		$queryDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/queries/";

		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $queryDir);
		sort($allFiles);

		$message = "";

		for($i = $this->From; $i < count($allFiles) && $i < $this->To; $i++){
			// execute queries in each file
			if($liveUpdateFnc->executeQueriesInFiles($allFiles[$i])){
				$text = substr(basename($allFiles[$i]), -40);
				$message .= "<div>&hellip;$text</div>";
			} else {
				$msg = $liveUpdateFnc->getQueryLog();
				$fileName = basename($allFiles[$i]);

				if($msg["tableExists"]){
					$message .= "<div class='messageDiv'>" . $this->Lang['notice'] . '<br />' . $fileName . ': ' . $this->Lang['tableExists'] . '</div>';
					$liveUpdateFnc->insertQueryLogEntries("tableExists", $fileName . ": " . $this->Lang['tableExists'], 2, $this->ClientVersion);
				}

				if($msg["tableChanged"]){
					$message .= "<div class='messageDiv'>" . $this->Lang['notice'] . '<br />' . $fileName . ': ' . $this->Lang['tableChanged'] . '</div>';
					$liveUpdateFnc->insertQueryLogEntries("tableChanged", $fileName . ": " . $this->Lang['tableChanged'], 2, $this->ClientVersion);
				}

				if($msg["entryExists"]){
					$message .= "<div class='messageDiv'>" . $this->Lang['notice'] . '<br />' . $fileName . ': ' . $this->Lang['entryExists'] . '</div>';
					$liveUpdateFnc->insertQueryLogEntries("entryExists", $fileName . ': ' . $this->Lang['entryExists'], 2, $this->ClientVersion);
				}

				if($msg["error"]){
					$message .= "<div class='errorDiv'>" . $this->Lang['notice'] . '<br />' . $fileName . ': ' . $this->Lang['error'] . '</div>';
					$liveUpdateFnc->insertQueryLogEntries("error", $fileName . ": " . $this->Lang['error'], 1, $this->ClientVersion);
				}

				$liveUpdateFnc->clearQueryLog();
			}
		}
		if(count($allFiles) > $this->To){ // continue with DB steps
			return $this->getProgress($message, $this->Repeat);
		}// proceed to next step.
		return $this->getProgress($message, $this->Next);
	}

	protected function copyFiles(){
		global $liveUpdateFnc;
		$filesDir = LIVEUPDATE_CLIENT_DOCUMENT_DIR . "/tmp/files/";
		$preLength = strlen($filesDir);

		$success = true;


		$allFiles = array();
		$liveUpdateFnc->getFilesOfDir($allFiles, $filesDir);

		$donotcopy = array(
			LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_hook/custom_hooks/weCustomHook_delete.inc.php",
			LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_hook/custom_hooks/weCustomHook_publish.inc.php",
			LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_hook/custom_hooks/weCustomHook_save.inc.php",
			LIVEUPDATE_SOFTWARE_DIR . "/webEdition/we/include/we_hook/custom_hooks/weCustomHook_unpublish.inc.php",
			LIVEUPDATE_SOFTWARE_DIR . "/webEdition/apps/toolfactory/hook/custom_hooks/weCustomHook_toolfactory_save.inc.php"
		);

		foreach($allFiles as $file){
			//$text = substr(basename($allFiles[$i]), -40);
			//$message .= "<div>&hellip;$text</div>";

			$success = (in_array(LIVEUPDATE_SOFTWARE_DIR . substr($file, $preLength), $donotcopy) ?
				$liveUpdateFnc->deleteFile($file) :
				$liveUpdateFnc->moveFile($file, LIVEUPDATE_SOFTWARE_DIR . substr($file, $preLength)));
			if(!$success){
				return $this->getErrorMsg();
			}
		}

		//$message = "<div>" . $message . "</div>";
		$message = "<div>" . $this->Message . "</div>";

		return $this->getProgress($message, $this->Next);
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
			case 'DBUpdate':
				return $this->dBUpdate();
			case 'CopyFiles':
				return $this->copyFiles();
			case 'ExecutePatches':
				return $this->executePatches();
			default:
				return parent::getOutput();
		}
	}

}
