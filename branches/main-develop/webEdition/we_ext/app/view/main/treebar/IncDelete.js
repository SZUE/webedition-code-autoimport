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

Ext.define('WE.view.main.treebar.IncDelete', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.main.treebar.incdelete',
	border: true,
	table: '',
	id: '',

	initComponent: function() {
		Ext.apply(this, {
			title: WE.Global.gl('l_newFile__title_deleteBox'),
			items: [{
				html: WE.Global.gl('l_newFile__delete_text')
			}, {
				xtype: 'button',
				text: WE.Global.gl('l_buttons_global__ok__value'),
				width: parseInt(WE.Global.gl('l_buttons_global__ok__width')),
				handler: function(){
					/*
					this.up('treetab').getTree().getStore().getRootNode().cascadeBy(function(n){
						if(n.get('checked')) n.getOwnerTree().getSelectionModel().select(n, true);
						else n.getOwnerTree().getSelectionModel().deselect(n);
					});
					*/

					var records = this.up('[xtype="main.treebar.treetab"]').getTree().getView().getChecked(),
						names = [], 
						ids = [];
					Ext.Array.each(records, function(rec){
						names.push(rec.get('text'));
						ids.push(rec.get('id'))
					});
						
					Ext.MessageBox.show({
						title:'Ausgewählte Einträge löschen?',
						msg: names.join('<br />'),
						buttons: Ext.MessageBox.OKCANCEL,
						fn: this.up().proceedCmd,
						icon: Ext.MessageBox.QUESTION,
						ids: ids.join(',')
					});
					
				}
			}, {
				xtype: 'button',
				text: WE.Global.gl('l_buttons_global__quit_delete__value'),
				width: parseInt(WE.Global.gl('l_buttons_global__quit_delete__width')),
				handler: function() {
					this.up('[xtype="main.treebar.treetab"]').setDeleteMode(false);
				}
			}]
		});
		this.callParent();
	},

	proceedCmd: function(btn, e, confObj){
		//
	}
});
