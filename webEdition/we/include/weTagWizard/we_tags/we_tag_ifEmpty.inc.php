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

$this->Attributes[] = new we_tagData_textAttribute('match', true, '');
$this->Attributes[] = new we_tagData_selectAttribute('type', [new we_tagData_option('img'),
	new we_tagData_option('flashmovie'),
	new we_tagData_option('href'),
	new we_tagData_option('object'),
	new we_tagData_option('binary'),
	new we_tagData_option('checkbox'),
	], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('doc', [new we_tagData_option('self'),
	new we_tagData_option('top'),
	], false, '');
