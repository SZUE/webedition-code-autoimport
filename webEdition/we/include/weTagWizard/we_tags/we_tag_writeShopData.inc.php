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
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);
$this->Module = 'shop';

$this->Attributes = [
	new weTagData_textAttribute('shopname', true, ''),
	new weTagData_textAttribute('pricename', true, ''),
	new weTagData_selectAttribute('netprices', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_selectAttribute('usevat', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('shipping', false, ''),
	new weTagData_selectAttribute('shippingisnet', weTagData_selectAttribute::getTrueFalse(), false, ''),
	new weTagData_textAttribute('shippingvatrate', false, ''),
	new weTagData_textAttribute('customPrefix', true, ''),
	new weTagData_textAttribute('customPostfix', true, ''),
];
