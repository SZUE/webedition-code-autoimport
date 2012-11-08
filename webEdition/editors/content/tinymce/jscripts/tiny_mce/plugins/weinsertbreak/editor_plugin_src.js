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
	//tinymce.PluginManager.requireLangPack('weinsertbreak');

	tinymce.create('tinymce.plugins.WeinsertbreakPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
			 
		init : function(ed, url) {
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceWeinsertbreak');
			ed.addCommand('mceWeinsertbreak', function() {
				var content = '<br />';
				ed.execCommand('mceInsertContent', false, content);	//TODO: trim before and after br
																	//Use tiny-methods to insert HTML			
			});

			// Register weinsertbreak button
			ed.addButton('weinsertbreak', {
				title : tinyMceGL.weinsertbreak.tooltip,
				cmd : 'mceWeinsertbreak',
				image : url + '/img/br.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				//cm.setActive('visualaid', n.nodeName == 'IMG');
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
				longname : 'Weinsertbreak plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weinsertbreak',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('weinsertbreak', tinymce.plugins.WeinsertbreakPlugin);
})();