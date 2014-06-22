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

Ext.define('WE.view.main.treebar.TreeTab', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.main.treebar.treetab',
	requires: [
		'WE.view.main.treebar.IncDelete',
		'WE.view.main.treebar.TreeMain'
	],

	border: true,
	layout: 'border',
	table: '',
	tblConst: '',
	id: '',
	deleteDialog: null,
	tree: null,

	initComponent: function() {
		Ext.apply(this, {
			items: [{
				xtype: 'main.treebar.incdelete',
				itemId: 'deleteDialog',
				region: 'north',
				height: 150,
				border: true,
				hidden: true
			},{
				xtype: 'main.treebar.treemain',
				itemId: 'tree',
				region: 'center',
				id: 'tree_' + this.table,
				table: this.table,
				selModel: {mode: 'MULTI'},
				store: Ext.create('WE.store.main.Tree',{
					table: this.table
				})
			}],
			dockedItems: [{
				xtype: 'toolbar',
				cls: 'we_editor_footer',
				dock: 'bottom',
				height: 40,
				items: [{
					xtype: 'button',
					text: 'Button' + this.table
				}]
			}]
		});
		this.callParent();
	},

	getDeletedialog: function(){
		return this.getComponent('deleteDialog');
	},

	getTree: function(){
		return this.getComponent('tree');
	},

	setDeleteMode: function(deletemode){
		var me = this, vtabs = this.up('[xtype="main.treebar"]');
		if(deletemode){
			me.getTree().getStore().getProxy().extraParams = {'we_cmd[7]': true};
			me.getDeletedialog().show();
			if(me !== vtabs.getActiveTab()){
				vtabs.setActiveTab(me);
			}
			vtabs.setWidth(400);
			me.getTree().setSelectedItemCls('tree_noselection_selected'); 
			me.getTree().getStore().setAllCheckboxes(false);
			
			/* version b: doing reload of tree
			me.getTree().getStore().getProxy().extraParams = {'we_cmd[7]': true};
			me.getTree().setSelectedItemCls('tree_noselection_selected');
			me.getDeletedialog().show();
			vtabs.setWidth(400);

			if(me == vtabs.getActiveTab()){
				me.getTree().getStore().load();
			} else {
				vtabs.setActiveTab(me);
			}
			*/
		} else {
			me.getTree().getStore().getProxy().extraParams = {};
			me.getTree().resetSelectedItemCls();
			me.getDeletedialog().hide();
			me.getTree().getStore().setAllCheckboxes(null);

			/* version b: doing reload of tree
			me.getTree().getStore().getProxy().extraParams = {};
			me.getTree().resetSelectedItemCls();
			me.getDeletedialog().hide();
			if(me == vtabs.getActiveTab()){
				me.getTree().getStore().load();
			}
			*/
		}
	}
});