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
//$this->Groups[] = 'if_tags';
$this->Module = 'users';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Attributes[] = new weTagData_selectAttribute('type', [new weTagDataOption('username'),
	new weTagDataOption('forename'),
	new weTagDataOption('surname'),
	new weTagDataOption('name'),
	new weTagDataOption('initials'),
	new weTagDataOption('salutation'),
	new weTagDataOption('email'),
	new weTagDataOption('address'),
	new weTagDataOption('zip'),
	new weTagDataOption('city'),
	new weTagDataOption('state'),
	new weTagDataOption('country'),
	new weTagDataOption('telephone'),
	new weTagDataOption('fax'),
	new weTagDataOption('mobile'),
	new weTagDataOption('description'),
		], false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', [new weTagDataOption('self'),
	new weTagDataOption('top'),
	new weTagDataOption('listview'),
		], false, '');
$this->Attributes[] = new weTagData_selectAttribute('creator', weTagData_selectAttribute::getTrueFalse(), false, '');
