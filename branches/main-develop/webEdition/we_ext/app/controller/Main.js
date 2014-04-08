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

Ext.define('WE.controller.Main', {
	extend: 'Ext.app.Controller',
	config: {
		stores: [
			//'main.Tree',
			'main.Files'
		],
		models: [
			'main.Files'
		],
		views: [
			'main.centerpanel.Multieditor',
			'main.centerpanel.MainGrid',
			'main.centerpanel.toolbar.Properties'
			//'main.TreeBar'
		],

		refs: [{
			ref: 'maingrid',
			selector: '[xtype="main.centerpanel.maingrid"]'
		}, {
			ref: 'multieditor',
			selector: '[xtype="main.centerpanel.multieditor"]'
		}, {
			ref: 'properties',
			selector: '[xtype="main.centerpanel.toolbar.properties"]'
		}, {
			ref: 'treebar',
			selector: '[xtype="main.treebar"]'
		}],

		storeFiles: null
		//clickTimeout: null,
		//lastIdClicked: 0, //TODO: move this to treeStore
		//lastTreeTabActive: null, //TODO: move this to vtabs
		//lastMenuCmd: ''
	},

	init: function() {
		//Get some references
		this.storeFiles = Ext.StoreManager.lookup('filestore');
		this.controllerBridge = WE.app.getController('Bridge');
		
		Ext.EventManager.on(window, 'beforeunload', function() {
			top.doUnload();
		});

		//Add listeners to named components
		this.control({
			'[xtype="main.centerpanel.maingrid"]': {
				//logSelectedFile: function(grid) {
				//	var selectedRecordsArray = this.getMaingrid().getSelectionModel().getSelection();
				//},
				removeItemAt: function(obj) {
					obj.grid.getStore().removeAt(obj.rowIndex);
				}
			},

			'[xtype="main.centerpanel.multieditor"]': {
				tabchange: function(tabPanel, newCard, oldCard, eOpts) {
					var treetab = this.getTreebar().getComponent('vtab_' + newCard.table);
					//TODO: why does getComponent not work?
					treetab = Ext.getCmp('vtab_' + newCard.table);

					//TODO: make functions to load data to all editors (e.g on toolbar)
					treetab.getTree().selectNode(newCard.table, newCard.we_id);
					this.getMaingrid().selectItem(newCard.rec);
					this.getProperties().setCt(newCard.rec.data.fullCt);
					this.getProperties().loadRecord(newCard.rec);
					//call we open in multieditor do select tab
					//TODO: move this call to Bridge!
					if(newCard.we_id !== 0){
						this.controllerBridge.openInMultiEditor(newCard.table, newCard.we_id);
					}
				},
				beforeremove: function(tabPanel, tab, eOpts ){
					tabPanel.setActiveTabOnRemove(tab);
				},
				remove: function(tabPanel, tab, eOpts){
					this.storeFiles.remove(this.storeFiles.getById(tab.id));
				}
			}

		});
	},
	onLaunch: function() {
		//
	},

	//move mainStore fns to class mainstore
	//http://stackoverflow.com/questions/7977303/read-extjs-message-from-ajax-store
	mainStoreLoad: function(table, id, ct, transaction) {
		var me = this;
			//var item = me.storeFiles.getById(table + '_' + id);
			//if (!items || id === 0 || id === '0') {
				me.storeFiles.load({
					addRecords: true,
					params: {
						'we_cmd[1]': table,
						'we_cmd[2]': id,
						'we_cmd[3]': ct,
						'we_transaction': transaction
					},
					callback: function(records, operation, success) {
						if(success){
							me.getMultieditor().addEditor(records[0]);
							me.getMaingrid().selectItem(records[0]);
							me.getProperties().loadRecord(records[0]);
						}
					}
				});
			//}
	},

	selectorProcessResponse: function(obj){
		var selObj = WE.classes.components.form.field.BaseSelector.selInstances[obj.selectorId];
		selObj.instance.processResponse(obj, selObj.recordId);
	},
				
	doUpdateTab: function(record, action){
		this.getMultieditor().getComponent(record.data.table + '_' + record.data.we_id).updateEditor(record, true, true);
	}
});