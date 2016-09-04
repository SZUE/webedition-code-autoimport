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
$we_doc = we_document::initDoc($we_dt);
$thumbs = [];

if(!($we_doc instanceof we_imageDocument)){
	exit("ERROR: Couldn't initialize we_imageDocument object");
}

echo we_html_tools::getHtmlTop(g_l('weClass', '[thumbnails]'), '', '',
	we_html_element::jsScript(JS_DIR . 'add_thumb.js', '', ['id' => 'loadVarAdd_thumb', 'data-thumbData' => setDynamicVar([
			'transaction' => $we_transaction
])]));

// SELECT Box with thumbnails
$thumbnails = new we_html_select(["multiple" => "multiple", "name" => "Thumbnails", "id" => "Thumbnails", 'class' => 'defaultfont', "size" => 6, "style" => "width: 340px;", "onchange" => "select_thumbnails(this);"]);
$DB_WE->query('SELECT ID,Name,Format,description FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

$thumbnail_counter_firsttime = true;

$doc_thumbs = ($we_doc->Thumbs == -1) ? [] : explode(',', $we_doc->Thumbs);

$selectedID = 0;
$enabled_buttons = false;
while($DB_WE->next_record()){
	if(!in_array($DB_WE->f('ID'), $doc_thumbs)){
		$enabled_buttons = true;
		$thumbnail_counter = $DB_WE->f('ID');
		if(we_base_imageEdit::is_imagetype_read_supported(we_base_imageEdit::$GDIMAGE_TYPE[strtolower($we_doc->Extension)]) && we_base_imageEdit::is_imagetype_supported(trim($DB_WE->f("Format")) ? $DB_WE->f("Format") : we_base_imageEdit::$GDIMAGE_TYPE[strtolower($we_doc->Extension)])){
			$thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'));
		}
		if($thumbnail_counter_firsttime){
			$selectedID = $DB_WE->f("ID");
			$thumbnails->selectOption($selectedID);
		}

		$thumbnail_counter_firsttime = false;
	}
}

$editbut = we_html_button::create_button('edit_all_thumbs', "javascript:we_cmd('editThumbs','top.opener.location = top.opener.location;');", false);

$thumbs[] = array("headline" => "", "html" => $thumbnails->getHtml() . '<p style="text-align:right">' . $editbut . '</p>');


$iframe = '<iframe name="showthumbs" id="showthumbs" src="' . WEBEDITION_DIR . 'showThumb.php?u=' . $uniqid . '&t=' . $we_transaction . '&id=' . $selectedID . '" style="width:340px;height:130px;"></iframe>';

$thumbs[] = array("headline" => "", "html" => $iframe);

$addbut = we_html_button::create_button(we_html_button::OK, "javascript:add_thumbnails();", false, 0, 0, "", "", !$enabled_buttons, false);
$cancelbut = we_html_button::create_button(we_html_button::CANCEL, "javascript:top.close();");

$buttons = we_html_button::position_yes_no_cancel($addbut, null, $cancelbut);

$dialog = we_html_multiIconBox::getHTML("", $thumbs, 30, $buttons, -1, "", "", false, g_l('weClass', '[thumbnails]'));
echo we_html_element::htmlBody(array('class' => "weDialogBody", "style" => "overflow: hidden;", "onload" => "top.focus();"), $dialog) . "</html>";
