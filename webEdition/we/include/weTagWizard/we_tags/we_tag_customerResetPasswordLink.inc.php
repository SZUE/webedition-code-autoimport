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
$this->Groups = array();
$this->Module = 'customer';

$this->Attributes = array(
	new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, 'customer'),
	new weTagData_textAttribute('host', false, 'customer'),
	new weTagData_selectAttribute('plain', weTagData_selectAttribute::getTrueFalse(), false, 'customer'),
);
