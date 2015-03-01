

//TODO: check if there's really no possibility to get TransferData 

var spacer = document.createElement("div");
spacer.style.height = '34px';
spacer.style.backgroundColor = 'white';
spacer.style.border = '1px solid #006db8';
spacer.style.width = '804px';
spacer.style.marginTop = '4px';
spacer.setAttribute("ondrop","drop(event)");
spacer.setAttribute("ondragover","allowDrop(event)");

var lastY = 0; 
var dragID = 0;
var dragEl = null;
var removed = false;
isDragRow = false;

function repaintAndRetrieveCsv(addIndex, addNum){
	var addAtIndex = addIndex || 0;
	var addNumber = addNum || 0;

	var t = document.getElementById('content_table'), index, csv = ',';
	for(var i = 0; i < t.childNodes.length; i++){
		index = t.childNodes[i].id.substr(5);
		csv += document.getElementById('yuiAcResultItem_' + index).value + ',';
		document.getElementById('label_' + index).innerHTML = i + 1;
		document.getElementById('btn_direction_up_' + index).disabled = (i === 0);
		document.getElementById('btn_direction_down_' + index).disabled = (i === (t.childNodes.length - 1));

		if(addAtIndex && addAtIndex == index && addNum){
			while(addNum > 0){
				csv += -1 + ',';
				addNum--;
			}
		}
	}
	document.we_form.elements['we_' + we_name + '_Collection'].value = csv;
}

function moveUp(elem){
	var el = getRow(elem);

	if(el.parentNode.firstChild !== el){
		el.parentNode.insertBefore(el, el.previousSibling);
		repaintAndRetrieveCsv();
	}
}

function moveDown(elem){
	var el = getRow(elem);
	var sib = el.nextSibling;

	if(true || sib){
		el.parentNode.insertBefore(el.nextSibling, el);
		repaintAndRetrieveCsv();
	}
}

function addRows(elem){
	var el = getRow(elem);
	var index = el.id.substr(5);//top.console.debug('numselect_' + index);

	repaintAndRetrieveCsv(index, document.getElementById('numselect_' + index).value);
}

function deleteRow(elem){
	var el = getRow(elem);

	el.parentNode.removeChild(el);
	repaintAndRetrieveCsv();
}

function getRow(el){
	while(el.className !== 'drop_reference' && el.className !== 'content_table'){
		el = el.parentNode;
	}

	return el;
}

function allowDrop(ev) {
	ev.preventDefault();
}

function drag(ev) {
	//console.debug("start y: " + ev.target.id);
	isDragRow = true;
	lastY = ev.clientY;
	dragEl = ev.target;
	dragID = ev.target.id;
	removed = false;
	ev.dataTransfer.setData("text", ev.target.id);
}

function enterDrag(ev){
	var el = getRow(ev.target);

	//TODO: to avoid problems use ev.dataTransfer.getData("text")); to decide what element is dragged: => add fromTree as first param when from tree
	if(isDragRow === true){
		if(el.id !== dragID){
			el = lastY < ev.clientY ? el.nextSibling : el;

			if(!removed){
				document.getElementById('content_table').removeChild(dragEl);
				removed = true;
			}

			if(spacer.parentNode){
				document.getElementById('content_table').removeChild(spacer);
			}
			document.getElementById('content_table').insertBefore(spacer, el);
			lastY = ev.clientY + (lastY < ev.clientY ? 20 : 20);
		}
	} else {
		var t = document.getElementById('content_table'), index;
		for(var i = 0; i < t.childNodes.length; i++){
			index = t.childNodes[i].id.substr(5);
			document.getElementById('drag_' + index).style.border = '1px solid #006db8';
		}
		el.style.border = '1px solid #00cc00';
	}
}

function drop(ev) {
	if(isDragRow){
		isDragRow = false;
		ev.preventDefault();
		var data = ev.dataTransfer.getData("text");
		document.getElementById('content_table').replaceChild(dragEl, spacer);
		repaintAndRetrieveCsv();
	} else {
		ev.preventDefault();
		//top.console.debug(we_remTable);
		var el = getRow(ev.target), index, data, item, id, table;

		index = el.id.substr(5);
		data = ev.dataTransfer.getData("text").split(',');
		el.style.border = '1px solid #006db8';

		//check table, if item or group and get path fpr ID(s) by ajax
		if(we_remTable === data[1]){
			document.getElementById('yuiAcInputItem_' + index).value = "get path using ajax: id = " + data[2];
			document.getElementById('yuiAcResultItem_' + index).value = data[2];
			repaintAndRetrieveCsv();
		} else {
			alert("your object's table does not match remTable");
		}
		
	}
}