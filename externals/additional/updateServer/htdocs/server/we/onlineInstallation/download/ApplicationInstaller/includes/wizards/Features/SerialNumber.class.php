<?php

	class SerialNumber extends leStep {

		function execute(&$Template) {

			return $this->executeOnline($Template = '', "feature", "serialForm");

		}


		function check(&$Template = '') {

			if(isset($_REQUEST["le_register"]) && $_REQUEST["le_register"] == 1) {

				$_SESSION['le_register'] = true;
				$_SESSION['le_serial'] = $_REQUEST["le_serial"];

				$_REQUEST['clientSerial'] = $_SESSION['le_serial'];

				return $this->executeOnline($Template, "feature", "checkSerial");

			} else {

				$_SESSION['le_register'] = false;
				$_SESSION['le_serial'] = "";

				$_REQUEST['clientSerial'] = $_SESSION['le_serial'];

				return $this->executeOnline($Template, "feature", "skipSerial");

			}

		}

	}

?>