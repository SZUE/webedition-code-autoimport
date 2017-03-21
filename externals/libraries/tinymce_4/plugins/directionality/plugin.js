/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.PluginManager.add('directionality', function(editor) {
	function setDir(dir) {
		var dom = editor.dom, curDir, blocks = editor.selection.getSelectedBlocks();

		if (blocks.length) {
			curDir = dom.getAttrib(blocks[0], "dir");

			tinymce.each(blocks, function(block) {
				// Add dir to block if the parent block doesn't already have that dir
				if (!dom.getParent(block.parentNode, "*[dir='" + dir + "']", dom.getRoot())) {
					if (curDir != dir) {
						dom.setAttrib(block, "dir", dir);
					} else {
						dom.setAttrib(block, "dir", null);
					}
				}
			});

			editor.nodeChanged();
		}
	}

	function generateSelector(dir) {
		var selector = [];

		tinymce.each('h1 h2 h3 h4 h5 h6 div p'.split(' '), function(name) {
			selector.push(name + '[dir=' + dir + ']');
		});

		return selector.join(',');
	}

	editor.addCommand('mceDirectionLTR', function() {
		setDir("ltr");
	});

	editor.addCommand('mceDirectionRTL', function() {
		setDir("rtl");
	});

	editor.addButton('ltr', {
		title: 'Left to right',
		cmd: 'mceDirectionLTR',
		stateSelector: generateSelector('ltr')
	});

	editor.addButton('rtl', {
		title: 'Right to left',
		cmd: 'mceDirectionRTL',
		stateSelector: generateSelector('rtl')
	});

	// IMI: move to custom plugs or add these menues editor.onInit
	editor.addMenuItem('rtl', {
		text: 'Right to left',
		icon: 'fa fa-paragraph',
		context: 'xhtml',
		cmd: 'rtl',
		onPostRender: function() {
			var ctrl = this;
			editor.on('NodeChange', function(e) {
				ctrl.active(false);
				ctrl.disabled(false);

				var n = e.element;
				if (n && n.nodeName) { // we check for lang spans recursive
					do {
						if(n.nodeName.toLowerCase() === 'p' && n.getAttribute('dir') && n.getAttribute('dir') !== 'rtl'){
							ctrl.active(true);
							break;
						}
					} while ((n = n.parentNode));
				}
			});
		}
	});

	editor.addMenuItem('ltr', {
		text: 'Left to right',
		icon: 'fa fa-paragraph',
		context: 'xhtml',
		cmd: 'ltr',
		onPostRender: function() {
			var ctrl = this;
			editor.on('NodeChange', function(e) {
				ctrl.active(false);
				ctrl.disabled(false);

				var n = e.element;
				if (n && n.nodeName) { // we check for lang spans recursive
					do {
						if(n.nodeName.toLowerCase() === 'p' && n.getAttribute('dir') && n.getAttribute('dir') !== 'ltr'){
							ctrl.active(true);
							break;
						}
					} while ((n = n.parentNode));
				}
			});
		}
	});
});