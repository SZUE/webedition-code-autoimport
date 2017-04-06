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
$this->Groups[] = 'input_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$id = new we_tagData_selectorAttribute('id', FILE_TABLE, we_base_ContentTypes::WEDOCUMENT, true, '');
$target = new we_tagData_choiceAttribute('target', [new we_tagData_option('_top'),
	new we_tagData_option('_parent'),
	new we_tagData_option('_self'),
	new we_tagData_option('_blank'),
	], false, false, '');
$confirm = new we_tagData_textAttribute('confirm', false, '');
$button = new we_tagData_selectAttribute('button', we_tagData_selectAttribute::getTrueFalse(), false, '');
$hrefonly = new we_tagData_selectAttribute('hrefonly', we_tagData_selectAttribute::getTrueFalse(), false, '');
$class = new we_tagData_textAttribute('class', false, '');
$style = new we_tagData_textAttribute('style', false, '');
$params = new we_tagData_textAttribute('params', false, '');
$hidedirindex = new we_tagData_selectAttribute('hidedirindex', we_tagData_selectAttribute::getTrueFalse(), false, '');
$amount = new we_tagData_textAttribute('amount', false, 'shop');
$delarticle = new we_tagData_selectAttribute('delarticle', we_tagData_selectAttribute::getTrueFalse(), false, '');
$delshop = new we_tagData_selectAttribute('delshop', we_tagData_selectAttribute::getTrueFalse(), false, 'shop');
$shopname = new we_tagData_textAttribute('shopname', false, 'shop');
$editself = new we_tagData_selectAttribute('editself', we_tagData_selectAttribute::getTrueFalse(), false, '');
$delete = new we_tagData_selectAttribute('delete', we_tagData_selectAttribute::getTrueFalse(), false, '');
$xml = new we_tagData_selectAttribute('xml', we_tagData_selectAttribute::getTrueFalse(), false, '');


$this->TypeAttribute = new we_tagData_typeAttribute('edit', [
	new we_tagData_option('', false, '', [$id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex], [$id]),
	new we_tagData_option('document', false, '', [$id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $editself, $delete], [$id]),
	new we_tagData_option('object', false, 'object', [$id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $editself, $delete], [$id]),
	new we_tagData_option('shop', false, 'shop', [$id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $amount, $delarticle, $delshop, $shopname,], [$id])]
	, false, '');


$this->Attributes = [$id, $target, $confirm, $button, $hrefonly, $class, $style, $params, $hidedirindex, $amount, $delarticle, $delshop, $shopname, $editself, $delete, $xml];
