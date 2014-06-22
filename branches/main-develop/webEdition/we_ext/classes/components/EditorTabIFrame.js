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

Ext.define('WE.classes.components.EditorTabIFrame', {
	extend: 'Ext.ux.IFrame',
	closable: false,
	tabId: 1,

	config: {
		we_id: 0,
		table: '',
		rec: null,
		transaction: ''
	},

	initComponent: function(){
		var me = this;
		me.src = '/webEdition/we_cmd.php?we_cmd[0]=load_editor&we_cmd[1]=' + me.tabId + '&we_transaction=' + me.transaction;
		me.callParent(arguments);
	},

	reloadContent: function(flag){
		var me = this,
			flag = flag || false;

		if(flag){
			this.load(this.src);
		}
	}
});