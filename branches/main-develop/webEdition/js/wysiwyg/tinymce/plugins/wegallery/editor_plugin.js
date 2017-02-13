/* global tinymce */
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
'use strict';

//(function(){tinymce.create('tinymce.plugins.WeabbrPlugin',{init:function(e,f){e.addCommand('mceWeabbr',function(){var a=e.selection;if(a.isCollapsed()&&(!e.dom.getParent(a.getNode(),'ABBR')))return;e.windowManager.open({file:f+'/../../../../we_tinymce/abbrDialog.php?we_dialog_args[editor]=tinyMce',popup_css:false,width:460+parseInt(e.getLang('weabbr.delta_width',0)),height:200+parseInt(e.getLang('weabbr.delta_height',0)),inline:1},{plugin_url:f,some_custom_arg:'custom arg'})});e.addButton('weabbr',{title:'we.tt_weabbr',cmd:'mceWeabbr'});e.onNodeChange.add(function(a,b,n,c){var d=n.nodeName=='ABBR';b.setDisabled('weabbr',c&&!d);b.setActive('weabbr',d)})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Weabbr plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weabbr',version:"1.0"}}});tinymce.PluginManager.add('weabbr',tinymce.plugins.WeabbrPlugin)})();
(function () {
	tinymce.create('tinymce.plugins.WegalleryPlugin', {
		init: function (e, f) {
			var templates = e.getParam('weGalleryTemplates');
			e.addButton('wegallery', {
				title: 'we.tt_wegallery',
				'class' : 'mce_wegallery mce_we_fa',
				cmd: 'mceWegallery'
			});

			e.addCommand('mceWegallery', function () {
				e.isWeGalleryInitialized = false;
				var c = e.selection, weid = 0, wetmpl = '';
				if(!templates){
					return;
				}
				if (e.dom.getParent(c.getNode(), 'WE-GALLERY') !== null) {
					weid = e.dom.getParent(c.getNode(), 'WE-GALLERY').id;
					wetmpl = e.dom.getParent(c.getNode(), 'WE-GALLERY').getAttribute('tmpl');
				}

				e.windowManager.open({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_gallery&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + e.getParam('weIsFrontend') + '&we_dialog_args[collid]=' + weid + '&we_dialog_args[tmpl]=' + wetmpl + '&we_dialog_args[templateIDs]=' + templates,
					popup_css: false,
					width: 500 + parseInt(e.getLang('weabbr.delta_width', 0)),
					height: 250 + parseInt(e.getLang('weabbr.delta_height', 0)),
					inline: 1
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});

			if(templates){
				e.onNodeChange.add(function(e, cm, n) {
					if(templates){
						if(!e.selection.isCollapsed()){
							cm.setDisabled('wegallery', n.nodeName !== 'WE-GALLERY');
							cm.setActive('wegallery', n.nodeName === 'WE-GALLERY');
						} else {
							cm.setDisabled('wegallery', 0);
							cm.setActive('wegallery', n.nodeName === 'WE-GALLERY');
						}
					} else {
						cm.setDisabled('wegallery', 1);
					}
				});
			} else {

			}
		},

		createControl: function (n, a) {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Wegallery plugin',
				author: 'webedtition.irg',
				authorurl: 'http://webedtition.org',
				infourl: 'http://webedtition.org',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wegallery', tinymce.plugins.WegalleryPlugin);
})();