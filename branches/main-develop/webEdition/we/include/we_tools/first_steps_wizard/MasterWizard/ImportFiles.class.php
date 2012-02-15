<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class ImportFiles extends leWizardStepBase{

	var $EnabledButtons = array(
		'back', 'next', 'reload'
	);
	var $ProgressBarVisible = true;

	function execute(&$Template){


		$wizard = new we_wizard_import();

		$Template->UseOnlineInstallerTemplate = false;
		$Output = $wizard->getWizCmd('first_steps_wizard');

		$Template->addJavascript(
			"top.document.getElementById('leWizardHeadline').innerHTML = '" . $this->Language['headline'] . "';");
		$Template->addJavascript(
			"top.document.getElementById('leWizardContent').innerHTML = '<p>" . $this->Language['content'] . "</p>';");
		$Template->addJavascript(
			"top.document.getElementById('leWizardDescription').innerHTML = '<p>" . $this->Language['description'] . "</p>';");

		$Javascript = "script type=\"text/javascript\">" . "top.document.getElementById('leWizardHeadline').innerHTML = '" . $this->Language['headline'] . "';" . "top.document.getElementById('leWizardContent').innerHTML = '<p>" . $this->Language['content'] . "</p>';" . "top.document.getElementById('leWizardDescription').innerHTML = '<p>" . $this->Language['description'] . "</p>';" . "top.leWizardForm.setInputField('leWizard', '" . $GLOBALS['WizardCollection']->NextStep->getWizardName() . "');" . "top.leWizardForm.setInputField('leStep', '" . $GLOBALS['WizardCollection']->NextStep->getName() . "');" . "</script>";
		$Template->Output = preg_replace("</head>", $Javascript . "</head", $Output);

		return LE_WIZARDSTEP_NEXT;
	}

}

?>