/* global tinymce */
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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';

(function () {
	tinymce.create('tinymce.plugins.WefullscreenPlugin', {
		init: function (d, e) {
			d.addCommand('mceWefullscreen', function () {
				var a = d.selection;
				var dialogProperties = d.getParam('weDialogProperties');
				var weEditorType = d.getParam('weEditorType');
				var language = d.getParam('language');

				switch(weEditorType){
					case 'fullscreen':
						return;
					case 'inlineFalse':
						var weInlineFalse_dialogMaximization = d.getParam('weInlineFalse_dialogMaximization');
						if (!weInlineFalse_dialogMaximization.isMaximized) {//weInlineFalse_dialogMaximization
							weInlineFalse_dialogMaximization = {
								isMaximized: true,
								lastX: window.screenX,
								lastY: window.screenY,
								lastW: window.outerWidth,
								lastH: window.outerHeight
							};
							d.settings.weInlineFalse_dialogMaximization = weInlineFalse_dialogMaximization;
							window.resizeTo(screen.availWidth, screen.availHeight);
							window.moveTo(0, 0);
							d.controlManager.setActive('wefullscreen', 1);
							document.getElementsByClassName('mce_wefullscreen')[1].className = 'mceIcon mce_wefullscreen_var mce_we_fa';
							document.getElementsByClassName('mce_wefullscreen')[0].title = (WE().consts.g_l.tinyMceTranslationObject ? WE().consts.g_l.tinyMceTranslationObject[language].we.tt_wefullscreen_reset : '');
						} else {
							d.controlManager.setActive('wefullscreen', 0);
							var v = document.getElementsByClassName('mce_wefullscreen_var');
							if (v) {
								v[0].className = 'mceIcon mce_wefullscreen mce_we_fa';
							}
							document.getElementsByClassName('mce_wefullscreen')[0].title = (WE().consts.g_l.tinyMceTranslationObject ? WE().consts.g_l.tinyMceTranslationObject[language].we.tt_wefullscreen_set : '');
							window.resizeTo(weInlineFalse_dialogMaximization.lastW, weInlineFalse_dialogMaximization.lastH);
							window.moveTo(weInlineFalse_dialogMaximization.lastX, weInlineFalse_dialogMaximization.lastY);
							weInlineFalse_dialogMaximization.isMaximized = false;
							d.settings.weFullscreenState = weInlineFalse_dialogMaximization;
						}
						break; 
					case 'inlineTrue':
						d.windowManager.open({
							file: '/webEdition/we_cmd_frontend.php?we_cmd[0]=open_dialog_fullscreen&we_dialog_args[dialogProperties]=' + dialogProperties,
							popup_css: false,
							width: screen.availWidth - 20,
							height: screen.availHeight - 70,
							inline: 1
						}, {
							plugin_url: e
						});
						break;
				}
			});

			d.addButton('wefullscreen', {
				title: 'we.tt_wefullscreen_set',
				cmd: 'mceWefullscreen',
				class: 'mce_wefullscreen mce_we_fa'
			});
		},
		getInfo: function () {
			return {
				longname: 'Wefullscreen plugin',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org'
			};
		}
	});
	tinymce.PluginManager.add('wefullscreen', tinymce.plugins.WefullscreenPlugin);
})();