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
	static $operators = ['=', '<>', '<', '<=', '>', '>=', 'LIKE'];

	static function getHTMLSortEditor(we_customer_frames &$pob){
		$branch = $pob->View->getHTMLBranchSelect();
		$branch->setOptionVT(1, g_l('modules_customer', '[other]'), g_l('modules_customer', '[other]'));


		$order = new we_html_select(['name' => 'order', 'style' => 'width:90px;']);
		foreach($pob->View->settings->OrderTable as $ord){
			$order->addOption($ord, $ord);
		}

		$function = new we_html_select(['name' => 'function', 'style' => 'width:130px;']);

		$counter = 0;
		$fhidden = '';

		$parts = [];
		foreach($pob->View->settings->SortView as $k => $sorts){
			$fcounter = 0;
			$row_num = 0;

			$sort_table = new we_html_table(['width' => 400, 'height' => 50], 1, 5);
			$sort_table->setCol(0, 0, ['class' => 'defaultfont'], we_html_element::htmlB(g_l('modules_customer', '[sort_branch]')));
			$sort_table->setCol(0, 1, ['class' => 'defaultfont'], we_html_element::htmlB(g_l('modules_customer', '[sort_field]')));
			//$sort_table->setCol(0, 2, ['class' => 'defaultfont'], we_html_element::htmlB(g_l('modules_customer', '[sort_function]')));
			$sort_table->setCol(0, 3, ['class' => 'defaultfont'], we_html_element::htmlB(g_l('modules_customer', '[sort_order]')));


			foreach($sorts as $sort){
				if(!$sort["branch"]){
					$branches_names = $pob->View->customer->getBranchesNames();
					$sort["branch"] = (isset($branches_names[0]) ?
							$branches_names[0] :
							g_l('modules_customer', '[common]'));
				}

				$branch->setAttributes(['name' => "branch_" . $counter . '_' . $fcounter, "class" => "weSelect", "onchange" => "we_cmd('selectBranch')", 'style' => "width:180px;"]);
				$branch->selectOption($sort["branch"]);

				$field = $pob->getHTMLFieldsSelect($sort["branch"]);
				$field->setAttributes(['name' => "field_" . $counter . '_' . $fcounter, 'style' => "width:180px;", "class" => "weSelect", "onchange" => "we_cmd('selectBranch')"]);

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

				$function->setAttributes(['name' => "function_" . $counter . "_" . $fcounter, "class" => "weSelect",]);

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

				$order->setAttributes(['name' => 'order_' . $counter . '_' . $fcounter, 'class' => 'weSelect',]);
				$order->selectOption($sort['order']);

				$row_num = $fcounter + 1;
				$sort_table->addRow();
				$sort_table->setCol($row_num, 0, ['class' => 'defaultfont'], $branch->getHtml());
				$sort_table->setCol($row_num, 1, ['class' => 'defaultfont'], $field->getHtml());
				$sort_table->setCol($row_num, 2, ['class' => 'defaultfont'], $function->getHtml());
				$sort_table->setCol($row_num, 3, ['class' => 'defaultfont'], $order->getHtml());
				$sort_table->setCol($row_num, 4, ['class' => 'defaultfont'], we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_sort_field','" . $k . "',$fcounter)"));

				$fcounter++;
			}

			$sort_table->addRow();
			$row_num++;
			$sort_table->setCol($row_num, 4, ['style' => 'padding-top:5px;'], we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_sort_field',document.we_form.sort_" . $counter . ".value)"));


			$fhidden.=we_html_element::htmlHidden("fcounter_" . $counter, "$fcounter");

			$htmlCode = $pob->getHTMLBox(we_html_element::htmlInput(['name' => "sort_" . $counter, "value" => $k, "size" => 40]), g_l('modules_customer', '[name]'), 100, 50, 25, 0, 0, 50) .
				$sort_table->getHtml() .
				we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_sort','" . $k . "')");

			$parts[] = ['html' => $htmlCode, 'headline' => $k];

			$counter++;
		}

		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_sort')");

		$buttons = we_html_button::position_yes_no_cancel($save, null, $cancel);

		$add_button = we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_sort')") . we_html_element::htmlDiv(['class' => "defaultfont lowContrast"], g_l('modules_customer', '[add_sort_group]'));
		$parts[] = ['html' => $add_button];

		$sort_code = we_html_multiIconBox::getHTML("", $parts, 30, $buttons) .
			we_html_element::htmlComment("hiddens start") .
			we_html_element::htmlHiddens(["pnt" => "sort_admin",
				"cmd" => "",
				"counter" => "$counter",
				"sortindex" => "",
				"fieldindex" => ""]) .
			$fhidden .
			we_html_element::htmlComment("hiddens ends");

		$out = we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "doScrollTo()"], we_html_element::jsScript(WE_JS_MODULES_DIR . 'customer/customer_sortAdmin.js', '', ['id' => 'loadVarCustomer_sortAdmin', 'data-settings' => setDynamicVar([
						'default_sort_view' => $pob->View->settings->getSettings('default_sort_view')
				])]) .
				we_html_element::htmlForm(['name' => 'we_form'], $sort_code)
		);

		return $pob->getHTMLDocument($out);
	}

	public static function getHTMLSearch(&$pob, &$search, &$select){
		$count = we_base_request::_(we_base_request::INT, 'count');

		$logic = ['AND' => 'AND', 'OR' => 'OR'];

		$search_arr = [];

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

		$advsearch = new we_html_table([], 1, 4);
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

			$branch->setAttributes(['name' => "branch_" . $i, "class" => 'weSelect', "onchange" => "we_cmd('selectBranch')", 'style' => "width:145px"]);
			$field->setAttributes(['name' => "field_" . $i, 'style' => "width:145px", "class" => 'weSelect', "onchange" => "isDateField($i)"]);

			if($i != 0){
				$advsearch->addRow();
				$advsearch->setCol($c, 0, ["colspan" => $colspan], we_html_tools::htmlSelect("logic_" . $i, $logic, 1, (isset($search_arr["logic_" . $i]) ? $search_arr["logic_" . $i] : ""), false, [], "value", 70));
				++$c;
			}
			$value_i = we_html_tools::htmlTextInput("value_" . $i, 20, (isset($search_arr["value_" . $i]) ? $search_arr["value_" . $i] : ""), "", "id='value_$i'", "text", 185);
			$value_date_i = we_html_tools::htmlTextInput("value_date_$i", 20, "", "", "id='value_date_$i' class='datepicker' style='display:none; width:150px' readonly='readonly'", "text", ""); // empty field to display the timestemp in date formate - handeld on the client in js
			$btnDatePicker = we_html_button::create_button(we_html_button::CALENDAR, "javascript:$('#value_" .$i. "').datepicker('show')", null, null, null, null, null, null, false, "_$i");
			$advsearch->addRow();
			$advsearch->setColContent($c, 0, $branch->getHtml());
			$advsearch->setColContent($c, 1, $field->getHtml());
			$advsearch->setColContent($c, 2, we_html_tools::htmlSelect("operator_" . $i, self::$operators, 1, (isset($search_arr["operator_" . $i]) ? $search_arr["operator_" . $i] : ""), false, [], "value", 60));
			$advsearch->setCol($c, 3, ["width" => 190], "<table class='default'><tr><td>" . $value_i . $value_date_i . "</td><td id='dpzell_$i' style='display:none; padding-left:5px;text-align:right'>$btnDatePicker</td></tr></table>");
			++$c;
		}

		$advsearch->addRow();
		$advsearch->setCol($c, 0, ["colspan" => $colspan, 'style' => 'padding-top:5px;'], we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_search')") .
			we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_search')")
		);

		$search->setCol(1, 0, [], we_html_element::htmlHidden("count", $count) . $advsearch->getHtml());
		$search->setCol(3, 0, ["colspan" => $colspan, 'style' => 'text-align:right;padding-top:5px;'], "<table class='default'><tr><td>" .
			we_html_element::htmlDiv(['class' => "defaultfont lowContrast"], g_l('modules_customer', '[simple_search]')) .
			we_html_button::create_button('fa:btn_direction_left,fa-lg fa-caret-left', "javascript:we_cmd('switchToSimple')") .
			$search_but
			. '</td><td>&nbsp;</td></tr></table>'
		);
		$max_res = $pob->View->settings->getMaxSearchResults();
		$result = ($search_arr && we_base_request::_(we_base_request::BOOL, 'search') ? self::getAdvSearchResults($pob->db, $search_arr, $count, $max_res) : []);

		$GLOBALS['advSearchFoundItems'] = count($result);

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

		$db->query('SELECT ID,CONCAT(Username, " (",Forename," ",Surname,")") AS user FROM ' . CUSTOMER_TABLE . ' WHERE ' . (empty($where) ? 0 : $where) . ' ORDER BY Username LIMIT 0,' . $res_num);
		return $db->getAllFirst(false);
	}

}
