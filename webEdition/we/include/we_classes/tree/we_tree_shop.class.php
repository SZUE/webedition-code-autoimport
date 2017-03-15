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
class we_tree_shop extends we_tree_base{

	public function __construct(we_base_jsCmd $jsCmd, $topFrame = '', $treeFrame = '', $cmdFrame = ''){
		parent::__construct($jsCmd, $topFrame, $treeFrame, $cmdFrame);
		$this->addSorted = false;
	}

	protected function customJSFile(){
		$year = we_base_request::_(we_base_request::INT, 'year', date('Y'));
		return we_html_element::jsScript(JS_DIR . 'shop_tree.js', 'initTree(' . $year . ');');
	}

	public static function getItems($ParentId, $Offset = 0, $Segment = 500, $sort = false){
		$year = we_base_request::_(we_base_request::INT, 'year', date('Y'));
		$db = new DB_WE();
		$items = [];
		$db->query("SELECT
o.ID,
CONCAT(o.ID,IF(o.shopname,CONCAT(' (',o.shopname,'), '),', '),DATE_FORMAT(o.DateOrder,'" . g_l('date', '[format][mysql]') . "')) AS text,
o.DateShipping IS NOT NULL AS published,
DATE_FORMAT(o.DateOrder,'%c%Y') AS mdate,
(o.DateConfirmation IS NOT NULL || DateShipping IS NOT NULL || EXISTS (SELECT * FROM " . SHOP_ORDER_DATES_TABLE . " od WHERE od.ID=o.ID AND od.type IN ('DateCustomA','DateCustomB','DateCustomC','DateCustomD','DateCustomE','DateCustomF','DateCustomG','DateCustomH','DateCustomI','DateCustomJ')) ) AS isActive,
o.DatePayment IS NOT NULL AS isPayed,
(o.DateCancellation IS NOT NULL || DateFinished IS NOT NULL) AS isFinished,
o.DateShipping IS NOT NULL AS isShipped
FROM " . SHOP_ORDER_TABLE . ' o WHERE
o.DateOrder BETWEEN "' . ($year - 1) . '-12-31" AND "' . ($year + 1) . '-01-01"
ORDER BY o.ID DESC');
		$l = [];
		while($db->next_record()){
			//added for #6786
			$style = 'default';

			if($db->f('isActive')){
				$style = 'active';
			}

			if($db->f('isPayed')){
				$style = 'payed';
			}

			if($db->f('isFinished')){
				$style = 'finished';
			}
			$items[] = [
				'id' => $db->f('ID'),
				'parentid' => $db->f('mdate'),
				'text' => $db->f('text'),
				'typ' => 'shop',
				'checked' => false,
				'contenttype' => 'shop',
				'table' => SHOP_ORDER_TABLE,
				'published' => $db->f("published"),
				'class' => $style,
			];

			if(!$db->f('isShipped')){
				if(isset($l[$db->f('mdate')])){
					$l[$db->f('mdate')] ++;
				} else {
					$l[$db->f('mdate')] = 1;
				}
			}

			if(isset($v[$db->f('mdate')])){
				$v[$db->f('mdate')] ++;
			} else {
				$v[$db->f('mdate')] = 1;
			}
		}

		$months = we_base_country::getTranslationList(we_base_country::MONTH, array_search($GLOBALS['WE_LANGUAGE'], getWELangs()));

		for($f = 12; $f > 0; $f--){
			$r = (isset($v[$f . $year]) ? $v[$f . $year] : '');
			$k = (isset($l[$f . $year]) ? $l[$f . $year] : '');
			$items[] = [
				'id' => $f . $year,
				'parentid' => 0,
				'text' => (($f < 10) ? '0' : '') . $f . ' ' . g_l('modules_shop', '[sl]') . " " . $months['wide'][$f] . " (" . (($k > 0) ? "<b>" . $k . "</b>" : '0') . '/' . (($r > 0) ? $r : 0) . ')',
				'typ' => 'folder',
				'open' => false,
				'contenttype' => 'we/shop',
				'table' => '',
				'loaded' => 0,
				'checked' => false,
				'published' => (($k > 0) ? 1 : 0),
			];
		}

		return $items;
	}

}
