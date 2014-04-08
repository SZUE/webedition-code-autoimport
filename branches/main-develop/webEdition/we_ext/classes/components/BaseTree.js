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

Ext.define('WE.classes.components.BaseTree', {
	extend: 'Ext.tree.Panel',

	border: true,
	rootVisible: false,
	id: '',
	cls: 'treepanel',

	//we-specific configs: inside config to let create setters/getters
	config: {
		table: '',
		selectedItemClsStandard: 'tree_standard_selected'
	},

	selModel: Ext.create('Ext.selection.RowModel', {
			mode: 'SINGLE',
			toggleOnClick: true,
			allowDeselect: true
	}),

	viewConfig: {
		selectedItemCls : 'tree_standard_selected'
	},

	selectNode: function(node){
		sm.select(node);
	},

	setSelectedItemCls: function(cls){
		this.getView().selectedItemCls = cls;
	},

	resetSelectedItemCls: function(){
		this.getView().selectedItemCls = this.selectedItemClsStandard;
	}
});