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
	protected $treeDefaultWidth = 204;

	function __construct($frameset){
		parent::__construct(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php');
		$this->Tree = new we_shop_tree($this->frameset, "top.content", "top.content", "top.content.cmd");
		$this->View = new we_shop_view(WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php', 'top.content');
	}

	function getJSCmdCode(){
		//taken from old edit_shop_frameset.php
		// grep the last element from the year-set, wich is the current year
		$this->db->query('SELECT DATE_FORMAT(MAX(DateOrder),"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' LIMIT 1');
		while($this->db->next_record()){
			$strs = array($this->db->f("DateOrd"));
			$yearTrans = end($strs);
		}
		// print $yearTrans;
		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', 'strFelder', $this->db));
		for($i = 0; $i <= 3; $i++){
			$feldnamen[$i] = isset($feldnamen[$i]) ? $feldnamen[$i] : '';
		}

		$fe = explode(',', $feldnamen[3]);
		if(empty($classid)){
			$classid = $fe[0];
		}
		//$resultO = count ($fe);
		$resultO = array_shift($fe);

		// whether the resultset is empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name ="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);


		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		$out = '
var hot = 0;

parent.document.title = "' . $title . '";

function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd(){
	var args = "";

	var url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "new_shop":
			top.content.editor.location="<?php echo WE_SHOP_MODULE_DIR; ?>edit_shop_frameset.php?pnt=editor";
			break;
		case "delete_shop":
			if (top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot == 1 ) {
				if(confirm("' . g_l('modules_shop', '[del_shop]') . '")){
					top.content.editor.edbody.deleteorder();
				}
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
			}
			break;
		case "new_article":
			if (top.content.right && top.content.editor.edbody.hot && top.content.editor.edbody.hot == 1 ) {
				top.content.editor.edbody.neuerartikel();
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[no_order_there]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			break;
		case "revenue_view":
		//FIXME: this is not correct; document doesnt work like this
			' . ($resultD ? 'top.content.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=document";' :
				(!empty($resultO) ? 'top.content.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=' . $classid . '";' :
					'top.content.editor.location="' . WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?pnt=editor&top=1&typ=document";')) . '
			break;
		';

		$years = we_shop_shop::getAllOrderYears();
		foreach($years as $cur){
			$out .= '
		case "year' . $cur . '":
			top.content.location="' . WE_MODULES_DIR . 'show.php?mod=shop&year=' . $cur . '";
				break;
		';
		}

		$out .= '
		case "pref_shop":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_pref.php","shoppref",-1,-1,470,600,true,true,true,false);
			break;

		case "edit_shop_vats":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_vats.php","edit_shop_vats",-1,-1,500,450,true,false,true,false);
			break;

		case "edit_shop_shipping":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_shipping.php","edit_shop_shipping",-1,-1,700,600,true,false,true,false);
			break;
		case "edit_shop_status":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_status.php","edit_shop_status",-1,-1,700,780,true,true,true,false);
			break;
		case "edit_shop_vat_country":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_vat_country.php","edit_shop_vat_country",-1,-1,700,780,true,true,true,false);
			break;
		case "payment_val":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_payment.php","shoppref",-1,-1,520,720,true,false,true,false);
			break;

		case "edit_shop_categories":
			var wind = new (WE().util.jsWindow)(top.window, "' . WE_SHOP_MODULE_DIR . 'edit_shop_categories.php","edit_shop_categories",-1,-1,500,450,true,false,true,false);
			break;

		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.opener.top.we_cmd.apply(this, args);

			break;
	}
}
		';

		return we_html_element::jsElement($out);
	}

	function getHTMLFrameset(){
		$extraHead = $this->Tree->getJSTreeCode();

		if(($bid = we_base_request::_(we_base_request::INT, 'bid')) === -1){
			$bid = intval(f('SELECT MAX(IntOrderID) FROM ' . SHOP_TABLE, '', $this->db));
		}

		$extraUrlParams = $bid > 0 ? '&bid=' . $bid : '&top=1&home=1';

		return parent::getHTMLFrameset($extraHead, $extraUrlParams);
	}

	function getHTMLIconbar(){ //TODO: move this to weShopView::getHTMLIconbar();
		$extraHead = we_html_element::jsElement('
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd() {
	switch (arguments[0]) {
		case "openOrder":
			//TODO: check this adress: mit oder ohne tree? Bisher: left
			if(top.content.doClick) {
				top.content.doClick(arguments[1], arguments[2], arguments[3]);//TODO: check this adress
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
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);

		$headline = $data ? '<a style="text-decoration: none;" href="javascript:we_cmd(\'openOrder\', ' . $data['IntOrderID'] . ',\'shop\',\'' . SHOP_TABLE . '\');">' . sprintf(g_l('modules_shop', '[lastOrder]'), $data['IntOrderID'], $data['orddate']) . '</a>' : '';

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
		$iconBarTable = new we_html_table(array("cellpadding" => 6, "style" => "margin-left:8px"), 1, 4);

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("fa:btn_shop_extArt,fa-lg fa-cart-plus", "javascript:top.opener.top.we_cmd('new_article')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("fa:btn_shop_delOrd,fa-lg fa-shopping-cart,fa-lg fa-trash-o", "javascript:top.opener.top.we_cmd('delete_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($resultD){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_sum,fa-lg fa-line-chart', "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=document '", true));
		} elseif($resultO){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, null, we_html_button::create_button('fa:btn_shop_sum,fa-lg fa-line-chart', "javascript:top.content.editor.location=' edit_shop_frameset.php?pnt=editor&top=1&typ=object&ViewClass=$classid '", true));
		}

		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("fa:btn_shop_pref,fa-lg fa-pencil,fa-lg fa-list-alt", "javascript:top.opener.top.we_cmd('pref_shop')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));
		$iconBarTable->setCol(0, $c++, null, we_html_button::create_button("fa:btn_payment_val,fa-lg fa-long-arrow-right,fa-lg fa-money", "javascript:top.opener.top.we_cmd('payment_val')", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_USER")));

		if($headline){
			$iconBarTable->addCol();
			$iconBarTable->setCol(0, $c++, array('style' => 'text-align:right', 'class' => 'header_shop'), '<span style="margin-left:15px">' . $headline . '</span>');
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
			$bodyURL = $this->frameset . '?home=1';
		} elseif($mid){
			$year = substr($mid, (strlen($mid) - 4));
			$month = str_replace($year, '', $mid);
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $year . '&ViewMonth=' . $month;
		} elseif($yearView){
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_revenueTop.php?ViewYear=' . $yearView;
		} else {
			$bodyURL = WE_SHOP_MODULE_DIR . 'edit_shop_frameset.php?bid=' . $bid;
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('style' => 'position: fixed; top: 0px; left: 0px; right: 0px; bottom: 0px; border: 0px none;'), we_html_element::htmlIFrame('edheader', $this->frameset . '?pnt=edheader&home=' . $home . '&mid=' . $mid . $yearView . '&bid=' . $bid, 'position: absolute; top: 0px; left: 0px; right: 0px; height: 40px; overflow: hidden;', '', '', false) .
					we_html_element::htmlIFrame('edbody', $bodyURL . '&pnt=edbody', 'position: absolute; top: 40px; bottom: 0px; left: 0px; right: 0px;', 'border:0px;width:100%;height:100%;')
				)
		);
	}

	function getHTMLEditorTop(){// TODO: merge getHTMLRight and getHTMLRightTop
		$DB_WE = $this->db;

		$home = we_base_request::_(we_base_request::BOOL, 'home');
		$mid = we_base_request::_(we_base_request::INT, 'mid', 0);
		$bid = we_base_request::_(we_base_request::INT, 'bid', 0);

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
			$bodyURL = $this->frameset . '?home=1&pnt=edbody'; //same as in getHTMLRight()
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
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][overview]'), we_tab::ACTIVE, 0));
		} else {
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][orderdata]'), we_tab::ACTIVE, "setTab(0);"));
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][orderlist]'), we_tab::NORMAL, "setTab(1);"));
		}

		$textPre = g_l('modules_shop', $bid > 0 ? '[orderList][order]' : '[order_view]');
		$textPost = !empty($_REQUEST['mid']) && $_REQUEST['mid'] > 0 ? (strlen($_REQUEST['mid']) > 5 ? g_l('modules_shop', '[month][' . substr($_REQUEST['mid'], 0, -5) . ']') . " " . substr($_REQUEST['mid'], -5, 4) : substr($_REQUEST['mid'], 1)) : ($bid ? sprintf(g_l('modules_shop', '[orderNo]'), $bid, $cdat) : '');

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
}');

		$tab_body_content = '<div id="main"><div id="headrow"><nobr><b>' . str_replace(" ", "&nbsp;", $textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . str_replace(" ", "&nbsp;", $textPost) . '</b></span></nobr></div>' .
			$we_tabs->getHTML() .
			'</div>';
		$tab_body = we_html_element::htmlBody(array("onresize" => "setFrameSize()", "onload" => "setFrameSize()", "id" => "eHeaderBody"), $tab_body_content);

		return $this->getHTMLDocument($tab_body, $tab_head);
	}

	function getHTMLEditorHeaderTop(){
//$yid = we_base_request::_(we_base_request::INT, "ViewYear", date("Y"));
//$bid = we_base_request::_(we_base_request::INT, "bid", 0);
//$cid = f('SELECT IntCustomerID FROM ' . SHOP_TABLE . ' WHERE IntOrderID=' . intval($bid), "IntCustomerID", $this->db);
		$data = getHash("SELECT IntOrderID,DATE_FORMAT(DateOrder,'" . g_l('date', '[format][mysqlDate]') . "') AS orddate FROM " . SHOP_TABLE . ' GROUP BY IntOrderID ORDER BY IntID DESC LIMIT 1', $this->db);
		$headline = ($data ? sprintf(g_l('modules_shop', '[lastOrder]'), $data["IntOrderID"], $data["orddate"]) : '');

/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . WE_SHOP_PREFS_TABLE . ' WHERE strDateiname="shop_pref"', '', $this->db));
		$fe = isset($feldnamen[3]) ? explode(",", $feldnamen[3]) : array(0);

		$classid = $fe[0];
		$resultO = array_shift($fe);

// wether the resultset ist empty?
		$resultD = f('SELECT 1 FROM ' . LINK_TABLE . ' WHERE Name="' . WE_SHOP_TITLE_FIELD_NAME . '" LIMIT 1', '', $this->db);

// grep the last element from the year-set, wich is the current year
		$yearTrans = f('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd DESC LIMIT 1', '', $this->db);


		$we_tabs = new we_tabs();
		if(!empty($_REQUEST["mid"])){
			$we_tabs->addTab(new we_tab(g_l('tabs', '[module][overview]'), we_tab::ACTIVE, "//"));
		} else {
			switch(true){
				default:
				case ($resultD):
					$we_tabs->addTab(new we_tab(g_l('tabs', '[module][admin_1]'), we_tab::ACTIVE, "setTab(0);"));
				case ($resultO):
					$we_tabs->addTab(new we_tab(g_l('tabs', '[module][admin_2]'), ($resultD ? we_tab::NORMAL : we_tab::ACTIVE), "setTab(1);"));
				case (isset($yearTrans) && $yearTrans != 0):
					$we_tabs->addTab(new we_tab(g_l('tabs', '[module][admin_3]'), we_tab::NORMAL, "setTab(2);"));
					break;
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
}');

		$tab_body_content = '<div id="main"><div id="headrow">&nbsp;' . we_html_element::htmlB($headline) . '</div>' .
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

	function getJSStart(){
		return 'start();';
	}

	protected function getHTMLEditorBody(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->View->getHomeScreen();
		}
		return $this->View->getProperties();
	}

}
