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
define('BANNER_TABLE', TBL_PREFIX . 'tblbanner');
define('BANNER_CLICKS_TABLE', TBL_PREFIX . 'tblbannerclicks');
define('BANNER_VIEWS_TABLE', TBL_PREFIX . 'tblbannerviews');

we_base_request::registerTables(array(
	'BANNER_TABLE' => BANNER_TABLE,
	'BANNER_CLICKS_TABLE' => BANNER_CLICKS_TABLE,
	'BANNER_VIEWS_TABLE' => BANNER_VIEWS_TABLE
));
