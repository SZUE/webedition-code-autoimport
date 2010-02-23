<?php
class SessionAndCookieTest extends leStep {

	var $AutoContinue = 10;


	function prepare() {
		$_SESSION["le_testSession"] = "test-session";
		@setcookie("le_testCookie", "test-cookie", time() + 3600);
		/*
		if(!headers_sent()) {
			setcookie("le_testCookie", "test-cookie", time() + 3600);
		}
		//$_COOKIE["le_testCookie"] = time() + 3600;
		error_log(print_r($_COOKIE),1);
		error_log(print_r($_SESSION),1);
		*/
	}


	function execute(&$Template) {

		$Success = true;

		$CookieState = true;
		$SessionState = true;

		//error_log(print_r($_COOKIE),1);
		//error_log(print_r($_SESSION),1);
		
		$SessionImage = leLayout::getRequirementStateImage();
		if ( !(isset($_SESSION["le_testSession"]) && $_SESSION["le_testSession"] == "test-session" ) ) {
			$Success = false;
			$SessionImage = leLayout::getRequirementStateImage(false);
			$Template->addError($this->Language['sessionFailed']);

		}

		$CookieImage = leLayout::getRequirementStateImage();
		
		if ( !(isset($_COOKIE["le_testCookie"]) && $_COOKIE["le_testCookie"] == "test-cookie") ) {
			$Success = false;
			$CookieImage = leLayout::getRequirementStateImage(false);
			$Template->addError($this->Language["cookieFailed"]);

		}

		$Content = <<<EOF
{$this->Language['content']}<br />
<table id="requirementsLog">
<tr>
	<td>&middot; {$this->Language['cookie']}</td>
	<td align="right">{$CookieImage}</td>
</tr>
<tr>
	<td>&middot; {$this->Language['session']}</td>
	<td align="right">{$SessionImage}</td>
</tr>
</table>
EOF;

		$this->setHeadline($this->Language['headline']);

		$this->setContent($Content);

		if ($Success) {
			return LE_STEP_NEXT;

		} else {
			$this->setContent($this->Language['failureMessage']);
			return LE_STEP_FATAL_ERROR;

		}

	}

}