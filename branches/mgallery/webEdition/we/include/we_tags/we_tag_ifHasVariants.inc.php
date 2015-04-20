<?php

/**
 * webEdition CMS
 *
 * $Rev: 9744 $
 * $Author: andreaswitt $
 * $Date: 2015-04-16 01:07:17 +0200 (Do, 16 Apr 2015) $
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
 * This function returns if an article has variants
 *
 * @param	$attribs array
 *
 * @return	boolean
 */
function we_tag_ifHasVariants(){
	return (we_base_variants::getNumberOfVariants($GLOBALS['we_doc']) > 0);
}
