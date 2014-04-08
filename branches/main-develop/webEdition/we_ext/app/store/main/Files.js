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

Ext.define('WE.store.main.Files', {
	extend: 'Ext.data.Store',
	storeId: 'filestore',
	id: 'files',
	model: 'WE.model.main.Files',
	autoLoad: false,
	autoSync: false, //sync will call we_save!
	table: '',
	proxy: {
			type: 'ajax',
			url: 'we_cmd_ext.php?we_cmd[0]=edit_document',//default
			api: {
				//IMPORTANT: we use update to trigger we_save with ext resetting dirty state of model fields on success
				update: 'we_cmd_ext.php?we_cmd[0]=save_document',
				read: 'we_cmd_ext.php?we_cmd[0]=edit_document'
				//destroy: 'app.php/users/destroy'
				//create: 'we_cmd_ext.php?we_cmd[0]=new_document',
			},
			actionMethods :{
				read: 'GET',
				//create: 'POST',
				update: 'POST'
			},
			reader: {
				type: 'json',
				root: 'file'
			}
	},

	listeners: {
		//IMPORTANT: fired when editing models in store
		update : function(store, record, operation, modifiedFieldNames, eOpts) {
			//we do not use store.sync: just write to session and do preserve dirtx state of modified model fields!
			//store.doWriteToSession(record, modifiedFieldNames);
		}
	},

	doWriteToSession: function(record, modifiedFieldNames){
		var fields = {};
		Ext.Array.each(modifiedFieldNames, function(field) {
			fields[field] = record.data[field];
		});

		params = {
			we_transaction: record.data.id,
			data: Ext.encode(fields)
		},

		Ext.Ajax.request({
			url: 'we_cmd_ext.php?we_cmd[0]=save_to_session',
			params: params,
			success: this.onSavedToSession,
			failure: function() {},
			scope: this
		});
	},

	onSavedToSession: function(){
		//reload editomode and preview from server
		//later: get editmode and preview from response and just reload inside gui
	},

	//IMPORTANT: fired when store's proxy get sucess after updating records on server
	onUpdateRecords: function(records, operation, success) {
		if(success){
			WE.app.getMainController().doUpdateTab(records[0], operation.action);
			WE.app.getTreeController().doUpdateNode(records[0], operation.action);
		}
	},

	/* fn will is called after successfully deleting documents via ajax */
	doRemoveDocumentsFromGui: function(table, ids){
		var me = this;

		Ext.Array.each(ids, function(we_id, index, me) {
			if(we_id){
				//close tab when open: onclose record will be removed from filestore
				if(tab = Ext.getCmp('weMultieditor').getComponent(table + '_' + we_id)){
					Ext.getCmp('weMultieditor').remove(tab);
				}

				//remove treenode
				try{
					var node = WE.store.main.Tree.getTreeByTable(table).getNodeById(we_id);
					node.remove(true);
				} catch(e){
					//TODO: implement WE error handling!
					Ext.Error.raise('node to remove not found');
				}
			}
		});
	}
});