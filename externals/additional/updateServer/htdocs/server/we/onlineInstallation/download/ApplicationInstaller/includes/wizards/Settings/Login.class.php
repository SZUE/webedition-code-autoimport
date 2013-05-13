<?php

	class Login extends leStep {

		function execute(&$Template = '') {

			// Hostname
			$Name = 'le_login_user';
			$Value = isset($_SESSION['le_login_user']) ? $_SESSION['le_login_user'] : "";
			$Attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 293px',
			);
			$UserInput = leInput::get($Name, $Value, $Attribs);
			$UserHelp = leLayout::getHelp($this->Language['user_help']);
			
			// Password
			$Name = 'le_login_pass';
			$Value = isset($_SESSION['le_login_pass']) ? $_SESSION['le_login_pass'] : "";
			$Attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 293px',
			);
			$PassInput = leInput::get($Name, $Value, $Attribs, "password");
			$PassHelp = leLayout::getHelp($this->Language["pass_help"]);

			// Confirm
			$Name = 'le_login_pass_confirm';
			$Value = isset($_SESSION['le_login_pass_confirm']) ? $_SESSION['le_login_pass_confirm'] : "";
			$Attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 293px',
			);
			$ConfirmInput = leInput::get($Name, $Value, $Attribs, "password");
			$ConfirmHelp = leLayout::getHelp($this->Language["confirm_help"]);


			$Content = <<<EOF
{$this->Language['content']}<br />
<br />

<b>{$this->Language['user']}:</b> {$UserHelp}<br />
{$UserInput}<br />

<b>{$this->Language['pass']}:</b> {$PassHelp}<br />
{$PassInput}<br />

<b>{$this->Language['confirm']}:</b> {$ConfirmHelp}<br />
{$ConfirmInput}<br />
EOF;

			if(!$this->CheckFailed) {
				$Template->addJavascript("top.leForm.setFocus('le_login_user');");

			}

			$this->setHeadline($this->Language['headline']);

			$this->setContent($Content);

			return LE_STEP_NEXT;

		}


		function check(&$Template = '') {

			if(		isset($_REQUEST['le_login_user'])
				&&	$_REQUEST['le_login_user'] != ""
				&&	!preg_match("/^[A-Za-z0-9.\-_]+$/i", $_REQUEST['le_login_user'])) {

				$Template->addError($this->Language['UsernameInvalid']);
				$Template->addJavascript("top.leForm.setFocus('le_login_user');");
				$_SESSION['le_login_user'] = "";
				return false;

			} else if(		isset($_REQUEST['le_login_user'])
						&&	strlen($_REQUEST['le_login_user']) < 2) {

				$Template->addError($this->Language["UsernameToShort"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_user');");
				$_SESSION['le_login_user'] = "";
				return false;

			} else if(		isset($_REQUEST['le_login_user'])
						&&	$_REQUEST['le_login_user'] != "") {

				$_SESSION['le_login_user'] = $_REQUEST['le_login_user'];

			} else {

				$Template->addError($this->Language["UsernameFailure"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_user');");
				$_SESSION['le_login_user'] = "";
				return false;

			}

			if(		isset($_REQUEST['le_login_pass'])
				&&	$_REQUEST['le_login_pass'] != ""
				&&	preg_match("/[ ]/i", $_REQUEST['le_login_pass'])) {

				$Template->addError($this->Language["PasswordInvalid"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_pass');");
				$_SESSION['le_login_pass'] = "";
				return false;

			} else if(		isset($_REQUEST['le_login_pass'])
						&&	strlen($_REQUEST['le_login_pass']) < 4) {

				$Template->addError($this->Language["PasswordToShort"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_pass');");
				$_SESSION['le_login_pass'] = "";
				return false;

			} else if(		!isset($_REQUEST['le_login_pass'])
						||	$_REQUEST['le_login_pass'] == "") {

				$Template->addError($this->Language["PasswordFailure"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_pass');");
				$_SESSION['le_login_pass'] = "";
				return false;

			}

			if(		isset($_REQUEST['le_login_pass'])
				&&	isset($_REQUEST['le_login_pass_confirm'])
				&&	$_REQUEST['le_login_pass'] != ""
				&&	$_REQUEST["le_login_pass_confirm"] != ""
				&&	$_REQUEST["le_login_pass"] == $_REQUEST['le_login_pass_confirm']) {

				$_SESSION['le_login_pass'] = $_REQUEST['le_login_pass'];
				$_SESSION['le_login_pass_confirm'] = $_REQUEST['le_login_pass_confirm'];
				return true;

			} else {

				$Template->addError($this->Language["ConfirmFailure"]);
				$Template->addJavascript("top.leForm.setFocus('le_login_pass_confirm');");
				$_SESSION['le_login_pass'] = $_REQUEST['le_login_pass'];
				$_SESSION['le_login_pass_confirm'] = "";
				return false;

			}

		}

	}

?>