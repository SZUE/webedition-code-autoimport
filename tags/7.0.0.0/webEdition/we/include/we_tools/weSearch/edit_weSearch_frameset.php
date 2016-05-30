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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require_once(WE_INCLUDES_PATH . 'we_tools/weSearch/conf/define.conf.php');

we_html_tools::protect();

$what = we_base_request::_(we_base_request::STRING, "pnt", "frameset");
//we_database_base::t_e_query(30);
$weFrame = new we_search_frames();
$weFrame->process();
echo $weFrame->getHTML($what);

//FIXME: check to replace this by we_showMod.php