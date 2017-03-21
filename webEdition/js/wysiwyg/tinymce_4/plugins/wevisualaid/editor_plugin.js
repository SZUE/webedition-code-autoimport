/* global tinymce */
'use strict';
(function() {
	tinymce.create('tinymce.plugins.WevisualaidPlugin', {
		init: function(d) {
			function toggleBorders(t, a) {
				var e = t.getBody();
				var s = t.settings;
				var b = tinymce.each;
				var c = tinymce.DOM;
				var tc = s.visual_table_class ? s.visual_table_class : 'mceItemTable';

				tinymce.execCommand('mceVisualBlocks');
				b(c.select('a,table,acronym,span,abbr', e), function(e) {
					var v;
					switch (e.nodeName) {
						case 'TABLE':
							v = c.getAttrib(e, 'border');
							if (true || !v || v == '0') {
								if (a.hasVisual) {
									c.addClass(e, tc);
								} else {
									c.removeClass(e, tc);
								}
							}
						return;
						case 'A':
							v = c.getAttrib(e, 'name');
							if (v) {
								if (a.hasVisual) {
									c.addClass(e, 'mceItemAnchor');
								} else {
									c.removeClass(e, 'mceItemAnchor');
								}
							}
							return;
						case 'ACRONYM':
							if (a.hasVisual) {
								c.addClass(e, 'mceItemWeAcronym');
							} else {
								c.removeClass(e, 'mceItemWeAcronym');
							}
							return;
						case 'ABBR':
							if (a.hasVisual) {
								c.addClass(e, 'mceItemWeAbbr');
							} else {
								c.removeClass(e, 'mceItemWeAbbr');
							}
							return;
						case 'SPAN':
							v = c.getAttrib(e, 'lang');
							if (v) {
								if (a.hasVisual) {
									c.addClass(e, 'mceItemWeLang');
								} else {
									c.removeClass(e, 'mceItemWeLang');
								}
							}
							return;
					}
				});
			}

			d.addCommand('mceWevisualaid', function() {
				//d.hasVisual = !d.hasVisual;
				toggleBorders(this, d);
			});

			d.addButton('wevisualaid', {
				title: tinymce.i18n.data.de.we.tt_wevisualaid,
				cmd: 'mceWevisualaid',
				icon: 'fa fa-ambulance'
				/*
				onPostRender: function() {
					var ctrl = this;

					d.on('NodeChange', function(e) {
						ctrl.active(a.hasVisual);// a = controlManager: where to get it in 4?
					});
				}
				*/
			});
			d.addMenuItem('wevisualaid', {
				text: tinymce.i18n.data.de.we.tt_wevisualaid,
				cmd: 'mceWevisualaid',
				icon: 'fa fa-ambulance',
				context: 'view'
				/*
				onPostRender: function() {
					var ctrl = this;

					d.on('NodeChange', function(e) {
						ctrl.active(a.hasVisual);// a = controlManager: where to get it in 4?
					});
				}
				*/
			});
			d.onNodeChange.add(function(a, b, n) {
				b.setActive('wevisualaid', a.hasVisual);
			});
		},
		createControl: function(n, a) {
			return null;
		},
		getInfo: function() {
			return {
				longname: 'Wevisualaid plugin',
				author: 'webEdition e.V.',
				authorurl: 'http://webedition.org',
				infourl: 'http://webedition.org',
				version: "1.0"
			};
		}
	});
	tinymce.PluginManager.add('wevisualaid', tinymce.plugins.WevisualaidPlugin);
})();