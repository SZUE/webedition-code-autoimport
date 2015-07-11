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
	maxIndex: 0,
	blankItem: {
		list : '',
		grid : ''
	},
	collectionName: '',
	csv: '',
	view: 'grid',
	gridItemDimension: {
		item: 200,
		icon: 32
	},
	gridItemDimension: {},
	itemsPerRow: 4,
	collectionArr: [],
	collectionCsv: '',
	collectionNum: 0,
	sliderDiv: null,
	iconSizes: {},

	ct: {
		grid: null,
		list: null
	},
	

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

	dd: {
		fillEmptyRows: true,
		placeholder: null,

counter: 0,
c2: 0,

		isMoveItem: true,
		moveItem: {
			el: null,
			id: 0,
			index: 0,
			next: null,
			pos: 0,
			removed: false
		}
	},

	g_l: {
		info_insertion: 'Inserted: ##INS##\nAs duplicates rejected: ##REJ##\n\nOthers items may have been rejecected because of inapropriate class/mime type.'
	},

	init: function(){
		this.ct.grid = document.getElementById('content_div_grid');
		this.ct.list = document.getElementById('content_div_list');
		this.sliderDiv = document.getElementById('sliderDiv');
		this.numSpan = document.getElementById('numSpan');
		this.itemsPerRow = document.we_form['we_' + this.we_doc.name + '_itemsPerRow'].value;
		
		/* use this when delivering complete html-items by php
		for(var i = 0; i < this.ct[this.view].children.length; i++){
			this.addListenersToItem(this.view, this.ct[this.view].children[i], i+1);
		}
		this.reindexAndRetrieveCollection(true);
		*/

		/* use this to render fron storage */
		this.renderView(false);
		
	},

	setView: function(view){
		this.view = view;
		document.we_form['we_' + this.we_doc.name + '_view'].value = this.view;
		this.ct.grid.style.display = this.view === 'grid' ? 'inline-block' : 'none';
		this.ct.list.style.display = this.view === 'list' ? 'block' : 'none';
		this.sliderDiv.style.display = this.view === 'grid' ? 'block' : 'none';
		this.dd.counter = 0;
		this.renderView(false);
	},

	renderView: function(fromServer){
		this.ct[this.view].innerHTML = '';
		this.maxIndex = 0;

		for(var i = 0; i < this.collectionArr.length; i++){
			this.insertItem(null, false, this.storage['item_' + this.collectionArr[i]], this);
		}
		this.reindexAndRetrieveCollection();
	},

	addListenersToItem: function(view, elem){
		var t = this, item, input, ctrls, space;

		//TODO: grab elems by getElementByClassName instead of counting children...
		if(this.view === 'grid'){
			item = elem.firstChild;
			item.addEventListener('mouseover', function(){t.overMouse('item', view, item);}, false);
			item.addEventListener('mouseout', function(){t.outMouse('item', view, item);}, false);
			ctrls = item.lastChild;
			ctrls.addEventListener('mouseover', function(){t.overMouse('btns', view, ctrls);}, false);
			ctrls.addEventListener('mouseout', function(){t.outMouse('btns', view, ctrls);}, false);
			space = elem.childNodes[1];
			space.addEventListener('drop', function(e){t.dropOnItem('space', view, e, space);}, false);
			space.addEventListener('dragover', function(e){t.allowDrop(e);}, false);
			space.addEventListener('dragenter', function(e){t.enterDrag('space', view, e, space);}, false);
			space.addEventListener('dragleave', function(e){t.leaveDrag('space', view, e, space);}, false);
		} else {
			item = elem;
			input = document.getElementById('yuiAcInputItem_' + item.id.substr(10));
			input.addEventListener('mouseover', function(){item.draggable=false;});
			input.addEventListener('mouseout', function(){item.draggable=true;});
		}
		item.addEventListener('dragleave', function(e){t.leaveDrag('item', view, e, item);}, false);
		item.addEventListener('drop', function(e){t.dropOnItem('item', view, e, item);}, false);
		item.addEventListener('dragenter', function(e){t.enterDrag('item', view, e, item);}, false);
		item.addEventListener('dragover', function(e){t.allowDrop(e);}, false);
		item.addEventListener('dragstart', function(e){t.startMoveItem(e, view);}, false);
		item.addEventListener('dragend', function(e){t.dragEnd(e);}, false);

	},

	doClickUp: function(elem){
		var el = this.getItem(elem);

		if(el.parentNode.firstChild !== el){
			el.parentNode.insertBefore(el, el.previousSibling);
			this.reindexAndRetrieveCollection();
		}
	},

	doClickDown: function(elem){
		var el = this.getItem(elem);
		var sib = el.nextSibling;

		if(true || sib){
			el.parentNode.insertBefore(el.nextSibling, el);
			this.reindexAndRetrieveCollection();
		}
	},

	doClickAdd: function(elem){
		var el = this.getItem(elem),
			num = 1;//document.getElementById('numselect_' + el.id.substr(10)).value;

		for(var i = 0; i < num; i++){
			el = this.insertItem(el, false);
		}
		this.reindexAndRetrieveCollection();
	},

	doClickAddItems: function(elem){
		var el = elem ? this.getItem(elem) : null,
			index = el ? el.id.substr(10) : 0,
			pos = -1;

		if(el){
			for(var i = 0; i < el.parentNode.childNodes.length; i++){
				if(el.parentNode.childNodes[i].id == el.id){
					pos = i;
					break;
				}
			}
		}

		top.we_cmd('addToCollection', 1, this.we_const.TBL_PREFIX + this.we_doc.remTable, this.we_doc.ID, this.we_doc.Path, index, pos);
	},

	doClickDelete: function(elem){
		var el = this.getItem(elem);

		el.parentNode.removeChild(el);
		this.reindexAndRetrieveCollection();
	},

	doZoomGrid: function(value){
		var attribDivs = this.ct['grid'].getElementsByClassName('toolbarAttribs');
		var iconDivs = this.ct['grid'].getElementsByClassName('divInner'), next;

		this.itemsPerRow = 7 - value;
		this.gridItemDimension = this.gridItemDimensions[this.itemsPerRow];
		document.we_form['we_' + this.we_doc.name + '_itemsPerRow'].value = this.itemsPerRow;

		for(var i = 0; i < this.ct['grid'].children.length; i++){
			this.ct['grid'].children[i].style.width = this.ct['grid'].children[i].style.height = this.gridItemDimension.item + 'px';
			//this.ct['grid'].children[i].style.backgroundSize = Math.max(item.icon.sizeX, item.icon.sizeY) < this.gridItemDimension.item ? 'auto' : 'contain';
			
			attribDivs[i].style.display = this.itemsPerRow > 4 ? 'none' : 'block';
			iconDivs[i].firstChild.style.fontSize = this.gridItemDimension.icon + 'px';
			if(next = iconDivs[i].firstChild.nextSibling){
				next.style.fontSize = this.gridItemDimension.font + 'px';
			}
		}
	},

	doClickOpenToEdit: function(id){
		var table = this.we_doc.remTable === 'tblFile' ? this.we_const.FILE_TABLE : this.we_const.OBJECT_FILES_TABLE,
			ct = this.storage['item_' + id].ct;
		top.weEditorFrameController.openDocument(table,id,ct);
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
			this.dd.placeholder.style.height = this.gridItemDimension.item + 'px';
			this.dd.placeholder.style.width = this.gridItemDimension.item + 'px';
			var inner = document.createElement("div");
			inner.style.height = (this.gridItemDimension.item - 14) + 'px';
			inner.style.width = (this.gridItemDimension.item - 18) + 'px';
			inner.style.border = '1px dotted #006db8';
			this.dd.placeholder.appendChild(inner);
		} else {
			this.dd.placeholder.setAttribute("ondrop","weCollectionEdit.dropOnItem(\'item\',\'grid\',event, this)");
			this.dd.placeholder.style.border = '1px dotted #006db8';
			this.dd.placeholder.style.height = '90px';
			this.dd.placeholder.style.margin = '4px 0 0 0';
		}

		return this.dd.placeholder;
	},

	getItem: function(elem){
		var itemClass = this.view === 'grid' ? 'gridItem' : 'listItem';
		while(elem.className !== itemClass){
			elem = elem.parentNode;
			if(elem.className === 'collection-content'){
				return false;
			}
		}

		return elem;
	},

	insertItem: function(elem, repaint, item, scope, color){
		var t = scope ? scope : this,
			el = elem ? t.getItem(elem) : null,
			div, newItem, cmd1, cmd2, cmd3, blank, elPreview,
			id = item && item.id ? item.id : -1,
			path = item && item.path ? item.path : '',
			ct = item && item.ct ? item.ct : '',
			name = item && item.name ? item.name : '*',
			ext = item && item.ext ? item.ext : 'txt',
			iconSrc = item && item.icon ? item.icon.url : '',
			sizeX = item && item.icon ? item.icon.sizeX : 0,
			sizeY = item && item.icon ? item.icon.sizeY : 0,
			color = color ? color : false,

			attrib_title = item && item.elements.attrib_title.Dat ? item.elements.attrib_title.Dat : this.g_l['element_not_set'],
			attrib_title_s = item && item.elements.attrib_title ? item.elements.attrib_title.state : 'we-state-none',
			attrib_title_w = item && item.elements.attrib_title ? item.elements.attrib_title.write : '',

			attrib_alt = item && item.elements.attrib_alt.Dat ? item.elements.attrib_alt.Dat : this.g_l['element_not_set'],
			attrib_alt_s = item && item.elements.attrib_alt ? item.elements.attrib_alt.state : 'we-state-none',
			attrib_alt_w = item && item.elements.attrib_alt ? item.elements.attrib_alt.write : '',

			meta_title = item && item.elements.meta_title.Dat ? item.elements.meta_title.Dat : this.g_l['element_not_set'],
			meta_title_s = item && item.elements.meta_title ? item.elements.meta_title.state : 'we-state-none',
			meta_title_w = item && item.elements.meta_title ? item.elements.meta_title.write : '',

			meta_desc = item && item.elements.meta_description.Dat ? item.elements.meta_description.Dat : this.g_l['element_not_set'],
			meta_desc_s = item && item.elements.meta_description ? item.elements.meta_description.state : 'we-state-none',
			meta_desc_w = item && item.elements.meta_description ? item.elements.meta_description.write : '';

		repaint = repaint || false;
		++t.maxIndex;

		if(id && !this.storage['item_' + id]){
			this.storage['item_' + id] = item;
		}

		div = document.createElement("div");
		blank = t.blankItem[t.view].replace(/##INDEX##/g, t.maxIndex).replace(/##ID##/g, id).replace(/##PATH##/g, path).
				replace(/##CT##/g, ct).replace(/##ICONURL##/g, iconSrc.replace('%2F', '/')).
				replace(/##ATTRIB_TITLE##/g, attrib_title).replace(/##S_ATTRIB_TITLE##/g, attrib_title_s).
				replace(/##ATTRIB_ALT##/g, attrib_alt).replace(/##S_ATTRIB_ALT##/g, attrib_alt_s).
				replace(/##META_TITLE##/g, meta_title).replace(/##S_META_TITLE##/g, meta_title_s).
				replace(/##META_DESC##/g, meta_desc).replace(/##S_META_DESC##/g, meta_desc_s);

		if(t.view === 'list'){
			blank = blank.replace(/##W_ATTRIB_TITLE##/g, attrib_title_w).replace(/##W_ATTRIB_ALT##/g, attrib_alt_w).
				replace(/##W_META_TITLE##/g, meta_title_w).replace(/##W_META_DESC##/g, meta_desc_w);
	
			//TODO: list fallback!
			//cmd1 = weCmdEnc(weCollectionEdit.selectorCmds[0].replace(/##INDEX##/g, t.maxIndex));
			//cmd2 = weCmdEnc(weCollectionEdit.selectorCmds[1].replace(/##INDEX##/g, t.maxIndex));
			//blank = blank.replace(/##CMD1##/g, cmd1).replace(/##CMD2##/g, cmd2);
			div.innerHTML = blank;

			if(ct !== 'image/*' && id !== -1){
				elPreview = div.getElementsByClassName('previewDiv')[0];
				elPreview.innerHTML = getTreeIcon(ct, false, ext);
				elPreview.style.background = 'transparent';
				elPreview.style.display = 'block';
			}
		} else {
			if(id === -1){
				cmd1 = weCmdEnc(weCollectionEdit.gridBtnCmds[0].replace(/##INDEX##/g, t.maxIndex));
				cmd3 = weCmdEnc(weCollectionEdit.gridBtnCmds[2].replace(/##INDEX##/g, t.maxIndex));
				blank = blank.replace(/##CMD1##/g, cmd1).replace(/##CMD2##/g, '').replace(/##CMD3##/g, cmd3).replace(/##SHOWBTN##/g, 'block');
			} else {
				blank = blank.replace(/##SHOWBTN##/g, 'none');
			}
			div.innerHTML = blank;
			div.getElementsByClassName('divContent')[0].style.backgroundSize = Math.max(sizeX, sizeY) < this.gridItemDimension.item ? 'auto' : 'contain';

			if(ct !== 'image/*' && id !== -1){
				elPreview = div.getElementsByClassName('divInner')[0];
				elPreview.innerHTML = getTreeIcon(ct, false, ext) + '<div class="divTitle defaultfont" style="font-size:' + this.gridItemDimension.font + 'px;">' + name + ext + '</div>';
					//<div class="divTitle defaultfont" style="font-size:10px;">Titel: ' + propDesc + '</div>';
				elPreview.getElementsByTagName('SPAN')[0].style.fontSize = this.gridItemDimension.icon + 'px';
				elPreview.style.background = 'transparent';
				elPreview.style.display = 'block';
				elPreview.style.textAlign = 'left';
				elPreview.style.padding = '14% 0 0 10%';
			}

			div.firstChild.style.width = div.firstChild.style.height = t.gridItemDimension.item + 'px';
			div.getElementsByClassName('toolbarAttribs')[0].style.display = this.itemsPerRow > 4 ? 'none' : 'block';
		}

		newItem = el ? t.ct[t.view].insertBefore(div.firstChild, el.nextSibling) : t.ct[t.view].appendChild(div.firstChild);
		newItem.firstChild.style.backgroundColor = color;
		t.addListenersToItem(t.view, newItem);

		if(repaint){
			t.reindexAndRetrieveCollection();
		}

		return newItem;
	},

	addItems: function(elem, items, notReplace, notReindex){
		if(elem === undefined){
			return false;
		}

		notReindex = notReindex ? true : false;

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
					var newEl = this.insertItem(el, false, item, this, '#00ee00');
					this.doClickDelete(el);
					el = newEl;
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
					if(id === -1 || id === 0){
						//TODO: use insertItem()!!
						if(this.view === 'grid'){
							el.nextSibling.childNodes[2].value = items[i].id;
							el.nextSibling.firstChild.style.background = 'url(' + items[i].icon.url('%2F', '/') + ') no-repeat center center';
							el.nextSibling.firstChild.style.backgroundSize = 'contain';
							el.nextSibling.firstChild.title = items[i].path;
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
				el = this.insertItem(el, false, items[i], null, '#00ee00');
			} else {
				itemsSet[1].push(items[i].id);
			}
		}
		this.reindexAndRetrieveCollection();
		return itemsSet;
	},

	reindexAndRetrieveCollection: function(notSetHot){
		var ct = this.ct[this.view], 
			val, btns_up, btns_down, btns_edit,
			labels = document.getElementsByClassName(this.view + '_label');

		btns_edit = ct.getElementsByClassName('btn_edit');
		if(this.view === 'list'){
			btns_up = ct.getElementsByClassName('btn_up');
			btns_down = ct.getElementsByClassName('btn_down');
		}

		this.collectionCsv = ',';
		this.collectionArr = [];
		this.collectionNum = 0;

		for(var i = 0; i < ct.childNodes.length; i++){
			switch(this.view){
				case 'grid':
					ct.childNodes[i].id = 'grid_item_' + (i+1);
					//labels[i].id = 'label_' + (i+1);
					val = ct.childNodes.length > 1 ? parseInt(document.we_form.collectionItem_we_id[i].value) : parseInt(document.we_form.collectionItem_we_id.value);
					break;
				case 'list':
					val = parseInt(document.getElementById('yuiAcResultItem_' + ct.childNodes[i].id.substr(10)).value);
					btns_up[i].disabled = i === 0;
					btns_down[i].disabled = (i === (ct.childNodes.length - 1));
					break;
			}
			labels[i].innerHTML = i+1;
			

			if(val === 0 || val === -1){
				this.collectionCsv += -1 + ',';
				this.collectionArr.push(-1);
			} else {
				this.collectionCsv += val + ',';
				this.collectionArr.push(val);
				this.collectionNum++;
			}
		}
		this.numSpan.innerHTML = this.collectionNum;

		if(val !== -1){
			if(this.view === 'grid'){
				this.insertItem(ct.lastChild, true);
			} else {
				this.insertItem(ct.lastChild, true);
			}
		}

		if(!this.collectionName){
			this.collectionName = (this.we_const.TBL_PREFIX + this.we_doc.remTable === this.we_const.FILE_TABLE) ? '_fileCollection' : '_objectCollection';
		}
		document.we_form.elements['we_' + this.we_doc.name + this.collectionName].value = this.collectionCsv;
		if(!notSetHot){
			top.weEditorFrameController.getActiveEditorFrame().setEditorIsHot(true);
		}
	},

	hideSpace: function(elem){ // TODO: use classes do define states!
		elem.style.width = '12px';
		elem.style.right = '0px';
		elem.style.border = '1px solid white';
		elem.style.backgroundColor = 'white';
		elem.style.margin = '0';
		elem.previousSibling.style.right = '14px';
		elem.parentNode.nextSibling.firstChild.style.left = '0px';
	},
	
	resetColors: function(){
		for(var i = 0; i < this.ct[this.view].childNodes.length; i++){
			this.resetItemColors(this.ct[this.view].childNodes[i]);
		}
	},

	resetItemColors: function(el){
		if(!el){
			return;
		}

		switch(this.view){
			case 'grid':
				el.firstChild.style.border = '1px solid #006db8';
				el.firstChild.style.backgroundColor = 'white';
				break;
			case 'list':
				el.style.border = '1px solid #006db8';
				el.firstChild.style.backgroundColor = '#f5f5f5';
				break;
		}
	},

	allowDrop: function(evt){
		evt.preventDefault();
	},

	enterDrag: function(type, view, evt, elem){
		var el = this.getItem(elem);
		var data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : top.dd.dataTransfer.text.split(',');

		if(this.view === 'grid' && type === 'item'){
			this.outMouse(type, this.view, elem);
		}

		switch(data[0]){
			case 'moveItem':
				if(type === 'item'){
					var c = this.ct[this.view],
						newPos;

					if(!this.dd.moveItem.removed){
						newPos = [].indexOf.call(c.children, el);
						c.removeChild(this.dd.moveItem.el);
						c.insertBefore(this.getPlaceholder(), c.childNodes[newPos + (newPos >= this.dd.moveItem.pos ? 0 : -1)]);
						this.dd.moveItem.removed = true;
						return;
					}

					newPos = [].indexOf.call(c.children, el);
					c.removeChild(this.getPlaceholder());
					c.insertBefore(this.getPlaceholder(), c.childNodes[newPos]);
				}
				break;
			case 'dragItem':
			case 'dragFolder':
				if(this.view === 'grid'){
					switch(type){
						case 'item':
							var c = this.ct[this.view],
								index;
							for(var i = 0; i < c.childNodes.length; i++){
								//el.firstChild.style.border = '1px solid #006db8';
								//el.firstChild.style.backgroundColor = 'white';
							}
							break;
						case 'space':
							if(elem.parentNode.id.substr(10) % this.itemsPerRow === 0){
								elem.style.width = '36px';
								elem.previousSibling.style.right = '42px';
								elem.style.margin = '0 0 0 4px';
							} else {
								elem.style.width = '48px';
								elem.style.right = '-22px';
								elem.style.margin = '0 4px 0 4px';
								elem.previousSibling.style.right = '37px';
								elem.parentNode.nextSibling.firstChild.style.left = '22px';
							}
							break;
					}
					if(data[0] === 'dragFolder'){
						elem.style.border = '1px dotted #ffff00';
						elem.style.backgroundColor = '#ffffee';
					} else if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) !== -1){
						elem.style.border = '1px dotted #00ff00';
						elem.style.backgroundColor = '#fafffa';
					} else {
						elem.style.border = '1px dotted #ff0000';
						elem.style.backgroundColor = '#fffafa';
					}
				} else {
					this.dd.counter++;
					var c = this.ct[this.view];
					for(var i = 0; i < c.childNodes.length; i++){
						c.childNodes[i].style.border = '1px solid #006db8';
						c.childNodes[i].firstChild.style.backgroundColor = '#f5f5f5';
					}
					switch(type){
						case 'item':

							if(data[0] === 'dragFolder'){
								el.style.border = '1px dotted #ffff00';
								el.firstChild.style.backgroundColor = '#ffffee';
							} else if(!this.we_doc.remCT || this.we_doc.remCT.search(',' + data[3]) !== -1){
								el.style.border = '1px dotted #00ff00';
								el.firstChild.style.backgroundColor = '#fafffa';
							} else {
								el.style.border = '1px dotted #ff0000';
								el.firstChild.style.backgroundColor = '#fffafa';
							}
							break;
						/*
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
						*/
					}
				}
				break;
			default:
				return;
		}
	},
			
	leaveDrag: function(type, view, evt, elem){
		if(this.view === 'list'){
			this.dd.counter--;
			if(this.dd.counter === 0){
				elem.style.border = '1px solid #006db8';
				elem.firstChild.style.backgroundColor = '#f5f5f5';
			}
		} else {
			var data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : top.dd.dataTransfer.text.split(',');
			switch(data[0]){
				case 'dragItem':
				case 'dragFolder':
					switch(type){
						case 'item':
							elem.style.border = '1px solid #006db8';
							elem.style.backgroundColor = '#ffffff';
							break;
						case 'space':
							this.hideSpace(elem);
							break;
					}
					break;
				default:
					return;
			}
		}
	},

	overMouse: function(type, view, elem){
		if(view === 'grid'){
			switch(type){
				case 'item':
					elem.lastChild.style.display = 'block';
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
					elem.lastChild.style.display = 'none';
					break;
				case 'btns':
					elem.style.opacity = '0.8';
					break;
			}
		}
	},

	startMoveItem: function(evt, view) {
		var el = this.getItem(evt.target),
			index = parseInt(el.id.substr(10)),
			position = [].indexOf.call(this.ct[view].children, el);

		this.view = view;
		this.dd.isMoveItem = true;
		this.dd.moveItem.el = el;
		this.dd.moveItem.id = el.id;
		this.dd.moveItem.index = index;
		this.dd.moveItem.next = el.nextSibling;
		this.dd.moveItem.pos = position;
		this.dd.moveItem.removed = false;

		top.dd.dataTransfer.text = 'moveItem,' + el.id;
		evt.dataTransfer.setData('text', 'moveItem,' + el.id);

		if(this.view === 'grid'){
			this.outMouse('item', this.view, el.firstChild);
		}
	},

	dropOnItem: function(type, view, evt, elem){
		evt.preventDefault();

		var data = evt.dataTransfer.getData("text") ? evt.dataTransfer.getData("text").split(',') : top.dd.dataTransfer.text.split(','),
			el, index;

		switch(data[0]){
			case 'moveItem':
				this.dd.isMoveItem = false;
				if(this.dd.moveItem.el !== this.getItem(elem)){
					var indexNextToNewPos = this.getPlaceholder().nextSibling ? this.getPlaceholder().nextSibling.id.substr(10) : 0,
						otherView = view === 'grid' ? 'list' : 'grid';

					this.ct[this.view].replaceChild(this.dd.moveItem.el, this.getPlaceholder());
					this.dd.moveItem.el.firstChild.style.borderColor = 'green';
					this.reindexAndRetrieveCollection();

					setTimeout(function(){
						weCollectionEdit.dd.moveItem.el.firstChild.style.borderColor = '#006db8';
						weCollectionEdit.resetDdParams();
					}, 200);
					
					//weCollectionEdit.resetDdParams();
				}
				break;
			case 'dragItem':
			case 'dragFolder':
					el = this.getItem(elem);
					index = el.id.substr(10);

					if(type === 'item'){
						if(this.view === 'list'){
							//el.style.border = '1px solid red';
						}
						//el.firstChild.style.backgroundColor = 'palegreen';
					} else {
						this.hideSpace(elem);
					}

					if(this.we_const.TBL_PREFIX + this.we_doc.remTable === data[1]){
						if(!this.we_doc.remCT || data[3] === 'folder' || this.we_doc.remCT.search(',' + data[3]) != -1){
							this.callForValidItemsAndInsert(index, data[2], false, type !== 'item', el);
							return;
						} else {
							
							//alert("the item you try to drag from doesn't match your collection's content types");
						}
					} else {
						alert("the tree you try to drag from doesn't match your collection's table property");
					}
					setTimeout(function(){
						weCollectionEdit.resetItemColors(el);
					}, 100);
				break;
			default:
				return;
		}
	},

	dragEnd: function(evt){
		if(this.dd.isMoveItem){
			this.cancelMoveItem();
		}
	},

	cancelMoveItem: function(){
		this.ct[this.view].removeChild(this.getPlaceholder());
		this.dd.moveItem.el.style.borderColor = 'red';
		this.ct[this.view].insertBefore(this.dd.moveItem.el, this.dd.moveItem.next);
		this.reindexAndRetrieveCollection();
		setTimeout(function(){
			weCollectionEdit.dd.moveItem.el.style.borderColor = '#006db8';
			weCollectionEdit.resetDdParams();
			if(this.view === 'list'){
				
			}
		}, 300);
	},

	resetDdParams: function(){
		this.dd.placeholder = null;
		this.dd.counter = 0;

		this.dd.isMoveItem = false;
		this.dd.moveItem = {
			el: null,
			id: 0,
			index: 0,
			next: null,
			pos: 0,
			removed: false,
		};
	},

	callForValidItemsAndInsert: function (index, csvIDs, message, notReplace) {
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
								weCollectionEdit.reindexAndRetrieveCollection();
							} else {
								var resp = weCollectionEdit.addItems(document.getElementById(weCollectionEdit.view + '_item_' + index), respArr, notReplace);
								if(message){
									top.we_showMessage(weCollectionEdit.g_l.info_insertion.replace(/##INS##/, resp[0]).replace(/##REJ##/, resp[1]), 1, window);
								}
							}
							setTimeout(function(){
								weCollectionEdit.resetColors();
							}, 300);
							
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
	},

	insertImportedDocuments : function(ids) {
		if(ids){
			this.callForValidItemsAndInsert(this.ct[this.view].lastChild.id.substr(10), ids.join());
		}

	}
};