wePropertiesEdit = {
	hasOptions: function(obj){
		if(obj!=null&&obj.options!=null){ return true; }
		return false;
	},

	moveSelectedOptions: function(from,to, sort, type){
		sort = sort || true;
		type = type || 'document';

		if(!this.hasOptions(from)){ return; }
		for(var i=0;i<from.options.length;i++){
			var o=from.options[i];
			if(o.selected){
				if(!this.hasOptions(to)){
					var index=0;
				}else{
					var index=to.options.length;
				}
				to.options[index]=new Option(o.text,o.value,false,false);
			}
		}
		for(var i=(from.options.length-1);i>=0;i--){
			var o=from.options[i];
			if(o.selected){
				from.options[i]=null;
			}
		}
		if(sort){
			this.sortSelect(from);
			this.sortSelect(to);
		}
		from.selectedIndex=-1;
		to.selectedIndex=-1;
		this.retrieveCsv(type);
	},

	sortSelect: function(obj){
		var o=[];
		if(!this.hasOptions(obj)){ return; }
		for(var i=0;i<obj.options.length;i++){
			o[o.length]=new Option(obj.options[i].text,obj.options[i].value,obj.options[i].defaultSelected,obj.options[i].selected);
		}
		if(o.length==0){ return; }
		o=o.sort(
			function(a,b){
				if((a.text+'')<(b.text+'')){ return -1; }
				if((a.text+'')>(b.text+'')){ return 1; }
				return 0;
			}
		);
		for(var i=0;i<o.length;i++){
			obj.options[i]=new Option(o[i].text,o[i].value,o[i].defaultSelected,o[i].selected);
		}
	},

	retrieveCsv: function(type){
		type = type || 'document';
		var mimeListTo = document.getElementById(type === 'document' ? 'mimeListTo' : 'classListTo'),
			mimeStr = '';

		for(var i = 0; i < mimeListTo.options.length; i++){
			mimeStr += mimeListTo.options[i].value + ',';
		}
		document.getElementById(type === 'document' ? 'we_remCT' : 'we_remClass').value = mimeStr ? ',' + mimeStr : mimeStr;
	}
};

