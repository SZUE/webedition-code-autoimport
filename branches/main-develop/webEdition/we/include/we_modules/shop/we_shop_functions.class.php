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
class we_shop_functions{

	static function getCustomersOrderList($customerId, $sameModul = true){
		$weShopStatusMails = we_shop_statusMails::getShopStatusMails();

		// get orderdata of user here
		$da = ( $GLOBALS['WE_LANGUAGE'] === 'Deutsch') ? '%d.%m.%Y' : '%m/%d/%Y';

		$hidden = array_keys(array_filter($weShopStatusMails->FieldsHiddenCOV, function ($v){
				return $v == 1;
			}));
		$showBaseFields = array_diff(we_shop_statusMails::$BaseDateFields, $hidden);
		$showDateFields = array_diff(we_shop_statusMails::$StatusFields, $hidden);
		$showAdvanced = array_diff($showDateFields, $showBaseFields);

		$format = [];
		foreach($showBaseFields as $field){
			$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS format' . $field;
		}
		if($showAdvanced){
			$advanced = $GLOBALS['DB_WE']->getAllFirstq('SELECT CONCAT(odt.ID,odt.type),DATE_FORMAT(odt.date,"' . $da . '") FROM ' . SHOP_ORDER_TABLE . ' o JOIN ' . SHOP_ORDER_DATES_TABLE . ' odt ON odt.ID=o.ID WHERE odt.type IN ("' . implode('","', $showAdvanced) . '") AND customerID=' . intval($customerId) . ' ORDER BY odt.ID', false);
		}

		$GLOBALS['DB_WE']->query('SELECT ID,shopname' . ($format ? ',' . implode(',', $format) : '') . ' FROM ' . SHOP_ORDER_TABLE . ' WHERE customerID=' . intval($customerId) . ' ORDER BY ID DESC');

		$orderStr = '<table class="defaultfont" style="width:100%">';
		if($GLOBALS['DB_WE']->num_rows()){
			$orderStr .= '<tr><td></td><td><b>' . g_l('modules_shop', '[orderList][order]') . '</b></td>';

			foreach($showDateFields as $field){
				$orderStr .= '<td><b>' . $weShopStatusMails->FieldsText[$field] . '</b></td>';
			}

			$orderStr .= '</tr>';

			while($GLOBALS['DB_WE']->next_record()){

				$orderStr .= '<tr>';
				if(we_base_permission::hasPerm('EDIT_SHOP_ORDER')){
					$orderStr .= ($sameModul ?
						('<td>' . we_html_button::create_button(we_html_button::EDIT, "javascript:top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&bid=" . $GLOBALS['DB_WE']->f('ID') . "';") . '</td>') :
						('<td>' . we_html_button::create_button(we_html_button::EDIT, "javascript:top.document.location=WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=show_frameset&bid=" . $GLOBALS['DB_WE']->f('IntOrderID') . "';") . '</td>')
						);
				} else {
					$orderStr .= '<td></td>';
				}
				$orderStr .= '<td>' . $GLOBALS['DB_WE']->f('ID') . ($GLOBALS['DB_WE']->f('shopname') ? ' (' . $GLOBALS['DB_WE']->f('shopname') . /* '. ' . g_l('modules_shop', '[orderList][order]') . */ ')' : '') . '</td>';
				foreach($showDateFields as $field){
					$val = (in_array($field, $showBaseFields) ?
						$GLOBALS['DB_WE']->f('format' . $field) :
						$advanced[$GLOBALS['DB_WE']->f('ID') . $field]);

					$orderStr .= '<td>' . ( $val ?: '-' ) . '</td>';
				}

				$orderStr .= '</tr>';
			}
		} else {
			$orderStr .= '<tr><td>' . g_l('modules_shop', '[orderList][noOrders]') . '</td></tr>';
		}
		$orderStr .= '</table>';

		return $orderStr;
	}

}
