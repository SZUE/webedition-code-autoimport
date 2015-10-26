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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
class we_wysiwyg_ToolbarButton extends we_wysiwyg_ToolbarElement{

	var $classname = __CLASS__;
	var $tooltiptext = '';
	var $imgSrc = '';

	function __construct($editor, $cmd, $width = 22, $height = 22){
		parent::__construct($editor, $cmd, $width, $height);
		$this->showMeInContextmenu = $this->hasProp('', true);
	}

	function hasProp($cmd = '', $contextMenu = false){
		//FIXME use of a structred array may be used for help to.
		/* $commands = array(
		  'table' => array(
		  'plugin' => 'table',
		  'commands' => array(
		  "inserttable",
		  "editcell",
		  "insertcolumnright",
		  "insertcolumnleft",
		  "insertrowabove",
		  "insertrowbelow",
		  "deleterow",
		  "deletecol",
		  "increasecolspan",
		  "decreasecolspan"
		  )
		  )
		  ); */
		switch($this->cmd){
			case 'fontname':
			case 'fontsize':
				return parent::hasProp('', $contextMenu) || parent::hasProp('font', $contextMenu);
			case 'formatblock':
			case 'applystyle':
			case 'bold':
			case 'italic':
			case 'underline':
			case 'subscript':
			case 'superscript':
			case 'strikethrough':
			case 'removetags':
			case 'removeformat':
				return parent::hasProp('', $contextMenu) || parent::hasProp('prop', $contextMenu);
			case 'styleprops':
				return $this->editor->setPlugin('style', parent::hasProp('', $contextMenu) || parent::hasProp('prop', $contextMenu));
			case 'abbr':
			case 'acronym':
			case 'lang':
				return parent::hasProp('', $contextMenu) || parent::hasProp('xhtmlxtras', $contextMenu);
			case 'del':
			case 'ins':
			case 'cite' :
				return $this->editor->setPlugin('xhtmlxtras', parent::hasProp('', $contextMenu) || parent::hasProp('xhtmlxtras', $contextMenu));
			case 'ltr':
			case 'rtl':
				return $this->editor->setPlugin('directionality', parent::hasProp('', $contextMenu) || parent::hasProp('xhtmlxtras', $contextMenu));
			case 'forecolor':
			case 'backcolor':
				return parent::hasProp('', $contextMenu) || parent::hasProp('color', $contextMenu);
			case 'justifyleft':
			case 'justifycenter':
			case 'justifyright':
			case 'justifyfull':
				return parent::hasProp('', $contextMenu) || parent::hasProp('justify', $contextMenu);
			case 'insertunorderedlist':
			case 'insertorderedlist':
			case 'indent':
			case 'outdent':
				$this->editor->setPlugin('lists', parent::hasProp('', $contextMenu) || parent::hasProp('list', $contextMenu));
				return $this->editor->setPlugin('advlist', parent::hasProp('', $contextMenu) || parent::hasProp('list', $contextMenu));
			case 'blockquote':
				return parent::hasProp('', $contextMenu) || parent::hasProp('list', $contextMenu);
			case 'anchor':
			case 'createlink':
			case 'unlink':
				return parent::hasProp('', $contextMenu) || parent::hasProp('link', $contextMenu);
			case 'insertimage':
			case 'insertgallery':
			case 'hr':
			case 'inserthorizontalrule':
			case 'insertspecialchar':
			case 'insertbreak':
				return parent::hasProp('', $contextMenu) || parent::hasProp('insert', $contextMenu);
			case 'insertdate':
			case 'inserttime':
				return $this->editor->setPlugin('insertdatetime', parent::hasProp('', $contextMenu) || parent::hasProp('insert', $contextMenu));
			case 'inserttable':
			case 'editcell':
			case 'insertcolumnright':
			case 'insertcolumnleft':
			case 'insertrowabove':
			case 'insertrowbelow':
			case 'deleterow':
			case 'deletecol':
			case 'increasecolspan':
			case 'decreasecolspan':
			case 'editrow':
			case 'deletetable':
				return $this->editor->setPlugin('table', parent::hasProp('', $contextMenu) || parent::hasProp('table', $contextMenu));
			case 'cut':
			case 'copy':
			case 'paste':
				return false;
			case 'pastetext':
			case 'pasteword':
				return $this->editor->setPlugin('paste', parent::hasProp('', $contextMenu) || parent::hasProp('copypaste', $contextMenu));
			case 'absolute':
			case 'insertlayer':
			case 'movebackward':
			case 'moveforward':
				return $this->editor->setPlugin('layer', parent::hasProp('', $contextMenu) || parent::hasProp('layer', $contextMenu));
			case 'undo':
			case 'redo':
			case 'spellcheck':
			case 'visibleborders':
				return parent::hasProp('', $contextMenu) || parent::hasProp('essential', $contextMenu);
			case 'selectall':
				return $this->editor->setPlugin('paste', parent::hasProp('', $contextMenu) || parent::hasProp('essential', $contextMenu));
			case 'search':
			case 'replace':
				return $this->editor->setPlugin('searchreplace', parent::hasProp('', $contextMenu) || parent::hasProp('essential', $contextMenu));
			case 'fullscreen':
				return !$this->editor->getIsFrontendEdit() && (parent::hasProp('', $contextMenu) || parent::hasProp('essential', $contextMenu));
			case 'editsource':
				return parent::hasProp('', $contextMenu) || parent::hasProp('advanced', $contextMenu);
			case 'template':
				return parent::hasProp('', $contextMenu) || parent::hasProp('advanced', $contextMenu);
			case 'fontname':
			case 'fontsize':
				return parent::hasProp('', $contextMenu) || parent::hasProp('font', $contextMenu);
			case 'formatblock':
			case 'applystyle':
				return parent::hasProp('', $contextMenu) || parent::hasProp('prop', $contextMenu);
			default:
				//FIXME: find the command using this case!
				return false;
		}
	}

}
