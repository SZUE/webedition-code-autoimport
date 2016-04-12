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
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */

/**
 * Class to display a button
 *
 * @category   we
 * @package none
 * @subpackage we_ui_controls
 * @license    http://www.gnu.org/licenses/lgpl-3.0.html  LGPL
 */
class we_ui_controls_Button extends we_ui_abstract_AbstractFormElement{
	/**
	 * Default class name for button
	 */
	const kButtonClassNormal = 'we_ui_controls_Button';

	/**
	 * class name for left part of button
	 */
	const kButtonClassLeft = 'we_ui_controls_Button_Left';

	/**
	 * class name for middle part of button
	 */
	const kButtonClassMiddle = 'we_ui_controls_Button_Middle';

	/**
	 * class name for right part of button
	 */
	const kButtonClassRight = 'we_ui_controls_Button_Right';

	/**
	 * Default class name for disabled button
	 */
	const kButtonClassDisabledNormal = 'we_ui_controls_Disabled_Button';

	/**
	 * class name for left part of disabled button
	 */
	const kButtonClassDisabledLeft = 'we_ui_controls_Disabled_Button_Left';

	/**
	 * class name for middle part of disabled button
	 */
	const kButtonClassDisabledMiddle = 'we_ui_controls_Disabled_Button_Middle';

	/**
	 * class name for right part of disabled button
	 */
	const kButtonClassDisabledRight = 'we_ui_controls_Disabled_Button_Right';

	/**
	 * class name for table position within the button
	 */
	const kButtonClassInnerTable = 'we_ui_controls_Button_InnerTable';

	/**
	 * class name for table position within the button if disabled
	 */
	const kButtonClassDisabledInnerTable = 'we_ui_controls_Disabled_Button_InnerTable';

	/**
	 * text attribute
	 *
	 * @var string
	 */
	protected $_text = '';

	/**
	 * width attribute
	 *
	 * @var string
	 */
	protected $_width = 150;

	/**
	 * type of button
	 *
	 * @var string
	 */
	protected $_type = 'onClick';

	/**
	 * name of internal icon image
	 *
	 * @var string
	 */
	protected $_icon = '';

	/**
	 * path of external button image
	 *
	 * @var string
	 */
	protected $_imagePath = '';

	/**
	 * position attribute
	 * possible values are: left,right
	 *
	 * @var string
	 */
	protected $_textPosition = 'right';

	/**
	 * href attribute
	 *
	 * @var string
	 */
	protected $_href = '';

	/**
	 * target attribute
	 *
	 * @var string
	 */
	protected $_target = '';

	/**
	 * height of button
	 * will be used for button type=submit for the hidden input type=image
	 *
	 * @var integer
	 */
	protected $_height = 22;

	/**
	 * onMouseOut attribute
	 *
	 * @var string
	 */
	protected $_onMouseOut = '';

	/**
	 * onMouseDown attribute
	 *
	 * @var string
	 */
	protected $_onMouseDown = '';

	/**
	 * onMouseUp attribute
	 *
	 * @var string
	 */
	protected $_onMouseUp = '';

	/**
	 * onClick attribute
	 *
	 * @var string
	 */
	protected $_onClick = '';

	/**
	 * Constructor
	 *
	 * Sets object properties if set in $properties array
	 *
	 * @param array $properties associative array containing named object properties
	 * @return void
	 */
	public function __construct($properties = null){
		parent::__construct($properties);

		// add needed CSS files
		$this->addCSSFile(we_ui_layout_Themes::computeCSSURL(__CLASS__));

		// add needed JS Files
		$this->addJSFile(we_ui_abstract_AbstractElement::computeJSURL(__CLASS__));
	}

	/**
	 * Retrieve text of button
	 *
	 * @return string
	 */
	public function getText(){
		return $this->_text;
	}

	/**
	 * Set text of button
	 *
	 * @param string $_text
	 */
	public function setText($_text){
		$this->_text = $_text;
	}

	/**
	 * Retrieve href link of button
	 *
	 * @return string
	 */
	public function getHref(){
		return $this->_href;
	}

	/**
	 * Set href of button
	 *
	 * @param string $_href
	 */
	public function setHref($_href){
		$this->_href = $_href;
	}

	/**
	 * Retrieve target of button = href
	 *
	 * @return string
	 */
	public function getTarget(){
		return $this->_target;
	}

	/**
	 * Set target of button
	 *
	 * @param string $_target
	 */
	public function setTarget($_target){
		$this->_target = $_target;
	}

