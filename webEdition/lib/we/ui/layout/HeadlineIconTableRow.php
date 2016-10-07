<?php

/**
 * webEdition SDK
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
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Class which creates a row to display in a we_ui_layout_HeadlineIconTable
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_layout_HeadlineIconTableRow extends we_ui_abstract_AbstractElement{
	
	/*
	 * buffer to store the content HTML
	 *
	 * @var string
	 */

	protected $_contentHTML = "";

	/*
	 * position where the title should displays.
	 * Possible values are "left" and "right"
	 *
	 * @var string
	 */
	protected $_titlePosition = 'left';

	/*
	 * path (src) where the icon is stored
	 *
	 * @var string
	 */
	protected $_iconPath = '';

	/*
	 * width of the left column
	 *
	 * @var integer
	 */
	protected $_leftWidth = 150;

	/*
	 * If set to true a line will be inserted at the end of the row
	 *
	 * @var boolean
	 */
	protected $_line = true;

	/**
	 * adds an element to the table row. The elements HTML
	 * will be added to the right columns innerHTML
	 *
	 * @param we_ui_abstract_AbstractElement $elem
	 * @return void
	 */
	public function addElement($elem){
		$this->addCSSFiles($elem->getCSSFiles());
		$this->addJSFiles($elem->getJSFiles());
		$this->_contentHTML .= $elem->getHTML();
	}

	/**
	 * adds HTML to the right columns innerHTML
	 *
	 * @param string $html
	 * @return void
	 */
	public function addHTML($html){
		$this->_contentHTML .= $html;
	}

	/**
	 * Renders and returns HTML of table
	 *
	 * @return string
	 */
	protected function _renderHTML(){
		$iconHTML = ($this->_iconPath !== '') ? ('<img src="' . oldHtmlspecialchars($this->_iconPath) . '" alt="" />') : "";
		$divID = we_util_Strings::createUniqueId();
		$imgID = we_util_Strings::createUniqueId();
		if($this->_isFoldable){
			$this->_leftWidth = 700;
			if($this->_isFolded){
				$folderHTML = we_html_button::create_button('fa:, fa-lg fa-caret-right', '', '', 0, 0, 'd = document.getElementById(\'' . $divID . '\'); btn = document.getElementById(\'' . $imgID . '\').firstChild; if(d.style.display == \'none\'){d.style.display = \'block\'; btn.classList.remove("fa-caret-right"); btn.classList.add("fa-caret-down");} else {d.style.display = \'none\'; btn.classList.remove("fa-caret-down"); btn.classList.add("fa-caret-right");}', '', false, false, '', false, 'open', $class = 'clipbutton', $imgID);
			} else {
				$folderHTML = we_html_button::create_button('fa:, fa-lg fa-caret-down', '', '', 0, 0, 'd = document.getElementById(\'' . $divID . '\'); btn = document.getElementById(\'' . $imgID . '\').firstChild; if(d.style.display == \'none\'){d.style.display = \'block\'; btn.classList.remove("fa-caret-right"); btn.classList.add("fa-caret-down");} else {d.style.display = \'none\'; btn.classList.remove("fa-caret-down"); btn.classList.add("fa-caret-right");}', '', false, false, '', false, 'open', $class = 'clipbutton', $imgID);
			}
		} else {
			$folderHTML = '';
		}
		$headlineHTML = ($this->_title !== '') ? ('<div class="' . we_ui_layout_HeadlineIconTable::kRowTitle . '" style="margin-bottom:10px;' . ($this->_isFoldable ? ' margin-left:-6px;' : '') . '">' . $folderHTML . oldHtmlspecialchars($this->_title) . '</div>') : "";

		$leftContent = ($iconHTML !== '') ? $iconHTML : (($this->_leftWidth && ($this->_titlePosition === 'left')) ? $headlineHTML : "");

		$rightContent = '<div style="float:left;">' . ((($iconHTML && $headlineHTML) || ($leftContent === "") || ($this->_titlePosition != 'left')) ? ($headlineHTML . '<div>' . $this->_contentHTML . '</div>') : '<div id="' . $divID . '" ' . ($this->_isFolded ? 'style="display:none"' : '') . ' >' . $this->_contentHTML . '</div>') . '</div>';

		$html = '';

		if($leftContent || $this->_leftWidth){
			if((!$leftContent) && $this->_leftWidth){
				$leftContent = "&nbsp;";
			}
			$html .= '<div style="float:left;width:' . $this->_leftWidth . 'px;' . ($this->_hidden ? 'display:none' : '') . '">' . $leftContent . '</div>';
		}

		$html .= $rightContent;
		$html .= '<br style="clear:both;">';

		return $html;
	}

	/**
	 * Retrieve line attribute
	 *
	 * @return boolean
	 */
	public function getLine(){
		return $this->_line;
	}

	/**
	 * Retrieve iconPath attribute
	 *
	 * @return string
	 */
	public function getIconPath(){
		return $this->_iconPath;
	}

	/**
	 * Retrieve leftWidth attribute
	 *
	 * @return integer
	 */
	public function getLeftWidth(){
		return $this->_leftWidth;
	}

	/**
	 * Retrieve line attribute => Alias for getLine()
	 *
	 * @return boolean
	 */
	public function hasLine(){
		return $this->getLine();
	}

	/**
	 * Set line attribute => Alias for getLine()
	 *
	 * @param string $iconPath
	 * @return void
	 */
	public function setIconPath($iconPath){
		$this->_iconPath = $iconPath;
	}

	/**
	 * Set leftWidth attribute
	 *
	 * @param integer $leftWidth
	 * @return void
	 */
	public function setLeftWidth($leftWidth){
		$this->_leftWidth = $leftWidth;
	}

	/**
	 * Set line attribute
	 *
	 * @param boolean $line
	 * @return void
	 */
	public function setLine($line){
		$this->_line = $line;
	}

	/**
	 * Set titlePosition attribute
	 *
	 * @param string $titlePosition possible values are "right" and "left"
	 * @return void
	 */
	public function setTitlePosition($titlePosition){
		$this->_titlePosition = $titlePosition;
	}

	protected $_isFoldable = false;

	public function setIsFoldable($isfoldable){
		$this->_isFoldable = $isfoldable;
	}

	protected $_isFolded = false;

	public function setIsFolded($isfolded){
		$this->_isFolded = $isfolded;
	}

}
