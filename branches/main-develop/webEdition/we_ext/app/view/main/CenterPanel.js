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

Ext.define('WE.view.main.CenterPanel', {
	extend: 'Ext.panel.Panel',
	alias: 'widget.main.centerpanel',

	requires: [
		'WE.view.main.centerpanel.Multieditor', 
		'WE.view.main.centerpanel.ToolBar'
	],

	layout: {
		type: 'border'
	},

	items: [{
		//FIX splitter when classic we is outer let element item of centerpanel
		xtype: 'component',
		region: 'west',
		hidden: WE.Conf.HYBRIDMODE,
		width: 4
	}, {
		//IMPORTANT: as long as classic WE is included, we need to pack multieditor and toolbar in seperate panel
		xtype: 'panel',
		region: 'center',
		hidden: !WE.Conf.HYBRIDMODE,
		layout: {
			type: 'border'
		},
		dockedItems: [{
			xtype: 'toolbar',
			dock: 'bottom',
			cls: 'we_editor_footer',
			height: 40,
			items: [{
				xtype: 'button',
				text: 'Button'
			}]
		}],
		items: [{
			xtype: 'main.centerpanel.multieditor',
			region: 'center',
			flex: 2
		}, {
			xtype: 'main.centerpanel.toolbar',
			region: 'east',
			collapsible: true,
			collapseDirection: 'right',
			flex: 1
		}]
	}, {
		xtype: 'panel',
		region: WE.Conf.HYBRIDMODE ? 'east' : 'center',
		width: 200,
		split: true,
		collapsible: WE.Conf.HYBRIDMODE ? true : false,
		collapsed: false,
		title: WE.Conf.HYBRIDMODE ? 'Classic webEdition' : '',
		loader: {
			autoLoad:{url: '/webEdition/webEdition_insideExt.php', scripts:true, renderer: 'html'}
		}
	}]
});