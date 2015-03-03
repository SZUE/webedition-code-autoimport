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
class we_shop_frames extends we_modules_frame{

	var $db;
	var $View;
	var $frameset;
	public $module = 'shop';
	protected $hasIconbar = true;
	protected $useMainTree = false;
	protected $treeDefaultWidth = 204;

	function __construct($frameset){
		parent::__construct(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php');
		$this->View = new we_shop_view(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php', 'top.content');
	}

	function getJSCmdCode(){
		return $this->View->getJSTop_tmp();
	}

	protected function getDoClick(){
		return "function doClick(id,ct,table){
top.content.editor.location='" . WE_SHOP_MODULE_DIR . "edit_shop_frameset.php?pnt=editor&bid='+id;
}
function doFolderClick(id,ct,table){
	top.content.editor.location='" . WE_SHOP_MODULE_DIR . "edit_shop_frameset.php?pnt=editor&mid='+id;
}
function doYearClick(yearView){
	top.content.editor.location='" . WE_SHOP_MODULE_DIR . "edit_shop_frameset.php?pnt=editor&ViewYear='+yearView;
}";
	}

	function getJSTreeCode(){ //TODO: use we_html_element::jsElement and move to new class weShopTree
		$ret = we_html_element::cssLink(CSS_DIR . 'tree.css') .
				we_html_element::jsElement('
var table="' . SHOP_TABLE . '";
var tree_icon_dir="' . TREE_ICON_DIR . '";
var tree_img_dir="' . TREE_IMAGE_DIR . '";
var we_dir="' . WEBEDITION_DIR . '";'
						. parent::getTree_g_l() . '
var treeYearClick="' . g_l('modules_shop', '[treeYearClick]') . '";
var treeYear="' . g_l('modules_shop', '[treeYear]') . '";
var perm_EDIT_SHOP_ORDER=' . permissionhandler::hasPerm("EDIT_SHOP_ORDER") . ';
') . we_html_element::jsScript(JS_DIR . 'tree.js') .
				we_html_element::jsScript(JS_DIR . 'shop_tree.js');
		$menu = 'function loadData() {
				menuDaten.clear();
				menuDaten.add(new self.rootEntry(0, "root", "root"));';


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


			$menu.= "  menuDaten.add(new urlEntry('" . we_base_ContentTypes::FILE_ICON . "'," . $this->db->f("IntOrderID") . "," . $this->db->f("mdate") . ",'" . $this->db->f("IntOrderID") . ". " . g_l('modules_shop', '[bestellung]') . " " . $this->db->f("orddate") . "','shop','" . SHOP_TABLE . "','" . (($this->db->f("DateShipping") > 0) ? 0 : 1) . "','" . $style . "'));\n";
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
			$menu.= "menuDaten.add(new dirEntry('" . we_base_ContentTypes::FOLDER_ICON . "',$f+''+$year,0, '" . (($f < 10) ? "0" . $f : $f) . ' ' . g_l('modules_shop', '[sl]') . " " . g_l('date', '[month][long][' . ($f - 1) . ']') . " (" . (($k > 0) ? "<b>" . $k . "</b>" : 0) . "/" . (($r > 0) ? $r : 0) . ")',0,'',''," . (($k > 0) ? 1 : 0) . "));";
		} //'".$this->db->f("mdate")."'
		$menu.='top.yearshop = ' . $year . ';
			}';
		return $ret . we_html_element::jsElement($menu);
	}

	function getHTMLFrameset(){
		$extraHead = $this->getJSTreeCode();

		if(($bid = we_base_request::_(we_base_request::INT, 'bid')) === -1){
			$this->db->query("SELECT IntOrderID FROM " . SHOP_TABLE . " ORDER BY IntID DESC");
			$bid = $this->db->next_record() ? $this->db->f("IntOrderID") : 0;
		}

		$extraUrlParams = $bid > 0 ? '&bid=' . $bid : '&top=1&home=1';

		return parent::getHTMLFrameset($extraHead, $extraUrlParams);
	}

