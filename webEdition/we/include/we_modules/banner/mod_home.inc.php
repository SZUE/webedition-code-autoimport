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
$createBanner = we_html_button::create_button("new_banner", "javascript:top.opener.top.we_cmd('new_banner');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_BANNER"));
$createGroup = we_html_button::create_button("new_bannergroup", "javascript:top.opener.top.we_cmd('new_bannergroup');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_BANNER"));
$content = $createBanner . we_html_tools::getPixel(2, 14) . $createGroup;
$modimage = "banner.gif";
