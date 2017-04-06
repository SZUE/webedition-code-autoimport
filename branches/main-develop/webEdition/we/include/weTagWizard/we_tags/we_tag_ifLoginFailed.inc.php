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
$this->Attributes = [new we_tagData_choiceAttribute('type', [new we_tagData_option('all'),
		new we_tagData_option('credentials'),
		new we_tagData_option('retrylimit'),
		], false, false, '')
];
