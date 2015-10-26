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

/* 
 * NOTICE: this component has its tool-buttons on tabbar not on header!!
 */

Ext.define('WE.classes.components.VerticalTabpanel', {
	extend: 'Ext.tab.Panel',
	//this component is for extending use only!
	//alias: 'widget.we.classes.compoments.verticaltabpanel',
	tabPosition: 'left',
	collapseDirection: 'left',
	collapsible: true,
	
	config: {
		
	},

	initComponent: function() {
		var me = this;
		me.callParent(arguments);

		me.tabBar.adjustTabPositions = function() {
			var me = this;
			if (!Ext.isIE9m) {//console.log(me.self.prototype);//Ext.tab.Bar
				me.self.prototype.adjustTabPositions.apply(me, arguments);//Ext.tab.Panel
				tool = me.down('tool');
				tool.el.setStyle('left', ((me.lastBox.width - tool.lastBox.width - tool.margin$.width) / 2) + 'px');
			}
		};

		me.collapseMode = 'header';
		me.hideCollapseTool = true;

		me.header = me.tabBar;
		me.tabBar.add({
			xtype: 'tbfill'
		});

		me.collapseTool = me.expandTool = me.tabBar.add({
			xtype: 'tool',
			//right margin is neptune-specific.
			margin: '5 5 5 0',
			handler: me.toggleCollapse,
			scope: me
		});
		me.updateCollapseTool();
	}
});