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
$this->Groups[] = 'if_tags';
$this->Module = 'customer';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes = [new weTagData_choiceAttribute('type', [new weTagDataOption('all'),
		new weTagDataOption('credentials'),
		new weTagDataOption('retrylimit'),
		], false, false, '')
];
