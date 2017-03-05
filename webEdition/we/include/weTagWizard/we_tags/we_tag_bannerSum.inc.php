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

$this->Attributes[] = new weTagData_selectAttribute('type', [new weTagDataOption('views'),
	new weTagDataOption('clicks'),
	new weTagDataOption('rate'),
	], true, '');
