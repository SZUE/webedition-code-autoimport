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

Ext.define('We.model.main.Tree', {
	extend: 'Ext.data.TreeModel',

	fields: [
		{ name: 'id', type: 'integer' },
		{ name: 'text', type: 'string' },
		{ name: 'iconCls', type: 'string' },
		{ name: 'table', type: 'string' },
		{ name: 'cls', type: 'string' },
		//{ name: 'supported', type: 'boolean' },
		{ name: 'expanded', type: 'boolean' },
		{ name: 'ct', type: 'string' },
		{ name: 'leaf', type: 'boolean' },
		{ name: 'doReload', type: 'false' }
	]
});