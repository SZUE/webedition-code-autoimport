<?php

/**
 * webEdition CMS
 *
 * $Rev: 6489 $
 * $Author: mokraemer $
 * $Date: 2013-08-19 15:19:40 +0200 (Mo, 19 Aug 2013) $
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

class we_shop_shipping{

	var $id = '';
	var $text = '';
	var $countries = array();
	var $cartValue = array();
	var $shipping = array();
	var $default = false;

	function __construct($id = '', $text = '', $countries, $cartValue, $shipping, $default){

		$this->id = $id;
		$this->text = $text;
		$this->countries = $countries;
		$this->cartValue = $cartValue;
		$this->shipping = $shipping;
		$this->default = $default;
	}

}