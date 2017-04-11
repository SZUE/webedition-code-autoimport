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

/**
 * This source is based on tinyMCE-plugin "contextmenu":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */

(function () {
	var Event = tinymce.dom.Event, each = tinymce.each, DOM = tinymce.DOM;

	/**
	 * This plugin a context menu to TinyMCE editor instances.
	 *
	 * @class tinymce.plugins.ContextMenu
	 */
	tinymce.create('tinymce.plugins.WeContextMenu', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @method init
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init: function (ed) {
			var t = this, showMenu, contextmenuNeverUseNative, realCtrlKey, hideMenu;

			t.editor = ed;

			contextmenuNeverUseNative = ed.settings.contextmenu_never_use_native;

			/**
			 * This event gets fired when the context menu is shown.
			 *
			 * @event onContextMenu
			 * @param {tinymce.plugins.ContextMenu} sender Plugin instance sending the event.
			 * @param {tinymce.ui.DropMenu} menu Drop down menu to fill with more items if needed.
			 */
			t.onContextMenu = new tinymce.util.Dispatcher(this);

			hideMenu = function (e) {
				hide(ed, e);
			};

			showMenu = ed.onContextMenu.add(function (ed, e) {
				// Block TinyMCE menu on ctrlKey and work around Safari issue
				if ((realCtrlKey !== 0 ? realCtrlKey : e.ctrlKey) && !contextmenuNeverUseNative) {
					return;
				}

				Event.cancel(e);

				// Select the image if it's clicked. WebKit would other wise expand the selection
				if (e.target.nodeName === 'IMG') {
					ed.selection.select(e.target);
				}

				t._getMenu(ed).showMenu(e.clientX || e.pageX, e.clientY || e.pageY);
				//Event.add(ed.getDoc(), 'click', hideMenu);

				ed.nodeChanged();
			});

			ed.onRemove.add(function () {
				if (t._menu) {
					t._menu.removeAll();
				}
			});

			function hide(ed, e) {
				realCtrlKey = 0;

				// Since the contextmenu event moves
				// the selection we need to store it away
				if (e && e.button === 2) {
					realCtrlKey = e.ctrlKey;
					return;
				}

				if (t._menu) {
					t._menu.removeAll();
					t._menu.destroy();
					Event.remove(ed.getDoc(), 'click', hideMenu);
					t._menu = null;
				}
			}

			ed.onMouseDown.add(hide);
			ed.onKeyDown.add(hide);
			ed.onKeyDown.add(function (ed, e) {
				if (e.shiftKey && !e.ctrlKey && !e.altKey && e.keyCode === 121) {
					Event.cancel(e);
					showMenu(ed, e);
				}
			});
		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @method getInfo
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo: function () {
			return {
				longname: 'WeContextmenu',
				author: 'webEdition e.V',
				authorurl: 'http://www.webedition.org',
				infourl: 'http://www.webedition.org',
				version: ""
			};
		},

		_getMenu: function (ed) {
			var t = this, m = t._menu, se = ed.selection, col = se.isCollapsed(), el = se.getNode() || ed.getBody(), p,
				cm_cmds = {}, c = 0, sep = false, small_cm = false, lastgroup = '', menu_item = null, restrict = ed.settings.weContextmenuCommands,
				item = null, is_active_items = false, active_items = {}, active_groups = {}, set_tableprops_active = false;

			if (m) {
				m.removeAll();
				m.destroy();
			}

			p = DOM.getPos(ed.getContentAreaContainer());

			m = ed.controlManager.createDropMenu('contextmenu', {
				offset_x: p.x + ed.getParam('contextmenu_offset_x', 0),
				offset_y: p.y + ed.getParam('contextmenu_offset_y', 0),
				constrain: 1,
				keyboard_focus: true
			});

			t._menu = m;

			function getParent(pa, sel) {
				sel = (sel !== undefined ? sel : false);
				if (sel && groups[pa][2] === 'top') {
					return m;
				}
				return (groups[pa][0] = groups[pa][0] ? groups[pa][0] : getParent(groups[pa][2], sel).addMenu({title: groups[pa][1]}));
			}

			var groups = {
				'top': [m, '', ''],
				//font: not in cm
				'prop': [null, 'advanced.style_select', 'top'],
				'xhtmlxtras': [null, 'we.group_xhtml', 'top'],
				//color: not in cm
				'justify': [null, 'contextmenu.align', 'top'],
				'list': [null, 'we.group_indent', 'top'],
				'link': [null, 'we.group_link', 'top'],
				'table': [null, 'we.group_table', 'top'],
				'insert': [null, 'we.group_insert', 'top'],
				'copypaste': [null, 'we.group_copypaste', 'top'],
				'layer': [null, 'we.group_layer', 'top'],
				//'essential' : [null,'we.group_essential','top'],//on top
				'advanced': [null, 'we.group_advanced', 'top'],
				'table_cell': [null, 'table.cell', 'table'],
				'table_row': [null, 'table.row', 'table'],
				'table_col': [null, 'table.col', 'table']
			};

			var langSuffix = ed.settings.language === 'de' ? 'de_' : '';

			var items = [];
			items.push(
				//['weadaptbold', ed.settings.wePluginClasses.weadaptbold, 'prop'],
				//['weadaptitalic', ed.settings.wePluginClasses.weadaptitalic, 'prop'],
				['bold', 'bold', 'prop'],
				['italic', 'bold', 'prop'],
				['underline', 'underline', 'prop'],
				['strikethrough', 'strikethrough', 'prop'],
				['separator', '', 'prop', true, true],
				['sub', 'sub', 'prop'],
				['sup', 'sup', 'prop'],
				['separator', '', 'prop', true, true],
				['styleprops', 'styleprops', 'prop'],
				['removeformat', 'removeformat', 'prop'],
				['cleanup', 'cleanup', 'prop'],
				['separator', '', 'prop', false, true],
				['cite', 'cite', 'xhtmlxtras'],
				['weacronym', ed.settings.wePluginClasses.weacronym, 'xhtmlxtras'],
				['weabbr', ed.settings.wePluginClasses.weabbr, 'xhtmlxtras'],
				['welang', 'welang', 'xhtmlxtras'],
				['separator', '', 'xhtmlxtras', true, true],
				['del', 'del', 'xhtmlxtras'],
				['ins', 'ins', 'xhtmlxtras'],
				['separator', '', 'xhtmlxtras', true, true],
				['ltr', 'ltr', 'xhtmlxtras'],
				['rtl', 'rtl', 'xhtmlxtras'],
				['separator', '', 'xhtmlxtras', false, true],
				['justifyleft', 'justifyleft', 'justify'],
				['justifycenter', 'justifycenter', 'justify'],
				['justifyright', 'justifyright', 'justify'],
				['justifyfull', 'justifyfull', 'justify'],
				['separator', '', 'justify', false, true],
				//['insertunorderedlist','insertunorderedlist','list'],
					//['insertorderedlist','insertorderedlist','list'],
						['indent', 'indent', 'list'],
						['outdent', 'outdent', 'list'],
						['blockquote', 'blockquote', 'list'],
						['separator', '', 'list', false, true],
						['welink', 'welink', 'link'],
						['weadaptunlink', 'unlink', 'link'],
						['anchor', 'anchor', 'link'],
						['separator', '', 'link', false, true],
						['table', 'table', 'table', 'we.cm_inserttable', {action: 'insert'}],
						['table', 'table_props', 'table', 'we.cm_table_props'],
						['delete_table', 'delete_table', 'table'],
						['cell_props', 'cell_props', 'table_cell'],
						['split_cells', 'split_cells', 'table_cell'],
						['merge_cells', 'merge_cells', 'table_cell'],
						['row_props', 'row_props', 'table_row'],
						['row_before', 'row_before', 'table_row'],
						['row_after', 'row_after', 'table_row'],
						['delete_row', 'delete_row', 'table_row'],
						['col_before', 'col_before', 'table_col'],
						['col_after', 'col_after', 'table_col'],
						['delete_col', 'delete_col', 'table_col'],
						['separator', '', 'table', false, true],
						['weimage', 'weimage', 'insert'],
						['separator', '', 'insert', true, true],
						['hr', 'hr', 'insert'],
						['advhr', 'advhr', 'insert'],
						['separator', '', 'insert', true, true],
						['charmap', 'charmap', 'insert'],
						['weinsertbreak', 'weinsertbreak', 'insert'],
						['separator', '', 'insert', true, true],
						['insertdate', 'insertdate', 'insert'],
						['inserttime', 'inserttime', 'insert'],
						['separator', '', 'insert', false, true],
						['pastetext', 'pastetext', 'copypaste'],
						['pasteword', 'pasteword', 'copypaste'],
						['separator', '', 'copypaste', false, true],
						['insertlayer', 'insertlayer', 'layer'],
						['absolute', 'absolute', 'layer'],
						['movebackward', 'backward', 'layer'],
						['moveforward', 'forward', 'layer'],
						['separator', '', 'top', true, true],
						['code', 'code', 'advanced'],
						['template', 'template', 'advanced'],
						['wevisualaid', 'wevisualaid', 'advanced'],
						['wefullscreen', 'wefullscreen', 'advanced'],
						['separator', '', 'top', true, true],
						['undo', 'undo', 'top'],
						['redo', 'redo', 'top'],
						['selectall', 'selectall', 'top'],
						['replace', 'replace', 'top'],
						['separator', '', 'top', false, true]
						);
					//verify commands, restrict cm-items when param weContextmenuCommands not empty, and count cm-items
					for (var i = 0; i < items.length; i++) {
						if (ed.buttons[items[i][0]] && (!restrict || restrict[items[i][0]])) {
						//if (ed.controlManager.controls[ed.editorId + '_' + items[i][0]] && (!restrict || restrict[items[i][0]])) {
							cm_cmds[items[i][0]] = true;
							c++;
						}
					}
					//small_cm = c < (ed.controlManager.controls[ed.editorId + '_table'] ? 25 : 15) ? true : false;
					small_cm = c < (ed.buttons.table ? 25 : 15) ? true : false;

					//correct wrong state of unlink
					if (ed.controlManager.buttons.unlink) {
						ed.controlManager.buttons.unlink.state.data.active = false;
					}

					//set rules for active items and folders
					//if (ed.controlManager.controls[ed.editorId + '_table'] && ed.controlManager.controls[ed.editorId + '_table'].active) {
						//ed.controlManager.controls[ed.editorId + '_table'].active = 0;
					if (ed.controlManager.buttons.table && ed.controlManager.buttons.table.state.data.active) {
						ed.controlManager.controls[ed.editorId + '_table'].active = 0;
						set_tableprops_active = true;
						active_groups.table = true;
						active_groups.table_col = true;
						active_groups.table_cell = true;
						active_groups.table_row = true;
					}
	/*
					if ((ed.controlManager.controls[ed.editorId + '_moveforward'] && !ed.controlManager.controls[ed.editorId + '_moveforward'].disabled) ||
						(ed.controlManager.controls[ed.editorId + '_movebackward'] && !ed.controlManager.controls[ed.editorId + '_movebackward'].disabled)) {
						active_groups.layer = true;
					}
	*/top.console.log('bold', ed.controlManager.buttons.bold);
					if ((ed.controlManager.buttons.justifyleft && ed.controlManager.buttons.justifyleft.state.data.active) ||
						(ed.controlManager.buttons.justifycenter && ed.controlManager.buttons.justifycenter.state.data.active) ||
						(ed.controlManager.buttons.justifyright && ed.controlManager.buttons.justifycenter.state.data.active) ||
						(ed.controlManager.buttons.justifyfull && ed.controlManager.buttons.justifycenter.state.data.active)) {
						active_groups.justify = true;
					}
					if (ed.controlManager.buttons.welink && ed.controlManager.buttons.welink.state.data.active) {
						active_items.weadaptunlink = true;
					}
					if (ed.controlManager.buttons.outdent && ed.controlManager.buttons.outdent.state.data.active) {
						active_items.indent = true;
						active_items.outdent = true;
					}
					if (ed.controlManager.buttons.undo && !ed.controlManager.buttons.undo.state.data.disabled) {
						active_items.undo = true;
					}
					if (ed.controlManager.buttons.redo && !ed.controlManager.buttons.redo.state.data.disabled) {
						active_items.redo = true;
					}

					//display top menu
					for (var i = 0; i < items.length; i++) {
						if (items[i][0] in cm_cmds) {
							menu_item = ed.controlManager.buttons[items[i][0]];
							if ((small_cm || menu_item && items[i][2] !== 'top') && (small_cm || menu_item.state.data.active || items[i][0] in active_items || items[i][2] in active_groups)) {
								item = getParent(items[i][2], true).add({title: items[i][3] ? items[i][3] : menu_item.settings.title, icon: items[i][1], cmd: menu_item.settings.cmd});
								item.setActive(menu_item.active || (items[i][1] === 'table_props' && set_tableprops_active));
								item.setDisabled(menu_item.disabled || (items[i][1] === 'table_props' && !set_tableprops_active));
								is_active_items = true;
								sep = true;
							}
						} else if (items[i][0] === 'separator' && items[i][4] === true && sep) {
							m.addSeparator();
							sep = false;
						}
					}
					for (var group in groups) {
						groups[group][0] = null;
					}
					groups.top[0] = m;
					if (is_active_items === true && sep) {
						m.addSeparator();
					}

					//display standard menu
					var separator = [];
					sep = false;
					if (!small_cm) {
						for (i = 0; i < items.length; i++) {
							if (items[i][0] in cm_cmds) {
								if (separator && sep && (separator[2] === 'top' || separator[2] === lastgroup)) {
									getParent(separator[2]).addSeparator();
									separator = [];
								}
								menu_item = ed.controlManager.buttons[items[i][0]];
								if(menu_item){
									item = getParent(items[i][2]).add({title: items[i][3] ? items[i][1] : menu_item.settings.title, icon: items[i][1], cmd: menu_item.settings.cmd, value: (!items[i][3] ? null : items[i][4])});
									item.setActive(menu_item.active || (items[i][1] === 'table_props' && set_tableprops_active));
									item.setDisabled(menu_item.disabled || (items[i][1] === 'table_props' && !set_tableprops_active));
									lastgroup = items[i][2];
									sep = true;
								}
							} else if (items[i][0] === 'separator' && items[i][3] === true) {
								separator = items[i];
							}
						}
					}

					t.onContextMenu.dispatch(t, m, el, col);

					return m;
				}
			});

			// Register plugin
			tinymce.PluginManager.add('wecontextmenu', tinymce.plugins.WeContextMenu);
		})();
