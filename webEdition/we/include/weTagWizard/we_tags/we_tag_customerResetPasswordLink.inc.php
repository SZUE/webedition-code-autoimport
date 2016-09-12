<?php
$this->NeedsEndTag = true;
$this->Groups = [];
$this->Module = 'customer';

$this->Attributes = [new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, 'customer'),
	new weTagData_textAttribute('host', false, 'customer'),
	new weTagData_selectAttribute('plain', weTagData_selectAttribute::getTrueFalse(), false, 'customer'),
];
