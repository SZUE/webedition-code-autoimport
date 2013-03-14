<?php

class downloadSnippet extends installer {

	var $LanguageIndex = "downloadSnippet";


	/**
	 * returns form to register webedition
	 *
	 * @return string
	 */
	function getGetOverviewResponse() {
		
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/snippet/overview.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * returns form to register webedition
	 *
	 * @return string
	 */
	function getGetRegisterImportResponse($SelectedImport) {
		
		if($_SESSION['clientImportType'] == "detail") {
			$AvailableImports = downloadSnippet::getDetailImports();
			
		} else {
			$AvailableImports = downloadSnippet::getMasterImports();
			
		}
		
		if(array_key_exists($SelectedImport, $AvailableImports)) {
			$_SESSION['clientSelectedImport'] = $SelectedImport;
			$ret = array (
				'Type' => 'executeOnline',
				'Code' => '<?php return true; ?>',
			);
				
		} else {
			$ret = array (
				'Type' => 'executeOnline',
				'Code' => '<?php $Template->addError($this->Language[\'no_import\']); return false; ?>',
			);
			
		}
		return updateUtil::getResponseString($ret);

	}


	function getGetFilesResponse() {

		$nextUrl = '?' . updateUtil::getCommonHrefParameters( $this->getNextUpdateDetail(), true );

		$message	=	'$this->Language[\'headline\']'
					.	'<p>' . sprintf($GLOBALS['lang']['installer']['downloadFilesTotal'], sizeof($_SESSION['clientChanges']['allChanges'])) . '</p>';

		$progress = $this->getInstallerProgressPercent();

		$retArray['Type'] = 'eval';
		$retArray['Code'] = '<?php

		' . updateUtil::getOverwriteClassesCode() . '
		$filesDir = LE_INSTALLER_TEMP_PATH;
		$liveUpdateFnc->deleteDir($filesDir);

		?>' . $this->getProceedNextCommandResponsePart($nextUrl, $progress);

		return updateUtil::getResponseString($retArray);

	}


	/**
	 * @return array
	 */
	function getFiles() {
		
		if($_SESSION['clientImportType'] == "detail") {
			$AvailableImports = downloadSnippet::getDetailImports();
			
		} else {
			$AvailableImports = downloadSnippet::getMasterImports();
		
		}
		
		$Import = $AvailableImports[$_SESSION['clientSelectedImport']];
		$Dath =  LIVEUPDATE_SERVER_DOWNLOAD_DIR;
		
		$clientPathPrefix = "/tmp/files/";
		
		$retFiles = array();
		
		foreach($Import['Files'] as $Type => $File) {
			
			$info = pathinfo($File);
			$retFiles["files"]['LIVEUPDATE_CLIENT_DOCUMENT_DIR . "' . $clientPathPrefix . $Type . "." . $info['extension'] . '"'] =  $File;
			$retFiles["allChanges"]['LIVEUPDATE_CLIENT_DOCUMENT_DIR . "' . $clientPathPrefix . $Type . "." . $info['extension'] . '"'] =  $File;
		
		}
		return $retFiles;

	}
	
	
	function getMasterImports() {
		
		// Add here the hostnames to use the test xml file
		$TestImportsFrom = array(
		);
		
		//$Language = "en"; --> english not available at the moment
		$Language = "de";
		$Charset = "ISO-8859-1";
		if(SHARED_LANGUAGE == "de") {
			$Language = "de";
			$Charset = "ISO-8859-1";
			
		} elseif(SHARED_LANGUAGE == "de_utf8") {
			$Language = "de";
			$Charset = "UTF-8";
		
		}
		
		if(in_array($_SESSION['clientDomain'], $TestImportsFrom)) {
			$_xml_file = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $Language . '/master/test.xml';
		} else {
			$_xml_file = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $Language . '/master/snippets.xml';
		}
		$Snippets = weSnippetCollection::initByXmlFile($_xml_file, $Charset);

		$MasterTemplates = $Snippets->getAsArray();

		return $MasterTemplates;
		
	}
	
	
	
	function getDetailImports() {
		
		// Add here the hostnames to use the test xml file
		$TestImportsFrom = array(
		);
		
		//$Language = "en"; --> english not available at the moment
		$Language = "de";
		$Charset = "ISO-8859-1";
		if(SHARED_LANGUAGE == "de") {
			$Language = "de";
			$Charset = "ISO-8859-1";
			
		} elseif(SHARED_LANGUAGE == "de_utf8") {
			$Language = "de";
			$Charset = "UTF-8";
		
		}
		
		if(in_array($_SESSION['clientDomain'], $TestImportsFrom)) {
			$_xml_file = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $Language . '/detail/test.xml';
		} else {
			$_xml_file = LIVEUPDATE_SERVER_DOWNLOAD_DIR . '/' . $Language . '/detail/snippets.xml';
		}
		
		$Snippets = weSnippetCollection::initByXmlFile($_xml_file, $Charset);

		$DetailTemplates = $Snippets->getAsArray();

		return $DetailTemplates;
		
	}
	

}

?>