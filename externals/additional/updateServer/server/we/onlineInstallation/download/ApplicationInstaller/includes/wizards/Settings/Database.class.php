<?php

class Database extends leStep{

	function execute(&$Template = ''){

		// Connection Type
		$connect = (!empty($_SESSION['le_db_connect']) && $_SESSION['le_db_connect'] == "mysqli_connect" ? " checked=\"checked\"" : "");
		$pconnect = (empty($_SESSION['le_db_connect']) || $_SESSION['le_db_connect'] != "mysqli_connect" ? " checked=\"checked\"" : "");
		$disabledText = "";
		$connect_help = leLayout::getHelp($this->Language['connect_help']);

		// Hostname
		$attribs = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		$host_input = leInput::get('le_db_host', (isset($_SESSION['le_db_host']) ? $_SESSION['le_db_host'] : ""), $attribs);
		$host_help = leLayout::getHelp($this->Language["host_help"]);

		// Username
		$user_input = leInput::get('le_db_user', (isset($_SESSION['le_db_user']) ? $_SESSION['le_db_user'] : ""), $attribs);
		$user_help = leLayout::getHelp($this->Language["user_help"]);

		// Password
		$pass_input = leInput::get('le_db_password', (isset($_SESSION['le_db_password']) ? $_SESSION['le_db_password'] : ""), $attribs, "password");
		$pass_help = leLayout::getHelp($this->Language["pass_help"]);

		// Databasename
		$name_input = leInput::get('le_db_database', (isset($_SESSION['le_db_database']) ? $_SESSION['le_db_database'] : ""), $attribs);
		$name_help = leLayout::getHelp($this->Language["name_help"]);

		$prefix_input = leInput::get('le_db_prefix', (isset($_SESSION['le_db_prefix']) ? $_SESSION['le_db_prefix'] : ""), $attribs);
		$prefix_help = leLayout::getHelp($this->Language["prefix_help"]);

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />
	<b>{$this->Language['connecttype']}:</b> {$connect_help}<br />
		<input id="pconnect" type="radio" name="le_db_connect" value="mysqli_pconnect"{$pconnect} /> <label for="pconnect">{$this->Language["pconnect"]}{$disabledText}</label><br />
		<input id="connect" type="radio" name="le_db_connect" value="mysqli_connect"{$connect} /> <label for="connect">{$this->Language["connect"]}</label><br />
		<br />
	<b>{$this->Language['host']}:</b> {$host_help}<br />
	{$host_input}<br />

	<b>{$this->Language['user']}:</b> {$user_help}<br />
	{$user_input}<br />

	<b>{$this->Language['pass']}:</b> {$pass_help}<br />
	{$pass_input}<br />

	<b>{$this->Language['name']}:</b> {$name_help}<br />
	{$name_input}<br />
	<b>{$this->Language['prefix']}:</b> {$prefix_help}<br />
	{$prefix_input}<br />
EOF;

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
		if(empty($_REQUEST['le_db_database']) || trim($_REQUEST['le_db_database']) == ""){
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
		if(empty($_REQUEST['le_db_connect']) || $_REQUEST['le_db_connect'] == "mysqli_pconnect"){
			$_SESSION["le_db_connect"] = "mysqli_pconnect";
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
		$_SESSION['we_db_exists'] = false;
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
