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
class we_wysiwyg_ToolbarSeparator extends we_wysiwyg_ToolbarElement{
	var $classname = __CLASS__;
	public $conditional = false;

	public function __construct($editor, $conditional = false, $width = 5, $height = 22){
		$width = $conditional ? 0 : $width; //TinyMCE: 3px separator + 1px block-border on both sides
		parent::__construct($editor, '', $width, $height);
		$this->conditional = $conditional;
		$this->isSeparator = true;
	}

	function getHTML(){
		return '<div style="border-right: #999999 solid 1px; font-size: 0px; height: ' . $this->height . 'px ! important; width: ' . ($this->width - 1) . 'px;position: relative;" class="tbButtonWysiwygDefaultStyle"></div>';
	}

	function hasProp($cmd = '', $contextMenu = false){
		return true;
	}

}
