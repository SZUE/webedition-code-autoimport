/* global WE, top */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
'use strict';

//FIXME: add text replacements
$(function () {
	if (WE(true)) {
		//var data = WE().util.getDynamicVar(document, 'loadVarJquery', 'data-jquery');

		$('.searchSelect').SumoSelect({search: true, searchText: 'Enter here.', csvDispCount: 0, selectAll: true, okCancelInMulti: false});
		$('.searchSelectUp').SumoSelect({search: true, searchText: 'Enter here.', csvDispCount: 0, selectAll: true, okCancelInMulti: false, up: true});
		$('.newSelect').SumoSelect({csvDispCount: 0, selectAll: true, okCancelInMulti: false});
		//init datepicker
		$.datepicker.setDefaults($.datepicker.regional[WE().session.lang.short ]);
		$.datepicker.setDefaults({
			changeMonth: true,
			changeYear: true,
			showWeek: true,
			showButtonPanel: true,
			dateFormat: "dd.mm.yy" //FIXME: we need to check where dates are processed
		});
		$('.datepicker').datepicker();
		$('.weSuggest').autocomplete(WE().layout.weSuggest.config);
	}
});
