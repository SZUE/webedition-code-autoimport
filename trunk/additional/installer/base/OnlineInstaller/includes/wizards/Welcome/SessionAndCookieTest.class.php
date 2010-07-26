<?php
class SessionAndCookieTest extends leStep {

	var $AutoContinue = 15;

	function ini_get_bool($val) {
		$bool = ini_get($val);
		if($val == "1") {
			return true;
		}
		if($val == "0") {
			return false;
		}
		switch (strtolower($bool)) {
			case '1':
			case 'on':
			case 'yes':
			case 'true':
				return true;
			default:
				return false;
		}
		return false;
	}

	function prepare() {
		$_SESSION["le_testSession"] = "test-session";
		@setcookie("le_testCookie", "test-cookie", time() + 3600);
		$_SESSION["le_testPHP"] = phpversion();
		
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
		
		
		$PHPImage = leLayout::getRequirementStateImage();
		if ( version_compare(PHP_VERSION, '5.2.4', '<' )) {
			$Success = false;
			$PHPImage = leLayout::getRequirementStateImage(false);
			$Template->addError(sprintf($this->Language['phpFailed'], PHP_VERSION));

		}
		$PHPText = sprintf($this->Language['php'], PHP_VERSION);
		
		
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
		
		$SafeModeImage = leLayout::getWarningStateImage();
		$SafeModeText = $this->Language['safe_mode_OK'];
		$SafeModeWarning = '';	
		if ( $this->ini_get_bool('safe_mode') ) {
			$SafeModeImage = leLayout::getWarningStateImage(false);
			$SafeModeText = $this->Language['safe_mode'];
			$SafeModeWarning = $this->Language['safe_mode_warning'];
		}
		
		$RegisterGlobalsImage = leLayout::getWarningStateImage();
		$RegisterGlobalsText = $this->Language['register_globals_OK'];
		$RegisterGlobalsWarning = '';	
		if ( $this->ini_get_bool('register_globals') ) {
			$RegisterGlobalsImage = leLayout::getWarningStateImage(false);
			$RegisterGlobalsText = $this->Language['register_globals'];
			$RegisterGlobalsWarning = $this->Language['register_globals_warning'];
		}
		
		$ShortOpenTagImage = leLayout::getWarningStateImage();
		$ShortOpenTagText = $this->Language['short_open_tag_OK'];
		$ShortOpenTagWarning = '';	
		if ( $this->ini_get_bool('short_open_tag') ) {
			$ShortOpenTagImage = leLayout::getWarningStateImage(false);
			$ShortOpenTagText = $this->Language['short_open_tag'];
			$ShortOpenTagWarning = $this->Language['short_open_tag_warning'];
		}
		
		$SuhosinImage = leLayout::getWarningStateImage();
		$SuhosinText = $this->Language['suhosin_OK'];
		$SuhosinWarning = '';	
		if ( in_array('suhosin',get_loaded_extensions() ) ){
			$SuhosinImage = leLayout::getWarningStateImage(false);
			$SuhosinText = $this->Language['suhosin'];
			$SuhosinWarning = $this->Language['suhosin_warning'];
		}
		

		$Content = <<<EOF
{$this->Language['content']}<br />
<table id="requirementsLog">
<tr>
	<td>&middot; {$PHPText}</td>
	<td align="right">{$PHPImage}</td>
</tr>
<tr>
	<td>&middot; {$this->Language['cookie']}</td>
	<td align="right">{$CookieImage}</td>
</tr>
<tr>
	<td>&middot; {$this->Language['session']}</td>
	<td align="right">{$SessionImage}</td>
</tr>
<tr>
	<td>&middot; {$SafeModeText}</td>
	<td align="right">{$SafeModeImage}</td>
</tr>
<tr>
	<td colspan="2">{$SafeModeWarning}</td>
</tr>
<tr>
	<td>&middot; {$RegisterGlobalsText}</td>
	<td align="right">{$RegisterGlobalsImage}</td>
</tr>
<tr>
	<td colspan="2">{$RegisterGlobalsWarning}</td>
</tr>
<tr>
	<td>&middot; {$ShortOpenTagText}</td>
	<td align="right">{$ShortOpenTagImage}</td>
</tr>
<tr>
	<td colspan="2">{$ShortOpenTagWarning}</td>
</tr>
<tr>
	<td>&middot; {$SuhosinText}</td>
	<td align="right">{$SuhosinImage}</td>
</tr>
<tr>
	<td colspan="2">{$SuhosinWarning}</td>
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