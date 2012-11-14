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
	//tinymce.PluginManager.requireLangPack('wevisualaid');

	tinymce.create('tinymce.plugins.WevisualaidPlugin', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		 
		hasWeVisual : false,
		 
		init : function(ed, url) {
			
			var t = this;

			function toggleBorders(t,ed) {
				
				//var e = ed;
				var e = t.getBody();// FIXME: get editor directly
				var s = t.settings;
				var each = tinymce.each;
				var dom = tinymce.DOM;

				each(dom.select('a,table,acronym,span', e), function(e) {
					var v;

					switch (e.nodeName) {
						case 'TABLE':
							v = dom.getAttrib(e, 'border');

							if (!v || v == '0') {
								if (ed.hasVisual){
									dom.addClass(e, s.visual_table_class);
								} else{
									dom.removeClass(e, s.visual_table_class);
								}
							}
							return;

						case 'A':
							v = dom.getAttrib(e, 'name');

							if (v) {
								if (ed.hasVisual){
									dom.addClass(e, 'mceItemAnchor');
								} else{
									dom.removeClass(e, 'mceItemAnchor');
								}
							}
							return;
							
						case 'ACRONYM':
								if (ed.hasVisual){
									dom.addClass(e, 'mceItemWeAcronym');
								} else{
									dom.removeClass(e, 'mceItemWeAcronym');
								}
							return;

						case 'ABBR':
								if (ed.hasVisual){
									dom.addClass(e, 'mceItemWeAbbr');
								} else{
									dom.removeClass(e, 'mceItemWeAbbr');
								}
							return;

						case 'SPAN':
								v = dom.getAttrib(e, 'lang');
								if(v){
									if (ed.hasVisual){
										dom.addClass(e, 'mceItemWeLang');
									} else{
										dom.removeClass(e, 'mceItemWeLang');
									}
								}
							return;
					}

				});
			}
			
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceWevisualaid');
			ed.addCommand('mceWevisualaid', function() {
				ed.hasVisual = !ed.hasVisual;
				toggleBorders(this,ed);
			});

			// Register wevisualaid button
			ed.addButton('wevisualaid', {
				title : tinyMceGL.wevisualaid.tooltip,
				cmd : 'mceWevisualaid',
				image : url + '/img/visibleborders.gif'
			});

			// Add a node change handler, selects the button in the UI when a image is selected
			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('wevisualaid', ed.hasVisual);
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
				longname : 'Wevisualaid plugin',
				author : 'Some author',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/wevisualaid',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('wevisualaid', tinymce.plugins.WevisualaidPlugin);
})();