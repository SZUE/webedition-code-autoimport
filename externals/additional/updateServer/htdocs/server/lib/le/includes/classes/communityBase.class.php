<?php

class communityBase {
	
	/**
	 * checks if there is a registration with the specified values
	 * @return bool true/false
	 * @param string $email e-mail address used as login and username
	 * @param string $password password of the user account
	 */
	function checkRegistration($email = "", $password = "", $checkPassword = true) {
		
		global $DB_Customer;
		$email = escapeshellcmd($email);
		$password = escapeshellcmd($password);
		if($checkPassword === true) {
			$query = "select id from users where `email` = '$email' AND password = '$password'";
		} else {
			$query = "select id from users where `email` = '$email'";
		}
		$res =& $DB_Customer->query($query);
		$members = array();
		//error_log(print_r($DB_Customer,true));
		//error_log(print_r($res,true));
		while ( $row = @$res->fetchRow() ) {
			$members[] = $row['id'];
		}
		//error_log(count($members)." members found");
		if(count($members) == "1") {
			return true;
		} else {
			return false;
		}
		
	}	
	
	/**
	 * creates the community registration form with the three choices
	 * - create a new account
	 * - use existing account
	 * - skip registration
	 */
    function getCommunityFormResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityForm.inc.php');
		return updateUtil::getResponseString($ret);
    }
	
	function getCommunityCreateRegistrationForm() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityCreateRegistrationForm.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	/**
	 * creates a response for using an existing account, called from Community::check()
	 */
	function getCommunityRegistrationCheckResponse() {
		$memberdata = @unserialize(base64_decode($_REQUEST["reqArray"]));
		//error_log(print_r($memberdata,true));
		if(isset($memberdata["le_communityChoice_Already_Email"])) {
			$email = $memberdata["le_communityChoice_Already_Email"];
		} else if(isset($memberdata["le_community_Email"])) {
			$email = $memberdata["le_community_Email"];
		} else {
			$email = "";
		}
		if(isset($memberdata["le_communityChoice_Already_Password"])) {
			$password = $memberdata["le_communityChoice_Already_Password"];
		} else if(isset($memberdata["le_community_Password"])) {
			$password = $memberdata["le_community_Password"];
		} else {
			$password = "";
		}
		
		if($memberdata["le_communityChoice"] == "Already") {
			$registrationCheck = self::checkRegistration($email,$password,true);
			//error_log("existing member");
			if($registrationCheck === false) {
				$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityCheckRegistration.inc.php');
				return updateUtil::getResponseString($ret);
			} else {
				return true;
			}
		} else {
			//error_log("new member");
			$registrationCheck = self::checkRegistration($email,$password,false);
			if($registrationCheck === true) {
				$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityCheckNewRegistration.inc.php');
				return updateUtil::getResponseString($ret);
			} else {
				$_SESSION["le_communityRegistrationVerified"] = true;
				return true;
			}
		}
	}
	
	/**
	 * creates a response for skipping the community registration
	 * shows an info text with a confirmation checkbox, called from Community::check()
	 */
	function getCommunitySkipRegistrationResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communitySkipRegistration.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	/**
	 * creates a response for showing an input form where the user can enter the required information for creating a new community account
	 * called from Community::check()
	 */
	function getCommunityRegistrationCreateResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityCreateRegistration.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function getCommunityRegistrationSuccessResponse() {
		$ret = updateUtil::getLiveUpdateResponseArrayFromFile(LIVEUPDATE_SERVER_TEMPLATE_DIR . '/community/communityWelcome.inc.php');
		return updateUtil::getResponseString($ret);
	}
	
	function saveCommunityRegistrationResponse() {
		global $DB_Customer;
		$memberdata = @unserialize(base64_decode($_REQUEST["reqArray"]));
		if(isset($_SESSION["le_communityRegistrationVerified"]) && $_SESSION["le_communityRegistrationVerified"] === true) {
			//error_log(print_r($memberdata,true));
			$ip = (isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : "");
			$query = "insert into users set `type`='customer',`password`='".$memberdata["le_community_Password"]."',`salutation`='".$memberdata["le_community_SalutationSelect"]."',`firstname`='".$memberdata["le_community_Prename"]."',`lastname`='".$memberdata["le_community_Surname"]."',`company`='".$memberdata["le_community_Company"]."',`country`='".$memberdata["le_community_CountrySelect"]."',`email`='".$memberdata["le_community_Email"]."',`www`='".$memberdata["le_community_Website"]."',`language`='".$memberdata["le_community_LanguageSelect"]."',`newsletter`='1',`created`='".time()."',`modified`='".time()."',`ip`='".$ip."'";
			//error_log($query);
			$res =& $DB_Customer->query($query);
			//error_log(print_r($res,true));
			return $res;
		}
	}
}
?>