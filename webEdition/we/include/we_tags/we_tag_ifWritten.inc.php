<?php

/**
 * webEdition CMS
 *
 * $Rev: 9498 $
 * $Author: andreaswitt $
 * $Date: 2015-03-09 23:23:42 +0100 (Mo, 09. Mär 2015) $
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
function we_tag_ifWritten($attribs){
	$type = (weTag_getAttribute('type', $attribs)? : weTag_getAttribute('var', $attribs, 'document'))? : weTag_getAttribute('doc', $attribs, 'document');
	return isset($GLOBALS['we_' . $type . '_write_ok']) && ($GLOBALS['we_' . $type . '_write_ok'] == true);
}
