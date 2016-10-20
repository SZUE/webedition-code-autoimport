<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_shop_shipping{

	var $id = '';
	var $text = '';
	var $countries = [];
	var $cartValue = [];
	var $shipping = [];
	var $default = false;

	public function __construct($id, $text, $countries, $cartValue, $shipping, $default){
		$this->id = $id;
		$this->text = $text;
		$this->countries = $countries;
		$this->cartValue = $cartValue;
		$this->shipping = $shipping;
		$this->default = $default;
	}

	public static function getNewEmptyShipping(){
		return new self('weShipping_' . md5(uniqid('', true)), g_l('modules_shop', '[new_entry]'), ['Deutschland'], [10, 20, 100], [15, 5, 0], 0);
	}

}
