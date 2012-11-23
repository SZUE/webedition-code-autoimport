/**
 * webEdition CMS
 *
 * $Rev: 5016 $
 * $Author: lukasimhof $
 * $Date: 2012-10-25 11:53:14 +0200 (Do, 25 Okt 2012) $
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

var isTinyMCE = true;

var WefullscreenDialog = { // TODO: clean code by using more vars
	
	init : function() {
		document.getElementById('we_dialog_args[src]').innerHTML = tinyMCEPopup.editor.getContent({format : 'html'});
	},

	writeback : function() {
		tinyMCEPopup.editor.execCommand('mceSetContent', true, tinyMCE.activeEditor.getContent({format : 'html'}));
	}
};

tinyMCEPopup.onInit.add(WefullscreenDialog.init, WefullscreenDialog);
