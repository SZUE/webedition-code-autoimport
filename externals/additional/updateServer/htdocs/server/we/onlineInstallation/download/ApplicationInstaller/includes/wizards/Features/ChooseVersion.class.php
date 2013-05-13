<?php

	class ChooseVersion extends leStep {

		function execute(&$Template = '') {

			return $this->executeOnline($Template, "feature", "versionForm");

		}


		function check(&$Template = '') {

			if(isset($_REQUEST['le_version'])) {
				$_SESSION['le_version'] = $_REQUEST['le_version'];
				return $this->executeOnline($Template, "feature", "registerVersion");

			}

			return true;

		}

	}

?>