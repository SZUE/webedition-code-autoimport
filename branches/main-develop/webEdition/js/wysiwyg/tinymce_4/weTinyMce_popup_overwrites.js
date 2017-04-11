/* global tinymce, top, tinyMCEPopup */

'use strict';

/* overwrite buggy function getWindowArg() */
tinyMCEPopup.getWindowArg = function (c, b) {
	return (this.params !== undefined && this.params !== null && this.params[c] !== undefined && tinymce.is(this.params[c])) ? this.params[c] : b;
};

tinyMCEPopup._onDOMLoaded = function () {
	var b = tinyMCEPopup,
					d = document.title,
					e, c, a;
	try {
		if (b.features.translate_i18n !== false) {
			c = document.body.innerHTML;
			if (tinymce.isIE) {
				c = c.replace(/ (value|title|alt)=([^"][^\s>]+)/gi, ' $1="$2"');
			}
			document.dir = b.editor.getParam("directionality", "");
			if ((a = b.editor.translate(c)) && a != c) {
				document.body.innerHTML = a;
			}
			if ((a = b.editor.translate(d)) && a != d) {
				document.title = d = a;
			}
		}
		if (!b.editor.getParam("browser_preferred_colors", false) || !b.isWindow) {
			b.dom.addClass(document.body, "forceColors");
		}
		document.body.style.display = "";
		if (tinymce.isIE && !tinymce.isIE11) {
			document.attachEvent("onmouseup", tinyMCEPopup._restoreSelection);
			b.dom.add(b.dom.select("head")[0], "base", {
				target: "_self"
			});
		} else {
			if (tinymce.isIE11) {
				document.addEventListener("mouseup", tinyMCEPopup._restoreSelection, false);
			}
		}
		b.restoreSelection();
		b.resizeToInnerSize();
		if (!b.isWindow) {
			b.editor.windowManager.setTitle(window, d);
		} else {
			window.focus();
		}
		if (!tinymce.isIE && !b.isWindow) {
			b.dom.bind(document, "focus", function () {
				b.editor.windowManager.focus(b.id);
			});
		}
		tinymce.each(b.dom.select("select"), function (f) {
			f.onkeydown = tinyMCEPopup._accessHandler;
		});
		tinymce.each(b.listeners, function (f) {
			f.func.call(f.scope, b.editor);
		});
		if (b.getWindowArg("mce_auto_focus", true)) {
			window.focus();
			tinymce.each(document.forms, function (g) {
				tinymce.each(g.elements, function (f) {
					if (b.dom.hasClass(f, "mceFocus") && !f.disabled) {
						f.focus();
						return false;
					}
				});
			});
		}
		document.onkeyup = tinyMCEPopup._closeWinKeyHandler;
	} catch (e) {
	}
};

/* webEdition Functions */

// overwrite function resizeToInnerSize to add some width and height to original tinyMce-Dialogs
// The call for resizeToInnerSize is a good moment for other actions to take place too

tinyMCEPopup.resizeToInnerSize = function () {
	var a = this;
	var ratio_h = a.dom.getAttrib(document.body, "role") === "application" ? 4 / 6 : 1;
	var ratio_w = top.isWeDialog === undefined ? 3 / 4 : 1;

	if ((a.dom.getAttrib(document.body, "id") === "table")) {
		document.getElementById("advanced_panel").style.height = "auto";
		ratio_h = 6 / 7;

		//TODO: move the following style-fixes to css
		var elem = document.getElementById("insert").parentNode;
		elem.style.float = "";
		elem.style.marginRight = "-8px";
		elem = document.getElementById("cancel").parentNode;
		elem.style.float = "";
		elem.style.marginRight = 0;
	}

	var a = this;
	setTimeout(function () {
		if (a.dom) {
			var b = a.dom.getViewPort(window), pw;
			var win = a.id || window;
			win.resizeBy(a.getWindowArg("mce_width") - (b.w * ratio_w), a.getWindowArg("mce_height") - (b.h * ratio_h));
		}
	}, 10);

	if (!tinymce.isIE && top.isWeDialog === undefined
					&& (a.dom.getAttrib(document.body, "role") === undefined || a.dom.getAttrib(document.body, "role") !== "application")) {
		a.dom.addClass(document.body, "useWeFooter");
	}

	//replace buttons
	if (top.isWeDialog === undefined) {
		var btn, tmp;
		var g_l = WE().consts.g_l.tinyMceTranslationObject[a.editor.getParam("language", "")].we.dialog_btns;
		var buttonsDiv = document.createElement("div");
		buttonsDiv.style.float = 'right';

		if ((btn = document.getElementById('apply'))) {
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(btn);
		}
		if ((btn = document.getElementById('remove'))) {
			var oc = btn.getAttribute('onclick');
			tmp = document.createElement("div");
			tmp.innerHTML = '<button id="remove" class="weBtn weIconButton" onclick="' + oc + '" title="' + g_l.btnDelete.alt + '" type="button" style="display:none;"><i class="fa fa-firsticon fa-lg fa-trash-o"> </i></button>';
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(tmp.firstChild);
		}
		if ((btn = document.getElementById('insert'))) {
			tmp = document.createElement("div");
			if (!document.getElementById('search_tab')) {
				tmp.innerHTML = '<button id="insert" class="weBtn weIconTextButton" title="' + g_l.btnOk.alt + '" type="insert"><i class="fa fa-firsticon fa-lg fa-check fa-ok"> </i> ' + g_l.btnOk.text + '</button>';
			} else {
				tmp.innerHTML = '<button id="insert" class="weBtn weIconTextButton" title="' + g_l.btnSearchNext.alt + '" type="insert"><i class="fa fa-firsticon fa-lg fa-binoculars"> </i> ' + g_l.btnSearchNext.text + '</button>';
			}
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(tmp.firstChild);
		}
		if ((btn = document.getElementById('replaceBtn'))) {
			var oc = btn.getAttribute('onclick');
			tmp = document.createElement("div");
			tmp.innerHTML = '<button id="replaceBtn" class="weBtn weIconTextButton" onclick="' + oc + '" title="' + g_l.btnReplace.alt + '" type="button" style="display:none;"> <i class="fa fa-firsticon fa-lg fa-retweet"> </i> ' + g_l.btnReplace.text + '</button>';
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(tmp.firstChild);
		}
		if ((btn = document.getElementById('replaceAllBtn'))) {
			var oc = btn.getAttribute('onclick');
			tmp = document.createElement("div");
			tmp.innerHTML = '<button id="replaceAllBtn" class="weBtn weIconTextButton" onclick="' + oc + '" title="' + g_l.btnReplaceAll.alt + '" type="button" style="display:none;"><i class="fa fa-firsticon fa-lg fa-database"> </i> <i class="fa fa-firsticon fa-lg fa-retweet"> </i> ' + g_l.btnReplaceAll.alt + '</button>';
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(tmp.firstChild);
		}
		if ((btn = document.getElementById('cancel'))) {
			tmp = document.createElement("div");
			tmp.innerHTML = '<button id="cancel" class="weBtn weIconTextButton" onclick="tinyMCEPopup.close();" title="' + g_l.btnCancel.alt + '" type="button"><i class="fa fa-firsticon fa-lg fa-ban fa-cancel"> </i> ' + g_l.btnCancel.text + '</button>';
			btn.parentNode.removeChild(btn);
			buttonsDiv.appendChild(tmp.firstChild);
		}
		try {
			document.getElementsByClassName('mceActionPanel')[0].appendChild(buttonsDiv);
		} catch (e) {
		}
	}
};

tinyMCEPopup.close = function() {
	var t = this;

	// To avoid domain relaxing issue in Opera
	function close() {
		if (t.editor.windowManager.windows.length) {
			t.editor.windowManager.windows.pop();
		}
		window.close();
		t.doUnregisterDialog();
		tinymce  = t.editor = t.params = t.dom = t.dom.doc = null;
	}

	if (tinymce.isOpera) {
		t.getWin().setTimeout(close, 0);
	} else {
		close();
	}
},

// onInit register Dialog we-popup-managment
tinyMCEPopup.onInit.add(function () {
	var t = this;
	var id = '';

	window.addEventListener('unload', tinyMCEPopup.doUnregisterDialog, false);

	try {
		id = t.document.body.id ? t.document.body.id : '';
	} catch (err) {
	}

	var action = "register";
	var dialogType = 'dialog';
	switch (id) {
		case("weDocSelecterInt"):
		case("colorpicker"):
			dialogType = 'secondaryDialog';
			break;
		/*
		case("weFullscreenDialog"):
			dialogType = 'dialog';
			break;
		*/
		default:
			dialogType = 'dialog';
			action = "register";
	}

	tinyMCEPopup.weDialogType = dialogType;
	tinyMCEPopup.weEditor = tinyMCEPopup.editor;

	/*
	 try{
	 action = t.weSelectorWindow ? "registerFileSelector" : action;
	 } catch(err){}

	 if(action == "registerFileSelector" && typeof(top.opener.isWeDialog) != "undefined"){
	 try{
	 top.opener.top.opener.tinyMCECallRegisterDialog(t,"registerSecondaryDialog");
	 } catch(err){}
	 return
	 }
	 */
	try {
		top.opener.top.console.log('try to register', tinyMCEPopup.weEditor, t, action, dialogType);
		WE().layout.we_tinyMCE.functions.registerDialog(tinyMCEPopup.weEditor, t, action, dialogType);
		return;
	} catch (err) {
	}

	return;
});

tinyMCEPopup.doUnregisterDialog = function(e){
	try {
		WE().layout.we_tinyMCE.functions.registerDialog(tinyMCEPopup.weEditor, window, 'unregister', tinyMCEPopup.weDialogType);
		return;
	} catch (err) {}
};


function WE() {
	if(top.opener){
		return top.opener.webEdition ? top.opener.webEdition : (top.opener.WE ? top.opener.WE() : null);
	}
}
