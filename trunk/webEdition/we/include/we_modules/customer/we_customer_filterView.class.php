<?php
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

/**
 * Basic view class for customer filters
 *
 */
class we_customer_filterView{
	/**
	 * filter for view
	 *
	 * @var we_customer_abstractFilter
	 */
	protected $filter = null;

	/**
	 * Javascript call for making the document hot
	 *
	 * @var string
	 */
	protected $hotScript = '';

	/**
	 * width of filter
	 *
	 * @var integer
	 */
	protected $width = 0;

	/**
	 * Constructor
	 *
	 * @param we_customer_abstractFilter $filter
	 * @param string $hotScript
	 * @param integer $width
	 * @return we_customer_filterView
	 */
	function __construct(&$filter, $hotScript = "", $width = 0){
		$this->setFilter($filter);
		$this->setHotScript($hotScript);
		$this->setWidth($width);
	}

	/* ##################################################### */

	/**
	 * Gets the HTML and Javascript for the filter
	 *
	 * @return string
	 */
	function getFilterHTML($ShowModeNone = false){
		$script = '
function wecf_hot() {' .
			$this->hotScript . '
}

function updateView() {' .
			$this->createUpdateViewScript() . '
}';
		$mode = $this->filter->getMode();

		// ################# Radio buttons ###############
		$modeRadioOff = we_html_forms::radiobutton(we_customer_abstractFilter::OFF, $mode === we_customer_abstractFilter::OFF, 'wecf_mode', g_l('modules_customerFilter', '[mode_off]'), true, "defaultfont", "wecf_hot();updateView();");
		$modeRadioNone = ($ShowModeNone ?
				we_html_forms::radiobutton(we_customer_abstractFilter::NOT_LOGGED_IN_USERS, $mode === we_customer_abstractFilter::NOT_LOGGED_IN_USERS, 'wecf_mode', g_l('modules_customerFilter', '[mode_none]'), true, "defaultfont", "wecf_hot();updateView();") :
				'');

		$modeRadioAll = we_html_forms::radiobutton(we_customer_abstractFilter::ALL, $mode === we_customer_abstractFilter::ALL, 'wecf_mode', g_l('modules_customerFilter', '[mode_all]'), true, "defaultfont", "wecf_hot();updateView();");
		$modeRadioSpecific = we_html_forms::radiobutton(we_customer_abstractFilter::SPECIFIC, $mode === we_customer_abstractFilter::SPECIFIC, 'wecf_mode', g_l('modules_customerFilter', '[mode_specific]'), true, "defaultfont", "wecf_hot();updateView();");
		$modeRadioFilter = we_html_forms::radiobutton(we_customer_abstractFilter::FILTER, $mode === we_customer_abstractFilter::FILTER, 'wecf_mode', g_l('modules_customerFilter', '[mode_filter]'), true, "defaultfont", "wecf_hot();updateView();");

		// ################# Selector for specific customers ###############
		list($specificCustomersSelect, $myscript) = $this->getMultiEdit('specificCustomersEdit', $this->filter->getSpecificCustomers(), "", $mode === we_customer_abstractFilter::SPECIFIC);
		$script.=$myscript;
		// ################# Selector blacklist ###############

		list($blackListSelect, $myscript) = $this->getMultiEdit('blackListEdit', $this->filter->getBlackList(), g_l('modules_customerFilter', '[black_list]'), $mode === we_customer_abstractFilter::FILTER);
		$script.=$myscript;
		// ################# Selector for whitelist ###############

		list($whiteListSelect, $myscript) = $this->getMultiEdit('whiteListEdit', $this->filter->getWhiteList(), g_l('modules_customerFilter', '[white_list]'), $mode === we_customer_abstractFilter::FILTER);
		$script.=$myscript;
		// ################# customer filter ###############

		$filterCustomers = we_customer_filterView::getDiv($this->getHTMLCustomerFilter(), 'filterCustomerDiv', $mode === we_customer_abstractFilter::FILTER, 25);


		// ################# concate and output #################

		$space = '<div style="height:4px;"></div>';

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_filterLogic.js') . $modeRadioOff . $space . $modeRadioAll . $space . $modeRadioSpecific . $space . $specificCustomersSelect . $space . $modeRadioFilter . $filterCustomers . $blackListSelect . $whiteListSelect . $space . $modeRadioNone . we_html_element::jsElement($script);
	}

