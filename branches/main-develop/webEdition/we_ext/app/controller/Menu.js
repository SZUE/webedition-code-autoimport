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

Ext.define('WE.controller.Menu', {
	extend: 'Ext.app.Controller',
	config: {
		stores: [
			'main.Menu'
		],
		models: [
			'main.Menu'
		],
		views: [
			'main.MenuBar'
		],
		refs: [{
			ref: 'menubar',
			selector: '[xtype="main.menubar"]'
		}],

		lastMenuCmd: ''
	},

	init: function() {
		//Get some references
		this.controllerBridge = WE.app.getController('Bridge');

		//Add listeners to named components
		this.control({
			'[xtype="main.menubar"] button': {
				click: function(btn, event){
					if(Ext.isString(btn.cmd)){
						switch(btn.cmd){
							case 'editor_home':
								break;
							case 'editor_reload':
								btn.up('[xtype="main.menubar"]').reloadMenus();
								break;
							case 'editor_back':
								break;
							case 'editor_next':
								WE.GLOBAL.setMyLastCustomer(12345);
								break;								
						}
					}
				}
			},

			'[xtype="main.menubar"] menuitem': {			
				click: function(item, e, eOpts ){
					// give menu time to collapse...
					Ext.Function.defer(this.doMenuClick, 1, this, [item]);
				}
			}
		});
	},
	onLaunch: function() {
		//
	},

	/* menu function (call deffered) */
	doMenuClick: function(item){
		switch(item.cmd){
			case 'close_document':
				menuaction(item.cmd);
				//activate when old command is not looped back to ext anymore 
				/*
				var rec = this.storeFiles.getById(getMultieditor().getActiveTab().itemId);
				this.storeFiles.remove(rec);
				*/
				break;
			case 'delete_documents':
				this.getTreebar().getComponent('FILE_TABLE').setDeleteMode(true);
				break;
			case 'delete_templates': 
				this.getTreebar().getComponent('TEMPLATES_TABLE').setDeleteMode(true);
				break;
			case 'delete_objectfile': 
				this.getTreebar().getComponent('OBJECT_FILES_TABLE').setDeleteMode(true);
				break;
			case 'delete_object': 
				this.getTreebar().getComponent('OBJECT_TABLE').setDeleteMode(true);
				break;
			default: 
				if(!Ext.isEmpty(item.cmd)){
					menuaction(item.cmd);
				}
		}
	}
});