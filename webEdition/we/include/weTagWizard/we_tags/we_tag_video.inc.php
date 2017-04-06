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
$this->Description = 'HTML 5 Video tag';//g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = new we_tagData_textAttribute('name', true, '');
/*$this->Attributes[] = new weTagData_textAttribute('width', false, '');
$this->Attributes[] = new weTagData_textAttribute('height', false, '');
$this->Attributes[] = new weTagData_textAttribute('alt', false, '');
 */
if(defined('FILE_TABLE')){
	$this->Attributes[] = new we_tagData_selectorAttribute('startid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
	$this->Attributes[] = new we_tagData_selectorAttribute('parentid', FILE_TABLE, we_tagData_selectorAttribute::FOLDER, false, '');
}
/*$this->Attributes[] = new weTagData_selectAttribute('showcontrol', weTagData_selectAttribute::getTrueFalse(), false, '');
$this->Attributes[] = new weTagData_selectAttribute('showvideo', weTagData_selectAttribute::getTrueFalse(), false, '');

 */
//$this->Attributes[] = new weTagData_selectAttribute('xml', weTagData_selectAttribute::getTrueFalse(), false, '');
