<?php

class update extends updateBase{

	function installLogStart(){
		$GLOBALS['DB_WE']->query('INSERT INTO ' . INSTALLLOG_TABLE . ' (date) VALUES (NOW())');
		$_SESSION['db_log_id'] = $GLOBALS['DB_WE']->getInsertId();
		$setvalues = "";
		$setvalues = "installerVersion = '" . str_replace('.', '', $_SESSION['le_installer_version']) . "'";
		if(isset($_SESSION['clientPhpVersion'])){
			$setvalues .= ", clientPhpVersion = '" . $_SESSION['clientPhpVersion'] . "'";
		}
		if(isset($_SESSION['clientPhpExtensions'])){
			$setvalues .= ", clientPhpExtensions = '" . $_SESSION['clientPhpExtensions'] . "'";
		}
		if(isset($_SESSION['clientPcreVersion'])){
			$setvalues .= ", clientPcreVersion = '" . $_SESSION['clientPcreVersion'] . "'";
		}
		if(isset($_SESSION['clientMySQLVersion'])){
			$setvalues .= ", clientMySqlVersion = '" . $_SESSION['clientMySQLVersion'] . "'";
		}
		if(isset($_SESSION['clientServerSoftware'])){
			$setvalues .= ", clientServerSoftware = '" . $_SESSION['clientServerSoftware'] . "'";
		}
		if(isset($_SESSION['clientEncoding'])){
			$setvalues .= ", clientEncoding = '" . $_SESSION['clientEncoding'] . "'";
		}
		if(isset($_SESSION['clientSyslng'])){
			$setvalues .= ", clientSysLng = '" . $_SESSION['clientSyslng'] . "'";
		}
		if(isset($_SESSION['clientExtension'])){
			$setvalues .= ", clientExtension = '" . $_SESSION['clientExtension'] . "'";
		}
		if(isset($_SESSION['clientDomain'])){
			$setvalues .= ", clientDomain = '" . base64_encode($_SESSION['clientDomain']) . "'";
		}
		if(isset($_SESSION['testUpdate'])){
			$setvalues .= ", testUpdate = '" . $_SESSION['testUpdate'] . "'";
		}
		if(isset($_SESSION['MatchingVersions'])){
			$matVer = array_keys($_SESSION['MatchingVersions']);
			$version = max($matVer);

			$svnrevision = self::getSubVersion($version);
			//$versionname = self::getVersionName($version);//imi
			$versiontype = self::getVersionType($version);
			$versionbranch = self::getOnlyVersionBranch($version);

			$setvalues .= ", newestVersion = '" . $version . "', newestVersionStatus = '" . $versiontype . "', newestSvnRevision = '" . $svnrevision . "', newestVersionBranch = '" . $versionbranch . "', success = '1' ";
		}
		if(isset($_SESSION['clientTargetVersionNumber'])){
			$setvalues .= ", installedVersion = '" . $_SESSION['clientTargetVersionNumber'] . "'";
		}
		//if(isset($_SESSION['clientTargetVersionName']) ) {$setvalues .= ", installedVersionName = '".$_SESSION['clientTargetVersionName']."'"; }//imi
		if(isset($_SESSION['clientTargetVersionType'])){
			$setvalues .= ", installedVersionStatus = '" . $_SESSION['clientTargetVersionType'] . "'";
		}
		if(isset($_SESSION['clientTargetSubVersionNumber'])){
			$setvalues .= ", installedSvnRevision = '" . $_SESSION['clientTargetSubVersionNumber'] . "'";
		}
		if(isset($_SESSION['clientTargetVersionBranch'])){
			$setvalues .= ", installedVersionBranch = '" . $_SESSION['clientTargetVersionBranch'] . "'";
		}
		if(isset($_SESSION['clientDesiredLanguages'])){
			$setvalues .= ", installedLanguages = '" . implode(',', $_SESSION['clientDesiredLanguages']) . "'";
		}
		$query = "UPDATE " . INSTALLLOG_TABLE . " SET " . $setvalues . " WHERE id = '" . $_SESSION['db_log_id'] . "' ;";

		$GLOBALS['DB_WE']->query($query);
	}

}
