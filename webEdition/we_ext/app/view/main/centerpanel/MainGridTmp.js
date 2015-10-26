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

Ext.define('WE.view.main.centerpanel.MainGridTmp', {
	extend: 'Ext.grid.Panel',
	alias: 'widget.main.centerpanel.maingridtmp',
	store: Ext.create('WE.store.main.FilesTmp'),
	//title: 'main store tmp',
	hideHeaders: true,

	initComponent: function() {
		Ext.apply(this, {
			columns: [{
			text: 'Name',
			width: 140,
			sortable: true,
			hideable: false,
			dataIndex: 'text'
			}, {
			text: 'Dir',
			width: 180,
			dataIndex: 'parentpath'
			}, {
			text: 'Pfad',
			width: 180,
			dataIndex: 'path'
			}, {
			text: 'PID',
			width: 40,
			dataIndex: 'parentid'
			}, {
			text: 'ContentType',
			width: 120,
			dataIndex: 'ct'
			}, {
			text: 'FullCt',
			width: 120,
			dataIndex: 'fullCt'
			},{
			text: 'Publish State',
			width: 80,
			dataIndex: 'published'
			}, {
			text: 'DID',
			width: 40,
			dataIndex: 'we_id'
		}, {
			text: 'table',
			width: 100,
			dataIndex: 'table'
		},{
			text: 'id (= Transaction)',
			width: 60,
			dataIndex: 'id',
		}, {
			text: 'Tmpl Id',
			width: 50,
			dataIndex: 'templateid'
		},{
			text: 'Tmpl Path',
			width: 120,
			dataIndex: 'templatepath',
		}/*, {
				xtype: 'actioncolumn',
				width: 30,
				sortable: false,
				menuDisabled: true,
				items: [{
					icon: 'images/multiTabs/close.gif',
					tooltip: 'Close',
					scope: this,
					handler: this.onRemoveClick
				}]
			}*/],
			selModel: {
				selType: 'rowmodel'
			}
		});

		this.callParent();
	}
});