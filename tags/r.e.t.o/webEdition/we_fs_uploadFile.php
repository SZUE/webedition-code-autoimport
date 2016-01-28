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

$we_ContentType = we_base_request::_(we_base_request::STRING, 'ct', (isset($_FILES['we_uploadedFile']['name']) ? getContentTypeFromFile($_FILES['we_uploadedFile']['name']) : ''));
switch($we_ContentType){
	case we_base_ContentTypes::IMAGE;
	case we_base_ContentTypes::VIDEO:
	case we_base_ContentTypes::AUDIO:
		$allowedContentTypes = implode(',', we_base_ContentTypes::inst()->getRealContentTypes($we_ContentType));
		break;
	case we_base_ContentTypes::APPLICATION;
		$allowedContentTypes = '';
		break;
	default:
		$allowedContentTypes = $we_ContentType;
}

$weFileupload = new we_fileupload_include('we_uploadedFile', '', '', '', '', true, 'document.forms[0].submit()', '', 330, true, false, 0, $allowedContentTypes, '', '', 'php, php4, php5, htaccess', array(), -1);
$weFileupload->setExternalProgress(true, 'progressbar', true, 120);

if($weFileupload->processFileRequest()){
	$tempName = $weFileupload->getFileNameTemp();

	echo we_html_tools::getHtmlTop(g_l('newFile', '[import_File_from_hd_title]')) . STYLESHEET;

	$pid = we_base_request::_(we_base_request::INT, 'pid', 0);
	$parts = array();
	$we_alerttext = (!in_workspace($pid, get_ws(FILE_TABLE, false, true), FILE_TABLE, $GLOBALS['DB_WE']) || isset($_FILES['we_uploadedFile']) && !permissionhandler::hasPerm(we_base_ContentTypes::inst()->getPermission(getContentTypeFromFile($_FILES['we_uploadedFile']['name']))) ?
			g_l('alert', '[upload_targetDir_notallowed]') :
			'');
	if((!$we_alerttext) && isset($_FILES['we_uploadedFile']) && $_FILES['we_uploadedFile']['type'] && (($allowedContentTypes === '') || (!(strpos($allowedContentTypes, $_FILES['we_uploadedFile']['type']) === false)))){
		if(!$we_ContentType){
			$we_ContentType = getContentTypeFromFile($_FILES['we_uploadedFile']['name']);
		}
		// initializing $we_doc
		include(WE_INCLUDES_PATH . 'we_editors/we_init_doc.inc.php');

		$overwrite = we_base_request::_(we_base_request::STRING, 'overwrite');
		$overwritten = false;

		if(!$tempName){
			$tempName = TEMP_PATH . we_base_file::getUniqueId();
			move_uploaded_file($_FILES['we_uploadedFile']['tmp_name'], $tempName);
		}

		$tmp_Filename = preg_replace('/[^A-Za-z0-9._-]/', '', $_FILES['we_uploadedFile']['name']);

		$we_doc->Filename = preg_replace('#^(.+)\..+$#', '$1', $tmp_Filename);
		$we_doc->Extension = (strpos($tmp_Filename, '.') > 0) ? preg_replace('#^.+(\..+)$#', '$1', $tmp_Filename) : '';
		$we_doc->Text = $we_doc->Filename . $we_doc->Extension;
		$we_doc->setParentID($pid);
		$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;

		// if file exists we have to see if we should create a new one or overwrite it!
		if(($file_id = f('SELECT ID FROM ' . FILE_TABLE . " WHERE Path='" . $DB_WE->escape($we_doc->Path) . "'"))){
			if($overwrite === 'yes'){
				$tmp = $we_doc->ClassName;
				$we_doc = new $tmp();
				$we_doc->initByID($file_id, FILE_TABLE);
				$overwritten = true;
			} else {
				$z = 0;
				$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
				while(f('SELECT ID FROM ' . FILE_TABLE . " WHERE Text='" . $DB_WE->escape($footext) . "' AND ParentID=" . $pid)){
					$z++;
					$footext = $we_doc->Filename . '_' . $z . $we_doc->Extension;
				}
				$we_doc->Text = $footext;
				$we_doc->Filename = $we_doc->Filename . '_' . $z;
				$we_doc->Path = $we_doc->getParentPath() . (($we_doc->getParentPath() != '/') ? '/' : '') . $we_doc->Text;
			}
		}
		$we_doc->setElement('type', $we_ContentType, 'attrib');
		$foo = explode('/', $_FILES['we_uploadedFile']['type']);
		$we_doc->setElement('data', $tempName, $foo[0]);

		switch($we_ContentType){
			case we_base_ContentTypes::IMAGE:
				if(!$we_doc->isSvg() && !in_array(we_base_imageEdit::detect_image_type($tempName), we_base_imageEdit::$GDIMAGE_TYPE)){
					$we_alerttext = g_l('alert', '[wrong_file][' . $we_ContentType . ']');
					break;
				}
			//no break
			case we_base_ContentTypes::FLASH:
				$we_size = $we_doc->getimagesize($tempName);
				$we_doc->setElement('width', $we_size[0], 'attrib');
				$we_doc->setElement('height', $we_size[1], 'attrib');
				$we_doc->setElement('origwidth', $we_size[0], 'attrib');
				$we_doc->setElement('origheight', $we_size[1], 'attrib');
				if(we_base_request::_(we_base_request::BOOL, 'import_metadata')){
					$we_doc->importMetaData();
				}
			//no break
			default:
				if($we_doc->Extension === '.pdf'){
					$we_doc->setMetaDataFromFile($tempName);
				}

				$we_doc->setElement('filesize', $_FILES['we_uploadedFile']['size'], 'attrib');
				if(($tit = we_base_request::_(we_base_request::STRING, 'img_title')) !== false){
					$we_doc->setElement('title', $tit, 'attrib');
				}
				if(($alt = we_base_request::_(we_base_request::STRING, 'img_alt')) !== false){
					$we_doc->setElement('alt', $alt, 'attrib');
				}
				if(($thumbs = we_base_request::_(we_base_request::INT, 'Thumbnails'))){
					$we_doc->Thumbs = (is_array($thumbs) ?
							makeCSVFromArray($thumbs, true) :
							$thumbs);
				}
				$we_doc->Table = we_base_request::_(we_base_request::TABLE, 'tab');
				$we_doc->Published = time();
				$we_doc->we_save();
				$id = $we_doc->ID;
		}
	} elseif(isset($_FILES['we_uploadedFile'])){
		$we_alerttext = (we_base_file::we_filenameNotValid($_FILES['we_uploadedFile']['name']) ?
				g_l('alert', '[we_filename_notValid]') :
				g_l('alert', '[wrong_file][' . ($we_ContentType ? : 'other') . ']'));
	}

	// find out the smallest possible upload size
	$maxsize = getUploadMaxFilesize(false);

	$yes_button = we_html_button::create_button('upload', 'javascript:' . $weFileupload->getJsBtnCmd('upload'), true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_btn');
	$cancel_button = we_html_button::create_button('cancel', 'javascript:' . $weFileupload->getJsBtnCmd('cancel'));
	$buttonsTable = new we_html_table(array('cellspacing' => 0, 'cellpadding' => 0, 'style' => 'border-width:0px;width:100%;'), 1, 2);
	$buttonsTable->setCol(0, 0, array(), we_html_element::htmlDiv(array('id' => 'progressbar', 'style' => 'display:none;padding-left:10px')));
	$buttonsTable->setCol(0, 1, array('align' => 'right'), we_html_button::position_yes_no_cancel($yes_button, null, $cancel_button));
	$buttons = $buttonsTable->getHtml();

	$parts[] = array('headline' => '', 'html' => $weFileupload->getHtmlAlertBoxes(), 'space' => 0, 'noline' => 1);
	echo $weFileupload->getJS() . $weFileupload->getCss();
	$parts[] = array('headline' => '', 'html' => $weFileupload->getHtml(), "space" => 0);

	//$parts[] = array('headline' => '', 'html' => '<input name="we_uploadedFile" TYPE="file"' . ($allowedContentTypes ? ' ACCEPT="' . $allowedContentTypes . '"' : '') . ' size="35" />', "space" => 0);
	$parts[] = array('headline' => '', 'html' => g_l('newFile', '[caseFileExists]') . '<br/>' . we_html_forms::radiobutton('yes', true, 'overwrite', g_l('newFile', '[overwriteFile]')) .
		we_html_forms::radiobutton('no', false, 'overwrite', g_l('newFile', '[renameFile]')), 'space' => 0);

	if($we_ContentType == we_base_ContentTypes::IMAGE){
		$_thumbnails = new we_html_select(array('multiple' => 'multiple', 'name' => 'Thumbnails[]', 'id' => 'Thumbnails', 'class' => 'defaultfont', 'size' => 6, 'style' => 'width: 330px;'));
		$DB_WE->query('SELECT ID,Name FROM ' . THUMBNAILS_TABLE . ' ORDER BY Name');

		$selectedID = 0;
		$_enabled_buttons = false;
		while($DB_WE->next_record()){
			$_enabled_buttons = true;
			$_thumbnail_counter = $DB_WE->f('ID');
			$_thumbnails->addOption($DB_WE->f('ID'), $DB_WE->f('Name'));
		}

		$parts[] = array('headline' => '', 'html' => we_html_forms::checkbox(1, true, 'import_metadata', g_l('metadata', '[import_metadata_at_upload]')), 'space' => 0);
		$parts[] = array('headline' => '', 'html' => g_l('thumbnails', '[create_thumbnails]') . '<br/>' . $_thumbnails->getHtml(), 'space' => 0);
		$parts[] = array('headline' => '', 'html' => g_l('global', '[title]') . '<br/>' . we_html_tools::htmlTextInput('img_title', 24, '', '', '', 'text', 330), 'space' => 0);
		$parts[] = array('headline' => '', 'html' => g_l('weClass', '[alt]') . '<br/>' . we_html_tools::htmlTextInput('img_alt', 24, '', '', '', 'text', 330), 'space' => 0);
	}
	?>
	<script type="text/javascript"><!--
	<?php
	if($we_alerttext){
		echo we_message_reporting::getShowMessageCall($we_alerttext, we_message_reporting::WE_MESSAGE_ERROR);
		if(!isset($_FILES['we_uploadedFile'])){
			//echo 'this.close();';
		}
	}
	if(isset($_FILES['we_uploadedFile']) && (!$we_alerttext)){
		if($we_doc->ID){
			if(!$overwritten){
				?>
				var ref;
				if (opener.top.opener && opener.top.opener.top.makeNewEntry) {
					ref = opener.top.opener.top;
				} else if (opener.top.opener && opener.top.opener.top.opener && opener.top.opener.top.opener.top.makeNewEntry) {
					ref = opener.top.opener.top.opener.top;
				} else if (opener.top.opener && opener.top.opener.top.opener && opener.top.opener.top.opener.top.opener && opener.top.opener.top.opener.top.opener.top.makeNewEntry) {
					ref = opener.top.opener.top.opener.top.opener.top;
				}
				if (ref.makeNewEntry) {
					ref.makeNewEntry(<?php echo '"' . $we_doc->Icon . '", "' . $we_doc->ID . '", "' . $we_doc->ParentID . '", "' . $we_doc->Text . '", 1, "' . $we_doc->ContentType . '", "' . $we_doc->Table . '"'; ?>);
				}
				<?php
			}
			?>
			opener.top.reloadDir();
			opener.top.unselectAllFiles();
			opener.top.addEntry(<?php echo '"' . $we_doc->ID . '", "' . $we_doc->Icon . '", "' . $we_doc->Text . '", "' . $we_doc->IsFolder . '", "' . $we_doc->Path . '"'; ?>);
			opener.top.doClick(<?php echo $we_doc->ID; ?>, 0);
			setTimeout('opener.top.selectFile(<?php echo $we_doc->ID; ?>)', 200);
		<?php } ?>
		setTimeout('self.close()', 250);
	<?php } ?>
	//-->
	</script>
	</head>
	<body class="weDialogBody" onload="self.focus();" ><center>
			<form method="post" enctype="multipart/form-data">
				<input type="hidden" name="table" value="<?php echo we_base_request::_(we_base_request::TABLE, 'tab'); ?>" />
				<input type="hidden" name="pid" value="<?php echo $pid; ?>" />
				<input type="hidden" name="ct" value="<?php echo $we_ContentType; ?>" />
				<?php echo we_html_multiIconBox::getHTML("", "100%", $parts, 30, $buttons, -1, "", "", false, g_l('newFile', '[import_File_from_hd_title]'), "", 620); ?>
			</form></center>
	</body>
	</html>
<?php } ?>