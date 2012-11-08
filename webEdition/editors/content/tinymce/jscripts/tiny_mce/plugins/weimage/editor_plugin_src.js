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
	tinymce.create('tinymce.plugins.WeimagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceWeimage', function() {
				ed.isWeDataInitialized=false;
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class', '').indexOf('mceItem') != -1){
					return;
				}

				ed.windowManager.open({
					file : url + '/../../../../../wysiwyg/imageDialog.php?we_dialog_args[editor]=tinyMce',
					width : 600 + parseInt(ed.getLang('advimage.delta_width', 0)),
					height : 610 + parseInt(ed.getLang('advimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('weimage', {
				title : tinyMceGL.weinsertbreak.tooltip,
				cmd : 'mceWeimage',
				image: url + "/img/weimage.gif"
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n, co) {
				//cm.setDisabled('weabbr', co && !(n.nodeName == 'SPAN' && n.getAttribute('lang')));
				var active = n.nodeName == 'IMG';
				cm.setActive('weimage', active);
			});
		},

		getInfo : function() {
			return {
				longname : 'webEdition Image-Dialog',
				author : 'webEdition e.V',
				authorurl : 'http://www.webedition.org',
				infourl : 'http://www.webedition.org',
				//version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('weimage', tinymce.plugins.WeimagePlugin);
})();