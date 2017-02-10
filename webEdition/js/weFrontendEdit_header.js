/* global tinyMCEPopup, tinymce,top, WE, tinyMCE */

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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

var WE_Frontend = {
		consts:{
			IS_FRONTEND_EDIT: true,
			tables:{},
			dirs:{},
			linkPrefix:{},
			g_l:{}
		},
		util:{
			getDynamicVar: function (doc, id, dataname) {
				var el = doc.getElementById(id);
				return (el ?
					this.decodeDynamicVar(el, dataname) :
					null
					);
			},
			decodeDynamicVar: function(el, dataname) {
				var data = el.getAttribute(dataname);
				return data ? JSON.parse(window.atob(data)) : null;
			}
		},
		layout: {}
	};

function WE(){
	return WE_Frontend;
}