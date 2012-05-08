<?php
class leWizard {

	var $IsCurrent = false;
	var $Name = "";

	var $LastStep    = null;
	var $CurrentStep = null;
	var $NextStep    = null;

	var $WizardSteps = array();

	function leWizard($Name, $Type = LE_APPLICATION_INSTALLER_WIZARD) {
		$this->__construct($Name, $Type);

	}

	function __construct($Name, $Type = LE_APPLICATION_INSTALLER_WIZARD) {
		$this->Name = $Name;

		/*
		 * Initialise all steps of this wizard
		 */
		if ($Type == LE_ONLINE_INSTALLER_WIZARD) {
			$Path = LE_ONLINE_INSTALLER_PATH;

		} else {
			$Path = LE_APPLICATION_INSTALLER_PATH;

		}

		if (file_exists($Path . "/includes/wizards/" . $this->Name . "/steps.inc.php")) {

			// get names of steps in the wizard
			require_once($Path . "/includes/wizards/" . $this->Name . "/steps.inc.php");

			foreach ($leInstallerSteps as $Step) {

				if (file_exists($Path .  "/includes/wizards/" . $this->Name . "/" . $Step . ".class.php")) {
					require_once($Path .  "/includes/wizards/" . $this->Name . "/" . $Step . ".class.php");
					$Classname = $Step;

				} else {
					die("Cannot load class '" . $Step . "'!");

				}

				$Language = array(
					'title' => 'Please overwrite',
					'headline' => 'Please overwrite',
					'content' => 'Please overwrite',
				);
				if(array_key_exists($Step, $GLOBALS['lang']['Step'])) {
					$Language = array_merge($Language, $GLOBALS['lang']['Step'][$Step]);

				}
				$this->WizardSteps[] = new $Classname($Classname, $this, $Language);

			}
		}

	}

	/**
	 * sets this wizard current, calls function to determine current step
	 */
	function setCurrent() {
		$this->IsCurrent = true;
		$this->initialize();

	}

	/**
	 * @param string $name
	 * @return le_OnlineInstaller_WizardStep
	 */
	function getWizardStepIndexByName($Name) {
		for ($i = 0; $i < sizeof($this->WizardSteps); $i++) {
			if ($this->WizardSteps[$i]->Name == $Name) {
				return $i;

			}

		}
		return null;

	}

	/**
	 * @param string $name
	 * @return le_OnlineInstaller_WizardStep
	 */
	function getWizardStepByName($Name) {
		return $this->WizardSteps[$this->getWizardStepIndexByName($Name)];

	}

	/**
	 * selects current active step, regarding request variables
	 */
	function initialize() {

		// detect current wizard regarding request-var "leWizard"
		if (isset($_REQUEST["leStep"])) {
			if ( is_int($index = $this->getWizardStepIndexByName($_REQUEST["leStep"])) ) {
				$this->CurrentStep = & $this->WizardSteps[$index];

			}

		}

		if (!$this->CurrentStep) {
			$this->CurrentStep = & $this->WizardSteps[0];

		}

	}

	/**
	 * @return string
	 */
	function getName() {
		return $this->Name;

	}

}