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
	this.weEditorType = args.weEditorType;
	this.weIsFrontend = args.weIsFrontend;
	this.weName = args.weName;
	this.weFieldName = args.weFieldName;
	this.weFieldNameClean = args.weFieldNameClean;
	this.weContentCssParts = args.weContentCssParts;
	this.weWin = args.weWin;
	this.weToolbarRows = args.weToolbarRows;
	this.weContextmenuCommands = args.weContextmenuCommands;
	this.weFullscreen_readyConfig = args.weFullscreen_readyConfig;
	this.weImageStartID = args.weImageStartID;
	this.weGalleryTemplates = args.weGalleryTemplates;
	this.weClassNames_urlEncoded = args.weCssClasses;
	this.weRemoveFirstParagraph = args.weRemoveFirstParagraph;
	this.weTinyParams = args.weTinyParams;
	this.wePluginClasses = args.wePluginClasses;
	this.weWordCounter = 0;
	this.weResizeLoops = 100;

	this.weInlineFalse_dialogMaximization = {
		isMaximized : false,
		lastX : 0,
		lastY : 0,
		lastW : 0,
		lastH: 0
	};
	this.weRegisteredDialogs = {
		dialog : null,
		secondaryDialog : null
	};
	this.weSynchronizeHot = {
		doSyncHot : false,
		tmpDoSyncHot : false,
		isEditorHot  : false,
		doOnChange : null,
		doOnKeyUp : null
	};

	this.doctype = '<!DOCTYPE html>';
	this.language = args.language;
	this.mode = 'exact';
	this.elements = args.elements;
	this.theme = 'advanced';
	this.skin = 'o2k7';
	this.skin_variant = 'silver';
	this.editor_css = args.editor_css;
	this.popup_css_add = args.popup_css_add;
	this.body_class = (args.weClassName ? args.weClassName + ' ' : '') + 'wetextarea tiny-wetextarea wetextarea-' + args.weOrigName;
	this.plugins = args.plugins;
	this.accessibility_warnings = false;
	this.relative_urls = false; //important!
	this.convert_urls = false; //important!
	this.force_p_newlines = 0; // value 0 instead of true (!) prevents adding additional lines with <p>&nbsp</p> when inlineedit="true"
	this.entity_encoding = 'named';
	this.entities = '160,nbsp';
	this.fix_list_elements = true;
	this.element_format = args.element_format;
	this.theme_advanced_toolbar_location = args.theme_advanced_toolbar_location; //external: toolbar floating on top of textarea
	this.theme_advanced_toolbar_align = 'left';
	this.theme_advanced_fonts = args.theme_advanced_fonts;
	this.theme_advanced_font_sizes = args.theme_advanced_font_sizes;
	this.theme_advanced_styles = args.theme_advanced_styles;
	this.theme_advanced_blockformats = args.theme_advanced_blockformats;
	this.theme_advanced_statusbar_location = args.theme_advanced_statusbar_location;
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
	this.paste_text_sticky = true;
	this.paste_auto_cleanup_on_paste = true;
	this.template_templates = args.template_templates;
	//this.force_br_newlines = true;
	//this.forced_root_block = '';

	this.setup = WE().layout.we_tinyMCE.setupEditor;
	this.oninit = WE().layout.we_tinyMCE.initEditor;
	this.paste_preprocess = WE().layout.we_tinyMCE.do.afterPastePlugin;
}

WE().layout.we_tinyMCE.setupEditor = function(ed){
	var conf = ed.settings;

	WE().layout.we_tinyMCE.functions.setContentCss(ed);
	WE().layout.we_tinyMCE.functions.setToolbarRows(ed);
	WE().layout.we_tinyMCE.functions.setTinyParams(ed);
	WE().layout.we_tinyMCE.functions.setSyncHot(ed);

	ed.onKeyDown.add(WE().layout.we_tinyMCE.do.onKeyDown);
	ed.onDblClick.add(WE().layout.we_tinyMCE.do.onDblClick);
	ed.onPostProcess.add(WE().layout.we_tinyMCE.do.onPostProcess);
	ed.onNodeChange.add(WE().layout.we_tinyMCE.do.onNodeChange);

	if(conf.weEditorType !== 'inlineTrue'){
		ed.onPostRender.add(WE().layout.we_tinyMCE.do.onPostRender); //still no solution for relative scaling when inlineedit=true
		conf.weWin.addEventListener('unload', function(e){WE().layout.we_tinyMCE.do.onUnloadWysiwygDialog(ed);}, false);
	}

	if(!conf.weIsFrontendEdit && conf.weSynchronizeHot.doSyncHot){
		conf.weSynchronizeHot.doOnChange = ed.onChange.add(WE().layout.we_tinyMCE.do.onChange);
		conf.weSynchronizeHot.doOnKeyUp = ed.onKeyDown.add(WE().layout.we_tinyMCE.do.onKeyUp);
		ed.onSaveContent.add(WE().layout.we_tinyMCE.do.onSaveContent);
	}

	ed.onInit.add(WE().layout.we_tinyMCE.onInitEditor);
};

WE().layout.we_tinyMCE.onInitEditor = function(ed){
	var conf = ed.settings;

	// set some controls
	ed.pasteAsPlainText = 1;
	ed.controlManager.setActive('pastetext', 0);
	if(conf.weEditorType === 'fullscreen'){
		ed.controlManager.setDisabled('wefullscreen', 1);
	}
	//ed.execCommand("mceWevisualaid", true);

	// custom node filters
	ed.serializer.addNodeFilter("a", WE().layout.we_tinyMCE.functions.customNodeFilter_A);

	// add listeners for events ed has no public events for
	ed.dom.bind(ed.getWin(), 'drop', function(e) {return WE().layout.we_tinyMCE.do.onDrop(e, ed);});

	// FIXME: replace copy/cut by keyDown
	ed.dom.bind(ed.getWin(), 'copy', function(e) {WE().layout.we_tinyMCE.do.onCopyCut(ed, false);});
	ed.dom.bind(ed.getWin(), 'cut', function(e) {WE().layout.we_tinyMCE.do.onCopyCut(ed, true);});

	// when not inlineTrue: get content from opener document => move to fn
	if(conf.weEditorType !== 'inlineTrue'){
		WE().layout.we_tinyMCE.functions.wysiwygDialog_setContent(ed);
	}

	/* ALL THIS EDITOR-REGISTERING STUFF IS NOT WORKING AT THE TIME! */
	if(conf.weFieldName){
		conf.weWin.tinyEditors[conf.weFieldName] = ed;

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

WE().layout.we_tinyMCE.getTinyConfObject = function(args){
	return new TinyMceConfObject(args);
};