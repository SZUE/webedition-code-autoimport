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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
we_html_tools::protect();
we_html_tools::htmlTop(g_l('thumbnails', '[thumbnails]'));

$reloadUrl = getServerUrl(true) . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=editThumbs';

// Check if we need to create a new thumbnail
if(isset($_GET['newthumbnail']) && $_GET['newthumbnail'] != ''){
	if(we_hasPerm('ADMINISTRATOR')){
		$DB_WE->query('INSERT INTO ' . THUMBNAILS_TABLE . ' SET Name ="' . $DB_WE->escape($_GET['newthumbnail']) . '"');
		header('Location: ' . $reloadUrl . '&id=' . f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' WHERE Name="' . $DB_WE->escape($_GET['newthumbnail']) . '"', 'ID', $DB_WE));
		exit();
	}
}

// Check if we need to delete a thumbnail
if(isset($_GET['deletethumbnail']) && $_GET['deletethumbnail'] != ''){
	if(we_hasPerm('ADMINISTRATOR')){
		// Delete thumbnails in filesystem
		we_thumbnail::deleteByThumbID($_GET['deletethumbnail']);

		// Delete entry in database
		$DB_WE->query('DELETE FROM ' . THUMBNAILS_TABLE . ' WHERE ID=' . intval($_GET['deletethumbnail']));

		header('Location: ' . $reloadUrl);
		exit();
	}
}

// Check which thumbnail to work with
if(!isset($_GET['id']) || $_GET['id'] == ''){
	$tmpid = f('SELECT ID FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name LIMIT 1', 'ID', $DB_WE);

	$_GET['id'] = $tmpid ? $tmpid : -1;
}

function getFooter(){
	$_javascript = we_html_element::jsElement('
function we_save() {
	top.we_thumbnails.document.getElementById("thumbnails_dialog").style.display = "none";
	top.we_thumbnails.document.getElementById("thumbnails_save").style.display = "";
	top.we_thumbnails.document.we_form.save_thumbnails.value = "true";
	top.we_thumbnails.document.we_form.submit();
}');

	return $_javascript .
		we_html_element::htmlDiv(array('class' => 'weDialogButtonsBody', 'style' => 'height:100%'), we_button::position_yes_no_cancel(we_button::create_button('save', 'javascript:we_save();'), '', we_button::create_button("close", "javascript:" . ((isset($_REQUEST["closecmd"]) && $_REQUEST["closecmd"]) ? ($_REQUEST["closecmd"] . ';') : '') . 'top.close()'), 10, '', '', 0));
}

//  check if gd_lib is installed ...
if(we_image_edit::gd_version() > 0){

	echo
	we_html_element::jsElement('self.focus();') .
	we_html_element::jsScript(JS_DIR . 'keyListener.js') .
	we_html_element::jsElement('
    			function closeOnEscape() {
					return true;

				}

				function saveOnKeyBoard() {
					window.frames[1].we_save();
					return true;

				}') . STYLESHEET . '</head>' .
	we_html_element::htmlBody(array('style' => 'margin: 0px;position:fixed;top:0px;left:0px;right:0px;bottom:0px;border:0px none;text-align:center;')
		, we_html_element::htmlDiv(array('style' => 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;')
			, we_html_element::htmlExIFrame('we_thumbnails', WE_INCLUDES_PATH . 'we_editors/we_thumbnails.inc.php', 'position:absolute;top:0px;bottom:40px;left:0px;right:0px;overflow: hidden;') .
			we_html_element::htmlExIFrame('we_thumbnails_footer', getFooter(), 'position:absolute;height:40px;bottom:0px;left:0px;right:0px;overflow: hidden;')
	)) . '</html>';
} else{ //  gd_lib is not installed - show error
	print STYLESHEET . '</head><body class="weDialogBody">';


	$parts = array(
		array(
			'headline' => '',
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('importFiles', '[add_description_nogdlib]'), we_html_tools::TYPE_INFO, 440),
			'space' => 0
		)
	);
	print we_multiIconBox::getHTML('thumbnails', '100%', $parts, 30, '', -1, '', '', false, g_l('thumbnails', '[thumbnails]'));
}
