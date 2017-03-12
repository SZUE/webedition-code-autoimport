<?php
/**
 * $Id$
 */

class ProxyServer extends leStep{

	function execute(&$Template = ''){

		// UseProxy
		$Attributes = array('onClick' => 'enableProxy(this.checked);',);
		$Checked = isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use'] ? true : false;
		if($Checked){
			$Template->addJavascript("top.leForm.setFocus('le_proxy_host');");
		}

		$UseProxy = leCheckbox::get('le_proxy_use', 1, $Attributes, $this->Language["labelUseProxy"], $Checked);

		// Hostname
		$Value = isset($_SESSION['le_proxy_host']) ? $_SESSION['le_proxy_host'] : "";
		$Attributes = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false){
			$Attributes['disabled'] = 'disabled';
		}
		$Value = isset($_SESSION['le_proxy_host']) ? $_SESSION['le_proxy_host'] : "";
		$Hostname = leInput::get('le_proxy_host', $Value, $Attributes);

		// Port
		$Value = isset($_SESSION['le_proxy_port']) ? $_SESSION['le_proxy_port'] : "";
		$Attributes = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false){
			$Attributes['disabled'] = 'disabled';
		}
		$Port = leInput::get('le_proxy_port', $Value, $Attributes);

		// Username
		$Value = isset($_SESSION['le_proxy_username']) ? $_SESSION['le_proxy_username'] : "";
		$Attributes = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false){
			$Attributes['disabled'] = 'disabled';
		}
		$Username = leInput::get('le_proxy_username', $Value, $Attributes);

		// Password
		$Value = isset($_SESSION['le_proxy_password']) ? $_SESSION['le_proxy_password'] : "";
		$Attributes = array(
			'size' => '40',
			'style' => 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false){
			$Attributes['disabled'] = 'disabled';
		}
		$Password = leInput::get('le_proxy_password', $Value, $Attributes, "password");

		$Checked = (isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use'] == true ? ' checked="checked"' : '');

		$this->setHeadline($this->Language['headline']);

		$Content = <<<EOF
{$this->Language['content']}<br />
<br />

{$UseProxy}

<b>{$this->Language['host']}:</b><br />
{$Hostname}<br />

<b>{$this->Language['port']}:</b><br />
{$Port}<br />

<b>{$this->Language['username']}:</b><br />
{$Username}<br />

<b>{$this->Language['password']}:</b><br />
{$Password}<br />
EOF;
		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

	function check(&$Template = ''){

		if(!empty($_REQUEST["le_proxy_use"])){
			$_SESSION['le_proxy_use'] = true;
			if(trim($_REQUEST["le_proxy_host"]) == ""){
				$Template->addJavascript("top.leForm.setFocus('le_proxy_host')");
				$Template->addError($this->Language["noProxyServer"]);
				return false;
			}
			$_SESSION['le_proxy_host'] = $_REQUEST["le_proxy_host"];
			$_SESSION['le_proxy_port'] = $_REQUEST["le_proxy_port"];
			$_SESSION['le_proxy_username'] = $_REQUEST["le_proxy_username"];
			$_SESSION['le_proxy_password'] = $_REQUEST["le_proxy_password"];
			return true;
		}
		$_SESSION['le_proxy_use'] = false;
		$_SESSION['le_proxy_host'] = "";
		$_SESSION['le_proxy_port'] = "";
		$_SESSION['le_proxy_username'] = "";
		$_SESSION['le_proxy_password'] = "";
		return true;
	}

}
