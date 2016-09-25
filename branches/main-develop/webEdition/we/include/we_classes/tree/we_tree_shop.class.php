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

	protected function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'shop_tree.js');
	}

	function getJSStartTree(){
		return '
function startTree(){
	treeData.frames={
		top:top.content,
		cmd:' . $this->cmdFrame . ',
		tree:' . $this->treeFrame . '
	};
	loadData();
	drawTree();
}';
	}

	function getJSTreeCode(){
		$year = we_base_request::_(we_base_request::INT, 'year', date('Y'));
		$menu = '
WE().util.loadConsts(document, "g_l.shop");
function loadData() {
				treeData.clear();
				treeData.add(node.prototype.rootEntry(0, "root", "root"));';

		$this->db->query("SELECT
o.ID,
CONCAT(o.ID,IF(o.shopname,CONCAT(' (',o.shopname,'), '),', '),DATE_FORMAT(o.DateOrder,'" . g_l('date', '[format][mysql]') . "')) AS text,
DateShipping IS NOT NULL AS published,
DATE_FORMAT(DateOrder,'%c%Y') AS mdate,
(DateConfirmation IS NOT NULL || DateShipping IS NOT NULL || EXISTS (SELECT * FROM " . SHOP_ORDER_DATES_TABLE . " od WHERE od.ID=o.ID AND od.type IN ('DateCustomA','DateCustomB','DateCustomC','DateCustomD','DateCustomE','DateCustomF','DateCustomG','DateCustomH','DateCustomI','DateCustomJ')) ) AS isActive,
o.DatePayment IS NOT NULL AS isPayed,
(DateCancellation IS NOT NULL || DateFinished IS NOT NULL) AS isFinished,
DateShipping IS NOT NULL AS isShipped
FROM " . SHOP_ORDER_TABLE . ' o WHERE
DateOrder BETWEEN "' . ($year - 1) . '-12-31" AND "' . ($year + 1) . '-01-01"
ORDER BY o.ID DESC');
		while($this->db->next_record()){
			//added for #6786
			$style = 'color:black;font-family:liberation_sansbold;';

			if($this->db->f('isActive')){
				$style = 'color:red;';
			}

			if($this->db->f('isPayed')){
				$style = 'color:#006699;';
			}

			if($this->db->f('isFinished')){
				$style = 'color:black;';
			}

			$menu .= "treeData.add({
				id:'" . $this->db->f('ID') . "',
				parentid:" . $this->db->f('mdate') . ",
				text:'" . $this->db->f('text') . "',
				typ:'shop',
				checked:false,
				contentType:'shop',
				table:'" . SHOP_ORDER_TABLE . "',
				published:" . $this->db->f("published") . ",
				st:'" . $style . "'
			});";

			if(!$this->db->f('isShipped')){
				if(isset($l[$this->db->f('mdate')])){
					$l[$this->db->f('mdate')] ++;
				} else {
					$l[$this->db->f('mdate')] = 1;
				}
			}

			if(isset($v[$this->db->f('mdate')])){
				$v[$this->db->f('mdate')] ++;
			} else {
				$v[$this->db->f('mdate')] = 1;
			}
		}


//unset($_SESSION['year']);
		for($f = 12; $f > 0; $f--){
			$r = (isset($v[$f . $year]) ? $v[$f . $year] : '');
			$k = (isset($l[$f . $year]) ? $l[$f . $year] : '');
			$menu .= "treeData.add({
	id:" . $f . $year . ",
	parentid:0,
	text:'" . (($f < 10) ? '0' : '') . $f . ' ' . g_l('modules_shop', '[sl]') . " " . g_l('date', '[month][long][' . ($f - 1) . ']') . " (" . (($k > 0) ? "<b>" . $k . "</b>" : '0') . '/' . (($r > 0) ? $r : 0) . ")',
	typ:'folder',
	open:0,
	contentType:'we/shop',
	table:'',
	loaded: 0,
	checked: false,
	published:" . (($k > 0) ? 1 : 0) . "
});";
		}
		$menu .= 'treeData.yearshop = ' . $year . ';
}';
		return we_html_element::cssLink(CSS_DIR . 'tree.css') . we_html_element::jsElement($menu) . parent::getJSTreeCode();
	}

}
