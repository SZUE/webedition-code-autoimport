<?php
class leStep {

	/**
	 * The name of the step
	 *
	 * @var string
	 */
	var $Name = "";

	/**
	 * The wizard object of the step
	 *
	 * @var object
	 */
	var $Wizard = null;

	/**
	 * This array define all buttons which are enabled per default
	 *
	 * @var array
	 */
	var $EnabledButtons = array('back', 'next');

	/**
	 * Should the progress bar shown at this step
	 *
	 * @var boolean
	 */
	var $ProgressBarVisible = false;

	/**
	 * This step is a screen which could autocontinue (only information
	 * screen)
	 *
	 * @var integer
	 */
	var $AutoContinue = -1;

	/**
	 * This step is a iteration step and repeats until a break criteria is
	 * reached
	 *
	 * @var boolean
	 */
	var $IterationStep = false;

	/**
	 * Should this step be shown in the status bar / naviagtion
	 *
	 * @var boolean
	 */
	var $ShowInStatusBar = true;

	/**
	 * This step is a server controlled step
	 *
	 * @var boolean
	 */
	var $ServerControlled = true;

	/**
	 * Headline to display in template
	 *
	 * @var string
	 */
	var $Headline = "";

	/**
	 * Content to display in template
	 *
	 * @var string
	 */
	var $Content = "";

	/**
	 * Language
	 *
	 * @var array
	 */
	var $Language = array();

	/**
	 * Did the check failed
	 *
	 * @var boolean
	 */
	var $CheckFailed = false;

	/**
	 * @var liveUpdateResponse
	 */
	var $liveUpdateHttpResponse = null;


	/**
	 * PHP4 Constructor
	 *
	 */
	function leStep($Name, $WizardObj, $Language = array()) {
		$this->__construct($Name, $WizardObj, $Language);

	}


	/**
	 * PHP5 Constructor
	 *
	 */
	function __construct($Name, $WizardObj, $Language = array()) {
		$this->Name = $Name;
		$this->Wizard = $WizardObj;
		$this->Language = $Language;

	}


	/**
	 * set the headline
	 *
	 * @param string $Headline
	 */
	function setHeadline($Headline) {
		$this->Headline = $Headline;

	}


	/**
	 * set the content
	 *
	 * @param string $Content
	 */
	function setContent($Content) {
		$this->Content = $Content;

	}


	/**
	 * return the name of the staep
	 *
	 * @return string
	 */
	function getName() {
		return $this->Name;

	}


	/**
	 * return the name of the wizard
	 *
	 * @return string
	 */
	function getWizardName() {
		return $this->Wizard->getName();

	}


	/**
	 * get the url for this step
	 *
	 * @return string
	 */
	function getUrl() {

		$additional = "";
		if(isset($_REQUEST['debug'])) {
			$additional .= "&debug=" . $_REQUEST['debug'];

		}
		return LE_INSTALLER_ADAPTER_URL . "?leWizard=" . $this->getWizardName() . "&leStep=" . $this->Name . $additional;

	}


	/**
	 * Execute the preparation of the step, liek setting cookies for example
	 *
	 * @return booelan
	 */
	function prepare() {
		return true;

	}


	function execute(&$Template) {
		return LE_STEP_NEXT;

	}


	/**
	 * executes a step at the live update / online installation server
	 *
	 * @param leTemplate $Template
	 * @param string $UpdateCmd
	 * @param string $UpdateCmdDetail
	 * @return integer
	 */
	function executeOnline(&$Template, $UpdateCmd = "", $UpdateCmdDetail = "") {

		if($UpdateCmd != "") {
			$_REQUEST['update_cmd'] = $UpdateCmd;

		} else {
			$_REQUEST['update_cmd'] = $this->Wizard->Name;

		}

		if($UpdateCmdDetail != "") {
			$_REQUEST['detail'] = $UpdateCmdDetail;

		} else {
			$_REQUEST['detail'] = $this->Name;

		}

		$this->liveUpdateHttpResponse = $this->getLiveUpdateHttpResponse();

		if($this->liveUpdateHttpResponse) {

			if($this->liveUpdateHttpResponse->Type == "executeOnline") {

				$code = $this->liveUpdateHttpResponse->Code;
				$this->liveUpdateHttpResponse = null;

				return eval('?>' . $code);

			}

		}

		return LE_STEP_NEXT;

	}


	/**
	 * Do some validation
	 *
	 * @return boolean
	 */
	function check(&$Template) {
		return true;

	}

	/**
	 * @return liveUpdateResponse
	 */
	function getLiveUpdateHttpResponse() {

		global $LU_IgnoreRequestParameters, $LU_ParameterNames;

		$parameters = array();

		foreach ($LU_ParameterNames as $parameterName) {

			if (isset($_REQUEST[$parameterName])) {
				$parameters[$parameterName] = $_REQUEST[$parameterName];
			}
		}

		// add nextWizardStep to parameters.
		$parameters["nextLeWizard"] = $GLOBALS["OnlineInstaller"]->NextStep->getWizardName();
		$parameters["nextLeStep"] = $GLOBALS["OnlineInstaller"]->NextStep->getName();

		// add all other request parameters to the request
		$reqVars = array();
		foreach ($_REQUEST as $key => $value) {
			if (!isset($parameters[$key]) && !in_array($key, $LU_IgnoreRequestParameters)) {
				$reqVars[$key] = $value;
			}
		}

		$parameters['reqArray'] = base64_encode(serialize($reqVars));
		$response = liveUpdateHttp::getHttpResponse($GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateServer'], $GLOBALS['leApplicationList'][$_SESSION['leApplication']]['UpdateScript'], $parameters);
		$liveUpdateResponse = new liveUpdateResponse();

		$liveUpdateResponse->initByHttpResponse($response);

		return $liveUpdateResponse;
	}

}