<?php

/**
 * webEdition CMS
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

class we_wysiwyg_ToolbarButton extends we_wysiwyg_ToolbarElement{

	var $classname = __CLASS__;
	var $tooltiptext = "";
	var $imgSrc = "";

	function __construct($editor, $cmd, $imgSrc, $tooltiptext = "", $width = 25, $height = 22){
		$width = we_wysiwyg::$editorType == 'tinyMCE' ? 21 : $width; // correct value: 20 : imi
		parent::__construct($editor, $cmd, $width, $height);
		if(we_wysiwyg::$editorType != 'tinyMCE'){
			$this->tooltiptext = $tooltiptext;
			$this->imgSrc = $imgSrc;
		}
	}

	function getHTML(){
		if(we_base_browserDetect::isSafari()){
			return '<div id="' . $this->editor->ref . 'edit_' . $this->cmd . 'Div" class="tbButton">
<img  width="' . ($this->width - 2) . '" height="' . $this->height . '" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" src="' . $this->imgSrc . '" alt="' . $this->tooltiptext . '" title="' . $this->tooltiptext . '"
onmouseover="' . $this->editor->ref . 'Obj.over(\'' . $this->cmd . '\');"
onmouseout="' . $this->editor->ref . 'Obj.out(\'' . $this->cmd . '\');"
onmousedown="' . $this->editor->ref . 'Obj.click(event,\'' . $this->cmd . '\');" /></div>';
		} else{

			return '<div id="' . $this->editor->ref . 'edit_' . $this->cmd . 'Div" class="tbButton">
<img  width="' . ($this->width - 2) . '" height="' . $this->height . '" id="' . $this->editor->ref . 'edit_' . $this->cmd . '" src="' . $this->imgSrc . '" alt="' . $this->tooltiptext . '" title="' . $this->tooltiptext . '"
onmouseover="' . $this->editor->ref . 'Obj.over(\'' . $this->cmd . '\');"
onmouseout="' . $this->editor->ref . 'Obj.out(\'' . $this->cmd . '\');"
onmousedown="' . $this->editor->ref . 'Obj.check(\'' . $this->cmd . '\');"
onmouseup="' . $this->editor->ref . 'Obj.uncheck(\'' . $this->cmd . '\');"
onclick="' . $this->editor->ref . 'Obj.click(\'' . $this->cmd . '\');" /></div>';
		}
	}

	function hasProp(){
		switch($this->cmd){

			case "caption":
			case "removecaption":
			case "edittable":
				return we_wysiwyg::$editorType == 'tinyMCE' ? false : stripos($this->editor->propstring, ",table,") !== false ||
					stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "inserttable":
			case "editcell":
			case "insertcolumnright":
			case "insertcolumnleft":
			case "insertrowabove":
			case "insertrowbelow":
			case "deleterow":
			case "deletecol":
			case "increasecolspan":
			case "decreasecolspan":
				return stripos($this->editor->propstring, ",table,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "editrow":
			case "deletetable":
				return we_wysiwyg::$editorType != 'tinyMCE' ? false : stripos($this->editor->propstring, ",table,") !== false ||
					stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "cut":
			case "copy":
			case "paste":
				return stripos($this->editor->propstring, ",copypaste,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "pastetext":
			case "pasteword":
			case "selectall":
				return we_wysiwyg::$editorType == 'tinyMCE' &&
					(stripos($this->editor->propstring, ",copypaste,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			case "forecolor":
			case "backcolor":
				return stripos($this->editor->propstring, ",color,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "createlink":
			case "unlink":
				return stripos($this->editor->propstring, ",link,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "insertunorderedlist":
			case "insertorderedlist":
			case "indent":
			case "outdent":
				return stripos($this->editor->propstring, ",list,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "blockquote":
				return we_wysiwyg::$editorType == 'tinyMCE' &&
					(stripos($this->editor->propstring, ",list,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			case "justifyleft":
			case "justifycenter":
			case "justifyright":
			case "justifyfull":
				return stripos($this->editor->propstring, ",justify,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "bold":
			case "italic":
			case "underline":
			case "subscript":
			case "superscript":
			case "strikethrough":
			case "removetags":
			case "removeformat":
				return stripos($this->editor->propstring, ",prop,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "importrtf":
				return we_wysiwyg::$editorType == 'tinyMCE' ? false : stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
			case "absolute":
			case "insertlayer":
			case "movebackward":
			case "moveforward":
				return we_wysiwyg::$editorType == 'tinyMCE' &&
					(stripos($this->editor->propstring, ",layer,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			//TODO: we shouldcombine the following command to "insertelements": emotions,insertdate,inserttime,nonbreaking,hr,advhr,specialchar,nbsp?
			//TODO: we should combine the following command to "direction": ltr,rtl?
			case "abbr":
			case "acronym":
			case "lang":
				(stripos($this->editor->propstring, ",xhtmlxtras ,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			case "del":
			case "ins":
			case "cite" :
				return we_wysiwyg::$editorType == 'tinyMCE' &&
					(stripos($this->editor->propstring, ",xhtmlxtras ,") !== false || stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			case "emotions":
				return false; // problems with path to emoticons
			case "insertdate":
			case "inserttime":
			case "nonbreaking":
			case "hr":
			case "ltr":
			case "rtl":
			case "search":
			case "replace":
			case "fullscreen":
			case "styleprops":
				return we_wysiwyg::$editorType == 'tinyMCE' &&
					(stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == ""));
			default:
				return stripos($this->editor->propstring, "," . $this->cmd . ",") !== false || ($this->editor->propstring == "");
		}
	}

}
