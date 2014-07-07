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

// init document
$we_alerttext = '';
$error = false;

if(isset($_SESSION['weS']['we_data'][$we_transaction])){
	$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
	include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');
	$isWeDoc = true;
	$contentType = $we_doc->ContentType;
} else {
	$isWeDoc = false;
	$contentType = weRequest('raw', 'we_cmd', '', 1);
}

switch($contentType){
	case we_base_ContentTypes::IMAGE;
		$allowedContentTypes = we_base_imageEdit::IMAGE_CONTENT_TYPES;
		break;
	case we_base_ContentTypes::APPLICATION;
		$allowedContentTypes = '';
		break;
	default:
		$allowedContentTypes = $contentType;
}

$inputTypeFile = new we_fileupload_uploader_include('we_File', 'top', '', 330, true, true, array(), $allowedContentTypes, '', '', '', array(), -1);
//$inputTypeFile->setExternalProgressbar(true, 'progressbar', true, 'top.', 120, '');
$we_File = $inputTypeFile->processFileRequest();

$maxsize = $inputTypeFile->getMaxUploadSize();
$we_maxfilesize_text = sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB));

echo we_html_tools::getHtmlTop(g_l('newFile', "[import_File_from_hd_title]")) .
 STYLESHEET . $inputTypeFile->getJS() . $inputTypeFile->getCss();

if(!$isWeDoc){
	$we_alerttext = $we_maxfilesize_text;
	$error = true;
} else {
	if(isset($_FILES['we_File']) && ($_FILES['we_File']['name']) && $_FILES['we_File']['type'] && ((empty($allowedContentTypes)) || (!(strpos($allowedContentTypes, $_FILES['we_File']['type']) === false)))){
		$we_doc->Extension = strtolower((strpos($_FILES['we_File']['name'], '.') > 0) ? preg_replace('/^.+(\..+)$/', "\\1", $_FILES['we_File']['name']) : ''); //strtolower for feature 3764
		if(!isset($we_File) || !$we_File){
			$we_File = TEMP_PATH . we_base_file::getUniqueId() . $we_doc->Extension;
			move_uploaded_file($_FILES['we_File']['tmp_name'], $we_File);
		}
		if((!$we_doc->Filename) || (!$we_doc->ID)){
			// Bug Fix #6284
			$we_doc->Filename = preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES["we_File"]["name"]);
			$we_doc->Filename = preg_replace('/^(.+)\..+$/', '\\1', $we_doc->Filename);
		}

		$foo = explode('/', $_FILES["we_File"]["type"]);
		$we_doc->setElement('data', $we_File, $foo[0]);

		if($we_doc->ContentType == we_base_ContentTypes::IMAGE && !$we_doc->isSvg() && !in_array(we_base_imageEdit::detect_image_type($we_File), we_base_imageEdit::$GDIMAGE_TYPE)){
			$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
		} else {

			if($we_doc->ContentType == we_base_ContentTypes::IMAGE || $we_doc->ContentType == we_base_ContentTypes::FLASH){
				$we_size = $we_doc->getimagesize($we_File);
				$we_doc->setElement('width', $we_size[0], 'attrib');
				$we_doc->setElement('height', $we_size[1], 'attrib');
				$we_doc->setElement('origwidth', $we_size[0]);
				$we_doc->setElement('origheight', $we_size[1]);
			}
			$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
			$we_doc->Path = $we_doc->getPath();
			$we_doc->DocChanged = true;

			if($we_doc->Extension == '.pdf'){
				$we_doc->setMetaDataFromFile($we_File);
			}

			$_SESSION['weS']['we_data']["tmpName"] = $we_File;
			if(we_base_request::_(we_base_request::BOOL, 'import_metadata')){
				$we_doc->importMetaData();
			}
			$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]); // save the changed object in session
		}
	} else if(isset($_FILES['we_File']['name']) && !empty($_FILES['we_File']['name'])){
		$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
	} else if(isset($_FILES['we_File']['name']) && empty($_FILES['we_File']['name'])){
		$we_alerttext = g_l('alert', '[no_file_selected]');
	}
}

$content = '<table border="0" cellpadding="0" cellspacing="0">' .
	($maxsize ? ('<tr><td>' . we_html_tools::htmlAlertAttentionBox(
			$we_maxfilesize_text, we_html_tools::TYPE_ALERT, 390) . '</td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>') : '') . '
				<tr><td>' . $inputTypeFile->getHtml() . '</td></tr>
				<tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>';
if($we_doc->ContentType == we_base_ContentTypes::IMAGE){
	$content .= '<tr><td>' . we_html_forms::checkbox(1, true, "import_metadata", g_l('metadata', "[import_metadata_at_upload]")) . '</td></tr>';
}
$content .= '</table>';


$_buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button("upload", "javascript:" . we_fileupload_uploader_include::getJsSubmitCall("top", 0, "document.forms[0].submit()")), "", we_html_button::create_button("cancel", "javascript:self.close();"));
?>

<script type="text/javascript"><!--
<?php
if($we_alerttext){
	print we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
	if($error){
		?>
		top.close();
		<?php
	}
}

if(isset($we_File) && $we_File && !$we_alerttext){
	?>
	opener.we_cmd("update_file");
	_EditorFrame = opener.top.weEditorFrameController.getActiveEditorFrame();
	_EditorFrame.getDocumentReference().frames[0].we_setPath("<?php print $we_doc->Path; ?>", "<?php print $we_doc->Text; ?>");
	self.close();
<?php } ?>
//-->
</script>
</head>

<body class="weDialogBody" onload="self.focus();">
	<center>
		<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="we_transaction" value="<?php print $we_transaction ?>" />
			<?php print we_html_tools::htmlDialogLayout($content, g_l('newFile', "[import_File_from_hd_title]"), $_buttons); ?>
		</form>
	</center>
</body>

</html>