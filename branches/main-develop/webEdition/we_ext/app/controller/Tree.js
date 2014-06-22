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

Ext.define('WE.controller.Tree', {
	extend: 'Ext.app.Controller',
	config: {
		stores: [
			'main.Tree',
		],
		models: [
			'main.Tree'
		],
		views: [
			'main.TreeBar'
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

		storeFiles: null,
		clickTimeout: null,
		lastIdClicked: 0, //TODO: move this to treeStore
		lastTreeTabActive: null //TODO: move this to vtabs
	},

	init: function() {
		//Get some references
		this.storeFiles = Ext.StoreManager.lookup('filestore');
		this.controllerBridge = WE.app.getController('Bridge');

		//Add listeners to named components
		this.control({
			'[xtype="main.treebar.treemain"]': {
				//obsolete?
				itemcollapse: this.doCollapseTreeFolder,
				beforeitemexpand: this.doExpandTreeFolder,

				//delete/move mode: select all checked nodes (for drag & drop)
				beforeitemmousedown: function(node, record, item, index, e, text){
					if(WE.Conf.TREE_DEL_DD_FIX_SEL && Ext.isBoolean(record.data.checked)){
						var n = record;
						n.getOwnerTree().getStore().getRootNode().cascadeBy(function(n){
						if(n.get('checked')) n.getOwnerTree().getSelectionModel().select(n, true);
						else n.getOwnerTree().getSelectionModel().deselect(n);
						});
					}
				},

				//recognize double clicks
				itemdblclick: function(node, record, item, index, e, text) {
					if(typeof record.data.checked === 'undefined' || record.data.checked === null){
						clearTimeout(this.clickTimeout);
						if (record.get('leaf')) {
							this.doDblClickTreeItem(node, record, item, index, e, text);
						}
					}
				},
				itemclick: function(node, record, item, index, e, text) {
					if(typeof record.data.checked === 'undefined' || record.data.checked === null){
						if (record.get('id') === this.lastIdClicked) {
							clearTimeout(this.clickTimeout);
						}
						this.lastIdClicked = record.get('id');
						if (record.get('leaf')) {
							this.clickTimeout = Ext.Function.defer(this.doClickTreeItem, 250, this, [node, record, item, index, e, text]);
						} else {
							this.clickTimeout = Ext.Function.defer(this.doClickTreeFolder, 250, this, [node, record, item, index, e, text]);
						}
					} else {
						//we do not apply any dblclick logic when in delete mode
						//TODO: should we apply it for folders? => but then we must accept 
						//		timeout what can be boring when checking checkboxes!!
						this.doClickTreeItemDelMode(node, record, item, index, e, text);
					}
				},

				//more listerners on tree
				itemcontextmenu: function(node, record, item, index, e, eOpts) {
					if (record.get('leaf')) {
						this.doCtTreeItem(node, record, item, index, e, eOpts);
					} else {
						this.doCtTreeFolder(node, record, item, index, e, eOpts);
					}
				}
			},

			'[xtype="main.treebar.treetab"]': {
				beforehide: function(tab) {
					tab.up().lastTabActive = tab;
					tab.setDeleteMode(false);
				},

				beforeshow: function(tab) {
					//tab.getTree().el.mask('Test', 'x-mask-loading');
					tab.getTree().getStore().compareTreeWithServer;
					//tab.up().lastTabActive.getTree().getStore().load();

					//TODO: split load routine to intercept with layout suspend/resume
					//http://www.sencha.com/forum/showthread.php?251342-TreePanel-very-slow-at-loading-amp-expanding-250-nodes-after-ajax-call-to-get-nodes
					//http://www.sencha.com/forum/showthread.php?257966-Ext-4.2.0.489-VERY-slow-tree-grid-on-second-load-with-bufferedrenderer-plugin/page2
				},

				show: function(tab){
					tab.up().lastTabActive.getTree().getStore().compareTreeWithServer();
					//tab.up().newTabActive = tab;
					//tab.up().lastTabActive.getTree().getStore().load();
				}
			},

			'[xtype="main.treebar"] tab': {
				click: function(tab, e) {
					if(tab.card.xtype === 'main.treebar.treetab' && e.ctrlKey){
						tab.card.getTree().getStore().setCompareBlocked(true);
						Ext.suspendLayouts();
						//tab.getTree().el.mask('Test', 'x-mask-loading');
						tab.card.getTree().getStore().load();
						Ext.resumeLayouts();
					}
				}
				
			}
		});
	},
	onLaunch: function() {
		//
	},

	/* tree functions */
	doClickTreeItem: function(tree, record, item, index, e, text) {
		//open file in multieditor
		//==>	as long as old multieditor is in use we let it initialise documents and open documents  
		//		in multieditor proto by transaction
		var me = this,
			m = me.getMultieditor(),
			mItem = m.getComponent(record.get('table') + '_' + record.get('id'));

		if(mItem){
			m.setActiveTab(mItem);
		} else {
			//this.mainStoreLoad(record.get('table'), record.get('id'), record.get('ct'));

			//open file in WE-Multieditor
			me.controllerBridge.openInMultiEditor(record.get('table'), record.get('id'), record.get('ct'));
		}
	},

	doDblClickTreeItem: function(node, record, item, index, e, text) {
		if (node.up('[xtype="main.treebar.treetab"]').tblConst === 'FILE_TABLE') {
			this.controllerBridge.openInBrowser(record.get('id'));
		}
	},

	doClickTreeFolder: function(node, record, item, index, e, text) {
			//open folder in WE-Multieditor => an items angleichen
			this.controllerBridge.openInMultiEditor(record.get('table'), record.get('id'), record.get('ct'));
	},

	doClickTreeItemDelMode: function(tree, record, item, index, e, text) {
		if(!e.shiftKey){
			if (!e.getTarget('.x-tree-checkbox',1,true)) {
				record.set('checked', !record.data.checked);
				if(record.get('checked') === true){
					tree.up().getStore().checkChildrenRecursive(record);
				} else {
					tree.up().getStore().uncheckParentRecursive(record);
				}
			} else {
				//it is absurde: when clicked in checkbox directly, checkboxe is changed, click event is fired, 
				//but check state of node has not changed yet!
				if(record.get('checked') === false){
					tree.up().getStore().checkChildrenRecursive(record);
				} else {
					tree.up().getStore().uncheckParentRecursive(record);
				}
				
			}
		} else {
			tree.up().getStore().checkRange(record);
		}
		tree.up().getStore().setLastNodeClicked(record);
	},

	doCollapseTreeFolder: function(node, eOpt) {
		//send message to server
		var store = node.getOwnerTree().getStore();
		Ext.Ajax.request({
			url: '/webEdition/we_cmd_ext.php',
			method: 'POST',
			params: {
				'we_cmd[0]': 'closeFolder',
				'we_cmd[1]': store.getTable(),
				'we_cmd[2]': node.get('id')
			},
			success: function(transport) {
			},
			failure: function(transport) {
			}
		});
	},

	doExpandTreeFolder: function(node, eOpt) {
		//reload nodes even if they could be taken from store
		if (node.isLoaded()) {
			var store = node.getOwnerTree().getStore();
			store.load({node: node});//BUG: does load only opened dir, not reload whole tree!
		}
	},

	doCtTreeItem: function(node, record, item, index, e, eOpts) {
		e.stopEvent();
	},

	doCtTreeFolder: function(node, record, item, index, e, eOpts) {
		e.stopEvent();
	},

	/*
	 *Look for tree the record belongs to and call it to do the job itself
	 */
	doUpdateNode: function(record, operation){
		var tree;
		if(tree = WE.store.main.Tree.getTreeByTable(record.data.table)){
			tree.updateNode(record);
		}
	}
});