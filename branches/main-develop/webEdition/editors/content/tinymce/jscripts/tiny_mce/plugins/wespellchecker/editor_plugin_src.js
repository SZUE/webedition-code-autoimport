(function() {

	tinymce.create('tinymce.plugins.WespellcheckerPlugin', {

		init : function(ed, url) {
			ed.addCommand('mceWespellchecker', function() {

				var se = ed.selection;

				ed.windowManager.open({
					file : url + '/../../../../../wysiwyg/spellcheck.php?we_dialog_args[editor]=tinyMce&we_dialog_args[editname]=tinyMce',
					width : 500 + parseInt(ed.getLang('wespellchecker.delta_width', 0)),
					height : 490 + parseInt(ed.getLang('wespellchecker.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register wespellchecker button
			ed.addButton('wespellchecker', {
				title : tinyMceGL.wespellchecker.tooltip,
				cmd : 'mceWespellchecker',
				image : url + '/img/spellcheck.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n, co) {
				cm.setDisabled('wespellchecker', false);
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Wespellchecker plugin',
				author : 'webEdition e.V',
				authorurl : 'http://www.webedition.org',
				infourl : 'http://www.webedition.org',
				//version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wespellchecker', tinymce.plugins.WespellcheckerPlugin);
})();