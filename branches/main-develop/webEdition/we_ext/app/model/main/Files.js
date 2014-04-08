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

Ext.define('WE.model.main.Files', {
	extend: 'Ext.data.Model',

	fields: [
			{ name: 'we_id', type: 'integer' },
			{ name: 'id', type: 'string' },
			//{ name: 'text', type: 'string' },
			{ name: 'filename', type: 'string' },
			{ name: 'extension', type: 'string' },
			{ name: 'text', type: 'string', convert: function(v, record) {
					return record.get('filename') + record.get('extension');
				}
			},
			{ name: 'parentpath', type: 'string' },
			{ name: 'path', type: 'string', convert: function(v, record) {
					return (record.get('parentid') !== 0 ? record.get('parentpath') : '') + '/' + record.get('filename') + record.get('extension');
				}
			},
			{ name: 'transaction', type: 'string' },
			{ name: 'table', type: 'string' },
			{ name: 'ct', type: 'string' },
			{ name: 'fullCt', type: 'string' },
			{ name: 'published', type: 'integer' },
			{ name: 'moddate', type: 'integer' },
			{ name: 'parameters', type: 'integer' },
			{ name: 'name', type: 'string' },
			{ name: 'parentid', type: 'integer' },
			{ name: 'iconCls', type: 'string' },
			{ name: 'isFolder', type: 'integer' },//obsolete?
			{ name: 'templateid', type: 'integer' },
			{ name: 'templatepath', type: 'string' }
		],

	//recompute converted fields after edit
	afterEdit: function() {
		this.set('path');
		this.set('text');
		//this.doWriteToSession(this);
	},

	/*	
	doWriteToSession: function(record){
		var fields = {};
		Ext.Array.each(record.modified, function(field) {
			fields[field] = record.data[field];
		});

		params = {
			we_transaction: record.data.id,
			data: Ext.encode(fields)
		},

		Ext.Ajax.request({
			url: 'we_cmd_ext.php?we_cmd[0]=save_to_session',
			params: params,
			success: this.onWrittenToSession,
			failure: function() {},
			scope: this
		});
	},
	*/

});