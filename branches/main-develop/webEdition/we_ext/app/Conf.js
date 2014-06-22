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

Ext.namespace('WE').Conf = {};
Ext.apply(WE.Conf, {
	//TODO: loading configuration does not work yet
	loadConfiguration : function(what){
		Ext.Ajax.request({
			url: 'we_cmd_ext.php?we_cmd[0]=get_congiguration',
			params: {
				'what' : what
			},
			success: this.applyConfiguration,
			failure: function() {},
			scope: this
		});
	},

	applyConfiguration : function(response, request){
		var config = Ext.JSON.decode(response.responseText).config;
		Ext.apply(this, config);
	}
	
});
