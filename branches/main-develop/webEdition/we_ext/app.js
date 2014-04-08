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

Ext.Loader.setConfig({
	enabled:true,
	paths: {
		WE: 'we_ext/app', //TODO: set path to app/ inside we_ext/, mabe inside we/
		Ux: 'we_ext/thirdparty/Ux',
		'Ext.i18n': 'we_ext/thirdparty/i18n',
		'WE.classes': 'we_ext/classes'
	},
	
	language: 'de'
});

Ext.application({
	name: 'WE',
	appFolder: 'we_ext/app',

	requires: [
		//TRYOUT: Ext.i18n.Bundle'
		//Simple and ok. There are problems using arrays in JSON mode: 
		//Dirty fix: using group_item instead od group[item].
		'Ext.i18n.Bundle',

		//TRYOUT: Ux.locale.Manager: 
		//Is too complicated for simple use: one must write ovverrides for every peace of text!
		//Would be tool of choice, if we wanted to chnage language on the fly, without reloading GUI
		/*
		'Ux.locale.Manager',
		'Ux.locale.override.extjs.Component',
		'Ux.locale.override.extjs.Button',
		'Ux.locale.override.extjs.FieldContainer',
		'Ux.locale.override.extjs.MenuItem',
		'Ux.locale.override.extjs.Panel',
		'Ux.locale.override.extjs.Text',
		*/
		'WE.Global'
	],
	autoCreateViewport: true,

	controllers: [
		'Main', 
		'Bridge',
		'Menu',
		'Tree'
	],

	//define bundle properties
	bundle: {
		bundle: 'lang',
		lang: WE.Conf.BACKEND_LANG || 'Deutsch',
		path: 'we_ext/language',
		noCache: true,
		// try adding this to read from json bundles!
		format: 'json'
	},

	launch:function(){
		/*
		Ux.locale.Manager.setConfig({
			ajaxConfig : {
			method : 'GET'
			},
			language : 'de',//TODO: how do we change locale? Maybe load this as .php
			tpl : 'we_ext/app/locales/{locale}.json',
			type : 'ajax'
		});
		Ux.locale.Manager.init();
		*/

		//is this the place for such things?
		Ext.QuickTips.init();

		Ext.apply(Ext.QuickTips.getQuickTip(), {
			maxWidth: 140,
			//minWidth: 100,
			showDelay: 300,      // Show 50ms after entering target
			trackMouse: true,
			bodyStyle: 'background-color: red'
		});
		//alert('all new');
	}
});