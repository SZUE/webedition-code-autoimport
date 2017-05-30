/* global top, WE, doc */

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

WE().util.loadConsts(document, 'collection');

function WeCollection(win, dynamicVars){
	this.win = win;
	this.doc = win.document;
	this.we_doc = dynamicVars.doc;
	this.content = dynamicVars.content;
	this.gui = dynamicVars.gui;

	this.initGui();
}

WeCollection.prototype.styles = {
	// FIXME: use classes!
	standard: {
		border: '1px solid #888888',
		borderLast: '1px solid #cccccc',
		backgroundColor: '#ffffff'
	},
	okPrev: {
		border: '1px dotted #00ff00',
		borderLast: '1px dotted #00ff00',
		backgroundColor: '#fafffa'
	},
	nokPrev: {
		border: '1px dotted #ff0000',
		borderLast: '1px dotted #ff0000',
		backgroundColor: '#fffafa'
	}
};

WeCollection.prototype.dd = {
	fillEmptyRows: true,
	placeholder: null,
	counter: 0,
	isMoveItem: true,
	moveItem: {
		el: null,
		id: 0,
		index: 0,
		next: null,
		pos: 0,
		removed: false
	}
};

WeCollection.prototype.initGui = function () {
	this.gui.elements.container.grid = this.doc.getElementById('content_div_grid');
	this.gui.elements.container.list = this.doc.getElementById('content_div_list');
	this.gui.elements.divSlider = this.doc.getElementById('sliderDiv');
	this.gui.elements.spanNum = this.doc.getElementById('numSpan');

	var t = this;
	var btnsView = this.doc.getElementsByClassName('collection_btnView');
	for (var i = 0; i < btnsView.length; i++) {
		btnsView[i].addEventListener('click', this.setView.bind(this), true);
	}
	this.doc.getElementById('collection_slider').addEventListener('click', this.doZoomGrid.bind(this), false);
	this.doc.getElementsByName('collection_btnAddFromTree')[0].addEventListener('click', this.doClickAddItems.bind(this, false), false);

	this.renderView(true);
};

WeCollection.prototype.setView = function (evt) {
	var view = evt.target.nodeName === 'BUTTON' ? evt.target.name : evt.target.parentNode.name;

	switch (view) {
		case 'list':
		case 'minimal':
			this.gui.view = 'list';
			this.gui.viewSub = view === 'minimal' ? 'minimal' : 'broad';
			this.gui.elements.container.grid.style.display = 'none';
			this.gui.elements.container.list.style.display = 'block';
			this.gui.elements.divSlider.style.display = 'none';
			break;
		case 'grid':
		/* falls through */
		default:
			this.gui.view = view;
			this.gui.elements.container.grid.style.display = 'inline-block';
			this.gui.elements.container.list.style.display = 'none';
			this.gui.elements.divSlider.style.display = 'block';
			break;
	}

	this.doc.we_form['we_' + this.we_doc.docName + '_view'].value = this.gui.view;
	this.doc.we_form['we_' + this.we_doc.docName + '_viewSub'].value = (this.gui.viewSub === 'minimal' ? 'minimal' : 'broad');
	this.dd.counter = 0;
	this.renderView(true);
};

WeCollection.prototype.renderView = function (notSetHot) {
	this.gui.elements.container[this.gui.view].innerHTML = '';
	this.content.maxIndex = 0;

	for (var i = 0; i < this.content.collectionArr.length; i++) {
		var last = (i === (this.content.collectionArr.length) - 1) && (this.content.storage['item_' + this.content.collectionArr[i]].id === -1);
		this.insertItem(null, false, this.content.storage['item_' + this.content.collectionArr[i]], this, '', last, false);
	}
	this.reindexAndRetrieveCollection(notSetHot);
};

