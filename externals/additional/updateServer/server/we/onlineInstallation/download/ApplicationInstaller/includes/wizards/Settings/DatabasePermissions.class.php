<?php
/**
 * $Id: DatabasePermissions.class.php 13540 2017-03-12 11:48:37Z mokraemer $
 */

class DatabasePermissions extends leStep{

	function execute(&$Template = ''){

		$Resource = $this->openConnection();

		$this->setHeadline($this->Language['headline']);

		// Check if database exists
		if(!$this->checkDatabaseExists($Resource)){

			// Database does not exist
			$this->EnabledButtons = array('back');
			$Template->addError(sprintf($this->Language['AccessDenied'], $_SESSION['le_db_database']));
			return LE_STEP_ERROR;
		}

		$TableName = "{$_SESSION["le_db_prefix"]}_le_installer_test_table";

		$ReturnValue = LE_STEP_NEXT;

		// check if user could create table
		if(!$this->checkCreateTable($Resource, $TableName)){

			// User can't create table
			$this->EnabledButtons = array('back');
			$Template->addError(sprintf($this->Language["errorNotCreateTable"]));

			$ReturnValue = LE_STEP_ERROR;
		}

		// check if user could alter table
		if(!$this->checkAlterTable($Resource, $TableName)){

			// User can't alter table
			$this->EnabledButtons = array('back');
			$Template->addError(sprintf($this->Language["errorNotAlterTable"]));

			$ReturnValue = LE_STEP_ERROR;
		}

		// check if user could delete table
		if(!$this->checkDropTable($Resource, $TableName)){

			// User can't delete table
			$this->EnabledButtons = array('back');
			$Template->addError(sprintf($this->Language["errorNotDropTable"]));

			$ReturnValue = LE_STEP_ERROR;
		}

		// Check if webEdition is already installed

		$Checked = true;
		if($ReturnValue == LE_STEP_ERROR){

			$this->closeConnection($Resource);

			return $ReturnValue;

			// webEdition seems to be installed
		} else if(mysqli_query($Resource, "SELECT * FROM {$_SESSION["le_db_prefix"]}tblPrefs LIMIT 1")){

			$Name = 'continue';
			$Value = 1;
			$Attributes = array(
				"onClick" => "top.leForm.evalCheckBox(this, 'top.leButton.enable(\'next\');top.document.getElementById(\'le_db_collation\').disabled=false;', 'top.leButton.disable(\'next\');top.document.getElementById(\'le_db_collation\').disabled=true;');",
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

			$_SESSION['le_dbserver_version'] = mysqli_get_server_info($Resource);
			if((float) $_SESSION['le_dbserver_version'] < 5.5){
				$Content .= sprintf($this->Language["dbserverwarning"], $_SESSION['le_dbserver_version']);
			}
		}
		$_SESSION['leInstallerLanguage'] = str_replace('_UTF-8', '', $_SESSION['leInstallerLanguage']);
		$_SESSION['le_db_version'] = mysqli_get_client_info();
		$_SESSION['le_dbserver_version'] = mysqli_get_server_info($Resource);
		$_REQUEST["le_dbserver_version"] = $_SESSION['le_dbserver_version'];

		$Result = mysqli_query($Resource, 'SHOW COLLATION WHERE Charset="utf8" OR Charset LIKE "latin%"');

		$Charsets = array();
		if(!mysqli_connect_errno()){
			$Charsets = array();
			while(($Row = mysqli_fetch_array($Result))){
				$Charsets[$Row['Charset']][] = $Row['Collation'];
			}
		}

		$SelectedCollation = (isset($_SESSION['we_db_collation']) ?
				$_SESSION['we_db_collation'] :
				'utf8_unicode_ci');

		ksort($Charsets);
		$Select = '<select name="le_db_collation" id="le_db_collation" class="textselect" style="width: 293px;" onblur="this.className=\'textselect\';" onfocus="this.className=\'textselectselected\'"' . ($Checked ? '' : ' disabled="disabled"') . ">";
		//$Select .= '<option value="-1"' . ($SelectedCollation == "-1" ? 'selected="selected"' : "") . ">" . $this->Language['defaultCollation'] . '</option>';
		foreach($Charsets as $Charset => $Collations){
			$Select .= '<optgroup label="' . $Charset . '">';

			asort($Collations);
			foreach($Collations as $Collation){
				$Select .= '<option value="' . $Collation . '"' . ($SelectedCollation == $Collation ? 'selected="selected"' : '') . '>' . $Collation . '</option>';
			}
			$Select .= '</optgroup>';
		}
		$Select .= '</select>';
		$Content .= '<br /><b>' . $this->Language['Collation'] . '</b><br />' . $Select;


		//$this->closeConnection($Resource);

		$this->setContent($Content);

		return LE_STEP_NEXT;
	}

	function check(&$Template = ''){

		$_SESSION["le_db_overwrite"] = (isset($_REQUEST["continue"]));

		if(isset($_REQUEST["le_db_collation"]) && $_REQUEST["le_db_collation"] != "-1"){
			$tmp = explode("_", $_REQUEST['le_db_collation']);
			$_SESSION["we_db_charset"] = $tmp[0];
			$_SESSION["we_db_collation"] = $_REQUEST['le_db_collation'];

			// Database was created with this installer, so change the collation
			//if(isset($_SESSION['we_db_exists']) && !$_SESSION['we_db_exists']){

			$Resource = $this->openConnection();

			$result = mysqli_query($Resource, 'ALTER DATABASE ' . $_SESSION['le_db_database'] . ' DEFAULT CHARACTER SET ' . $_SESSION['we_db_charset'] . ' COLLATE ' . $_SESSION['we_db_collation']);
			if(!$result){
				// Can't change the collation
			}

			$this->closeConnection($Resource);
			//}
		}
		return true;
	}

	function openConnection(){
		$preHost = (isset($_SESSION['le_db_connect']) && $_SESSION['le_db_connect'] == "mysqli_pconnect" ? 'p:' : '');

		return mysqli_connect($preHost . $_SESSION['le_db_host'], $_SESSION['le_db_user'], $_SESSION['le_db_password']);
	}

	function checkDatabaseExists($Resource){
		$result = mysqli_select_db($Resource, $_SESSION["le_db_database"]);
		return !mysqli_error($Resource);
	}

	function closeConnection($Resource){
		mysqli_close($Resource);
	}

	function checkCreateTable($Resource, &$TableName){
		$TableExists = false;
		$Result = null;

		// try to create a non existing table
		do{
			$Result = mysqli_query($Resource, "CREATE TABLE {$TableName} ( mytext varchar(255) NOT NULL, myText2 varchar(255) NOT NULL) ENGINE=MyISAM");

			// Table already exists
			if(!$Result && mysqli_errno($Resource) == 1050){
				$TableExists = true;
				$TableName .= "1";
			} else {
				$TableExists = false;
			}
		} while($TableExists);

		return $Result;
	}

	function checkAlterTable($Resource, $TableName){
		return mysqli_query($Resource, "ALTER TABLE {$TableName} ADD myTest VARCHAR( 255 ) NOT NULL;");
	}

	function checkDropTable($Resource, $TableName){
		return mysqli_query($Resource, "DROP TABLE {$TableName}");
	}

}
