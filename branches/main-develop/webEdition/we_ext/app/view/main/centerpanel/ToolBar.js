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

Ext.define('WE.view.main.centerpanel.ToolBar', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.main.centerpanel.toolbar',
	requires: ['WE.view.main.centerpanel.toolbar.Properties'],

	cls: 'main-toolbar',
	title: 'Ex-Multieditor-Tabs',
	layout: {
		type: 'accordion',
		multi: false,
		hideCollapseTool: true,
		activeOnTop: false
	},
	items: [{
		xtype: 'main.centerpanel.toolbar.properties'
	}, {
		xtype: 'panel',
		title: 'to become info'
	}, {
		xtype: 'panel',
		title: 'to become scheduler'
	}],

	tools: [{
		type: 'gear',
		callback: function (panel, tool) {
			function setWeight () {
				panel.setRegionWeight(parseInt(this.text, 10));
			}

			var regionMenu = panel.regionMenu || (panel.regionMenu =
					Ext.widget({
						xtype: 'menu',
						items: [{
							text: 'North',
							glyph: '9650@',
							handler: function () {
								panel.setBorderRegion('north');
							}
						},{
							text: 'South',
							glyph: '9660@',
							handler: function () {
								panel.setBorderRegion('south');
							}
						},{
							text: 'East',
							glyph: '9658@',
							handler: function () {
								panel.setBorderRegion('east');
							}
						},{
							text: 'West',
							glyph: '9668@',
							handler: function () {
								panel.setBorderRegion('west');
							}
						},
						'-', {
							text: 'Weight',
							menu: [{
								text: '-10',
								group: 'weight',
								xtype: 'menucheckitem',
								handler: setWeight
							},{
								text: '10',
								group: 'weight',
								xtype: 'menucheckitem',
								handler: setWeight
							},{
								text: '20',
								group: 'weight',
								xtype: 'menucheckitem',
								handler: setWeight
							},{
								text: '50',
								group: 'weight',
								xtype: 'menucheckitem',
								handler: setWeight
							},{
								text: '100',
								group: 'weight',
								xtype: 'menucheckitem',
								handler: setWeight
							}]
						}]
					}));

			regionMenu.showBy(tool.el);
		}
	}]
});