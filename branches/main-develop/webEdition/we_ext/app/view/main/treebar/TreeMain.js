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

Ext.define('WE.view.main.treebar.TreeMain', {
	extend: 'WE.classes.components.BaseTree',
	alias: 'widget.main.treebar.treemain',

	viewConfig: {
		
		plugins: {
			ptype: 'treeviewdragdrop',
			enableDrag: WE.Conf.TREE_DEL_DD ? true : false,
			//enableDrop: true
			containerScroll: false,
			allowParentInsert: true,
			allowContainerDrops: true,
			appendOnly: true
		},
		//TODO: how do we add plugins without overriding other props? (that's why selectedItemsCls must be added here oncegain)
		selectedItemCls : 'tree_standard_selected'
	},

	constructor: function(config) {
		this.callParent(arguments);
		this.getStore().treePane = this;
	},

	//when node not in this tree deselect all!
	selectNode: function(table, id){
		var me = this,
			sm = me.getSelectionModel();
		if(me.getId() === 'tree_' + table){
			sm.select(me.getStore().getNodeById(id));
		} else {
			var selection = sm.getSelection();
			sm.deselect(selection[0]);
		}
	}
});