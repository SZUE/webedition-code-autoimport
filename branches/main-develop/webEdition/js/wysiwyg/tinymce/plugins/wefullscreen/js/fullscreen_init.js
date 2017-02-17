/* global top, tinyMCE, tinyMCEPopup */

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

var isTinyMCE = true;

var WefullscreenDialog = { // TODO: clean code by using more vars
	writeback : function() {
		// only if inlineedit=true we set isHot from here: otherwise the editor-popup cares for setting hot itself
		if(top.opener._EditorFrame !== undefined && tinyMCE.activeEditor.isDirty()){
			top.opener._EditorFrame.setEditorIsHot(true);
		}
		tinyMCEPopup.editor.execCommand('mceSetContent', true, tinyMCE.activeEditor.getContent({format : 'html'}));
	}
};


function weTinyDialog_doOk(){
	WefullscreenDialog.writeback();
	top.close();
}