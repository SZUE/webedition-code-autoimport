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

		$format = array();
		foreach(we_shop_statusMails::$StatusFields as $field){
			$format[] = 'DATE_FORMAT(' . $field . ',"' . $da . '") AS format' . $field;
		}

		$GLOBALS['DB_WE']->query('SELECT IntOrderID, ' . implode(',', we_shop_statusMails::$StatusFields) . ', ' . implode(',', $format) . ' FROM ' . SHOP_TABLE . ' WHERE IntCustomerID=' . intval($customerId) . ' GROUP BY IntOrderId ORDER BY IntID DESC');

		$orderStr = '<table class="defaultfont" style="width:1200px">';
		if($GLOBALS['DB_WE']->num_rows()){
			$orderStr .='<tr>
			<td></td><td><b>' . g_l('modules_shop', '[orderList][order]') . '</b></td>';

			foreach(we_shop_statusMails::$StatusFields as $field){
				if(!$weShopStatusMails->FieldsHidden[$field]){
					$orderStr .='<td><b>' . $weShopStatusMails->FieldsText[$field] . '</b></td>';
				}
			}

			$orderStr .='</tr>';

			while($GLOBALS['DB_WE']->next_record()){

				$orderStr .= '<tr>';
				if(permissionhandler::hasPerm('EDIT_SHOP_ORDER')){
					$orderStr .= ($sameModul ?
							('<td>' . we_html_button::create_button(we_html_button::EDIT, 'javascript:top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + \'we_showMod.php?mod=shop&pnt=editor&bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';') . '</td>') :
							('<td>' . we_html_button::create_button(we_html_button::EDIT, 'javascript:top.document.location=\'' . WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=show_frameset&bid=' . $GLOBALS['DB_WE']->f('IntOrderID') . '\';') . '</td>')
						);
				} else {
					$orderStr .='<td></td>';
				}
				$orderStr .= '<td>' . $GLOBALS['DB_WE']->f('IntOrderID') . '. ' . g_l('modules_shop', '[orderList][order]') . '</td>';
				foreach(we_shop_statusMails::$StatusFields as $field){
					if(!$weShopStatusMails->FieldsHidden[$field]){
						$orderStr .='<td>' . ( $GLOBALS['DB_WE']->f($field) > 0 ? $GLOBALS['DB_WE']->f('format' . $field) : '-' ) . '</td>';
					}
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
