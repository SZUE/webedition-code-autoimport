<?php
class ProxyServer extends leStep {

	function execute(&$Template = '') {

		// UseProxy
		$Name = 'le_proxy_use';
		$Value = 1;
		$Attributes = array(
			'onClick'	=> 'enableProxy(this.checked);',
		);
		$Text = $this->Language["labelUseProxy"];
		$Checked = isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use'] ? true : false;
		if($Checked) {
			$Template->addJavascript("top.leForm.setFocus('le_proxy_host');");

		}

		$UseProxy = leCheckbox::get($Name, $Value, $Attributes, $Text, $Checked);

		// Hostname
		$Name = 'le_proxy_host';
		$Value = isset($_SESSION['le_proxy_host']) ? $_SESSION['le_proxy_host'] : "";
		$Attributes = array(
			'size'		=> '40',
			'style'		=> 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false) {
			$Attributes['disabled'] = 'disabled';
		}
		$Value = isset($_SESSION['le_proxy_host']) ? $_SESSION['le_proxy_host'] : "";
		$Hostname = leInput::get($Name, $Value, $Attributes);

		// Port
		$Name = 'le_proxy_port';
		$Value = isset($_SESSION['le_proxy_port']) ? $_SESSION['le_proxy_port'] : "";
		$Attributes = array(
			'size'	=> '40',
			'style'	=> 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false) {
			$Attributes['disabled'] = 'disabled';
		}
		$Port = leInput::get($Name, $Value, $Attributes);

		// Username
		$Name = 'le_proxy_username';
		$Value = isset($_SESSION['le_proxy_username']) ? $_SESSION['le_proxy_username'] : "";
		$Attributes = array(
			'size'	=> '40',
			'style'	=> 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false) {
			$Attributes['disabled'] = 'disabled';
		}
		$Username = leInput::get($Name, $Value, $Attributes);

		// Password
		$Name = 'le_proxy_password';
		$Value = isset($_SESSION['le_proxy_password']) ? $_SESSION['le_proxy_password'] : "";
		$Attributes = array(
			'size'	=> '40',
			'style'	=> 'width: 293px',
		);
		if(!isset($_SESSION['le_proxy_use']) || $_SESSION['le_proxy_use'] == false) {
			$Attributes['disabled'] = 'disabled';
		}
		$Password = leInput::get($Name, $Value, $Attributes, "password");

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


	function check(&$Template = '') {

		if(isset($_REQUEST["le_proxy_use"]) && $_REQUEST["le_proxy_use"] == 1) {
			
			$_SESSION['le_proxy_use'] = true;
			if(trim($_REQUEST["le_proxy_host"]) == "") {
				$Template->addJavascript("top.leForm.setFocus('le_proxy_host')");
				$Template->addError($this->Language["noProxyServer"]);
				return false;
				
			} else {
				$_SESSION['le_proxy_host'] = $_REQUEST["le_proxy_host"];
				$_SESSION['le_proxy_port'] = $_REQUEST["le_proxy_port"];
				$_SESSION['le_proxy_username'] = $_REQUEST["le_proxy_username"];
				$_SESSION['le_proxy_password'] = $_REQUEST["le_proxy_password"];
				
			}

		} else {
			$_SESSION['le_proxy_use'] = false;
			$_SESSION['le_proxy_host'] = "";
			$_SESSION['le_proxy_port'] = "";
			$_SESSION['le_proxy_username'] = "";
			$_SESSION['le_proxy_password'] = "";

		}

		return true;

	}

}