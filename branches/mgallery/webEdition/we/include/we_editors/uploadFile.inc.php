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
//TODO: let do we_fileuploade_binaryDocument::processFileRequest() do the job!

require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
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
		$allowedContentTypes = implode(',', we_base_ContentTypes::inst()->getRealContentTypes($contentType));
		$allowedExtensions = we_base_imageEdit::IMAGE_EXTENSIONS;
		break;
	case we_base_ContentTypes::APPLICATION;
		$allowedContentTypes = '';
		$allowedExtensions = '';
		break;
	default:
		$allowedContentTypes = $contentType;
		$allowedExtensions = '';
}

$mode = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 1);
$weFileupload = new we_fileupload_include('we_File', '', '', '', '', true, 'document.forms[0].submit();', '', 330, true, false, 200, $allowedContentTypes, $allowedExtensions, '', '', array(), -1);
$weFileupload->setIsFallback($mode === 'legacy' ? true : true);
$weFileupload->setExternalProgress(true, 'progressbar', true, 120);

if($weFileupload->processFileRequest()){
	$we_File = $weFileupload->getFileNameTemp();
	$maxsize = $weFileupload->getMaxUploadSize();
	$we_maxfilesize_text = sprintf(g_l('newFile', '[max_possible_size]'), we_base_file::getHumanFileSize($maxsize, we_base_file::SZ_MB));

	echo we_html_tools::getHtmlTop(g_l('newFile', '[import_File_from_hd_title]')) .
	STYLESHEET . we_html_element::jsElement('parent.openedWithWE = 1;') . $weFileupload->getJS() . $weFileupload->getCss();

	if(!isset($_SESSION['weS']['we_data'][$we_transaction])){
		$we_alerttext = $we_maxfilesize_text;
		$error = true;
	} else {
		$we_dt = $_SESSION['weS']['we_data'][$we_transaction];
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		if(isset($_FILES['we_File']) && ($_FILES['we_File']['name']) && $_FILES['we_File']['type'] && ((empty($allowedContentTypes)) || (!(strpos($allowedContentTypes, $_FILES['we_File']['type']) === false)))){
			$we_doc->Extension = strtolower((strpos($_FILES['we_File']['name'], '.') > 0) ? preg_replace('/^.+(\..+)$/', '$1', $_FILES['we_File']['name']) : ''); //strtolower for feature 3764
			if(!isset($we_File) || !$we_File){
				$we_File = TEMP_PATH . we_base_file::getUniqueId() . $we_doc->Extension;
				move_uploaded_file($_FILES['we_File']['tmp_name'], $we_File);
			}
			if((!$we_doc->Filename) || (!$we_doc->ID)){
				// Bug Fix #6284
				$we_doc->Filename = preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES["we_File"]["name"]);
				$we_doc->Filename = preg_replace('/^(.+)\..+$/', '$1', $we_doc->Filename);
			}

			$foo = explode('/', $_FILES["we_File"]["type"]);
			$we_doc->setElement('data', $we_File, $foo[0]);

			switch($we_doc->ContentType){
				case we_base_ContentTypes::IMAGE:
					if(!$we_doc->isSvg() && !in_array(we_base_imageEdit::detect_image_type($we_File), we_base_imageEdit::$GDIMAGE_TYPE)){
						$we_alerttext = g_l('alert', '[wrong_file][' . $we_doc->ContentType . ']');
						break;
					}
				//no break;
				case we_base_ContentTypes::FLASH:
					$we_size = $we_doc->getimagesize($we_File);
					$we_doc->setElement('width', $we_size[0], 'attrib');
					$we_doc->setElement('height', $we_size[1], 'attrib');
					$we_doc->setElement('origwidth', $we_size[0], 'attrib');
					$we_doc->setElement('origheight', $we_size[1], 'attrib');
				//no break;
				default:
					$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
					$we_doc->Path = $we_doc->getPath();
					$we_doc->DocChanged = true;

					if($we_doc->Extension === '.pdf'){
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

	$content = '<table class="default">
					<tr><td style="padding-bottom:10px;">' . $weFileupload->getHtmlAlertBoxes() . '</td></tr>
					<tr><td style="padding-bottom:10px;">' . $weFileupload->getHtml() . '</td></tr>';
	if($we_doc->ContentType == we_base_ContentTypes::IMAGE){
		$content .= '<tr><td>' . we_html_forms::checkbox(1, true, "import_metadata", g_l('metadata', '[import_metadata_at_upload]')) . '</td></tr>';
	}
	$content .= '</table>';

	$_buttons = we_html_button::position_yes_no_cancel(
			we_html_button::create_button(we_html_button::UPLOAD, "javascript:" . $weFileupload->getJsBtnCmd('upload'), true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_btn'), "", we_html_button::create_button(we_html_button::CANCEL, "javascript:" . $weFileupload->getJsBtnCmd('cancel'))
	);
	$buttonsTable = new we_html_table(array('class' => 'default', 'style' => 'width:100%;'), 1, 2);
	$buttonsTable->setCol(0, 0, array(), we_html_element::htmlDiv(array('id' => 'progressbar', 'style' => 'display:none;padding-left:10px')));
	$buttonsTable->setCol(0, 1, array('style' => 'text-align:right'), $_buttons);
	$_buttons = $buttonsTable->getHtml();
	?>

	<script><!--
	<?php
	if($we_alerttext){
		echo we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
		if($error){
			?>
			top.close();
			<?php
		}
	}

	if(!empty($we_File) && !$we_alerttext){
		?>
		opener.we_cmd("update_file");
		_EditorFrame = opener.top.weEditorFrameController.getActiveEditorFrame();
		_EditorFrame.getDocumentReference().frames.editHeader.we_setPath("<?php echo $we_doc->Path; ?>", "<?php echo $we_doc->Text; ?>",<?php echo $we_doc->ID; ?>, "published");
		self.close();
	<?php } ?>
	//-->
	</script>
	</head>

	<body class="weDialogBody" onload="self.focus();">
		<div style="text-align:center">
			<form method="post" enctype="multipart/form-data">
				<?php
				echo we_html_element::htmlHidden("we_transaction", $we_transaction) .
				we_html_tools::htmlDialogLayout($content, g_l('newFile', '[import_File_from_hd_title]'), $_buttons);
				?>
			</form>
		</div>
	</body>

	</html>
	<?php
}