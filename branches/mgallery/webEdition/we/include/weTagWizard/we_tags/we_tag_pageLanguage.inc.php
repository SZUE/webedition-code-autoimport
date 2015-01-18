<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new weTagData_selectAttribute('type', array(new weTagDataOption('complete'),
	new weTagDataOption('language'),
	new weTagDataOption('language_name'),
	new weTagDataOption('country'),
	new weTagDataOption('country_name'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('case', array(new weTagDataOption('unchanged'),
	new weTagDataOption('uppercase'),
	new weTagDataOption('lowercase'),
	), false, '');
$this->Attributes[] = new weTagData_selectAttribute('doc', array(new weTagDataOption('self'),
	new weTagDataOption('top'),
	), false, '');
