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
we_html_tools::protect();

$uniqid = md5(uniqid(__FILE__, true)); // #6590, changed from: uniqid(time())

$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', '', 1);

// init document
$we_dt = isset($_SESSION['weS']['we_data'][$we_transaction]) ? $_SESSION['weS']['we_data'][$we_transaction] : "";
include(WE_INCLUDES_PATH . "we_editors/we_init_doc.inc.php");
$_thumbs = array();

if(!($we_doc instanceof we_imageDocument)){
	exit("ERROR: Couldn't initialize we_imageDocument object");
}

	echo we_html_tools::getHtmlTop(g_l('weClass', '[thumbnails]')) .
	we_html_element::jsElement('
function select_thumbnails(sel){

	var thumbs = "";

	for(var i=0; i<sel.options.length; i++){
		if(sel.options[i].selected){
			thumbs += (sel.options[i].value + ",");
		}
	}

	if(thumbs.length){
		thumbs = "," + thumbs;
		add_enabled = switch_button_state("add", "add_enabled", "enabled");
	}else{
		add_enabled = switch_button_state("add", "add_enabled", "disabled");
	}

	self.showthumbs.location = "' . WEBEDITION_DIR . 'showThumb.php?u=' . $uniqid . '&t=' . $we_transaction . '&id="+encodeURI(thumbs);

}

function add_thumbnails(){
	sel = document.getElementById("Thumbnails");
	var thumbs = "";
	for(var i=0; i<sel.options.length; i++){
		if(sel.options[i].selected){
			thumbs += (sel.options[i].value + ",");
		}
	}

	if(thumbs.length){
		thumbs = "," + thumbs;
		opener.we_cmd("do_add_thumbnails",thumbs);
	}

	self.close();

}

function we_cmd(){
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}

	switch (arguments[0]){
		case "editThumbs":
			new jsWindow(url, "thumbnails", -1, -1, 500, 550, true, true, true);
			break;
		default:
			for(var i = 0; i < arguments.length; i++){
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'parent.we_cmd(\'+args+\')\');
	}
}');

echo we_html_element::jsScript(JS_DIR . 'windows.js');


echo STYLESHEET . "</head>";

// SELECT Box with thumbnails
$_thumbnails = new we_html_select(array("multiple" => "multiple", "name" => "Thumbnails", "id" => "Thumbnails", "class" => "defaultfont", "size" => 6, "style" => "width: 340px;", "onchange" => "select_thumbnails(this);"));
$DB_WE->query("SELECT ID,Name,Format FROM " . THUMBNAILS_TABLE . " ORDER BY Name");

$_thumbnail_counter_firsttime = true;

$doc_thumbs = ($we_doc->Thumbs == -1) ? array() : makeArrayFromCSV($we_doc->Thumbs);

$selectedID = 0;
$_enabled_buttons = false;
while($DB_WE->next_record()){
	if(!in_array($DB_WE->f("ID"), $doc_thumbs)){
		$_enabled_buttons = true;
		$_thumbnail_counter = $DB_WE->f("ID");
		if(we_base_imageEdit::is_imagetype_read_supported(we_base_imageEdit::$GDIMAGE_TYPE[strtolower($we_doc->Extension)]) && we_base_imageEdit::is_imagetype_supported(trim($DB_WE->f("Format")) ? $DB_WE->f("Format") : we_base_imageEdit::$GDIMAGE_TYPE[strtolower($we_doc->Extension)])){
			$_thumbnails->addOption($DB_WE->f("ID"), $DB_WE->f("Name"));
		}
		if($_thumbnail_counter_firsttime){
			$selectedID = $DB_WE->f("ID");
			$_thumbnails->selectOption($selectedID);
		}

		$_thumbnail_counter_firsttime = false;
	}
}

$editbut = we_html_button::create_button("edit_all_thumbs", "javascript:we_cmd('editThumbs','top.opener.location = top.opener.location;');", false);

$_thumbs[] = array("headline" => "", "html" => $_thumbnails->getHtml() . '<p align="right">' . $editbut . '</p>', "space" => 0);


$iframe = '<iframe name="showthumbs" id="showthumbs" src="' . WEBEDITION_DIR . 'showThumb.php?u=' . $uniqid . '&t=' . $we_transaction . '&id=' . $selectedID . '" width="340" height="130"></iframe>';

$_thumbs[] = array("headline" => "", "html" => $iframe, "space" => 0);

$addbut = we_html_button::create_button("add", "javascript:add_thumbnails();", false, 0, 0, "", "", !$_enabled_buttons, false);
$cancelbut = we_html_button::create_button("cancel", "javascript:top.close();");

$buttons = we_html_button::position_yes_no_cancel($addbut, null, $cancelbut);

$dialog = we_html_multiIconBox::getHTML("", "100%", $_thumbs, 30, $buttons, -1, "", "", false, g_l('weClass', '[thumbnails]'));
echo we_html_element::htmlBody(array("class" => "weDialogBody", "style" => "overflow: hidden;", "onload" => "top.focus();"), $dialog) . "</html>";
