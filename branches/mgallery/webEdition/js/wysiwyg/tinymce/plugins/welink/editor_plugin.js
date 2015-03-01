(function () {
	tinymce.create("tinymce.plugins.WelinkPlugin", {
		init: function (a, b) {
			this.editor = a;

			a.addCommand("mceWelink", function () {
				a.isWeLinkInitialized = false;
				var c = a.selection;
				if (c.isCollapsed() && !a.dom.getParent(c.getNode(), "A")) {
					return;
				}
				var wehref = "";
				if (a.dom.getParent(c.getNode(), 'A') !== null) {
					wehref = a.dom.getParent(c.getNode(), 'A').href;
				}
				a.windowManager.open({
					file: "/webEdition/dynamic/wysiwyg/linkDialog.php?we_dialog_args[editor]=tinyMce&we_dialog_args[href]=" + encodeURIComponent(wehref) + "&we_dialog_args[cssclasses]=" + a.getParam('weClassNames_urlEncoded') + "&we_dialog_args[isFrontend]=" + a.getParam('weIsFrontend'),
					width: 600 + parseInt(a.getLang("welink.delta_width", 0)),
					popup_css: false,
					height: 600 + parseInt(a.getLang("welink.delta_height", 0)),
					inline: 1
					}, {
					plugin_url: b
				});
			});

			a.addButton("welink", {
				title: 'we.tt_welink',
				'class' : 'mce_link',
				cmd: "mceWelink"
			});

			a.addShortcut("ctrl+k", "welink.welink_desc", "mceWelink");
		},

		getInfo: function () {
			return {
				longname: "webEdition Link Plugin",
				author: "webEdition e.V.",
				authorurl: "http://http://www.webedition.org",
				infourl: "http://www.webedition.org",
				version: tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});
	tinymce.PluginManager.add("welink", tinymce.plugins.WelinkPlugin);
})();