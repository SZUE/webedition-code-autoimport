/* global tinymce */

/**
 * webEdition CMS
 *
 * $Rev: 13357 $
 * $Author: lukasimhof $
 * $Date: 2017-02-13 22:29:45 +0100 (Mo, 13 Feb 2017) $
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
'use strict';

//(function(){tinymce.create('tinymce.plugins.WeabbrPlugin',{init:function(e,f){e.addCommand('mceWeabbr',function(){var a=e.selection;if(a.isCollapsed()&&(!e.dom.getParent(a.getNode(),'ABBR')))return;e.windowManager.open({file:f+'/../../../../we_tinymce/abbrDialog.php?we_dialog_args[editor]=tinyMce',popup_css:false,width:460+parseInt(e.getLang('weabbr.delta_width',0)),height:200+parseInt(e.getLang('weabbr.delta_height',0)),inline:1},{plugin_url:f,some_custom_arg:'custom arg'})});e.addButton('weabbr',{title:'we.tt_weabbr',cmd:'mceWeabbr'});e.onNodeChange.add(function(a,b,n,c){var d=n.nodeName=='ABBR';b.setDisabled('weabbr',c&&!d);b.setActive('weabbr',d)})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Weabbr plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weabbr',version:"1.0"}}});tinymce.PluginManager.add('weabbr',tinymce.plugins.WeabbrPlugin)})();
(function () {
	tinymce.create('tinymce.plugins.WeabbrPlugin', {
		init: function (ed, f) {
			ed.addCommand('mceWeabbr', function () {
				var a = ed.selection;
				if (a.isCollapsed() && (!ed.dom.getParent(a.getNode(), 'ABBR'))){
					return;
				}
				ed.windowManager.openLegacyPlugin({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_abbr&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + ed.getParam('weIsFrontend'),
					popup_css: false,
					width: 460 + parseInt(ed.getLang('weabbr.delta_width', 0)),
					height: 200 + parseInt(ed.getLang('weabbr.delta_height', 0)),
					inline: false
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});

			ed.addButton('weabbr', {
				title: tinymce.i18n.data.de.we.tt_weabbr,
				text: 'ABK',
				cmd: 'mceWeabbr'
			});

			ed.addMenuItem('weabbr', {
				text: tinymce.i18n.data.de.we.tt_weabbr,
				//icon: 'fa fa-image',
				context: 'xhtml',
				cmd: 'mceWeabbr',
				onPostRender: function() {
					var ctrl = this;
					ed.on('NodeChange', function(e) {
						ctrl.active(false);
						ctrl.disabled(ed.selection.isCollapsed());

						var n = e.element;
						if (n && n.nodeName) { // we check for lang spans recursive
							do {
								if(n.nodeName.toLowerCase() === 'abbr'){
									ctrl.active(true);
									ctrl.disabled(false);
									break;
								}
							} while ((n = n.parentNode));
						}
					});
				}
			});
		},

		createControl: function (n, a) {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Weabbr plugin',
				author: 'Some author',
				authorurl: 'http://tinymce.moxiecode.com',
				infourl: 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weabbr',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('weabbr', tinymce.plugins.WeabbrPlugin);
})();