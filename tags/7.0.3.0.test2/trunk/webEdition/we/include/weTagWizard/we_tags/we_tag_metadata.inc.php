<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->DefaultValue = '<we:field name="NameOfField" />';

$this->Attributes[] = new weTagData_textAttribute('name', false, '');
$this->Attributes[] = new weTagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
