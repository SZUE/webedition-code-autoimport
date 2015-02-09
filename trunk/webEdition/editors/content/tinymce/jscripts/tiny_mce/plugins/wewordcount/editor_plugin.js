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

/**
 * This source is based on tinyMCE-plugin "wordcount":
 * Moxiecode Systems AB, http://tinymce.moxiecode.com/license.
 */

(function () {
    tinymce.create("tinymce.plugins.WeWordCount", {
        block: 0,
        id: null,
        countre: null,
        cleanre: null,
        init: function (c, d) {
            var e = this,
                f = 0,
                g = tinymce.VK;
            e.countre = c.getParam("wordcount_countregex", /[\w\u2019\'-]+/g);
            e.cleanre = c.getParam("wordcount_cleanregex", /[0-9.(),;:!?%#$?\'\"_+=\\\/-]*/g);
            e.update_rate = c.getParam("wordcount_update_rate", 200);
            e.update_on_delete = c.getParam("wordcount_update_on_delete", true);
            e.id = c.id + "-word-count";
            c.onPostRender.add(function (i, h) {
                var j, k;
                k = i.getParam("wordcount_target_id");
                if (!k) {
                    j = tinymce.DOM.get(i.id + "_path_row");
                    if (j) {
                        tinymce.DOM.add(j.parentNode, "div", {
                            style: "float: right"
                        }, i.getLang("wordcount.words", "Words: ") + '<span id="' + e.id + '">0</span>')
                    }
                } else {
                    tinymce.DOM.add(k, "span", {}, '<span id="' + e.id + '">0</span>')
                }
            });
            c.onInit.add(function (h) {
                h.selection.onSetContent.add(function () {
                    e._count(h)
                });
                e._count(h)
            });
            c.onSetContent.add(function (h) {
                e._count(h)
            });

            function b(h) {
                return h !== f && (h === g.ENTER || f === g.SPACEBAR || a(f))
            }

            function a(h) {
                return h === g.DELETE || h === g.BACKSPACE
            }
            c.onKeyUp.add(function (h, i) {
                if (b(i.keyCode) || e.update_on_delete && a(i.keyCode)) {
                    e._count(h);
                }
                f = i.keyCode
            })
        },
        _getCount: function (c) {
            var a = 0;
            var b = c.getContent({
                format: "raw"
            });
            if (b) {
                b = b.replace(/\.\.\./g, " ");
                b = b.replace(/<.[^<>]*?>/g, " ").replace(/&nbsp;|&#160;/gi, " ");
                b = b.replace(/(\w+)(&.+?;)+(\w+)/, "$1$3").replace(/&.+?;/g, " ");
                b = b.replace(this.cleanre, "");
                var d = b.match(this.countre);
                if (d) {
                    a = d.length
                }
            }
			c.settings.weWordCounter = a;
            return a
        },
        _count: function (a) {
            var b = this;
            if (b.block) {
                return
            }
            b.block = 1;
            setTimeout(function () {
                if (!a.destroyed) {
                    var c = b._getCount(a);
                    tinymce.DOM.setHTML(b.id, c.toString());
                    setTimeout(function () {
                        b.block = 0
                    }, b.update_rate)
                }
            }, 1)
        },
        getInfo: function () {
            return {
                longname: "Word Count plugin",
                author: "Moxiecode Systems AB",
                authorurl: "http://tinymce.moxiecode.com",
                infourl: "http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/wewordcount",
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            }
        }
    });
    tinymce.PluginManager.add("wewordcount", tinymce.plugins.WeWordCount)
})();