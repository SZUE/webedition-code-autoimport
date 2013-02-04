<?php
class leTemplate {

	/**
	 * occurd errors
	 *
	 * @var array
	 */
	var $_Errors = array();

	/**
	 * occurd errors as string for output
	 *
	 * @var string
	 */
	var $Errors = "";

	/**
	 * Javascripts to display
	 *
	 * @var array
	 */
	var $_Javascripts = array();

	/**
	 * Javascripts for output
	 *
	 * @var string
	 */
	var $Javascript = "";

	/**
	 * Output if Online Installer Template is not used
	 *
	 * @var string
	 */
	var $Output = "";

	/**
	 * Should the online installer tamplate be used
	 *
	 * @var boolean
	 */
	var $UseOnlineInstallerTemplate = true;


	function __construct() {
		//
	}

	function addError($ErrorMessage) {
		$this->_Errors[] = $ErrorMessage;
	}

	function addErrors($Errors = array()) {
		$this->_Errors = array_merge($this->Errors, $Errors);
	}


	function addJavascript($Javascript) {
		$this->_Javascripts[] = $Javascript;
	}

	function setButtons(&$nextStep, &$backStep) {
		if($nextStep){
			$this->addJavascript('top.leForm.setInputField("leWizard", "' . $nextStep->getWizardName() . '");');
			$this->addJavascript('top.leForm.setInputField("leStep", "' . $nextStep->getName() . '");');

			if(isset($_REQUEST['liveUpdateSession']) && $_REQUEST['liveUpdateSession'] != ""){
				$this->addJavascript('top.leForm.setInputField("liveUpdateSession", "' . $_REQUEST['liveUpdateSession'] . '");');
			}
		}

		if($backStep){
			$this->addJavascript('top.backUrl = "' . $backStep->getUrl() . (isset($_REQUEST['liveUpdateSession']) && $_REQUEST['liveUpdateSession'] != "" ? "&liveUpdateSession=" . $_REQUEST['liveUpdateSession'] : "") . '&backStep=true";');
		}
	}

	function getProgressBarJs(&$CurrentStep) {
		// enable/disable the progress bar
		return 'top.leProgressBar.enable("leProgress", ' . ($CurrentStep->ProgressBarVisible ? "true" : "false") . ')';
	}

	function getButtonJs(&$CurrentStep) {

		// enable/disable buttons
		$ButtonNames = array(
			"next",
			"back",
			"reload"
		);

		$ReturnValue = "";

		foreach($ButtonNames as $Button){
			if(in_array($Button, $CurrentStep->EnabledButtons)){
				$ReturnValue .= 'top.leButton.enable("' . $Button . '");';
			} else{
				$ReturnValue .= 'top.leButton.disable("' . $Button . '");';
			}
		}

		return $ReturnValue;
	}

	function getOutput(&$CurrentStep) {
		if($CurrentStep->liveUpdateHttpResponse){
			$Output = "<script type=\"text/javascript\">"
					. $this->getButtonJs($CurrentStep)
					. $this->getProgressBarJs($CurrentStep)
					. "</script>"
					. $CurrentStep->liveUpdateHttpResponse->getOutput();
			return $Output;
		}

		if($this->UseOnlineInstallerTemplate){
			$this->addJavascript($this->getButtonJs($CurrentStep));
			$this->addJavascript($this->getProgressBarJs($CurrentStep));

			// update the status
			if($CurrentStep->ShowInStatusBar){
				$this->addJavascript('top.leStatus.update("leStatus", "' . $CurrentStep->getWizardName() . '", "' . $CurrentStep->getName() . '");');
			}

			if(sizeof($this->_Errors) > 0){
				for ($i = 0; $i < sizeof($this->_Errors); $i++) {
					$this->Errors .= "<h1 class=\"error\">{$this->_Errors[$i]}</h1>\n";
				}
			} else{
				if($CurrentStep->AutoContinue >= 0){
					$CurrentStep->Content .= "<br /><br /><div class=\"defaultfont\">" .  sprintf($GLOBALS["lang"]["Template"]["autocontinue"], "<span id=\"secondTimer\">" . $CurrentStep->AutoContinue . "</span>") . "</div>";
					$this->addJavascript("top.leForm.forward();");
				}
			}

			// replace content
			$this->addJavascript('top.leContent.replaceElement(document.getElementById("leContent"));');

			if(sizeof($this->_Javascripts) > 0){
				$this->_Javascripts = array_reverse($this->_Javascripts);
				$this->Javascript .= '<script type="text/javascript">';
				foreach($this->_Javascripts as $Javascript){
					$this->Javascript .= $Javascript . "\n";
				}
				$this->Javascript .= '</script>';
			}

			$Output = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset={$_SESSION['leInstallerCharset']}" />
	</head>
	<body>
		<div id="leContent">
			<h1>{$CurrentStep->Headline}</h1>
			{$this->Errors}
			<p>
				{$CurrentStep->Content}
			</p>
		</div>
		{$this->Javascript}
	</body>
</html>
EOF;

			return $Output;
		} else{
			return $this->Output;
		}
	}

}