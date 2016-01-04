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
	var $_filter = null;

	/**
	 * Javascript call for making the document hot
	 *
	 * @var string
	 */
	var $_hotScript = '';

	/**
	 * width of filter
	 *
	 * @var integer
	 */
	var $_width = 0;

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
		$_script = '
function wecf_hot() {' .
			$this->_hotScript . '
}

function updateView() {' .
			$this->createUpdateViewScript() . '
}';
		$mode = $this->_filter->getMode();

		// ################# Radio buttons ###############
		$_modeRadioOff = we_html_forms::radiobutton(we_customer_abstractFilter::OFF, $mode === we_customer_abstractFilter::OFF, 'wecf_mode', g_l('modules_customerFilter', '[mode_off]'), true, "defaultfont", "wecf_hot();updateView();");
		$_modeRadioNone = ($ShowModeNone ?
				we_html_forms::radiobutton(we_customer_abstractFilter::NOT_LOGGED_IN_USERS, $mode === we_customer_abstractFilter::NOT_LOGGED_IN_USERS, 'wecf_mode', g_l('modules_customerFilter', '[mode_none]'), true, "defaultfont", "wecf_hot();updateView();") :
				'');

		$_modeRadioAll = we_html_forms::radiobutton(we_customer_abstractFilter::ALL, $mode === we_customer_abstractFilter::ALL, 'wecf_mode', g_l('modules_customerFilter', '[mode_all]'), true, "defaultfont", "wecf_hot();updateView();");
		$_modeRadioSpecific = we_html_forms::radiobutton(we_customer_abstractFilter::SPECIFIC, $mode === we_customer_abstractFilter::SPECIFIC, 'wecf_mode', g_l('modules_customerFilter', '[mode_specific]'), true, "defaultfont", "wecf_hot();updateView();");
		$_modeRadioFilter = we_html_forms::radiobutton(we_customer_abstractFilter::FILTER, $mode === we_customer_abstractFilter::FILTER, 'wecf_mode', g_l('modules_customerFilter', '[mode_filter]'), true, "defaultfont", "wecf_hot();updateView();");

		// ################# Selector for specific customers ###############
		list($_specificCustomersSelect, $script) = $this->getMultiEdit('specificCustomersEdit', id_to_path($this->_filter->getSpecificCustomers(), CUSTOMER_TABLE, null, false, true), "", $mode === we_customer_abstractFilter::SPECIFIC);
		$_script.=$script;
		// ################# Selector blacklist ###############

		list($_blackListSelect, $script) = $this->getMultiEdit('blackListEdit', id_to_path($this->_filter->getBlackList(), CUSTOMER_TABLE, null, false, true), g_l('modules_customerFilter', '[black_list]'), $mode === we_customer_abstractFilter::FILTER);
		$_script.=$script;
		// ################# Selector for whitelist ###############

		list($_whiteListSelect, $script) = $this->getMultiEdit('whiteListEdit', id_to_path($this->_filter->getWhiteList(), CUSTOMER_TABLE, null, false, true), g_l('modules_customerFilter', '[white_list]'), $mode === we_customer_abstractFilter::FILTER);
		$_script.=$script;
		// ################# customer filter ###############

		$_filterCustomers = we_customer_filterView::getDiv($this->getHTMLCustomerFilter(), 'filterCustomerDiv', $mode === we_customer_abstractFilter::FILTER, 25);


		// ################# concate and output #################

		$_space = '<div style="height:4px;"></div>';

		return we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_filterLogic.js') . $_modeRadioOff . $_space . $_modeRadioAll . $_space . $_modeRadioSpecific . $_space . $_specificCustomersSelect . $_space . $_modeRadioFilter . $_filterCustomers . $_blackListSelect . $_whiteListSelect . $_space . $_modeRadioNone . we_html_element::jsElement($_script);
	}

	public function getFilterCustomers(){
		$this->_filter->setMode(we_customer_abstractFilter::FILTER);

		return we_html_element::jsElement('
function $(id) {
	return document.getElementById(id);
}

function updateView() {' .
				$this->createUpdateViewScript() . '
}

function wecf_hot() {' .
				$this->_hotScript . '
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
		return '<div' . ($divId ? (' id="' . $divId . '"') : '') . ' style="display:' . ($isVisible ? 'block' : 'none') . ';margin-left:' . $marginLeft . 'px;margin-top:5px;margin-bottom:10px;">' . $content . '</div>';
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
		$_delBut = addslashes(we_html_button::create_button(we_html_button::TRASH, "javascript:#####placeHolder#####;wecf_hot();"));
		$_script = <<<EO_SCRIPT

var $name = new multi_edit("{$name}MultiEdit",document.we_form,0,"$_delBut",$this->_width,false);
$name.addVariant();
document.we_form.{$name}Control.value = $name.name;

EO_SCRIPT;


		if(is_array($data)){
			foreach($data as $_dat){
				$_script .= $name . '.addItem();' .
					$name . '.setItem(0,(' . $name . '.itemCount-1),"' . $_dat . '");';
			}
		}

		$_script .= $name . '.showVariant(0);';

		$_addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.addToMultiEdit(opener." . $name . ", top.allPaths);opener.wecf_hot();','','','',1)");

		$_buttonTable = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:removeFromMultiEdit(" . $name . ")") . $_addbut;

		$_select = we_html_tools::hidden($name . 'Control', we_base_request::_(we_base_request::RAW, $name . 'Control', 0)) .
			we_html_tools::hidden($name . 'Count', (isset($data) ? count($data) : '0')) .
			($headline ? '<div class="defaultfont">' . $headline . '</div>' : '') .
			'<div id="' . $name . 'MultiEdit" style="overflow:auto;background-color:white;padding:5px;width:' . $this->_width . 'px; height: 120px; border: #AAAAAA solid 1px;margin-bottom:5px;"></div>' .
			'<div style="width:' . ($this->_width + 13) . 'px;text-align:right">' . $_buttonTable . '</div>';
		return array(self::getDiv($_select, $name . 'Div', $isVisible, 22), $_script);
	}

	function getHTMLCustomerFilter($startEmpty = false){
		$_filter_args = array();

		$GLOBALS['DB_WE']->query('SHOW FIELDS FROM ' . CUSTOMER_TABLE);
		while($GLOBALS['DB_WE']->next_record()){
			$_filter_args[$GLOBALS['DB_WE']->f("Field")] = $GLOBALS['DB_WE']->f("Field");
		}
		$_filter_args = we_html_tools::groupArray($_filter_args);
		$_filter_op = array(
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

		$_filter_logic = array(
			'AND' => g_l('modules_customerFilter', '[AND]'),
			'OR' => g_l('modules_customerFilter', '[OR]')
		);

		$_filter = $this->_filter->getFilter();

		if(!$startEmpty && empty($_filter)){
			$this->_filter->setFilter(
				array(
					array(
						'logic' => '',
						'field' => 'id',
						'operation' => 0,
						'value' => ''
					)
				)
			);
		}

		$_i = 0;
		$_adv_row = '';
		$_first = 0;

		$_filter = $this->_filter->getFilter();
		foreach($_filter as $_key => $_value){
			$_adv_row .= '
				<tr id="filterRow_' . $_i . '">
					<td style="padding-top: ' . ($_value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';width:64px;">' .
				(($_i == 0) ? '' : we_html_tools::htmlSelect('filterLogic_' . $_i, $_filter_logic, 1, $_value['logic'], false, array('onchange' => "wecf_logic_changed(this);", 'class' => "defaultfont logicFilterInput"))) .
				'</td>

					<td style="padding-top: ' . ($_value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_tools::htmlSelect('filterSelect_' . $_i, $_filter_args, 1, $_value['field'], false, array('onchange' => "wecf_hot();", 'class' => "defaultfont leftFilterInput")) .
				'</td>

					<td style="padding-top: ' . ($_value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_tools::htmlSelect('filterOperation_' . $_i, $_filter_op, 1, $_value['operation'], false, array('onchange' => "wecf_hot();", 'class' => "defaultfont middleFilterInput")) .
				'</td>

					<td style="padding-top: ' . ($_value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				'<input name="filterValue_' . $_i . '" value="' . $_value['value'] . '" type="text" onchange="wecf_hot();" class="defaultfont rightFilterInput"/>' .
				'</td>
					<td style="padding-top: ' . ($_value['logic'] === 'OR' ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';">' .
				we_html_button::create_button(we_html_button::PLUS, "javascript:addRow(" . ($_i + 1) . ")", true, 25) .
				'</td>
					<td style="padding-left:5px;padding-top: ' . ($_value['logic'] === "OR" ? "10px;border-top:1px solid grey" : "4px;border-top:0") . ';padding-bottom:' .
				((isset($_filter[$_key + 1]) && $_filter[$_key + 1]['logic'] === 'OR') ? '10px' : '0px') . ';width:25px;">' .
				(($_i == 0) ? '' : we_html_button::create_button(we_html_button::TRASH, "javascript:delRow($_i)", true, 25)) .
				'</td>
				</tr>';
			$_i++;
			$_first = 1;
		}

		$_filter_logic_str = we_html_tools::htmlSelect('', $_filter_logic);
		$_filter_args_str = we_html_tools::htmlSelect('', $_filter_args);
		$_filter_op_str = we_html_tools::htmlSelect('', $_filter_op);

		$_filterTable = '
		<table class="default" width="' . $this->_width . ' height="50">
			<tbody id="filterTable">
				' . $_adv_row . '
			</tbody>
		</table>
		';


		return
			we_html_element::jsElement('
var filter={
	"logic":\'' . $_filter_logic_str . '\',
	"args":\'' . $_filter_args_str . '\',
	"op":\'' . $_filter_op_str . '\'
};
var buttons={
	"add":\'' . we_html_button::create_button(we_html_button::PLUS, "javascript:addRow(__CNT__)", true, 25) . '\',
	"trash":\'' . we_html_button::create_button(we_html_button::TRASH, "javascript:delRow(__CNT__)", true, 25) . '\'
};') .
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_filter.js') .
			$_filterTable .
			'<div style="height:5px;"></div>' .
			we_html_button::create_button(we_html_button::PLUS, "javascript:addRow()");
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
		return $this->_filter;
	}

	/**
	 * mutator method for $this->_filter
	 *
	 * @param we_customer_abstractFilter $filter
	 */
	function setFilter(&$filter){
		$this->_filter = $filter;
	}

	/**
	 * accessor method for $this->_hotScript
	 *
	 * @return string
	 */
	function getHotScript(){
		return $this->_hotScript;
	}

	/**
	 * mutator method for $this->_hotScript
	 *
	 * @param string $hotScript
	 */
	function setHotScript($hotScript){
		$this->_hotScript = $hotScript;
	}

	/**
	 * accessor method for $this->_width
	 *
	 * @return integer
	 */
	function getWidth(){
		return $this->_width;
	}

	/**
	 * mutator method for $this->_width
	 *
	 * @param integer $width
	 */
	function setWidth($width){
		$this->_width = $width;
	}

}
