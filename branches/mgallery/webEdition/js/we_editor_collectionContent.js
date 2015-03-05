weCollectionEdit = {
	maxIndex: 0,
	blankRow: '',
	lastY: 0,
	dragID: 0,
	dragEl: null,
	removed: false,
	isDragRow: false,
	dd: {
		fillEmptyRows: false
	},
	spacer: null,


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
			num = document.getElementById('numselect_' + el.id.substr(5)).value;

		for(var i = 0; i < num; i++){
			el = this.addRow(el, false);
		}
		this.repaintAndRetrieveCsv();
	},

	doClickDelete: function(elem){
		var el = this.getRow(elem);

		el.parentNode.removeChild(el);
		this.repaintAndRetrieveCsv();
	},

	getSpacer: function(){
		if(this.spacer !== null){
			return this.spacer;
		}

		this.spacer = document.createElement("div");
		this.spacer.style.height = '34px';
		this.spacer.style.backgroundColor = 'white';
		this.spacer.style.border = '1px solid #006db8';
		this.spacer.style.width = '804px';
		this.spacer.style.marginTop = '4px';
		this.spacer.setAttribute("ondrop","weCollectionEdit.dropOnRow(event)");
		this.spacer.setAttribute("ondragover","weCollectionEdit.allowDrop(event)");

		return this.spacer;
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


		newElem = document.getElementById('content_table').insertBefore(div.firstChild, el.nextSibling);
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
			index = el.id.substr(5),
			rowsFull = false,
			item, id;

		//set first item on drop row
		if(items.length){
			item = items.shift();
			document.getElementById('yuiAcInputItem_' + index).value = item.path;
			document.getElementById('yuiAcResultItem_' + index).value = item.id;
		}

		for(var i = 0; i < items.length; i++){
			if(this.dd.fillEmptyRows && !rowsFull && el.nextSibling && typeof el.nextSibling.id !== 'undefined' && el.nextSibling.id.substr(0, 5) === 'drag_'){
				index = el.nextSibling.id.substr(5);
				id = document.getElementById('yuiAcResultItem_' + index).value;
				if(id == -1 || id == 0){
					document.getElementById('yuiAcInputItem_' + index).value = items[i].path;
					document.getElementById('yuiAcResultItem_' + index).value = items[i].id;
					el = el.nextSibling;
					continue;
				} else {
					rowsFull = true;
				}
			}
			el = this.addRow(el, false, items[i].id, items[i].path);
		}
		this.repaintAndRetrieveCsv();
	},

	repaintAndRetrieveCsv: function(){
		var t = document.getElementById('content_table'), index, csv = ',', val;
		for(var i = 0; i < t.childNodes.length; i++){
			index = t.childNodes[i].id.substr(5);
			val = document.getElementById('yuiAcResultItem_' + index).value;
			csv += (val != 0 ? val : -1) + ',';
			document.getElementById('label_' + index).innerHTML = i + 1;
			document.getElementById('btn_direction_up_' + index).disabled = (i === 0);
			document.getElementById('btn_direction_down_' + index).disabled = (i === (t.childNodes.length - 1));
		}
		document.we_form.elements['we_' + we_name + '_fileCollection'].value = csv;
	},

	allowDrop: function(evt){
		evt.preventDefault();
	},

	enterDrag: function(evt){
		var el = this.getRow(evt.target),
			data = evt.dataTransfer.getData("text").split(',');
		
		switch(data[0]){
			case 'moveRow':
				if(el.id !== this.dragID){
					el = this.lastY < evt.clientY ? el.nextSibling : el;

					if(!this.removed){
						document.getElementById('content_table').removeChild(this.dragEl);
						this.removed = true;
					}

					if(this.getSpacer().parentNode){
						document.getElementById('content_table').removeChild(this.getSpacer());
					}
					document.getElementById('content_table').insertBefore(this.getSpacer(), el);
					this.lastY = evt.clientY + (this.lastY < evt.clientY ? 20 : 20);
				}
				break;
			case 'dragItem':
			case 'dragFolder':
				var t = document.getElementById('content_table'), index;
				for(var i = 0; i < t.childNodes.length; i++){
					index = t.childNodes[i].id.substr(5);
					document.getElementById('drag_' + index).style.border = '1px solid #006db8';
				}
				el.style.border = '1px solid #00cc00';
				break;
			default: 
				return;
		}
	},

	startDragRow: function(evt) {
		this.isDragRow = true;
		this.lastY = evt.clientY;
		this.dragEl = evt.target;
		this.dragID = evt.target.id;
		this.removed = false;
		evt.dataTransfer.setData('text', 'moveRow,' + evt.target.id);
	},

	dropOnRow: function(evt) {
		evt.preventDefault();

		var data = evt.dataTransfer.getData("text").split(','),
			el, index;

		switch(data[0]){
			case 'moveRow':
				this.isDragRow = false;
				document.getElementById('content_table').replaceChild(this.dragEl, this.getSpacer());
				this.repaintAndRetrieveCsv();
				break;
			case 'dragItem':
			case 'dragFolder':
				el = this.getRow(evt.target);
				index = el.id.substr(5);
				el.style.border = '1px solid #006db8';

				if(we_remTable === data[1]){
					this.setDataFromServer(index, data[2], data[1], (data[0] === 'dragItem' ? 'item' : 'folder'), '', (data[0] === 'dragIitem' ? false : true));
				} else {
					alert("the tree you try to drag from doesn't match your collection's table property");
				}
				break;
			default: 
				return;
		}
	},

	setDataFromServer: function (index, id, table, type, ct, recursive) {
		try {
			if(id){
				var postData;

				table = table || we_fileTable;
				type = type || 'item';
				ct = ct || '';
				recursive = recursive || 0;

				postData = 'we_cmd[id]=' + encodeURIComponent(id);
				postData += '&we_cmd[table]=' + encodeURIComponent(table);
				postData += '&we_cmd[type]=' + encodeURIComponent(type);
				postData += '&we_cmd[ct]=' + encodeURIComponent(ct);
				postData += '&we_cmd[recursive]=' + encodeURIComponent(recursive);

				xhr = new XMLHttpRequest();
				xhr.onreadystatechange = function () {
					if (xhr.readyState === 4) {
						if (xhr.status === 200) {
							var respArr = JSON.parse(xhr.responseText);
							if(type === 'item'){
								document.getElementById('yuiAcInputItem_' + index).value = respArr[0].path;
								document.getElementById('yuiAcResultItem_' + index).value = respArr[0].id;
								weCollectionEdit.repaintAndRetrieveCsv();
							} else {
								weCollectionEdit.addItems(document.getElementById('drag_' + index), respArr);
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
			}
		} catch (e) {
			top.console.debug(e);
		}
	}
};