	public function getFilterCustomers(){
		$this->filter->setMode(we_customer_abstractFilter::FILTER);

		return we_html_element::jsElement('
function $(id) {
	return document.getElementById(id);
}

function updateView() {' .
				$this->createUpdateViewScript() . '
}

function wecf_hot() {' .
				$this->hotScript . '
}') .
			we_customer_filterView::getDiv($this->getHTMLCustomerFilter(true), 'filterCustomerDiv', true, 25);
	}

	/**
	 * Creates the content for the JavaScript updateView() function
	 *
	 * @return string
	 */
	function createUpdateViewScript(){

		return <<<EOS

	var f = document.forms[0];
	var r = f.wecf_mode;
	var modeRadioOff 		= r[0];
	var modeRadioAll 		= r[1];
	var modeRadioSpecific 	= r[2];
	var modeRadioFilter 	= r[3];
    var modeRadioNone	 	= r[4];

	$('specificCustomersEditDiv').style.display = modeRadioSpecific.checked ? "block" : "none";
	$('blackListEditDiv').style.display = modeRadioFilter.checked ? "block" : "none";
	$('whiteListEditDiv').style.display = modeRadioFilter.checked ? "block" : "none";
	$('filterCustomerDiv').style.display = modeRadioFilter.checked ? "block" : "none";

EOS;
	}

	/**
	 * Creates a HTML div
	 *
	 * @param string $content  Content of the div
	 * @param string $divId id of the div
	 * @param boolean $isVisible
	 * @param integer $marginLeft
	 * @static
	 * @return string
	 */
	function getDiv($content = '', $divId = '', $isVisible = true, $marginLeft = 0){
		return '<div' . ($divId ? (' id="' . $divId . '"') : '') . ' style="display:' . ($isVisible ? 'block' : 'none') . ';margin-left:' . $marginLeft . 'px;">' . $content . '</div>';
	}

	/**
	 * Creates a multi edit gui element for use in customerFilterViews
	 *
	 * @param string $name
	 * @param array $data
	 * @param string $headline
	 * @param boolean $isVisible
	 * @return string
	 */
	private function getMultiEdit($name, $data, $headline = "", $isVisible = true){
		static $settingsSQL = false;
		if(!$settingsSQL){
			$settings = new we_customer_settings();
			$settings->load();
			$settingsSQL = $settings->treeTextFormatSQL;
		}
		$delBut = addslashes(we_html_button::create_button(we_html_button::TRASH, "javascript:#####placeHolder#####;wecf_hot();"));
		$script = <<<EO_SCRIPT

var $name = new multi_edit("{$name}MultiEdit",document.we_form,0,"$delBut",$this->width,false);
$name.addVariant();
$name.addVariant();
document.we_form.{$name}Control.value = $name.name;

EO_SCRIPT;


		if($data){
			$db = new DB_WE();
			$data = $db->getAllq('SELECT ID,' . $settingsSQL . ' AS Text FROM ' . CUSTOMER_TABLE . ' WHERE ID IN (' . implode(',', $data) . ') ORDER BY Text', false);
			foreach($data as $dat){
				$script .= $name . '.addItem();' .
					$name . '.setItem(0,(' . $name . '.itemCount-1),"' . $dat['Text'] . '");' .
					$name . '.setItem(1,(' . $name . '.itemCount-1),"' . $dat['ID'] . '");';
			}
		}

		$script .= $name . '.showVariant(0);';

		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','fillIDs(true);opener.addToMultiEdit(opener." . $name . ", top.allTexts,top.allIDs);opener.wecf_hot();','','','',1)");

		$buttonTable = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeFromMultiEdit(" . $name . ")") . $addbut;

		$select = we_html_element::htmlHiddens(array(
				$name . 'Control' => we_base_request::_(we_base_request::RAW, $name . 'Control', 0),
				$name . 'Count' => (isset($data) ? count($data) : '0')
			)) .
			($headline ? '<div class="defaultfont">' . $headline . '</div>' : '') .
			'<div id="' . $name . 'MultiEdit" style="overflow:auto;background-color:white;padding:5px;width:' . $this->width . 'px; height: 120px; border: #AAAAAA solid 1px;margin-bottom:5px;"></div>' .
			'<div style="width:' . ($this->width + 13) . 'px;text-align:right">' . $buttonTable . '</div>';
		return array(self::getDiv($select, $name . 'Div', $isVisible, 22), $script);
	}

	function getHTMLCustomerFilter($startEmpty = false){
		$filter_args = array();

		$GLOBALS['DB_WE']->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
		while($GLOBALS['DB_WE']->next_record()){
			$filter_args[$GLOBALS['DB_WE']->f("Field")] = $GLOBALS['DB_WE']->f("Field");
		}
		$filter_args = we_html_tools::groupArray($filter_args);
		$filter_op = array(
			we_customer_abstractFilter::OP_EQ => g_l('modules_customerFilter', '[equal]'),
			we_customer_abstractFilter::OP_NEQ => g_l('modules_customerFilter', '[not_equal]'),
			we_customer_abstractFilter::OP_LESS => g_l('modules_customerFilter', '[less]'),
			we_customer_abstractFilter::OP_LEQ => g_l('modules_customerFilter', '[less_equal]'),
			we_customer_abstractFilter::OP_GREATER => g_l('modules_customerFilter', '[greater]'),
			we_customer_abstractFilter::OP_GEQ => g_l('modules_customerFilter', '[greater_equal]'),
			we_customer_abstractFilter::OP_STARTS_WITH => g_l('modules_customerFilter', '[starts_with]'),
			we_customer_abstractFilter::OP_ENDS_WITH => g_l('modules_customerFilter', '[ends_with]'),
			we_customer_abstractFilter::OP_CONTAINS => g_l('modules_customerFilter', '[contains]'),
			we_customer_abstractFilter::OP_NOT_CONTAINS => g_l('modules_customerFilter', '[not_contains]'),
			we_customer_abstractFilter::OP_IN => g_l('modules_customerFilter', '[in]'),
			we_customer_abstractFilter::OP_NOT_IN => g_l('modules_customerFilter', '[not_in]'),
		);

		$filter_logic = array(
			'AND' => g_l('modules_customerFilter', '[AND]'),
			'OR' => g_l('modules_customerFilter', '[OR]')
		);

		$filter = $this->filter->getFilter();

		if(!$startEmpty && empty($filter)){
			$filter = array(
				array(
					'logic' => '',
					'field' => 'id',
					'operation' => 0,
					'value' => ''
				)
			);
			$this->filter->setFilter($filter);
		}

		$i = 0;
		$adv_row = '';

		foreach($filter as $key => $value){
			if(!is_array($value)||empty($value)){
				continue;
			}
			$value['logic'] = trim($value['logic']);
			$adv_row .= '
<tr id="filterRow_' . $i . '">
	<td style="padding-top: ' . ($value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';width:64px;">' .
				(($i == 0) ? '' : we_html_tools::htmlSelect('filterLogic_' . $i, $filter_logic, 1, $value['logic'], false, array('onchange' => "wecf_logic_changed(this);", 'class' => "defaultfont logicFilterInput"))) . '</td>
	<td style="padding-top: ' . ($value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_tools::htmlSelect('filterSelect_' . $i, $filter_args, 1, $value['field'], false, array('onchange' => "wecf_hot();", 'class' => "defaultfont leftFilterInput")) . '</td>
	<td style="padding-top: ' . ($value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_tools::htmlSelect('filterOperation_' . $i, $filter_op, 1, $value['operation'], false, array('onchange' => "wecf_hot();", 'class' => "defaultfont middleFilterInput")) . '</td>
	<td style="padding-top: ' . ($value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				'<input name="filterValue_' . $i . '" value="' . $value['value'] . '" type="text" onchange="wecf_hot();" class="defaultfont rightFilterInput"/></td>
	<td style="padding-top: ' . ($value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_button::create_button(we_html_button::PLUS, "javascript:addRow(" . ($i + 1) . ")", true, 25) . '</td>
	<td style="padding-left:5px;padding-top: ' . ($value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($filter[$key + 1]) && $filter[$key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';width:25px;">' .
				(($i != 0 || $startEmpty) ? we_html_button::create_button(we_html_button::TRASH, "javascript:delRow($i)") : '') . '</td>
</tr>';
			$i++;
		}

		return
			we_html_element::jsElement('
var filter={
	logic:\'' . we_html_tools::htmlSelect('', $filter_logic) . '\',
	args:\'' . we_html_tools::htmlSelect('', $filter_args) . '\',
	op:\'' . we_html_tools::htmlSelect('', $filter_op) . '\'
};
var buttons={
	add:\'' . we_html_button::create_button(we_html_button::PLUS, "javascript:addRow(__CNT__)") . '\',
	trash:\'' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__CNT__)") . '\'
};') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_filter.js') . '
<table class="default" style="width:' . $this->width . 'px;height:50px;">
	<tbody id="filterTable">
		' . $adv_row . '
	</tbody>
</table>' .
			($filter ? '' : '<div>' . we_html_button::create_button(we_html_button::PLUS, "javascript:addRow();") . '</div>');
	}

	/* #########################################################################################
	  ############################### mutator and accessor methods ##############################
	  ######################################################################################### */

	/**
	 * accessor method for $this->_filter
	 *
	 * @return we_customer_abstractFilter
	 */
	function getFilter(){
		return $this->filter;
	}

	/**
	 * mutator method for $this->_filter
	 *
	 * @param we_customer_abstractFilter $filter
	 */
	function setFilter(&$filter){
		$this->filter = $filter;
	}

	/**
	 * accessor method for $this->_hotScript
	 *
	 * @return string
	 */
	function getHotScript(){
		return $this->hotScript;
	}

	/**
	 * mutator method for $this->_hotScript
	 *
	 * @param string $hotScript
	 */
	function setHotScript($hotScript){
		$this->hotScript = $hotScript;
	}

	/**
	 * accessor method for $this->_width
	 *
	 * @return integer
	 */
	function getWidth(){
		return $this->width;
	}

	/**
	 * mutator method for $this->_width
	 *
	 * @param integer $width
	 */
	function setWidth($width){
		$this->width = $width;
	}

}
