<?php
	/**
	 * opens a popup that contains a form for entering registration data
	 * to join the webEdition community
	 */
	class CommunityRegistration extends leStep {

		//var $EnabledButtons = array('next');
		
		function execute(&$Template = '') {
			//error_log($_SESSION["le_communityChoice"]);
			//error_log(print_r($_SESSION,true));
	    	
			switch($_SESSION["le_communityChoice"]) {
				case "notYet":
					return $this->executeOnline($Template, "community", "communityCreateRegistrationForm");
					break;
				case "Already":
					return $this->executeOnline($Template, "community", "checkCommunityRegistrationSuccess");
					break;
				default:
					return $this->executeOnline($Template, "community", "skipCommunityRegistration");
					break;
			}
		}

		function check(&$Template = '') {
			
	    	//error_log("community wizard class - check()");
	    	//error_log("choice: ".$_SESSION["le_communityChoice"]);
	    	//error_log(print_r($_SESSION,true));
	    	if(isset($_REQUEST["le_communityChoice"]) && !empty($_REQUEST["le_communityChoice"])) {
	    		$_SESSION["le_communityChoice"] = $_REQUEST["le_communityChoice"];
	    	} else if(!isset($_SESSION["le_communityChoice"]) && empty($_SESSION["le_communityChoice"])){
	    		$_SESSION["le_communityChoice"] = "";
	    		$_REQUEST["le_communityChoice"] = "";
	    	}
			switch($_SESSION["le_communityChoice"]) {
				case "notYet":
					return true;
					$accountAvailable = $this->_validateExistingUser(&$Template);
					if($accountAvailable === false) {
						// user already exists
						return false;
					} else {
						$isComplete = $this->_validateUserData(&$Template);
						if($isComplete === true) {
							return $this->executeOnline($Template, "community", "saveCommunityRegistration");
						}
						return $isComplete;
					}
					//return $this->executeOnline($Template, "community", "checkCommunityCreateRegistration");
					break;
				case "Already":
					// check existing user data (email, password, connect test):
					return $this->_validateExistingUser(&$Template);
					break;
				default:
					// skip registration:
					if(isset($_REQUEST["le_communityChoice_ReallySkip"]) && $_REQUEST["le_communityChoice_ReallySkip"] == "yes") {
						return true;
					} else {
						return false;
					}
					break;
			}
	    	
			/*
			if(isset($_REQUEST["le_community"]) && $_REQUEST["le_community"] == 1) {

				$_SESSION['le_community'] = true;
				$_SESSION['le_serial'] = $_REQUEST["le_serial"];

				$_REQUEST['clientSerial'] = $_SESSION['le_serial'];

				return $this->executeOnline($Template, "community", "checkCommunityRegistration");

			} else {

				$_SESSION['le_community'] = false;
				$_SESSION['le_serial'] = "";

				$_REQUEST['clientSerial'] = $_SESSION['le_serial'];

				return $this->executeOnline($Template, "community", "skipCommunityRegistration");

			}
			*/
		}
		
		function _validateUserData(&$Template = '') {
			// check if all required fields are filled:
			$_SESSION["le_community_SalutationSelect"] = @escapeshellcmd($_REQUEST["le_community_SalutationSelect"]);
			$_SESSION["le_community_Prename"] = escapeshellcmd(@$_REQUEST["le_community_Prename"]);
			$_SESSION["le_community_Surname"] = escapeshellcmd(@$_REQUEST["le_community_Surname"]);
			$_SESSION["le_community_Company"] = escapeshellcmd(@$_REQUEST["le_community_Company"]);
			$_SESSION["le_community_Website"] = escapeshellcmd(@$_REQUEST["le_community_Website"]);
			$_SESSION["le_community_LanguageSelect"] = escapeshellcmd(@$_REQUEST["le_community_LanguageSelect"]);
			$_SESSION["le_community_CountrySelect"] = @escapeshellcmd($_REQUEST["le_community_CountrySelect"]);
	    	if(empty($_SESSION["le_community_Prename"]) || empty($_SESSION["le_community_Surname"])) {
				$Template->addError($this->Language['error']["userData"]);
				//$Template->addJavascript("top.leForm.setFocus('le_community_PasswordVerification');");
				//$Template->addJavascript("top.leContent.scrollDown();");
				//$_SESSION['le_community_PasswordVerification'] = "";
				return false;
	    	}
			return true;
			
		}
		
		function _validateExistingUser(&$Template = '') {
			
			// check le_community_Email
	    	if(isset($_REQUEST["le_community_Email"]) && !empty($_REQUEST["le_community_Email"])) {
	    		$_SESSION["le_community_Email"] = $_REQUEST["le_community_Email"];
	    		$_SESSION['le_community_Email'] = trim(escapeshellcmd($_SESSION['le_community_Email']));
	    	}
			if(!isset($_SESSION['le_community_Email']) || trim($_SESSION['le_community_Email']) == "") {
				$Template->addError($this->Language['error']["email"]);
				$Template->addJavascript("top.leForm.setFocus('le_community_Email');");
				//$Template->addJavascript("top.leContent.scrollDown();");
				//$_SESSION['le_communityChoice_Already_Email'] = "";
				return false;
			} else {
				$_SESSION['le_community_Email'] = trim($_REQUEST['le_community_Email']);
				if(!$this->_validateEmailAddress($_SESSION['le_community_Email'])) {
					$Template->addError($this->Language['error']["email"]);
					$Template->addJavascript("top.leForm.setFocus('le_community_Email');");
					//$Template->addJavascript("top.leContent.scrollDown();");
					return false;
				}
			}
			
			// check le_community_Password
	    	if(isset($_REQUEST["le_community_Password"]) && !empty($_REQUEST["le_community_Password"])) {
	    		$_SESSION["le_community_Password"] = $_REQUEST["le_community_Password"];
	    		$_SESSION['le_community_Password'] = trim($_SESSION['le_community_Password']);
	    	}
	    	if(isset($_REQUEST["le_community_PasswordVerification"]) && !empty($_REQUEST["le_community_PasswordVerification"])) {
	    		$_SESSION["le_community_PasswordVerification"] = $_REQUEST["le_community_PasswordVerification"];
	    		$_SESSION['le_community_PasswordVerification'] = trim($_SESSION['le_community_PasswordVerification']);
	    	}
			if(!isset($_SESSION['le_community_Password']) || trim($_SESSION['le_community_Password']) == "" || strlen($_SESSION['le_community_Password']) < 8) {
				$Template->addError($this->Language['error']["password"]);
				$Template->addJavascript("top.leForm.setFocus('le_community_Password');");
				//$Template->addJavascript("top.leContent.scrollDown();");
				//$_SESSION['le_community_Password'] = "";
				return false;
			} else {
				
			}
			
			// additionally check le_community_PasswordVerification
			if (isset($_REQUEST["le_community_PasswordVerification"])) {
		    	if(isset($_REQUEST["le_community_PasswordVerification"]) && !empty($_REQUEST["le_community_PasswordVerification"])) {
		    		$_SESSION["le_community_PasswordVerification"] = $_REQUEST["le_community_PasswordVerification"];
		    	}
				if(!isset($_SESSION['le_community_PasswordVerification']) || trim($_SESSION['le_community_PasswordVerification']) == "" || strlen($_SESSION['le_community_PasswordVerification']) < 8) {
					$Template->addError($this->Language['error']["password"]);
					$Template->addJavascript("top.leForm.setFocus('le_community_PasswordVerification');");
					//$Template->addJavascript("top.leContent.scrollDown();");
					//_SESSION['le_community_PasswordVerification'] = "";
					return false;
				} else if($_SESSION['le_community_Password'] != $_SESSION['le_community_PasswordVerification']) {
					$Template->addError($this->Language['error']["passwordVerification"]);
					$Template->addJavascript("top.leForm.setFocus('le_community_PasswordVerification');");
					//$Template->addJavascript("top.leContent.scrollDown();");
					return false;
				} else {
					
				}
			}
				
			//return true;
			return $this->executeOnline($Template, "community", "checkCommunityRegistration");
		}
		
		/**
		 * Validates an email-address.
		 * The function changes the parameter by cutting of leading and following whitespaces and setting it to lower case.
		 * 
		 * @param Reference to a string holding an email-address.
		 * @param Boolean (default false) telling wether the validation should be strict or not.
		 *        Strict validation does not allow special characters (like umlauts) in the email-address.
		 * @returm Boolean which is true if parameter is a valid email-address, false otherwise.
		 */
		function _validateEmailAddress(&$address_to_validate = '', $strict = false) {
		    //Leading and following whitespaces are ignored
		    $address_to_validate = trim($address_to_validate);
		    //Email-address is set to lower case
		    $address_to_validate = strtolower($address_to_validate);
		    
		    //List of signs which are illegal in name, subdomain and domain
		    $illegal_string = '\\\\(\\n)@';
		    
		    //Parts of the regular expression = name@subdomain.domain.toplevel
		    $name      = '([^\\.'.$illegal_string.'][^'.$illegal_string.']?)+';
		    $subdomain = '([^\\._'.$illegal_string.']+\\.)?';
		    $domain    = '[^\\.\\-_'.$illegal_string.'][^\\._'.$illegal_string.']*[^\\.\\-_'.$illegal_string.']';
		    $toplevel  = '([a-z]{2,4}|museum|travel)';    //.museum and .travel are the only TLDs longer than four signs
		
		    $regular_expression = '/^'.$name.'[@]'.$subdomain.$domain.'\.'.$toplevel.'$/';
		    //error_log("mail: ".$address_to_validate);
		    return preg_match($regular_expression, $address_to_validate) ? true : false;
		}  

	}

?>