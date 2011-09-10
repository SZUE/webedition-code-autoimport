<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes[] = new weTagData_sqlRowAttribute('id',WE_SHOP_VAT_TABLE, false, 'id', 'text', 'text', '');
//$this->Attributes[] = new weTagData_textAttribute('cachelifetime', false, '');
