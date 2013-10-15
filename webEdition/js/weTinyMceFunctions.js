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

var tinyEditors = {};
var tinyEditorsInPopup = {};

function TinyWrapper(fieldname) {
	if(!(this instanceof TinyWrapper)){
		return new TinyWrapper(fieldname);
	}

	var _fn = fieldname;
	var _isInlineedit = typeof tinyEditors[_fn] === "object";
	var _id = _isInlineedit ? tinyEditors[_fn].id : (typeof tinyEditors[_fn] === "undefined" ? "undefined" : tinyEditors[_fn]);

	this.getFieldName = function(){return _fn;};
	this.getId = function(){return _id;};
	this.getIsInlineedit = function(){return _isInlineedit;};

	this.getEditor = function(tryPopup){
		var _tryPopup = typeof tryPopup === "undefined" ? false : tryPopup;
		if(tryPopup && this.getEditorInPopup() !== "undefined"){
			return this.getEditorInPopup();
		}

		return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? tinyEditors[_fn] : "undefined");
	};

	this.getEditorInPopup = function(){
		if(typeof tinyEditorsInPopup[_fn] !== "undefined"){
			try{
				tinyEditorsInPopup[_fn].getContent();
				return tinyEditorsInPopup[_fn];
			} 
			catch(err){
				delete tinyEditorsInPopup[_fn];
				return "undefined";
			}
		} else{
			return "undefined";
		}
	};

	this.getTextarea = function(){return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? "undefined" : document.getElementById(tinyEditors[_fn]));};
	this.getDiv = function(){return typeof tinyEditors[_fn] === "undefined" ? "undefined" : (typeof tinyEditors[_fn] === "object" ? "undefined" : document.getElementById("div_wysiwyg_" + tinyEditors[_fn]));};

	this.getContent = function(forcePopup){
		var _forcePopup = typeof forcePopup === "undefined" ? false : forcePopup;
		if(!_isInlineedit){
			if(_forcePopup){
				try{
					return tinyEditorsInPopup[_fn].getContent();
				} catch(err){
					//console.log("No Editor \'" + _fn + "\' in Popup found!");
				}
			}
			try{
				return document.getElementById(tinyEditors[_fn]).value;
			} catch(err){
				//console.log("No Editor \'" + _fn + "\' found!");
			}
		} else{
			try{
				return tinyEditors[_fn].getContent();
			} catch(err){
				//console.log("No Editor \'" + _fn + "\' found!");
			}
		}
	};

	this.setContent = function(cnt){
		if(!_isInlineedit){
			try{
				document.getElementById(tinyEditors[_fn]).value = cnt;
				document.getElementById("div_wysiwyg_" + tinyEditors[_fn]).innerHTML = cnt;
			} catch(err){
				//console.log("No Editor \'" + _fn + "\' found!");
			}
			try{
				tinyEditorsInPopup[_fn].setContent(cnt);
			} catch(err){
				//console.log("No Editor \'" + _fn + "\' in Popup found!");
			}
		} else{
			try{
				tinyEditors[_fn].setContent(cnt);
			} catch(err){
				//console.log("No Editor \'" + _fn + "\' found!");
			}
		}
	};

	this.on = function(sEvtObj, func){
			var editor = this.getEditor(true);
			try{
				editor["on" + sEvtObj].add(func);
			} catch(e){
				console.log("unable to add event");
			}
	};
}