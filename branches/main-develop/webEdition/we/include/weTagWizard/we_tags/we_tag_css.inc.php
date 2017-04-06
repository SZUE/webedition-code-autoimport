<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/

$this->NeedsEndTag = false;
//$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('id', FILE_TABLE, 'text/css', true, '');
}
$this->Attributes[] = new we_tagData_selectAttribute('rel', [new we_tagData_option('stylesheet'),
	new we_tagData_option('alternate stylesheet'),
	], false, '');
$this->Attributes[] = new we_tagData_textAttribute('title', false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('media', [new we_tagData_option('all'),
	new we_tagData_option('braille'),
	new we_tagData_option('embossed'),
	new we_tagData_option('handheld'),
	new we_tagData_option('print'),
	new we_tagData_option('projection'),
	new we_tagData_option('screen'),
	new we_tagData_option('speech'),
	new we_tagData_option('tty'),
	new we_tagData_option('tv'),
	], false, false, '');
$this->Attributes[] = new we_tagData_selectAttribute('applyto', [new we_tagData_option('all'),
	new we_tagData_option('wysiwyg'),
	new we_tagData_option('around'),
	], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
