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

//(function(){tinymce.create('tinymce.plugins.WeabbrPlugin',{init:function(e,f){e.addCommand('mceWeabbr',function(){var a=e.selection;if(a.isCollapsed()&&(!e.dom.getParent(a.getNode(),'ABBR')))return;e.windowManager.open({file:f+'/../../../../we_tinymce/abbrDialog.php?we_dialog_args[editor]=tinyMce',popup_css:false,width:460+parseInt(e.getLang('weabbr.delta_width',0)),height:200+parseInt(e.getLang('weabbr.delta_height',0)),inline:1},{plugin_url:f,some_custom_arg:'custom arg'})});e.addButton('weabbr',{title:'we.tt_weabbr',cmd:'mceWeabbr'});e.onNodeChange.add(function(a,b,n,c){var d=n.nodeName=='ABBR';b.setDisabled('weabbr',c&&!d);b.setActive('weabbr',d)})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Weabbr plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weabbr',version:"1.0"}}});tinymce.PluginManager.add('weabbr',tinymce.plugins.WeabbrPlugin)})();
(function () {
	tinymce.create('tinymce.plugins.WeabbrPlugin', {
		init: function (e, f) {
			e.addCommand('mceWeabbr', function () {
				var a = e.selection;
				if (a.isCollapsed() && (!e.dom.getParent(a.getNode(), 'ABBR'))){
					return;
				}
				e.windowManager.open({
					file: '/webEdition/dynamic/wysiwyg/abbrDialog.php?we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + e.getParam('weIsFrontend'),
					popup_css: false,
					width: 460 + parseInt(e.getLang('weabbr.delta_width', 0)),
					height: 200 + parseInt(e.getLang('weabbr.delta_height', 0)),
					inline: 1
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});

			e.addButton('weabbr', {
				title: 'xhtmlxtras.abbr_desc',
				'class' : 'mce_' + e.settings.wePluginClasses.weabbr,
				cmd: 'mceWeabbr'
			});

			// ed.onNodeChange.add(function(ed, cm, n) {} => moved to weutil
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