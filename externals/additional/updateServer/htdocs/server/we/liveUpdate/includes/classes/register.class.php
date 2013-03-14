<?php

class register extends registerBase {

	/**
	 * Register webEdition online and on the client
	 *
	 * @return array
	 */
	function getRegisterResponse($serial) {

		// start with inserting/updateing registration information on server
		$uid = register::generateUniqueId();
		$stockTableId = license::getStockTableIdBySerial($serial);
		$licensee = license::getLicensee($stockTableId);

		//	all modules, installed and licensed, must be saved in database and on local file of user.
		$reinstallModules = array();

		$domainId = license::checkDomain($_SESSION['clientDomain'], $stockTableId);

		if( $domainId ){

			// important !!
			// if there are missing modules - save information on client, only if modules are installed -> module installation

			$domainInformation = license::getRegisteredDomainInformationById($domainId);

			for ($i=0; $i<sizeof($domainInformation['registeredModules']); $i++) {

				if(!in_array( $domainInformation['registeredModules'][$i], $_SESSION['clientInstalledModules'])){

					$reinstallModules[] = $domainInformation['registeredModules'][$i];
				}
			}
		}

		// insert newsletter
		register::insertNewsletter($stockTableId);

		// register this webEdition on server!
		license::insertRegistration($uid, $stockTableId, $domainId);

		// now build code for the client to make fullversion
		// for fullversion we have to replace several file parts

		$we_conf = updateUtil::getReplaceCode('we_conf', array($licensee));
		$menu1 = updateUtil::getReplaceCode('menu1');
		$menu2 = updateUtil::getReplaceCode('menu2');
		$templateSaveCode = updateUtil::getReplaceCode('templateSaveCode');
		$webEdition = updateUtil::getReplaceCode('webEdition');
		$we_version = updateUtil::getReplaceCode('we_version', array($_SESSION['clientVersion'], $uid));

		if ( sizeof($reinstallModules) ) {


			$_SESSION['clientUid'] = $uid;
			$_SESSION['clientDesiredModules'] = $reinstallModules;

			$GLOBALS['updateServerTemplateData']['reinstallModules'] = $reinstallModules;
			$GLOBALS['updateServerTemplateData']['existingModules'] = modules::getExistingModules();

			$internalResponse = updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/reinstallModules.inc.php' );

		} else {

			$internalResponse = updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerSuccess.inc.php' );
		}

		$ret = array (
			'Type' => 'eval',
			'Code' => '
			<?php

			' . updateUtil::getOverwriteClassesCode() . '

			if (
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_conf['path'] . '", "' . updateUtil::encodeCode($we_conf['replace']) . '", "' . updateUtil::encodeCode($we_conf['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $menu1['path'] . '", "' . updateUtil::encodeCode($menu1['replace']) . '", "' . updateUtil::encodeCode($menu1['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $menu2['path'] . '", "' . updateUtil::encodeCode($menu2['replace']) . '", "' . updateUtil::encodeCode($menu2['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $templateSaveCode['path'] . '", "' . updateUtil::encodeCode($templateSaveCode['replace']) . '", "' . updateUtil::encodeCode($templateSaveCode['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $webEdition['path'] . '", "' . updateUtil::encodeCode($webEdition['replace']) . '", "' . updateUtil::encodeCode($webEdition['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_version['path'] . '", "' . updateUtil::encodeCode($we_version['replace']) . '", "' . updateUtil::encodeCode($we_version['needle']) . '")
				) {

				$GLOBALS["liveUpdateError"]["headline"] = "' . addslashes($GLOBALS['lang']['register']['registerErrorDetail']) . '";

				restore_error_handler();
				// führe response aus
				' . updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerError.inc.php' ) . '
				exit;

			} else {

				$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['register']['finished'] . '", "' . $_SESSION['clientVersion'] . '", 0);
				' . $internalResponse . '
			}
			?>'
		);

		return updateUtil::getResponseString($ret);
	}

	/**
	 * Enter description here...
	 *
	 * @param serial $serial
	 * @return string
	 */
	function getRepeatRegistrationResponse($serial) {

		// start with inserting/updateing registration information on server
		$uid = register::generateUniqueId();
		$stockTableId = license::getStockTableIdBySerial($serial);
		$licensee = license::getLicensee($stockTableId);

		//	all modules, installed and licensed, must be saved in database and on local file of user.
		$reinstallModules = array();

		$domainId = license::checkDomain($_SESSION['clientDomain'], $stockTableId);

		if( $domainId ){

			// important !!
			// if there are missing modules - save information on client, only if modules are installed -> module installation

			$domainInformation = license::getRegisteredDomainInformationById($domainId);

			for ($i=0; $i<sizeof($domainInformation['registeredModules']); $i++) {

				if(!in_array( $domainInformation['registeredModules'][$i], $_SESSION['clientInstalledModules'])){

					$reinstallModules[] = $domainInformation['registeredModules'][$i];
				}
			}
		}

		// insert newsletter
		register::insertNewsletter($stockTableId);

		// register this webEdition on server!
		license::insertRegistration($uid, $stockTableId, $domainId);

		// now build code for the client to make fullversion
		// for fullversion we have to replace several file parts

		$we_conf = updateUtil::getReplaceCode('we_conf', array($licensee));
		$we_version = updateUtil::getReplaceCode('we_version', array($_SESSION['clientVersion'], $uid));

		if ( sizeof($reinstallModules) ) {


			$_SESSION['clientUid'] = $uid;
			$_SESSION['clientDesiredModules'] = $reinstallModules;

			$GLOBALS['updateServerTemplateData']['reinstallModules'] = $reinstallModules;
			$GLOBALS['updateServerTemplateData']['existingModules'] = modules::getExistingModules();

			$internalResponse = updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/reinstallModules.inc.php' );

		} else {

			$internalResponse = updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerSuccess.inc.php' );
		}

		$ret = array (
			'Type' => 'eval',
			'Code' => '<?php

			' . updateUtil::getOverwriteClassesCode() . '

			if (
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_conf['path'] . '", "' . updateUtil::encodeCode($we_conf['replace']) . '", "' . updateUtil::encodeCode($we_conf['needle']) . '") ||
				!$liveUpdateFnc->replaceCode( LIVEUPDATE_SOFTWARE_DIR . "' . $we_version['path'] . '", "' . updateUtil::encodeCode($we_version['replace']) . '", "' . updateUtil::encodeCode($we_version['needle']) . '")
				) {

				$GLOBALS["liveUpdateError"]["headline"] = "' . addslashes($GLOBALS['lang']['register']['registerErrorDetail']) . '";

				restore_error_handler();
				// führe response aus

				' . updateUtil::getInternalResponse( LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerError.inc.php' ) . '
				exit;

			} else {

				$liveUpdateFnc->insertUpdateLogEntry("' . $GLOBALS['luSystemLanguage']['repeatRegistration']['finished'] . '", "' . $_SESSION['clientVersion'] . '", 0);

				' . $internalResponse . '
			}
			?>'
		);

		return updateUtil::getResponseString($ret);
	}

	function getNewsletterHtml() {

		global $clientRequestVars;

		$salutations = array($GLOBALS['lang']['register']['salutationMr'], $GLOBALS['lang']['register']['salutationMrs']);
		$salutationSelect = '<select name="salutation" id="salutation">';
		foreach ($salutations as $salutation) {
			$salutationSelect .= '<option' . (isset($clientRequestVars['salutation']) && $clientRequestVars['salutation'] == $salutation ? ' selected="selected"' : '') . '>' . $salutation . '</option>';
		}
		$salutationSelect .= '</select>';

		$languages = array('Deutsch', 'English');
		$languageSelect = '<select name="language" id="language">';
		foreach ($languages as $lng) {
			$languageSelect .= '<option' . (isset($clientRequestVars['language']) && $clientRequestVars['language'] == $lng ? ' selected="selected"' : '') . '>' . $lng . '</option>';
		}
		$languageSelect .= '</select>';

		return '
		<div class="messageDiv">

			<table cellpadding="0" cellspacing="10" class="defaultfont">
			<tr>
				<td colspan="3">\' . we_forms::checkbox("yes", true, "newsletter", "' . $GLOBALS['lang']['register']['informAboutUpdates'] . '", false, "defaultfont", "clickNewsletter();") . \'</td>
			</tr>
			<tr>
				<td><label for="email">' . $GLOBALS['lang']['register']['email'] . '</label></td>
				<td width="10px"></td>
				<td> <input class="wetextinput" size="24" type="text" name="email" id="email"' . (isset($clientRequestVars['email']) ? " value=\"" . $clientRequestVars['email'] . "\"" : '' ) . ' /></td>
			</tr>
			<tr>
				<td><label for="salutation">' . $GLOBALS['lang']['register']['salutation'] . '</label></td>
				<td width="10px"></td>
				<td>' . $salutationSelect . '</td>
			</tr>
			<tr>
				<td><label for="title">' . $GLOBALS['lang']['register']['titel'] . '</label></td>
				<td width="10px"></td>
				<td> <input class="wetextinput" size="24" type="text" name="title" id="title"' . (isset($clientRequestVars['title']) ? " value=\"" . $clientRequestVars['title'] . "\"" : '' ) . ' /></td>
			</tr>
			<tr>
				<td><label for="forename">' . $GLOBALS['lang']['register']['forename'] . '</label></td>
				<td width="10px"></td>
				<td> <input class="wetextinput" size="24" type="text" name="forename" id="forename"' . (isset($clientRequestVars['forename']) ? " value=\"" . $clientRequestVars['forename'] . "\"" : '' ) . ' /></td>
			</tr>
			<tr>
				<td><label for="surname">' . $GLOBALS['lang']['register']['surname'] . '</label></td>
				<td width="10px"></td>
				<td> <input class="wetextinput" size="24" type="text" name="surname" id="surname"' . (isset($clientRequestVars['surname']) ? " value=\"" . $clientRequestVars['surname'] . "\"" : '' ) . ' /></td>
			</tr>
			<tr>
				<td><label for="language">' . $GLOBALS['lang']['register']['language'] . '</label></td>
				<td width="10px"></td>
				<td>' . $languageSelect . '</td>
			</tr>
			</table>
			<script type="text/Javascript">

				function clickNewsletter(){

					var fields = new Array("email","salutation","title","forename","surname","language");

					val = !document.forms[0].newsletter.checked;

					for(i=0;i<fields.length;i++){
						document.forms[0][fields[i]].disabled=val;
					}
				}

				function submitRegistration(){

					if(document.forms[0].newsletter.checked){

						pattern = "^[a-zA-Z0-9-äöü_\.]+@[a-zA-Z0-9äöü\.-]+.[a-zA-Z0-9]{1,4}$";

						if(document.forms[0].email.value == "" || !document.forms[0].email.value.match(pattern) ){
							alert("' . $GLOBALS['lang']['register']['enterValidEmail'] . '");
							document.forms[0].email.focus();
							return false;
						}
					}
					document.we_form.submit();
				}
			</script>
		</div>
		';
	}

	function insertNewsletter($stockTableId) {

		//global $DB_Register, $clientRequestVars;
		global $clientRequestVars;

		if(isset($clientRequestVars["newsletter"]) && $clientRequestVars["newsletter"] == "yes" && $clientRequestVars["email"]) {

			//where does the User come from?
			//	for prices of newsletter
			/*
			$query = "
					SELECT *
					FROM tblCustomer
					WHERE weID=" . $stockTableId . "
			";
			
			$res =& $DB_Register->query($query);

			if ($row = $res->fetchRow()) {

				$queryFields[] = "FK_tblCustomer";
				$queryVals[]   = $row['id'];

				if($row['isus']){

					$queryFields[] = "currency";
					$queryVals[]   = "us";
				} else {
					$queryFields[] = "currency";
					$queryVals[]   = "eu";
				}

				$fields = array("email", "salutation", "title", "forename", "surname", "language");
				foreach($fields AS $name){
					if(isset($clientRequestVars[$name]) && $clientRequestVars[$name] != ""){
						$queryFields[] = $name;
						$queryVals[] = $clientRequestVars[$name];
					}
				}

				//	now build the query ...
				$insertQuery = 'INSERT INTO newsletter (' . implode(",", $queryFields) . ')
								VALUES ("' . implode('","', str_replace('"', '\\"', $queryVals)) . '")
				';

				$res =& $DB_Register->query($insertQuery);
			}
			*/
		}
	}


	/**
	 * returns form to register a demoversion of webedition
	 *
	 * @return string
	 */
	function getRegisterFormResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerForm.inc.php');
		return updateUtil::getResponseString($ret);

	}


	function getRepeatRegistrationFormResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/repeatRegistrationForm.inc.php');
		return updateUtil::getResponseString($ret);

	}


	/**
	 * returns form to register a demoversion of webedition
	 *
	 * @return string
	 */
	function getRegisterFormErrorResponse($serialstate) {

		$GLOBALS['updateServerTemplateData']['licenceError'] = $GLOBALS['lang']['license']['undefinedError'] . ': <code>' . $serialstate . '</code>';

		if (file_exists(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php')) {

			$GLOBALS['updateServerTemplateData']['licenceError'] = updateUtil::getTemplateContentForResponse(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php');
		}

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/registerFormError.inc.php');
		return updateUtil::getResponseString($ret);

	}


	function getRepeatRegistrationFormErrorResponse($serialstate) {

		$GLOBALS['updateServerTemplateData']['licenceError'] = $GLOBALS['lang']['license']['undefinedError'] . ': <code>' . $serialstate . '</code>';

		if (file_exists(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php')) {

			$GLOBALS['updateServerTemplateData']['licenceError'] = updateUtil::getTemplateContentForResponse(SHARED_TEMPLATE_DIR . '/license/' . $serialstate . '.inc.php');
		}

		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/register/repeatRegistrationFormError.inc.php');
		return updateUtil::getResponseString($ret);

	}

}

?>