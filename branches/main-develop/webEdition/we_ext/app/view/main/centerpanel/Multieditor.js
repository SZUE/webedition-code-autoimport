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
 * @package    webEdition_EXT
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

Ext.define('WE.view.main.centerpanel.Multieditor', {
	extend: 'Ext.tab.Panel',
	requires: [
		'WE.view.main.centerpanel.multieditor.Editor',
		'Ext.ux.TabScrollerMenu',
		//'Ext.ux.BoxReorderer',
		//'Ext.ux.TabReorderer'
	],
	id: 'weMultieditor',
	alias: 'widget.main.centerpanel.multieditor',

	//TODO: use cls
	bodyStyle:{
		background: "url('/webEdition/images/backgrounds/blank_editor_bg.gif') repeat-x fixed right bottom #DDE7F1"
	},
	/*
	html: '<img class="blank_editor_logo" alt="logo" src="/webEdition/images/backgrounds/blank_editor.gif" style="bottom: 0; position: absolute; right: 0;">',
	*/
	/*
	locales: {
		title: 'multieditor1.title',
		custom: 'multieditor1.title'
	},
	*/

	plugins: [{
		ptype: 'tabscrollermenu',
		maxText  : 30,
		pageSize : 16
	} //Ext.create('Ext.ux.TabReorderer')
	],

	tabBar: {
		//plugins : Ext.create('Ext.ux.BoxReorderer', {})
	},

	items: [],

	addEditor: function(record){
		var me = this, ed;

		ed = Ext.create('WE.view.main.centerpanel.multieditor.Editor', {
			//src: '/webEdition/we_cmd.php?we_cmd[0]=load_editor&we_cmd[1]=1&we_transaction=' + record.data.id,
			title: record.data.text,
			id: record.data.id, // = transaction = multieditorStore.id = multieditor-tab id
			itemId: record.data.table + '_' + record.data.we_id, // = treeStore.id = multieditor-tab itemId
			we_id: record.data.we_id,
			table: record.data.table,
			iconCls: record.data.iconCls,
			rec: record //to serve other parts of view with reference to record
		});

		me.add(ed);
		me.setActiveTab(ed);
	},

	removeEditor: function(record){
		//
	},

	selectTabByTabID: function(table, id){
		this.setActiveTab(this.getComponent(table + '_' + id));
	},

	setActiveTabOnRemove: function(tab){
		var me = this, 
			activeTab = me.getActiveTab();

		if(me.getActiveTab() === tab){
			var index = me.items.findIndex('id', tab.id);
			var indexNew = index > 0 ? index-1 : 0;
			me.setActiveTab(indexNew);
		}
	}
});