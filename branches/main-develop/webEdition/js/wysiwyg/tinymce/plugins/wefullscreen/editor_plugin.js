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

//(function(){tinymce.create('tinymce.plugins.WefullscreenPlugin',{init:function(d,e){d.addCommand('mceWefullscreen',function(){var a=d.selection;var weParams=d.getParam('weFullscrenParams');d.windowManager.open({file:e+'/../../../../we_tinymce/fullscreenEditDialog.php?we_dialog_args[editor]=tinyMce'+'&we_dialog_args[editname]=tinyMce'+'&we_dialog_args[outsideWE]='+weParams.outsideWE+'&we_dialog_args[xml]='+weParams.xml+'&we_dialog_args[removeFirstParagraph]='+weParams.removeFirstParagraph+'&we_dialog_args[baseHref]='+weParams.baseHref+'&we_dialog_args[charset]='+weParams.charset+'&we_dialog_args[cssClasses]='+weParams.cssClasses+'&we_dialog_args[bgcolor]='+weParams.bgcolor+'&we_dialog_args[language]='+weParams.language+'&we_dialog_args[screenWidth]='+weParams.screenWidth+'&we_dialog_args[screenHeight]='+weParams.screenHeight+'&we_dialog_args[className]='+weParams.className+'&we_dialog_args[fontnames]='+weParams.fontnames+'&we_dialog_args[propString]='+weParams.propString+'&we_dialog_args[contentCss]='+weParams.contentCss+'&we_dialog_args[origName]='+weParams.origName+'&we_dialog_args[tinyParams]='+weParams.tinyParams+'&we_dialog_args[contextmenu]='+weParams.contextmenu,popup_css:false,width:screen.availWidth-20,height:screen.availHeight-70,inline:1},{plugin_url:e})});d.addButton('wefullscreen',{title:'we.tt_wefullscreen',cmd:'mceWefullscreen'});d.onNodeChange.add(function(a,b,n,c){b.setDisabled('wefullscreen',false)})},getInfo:function(){return{longname:'Wefullscreen plugin',author:'webEdition e.V',authorurl:'http://www.webedition.org',infourl:'http://www.webedition.org'}}});tinymce.PluginManager.add('wefullscreen',tinymce.plugins.WefullscreenPlugin)})();

(function () {
	tinymce.create('tinymce.plugins.WefullscreenPlugin', {
		init: function (d, e) {
			d.addCommand('mceWefullscreen', function () {
				var a = d.selection;
				var weParams = d.getParam('weFullscrenParams');
				if(weParams.isInPopup){
					var weFullscreenState = d.getParam('weFullscreenState');
					if(!weFullscreenState.fullscreen){
						weFullscreenState = {
							fullscreen : true,
							lastX : window.screenX,
							lastY : window.screenY,
							lastW : window.outerWidth,
							lastH : window.outerHeight
						};
						d.settings.weFullscreenState = weFullscreenState;
						window.resizeTo(screen.availWidth, screen.availHeight);
						window.moveTo(0,0);
						d.controlManager.setActive('wefullscreen', 1);
						document.getElementsByClassName('mce_wefullscreen')[1].className = 'mceIcon mce_wefullscreen_var mce_we_fa';
						document.getElementsByClassName('mce_wefullscreen')[0].title = tinyMceTranslationObject.de.we.tt_wefullscreen_reset;
					} else {
						d.controlManager.setActive('wefullscreen', 0);
						document.getElementsByClassName('mce_wefullscreen_var')[0].className = 'mceIcon mce_wefullscreen mce_we_fa';
						document.getElementsByClassName('mce_wefullscreen')[0].title = tinyMceTranslationObject.de.we.tt_wefullscreen_set;
						window.resizeTo(weFullscreenState.lastW, weFullscreenState.lastH);
						window.moveTo(weFullscreenState.lastX,weFullscreenState.lastY);
						weFullscreenState.fullscreen = false;
						d.settings.weFullscreenState = weFullscreenState;
					}
				} else {
					d.windowManager.open({
						file: '/webEdition/dynamic/wysiwyg/fullscreenEditDialog.php?we_dialog_args[editor]=tinyMce' + '&we_dialog_args[editname]=tinyMce' + '&we_dialog_args[outsideWE]=' + weParams.outsideWE + '&we_dialog_args[xml]=' + weParams.xml + '&we_dialog_args[removeFirstParagraph]=' + weParams.removeFirstParagraph + '&we_dialog_args[baseHref]=' + weParams.baseHref + '&we_dialog_args[charset]=' + weParams.charset + '&we_dialog_args[cssClasses]=' + weParams.cssClasses + '&we_dialog_args[bgcolor]=' + weParams.bgcolor + '&we_dialog_args[language]=' + weParams.language + '&we_dialog_args[screenWidth]=' + weParams.screenWidth + '&we_dialog_args[screenHeight]=' + weParams.screenHeight + '&we_dialog_args[className]=' + weParams.className + '&we_dialog_args[fontnames]=' + weParams.fontnames + '&we_dialog_args[propString]=' + weParams.propString + '&we_dialog_args[contentCss]=' + weParams.contentCss + '&we_dialog_args[origName]=' + weParams.origName + '&we_dialog_args[tinyParams]=' + weParams.tinyParams + '&we_dialog_args[contextmenu]=' + weParams.contextmenu + '&we_dialog_args[templates]=' + weParams.templates + '&we_dialog_args[formats]=' + weParams.formats + '&we_dialog_args[imageStartID]=' + weParams.imageStartID + '&we_dialog_args[galleryTemplates]=' + weParams.galleryTemplates + '&we_dialog_args[fontsizes]=' + weParams.fontsizes,
						popup_css: false,
						width: screen.availWidth - 20,
						height: screen.availHeight - 70,
						inline: 1
						}, {
						plugin_url: e
					});
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