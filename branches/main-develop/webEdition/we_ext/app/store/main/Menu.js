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

//TODO: Base this class on WE...MenuStore for reusing it in modules

Ext.define('WE.store.main.Menu', {
	extend: 'Ext.data.Store',
	model: 'WE.model.main.Menu',

	config: {
		menuBar : null
	},

	proxy: {
		type: 'ajax',
		url: 'we_cmd_ext.php?we_cmd[0]=load_menu',
		reader: {
			type: 'json',
			root: 'tb'
		}
	},

	listeners: {
		load: function(store,records,success,operation,opts) {
			var ownerMenu = store.menuBar, 
				c = 0,
				tmpItems = {
					'm_0' : ownerMenu
				};
				
			ownerMenu.removeMenus();

			store.each(function(record) {
				var item;
				if(record.data.parent == 'm_0'){
					item = Ext.create('Ext.button.Button',{
							text: record.data.name,
							arrowCls: '',//TODO: define class without arrow
							showEmptyMenu: true,
							margin: '0 0 0 6',
							padding: '3 10 3 10',
							cls: 'top_menu_item',
							menu: {
								xtype: menu
							}
					});

					ownerMenu.weMenus.push(item);
					tmpItems[record.data.parent].insert(c, tmpItems[record.data.menuId] = item);
					c++;
				} else {
					if(typeof tmpItems[record.data.parent] !== "undefined"){
						if(record.data.name != ''){
							item = Ext.create('Ext.menu.Item',{
								text: record.data.name,
								cmd: record.data.cmd
							});
						} else {
							item = Ext.create('Ext.menu.Separator', {});
						}
						var menu = (typeof tmpItems[record.data.parent].menu !== "undefined") ? 
							tmpItems[record.data.parent].menu :
								tmpItems[record.data.parent].menu = Ext.create('Ext.menu.Menu');
						menu.add(tmpItems[record.data.menuId] = item);
					}
				}
			});
			//add rest of tb
			if(ownerMenu.first){
				ownerMenu.addButtons();
				ownerMenu.first = 0;
			}
		}
	}
});