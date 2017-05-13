/* global container, WE,treeData,drawTree, top */

/**
 * webEdition SDK
 *
 * webEdition CMS
 * $Rev: 13751 $
 * $Author: lukasimhof $
 * $Date: 2017-05-11 15:56:04 +0200 (Do, 11. Mai 2017) $
 *
 * This source is part of the webEdition SDK. The webEdition SDK is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License
 * the Free Software Foundation; either version 3 of the License, or
 * any later version.
 *
 * The GNU Lesser General Public License can be found at
 * http://www.gnu.org/licenses/lgpl-3.0.html.
 * A copy is found in the textfile
 * webEdition/licenses/webEditionSDK/License.txt
 *
 *
 * @category   we
 * @package    we_ui
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
'use strict';

window.dynVars = WE().util.getDynamicVar(document, 'loadVarExport_cmd_loadTree', 'data-cmdDynVars');
var win = (top.content && top.content.editor.edbody.treeData ? top.content.editor.edbody : top);

win.loadTreeItems(window.dynVars.parentFolder, window.dynVars.clear, window.dynVars.treeItems);
