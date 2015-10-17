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
class we_shop_tree extends weTree{

	function customJSFile(){
		return we_html_element::jsScript(JS_DIR . 'shop_tree.js');
	}

	function getJSStartTree(){
		return '
function startTree(){
			frames={
	"top":' . $this->topFrame . ',
	"cmd":' . $this->cmdFrame . '
	};
	treeData.frames=frames;
	frames.cmd.location=treeData.frameset+"&pnt=cmd&pid=0";
}';
	}

	function getJSTreeCode(){
		$ret = we_html_element::cssLink(CSS_DIR . 'tree.css') .
			we_html_element::jsElement('
var table="' . SHOP_TABLE . '";
WE().consts.g_l.shop.tree={
	treeYearClick:"' . g_l('modules_shop', '[treeYearClick]') . '",
	treeYear:"' . g_l('modules_shop', '[treeYear]') . '"
};
') .
			we_html_element::jsScript(JS_DIR . 'tree.js', 'self.focus();') .
			we_html_element::jsScript(JS_DIR . 'shop_tree.js');
		$menu = '
function loadData() {
	treeData.clear();
	treeData.add(node.prototype.rootEntry(0, "root", "root"));';


		$this->db->query("SELECT IntOrderID,DateShipping,DateConfirmation,DateCustomA,DateCustomB,DateCustomC,DateCustomD,DateCustomE,DatePayment,DateCustomF,DateCustomG,DateCancellation,DateCustomH,DateCustomI,DatecustomJ,DateFinished, DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') as orddate, DATE_FORMAT(DateOrder,'%c%Y') as mdate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC');
		while($this->db->next_record()){
//added for #6786
			$style = 'color:black;font-weight:bold;';

			if($this->db->f('DateCustomA') != '' || $this->db->f('DateCustomB') != '' || $this->db->f('DateCustomC') != '' || $this->db->f('DateCustomD') != '' || $this->db->f('DateCustomE') != '' || $this->db->f('DateCustomF') != '' || $this->db->f('DateCustomG') != '' || $this->db->f('DateCustomH') != '' || $this->db->f('DateCustomI') != '' || $this->db->f('DateCustomJ') != '' || $this->db->f('DateConfirmation') != '' || ($this->db->f('DateShipping') != '0000-00-00 00:00:00' && $this->db->f('DateShipping') != '')){
				$style = 'color:red;';
			}

			if($this->db->f('DatePayment') != '0000-00-00 00:00:00' && $this->db->f('DatePayment') != ''){
				$style = 'color:#006699;';
			}

			if($this->db->f('DateCancellation') != '' || $this->db->f('DateFinished') != ''){
				$style = 'color:black;';
			}
			$menu.= "  treeData.add({
	id:'" . $this->db->f("IntOrderID") . "',
	parentid:" . $this->db->f("mdate") . ",
	text:'" . $this->db->f("IntOrderID") . ". " . g_l('modules_shop', '[bestellung]') . " " . $this->db->f("orddate") . "',
	typ:'shop',
	checked:false,
	contentType:'shop',
	table:'" . SHOP_TABLE . "',
	published:" . (($this->db->f("DateShipping") > 0) ? 0 : 1) . ",
	st:'" . $style . "'
});";

			if($this->db->f('DateShipping') <= 0){
				if(isset(${'l' . $this->db->f('mdate')})){
					${'l' . $this->db->f('mdate')} ++;
				} else {
					${'l' . $this->db->f('mdate')} = 1;
				}
			}


//FIXME: remove eval
			if(isset(${'v' . $this->db->f('mdate')})){
				${'v' . $this->db->f('mdate')} ++;
			} else {
				${'v' . $this->db->f('mdate')} = 1;
			}
		}

		$year = we_base_request::_(we_base_request::INT, 'year', date('Y'));
//unset($_SESSION['year']);
		for($f = 12; $f > 0; $f--){
			$r = (isset(${'v' . $f . $year}) ? ${'v' . $f . $year} : '');
			$k = (isset(${'l' . $f . $year}) ? ${'l' . $f . $year} : '');
			$menu.= "treeData.add({
	id:'" . $f . $year . "',
	parentid:0,
	text:'" . (($f < 10) ? "0" . $f : $f) . ' ' . g_l('modules_shop', '[sl]') . " " . g_l('date', '[month][long][' . ($f - 1) . ']') . " (" . (($k > 0) ? "<b>" . $k . "</b>" : 0) . "/" . (($r > 0) ? $r : 0) . ")',
	typ:'folder',
	open:0,
	contentType:'we/shop',
	table:'',
	loaded: 0,
	checked: false,
	published:" . (($k > 0) ? 1 : 0) . "
});";
		}
		$menu.='top.yearshop = ' . $year . ';
			}';
		return parent::getJSTreeCode().$ret . we_html_element::jsElement($menu);
	}

}
