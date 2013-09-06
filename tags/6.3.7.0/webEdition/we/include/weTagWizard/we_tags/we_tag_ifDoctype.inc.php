<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_sqlRowAttribute('doctypes',DOC_TYPES_TABLE, false, 'DocType', 'DocType', 'DocType', '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
 new weTagDataOption('self'),
 new weTagDataOption('listview'),
), false, '');
