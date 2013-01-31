<?php

class leOnlineInstaller {

	/**
	 * @var array
	 */
	var $Wizards = array();

	/**
	 * @var array
	 */
	var $WizardNames = array();

	/**
	 * @var array
	 */
	var $WizardStepNames = array();

	/**
	 * @var le_OnlineInstaller_Wizard
	 */
	var $CurrentWizard = null;

	/**
	 * @var le_OnlineInstaller_WizardStep
	 */
	var $CurrentStep   = null;

	/**
	 * @var le_OnlineInstaller_WizardStep
	 */
	var $BackStep   = null;

	/**
	 * @var le_OnlineInstaller_WizardStep
	 */
	var $NextStep   = null;

	/**
	 * @var le_OnlineInstaller_StepTemplate
	 */
	var $StepTemplate = null;


	//function leOnlineInstaller() {
		//$this->__construct();

	//}

	function __construct() {
		$this->initLanguage();
	}

	function initLanguage() {

		$AvailableLanguages = $this->getAvailableLanguages();
		$DefaultLanguage = 'Deutsch_UTF-8';

		if(isset($_REQUEST['leInstallerLanguage']) && in_array($_REQUEST['leInstallerLanguage'], $AvailableLanguages)){
			$_SESSION['leInstallerLanguage'] = $_REQUEST['leInstallerLanguage'];

		} elseif(isset($_SESSION['leInstallerLanguage']) && in_array($_SESSION['leInstallerLanguage'], $AvailableLanguages)){
			$_SESSION['leInstallerLanguage'] = $_SESSION['leInstallerLanguage'];

		} else{
			$_SESSION['leInstallerLanguage'] = $this->getLanguageFromBrowser($AvailableLanguages, $DefaultLanguage);

		}
		
		if(substr($_SESSION['leInstallerLanguage'],-6) == "_UTF-8"){
			$_SESSION['leInstallerCharset'] = "UTF-8";
		} else {
			$_SESSION['leInstallerCharset'] = "ISO-8859-1";
		}

		// Load language files
		$LanguageOnlineInstaller = array();
		if(file_exists(LE_ONLINE_INSTALLER_PATH . "/includes/language/" . $_SESSION['leInstallerLanguage'] . ".inc.php")) {
			require(LE_ONLINE_INSTALLER_PATH . "/includes/language/" . $_SESSION['leInstallerLanguage'] . ".inc.php");
			$LanguageOnlineInstaller = $lang;
		}

		$LanguageApplicationInstaller = array();
		if(file_exists(LE_APPLICATION_INSTALLER_PATH . "/includes/language/" . $_SESSION['leInstallerLanguage'] . ".inc.php")) {
			require(LE_APPLICATION_INSTALLER_PATH . "/includes/language/" . $_SESSION['leInstallerLanguage'] . ".inc.php");
			$LanguageApplicationInstaller = $lang;
		}
		$GLOBALS['lang'] = array_merge($LanguageOnlineInstaller, $LanguageApplicationInstaller);

	}


	function getAvailableLanguages() {

		$AvailableLanguages = array();

		if(file_exists(LE_ONLINE_INSTALLER_PATH . "/includes/language") && is_dir(LE_ONLINE_INSTALLER_PATH . "/includes/language")){
			$_handle = opendir(LE_ONLINE_INSTALLER_PATH . "/includes/language");

			while(false !== ($_readdir = readdir($_handle)) ){
				if($_readdir != '.' && $_readdir != '..') {
					$_path = LE_ONLINE_INSTALLER_PATH . '/includes/language/'. $_readdir;
					if (is_file($_path)) {
						array_push($AvailableLanguages, preg_replace('/\.inc\.php$/', '', $_readdir));

					}

				}

			}
			closedir($_handle);

		}
		return $AvailableLanguages;
	}


