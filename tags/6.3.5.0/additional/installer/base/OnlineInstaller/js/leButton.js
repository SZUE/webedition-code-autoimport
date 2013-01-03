function leButton() {}


leButton.down = function(el){
	if (el.className != "leBtnDisabled") {
		var tds = el.getElementsByTagName("TD");
		el.className = "leBtnClicked";
		tds[0].className = "leBtnLeftClicked";
		tds[1].className = "leBtnMiddleClicked";
		tds[2].className = "leBtnRightClicked";

	}

}


leButton.up = function(el) {
	if (el.className != "leBtnDisabled") {
		leButton.out(el);
		return true;

	}
	return false;

}


leButton.out = function(el) {
	if (el.className != "leBtnDisabled" && el.className != "leBtn") {
		var tds = el.getElementsByTagName("TD");
		el.className = "leBtn";
		tds[0].className = "leBtnLeft";
		tds[1].className = "leBtnMiddle";
		tds[2].className = "leBtnRight";

	}

}


leButton.disable = function(id) {
	var el = document.getElementById(id + '_table');
	if(el != null) {
		el.className = "leBtnDisabled";
		var tds = el.getElementsByTagName("TD");
		tds[0].className = "leBtnLeftDisabled";
		tds[1].className = "leBtnMiddleDisabled";
		tds[2].className = "leBtnRightDisabled";
		var img = document.getElementById(el.id + "_img");
		if(img != null && img.src.indexOf("Disabled.gif") == -1) {
			img.src = img.src.replace(/\.gif/, "Disabled.gif");

		}
		eval(id + '_enabled = false;');

	}

}


leButton.enable = function(id) {
	var el = document.getElementById(id + '_table');
	if(el != null) {
		el.className = "leBtn";
		var tds = el.getElementsByTagName("TD");
		tds[0].className = "leBtnLeft";
		tds[1].className = "leBtnMiddle";
		tds[2].className = "leBtnRight";
		var img = document.getElementById(el.id + "_img");
		if(img != null) {
			img.src = img.src.replace(/\Disabled.gif/, ".gif");

		}
		eval(id + '_enabled = true;');

	}

}


leButton.isDisabled = function(id) {
	var el = document.getElementById(id + '_table');
	if(el != null && el.className == "leBtnDisabled") {
		return true

	} else {
		return false;

	}

}


leButton.isEnabled = function(id) {
	return !this.isDisabled(id);

}

leButton.hide = function(id) {
	var el = document.getElementById(id + '_table');
	if(el != null){
		el.style.display = 'none';

	}

}


leButton.show = function(id) {
	var el = document.getElementById(id + '_table');
	if(el != null) {
		el.style.display = 'block';

	}

}