	function getHTMLIconbar(){ //TODO: move this to weShopView::getHTMLIconbar();
		$extraHead = we_html_element::jsElement('
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	switch (arguments[0]) {
		case "openOrder":
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if(top.content.tree.window.doClick) {
				top.content.tree.window.doClick(arguments[1], arguments[2], arguments[3]);//TODO: check this adress
			}
		break;

		default:
			// not needed yet
		break;
	}
}

		');

		//	$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		//	$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, '', $this->db);
		$this->db->query("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC');

		$headline = $this->db->next_record() ? '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $this->db->f("IntOrderID") . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $this->db->f("IntOrderID"), $this->db->f("orddate")) . '</a>' : '';

		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $this->db));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);

		$classid = $fe[0];


		/* TODO: we have this or similar code at least four times!! */

		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

		$c = 0;
		$iconBarTable = new we_html_table(array("border" => 0, "cellpadding" => 6, "cellspacing" => 0, "style" => "margin-left:8px"), 1, 4);

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_extArt", "javascript:top.opener.top.we_cmd('new_article')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_delOrd", "javascript:top.opener.top.we_cmd('delete_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($resultD){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_sum", "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=document '", true));
		} elseif(!empty($resultO)){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_sum", "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=$classid '", true));
		}

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_shop_pref", "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("image:btn_payment_val", "javascript:top.opener.top.we_cmd('payment_val')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($headline){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, array('align' => 'right', 'class' => 'header_shop'), '<span style="margin-left:15px">' . $headline . '</span>');
		}

		$body = we_html_element::htmlBody(array('id' => 'iconBar', 'marginwidth' => 0, 'topmargin' => 5, 'marginheight' => 5, 'leftmargin' => 0), $iconBarTable->getHTML());

		return $this->getHTMLDocument($body, $extraHead);
	}

	function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody());
	}

	protected function getHTMLEditor(){//TODO: maybe abandon the split between former Top- and other editor files
		if(we_base_request::_(we_base_request::BOOL, 'top')){//doing what have been done in edit_shop_editorFramesetTop before
			return $this->getHTMLEditorTop();
		}

		//do what have been done in edit_shop_editorFrameset before

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		$mid = we_base_request::_(we_base_request::STRING, 'mid', 0);
		$yearView = we_base_request::_(we_base_request::INT, 'ViewYear', 0);
		$home = we_base_request::_(we_base_request::BOOL, 'home');

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=shop';
		} elseif($mid){
			$year = substr($mid, (strlen($mid) - 4));
			$month = str_replace($year, '', $_REQUEST["mid"]);
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $year . '&ViewMonth=' . $month;
		} elseif($yearView){
			$year = $yearView;
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $year;
		} else {
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?bid=' . $bid;
		}

		$body = we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&home=' . $home . '&mid=' . $mid . $yearView . '&bid=' . $bid, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', '', '', false) .
						we_html_element::htmlIFrame('edbody', $bodyURL . '&pnt=edbody', 'position: absolute; top: 40px; bottom: 0px; left: 0px; right: 0px; overflow: auto;', 'border:0px;width:100%;height:100%;overflow: auto;')
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLEditorTop(){// TODO: merge getHTMLRight and getHTMLRightTop
		$DB_WE = $this->db;

		$home = we_base_request::_(we_base_request::BOOL, "home");
		$mid = we_base_request::_(we_base_request::INT, "mid", 0);
		$bid = we_base_request::_(we_base_request::INT, "bid", 0);

		// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $DB_WE));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);

		$classid = $fe[0];


		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . $DB_WE->escape(WE_SHOP_TITLE_FIELD_NAME) . '" LIMIT 1', '', $DB_WE);

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=mod_home&mod=shop'; //same as in getHTMLRight()
		} elseif($mid){
			// TODO::WANN UND VON WEM WIRD DAS AUFGERUFEN ????
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_overviewTop.php?mid=' . $mid;
		} elseif($resultD && !$resultO){ // docs but no objects
			$bodyURL = 'edit_shop_article_extend.php?typ=document';
		} elseif(!$resultD && $resultO){ // no docs but objects
			$bodyURL = 'edit_shop_article_extend.php?typ=object&ViewClass=' . $classid;
		} elseif($resultD && $resultO){
			$bodyURL = 'edit_shop_article_extend.php?typ=document';
		}

		$body = we_html_element::htmlIFrame('edheader', 'edit_shop_frameset.php?pnt=edheader&top=1&home=' . $home . '&mid=' . $mid . '&bid=' . $bid . '&typ=object&ViewClass=' . $classid, 'position:absolute;top:0px;height:40px;left:0px;right:0px;', '', '', false) .
				we_html_element::htmlIFrame('edbody', $bodyURL, 'position:absolute;top:40px;bottom:0px;left:0px;right:0px;', '', '', true);
		return $this->getHTMLDocument(we_html_element::htmlBody(array(), $body));
	}

	protected function getHTMLEditorHeader(){
		$DB_WE = $this->db;
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument('<body bgcolor="#F0EFF0"></body></html>');
		}

		if(we_base_request::_(we_base_request::BOOL, 'top')){
			return $this->getHTMLEditorHeaderTop();
		}

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);

		$hash = getHash('SELECT IntCustomerID,DATE_FORMAT(DateOrder,"' . g_l('date', '[format][mysqlDate]') . '") AS d FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, $DB_WE);
		if($hash){
			$cid = $hash['IntCustomerID'];
			$cdat = $hash['d'];
		} else {
			$cid = 0;
			$cdat = '';
		}
		//$order = getHash('SELECT IntOrderID,DATE_FORMAT(DateOrder,"' . g_l('date', '[format][mysqlDate]') . '") as orddate FROM ' . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $DB_WE);
		//$headline = ($order ? sprintf(g_l('modules_shop', '[lastOrder]'), $order["IntOrderID"], $order["orddate"]) : '');

		$we_tabs = new we_tabs();

		if(isset($_REQUEST["mid"]) && $_REQUEST["mid"] && $_REQUEST["mid"] != '00'){
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][overview]'), we_tab::ACTIVE, 0));
		} else {
			$we_tabs->addTab(new we_tab('#', g_l('tabs', '[module][orderdata]'), we_tab::ACTIVE, "setTab(0);"));
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][orderlist]'), we_tab::NORMAL, "setTab(1);"));
		}

		$textPre = g_l('modules_shop', $bid > 0 ? '[orderList][order]' : '[order_view]');
		$textPost = isset($_REQUEST['mid']) && $_REQUEST['mid'] > 0 ? (strlen($_REQUEST['mid']) > 5 ? g_l('modules_shop', '[month][' . substr($_REQUEST['mid'], 0, -5) . ']') . " " . substr($_REQUEST['mid'], -5, 4) : substr($_REQUEST['mid'], 1)) : ($bid ? sprintf(g_l('modules_shop', '[orderNo]'), $bid, $cdat) : '');

		$tab_head = $we_tabs->getHeader() . we_html_element::jsElement('
function setTab(tab) {
	switch (tab) {
		case 0:
			parent.edbody.document.location = "edit_shop_frameset.php?pnt=edbody&bid=' . $bid . '";
			break;
		case 1:
			parent.edbody.document.location = "edit_shop_orderlist.php?cid=' . $cid . '";
			break;
	}
}

top.content.hloaded = 1;
		');

		$tab_body_content = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;padding-left:10px;" id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $textPost) . '</b></span></nobr></div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>';
		$tab_body = we_html_element::htmlBody(array("onresize" => "setFrameSize()", "onload" => "setFrameSize()", "id" => "eHeaderBody"), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	function getHTMLEditorHeaderTop(){
		$DB_WE = $this->db;

		//$yid = we_base_request::_(we_base_request::INT, "ViewYear", date("Y"));
		//$bid = we_base_request::_(we_base_request::INT, "bid", 0);
		//$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), "IntCustomerID", $this->db);
		$this->db->query("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC');
		$headline = ($this->db->next_record() ? sprintf(g_l('modules_shop', '[lastOrder]'), $this->db->f("IntOrderID"), $this->db->f("orddate")) : '');

		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $DB_WE));
		$fe = isset($feldnamen[3]) ? explode(",", $feldnamen[3]) : array(0);

		$classid = $fe[0];
		$resultO = array_shift($fe);

		// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

		// grep the last element from the year-set, wich is the current year
		$yearTrans = f('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd DESC LIMIT 1', 'DateOrd', $this->db);


		$we_tabs = new we_tabs();
		if(isset($_REQUEST["mid"]) && $_REQUEST["mid"]){
			$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][overview]'), we_tab::ACTIVE, "//"));
		} else {
			if($resultD && $resultO){ //docs and objects
				$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_1]'), we_tab::ACTIVE, "setTab(0);"));
				$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_2]'), we_tab::NORMAL, "setTab(1);"));
			} elseif($resultD && !$resultO){ // docs but no objects
				$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_1]'), we_tab::NORMAL, "setTab(0);"));
			} elseif(!$resultD && $resultO){ // no docs but objects
				$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_2]'), we_tab::NORMAL, "setTab(1);"));
			}
			if(isset($yearTrans) && $yearTrans != 0){
				$we_tabs->addTab(new we_tab("#", g_l('tabs', '[module][admin_3]'), we_tab::NORMAL, "setTab(2);"));
			}
		}

		$tab_head = $we_tabs->getHeader() . we_html_element::jsElement('
function setTab(tab) {
	switch (tab) {
		case 0:
			parent.edbody.document.location = "edit_shop_article_extend.php?typ=document";
			break;
		case 1:
			parent.edbody.document.location = "edit_shop_article_extend.php?typ=object&ViewClass=' . $classid . '";
			break;
		' . (isset($yearTrans) ? '
		case 2:
			parent.edbody.document.location = "edit_shop_revenueTop.php?ViewYear=' . $yearTrans . '" // " + top.yearshop
			break;
		' : '') . '
	}
}
top.content.hloaded = 1;
		');

		$tab_body_content = '<div id="main" >' . we_html_tools::getPixel(100, 3) . '<div style="margin:0px;" id="headrow">&nbsp;' . we_html_element::htmlB($headline) . '</div>' . we_html_tools::getPixel(100, 3) .
				$we_tabs->getHTML() .
				'</div>';
		$tab_body = we_html_element::htmlBody(array('id' => 'eHeaderBody'), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	public function getHTML($what = ''){
		switch($what){
			case 'iconbar':
				return $this->getHTMLIconbar();
			default:
				return parent::getHTML($what);
		}
	}

}
