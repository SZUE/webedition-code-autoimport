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

Ext.define('WE.view.main.SouthPanelTmp', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.main.southpanel',

	requires: [
		'WE.view.main.centerpanel.MainGrid', 
		'WE.view.main.centerpanel.MainGridTmp', 
	],

	collapsible: true,
	collapsed: true,
	title: "See what's loaded to the in Main documents' store!",
	tools: [{
		type: 'maximize',
		tooltip: 'Maximize'
	}],
	height: 170,

	layout: {
		type: 'vbox',
		align: 'stretch'
	},

	items: [{
		xtype: 'main.centerpanel.maingrid',
		itemId: 'maingrid',
		autoScroll: true,
		flex: 1
	} ,{
		xtype: 'main.centerpanel.maingridtmp',
		height: 30
	}]

});