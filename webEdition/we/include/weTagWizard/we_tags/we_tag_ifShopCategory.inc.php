<?php

//NOTE you are inside the constructor of weTagData.class.php

$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

	$options = array();
	$opts = we_shop_category::getFieldFromAll('Path');
	foreach($opts as $k => $v){
		$options[] = new weTagDataOption($v, $k);
	}
	$this->Attributes[] = new weTagData_selectAttribute('id', $options, true);
