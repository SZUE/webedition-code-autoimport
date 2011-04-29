function leProgressBar() {}


leProgressBar.hide = function(id) {
	document.getElementById(id).style.display = "none";
	leButton.hide('reload');
	leButton.show('print');

}


leProgressBar.show = function(id) {
	leButton.hide('print');
	document.getElementById(id).style.display = "block";
	leButton.show('reload');

}


leProgressBar.set = function(id, width) {
	var progressPercent = document.getElementById(id + "Percent");
	var progressBar = document.getElementById(id + "Bar");

	if (progressPercent) {
		progressPercent.innerHTML = width + "%";

	}

	if (progressBar) {
		progressBar.style.width = width + "%";

	}

}


leProgressBar.enable = function(id, status) {
	if (status) {
		leProgressBar.show(id);

	} else {
		leProgressBar.hide(id);

	}

}