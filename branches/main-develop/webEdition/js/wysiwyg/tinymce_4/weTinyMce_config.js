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
	setup: {},
	vars: {},
	overwrite: {}
};

function TinyMceConfObject(args) {
	this.weIsFrontend = args.weIsFrontend;
	this.weEditorType = args.weEditorType;
	this.weName = args.weName;
	this.weFieldName = args.weFieldName;
	this.weFieldNameClean = args.weFieldNameClean;
	this.weContentCssParts = args.weContentCssParts;
	this.weWin = args.weWin;

	//this.weContextmenuCommands = args.weContextmenuCommands;
	this.weDialogProperties = args.weDialogProperties;
	this.weImageStartID = args.weImageStartID;

	this.weGalleryTemplates = args.weGalleryTemplates;

	this.weClassNames_urlEncoded = args.weCssClasses;
	this.weRemoveFirstParagraph = args.weRemoveFirstParagraph;

	this.weTinyParams = args.weTinyParams;

	this.wePluginClasses = args.wePluginClasses;
	this.weSubmenuAlignments = args.weSubmenuAlignments;
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
		doOnChange : null
	};

	this.doctype = '<!DOCTYPE html>';
	this.language = args.language;
	this.selector = 'textarea.' + args.weSelectorClass;
	this.height =  500; 
	this.width = 700;
	this.menu = args.menu;
	//this.contextmenu = '';

	this.plugins = args.plugins;
	this.external_plugins = {
		welink: '/webEdition/js/wysiwyg/tinymce_4/plugins/welink/editor_plugin.js',
		weimage: '/webEdition/js/wysiwyg/tinymce_4/plugins/weimage/editor_plugin.js',
		weabbr: '/webEdition/js/wysiwyg/tinymce_4/plugins/weabbr/editor_plugin.js',
		weacronym: '/webEdition/js/wysiwyg/tinymce_4/plugins/weacronym/editor_plugin.js',
		wegallery: '/webEdition/js/wysiwyg/tinymce_4/plugins/wegallery/editor_plugin.js',
		//wecontextmenu: '/webEdition/js/wysiwyg/tinymce_4/plugins/wecontextmenu/editor_plugin.js',
		weutil: '/webEdition/js/wysiwyg/tinymce_4/plugins/weutil/editor_plugin.js',
		weadaptunlink: '/webEdition/js/wysiwyg/tinymce_4/plugins/weadaptunlink/editor_plugin.js',
		weinsertbreak: '/webEdition/js/wysiwyg/tinymce_4/plugins/weinsertbreak/editor_plugin.js',
		wefullscreen: '/webEdition/js/wysiwyg/tinymce_4/plugins/wefullscreen/editor_plugin.js',
		wevisualaid: '/webEdition/js/wysiwyg/tinymce_4/plugins/wevisualaid/editor_plugin.js',
		welang: '/webEdition/js/wysiwyg/tinymce_4/plugins/welang/editor_plugin.js',
		table: '/webEdition/js/wysiwyg/tinymce_4/plugins/wetable/plugin.js',
		xhtmlxtras: '/webEdition/js/wysiwyg/tinymce_4/plugins/xhtmlxtras/editor_plugin.js'
	};

	this.weBlockImportCss = args.weBlockImportCss;
	
	this.style_formats = args.style_formats; // checked
	this.style_formats_autohide = true; // checked
	this.importcss_append = true; // checked
	this.importcss_file_filter = ''; // filled in set
	this.importcss_merge_classes = true; // use this to insert classes from attribute!
	/*
	this.importcss_groups = [
		{title: 'Table styles', filter: /^(td|tr)\./}, // td.class and tr.class
		{title: 'Block styles', filter: /^(div|p)\./}, // div.class and p.class
		{title: 'Other styles'} // The rest
	];
	*/

	this.toolbar = args.weToolbarRows; // checked
	this.accessibility_warnings = false; // checked

	this.popup_css_add = args.popup_css_add;

	this.table_toolbar = args.table_toolbar;
	this.table_appearance_options = false;
	
	this.paste_data_images = false;
	this.paste_data_images = true;
	this.table_tab_navigation = true;
	//table_class_list
	//table_cell_class_list
	//table_row_class_list


	//this.bbcode_dialect = 'punbb';

	/*
	this.body_class = (args.weClassName ? args.weClassName + ' ' : '') + 'wetextarea tiny-wetextarea wetextarea-' + args.weOrigName;

	this.relative_urls = false; //important!
	this.convert_urls = false; //important!
	this.force_p_newlines = 0; // value 0 instead of true (!) prevents adding additional lines with <p>&nbsp</p> when inlineedit="true"
	this.entity_encoding = 'named';
	this.entities = '160,nbsp';
	this.fix_list_elements = true;
	this.element_format = args.element_format;
	this.theme_advanced_toolbar_location = args.theme_advanced_toolbar_location; //external: toolbar floating on top of textarea
	this.theme_advanced_toolbar_align = 'left';
	
	
	
	*/
   
	this.weFormatselects = args.weFormatselects;
	this.font_formats = args.weFormatselects.toolbarSettings.fonts;
	this.fontsize_formats = args.weFormatselects.toolbarSettings.fontsizes;

	/*
	this.theme_advanced_styles = args.theme_advanced_styles;
	this.theme_advanced_blockformats = args.theme_advanced_blockformats;
	//this.theme_advanced_statusbar_location = args.theme_advanced_statusbar_location;
	this.theme_advanced_default_foreground_color = '#FF0000';
	this.theme_advanced_default_background_color = '#FFFF99';
	this.plugin_preview_height = '300';
	this.plugin_preview_width = '500';
	this.theme_advanced_disable = '';
	*/
	this.extended_valid_elements = 'we-gallery[id|tmpl|class],@[we-tiny]';
	this.custom_elements = 'we-gallery';

	this.visual = true;
	//this.paste_text_sticky = true; // obsolete
	this.paste_auto_cleanup_on_paste = true;
	this.template_templates = args.template_templates;
	//this.force_br_newlines = true;
	//this.forced_root_block = '';
	
	
	this.codesample_dialog_width = 600;
	this.codesample_languages = [
        {text: 'HTML/XML', value: 'markup'},
        {text: 'JavaScript', value: 'javascript'},
        {text: 'CSS', value: 'css'},
        {text: 'PHP', value: 'php'},
        {text: 'Ruby', value: 'ruby'},
        {text: 'Python', value: 'python'},
        {text: 'Java', value: 'java'},
        {text: 'C', value: 'c'},
        {text: 'C#', value: 'csharp'},
        {text: 'C++', value: 'cpp'}
    ],

	this.setup = WE().layout.we_tinyMCE.setupEditor;
	
	
}

