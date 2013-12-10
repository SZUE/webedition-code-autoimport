<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = 'object';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_textAttribute('name', true, '');
$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('text'),
	new weTagDataOption('date'),
	new weTagDataOption('img'),
	new weTagDataOption('flashmovie'),
	new weTagDataOption('href'),
	new weTagDataOption('link'),
	new weTagDataOption('day'),
	new weTagDataOption('dayname'),
	new weTagDataOption('month'),
	new weTagDataOption('monthname'),
	new weTagDataOption('year'),
	new weTagDataOption('select'),
	new weTagDataOption('binary'),
	new weTagDataOption('float'),
	new weTagDataOption('int'),
	new weTagDataOption('shopVat'),
	new weTagDataOption('checkbox'),
	), true, '');
$this->Attributes[] = new weTagData_textAttribute('match', true, '');
$this->Attributes[] = new weTagData_selectAttribute('operator', array(new weTagDataOption('equal'),
	new weTagDataOption('less'),
	new weTagDataOption('less|equal'),
	new weTagDataOption('greater'),
	new weTagDataOption('greater|equal'),
	new weTagDataOption('contains'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('striphtml', array(new weTagDataOption('false'),
	new weTagDataOption('true'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('usekey', weTagData_selectAttribute::getTrueFalse(), false, '');
