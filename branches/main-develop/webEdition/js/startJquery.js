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
		$('.weSuggest').autocomplete({
			delay: 150,
			minLength: 2,
			autoFocus: true,
			source: function (request, response) {
				var el = this.element[0];
				var term = request.term;
				if (term in el.cache) {
					response(el.cache[ term ]);
					return;
				}
				var target = WE().consts.dirs.WEBEDITION_DIR + "rpc.php?protocol=json&cmd=SelectorSuggest" +
					"&we_cmd[table]=" + el.getAttribute('data-table') +
					"&we_cmd[contenttypes]=" + el.getAttribute('data-contenttype') +
					"&we_cmd[contenttypes]=" + el.getAttribute('data-contenttype') +
					"&we_cmd[basedir]=" + el.getAttribute('data-basedir') +
					"&we_cmd[max]=" + el.getAttribute('data-max') +
					"&we_cmd[currentDocumentType]=" + el.getAttribute('data-currentDocumentType') +
					"&we_cmd[currentDocumentID]=" + el.getAttribute('data-currentDocumentID') +
					"&we_cmd[query]=" + request.term;
				$.getJSON(target, request, function (data, status, xhr) {
					el.cache[term] = data;
					response(data);
				});
			},
			create: function () {
				var res = this.getAttribute('data-result');
				this.result = document.getElementById(res);
				this.cache = {};
			},
			search: function (event, ui) {
				//FIXME result value -1
				this.result.value = 0;
			},
			select: function (event, ui) {
				this.result.value = ui.item.ID;
				this.result.setAttribute('data-contenttype', ui.item.contenttype);
				this.classList.remove("weMarkInputError");
			},
			change: function (event, ui) {
				if (this.value == "") {//is this correct?!
					this.value = "/";
					this.result.value = 0;
					this.result.setAttribute('data-contenttype', WE().consts.contentTypes.FOLDER);
				}
				if (
					!this.getAttribute("disbled") && (
					this.value && !parseInt(this.result.value) || //sth. was typed, but not selected
					!parseInt(this.result.value) && this.getAttribute("required") || //a required field has no value
					this.value.indexOf(this.getAttribute("data-basedir")) !== 0 || //basedir must match the selected path
					(this.getAttribute("data-selector") === "docSelector" && this.result.getAttribute('data-contenttype') === WE().consts.contentTypes.FOLDER) //we need a document, but only a folder is selected
					)
					) {
					this.classList.add("weMarkInputError");
				} else {
					this.classList.remove("weMarkInputError");
				}
			}
		});
	}
});
