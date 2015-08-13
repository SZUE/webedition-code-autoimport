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
 * This function returns if an article has variants
 *
 * @param	$attribs array
 *
 * @return	boolean
 */
function we_tag_ifHasVariants($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs, 'self');

	if(isset($GLOBALS['lv']) && $docAttr === 'listview'){
		// get variants from listview object
		switch(get_class($GLOBALS['lv'])){
			case 'we_object_listview' :
			case 'we_object_listviewMultiobject' :
				$objID = $GLOBALS['lv']->f('WE_ID');
				$model = new we_objectFile();
				$model->initByID($objID, OBJECT_FILES_TABLE);
				break;
			default :
				$docID = $GLOBALS['lv']->f('WE_ID');
				$model = new we_webEditionDocument();
				$model->initByID($docID);
				break;
		}
	}else{
		$model = $GLOBALS['we_doc'];
	}

	return (we_base_variants::getNumberOfVariants($model) > 0);
}
