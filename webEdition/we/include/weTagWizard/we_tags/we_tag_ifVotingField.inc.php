<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Groups[] = 'if_tags';
$this->Module = 'voting';

$this->Attributes[] = new we_tagData_selectAttribute('name', [new we_tagData_option('question'),
	new we_tagData_option('answer'),
	new we_tagData_option('result'),
	new we_tagData_option('id'),
	new we_tagData_option('date'),
 ], true, '');
$this->Attributes[] = new we_tagData_selectAttribute('type', [new we_tagData_option('text'),
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
 ], false, '');
$this->Attributes[] = new we_tagData_textAttribute('match', true, '');
$this->Attributes[] = new we_tagData_selectAttribute('operator', [new we_tagData_option('equal'),
	new we_tagData_option('less'),
	new we_tagData_option('less|equal'),
	new we_tagData_option('greater'),
	new we_tagData_option('greater|equal'),
	new we_tagData_option('contains'),
 ], false, '');
