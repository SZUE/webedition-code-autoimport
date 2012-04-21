<?php

class leButton {

	function get($name = "", $text = "", $href = "", $width = 100, $height = 22, $on_click = "", $disabled = false, $uniqid = true){

	 	//	Size of button
	 	$width_left  = 11;
	 	$width_right = 12;

	 	$_formIdentifier   = "form:";
	 	$_submitIdentifier = "submit:";
	 	$_jsIdentifier     = "javascript:";

	 	$_add_form_submit_dummy = false;

	 	//	Define the name of the button.
	 	$_button_name = ($uniqid ? uniqid($name . "_") : $name);

	 	// Define the name of the left/right image of the button
		$_button_image_left = $name . "_left";
		$_button_image_right = $name . "_right";



		//	Onmousedown-Events
		$_on_mouse_down  = $_button_name . "_mouse_event = true;";
		$_on_mouse_down .= "if (" . $_button_name . "_enabled) { document.getElementById('" . $_button_name . "_left').className = 'leBtnLeftClicked'; }";
		$_on_mouse_down .= "if (" . $_button_name . "_enabled) { document.getElementById('" . $_button_name . "_middle').className = 'leBtnMiddleClicked'; }";
		$_on_mouse_down .= "if (" . $_button_name . "_enabled) { document.getElementById('" . $_button_name . "_right').className = 'leBtnRightClicked'; }";

		//	Onmouseover-Events
		if(!preg_match('/\.gif$/', $text)) {
			$_on_mouse_over = "window.status='" . $text . "';return true;";
		} else {
			$_on_mouse_over = "";
			$text = '<img src="' . LE_ONLINE_INSTALLER_URL . '/img/leButton/'.$text.'" class="leBtnImage" />';
		}

		//	Onmouseout
		$_on_mouse_out  = "document.getElementById('" . $_button_name . "_left').className = 'leBtnLeft';";
		$_on_mouse_out .= "document.getElementById('" . $_button_name . "_middle').className = 'leBtnMiddle';";
		$_on_mouse_out .= "document.getElementById('" . $_button_name . "_right').className = 'leBtnRight';";

		//	OnmouseUp
	 	$_on_mouse_up  = $_on_mouse_out . $_button_name . "_mouse_event = false;";


	 	//	Check href of document
	 	if (strpos($href, $_formIdentifier) === false) { // Button will NOT be used in a form

	 		// Check if the buttons target will be a JavaScript
			if (strpos($href, $_jsIdentifier) === false) { // Buttons target will NOT be a JavaScript

				$_button_link = "window.location.href='" . $href . "';";
				$_on_mouse_up .= $_button_link;

			} else { // Buttons target will be a JavaScript

				// Get content of JavaScript
				$_javascript_content = substr($href, (strpos($href, $_jsIdentifier) + strlen($_jsIdentifier)));
				$_on_mouse_up .= $_javascript_content;
			}
		} else { // Button will be used in a form

			// Check if the button shall call the onSubmit event
			if (strpos($href, $_submitIdentifier) === false) { // Button shall not call the onSubmit event

				// Get name of form
				$_form_name = substr($href, (strpos($href, $_formIdentifier) + strlen($_formIdentifier)));

				// Render link
				$_on_mouse_up .= "document." . substr($href, (strpos($href, $_formIdentifier) + strlen($_formIdentifier))) . ".submit();return false;";
			} else { // Button must call the onSubmit event

				// Set variable for Form:Submit behaviour
				$_add_form_submit_dummy = true;

				// Get name of form
				$_form_name = substr($href, (strpos($href, $_submitIdentifier) + strlen($_submitIdentifier)));

				// Render link
				$_on_mouse_up .= "if (document." . $_form_name . ".onsubmit()) { document." . $_form_name . ".submit(); } return false;";
			}
		}

	 	// Finalize the onMouseUp event
		$_on_mouse_up = "if (" . $_button_name . "_enabled) { " . $_on_mouse_up . "}";
		// Finalize the onMouseOut event
		$_on_mouse_out = "if (" . $_button_name . "_enabled) { if (" . $_button_name . "_mouse_event) { " . $_on_mouse_out . "} } window.status='';";

	 	//	First some Javascript to the button
	 	$_buttonString  = "
<script type=\"text/javascript\" language=\"JavaScript\">
var " . $_button_name . "_mouse_event;var " . $_button_name . "_enabled = " . ($disabled ? "false" : "true") . ";
</script>";

	 	//	attribs of the button-sides
		$_button_attribs_left   = " id=\"" . $_button_name . "_left\" class=\"leBtnLeft" . ($disabled ? "Disabled" : "") . "\" style=\"width: " . $width_left . "px\"";
		$_button_attribs_middle = " id=\"" . $_button_name . "_middle\" align=\"center\"  class=\"leBtnMiddle" . ($disabled ? "Disabled" : "") . "\" style=\"width: " . ($width - $width_left - $width_right) . "px;\"";
		$_button_attribs_right  = " id=\"" . $_button_name . "_right\" class=\"leBtnRight" . ($disabled ? "Disabled" : "") . "\" style=\"width: " . $width_right . "px\"";


		$_button_attributes["id"] = $_button_name . "_table";

	 	//	Build the table
	 	$_buttonString .= '
<table id="' . $_button_name . '_table" cellpadding="0" cellspacing="0" border="0" style="cursor: ' . ($disabled ? "default" : "pointer") . '; width: ' . $width . 'px;-moz-user-select: none;"  onclick="'.$on_click.'" onmousedown="' . $_on_mouse_down . '" onmouseup="' . $_on_mouse_up . '" onmouseout="' . $_on_mouse_out . '" onmouseover="' . $_on_mouse_over . '">';
	 	//	First row of table.
	 	$_buttonString .= '
<tr style="height: ' . $height . 'px;">
	<td' . $_button_attribs_left . '></td>
	<td' . $_button_attribs_middle . '>' . $text . '</td>
	<td' . $_button_attribs_right . '>'.($name=='next'?'<input type="image" src="' . LE_ONLINE_INSTALLER_URL . '/img/leButton/pixel.gif" />':'').'</td>
</tr>
</table>
';
	 	if($_add_form_submit_dummy){
	 		$_buttonString .= '<div style="height: 0px;width: 0px;"><input type="image" src="' . LE_ONLINE_INSTALLER_URL . '/img/leButton/pixel.gif" height="1" width="1"  onfocus="this.blur();" class="defaultfont" /></div>';
	 	}
	 	return $_buttonString;
	 }

}