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
	blankRow: '',
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
		spacer: null
	},

	g_l: {
		info_insertion: 'Inserted: ##INS##\nAs duplicates rejected: ##REJ##\n\nOthers items may have been rejecected because of inapropriate class/mime type.'
	},

	init: function(){
		this.collectionName = (this.we_const.TBL_PREFIX + this.we_doc.remTable === this.we_const.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
		this.collectionCsv = document.we_form.elements['we_' + this.we_doc.name + this.collectionName].value;
	},

	doClickUp: function(elem){
		var el = this.getRow(elem);

		if(el.parentNode.firstChild !== el){
			el.parentNode.insertBefore(el, el.previousSibling);
			this.repaintAndRetrieveCsv();
		}
	},

	doClickDown: function(elem){
		var el = this.getRow(elem);
		var sib = el.nextSibling;

		if(true || sib){
			el.parentNode.insertBefore(el.nextSibling, el);
			this.repaintAndRetrieveCsv();
		}
	},

	doClickAdd: function(elem){
		var el = this.getRow(elem),
			num = document.getElementById('numselect_' + el.id.substr(10)).value;

		for(var i = 0; i < num; i++){
			el = this.addRow(el, false);
		}
		this.repaintAndRetrieveCsv();
	},

	doClickAddItems: function(elem){
		var el = this.getRow(elem),
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
		var el = this.getRow(elem);

		el.parentNode.removeChild(el);
		this.repaintAndRetrieveCsv();
	},

	getSpacer: function(){
		if(this.dd.spacer !== null){
			return this.dd.spacer;
		}

		this.dd.spacer = document.createElement("div");
		this.dd.spacer.style.backgroundColor = 'white';
		this.dd.spacer.setAttribute("ondragover","weCollectionEdit.allowDrop(event)");
		if(this.view === 'grid'){
			this.dd.spacer.setAttribute("ondrop","weCollectionEdit.dropOnCollectionItem(\'item\',\'grid\',event, this)");
			this.dd.spacer.style.float = 'left';
			this.dd.spacer.style.display = 'block';
			this.dd.spacer.style.height = '242px';
			this.dd.spacer.style.width = '256px';
			var inner = document.createElement("div");
			inner.style.height = '230px';
			inner.style.width = '240px';
			inner.style.border = '1px dotted #006db8';
			this.dd.spacer.appendChild(inner);
		} else {
			this.dd.spacer.setAttribute("ondrop","weCollectionEdit.dropOnRow(\'item\',\'grid\',event, this)");
			this.dd.spacer.style.border = '1px solid #006db8';
			this.dd.spacer.style.height = '34px';
			this.dd.spacer.style.width = '804px';
			this.dd.spacer.style.marginTop = '4px';
		}

		return this.dd.spacer;
	},

	getRow: function(elem){
		while(elem.className !== 'drop_reference' && elem.className !== 'content_table'){
			elem = elem.parentNode;
		}

		return elem;
	},

	addRow: function(elem, repaint, id, path){
		var el = this.getRow(elem),
			div, newElem, cmd1, cmd2;

		id = id || -1;
		path = path || '';
		repaint = repaint || false;

		cmd1 = weCmdEnc(weCollectionEdit.selectorCmds[0].replace(/XX/g, ++this.maxIndex));
		cmd2 = weCmdEnc(weCollectionEdit.selectorCmds[1].replace(/XX/g, this.maxIndex));
		div = document.createElement("div");
		div.innerHTML = this.blankRow.replace(/XX/g, this.maxIndex).replace(/CMD1/, cmd1).replace(/CMD2/, cmd2);

		newElem = document.getElementById('content_table' + this.view).insertBefore(div.firstChild, el.nextSibling);
		document.getElementById('yuiAcInputItem_' + this.maxIndex).value = path;
		document.getElementById('yuiAcResultItem_' + this.maxIndex).value = id;

		if(repaint){
			this.repaintAndRetrieveCsv();
		}

		return newElem;
	},

	addItems: function(elem, items){
		if(elem === undefined){
			return false;
		}

		var el = this.getRow(elem),
			index = el.id.substr(10),
			rowsFull = false,
			isFirstSet = false,
			itemsSet = [[],[]],
			item, id;

		//set first item on drop row
		if(items.length){
			/*
			this.dd.fillEmptyRows = document.we_form['check_we_' + this.we_doc.name + '_useEmpty'].checked;
			this.dd.doubleOk = document.we_form['check_we_' + this.we_doc.name + '_doubleOk'].checked;
			*/

			while(!isFirstSet && items.length){
				var item = items.shift();
				if(this.dd.doubleOk === 1 || this.collectionCsv.search(',' + item.id + ',') === -1){
					//document.getElementById('yuiAcInputItem_' + index).value = item.path;
					//document.getElementById('yuiAcResultItem_' + index).value = item.id;

					document.getElementById('grid_elem_' + index).firstChild.style.backgroundImage = 'url(' + item.iconSrc.replace('%2F', '/') + ')';
					//TODO: name id-fiels using index from item-id and rename when reordering...
					document.getElementById('grid_elem_' + index).childNodes[2].value = item.id;

					itemsSet[0].push(item.id);
					isFirstSet = true;
				} else {
					itemsSet[1].push(item.id);
				}
			}
		}

		for(var i = 0; i < items.length; i++){
			if(this.dd.doubleOk || this.collectionCsv.search(',' + items[i].id + ',') === -1){
				itemsSet[0].push(items[i].id);
				if(this.dd.fillEmptyRows && !rowsFull && el.nextSibling && typeof el.nextSibling.id !== 'undefined' && el.nextSibling.id.substr(0, 10) === this.view + '_elem_'){
					index = el.nextSibling.id.substr(10);
					id = document.getElementById('yuiAcResultItem_' + index).value;
					if(id == -1 || id == 0){
						//document.getElementById('yuiAcInputItem_' + index).value = items[i].path;
						//document.getElementById('yuiAcResultItem_' + index).value = items[i].id;

						document.getElementById('grid_elem_' + index).firstChild.style.backgroundImage = 'url(' + item.iconSrc.replace('%2F', '/') + ')';
						//TODO: name id-fiels using index from item-id and rename when reordering...
						document.getElementById('grid_elem_' + index).childNodes[2].value = item.id;
						el = el.nextSibling;
						continue;
					} else {
						rowsFull = true;
					}
				}
				el = this.addRow(el, false, items[i].id, items[i].path);
			} else {
				itemsSet[1].push(items[i].id);
			}
		}
		this.repaintAndRetrieveCsv(this.view);
		return itemsSet;
	},

	repaintAndRetrieveCsv: function(view){
		var t = document.getElementById('content_table_' + view), row, item, index, csv = ',', val, btns;

		switch(view){
			case 'grid':
				for(var i = 0; i < t.childNodes.length; i++){
					item = t.childNodes[i];
					item.id = 'grid_elem_' + (i+1);
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
			//this.addRow(t.lastChild, true);
		}

		if(!this.collectionName){
			this.collectionName = (this.we_const.TBL_PREFIX + this.we_doc.remTable === this.we_const.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
		}
		document.we_form.elements['we_' + this.we_doc.name + this.collectionName].value = csv;
		this.collectionCsv = csv;
		top.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
	},

	allowDrop: function(evt){
		evt.preventDefault();
	},

	enterDrag: function(type, view, evt, elem){
		var el = this.getRow(elem),//this.getRow(evt.target),
			data = evt.dataTransfer.getData("text").split(',');
		this.view = view;

		switch(data[0]){
			case 'moveElement':
				if(el.id !== this.dd.dragID && type === 'item'){
					var elIndex = el.id.substr(10),
						diff = elIndex - this.dd.dragIndex,
						tmpIndex = this.dd.dragIndex,
						tmpID = this.dd.dragID;

					if(!this.dd.removed){
						document.getElementById('content_table_' + this.view).removeChild(this.dd.dragEl);
						this.dd.removed = true;
					}

					if(this.getSpacer(this.dd.dragID).parentNode){
						document.getElementById('content_table_' + this.view).removeChild(this.getSpacer());
					}
					document.getElementById('content_table_' + this.view).insertBefore(this.getSpacer(), (diff > 0 ? el.nextSibling : el));

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
									document.getElementById('content_table_' + this.view).childNodes[tmpIndex-1].id = 'grid_elem_' + tmpIndex++;
								}
							} else {
								for(var i = 0; i < 3; i++){
									document.getElementById('content_table_' + this.view).childNodes[elIndex].id = 'grid_elem_' + ++elIndex;
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

					if(this.getSpacer().parentNode){
						document.getElementById('content_table').removeChild(this.getSpacer());
					}
					document.getElementById('content_table').insertBefore(this.getSpacer(), el);
					this.dd.lastY = evt.clientY + (this.dd.lastY < evt.clientY ? 20 : 20);
				}
				break;
			case 'dragItem':
			case 'dragFolder':
				if(this.view === 'grid'){
					switch(type){
						case 'item':
							var t = document.getElementById('content_table_' + this.view), index;
							for(var i = 0; i < t.childNodes.length; i++){
								el.firstChild.style.border = '1px solid #006db8';
							}

							if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
								el.firstChild.style.border = '1px solid #00cc00';
							} else {
								el.firstChild.style.border = '1px solid red';
							}
							break;
						case 'inter':
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
						case 'inter':
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
					case 'inter':
						elem.style.width = '12px';
						elem.style.border = '1px solid white';
						elem.style.margin = '0';
						elem.previousSibling.style.width = '240px';
						elem.parentNode.nextSibling.firstChild.style.width = '240px';
						break;
				}
				break;
			default:
				return;
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
			
	startDragCollectionElement: function(evt, view) {
		var el = this.getRow(evt.target);
		this.view = view;
		this.dd.isDragRow = true;
		this.dd.lastY = evt.clientY;
		this.dd.dragEl = el;
		this.dd.dragID = el.id;
		this.dd.dragIndex = el.id.substr(10);
		this.dd.dragNextsibling = el.nextSibling;
		this.dd.removed = false;
		evt.dataTransfer.setData('text', 'moveElement,' + el.id);
	},
			
	dropOnCollectionItem: function(type, view, evt, elem){
		evt.preventDefault();

		var data = evt.dataTransfer.getData("text").split(','),
			el, index;

		switch(data[0]){
			case 'moveElement':
				this.dd.isDragRow = false;
				document.getElementById('content_table_' + this.view).replaceChild(this.dd.dragEl, this.getSpacer());
				this.repaintAndRetrieveCsv(view);
				this.dd.dragEl.firstChild.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.firstChild.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'dragItem':
			case 'dragFolder':
				el = this.getRow(elem);
				index = el.id.substr(10);
				el.firstChild.style.border = '1px solid #006db8';

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

	dropOnRow: function(evt) {
		//this.view = view;
		evt.preventDefault();
		var data = evt.dataTransfer.getData("text").split(','),
			el, index;

		switch(data[0]){
			case 'moveRow':
				this.dd.isDragRow = false;
				document.getElementById('content_table').replaceChild(this.dd.dragEl, this.getSpacer());
				this.repaintAndRetrieveCsv();
				this.dd.dragEl.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'moveElement':
				this.dd.isDragRow = false;
				document.getElementById('content_table_' + this.view).replaceChild(this.dd.dragEl, this.getSpacer());
				this.repaintAndRetrieveCsv();
				this.dd.dragEl.style.borderColor = 'green';
				setTimeout(function(){
					weCollectionEdit.dd.dragEl.style.borderColor = '#006db8';
					weCollectionEdit.resetDdParams();
				}, 200);
				break;
			case 'dragItem':
			case 'dragFolder':
				el = this.getRow(elem);
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
			this.cancelDragRow();
		}
	},

	cancelDragRow: function(){
		document.getElementById('content_table').removeChild(this.getSpacer());
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
		this.dd.spacer = null;
	},

	callForVerifiedItemsAndInsert: function (index, csvIDs, message) {
		try {
			if(csvIDs){
				var postData;
				postData = 'we_cmd[transaction]=' + encodeURIComponent(we_transaction);
				postData += '&we_cmd[id]=' + encodeURIComponent(csvIDs);
				postData += '&we_cmd[collection]=' + encodeURIComponent(this.we_doc.ID);
				postData += '&we_cmd[full]=' + encodeURIComponent(1);
				postData += '&we_cmd[recursive]=' + encodeURIComponent(document.we_form['check_we_' + weCollectionEdit.we_doc.name + '_insertRecursive'].checked);

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
								var resp = weCollectionEdit.addItems(document.getElementById(weCollectionEdit.view + '_elem_' + index), respArr);
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