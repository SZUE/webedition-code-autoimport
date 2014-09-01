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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

function weFile($f){
	return f('SELECT 1 FROM ' . FILE_TABLE . " WHERE Path='" . $GLOBALS['DB_WE']->escape($f) . "'");
}

$weFileupload = new we_fileupload_include('we_uploadFile', '', '', 'we_form', '', 'document.forms["we_form"].submit()', '', 330, true, false, 0);
$weFileupload->setExternalProgress(true, 'progressbar', true, 120);

if($weFileupload->processFileRequest()){
	$tempName = $weFileupload->getFileNameTemp();
	echo we_html_tools::getHtmlTop() .
		STYLESHEET .
		$weFileupload->getCss() . $weFileupload->getJs();

	$path = we_base_request::_(we_base_request::FILE,'pat');
	$cpat = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'] . $path);

	$we_alerttext = "";

	if(isset($_FILES['we_uploadFile'])){
		$overwrite = we_base_request::_(we_base_request::BOOL,"overwrite");

		if(!$tempName){
			$tempName = TEMP_PATH . we_base_file::getUniqueId();
			move_uploaded_file($_FILES['we_uploadFile']['tmp_name'], $tempName);
		}

		if(file_exists($cpat . "/" . $_FILES['we_uploadFile']["name"])){
			if($overwrite){
				if(weFile($path . "/" . $_FILES['we_uploadFile']["name"])){
					$we_alerttext = g_l('fileselector', "[can_not_overwrite_we_file]");
				}
			} else {
				$z = 0;

				if(preg_match('|^(.+)(\.[^\.]+)$|', $_FILES['we_uploadFile']["name"], $regs)){
					$extension = $regs[2];
					$filename = $regs[1];
				} else {
					$extension = "";
					$filename = $_FILES['we_uploadFile']["name"];
				}

				$footext = $filename . "_" . $z . $extension;
				while(file_exists($cpat . "/" . $footext)){
					$z++;
					$footext = $filename . "_" . $z . $extension;
				}
				$_FILES['we_uploadFile']["name"] = $footext;
			}
		}
		if(!$we_alerttext){
			copy($tempName, str_replace(array('\\', '//'), '/', $cpat . '/' . $_FILES['we_uploadFile']['name']));
		}
		we_base_file::deleteLocalFile($tempName);
	}
	$maxsize = getUploadMaxFilesize(false);

	$yes_button = we_html_button::create_button("upload", "javascript:if(!document.forms['we_form'].elements['we_uploadFile'].value) { " . we_message_reporting::getShowMessageCall(g_l('fileselector', "[edit_file_nok]"), we_message_reporting::WE_MESSAGE_ERROR) . "} else {" . $weFileupload->getJsBtnCmdStatic($btn = 'upload', '', "document.forms['we_form'].submit()") . "}", true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_btn');
	$cancel_button = we_html_button::create_button("cancel", "javascript:" . $weFileupload->getJsBtnCmdStatic('cancel'));
	$buttons = we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button);
	$buttonsTable = new we_html_table(array('cellspacing' => 0, 'cellpadding' => 0, 'style' => 'border-width:0px;width:100%;'), 1, 2);
	$buttonsTable->setCol(0, 0, $attribs = array(), we_html_element::htmlDiv(array('id' => 'progressbar', 'style' => 'display:none;padding-left:10px')));
	$buttonsTable->setCol(0, 1, $attribs = array('align' => 'right'), $buttons);

	$content = '<table border="0" cellpadding="0" cellspacing="0">' .
			($maxsize ? ('<tr><td>' . $weFileupload->getHtmlAlertBoxes() . '</td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>') : '') . '
				<tr><td>' . $weFileupload->getHTML() . '</td></tr><tr><td>' . we_html_tools::getPixel(2, 10) . '</td></tr>
				<tr><td class="defaultfont">' . g_l('newFile', '[caseFileExists]') . '</td></tr><tr><td>' .
			we_html_forms::radiobutton("1", true, "overwrite", g_l('newFile', '[overwriteFile]')) .
			we_html_forms::radiobutton("0", false, "overwrite", g_l('newFile', '[renameFile]')) . '</td></tr></table>';

	$content = we_html_tools::htmlDialogLayout($content, g_l('newFile', '[import_File_from_hd_title]'), $buttonsTable->getHTML());
	?>
	<script type="text/javascript"><!--
		self.focus();
	<?php if(isset($_FILES['we_uploadFile']) && (!$we_alerttext)){ ?>
			opener.top.fscmd.selectFile('<?php print $_FILES['we_uploadFile']['name']; ?>');
			opener.top.fscmd.selectDir();
			self.close();
		<?php
	} elseif($we_alerttext){
		echo we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
	}
	?>
	//-->
	</script>
	</head>
	<body class="weDialogBody" onload="self.focus();"><center>
		<input type="hidden" name="pat" value="<?php echo $path; ?>" />
		<form method="post" enctype="multipart/form-data" name="we_form">
			<?php echo $content; ?>
		</form>
	</center>
	</body>
	</html>
<?php } ?>