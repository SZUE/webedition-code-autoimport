<?php
/**
 * $Id: ChooseLanguage.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class ChooseLanguage extends leStep{

	function execute(&$Template = ''){

		return $this->executeOnline($Template, "feature", "languagesForm");
	}

	function check(&$Template = ''){

		$_SESSION['le_defaultLanguage'] = $_REQUEST['le_defaultLanguage'];

		if(isset($_REQUEST['le_extraLanguages'])){
			$_SESSION['le_extraLanguages'] = $_REQUEST['le_extraLanguages'];
		} else {
			$_SESSION['le_extraLanguages'] = array();
		}

		return $this->executeOnline($Template, "feature", "registerLanguages");
	}

}
