<?php

	class InstallationDirectory extends leStep {

		function execute(&$Template = '') {

			// check if there is already a webedition installed on this server
			$Error = false;

			$extension = "";
			$path_parts = pathinfo(__FILE__);
			$extension = $path_parts["extension"];

			$docRoot = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] : $_SERVER['DOCUMENT_' . 'ROOT'];

			// webEdition already installed on this server
			if ( is_dir($docRoot . "/webEdition") ) {
				if (!file_exists($docRoot . "/webEdition/we/include/conf/we_conf.inc.$extension")) { // verify passwords
					// check if the webEdition directory is empty. In this case the installation may continue:
					$dir = @opendir($docRoot . "/webEdition");
					$files = array();
				    while (false !== ($file = readdir($dir))) {
				        if ($file != "." && $file != "..") {
				            $files[] = $file;
				        }
				    }				
					if(empty($files) && is_writable($docRoot . "/webEdition")) {
						$this->setHeadline($this->Language['headline']);
		
						$this->setContent($this->Language['content']);
		
						$this->AutoContinue = 10;
					} else {
						// error - directory is not empty, user must delete the files manually:
						$Template->addError($this->Language['installationForbidden']);
						$Error = true;
					}

				// webEdition is already installed - user must verify his right to install !!
				} else {
					if (isset($_SESSION["le_verifyWebEdition"]) && $_SESSION["le_verifyWebEdition"]) {
						$Content = $this->Language['installationVeryfied'];

					// user has to verify
					} else {

						// Username
						$Name = 'le_tmp_db_user';
						$Attribs = array(
							'size'	=> '40',
							'style'	=> 'width: 293px',
							'id'	=> $Name
						);
						$InputUser = leInput::get($Name, "", $Attribs);

						// Username
						$Name = 'le_tmp_db_pass';
						$Attribs = array(
							'size'	=> '40',
							'style'	=> 'width: 293px',
							'id'	=> $Name
						);
						$InputPassword = leInput::get($Name, "", $Attribs, "password");

						$Content = <<<EOF
{$this->Language['alreadyInstalled']}<br />
<br />

<input type="hidden" name="le_verifyWebEdition" value="1" />


<b><label for="le_tmp_db_user">{$this->Language['userNameDb']}:</label></b><br />
{$InputUser}<br />
<b><label for="le_tmp_db_pass">{$this->Language['passDb']}:</label></b><br />
{$InputPassword}<br />
EOF;

						$Template->addJavascript("top.leForm.setFocus('le_tmp_db_user');");

					}

				}

				$this->setHeadline($this->Language['headline']);

				$this->setContent($Content);

			// webEdition not installed on this server
			} else {
				$this->setHeadline($this->Language['headline']);

				$this->setContent($this->Language['content']);

				$this->AutoContinue = 10;

			}

			if($Error) {
				return LE_STEP_FATAL_ERROR;

			} else {
				return LE_STEP_NEXT;

			}

		}


		function check(&$Template = '') {

			$docRoot = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] : $_SERVER['DOCUMENT_' . 'ROOT'];

			if (isset($_REQUEST["le_verifyWebEdition"])) {

				$extension = "";
				$path_parts = pathinfo(__FILE__);
				$extension = $path_parts["extension"];

				$docRoot = isset($_SESSION["le_documentRoot"]) ? $_SESSION["le_documentRoot"] : $_SERVER['DOCUMENT_' . 'ROOT'];

				// check for extensions ...
				if (file_exists($docRoot . "/webEdition/we/include/conf/we_conf.inc.$extension")) { // verify passwords
					include($docRoot . "/webEdition/we/include/conf/we_conf.inc.$extension");
					if ($_REQUEST["le_tmp_db_user"] == DB_USER && $_REQUEST["le_tmp_db_pass"] == DB_PASSWORD ) {
						$_SESSION["le_verifyWebEdition"] = true;
						$_SESSION["le_installationDirectory"]  = $docRoot . "/";
						return true;

					} else {
						$_SESSION["le_verifyWebEdition"] = false;
						$Template->addError($this->Language['dataNotValid']);
						$Template->addJavascript("top.leForm.setFocus('le_tmp_db_user');");
						return false;

					}

				}

			} else {
				$_SESSION["le_installationDirectory"]  = $docRoot . "/";
				return true;

			}

		}

	}


?>