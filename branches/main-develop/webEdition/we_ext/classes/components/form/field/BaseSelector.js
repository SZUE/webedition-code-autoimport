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
 * NOTICE:
 * /*
 * Because of the actual implementation of BaseProperties 
 * the selector button does not take its cmd values from initial class members,
 * but from actual content of (partly hidden) fields
 */

Ext.define('WE.classes.components.form.field.BaseSelector', {
	extend: 'Ext.form.FieldContainer',
	alias: 'widget.baseselector',
	layout: 'hbox',
	selectorId: '',
	tableConst: '',
	table: '',
	elemTxt: {},
	elemId: {},

	//default values for some config vars
	//tableField: 'table',
	//transactionField: 'id',
	//checkTransaction: true,
	//writeToStore: true,
	//storeName: 'Files',

	config: {
		fieldLabel: '',
		tableField: 'table',
		txtField: '',
		idField: '',
		recordIdField: 'id',
		checkRecordId: true,
		writeToStore: true,
		//storeName: '',obsolete: the loaded form record knows to what store it belongs
		tableConst: ''
	},

	initComponent: function() {
		//console.log(this);
		this.callParent();

		this.elemTxt = new Ext.form.field.Text({
			itemId: 'fieldTxt',
			name: this.txtField,
			labelAlign: 'top',
			flex: 1
		});
		this.elemId = new Ext.form.field.Number({
			xtype: 'textfield',
			itemId: 'fieldId',
			hidden: true,
			name: this.idField
		});

		this.add([this.elemTxt, this.elemId, {
			xtype: 'textfield',
			itemId: 'fieldTable',
			hidden: true,
			name: this.tableField,
			labelAlign: 'top',
			flex: 1
		},{
			xtype: 'button',
			text: 'Select',
			width: 80,
			handler: this.openSelector,
			scope: this
		}]);
	},

	openSelector: function(){
		//TODO: make this secure: not every form has a record loaded
		var recordId = this.up('form').getRecord().get(this.recordId),
			selId = 'extSelector_' + this.statics().c++,
			table = this.getTableConst() ? WE.Conf[this.getTableConst()] : this.getComponent('fieldTable').getValue();

		//save object reference and actual recordId to static collection selInstances
		this.statics().selInstances[selId] = {instance: this, recordId: recordId};

		top.we_cmd('open' + this.type, this.getComponent('fieldId').getValue(), table, 'returnToExt', selId);
	},

	getTable: function(tableConst){
		return WE.Conf[tableConst];
	},

	processResponse: function(response, savedRecordId){
		//TODO: move this to respective selector class
		//console.log(response);

		//write to selector fields:
		//the record loaded to the form may have changed
		if(!this.checkRecordId || 
				(this.checkRecordId && this.up('form') && this.up('form').getRecord() && this.up('form').getRecord().get(this.recordId) === savedRecordId)){
			this.elemTxt.setValue(response.respPath);
			this.elemId.setValue(response.respId);
		}

		//write to form record
		var record;
		if(this.writeToStore && this.up('form') && (record = this.up('form').getRecord())){
			record.beginEdit();
			record.set(this.idField, response.respId);
			record.set(this.txtField, response.respPath);
			record.endEdit();
			//TODO: write updated record to session
			//Ext.getStore('Files').saveToSession(record);
		}
	},

	statics : {
		selInstances : {},
		c: 0
	}
});