	function getLanguageFromBrowser($AllowedLanguages, $DefaultLanguage, $LanguageVariable = null, $StrictMode = true) {

		// use $_SERVER['HTTP_ACCEPT_LANGUAGE'] if no Language Varviable is given
		if($LanguageVariable === null){
			$LanguageVariable = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		}

		// are there some information inside
		if(empty($LanguageVariable)){
			// No? => return default language
			return $DefaultLanguage;
		}

		// Split Header
		$AcceptedLanguages = preg_split('/,\s*/', $LanguageVariable);

		// set defaults
		$CurrentLanguage = $DefaultLanguage;
		$CurrentQuality = 0;

		foreach($AcceptedLanguages as $AcceptedLanguage){
			// get all information about the language
			$matches = array();
			$res = preg_match ('/^([a-z]{1,8}(?:-[a-z]{1,8})*)(?:;\s*q=(0(?:\.[0-9]{1,3})?|1(?:\.0{1,3})?))?$/i', $AcceptedLanguage, $matches);

			// the sytnax was valid
			if(!$res){
				// no, then ignore
				continue;
			}

			// get the langugae code
			$LanguageCode = explode ('-', $matches[1]);

			// is there a qaulity
			if(isset($matches[2])){
				// use quality
				$LanguageQuality = (float)$matches[2];
			} else{
				// Compatibility mode: quality 1.0
				$LanguageQuality = 1.0;
			}

			// until Language Code is empty
			while(count ($LanguageCode)){
				if(in_array (strtolower (join ('-', $LanguageCode)), $AllowedLanguages)){
					if($LanguageQuality > $CurrentQuality){
						// diese Sprache verwenden
						$CurrentLanguage = strtolower (join ('-', $LanguageCode));
						$CurrentQuality = $LanguageQuality;
						break;
					}
				}
				if($StrictMode){
					break;
				}
				array_pop ($LanguageCode);
			}
		}

		// die gefundene Sprache zurückgeben
		return $CurrentLanguage;
	}


	/**
	 * returns index (position) of wizard by name
	 *
	 * @param string $name
	 * @return integer
	 */
	function getWizardIndexByName($name) {
		for($i=0; $i<sizeof($this->Wizards); $i++){
			if($this->Wizards[$i]->Name == $name){
				return $i;
			}
		}
		return null;
	}


	/**
	 * @param string $name
	 * @return le_OnlineInstaller_Wizard
	 */
	function getWizardByName($name) {
		return $this->Wizards[$this->getWizardIndexByName($name)];
	}


	function initialize() {

		unset($leInstallerWizards);
		if(file_exists(LE_ONLINE_INSTALLER_PATH . "/includes/wizards/wizards.inc.php")){
			require(LE_ONLINE_INSTALLER_PATH . "/includes/wizards/wizards.inc.php");

			for($i = 0; $i<sizeof($leInstallerWizards); $i++){
				$temp = new leWizard($leInstallerWizards[$i], LE_ONLINE_INSTALLER_WIZARD);

				// array with all steps
				foreach($temp->WizardSteps as $Step){
					$this->WizardStepNames[][$leInstallerWizards[$i]] = $Step->Name;
				}
				$this->Wizards[] = $temp;
			}
		}

		unset($leInstallerWizards);
		if(isset($_REQUEST["leWizard"]) && file_exists(LE_APPLICATION_INSTALLER_PATH . "/includes/wizards/wizards.inc.php")){

			require(LE_APPLICATION_INSTALLER_PATH . "/includes/wizards/wizards.inc.php");

			for($i = 0; $i<sizeof($leInstallerWizards); $i++){

				$temp = new leWizard($leInstallerWizards[$i]);

				// array with all steps
				foreach($temp->WizardSteps as $Step){
					$this->WizardStepNames[][$leInstallerWizards[$i]] = $Step->Name;
				}
				$this->Wizards[] = $temp;
			}

		}

		if(isset($_REQUEST["leWizard"])){

			if( is_int($index = $this->getWizardIndexByName($_REQUEST["leWizard"])) ){
				$this->CurrentWizard = & $this->Wizards[$index];
			}
		}

		if(!$this->CurrentWizard){
			$this->CurrentWizard = & $this->Wizards[0];
		}

		// now set a wizard as current
		if($this->CurrentWizard){

			$this->CurrentWizard->setCurrent();
			$this->CurrentStep = $this->CurrentWizard->CurrentStep;

			// init next Step
			$this->BackStep = $this->getLastWizardStep();

			// init last Step
			$this->NextStep = $this->getNextWizardStep();
		}
	}

