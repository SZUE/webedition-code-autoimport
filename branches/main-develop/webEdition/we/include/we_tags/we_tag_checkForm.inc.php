<?php
/**
 * @return string
 * @param array $attribs
 * @param string $content
 * @desc Beschreibung eingeben...
 */
function we_tag_checkForm($attribs, $content){
	//  dont make this in editMode
	if (isset($GLOBALS["we_editmode"]) && $GLOBALS["we_editmode"]) {
		return "";
	}

	//  check required Fields
	$missingAttrib = attributFehltError($attribs, "match", "we_tag_checkForm");
	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}

	$missingAttrib = attributFehltError($attribs, "type", "we_tag_checkForm");
	if ($missingAttrib) {
		print $missingAttrib;
		return "";
	}

	// get fields of $attribs
	$match = we_getTagAttribute("match", $attribs);
	$type = we_getTagAttribute("type", $attribs);

	$mandatory = we_getTagAttribute("mandatory", $attribs);
	$email = we_getTagAttribute("email", $attribs);
	$password = we_getTagAttribute("password", $attribs);

	$onError = we_getTagAttribute("onError", $attribs);
	$jsIncludePath = we_getTagAttribute("jsIncludePath", $attribs);

	$xml = we_getTagAttribute("xml", $attribs, "");

	//  then lets parse the content
	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);
	$tp->parseTags($tags, $content);

	//  Generate errorHandler:
	if ($onError) {
		$jsOnError = '
            if(self.' . $onError . '){
                ' . $onError . '(formular,missingReq,wrongEmail,pwError);
            } else {
            	' . we_message_reporting::getShowMessageCall(
				$content,
				WE_MESSAGE_FRONTEND) . '
            }
        ';
	} else {
		$jsOnError = we_message_reporting::getShowMessageCall($content, WE_MESSAGE_FRONTEND);
	}

	//  Generate mandatory array
	if ($mandatory) {
		$_fields = explode(',', $mandatory);
		$jsMandatory = '//  check mandatory
        var required = new Array("' . implode('", "', $_fields) . '");
        missingReq = weCheckFormMandatory(formular, required);';
	} else {
		$jsMandatory = '';
	}

	if ($email) { //  code to check Emails
		$_emails = explode(',', $email);
		$jsEmail = '//  validate emails
        var email = new Array("' . implode('", "', $_emails) . '");
        wrongEmail = weCheckFormEmail(formular, email);';
	} else {
		$jsEmail = '';
	}

	if ($password) {
		$_pwFields = explode(',', $password);
		if (sizeof($_pwFields) != 3) {
			$jsPasword = '';
			return parseError($GLOBALS["l_parser"]["checkForm_password"]);
		}
		$jsPasword = '//  check passwords
        var password = new Array("' . implode('", "', $_pwFields) . '");
        pwError = weCheckFormPassword(formular, password);
        ';
	} else {
		$jsPasword = '';
	}

	//  deal with alwasy needed stuff - "class weCheckFormEvent"
	if ($jsIncludePath) {

		if (is_numeric($jsIncludePath)) {
			$jsTag = we_tag('js',array(
				'id' => $jsIncludePath, 'xml' => $xml
			));
			if ($jsTag) {
				$jsEventHandler = $jsTag;
			} else {
				$jsEventHandler = '';
				return parseError($GLOBALS["l_parser"]["checkForm_jsIncludePath_not_found"]);
			}

		} else {
			$jsEventHandler = '
    <script type="text/javascript" src="' . $jsIncludePath . '"></script>
            ';
		}

	} else {
		$jsEventHandler = '
    <script type="text/javascript" src="' . JS_DIR . 'external/weCheckForm.js"></script>
    ';
	}

	switch ($type) {

		case "id" : //  id of formular is given


			$initFunction = '
    weCheckFormEvent.addEvent( window, "load", function(){
        initWeCheckForm_by_id("' . $match . '");
        }
    );
            ';
			$checkFunction = '
    function weCheckForm_id_' . $match . '(ev){

        var missingReq = new Array(0);
        var wrongEmail = new Array(0);
        var pwError    = false;

        formular = document.getElementById("' . $match . '");

        ' . $jsMandatory . '

        ' . $jsEmail . '

        ' . $jsPasword . '

        //  return true or false depending on errors
        if( (wrongEmail.length>0) || (missingReq.length>0) || pwError){

            ' . $jsOnError . '
            weCheckFormEvent.stopEvent(ev);
            return false;
        } else {
            return true;
        }
    }
            ';

			$function = '
    <script type="text/javascript">
    <!--
    ' . $initFunction . '
    ' . $checkFunction . '
    //-->
    </script>';
			break;

		case "name" : //  name of formular is given


			$initFunction = '
    weCheckFormEvent.addEvent( window, "load", function(){
        initWeCheckForm_by_name("' . $match . '");
        }
    );
            ';
			$checkFunction = '
    function weCheckForm_n_' . $match . '(ev){


        var missingReq = new Array(0);
        var wrongEmail = new Array(0);
        var pwError    = false;

        formular = document.forms["' . $match . '"];

        ' . $jsMandatory . '

        ' . $jsEmail . '

        ' . $jsPasword . '

        //  return true or false depending on errors
        if( wrongEmail.length || missingReq.length || pwError){

            ' . $jsOnError . '
            weCheckFormEvent.stopEvent(ev);
            return false;
        } else {
            return true;
        }
    }
            ';

			$function = '
    <script type="text/javascript">
    <!--
    ' . $initFunction . '
    ' . $checkFunction . '
    //-->
    </script>';
			break;
	}

	return "
            $jsEventHandler
            $function
           ";
}?>
