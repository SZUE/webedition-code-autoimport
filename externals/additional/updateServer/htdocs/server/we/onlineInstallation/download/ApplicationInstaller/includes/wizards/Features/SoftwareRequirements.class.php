<?php

	class SoftwareRequirements extends leStep {

		function execute(&$Template = '') {

			$SoftwareRequirementsFulfilled = true;
			$phpExtensionsDetectable = true;
			$phpVersionState = true;
			$mbstringAvailable = true;
			$gdlibAvailable = true;
			$exifAvailable = true;
			$pcreversionOK = true;
			$phpExtensionsOK = true;
			$sdkDbOK = true;
			
			$phpextensions = get_loaded_extensions();
			foreach ($phpextensions as &$extens){
				$extens= strtolower($extens);
			}
			$phpextensionsMissing = array();
			$phpextensionsMin = array('ctype','date','dom','filter','iconv','libxml','mysql','pcre','Reflection','session','SimpleXML','SPL','standard','tokenizer','xml','zlib');
			
			if (count($phpextensions)> 3) {
				foreach ($phpextensionsMin as $exten){
					if(!in_array(strtolower($exten),$phpextensions,true) ){$phpextensionsMissing[]=$exten;}
				}
				
				if ( in_array(strtolower('PDO'),$phpextensions) && in_array(strtolower('pdo_mysql'),$phpextensions) ){//später ODER mysqli
						
				} else {$sdkDbOK = false;}
			} else {
				$phpExtensionsDetectable = false;
			} 
			// check if mbstring functions are available:
			if(!is_callable("mb_get_info")) {
				$mbstringAvailable = false;
			}
			
			// check if gdlib functions are available:
			if(!is_callable("gd_info")) {
				$gdlibAvailable = false;
				$gdVersion = "";
			} else {
				// GD_VERSION is mor precise but only available in PHP 5.2.4 or newer
				if(defined("GD_VERSION")) {
					$gdVersion = GD_VERSION;
				} else {
					$gdInfo=gd_info();
					$gdVersion = $gdInfo["GD Version"];
				}
			}
			if(!is_callable("exif_imagetype")) {
				$exifAvailable = false;
			}
			
			// identify webEdition version that has to be installed
			if($_SESSION["le_version"] >= "6391") {
				$phpVersionMin="5.3.7";
			} else if($_SESSION["le_version"] >= "6000") {
				$phpVersionMin="5.2.4";
			} 
			
			if(!$this->checkPHPVersion($phpVersionMin)) {
				$phpVersionState = false;
				$SoftwareRequirementsFulfilled = false;

			}
			if(defined("PCRE_VERSION") && substr(PCRE_VERSION,0,1)<7) {
				$pcreversionOK = false;
			}
			
			if(!empty($phpextensionsMissing)){
				$phpExtensionsOK = false;
				$SoftwareRequirementsFulfilled = false;
			}
			
			$_SESSION["phpVersionState"] = $phpVersionState;
			
						$Content = "
{$this->Language['content']}<br />
<table id=\"requirementsLog\">
<tr>
	<td>&middot; {$this->Language['php_version']} (>= ".$phpVersionMin.")</td>
	<td>" . leLayout::getRequirementStateImage($phpVersionState) . "</td>
</tr>";
if ($phpExtensionsDetectable){
$Content .= "<tr>
	<td>&middot; {$this->Language['phpext']} </td>
	<td>" . leLayout::getRequirementStateImage($phpExtensionsOK) . "</td>
</tr>
<tr>
	<td>&middot; {$this->Language['sdk_db']}</td>
	<td>" . leLayout::getRequirementStateImage($sdkDbOK) . "</td>
</tr>";
} else {
$Content .= "<tr>
	<td>&middot; {$this->Language['softreq']} </td>
	<td>" . leLayout::getRequirementStateImage($phpExtensionsDetectable) . "</td>
</tr>";

}

$Content .="<tr>
	<td>&middot; {$this->Language['pcre']} ".PCRE_VERSION."</td>
	<td>" . leLayout::getRequirementStateImage($pcreversionOK) . "</td>
</tr>
<tr>
	<td>&middot; {$this->Language['mbstring']}</td>
	<td>" . leLayout::getRequirementStateImage($mbstringAvailable) . "</td>
</tr>
<tr>
	<td>&middot; {$this->Language['gdlib']} ".($gdlibAvailable ? "(".$this->Language['found'].": ".$gdVersion.")" : "")."</td>
	<td>" . leLayout::getRequirementStateImage($gdlibAvailable) . "</td>
</tr>
<tr>
	<td>&middot; {$this->Language['exif']}</td>
	<td>" . leLayout::getRequirementStateImage($exifAvailable) . "</td>
</tr>

</table>
";

			$this->setHeadline($this->Language['headline']);

			$this->setContent($Content);
			
			if(!$phpExtensionsOK) {
				$Template->addError($this->Language['phpextWarning'].implode(', ', $phpextensionsMissing) );
			}
			
			if(!$pcreversionOK) {
				$Template->addError($this->Language['pcreOLD']);
			}
			
			if(!$mbstringAvailable) {
				$Template->addError($this->Language['mbstringNotAvailable']);
			}

			if(!$gdlibAvailable) {
				$Template->addError($this->Language['gdlibNotAvailable']);
			}
			if(!$exifAvailable) {
				$Template->addError($this->Language['exifNotAvailable']);
			}
			if(!$sdkDbOK) {
				$Template->addError($this->Language['sdk_dbWarnung']);
			}
			if(!$phpExtensionsDetectable) {
				$Template->addError($this->Language['reqNotDetec']);
			}
			if(!$SoftwareRequirementsFulfilled) {
				$Template->addError($this->Language['error']);
				$Template->addJavascript("top.leButton.disable(\"next\");");
				return LE_STEP_ERROR;

			} else {
				return LE_STEP_NEXT;

			}

			return LE_STEP_NEXT;

		}


		function checkPHPVersion($NeededPHPVersion) {

			if(version_compare(phpversion(), $NeededPHPVersion) == -1) {
				return false;

			} else {
				return true;

			}
		}


		function checkIsWriteable($Path = '') {
			return is_writable($Path);

		}

	}

?>