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
$this->Groups[] = 'navigation_tags';
$this->Module = 'navigation';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<a href="<we:navigationField name="href" />"><we:navigationField name="text" /></a><br />';

$this->Attributes = [
	new we_tagData_textAttribute('navigationname', false, ''),
	new we_tagData_selectAttribute('type', [new we_tagData_option(we_tagData_selectorAttribute::FOLDER),
		new we_tagData_option('item'),
		], true, ''),
	new we_tagData_textAttribute('level', false, ''),
	new we_tagData_selectAttribute('current', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_choiceAttribute('position', [new we_tagData_option('first'),
		new we_tagData_option('odd'),
		new we_tagData_option('even'),
		new we_tagData_option('last'),
		], false, false, ''),
];
