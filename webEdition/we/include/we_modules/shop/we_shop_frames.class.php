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
	const TAB_OVERVIEW = 0;
	const TAB_ORDERLIST = 1;
	const TAB_ADMIN1 = 0;
	const TAB_ADMIN2 = 1;
	const TAB_ADMIN3 = 2;

	public function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'shop';
		$this->treeDefaultWidth = 204;
		$this->hasIconbar = true;
		$this->Tree = new we_tree_shop($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_shop_view($frameset, 'top.content');
	}

	function getHTMLIconbar(){ //TODO: move this to weShopView::getHTMLIconbar();
		$extraHead = we_html_element::jsElement('
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {
		case "openOrder":
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if(top.content.doClick) {
				top.content.doClick(args[1], args[2], args[3]);//TODO: check this adress
			}
			break;
		default:
			// not needed yet
			top.opener.top.we_cmd.apply(this, Array.prototype.slice.call(arguments));
	}
}

		');

//	$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
//	$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid, '', $this->db);
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);

		$headline = $data ? '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $data['IntOrderID'] . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $data['IntOrderID'], $data['orddate']) . '</a>' : '';

/// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db));
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
		$iconBarTable = new we_html_table(array('class' => 'iconBar'), 1, 4);

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_extArt,fa-lg fa-cart-plus', "javascript:top.opener.top.we_cmd('new_article')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_delOrd,fa-lg fa-shopping-cart,fa-lg fa-trash-o', "javascript:top.opener.top.we_cmd('delete_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($resultD){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_sum,fa-lg fa-line-chart', "javascript:top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&top=1&typ=document '", true));
		} elseif($resultO){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_sum,fa-lg fa-line-chart', "javascript:top.content.editor.location=WE().consts.dirs.WEBEDITION_DIR + 'we_showMod.php?mod=shop&pnt=editor&top=1&typ=object&ViewClass=$classid '", true));
		}

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_pref,fa-lg fa-pencil,fa-lg fa-list-alt', "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_payment_val,fa-lg fa-long-arrow-right,fa-lg fa-money', "javascript:top.opener.top.we_cmd('payment_val')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($headline){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, array('style' => 'text-align:right', 'class' => 'header_shop'), '<span style="margin-left:15px">' . $headline . '</span>');
		}

		return $this->getHTMLDocument(we_html_element::htmlBody(array('id' => 'iconBar'), $iconBarTable->getHTML()), $extraHead);
	}

	protected function getHTMLCmd(){
		return $this->getHTMLDocument(we_html_element::htmlBody());
	}

	protected function getHTMLEditor($extraUrlParams = '', $extraHead = ''){//TODO: maybe abandon the split between former Top- and other editor files
		if(we_base_request::_(we_base_request::BOOL, 'top')){//doing what have been done in edit_shop_editorFramesetTop before
			return $this->getHTMLEditorTop();
		}

//do what have been done in edit_shop_editorFrameset before

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);
		$mid = we_base_request::_(we_base_request::STRING, 'mid', 0);
		$yearView = we_base_request::_(we_base_request::INT, 'ViewYear', 0);
		$home = we_base_request::_(we_base_request::BOOL, 'home');

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_showMod.php?mod=shop&home=1';
		} elseif($mid){
			$year = substr($mid, (strlen($mid) - 4));
			$month = str_replace($year, '', $mid);
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $year . '&ViewMonth=' . $month;
		} elseif($yearView){
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $yearView;
		} else {
			$bodyURL = WEBEDITION_DIR . 'we_showMod.php?mod=shop&bid=' . $bid;
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('class' => 'moduleEditor'), we_html_element::htmlIFrame('edheader', WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edheader&home=' . $home . '&mid=' . $mid . $yearView . '&bid=' . $bid, '', '', '', false, 'editorHeader') .
					we_html_element::htmlIFrame('edbody', $bodyURL . '&pnt=edbody', 'bottom: 0px;', 'border:0px;width:100%;height:100%;', '', true, 'editorBody')
				)
		);
	}

	function getHTMLEditorTop(){// TODO: merge getHTMLRight and getHTMLRightTop
		$DB_WE = $this->db;

		$home = we_base_request::_(we_base_request::BOOL, 'home');
		$mid = we_base_request::_(we_base_request::INT, 'mid', 0);
		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);

// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $DB_WE));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}
		$fe = explode(',', $feldnamen[3]);

		$classid = $fe[0];


		$resultO = array_shift($fe);

// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . $DB_WE->escape(WE_SHOP_TITLE_FIELD_NAME) . '" LIMIT 1', '', $DB_WE);

		if($home){
			$bodyURL = WEBEDITION_DIR . 'we_showMod.php?mod=shop&home=1&pnt=edbody'; //same as in getHTMLRight()
		} elseif($mid){
// TODO::WANN UND VON WEM WIRD DAS AUFGERUFEN ????
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_overviewTop.php?mid=' . $mid;
		} elseif($resultD && !$resultO){ // docs but no objects
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_article_extend.php?typ=document';
		} elseif(!$resultD && $resultO){ // no docs but objects
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_article_extend.php?typ=object&ViewClass=' . $classid;
		} elseif($resultD && $resultO){
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_article_extend.php?typ=document';
		}

		$body = we_html_element::htmlIFrame('edheader', WEBEDITION_DIR . 'we_showMod.php?mod=shop&pnt=edheader&top=1&home=' . $home . '&mid=' . $mid . '&bid=' . $bid . '&typ=object&ViewClass=' . $classid, 'position:absolute;top:0px;height:40px;left:0px;right:0px;', '', '', false) .
			we_html_element::htmlIFrame('edbody', $bodyURL, 'position:absolute;top:40px;bottom:0px;left:0px;right:0px;', '', '', true);
		return $this->getHTMLDocument(we_html_element::htmlBody([], $body));
	}

	protected function getHTMLEditorHeader($mode = 0){
		$DB_WE = $this->db;
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorHeader(0);
		}

		if(we_base_request::_(we_base_request::BOOL, 'top')){
			return $this->getHTMLEditorHeaderTop();
		}

		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);

		$hash = getHash('SELECT IntCustomerID,DATE_FORMAT(DateOrder,"' . g_l('date', '[format][mysqlDate]') . '") AS d FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . $bid . ' LIMIT 1', $DB_WE);
		if($hash){
			$cid = $hash['IntCustomerID'];
			$cdat = $hash['d'];
		} else {
			$cid = 0;
			$cdat = '';
		}
		$we_tabs = new we_tabs();

		if(!empty($_REQUEST["mid"]) && $_REQUEST["mid"] != '00'){
			$we_tabs->addTab(g_l('tabs', '[module][overview]'), true, 0);
		} else {
			$we_tabs->addTab(g_l('tabs', '[module][orderdata]'), true, "setTab(" . self::TAB_OVERVIEW . ");");
			$we_tabs->addTab(g_l('tabs', '[module][orderlist]'), false, "setTab(" . self::TAB_ORDERLIST . ");");
		}

		$textPre = g_l('modules_shop', $bid > 0 ? '[orderList][order]' : '[order_view]');
		$textPost = !empty($_REQUEST['mid']) && $_REQUEST['mid'] > 0 ? (strlen($_REQUEST['mid']) > 5 ? g_l('modules_shop', '[month][' . substr($_REQUEST['mid'], 0, -5) . ']') . " " . substr($_REQUEST['mid'], -5, 4) : substr($_REQUEST['mid'], 1)) : ($bid ? sprintf(g_l('modules_shop', '[orderNo]'), $bid, $cdat) : '');

		$tab_head = we_tabs::getHeader('
function setTab(tab) {
	switch (tab) {
		case ' . self::TAB_OVERVIEW . ':
			parent.edbody.document.location = WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=shop&pnt=edbody&bid=' . $bid . '";
			break;
		case ' . self::TAB_ORDERLIST . ':
			parent.edbody.document.location = WE().consts.dirs.WE_MODULES_DIR+"shop/edit_shop_orderlist.php?cid=' . $cid . '";
			break;
	}
}');

		$tab_body_content = '<div id="main"><div id="headrow"><b>' . str_replace(" ", "&nbsp;", $textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $textPost) . '</b></span></div>' .
			$we_tabs->getHTML() .
			'</div>';
		$tab_body = we_html_element::htmlBody(array("onresize" => "weTabs.setFrameSize()", "onload" => "weTabs.setFrameSize()", "id" => "eHeaderBody"), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	function getHTMLEditorHeaderTop(){
//$yid = we_base_request::_(we_base_request::INT, "ViewYear", date("Y"));
//$bid = we_base_request::_(we_base_request::INT, "bid", 0);
//$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), "IntCustomerID", $this->db);
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);
		$headline = ($data ? sprintf(g_l('modules_shop', '[lastOrder]'), $data["IntOrderID"], $data["orddate"]) : '');

/// config
		$feldnamen = explode('|', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="shop" AND pref_name="shop_pref"', '', $this->db));
		$fe = isset($feldnamen[3]) ? explode(",", $feldnamen[3]) : array(0);

		$classid = $fe[0];
		$resultO = array_shift($fe);

// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

// grep the last element from the year-set, wich is the current year
		$yearTrans = f('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd DESC LIMIT 1', '', $this->db);


		$we_tabs = new we_tabs();
		if(!empty($_REQUEST["mid"])){
			$we_tabs->addTab(g_l('tabs', '[module][overview]'), true, "//");
		} else {
			switch(true){
				default:
				case ($resultD):
					$we_tabs->addTab(g_l('tabs', '[module][admin_1]'), true, "setTab(" . self::TAB_ADMIN1 . ");");
				case ($resultO):
					$we_tabs->addTab(g_l('tabs', '[module][admin_2]'), (!$resultD), "setTab(" . self::TAB_ADMIN2 . ");");
				case (isset($yearTrans) && $yearTrans != 0):
					$we_tabs->addTab(g_l('tabs', '[module][admin_3]'), false, "setTab(" . self::TAB_ADMIN3 . ");");
					break;
			}
		}

		$tab_head = we_tabs::getHeader('
function setTab(tab) {
	switch (tab) {
		case ' . self::TAB_ADMIN1 . ':
			parent.edbody.document.location = WE().consts.dirs.WE_MODULES_DIR+"shop/edit_shop_article_extend.php?typ=document";
			break;
		case ' . self::TAB_ADMIN2 . ':
			parent.edbody.document.location = WE().consts.dirs.WE_MODULES_DIR+"shop/edit_shop_article_extend.php?typ=object&ViewClass=' . $classid . '";
			break;
		' . (isset($yearTrans) ? '
		case ' . self::TAB_ADMIN2 . ':
			parent.edbody.document.location = WE().consts.dirs.WE_MODULES_DIR+"shop/edit_shop_revenueTop.php?ViewYear=' . $yearTrans . '" // " + treeData.yearshop
			break;
		' : '') . '
	}
}');

		$tab_body_content = '<div id="main"><div id="headrow">&nbsp;' . we_html_element::htmlB($headline) . '</div>' .
			$we_tabs->getHTML() .
			'</div>';
		$tab_body = we_html_element::htmlBody(array('id' => 'eHeaderBody'), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	public function getHTML($what = '', $mode = '', $step = 0){
		switch($what){
			case 'iconbar':
				return $this->getHTMLIconbar();
			case 'frameset':
				$bid = we_base_request::_(we_base_request::INT, 'bid');
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode(), ($bid > 0 ? '&bid=' . ($bid === -1 ? intval(f('SELECT MAX(IntOrderID) FROM ' . SHOP_TABLE, '', $this->db)) : $bid) : '&top=1&home=1'));
			default:
				return parent::getHTML($what, $mode, $step);
		}
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		return $this->View->getProperties();
	}

}
