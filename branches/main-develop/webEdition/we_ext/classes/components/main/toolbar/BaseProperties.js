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

/*
 * IMPORTANT:
 * In the actual implementation we have just one BaseProperties object who's form fields are shown and field 
 * depending on contenttype EACH TIME the multierditor button is changed.
 * 
 */

Ext.define('WE.classes.components.main.toolbar.BaseProperties', {
	extend: 'Ext.form.Panel',
	title: 'Properties',
	requires: [
		'WE.classes.components.form.field.DirSelector',
		'WE.classes.components.form.field.DocSelector'
	],

	ctConfig : {
		'text/webedition': ['hidden.fields', 'path.nameext', 'path.dir', 'document.tmpl'],
		'image/*': ['hidden.fields', 'path.dir'], 
		'folder/file': ['hidden.fields', 'path.nameext', 'path.dir', 'path.domain'],

		'text/weTmpl': ['hidden.fields', 'path.nameext', 'path.dir', 'document.text'],
		'folder/tmpl': ['hidden.fields', 'path.nameext', 'path.dir'],
		
		'objectFile': ['hidden.fields', 'path.nameext', 'path.dir']
		
		//'folder/tmpl': ['path.nameext', 'path.dir']
	},
	
	config: {
		ct: 'empty'
	},
	
	defaults: {
		anchor: '100%',
		labelAlign : 'top'
		//disabled: true
	},
	defaultType: 'textfield',
	
	buttons: [/*{
		//TODO: reset to last saved/or commited state: sync with store record
		text: 'Reset',
		handler: function() {
			this.up('form').getForm().reset();
		}
	},*/ {
		text: 'Übernehmen',
		handler: function() {
			//console.log(this.up().up());//not quite elegant...
			this.up().up().submitToStore();
		}
	}],

	initComponent: function() {
		this.callParent();
		this.add([this.blocks.hiddenData, this.blocks.path, this.blocks.document, this.blocks.meta]);
	},
			
	blocks: {
		hiddenData: {
			xtype:'fieldset',
			title: 'Hidden Data',
			collapsible: true,
			collapsed: false,
			itemId: 'hiddenData',
			hidden: true,
			defaults: {anchor: '100%'},
			layout: 'anchor',
			items: [{
				itemId: 'hidden.fields',
				xtype: 'fieldcontainer',
				layout: 'hbox',
				hidden: true,
				defaultType: 'displayfield',
				fieldDefaults: {
					labelAlign: 'top'
				},
				items: [{
					xtype: 'numberfield',
					hideTrigger: true,
					readOnly: true,
					fieldLabel: 'Published Status',
					name: 'published',
					flex: 1
				}, {
					xtype: 'numberfield',
					hideTrigger: true,
					readOnly: true,
					fieldLabel: 'DID',
					name: 'we_id',
					flex: 1
				}]
			}]
		},
		path: {
			xtype:'fieldset',
			title: 'Pfad',
			collapsible: true,
			itemId: 'path',
			hidden: true,
			defaultType: 'textfield',
			defaults: {anchor: '100%'},
			layout: 'anchor',
			items :[{
				itemId: 'path.nameext',
				xtype: 'fieldcontainer',
				layout: 'hbox',
				hidden: true,
				defaultType: 'textfield',
				fieldDefaults: {
					labelAlign: 'top'
				},
				items: [{
					fieldLabel: 'Dateiname',
					name: 'filename',
					flex: 1
				}, {
					fieldLabel: 'Erweiterung',
					name: 'extension',
					width: 80
				}]
			}, {
				itemId: 'path.dir',
				hidden: true,
				xtype: 'dirselector',
				fieldLabel: 'Verzeichnis',
				labelAlign: 'top',

				txtField: 'parentpath',
				idField: 'parentid'

				//the following vars are set now by default
				//tableField: 'table',
				//transactionField: 'id',
				//checkTransaction: true,
				//writeToStore: true,
				//storeName: 'Files'
			}, {
				itemId: 'path.domain',
				hidden: true,
				fieldLabel: 'Verzeichnis durch Domain ersetzen',
				name: '',
				labelAlign: 'top'
			}]
		},
		document: {
			xtype:'fieldset',
			title: 'Dokument',
			collapsible: true,
			itemId: 'document',
			hidden: true,
			//disabled: true,
			defaultType: 'textfield',
			items: [{
				itemId: 'document.tmpl',
				xtype: 'docselector',
				fieldLabel: 'Vorlage',
				labelAlign: 'top',

				txtField: 'templatepath',
				idField: 'templateid',
				tableConst: 'TEMPLATES_TABLE',
				hidden: true
			}]
		}
	},

	setCt: function(fullCt){
		this.ct = fullCt;
		//this.removeAll();
		this.items.each(function(item){
			item.hide();
			//iterate elements inside blocks
			item.items.each(function(inneritem){
				if(this.ctConfig[this.ct] && Ext.Array.contains(this.ctConfig[this.ct], inneritem.itemId)){
					inneritem.show();
					inneritem.up().show();
				} else {
					inneritem.hide();
				}
			}, this);
		}, this);
	},

	submitToStore: function(){
		//to be overridden
	}
});