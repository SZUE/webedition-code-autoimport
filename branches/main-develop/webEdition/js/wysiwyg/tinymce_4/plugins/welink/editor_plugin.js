/* global tinymce */
'use strict';
(function () {
	tinymce.create("tinymce.plugins.WelinkPlugin", {
		init: function (a, b) {
			this.editor = a;

			a.addCommand("mceWelink", function (ui, dropID) {
				var dropID = dropID || 0;

				a.isWeLinkInitialized = false;
				var c = a.selection;
				if (c.isCollapsed() && !a.dom.getParent(c.getNode(), "A")) {
					return;
				}
				var wehref = "";
				if (a.dom.getParent(c.getNode(), 'A') !== null) {
					wehref = a.dom.getParent(c.getNode(), 'A').href;
					if(dropID){
						wehref = 'document:' + dropID; // replace existing paths by DnD!
					}
				} else if(dropID) {
					wehref = 'document:' + dropID;
				}

				a.windowManager.openLegacyPlugin({
					file: "/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_hyperlink&we_dialog_args[editor]=tinyMce&we_dialog_args[href]=" + encodeURIComponent(wehref) + "&we_dialog_args[cssclasses]=" + a.getParam('weClassNames_urlEncoded') + "&we_dialog_args[isFrontend]=" + a.getParam('weIsFrontend'),
					width: 600 + parseInt(a.getLang("welink.delta_width", 0)),
					popup_css: false,
					height: 600 + parseInt(a.getLang("welink.delta_height", 0)),
					inline: false
					}, {
					plugin_url: b
				});
			});

			a.addButton("welink", {
				icon: 'fa fa-link',
				title: tinymce.i18n.data.de.we.tt_welink,
				'class' : 'bullistk',
				cmd: "mceWelink"
			});

			a.addMenuItem('welink', {
				text: tinymce.i18n.data.de.we.tt_welink,
				icon: 'fa fa-link',
				context: 'insert',
				cmd: 'mceWelink',
				onPostRender: function() {
					var ctrl = this;
					a.on('NodeChange', function(e) {
						ctrl.active(false);
						ctrl.disabled(a.selection.isCollapsed());

						var n = e.element;
						if (n && n.nodeName) { // we check for lang spans recursive
							do {
								if(n.nodeName.toLowerCase() === 'a' && !n.getAttribute('name') && !n.getAttribute('id')){
									ctrl.active(true);
									ctrl.disabled(false);
									break;
								}
							} while ((n = n.parentNode));
						}
					});
				}
			});

			//a.addShortcut("ctrl+k", "welink.welink_desc", "mceWelink");
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