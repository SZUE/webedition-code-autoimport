weSearch = {
	we_const: {// FIXME: move most important constants to webedition.js
		SEARCH_DOCS: '',
		SEARCH_TMPL: '',
		SEARCH_MEDIA: '',
		SEARCH_ADV: ''
	},
	conf: {
		whichsearch: '',
		editorBodyFrame: '',
		ajaxURL: '',
		tab: 0,
		modelClassName: '',
		modelID: 0,
		modelIsFolder: 0,
		showSelects: 0,
		rows: 0,
		we_transaction: '',
		checkRightTempTable: 0,
		checkRightDropTable: 0
	},
	elems: {
		btnTrash: '',
		btnSelector: '',
		fieldSearch: '',
		selStatus: '',
		selSpeicherart: '',
		selLocation: '',
		selModFields: '',
		selUsers: '',
		pixel: '',
		searchFields: ''
	},
	g_l: {
		noTempTableRightsSearch: '',
		nothingCheckedAdv: '',
		nothingCheckedTmplDoc: '',
		buttonSelectValue: '',
		versionsResetAllVersionsOK: '',
		versionsNotChecked: '',
		searchtool__notChecked: '',
		searchtool__publishOK: ''
	},
	elem: null,
	init: function (we_const, conf, g_l) {
		top.console.debug("running");
		/*
		 this.we_const = we_const;
		 this.conf = conf;
		 this.g_l = g_l;

		 if (this.conf.editorBodyFrame.loaded) {
		 this.sizeScrollContent();
		 } else {
		 setTimeout(function(){
		 this.init();
		 }, 10);
		 }
		 */

		//document.onmousemove = this.updateElem();
	},
	ajaxCallbackResultList: {
		success: function (o) {
			if (o.responseText !== undefined && o.responseText !== '') {
				weSearch.conf.editorBodyFrame.document.getElementById('scrollContent_' + weSearch.conf.whichsearch).innerHTML = o.responseText;
				weSearch.makeAjaxRequestParametersTop();
				weSearch.makeAjaxRequestParametersBottom();
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
	search: function (newSearch) {
		if (!this.conf.checkRightTempTable && !this.conf.heckRightDropTable) {
			top.we_showMessage(this.g_l.noTempTableRightsSearch, WE().consts.message.WE_MESSAGE_NOTICE, window);
			top.console.debug("hier??");
			return;
		}

		var Checks = [], m = 0, i, table;

		switch (this.conf.whichsearch) {
			case this.we_const.SEARCH_ADV:
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
					top.we_showMessage(this.g_l.nothingCheckedAdv, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
				break;
			case this.we_const.SEARCH_DOCS:
			case this.we_const.SEARCH_MEDIA:
				//top.console.debug(this.conf.editorBodyFrame.document.we_form.elements);
				for (i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
					table = this.conf.editorBodyFrame.document.we_form.elements[i].name;
					if (table === 'searchForText' + this.conf.whichsearch || table === 'searchForTitle' + this.conf.whichsearch || table === 'searchForContent' + this.conf.whichsearch) {
						if (encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value) == 1) {
							Checks[m] = encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
							m++;
						}
					}
				}

				//top.console.debug('cl', Checks.length);
				if (Checks.length === 0) {
					//FIXME: dirty fix => allow to search without searchForXX when no searchFieldsMediaSearch[0] is empty
					if (this.conf.editorBodyFrame.document.we_form.elements['searchMediaSearch[0]'].value) {
						top.we_showMessage(this.g_l.nothingCheckedTmplDoc, WE().consts.message.WE_MESSAGE_ERROR, window);
					} else {
						Checks[0] = '';
					}
				}
				break;
			case this.we_const.SEARCH_TMPL:
				for (i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
					table = this.conf.editorBodyFrame.document.we_form.elements[i].name;
					if (table == 'searchForText' + this.conf.whichsearch || table === 'searchForContent' + this.conf.whichsearch) {
						if (encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value) == 1) {
							Checks[m] = encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
							m++;
						}
					}
				}
				if (Checks.length === 0) {
					top.we_showMessage(this.g_l.nothingCheckedTmplDoc, WE().consts.message.WE_MESSAGE_ERROR, window);
				}
				break;
		}

		if (Checks.length !== 0) {
			if (newSearch) {
				this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value = 0;
			}
			this.makeAjaxRequestDoclist();
		}
	},
	makeAjaxRequestDoclist: function () {
		this.getMouseOverDivs();
		var args = '', newString = '';

		for (var i = 0, elem; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			elem = this.conf.editorBodyFrame.document.we_form.elements[i];
			if (elem.type === "radio" && !elem.checked) {
				continue;
			}
			newString = elem.name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(elem.value);
		}
		this.conf.editorBodyFrame.document.getElementById('scrollContent_' + this.conf.whichsearch).innerHTML = '<table border="0" width="100%" height="100%"><tr><td align="center"><i class="fa fa-2x fa-spinner fa-pulse"></i><div id="scrollActive"></div></td></tr></table>';
		YAHOO.util.Connect.asyncRequest('POST', this.conf.ajaxURL, this.ajaxCallbackResultList, 'protocol=json&cns=tools/weSearch&tab=' + this.conf.tab + '&cmd=GetSearchResult&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	makeAjaxRequestParametersTop: function () {
		var args = '', newString = "";

		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		YAHOO.util.Connect.asyncRequest('POST', this.conf.ajaxURL, this.ajaxCallbackParametersTop, 'protocol=json&cns=tools/weSearch&tab=' + this.conf.tab + '&cmd=GetSearchParameters&position=top&whichsearch=' + this.conf.whichsearch + '&classname' + this.conf.modelClassName + '=&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	makeAjaxRequestParametersBottom: function () {
		var args = '', newString = '';
		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		YAHOO.util.Connect.asyncRequest('POST', this.conf.ajaxURL, this.ajaxCallbackParametersBottom, 'protocol=json&cns=tools/weSearch&tab=' + this.conf.tab + '&cmd=GetSearchParameters&position=bottom&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	getMouseOverDivs: function () {
		var args = '', newString = '';
		for (var i = 0; i < this.conf.editorBodyFrame.document.we_form.elements.length; i++) {
			newString = this.conf.editorBodyFrame.document.we_form.elements[i].name;
			args += '&we_cmd[' + encodeURI(newString) + ']=' + encodeURI(this.conf.editorBodyFrame.document.we_form.elements[i].value);
		}
		YAHOO.util.Connect.asyncRequest('POST', this.conf.ajaxURL, this.ajaxCallbackgetMouseOverDivs, 'protocol=json&cns=tools/weSearch&tab=' + this.conf.tab + '&cmd=GetMouseOverDivs&whichsearch=' + this.conf.whichsearch + '&classname=' + this.conf.modelClassName + '&id=' + this.conf.modelID + '&we_transaction=' + this.conf.we_transaction + args);
	},
	setView: function (value) {
		this.conf.editorBodyFrame.document.we_form.elements['setView' + this.conf.whichsearch].value = value;
		this.search(false);
	},
	showImageDetails: function (picID) {
		this.elem = document.getElementById(picID);
		this.elem.style.visibility = 'visible';
	},
	hideImageDetails: function (picID) {
		this.elem = document.getElementById(picID);
		this.elem.style.visibility = 'hidden';
		this.elem.style.left = '-9999px';
	},
	updateElem: function (e) {
		var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight,
						w = window.innerWidth ? window.innerWidth : document.body.offsetWidth,
						x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX,
						y = (document.all) ? window.event.y + document.body.scrollTop : e.pageY,
						elemWidth = 0, elemHeight = 0;

		if (this.elem !== null && elem.style.visibility == 'visible') {
			elemWidth = this.elem.offsetWidth;
			elemHeight = this.elem.offsetHeight;
			this.elem.style.left = (x + 10) + 'px';
			this.elem.style.top = (y - 120) + 'px';

			if ((w - x) < 400 && (h - y) < 250) {
				this.elem.style.left = (x - elemWidth - 10) + 'px';
				this.elem.style.top = (y - elemHeight - 10) + 'px';
			}
			else if ((w - x) < 400) {
				this.elem.style.left = (x - elemWidth - 10) + 'px';
			}
			else if ((h - y) < 250) {
				this.elem.style.top = (y - elemHeight - 10) + 'px';
			}
		}
	},
	absLeft: function (el) {
		return (el.offsetParent) ? el.offsetLeft + this.absLeft(el.offsetParent) : el.offsetLeft;
	},
	absTop: function (el) {
		return (el.offsetParent) ? el.offsetTop + this.absTop(el.offsetParent) : el.offsetTop;
	},
	next: function (anzahl) {
		var scrollActive = document.getElementById('scrollActive');
		if (scrollActive === null) {
			this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value = parseInt(this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value) + anzahl;
			this.search(false);
		}
	},
	back: function (anzahl) {
		var scrollActive = document.getElementById('scrollActive');
		if (scrollActive === null) {
			this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value = parseInt(this.conf.editorBodyFrame.document.we_form.elements['searchstart' + this.conf.whichsearch].value) - anzahl;
			this.search(false);
		}
	},
	getMainWindow: function () {
		if (top.opener && top.opener.top.weEditorFrameController) {
			return top.opener.top;
		} else if (top.opener.top.opener && top.opener.top.opener.top.weEditorFrameController) {
			return top.opener.top.opener.top;
		} else if (top.opener.top.opener.top.opener && top.opener.top.opener.top.opener.top.weEditorFrameController) {
			return top.opener.top.opener.top.opener.top;
		}
	},
	openToEdit: function (tab, id, contentType) {
		this.getMainWindow().weEditorFrameController.openDocument(tab, id, contentType);
	},
	openModule: function (mod, id) {
		this.getMainWindow().we_cmd(mod + '_edit_ifthere', id);
	},
	openCategory: function (id) {
		this.getMainWindow().we_cmd('editCat', id);
	},
	setOrder: function (order, whichSearch) {
		//FIXME: ordering media search does not work yet
		if (whichSearch === 'MediaSearch') {
			alert('ordering columns temporarily disabled');
			return;
		}

		var columns = ['Text', 'SiteTitle', 'CreationDate', 'ModDate'],
						deleteArrow, arrow, foo;

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
		if (!this.conf.modelIsFolder) {
			return;
		}

		if (this.conf.editorBodyFrame.loaded) {
			var scrollheight;
			if (this.conf.whichsearch === this.we_const.SEARCH_ADV) {
				scrollheight = 140;
			} else {
				scrollheight = 170;
			}

			var elem = document.getElementById('filterTable' + this.conf.whichsearch),
							//newID = elem.rows.length - 1,
							h = window.innerHeight ? window.innerHeight : document.body.offsetHeight,
							scrollContent = document.getElementById("scrollContent_' + whichSearch + '"),
							heightDiv = 180;

			if ((h - heightDiv) > 0) {
				scrollContent.style.height = (h - heightDiv) + 'px';
			}

			if ((scrollContent.offsetHeight - scrollheight) > 0) {
				scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) + 'px';
			}
		} else {
			setTimeout(this.sizeScrollContent, 1000);
		}
	},
	newinputAdvSearch: function () {
		var elem = document.getElementById('filterTable' + this.conf.whichsearch),
						newID = elem.rows.length - 1,
						scrollContent = document.getElementById('scrollContent_' + this.conf.whichsearch),
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

			elem.appendChild(newRow);
		}
	},
	newinpuMediaSearch: function () {
		top.console.debug('is fn');

	},
	getCell: function (type, rowID) {
		var cell = document.createElement('TD'),
						html;
		switch (type) {
			case 'delButton':
				cell.setAttribute('id', 'td_delButton[' + rowID + ']');
				cell.innerHTML = this.elems.btnTrash.replace(/__we_new_id__/g, rowID);
				break;
			case 'searchAdvSearch':
				cell.setAttribute('id', 'td_searchAdvSearch[' + rowID + ']');
				cell.innerHTML = this.elems.fieldSearch.replace(/__we_new_id__/g, rowID).replace(/__we_read_only__/g, arguments[2] ? 'readonly="1" ' : '');
				break;
			case 'locationAdvSearch':
				cell.setAttribute("id", "td_locationAdvSearch[" + rowID + "]");
				cell.innerHTML = this.elems.selLocation.replace(/__we_new_id__/g, rowID);
				break;
			case 'searchMediaSearch':
				cell.setAttribute('id', 'td_searchMediaSearch[' + rowID + ']');
				cell.innerHTML = this.elems.fieldSearch.replace(/__we_new_id__/g, rowID).replace(/__we_read_only__/g, arguments[2] ? 'readonly="1" ' : '');
				break;
			case 'locationMediaSearch':
				cell.setAttribute("id", "td_locationMediaSearch[" + rowID + "]");
				cell.innerHTML = this.elems.selLocation.replace(/__we_new_id__/g, rowID);
				break;
		}
		return cell;

	},
	delRow: function (id) {
		var scrollContent = document.getElementById('scrollContent_' + this.conf.whichsearch),
						elem = document.getElementById("filterTable" + this.conf.whichsearch);

		if (elem) {
			var trows = elem.rows,
							rowID = 'filterRow_' + id;

			for (var i = 0; i < trows.length; i++) {
				if (rowID == trows[i].id) {
					elem.deleteRow(i);
				}
			}
		}
	},
	changeit: function (value, rowNr) {
		setTimeout(function () {
			// just wait 1 ms!
		}, 1);

		var setValue = document.getElementsByName('search' + this.conf.whichsearch + '[' + rowNr + ']')[0].value;
		var from = document.getElementsByName('hidden_searchFields' + this.conf.whichsearch + '[' + rowNr + ']')[0].value;
		var row = document.getElementById('filterRow_' + rowNr);
		var locationTD = document.getElementById('td_location' + this.conf.whichsearch + '[' + rowNr + ']');
		var searchTD = document.getElementById('td_search' + this.conf.whichsearch + '[' + rowNr + ']');
		var delButtonTD = document.getElementById('td_delButton[' + rowNr + ']');
		var location = document.getElementById('location' + this.conf.whichsearch + '[' + rowNr + ']');

		switch (value) {
			case 'Content':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);

				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				row.appendChild(this.getCell('search' + this.conf.whichsearch, this.conf.rows));
				row.appendChild(this.getCell('delButton', rowNr));
				document.getElementById("search" + this.conf.whichsearch + "[" + rowNr + "]").value = setValue;
				break;
			case 'temp_category':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);

				var innerhtml = '<table class="default"><tbody><tr>' +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search" + this.conf.whichsearch + "ParentID[' + rowNr + ']" type="hidden"></td><td>' + this.elems.pixel + '</td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, WE().consts.tables.CATEGORY_TABLE).replace(/__we_selector__/, 'we_selector_category') + '</td>' +
								'</tr></tbody></table>';

				var cell = document.createElement('TD');
				cell.setAttribute('id', 'td_search" + this.conf.whichsearch + "[' + rowNr + ']');
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}
				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'temp_template_id':
			case 'MasterTemplateID':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);

				var innerhtml = "<table class=\"default\"><tbody><tr>" +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td>' + this.elems.pixel + '</td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, WE().consts.tables.TEMPLATES_TABLE).replace(/__we_selector__/, 'we_selector_document') + '</td>' +
								"</tr></tbody></table>";

				cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}
				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'ParentIDDoc':
			case 'ParentIDObj':
			case 'ParentIDTmpl':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);

				var table = value === 'ParentIDDoc' ? WE().consts.tables.FILE_TABLE : (value === 'ParentIDObj' ? WE().consts.tables.OBJECT_FILES_TABLE : WE().consts.tables.TEMPLATES_TABLE);

				var innerhtml = "<table class=\"default\"><tbody><tr>" +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ') + '</td>' +
								'<td><input value="" name="search' + this.conf.whichsearch + 'ParentID[' + rowNr + ']" type="hidden"></td><td>' + this.elems.pixel + '</td>' +
								'<td>' + this.elems.btnSelector.replace(/__we_new_id__/g, rowNr).replace(/__we_sel_table__/, table).replace(/__we_selector__/, 'we_selector_directory') + '</td>' +
								"</tr></tbody></table>";

				cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}
				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'Status':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);
				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				var cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = this.elems.selStatus.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'Speicherart':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);
				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				var cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = this.elems.selSpeicherart.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'Published':
			case 'CreationDate':
			case 'ModDate':

				row.removeChild(locationTD);
				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr));
				row.removeChild(searchTD);

				// FIXME: move datepicker-button to search_view
				var innerhtml = "<table id=\"search" + this.conf.whichsearch + "[" + rowNr + "]_cell\" class=\"default\"><tbody><tr>" +
								"<td></td>" +
								"<td></td>" +
								'<td>' + this.elems.fieldSearch.replace(/__we_new_id__/g, rowNr).replace(/__we_read_only__/, 'readonly="1" ').replace('width: 170px', 'width: 100px') + '</td>' +
								"<td>&nbsp;</td>" +
								"<td><a href=\"#\"><button id=\"date_picker_from" + rowNr + "\" class=\"weBtn\"><i class='fa fa-lg fa-calendar'></i></button></a></td>" +
								"</tr></tbody></table>";


				cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = innerhtml;
				row.appendChild(cell);

				Calendar.setup({inputField: "search" + this.conf.whichsearch + "[" + rowNr + "]", ifFormat: "%d.%m.%Y", button: "date_picker_from" + rowNr + "", align: "Tl", singleClick: true});

				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				row.appendChild(this.getCell('delButton', rowNr));

				break;
			case 'allModsIn':// FIXME: does nit work yet
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);
				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				var cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = this.elems.selModFields.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				break;
			case 'modifierID':
				if (locationTD !== null) {
					location.disabled = true;
				}
				row.removeChild(searchTD);
				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				var cell = document.createElement("TD");
				cell.setAttribute("id", "td_search" + this.conf.whichsearch + "[" + rowNr + "]");
				cell.innerHTML = this.elems.selUsers.replace(/__we_new_id__/g, rowNr);
				row.appendChild(cell);

				row.appendChild(this.getCell('delButton', rowNr));
				break;
			default:
				row.removeChild(searchTD);

				if (locationTD !== null) {
					row.removeChild(locationTD);
				}
				if (delButtonTD !== null) {
					row.removeChild(delButtonTD);
				}

				row.appendChild(this.getCell('location' + this.conf.whichsearch, rowNr));
				row.appendChild(this.getCell('search' + this.conf.whichsearch, rowNr));
				row.appendChild(this.getCell('delButton', rowNr));

				document.getElementById("search" + this.conf.whichsearch + "[" + rowNr + "]").value = setValue;
		}
		;

		switch (from) {
			case "allModsIn":
			case "MasterTemplateID":
			case "ParentIDTmpl":
			case "ParentIDObj":
			case "ParentIDDoc":
			case "temp_template_id":
			case "ContentType":
			case "temp_category":
			case "Status":
			case "Speicherart":
			case "Published":
			case "CreationDate":
			case "ModDate":
				document.getElementById("search" + this.conf.whichsearch + "[" + rowNr + "]").value = "";
				//|| value =="allModsIn" || value =="MasterTemplateID" || value=="ParentIDTmpl" || value=="ParentIDObj" || value=="ParentIDDoc" || value=="temp_template_id" || value=="ContentType" || value=="temp_category" || value=="Status" || value=="Speicherart" || value=="Published" || value=="CreationDate" || value=="ModDate") {
			default:
				document.getElementById("search" + this.conf.whichsearch + "[" + rowNr + "]").value = setValue;
		}
		;

		document.getElementsByName("hidden_searchFields" + this.conf.whichsearch + "[" + rowNr + "]")[0].value = value;

	},
	ajaxCallbackResetVersion: {
		success: function (o) {
			//top.we_cmd("save_document","' . $GLOBALS['we_transaction'] . '","0","1","0", "","");
			top.we_showMessage(weSearch.g_l.versionsResetAllVersionsOK, WE().consts.message.WE_MESSAGE_NOTICE, window);

			// reload current document => reload all open Editors on demand
			var _usedEditors = top.opener.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {
				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			if (top.opener.treeData) {
				top.opener.we_cmd("load", top.opener.treeData.table, 0);
			}
			document.getElementById("resetBusy" + this.conf.whichsearch).innerHTML = "";
		},
		failure: function (o) {
		}
	},
	resetVersionAjax: function (id, documentID, version, table) {
		document.getElementById("resetBusy" + this.conf.whichsearch).innerHTML = "<table border='0' width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i><div id='scrollActive'></div></td></tr></table>";

		YAHOO.util.Connect.asyncRequest("POST", this.conf.ajaxURL, this.ajaxCallbackResetVersion, "protocol=json&cns=versionlist&cmd=ResetVersion&id=" + id + "&documentID=" + documentID + "&version=" + version + "&documentTable=" + table + "&we_transaction=' . $GLOBALS['we_transaction'] . '");
	},
	resetVersions: function () {
		var checkboxes = [];
		check = false;
		var m = 0;
		for (var i = 0; i < document.we_form.elements.length; i++) {
			var table = document.we_form.elements[i].name;
			if (table.substring(0, 12) == "resetVersion") {
				if (document.we_form.elements[i].checked == true) {
					checkboxes[m] = document.we_form.elements[i].value;
					check = true;
					m++;
				}
			}
		}

		if (check == false) {
			top.we_showMessage(this.g_l.versionsNotChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		} else {
			Check = confirm("' . g_l('versions', '[resetVersionsSearchtool]') . '");
			if (Check == true) {
				var vals = "";
				for (var i = 0; i < checkboxes.length; i++) {
					if (vals != "")
						vals += ",";
					vals += checkboxes[i];
					if (document.getElementById("publishVersion_" + checkboxes[i]) != null) {
						if (document.getElementById("publishVersion_" + checkboxes[i]).checked) {
							vals += "___1";
						}
						else {
							vals += "___0";
						}
					}
				}
				this.resetVersionAjax(vals, 0, 0, 0);
			}
		}
	},
	setview: function (setView) {
		/*
		 document.we_form.setView.value = setView;
		 this.search(false);
		 */
	},
	checkAllActionChecks: function () {
		var checkAll = document.getElementsByName("action_all_" + this.conf.whichsearch);
		var checkboxes = document.getElementsByName((this.conf.whichsearch == this.we_const.SEARCH_MEDIA ? 'delete_docs_MediaSearch' : 'publish_docs_DocSearch'));
		var check = false;

		if (checkAll[0].checked) {
			check = true;
		}
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = check;
		}
	},
	publishDocs: function (whichSearch) {
		var checkAll = document.getElementsByName("publish_all_" + whichSearch);
		var checkboxes = document.getElementsByName("publish_docs_" + whichSearch);
		var check = false;

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				check = true;
				break;
			}
		}

		if (checkboxes.length == 0) {
			check = false;
		}

		if (check == false) {//searchtool__notChecked
			top.we_showMessage(this.g_l.searchtool__notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
		else {
			Check = confirm("' . g_l('searchtool', '[publish_docs]') . '");
			if (Check == true) {
				this.publishDocsAjax(whichSearch);
			}
		}
	},
	deleteDocs: function (whichSearch) {
		var checkAll = document.getElementsByName("action_all_" + whichSearch);
		var checkboxes = document.getElementsByName("delete_docs_" + whichSearch);
		var check = false;

		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				check = true;
				break;
			}
		}

		if (checkboxes.length == 0) {
			check = false;
		}

		if (check == false) {//searchtool__notChecked
			top.we_showMessage(this.g_l.searchtool__notChecked, WE().consts.message.WE_MESSAGE_NOTICE, window);
		}
		else {
			Check = confirm("you really want to delete them?\n=> coming soon...");
			if (Check == true) {
				//this.publishDocsAjax(whichSearch);
			}
		}
	},
	toggleAdditionalContent: function (btn, id) {
		var elem = document.getElementById('infoTable_' + id);

		if (elem) {
			elem.style.display = elem.style.display === 'block' ? 'none' : 'block';
		}
		btn.firstChild.src = WE().consts.dirs.IMAGE_DIR + 'button/icons/direction_' + (elem.style.display === 'block' ? 'down' : 'right') + '.gif';
	},
	ajaxCallbackPublishDocs: {
		success: function (o) {

			// reload current document => reload all open Editors on demand

			var _usedEditors = top.opener.weEditorFrameController.getEditorsInUse();
			for (frameId in _usedEditors) {
				if (_usedEditors[frameId].getEditorIsActive()) { // reload active editor
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
					_usedEditors[frameId].setEditorIsActive(true);
				} else {
					_usedEditors[frameId].setEditorReloadAllNeeded(true);
				}
			}
			_multiEditorreload = true;

			//reload tree
			if (top.opener.treeData) {
				top.opener.we_cmd("load", top.opener.treeData.table, 0);
			}
			document.getElementById("resetBusy" + this.conf.whichsearch).innerHTML = "";
			document.getElementById("resetBusyDocSearch").innerHTML = "";
			top.we_showMessage(weSearch.g_l.searchtool__publishOK, WE().consts.message.WE_MESSAGE_NOTICE, window);

		},
		failure: function (o) {
			//alert("Failure");
		}
	},
	publishDocsAjax: function (whichSearch) {
		var args = "";
		var check = "";
		var checkboxes = document.getElementsByName("publish_docs_" + whichSearch);
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].checked) {
				if (check != "")
					check += ",";
				check += checkboxes[i].value;
			}
		}
		args += "&we_cmd[0]=" + encodeURI(check);
		var scroll = document.getElementById("resetBusy" + whichSearch);
		scroll.innerHTML = "<table border='0' width='100%' height='100%'><tr><td align='center'><i class=\"fa fa-2x fa-spinner fa-pulse\"></i></td></tr></table>";

		YAHOO.util.Connect.asyncRequest("POST", this.conf.ajaxURL, this.ajaxCallbackPublishDocs, "protocol=json&cns=tools/weSearch&cmd=PublishDocs&" + args + "");

	},
	previewVersion: function (ID) {
		top.we_cmd("versions_preview", ID, 0);
		//new (WE().util.jsWindow)(top.window, "' . WEBEDITION_DIR . 'we/include/we_versions/weVersionsPreview.php?ID="+ID+"", "version_preview",-1,-1,1000,750,true,true,true,true);
	},
	calendarSetup: function (x) {
		for (i = 0; i < x; i++) {
			if (document.getElementById("date_picker_from" + i + "") != null) {
				//Calendar.setup({inputField:"search" + this.conf.whichsearch + "["+i+"]",ifFormat:"%d.%m.%Y",button:"date_picker_from"+i+"",align:"Tl",singleClick:true});
			}
		}
	}

};