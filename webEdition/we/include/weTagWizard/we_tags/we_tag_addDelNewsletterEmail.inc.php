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
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$path = new we_tagData_textAttribute('path', false, '');
$mailingList = new we_tagData_textAttribute('mailingList', false, '');
$doubleoptin = new we_tagData_selectAttribute('doubleoptin', we_tagData_selectAttribute::getTrueFalse(), false, '');
$expiredoubleoptin = new we_tagData_textAttribute('expiredoubleoptin', false, '');
$mailid = new we_tagData_selectorAttribute('mailid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$adminmailid = new we_tagData_selectorAttribute('adminmailid', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$subject = new we_tagData_textAttribute('subject', false, '');
$adminsubject = new we_tagData_textAttribute('adminsubject', false, '');
$adminemail = new we_tagData_textAttribute('adminemail', false, '');
$from = new we_tagData_textAttribute('from', false, '');
$id = new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
$fieldGroup = new we_tagData_textAttribute('fieldGroup', false, '');
$recipientCC = new we_tagData_textAttribute('recipientCC', false, '');
$recipientBCC = new we_tagData_textAttribute('recipientBCC', false, '');
$includeimages = new we_tagData_selectAttribute('includeimages', we_tagData_selectAttribute::getTrueFalse(), false, '');

$this->TypeAttribute = new we_tagData_typeAttribute('type', [new we_tagData_option('csv', false, '', [$path, $doubleoptin, $expiredoubleoptin, $mailid, $subject, $from, $id, $mailingList, $recipientCC, $recipientBCC, $adminmailid, $adminsubject, $adminemail, $includeimages], [$path]),
	new we_tagData_option('customer', false, 'customer', [$doubleoptin, $expiredoubleoptin, $mailid, $subject, $from, $id, $fieldGroup, $mailingList, $recipientCC, $recipientBCC, $adminmailid, $adminsubject, $adminemail, $includeimages], []),
	new we_tagData_option('emailonly', false, '', [$doubleoptin, $expiredoubleoptin, $mailid, $subject, $from, $id, $adminmailid, $adminsubject, $adminemail, $includeimages], [$adminmailid, $adminsubject, $adminemail])], false, 'newsletter');


$this->Attributes = [$path, $mailingList, $doubleoptin, $expiredoubleoptin, $mailid, $subject, $adminmailid, $adminsubject, $adminemail,
	$from, $id, $fieldGroup, $recipientCC, $recipientBCC, $includeimages];
