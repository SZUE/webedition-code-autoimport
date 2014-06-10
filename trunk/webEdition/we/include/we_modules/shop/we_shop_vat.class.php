<?php

/**
 * webEdition CMS
 *
 * $Rev: 7121 $
 * $Author: mokraemer $
 * $Date: 2013-12-10 22:47:43 +0100 (Di, 10. Dez 2013) $
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
class we_shop_vat{

	var $id;
	var $text;
	var $vat;
	var $standard;

	function __construct($id, $text, $vat, $standard = false){

		$this->id = $id;
		$this->text = $text;
		$this->vat = $vat;
		$this->standard = $standard;
	}

}
