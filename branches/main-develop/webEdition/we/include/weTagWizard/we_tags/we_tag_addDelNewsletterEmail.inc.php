<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
$this->Module = 'newsletter';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('17', 'path', false, '');
$this->Attributes[] = new weTagData_typeAttribute('806', 'type', array(new weTagDataOption('csv', false, '', array('id806_type','id17_path','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id808_mailingList','id825_recipientCC','id826_recipientBCC','id814_includeimages'), array('id17_path')), new weTagDataOption('customer', false, 'customer', array('id806_type','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id807_fieldGroup','id808_mailingList','id825_recipientCC','id826_recipientBCC','id814_includeimages'), array()),new weTagDataOption('emailonly', false, '', array('id806_type','id18_doubleoptin','id19_expiredoubleoptin','id20_mailid','id21_subject','id22_from','id23_id','id200_adminmailid','id210_adminsubject','id300_adminemail','id814_includeimages'), array('id200_adminmailid','id210_adminsubject','id300_adminemail')) ), false, 'newsletter');
$this->Attributes[] = new weTagData_textAttribute('808', 'mailingList', false, '');
$this->Attributes[] = new weTagData_selectAttribute('18', 'doubleoptin', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
$this->Attributes[] = new weTagData_textAttribute('19', 'expiredoubleoptin', false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('20', 'mailid',FILE_TABLE, 'text/webedition', false, ''); }
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('200', 'adminmailid',FILE_TABLE, 'text/webedition', false, '');}
$this->Attributes[] = new weTagData_textAttribute('21', 'subject', false, '');
$this->Attributes[] = new weTagData_textAttribute('210', 'adminsubject', false, '');
$this->Attributes[] = new weTagData_textAttribute('300', 'adminemail', false, '');
$this->Attributes[] = new weTagData_textAttribute('22', 'from', false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('23', 'id',FILE_TABLE, 'text/webedition', false, ''); }
$this->Attributes[] = new weTagData_textAttribute('807', 'fieldGroup', false, '');
$this->Attributes[] = new weTagData_textAttribute('825', 'recipientCC', false, '');
$this->Attributes[] = new weTagData_textAttribute('826', 'recipientBCC', false, '');
$this->Attributes[] = new weTagData_selectAttribute('814', 'includeimages', array(new weTagDataOption('true', false, ''), new weTagDataOption('false', false, '')), false, '');