	function getFirstStepUrl() {
		
		$leWizard = $this->Wizards[0]->Name;
		$leStep =  $this->Wizards[0]->WizardSteps[0]->Name;

		return LE_INSTALLER_ADAPTER_URL . "?leWizard=" . $leWizard. "&amp;leStep=" . $leStep;
	}


	/**
	 * @return le_OnlineInstaller_WizardStep
	 */
	function &getNextWizardStep() {

		$wizardStepInformation = $this->_getCurrentWizardStepInformation();
		$currentPosition = $wizardStepInformation["position"];

		$nextPosition = ($currentPosition + 1);

		if(isset($this->WizardStepNames[$nextPosition])){

			$nextStep = $this->WizardStepNames[$nextPosition];

			list($wizardName, $wizardStepName) = each($nextStep);

			$nextWizard = $this->getWizardByName($wizardName);
			$nextWizardStep = $nextWizard->getWizardStepByName($wizardStepName);
			return $nextWizardStep;
		}
		$null = null;
		return $null;
	}


	/**
	 * @return le_OnlineInstaller_WizardStep
	 */
	function &getLastWizardStep() {

		$wizardStepInformation = $this->_getCurrentWizardStepInformation();
		$currentPosition = $wizardStepInformation["position"];

		$lastPosition = ($currentPosition - 1);

		if(isset($this->WizardStepNames[$lastPosition])){

			$nextStep = $this->WizardStepNames[$lastPosition];

			list ($wizardName, $wizardStepName) = each($nextStep);

			$nextWizard = $this->getWizardByName($wizardName);
			$nextWizardStep = $nextWizard->getWizardStepByName($wizardStepName);

			return $nextWizardStep;
		}
		$null = null;
		return $null;
	}


	/**
	 * @access private
	 * @return array
	 */
	function _getCurrentWizardStepInformation() {

		$i=0;

		$current = null;

		foreach($this->WizardStepNames as $wizStep){
			foreach($wizStep as $wizard => $wizardStep){

				if($this->CurrentWizard->Name == "$wizard" && $this->CurrentStep->Name == "$wizardStep"){

					$current = array(
						"position" => $i,
						"wizard" => $wizard,
						"wizardStep" => $wizardStep
					);
					return $current;
				}
				$i++;
			}
		}
		return $current;
	}


	/**
	 * @return string
	 */
	function executeStep() {

		$Template = new leTemplate();

		if($this->BackStep && !isset($_REQUEST["backStep"]) && !$this->BackStep->check($Template)){


			$this->BackStep->CheckFailed = true;

			$this->CurrentWizard = & $this->BackStep->Wizard;
			$this->CurrentStep = clone ($this->BackStep);

			$this->CurrentStep->execute($Template);

			$this->BackStep = $this->getLastWizardStep();
			$this->NextStep = $this->getNextWizardStep();

		} else{ // excute current step

			switch($this->CurrentStep->execute($Template)){

				// all was fine, open next step
				case LE_STEP_NEXT:
					if ($this->NextStep) {
						$this->NextStep->prepare();

					}
					break;

				case LE_STEP_ITERATE:
					break;

				// error with step repeat it
				case LE_STEP_ERROR:
					$this->NextStep = $this->CurrentStep;
					break;

				// only back button is enabled
				case LE_STEP_FATAL_ERROR:
					$this->CurrentStep->EnabledButtons = array('back');
					break;
			}
		}

		$Template->setButtons($this->NextStep, $this->BackStep);

		print $Template->getOutput($this->CurrentStep);
	}
	
}
