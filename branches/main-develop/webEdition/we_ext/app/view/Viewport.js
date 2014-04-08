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

Ext.define('WE.view.Viewport', {
	extend: 'Ext.container.Viewport',
	//alias: 'widget.viewport',
	requires: [
		'WE.view.main.MenuBar', 
		'WE.view.main.TreeBar',
		'WE.view.main.CenterPanel',
		'WE.view.main.SouthPanelTmp'
	],
	id: 'mainviewport',
	layout: 'border',
	//displayHybridMode: true,

	//no need to call define initComponent when not used...
	initComponent: function(){
		//this.displayHybridMode = true,
		//do something
		this.callParent();
	},
	items: [{
			region: 'north',
			xtype: 'main.menubar'
		}, {
			region: 'west',
			xtype: 'main.treebar',
			split: true,
			width: 280
		},{
			region: 'center',
			xtype: 'main.centerpanel',
		}, {
			region: 'east',
			hidden: WE.Conf.HYBRIDMODE ? false : true,
			width: 150,
			split: true,
			collapsible: true,
			collapsed: true,
			title: 'Future Sidebar'
		}, {
			region: 'south',
			xtype: 'main.southpanel',
			height: 200,
			collapsible: true,
			collapsed: true
		}]
});