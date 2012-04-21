function leForm() {}

leForm.ForwardInterval = null;

leForm.next = function() {
	if(leButton.isEnabled("next")) {
		window.clearInterval(leForm.ForwardInterval);
		document.leWebForm.submit();

	}

}

leForm.forceNext = function() {
	window.clearInterval(leForm.ForwardInterval);
	document.leWebForm.submit();

}


leForm.back = function() {
	if(leButton.isEnabled("back")) {
		window.clearInterval(leForm.ForwardInterval);
		window.frames["leLoadFrame"].document.location = backUrl;

	}

}


leForm.reload = function() {
	// reload uses nextUrl - there was an error
	if(leButton.isEnabled("reload")) {
		window.clearInterval(leForm.ForwardInterval);
		window.frames["leLoadFrame"].document.location = nextUrl;

	}

}


leForm.proceedUrl = function() {
	window.clearInterval(leForm.ForwardInterval);
	window.frames["leLoadFrame"].document.location = nextUrl;

}


leForm.setInputField = function(name, value) {
	document.leWebForm[name].value = value;

}


leForm.evalCheckBox = function(field, onChecked, onNotChecked) {
	if(field.checked) {
		eval(onChecked);

	} else {
		eval(onNotChecked);

	}

}


leForm.checkSubmit = function(source) {

	// IE
	if (null!=window.event) {
		w = window.event;

	// Netscape/Mozilla
	} else if(null!=source) {
		w = source;

	// schade
	} else {
		w = null;

	}

	if (null!=w) {
		// check if enter is pressed
		if (13==w.keyCode) {
			window.clearInterval(leForm.ForwardInterval);
			leForm.next();

		}

	}

}


leForm.setFocus = function(name) {
	field = eval('document.leWebForm.' + name);
	if(field != undefined) {
		// do it twice, cause ie ignores sometimes the first call
		field.focus();
		field.focus();

	}

}

leForm.forward = function() {

	var elem = document.getElementById("secondTimer");
	if (elem) {
		var counter = elem.innerHTML;
		switch (counter) {
			case "1":
				elem.innerHTML = 0;
				window.clearInterval(leForm.ForwardInterval);
				leForm.forceNext();
				break;

			default:
				elem.innerHTML = (counter - 1);
				leForm.ForwardInterval = window.setTimeout('leForm.forward()', 1000);
				break;

		}

	} else {
		leForm.ForwardInterval = window.setTimeout('leForm.forward()', 1000);

	}

}