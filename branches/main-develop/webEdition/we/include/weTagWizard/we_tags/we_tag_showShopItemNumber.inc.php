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

$this->Attributes = [new weTagData_textAttribute('shopname', true, ''),
	$this->Attributes[] = new weTagData_choiceAttribute('type', [new weTagDataOption('select'),
	new weTagDataOption('textinput'),
	new weTagDataOption('print'),
	], false, false, ''),
	new weTagData_textAttribute('start', false, ''),
	new weTagData_textAttribute('stop', false, ''),
	new weTagData_selectAttribute('floatquantities', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('step', false, '1'),
	new weTagData_choiceAttribute('num_format', [new weTagDataOption('german'),
		new weTagDataOption('french'),
		new weTagDataOption('english'),
		new weTagDataOption('swiss'),
		], false, false, ''),
];
