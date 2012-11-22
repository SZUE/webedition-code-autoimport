/**
 * webEdition CMS
 *
 * $Rev: 5016 $
 * $Author: lukasimhof $
 * $Date: 2012-10-25 11:53:14 +0200 (Do, 25 Okt 2012) $
 *
 * This source is part of webEdition CMS. webEdition CMS is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionCMS/License.txt
 *
 * @category   webEdition
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

(function() {
	// Load plugin specific language pack
	//tinymce.PluginManager.requireLangPack('welang');

	tinymce.create('tinymce.plugins.WelangPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceWelang');
			ed.addCommand('mceWelang', function() {

				var se = ed.selection;

				// No selection and not span with attribute lang
				if (se.isCollapsed() && (!ed.dom.getParent(se.getNode(), 'SPAN')))

					return;
				
				ed.windowManager.open({
					file : url + '/../../../../../wysiwyg/langDialog.php?we_dialog_args[editor]=tinyMce',
					width : 460 + parseInt(ed.getLang('welang.delta_width', 0)),
					height : 160 + parseInt(ed.getLang('welang.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});

			// Register welang button
			ed.addButton('welang', {
				title : tinyMceGL.welang.tooltip,
				cmd : 'mceWelang',
				image : url + '/img/lang.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n, co) {
				//cm.setDisabled('welang', co && !(n.nodeName == 'SPAN' && n.getAttribute('lang')));
				var active = n.nodeName == 'SPAN' && n.getAttribute('lang');
				cm.setDisabled('welang', co && !active);
				cm.setActive('welang', active);
			});
		},

		/**
		 * Creates control instances based in the incomming name. This method is normally not
		 * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
		 * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
		 * method can be used to create those.
		 *
		 * @param {String} n Name of the control to create.
		 * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
		 * @return {tinymce.ui.Control} New control instance or null if no control was created.
		 */
		createControl : function(n, cm) {
			return null;
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Welang plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/welang',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('welang', tinymce.plugins.WelangPlugin);
})();