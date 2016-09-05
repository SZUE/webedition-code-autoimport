/* global WE, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev$
 * $Author$
 * $Date$
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

WE().util.loadConsts("weSearch");
WE().util.loadConsts("g_l.weSearch");

weSearch = {
	conf: {
		whichsearch: '',
		editorBodyFrame: '',
		tab: 0,
		modelClassName: '',
		modelID: 0,
		modelIsFolder: 0,
		showSelects: 0,
		rows: 0,
		we_transaction: '',
	},
	elems: {
		btnTrash: '',
		btnSelector: '',
		fieldSearch: '',
		selStatus: '',
		selSpeicherart: '',
		selLocation: '',
		selLocationText: '',
		selLocationDate: '',
		selModFields: '',
		selUsers: '',
		pixel: '',
		searchFields: ''
	},
	elem: null,
	rolloverElem: null,
	init: function () {
		if (weSearch.conf.editorBodyFrame.document.readyState === "complete") {
			if (weSearch.conf.editorBodyFrame.document.getElementById('mouseOverDivs_' + weSearch.conf.whichsearch)) {
				weSearch.conf.editorBodyFrame.document.getElementById('mouseOverDivs_' + weSearch.conf.whichsearch).innerHTML = weSearch.conf.editorBodyFrame.document.getElementById('movethemaway').innerHTML;
				weSearch.conf.editorBodyFrame.document.getElementById('movethemaway').innerHTML = '';

				weSearch.sizeScrollContent();
				document.addEventListener('mousemove', weSearch.updateElem, false);
				document.addEventListener('resize', weSearch.sizeScrollContent(), false);
				WE().util.setIconOfDocClass(document, 'resultIcon');
			}
		} else {
			setTimeout(weSearch.init, 10);
		}
	},
	setNextPrevData: function () {
		var dataElem = this.conf.editorBodyFrame.document.getElementsByClassName('nextPrevData')[0],
						spanText = this.conf.editorBodyFrame.document.getElementsByClassName('spanSearchText'),
						btnBack = this.conf.editorBodyFrame.document.getElementsByClassName('btnSearchBack'),
						btnNext = this.conf.editorBodyFrame.document.getElementsByClassName('btnSearchNext'),
						selPages = this.conf.editorBodyFrame.document.getElementsByClassName('selectSearchPages'),
						selPagesVals = dataElem.getAttribute('data-pagevalue').split(','),
						selPagesTexts = dataElem.getAttribute('data-pagetext').split(','),
						selPagesPage = dataElem.getAttribute('data-page'),
						opt, i, j;

		spanText[0].innerHTML = spanText[1].innerHTML = dataElem.getAttribute('data-text');
		btnBack[0].disabled = btnBack[1].disabled = dataElem.getAttribute('data-disableback') === 'true' ? true : false;
		btnNext[0].disabled = btnNext[1].disabled = dataElem.getAttribute('data-disablenext') === 'true' ? true : false;

		for (i = 0; i < 2; i++) {
			selPages[i].innerHTML = '';
			for (j = 0; j < selPagesVals.length; j++) {
				opt = document.createElement("option");
				opt.text = selPagesTexts[j];
				opt.value = selPagesVals[j];
				selPages[i].add(opt);
			}
			selPages[i].value = selPagesPage;
		}

		// FIXME: check if we need to set the following params when html is not overwritten
		this.conf.editorBodyFrame.document.getElementsByClassName('selectSearchNumber').value = dataElem.getAttribute('data-number');
		this.conf.editorBodyFrame.document.we_form.elements['setView' + this.conf.whichsearch].value = dataElem.getAttribute('data-setView');
		this.conf.editorBodyFrame.document.we_form.elements['Order' + this.conf.whichsearch].value = dataElem.getAttribute('data-order');
		this.conf.editorBodyFrame.document.we_form.elements.mode.value = dataElem.getAttribute('data-mode');
	},
	ajaxCallbackResultList: {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== '') {
				weSearch.conf.editorBodyFrame.document.getElementById('scrollContent_' + weSearch.conf.whichsearch).innerHTML = o.responseText;
				WE().util.setIconOfDocClass(document, 'resultIcon');

				if (weSearch.conf.editorBodyFrame.document.getElementsByClassName('nextPrevData') && weSearch.conf.editorBodyFrame.document.getElementsByClassName('nextPrevData')[0]) {
					weSearch.setNextPrevData();
				} else {
					//weSearch.makeAjaxRequestParametersTop();
					//weSearch.makeAjaxRequestParametersBottom();
				}

				// IMPORTANT: we must move mouseoverdivs because of scrolling. FIXME: use display:none to avoid this
				weSearch.conf.editorBodyFrame.document.getElementById('mouseOverDivs_' + weSearch.conf.whichsearch).innerHTML = weSearch.conf.editorBodyFrame.document.getElementById('movethemaway').innerHTML;
				weSearch.conf.editorBodyFrame.document.getElementById('movethemaway').innerHTML = '';

				if (weSearch.conf.whichsearch === WE().consts.weSearch.SEARCH_MEDIA || weSearch.conf.whichsearch === WE().consts.weSearch.SEARCH_ADV) {
					window.scrollTo(0, document.body.scrollHeight);

					// correct result header when result list has vertical scrollbar
					var sc = document.getElementById('scrollContent_' + weSearch.conf.whichsearch);
					document.getElementById('headerLast').style.width = weSearch.conf.whichsearch === WE().consts.weSearch.SEARCH_MEDIA ? ((sc.firstChild.offsetHeight > sc.offsetHeight ? 78 : 64) + 'px') :
									((sc.firstChild.offsetHeight > sc.offsetHeight ? 20 : 18) + '%');
				}
			}
		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	ajaxCallbackParametersTop: {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== '') {
				weSearch.conf.editorBodyFrame.document.getElementById('parametersTop_' + weSearch.conf.whichsearch).innerHTML = o.responseText;
			}
		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	ajaxCallbackParametersBottom: {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== "") {
				weSearch.conf.editorBodyFrame.document.getElementById('parametersBottom_' + weSearch.conf.whichsearch).innerHTML = o.responseText;
			}
		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	ajaxCallbackgetMouseOverDivs: {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== "") {
				weSearch.conf.editorBodyFrame.document.getElementById('mouseOverDivs_' + weSearch.conf.whichsearch).innerHTML = o.responseText;
			}
		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	search: function (newSearch, sameRange) {
		var Checks = [], m = 0, i, table;

		newSearch = newSearch === undefined ? true : newSearch;
		sameRange = sameRange === undefined ? false : sameRange;

		sameRange = sameRange || !newSearch ? true : false;// if not newSearch we preserve range anyway.

		switch (this.conf.whichsearch) {
			case WE().consts.weSearch.SEARCH_DOCLIST:
				Checks[0] = ''; // no search options needed at all!
				break;
			case WE().consts.weSearch.SEARCH_ADV:
				for (i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
					table = this.conf.editorBodyFrame.document.we_form.elements[i].name;
					if (table.substring(0, 23) === 'search_tables_advSearch') {
						if (encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value) == 1) {
							Checks[m] = encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
							m++;
						}
					}
				}
				if (Checks.length === 0) {
					top.we_showMessage(WE().consts.g_l.weSearch.nothingCheckedAdv, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
				break;
			case WE().consts.weSearch.SEARCH_DOCS:
			case WE().consts.weSearch.SEARCH_MEDIA:
				var thirdName = this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCS ? 'searchForContent' : 'searchForMeta';
				for (i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
					table = this.conf.editorBodyFrame.document.we_form.elements[i].name;
					if (table === 'searchForText' + this.conf.whichsearch || table === 'searchForTitle' + this.conf.whichsearch || table === thirdName + this.conf.whichsearch) {
						if (encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value) == 1) {
							Checks[m] = encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
							m++;
						}
					}
				}

				if (Checks.length === 0) {
					//FIXME: dirty fix => allow to search without searchForXX when no searchFieldsMediaSearch[0] is empty
					if (this.conf.editorBodyFrame.document.we_form.elements['searchMediaSearch[0]'] !== undefined && this.conf.editorBodyFrame.document.we_form.elements['searchMediaSearch[0]'].value) {
						top.we_showMessage(WE().consts.g_l.weSearch.nothingCheckedTmplDoc, WE().consts.message.WE_MESSAGE_ERROR, window);
					} else {
						Checks[0] = '';
					}
				}
				break;
			case WE().consts.weSearch.SEARCH_TMPL:
				for (i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
					table = this.conf.editorBodyFrame.document.we_form.elements[i].name;
					if (table === 'searchForText' + this.conf.whichsearch || table === 'searchForContent' + this.conf.whichsearch) {
						if (encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value) == 1) {
							Checks[m] = encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
							m++;
						}
					}
				}
				if (Checks.length === 0) {
					top.we_showMessage(WE().consts.g_l.weSearch.nothingCheckedTmplDoc, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
				break;
		}

		if (Checks.length !== 0) {
			if (!sameRange) {
				window.document.we_form.elements['searchstart' + this.conf.whichsearch].value = 0;
			}
			window.document.we_form.elements.newSearch.value = newSearch ? 1 : 0;

			this.makeAjaxRequestDoclist();
		}
	},
	makeAjaxRequestDoclist: function () {
		//this.getMouseOverDivs();
		var args = '', newString = '';

		for (var i = 0, elem; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			elem = this.conf.editorBodyFrame.document.we_form.elements[i];
			if (elem.type === "radio" && !elem.checked) {
				continue;
			}
			newString = elem.name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(elem.value);
		}
		this.conf.editorBodyFrame.document.getElementById('scrollContent_' + this.conf.whichsearch).innerHTML = '<table style="width:100%;height:100%"><tr><td style="text-align:center"><i class="fa fa-2x fa-spinner fa-pulse"></i><div id="scrollActive"></div></td></tr></table>';
		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackResultList, 'protocol=json&cns=' + (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST ? 'doclist' : 'tools/weSearch') + '&tab=' + this.conf.tab + '&cmd=GetSearchResult&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	makeAjaxRequestParametersTop: function () {
		var args = '', newString = "";

		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackParametersTop, 'protocol=json&cns=' + (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST ? 'doclist' : 'tools/weSearch') + '&tab=' + this.conf.tab + '&cmd=GetSearchParameters&position=top&whichsearch=' + this.conf.whichsearch + '&classname' + this.conf.modelClassName + '=&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	makeAjaxRequestParametersBottom: function () {
		var args = '', newString = '';
		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackParametersBottom, 'protocol=json&cns=' + (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST ? 'doclist' : 'tools/weSearch') + '&tab=' + this.conf.tab + '&cmd=GetSearchParameters&position=bottom&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	getMouseOverDivs: function () {
		var args = '', newString = '';
		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackgetMouseOverDivs, 'protocol=json&cns=' + (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST ? 'doclist' : 'tools/weSearch') + '&tab=' + this.conf.tab + '&cmd=GetMouseOverDivs&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	setView: function (value) {
		this.conf.editorBodyFrame.document.we_form.elements['setView' + this.conf.whichsearch].value = value;
		this.search(false);
	},
	showImageDetails: function (picID) {
		if ((weSearch.rolloverElem = document.getElementById(picID))) {
			weSearch.rolloverElem.style.visibility = 'visible';
		}
	},
	hideImageDetails: function (picID) {
		if (weSearch.rolloverElem) {
			weSearch.rolloverElem.style.visibility = 'hidden';
			weSearch.rolloverElem.style.left = '-9999px';
		}
	},
	updateElem: function (e) {
		if (weSearch.rolloverElem && weSearch.rolloverElem.style.visibility === 'visible') {
			var elem = weSearch.rolloverElem,
							elemW = elem.offsetWidth,
							elemH = elem.offsetHeight,
							frameW = window.innerWidth ? window.innerWidth : document.body.offsetWidth,
							frameH = window.innerHeight ? window.innerHeight : document.body.offsetHeight,
							posX = e.pageX,
							posY = e.pageY,
							scrollY = window.scrollY;

			elem.style.left = (((frameW - posX) < elemW + 10) ? (posX - elemW - 10) : (posX + 10)) + 'px';
			elem.style.top = (Math.max((scrollY + 4), (Math.min((scrollY + frameH - elemH - 4), (posY - Math.round(elemH / 5 * 3)))))) + 'px';
		}
	},
	absLeft: function (el) {
		return (el.offsetParent) ? el.offsetLeft + this.absLeft(el.offsetParent) : el.offsetLeft;
	},
	absTop: function (el) {
		return (el.offsetParent) ? el.offsetTop + this.absTop(el.offsetParent) : el.offsetTop;
	},
	reloadSameRange: function () {// FIXME: add param "newSearchTable"
		var scrollActive = document.getElementById('scrollActive');
		if (scrollActive === null) {
			//this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value = parseInt(this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value) + anzahl;
			this.search(false);
		}
	},
	next: function () {
		var scrollActive = document.getElementById('scrollActive');
		var doc = this.conf.editorBodyFrame.document; //this.conf.editorBodyFrame ? this.conf.editorBodyFrame.document : document;
		if (scrollActive === null) {
			doc.we_form.elements['searchstart' + this.conf.whichsearch].value = parseInt(doc.we_form.elements['searchstart' + this.conf.whichsearch].value) + parseInt(doc.we_form.elements['anzahl' + this.conf.whichsearch].value);
			this.search(false);
		}
	},
	back: function (anzahl) {
		var scrollActive = document.getElementById('scrollActive');
		var doc = this.conf.editorBodyFrame.document; //this.conf.editorBodyFrame ? this.conf.editorBodyFrame.document : document;
		if (scrollActive === null) {
			doc.we_form.elements['searchstart' + this.conf.whichsearch].value = parseInt(doc.we_form.elements['searchstart' + this.conf.whichsearch].value) - parseInt(doc.we_form.elements['anzahl' + this.conf.whichsearch].value);
			this.search(false);
		}
	},
	openModule: function (mod, id) {
		top.we_cmd(mod + '_edit_ifthere', id);
	},
	openCategory: function (id) {
		top.we_cmd('editCat', id);
	},
	setOrder: function (order, whichSearch) {
		var columns = whichSearch === 'MediaSearch' ? ['Text', 'media_filesize', 'IsUsed', 'media_alt', 'media_title', 'CreationDate', 'ModDate'] :
						['Text', 'SiteTitle', 'CreationDate', 'ModDate'];
		var deleteArrow, arrow, foo;

		for (var i = 0; i < columns.length; i++) {
			if (order !== columns[i]) {
				deleteArrow = document.getElementById(columns[i] + '_' + whichSearch);
				deleteArrow.innerHTML = '';
			}
		}
		arrow = document.getElementById(order + '_' + whichSearch);
		foo = document.we_form.elements['Order' + whichSearch].value;

		if (order + ' DESC' === foo) {
			document.we_form.elements['Order' + whichSearch].value = order;
			arrow.innerHTML = '<i class="fa fa-sort-asc fa-lg"></i>';
		} else {
			document.we_form.elements['Order' + whichSearch].value = order + ' DESC';
			arrow.innerHTML = '<i class="fa fa-sort-desc fa-lg"></i>';
		}
		this.search(false);
	},
	sizeScrollContent: function () {
		var frameH = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
		var h = 0, hMin = 120, rows, mode;
		var scrollContent = document.getElementById('scrollContent_' + this.conf.whichsearch);

		if (scrollContent) {
			switch (this.conf.whichsearch) {
				case WE().consts.weSearch.SEARCH_DOCS:
				case WE().consts.weSearch.SEARCH_TMPL:
					//top.console.log('found');
					h = frameH - 324;
					break;
					/*
					 case WE().consts.weSearch.SEARCH_MEDIA:
					 rows = (document.getElementById('filterTableMediaSearch').rows.length - 1);
					 h = frameH - (534 + (rows * 32));
					 hMin = 300;
					 break;
					 case WE().consts.weSearch.SEARCH_ADV:
					 rows = (document.getElementById('filterTableAdvSearch').rows.length - 1);
					 h = frameH - (290 + (rows * 32));
					 break;
					 */
				case WE().consts.weSearch.SEARCH_MEDIA:
				case WE().consts.weSearch.SEARCH_ADV:
					h = frameH - 136;
					hMin = 300;
					break;
				case WE().consts.weSearch.SEARCH_DOCLIST:
					//top.console.log('hier');
					rows = (document.getElementById('filterTableDoclistSearch').rows.length);
					mode = document.we_form.mode.value;
					//top.console.log('hier', mode);
					h = parseInt(mode) === 1 ? (frameH - (220 + (rows * 28))) : (frameH - 183);
					break;
			}

			scrollContent.style.height = (Math.max(h, hMin)) + 'px';
		}
	},
	newinput: function () {
		var elem = document.getElementById('filterTable' + this.conf.whichsearch),
						//c = elem.rows.length - 1,
						//scrollContent = document.getElementById('scrollContent_' + this.conf.whichsearch),
						newRow, cell;

		this.conf.rows++;

		if (elem) {
			newRow = document.createElement('TR');
			newRow.setAttribute('id', 'filterRow_' + this.conf.rows);

			cell = document.createElement('TD');
			cell.innerHTML = '<input type="hidden" value="" name="hidden_searchFields' + this.conf.whichsearch + '[' + this.conf.rows + ']" value="">' + this.elems.searchFields.replace(/__we_new_id__/g, this.conf.rows);
			newRow.appendChild(cell);

			newRow.appendChild(this.getCell('location' + this.conf.whichsearch, this.conf.rows));
			newRow.appendChild(this.getCell('search' + this.conf.whichsearch, this.conf.rows));
			newRow.appendChild(this.getCell('delButton', this.conf.rows));
			newRow.appendChild(this.getCell('hiddenLocation_empty', this.conf.rows));

			elem.appendChild(newRow);
			this.sizeScrollContent();
		}
	},
	newinpuMediaSearch: function () {
		//top.console.debug('is fn');

	},
	getCell: function (type, rowID, replacement, value) { // FIXME: use this-whichsearch to reduce cases
		var cell = document.createElement('TD'),
						locationType;

		switch (type) {
			case 'delButton':
				cell.setAttribute('id', 'td_delButton[' + rowID + ']');
				cell.innerHTML = this.elems.btnTrash.replace(/__we_new_id__/g, rowID);
				break;
			case 'searchAdvSearch':
				cell.setAttribute('id', 'td_searchAdvSearch[' + rowID + ']');
				cell.innerHTML = this.elems.fieldSearch.replace(/__we_new_id__/g, rowID).replace(/__we_read_only__/g, replacement ? 'readonly="1" ' : '');
				break;
			case 'locationAdvSearch':
			case 'locationDateAdvSearch':
			case 'locationTextAdvSearch':
				locationType = (type === 'locationTextAdvSearch' ? 'selLocationText' : (type === 'locationDateAdvSearch' ? 'selLocationDate' : 'selLocation'));
				cell.setAttribute("id", "td_locationAdvSearch[" + rowID + "]");
				cell.innerHTML = this.elems[locationType].replace(/__we_new_id__/g, rowID);
				break;
			case 'searchMediaSearch':
				cell.setAttribute('id', 'td_searchMediaSearch[' + rowID + ']');
				cell.innerHTML = this.elems.fieldSearch.replace(/__we_new_id__/g, rowID).replace(/__we_read_only__/g, replacement ? 'readonly="1" ' : '');
				break;
			case 'locationMediaSearch':
			case 'locationDateMediaSearch':
			case 'locationTextMediaSearch':
				locationType = (type === 'locationTextMediaSearch' ? 'selLocationText' : (type === 'locationDateMediaSearch' ? 'selLocationDate' : 'selLocation'));
				cell.setAttribute("id", "td_locationMediaSearch[" + rowID + "]");
				cell.innerHTML = this.elems[locationType].replace(/__we_new_id__/g, rowID);
				break;
			case 'searchDoclistSearch':
				cell.setAttribute('id', 'td_searchDoclistSearch[' + rowID + ']');
				cell.innerHTML = this.elems.fieldSearch.replace(/__we_new_id__/g, rowID).replace(/__we_read_only__/g, replacement ? 'readonly="1" ' : '');
				break;
			case 'locationDoclistSearch':
			case 'locationDateDoclistSearch':
			case 'locationTextDoclistSearch':
				locationType = (type === 'locationTextDoclistSearch' ? 'selLocationText' : (type === 'locationDateDoclistSearch' ? 'selLocationDate' : 'selLocation'));
				cell.setAttribute("id", "td_locationDoclistSearch[" + rowID + "]");
				cell.innerHTML = this.elems[locationType].replace(/__we_new_id__/g, rowID);
				break;
			case 'hiddenLocationAdvSearch':
			case 'hiddenLocationMediaSearch':
			case 'hiddenLocationDoclistSearch':
				var namepart = type === 'hiddenLocationAdvSearch' ? 'locationAdvSearch' : (type === 'hiddenLocationMediaSearch' ? 'locationMediaSearch' : 'locationDoclistSearch');
				var hidden = document.createElement("input");

				cell.setAttribute('id', 'td_hiddenLocation[' + rowID + ']');
				hidden.setAttribute('type', 'hidden');
				hidden.setAttribute('name', namepart + '[' + rowID + ']');
				hidden.setAttribute('value', (value ? value : 'IS'));
				cell.appendChild(hidden);
				break;
			case 'hiddenLocation_empty':
				cell.setAttribute('id', 'td_hiddenLocation[' + rowID + ']');
		}
		return cell;

	},
	reload: function () {
		if (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST) {
			top.we_cmd("reload_editpage");
		}
	},
	switchSearch: function (mode) {
		document.we_form.mode.value = mode;
		var defSearch = document.getElementById('defSearch');
		var advSearch = document.getElementById('advSearch');
		var filterTable = document.getElementById('filterTable' + this.conf.whichsearch);
		var advSearch3 = document.getElementById('advSearch3');

		if (parseInt(mode) === 1) {
			defSearch.style.display = "none";
			advSearch.style.display = "block";
			filterTable.style.display = "block";
			advSearch3.style.display = "block";
		} else {
			defSearch.style.display = "block";
			advSearch.style.display = "none";
			filterTable.style.display = "none";
			advSearch3.style.display = "none";
			if (this.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST) {
				this.search(false);
			}
		}
		this.sizeScrollContent();
	},
	delRow: function (id) {
		var //scrollContent = document.getElementById('scrollContent_' + this.conf.whichsearch),
						elem = document.getElementById('filterTable' + this.conf.whichsearch);

		if (elem) {
			var trows = elem.rows,
							rowID = 'filterRow_' + id;

			for (var i = 0; i < trows.length; i++) {
				if (rowID == trows[i].id) {
					elem.deleteRow(i);
				}
			}
		}
		this.sizeScrollContent();
	},
	changeit: function (value, rowNr) {
		var setValue = document.getElementsByName('search' + this.conf.whichsearch + '[' + rowNr + ']')[0].value;
		var from = document.getElementsByName('hidden_searchFields' + this.conf.whichsearch + '[' + rowNr + ']')[0].value;
		var row = document.getElementById('filterRow_' + rowNr);
		var locationTD = document.getElementById('td_location' + this.conf.whichsearch + '[' + rowNr + ']');
		var hiddenLocationTD = document.getElementById('td_hiddenLocation[' + rowNr + ']');
		var searchTD = document.getElementById('td_search' + this.conf.whichsearch + '[' + rowNr + ']');
		var delButtonTD = document.getElementById('td_delButton[' + rowNr + ']');
		var location;
		var locationType = '';
		var innerhtml;
		var cell;

		if (hiddenLocationTD) {
			row.removeChild(hiddenLocationTD);
		}
		if (delButtonTD) {
			row.removeChild(delButtonTD);
		}
		if (searchTD) {
			row.removeChild(searchTD);
		}
		if (locationTD) {
			row.removeChild(locationTD);
		}
		var table;

		switch (value) {
			case 'Content':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'CONTAIN';
				location.disabled = true;

				row.appendChild(this.getCell('search' + this.conf.whichsearch, this.conf.rows));
				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'CONTAIN'));
				document.getElementById("search" + this.conf.whichsearch + "[" + rowNr + "]").value = setValue;
				break;
			case 'temp_category':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				innerhtml = '<table class="default"><tbody><tr>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td></td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, WE().consts.tables.CATEGORY_TABLE).replace(/__we_selector__/, 'we_selector_category').replace(/__we_content_types__/, '') + '</td>' +
								'</tr></tbody></table>';

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'temp_template_id':
			case 'MasterTemplateID':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				innerhtml = '<table class="default"><tbody><tr>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td></td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, WE().consts.tables.TEMPLATES_TABLE).replace(/__we_selector__/, 'we_selector_document').replace(/__we_content_types__/, '') + '</td>' +
								'</tr></tbody></table>';

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'ParentIDDoc':
			case 'ParentIDObj':
			case 'ParentIDTmpl':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				table = value === 'ParentIDDoc' ? WE().consts.tables.FILE_TABLE : (value === 'ParentIDObj' ? WE().consts.tables.OBJECT_FILES_TABLE : WE().consts.tables.TEMPLATES_TABLE);
				innerhtml = '<table class="default"><tbody><tr>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td></td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, table).replace(/__we_selector__/, 'we_selector_directory').replace(/__we_content_types__/, '') + '</td>' +
								'</tr></tbody></table>';

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'HasReferenceToID':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				table = WE().consts.tables.FILE_TABLE;
				innerhtml = '<table class="default"><tbody><tr>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td></td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, table).replace(/__we_selector__/, 'we_selector_document').replace(/__we_content_types__/, WE().consts.weSearch.MEDIA_CONTENTTYPES_CSV) + '</td>' +
								'</tr></tbody></table>';

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'Status':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = this.elems.selStatus.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'Speicherart':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = this.elems.selSpeicherart.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			case 'Published':
			case 'CreationDate':
			case 'ModDate':
				row.appendChild(this.getCell('locationDate' + this.conf.whichsearch, rowNr));

				// FIXME: move datepicker-button to search_view
				innerhtml = '<table id="search' + this.conf.whichsearch + '[' + rowNr + ']_cell" class="default"><tbody><tr>' +
								'<td></td>' +
								'<td></td>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td></td>' +
								'<td><a href="#"><button id="date_picker_from' + rowNr + '" class="weBtn multiicon"><i class="fa fa-lg fa-calendar"></i></button></a></td>' +
								'</tr></tbody></table>';


				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				Calendar.setup({inputField: 'search' + this.conf.whichsearch + '[' + rowNr + ']', ifFormat: '%d.%m.%Y', button: 'date_picker_from' + rowNr + '', align: 'Tl', singleClick: true});

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation_empty', rowNr));
				break;
			case 'allModsIn':// FIXME: does not work yet
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IN';
				location.disabled = true;

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = this.elems.selModFields.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation_empty', rowNr));
				break;
			case 'modifierID':
				//case 'Creator':
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr, '', 'CONTAIN', true));
				location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');
				location.value = 'IS';
				location.disabled = true;

				cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search' + this.conf.whichsearch + '[' + rowNr + ']');
				cell.innerHTML = this.elems.selUsers.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation' + this.conf.whichsearch, rowNr, '', 'IS'));
				break;
			default:
				switch (value) {
					case 'meta__Title':
					case 'meta__Description':
					case 'meta__Keywords':
					case 'meta__Autor':
					case 'meta__MIME':
					case 'Path':
					case 'CreatorName': // FIXME: reduce CreatorID and CreatorName to Creator analogue to modifierID
					case 'Text':
					case 'ContentType': // FIXME: make select
					case 'WebUserName':
						locationType = 'Text';
						break;
					case 'ID':
					case 'CreatorID':
					case 'WebUserID':
						locationType = 'Date';
						break;
				}

				row.appendChild(this.getCell('location' + locationType + this.conf.whichsearch, rowNr));
				row.appendChild(this.getCell('search' + this.conf.whichsearch, rowNr));
				row.appendChild(this.getCell('delButton', rowNr));
				row.appendChild(this.getCell('hiddenLocation_empty', rowNr));

				document.getElementById('search' + this.conf.whichsearch + '[' + rowNr + ']').value = setValue;
		}

		switch (from) {// FIXME: this is nonsens! move this to the above cases
			//switch (value) {
			case 'allModsIn':
			case 'MasterTemplateID':
			case 'ParentIDTmpl':
			case 'ParentIDObj':
			case 'ParentIDDoc':
			case 'temp_template_id':
			case 'ContentType':
			case 'temp_category':
			case 'Status':
			case 'Speicherart':
			case 'Published':
			case 'CreationDate':
			case 'ModDate':
				document.getElementById('search' + this.conf.whichsearch + '[' + rowNr + ']').value = '';
				break;
			default:
				document.getElementById('search' + this.conf.whichsearch + '[' + rowNr + ']').value = setValue;
		}

		document.getElementsByName('hidden_searchFields' + this.conf.whichsearch + '[' + rowNr + ']')[0].value = value;

	},
	ajaxCallbackResetVersion: {
		success: function (o) {
			//top.we_cmd("save_document","' . $GLOBALS['we_transaction'] . '","0","1","0", "","");
			top.we_showMessage(WE().consts.g_l.weSearch.versionsResetAllVersionsOK, WE().consts.message.WE_MESSAGE_NOTICE, window);

			// reload current document => reload all open Editors on demand
			var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
			for (var frameId in _usedEditors) {
				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			if (top.opener.top.treeData) {
				top.opener.we_cmd('load', top.opener.top.treeData.table, 0);
			}
			document.getElementById('resetBusy' + this.conf.whichsearch).innerHTML = '';
		},
		failure: function (o) {
		}
	},
	resetVersionAjax: function (id, documentID, version, table) {
		document.getElementById('resetBusy' + this.conf.whichsearch).innerHTML = "<table border='0' width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i><div id='scrollActive'></div></td></tr></table>";

		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackResetVersion, "protocol=json&cns=versionlist&cmd=ResetVersion&id=" + id + "&documentID=" + documentID + "&version=" + version + "&documentTable=" + table + "&we_transaction=' . $GLOBALS['we_transaction'] . '");
	},
	resetVersions: function () {
		var checkboxes = [];
		var i, pubElem;
		var elems = document.we_form.querySelectorAll("input[name^=resetVersion]");
		for (i = 0; i < elems.length; i++) {
			if (elems[i].checked === true) {
				pubElem = document.getElementById("publishVersion_" + elems[i].value);
				checkboxes.push(elems[i].value + (pubElem !== null && pubElem.checked ? "___1" : "___0"));
			}
		}
		if (checkboxes.length === 0) {
			top.we_showMessage(WE().consts.g_l.weSearch.versionsNotChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
			return;
		}
		if (confirm(WE().consts.g_l.weSearch.resetVersionsSearchtool) !== true) {
			return;
		}
		this.resetVersionAjax(checkboxes.join(","), 0, 0, 0);

	},
	checkAllActionChecks: function () {
		var checkAll = document.getElementsByName("action_all_" + this.conf.whichsearch);
		var checkboxes = document.getElementsByName((this.conf.whichsearch == WE().consts.weSearch.SEARCH_MEDIA ? 'delete_docs_MediaSearch' : 'publish_docs_' + this.conf.whichsearch));
		var check = false;

		if (checkAll[0].checked) {
			check = true;
		}
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = check;
		}
	},
	publishDocs: function (whichSearch) {
		var checkAll = document.getElementsByName("action_all_" + whichSearch);
		var checkboxes = document.getElementsByName("publish_docs_" + whichSearch);
		var check = false;

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				check = true;
				break;
			}
		}

		if (checkboxes.length === 0) {
			check = false;
		}

		if (check === false) {//searchtool__notChecked
			top.we_showMessage(WE().consts.g_l.weSearch.searchtool__notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		} else {
			Check = confirm(WE().consts.g_l.weSearch.publish_docs);
			if (Check === true) {
				this.publishDocsAjax(whichSearch);
			}
		}
	},
	toggleAdditionalContent: function (btn, id) {
		var elem = document.getElementById('infoTable_' + id);

		if (elem) {
			elem.style.display = elem.style.display === 'block' ? 'none' : 'block';

			if (elem.style.display === 'block') {
				// Group is expanded
				btn.firstChild.classList.remove("fa-caret-right");
				btn.firstChild.classList.add("fa-caret-down");
			} else {
				// Group is folded
				btn.firstChild.classList.remove("fa-caret-down");
				btn.firstChild.classList.add("fa-caret-right");
			}
		}
	},
	ajaxCallbackPublishDocs: {
		success: function (o) {

			// reload current document => reload all open Editors on demand

			var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse();
			for (var frameId in _usedEditors) {
				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			if (weSearch.conf.whichsearch === WE().consts.weSearch.SEARCH_DOCLIST) {
				top.we_cmd("load", top.treeData.table, 0);
			} else {
				if (top.opener.top.treeData) {
					top.opener.we_cmd("load", top.opener.top.treeData.table, 0);
				}
			}

			document.getElementById("resetBusy" + weSearch.conf.whichsearch).innerHTML = "";
			//document.getElementById("resetBusyDocSearch").innerHTML = "";
			top.we_showMessage(WE().consts.g_l.weSearch.searchtool__publishOK, WE().consts.message.WE_MESSAGE_NOTICE, window);
		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	publishDocsAjax: function (whichSearch) {
		var args = '';
		var check = '';
		var checkboxes = document.getElementsByName('publish_docs_' + whichSearch);
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				if (check !== "") {
					check += ",";
				}
				check += checkboxes[i].value;
			}
		}
		args += "&we_cmd[0]=" + encodeURI(check);
		var scroll = document.getElementById("resetBusy" + whichSearch);
		scroll.innerHTML = "<table border='0' width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";

		top.YAHOO.util.Connect.asyncRequest("POST", WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackPublishDocs, "protocol=json&cns=tools/weSearch&cmd=PublishDocs&" + args + "");

	},
	previewVersion: function (table, ID, version) {
		top.we_cmd("versions_preview", table, ID, version, 0);
	},
	calendarSetup: function (x) {
		for (i = 0; i < x; i++) {
			if (document.getElementById("date_picker_from" + i + "") !== null) {
				//Calendar.setup({inputField:"search" + this.conf.whichsearch + "["+i+"]",ifFormat:"%d.%m.%Y",button:"date_picker_from"+i+"",align:"Tl",singleClick:true});
			}
		}
	},
	deleteMediaDocs: function (whichSearch) {
		var checkboxes = document.getElementsByName("delete_docs_" + whichSearch);
		var check = false;

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				check = true;
				break;
			}
		}

		if (!check) {
			top.we_showMessage(WE().consts.g_l.weSearch.searchtool__notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		} else {
			var conf = confirm("you really want to delete them?");
			if (conf) {
				this.deleteMediaDocsAjax(whichSearch);
			}
		}
	},
	deleteMediaDocsAjax: function (whichSearch) {
		var args = '',
						check = '',
						checkboxes = document.getElementsByName("delete_docs_" + whichSearch);

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				check += (check ? ',' : '') + checkboxes[i].value;
			}
		}
		args += '&we_cmd[0]=' + encodeURI(check);

		var scroll = document.getElementById('resetBusy' + whichSearch);
		scroll.innerHTML = '<div><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></div>';

		top.YAHOO.util.Connect.asyncRequest('POST', WE().consts.dirs.WEBEDITION_DIR + "rpc.php", this.ajaxCallbackDeleteMediaDocs, 'protocol=json&cns=tools/weSearch&cmd=DeleteMediaDocs&' + args + '');
	},
	ajaxCallbackDeleteMediaDocs: {
		success: function (o) {
			var response = JSON.parse(o.responseText);

			top.we_showMessage(response.message, WE().consts.message.WE_MESSAGE_NOTICE, window);

			// close all Editors with deleted documents
			var _usedEditors = WE().layout.weEditorFrameController.getEditorsInUse(),
							_delete_table = WE().consts.tables.FILE_TABLE,
							_delete_Ids = ',' + response.deletedItems.join() + ',',
							frameId;

			for (frameId in _usedEditors) {
				if (_delete_table == _usedEditors[frameId].getEditorEditorTable() && (_delete_Ids.indexOf(',' + _usedEditors[frameId].getEditorDocumentId() + ',') != -1)) {
					_usedEditors[frameId].setEditorIsHot(false);
					WE().layout.weEditorFrameController.closeDocument(frameId);
				}
			}

			//reload tree
			if (top.opener.top.treeData) {
				top.opener.we_cmd("load", top.opener.top.treeData.table, 0);
			}

			// reset busy
			document.getElementById("resetBusy" + weSearch.conf.whichsearch).innerHTML = '';

			weSearch.search(true, true);
		},
		failure: function (o) {
			top.console.log("callback failure");
		}
	}
};