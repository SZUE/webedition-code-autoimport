<?php
/**
 * $Id$
 */

class Summary extends leStep{

	function execute(&$Template = ''){

		$this->setHeadline($this->Language['headline']);

		$Summary = array(
			$this->Language['webEditionBase'] => array(
				$this->Language['webEditionURL'] => LE_URL . "/webEdition/",
				$this->Language['webEditionUsername'] => $_SESSION['le_login_user'],
				$this->Language['webEditionPassword'] => $_SESSION['le_login_pass'],
				$this->Language['webEditionVersion'] => $_SESSION['le_version'],
				$this->Language['webEditionSystemLanguage'] => $_SESSION['le_defaultLanguage'],
				$this->Language['webEditionAdditionalLanguages'] => (isset($_SESSION['le_extraLanguages']) && count($_SESSION['le_extraLanguages']) > 0) ? implode(", ", $_SESSION['le_extraLanguages']) : "-",
			),
			$this->Language['databaseConnection'] => array(
				$this->Language['databaseHost'] => $_SESSION['le_db_host'],
				$this->Language['databaseName'] => $_SESSION['le_db_database'],
				$this->Language['databaseUsername'] => $_SESSION['le_db_user'],
				$this->Language['databasePassword'] => $_SESSION['le_db_password'],
				$this->Language['databaseTablePrefix'] => (isset($_SESSION['le_db_prefix']) && $_SESSION['le_db_prefix'] != "" ? $_SESSION['le_db_prefix'] : "-"),
				$this->Language['databaseConnectionType'] => (isset($_SESSION['le_db_connect']) ? $_SESSION['le_db_connect'] : "connect"),
			)
		);


		$Summary[$this->Language['databaseConnection']][$this->Language['databaseCharset']] = ($_SESSION['we_db_charset'] ? $_SESSION['we_db_charset'] : $this->Language['databaseDefault']);
		$Summary[$this->Language['databaseConnection']][$this->Language['databaseCollation']] = ($_SESSION['we_db_collation'] ? $_SESSION['we_db_collation'] : $this->Language['databaseDefault']);


		$passwordFields = array(
			$this->Language['webEditionPassword'],
			$this->Language['databasePassword'],
		);

		if(!empty($_SESSION['le_modules'])){
			$i = 0;
			foreach($_SESSION['le_modules'] as $key => $value){
				$i++;
				$Summary[$this->Language['webEditionBase']][$this->Language['Module'] . " #" . ($i)] = $value;
			}
		}

		if(isset($_SESSION['le_proxy_use']) && $_SESSION['le_proxy_use']){
			$Summary[$this->Language['proxyServer']] = array(
				$this->Language['proxyHost'] => $_SESSION['le_proxy_host'],
				$this->Language['proxyPort'] => $_SESSION['le_proxy_port'],
				$this->Language['proxyUsername'] => $_SESSION['le_proxy_username'],
				$this->Language['proxyPassword'] => $_SESSION['le_proxy_password'],
			);
		}

		$Javascript = "";
		$showPasswordsJS = "";
		$hidePasswordsJS = "";
		$Content = "";
		$temp = 0;
		foreach($Summary as $Head => $Table){
			$Content .= '<p><strong>' . $Head . '</strong></p>' .
				'<table id="leSummary">';
			foreach($Table as $Key => $Value){
				$temp++;
				$Content .= "<tr><td class=\"left\">" . $Key . "</td><td class=\"middle\">:</td><td><input id=\"field_" . $temp . "\" name=\"field_" . $temp . "\" type=\"" . (in_array($Key, $passwordFields) ? "password" : "text") . "\" value=\"" . stripslashes(htmlspecialchars($Value)) . "\" class=\"right\" readonly=\"readonly\"/>" . (in_array($Key, $passwordFields) ? "<input type=\"text\" id=\"field_" . $temp . "_2\" name=\"field_" . $temp . "_2\" class=\"right\" value=\"" . stripslashes(htmlspecialchars($Value)) . "\" readonly=\"readonly\" style=\"display: none;\" />" : "") . "</td></tr>";

				if(in_array($Key, $passwordFields)){
					$showPasswordsJS .= "top.document.getElementById('field_" . $temp . "').style.display = 'none';top.document.getElementById('field_" . $temp . "_2').style.display = 'block';";
					$hidePasswordsJS .= "top.document.getElementById('field_" . $temp . "_2').style.display = 'none';top.document.getElementById('field_" . $temp . "').style.display = 'block';";
					$Javascript .= "top.document.getElementById('field_" . $temp . "').value = document.getElementById('field_" . $temp . "').value;";
				}
			}
			$Content .= "</table>";
		}


		$showPasswordText = $this->Language['showPasswords'];
		$hidePasswordText = $this->Language['hidePasswords'];
		$Content .= "<div style=\"float: right;\"><a id=\"pwdLink\" href=\"javascript:top.togglePasswords();\">" . $showPasswordText . "</a>&nbsp;</div>";

		$Javascript .= <<<EOF
var mode = 'password';
top.togglePasswords = function() {
	if(mode == 'password') {
		{$showPasswordsJS}
		mode = 'text';
		top.document.getElementById('pwdLink').innerHTML = '{$hidePasswordText}';
	} else {
		{$hidePasswordsJS}
		mode = 'password';
		top.document.getElementById('pwdLink').innerHTML = '{$showPasswordText}';
	}
}
EOF;

		$Template->addJavascript($Javascript);


		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

}
