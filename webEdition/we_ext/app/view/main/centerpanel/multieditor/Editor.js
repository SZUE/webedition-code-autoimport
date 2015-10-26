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

//TODO: load different editors for different content types!

Ext.define('WE.view.main.centerpanel.multieditor.Editor', {
	extend: 'Ext.panel.Panel',
	requires: [
		'WE.view.main.centerpanel.multieditor.EditTab',
		'WE.view.main.centerpanel.multieditor.PreviewTab'
	],

	alias: 'widget.main.centerpanel.multieditor.editor',
	closable: true,

	editTab: {},
	previewTab: {},

	layout: {
		type: 'vbox',
		align: 'stretch'
	},

	config: {
		we_id: '',
		table: '',
		rec: null
	},

	initComponent: function(){
		var me = this;

		me.editTab = new WE.view.main.centerpanel.multieditor.EditTab({
				title: WE.Global.gl('l_tabs_editor_edit'),
				itemId: 'edit',
				id: 'edit_' + me.id,
				transaction: me.id,
				flex: 1
		});

		me.previewTab = new WE.view.main.centerpanel.multieditor.PreviewTab({
				xtype: 'main.centerpanel.multieditor.previewtab',
				title: WE.Global.gl('l_tabs_editor_preview'),
				itemId: 'properties',
				id: 'properties_' + me.id,
				transaction: me.id,
				flex: 1
		});

		me.callParent(arguments);

		me.add([{
			//TODO: use ext tpl to design header
			xtype: 'component',
			html: 'dies ist der doc head<br />mit zwei zeilen',
			cls: 'we_editor_head'
		}, {
			xtype: 'tabpanel',
			flex: 1,
			tabBar: {
				defaults: {
					flex: 1
				},
				dock: 'top'
			},
			items: [me.editTab, me.previewTab],
		}]);
	},

	updateEditor: function(record, reloadFirstTab, reloadSecondTab){
		this.setTitle(record.data.text);
		this.getComponent(1).getComponent(0).reloadContent(reloadFirstTab);
	}
});