	/**
	 * Retrieve type of button
	 *
	 * @return string
	 */
	public function getType(){
		return $this->_type;
	}

	/**
	 * Set type of button
	 *
	 * @param string $_type
	 */
	public function setType($_type){
		$this->_type = $_type;
	}

	/**
	 * Retrieve onMouseOut attribute
	 *
	 * @return string
	 */
	public function getOnMouseOut(){
		return $this->_onMouseOut;
	}

	/**
	 * Set onMouseOut attribute
	 *
	 * @param string $_onMouseOut
	 */
	public function setOnMouseOut($_onMouseOut){
		$this->_onMouseOut = $_onMouseOut;
	}

	/**
	 * Retrieve onMouseDown attribute
	 *
	 * @return string
	 */
	public function getOnMouseDown(){
		return $this->_onMouseDown;
	}

	/**
	 * Set onMouseDown attribute
	 *
	 * @param string $_onMouseDown
	 */
	public function setOnMouseDown($_onMouseDown){
		$this->_onMouseDown = $_onMouseDown;
	}

	/**
	 * Retrieve onMouseUp attribute
	 *
	 * @return string
	 */
	public function getOnMouseUp(){
		return $this->_onMouseUp;
	}

	/**
	 * Set onMouseUp attribute
	 *
	 * @param string $_onMouseUp
	 */
	public function setOnMouseUp($_onMouseUp){
		$this->_onMouseUp = $_onMouseUp;
	}

	/**
	 * Retrieve onClick attribute
	 *
	 * @return string
	 */
	public function getOnClick(){
		return $this->_onClick;
	}

	/**
	 * Set onClick attribute
	 *
	 * @param string $_onClick
	 */
	public function setOnClick($_onClick){
		$this->_onClick = $_onClick;
	}

	/**
	 * Retrieve icon of internal button
	 *
	 * @return string
	 */
	public function getIcon(){
		return $this->_icon;
	}

	/**
	 * Set icon of internal button
	 *
	 * @param string $_icon
	 */
	public function setIcon($_icon){
		$this->_icon = $_icon;
	}

	/**
	 * Retrieve imagePath of external button
	 *
	 * @return string
	 */
	public function getImagePath(){
		return $this->_imagePath;
	}

	/**
	 * Set imagePath of external button
	 *
	 * @param string $_imagePath
	 */
	public function setImagePath($_imagePath){
		$this->_imagePath = $_imagePath;
	}

	/**
	 * Retrieve textPosition of text
	 *
	 * @return string
	 */
	public function getTextPosition(){
		return $this->_textPosition;
	}

	/**
	 * Set textPosition of text
	 *
	 * @param string $_textPosition
	 */
	public function setTextPosition($_textPosition){
		$this->_textPosition = $_textPosition;
	}

	/**
	 * Retrieve start tag <a> if button is type = href or <div> if button is type = submit
	 *
	 * @return string
	 */
	public function _getWrapperStart(){
		if($this->getType() === 'href'){

			$onClick = ($this->getDisabled() ? "return false;" : "return true;");

			return '<div style="width:' . $this->getWidth() . 'px;
			height:' . $this->getHeight() . 'px;"><a onclick="' . $onClick . '" id="a_' . $this->getId() . '" style="text-decoration:none;display:block;"  ' . $this->_getNonBooleanAttribs('href,target,title') . '>';
		}
		if($this->getType() === 'submit'){
			return '<div style="position:relative;z-index:1;width:' . $this->getWidth() . 'px;
				height:' . $this->getHeight() . 'px;">
				<input id="input_' . $this->getId() . '" ' . $this->_getBooleanAttribs('disabled') . ' ' . $this->_getNonBooleanAttribs('onMouseDown,onMouseOut') . '
				style="position:absolute;z-index:2;width:' . $this->getWidth() . 'px;
				height:' . $this->getHeight() . 'px;"
				type="image" title="' . $this->getTitle() . '">';
		}

		return "";
	}

	/**
	 * Returns end tag </a> if button is type = href or </div> if button is type = submit
	 *
	 * @return string
	 */
	public function _getWrapperEnd(){
		switch($this->getType()){
			case 'href':
				return '</a></div>';
			case 'submit':
				return '</div>';
		}

		return "";
	}

