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
 * This function inits a shop variant if available
 *
 * @param	$attribs array
 *
 * @return	void
 */
function we_tag_useVariants(){
	if(!$GLOBALS['we_doc']->InWebEdition && ($var = we_base_request::_(we_base_request::STRING, we_base_constants::WE_VARIANT_REQUEST)) !== false){
		we_base_variants::useVariant($GLOBALS['we_doc'], $var);
	}
}
