<?php
/**
 * webEdition CMS
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

		include($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_language/".$GLOBALS["WE_LANGUAGE"]."/charset/charset.inc.php");

		$GLOBALS['weXmlExImNewLine'] = "\n";

		$GLOBALS['weXmlExImHeader'] = '<?xml version="1.0" encoding="'.$_language['charset'].'" standalone="yes"?>' . $GLOBALS['weXmlExImNewLine'] .
					 '<webEdition version="' . WE_VERSION . '" xmlns:we="we-namespace">' . $GLOBALS['weXmlExImNewLine'];
					 
		$GLOBALS['weXmlExImFooter'] = '</webEdition>';
		
		$GLOBALS['weXmlExImProtectCode'] = '<?php exit();?>';


?>