<?php

class register extends registerBase{

	function getNewsletterHtml(){

		global $clientRequestVars;

		$salutations = array($GLOBALS['lang']['register']['salutationMr'], $GLOBALS['lang']['register']['salutationMrs']);
		$salutationSelect = '<select name="salutation" id="salutation">';
		foreach($salutations as $salutation){
			$salutationSelect .= '<option' . (isset($clientRequestVars['salutation']) && $clientRequestVars['salutation'] == $salutation ? ' selected="selected"' : '') . '>' . $salutation . '</option>';
		}
		$salutationSelect .= '</select>';

		$languages = array('Deutsch', 'English');
		$languageSelect = '<select name="language" id="language">';
		foreach($languages as $lng){
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
			<script>

function clickNewsletter(){

	var fields = new Array("email","salutation","title","forename","surname","language");

	val = !document.forms[0].newsletter.checked;

	for(i=0;i<fields.length;i++){
		document.forms[0][fields[i]].disabled=val;
	}
}

function submitRegistration(){

	if(document.forms[0].newsletter.checked){

		pattern = "^[a-zA-Z0-9-���_\.]+@[a-zA-Z0-9���\.-]+.[a-zA-Z0-9]{1,4}$";

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
	
}
