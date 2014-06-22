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

Ext.define('WE.controller.Bridge', {
	extend: 'Ext.app.Controller',
	config: {
		stores: ['main.Tree', 'main.Files', 'main.FilesTmp'],
		models: ['main.Tree'],
		views: [
			'main.centerpanel.Multieditor',
			'main.centerpanel.MainGrid',
			'main.centerpanel.toolbar.Properties'
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
			ref: 'menubar',
			selector: '[xtype="main.menubar"]'
		}],

		storeFiles: null,
		lastIdClicked: 0
	},
	
	init: function() {
		//get some referencies
		this.storeFiles = Ext.StoreManager.lookup('filestore');
		this.storeFilesTmp = Ext.StoreManager.lookup('filestoreTmp');
		//this.multiEditor = Ext.getCmp('weMultieditor');//TODO: Does not work?
		this.controllerMain = WE.app.getController('Main'); 
		this.control({
		});
	},
			
	onLaunch: function() {
		//
	},

	/* ---------- add listeners to WE-iFrames when loaded -------------- */

	registerWeIframe: function(elem, isBodyTag) {
		try{
			if(isBodyTag){
				elem.addEventListener("click", top.WE.app.getController('Bridge').closeMainMenu, false);
			} else{
				elem.contentWindow.document.body.addEventListener("click", top.WE.app.getController('Bridge').closeMainMenu, false);
			}
		} catch(e){
			//
		}
	},

	/* called by we after GET: .../edit_document */
	loadFileToMainStore: function(transaction, table, id){
		this.controllerMain.mainStoreLoad(table, id, '', transaction);
	},
	
	
	/* ---------- SIMULATION OF Multieditor actions in EXT ------------- */
	/*
	 * after successfully saving/publishing/unpublishing document 
	 * we load the finalized entry from db to some temporary store
	 * 
	 * when loaded we synchronize form fields in properties editor and
	 * submit form: from this point the submit call is processed in ext
	 * 
	 */
	syncExtAfterEdAction: function(action, success, table, id, transaction) {
		var me = this;
		this.storeFilesTmp.removeAll();

		me.storeFilesTmp.load({
			addRecords: true,
			params: {
				'we_cmd[1]': table,
				'we_cmd[2]': id,
				'we_transaction': transaction
				
			},
			callback: function(records) {
				try{
					me.syncProperties(records[0]);
				} catch(e){
					//
				}
			}
		});
	}, 

	/*
	 * syncronize form fields in new properties editor and submit form
	 * important: files store does autosync:
	 * successfully processing request is simulated by simply returning success = true
	 */
	syncProperties: function(rec) {
		var me = this,
		properties = me.getProperties();//console.log(properties);

		if(properties.we_id === 0){
			//ACTUAL
			//TODO: what of these is obsolete?
			//=> we must have new multieditor tab-component and this one should 
			//be updatet (itemId, title) after "saving" (on success)
			properties.we_id = rec.data.we_id;
			properties.setTitle(rec.data.text);
			properties.itemId = rec.data.table + '_' + rec.data.we_id;
		}

		Ext.iterate(properties.getForm().getFields().items, function(field){
			field.setValue(rec.data[field.name]);
			//console.log(field);
			//console.log(rec.data[field.name]);
		});
		/*
		properties.getForm().getValues().each(function(field){
			field.setValue(rec.data[field.name]);
		});
		*/
		//Later: submit data from all tools and from edit and call save
		properties.submitToStore();
		this.storeFiles.sync();//to get success 
	},

	selectMultieditorTab: function(table, id){
		this.getMultieditor().selectTabByTabID(table, id);
	},

	multieditorCloseTab: function(transaction, table, id){
		var tab = Ext.getCmp(transaction);

		if(tab && this.getMultieditor()){
			this.getMultieditor().remove(tab);
		}
	},

	/* ---------- Call some WE-JS-Fns from within EXT  ------------- */

	openInMultiEditor: function(table, id, ct) {
		top.weEditorFrameController.openDocument(table, id, ct);
	},

	openInBrowser: function(id) {
		top.openBrowser(id);
	},

	
	/* EXT Fns used from within WE */
	/*
	 * When clicking on some WE-iFrame we must close Ext MainMenu
	 */
	closeMainMenu: function() {
		Ext.getCmp('weMenu').collapseAll();
	},

	deleteDocumentsTmp: function(table, ids){
		//Fn does, what delete-fn on filestore will do after successfully deleting docs via ajax
		this.storeFiles.doRemoveDocumentsFromGui(table, ids.split(','));
	}, 

	menuactionExt: function(cmd){
		switch(cmd){
			case 'delete_documents':
				Ext.getCmp('vtabs').getComponent('FILE_TABLE').setDeleteMode(true);
				break;
			case 'delete_templates': 
				//use refs to reference vtabs
				Ext.getCmp('vtabs').getComponent('TEMPLATES_TABLE').setDeleteMode(true);
				break;
			case 'delete_objectfile': 
				//use refs to reference vtabs
				Ext.getCmp('vtabs').getComponent('OBJECT_FILES_TABLE').setDeleteMode(true);
				break;
			case 'delete_object': 
				//use refs to reference vtabs
				Ext.getCmp('vtabs').getComponent('OBJECT_TABLE').setDeleteMode(true);
				break;
			default: 
				//
		}
	},

	doReloadMainMenu: function(){
		this.getMenubar().reloadMenus();
	}
});

