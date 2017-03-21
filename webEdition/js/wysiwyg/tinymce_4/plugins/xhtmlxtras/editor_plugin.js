/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	tinymce.create('tinymce.plugins.XHTMLXtrasPlugin', {
		plugin : this,
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceCite', function() {
				ed.windowManager.openLegacyPlugin({
					file : url + '/cite.htm',
					width : 350 + parseInt(ed.getLang('xhtmlxtras.cite_delta_width', 0)),
					height : 250 + parseInt(ed.getLang('xhtmlxtras.cite_delta_height', 0)),
					inline : false
				}, {
					plugin_url : url
				});
			});

			ed.addCommand('mceDel', function() {
				ed.windowManager.openLegacyPlugin({
					file : url + '/del.htm',
					width : 340 + parseInt(ed.getLang('xhtmlxtras.del_delta_width', 0)),
					height : 310 + parseInt(ed.getLang('xhtmlxtras.del_delta_height', 0)),
					inline : false
				}, {
					plugin_url : url
				});
			});

			ed.addCommand('mceIns', function() {
				ed.windowManager.openLegacyPlugin({
					file : url + '/ins.htm',
					width : 340 + parseInt(ed.getLang('xhtmlxtras.ins_delta_width', 0)),
					height : 310 + parseInt(ed.getLang('xhtmlxtras.ins_delta_height', 0)),
					inline : false
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('cite', {
				title : 'xhtmlxtras.cite_desc',
				cmd : 'mceCite',
				text: 'ZIT',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'CITE');});}
			});
			ed.addButton('del', {
				title : 'xhtmlxtras.del_desc',
				cmd : 'mceDel',
				text: 'DEL',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'DEL');});}
			});
			ed.addButton('ins', {
				title : 'xhtmlxtras.ins_desc',
				cmd : 'mceIns',
				text : 'INS',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'INS');});}
			});

			// Register menu
			ed.addMenuItem('cite', {
				text: 'CITE',//tinymce.i18n.data.de.we.tt_weabbr,
				//icon: 'fa fa-image',
				context: 'xhtml',
				cmd: 'mceCite',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'CITE');});}
			});
			ed.addMenuItem('del', {
				text: 'DEL',//tinymce.i18n.data.de.we.tt_weabbr,
				//icon: 'fa fa-image',
				context: 'xhtml',
				cmd: 'mceDel',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'DEL');});}
			});
			ed.addMenuItem('ins', {
				text: 'INS',//tinymce.i18n.data.de.we.tt_weabbr,
				//icon: 'fa fa-image',
				context: 'xhtml',
				cmd: 'mceIns',
				onPostRender: function() {var ctrl = this; ed.on('NodeChange', function(e){xhtmlxtra_doOnNodeChange(ed, ctrl, e, 'INS');});}
			});
		},

		getInfo : function() {
			return {
				longname : 'XHTML Xtras Plugin',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/xhtmlxtras',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		},
		xhtmlxtra_doOnNodeChange2 : function(){
			top.console.log('reto');
		}
	});

	// Register plugin
	tinymce.PluginManager.add('xhtmlxtras', tinymce.plugins.XHTMLXtrasPlugin);
})();

function xhtmlxtra_doOnNodeChange(ed, ctrl, e, name){
		ctrl.active(false);
		ctrl.disabled(ed.selection.isCollapsed());

		var n = e.element;
		if (n && n.nodeName) { // we check for lang spans recursive
			do {
				if(n.nodeName.toLowerCase() === name.toLowerCase()){
					ctrl.active(true);
					ctrl.disabled(false);
					break;
				}
			} while ((n = n.parentNode));
		}
}