weCollectionEdit = {
	we_const: {// FIXME: move such "constants" to webEdition.js ("global" namespace)
		TBL_PREFIX: '',
		FILE_TABLE: '',
		OBJECT_FILES_TABLE: ''
	},

	we_doc: {
		ID: 0,
		name: '',
		remTable: '',
		remCT: '',
		remClass: ''
	},

	maxIndex: 0,
	blankGridItem: '',
	blankListItem: '',
	collectionName: '',
	csv: '',
	view: 'grid',

	dd: {
		dragID: 0,
		dragEl: null,
		dragNextsibling: null,
		lastY: 0,
		lastIndex: 0,
		removed: false,
		isDragRow: false,
		fillEmptyRows: false,
		placeholder: null
	},

	g_l: {
		info_insertion: 'Inserted: ##INS##\nAs duplicates rejected: ##REJ##\n\nOthers items may have been rejecected because of inapropriate class/mime type.'
	},

	init: function(){
		var c = document.getElementById('content_table_grid');
		for(var i = 0; i < c.childNodes.length; i++){
			this.addListenersToItem('grid', c.childNodes[i], i+1);
		}

		this.collectionName = (this.we_const.TBL_PREFIX + this.we_doc.remTable === this.we_const.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
		this.collectionCsv = document.we_form.elements['we_' + this.we_doc.name + this.collectionName].value;
	},
	
	addListenersToItem: function(view, elem, num){
		var t = this, item, ctrls, space;
	
		if(view === 'grid'){
			item = elem.firstChild;
			item.addEventListener('drop', function(e){t.dropOnItem('item', view, e, item);}, false);
			item.addEventListener('dragenter', function(e){t.enterDrag('item', view, e, item);}, false);
			item.addEventListener('dragover', function(e){t.allowDrop(e);}, false);
			item.addEventListener('dragleave', function(e){t.leaveDrag('item', view, e, item);}, false);
			item.addEventListener('dragstart', function(e){t.startMoveItem(e, view);}, false);
			item.addEventListener('dragend', function(e){t.dragEnd(e);}, false);
			item.addEventListener('mouseover', function(e){t.overMouse('item', view, item);}, false);
			item.addEventListener('mouseout', function(e){t.outMouse('item', view, item);}, false);

			ctrls = item.lastChild;
			ctrls.addEventListener('mouseover', function(e){t.overMouse('btns', view, ctrls);}, false);
			ctrls.addEventListener('mouseout', function(e){t.outMouse('btns', view, ctrls);}, false);

			space = elem.childNodes[1]; //document.getElementById('grid_space_' + num);
			space.addEventListener('drop', function(e){t.dropOnItem('space', view, e, space);}, false);
			space.addEventListener('dragover', function(e){t.allowDrop(e);}, false);
			space.addEventListener('dragenter', function(e){t.enterDrag('space', view, e, space);}, false);
			space.addEventListener('dragleave', function(e){t.leaveDrag('space', view, e, space);}, false);
		}
	},

	doClickUp: function(elem){
		var el = this.getItem(elem);

		if(el.parentNode.firstChild !== el){
			el.parentNode.insertBefore(el, el.previousSibling);
			this.repaintAndRetrieveCsv();
		}
	},

	doClickDown: function(elem){
		var el = this.getItem(elem);
		var sib = el.nextSibling;

		if(true || sib){
			el.parentNode.insertBefore(el.nextSibling, el);
			this.repaintAndRetrieveCsv();
		}
	},

	doClickAdd: function(elem){
		var el = this.getItem(elem),
			num = document.getElementById('numselect_' + el.id.substr(10)).value;

		for(var i = 0; i < num; i++){
			el = this.addListItem(el, false);
		}
		this.repaintAndRetrieveCsv();
	},

	doClickAddItems: function(elem){
		var el = this.getItem(elem),
			index = el.id.substr(10),
			pos = -1;

		for(var i = 0; i < el.parentNode.childNodes.length; i++){
			if(el.parentNode.childNodes[i].id == el.id){
				pos = i;
				break;
			}
		}

		top.we_cmd('addToCollection', 1, this.we_const.TBL_PREFIX + this.we_doc.remTable, this.we_doc.ID, this.we_doc.Path, index, pos);
	},

	doClickDelete: function(elem){
		var el = this.getItem(elem);

		el.parentNode.removeChild(el);
		this.repaintAndRetrieveCsv();
	},

	getPlaceholder: function(){
		if(this.dd.placeholder !== null){
			return this.dd.placeholder;
		}

		this.dd.placeholder = document.createElement("div");
		this.dd.placeholder.style.backgroundColor = 'white';
		this.dd.placeholder.setAttribute("ondragover","weCollectionEdit.allowDrop(event)");
		if(this.view === 'grid'){
			this.dd.placeholder.setAttribute("ondrop","weCollectionEdit.dropOnItem(\'item\',\'grid\',event, this)");
			this.dd.placeholder.style.float = 'left';
			this.dd.placeholder.style.display = 'block';
			this.dd.placeholder.style.height = '242px';
			this.dd.placeholder.style.width = '256px';
			var inner = document.createElement("div");
			inner.style.height = '230px';
			inner.style.width = '240px';
			inner.style.border = '1px dotted #006db8';
			this.dd.placeholder.appendChild(inner);
		} else {
			this.dd.placeholder.setAttribute("ondrop","weCollectionEdit.dropOnRow(\'item\',\'grid\',event, this)");
			this.dd.placeholder.style.border = '1px solid #006db8';
			this.dd.placeholder.style.height = '34px';
			this.dd.placeholder.style.width = '804px';
			this.dd.placeholder.style.marginTop = '4px';
		}

		return this.dd.placeholder;
	},

	getItem: function(elem){
		while(elem.className !== 'drop_reference' && elem.className !== 'content_table'){
			elem = elem.parentNode;
		}

		return elem;
	},

	addListItem: function(elem, repaint, id, path){
		var el = this.getItem(elem),
			div, newElem, cmd1, cmd2;

		id = id || -1;
		path = path || '';
		repaint = repaint || false;

		cmd1 = weCmdEnc(weCollectionEdit.selectorCmds[0].replace(/XX/g, ++this.maxIndex));
		cmd2 = weCmdEnc(weCollectionEdit.selectorCmds[1].replace(/XX/g, this.maxIndex));
		div = document.createElement("div");
		div.innerHTML = this.blankListItem.replace(/XX/g, this.maxIndex).replace(/CMD1/, cmd1).replace(/CMD2/, cmd2);

		newElem = document.getElementById('content_table' + this.view).insertBefore(div.firstChild, el.nextSibling);
		document.getElementById('yuiAcInputItem_' + this.maxIndex).value = path;
		document.getElementById('yuiAcResultItem_' + this.maxIndex).value = id;

		if(repaint){
			this.repaintAndRetrieveCsv();
		}

		return newElem;
	},

	addGridItem: function(elem, repaint, object){
		var el = this.getItem(elem),
			div, newElem;

		id = object.id || -1;
		repaint = repaint || false;

		div = document.createElement("div");
		div.innerHTML = this.blankGridItem.replace(/##INDEX##/g, '100').replace(/##ID##/, object.id).replace(/##URL##/g, object.iconSrc);
		this.addListenersToItem('grid', div.firstChild);

		newElem = document.getElementById('content_table_' + this.view).insertBefore(div.firstChild, el.nextSibling);
		if(repaint){
			this.repaintAndRetrieveCsv();
		}

		return newElem;
	},

	addItems: function(elem, items, notReplace){
		if(elem === undefined){
			return false;
		}

		var el = this.getItem(elem),
			index = el.id.substr(10),
			rowsFull = false,
			isFirstSet = notReplace !== undefined ? notReplace : false,
			itemsSet = [[],[]],
			item, id;

		//set first item on drop row
		if(items.length){
			/*
			this.dd.IsDuplicates = document.we_form['check_we_' + this.we_doc.name + '_IsDuplicates'].checked;
			*/
			while(!isFirstSet && items.length){
				var item = items.shift();
				if(this.dd.IsDuplicates === 1 || this.collectionCsv.search(',' + item.id + ',') === -1){
					if(this.view === 'grid'){
						document.getElementById('grid_item_' + index).firstChild.style.background = 'url(' + item.iconSrc.replace('%2F', '/') + ') no-repeat center center';
						//TODO: name id-fiels using index from item-id and rename when reordering...
						document.getElementById('grid_item_' + index).childNodes[2].value = item.id;
					} else {
						document.getElementById('yuiAcInputItem_' + index).value = item.path;
						document.getElementById('yuiAcResultItem_' + index).value = item.id;
					}
					itemsSet[0].push(item.id);
					isFirstSet = true;
				} else {
					itemsSet[1].push(item.id);
				}
			}
		}

		for(var i = 0; i < items.length; i++){
			if(this.dd.IsDuplicates || this.collectionCsv.search(',' + items[i].id + ',') === -1){
				itemsSet[0].push(items[i].id);
				if(this.dd.fillEmptyRows && !rowsFull && el.nextSibling && typeof el.nextSibling.id !== 'undefined' && el.nextSibling.id.substr(0, 10) === this.view + '_item_'){
					index = el.nextSibling.id.substr(10);
					id = this.view === 'grid' ? el.nextSibling.childNodes[2].value : document.getElementById('yuiAcResultItem_' + index).value;
					if(id == -1 || id == 0){
						if(this.view === 'grid'){
							el.nextSibling.childNodes[2].value = items[i].id;
							el.nextSibling.firstChild.style.background = 'url(' + items[i].iconSrc.replace('%2F', '/') + ') no-repeat center center';
						} else {
							document.getElementById('yuiAcInputItem_' + index).value = items[i].path;
							document.getElementById('yuiAcResultItem_' + index).value = items[i].id;
						}
						el = el.nextSibling;
						continue;
					} else {
						rowsFull = true;
					}
				}
				if(this.view === 'grid'){
					el = this.addGridItem(el, false, items[i]);
				} else {
					el = this.addListItem(el, false, items[i].id, items[i].path);
				}
			} else {
				itemsSet[1].push(items[i].id);
			}
		}
		this.repaintAndRetrieveCsv(this.view);
		return itemsSet;
	},

	deleteItem: function(view, elem){
		var el;
		if(view == 'grid'){
			el = this.getItem(elem);
			el.parentNode.removeChild(el);
			this.repaintAndRetrieveCsv(this.view);
		}
	},

	repaintAndRetrieveCsv: function(view){
		var t = document.getElementById('content_table_' + view), row, item, index, csv = ',', val, btns;

		switch(view){
			case 'grid':
				for(var i = 0; i < t.childNodes.length; i++){
					item = t.childNodes[i];
					item.id = 'grid_item_' + (i+1);
					val = parseInt(document.we_form.collectionItem_we_id[i].value);
					csv += (val !== 0 ? val : -1) + ',';
				}
				break;
			case 'list':
				for(var i = 0; i < t.childNodes.length; i++){
					row = t.childNodes[i];
					btns = row.getElementsByTagName('BUTTON');
					index = row.id.substr(10);
					val = parseInt(document.getElementById('yuiAcResultItem_' + index).value);
					csv += (val !== 0 ? val : -1) + ',';
					document.getElementById('label_' + index).innerHTML = i + 1;
					btns[2].disabled = (val === - 1);
					btns[4].disabled = (i === 0);
					btns[5].disabled = (i === (t.childNodes.length - 1));
					btns[6].disabled = (t.childNodes.length === 1);
				}
				break;
		}
		if(val !== -1){
			//this.addListItem(t.lastChild, true);
		}

		if(!this.collectionName){
			this.collectionName = (this.we_const.TBL_PREFIX + this.we_doc.remTable === this.we_const.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
		}
		document.we_form.elements['we_' + this.we_doc.name + this.collectionName].value = csv;
		this.collectionCsv = csv;
		top.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
	},

	hideSpace: function(elem){
		elem.style.width = '12px';
		elem.style.border = '1px solid white';
		elem.style.margin = '0';
		elem.previousSibling.style.width = '240px';
		elem.parentNode.nextSibling.firstChild.style.width = '240px';
		
	},

	allowDrop: function(evt){
		evt.preventDefault();
	},

	enterDrag: function(type, view, evt, elem){
		var el = this.getItem(elem),//this.getItem(evt.target),
			data = evt.dataTransfer.getData("text").split(',');
		this.view = view;
		
		if(this.view === 'grid' && type === 'item'){
			this.outMouse(type, this.view, elem);
		}

		switch(data[0]){
			case 'moveItem':
				if(el.id !== this.dd.dragID && type === 'item'){
					var c = document.getElementById('content_table_' + this.view),
						elIndex = el.id.substr(10),
						diff = elIndex - this.dd.dragIndex,
						tmpIndex = this.dd.dragIndex,
						tmpID = this.dd.dragID;

					if(!this.dd.removed){
						c.removeChild(this.dd.dragEl);
						this.dd.removed = true;
					}

					if(this.getPlaceholder(this.dd.dragID).parentNode){
						c.removeChild(this.getPlaceholder());
					}
					c.insertBefore(this.getPlaceholder(), (diff > 0 ? el.nextSibling : el));

					//rewrite element ids (doing only the absolute neccessary) 
					this.dd.dragID = el.id;
					this.dd.dragIndex = elIndex;

					switch(diff){
						case 1:
						case -1:
							el.id = tmpID;
							break;
						default:
							
							if(diff > 1){
								for(var i = 0; i < 3; i++){
									c.childNodes[tmpIndex-1].id = 'grid_item_' + tmpIndex++;
								}
							} else {
								for(var i = 0; i < 3; i++){
									c.childNodes[elIndex].id = 'grid_item_' + ++elIndex;
								}
							}

					}
				}
				break;
			case 'moveRow':
				if(el.id !== this.dd.dragID){
					el = this.dd.lastY < evt.clientY ? el.nextSibling : el;

					if(!this.dd.removed){
						document.getElementById('content_table').removeChild(this.dd.dragEl);
						this.dd.removed = true;
					}

					if(this.getPlaceholder().parentNode){
						document.getElementById('content_table').removeChild(this.getPlaceholder());
					}
					document.getElementById('content_table').insertBefore(this.getPlaceholder(), el);
					this.dd.lastY = evt.clientY + (this.dd.lastY < evt.clientY ? 20 : 20);
				}
				break;
			case 'dragItem':
			case 'dragFolder':
				if(this.view === 'grid'){
					switch(type){
						case 'item':
							var c = document.getElementById('content_table_' + this.view), 
									index;
							for(var i = 0; i < c.childNodes.length; i++){
								el.firstChild.style.border = '1px solid #006db8';
							}

							if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
								el.firstChild.style.border = '1px solid #00cc00';
							} else {
								el.firstChild.style.border = '1px solid red';
							}
							break;
						case 'space':
							if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
								elem.style.width = '36px';
								elem.style.margin = '0 4px 0 4px';
								elem.style.border = '1px dotted #00cc00';
								elem.previousSibling.style.width = '224px';
								elem.parentNode.nextSibling.firstChild.style.width = '224px';
							} else {
								//el.style.border = '1px solid red';
							}
					}
				} else {
					/*
					switch(type){
						case 'item':
							var t = document.getElementById('content_table_' + this.view), index;
							for(var i = 0; i < t.childNodes.length; i++){
								index = t.childNodes[i].id.substr(10);
								//document.getElementById(this.view + '_elem_ + index).style.border = '1px solid #006db8';
							}

							if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
								el.style.border = '1px solid #00cc00';
							} else {
								el.style.border = '1px solid red';
							}
							break;
						case 'space':
							if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
								elem.style.width = '36px';
								elem.style.margin = '0 4px 0 4px';
								elem.style.border = '1px dotted #00cc00';
								elem.previousSibling.style.width = '224px';
								elem.parentNode.nextSibling.firstChild.style.width = '224px';
							} else {
								//el.style.border = '1px solid red';
							}
					}
					*/
				}
				break;
			default:
				return;
		}
	},
			
	leaveDrag: function(type, view, evt, elem){
		var data = evt.dataTransfer.getData("text").split(',');

		switch(data[0]){
			case 'dragItem':
			case 'dragFolder':
				switch(type){
					case 'item':
						elem.style.border = '1px solid #006db8';
						break;
					case 'space':
						this.hideSpace(elem);
						break;
				}
				break;
			default:
				return;
		}
	},

	overMouse: function(type, view, elem){
		if(view === 'grid'){
			switch(type){
				case 'item':
					elem.firstChild.style.display = 'block';
					break;
				case 'btns':
					elem.style.opacity = '1';
					break;
			}
		}
	},

	outMouse: function(type, view, elem){
		if(view === 'grid'){
			switch(type){
				case 'item':
					elem.firstChild.style.display = 'none';
					break;
				case 'btns':
					elem.style.opacity = '0.8';
					break;
			}
		}
	},

	startDragRow: function(evt) {
		this.dd.isDragRow = true;
		this.dd.lastY = evt.clientY;
		this.dd.dragEl = evt.target;
		this.dd.dragID = evt.target.id;
		this.dd.dragNextsibling = evt.target.nextSibling;
		this.dd.removed = false;
		evt.dataTransfer.setData('text', 'moveRow,' + evt.target.id);
	},
			
	startMoveItem: function(evt, view) {
		var el = this.getItem(evt.target);

		if(this.view === 'grid'){
			this.outMouse('item', this.view, el.firstChild);
		}
		this.view = view;
		this.dd.isDragRow = true;
		this.dd.lastY = evt.clientY;
		this.dd.dragEl = el;
		this.dd.dragID = el.id;
		this.dd.dragIndex = el.id.substr(10);
		this.dd.dragNextsibling = el.nextSibling;
		this.dd.removed = false;
		evt.dataTransfer.setData('text', 'moveItem,' + el.id);
	},

	dropOnItem: function(type, view, evt, elem){
		evt.preventDefault();

		var data = evt.dataTransfer.getData("text").split(','),
			el, index;

		switch(data[0]){
			case 'moveItem':
				this.dd.isDragRow = false;
				document.getElementById('content_table_' + this.view).replaceChild(this.dd.dragEl, this.getPlaceholder());
				this.repaintAndRetrieveCsv(view);
				this.dd.dragEl.firstChild.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.firstChild.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'dragItem':
			case 'dragFolder':
					el = this.getItem(elem);
					index = el.id.substr(10);
					
					if(type === 'item'){
						el.firstChild.style.border = '1px solid #006db8';
					} else {
						this.hideSpace(elem)
					}

					if(this.we_const.TBL_PREFIX + this.we_doc.remTable === data[1]){
						if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
							this.callForVerifiedItemsAndInsert(index, data[2], false, type !== 'item');
						} else {
							//alert("the item you try to drag from doesn't match your collection's content types");
						}
					} else {
						alert("the tree you try to drag from doesn't match your collection's table property");
					}
				break;
			default:
				return;
		}

	},

	dropOnRow: function(evt) {
		//this.view = view;
		evt.preventDefault();
		var data = evt.dataTransfer.getData("text").split(','),
			el, index;

		switch(data[0]){
			case 'moveRow':
				this.dd.isDragRow = false;
				document.getElementById('content_table').replaceChild(this.dd.dragEl, this.getPlaceholder());
				this.repaintAndRetrieveCsv();
				this.dd.dragEl.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'moveItem':
				this.dd.isDragRow = false;
				document.getElementById('content_table_' + this.view).replaceChild(this.dd.dragEl, this.getPlaceholder());
				this.repaintAndRetrieveCsv();
				this.dd.dragEl.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'dragItem':
			case 'dragFolder':
				el = this.getItem(elem);
				index = el.id.substr(10);
				el.style.border = '1px solid #006db8';

				if(this.we_const.TBL_PREFIX + this.we_doc.remTable === data[1]){
					if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
						this.callForVerifiedItemsAndInsert(index, data[2]);
					} else {
						//alert("the item you try to drag from doesn't match your collection's content types");
					}

				} else {
					alert("the tree you try to drag from doesn't match your collection's table property");
				}
				break;
			default:
				return;
		}
	},

	dragEnd: function(evt){
		if(this.dd.isDragRow){
			this.cancelMoveItem();
		}
	},

	cancelMoveItem: function(){
		document.getElementById('content_table').removeChild(this.getPlaceholder());
		this.dd.dragEl.style.borderColor = 'red';
		document.getElementById('content_table').insertBefore(this.dd.dragEl, this.dd.dragNextsibling);
		this.repaintAndRetrieveCsv();
		setTimeout(function(){
			weCollectionEdit.dd.dragEl.style.borderColor = '#006db8';
			weCollectionEdit.resetDdParams();
		}, 300);
	},

	resetDdParams: function(){
		this.dd.dragID = 0;
		this.dd.dragEl = null;
		this.dd.dragNextsibling = null;
		this.dd.lastY = 0;
		this.dd.removed = false;
		this.dd.isDragRow = false;
		this.dd.fillEmptyRows = false;
		this.dd.placeholder = null;
	},

	callForVerifiedItemsAndInsert: function (index, csvIDs, message, notReplace) {
		notReplace = notReplace !== undefined ? notReplace : false;
		try {
			if(csvIDs){
				var postData;
				postData = 'we_cmd[transaction]=' + encodeURIComponent(we_transaction);
				postData += '&we_cmd[id]=' + encodeURIComponent(csvIDs);
				postData += '&we_cmd[collection]=' + encodeURIComponent(this.we_doc.ID);
				postData += '&we_cmd[full]=' + encodeURIComponent(1);
				postData += '&we_cmd[recursive]=' + encodeURIComponent(document.we_form['check_we_' + weCollectionEdit.we_doc.name + '_InsertRecursive'].checked);

				xhr = new XMLHttpRequest();
				xhr.onreadystatechange = function () {
					if (xhr.readyState === 4) {
						if (xhr.status === 200) {
							var respArr = JSON.parse(xhr.responseText);
							if(respArr.length === -1){ // option deactivated: check doublettes for single insert too
								document.getElementById('yuiAcInputItem_' + index).value = respArr[0].path;
								document.getElementById('yuiAcResultItem_' + index).value = respArr[0].id;
								weCollectionEdit.repaintAndRetrieveCsv(this.view);
							} else {
								var resp = weCollectionEdit.addItems(document.getElementById(weCollectionEdit.view + '_item_' + index), respArr, notReplace);
								if(message){
									top.we_showMessage(weCollectionEdit.g_l.info_insertion.replace(/##INS##/, resp[0]).replace(/##REJ##/, resp[1]), 1, window);
								}
							}
						} else {
							top.console.debug('http request failed');
							return false;
						}
					}
				};
				xhr.open('POST', '/webEdition/rpc/rpc.php?protocol=json&cmd=GetItemsFromDB&cns=collection', true);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.send(postData);
				// set max waiting time
			}
		} catch (e) {
			top.console.debug(e);
		}
	}
};