WE().layout.we_tinyMCE.setupEditor = function(ed){
	var conf = ed.settings;
	

top.console.log('block', conf.weBlockImportCss);
	//if(!conf.weBlockImportCss){
		conf.plugins += ' importcss';
	//}
	if(!conf.menu){
		conf.menubar = false;
	}

	WE().layout.we_tinyMCE.setup.wePasteFromTiny(ed);
	WE().layout.we_tinyMCE.functions.setContentCss(ed);
	WE().layout.we_tinyMCE.functions.setTinyParams(ed);
	WE().layout.we_tinyMCE.functions.setSyncHot(ed);

	WE().layout.we_tinyMCE.setup.addMissingMenuItems(ed);


	ed.on('KeyDown', function(e){WE().layout.we_tinyMCE.do.onKeyDown(ed, e);});
	ed.on('DblClick', function(e){WE().layout.we_tinyMCE.do.onDblClick(ed, e);});
	ed.on('PostProcess', function(e){WE().layout.we_tinyMCE.do.onPostProcess(ed, e);});
	ed.on('NodeChange', function(e){WE().layout.we_tinyMCE.do.onNodeChange(ed, e);});
		

	if(!conf.weIsFrontendEdit && conf.weSynchronizeHot.doSyncHot){
		conf.weSynchronizeHot.doOnChange = ed.once('change', function(e){WE().layout.we_tinyMCE.do.onChange(ed, e);});
		ed.on('SaveContent', function(e){WE().layout.we_tinyMCE.do.onSaveContent(ed, e);});
	}


	ed.on('Init', function(e){WE().layout.we_tinyMCE.onInitEditor(ed, e);});
};

WE().layout.we_tinyMCE.onInitEditor = function(ed){
	var conf = ed.settings;
top.console.log(conf.wePluginClasses);
top.console.log('FormatControls', conf.weWin.tinymce.ui.FormatControls);
	ed.windowManager.openLegacyPlugin = function(args, params){
		WE().layout.we_tinyMCE.overwrite.openLegacyPlugin.apply(ed.windowManager, [ed, args, params]);
	};

	// move to some fn
	if(ed.menuItems['searchreplace']){
		ed.menuItems['searchreplace'].icon = 'fa fa-search';
	}
	if(ed.menuItems['selectall']){
		ed.menuItems['selectall'].icon = 'fa fa-hand-pointer-o';
	}
	if(ed.buttons['fullscreen']){
		ed.buttons['fullscreen'].icon = 'fa fa-train';
		//ed.buttons['fullscreen'].tooltip = 'Editor auf maximale Größe ziehen'; does not work
	}
	if(ed.menuItems['fullscreen']){
		ed.menuItems['fullscreen'].text = 'Editor auf maximale Größe ziehen';
	}
	if(ed.buttons['codesample'] && ed.menuItems['codesample']){
		ed.menuItems['codesample'].icon = ed.buttons['codesample'].icon;
	}

	//ed.menuItems['formats'].menu.items = [];
	/*
	if(conf.weEditorType === 'fullscreen'){
		ed.controlManager.setDisabled('wefullscreen', false);
	}
	*/

	//ed.execCommand("mceWevisualaid", true);

	// custom node filters
//	ed.serializer.addNodeFilter("a", WE().layout.we_tinyMCE.functions.customNodeFilter_A);

	// add listeners for events ed has no public events for
//	ed.dom.bind(ed.getWin(), 'drop', function(e) {return WE().layout.we_tinyMCE.do.onDrop(e, ed);});


	// when not inlineTrue: get content from opener document => move to fn
	if(conf.weEditorType !== 'inlineTrue'){
		WE().layout.we_tinyMCE.functions.wysiwygDialog_setContent(ed);
	}

	// call custom init if there: we_tinyMCE_FIELDNAME_init(ed)
//	WE().layout.we_tinyMCE.functions.callCustomInit(ed);
};

WE().layout.we_tinyMCE.getTinyConfObject = function(args){
	return new TinyMceConfObject(args);
};
