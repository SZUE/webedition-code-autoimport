/**
 * webEdition CMS
 *
 * $Rev: 7210 $
 * $Author: lukasimhof $
 * $Date: 2013-12-28 14:19:02 +0100 (Sa, 28 Dez 2013) $
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
 * @package    webEdition_EXT
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

//TODO: Base this class on WE...BasicMenuBar for reusing it in modules

Ext.define('WE.view.main.MenuBar', {
	extend: 'Ext.toolbar.Toolbar',
	requires: [
		'WE.store.main.Menu'
	],

	id: 'weMenu',
	alias: 'widget.main.menubar',
	height: 38,
	cls: 'top_menu',
	items: [],
	menuStore:{},

	listeners: {
		beforerender: function(){
			this.menuStore = new WE.store.main.Menu({
				menuBar: this
			});
			this.menuStore.load();
		}
	},
			
	
			
	weMenus: [],
	first: 1,

	addButtons: function(){
		var me = Ext.getCmp('weMenu');//not really elegant...: try doing it by injecting this as scope
		this.add({ 
			xtype: 'button', 
			margin: '0 0 0 15',
			padding: '0 3 4 2',
			iconCls: 'top_menu_icn_home',
			cmd: 'editor_home',
			tooltip: 'hello and more text so it will be too long'
		});
		this.add({ 
			xtype: 'button', 
			margin: '0 0 0 1',
			padding: '0 3 4 3',
			iconCls: 'top_menu_icn_reload',
			cmd: 'editor_reload'
		});
		this.add({ 
			xtype: 'button', 
			margin: 0,
			padding: '0 3 4 3',
			iconCls: 'top_menu_icn_back',
			cmd: 'editor_back'
		});
		this.add({ 
			xtype: 'button', 
			margin: 0,
			padding: '0 3 4 3',
			iconCls: 'top_menu_icn_next',
			cmd: 'editor_next'
		});
	},

	removeMenus: function(){
		for(var i = 0; i < this.weMenus.length; i++) {
			this.remove(this.weMenus[i]);
		}
		this.weMenus = [];
	},

	reloadMenus: function(){
		this.menuStore.load();
	},

	collapseAll: function(){
		this.items.each(function(item){
			if(item.menu){
				item.menu.hide();
				item.blur();
			}
		});
	}
});