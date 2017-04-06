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
$this->Groups = [];
$this->Module = 'customer';

$this->Attributes = [new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, 'customer'),
	new we_tagData_textAttribute('host', false, 'customer'),
	new we_tagData_selectAttribute('plain', we_tagData_selectAttribute::getTrueFalse(), false, 'customer'),
];
