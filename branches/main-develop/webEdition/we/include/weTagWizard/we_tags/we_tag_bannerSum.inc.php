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

$this->Attributes[] = new we_tagData_selectAttribute('type', [new we_tagData_option('views'),
	new we_tagData_option('clicks'),
	new we_tagData_option('rate'),
	], true, '');