	/**
	 * Returns button content, image or text or both
	 *
	 * @return string
	 */
	public function _getButtonContent(){
		$buttonHTML = '';

		if($this->getDisabled()){
			$classLeft = self::kButtonClassDisabledLeft;
			$classMiddle = self::kButtonClassDisabledMiddle;
			$classRight = self::kButtonClassDisabledRight;
			$tblClass = self::kButtonClassDisabledInnerTable;
		} else {
			$classLeft = self::kButtonClassLeft;
			$classMiddle = self::kButtonClassMiddle;
			$classRight = self::kButtonClassRight;
			$tblClass = self::kButtonClassInnerTable;
		}
		if($this->getImagePath() === ""){
			$buttonHTML .= '<div' . $this->_getComputedClassAttrib($classLeft) . ' style="height:' . $this->_height . 'px"></div><div style="width:' . $this->getWidth() . 'px;height:' . $this->getHeight() . 'px;"' . $this->_getComputedClassAttrib($classMiddle) . '>';
		}
		$buttonHTML .= '<table id="table_' . $this->getId() . '" class="default ' . $tblClass . '"><tr>';

		if($this->getIcon() !== '' || $this->getImagePath() !== ''){
			$image = '';
			if($this->getImagePath() !== ''){
				$image = $this->getImagePath();
			} elseif($this->getIcon() !== ''){
				$image = $this->getIcon();
			}
			$imagePath = $_SERVER['DOCUMENT_ROOT'] . $image;
			if(file_exists($_SERVER['DOCUMENT_ROOT'] . $image) && is_readable($imagePath)){
				$button = '<img src="' . $image . '" style="-khtml-user-select: none;padding:0px 5px 0px 5px;" />';
				if($this->getText() !== ""){
					$text = $this->getText();
					switch($this->getTextPosition()){
						case "left" :
							$buttonHTML .= '<td>' . $text . '</td><td>' . $button . '</td>';
							break;
						case "right" :
							$buttonHTML .= '<td>' . $button . '</td><td>' . $text . '</td>';
							break;
					}
				} else {
					$buttonHTML .= '<td>' . $button . '</td>';
				}
			}
		} else {
			$buttonHTML .= '<td>' . $this->getText() . '</td>';
		}
		$buttonHTML .= '</tr></table>';
		if($this->getImagePath() === ""){
			$buttonHTML .= '</div><div' . $this->_getComputedClassAttrib($classRight) . ' style="height:' . $this->_height . 'px"></div>';
		}

		return $buttonHTML;
	}

	/**
	 * Returns string with non boolean attribs to insert into html tag
	 *
	 * @param string $attribsString comma separated string with attribute names
	 * @return string
	 */
	protected function _getNonBooleanAttribs($attribsString){
		$arr = explode(',', $attribsString);
		$attribs = '';
		foreach($arr as $attribName){
			$internalName = "_$attribName";
			switch($internalName){
				case "_onMouseDown":
					$attribs .= ' ' . oldHtmlspecialchars($attribName) . '="if(we_ui_controls_Button.down(\'' . $this->getId() . '\')) {' . oldHtmlspecialchars($this->$internalName) . '}"';
					break;
				case "_onMouseUp":
					$attribs .= ' ' . oldHtmlspecialchars($attribName) . '="if(we_ui_controls_Button.up(\'' . $this->getId() . '\')) {' . oldHtmlspecialchars($this->$internalName) . '}"';
					break;
				case "_onMouseOut":
					$attribs .= ' ' . oldHtmlspecialchars($attribName) . '="if(we_ui_controls_Button.out(\'' . $this->getId() . '\')) {' . oldHtmlspecialchars($this->$internalName) . '}"';
					break;
			}
			if(isset($this->$internalName) && $this->$internalName !== ''){
				$attribs .= ' ' . oldHtmlspecialchars($attribName) . '="' .
					($internalName === "_onClick" ?
						'if(we_ui_controls_Button.up(\'' . $this->getId() . '\')) {' . oldHtmlspecialchars($this->$internalName) . '}' :
						oldHtmlspecialchars($this->$internalName)) .
					'"';
			}
		}
		return $attribs;
	}

	/**
	 * Renders and returns HTML of button
	 *
	 * @return string
	 */
	public function _renderHTML(){

		if($this->getDisabled()){
			$classNormal = self::kButtonClassDisabledNormal;
		} else {
			$classNormal = self::kButtonClassNormal;
		}

		if($this->getHidden()){
			$this->_style .= 'display:none;';
		}

		return $this->_getWrapperStart() . '<div' . $this->_getNonBooleanAttribs('id,title,onclick,onClick,onMouseUp,onMouseDown,onMouseOut') . $this->_getComputedStyleAttrib() . $this->_getComputedClassAttrib($classNormal) . '>' . $this->_getButtonContent() . '</div>' . $this->_getWrapperEnd();
	}

}
