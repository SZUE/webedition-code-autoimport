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

Ext.define('WE.view.main.TreeBar', {
	extend: 'WE.classes.components.VerticalTabpanel',
	alias: 'widget.main.treebar',

	requires: [
		'WE.view.main.treebar.TreeTab'
	],

	initComponent: function() {
		var me = this;
		me.callParent(arguments);

		Ext.Ajax.request({
			url: 'we_cmd_ext.php?we_cmd[0]=load_vtabs',
			success: this.onTabsLoad,
			failure: function() {},
			scope: this
		});
	},

	onTabsLoad: function(response) {
		var items = Ext.decode(response.responseText).vtabs;
		var me = this;

		Ext.each(items, function(item){
			var i = 0, tab = null;
			if(item.table){
				tab = {
					xtype: 'main.treebar.treetab',
					title : item.text,
					itemId : item.tblconst,
					id: 'vtab_' + item.table,
					table: item.table,
					tblConst: item.tblconst
				},
				me.add(tab);
				if(i===0){
					me.lastTabActive = tab;
					me.newTabActive = tab;
				}
			}
			i++;
		});
		me.setActiveTab(0);
		me.lastTabActive = null;
	}
});