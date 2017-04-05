<?php
/**
 * $Id: ChooseVersion.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class ChooseVersion extends leStep{

	function execute(&$Template = ''){

		return $this->executeOnline($Template, "feature", "versionForm");
	}

	function check(&$Template = ''){

		if(isset($_REQUEST['le_version'])){
			$_SESSION['le_version'] = $_REQUEST['le_version'];
			return $this->executeOnline($Template, "feature", "registerVersion");
		}

		return true;
	}

}
