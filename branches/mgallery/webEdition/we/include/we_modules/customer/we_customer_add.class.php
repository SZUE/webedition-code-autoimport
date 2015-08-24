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
abstract class we_customer_add{
	static $operators = array('=', '<>', '<', '<=', '>', '>=', 'LIKE');

	static function getHTMLSortEditor(we_customer_frames &$pob){
		$branch = $pob->View->getHTMLBranchSelect();
		$branch->setOptionVT(1, g_l('modules_customer', '[other]'), g_l('modules_customer', '[other]'));


		$order = new we_html_select(array('name' => 'order', 'style' => 'width:90px;'));
		foreach($pob->View->settings->OrderTable as $ord){
			$order->addOption($ord, $ord);
		}

		$function = new we_html_select(array('name' => 'function', 'style' => 'width:130px;'));

		$counter = 0;
		$fhidden = '';

		$_parts = array();
		foreach($pob->View->settings->SortView as $k => $sorts){
			$fcounter = 0;
			$row_num = 0;

			$sort_table = new we_html_table(array('width' => 400, 'height' => 50), 1, 5);
			$sort_table->setCol(0, 0, array('class' => 'defaultfont'), we_html_element::htmlB(g_l('modules_customer', '[sort_branch]')));
			$sort_table->setCol(0, 1, array('class' => 'defaultfont'), we_html_element::htmlB(g_l('modules_customer', '[sort_field]')));
			//$sort_table->setCol(0, 2, array('class' => 'defaultfont'), we_html_element::htmlB(g_l('modules_customer', '[sort_function]')));
			$sort_table->setCol(0, 3, array('class' => 'defaultfont'), we_html_element::htmlB(g_l('modules_customer', '[sort_order]')));


			foreach($sorts as $sort){
				if(!$sort["branch"]){
					$branches_names = $pob->View->customer->getBranchesNames();
					$sort["branch"] = (isset($branches_names[0]) ?
							$branches_names[0] :
							g_l('modules_customer', '[common]'));
				}

				$branch->setAttributes(array("name" => "branch_" . $counter . '_' . $fcounter, "class" => "weSelect", "onchange" => "we_cmd('selectBranch')", "style" => "width:180px;"));
				$branch->selectOption($sort["branch"]);

				$field = $pob->getHTMLFieldsSelect($sort["branch"]);
				$field->setAttributes(array("name" => "field_" . $counter . '_' . $fcounter, "style" => "width:180px;", "class" => "weSelect", "onchange" => "we_cmd('selectBranch')"));

				$fields_names = array_keys($pob->View->customer->getFieldsNames($sort["branch"]));
				if($sort["branch"] == g_l('modules_customer', '[common]') || $sort["branch"] == g_l('modules_customer', '[other]')){
					foreach($fields_names as &$fnv){
						$fnv = str_replace($sort["branch"] . "_", "", $fnv);
					}
					unset($fnv);
				}

				if(!isset($sort["field"])){
					$sort["field"] = "";
				}

				if(is_array($fields_names)){
					if(!in_array($sort["field"], $fields_names)){
						$sort["field"] = array_shift($fields_names);
					}
				}

				if($sort["branch"] == g_l('modules_customer', '[common]') && isset($sort["field"])){
					$field->selectOption(g_l('modules_customer', '[common]') . "_" . $sort["field"]);
				} else if(isset($sort["field"])){
					$field->selectOption($sort["field"]);
				}

				$function->setAttributes(array("name" => "function_" . $counter . "_" . $fcounter, "class" => "weSelect",));

				$function->delAllOptions();
				$function->addOption('', '');
				foreach(array_keys($pob->View->settings->FunctionTable) as $ftk){
					if(isset($sort["field"])/* && $pob->View->settings->isFunctionForField($ftk, $sort["field"]) */){
						$function->addOption($ftk, g_l('modules_customer', '[filter][' . $ftk . ']'));
					}
				}

				if(isset($sort['function'])){
					$function->selectOption($sort['function']);
				}

				$order->setAttributes(array('name' => 'order_' . $counter . '_' . $fcounter, 'class' => 'weSelect',));
				$order->selectOption($sort['order']);

				$row_num = $fcounter + 1;
				$sort_table->addRow();
				$sort_table->setCol($row_num, 0, array("class" => "defaultfont"), $branch->getHtml());
				$sort_table->setCol($row_num, 1, array("class" => "defaultfont"), $field->getHtml());
				$sort_table->setCol($row_num, 2, array("class" => "defaultfont"), $function->getHtml());
				$sort_table->setCol($row_num, 3, array("class" => "defaultfont"), $order->getHtml());
				$sort_table->setCol($row_num, 4, array("class" => "defaultfont"), we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_sort_field','" . $k . "',$fcounter)", true, 30));

				$fcounter++;
			}

			$sort_table->addRow();
			$row_num++;
			$sort_table->setCol($row_num, 4, array('style' => 'padding-top:5px;'), we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_sort_field',document.we_form.sort_" . $counter . ".value)", true, 30));


			$fhidden.=we_html_element::htmlHidden("fcounter_" . $counter, "$fcounter");

			$_htmlCode = $pob->getHTMLBox(we_html_element::htmlInput(array("name" => "sort_" . $counter, "value" => $k, "size" => 40)), g_l('modules_customer', '[name]'), 100, 50, 25, 0, 0, 50) .
				$sort_table->getHtml() .
				we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_sort','" . $k . "')");

			$_parts[] = array('html' => $_htmlCode, 'headline' => $k);

			$counter++;
		}

		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_sort')");

		$_buttons = we_html_button::position_yes_no_cancel($save, null, $cancel);

		$add_button = we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_sort')") . we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_customer', '[add_sort_group]'));
		$_parts[] = array('html' => $add_button);

		$sort_code = we_html_multiIconBox::getHTML("", $_parts, 30, $_buttons, -1, "", "", false, "", "", 459) .
			we_html_element::htmlComment("hiddens start") .
			we_html_element::htmlHiddens(array(
				"pnt" => "sort_admin",
				"cmd" => "",
				"counter" => "$counter",
				"sortindex" => "",
				"fieldindex" => "")) .
			$fhidden .
			we_html_element::htmlComment("hiddens ends");




		$out = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "doScrollTo()"), self::getJSSortAdmin($pob->View) .
				we_html_element::htmlForm(array("name" => "we_form"), $sort_code
				)
		);

		return $pob->getHTMLDocument($out);
	}

	public static function getJSSortAdmin($pob){
		return we_html_element::jsElement('
var frames={
	"set":"' . $pob->frameset . '"
};

var g_l={
	"default_soting_no_del": "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[default_soting_no_del]')) . '",
	"sortname_empty": "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[sortname_empty]')) . '",
};

var settings={
	"default_sort_view":"' . $pob->settings->getSettings('default_sort_view') . '"
};

function doScrollTo(){
	if(opener.' . $pob->topFrame . '.scrollToVal){
		window.scrollTo(0,opener.' . $pob->topFrame . '.scrollToVal);
		opener.' . $pob->topFrame . '.scrollToVal=0;
	}
}

function setScrollTo(){
		opener.' . $pob->topFrame . '.scrollToVal=pageYOffset;
}' .
				$pob->getJSSubmitFunction("sort_admin")) .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_sortAdmin.js');
	}

	public static function getHTMLSearch(&$pob, &$search, &$select){
		$count = we_base_request::_(we_base_request::INT, 'count');

		$logic = array('AND' => 'AND', 'OR' => 'OR');

		$search_arr = array();

		$search_but = we_html_button::create_button(we_html_button::SEARCH, "javascript:we_cmd('search')");
		$colspan = 4;

		for($i = 0; $i < $count; $i++){
			if(($branch = we_base_request::_(we_base_request::STRING, 'branch_' . $i))){
				$search_arr['logic_' . $i] = we_base_request::_(we_base_request::STRING, 'logic_' . $i);
				$search_arr['branch_' . $i] = we_base_request::_(we_base_request::STRING, 'branch_' . $i);
				$search_arr['field_' . $i] = we_base_request::_(we_base_request::STRING, 'field_' . $i);
				$search_arr['operator_' . $i] = we_base_request::_(we_base_request::INT, 'operator_' . $i);
				$search_arr['value_' . $i] = we_base_request::_(we_base_request::STRING, 'value_' . $i);
			}
		}


		$advsearch = new we_html_table(array(), 1, 4);
		$branch = $pob->View->getHTMLBranchSelect();
		$branch->setOptionVT(1, g_l('modules_customer', '[other]'), g_l('modules_customer', '[other]'));

		$field = $pob->getHTMLFieldsSelect(g_l('modules_customer', '[common]'));

		$c = 0;

		for($i = 0; $i < $count; $i++){

			if(isset($search_arr["branch_" . $i])){
				$branch->selectOption($search_arr["branch_" . $i]);
				$field = (!$search_arr["branch_" . $i] ?
						$pob->getHTMLFieldsSelect(g_l('modules_customer', '[common]')) :
						$pob->getHTMLFieldsSelect($search_arr["branch_" . $i]));
			}

			if(isset($search_arr["field_" . $i])){
				$field->selectOption($search_arr["field_" . $i]);
			}

			$branch->setAttributes(array("name" => "branch_" . $i, "onchange" => "we_cmd('selectBranch')", "style" => "width:145px"));
			$field->setAttributes(array("name" => "field_" . $i, "style" => "width:145px", "onchange" => "isDateField($i)"));

			if($i != 0){
				$advsearch->addRow();
				$advsearch->setCol($c, 0, array("colspan" => $colspan), we_html_tools::htmlSelect("logic_" . $i, $logic, 1, (isset($search_arr["logic_" . $i]) ? $search_arr["logic_" . $i] : ""), false, array(), "value", 70));
				++$c;
			}
			$value_i = we_html_tools::htmlTextInput("value_" . $i, 20, (isset($search_arr["value_" . $i]) ? $search_arr["value_" . $i] : ""), "", "id='value_$i'", "text", 185);
			$value_date_i = we_html_tools::htmlTextInput("value_date_$i", 20, "", "", "id='value_date_$i' style='display:none; width:150' readonly", "text", ""); // empty field to display the timestemp in date formate - handeld on the client in js
			$btnDatePicker = we_html_button::create_button(we_html_button::CALENDAR, "javascript:", null, null, null, null, null, null, false, "_$i");
			$advsearch->addRow();
			$advsearch->setCol($c, 0, array(), $branch->getHtml());
			$advsearch->setCol($c, 1, array(), $field->getHtml());
			$advsearch->setCol($c, 2, array(), we_html_tools::htmlSelect("operator_" . $i, self::$operators, 1, (isset($search_arr["operator_" . $i]) ? $search_arr["operator_" . $i] : ""), false, array(), "value", 60));
			$advsearch->setCol($c, 3, array("width" => 190), "<table class='default'><tr><td>" . $value_i . $value_date_i . "</td><td id='dpzell_$i' style='display:none; padding-left:5px;text-align:right'>$btnDatePicker</td></tr></table>");
			++$c;
		}

		$advsearch->addRow();
		$advsearch->setCol($c, 0, array("colspan" => $colspan,'style'=>'padding-top:5px;'), we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_search')") .
			we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_search')")
		);

		$search->setCol(1, 0, array(), we_html_element::htmlHidden("count", $count) . $advsearch->getHtml());
		$search->setCol(3, 0, array("colspan" => $colspan, 'style' => 'text-align:right;padding-top:5px;'), "<table class='default'><tr><td>" .
			we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_customer', '[simple_search]')) .
			we_html_button::create_button("fa:btn_direction_left,fa-lg fa-caret-left", "javascript:we_cmd('switchToSimple')") .
			$search_but
			. '</td><td>&nbsp;</td></tr></table>'
		);
		$max_res = $pob->View->settings->getMaxSearchResults();
		$result = ($search_arr && we_base_request::_(we_base_request::BOOL, 'search') ? self::getAdvSearchResults($pob->db, $search_arr, $count, $max_res) : array());

		foreach($result as $id => $text){
			$select->addOption($id, $text);
		}
	}

	static function getAdvSearchResults(we_database_base $db, $keywords, $count, $res_num){
		$where = '';

		for($i = 0; $i < $count; $i++){
			if(isset($keywords['field_' . $i])){
				$keywords['field_' . $i] = str_replace(g_l('modules_customer', '[common]') . '_', '', $keywords['field_' . $i]);
			}
			if(isset($keywords['field_' . $i]) && isset($keywords["operator_" . $i]) && isset($keywords["value_" . $i])){
				$where.=
					(isset($keywords['logic_' . $i]) ? ' ' . $keywords['logic_' . $i] . ' ' : '') .
					$keywords['field_' . $i] . ' ' . self::$operators[$keywords['operator_' . $i]] . " '" .
					(is_numeric($keywords['value_' . $i]) ? $keywords['value_' . $i] : $db->escape($keywords['value_' . $i])) .
					"'";
			}
		}

		$db->query('SELECT ID,CONCAT(Username, " (",Forename," ",Surname,")") AS user FROM ' . CUSTOMER_TABLE . ' WHERE ' . (empty($where) ? 0 : $where) . ' ORDER BY Text LIMIT 0,' . $res_num);
		return $db->getAllFirst(false);
	}

	static function getHTMLTreeHeader(&$pob){
		$select = $pob->View->getHTMLSortSelect();
		$select->setAttributes(array('onchange' => 'applySort();', 'style' => 'width:150px'));
		$select->selectOption($pob->View->settings->getSettings('default_sort_view'));

		$table = $select->getHtml() .
			we_html_button::create_button(we_html_button::RELOAD, "javascript:applySort();") .
			we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('show_sort_admin')");

		return we_html_element::htmlForm(array("name" => "we_form_treeheader", 'style' => 'margin:5px'), we_html_element::htmlHiddens(array(
					"pnt" => "treeheader",
					"pid" => 0,
					"cmd" => "no_cmd")) .
				$table
		);
	}

}
