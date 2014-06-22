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

Ext.define('WE.view.main.centerpanel.toolbar.Properties', {
	extend: 'WE.classes.components.main.toolbar.BaseProperties',
	alias: 'widget.main.centerpanel.toolbar.properties',
	title: 'Properties',
	rec: null,
	we_id: 0,
	table: '',
	bodyStyle:{
		'background-color' : '#EDEDED'
	}, 

	bodyPadding: 5,
	layout: 'anchor',

	submitToStore: function(){
		var record = this.getRecord();
		record.beginEdit();
		Ext.iterate(this.getForm().getFields().items, function(field){
			if(typeof record.get(field.name) !== 'undefined'){
				record.set(field.name, field.value);
			}
		});
		record.endEdit();
	}
});