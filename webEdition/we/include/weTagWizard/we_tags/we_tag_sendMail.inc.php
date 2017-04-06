<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 */
$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [
	new we_tagData_textAttribute('id', false, ''),
	new we_tagData_textAttribute('subject', false, ''),
	new we_tagData_textAttribute('recipient', true, ''),
	new we_tagData_textAttribute('recipientCC', false, ''),
	new we_tagData_textAttribute('recipientBCC', false, ''),
	new we_tagData_textAttribute('from', true, ''),
	new we_tagData_textAttribute('reply', false, ''),
	new we_tagData_selectAttribute('mimetype', [
		new we_tagData_option(we_mail_mime::TYPE_TEXT),
		new we_tagData_option(we_mail_mime::TYPE_HTML),
		], false, ''),
	new we_tagData_textAttribute('charset', false, ''),
	new we_tagData_selectAttribute('includeimages', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('usebasehref', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('useformmailLog', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('useformmailBlock', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
