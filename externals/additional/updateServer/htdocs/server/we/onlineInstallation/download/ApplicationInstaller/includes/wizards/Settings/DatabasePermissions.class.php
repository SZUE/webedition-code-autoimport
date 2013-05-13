<?php

	class DatabasePermissions extends leStep {

		function execute(&$Template = '') {

			$Resource = $this->openConnection();

			$this->setHeadline($this->Language['headline']);

			// Check if database exists
			if(!$this->checkDatabaseExists($Resource)) {

				// Database does not exist
				$this->EnabledButtons = array('back');
				$Template->addError(sprintf($this->Language["AccessDenied"], $_SESSION['le_db_database']));
				return LE_STEP_ERROR;

			}

			$TableName = "{$_SESSION["le_db_prefix"]}_le_installer_test_table";

			$ReturnValue = LE_STEP_NEXT;
			
			// check if user could create table
			if(!$this->checkCreateTable($Resource, $TableName)) {

				// User can't create table
				$this->EnabledButtons = array('back');
				$Template->addError(sprintf($this->Language["errorNotCreateTable"]));
				
				$ReturnValue = LE_STEP_ERROR;

			}

			// check if user could alter table
			if(!$this->checkAlterTable($Resource, $TableName)) {

				// User can't alter table
				$this->EnabledButtons = array('back');
				$Template->addError(sprintf($this->Language["errorNotAlterTable"]));
				
				$ReturnValue = LE_STEP_ERROR;

			}

			// check if user could delete table
			if(!$this->checkDropTable($Resource, $TableName)) {

				// User can't delete table
				$this->EnabledButtons = array('back');
				$Template->addError(sprintf($this->Language["errorNotDropTable"]));
				
				$ReturnValue = LE_STEP_ERROR;

			}

			// Check if webEdition is already installed
			$Query1 = "SELECT * FROM {$_SESSION["le_db_prefix"]}tblPasswd";
			$Query2 = "SELECT * FROM {$_SESSION["le_db_prefix"]}tblPrefs";
			
			$Checked = true;
			if($ReturnValue == LE_STEP_ERROR) {

				$this->closeConnection($Resource);
				
				return $ReturnValue;

			// webEdition seems to be installed
			} else if (mysql_query($Query1, $Resource) || mysql_query($Query2, $Resource)) {

				$Name = 'continue';
				$Value = 1;
				$Attributes = array(
					"onClick"	=> "top.leForm.evalCheckBox(this, 'top.leButton.enable(\'next\');top.document.getElementById(\'le_db_collation\').disabled=false;', 'top.leButton.disable(\'next\');top.document.getElementById(\'le_db_collation\').disabled=true;');",
				);
				$Text = $this->Language["overWriteExistingDbCheckBox"];
				$Checked = false;
				$Overwrite = leCheckbox::get($Name, $Value, $Attributes, $Text, $Checked);

				$this->EnabledButtons = array("back");

				$Content = <<<EOF
{$this->Language['overWriteExistingDb']}<br />
<br />
{$Overwrite}
EOF;

			} else {

				$this->EnabledButtons = array("back", "next");

				$Content = $this->Language["content"];
				
				$_SESSION['le_dbserver_version'] = mysql_get_server_info($Resource);
				if ( (float) $_SESSION['le_dbserver_version'] < 5.0) {
					$Content .= sprintf($this->Language["dbserverwarning"],$_SESSION['le_dbserver_version']);
				}

			}
			$isoLanguages = false;
			if(!strpos($_SESSION['leInstallerLanguage'],"UTF-8")) {
				$isoLanguages = true;
				$v .= " (ISO 8859-1)";
			}
			$_SESSION['le_db_version'] = mysql_get_client_info();
			$_SESSION['le_dbserver_version'] = mysql_get_server_info($Resource);
			$_REQUEST["le_dbserver_version"] = $_SESSION['le_dbserver_version'];
			if(version_compare("4.1.0", $_SESSION['le_db_version']) < 1) {
				if(version_compare("5.0.0", $_SESSION['le_dbserver_version']) < 1) {
					$Query = "SHOW COLLATION WHERE Compiled = 'Yes' ";
				} else {
					$Query = "SHOW COLLATION  ";
				}
				$Result = mysql_query($Query, $Resource);
				
				if(!is_resource($Result)) {
					$Charsets = array();
				} else {
					$Charsets = array();
					while($Row = mysql_fetch_array($Result)) {
						$Charsets[$Row['Charset']][] = $Row['Collation'];
					}
				}
				
				$SelectedCollation = "";
				if (isset($_SESSION["le_db_collation"])) {
					$SelectedCollation = $_SESSION['le_db_collation'];
	
				} else {
					if($isoLanguages){
						$SelectedCollation = 'latin1_general_ci';
					} else {
						$SelectedCollation = 'utf8_general_ci';				
					}				
				}
				
				ksort($Charsets);
				print_r($_SESSION);
				$Select = "<select name=\"le_db_collation\" id=\"le_db_collation\" class=\"textselect\" style=\"width: 293px;\" onblur=\"this.className='textselect';\" onfocus=\"this.className='textselectselected'\"" . ($Checked?'':' disabled=\"disabled\"') . ">";
				$Select .= "<option value=\"-1\"".($SelectedCollation=="-1"? "selected=\"selected\"":"").">" . $this->Language['defaultCollation'] . "</option>";
				foreach($Charsets as $Charset => $Collations) {
					$Select .= "<optgroup label=\"" . $Charset . "\">";
					
					asort($Collations);
					foreach($Collations as $Collation) {
						$Select .= "<option value=\"" . $Collation . "\"".($SelectedCollation==$Collation? "selected=\"selected\"":"").">" . $Collation . "</option>";
					}
					$Select .= "</optgroup>";
				}
				$Select .= "</select>";
				$Content	.=	"<br />"
							.	"<b>" . $this->Language['Collation'] . "</b><br />"
							.	$Select;
				
			} else {
				$this->AutoContinue = 5;
				
			}

			$this->closeConnection($Resource);

			$this->setContent($Content);

			return LE_STEP_NEXT;

		}


		function check(&$Template = '') {

			$_SESSION["le_db_overwrite"] = false;
			if (isset($_REQUEST["continue"])) {
				$_SESSION["le_db_overwrite"] = true;

			}

			$_SESSION["le_db_charset"] = "";
			$_SESSION["le_db_collation"] = "";
			if (isset($_REQUEST["le_db_collation"]) && $_REQUEST["le_db_collation"] != "-1") {
				$tmp = explode("_", $_REQUEST['le_db_collation']);
				$_SESSION["le_db_charset"] = $tmp[0];
				$_SESSION["le_db_collation"] = $_REQUEST['le_db_collation'];
				$_SESSION["le_db_set_charset"] = $_SESSION["le_db_charset"];
				

				// Database was created with this installer, so change the collation
				if(isset($_SESSION['le_db_exists']) && !$_SESSION['le_db_exists']) {
					
					$Resource = $this->openConnection();
					
					$result = mysql_query("ALTER DATABASE " . $_SESSION['le_db_database'] . " DEFAULT CHARACTER SET " . $_SESSION["le_db_charset"] . " COLLATE " . $_SESSION["le_db_collation"], $Resource);
					if(!$result) {
						// Can't change the collation
					}
					
					$this->closeConnection($Resource);
					
				}
				
			}
			return true;

		}



		function openConnection() {

			if(isset($_SESSION['le_db_connect']) && $_SESSION['le_db_connect'] == "pconnect") {
				$Function = "mysql_pconnect";

			} else {
				$Function = "mysql_connect";

			}

			$Resource = @$Function($_SESSION['le_db_host'], $_SESSION['le_db_user'], $_SESSION['le_db_password']);

			return $Resource;

		}


		function checkDatabaseExists($Resource) {

			mysql_select_db($_SESSION['le_db_database'], $Resource);

			return !mysql_error($Resource);

		}


		function closeConnection($Resource) {

			mysql_close($Resource);

		}


		function checkCreateTable($Resource, &$TableName) {

			$TableExists = false;
			$Result = null;

			// try to create a non existing table
			do {
				$Query = "CREATE TABLE {$TableName} ( mytext varchar(255) NOT NULL, myText2 varchar(255) NOT NULL) ENGINE=MyISAM";
				$Result = mysql_query($Query, $Resource);

				// Table already exists
				if (!$Result && mysql_errno($Resource) == 1050) {
					$TableExists = true;
					$TableName .= "1";

				} else {
					$TableExists = false;

				}

			} while ($TableExists);

			return $Result;

		}


		function checkAlterTable($Resource, $TableName) {

			$Query = "ALTER TABLE {$TableName} ADD myTest VARCHAR( 255 ) NOT NULL;";

			return mysql_query($Query, $Resource);

		}


		function checkDropTable($Resource, $TableName) {

			$Query = "DROP TABLE {$TableName}";

			return mysql_query($Query, $Resource);

		}

	}


?>