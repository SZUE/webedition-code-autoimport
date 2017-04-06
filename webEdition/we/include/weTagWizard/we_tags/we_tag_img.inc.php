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

$this->Attributes[] = new we_tagData_textAttribute('name', false, '');
$this->Attributes[] = new we_tagData_choiceAttribute('only', [new we_tagData_option('width', false, ''),
	new we_tagData_option('height', false, ''),
	new we_tagData_option('alt', false, ''),
	new we_tagData_option('title', false, ''),
	new we_tagData_option('src', false, ''),
	new we_tagData_option('id', false, ''),
	new we_tagData_option('path', false, ''),
	], false, true, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('id', FILE_TABLE, 'image/*', false, '');
}
$this->Attributes[] = new we_tagData_textAttribute('width', false, '');
$this->Attributes[] = new we_tagData_textAttribute('height', false, '');
$this->Attributes[] = new we_tagData_textAttribute('border', false, '');
$this->Attributes[] = new we_tagData_textAttribute('hspace', false, '');
$this->Attributes[] = new we_tagData_textAttribute('vspace', false, '');
$this->Attributes[] = new we_tagData_textAttribute('alt', false, '');
$this->Attributes[] = new we_tagData_textAttribute('title', false, '');
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('startid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('parentid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
$this->Attributes[] = new we_tagData_selectAttribute('align', [new we_tagData_option('left', false, ''), new we_tagData_option('right', false, ''), new we_tagData_option('top', false, ''), new we_tagData_option('bottom', false, ''), new we_tagData_option('absmiddle', false, ''), new we_tagData_option('middle', false, ''), new we_tagData_option('texttop', false, ''), new we_tagData_option('baseline', false, ''), new we_tagData_option('absbottom', false, '')], false, '');
$this->Attributes[] = new we_tagData_sqlRowAttribute('thumbnail', THUMBNAILS_TABLE, false, 'Name', '', '', '');
$this->Attributes[] = new we_tagData_selectAttribute('showcontrol', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('showimage', [new we_tagData_option('false', false, '')], false, '');
$this->Attributes[] = new we_tagData_selectAttribute('showinputs', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('showthumbcontrol', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new we_tagData_textAttribute('sizingrel', false, '');
$this->Attributes[] = new we_tagData_selectAttribute('sizingstyle', [new we_tagData_option('none', false, ''), new we_tagData_option('em', false, ''), new we_tagData_option('ex', false, ''), new we_tagData_option('%', false, ''), new we_tagData_option('px', false, '')], false, '');
$this->Attributes[] = new we_tagData_textAttribute('sizingbase', false, '');
