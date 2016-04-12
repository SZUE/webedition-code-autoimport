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
function we_tag_ifMale(){
	if(!empty($GLOBALS['we_editmode'])){
		return true;
	}
	static $maleSalutation = '';
	if(!empty($GLOBALS['WE_SALUTATION'])){
		$maleSalutation = $maleSalutation ? : f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name="' . we_newsletter_newsletter::MALE_SALUTATION_FIELD . '"');

		$maleSalutation = $maleSalutation ? : g_l('modules_newsletter', '[default][male]');

		return ($GLOBALS['WE_SALUTATION'] == $maleSalutation);
	}
	return false;
}
