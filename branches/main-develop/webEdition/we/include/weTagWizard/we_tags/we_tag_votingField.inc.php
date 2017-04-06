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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'voting';

$this->Attributes = [
	new we_tagData_selectAttribute('name', [new we_tagData_option('question'),
		new we_tagData_option('answer'),
		new we_tagData_option('result'),
		new we_tagData_option('id'),
		new we_tagData_option('date'),
		], true, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option('text'),
		new we_tagData_option('radio'),
		new we_tagData_option('checkbox'),
		new we_tagData_option('select'),
		new we_tagData_option('count'),
		new we_tagData_option('percent'),
		new we_tagData_option('total'),
		new we_tagData_option('answer'),
		new we_tagData_option('voting'),
		new we_tagData_option('textinput'),
		new we_tagData_option('textarea'),
		new we_tagData_option('image'),
		new we_tagData_option('media'),
		], false, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('format', false, ''),
	new we_tagData_choiceAttribute('num_format', [new we_tagData_option('german'),
		new we_tagData_option('french'),
		new we_tagData_option('english'),
		new we_tagData_option('swiss'),
		], false, false, ''),
	new we_tagData_textAttribute('precision', false, ''),
];