WeCollection.prototype.insertItem = function (elem, repaint, item, scope, color, last, insertBefore) {
	var t = scope ? scope : this,
		el = elem ? t.getItem(elem) : null,
		mustInsertPathCutLeft = false,
		div, newItem, blank, elPreview, btn;

	color = color ? color : false;
	item = item ? item : t.content.storage['item_-1'];
	repaint = repaint || false;
	++t.content.maxIndex;

	if (item.id && !t.content.storage['item_' + item.id]) {
		t.content.storage['item_' + item.id] = item;
	}

	div = this.doc.createElement("div");
	this.doc.body.appendChild(div); // we must append temporary div to a visible element to get offsetWidth of some sub elems

	// FIXME: reduce obsolete replacements for listMinimal
	var viewPlusSub = t.gui.view !== 'list' ? 'grid' : (t.gui.viewSub === 'minimal' ? 'listMinimal' : 'list');

	blank = WE().consts.collection.blankItem[viewPlusSub].replace(/##INDEX##/g, t.content.maxIndex).replace(/##ID##/g, item.id).replace(/##PATH##/g, item.path).
	//blank = this.blankItem[viewPlusSub].replace(/##INDEX##/g, t.content.maxIndex).replace(/##ID##/g, item.id).replace(/##PATH##/g, item.path).
		replace(/##CT##/g, item.ct).replace(/##ICONURL##/g, (item.icon ? item.icon.url.replace('%2F', '/') : '')).
		replace(/##NAME##/g, t.we_doc.docName).
		replace(/##ATTRIB_TITLE##/g, item.elements.attrib_title.Dat).replace(/##S_ATTRIB_TITLE##/g, item.elements.attrib_title.state).
		replace(/##ATTRIB_ALT##/g, item.elements.attrib_alt.Dat).replace(/##S_ATTRIB_ALT##/g, item.elements.attrib_alt.state).
		replace(/##META_TITLE##/g, item.elements.meta_title.Dat).replace(/##S_META_TITLE##/g, item.elements.meta_title.state).
		replace(/##META_DESC##/g, item.elements.meta_description.Dat).replace(/##S_META_DESC##/g, item.elements.meta_description.state);

	if (t.gui.view === 'list') {
		blank = blank.replace(/##W_ATTRIB_TITLE##/g, item.elements.attrib_title.write).replace(/##W_ATTRIB_ALT##/g, item.elements.attrib_alt.write).
			replace(/##W_META_TITLE##/g, item.elements.meta_title.write).replace(/##W_META_DESC##/g, item.elements.meta_description.write).
			replace(/##CLASS##/g, (t.gui.viewSub === 'minimal' ? 'minimalListItem' : 'broadListItem'));

		div.innerHTML = blank;

		if (item.id === -1) {
			var inners = div.getElementsByClassName('innerDiv');
			for (var i = 0; i < inners.length; i++) {
				inners[i].style.display = 'none';
			}
			div.getElementsByClassName('collectionItem_btnEdit')[0].disabled = 1;
			div.getElementsByClassName('divBtnSelect')[0].style.display = 'block';
		} else {
			div.getElementsByClassName('previewDiv')[0].innerHTML = '';
			if (t.gui.viewSub === 'minimal') {
				div.getElementsByClassName('colContentInput')[0].style.display = 'none';
				div.getElementsByClassName('colContentTextOnly')[0].style.display = 'inline-block';
				div.getElementsByClassName('divBtnEditTextOnly')[0].style.display = 'inline-block';
				mustInsertPathCutLeft = true;
			}
		}

		if ((t.gui.viewSub === 'minimal' || item.ct !== 'image/*') && item.id !== -1) {
			elPreview = div.getElementsByClassName('previewDiv')[0];
			elPreview.innerHTML = WE().util.getTreeIcon(item.ct, false, item.ext);
			elPreview.style.background = 'transparent';
			elPreview.style.display = 'block';
		}
		if (last) {
			div.getElementsByClassName('colControls')[0].style.display = 'none';
		}
	} else {
		blank = blank.replace(/##SHOWBTN##/g, (item.id === -1 ? 'block' : 'none'));

		div.innerHTML = blank;

		if (item.icon) {
			div.getElementsByClassName('divContent')[0].style.backgroundSize = Math.max(item.icon.sizeX, item.icon.sizeY) < t.gui.gridItemDimension.item ? 'auto' : 'contain';
		}

		if (item.ct !== 'image/*' && item.id !== -1) {
			elPreview = div.getElementsByClassName('divInner')[0];
			elPreview.innerHTML = WE().util.getTreeIcon(item.ct, false, item.ext) + '<div class="divTitle defaultfont" style="font-size:' + t.gui.gridItemDimension.font + 'px;">' + item.name + item.ext + '</div>';
			//<div class="divTitle defaultfont" style="font-size:10px;">Titel: ' + propDesc + '</div>';
			elPreview.getElementsByTagName('SPAN')[0].style.fontSize = t.gui.gridItemDimension.icon + 'px';
			elPreview.style.background = 'transparent';
			elPreview.style.display = 'block';
			elPreview.style.textAlign = 'left';
			elPreview.style.padding = '14% 0 0 10%';
		}

		div.firstChild.style.width = div.firstChild.style.height = t.gui.gridItemDimension.item + 'px';

		div.getElementsByClassName('toolbarAttribs')[0].style.display = t.gui.itemsPerRow > 5 ? 'none' : 'block';
		if (item.id === -1) {
			btn = div.getElementsByClassName('divInner')[0].firstChild;
			if (btn.tagName === 'BUTTON') {
				btn.style.fontSize = t.gui.gridItemDimension.btnFontsize + 'px';
				btn.style.height = t.gui.gridItemDimension.btnHeight + 'px';
			}
			div.getElementsByClassName('toolbarBtns')[0].removeChild(div.getElementsByClassName('toolbarBtns')[0].firstChild);
			div.getElementsByClassName('toolbarAttribs')[0].style.display = 'none';
		}
	}

	this.doc.body.removeChild(div);
	newItem = el ? t.gui.elements.container[t.gui.view].insertBefore(div.firstChild, (insertBefore ? el : el.nextSibling)) : t.gui.elements.container[t.gui.view].appendChild(div.firstChild);

	if (mustInsertPathCutLeft) {
		var colContentText = newItem.getElementsByClassName('colContentTextOnly')[0];
		t.addTextCutLeft(colContentText, item.path, colContentText.parentNode.offsetWidth - 10);
	}

	if (last) {
		newItem.setAttribute('name', 'lastItem_' + this.gui.view);
	}
	t.resetItemColors(newItem);
	t.addListenersToItem(viewPlusSub, newItem, last, t.content.maxIndex, item.id, item.type);

	if (repaint) {
		t.reindexAndRetrieveCollection();
	}

	return newItem;
};

WeCollection.prototype.addListenersToItem = function (viewPlusSub, elem, last, index, id, type) {
	var input, ctrls, space_left, space_right, view;

	switch (viewPlusSub) {
		case 'grid':
			view = 'grid';
			elem = elem.getElementsByClassName('divContent')[0];

			if (!last) {
				elem.addEventListener('mouseover', this.overMouse.bind(this, 'item', view, elem), false);
				elem.addEventListener('mouseout', this.outMouse.bind(this, 'item', view, elem), false);

				ctrls = elem.getElementsByClassName('divToolbar')[0];
				ctrls.addEventListener('mouseover', this.overMouse.bind(this, 'btns', view, ctrls), false);
				ctrls.addEventListener('mouseout', this.outMouse.bind(this, 'btns', view, ctrls), false);

				if (this.gui.isDragAndDrop) {
					space_right = this.getItem(elem).getElementsByClassName('divSpace_right')[0];
					space_right.addEventListener('drop', this.dropOnItem.bind(this, 'space_right', view, space_right, false), false);
					space_right.addEventListener('dragover', this.allowDrop.bind(this), false);
					space_right.addEventListener('dragenter', this.enterDrag.bind(this, 'space_right', view, space_right, last), false);
					space_right.addEventListener('dragleave', this.leaveDrag.bind(this, 'space_right', view, space_right), false);
					space_right.addEventListener('dblclick', this.dblClick.bind(this, 'space_right', view, space_right), false);

					space_left = this.getItem(elem).getElementsByClassName('divSpace_left')[0];
					space_left.addEventListener('drop', this.dropOnItem.bind(this, 'space_left', view, space_left, false), false);
					space_left.addEventListener('dragover', this.allowDrop.bind(this), false);
					space_left.addEventListener('dragenter', this.enterDrag.bind(this, 'space_left', view, space_left, last), false);
					space_left.addEventListener('dragleave', this.leaveDrag.bind(this, 'space_left', view, space_left), false);
					space_left.addEventListener('dblclick', this.dblClick.bind(this, 'space_left', view, space_left), false);
				}
			}
			break;
		case 'list':
			view = 'list';
			if (!last) {
				elem.getElementsByClassName('collectionItem_btnUp')[0].addEventListener('click', this.doClickUp.bind(this, elem), false);
				elem.getElementsByClassName('collectionItem_btnDown')[0].addEventListener('click', this.doClickDown.bind(this, elem), false);
			}

			elem.getElementsByClassName('collectionItem_btnAddFromTree')[0].addEventListener('click', this.doClickAddItems.bind(this, true), false);
			/*falls through*/
		case 'listMinimal':
			view = 'list';
			input = this.doc.getElementById('yuiAcInputItem_' + elem.id.substr(10));
			input.addEventListener('mouseover', this.doSetDraggable.bind(this, false));
			input.addEventListener('mouseout', this.doSetDraggable.bind(this, true));
			elem.getElementsByClassName('collectionItem_btnAdd')[0].addEventListener('click', this.doClickAdd.bind(this, elem), false);
			if (id === -1) {
				elem.getElementsByClassName('collectionItem_btnSelect')[0].addEventListener('click', this.doClickSelector.bind(this, index, view), false);
			}
			break;
	}

	if (id !== -1) {
		elem.getElementsByClassName('collectionItem_btnEdit')[0].addEventListener('click', this.doClickOpenToEdit.bind(this, id), false);
	} else {
		elem.getElementsByClassName('collectionItem_btnSelect')[0].addEventListener('click', this.doClickSelector.bind(this, index, viewPlusSub === 'grid' ? 'grid' : 'list'), false);
	}
	elem.getElementsByClassName('collectionItem_btnTrash')[0].addEventListener('click', this.doClickDelete.bind(this, elem), false);

	if (this.gui.isDragAndDrop) {
		if (!last) {
			elem.style.cursor = 'move';
			elem.draggable = true;
		}

		elem.addEventListener('dragleave', this.leaveDrag.bind(this, 'item', view, elem), false);
		elem.addEventListener('drop', this.dropOnItem.bind(this, 'item', view, elem, last), false);
		elem.addEventListener('dragenter', this.enterDrag.bind(this, 'item', view, elem, last), false);
		elem.addEventListener('dragover', this.allowDrop.bind(this), false);
		elem.addEventListener('dragstart', this.startMoveItem.bind(this, view), false);
		elem.addEventListener('dragend', this.dragEnd.bind(this), false);
	}
};

WeCollection.prototype.doClickUp = function (elem) {
	var el = this.getItem(elem);

	if (el.parentNode.firstChild !== el) {
		el.parentNode.insertBefore(el, el.previousSibling);
		this.reindexAndRetrieveCollection();
	}
};

WeCollection.prototype.doClickDown = function (elem) {
	var el = this.getItem(elem);
	var sib = el.nextSibling;

	if (true || sib) {
		el.parentNode.insertBefore(el.nextSibling, el);
		this.reindexAndRetrieveCollection();
	}
};

WeCollection.prototype.doClickAdd = function (elem, type, insertBefore) {
	var el = this.getItem(elem),
		num = 1;//this.doc.getElementById('numselect_' + el.id.substr(10)).value;

	for (var i = 0; i < num; i++) {
		el = this.insertItem(el, true, null, this, '', false, (insertBefore ? true : false));//(el, true);
	}
	this.reindexAndRetrieveCollection();
};

WeCollection.prototype.doClickSelector = function (index, view) {
	var selector = this.we_doc.docRemCT.replace(/,/g, '') === WE().consts.contentTypes.IMAGE ? 'we_selector_image' : 'we_selector_document';
	this.win.we_cmd(selector, this.we_doc.docDefaultDir, WE().consts.tables.TBL_PREFIX + this.we_doc.docRemTable, '', '', 'updateCollectionItem,' + index + ',' + view + ',' + this.getIsRecursive(), '', '', this.we_doc.docRemCT, 1, 0, 1);
};

WeCollection.prototype.doClickAddItems = function (usePos, e) {
	var el = usePos ? this.getItem(e.target) : null,
		index = el ? el.id.substr(10) : -1,
		pos = -1;

	if (el) {
		for (var i = 0; i < el.parentNode.childNodes.length; i++) {
			if (parseInt(el.parentNode.childNodes[i].id) === parseInt(el.id)) {
				pos = i;
				break;
			}
		}
	}

	top.we_cmd('addToCollection', 1, WE().consts.tables.TBL_PREFIX + this.we_doc.docRemTable, this.we_doc.docId, this.we_doc.docPath, index, pos, true, this.getIsRecursive());
};

WeCollection.prototype.doClickDelete = function (elem) {
	var el = this.getItem(elem);

	el.parentNode.removeChild(el);
	this.reindexAndRetrieveCollection();
};

WeCollection.prototype.doZoomGrid = function (evt) {
	var value = evt.target.value;
	var attribDivs = this.gui.elements.container.grid.getElementsByClassName('toolbarAttribs');
	var iconDivs = this.gui.elements.container.grid.getElementsByClassName('divInner'), next;

	this.gui.itemsPerRow = 7 - value;
	this.gui.gridItemDimension = this.gui.gridItemDimensions[this.gui.itemsPerRow];
	this.doc.we_form['we_' + this.we_doc.docName + '_itemsPerRow'].value = this.gui.itemsPerRow;

	for (var i = 0; i < this.gui.elements.container.grid.children.length; i++) {
		this.gui.elements.container.grid.children[i].style.width = this.gui.elements.container.grid.children[i].style.height = this.gui.gridItemDimension.item + 'px';
		//this.gui.elements.container['grid'].children[i].style.backgroundSize = Math.max(item.icon.sizeX, item.icon.sizeY) < this.gui.gridItemDimension.item ? 'auto' : 'contain';

		attribDivs[i].style.display = this.gui.itemsPerRow > 5 ? 'none' : 'block';
		if (iconDivs[i].firstChild.tagName === 'BUTTON') {
			iconDivs[i].firstChild.style.fontSize = this.gui.gridItemDimension.btnFontsize + 'px';
			iconDivs[i].firstChild.style.height = this.gui.gridItemDimension.btnHeight + 'px';
		} else {
			iconDivs[i].firstChild.style.fontSize = this.gui.gridItemDimension.icon + 'px';
			if ((next = iconDivs[i].firstChild.nextSibling)) {
				next.style.fontSize = this.gui.gridItemDimension.font + 'px';
			}
		}
	}
};

WeCollection.prototype.doSetDraggable = function (draggable, evt) {
	this.getItem(evt.target).draggable = draggable ? true : false;
};

WeCollection.prototype.doClickOpenToEdit = function (id) {
	var table = this.we_doc.docRemTable === 'tblFile' ? WE().consts.tables.FILE_TABLE : WE().consts.tables.OBJECT_FILES_TABLE,
		ct = this.content.storage['item_' + id].ct;
	WE().layout.weEditorFrameController.openDocument(table, id, ct);
};

WeCollection.prototype.getPlaceholder = function () {
	if (this.dd.placeholder !== null) {
		return this.dd.placeholder;
	}

	var ph = this.dd.placeholder = this.doc.createElement("div");
	this.dd.placeholder.style.backgroundColor = 'white';
	this.dd.placeholder.setAttribute("ondragover", "window.weCollectionEdit.allowDrop(event)");

	if (this.gui.view === 'grid') {
		this.dd.placeholder.setAttribute("ondrop", "window.weCollectionEdit.dropOnItem(\'item\',\'grid\', this, false, event)");
		//this.dd.placeholder.addEventListener('ondrop', this.dropOnItem.bind(this, 'item', 'grid', this.dd.placeholder, false), false);
		this.dd.placeholder.style.float = 'left';
		this.dd.placeholder.style.display = 'block';
		this.dd.placeholder.style.height = this.gui.gridItemDimension.item + 'px';
		this.dd.placeholder.style.width = this.gui.gridItemDimension.item + 'px';
		var inner = this.doc.createElement("div");
		inner.style.height = (this.gui.gridItemDimension.item - 14) + 'px';
		inner.style.width = (this.gui.gridItemDimension.item - 18) + 'px';
		inner.style.border = this.styles.standard.border;
		inner.style.borderStyle = 'dotted';
		this.dd.placeholder.appendChild(inner);
	} else {
		this.dd.placeholder.setAttribute("ondrop", "window.weCollectionEdit.dropOnItem(\'item\',\'grid\', this, false, event)");
		this.dd.placeholder.style.height = (this.gui.viewSub === 'minimal' ? '40px' : '90px');
		this.dd.placeholder.style.margin = '4px 0 0 0';
		this.dd.placeholder.style.border = this.styles.standard.border;
		this.dd.placeholder.style.borderStyle = 'dotted';
	}

	return this.dd.placeholder;
};

WeCollection.prototype.getItem = function (elem) {
	var itemClass = this.gui.view === 'grid' ? 'gridItem' : 'listItem';

	while (elem.className !== itemClass) {
		elem = elem.parentNode;
		if (elem.className === 'collection-content') {
			return false;
		}
	}

	return elem;
};

WeCollection.prototype.getItemId = function (elem) {
	var item = this.getItem(elem);

	return item ? item.id.substr(10) : 0;
};

WeCollection.prototype.getItemIndex = function (elem) {
	var item = this.getItem(elem);
	var tmp = item.getElementsByClassName('collectionItem_index')[0].id.split('_');

	return tmp[tmp.length - 1];
};

WeCollection.prototype.getIsRecursive = function () {
	return this.doc.we_form.elements['we_' + this.we_doc.docName + '_InsertRecursive'].value;
};

WeCollection.prototype.addItems = function (elem, items, notReplace, notReindex, insertBefore) {
	if (elem === undefined) {
		return false;
	}

	notReindex = notReindex ? true : false;

	var el = this.getItem(elem),
		index = el.id.substr(10),
		rowsFull = false,
		isFirstSet = notReplace !== undefined ? notReplace : false,
		itemsSet = [[], []],
		item, id;

	//set first item on drop row
	if (items.length) {
		while (!isFirstSet && items.length) {
			item = items.shift();
			if (this.we_doc.docIsDuplicates === 1 || this.content.collectionCsv.search(',' + item.id + ',') === -1) {
				var newEl = this.insertItem(el, false, item, this, '#00ee00', false, insertBefore);
				this.doClickDelete(el);
				el = newEl;
				itemsSet[0].push(item.id);
				isFirstSet = true;
				insertBefore = false;
			} else {
				itemsSet[1].push(item.id);
			}
		}
	}

	for (var i = 0; i < items.length; i++) {
		if (this.we_doc.docIsDuplicates || this.content.collectionCsv.search(',' + items[i].id + ',') === -1) {
			itemsSet[0].push(items[i].id);
			if (this.dd.fillEmptyRows && !rowsFull && el.nextSibling && el.nextSibling.id !== undefined && el.nextSibling.id.substr(0, 10) === this.gui.view + '_item_') {
				index = el.nextSibling.id.substr(10);
				id = this.gui.view === 'grid' ? el.nextSibling.childNodes[2].value : this.doc.getElementById('yuiAcResultItem_' + index).value;
				if (id === -1 || id === 0) {
					//TODO: use insertItem()!
					if (this.gui.view === 'grid') {
						el.nextSibling.childNodes[2].value = items[i].id;
						el.nextSibling.firstChild.style.background = 'url(' + items[i].icon.url('%2F', '/') + ') no-repeat center center';
						el.nextSibling.firstChild.style.backgroundSize = 'contain';
						el.nextSibling.firstChild.title = items[i].path;
					} else {
						this.doc.getElementById('yuiAcInputItem_' + index).value = items[i].path;
						this.doc.getElementById('yuiAcResultItem_' + index).value = items[i].id;
					}
					el = el.nextSibling;
					continue;
				} else {
					rowsFull = true;
				}
			}
			el = this.insertItem(el, false, items[i], null, '#00ee00', false, insertBefore);
			insertBefore = false;
		} else {
			itemsSet[1].push(items[i].id);
		}
	}
	this.reindexAndRetrieveCollection();
	return itemsSet;
};

WeCollection.prototype.reindexAndRetrieveCollection = function (notSetHot) {
	var ct = this.gui.elements.container[this.gui.view],
		val, btns_up, btns_down, btns_edit,
		labels = this.doc.getElementsByClassName(this.gui.view + '_label');

	btns_edit = ct.getElementsByClassName('collectionItem_btnEdit');
	if (this.gui.view === 'list') {
		btns_up = ct.getElementsByClassName('btn_up');
		btns_down = ct.getElementsByClassName('btn_down');
	}

	this.content.collectionCsv = ',';
	this.content.collectionArr = [];
	this.content.collectionCount = 0;

	for (var i = 0; i < ct.childNodes.length; i++) {
		switch (this.gui.view) {
			case 'grid':
				ct.childNodes[i].id = 'grid_item_' + (i + 1);
				//ct.childNodes[i].firstChild.style.display  = i % this.gui.itemsPerRow === 0 ? 'block' : 'none';
				//labels[i].id = 'label_' + (i+1);
				val = ct.childNodes.length > 1 ? parseInt(this.doc.we_form.collectionItem_we_id[i].value) : parseInt(this.doc.we_form.collectionItem_we_id.value);
				break;
			case 'list':
				val = parseInt(this.doc.getElementById('yuiAcResultItem_' + ct.childNodes[i].id.substr(10)).value);
				if (this.gui.viewSub !== 'minimal') {
					btns_up[i].disabled = i === 0;
					btns_down[i].disabled = (i === (ct.childNodes.length - 1));
				}
				break;
		}
		labels[i].innerHTML = i + 1;

		if (val === 0 || val === -1) {
			this.content.collectionCsv += -1 + ',';
			this.content.collectionArr.push(-1);
		} else {
			this.content.collectionCsv += val + ',';
			this.content.collectionArr.push(val);
			this.content.collectionCount++;
		}
	}
	this.gui.elements.spanNum.innerHTML = this.content.collectionCount;

	if (val !== -1) {
		this.insertItem(ct.lastChild, true, null, this, '', true, false);//elem, repaint, item, scope, color, last
	}

	if (!this.content.collectionName) {
		this.content.collectionName = (WE().consts.tables.TBL_PREFIX + this.we_doc.docRemTable === WE().consts.tables.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
	}
	this.doc.we_form.elements['we_' + this.we_doc.docName + this.content.collectionName].value = this.content.collectionCsv;
	if (!notSetHot) {
		WE().layout.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
	}
};

WeCollection.prototype.hideSpace = function (elem, type) { // TODO: use classes do define states!
	elem.style.width = '8px';
	elem.style.border = 'none';
	elem.style.backgroundColor = 'transparent';
	elem.style.margin = '0';

	switch(type){
		case 'space_right':
			elem.style.right = '0px';
			elem.previousSibling.style.right = '8px';
			if (elem.parentNode.nextSibling) {
				elem.parentNode.nextSibling.getElementsByClassName('divSpace_left')[0].style.left = '0px';
				elem.parentNode.nextSibling.getElementsByClassName('divContent')[0].style.left = '0px';
			}
			break;
		case 'space_left':
			elem.style.left = '0px';
			elem.nextSibling.style.left = '8px';
			if (elem.parentNode.previousSibling) {
				elem.parentNode.previousSibling.lastChild.style.right = '0px';
				elem.parentNode.previousSibling.getElementsByClassName('divSpace_right')[0].style.right = '0px';
				elem.parentNode.previousSibling.getElementsByClassName('divContent')[0].style.right = '8px';
			}
			break;
	}
};

WeCollection.prototype.resetColors = function (scope) {
	var t = scope || this;

	for (var i = 0; i < t.gui.elements.container[t.gui.view].childNodes.length; i++) {
		t.resetItemColors(t.gui.elements.container[t.gui.view].childNodes[i], false, t);
	}
};

WeCollection.prototype.resetItemColors = function (el, color, type) {
	if (!el) {
		return false;
	}
	color = color || 'standard';
	var set = this.styles[color],
		elem;

	switch (this.gui.view) {
		case 'grid':
			switch(type){
				case 'space_left':
					elem = el.getElementsByClassName('divSpace_left')[0];
					break;
				case 'space_right':
					elem = el.getElementsByClassName('divSpace_right')[0];
					break;
				default:
					elem = this.getItem(el).getElementsByClassName('divContent')[0];
			}

			elem.style.border = (el.getAttribute('name') === 'lastItem_grid' ? set.borderLast : set.border);
			elem.style.backgroundColor = set.backgroundColor;

			break;
		case 'list':
			el.style.border = (el.getAttribute('name') === 'lastItem_list' ? set.borderLast : set.border);
			el.firstChild.style.backgroundColor = set.backgroundColor;
			break;
	}
};

WeCollection.prototype.addTextCutLeft = function (elem, text, maxwidth) {
	if (!elem) {
		return;
	}

	maxwidth = maxwidth || 400;
	text = text ? text : '';
	var i = 2000;
	elem.innerHTML = text;
	while (elem.offsetWidth > maxwidth && i > 0) {
		text = text.substr(4);
		elem.innerHTML = '...' + text;
		--i;
	}
	return;
};

WeCollection.prototype.dblClick = function (type, view, elem) {
	var insertBefore = false;
	switch (type) {
		case 'space_left':
			if(parseInt(this.getItemId(elem)) === 1){
				insertBefore = true;
				
			} else {
				elem = this.getItem(elem).previousSibling;
			}
			/* fall through */
		case 'space_right':
			this.doClickAdd(elem, type, insertBefore);
			break;
		default:
	}
};

WeCollection.prototype.allowDrop = function (evt) {
	evt.preventDefault();
};

WeCollection.prototype.enterDrag = function (type, view, elem, last, evt) {
	var el = this.getItem(elem);
	var data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : WE().layout.dragNDrop.dataTransfer.text.split(',');
	var c, newPos;

	if (this.gui.view === 'grid' && type === 'item') {
		this.outMouse(type, this.gui.view, elem);
	}

	switch (data[0]) {
		case 'moveItem':
			if (!last && type === 'item') {
				c = this.gui.elements.container[this.gui.view];

				if (!this.dd.moveItem.removed) {
					newPos = Array.prototype.indexOf.call(c.children, el);
					c.removeChild(this.dd.moveItem.el);
					c.insertBefore(this.getPlaceholder(), c.childNodes[newPos + (newPos >= this.dd.moveItem.pos ? 0 : -1)]);
					this.dd.moveItem.removed = true;
					return false;
				}

				newPos = Array.prototype.indexOf.call(c.children, el);
				c.removeChild(this.getPlaceholder());
				c.insertBefore(this.getPlaceholder(), c.childNodes[newPos]);
			}
			break;
		case 'dragItem':
		case 'dragFolder':
			if (this.gui.view === 'grid') {
				this.resetColors();
				switch (type) {
					case 'item':
						this.dd.counter++;
						break;
					case 'space_right':
						// TODO: use classes
						if (elem.parentNode.id.substr(10) % this.gui.itemsPerRow === 0) {
							elem.style.width = '36px';
							elem.previousSibling.style.right = '42px';
							elem.style.margin = '0 0 0 4px';
						} else {
							elem.style.width = '48px';
							elem.style.right = '-30px';
							elem.style.margin = '0 4px 0 4px';
							elem.previousSibling.style.right = '28px';
							if (elem.parentNode.nextSibling) {
								elem.parentNode.nextSibling.getElementsByClassName('divSpace_left')[0].style.left = '50px';
								elem.parentNode.nextSibling.getElementsByClassName('divContent')[0].style.left = '30px';
							}
						}
						break;
					case 'space_left':
						// TODO: use classes
						if (elem.parentNode.id.substr(10) % this.gui.itemsPerRow === 1) {
							elem.style.width = '36px';
							elem.nextSibling.style.left = '42px';
							elem.style.margin = '0 4px 0 0';
						} else {
							elem.style.width = '48px';
							elem.style.left = '-28px';
							elem.style.margin = '0 4px 0 4px';
							elem.nextSibling.style.left = '30px';
							if (elem.parentNode.previousSibling) {
								elem.parentNode.previousSibling.getElementsByClassName('divSpace_right')[0].style.right = '50px';
								elem.parentNode.previousSibling.getElementsByClassName('divContent')[0].style.right = '28px';
							}
						}
						break;
				}
				if (data[0] === 'dragFolder' || (!this.we_doc.docRemCT || data[3] === WE().consts.contentTypes.FOLDER || this.we_doc.docRemCT.search(',' + data[3]) !== -1)) {
					this.resetItemColors(el, 'okPrev', type);
				} else {
					this.resetItemColors(el, 'nokPrev', type);
				}
			} else {
				this.dd.counter++;
				this.resetColors();
				switch (type) {
					case 'item':
						if (data[0] === 'dragFolder' || (!this.we_doc.docRemCT || this.we_doc.docRemCT.search(',' + data[3]) !== -1)) {
							this.resetItemColors(el, 'okPrev');
						} else {
							this.resetItemColors(el, 'nokPrev');
						}
						break;
				}
			}
			break;
		default:
			return;
	}
};

WeCollection.prototype.leaveDrag = function (type, view, elem, evt) {
	if (this.gui.view === 'list') {
		this.dd.counter--;
		if (this.dd.counter === 0) {
			this.resetItemColors(elem);
		}
	} else {
		var data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : WE().layout.dragNDrop.dataTransfer.text.split(',');
		switch (data[0]) {
			case 'dragItem':
			case 'dragFolder':
				switch (type) {
					case 'item':
						this.dd.counter--;
						if (this.dd.counter === 0) {
							this.resetItemColors(elem);
						}
						break;
					case 'space_left':
					case 'space_right':
						this.hideSpace(elem, type);
						break;
				}
				break;
			default:
				return;
		}
	}
};

WeCollection.prototype.overMouse = function (type, view, elem) {
	if (view === 'grid') {
		switch (type) {
			case 'item':
				elem.lastChild.style.display = 'block';
				break;
			case 'btns':
				elem.style.opacity = '1';
				break;
		}
	}
};

WeCollection.prototype.outMouse = function (type, view, elem) {
	if (view === 'grid') {
		switch (type) {
			case 'item':
				elem.lastChild.style.display = 'none';
				break;
			case 'btns':
				elem.style.opacity = '0.8';
				break;
		}
	}
};

WeCollection.prototype.startMoveItem = function (view, evt) {
	var elem = this.getItem(evt.target);
	var position = Array.prototype.indexOf.call(this.gui.elements.container[view].children, elem);
	this.gui.view = view;
	this.dd.isMoveItem = true;
	this.dd.moveItem.el = elem;
	this.dd.moveItem.id = elem.id;
	this.dd.moveItem.index = parseInt(elem.id.substr(10));
	this.dd.moveItem.next = elem.nextSibling;
	this.dd.moveItem.pos = position;
	this.dd.moveItem.removed = false;

	WE().layout.dragNDrop.dataTransfer.text = 'moveItem,' + elem.id;
	evt.dataTransfer.setData('text', 'moveItem,' + elem.id);

	if (this.gui.view === 'grid') {
		this.outMouse('item', this.gui.view, elem.firstChild);
	}
};

WeCollection.prototype.dropOnItem = function (type, view, elem, last, evt) {
	evt.preventDefault();

	var data = [], el, index;
	var t = this;

	if (!evt.dataTransfer.getData("text") && evt.dataTransfer.files.length === 1) {
		data[0] = 'dragItemFromExtern';
	} else {
		data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : WE().layout.dragNDrop.dataTransfer.text.split(',');
	}

	switch (data[0]) {
		case 'moveItem':
			if (!last) {
				this.dd.isMoveItem = false;
				if (this.dd.moveItem.el !== this.getItem(elem)) {
					var indexNextToNewPos = this.getPlaceholder().nextSibling ? this.getPlaceholder().nextSibling.id.substr(10) : 0,
						otherView = (view === 'grid' ? 'list' : 'grid');

					this.gui.elements.container[this.gui.view].replaceChild(this.dd.moveItem.el, this.getPlaceholder());
					this.dd.moveItem.el.firstChild.style.borderColor = 'green';
					this.reindexAndRetrieveCollection();
					window.setTimeout(this.resetDdParams.bind(this), 200);
				}
			}
			break;
		case 'dragItem':
		case 'dragFolder':
			el = this.getItem(elem);
			position = (type === 'space_left' ? el.id.substr(10) - 1 : el.id.substr(10));

			if (type === 'item') {
				if (this.gui.view === 'list') {
					//el.style.border = '1px solid red';
				}
				//el.firstChild.style.backgroundColor = 'palegreen';
			} else {
				this.hideSpace(elem, type);
			}

			if (WE().consts.tables.TBL_PREFIX + this.we_doc.docRemTable === data[1]) {
				if (!this.we_doc.docRemCT || data[3] === WE().consts.contentTypes.FOLDER || this.we_doc.docRemCT.search(',' + data[3]) !== -1) {
					var recursive = this.doc.we_form.elements['we_' + this.we_doc.docName + '_InsertRecursive'].value;
					this.callForValidItemsAndInsert(-1, position, data[2], false, type !== 'item', recursive);
					return;
				}
				WE().util.showMessage("The item you try to drag doesn't match your collection's contenttypes", WE().consts.message.WE_MESSAGE_ERROR, this.win); // FIXME: GL()
				this.resetColors();
			} else {
				WE().util.showMessage("The tree you try to drag from doesn't match your collection's table property", WE().consts.message.WE_MESSAGE_ERROR, this.win); // FIXME: GL()
			}
			window.setTimeout(this.resetItemColors.bind(this), 100, el);
			break;
		case 'dragItemFromExtern':
			var files = evt.dataTransfer.files;
			if (this.we_doc.docRealRemCT.search(',' + files[0].type + ',') === -1) {
				WE().util.showMessage('wrong type', WE().consts.message.WE_MESSAGE_ERROR, this.win); // FIXME: GL()
				return;
			}

			var parentID = this.we_doc.docDefaultDir,
				ct = files[0].type,
				position, nextCmd, index, tmp;

			el = this.getItem(elem);
			position = el.id.substr(10);
			tmp = el.getElementsByClassName('collectionItem_index')[0].id.split('_');
			index = tmp[tmp.length - 1];

			nextCmd = 'collection_insertFiles,' + this.we_doc.docId + ',' + index + ',' + position;

			this.doc.presetFileupload = files;
			top.we_cmd('we_fileupload_editor', ct, 1, '', parentID, 0, true, nextCmd);
			break;
		default:
			return;
	}
};

WeCollection.prototype.dragEnd = function (evt) {
	if (this.dd.isMoveItem) {
		this.cancelMoveItem();
	}
};

WeCollection.prototype.cancelMoveItem = function () {
	this.gui.elements.container[this.gui.view].removeChild(this.getPlaceholder());
	this.dd.moveItem.el.style.borderColor = 'red';
	this.gui.elements.container[this.gui.view].insertBefore(this.dd.moveItem.el, this.dd.moveItem.next);
	this.reindexAndRetrieveCollection();
	window.setTimeout(this.resetDdParams.bind(this), 300);
};

WeCollection.prototype.resetDdParams = function () {
	if(this.dd.moveItem && this.dd.moveItem.el){
		this.resetItemColors(this.dd.moveItem.el);
	}

	this.dd.placeholder = null;
	this.dd.counter = 0;
	this.dd.isMoveItem = false;
	this.dd.moveItem.el = null;
	this.dd.moveItem.id = 0;
	this.dd.moveItem.index = 0;
	this.dd.moveItem.next = null;
	this.dd.moveItem.pos = 0;
	this.dd.moveItem.removed = false;
};

WeCollection.prototype.callForValidItemsAndInsert = function (index, position, csvIDs, message, notReplace, recursive) {
	// FIXME: we need a consize distinction between index and position

	if(!(Number.isInteger(parseInt(position)) && parseInt(position) > -1)){
		if(Number.isInteger(parseInt(index)) && parseInt(index) > 0){
			position = this.getItemId(this.doc.getElementById('collectionItem_index_' + this.gui.view + '_' + index));
		} else {
			position = this.getItemId(this.doc.getElementsByName('lastItem_' + this.gui.view)[0]);
		}
	}

	notReplace = notReplace !== undefined ? notReplace : false;

	try {
		if (csvIDs) {
			var postData = 'cns=collection' +
				'&we_cmd[transaction]=' + encodeURIComponent(this.we_doc.docTransaction) +
				'&we_cmd[ids]=' + encodeURIComponent(csvIDs) +
				'&we_cmd[collection]=' + encodeURIComponent(this.we_doc.docId) +
				'&we_cmd[full]=' + encodeURIComponent(1) +
				'&we_cmd[recursive]=' + encodeURIComponent(recursive) +
				'&we_cmd[index]=' + encodeURIComponent(position) +
				'&we_cmd[notReplace]=' + encodeURIComponent(notReplace) +
				'&we_cmd[message]=' + encodeURIComponent(message);
			//postData += '&we_cmd[recursive]=' + encodeURIComponent(this.doc.we_form['check_we_' + weCollectionEdit.we_doc.docName + '_InsertRecursive'].checked);

			WE().util.rpc(WE().consts.dirs.WEBEDITION_DIR + "rpc.php?cmd=GetValidItemsByID", postData, this.ajaxCallbackGetValidItemsByID.bind(this));
		}
	} catch (e) {
		top.console.debug(e);
	}
};

WeCollection.prototype.ajaxCallbackGetValidItemsByID = function(weResponse) {
	var respArr = weResponse.DataArray.items,
		index = parseInt(weResponse.DataArray.index),
		message = weResponse.DataArray.message,
		notReplace = weResponse.DataArray.notReplace,
		insertBefore = false;

		if(index === 0){
			index = 1;
			insertBefore = true;
		}

	if (respArr.length === -1) { // option deactivated: check doublettes for single insert too
		this.doc.getElementById('yuiAcInputItem_' + index).value = respArr[0].path;
		this.doc.getElementById('yuiAcResultItem_' + index).value = respArr[0].id;
		this.reindexAndRetrieveCollection();
	} else {
		var resp = this.addItems(this.doc.getElementById(this.gui.view + '_item_' + index), respArr, notReplace, false, insertBefore);
		if (message) {
			WE().util.showMessage(WE().consts.g_l.weCollection.info_insertion.replace(/##INS##/, resp[0]).replace(/##REJ##/, resp[1]), 1, this.win);
		}
	}
	window.setTimeout(this.resetColors.bind(this), 300);
};

WeCollection.prototype.insertImportedDocuments = function (ids) {
	if (ids) {
		this.callForValidItemsAndInsert(-1, this.gui.elements.container[this.gui.view].lastChild.id.substr(10), ids.join());
	}
};

WE().layout.getGUI_Collection = function(win, dynamicVars){
	return new WeCollection(win, dynamicVars);
};