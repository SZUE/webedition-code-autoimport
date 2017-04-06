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
$this->Attributes[] = new we_tagData_selectAttribute('type', [new we_tagData_option('username'),
	new we_tagData_option('forename'),
	new we_tagData_option('surname'),
	new we_tagData_option('name'),
	new we_tagData_option('initials'),
	new we_tagData_option('salutation'),
	new we_tagData_option('email'),
	new we_tagData_option('address'),
	new we_tagData_option('zip'),
	new we_tagData_option('city'),
	new we_tagData_option('state'),
	new we_tagData_option('country'),
	new we_tagData_option('telephone'),
	new we_tagData_option('fax'),
	new we_tagData_option('mobile'),
	new we_tagData_option('description'),
		], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	new we_tagData_option('listview'),
		], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('creator', we_tagData_selectAttribute::getTrueFalse(), false, '');
