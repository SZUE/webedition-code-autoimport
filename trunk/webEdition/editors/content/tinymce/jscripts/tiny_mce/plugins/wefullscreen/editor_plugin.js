(function() {

	tinymce.create('tinymce.plugins.WefullscreenPlugin', {

		init : function(ed, url) {
			ed.addCommand('mceWefullscreen', function() {
				var se = ed.selection;
				ed.windowManager.open({

					file : url + '/../../../../../wysiwyg/fullscreenEditDialog.php?we_dialog_args[editor]=tinyMce' +
						'&we_dialog_args[editname]=tinyMce' +
						'&we_dialog_args[outsideWE]='+ wefullscreenVars["outsideWE"] +
						'&we_dialog_args[xml]='+ wefullscreenVars["xml"] +
						'&we_dialog_args[removeFirstParagraph]='+ wefullscreenVars["removeFirstParagraph"] +
						'&we_dialog_args[baseHref]='+ wefullscreenVars["baseHref"] +
						'&we_dialog_args[charset]='+ wefullscreenVars["charset"] +
						'&we_dialog_args[cssClasses]='+ wefullscreenVars["cssClasses"] +
						'&we_dialog_args[bgcolor]='+ wefullscreenVars["bgcolor"] +
						'&we_dialog_args[language]='+ wefullscreenVars["language"] +
						'&we_dialog_args[screenWidth]='+ wefullscreenVars["screenWidth"] +
						'&we_dialog_args[screenHeight]='+ wefullscreenVars["screenHeight"] +
						'&we_dialog_args[className]='+ wefullscreenVars["className"] +
						'&we_dialog_args[fontnames]='+ wefullscreenVars["fontnames"] +
						'&we_dialog_args[propString]='+ wefullscreenVars["propString"],

					width : screen.availWidth-20,
					height : screen.availHeight - 70,
					inline : 1
				}, {

					plugin_url : url, // Plugin absolute URL
				});

				//ed.execCommand("mceSetContent", false, "das ist der text");

			});

			// Register wefullscreen button
			ed.addButton('wefullscreen', {
				title : tinyMceGL.wefullscreen.tooltip,
				cmd : 'mceWefullscreen',
				image : url + '/img/fullscreen.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('wefullscreen', false);
			});
		},



		/**
		 * Returns information about the plugin as a name/value array.

		 * The current keys are longname, author, authorurl, infourl and version.
		 * @return {Object} Name/value array containing information about the plugin.
		 */

		getInfo : function() {
			return {
				longname : 'Wefullscreen plugin',
				author : 'webEdition e.V',
				authorurl : 'http://www.webedition.org',
				infourl : 'http://www.webedition.org'
				//version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wefullscreen', tinymce.plugins.WefullscreenPlugin);
})();