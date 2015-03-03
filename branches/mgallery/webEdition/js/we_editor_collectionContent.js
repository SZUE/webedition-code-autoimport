weCollectionEdit = {
	maxIndex: 0,
	blankRow: '',
	lastY: 0,
	dragID: 0,
	dragEl: null,
	removed: false,
	isDragRow: false,
	spacer: null,

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
		this.spacer.setAttribute("ondrop","weCollectionEdit.drop(event)");
		this.spacer.setAttribute("ondragover","weCollectionEdit.allowDrop(event)");

		return this.spacer;
	}, 

	repaintAndRetrieveCsv: function(addIndex, addNum){
		var addAtIndex = addIndex || 0;
		var addNumber = addNum || 0;

		var t = document.getElementById('content_table'), index, csv = ',', val;
		for(var i = 0; i < t.childNodes.length; i++){
			index = t.childNodes[i].id.substr(5);
			val = document.getElementById('yuiAcResultItem_' + index).value;
			csv += (val != 0 ? val : -1) + ',';
			document.getElementById('label_' + index).innerHTML = i + 1;
			document.getElementById('btn_direction_up_' + index).disabled = (i === 0);
			document.getElementById('btn_direction_down_' + index).disabled = (i === (t.childNodes.length - 1));
			/*
			if(addAtIndex && addAtIndex == index && addNum){
				while(addNum > 0){
					csv += -1 + ',';
					addNum--;
				}
			}
			*/
		}
		document.we_form.elements['we_' + we_name + '_Collection'].value = csv;
	},

	moveUp: function(elem){
		var el = this.getRow(elem);

		if(el.parentNode.firstChild !== el){
			el.parentNode.insertBefore(el, el.previousSibling);
			this.repaintAndRetrieveCsv();
		}
	},

	moveDown: function(elem){
		var el = this.getRow(elem);
		var sib = el.nextSibling;

		if(true || sib){
			el.parentNode.insertBefore(el.nextSibling, el);
			this.repaintAndRetrieveCsv();
		}
	},

	addRows: function(elem){
		var el = this.getRow(elem),
			num = document.getElementById('numselect_' + el.id.substr(5)).value,
			div, newElem, cmd1, cmd2;

		for(var i = 0; i < num; i++){
			cmd1 = weCmdEnc(weCollectionEdit.selectorCmds[0].replace(/XX/g, ++this.maxIndex));
			cmd2 = weCmdEnc(weCollectionEdit.selectorCmds[1].replace(/XX/g, this.maxIndex));
			div = document.createElement("div");
			div.innerHTML = this.blankRow.replace(/XX/g, this.maxIndex).replace(/CMD1/, cmd1).replace(/CMD2/, cmd2)
			newElem = document.getElementById('content_table').insertBefore(div.firstChild, el.nextSibling);
			el = newElem;
		}
		this.repaintAndRetrieveCsv();
	},


	deleteRow: function(elem){
		var el = this.getRow(elem);

		el.parentNode.removeChild(el);
		this.repaintAndRetrieveCsv();
	},

	getRow: function(elem){
		while(elem.className !== 'drop_reference' && elem.className !== 'content_table'){
			elem = elem.parentNode;
		}

		return elem;
	},

	allowDrop: function(evt){
		evt.preventDefault();
	},

	drag: function(evt) {
		//console.debug("start y: " + evt.target.id);
		this.isDragRow = true;
		this.lastY = evt.clientY;
		this.dragEl = evt.target;
		this.dragID = evt.target.id;
		this.removed = false;
		evt.dataTransfer.setData("text", evt.target.id);
	},

	enterDrag: function(evt){
		var el = this.getRow(evt.target);

		//TODO: to avoid problems use evt.dataTransfer.getData("text")); to decide what element is dragged: => add fromTree as first param when from tree
		if(this.isDragRow === true){
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
		} else {
			var t = document.getElementById('content_table'), index;
			for(var i = 0; i < t.childNodes.length; i++){
				index = t.childNodes[i].id.substr(5);
				document.getElementById('drag_' + index).style.border = '1px solid #006db8';
			}
			el.style.border = '1px solid #00cc00';
		}
	},

	drop: function(evt) {
		if(this.isDragRow){
			this.isDragRow = false;
			evt.preventDefault();
			var data = evt.dataTransfer.getData("text");
			document.getElementById('content_table').replaceChild(this.dragEl, this.getSpacer());
			this.repaintAndRetrieveCsv();
		} else {
			evt.preventDefault();
			var el = this.getRow(evt.target), index, data, item, id, table;

			index = el.id.substr(5);
			data = evt.dataTransfer.getData("text").split(',');
			el.style.border = '1px solid #006db8';

			//check table, if item or group and get path fpr ID(s) by ajax
			if(we_remTable === data[1]){
				if(data[0] === 'item'){
					document.getElementById('yuiAcInputItem_' + index).value = "get path using ajax: id = " + data[2];
					document.getElementById('yuiAcResultItem_' + index).value = data[2];
					this.repaintAndRetrieveCsv();
				} else {
					alert("drag folder");
				}
			} else {
				alert("your object's table does not match remTable");
			}

		}
	}
	
	
};