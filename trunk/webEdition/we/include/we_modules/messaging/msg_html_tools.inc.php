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

/**
 * This function build the sort arrow for the messaging module.
 *
 * @param          $name                                   string
 * @param          $href                                   string
 *
 * @return         string
 */
function sort_arrow($name, $href){
	$_image_path = IMAGE_DIR . 'modules/messaging/' . $name . '.gif';

	// Check if we have to create a form or href
	return $href ? '<a href="' . $href . '"><img src="' . $_image_path . '" border="0" alt="" /></a>' :
		'<input type="image" src="' . $_image_path . '" border="0" alt="" />';
}
