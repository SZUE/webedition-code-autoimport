<?php

	class ChooseModules extends leStep {

		function execute(&$Template = '') {
			
			if(!$this->CheckFailed) {
				//$Template->addJavascript("top.leEffect.resizeSmall(316);");

			}

			return $this->executeOnline($Template, "feature", "modulesForm");

		}


		function check(&$Template = '') {

			//$Template->addJavascript("top.leEffect.resizeWide(554);");

			$_SESSION['le_modules'] = isset($_REQUEST['le_modules']) ? $_REQUEST['le_modules'] : array();

			return $this->executeOnline($Template, "feature", "registerModules");

		}

	}

?>