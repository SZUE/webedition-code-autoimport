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

//(function(){tinymce.create('tinymce.plugins.WeacronymPlugin',{init:function(e,f){e.addCommand('mceWeacronym',function(){var a=e.selection;if(a.isCollapsed()&&(!e.dom.getParent(a.getNode(),'ACRONYM')))return;e.windowManager.open({file:f+'/../../../../we_tinymce/acronymDialog.php?we_dialog_args[editor]=tinyMce',popup_css:false,width:460+parseInt(e.getLang('weacronym.delta_width',0)),height:200+parseInt(e.getLang('weacronym.delta_height',0)),inline:1},{plugin_url:f,some_custom_arg:'custom arg'})});e.addButton('weacronym',{title:'we.tt_weacronym',cmd:'mceWeacronym'});e.onNodeChange.add(function(a,b,n,c){var d=n.nodeName=='ACRONYM';b.setDisabled('weacronym',c&&!d);b.setActive('weacronym',d)})},createControl:function(n,a){return null},getInfo:function(){return{longname:'Weacronym plugin',author:'Some author',authorurl:'http://tinymce.moxiecode.com',infourl:'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/weacronym',version:"1.0"}}});tinymce.PluginManager.add('weacronym',tinymce.plugins.WeacronymPlugin)})();

(function () {
	tinymce.create('tinymce.plugins.WeacronymPlugin', {
		init: function (e, f) {
			e.addCommand('mceWeacronym', function () {
				var a = e.selection;
				if (a.isCollapsed() && (!e.dom.getParent(a.getNode(), 'ACRONYM'))){
					return;
				}
				e.windowManager.open({
					file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_acronym&we_dialog_args[editor]=tinyMce&we_dialog_args[isFrontend]=' + e.getParam('weIsFrontend'),
					popup_css: false,
					width: 460 + parseInt(e.getLang('weacronym.delta_width', 0)),
					height: 200 + parseInt(e.getLang('weacronym.delta_height', 0)),
					inline: 1
					}, {
					plugin_url: f,
					some_custom_arg: 'custom arg'
				});
			});

			e.addButton('weacronym', {
				'class' : 'mce_' + e.settings.wePluginClasses.weacronym,
				title: 'xhtmlxtras.acronym_desc',
				cmd: 'mceWeacronym'
			});

			// ed.onNodeChange.add(function(ed, cm, n) {} => moved to weutil
		},

		createControl: function (n, a) {
			return null;
		},

		getInfo: function () {
			return {
				longname: 'Weacronym Plugin',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('weacronym', tinymce.plugins.WeacronymPlugin);
})();