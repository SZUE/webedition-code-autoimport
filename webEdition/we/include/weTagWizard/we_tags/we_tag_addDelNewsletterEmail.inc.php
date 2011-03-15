<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_textAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_typeAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectAttribute.class.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/weTagWizard/classes/weTagData_selectorAttribute.class.php');

$GLOBALS['weTagWizard']['weTagData']['needsEndtag'] = false;

$GLOBALS['weTagWizard']['attribute']['id17_path'] = new weTagData_textAttribute('17', 'path', false, '');
$GLOBALS['weTagWizard']['attribute']['id806_type'] = new weTagData_typeAttribute('806', 'type', array(new weTagDataOption('csv', false, '', array('id806_type','id17_path','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id808_mailingList','id825_recipientCC','id826_recipientBCC','id814_includeimages'), array('id17_path')), new weTagDataOption('customer', false, 'customer', array('id806_type','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id807_fieldGroup','id808_mailingList','id825_recipientCC','id826_recipientBCC','id814_includeimages'), array()),new weTagDataOption('emailonly', false, '', array('id806_type','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id200_adminmailid','id210_adminsubject','id300_adminemail','id814_includeimages'), array('id200_adminmailid','id210_adminsubject','id300_adminemail')) ), false, 'newsletter');
$GLOBALS['weTagWizard']['attribute']['id808_mailingList'] = new weTagData_textAttribute('808', 'mailingList', false, '');
$GLOBALS['weTagWizard']['attribute']['id18_doubleoptin'] = new weTagData_selectAttribute('18', 'doubleoptin', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$GLOBALS['weTagWizard']['attribute']['id19_expiredoubleoptin'] = new weTagData_textAttribute('19', 'expiredoubleoptin', false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id20_mailid'] = new weTagData_selectorAttribute('20', 'mailid',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id200_adminmailid'] = new weTagData_selectorAttribute('200', 'adminmailid',FILE_TABLE, 'text/webedition', false, '');}
$GLOBALS['weTagWizard']['attribute']['id21_subject'] = new weTagData_textAttribute('21', 'subject', false, '');
$GLOBALS['weTagWizard']['attribute']['id210_adminsubject'] = new weTagData_textAttribute('210', 'adminsubject', false, '');
$GLOBALS['weTagWizard']['attribute']['id300_adminemail'] = new weTagData_textAttribute('300', 'adminemail', false, '');
$GLOBALS['weTagWizard']['attribute']['id22_from'] = new weTagData_textAttribute('22', 'from', false, '');
if(defined("FILE_TABLE")) { $GLOBALS['weTagWizard']['attribute']['id23_id'] = new weTagData_selectorAttribute('23', 'id',FILE_TABLE, 'text/webedition', false, ''); }
$GLOBALS['weTagWizard']['attribute']['id807_fieldGroup'] = new weTagData_textAttribute('807', 'fieldGroup', false, '');
$GLOBALS['weTagWizard']['attribute']['id825_recipientCC'] = new weTagData_textAttribute('825', 'recipientCC', false, '');
$GLOBALS['weTagWizard']['attribute']['id826_recipientBCC'] = new weTagData_textAttribute('826', 'recipientBCC', false, '');
$GLOBALS['weTagWizard']['attribute']['id814_includeimages'] = new weTagData_selectAttribute('814', 'includeimages', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
