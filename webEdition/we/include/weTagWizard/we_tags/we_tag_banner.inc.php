<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/

$this->Module = 'banner';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);


$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
$this->Attributes[] = new we_tagData_textAttribute('width', false, '');
$this->Attributes[] = new we_tagData_textAttribute('height', false, '');
$this->Attributes[] = new we_tagData_textAttribute('paths', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('type', [new we_tagData_option('js'),
	new we_tagData_option('iframe'),
	new we_tagData_option('cookie'),
	new we_tagData_option('pixel'),
	], false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
	new we_tagData_option('_parent'),
	new we_tagData_option('_self'),
	new we_tagData_option('_blank'),
	], false, false, '');
$this->Attributes[] = new we_tagData_selectAttribute('link', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('clickscript', false, '');
$this->Attributes[] = new we_tagData_textAttribute('getscript', false, '');
$this->Attributes[] = new we_tagData_textAttribute('page', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
