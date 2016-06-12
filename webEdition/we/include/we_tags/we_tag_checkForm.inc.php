<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_parse_tag_checkForm($attribs, $content, array $arr){
	if(($foo = attributFehltError($arr, array('match' => false, 'type' => false), __FUNCTION__))){
		return $foo;
	}

	return '<?php printElement(' . we_tag_tagParser::printTag('checkForm', $attribs, $content, true) . '); ?>';
}

/**
 * @return string
 * @param array $attribs
 * @param string $content
 */
function we_tag_checkForm(array $attribs, $content){
	//  dont make this in editMode
	if(!empty($GLOBALS["we_editmode"])){
		return "";
	}

	//  check required Fields
	if(($missingAttrib = attributFehltError($attribs, array("match" => false, 'type' => false), __FUNCTION__))){
		echo $missingAttrib;
		return '';
	}

	ob_start();
	//FIXME:eval
	eval('?>' . $content);
	$content = ob_get_clean();

	// get fields of $attribs
	$match = weTag_getAttribute("match", $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute("type", $attribs, '', we_base_request::STRING);
	$mandatory = weTag_getAttribute("mandatory", $attribs, '', we_base_request::STRING);
	$email = weTag_getAttribute("email", $attribs, '', we_base_request::STRING);
	$password = weTag_getAttribute("password", $attribs, '', we_base_request::STRING);
	$onError = weTag_getAttribute("onError", $attribs, '', we_base_request::JS);
	$jsIncludePath = weTag_getAttribute("jsIncludePath", $attribs, '', we_base_request::RAW);
	$xml = weTag_getAttribute("xml", $attribs, XHTML_DEFAULT, we_base_request::BOOL);

	//  Generate errorHandler:
	$jsOnError = ($onError ?
			$jsOnError = '
if(self.' . $onError . '){' .
			$onError . '(formular,missingReq,wrongEmail,pwError);
} else {' .
			we_message_reporting::getShowMessageCall($content, we_message_reporting::WE_MESSAGE_FRONTEND) . '
}' :
			we_message_reporting::getShowMessageCall($content, we_message_reporting::WE_MESSAGE_FRONTEND)
		);

	//  Generate mandatory array
	if($mandatory){
		$fields = explode(',', $mandatory);
		$jsMandatory = '//  check mandatory
        var required = ["' . implode('", "', $fields) . '"];
        missingReq = weCheckFormMandatory(formular, required);';
	} else {
		$jsMandatory = '';
	}

	$jsEmail = ($email ? //  code to check Emails
			'//  validate emails
        var email = ["' . implode('", "', explode(',', $email)) . '"];
        wrongEmail = weCheckFormEmail(formular, email);' :
			'');


	if($password){
		$pwFields = explode(',', $password);
		if(count($pwFields) != 3){
			$jsPasword = '';
			return parseError(g_l('parser', '[checkForm_password]'));
		}
		$jsPasword = '//  check passwords
        var password = ["' . implode('", "', $pwFields) . '"];
        pwError = weCheckFormPassword(formular, password);
        ';
	} else {
		$jsPasword = '';
	}

	//  deal with alwasy needed stuff - "class weCheckFormEvent"
	if($jsIncludePath){

		if(is_numeric($jsIncludePath)){
			$jsEventHandler = we_tag('js', array('id' => $jsIncludePath, 'xml' => $xml));
			if(!$jsEventHandler){
				return parseError(g_l('parser', '[checkForm_jsIncludePath_not_found]'));
			}
		} else {
			$jsEventHandler = we_html_element::jsScript($jsIncludePath);
		}
	} else {
		$jsEventHandler = we_html_element::jsScript(JS_DIR . 'external/weCheckForm.js');
	}

	switch($type){
		case "id" : //  id of formular is given
			$function = we_html_element::jsElement(
					'weCheckFormEvent.addEvent(window, "load", function(){
	initWeCheckForm_by_id(weCheckForm_id_' . $match . ',"' . $match . '");
 });
 function weCheckForm_id_' . $match . '(ev){
	var missingReq = [0];
	var wrongEmail = [0];
	var pwError    = false;

	formular = document.forms.' . $match . ';
	' . $jsMandatory . '
	' . $jsEmail . '
	' . $jsPasword . '

	//  return true or false depending on errors
	if( (wrongEmail.length>0) || (missingReq.length>0) || pwError){

			' . $jsOnError . '
			weCheckFormEvent.stopEvent(ev);
			return false;
	}
	return true;
}');

			break;

		case "name" : //  name of formular is given
			$function = we_html_element::jsElement(
					'weCheckFormEvent.addEvent( window, "load", function(){
        initWeCheckForm_by_name(weCheckForm_n_' . $match . ',"' . $match . '");
        }
    );
function weCheckForm_n_' . $match . '(ev){
		var missingReq = [0];
		var wrongEmail = [0];
		var pwError    = false;

		formular = document.forms.' . $match . ';
		' . $jsMandatory . '
		' . $jsEmail . '
		' . $jsPasword . '

		//  return true or false depending on errors
		if( wrongEmail.length || missingReq.length || pwError){

				' . $jsOnError . '
				weCheckFormEvent.stopEvent(ev);
				return false;
		}
	return true;
}');

			break;
	}

	return $jsEventHandler . $function;
}
