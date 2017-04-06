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
$this->Module = 'shop';

$this->Attributes = [new we_tagData_textAttribute('shopname', true, ''),
	$this->Attributes[] = new we_tagData_choiceAttribute('type', [new we_tagData_option('select'),
	new we_tagData_option('textinput'),
	new we_tagData_option('print'),
	], false, false, ''),
	new we_tagData_textAttribute('start', false, ''),
	new we_tagData_textAttribute('stop', false, ''),
	new we_tagData_selectAttribute('floatquantities', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('step', false, '1'),
	new we_tagData_choiceAttribute('num_format', [new we_tagData_option('german'),
		new we_tagData_option('french'),
		new we_tagData_option('english'),
		new we_tagData_option('swiss'),
		], false, false, ''),
];
