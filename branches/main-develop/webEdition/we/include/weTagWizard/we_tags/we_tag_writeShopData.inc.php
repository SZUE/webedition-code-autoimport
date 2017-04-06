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
	new we_tagData_textAttribute('shopname', true, ''),
	new we_tagData_textAttribute('pricename', true, ''),
	new we_tagData_selectAttribute('netprices', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_selectAttribute('usevat', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('shipping', false, ''),
	new we_tagData_selectAttribute('shippingisnet', we_tagData_selectAttribute::getTrueFalse(), false, ''),
	new we_tagData_textAttribute('shippingvatrate', false, ''),
	new we_tagData_textAttribute('customPrefix', true, ''),
	new we_tagData_textAttribute('customPostfix', true, ''),
];
