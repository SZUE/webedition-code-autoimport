<?php
	/**
	 * opens a popup that contains a form for entering registration data
	 * to join the webEdition community
	 */
	class Community extends leStep {

		//var $EnabledButtons = array('next');
		
		function execute(&$Template = '') {

			//error_log("community wizard class - execute()");
			return $this->executeOnline($Template, "community", "communityRegistrationForm");
			/*
			//return $this->executeOnline($Template, "community", "communityRegistrationForm");
			$Output = "<table class='leContentTable'>\n";
			$_choiceNotYetOnClickJs ="document.getElementById('le_communityChoice_notYet_Form').style.display = '';\n"
				."document.getElementById('le_communityChoice_already_Form').style.display = 'none';\n";
			//error_log($_SESSION["le_communityChoice"]);
			if(!isset($_SESSION["le_communityChoice"]) || $_SESSION["le_communityChoice"] == "" || $_SESSION["le_communityChoice"] == "notYet") {
				$_choiceNotYetSelected = true;
				$_choiceNotYetRadioButtonDisable = " ";
			} else {
				$_choiceNotYetSelected = false;
				$_choiceNotYetRadioButtonDisable = "display:none";
			}
			$_choiceNotYetRadio = leCheckbox::get(
									"le_communityChoice",
									"notYet",
									array(
										"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceNotYetOnClickJs)),
										"id"		=> "le_communityChoice_notYet",
									),
									"",
									$_choiceNotYetSelected,
									"radio"
								);
			$_choiceNotYetButton = leButton::get("le_communityChoice_notYet_button", $this->Language["button"]["enterData"], "javascript:alert('huhu');", "150", "22", "", false, true);
			$Output	.=	'<tr>'
					.	'<td class="defaultfont" width="20px">' . $_choiceNotYetRadio . '</td>'
					.	'<td class="defaultfont"><label for="le_communityChoice_notYet">' . $this->Language["choice"]["notRegisteredYet"] . '</label></td>'
					.	'</tr>';
			$Output	.=	'<tr id="le_communityChoice_notYet_Form" style="' . $_choiceNotYetRadioButtonDisable . ';">'
					.	'<td class="defaultfont" width="20px"></td>'
					.	'<td class="defaultfont">'.$_choiceNotYetButton.'</td>'
					.	'</tr>';
			
			$_choiceAlreadyOnClickJs = "document.getElementById('le_communityChoice_notYet_Form').style.display = 'none';\n"
				."document.getElementById('le_communityChoice_already_Form').style.display = '';\n";
			//error_log($_SESSION["le_communityChoice"]);
			if(isset($_SESSION["le_communityChoice"]) && $_SESSION["le_communityChoice"] == "Already") {
				$_choiceAlreadySelected = true;
				$_choiceAlreadyRadioButtonDisable = "";
			} else {
				$_choiceAlreadySelected = false;
				$_choiceAlreadyRadioButtonDisable = "display:none;";
			}
			// username/e-mail address
			$name = 'le_communityChoice_Already_Email';
			$value = isset($_SESSION['le_communityChoice_Already_Email']) ? $_SESSION['le_communityChoice_Already_Email'] : "";
			$attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 250px',
			);
			$type = "text";
			$email_input = leInput::get($name, $value, $attribs, $type);
			$email_help = leLayout::getHelp($this->Language["help"]["email"]);
			// username/e-mail address
			$name = 'le_communityChoice_Already_Password';
			$value = isset($_SESSION['le_communityChoice_Already_Password']) ? $_SESSION['le_communityChoice_Already_Password'] : "";
			$attribs = array(
				'size'	=> '40',
				'style'	=> 'width: 250px',
			);
			$type = "password";
			$password_input = leInput::get($name, $value, $attribs, $type);
			$password_help = leLayout::getHelp($this->Language["help"]["password"]);
			// html code:
			$_choiceAlreadyForm = '<b>'.$this->Language["input"]["email"].':</b> '.$email_help.'<br />'
				.$email_input.'<br />'
				.'<b>'.$this->Language["input"]["password"].': </b> '.$password_help.'<br />'
				.$password_input.'<br />';
			$_choiceAlreadyRadio = leCheckbox::get(
									"le_communityChoice",
									"Already",
									array(
										"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceAlreadyOnClickJs)),
										"id"		=> "le_communityChoice_Already",
									),
									"",
									$_choiceAlreadySelected,
									"radio"
								);

			$Output	.=	'<tr>'
					.	'<td class="defaultfont" width="20px">' . $_choiceAlreadyRadio . '</td>'
					.	'<td class="defaultfont"><label for="le_communityChoice_Already">' . $this->Language["choice"]["alreadyRegistered"] . '</label></td>'
					.	'</tr>';
			$Output	.=	'<tr id="le_communityChoice_already_Form" style="'.$_choiceAlreadyRadioButtonDisable.'">'
					.	'<td class="defaultfont" width="20px"></td>'
					.	'<td class="defaultfont">'.$_choiceAlreadyForm.'</td>'
					.	'</tr>';

			$_choiceSkipOnClickJs = "document.getElementById('le_communityChoice_notYet_Form').style.display = 'none';\n"
				."document.getElementById('le_communityChoice_already_Form').style.display = 'none';\n";
			
			//error_log($_SESSION["le_communityChoice"]);
			if(isset($_SESSION["le_communityChoice"]) && $_SESSION["le_communityChoice"] == "skip") {
				$_choiceSkipSelected = true;
			} else {
				$_choiceSkipSelected = false;
			}
			$_choiceSkipRadio = leCheckbox::get(
									"le_communityChoice",
									"skip",
									array(
										"onClick"	=> str_replace("\n", "", str_replace("\r\n", "\n", $_choiceSkipOnClickJs)),
										"id"		=> "le_defaultLanguage_skip",
									),
									"",
									$_choiceSkipSelected,
									"radio"
								);

			$Output	.=	'<tr>'
					.	'<td class="defaultfont" width="20px">' . $_choiceSkipRadio . '</td>'
					.	'<td class="defaultfont"><label for="le_defaultLanguage_skip">' . $this->Language["choice"]["skip"] . '</label></td>'
					.	'</tr>';
					
			$this->setHeadline($this->Language['headline']);
			$this->setContent($this->Language['content'] . $Output);
			return LE_STEP_NEXT;
			*/
		}

		function check(&$Template = '') {

	    	//error_log("community wizard class - check()");
	    	//error_log($_SESSION["le_community"]);
	    	//error_log(print_r($_REQUEST,true));
	    	if(isset($_REQUEST["le_communityChoice"]) && !empty($_REQUEST["le_communityChoice"])) {
	    		$_SESSION["le_communityChoice"] = $_REQUEST["le_communityChoice"];
	    	} else {
	    		$_SESSION["le_communityChoice"] = "";
	    		$_REQUEST["le_communityChoice"] = "";
	    	}
			
			switch($_SESSION["le_communityChoice"]) {
				case "notYet":
					return true;
					break;
				case "Already":
					// check existing user data (email, password, connect test):
					return $this->_validateExistingUser(&$Template);
					break;
				default:
					// skip registration:
					//return $this->executeOnline($Template, "community", "skipCommunityRegistration");
					return true;
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
		
		function _validateExistingUser(&$Template = '') {
	    	if(isset($_REQUEST["le_communityChoice_Already_Email"]) && !empty($_REQUEST["le_communityChoice_Already_Email"])) {
	    		$_SESSION["le_communityChoice_Already_Email"] = $_REQUEST["le_communityChoice_Already_Email"];
	    	}
			if(!isset($_SESSION['le_communityChoice_Already_Email']) || trim($_SESSION['le_communityChoice_Already_Email']) == "") {
				$Template->addError($this->Language['error']["email"]);
				$Template->addJavascript("top.leForm.setFocus('le_communityChoice_Already_Email');");
				$Template->addJavascript("top.leContent.scrollDown();");
				//$_SESSION['le_communityChoice_Already_Email'] = "";
				return false;
			} else {
				$_SESSION['le_communityChoice_Already_Email'] = trim($_REQUEST['le_communityChoice_Already_Email']);
				if(!$this->_validateEmailAddress($_SESSION['le_communityChoice_Already_Email'])) {
					$Template->addError($this->Language['error']["email"]);
					$Template->addJavascript("top.leForm.setFocus('le_communityChoice_Already_Email');");
					$Template->addJavascript("top.leContent.scrollDown();");
					return false;
				}
			}
			
			// check le_communityChoice_Already_Password
	    	if(isset($_REQUEST["le_communityChoice_Already_Password"]) && !empty($_REQUEST["le_communityChoice_Already_Password"])) {
	    		$_SESSION["le_communityChoice_Already_Password"] = $_REQUEST["le_communityChoice_Already_Password"];
	    	}
			if(!isset($_SESSION['le_communityChoice_Already_Password']) || trim($_SESSION['le_communityChoice_Already_Password']) == "" || strlen($_SESSION['le_communityChoice_Already_Password']) < 8) {
				$Template->addError($this->Language['error']["password"]);
				$Template->addJavascript("top.leForm.setFocus('le_communityChoice_Already_Password');");
				$Template->addJavascript("top.leContent.scrollDown();");
				$_SESSION['le_communityChoice_Already_Password'] = "";
				return false;
			} else {
				$_SESSION['le_communityChoice_Already_Password'] = trim($_SESSION['le_communityChoice_Already_Password']);
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