 <?php
$_params = unserialize(base64_decode($_REQUEST["reqArray"]));
// execute command
switch ($_REQUEST['detail']) {
	// show the form for joining the webEdition community
	case 'communityRegistrationForm':
		print community::getCommunityFormResponse();
		break;
	// show the form for creating a new webEdition community membership
	case 'communityCreateRegistrationForm':
		print community::getCommunityCreateRegistrationForm();
		break;
	
	// check entered data for creating a new account
	case 'checkCommunityCreateRegistration':
		print community::getCommunityRegistrationCreateResponse();
		break;
	
	// check email and passwort for existing community member
	case 'checkCommunityRegistration':
		print community::getCommunityRegistrationCheckResponse();
		break;
	
	// skip the webEdition community registration form
	case 'skipCommunityRegistration':
		if(isset($_params["le_communityChoice_ReallySkip"]) && $_params["le_communityChoice_ReallySkip"] == "yes") {
			//error_log("yes, really skip");
		} else {
			//error_log("skip");
			//$_SESSION["le_communityChoice_ReallySkip"] = true;
			print community::getCommunitySkipRegistrationResponse();
		}
		break;
	
	// print welcome message if it is an already existing community member
	case "checkCommunityRegistrationSuccess":
		print community::getCommunityRegistrationSuccessResponse();
		break;
		
	case "saveCommunityRegistration":
		print community::saveCommunityRegistrationResponse();
		break;
		
	default:
		print notification::getCommandNotKnownResponse();
		break;
}		
?>