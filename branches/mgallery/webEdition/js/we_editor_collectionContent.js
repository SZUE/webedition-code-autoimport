var spacer = document.createElement("div");
spacer.style.height = '33px';
spacer.setAttribute("ondrop","drop(event)");
spacer.setAttribute("ondragover","allowDrop(event)");

var lastY = 0; 
var dragID = 0;
var dragEl = null;
var removed = false;

function moveUp(el){
	while(el.className !== 'drop_reference' && el.className !== 'content_table'){console.debug("loop");
		el = el.parentNode;
	}
	if(el.parentNode.firstChild !== el){
		el.parentNode.insertBefore(el, el.previousSibling);
	}
}

function moveDown(el){
	while(el.className !== 'drop_reference' && el.className !== 'content_table'){
		el = el.parentNode;
	}
	var sib = el.nextSibling;

	if(true || sib){
		//el.parentNode.removeChild(el.nextSibling);
		el.parentNode.insertBefore(el.nextSibling, el);
	}
}

function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
	console.debug("start y: " + ev.target.id);
	lastY = ev.clientY;
	dragEl = ev.target;
	dragID = ev.target.id;
	removed = false;
    ev.dataTransfer.setData("text", ev.target.id);
}

function enterDrag(ev){
	var el = ev.target;
		
	while(el.className !== 'drop_reference' && el.className !== 'content_table'){console.debug("loop");
		el = el.parentNode;
	}
	console.debug(el.id + ' - ' + dragID);

	if(el.id !== dragID){
		console.debug(ev);

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
}

function drop(ev) {
    ev.preventDefault();
    var data = ev.dataTransfer.getData("text");
	/*
	var el = ev.target;
	while(el.className !== 'drop_reference' && el.className !== 'content_table'){console.debug("loop");
		el = el.parentNode;
	}
	*/
	document.getElementById('content_table').replaceChild(dragEl, spacer);
}