function leContent() {}


leContent.appendElement = function(element) {
	var messageLog = document.getElementById("leContent");
	var messageLogHeight = document.getElementById("leContent");
	messageLog.innerHTML += "\n" + element.innerHTML;
	messageLogHeight.scrollTop = 1000000;

}


leContent.appendText = function(text) {
	var messageLog = document.getElementById("leContent");
	var messageLogHeight = document.getElementById("leContent");
	messageLog.innerHTML += "\n" + text + "\n";
	messageLogHeight.scrollTop = 1000000;

}


leContent.appendErrorText = function(text) {
	var messageLog = document.getElementById("leContent");
	var messageLogHeight = document.getElementById("leContent");
	messageLog.innerHTML += "\n<h1 class=\"error\">" + text + "</h1>\n";
	messageLogHeight.scrollTop = 1000000;

}


leContent.replaceElement = function(element) {
	var messageLog = document.getElementById("leContent");
	messageLog.innerHTML = "\n" + element.innerHTML;
}


leContent.replaceText = function(text) {
	var messageLog = document.getElementById("leContent");
	var messageLogHeight = document.getElementById("leContent");
	messageLog.innerHTML = text + "\n";
	messageLogHeight.scrollTop = 1000000;
}


leContent.scrollDown = function() {
	document.getElementById("leContent").scrollTop = 1000000;

}


function enableProxy(checked) {
	document.leWebForm.le_proxy_host.disabled = !checked;
	document.leWebForm.le_proxy_port.disabled = !checked;
	document.leWebForm.le_proxy_username.disabled = !checked;
	document.leWebForm.le_proxy_password.disabled = !checked;
	if(checked) {
		document.leWebForm.le_proxy_host.focus();
	}
}


function enableRegistration(checked) {
	document.leWebForm.le_serial.disabled = !checked;
	if(checked) {
		document.leWebForm.le_serial.focus();
	}
}

