<?php

class Database extends leStep{

	function execute(&$Template = ''){

		$AllowTablePrefix = true;
		$AllowChooseOfConnectionType = true;

		// Connection Type
		if($AllowChooseOfConnectionType){
			$connect = (!isset($_SESSION['le_db_connect']) || $_SESSION['le_db_connect'] != "mysqli_pconnect" ? " checked=\"checked\"" : "");
			$pconnect = (isset($_SESSION['le_db_connect']) && $_SESSION['le_db_connect'] == "mysqli_pconnect" ? " checked=\"checked\"" : "");
//			$disabled = (!function_exists('mysqli_pconnect') ? " disabled=\"disabled\"" : "");
			$disabledText = ""; //(!function_exists('mysqli_pconnect') ? " " . $this->Language['pconnect_na'] : "");
			$connect_help = leLayout::getHelp($this->Language['connect_help']);
		}

		// Hostname
		$name = 'le_db_host';
		$value = isset($_SESSION['le_db_host']) ? $_SESSION['le_db_host'] : "";
		$attribs = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		$host_input = leInput::get($name, $value, $attribs);
		$host_help = leLayout::getHelp($this->Language["host_help"]);

		// Username
		$name = 'le_db_user';
		$value = isset($_SESSION['le_db_user']) ? $_SESSION['le_db_user'] : "";
		$attribs = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		$user_input = leInput::get($name, $value, $attribs);
		$user_help = leLayout::getHelp($this->Language["user_help"]);

		// Password
		$name = 'le_db_password';
		$value = isset($_SESSION['le_db_password']) ? $_SESSION['le_db_password'] : "";
		$attribs = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		$pass_input = leInput::get($name, $value, $attribs, "password");
		$pass_help = leLayout::getHelp($this->Language["pass_help"]);

		// Databasename
		$name = 'le_db_database';
		$value = isset($_SESSION['le_db_database']) ? $_SESSION['le_db_database'] : "";
		$attribs = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		$name_input = leInput::get($name, $value, $attribs);
		$name_help = leLayout::getHelp($this->Language["name_help"]);

		if($AllowTablePrefix){
			$name = 'le_db_prefix';
			$value = isset($_SESSION['le_db_prefix']) ? $_SESSION['le_db_prefix'] : "";
			$attribs = array(
				'size' => '40',
				'style' => 'width: 293px',
			);
			$prefix_input = leInput::get($name, $value, $attribs);
			$prefix_help = leLayout::getHelp($this->Language["prefix_help"]);
		}

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />
EOF;

		if($AllowChooseOfConnectionType){
			$Content .= <<<EOF
	<b>{$this->Language['connecttype']}:</b> {$connect_help}<br />
		<input id="connect" type="radio" name="le_db_connect" value="mysqli_connect"{$connect} /> <label for="connect">{$this->Language["connect"]}</label><br />
		<input id="pconnect" type="radio" name="le_db_connect" value="mysqli_pconnect"{$pconnect} /> <label for="pconnect">{$this->Language["pconnect"]}{$disabledText}</label><br />
		<br />
EOF;
		}


		$Content .= <<<EOF
	<b>{$this->Language['host']}:</b> {$host_help}<br />
	{$host_input}<br />

	<b>{$this->Language['user']}:</b> {$user_help}<br />
	{$user_input}<br />

	<b>{$this->Language['pass']}:</b> {$pass_help}<br />
	{$pass_input}<br />

	<b>{$this->Language['name']}:</b> {$name_help}<br />
	{$name_input}<br />
EOF;

		if($AllowTablePrefix){
			$Content .= <<<EOF
	<b>{$this->Language['prefix']}:</b> {$prefix_help}<br />
	{$prefix_input}<br />
EOF;
		}

		$Template->addJavascript("top.leForm.setFocus('le_db_host');");

		$this->setHeadline($this->Language['headline']);

		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

	function check(&$Template = ''){

		/*
		 * Check and save REQUEST-vars in SESSION:
		 * le_db_host
		 * le_db_user
		 * le_db_database
		 * le_db_password
		 * le_db_prefix (optional)
		 */

		// check le_db_host
		if(!isset($_REQUEST['le_db_host']) || trim($_REQUEST['le_db_host']) == ""){
			$Template->addError($this->Language['ErrorDBHost']);
			$Template->addJavascript("top.leForm.setFocus('le_db_host');");
			$Template->addJavascript("top.leContent.scrollDown();");
			$_SESSION['le_db_host'] = "";
			return false;
		}
		$_SESSION['le_db_host'] = trim($_REQUEST['le_db_host']);

		// check le_db_user
		if(!isset($_REQUEST['le_db_user']) || trim($_REQUEST['le_db_user']) == ""){
			$Template->addError($this->Language['ErrorDBUser']);
			$Template->addJavascript("top.leForm.setFocus('le_db_user');");
			$Template->addJavascript("top.leContent.scrollDown();");
			$_SESSION['le_db_user'] = "";
			return false;
		}
		$_SESSION['le_db_user'] = trim($_REQUEST['le_db_user']);

		// set le_db_password
		$_SESSION['le_db_password'] = $_REQUEST['le_db_password'];

		// Check le_db_database
		if(!isset($_REQUEST['le_db_database']) || trim($_REQUEST['le_db_database']) == ""){
			$Template->addError($this->Language['ErrorDBName']);
			$Template->addJavascript("top.leForm.setFocus('le_db_database');");
			$Template->addJavascript("top.leContent.scrollDown();");
			$_SESSION['le_db_database'] = "";
			return false;
		}
		$_SESSION['le_db_database'] = trim($_REQUEST['le_db_database']);

		// set le_db_prefix
		$_SESSION["le_db_prefix"] = isset($_REQUEST['le_db_prefix']) ? trim($_REQUEST['le_db_prefix']) : "";


		/*
		 * check data
		 * connection possible?
		 * create database if not exists
		 * try to create/alter/drop table
		 */
		if(isset($_REQUEST['le_db_connect']) && $_REQUEST['le_db_connect'] == "mysqli_pconnect"){
			$_SESSION["le_db_connect"] = $_REQUEST['le_db_connect'];
			$preHost = 'p:';
		} else {
			$_SESSION["le_db_connect"] = "mysqli_connect";
			$preHost = '';
		}
		$resource = mysqli_connect($preHost . $_SESSION['le_db_host'], $_SESSION['le_db_user'], $_SESSION['le_db_password'], $_SESSION["le_db_database"]);

		// connect to server
		if(mysqli_connect_errno()){
			$Template->addError($this->Language['ErrorDBConnect'] . ' Err: ' . mysqli_connect_error()/* . '<br/>'.
				  $preHost . $_SESSION['le_db_host'].' '. $_SESSION['le_db_user'].' '. $_SESSION['le_db_password'].' '. $_SESSION["le_db_database"]
				 */
			);
			$Template->addJavascript("top.leForm.setFocus('le_db_password');");
			$Template->addJavascript("top.leContent.scrollDown();");
			return false;
		}

		// check if database exists
		$_SESSION['le_db_exists'] = true;
		//$result = mysqli_query($resource, "USE " . $_SESSION["le_db_database"]);
		/* 	if(!$result && mysqli_errno($resource) == 1049){
		  $_SESSION['le_db_exists'] = false;
		  } */

		// check if database exists, create if possible
		$result = mysqli_query($resource, "CREATE DATABASE IF NOT EXISTS `" . $_SESSION["le_db_database"] . "`");
		if(!($result)){
			$Template->addJavascript("top.leForm.setFocus('le_db_user');");
			$Template->addJavascript("top.leContent.scrollDown();");
			$Template->addError(sprintf($this->Language["ErrorCreateDb"], $_SESSION["le_db_database"], mysqli_error($resource), mysqli_errno($resource)));
			mysqli_close($resource);
			return false;
		}

		mysqli_close($resource);
		return true;
	}

}
