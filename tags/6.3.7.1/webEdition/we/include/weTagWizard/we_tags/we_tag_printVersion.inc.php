<?php
//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined("TEMPLATES_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('tid',TEMPLATES_TABLE, 'text/weTmpl', true, ''); }
$this->Attributes[] = new weTagData_choiceAttribute('target', array(new weTagDataOption('_top'),
 new weTagDataOption('_parent'),
 new weTagDataOption('_self'),
 new weTagDataOption('_blank'),
), false,false, '');
$this->Attributes[] = new weTagData_selectAttribute('link', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('top'),
 new weTagDataOption('self'),
), false, '');
if(defined("FILE_TABLE")) { $this->Attributes[] = new weTagData_selectorAttribute('triggerid',FILE_TABLE, 'text/webedition', false, ''); }
