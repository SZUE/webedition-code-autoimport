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
		$this->showMeInContextmenu = $this->hasProp('',true);
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

	function hasProp($cmd = '', $contextMenu = false){
		switch($this->cmd){
			case "caption":
			case "removecaption":
			case "edittable":
				return we_wysiwyg::$editorType == 'tinyMCE' ? false : parent::hasProp('', $contextMenu) || parent::hasProp('table',$contextMenu);
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
				return $this->editor->setPlugin('table', parent::hasProp('', $contextMenu) || parent::hasProp('table',$contextMenu));
			case "editrow":
			case "deletetable":
				return $this->editor->setPlugin('table', parent::hasProp('', $contextMenu) || parent::hasProp('table',$contextMenu));
			case "cut":
			case "copy":
			case "paste":
				return $this->editor->setPlugin('paste', parent::hasProp('', $contextMenu) || parent::hasProp('copypaste',$contextMenu));
			case "pastetext":
			case "pasteword":
			case "selectall":
				return $this->editor->setPlugin('paste', parent::hasProp('', $contextMenu) || parent::hasProp('copypaste',$contextMenu));
			case "forecolor":
			case "backcolor":
				return parent::hasProp('', $contextMenu) || parent::hasProp('color',$contextMenu);
			case "createlink":
			case "unlink":
				return parent::hasProp('', $contextMenu) || parent::hasProp('link',$contextMenu);
			case "insertunorderedlist":
			case "insertorderedlist":
			case "indent":
			case "outdent":
				$this->editor->setPlugin('lists', parent::hasProp('', $contextMenu) || parent::hasProp('list',$contextMenu));
				return $this->editor->setPlugin('advlist', parent::hasProp('', $contextMenu) || parent::hasProp('list',$contextMenu));
			case "blockquote":
				return we_wysiwyg::$editorType == 'tinyMCE' && parent::hasProp('', $contextMenu) || parent::hasProp('list',$contextMenu);
			case "justifyleft":
			case "justifycenter":
			case "justifyright":
			case "justifyfull":
				return parent::hasProp('', $contextMenu) || parent::hasProp('justify',$contextMenu);
			case "bold":
			case "italic":
			case "underline":
			case "subscript":
			case "superscript":
			case "strikethrough":
			case "removetags":
			case "removeformat":
				return parent::hasProp('', $contextMenu) || parent::hasProp('prop',$contextMenu);
			case "importrtf":
				return we_wysiwyg::$editorType == 'tinyMCE' ? false : parent::hasProp('', $contextMenu);
			case "absolute":
			case "insertlayer":
			case "movebackward":
			case "moveforward":
				return $this->editor->setPlugin('layer', parent::hasProp('', $contextMenu) || parent::hasProp('layer',$contextMenu));
			//TODO: we shouldcombine the following command to "insertelements": emotions,insertdate,inserttime,nonbreaking,hr,advhr,specialchar,nbsp?
			//TODO: we should combine the following command to "direction": ltr,rtl?
			case "abbr":
			case "acronym":
			case "lang":
				return parent::hasProp('', $contextMenu) || parent::hasProp('xhtmlxtras',$contextMenu);
			case "del":
			case "ins":
			case "cite" :
				return $this->editor->setPlugin('xhtmlxtras', parent::hasProp('', $contextMenu) || parent::hasProp('xhtmlxtras',$contextMenu));
			case "insertdate":
			case "inserttime":
				return $this->editor->setPlugin('insertdatetime', parent::hasProp('', $contextMenu));
			case "ltr":
			case "rtl":
				return $this->editor->setPlugin('directionality', parent::hasProp('', $contextMenu));
			case "search":
			case "replace":
				return $this->editor->setPlugin('searchreplace', parent::hasProp('', $contextMenu));
			case "styleprops":
				return $this->editor->setPlugin('style', parent::hasProp('', $contextMenu));
			case "nonbreaking":
			case "hr":
			case "fullscreen":
				return we_wysiwyg::$editorType == 'tinyMCE' && parent::hasProp('', $contextMenu);
			default:
				return parent::hasProp('', $contextMenu);
		}
	}

}
