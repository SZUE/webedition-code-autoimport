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
var initVars = WE().util.getDynamicVar(document, 'loadVarWeTinyMce_init', 'data-dynvars');
initVars.win = window;

var tinyMceConfObjects = tinyMceConfObjects ? tinyMceConfObjects : {};
tinyMceConfObjects[initVars.weFieldNameClean] = WE().layout.we_tinyMCE.getTinyConfObject(initVars);

// to be temporarily compatible with we_object
window['tinyMceConfObject__' + initVars.weFieldNameClean] = tinyMceConfObjects[initVars.weFieldNameClean];

tinyMCE.addI18n(WE().consts.g_l.tinyMceTranslationObject);
tinyMCE.PluginManager.load = initVars.win.tinyPluginManager;

// try to move this to tiny setup or init
tinyMCE.weResizeLoops = 100;

tinyMCE.init(window.tinyMceConfObjects[initVars.weFieldNameClean]);