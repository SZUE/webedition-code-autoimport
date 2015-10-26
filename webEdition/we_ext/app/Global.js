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

Ext.define('WE.Global',{
	singleton : true,
	config : {
		myLastCustomer : 0,
		bundle: null
	},
	constructor : function(config){
		this.initConfig(config);
	},

	gl: function(args){
		this.bundle = this.bundle ? this.bundle : WE.getApplication().bundle;
		return this.bundle.getMsg(arguments[0], arguments);
	}

});