<?php

	class SystemRequirements extends leStep {

		function execute(&$Template = '') {

			$SystemRequirementsFulfilled = true;

			$isWriteable = true;
			$mysqlAvailable = true;
			
			// check if the document root directory is writable:
			$Directory = isset($_SESSION['le_documentRoot']) ? $_SESSION['le_documentRoot'] : $_SERVER['DOCUMENT_' . 'ROOT'];
			if(!$this->checkIsWriteable($Directory)) {
				if(!$this->checkIsWriteable($Directory."/webEdition/")) {
					$isWriteable = false;
					$SystemRequirementsFulfilled = false;
				}
			}
			
			//check if the mysql functions are available: 
			if(!is_callable("mysql_connect")) {
				$mysqlAvailable = false;
				$SystemRequirementsFulfilled = false;
			}

			$Content = "
{$this->Language['content']}<br />
<table id=\"requirementsLog\">
<tr>
	<td>&middot; {$this->Language['is_writeable']}</td>
	<td>" . leLayout::getRequirementStateImage($isWriteable) . "</td>
</tr>
<tr>
	<td>&middot; {$this->Language['mysql']}</td>
	<td>" . leLayout::getRequirementStateImage($mysqlAvailable) . "</td>
</tr>
</table>
";

			$this->setHeadline($this->Language['headline']);

			$this->setContent($Content);

			if(!$SystemRequirementsFulfilled) {
				$Template->addError($this->Language['error']);
				$Template->addJavascript("top.leButton.disable(\"next\");");
				return LE_STEP_ERROR;

			} else {
				return LE_STEP_NEXT;

			}

			return LE_STEP_NEXT;

		}


		function checkPHPVersion($NeededPHPVersion) {

			if(version_compare(phpversion(), $NeededPHPVersion) == -1) {
				return false;

			} else {
				return true;

			}
		}


		function checkIsWriteable($Path) {
			return is_writable($Path);

		}

	}

?>