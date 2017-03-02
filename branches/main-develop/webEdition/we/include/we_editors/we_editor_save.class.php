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
class we_editor_save extends we_editor_base{

	public function show(){
		//FIXME: this will be printed instantly
		we_editor_functions::saveInc($GLOBALS['we_transaction'], $this->we_doc, $GLOBALS['we_responseText'], $GLOBALS['we_responseTextType'], $GLOBALS['we_JavaScript'], !empty($GLOBALS['wasSaved']), !empty($GLOBALS['saveTemplate']), (!empty($GLOBALS['we_responseJS']) ? $GLOBALS['we_responseJS'] : [
						]), isset($GLOBALS['isClose']) && $GLOBALS['isClose'], (isset($GLOBALS['showAlert']) && $GLOBALS['showAlert']), !empty($GLOBALS["publish_doc"]));
	}

}
