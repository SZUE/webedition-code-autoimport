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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
//FIXME: remove this class
class weBrowser{

	function getDownloadLinkText(){
		$browser = new we_base_browserDetect();

		switch($browser->getBrowser()){
			case we_base_browserDetect::SAFARI:
			case we_base_browserDetect::APPLE:
				$out = g_l('browser', '[save_link_as_SAFARI]');
				break;
			case we_base_browserDetect::IE:
				$out = g_l('browser', '[save_link_as_IE]');
				break;
			case we_base_browserDetect::FF:
				$out = g_l('browser', '[save_link_as_FF]');
				break;
			case we_base_browserDetect::OPERA:
			default:
				$out = g_l('browser', '[save_link_as_DEFAULT]');
		}

		return nl2br(htmlspecialchars(preg_replace('#<br\s*/?\s*>#i', "\n", $out)));
	}

}
