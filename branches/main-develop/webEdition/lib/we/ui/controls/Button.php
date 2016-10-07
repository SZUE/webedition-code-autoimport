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
	const kIconAddCat = 'kIconAddCat';
	const kIconAddDoc = 'kIconAddDoc';
	const kIconAddField = 'kIconAddField';
	const kIconAddFile = 'kIconAddFile';
	const kIconAddFlash = 'kIconAddFlash';
	const kIconAddImage = 'kIconAddImage';
	const kIconAddLink = 'kIconAddLink';
	const kIconAddListElement = 'kIconAddListElement';
	const kIconAddNote = 'kIconAddNote';
	const kIconAddSchedule = 'kIconAddSchedule';
	const kIconAddTemplate = 'kIconAddTemplate';
	const kIconAddThumbnail = 'kIconAddThumbnail';
	const kIconDatePicker = 'kIconDatePicker';
	const kIconDirectionDown = 'kIconDirectionDown';
	const kIconDirectionLeft = 'kIconDirectionLeft';
	const kIconDirectionRight = 'kIconDirectionRight';
	const kIconDirectionUp = 'kIconDirectionUp';
	const kIconEdit = 'kIconEdit';
	const kIconEditFlash = 'kIconEditFlash';
	const kIconEditImage = 'kIconEditImage';
	const kIconEditInclude = 'kIconEditInclude';
	const kIconEditLink = 'kIconEditLink';
	const kIconEditList = 'kIconEditList';
	const kIconEditObject = 'kIconEditObject';
	const kIconEditPDF = 'kIconEditPDF';
	const kIconFolderBack = 'kIconFolderBack';
	const kIconPlus = 'kIconPlus';
	const kIconPublish = 'kIconPublish';
	const kIconReload = 'kIconReload';
	const kIconSearch = 'kIconSearch';
	const kIconTrash = 'kIconTrash';
	const kIconUnpublish = 'kIconUnpublish';
	const kIconView = 'kIconView';
	const kIconHelp = 'kIconHelp';
	const kIconIconView = 'kIconIconView';
	const kIconListview = 'kIconListview';
	const kIconMessagesCopy = 'kIconMessagesCopy';
	const kIconMessagesCreate = 'kIconMessagesCreate';
	const kIconMessagesCut = 'kIconMessagesCut';
	const kIconMessagesPaste = 'kIconMessagesPaste';
	const kIconMessagesReply = 'kIconMessagesReply';
	const kIconMessagesTasks = 'kIconMessagesTasks';
	const kIconMessagesTrash = 'kIconMessagesTrash';
	const kIconMessagesUpdate = 'kIconMessagesUpdate';
	const kIconNewBannergroup = 'kIconNewBannergroup';
	const kIconNewDirectory = 'kIconNewDirectory';
	const kIconPaymentVal = 'kIconPaymentVal';
	const kIconSelectImage = 'kIconSelectImage';
	const kIconShopAddNew = 'kIconShopAddNew';
	const kIconShopDelArt = 'kIconShopDelArt';
	const kIconShopDelOrd = 'kIconShopDelOrd';
	const kIconShopExtArt = 'kIconShopExtArt';
	const kIconShopPrefs = 'kIconShopPrefs';
	const kIconShopSum = 'kIconShopSum';
	const kIconShopVariants = 'kIconShopVariants';
	const kIconSpellcheck = 'kIconSpellcheck';
	const kIconTaskCopy = 'kIconTaskCopy';
	const kIconTaskCreate = 'kIconTaskCreate';
	const kIconTaskCut = 'kIconTaskCut';
	const kIconTaskForward = 'kIconTaskForward';
	const kIconTaskMessages = 'kIconTaskMessages';
	const kIconTaskPaste = 'kIconTaskPaste';
	const kIconTaskReject = 'kIconTaskReject';
	const kIconTaskStatus = 'kIconTaskStatus';
	const kIconTaskTrash = 'kIconTaskTrash';
	const kIconTaskUpdate = 'kIconTaskUpdate';

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
	 * style element margin
	 *
	 * @var string
	 */
	protected $_margin = '6px 0 0 -3px';

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

	protected $_isTextReady = false;

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
	 * Set style element margin of button
	 *
	 * @param string $_margin
	 */
	public function setMargin($_margin){
		$this->_margin = $_margin;
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

	public function getButtonContent(){
		switch($this->getIcon()){
				case self::kIconAddCat:
					return '';
				case self::kIconAddDoc:
					return '';
				case self::kIconAddField:
					return '';
				case self::kIconAddFile:
					return '';
				case self::kIconAddFlash:
					return '';
				case self::kIconAddImage:
					return '';
				case self::kIconAddLink:
					return '';
				case self::kIconAddListElement:
					return '';
				case self::kIconAddNote:
					return '';
				case self::kIconAddSchedule:
					return '';
				case self::kIconAddTemplate:
					return '';
				case self::kIconAddThumbnail:
					return '';
				case self::kIconDatePicker:
					return '';
				case self::kIconDirectionDown:
					return '';
				case self::kIconDirectionLeft:
					return '';
				case self::kIconDirectionRight:
					return '';
				case self::kIconDirectionUp:
					return '';
				case self::kIconEdit:
					return '';
				case self::kIconEditFlash:
					return '';
				case self::kIconEditImage:
					return '';
				case self::kIconEditInclude:
					return '';
				case self::kIconEditLink:
					return '';
				case self::kIconEditList:
					return '';
				case self::kIconEditObject:
					return '';
				case self::kIconEditPDF:
					return '';
				case self::kIconFolderBack:
					return '';
				case self::kIconPlus:
					return we_html_button::PLUS;
				case self::kIconPublish:
					return we_html_button::PUBLISH;
				case self::kIconReload:
					return '';
				case self::kIconSearch:
					return we_html_button::SEARCH;
				case self::kIconTrash:
					return we_html_button::TRASH;
				case self::kIconUnpublish:
					return '';
				case self::kIconView:
					return we_html_button::VIEW;
				case self::kIconHelp:
					return '';
				case self::kIconIconView:
					return '';
				case self::kIconListview:
					return '';
				case self::kIconMessagesCopy:
					return '';
				case self::kIconMessagesCreate:
					return '';
				case self::kIconMessagesCut:
					return '';
				case self::kIconMessagesPaste:
					return '';
				case self::kIconMessagesReply:
					return '';
				case self::kIconMessagesTasks:
					return '';
				case self::kIconMessagesTrash:
					return '';
				case self::kIconMessagesUpdate:
					return '';
				case self::kIconNewBannergroup:
					return '';
				case self::kIconNewDirectory:
					return '';
				case self::kIconPaymentVal:
					return '';
				case self::kIconSelectImage:
					return '';
				case self::kIconShopAddNew:
					return '';
				case self::kIconShopDelArt:
					return '';
				case self::kIconShopDelOrd:
					return '';
				case self::kIconShopExtArt:
					return '';
				case self::kIconShopPrefs:
					return '';
				case self::kIconShopSum:
					return '';
				case self::kIconShopVariants:
					return '';
				case self::kIconSpellcheck:
					return '';
				case self::kIconTaskCopy:
					return '';
				case self::kIconTaskCreate:
					return '';
				case self::kIconTaskCut:
					return '';
				case self::kIconTaskForward:
					return '';
				case self::kIconTaskMessages:
					return '';
				case self::kIconTaskPaste:
					return '';
				case self::kIconTaskReject:
					return '';
				case self::kIconTaskStatus:
					return '';
				case self::kIconTaskTrash:
					return '';
				case self::kIconTaskUpdate:
					return '';
				default:
					$this->_isTextReady = true;
					return $this->getText();
		}
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
	 * Renders and returns HTML of button
	 *
	 * @return string
	 */
	public function _renderHTML(){
		$dimensions = array('width' => $this->getWidth(), 'height' => 0 /* $this->getHeight()*/);

		//FIXME: make css
		return we_html_element::htmlDiv(array('style' => 'margin: ' . $this->_margin . ';'), we_html_button::create_button($this->getButtonContent(), $this->getHref(), '', 0, 0, $this->getOnClick(), $this->getTarget(), $this->getDisabled(), false, '', false, $this->getTitle(), $this->getClass(), $this->getId(), $this->_isTextReady, array_filter($dimensions)));
	}

}