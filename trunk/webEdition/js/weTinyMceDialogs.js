/**
 * webEdition CMS
 *
 * $Rev: 5112 $
 * $Author: mokraemer $
 * $Date: 2012-11-09 20:02:22 +0100 (Fr, 09 Nov 2012) $
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
var tinyMceDialog = null;
var tinyMceSecondaryDialog = null;

function weRegisterTinyMcePopup(win){
	var id = (typeof(win.document.body.id) == "undefined") ? "" : win.document.body.id;
	
	if(id != "colorpicker"){
		if(tinyMceDialog !== null){
			try{
				tinyMceDialog.close();
			}catch(err){}
		}
		if(tinyMceSecondaryDialog !== null){
			try{
				tinyMceSecondaryDialog.close();
			}catch(err){}
		}
		tinyMceDialog = win;
		
	} else { // secondary dialog
		if(tinyMceSecondaryDialog !== null){
			try{
				tinyMceSecondaryDialog.close();
			}catch(err){}
		} 
		tinyMceSecondaryDialog = win;
	}
}

function weCloseSecondaryDialog(){
	if(tinyMceSecondaryDialog !== null){
		try{
			tinyMceSecondaryDialog.close();
		}catch(err){}
	}
}