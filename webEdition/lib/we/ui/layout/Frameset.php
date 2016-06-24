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
 * Class to display a frameset
 *
 * @category   we
 * @package none
 * @subpackage we_ui_layout
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_layout_Frameset extends we_ui_abstract_AbstractElement{
	/**
	 * _framespacing attribute
	 *
	 * @var integer
	 */
	protected $_framespacing = 0;

	/**
	 * _border attribute
	 *
	 * @var integer
	 */
	protected $_border = 0;

	/**
	 * _frameborder attribute
	 *
	 * @var string
	 */
	protected $_frameborder = 'no';

	/**
	 * _rows attribute
	 *
	 * @var integer
	 */
	protected $_rows;

	/**
	 * _cols attribute
	 *
	 * @var integer
	 */
	protected $_cols;

	/**
	 * _onload attribute
	 *
	 * @var string
	 */
	protected $_onload;

	/**
	 * _frames attribute
	 *
	 * @var array
	 */
	protected $_frames = [];

	/**
	 * add frame
	 *
	 */
	public function addFrame($attributes){
		$this->_frames[] = $attributes;
	}

	/**
	 * Retrieve HTML of ui element
	 *
	 * @return string
	 */
	public function getHTML($isTopFrame = false, $appName = ''){
		$this->_willRenderHTML();
		$html = $this->_renderHTML($isTopFrame, $appName);
		$this->_didRenderHTML();
		return $html;
	}

	/**
	 * Renders and returns HTML of frameset
	 *
	 * @return string
	 */
	protected function _renderHTML($isTopFrame = false, $appName = ''){
		t_e('deprecated', 'usage of this class is deprecated');
		if(!$isTopFrame || !$appName || we_app_Common::isJMenu($appName)){
			$html = '<frameset' . $this->_getNonBooleanAttribs('id,framespacing,border,frameborder,rows,cols,onload') . '>';
			foreach($this->_frames as $frame){
				if($frame instanceof we_ui_layout_Frameset){
					$html .= $frame->getHTML();
				} else {
					$html .= getHtmlTag('frame', $frame, true);
				}
			}
			$html .= '</frameset>';
		} else {

			$isToolbar = false;
			$positioning = array('top: 0px; height: 40px;', '', 'top: 43px; bottom: 0px;');
			$sources = array($this->_frames[0]['src'], '', $this->_frames[1]['src']);
			$names = array('', '', $this->_frames[1]['name']);

			if(count($this->_frames) == 4){
				$isToolbar = true;
				$rows = explode(',', $this->_getNonBooleanAttribs('rows'));
				$toolBarHeight = intval(trim($rows[1]));
				$positioning = array('top: 0px; height: 40px;', 'top: 43px; height: ' . $toolBarHeight . 'px', 'top: ' . (43 + $toolBarHeight) . 'px; bottom: 0px;');
				$sources = array($this->_frames[0]['src'], $this->_frames[1]['src'], $this->_frames[2]['src']);
				$names = array('', $this->_frames[1]['name'], $this->_frames[2]['name']);
			}
			$html = we_html_element::htmlDiv(array('name' => "headerDiv", 'id' => "headerDiv", 'style' => 'position: absolute; ' . $positioning[0] . ' left: 0px; right: 0px;'), $this->getHTMLCssMenu($appName)) .
				($isToolbar ? we_html_element::htmlIFrame($names[1], $sources[1], 'position: absolute; ' . $positioning[1] . ' left: 0px; right: 0px; overflow: hidden;', '', '', false) : '') .
				we_html_element::htmlIFrame($names[2], $sources[2], 'position: absolute; ' . $positioning[2] . ' left: 0px; right: 0px; overflow: hidden;') .
				we_html_element::htmlIFrame('cmd_' . $appName, 'about:blank', 'position: absolute; bottom: 0px; height: 1px; left: 0px; right: 0px; overflow: hidden;', '', '', false);
		}
		return $html;
	}

	/**
	 * retrieve border
	 *
	 * @return integer
	 */
	public function getBorder(){
		return $this->_border;
	}

	/**
	 * retrieve frameborder
	 *
	 * @return integer
	 */
	public function getFrameborder(){
		return $this->_frameborder;
	}

	/**
	 * retrieve framespacing
	 *
	 * @return integer
	 */
	public function getFramespacing(){
		return $this->_framespacing;
	}

	/**
	 * set border
	 *
	 * @param integer $border
	 */
	public function setBorder($border){
		$this->_border = $border;
	}

	/**
	 * set frameborder
	 *
	 * @param integer $frameborder
	 */
	public function setFrameborder($frameborder){
		$this->_frameborder = $frameborder;
	}

	/**
	 * set framespacing
	 *
	 * @param integer $framespacing
	 */
	public function setFramespacing($framespacing){
		$this->_framespacing = $framespacing;
	}

	/**
	 * retrieve cols
	 *
	 * @return integer
	 */
	public function getCols(){
		return $this->_cols;
	}

	/**
	 * retrieve onload
	 *
	 * @return string
	 */
	public function getOnLoad(){
		return $this->_onload;
	}

	/**
	 * retrieve rows
	 *
	 * @return integer
	 */
	public function getRows(){
		return $this->_rows;
	}

	/**
	 * set cols
	 *
	 * @param integer $cols
	 */
	public function setCols($cols){
		$this->_cols = $cols;
	}

	/**
	 * set onLoad
	 *
	 * @param string $onload
	 */
	public function setOnLoad($onload){
		$this->_onload = $onload;
	}

	/**
	 * set rows
	 *
	 * @param integer $rows
	 */
	public function setRows($rows){
		$this->_rows = $rows;
	}

	protected function getHTMLCssMenu($appName = ''){
		include ($appName . '/conf/we_menu_' . $appName . '.conf.php');
		$lang_arr = 'we_menu_' . $appName;
		$jmenu = new we_ui_controls_CssMenu(${$lang_arr}, 'cmd_' . $appName, '');

		$messageConsole = new we_ui_controls_MessageConsole(array('consoleName' => 'toolFrame'));

		return
			we_html_element::htmlDiv(array('class' => 'menuDiv'), $jmenu->getHTML(false)) .
			we_html_element::htmlDiv(array('style' => 'width:5em;position: absolute;top: 0px;right: 0px;'), $messageConsole->getHTML());
	}

}
