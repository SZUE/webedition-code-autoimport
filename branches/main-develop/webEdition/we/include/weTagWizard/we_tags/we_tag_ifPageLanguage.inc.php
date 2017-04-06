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
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$locales = [];
foreach($GLOBALS["weFrontendLanguages"] as $lv){
	$locales[] = new we_tagData_option($lv);
}

$this->Attributes = [
	new we_tagData_choiceAttribute('match', $locales, false, true, ''),
	new we_tagData_selectAttribute('doc', [new we_tagData_option('top'),
		new we_tagData_option('self'),
		], false, ''),
];
