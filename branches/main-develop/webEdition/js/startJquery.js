/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 12857 $
 * $Author: mokraemer $
 * $Date: 2016-09-21 19:05:02 +0200 (Mi, 21. Sep 2016) $
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

//FIXME: add text replacements
$(function () {
	$('.searchSelect').SumoSelect({search: true, searchText: 'Enter here.', csvDispCount: 0, selectAll: true, okCancelInMulti: false});
	$('.searchSelectUp').SumoSelect({search: true, searchText: 'Enter here.', csvDispCount: 0, selectAll: true, okCancelInMulti: false, up: true});
	$('.newSelect').SumoSelect({csvDispCount: 0, selectAll: true, okCancelInMulti: false});
});
