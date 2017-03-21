/**
 * FormatControls.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * Internal class containing all TinyMCE specific control types such as
 * format listboxes, fontlist boxes, toolbar buttons etc.
 *
 * @class tinymce.ui.FormatControls
 */
tinymce.ui.FormatControls = function(Control, Widget, FloatPanel, Tools, Arr, DOMUtils, EditorManager, Env, FontInfo) {
	var each = Tools.each;

	var flatten = function (ar) {
		return Arr.reduce(ar, function (result, item) {
			return result.concat(item);
		}, []);
	};

	EditorManager.on('AddEditor', function(e) {
		var editor = e.editor;

		setupRtlMode(editor);
		registerControls(editor);
		setupContainer(editor);
	});

	Control.translate = function(text) {
		return EditorManager.translate(text);
	};

	Widget.tooltips = !Env.iOS;

	function setupContainer(editor) {
		if (editor.settings.ui_container) {
			Env.container = DOMUtils.DOM.select(editor.settings.ui_container)[0];
		}
	}

	function setupRtlMode(editor) {
		editor.on('ScriptsLoaded', function () {
			if (editor.rtl) {
				Control.rtl = true;
			}
		});
	}

	function registerControls(editor) {
		var formatMenu;

		function createListBoxChangeHandler(items, formatName) {
			return function() {
				var self = this;

				editor.on('nodeChange', function(e) {
					var formatter = editor.formatter;
					var value = null;

					each(e.parents, function(node) {
						each(items, function(item) {
							if (formatName) {
								if (formatter.matchNode(node, formatName, {value: item.value})) {
									value = item.value;
								}
							} else {
								if (formatter.matchNode(node, item.value)) {
									value = item.value;
								}
							}

							if (value) {
								return false;
							}
						});

						if (value) {
							return false;
						}
					});

					self.value(value);
				});
			};
		}

		function createFontNameListBoxChangeHandler(items) {
			return function() {
				var self = this;

				var getFirstFont = function (fontFamily) {
					return fontFamily ? fontFamily.split(',')[0] : '';
				};

				editor.on('nodeChange', function(e) {
					var fontFamily, value = null;

					fontFamily = FontInfo.getFontFamily(editor.getBody(), e.element);

					each(items, function(item) {
						if (item.value.toLowerCase() === fontFamily.toLowerCase()) {
							value = item.value;
						}
					});

					each(items, function(item) {
						if (!value && getFirstFont(item.value).toLowerCase() === getFirstFont(fontFamily).toLowerCase()) {
							value = item.value;
						}
					});

					self.value(value);

					if (!value && fontFamily) {
						self.text(getFirstFont(fontFamily));
					}
				});
			};
		}

		function createFontSizeListBoxChangeHandler(items) {
			return function() {
				var self = this;

				editor.on('nodeChange', function(e) {
					var px, pt, value = null;

					px = FontInfo.getFontSize(editor.getBody(), e.element);
					pt = FontInfo.toPt(px);

					each(items, function(item) {
						if (item.value === px) {
							value = px;
						} else if (item.value === pt) {
							value = pt;
						}
					});

					self.value(value);

					if (!value) {
						self.text(pt);
					}
				});
			};
		}

		function createFormats(formats) {
			formats = formats.replace(/;$/, '').split(';');

			var i = formats.length;
			while (i--) {
				formats[i] = formats[i].split('=');
			}

			return formats;
		}
		
		//var count = 0, newFormats = [];

		function createFormatMenu(whatMenu, settings) {top.console.log('createFormatMenu', whatMenu, settings);
			var count = 0, newFormats = [];

			function createMenu(whatMenu, formats) {
				var menu = [];

				if (!formats) {
					return;
				}

				each(formats, function(format) {
					var menuItem = {
						text: format.title,
						icon: format.icon
					};

					if (format.items) {
						menuItem.menu = createMenu(whatMenu, format.items);
					} else {
						var formatName = format.format || "custom" + (whatMenu && whatMenu !== 'formats' ? whatMenu : '') + count++;

						if (!format.format) {
							format.name = formatName;
							newFormats.push(format);
						}

						menuItem.format = formatName;
						menuItem.cmd = format.cmd;
						if(whatMenu && whatMenu !== 'formats'){
							menuItem.preview = true;
							menuItem.textStyle = item_TextStyle;
							menuItem.onclick = item_onclick;
							menuItem.onPostRender = item_onPostRender;
						}
					}

					menu.push(menuItem);
				});

				return menu;
			}

			/* WE: replaced
			function createStylesMenu() {
				var menu;

				if (editor.settings.style_formats_merge) {
					if (editor.settings.style_formats) {
						menu = createMenu(defaultStyleFormats.concat(editor.settings.style_formats));
					} else {
						menu = createMenu(defaultStyleFormats);
					}
				} else {
					menu = createMenu(editor.settings.style_formats || defaultStyleFormats);
				}

				return menu;
			}
			*/

			function createStylesMenu(whatMenu, settings) {
				switch(whatMenu){
					case 'fontselect':
					case 'fontsizeselect': 
					case 'headers':
					case 'blocks':
						return createMenu(whatMenu, settings);
					case 'formats':
					default:
						return createMenu('formats', settings);
				}
			}

			editor.on('init', function() {
				each(newFormats, function(format) {
					editor.formatter.register(format.name, format);
				});
			});

			var items = createStylesMenu(whatMenu, settings);

			// WE: added
			if(whatMenu && whatMenu !== 'formats'){
				return {
						whatMenu: whatMenu ? whatMenu : 'styles',
						type: 'menu',
						items: items
					};
			}

			// WE: added
			function item_TextStyle() {
				if (this.settings.format) {
					return editor.formatter.getCssText(this.settings.format);
				}
			}

			// WE: added
			function item_onPostRender() {
				var self = this;

				self.parent().on('show', function() {
					var formatName, command;

					formatName = self.settings.format;
					if (formatName) {
						self.disabled(!editor.formatter.canApply(formatName));
						self.active(editor.formatter.match(formatName));
					}

					command = self.settings.cmd;
					if (command) {
						self.active(editor.queryCommandState(command));
					}
				});
			}

			// WE: added
			function item_onclick() {
				if (this.settings.format) {
					toggleFormat(this.settings.format);
				}

				if (this.settings.cmd) {
					editor.execCommand(this.settings.cmd);
				}
			}

			/* replaced
			return {
				type: 'menu',
				items: createStylesMenu(),
				onPostRender: function(e) {
					editor.fire('renderFormatsMenu', {control: e.control});
				},
				itemDefaults: {
					preview: true,

					textStyle: function() {
						if (this.settings.format) {
							return editor.formatter.getCssText(this.settings.format);
						}
					},

					onPostRender: function() {
						var self = this;

						self.parent().on('show', function() {
							var formatName, command;

							formatName = self.settings.format;
							if (formatName) {
								self.disabled(!editor.formatter.canApply(formatName));
								self.active(editor.formatter.match(formatName));
							}

							command = self.settings.cmd;
							if (command) {
								self.active(editor.queryCommandState(command));
							}
						});
					},

					onclick: function() {
						if (this.settings.format) {
							toggleFormat(this.settings.format);
						}

						if (this.settings.cmd) {
							editor.execCommand(this.settings.cmd);
						}
					}
				}
			};
			 */

			return {
				type: 'menu',
				items: items,
				onPostRender: function(e) {
					editor.fire('renderFormatsMenu', {control: e.control});
				},
				itemDefaults: {
					preview: true,
					textStyle: item_TextStyle,
					onPostRender: item_onPostRender,
					onclick: item_onclick
				}
			};
		}

		function initOnPostRender(name) {
			return function() {
				var self = this;

				// TODO: Fix this
				if (editor.formatter) {
					editor.formatter.formatChanged(name, function(state) {
						self.active(state);
					});
				} else {
					editor.on('init', function() {
						editor.formatter.formatChanged(name, function(state) {
							self.active(state);
						});
					});
				}
			};
		}

		// Simple format controls <control/format>:<UI text>
		each({
			bold: 'Bold',
			italic: 'Italic',
			underline: 'Underline',
			strikethrough: 'Strikethrough',
			subscript: 'Subscript',
			superscript: 'Superscript'
		}, function(text, name) {
			editor.addButton(name, {
				tooltip: text,
				onPostRender: initOnPostRender(name),
				onclick: function() {
					toggleFormat(name);
				}
			});
		});

		// Simple command controls <control>:[<UI text>,<Command>]
		each({
			outdent: ['Decrease indent', 'Outdent'],
			indent: ['Increase indent', 'Indent'],
			cut: ['Cut', 'Cut'],
			copy: ['Copy', 'Copy'],
			paste: ['Paste', 'Paste'],
			help: ['Help', 'mceHelp'],
			selectall: ['Select all', 'SelectAll'],
			removeformat: ['Clear formatting', 'RemoveFormat'],
			visualaid: ['Visual aids', 'mceToggleVisualAid'],
			newdocument: ['New document', 'mceNewDocument']
		}, function(item, name) {
			editor.addButton(name, {
				tooltip: item[0],
				cmd: item[1]
			});
		});

		// Simple command controls with format state
		each({
			blockquote: ['Blockquote', 'mceBlockQuote'],
			subscript: ['Subscript', 'Subscript'],
			superscript: ['Superscript', 'Superscript'],
			alignleft: ['Align left', 'JustifyLeft'],
			aligncenter: ['Align center', 'JustifyCenter'],
			alignright: ['Align right', 'JustifyRight'],
			alignjustify: ['Justify', 'JustifyFull'],
			alignnone: ['No alignment', 'JustifyNone']
		}, function(item, name) {
			editor.addButton(name, {
				tooltip: item[0],
				cmd: item[1],
				onPostRender: initOnPostRender(name)
			});
		});

		function toggleUndoRedoState(type) {
			return function() {
				var self = this;

				function checkState() {
					var typeFn = type === 'redo' ? 'hasRedo' : 'hasUndo';
					return editor.undoManager ? editor.undoManager[typeFn]() : false;
				}

				self.disabled(!checkState());
				editor.on('Undo Redo AddUndo TypingUndo ClearUndos SwitchMode', function() {
					self.disabled(editor.readonly || !checkState());
				});
			};
		}

		function toggleVisualAidState() {
			var self = this;

			editor.on('VisualAid', function(e) {
				self.active(e.hasVisual);
			});

			self.active(editor.hasVisual);
		}

		var trimMenuItems = function (menuItems) {
			var outputMenuItems = menuItems;

			if (outputMenuItems.length > 0 && outputMenuItems[0].text === '-') {
				outputMenuItems = outputMenuItems.slice(1);
			}

			if (outputMenuItems.length > 0 && outputMenuItems[outputMenuItems.length - 1].text === '-') {
				outputMenuItems = outputMenuItems.slice(0, outputMenuItems.length - 1);
			}

			return outputMenuItems;
		};

		var createCustomMenuItems = function (names) {
			var items, nameList;

			if (typeof names === 'string') {
				nameList = names.split(' ');
			} else if (Tools.isArray(names)) {
				return flatten(Tools.map(names, createCustomMenuItems));
			}

			items = Tools.grep(nameList, function (name) {
				return name === '|' || name in editor.menuItems;
			});

			return Tools.map(items, function (name) {
				return name === '|' ? {text: '-'} : editor.menuItems[name];
			});
		};

		var createContextMenuItems = function (context) {
			var outputMenuItems = [{text: '-'}];
			var menuItems = Tools.grep(editor.menuItems, function (menuItem) {
				return menuItem.context === context;
			});

			Tools.each(menuItems, function (menuItem) {
				if (menuItem.separator === 'before') {
					outputMenuItems.push({text: '|'});
				}

				if (menuItem.prependToContext) {
					outputMenuItems.unshift(menuItem);
				} else {
					outputMenuItems.push(menuItem);
				}

				if (menuItem.separator === 'after') {
					outputMenuItems.push({text: '|'});
				}
			});

			return outputMenuItems;
		};

		var createInsertMenu = function (editorSettings) {
			if (editorSettings.insert_button_items) {
				return trimMenuItems(createCustomMenuItems(editorSettings.insert_button_items));
			} else {
				return trimMenuItems(createContextMenuItems('insert'));
			}
		};

		editor.addButton('undo', {
			tooltip: 'Undo',
			onPostRender: toggleUndoRedoState('undo'),
			cmd: 'undo'
		});

		editor.addButton('redo', {
			tooltip: 'Redo',
			onPostRender: toggleUndoRedoState('redo'),
			cmd: 'redo'
		});

		editor.addMenuItem('newdocument', {
			text: 'New document',
			icon: 'newdocument',
			cmd: 'mceNewDocument'
		});

		editor.addMenuItem('undo', {
			text: 'Undo',
			icon: 'undo',
			shortcut: 'Meta+Z',
			onPostRender: toggleUndoRedoState('undo'),
			cmd: 'undo'
		});

		editor.addMenuItem('redo', {
			text: 'Redo',
			icon: 'redo',
			shortcut: 'Meta+Y',
			onPostRender: toggleUndoRedoState('redo'),
			cmd: 'redo'
		});

		editor.addMenuItem('visualaid', {
			text: 'Visual aids',
			selectable: true,
			onPostRender: toggleVisualAidState,
			cmd: 'mceToggleVisualAid'
		});

		editor.addButton('remove', {
			tooltip: 'Remove',
			icon: 'remove',
			cmd: 'Delete'
		});

		editor.addButton('insert', {
			type: 'menubutton',
			icon: 'insert',
			menu: [],
			oncreatemenu: function () {
				this.menu.add(createInsertMenu(editor.settings));
				this.menu.renderNew();
			}
		});

		each({
			cut: ['Cut', 'Cut', 'Meta+X'],
			copy: ['Copy', 'Copy', 'Meta+C'],
			paste: ['Paste', 'Paste', 'Meta+V'],
			selectall: ['Select all', 'SelectAll', 'Meta+A'],
			bold: ['Bold', 'Bold', 'Meta+B'],
			italic: ['Italic', 'Italic', 'Meta+I'],
			underline: ['Underline', 'Underline', 'Meta+U'],
			strikethrough: ['Strikethrough', 'Strikethrough'],
			subscript: ['Subscript', 'Subscript'],
			superscript: ['Superscript', 'Superscript'],
			removeformat: ['Clear formatting', 'RemoveFormat']
		}, function(item, name) {
			editor.addMenuItem(name, {
				text: item[0],
				icon: name,
				shortcut: item[2],
				cmd: item[1]
			});
		});

		editor.on('mousedown', function() {
			FloatPanel.hideAll();
		});

		function toggleFormat(fmt) {
			if (fmt.control) {
				fmt = fmt.control.value();
			}

			if (fmt) {
				editor.execCommand('mceToggleFormat', false, fmt);
			}
		}

		function hideMenuObjects(menu) {
			var count = menu.length;

			Tools.each(menu, function (item) {
				if (item.menu) {
					item.hidden = hideMenuObjects(item.menu) === 0;
				}

				var formatName = item.format;
				if (formatName) {
					item.hidden = !editor.formatter.canApply(formatName);
				}

				if (item.hidden) {
					count--;
				}
			});

			return count;
		}

		function hideFormatMenuItems(menu) {
			var count = menu.items().length;

			menu.items().each(function (item) {
				if (item.menu) {
					item.visible(hideFormatMenuItems(item.menu) > 0);
				}

				if (!item.menu && item.settings.menu) {
					item.visible(hideMenuObjects(item.settings.menu) > 0);
				}

				var formatName = item.settings.format;
				if (formatName) {
					item.visible(editor.formatter.canApply(formatName));
				}

				if (!item.visible()) {
					count--;
				}
			});

			return count;
		}

		editor.addButton('styleselect', {
			type: 'menubutton',
			text: 'Formats',
			menu: formatMenu,
			onShowMenu: function () {
				if (editor.settings.style_formats_autohide) {
					hideFormatMenuItems(this.menu);
				}
			}
		});

		editor.addButton('formatselect', function() {
			var items = [], blocks = createFormats(editor.settings.block_formats ||
				'Paragraph=p;' +
				'Heading 1=h1;' +
				'Heading 2=h2;' +
				'Heading 3=h3;' +
				'Heading 4=h4;' +
				'Heading 5=h5;' +
				'Heading 6=h6;' +
				'Preformatted=pre'
			);

			each(blocks, function(block) {
				items.push({
					text: block[0],
					value: block[1],
					textStyle: function() {
						return editor.formatter.getCssText(block[1]);
					}
				});
			});

			return {
				type: 'listbox',
				text: blocks[0][0],
				values: items,
				fixedWidth: true,
				onselect: toggleFormat,
				onPostRender: createListBoxChangeHandler(items)
			};
		});

		editor.addButton('fontselect', function() {
			var defaultFontsFormats = '';
			var items = [], fonts = createFormats(editor.settings.font_formats || defaultFontsFormats);

			each(fonts, function(font) {
				items.push({
					text: {raw: font[0]},
					value: font[1],
					textStyle: font[1].indexOf('dings') === -1 ? 'font-family:' + font[1] : ''
				});
			});

			return {
				type: 'listbox',
				text: 'Font Family',
				tooltip: 'Font Family',
				values: items,
				fixedWidth: true,
				onPostRender: createFontNameListBoxChangeHandler(items),
				onselect: function(e) {
					if (e.control.settings.value) {
						editor.execCommand('FontName', false, e.control.settings.value);
					}
				}
			};
		});

		editor.addButton('fontsizeselect', function() {
			var items = [], defaultFontsizeFormats = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';
			var fontsize_formats = editor.settings.fontsize_formats || defaultFontsizeFormats;

			each(fontsize_formats.split(' '), function(item) {
				var text = item, value = item;
				// Allow text=value font sizes.
				var values = item.split('=');
				if (values.length > 1) {
					text = values[0];
					value = values[1];
				}
				items.push({text: text, value: value});
			});

			return {
				type: 'listbox',
				text: 'Font Sizes',
				tooltip: 'Font Sizes',
				values: items,
				fixedWidth: true,
				onPostRender: createFontSizeListBoxChangeHandler(items),
				onclick: function(e) {
					if (e.control.settings.value) {
						editor.execCommand('FontSize', false, e.control.settings.value);
					}
				}
			};
		});

/*
weFormatselects = {
	menus: ['fonts', 'fontsizes', 'headers', 'blocks', 'styles'],
	menuSettings : {
		fonts: {},
		fontsizes: {},
		headers: {},
		blocks: {},
	}

 */
top.console.log('all settings', editor.settings.weFormatselects);
		var i, menuId, menu;
		var settings = editor.settings.weFormatselects;

		for (i = 0; i < settings.menus.length; i++) {
			menuId = settings.menus[i];
			if(menuId !== 'styles'){
				menu = createFormatMenu(menuId, settings.menuSettings[menuId]);top.console.log('c', menu);
				if(menu){
					editor.addMenuItem(menuId, {
						text: settings.menuSettings[menuId][0].title,
						menu: menu.items[0].menu,
						context: 'format'
					});
				}
			} else {
				// no need for own processing
				menu = createFormatMenu('formats', settings.menuSettings[menuId].items);
				if(menu){
					editor.addMenuItem('formats', {
						text: settings.menuSettings[menuId].title,
						menu: menu,
						context: 'format'
					});
				}
			}
		}
	}
};

// added
tinymce.ui.FormatControls(tinymce.ui.Control,
	tinymce.ui.Widget,
	tinymce.ui.FloatPanel,
	tinymce.util.Tools,
	tinymce.util.Arr,
	tinymce.dom.DOMUtils,
	tinymce.EditorManager,
	tinymce.Env//,
	//tinymce.fmt.FontInfo
);
