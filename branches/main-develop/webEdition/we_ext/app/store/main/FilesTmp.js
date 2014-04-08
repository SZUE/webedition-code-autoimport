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

Ext.define('WE.store.main.FilesTmp', {
	extend: 'Ext.data.Store',
	storeId: 'filestoreTmp',
	model: 'WE.model.main.Files',

	autoLoad: false,
	table: '',

	constructor: function(config) {

		this.initConfig(config);

		Ext.apply(this,{
			proxy: {
				type: 'ajax',
				url: 'we_cmd_ext.php?we_cmd[0]=edit_document_tmp',
				actionMethods :{
					read: 'POST'
				},
				reader: {
					type: 'json',
					root: 'file'
				}
			}
		});
		this.callParent(arguments);
	}
});