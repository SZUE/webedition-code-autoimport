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
class we_wysiwyg_ToolbarElement{
	var $width;
	var $height;
	var $cmd;
	var $editor;
	var $classname = __CLASS__;
	protected $showMeInToolbar = false;
	protected $showMeInContextmenu = false;
	protected $isSeparator = false;
	protected $showWhere = '';
	
	const SHOW_IN_TOOLBAR = 'toolbar';
	const SHOW_IN_CONTEXTMENU = 'contextmenu';
	const SHOW_IN_MENU = 'menu';

	function __construct($editor, $cmd, $width, $height = ""){
		$this->editor = $editor;
		$this->width = $width;
		$this->height = $height;
		$this->cmd = $cmd;
		$this->showMeInToolbar = $this->hasProp('', self::SHOW_IN_TOOLBAR);
	}

	public function isSeparator(){
		return $this->isSeparator;
	}

	public function isShowInToolbar(){
		return $this->showMeInToolbar;
	}

	public function isShowInMenu(){
		return $this->showMeInMenu;
	}

	public function isShowInContextmenu(){
		return $this->showMeInContextmenu;
	}

	function getHTML(){
		return '';
	}

	function hasProp($cmd = '', $showWhere = ''){
		$cmd = ($cmd ? : $this->cmd);

		switch($showWhere){
			case self::SHOW_IN_MENU:
				return in_array($cmd, $this->editor->getMenuCommands());
			case self::SHOW_IN_CONTEXTMENU:
				return stripos($this->editor->getRestrictContextmenu(), ',' . $cmd . ',') !== false;
			default: // classic toolbar
				return stripos($this->editor->getPropString(), ',' . $cmd . ',') !== false || $this->editor->getPropString() == '';
		}
	}

}
