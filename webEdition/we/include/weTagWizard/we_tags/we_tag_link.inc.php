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
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes = [new we_tagData_textAttribute('name', false, ''),
	new we_tagData_selectAttribute('only', [new we_tagData_option('href'),
		new we_tagData_option('jsstatus'),
		new we_tagData_option('jsscrollbars'),
		new we_tagData_option('jsmenubar'),
		new we_tagData_option('jstoolbar'),
		new we_tagData_option('jsresizable'),
		new we_tagData_option('jslocation'),
		new we_tagData_option('img_id'),
		new we_tagData_option('type'),
		new we_tagData_option('ctype'),
		new we_tagData_option('border'),
		new we_tagData_option('hspace'),
		new we_tagData_option('vspace'),
		new we_tagData_option('align'),
		new we_tagData_option('alt'),
		new we_tagData_option('jsheight'),
		new we_tagData_option('jswidth'),
		new we_tagData_option('jsposx'),
		new we_tagData_option('id'),
		new we_tagData_option('text'),
		new we_tagData_option('title'),
		new we_tagData_option('accesskey'),
		new we_tagData_option('tabindex'),
		new we_tagData_option('lang'),
		new we_tagData_option('rel'),
		new we_tagData_option('obj_id'),
		new we_tagData_option('anchor'),
		new we_tagData_option('params'),
		new we_tagData_option('target'),
		new we_tagData_option('jswin'),
		new we_tagData_option('jscenter'),
		new we_tagData_option('jsposy'),
		new we_tagData_option('img_title'),
		], false, ''),
	new we_tagData_textAttribute('class', false, ''),
	new we_tagData_textAttribute('style', false, ''),
	new we_tagData_textAttribute('text', false, ''),
	new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, ''),
];
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, false, '');
}
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('imageid', FILE_TABLE, 'image/*', false, '');
}
$this->Attributes[] = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('objectseourls', we_tagData_selectAttribute::getTrueFalse(), false, '');
