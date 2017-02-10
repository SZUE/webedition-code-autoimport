/* global tinyMCEPopup, tinymce,top, WE, tinyMCE */

/**
 * webEdition CMS
 *
 * $Rev: 13330 $
 * $Author: mokraemer $
 * $Date: 2017-02-07 23:53:04 +0100 (Di, 07 Feb 2017) $
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

if(WE().consts.IS_FRONTEND_EDIT){
	var consts = WE().util.getDynamicVar(document, 'loadVarWeTinyMce_config', 'data-consts');

	WE().consts.g_l.tinyMceTranslationObject = consts.g_l.tinyMceTranslationObject;
	WE().consts.tables.FILE_TABLE = consts.tables.FILE_TABLE;
	WE().consts.tables.OBJECT_FILES_TABLE = consts.tables.OBJECT_FILES_TABLE;
	WE().consts.dirs.WE_JS_TINYMCE_DIR = consts.dirs.WE_JS_TINYMCE_DIR;
	WE().consts.linkPrefix.TYPE_INT_PREFIX = consts.linkPrefix.TYPE_INT_PREFIX;
	WE().consts.linkPrefix.TYPE_OBJ_PREFIX = consts.linkPrefix.TYPE_OBJ_PREFIX;
	
}

WE().layout.we_tinyMCE = {
	'do': {},
	functions: {},
	vars: {}
};

function TinyMceConfObject(args) {
	this.weLanguage = args.weLanguage;
	this.weIsFullscreen = args.weIsFullscreen;
	this.weIsInPopup = args.weIsInPopup;
	this.weIsFrontendEdit = args.weIsFrontendEdit;
	this.weIsFrontend = args.weIsFrontendEdit;
	this.weName = args.weName;
	this.weFieldName = args.weFieldName;
	this.weFieldNameClean = args.weFieldNameClean;
	this.win = args.win;
	this.toolbarRows = args.toolbarRows;

	this.doctype = '<!DOCTYPE html>';
	this.fix_list_elements = true;
	this.wePluginClasses = {
		weadaptbold : args.editorLangSuffix + 'weadaptbold',
		weadaptitalic : args.editorLangSuffix + 'weadaptitalic',
		weabbr : args.editorLangSuffix + 'weabbr',
		weacronym : args.editorLangSuffix + 'weacronym'
	};
	this.weFullscreenState = {
		fullscreen : false,
		lastX : 0,
		lastY : 0,
		lastW : 0,
		lastH: 0
	},
	this.tinyGl = {
		removedInlinePictures: args.removedInlinePictures
	};
	this.weFullscrenParams = args.weFullscrenParams;
	this.weFullscrenParamsscreenWidth = screen.availWidth-10;
	this.weFullscrenParams.screenHeight = screen.availHeight - 70;
	this.weFullscrenParams.contentCss = args.weFullscrenParams.contentCss + WE().layout.we_tinyMCE.functions.getDocumentCss(args.win,args.weFullscrenParams.contentCss ? true : ''),
	this.weImageStartID = args.weImageStartID;
	this.weGalleryTemplates = args.weGalleryTemplates;
	this.weClassNames_urlEncoded = args.weCssClasses;
	this.weWordCounter = 0;
	this.weRemoveFirstParagraph = args.weRemoveFirstParagraph;
	this.wePopupGl = args.wePopupGl;
	this.language = args.language;
	this.mode = 'exact',
	this.elements = args.elements;
	this.theme = 'advanced';
	//this.dialog_type = 'modal';
	this.accessibility_warnings = false;
	this.relative_urls = false; //important!
	this.convert_urls = false; //important!
	//this.force_br_newlines = true;
	this.force_p_newlines = 0; // value 0 instead of true (!) prevents adding additional lines with <p>&nbsp</p> when inlineedit="true"
	//this.forced_root_block = '';
	this.entity_encoding = 'named';
	this.entities = '160,nbsp';
	this.element_format = args.xml;
	this.body_class = (args.className ? args.className + ' ' : '') + 'wetextarea tiny-wetextarea wetextarea-' + args.origName;
	this.plugins = args.plugins;
	this.we_restrict_contextmenu = args.contextmenuCommands;
	this.toolbarRows = args.toolbarRows;
	this.theme_advanced_toolbar_location = args.buttonpos; //external: toolbar floating on top of textarea
	this.theme_advanced_fonts = args.fontnames;
	this.theme_advanced_font_sizes = args.fontsizes,
	this.theme_advanced_styles = args.tinyCssClasses;
	this.theme_advanced_blockformats = args.formats;
	this.theme_advanced_toolbar_align = 'left';
	this.theme_advanced_statusbar_location = args.statuspos;
	this.theme_advanced_resizing = false;
	this.theme_advanced_default_foreground_color = '#FF0000';
	this.theme_advanced_default_background_color = '#FFFF99';
	this.plugin_preview_height = '300';
	this.plugin_preview_width = '500';
	this.theme_advanced_disable = '';
	this.extended_valid_elements = 'we-gallery[id|tmpl|class]';
	this.custom_elements = 'we-gallery';
	this.visual = true;
	this.extended_valid_elements = '@[we-tiny]';
	this.editor_css = args.editorCss;
	//this.content_css = args.fontawsomeCss + ',' + args.contentCssFirst + WE().layout.we_tinyMCE.functions.getDocumentCss(args.win,true) + (args.contentCssLast ? ',' + args.contentCssLast : ''),
	this.popup_css_add = args.popupCssAdd; 
	this.template_templates = args.templates;
	this.skin = 'o2k7';
	this.skin_variant = 'silver';
	this.paste_text_sticky = true;
	this.paste_auto_cleanup_on_paste = true;
	
	this.setup = WE().layout.we_tinyMCE.setupEditor;
	this.oninit = WE().layout.we_tinyMCE.initEditor;
	
	this.paste_preprocess = WE().layout.we_tinyMCE.do.afterPastePlugin;
};

WE().layout.we_tinyMCE.getTinyConfObject = function(args){
	return new TinyMceConfObject(args);
};

WE().layout.we_tinyMCE.setupEditor = function(ed){
	var conf = ed.settings;

	// add some editor props
	ed.weEditorFrameIsHot = false;
	ed.editorLevel = '';

	// add toolbar definitions to settings
	for(var i = 0; i < conf.toolbarRows.length; i++){
		ed.settings[conf.toolbarRows[i].name] = conf.toolbarRows[i].value;
	}

	// add tinyParams. we need them as name/value pairs: for this we need them in JSON-syntax

	// add event listeners
	ed.onInit.add(WE().layout.we_tinyMCE.onInitEditor);

	ed.onKeyDown.add(WE().layout.we_tinyMCE.do.onKeyDown);
	ed.onDblClick.add(WE().layout.we_tinyMCE.do.onDblClick);
	ed.onPostProcess.add(WE().layout.we_tinyMCE.do.onPostProcess);

	if(conf.weIsInPopup){
		ed.onPostRender.add(WE().layout.we_tinyMCE.do.onPostRender); //still no solution for relative scaling when inlineedit=true
	}

	if(!conf.weIsFrontendEdit){
		WE().layout.we_tinyMCE.functions.tinySetEditorLevel(ed); // find a simpler way to establish editorLevel...
		try {
			// do we need this on setup?
			ed.weEditorFrameIsHot = ed.editorLevel === "inline" ? ed.weEditorFrame.EditorIsHot : false;
		} catch (e) {}

		ed.onPaste.add(WE().layout.we_tinyMCE.do.beforePastePlugin);
		ed.onSaveContent.add(WE().layout.we_tinyMCE.do.onSaveContent);
		ed.onNodeChange.add(WE().layout.we_tinyMCE.do.onNodeChange);
		// ed.onChange.add(WE().layout.we_tinyMCE.do.onChange);
		// ed.onClick.add(WE().layout.we_tinyMCE.do.onClick);
	}
};

WE().layout.we_tinyMCE.onInitEditor = function(ed){
	var conf = ed.settings;

	// set some controls
	ed.pasteAsPlainText = 1;
	ed.controlManager.setActive('pastetext', 1);
	//ed.execCommand("mceWevisualaid", true);

	// custom node filters
	ed.serializer.addNodeFilter("a", WE().layout.we_tinyMCE.functions.customNodeFilter_A);

	// add listeners for events ed ha sno public events for
	ed.dom.bind(ed.getWin(), 'drop', function(e) {return WE().layout.we_tinyMCE.do.onDrop(e, ed);});
	ed.dom.bind(ed.getWin(), 'copy', function(e) {WE().layout.we_tinyMCE.do.onCopyCut(ed, false);});
	ed.dom.bind(ed.getWin(), 'cut', function(e) {WE().layout.we_tinyMCE.do.onCopyCut(ed, true);});

	// when not inlineedit: get content from opener document
	var openerDocument = !conf.weIsInPopup ? '' : (conf.weIsFrontendEdit ? top.opener.document : WE().layout.weEditorFrameController.getVisibleEditorFrame().document);
	if(conf.weIsInPopup){
		try{
			ed.setContent(openerDocument.getElementById(conf.weName).value);
		}catch(e){}
	}

	/* ALL THIS EDITOR-REGISTERING STUFF IS NOT WORKING AT THE TIME! */
	if(conf.weFieldName){
		conf.win.tinyEditors[conf.weFieldName] = ed;

		var hasOpener = false;
		try{
			hasOpener = opener ? true : false;
		} catch(e){}

		//FIXME: change this & every call to an object/array element call!
		if(typeof window['we_tinyMCE_' + conf.weFieldNameClean + '_init'] === 'function'){ // FIXME: can this work? 
			try{
				window['we_tinyMCE_' + conf.weFieldNameClean + '_init'](ed);
			} catch(e){
				//nothing
			}
		} else if(hasOpener){
			if(WE().layout.weEditorFrameController){
				//we are in backend
				var editor = WE().layout.weEditorFrameController.ActiveEditorFrameId;
				var wedoc = null;
				try{
					wedoc = opener.top.bm_content_frame.frames[editor].frames["contenteditor_" + editor];
					wedoc.tinyEditorsInPopup[conf.weFieldName] = ed;
					wedoc['we_tinyMCE_' + conf.weFieldNameClean + '_init'](ed);
				}catch(e){
					//opener.console.log("no external init function for ' . $this->fieldName . ' found");
				}
				try{
					wedoc = opener.top.bm_content_frame.frames[editor].frames["editor_" + editor];
					wedoc.tinyEditorsInPopup[conf.weFieldName] = ed;
					wedoc['we_tinyMCE_' + conf.weFieldNameClean + '_init'](ed);
				}catch(e){
					//opener.console.log("no external init function for ' . $this->fieldName . ' found");
				}
			} else{
				//we are in frontend
				try{
					window.opener.tinyEditorsInPopup[conf.weFieldName] = ed;
					window.opener['we_tinyMCE_' + conf.weFieldNameClean + '_init'](ed);
				}catch(e){
					//opener.console.log("no external init function for ' . $this->fieldName . ' defined");
				}
			}
		}
	}
	
};
