/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
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

/**
 * This source is based on tinyMCE-plugin "contextmenu":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */

(function() {
	tinymce.create('tinymce.plugins.WeAdaptBoldPlugin', {
		init : function(ed) {
			// Register commands
			this.editor = ed;

			// Register buttons
			ed.addButton('weadaptbold', {
				title : 'advanced.bold_desc',
				'class' : 'mce_' + ed.settings.wePluginClasses.weadaptbold,
				cmd : 'bold'
			});
			// ed.onNodeChange.add(function(ed, cm, n) {} => moved to weutil
		},

		getInfo : function() {
			return {
				longname : 'We Adapter Bold',
				author : 'wededition e.V.',
				authorurl : 'http://www.webedition.org',
				infourl : 'http://www.webedition.org',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('weadaptbold', tinymce.plugins.WeAdaptBoldPlugin);
})();