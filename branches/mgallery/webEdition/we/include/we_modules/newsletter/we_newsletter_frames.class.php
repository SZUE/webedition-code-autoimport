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
class we_newsletter_frames extends we_modules_frame{
	const def_width = 450;

	private $weAutoCompleter;

	function __construct(){
		parent::__construct(WE_NEWSLETTER_MODULE_DIR . 'edit_newsletter_frameset.php');
		$this->module = 'newsletter';
		$this->View = new we_newsletter_view();
		$this->View->setFrames('top.content', 'top.content.tree', 'top.content.cmd');
		$this->Tree = new we_newsletter_tree($this->frameset, 'top.content', 'top.content', 'top.content.cmd');
		$this->setFrames('top.content', 'top.content', 'top.content.cmd');
		$this->weAutoCompleter = &weSuggest::getInstance();
	}

	public function getHTMLDocumentHeader($what = '', $mode = ''){
		switch($what){
			case 'send':
			case 'send_body':
			case 'send_cmd':
			//case 'edbody':
			case 'preview':
			case 'black_list':
			case 'newsletter_settings':
			case 'eemail':
			case 'edit_file':
			case 'clear_log':
			case 'export_csv_mes':
			case 'qsend':
			case 'qsave1':
				return;
			default:
				echo parent::getHTMLDocumentHeader();
		}
	}

	function getHTML($what = '', $mode = 0){
		switch($what){
			case 'edheader':
				return $this->getHTMLEditorHeader($mode);
			case 'edfooter':
				return $this->getHTMLEditorFooter($mode);
			case 'qlog':
				return $this->getHTMLLogQuestion();
			case 'domain_check':
				return $this->getHTMLDCheck();
			case 'show_log':
				return $this->getHTMLLog();
			case 'newsletter_settings':
				return $this->getHTMLSettings();
			case 'print_lists':
				return $this->getHTMLPrintLists();
			case 'qsend':
				return $this->getHTMLSendQuestion();
			case 'qsave1':
				return $this->getHTMLSaveQuestion1();
			case 'eemail':
				return $this->getHTMLEmailEdit();
			case 'preview':
				return $this->getHTMLPreview();
			case 'black_list':
				return $this->getHTMLBlackList();
			case 'upload_black':
				return $this->getHTMLUploadCsv($what);
			case 'upload_csv':
				return $this->getHTMLUploadCsv($what);
			case 'export_csv_mes':
				return $this->getHTMLExportCsvMessage();
			case 'edit_file':
				return $this->getHTMLEditFile($mode);
			case 'clear_log':
				return $this->getHTMLClearLog();
			case 'send':
				return $this->getHTMLSendWait();
			case 'send_frameset':
				return $this->getHTMLSendFrameset();
			case 'send_body':
				return $this->getHTMLSendBody();
			case 'send_cmd':
				return $this->getHTMLSendCmd();
			case 'send_control':
				return $this->getHTMLSendControl();
			default:
				return parent::getHTML($what);
		}
	}

	function getHTMLFrameset(){
		$extraHead = we_html_element::jsElement('
				var hot = 0;
				var scrollToVal = 0;
			') .
			$this->Tree->getJSTreeCode();

		return parent::getHTMLFrameset($extraHead);
	}

	function getJSCmdCode(){
		echo $this->View->getJSTopCode();
	}

	/**
	 * Modul Header
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @param Integer $mode
	 * @return String
	 */
	protected function getHTMLEditorHeader($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, "home")){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#F0EFF0"), ""));
		}

		$group = we_base_request::_(we_base_request::BOOL, "group");

		$page = ($group ? 0 : we_base_request::_(we_base_request::INT, "page", 0));


		$textPre = g_l('modules_newsletter', ($group ? '[group]' : '[newsletter][text]'));

		$textPost = we_base_request::_(we_base_request::STRING, "txt", g_l('modules_newsletter', ($group ? '[new_newsletter_group]' : '[new_newsletter]')));

		$js = we_html_element::jsElement('
function setTab(tab) {
	switch (tab) {
		case 0:
			top.content.editor.edbody.we_cmd("switchPage",0);
			break;

		case 1:
			top.content.editor.edbody.we_cmd("switchPage",1);
			break;

		case 2:
			top.content.editor.edbody.we_cmd("switchPage",2);
			break;

		case 3: //Tab Auswertung
			top.content.editor.edbody.we_cmd("switchPage",3);
			break;
	}
}');

		$we_tabs = new we_tabs();

		$we_tabs->addTab(new we_tab(g_l('modules_newsletter', '[property]'), (($page == 0) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(0);"));

		if(!$group){
			$we_tabs->addTab(new we_tab(sprintf(g_l('modules_newsletter', '[mailing_list]'), ""), (($page == 1) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(1);"));
			$we_tabs->addTab(new we_tab(g_l('modules_newsletter', '[edit]'), (($page == 2) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(2);"));
			//if($this->View->newsletter->ID){ // zusaetzlicher tab fuer auswertung
			$we_tabs->addTab(new we_tab(g_l('modules_newsletter', '[reporting][tab]'), (($page == 3) ? "TAB_ACTIVE" : "TAB_NORMAL"), "self.setTab(3);"));
			//}
		}

		$tabHead = $we_tabs->getHeader() . $js;

		$body = we_html_element::htmlBody(array("onresize" => "setFrameSize()", "onload" => "setFrameSize()", "id" => "eHeaderBody"), '<div id="main"><div id="headrow"><nobr><b>' . oldHtmlspecialchars($textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b></span></nobr></div>' .
				$we_tabs->getHTML() .
				'</div>'
		);
		return $this->getHTMLDocument($body, $tabHead);
	}

	/**
	 * Modul Body
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @return String
	 */
	protected function getHTMLEditorBody(){
		return $this->getHTMLProperties();
	}

	/**
	 * Modul Footer
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @param Integer $mode
	 * @return String
	 */
	protected function getHTMLEditorFooter($mode = 0){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return $this->getHTMLDocument(we_html_element::htmlBody(array("bgcolor" => "#EFF0EF"), ""));
		}

		$group = we_base_request::_(we_base_request::INT, "group", 0);

		$js = we_html_element::jsElement('
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = /webEdition/we_cmd.php?";
	for(var i = 0; i < arguments.length; i++){
	url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
	if(i < (arguments.length - 1)){
	url += "&";
	}
	}

	switch (arguments[0]) {
		case "empty_log":
			break;

		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			parent.edbody.we_cmd.apply(this, args);

	}
}

function addGroup(text, val) {
	 ' . ($group ? '' : 'document.we_form.gview[document.we_form.gview.length] = new Option(text,val);' ) . '
}

function delGroup(val) {
	 document.we_form.gview[val] = null;
}

function populateGroups() {
	if (top.content.editor.edbody.getGroupsNum) {

		if (top.content.editor.edbody.loaded) {
			var num=top.content.editor.edbody.getGroupsNum();

				if (!num) {
					num = 1;
				} else {
					num++;
				}

				addGroup(sprintf("' . g_l('modules_newsletter', '[all_list]') . '",0),0);

				for (i = 1; i < num; i++) {
					addGroup(sprintf("' . g_l('modules_newsletter', '[mailing_list]') . '",i),i);
				}
		} else {
			setTimeout(populateGroups,100);
		}
	} else {
		setTimeout(populateGroups,100);
	}
}

function we_save() {
		setTimeout(\'top.content.we_cmd("save_newsletter")\',100);
}

function afterLoad(){
if(self.document.we_form.htmlmail_check!==undefined) {
	if(top.opener.top.nlHTMLMail) {
		self.document.we_form.htmlmail_check.checked = true;
		document.we_form.hm.value=1;
	} else {
		self.document.we_form.htmlmail_check.checked = false;
		document.we_form.hm.value=0;
	}
}
}');

		$select = new we_html_select(array('name' => 'gview'));

		$table2 = new we_html_table(array('class' => 'default', "width" => 300), 1, 5);
		if($mode == 0){
			$table2->setRow(0, array('style' => 'vertical-align:middle;'));

			$table2->setCol(0, 0, array("nowrap" => null), ((permissionhandler::hasPerm("NEW_NEWSLETTER") || permissionhandler::hasPerm("EDIT_NEWSLETTER")) ?
					we_html_button::create_button(we_html_button::SAVE, "javascript:we_save()") :
					""
				)
			);

			if(!$group){
				$table2->setCol(0, 1, array("nowrap" => null, 'style' => 'padding-left:70px;'), $select->getHtml());
				$table2->setCol(0, 2, array("nowrap" => null, 'style' => 'padding-left:5px;'), we_html_forms::checkbox(0, false, "htmlmail_check", g_l('modules_newsletter', '[html_preview]'), false, "defaultfont", "if(document.we_form.htmlmail_check.checked) { document.we_form.hm.value=1;top.opener.top.nlHTMLMail=1; } else { document.we_form.hm.value=0;top.opener.top.nlHTMLMail=0; }"));
				$table2->setCol(0, 3, array("nowrap" => null), we_html_button::create_button(we_html_button::PREVIEW, "javascript:we_cmd('popPreview')"));
				$table2->setCol(0, 4, array("nowrap" => null), (permissionhandler::hasPerm("SEND_NEWSLETTER") ? we_html_button::create_button("send", "javascript:we_cmd('popSend')") : ""));
			}
		}

		$body = we_html_element::htmlBody(array("id" => "footerBody", "onload" => "afterLoad();setTimeout(populateGroups,100)"), we_html_element::htmlForm(array(), we_html_element::htmlHidden("hm", 0) .
					$table2->getHtml()
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLLog(){
		$start = we_base_request::_(we_base_request::INT, 'newsletterStartTime', 0);
		$end = we_base_request::_(we_base_request::INT, 'newsletterEndTime', 0);
		$status = we_base_request::_(we_base_request::INT, 'newsletterStatus');
		$this->View->db->query('SELECT Log,Param,DATE_FORMAT(stamp,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS LogTime FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ($status !== false ? ' AND Log=' . $status : '') . ($start && $end ? ' AND stamp BETWEEN FROM_UNIXTIME(' . $start . ') AND FROM_UNIXTIME(' . $end . ')' : '') . ' ORDER BY stamp DESC');

		$content = "";
		while($this->View->db->next_record()){
			$log = g_l('modules_newsletter', '[' . $this->View->db->f('Log') . ']');
			$param = $this->View->db->f("Param");
			$content.=we_html_element::htmlDiv(array("class" => "defaultfont"), $this->View->db->f("LogTime") . '&nbsp;' . ($param ? sprintf($log, $param) : $log));
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_tools::htmlDialogLayout(
							we_html_element::htmlDiv(array("class" => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;margin:5px 10px 15px 10px;"), $content)
							, g_l('modules_newsletter', '[show_log]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
						)
					)
				), we_html_element::jsElement("self.focus();"));
	}

	/*
	 * Grafische Aufbereitung des Versandlogs im Tab "Auswertung"
	 */

	function getHTMLReporting(){

		function getPercent($total, $value, $precision = 0){
			$result = ($total ? round(($value * 100) / $total, $precision) : 0);
			return we_base_util::formatNumber($result, strtolower($GLOBALS['WE_LANGUAGE']));
		}

		$this->View->db->query('SELECT Log,stamp,DATE_FORMAT(stamp,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS LogTime FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ' AND Log IN(\'log_start_send\', \'log_end_send\') ORDER BY stamp ASC');

		$newsletterMailOrders = array();
		$newsletterMailOrdersCnt = 0;

		while($this->View->db->next_record()){
			if($this->View->db->f("Log") === "log_start_send"){
				$newsletterMailOrders[++$newsletterMailOrdersCnt]['start_send'] = $this->View->db->f("stamp");
				$newsletterMailOrders[$newsletterMailOrdersCnt]['startTime'] = $this->View->db->f("LogTime");
			} else {
				$newsletterMailOrders[$newsletterMailOrdersCnt]['end_send'] = $this->View->db->f("stamp");
			}
		}

		$parts = array();

		foreach($newsletterMailOrders as $key => $newsletterMailOrder){
			$table = new we_html_table(array('class' => 'defaultfont', 'style' => 'width: 588px'), 1, 5);
			$this->View->db->query('SELECT Log,COUNT(1) FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ' AND Log NOT IN (\'log_start_send\',\'log_end_send\') AND stamp BETWEEN "' . $newsletterMailOrder['start_send'] . '" AND "' . (isset($newsletterMailOrder['end_send']) ? $newsletterMailOrder['end_send'] : 'NOW()') . '" GROUP BY Log');

			$results = $this->View->db->getAllFirst(false);

			$allRecipients = array_sum($results);

			/* process bar blocked by blacklist */
			$allBlockedByBlacklist = (array_key_exists("email_is_black", $results) ? $results['email_is_black'] : 0);
			$percentBlockedByBlacklist = getPercent($allRecipients, $allBlockedByBlacklist, 2);

			$pbByB = new we_progressBar($percentBlockedByBlacklist);
			$pbByB->setName('blacklist' . $key);
			$pbByB->setStudWidth(10);
			$pbByB->setStudLen(150);

			$table->addRow();
			$table->setColContent(1, 0, we_html_element::htmlSpan(array('id' => 'blacklist_' . $key), g_l('modules_newsletter', '[reporting][mailing_emails_are_black]')));
			$table->setColContent(1, 1, $pbByB->getJS() . $pbByB->getHTML());
			$table->setCol(1, 2, array("style" => "padding: 0 5px 0 5px;"), we_html_element::htmlSpan(array('id' => 'blacklist_total', 'style' => 'color:' . (($allBlockedByBlacklist > 0) ? 'red' : 'green') . ';'), $allBlockedByBlacklist));
			$table->setCol(1, 3, array("style" => "padding: 0 5px 0 5px;"), '<i class="fa fa-lg ' . ($allBlockedByBlacklist == 0 ? "fa-check fa-ok" : "fa-close fa-cancel") . '"></i>');
			//todo: statt show black list, sollte show_log begrenzt auf Log=email_is_black + $start_send + start_end
			$table->setCol(1, 4, array('style' => 'width: 35px'), (($allBlockedByBlacklist == 0) ? '' : we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('black_list');"))));

			/* process bar blocked by domain check */
			$allBlockedByDomainCheck = (array_key_exists("domain_nok", $results) ? $results['domain_nok'] : 0);
			$percentBlockedByDomain = getPercent($allRecipients, $allBlockedByDomainCheck, 2);

			$pbBbD = new we_progressBar($percentBlockedByDomain);
			$pbBbD->setName('domain' . $key);
			$pbBbD->setStudWidth(10);
			$pbBbD->setStudLen(150);

			$table->addRow();
			$table->setColContent(2, 0, we_html_element::htmlSpan(array('id' => 'domain_' . $key), g_l('modules_newsletter', '[reporting][mailing_emails_nok]')));
			$table->setColContent(2, 1, $pbBbD->getJS() . $pbBbD->getHTML());
			$table->setCol(2, 2, array("style" => "padding: 0 5px 0 5px;"), we_html_element::htmlSpan(array('id' => 'domain_total', 'style' => 'color:' . (($allBlockedByDomainCheck > 0) ? 'red' : 'green') . ';'), $allBlockedByDomainCheck));
			$table->setCol(2, 3, array("style" => "padding: 0 5px 0 5px;"), '<i class="fa fa-lg ' . ($allBlockedByDomainCheck == 0 ? "fa-check fa-ok" : "fa-close fa-cancel") . '"></i>');
			//todo: statt domain, sollte show_log begrenzt auf Log=domain_nok + $start_send + start_end
			$table->setCol(2, 4, array('style' => 'width: 35px'), (($allBlockedByDomainCheck == 0) ? '' : we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('domain_check');"))));

			/* process bar all clear recipients */
			$allClearRecipients = (array_key_exists("mail_sent", $results) ? $results['mail_sent'] : 0);
			$percentClearRecipients = getPercent($allRecipients, $allClearRecipients, 2);

			$pbCR = new we_progressBar($percentClearRecipients);
			$pbCR->setName('recipients' . $key);
			$pbCR->setStudWidth(10);
			$pbCR->setStudLen(150);

			$table->addRow();
			$table->setColContent(3, 0, we_html_element::htmlSpan(array('id' => 'recipients_' . $key), g_l('modules_newsletter', '[reporting][mailing_emails_success]')));
			$table->setColContent(3, 1, $pbCR->getJS() . $pbCR->getHTML());
			$table->setCol(3, 2, array("style" => "padding: 0 5px 0 5px;"), we_html_element::htmlSpan(array('id' => 'recipients_total', 'style' => 'color:' . (($allClearRecipients <= 0) ? 'red' : 'green') . ';'), $allClearRecipients));
			$table->setCol(3, 3, array("style" => "padding: 0 5px 0 5px;"), '<i class="fa fa-lg ' . ($allClearRecipients == $allRecipients ? "fa-check fa-ok" : "fa-exclamation-triangle fa-cancel") . '" title="' . ($allClearRecipients < $allRecipients ? g_l('modules_newsletter', '[reporting][mailing_advice_not_success]') : '') . '"></i>');
			//todo: statt show_log, sollte show_log begrenzt auf Log=email_sent + $start_send + start_end
			$table->setCol(3, 4, array('style' => 'width: 35px'), we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('show_log')")));

			/* total recipients */
			$table->addRow();
			$table->setColContent(4, 0, we_html_element::htmlB(g_l('modules_newsletter', '[reporting][mailing_all_emails]')));
			$table->setCol(4, 2, array('colspan' => 2, "style" => "padding: 0 5px 0 5px;"), we_html_element::htmlB($allRecipients));

			$parts[] = array(
				"headline" => g_l('modules_newsletter', '[reporting][mailing_send_at]') . '&nbsp;' . $newsletterMailOrder['startTime'],
				"html" => $table->getHTML() . we_html_element::htmlBr()
			);
		}

		return $parts;
	}

	function getHTMLCmd(){
		$pid = we_base_request::_(we_base_request::INT, 'pid');
		if($pid === false){
			exit;
		}

		$rootjs = (!$pid ?
				$this->Tree->topFrame . '.treeData.clear();' .
				$this->Tree->topFrame . '.treeData.add(' . $this->Tree->topFrame . '.rootEntry(\'' . $pid . '\',\'root\',\'root\'));' :
				'');


		$hiddens = we_html_element::htmlHiddens(array(
				"pnt" => "cmd",
				"ncmd" => "",
				"nopt" => ""));

		return $this->getHTMLDocument(we_html_element::htmlBody(array(), we_html_element::htmlForm(array("name" => "we_form"), $hiddens .
						we_html_element::jsElement($rootjs . $this->Tree->getJSLoadTree(we_newsletter_tree::getItemsFromDB($pid)))
					)
		));
	}

	function getHTMLSendQuestion(){
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onblur" => "self.focus", "onunload" => "doUnload()"), we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[continue_camp]'), IMAGE_DIR . "alert.gif", "ja", "nein", "abbrechen", "opener.yes();self.close();", "opener.no();self.close();", "opener.cancel();self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLSaveQuestion1(){
		$body = we_html_element::htmlBody(array("class" => "weEditorBody", "onblur" => "self.focus", "onunload" => "doUnload()"), we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[ask_to_preserve]'), IMAGE_DIR . "alert.gif", "ja", "nein", "", "opener.document.we_form.ask.value=0;opener.we_cmd('save_newsletter');self.close();", "self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLPrintLists(){
		$emails = array();
		$out = '';
		$count = count($this->View->newsletter->groups) + 1;

		$tab1 = '&nbsp;&nbsp;&nbsp;';
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;
		$c = 0;
		for($k = 1; $k < $count; $k++){
			$out.=we_html_element::htmlBr() .
				we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[mailing_list]'), $k)));
			$gc = 0;
			if(defined('CUSTOMER_TABLE')){
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[customers]'));
				$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_CUSTOMER, 1);

				foreach($emails as $email){
					$gc++;
					$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
				}
			}

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[emails]'));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_EMAILS, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
			}

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . g_l('modules_newsletter', '[file_email]'));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_FILE, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . $email);
			}
			$c+=$gc;
			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[sum_group]'), $k) . ":" . $gc));
		}

		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[sum_all]') . ":" . $c)) .
			we_html_element::htmlBr();
		echo self::getHTMLDocument(we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onload" => "self.focus()"), we_html_tools::htmlDialogLayout(
						we_html_element::htmlBr() .
						we_html_element::htmlDiv(array("class" => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"), $out) .
						we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
					)
		)));
		flush();
	}

	function getHTMLDCheck(){
		$tab1 = "&nbsp;&nbsp;&nbsp;";
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;

		$emails = array();
		$count = count($this->View->newsletter->groups) + 1;

		$out = we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_begins]'))) .
			we_html_element::htmlBr();

		for($k = 1; $k < $count; $k++){

			$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab2 . sprintf(g_l('modules_newsletter', '[domain_check_list]'), $k));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_ALL, 1);

			foreach($emails as $email){
				if($this->View->newsletter->check_email($email)){
					$domain = "";

					if(!$this->View->newsletter->check_domain($email, $domain)){
						$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . sprintf(g_l('modules_newsletter', '[domain_nok]'), $domain));
					}
				} else {
					$out.=we_html_element::htmlDiv(array("class" => "defaultfont"), $tab3 . sprintf(g_l('modules_newsletter', '[email_malformed]'), $email));
				}
			}
		}
		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(array("class" => "defaultfont"), $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_ends]'))) .
			we_html_element::htmlBr();
		echo self::getHTMLDocument(we_html_element::htmlBody(array('class' => 'weDialogBody'), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onload" => "self.focus()"), we_html_tools::htmlDialogLayout(
						we_html_element::htmlBr() .
						we_html_element::htmlDiv(array("class" => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"), $out) .
						we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
					)
		)));
		flush();
	}

	/* creates the FileChoooser field with the "browse"-Button. Clicking on the Button opens the fileselector */

	private function formFileChooser($width = '', $IDName = 'ParentID', $IDValue = '/', $cmd = '', $filter = '', $acObject = null, $contentType = ''){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server','" . $wecmdenc1 . "','" . $filter . "',document.we_form.elements['" . $IDName . "'].value);");

		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($IDName, 30, $IDValue, '', 'readonly', 'text', $width, 0), '', 'left', 'defaultfont', '', permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES') ? $button : '');
	}

	function getHTMLSettings(){
		$settings = we_newsletter_view::getSettings();

		$closeflag = false;

		if(we_base_request::_(we_base_request::STRING, "ncmd") === 'save_settings'){
			$this->View->processCommands();
			$closeflag = true;
		}


		$js = $this->View->getJSProperty();

		$texts = array('send_step', 'send_wait', 'test_account', 'default_sender', 'default_reply', we_newsletter_newsletter::FEMALE_SALUTATION_FIELD, we_newsletter_newsletter::MALE_SALUTATION_FIELD);
		$radios = array('reject_malformed', 'reject_not_verified', 'reject_save_malformed', 'log_sending', 'default_htmlmail', 'isEmbedImages', 'title_or_salutation', 'use_base_href', 'use_https_refer', 'use_port');
		$extra_radio_text = array('use_port');
		$defaults = array('reject_save_malformed' => 1, 'use_https_refer' => 0, 'send_wait' => 0, 'use_port' => 0, 'use_port_check' => 80, 'isEmbedImages' => 0, 'use_base_href' => 1);

		$table = new we_html_table(array('class' => 'default withSpace'), 1, 3);
		$c = 0;

		foreach($texts as $text){

			if(!isset($settings[$text])){
				$this->View->putSetting($text, (isset($defaults[$text]) ? $defaults[$text] : 0));
				$settings = we_newsletter_view::getSettings();
			}

			$botPad = ($text === 'default_reply' || $text == we_newsletter_newsletter::MALE_SALUTATION_FIELD ? 10 : 5);

			$table->setCol($c, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[' . $text . ']') . ":&nbsp;");
			$table->setCol($c, 2, array("class" => "defaultfont", 'style' => 'padding-left:5px;padding-bottom:' . $botPad . 'px;'), we_html_tools::htmlTextInput($text, 40, $settings[$text], "", "", "text", 308));

			$c++;
			$table->addRow();
		}

		if(defined('CUSTOMER_TABLE')){
			$custfields = array();

			foreach($this->View->customers_fields as $fv){
				$custfields[$fv] = $fv;
			}

			$table->addRow(6);

			$table->setCol($c, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[customer_email_field]') . ":&nbsp;");
			$table->setCol($c, 0, 1, array('style' => 'width:5px;'));
			$table->setCol($c, 2, array("class" => "defaultfont", 'style' => 'padding-left:5px'), we_html_tools::htmlSelect("customer_email_field", $custfields, 1, $settings["customer_email_field"], false, array(), "value", 308));

			$table->setCol($c + 1, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_html_field]') . ':&nbsp;');
			$table->setCol($c + 1, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_html_field', $custfields, 1, $settings['customer_html_field'], false, array(), 'value', 308));

			$table->setCol($c + 2, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_salutation_field]') . ':&nbsp;');
			$table->setCol($c + 2, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_salutation_field', $custfields, 1, $settings['customer_salutation_field'], false, array(), 'value', 308));

			$table->setCol($c + 3, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_title_field]') . ':&nbsp;');
			$table->setCol($c + 3, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_title_field', $custfields, 1, $settings['customer_title_field'], false, array(), 'value', 308));

			$table->setCol($c + 4, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_firstname_field]') . ':&nbsp;');
			$table->setCol($c + 4, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_firstname_field', $custfields, 1, $settings['customer_firstname_field'], false, array(), 'value', 308));

			$table->setCol($c + 5, 0, array('class' => 'defaultfont'), g_l('modules_newsletter', '[customer_lastname_field]') . ':&nbsp;');
			$table->setCol($c + 5, 2, array('class' => 'defaultfont'), we_html_tools::htmlSelect('customer_lastname_field', $custfields, 1, $settings['customer_lastname_field'], false, array(), 'value', 308));
		}

		$close = we_html_button::create_button(we_html_button::CLOSE, 'javascript:self.close();');
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_settings')");

		$radios_code = '';
		foreach($radios as $radio){
			if(!isset($settings[$radio])){

				$this->View->putSetting($radio, (isset($defaults[$radio]) ? $defaults[$radio] : 1));
				$settings = we_newsletter_view::getSettings();
			}
			if(in_array($radio, $extra_radio_text)){
				$radios_code.= we_html_forms::checkbox($settings[$radio], (($settings[$radio] > 0) ? true : false), $radio . "_check", g_l('modules_newsletter', '[' . $radio . "_check]"), false, "defaultfont", "if(document.we_form." . $radio . "_check.checked) document.we_form." . $radio . ".value=" . (isset($defaults[$radio . "_check"]) ? $defaults[$radio . "_check"] : 0) . "; else document.we_form." . $radio . ".value=0;");

				$radio_table = new we_html_table(array('class' => 'default', 'style' => 'margin-left:25px;'), 1, 2);
				$radio_table->setCol(0, 0, array("class" => "defaultfont"), oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')) . ":&nbsp;");
				$radio_table->setCol(0, 1, array("class" => "defaultfont", 'style' => 'padding-left:5px;'), we_html_tools::htmlTextInput($radio, 5, $settings[$radio], "", "OnChange='if(document.we_form." . $radio . ".value!=0) document.we_form." . $radio . "_check.checked=true; else document.we_form." . $radio . "_check.checked=false;'"));
				$radios_code.=$radio_table->getHtml();
			} else {
				$radios_code.=we_html_forms::checkbox($settings[$radio], (($settings[$radio] == 1) ? true : false), $radio, oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')), false, "defaultfont", "if(document.we_form." . $radio . ".checked) document.we_form." . $radio . ".value=1; else document.we_form." . $radio . ".value=0;");
			}
		}

		$deselect = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.global_mailing_list.value=''");

		$gml_table = new we_html_table(array('class' => 'default withSpace', "width" => 538), 4, 2);
		$gml_table->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[global_mailing_list]'));
		$gml_table->setCol(2, 0, array(), $this->formFileChooser(380, "global_mailing_list", $settings["global_mailing_list"]));
		$gml_table->setCol(2, 1, array('style' => 'text-align:right'), $deselect);

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(array("name" => "we_form"), $this->View->getHiddens() .
					we_html_tools::htmlDialogLayout(
						$table->getHtml() .
						we_html_tools::getPixel(5, 10) .
						$radios_code .
						we_html_tools::getPixel(5, 15) .
						$gml_table->getHtml() .
						we_html_tools::getPixel(5, 10), g_l('modules_newsletter', '[settings]'), we_html_button::position_yes_no_cancel($save, $close)
					)
				)
				. ($closeflag ? we_html_element::jsElement('top.close();') : "")
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLBlockType($name, $selected = 1){
		$values = array(
			we_newsletter_block::DOCUMENT => g_l('modules_newsletter', '[newsletter_type_0]'),
			we_newsletter_block::DOCUMENT_FIELD => g_l('modules_newsletter', '[newsletter_type_1]'),
		);

		if(defined('OBJECT_TABLE')){
			$values[we_newsletter_block::OBJECT] = g_l('modules_newsletter', '[newsletter_type_2]');
			$values[we_newsletter_block::OBJECT_FIELD] = g_l('modules_newsletter', '[newsletter_type_3]');
		}

		if(permissionhandler::hasPerm("NEWSLETTER_FILES")){
			$values[we_newsletter_block::FILE] = g_l('modules_newsletter', '[newsletter_type_4]');
		}
		$values[we_newsletter_block::TEXT] = g_l('modules_newsletter', '[newsletter_type_5]');
		$values[we_newsletter_block::ATTACHMENT] = g_l('modules_newsletter', '[newsletter_type_6]');
		$values[we_newsletter_block::URL] = g_l('modules_newsletter', '[newsletter_type_7]');

		return we_html_tools::htmlSelect($name, $values, 1, $selected, false, array('style' => "width:440px;", 'onchange' => 'we_cmd(\'switchPage\',2);'), "value", 315, "defaultfont");
	}

	function getHTMLBox($w, $h, $content, $headline = "", $width = 120, $height = 2){
		$headline = str_replace(" ", "&nbsp;", $headline);

		return '<table class="default" style="margin:' . $height . 'px 0 ' . $height . 'px 24px;">' . ($headline ? '<tr>
		<td style="vertical-align:top" class="defaultgray">' . $headline . '</td>
		<td>' . $content . '</td>
	</tr>
</table>' : '
	<tr>
		<td></td>
		<td>' . $content . '</td>
	</tr>
</table>'
			);
	}

	function getHTMLCopy(){
		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements.copyid.value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements.copyid_text.value");
		$wecmdenc3 = we_base_request::encCmd("opener.we_cmd('copy_newsletter');");

		return we_html_element::htmlHiddens(array('copyid' => 0,
				'copyid_text' => "")) .
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_file',document.we_form.elements.copyid.value,'" . NEWSLETTER_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . get_ws(NEWSLETTER_TABLE) . "')");
	}

	function getHTMLCustomer($group){
		$out = we_html_forms::checkbox($this->View->newsletter->groups[$group]->SendAll, (($this->View->newsletter->groups[$group]->SendAll == 0) ? false : true), "sendallcheck_$group", g_l('modules_newsletter', '[send_all]'), false, "defaultfont", "we_cmd('switch_sendall',$group);");

		if($this->View->newsletter->groups[$group]->SendAll == 0){

			$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_customers'," . $group . ")");
			$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.we_cmd(\\'add_customer\\',top.allIDs," . $group . ");','','','',1)");

			$cats = new we_chooser_multiDir(self::def_width, $this->View->newsletter->groups[$group]->Customers, "del_customer", $delallbut . $addbut, "", '"we/customer"', CUSTOMER_TABLE);
			$cats->extraDelFn = "document.we_form.ngroup.value=$group";
			$out.=$cats->get();
		}

		$out.=$this->getHTMLCustomerFilter($group);

		return $out;
	}

	function getHTMLExtern($group){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_files'," . $group . ")");
		$wecmdenc4 = we_base_request::encCmd("opener.we_cmd('add_file',top.currentID,$group);");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('browse_server','fileselect','" . we_base_ContentTypes::TEXT . "','/','" . $wecmdenc4 . "','',1);");


		$buttons = $delallbut . (permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES')) ? $addbut : '';

		$cats = new we_chooser_multiFile(self::def_width, $this->View->newsletter->groups[$group]->Extern, "del_file", $buttons, "edit_file");

		$cats->extraDelFn = 'document.we_form.ngroup.value=' . $group;
		return we_html_element::htmlHiddens(array('fileselect' => '')) .
			$cats->get();
	}

	function getHTMLCustomerFilter($group){
		$custfields = array();
		foreach($this->View->customers_fields as $fv){
			switch($fv){
				case 'ParentID':
				case 'IsFolder':
				case 'Path':
				case 'Text' :
					continue;
				default:
					$custfields[$fv] = $fv;
			}
		}

		$operators = array(
			we_newsletter_newsletter::OP_EQ => '=',
			we_newsletter_newsletter::OP_NEQ => '<>',
			we_newsletter_newsletter::OP_LE => '<',
			we_newsletter_newsletter::OP_LEQ => '<=',
			we_newsletter_newsletter::OP_GE => '>',
			we_newsletter_newsletter::OP_GEQ => '>=',
			we_newsletter_newsletter::OP_CONTAINS => g_l('modules_newsletter', '[operator][contains]'),
			we_newsletter_newsletter::OP_STARTS => g_l('modules_newsletter', '[operator][startWith]'),
			we_newsletter_newsletter::OP_ENDS => g_l('modules_newsletter', '[operator][endsWith]'),
			we_newsletter_newsletter::OP_LIKE => 'LIKE',
		);
		$logic = array('AND' => g_l('modules_newsletter', '[logic][and]'), "OR" => g_l('modules_newsletter', '[logic][or]'));
		$hours = array();
		for($i = 0; $i < 24; $i++){
			$hours[] = ($i <= 9 ? '0' : '') . $i;
		}
		$minutes = array();
		for($i = 0; $i < 60; $i++){
			$minutes[] = ($i <= 9 ? '0' : '') . $i;
		}

		$filter = $this->View->newsletter->groups[$group]->getFilter();

		$table = new we_html_table(array('class' => 'default'), 1, 7);
		$colspan = "7";
		$table->setCol(0, 0, (($filter) ? array("colspan" => $colspan) : array()), we_html_forms::checkbox(($filter ? 1 : 0), ($filter ? true : false), "filtercheck_$group", g_l('modules_newsletter', '[filter]'), false, "defaultfont", "if(document.we_form.filtercheck_$group.checked) we_cmd('add_filter',$group); else we_cmd('del_all_filters',$group);"));

		$k = 0;
		$c = 1;
		if($filter){
			foreach($filter as $k => $v){
				if($k != 0){
					$table->addRow();
					$table->setCol($c, 0, array("colspan" => $colspan), we_html_tools::htmlSelect("filter_logic_" . $group . "_" . $k, $logic, 1, $v["logic"], false, array(), "value", 70));
					$c++;
				}

				$table->addRow();
				$table->setCol($c, 0, array(), we_html_tools::htmlSelect("filter_fieldname_" . $group . "_" . $k, $custfields, 1, $v["fieldname"], false, array('onchange' => 'top.content.hot=1;changeFieldValue(this.val,\'filter_fieldvalue_' . $group . '_' . $k . '\');'), "value", 170));
				$table->setCol($c, 1, array(), we_html_tools::htmlSelect("filter_operator_" . $group . "_" . $k, $operators, 1, $v["operator"], false, array('onchange' => "top.content.hot=1;"), "value", 80));
				if($v['fieldname'] === "MemberSince" || $v['fieldname'] === "LastLogin" || $v['fieldname'] === "LastAccess"){
					$table->setCol($c, 2, array("id" => "td_value_fields_" . $group . "_" . $k), we_html_tools::getDateSelector("filter_fieldvalue_" . $group . "_" . $k, "_from_" . $group . "_" . $k, !empty($v["fieldvalue"]) ? !stristr($v["fieldvalue"], ".") ? date("d.m.Y", $v["fieldvalue"]) : $v["fieldvalue"] : ""));
					$table->setCol($c, 3, array(), we_html_tools::htmlSelect("filter_hours_" . $group . "_" . $k, $hours, 1, isset($v["hours"]) ? $v["hours"] : "", false, array('onchange' => 'top.content.hot=1;')));
					$table->setCol($c, 4, array("class" => "defaultfont"), "&nbsp;h :");
					$table->setCol($c, 5, array(), we_html_tools::htmlSelect("filter_minutes_" . $group . "_" . $k, $minutes, 1, isset($v["minutes"]) ? $v["minutes"] : "", false, array('onchange' => "top.content.hot=1;")));
					$table->setCol($c, 6, array("class" => "defaultfont"), "&nbsp;m");
				} else {
					$table->setCol($c, 2, array("colspan" => $colspan, "id" => "td_value_fields_" . $group . "_" . $k), we_html_tools::htmlTextInput("filter_fieldvalue_" . $group . "_" . $k, 16, isset($v["fieldvalue"]) ? $v["fieldvalue"] : "", "", 'onKeyUp="top.content.hot=1;"', "text", 200));
				}

				$c++;
			}
		}

		if($filter){
			$plus = we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('add_filter',$group)");
			$trash = we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('del_filter',$group)");

			$c++;
			$table->addRow();
			$table->setCol($c, 0, array("colspan" => $colspan, 'style' => 'padding-top:5px;'), $plus . $trash);
		}

		$js = we_html_element::jsElement("calendarSetup(" . $group . "," . $k . ");");

		return we_html_element::htmlHiddens(array("filter_" . $group => count($filter))) .
			$table->getHtml() . $js;
	}

	/**
	 * Mailing list - block Emails
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 *
	 * @param unknown_type $group
	 * @return unknown
	 */
	function getHTMLEmails($group){
		$arr = $this->View->newsletter->getEmailsFromList(oldHtmlspecialchars($this->View->newsletter->groups[$group]->Emails), 1);
		// Buttons to handle the emails in  the email list
		$buttons_table = new we_html_table(array('class' => 'default withSpace'), 4, 1);
		$buttons_table->setCol(0, 0, array(), we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('add_email', " . $group . ");"));
		$buttons_table->setCol(1, 0, array(), we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('edit_email', " . $group . ");"));
		$buttons_table->setCol(2, 0, array(), we_html_button::create_button(we_html_button::DELETE, "javascript:deleteit(" . $group . ")"));
		$buttons_table->setCol(3, 0, array(), we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:deleteall(" . $group . ")"));

		// Dialog table for the email block
		$table = new we_html_table(array('class' => 'default withSpace'), 3, 3);

		// 1. ROW: select status
		$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", array("onchange" => "weShowMailsByStatus(this.value, $group);", 'id' => 'weViewByStatus'), "value", 150);
		$table->setCol(0, 0, array('style' => 'vertical-align:middle;', "colspan" => 3, "class" => "defaultfont"), $selectStatus);

		// 2. ROW: Mail list with handling buttons
		$table->setCol(1, 0, array('style' => 'vertical-align:top;'), $this->View->newsletter->htmlSelectEmailList("we_recipient" . $group, $arr, 10, "", false, 'style="width:' . (self::def_width - 110) . 'px; height:140px" id="we_recipient' . $group . '"', "value", 600));
		$table->setCol(1, 1, array('style' => 'vertical-align:middle;width:10px;'));
		$table->setCol(1, 2, array('style' => 'vertical-align:top;'), $buttons_table->getHtml());

		// 3. ROW: Buttons for email import and export
		$importbut = we_html_button::create_button("import", "javascript:we_cmd('set_import'," . $group . ")");
		$exportbut = we_html_button::create_button("export", "javascript:we_cmd('set_export'," . $group . ")");

		$table->setCol(2, 0, array("colspan" => 3), $importbut . $exportbut);

		// Import dialog
		if($this->View->getShowImportBox() == $group){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('import_csv')");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('reset_import');");

			$import_options = new we_html_table(array('class' => 'default withSpace'), 7, 3);

			$import_options->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, array(), we_html_tools::htmlTextInput("csv_delimiter" . $group, 1, ","));
			$import_options->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(1, 1, array(), we_html_tools::htmlTextInput("csv_col" . $group, 2, 1));
			$import_options->setCol(2, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_hmcol]') . ":&nbsp;");
			$import_options->setCol(2, 1, array(), we_html_tools::htmlTextInput("csv_hmcol" . $group, 2, 2));
			$import_options->setCol(2, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_html_explain]'));
			$import_options->setCol(3, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_salutationcol]') . ":&nbsp;");
			$import_options->setCol(3, 1, array(), we_html_tools::htmlTextInput("csv_salutationcol" . $group, 2, 3));
			$import_options->setCol(3, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_salutation_explain]'));
			$import_options->setCol(4, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_titlecol]') . ":&nbsp;");
			$import_options->setCol(4, 1, array(), we_html_tools::htmlTextInput("csv_titlecol" . $group, 2, 4));
			$import_options->setCol(4, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_title_explain]'));
			$import_options->setCol(5, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_firstnamecol]') . ":&nbsp;");
			$import_options->setCol(5, 1, array(), we_html_tools::htmlTextInput("csv_firstnamecol" . $group, 2, 5));
			$import_options->setCol(5, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_firstname_explain]'));
			$import_options->setCol(6, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_lastnamecol]') . ":&nbsp;");
			$import_options->setCol(6, 1, array(), we_html_tools::htmlTextInput("csv_lastnamecol" . $group, 2, 6));
			$import_options->setCol(6, 2, array("class" => "defaultgray"), "&nbsp;" . g_l('modules_newsletter', '[csv_lastname_explain]'));


			$import_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'margin-top:10px;'), 4, 1);
			$import_box->setCol(0, 0, array(), $this->formFileChooser(200, "csv_file" . $group, "/", ""));
			$import_box->setCol(1, 0, array(), we_html_button::create_button(we_html_button::UPLOAD, "javascript:we_cmd('upload_csv',$group)"));
			$import_box->setCol(2, 0, array(), $import_options->getHtml());
			$import_box->setCol(3, 0, array("nowrap" => null), $ok . $cancel);

			$table->setCol(5, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_import" => $group)) . $import_box->getHtml());
		}

		// Export dialog
		if($this->View->getShowExportBox() == $group){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('export_csv')");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('reset_import');");

			$export_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'margin-top:10px;'), 2, 1);

			$export_box->setCol(1, 0, array(), $this->formFileChooser(200, "csv_dir" . $group, "/", "", "folder"));
			$export_box->setCol(2, 0, array("nowrap" => null), $ok . $cancel);

			$table->setCol(5, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_export" => $group)) . $export_box->getHtml());
		}

		return $table->getHtml();
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $open_doc = '', $acObject = null, $contentType = ''){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), '', $this->db);
		}

		$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['" . $IDName . "'].value");
		$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value");
		$wecmdenc3 = we_base_request::encCmd(str_replace('\\', '', $cmd));
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','" . $wecmdenc3 . "','','" . $rootDirID . "','','" . $open_doc . "')");
		if(is_object($acObject)){

			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'top.content.hot=1; readonly', 'text', $width, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(trim($IDName), oldHtmlspecialchars($IDValue)), $button);
	}

	private function formWeDocChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $filter = we_base_ContentTypes::WEDOCUMENT, $acObject = null){
		$Pathvalue = $Pathvalue? : f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), '', $this->db);

		$wecmd1 = "document.we_form.elements['" . $IDName . "'].value";

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $wecmd1 . ",'" . $table . "','" . we_base_request::encCmd($wecmd1) . "','" . we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value") . "','" . we_base_request::encCmd(str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "','" . $filter . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");
		if(is_object($acObject)){
			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType(implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME)));
			$yuiSuggest->setInput($Pathname, $Pathvalue);
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DocSelector);
			$yuiSuggest->setTable($table);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);
			return $yuiSuggest->getHTML();
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, "", 'top.content.hot=1; readonly', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(trim($IDName), oldHtmlspecialchars($IDValue)), $button);
	}

	function getHTMLNewsletterBlocks(){
		$counter = 0;

		$parts = array(
			array("headline" => "", "html" => we_html_element::htmlHiddens(array("blocks" => count($this->View->newsletter->blocks))), "space" => 140, "noline" => 1)
		);

		foreach($this->View->newsletter->blocks as $block){
			$content = we_html_tools::htmlFormElementTable($this->getHTMLBlockType("block" . $counter . "_Type", $block->Type), g_l('modules_newsletter', '[name]'));

			$values = array();
			$count = count($this->View->newsletter->groups) + 1;

			for($i = 1; $i < $count; $i++){
				$values[$i] = sprintf(g_l('modules_newsletter', '[mailing_list]'), $i);
			}

			$selected = $block->Groups ? : "1";
			$content.=we_html_element::htmlHiddens(array("block" . $counter . "_Groups" => $selected,
					"block" . $counter . "_Pack" => $block->Pack)) .
				we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_GroupsSel", $values, 5, $selected, true, array("style" => 'width:440px;', "onchange" => "PopulateMultipleVar(document.we_form.block" . $counter . "_GroupsSel,document.we_form.block" . $counter . "_Groups);top.content.hot=1")), g_l('modules_newsletter', '[block_lists]'));

			switch($block->Type){
				case we_newsletter_block::DOCUMENT:
					$content.=we_html_tools::htmlFormElementTable($this->formWeDocChooser(FILE_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.top.content.hot=1;", we_base_ContentTypes::WEDOCUMENT, $this->weAutoCompleter), g_l('modules_newsletter', '[block_document]')) .
						we_html_tools::htmlFormElementTable(we_html_forms::checkbox((($block->Field) ? 0 : 1), (($block->Field) ? false : true), "block" . $counter . "_use_def_template", g_l('modules_newsletter', '[use_default]'), false, "defaultfont", "top.content.hot=1;if(document.we_form.block" . $counter . "_use_def_template.checked){ document.we_form.block" . $counter . "_Field.value=0; document.we_form.block" . $counter . "_FieldPath.value='';}"), "&nbsp;&nbsp;&nbsp;") .
						we_html_tools::htmlFormElementTable($this->formWeChooser(TEMPLATES_TABLE, 320, 0, "block" . $counter . "_Field", (!is_numeric($block->Field) ? 0 : $block->Field), "block" . $counter . "_FieldPath", "", "if(opener.document.we_form.block" . $counter . "_use_def_template.checked) opener.document.we_form.block" . $counter . "_use_def_template.checked=false;opener.top.content.hot=1;", "", $this->weAutoCompleter, 'folder,' . we_base_ContentTypes::TEMPLATE), g_l('modules_newsletter', '[block_template]'));
					break;

				case we_newsletter_block::DOCUMENT_FIELD:
					$content.=we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.we_cmd(\'switchPage\',2);opener.top.content.hot=1;", "", $this->weAutoCompleter, "folder," . we_base_ContentTypes::WEDOCUMENT), g_l('modules_newsletter', '[block_document]'));

					if($block->LinkID){
						$values = $this->View->getFields($block->LinkID, FILE_TABLE);

						$content.=(!empty($values) ?
								we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_Field", $values, 1, $block->Field, "", array("style" => 'width:440px;', "onkeyup" => 'top.content.hot=1;')), g_l('modules_newsletter', '[block_document_field]')) :
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
							);
					}
					break;

				case we_newsletter_block::OBJECT:
					$content.=we_html_tools::htmlFormElementTable($this->formWeChooser(OBJECT_FILES_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.top.content.hot=1;", (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1), $this->weAutoCompleter, "folder,objectFile"), g_l('modules_newsletter', '[block_object]')) .
						we_html_tools::htmlFormElementTable($this->formWeChooser(TEMPLATES_TABLE, 320, 0, "block" . $counter . "_Field", (!is_numeric($block->Field) ? 0 : $block->Field), "block" . $counter . "_FieldPath", "", "opener.top.content.hot=1;", "", $this->weAutoCompleter, 'folder,' . we_base_ContentTypes::TEMPLATE), g_l('modules_newsletter', '[block_template]'));
					break;

				case we_newsletter_block::OBJECT_FIELD:
					$content.=we_html_tools::htmlFormElementTable($this->formWeChooser(OBJECT_FILES_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "opener.we_cmd(\'switchPage\',2);opener.top.content.hot=1;", (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1), $this->weAutoCompleter, "folder,objectFile"), g_l('modules_newsletter', '[block_object]'));

					if($block->LinkID){
						$values = $this->View->getFields($block->LinkID, OBJECT_FILES_TABLE);

						$content.=(!empty($values) ?
								we_html_tools::htmlFormElementTable(we_html_tools::htmlSelect("block" . $counter . "_Field", $values, 1, $block->Field, false, array('OnChange' => "top.content.hot=1;")), g_l('modules_newsletter', '[block_object_field]')) :
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array("class" => "defaultgray"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
							);
					}
					break;

				case we_newsletter_block::FILE:
					$content.=we_html_tools::htmlFormElementTable($this->formFileChooser(320, "block" . $counter . "_Field", (is_numeric($block->Field) ? "" : ((substr($block->Field, 0, 1) != "/") ? "" : $block->Field))), g_l('modules_newsletter', '[block_file]'));
					break;

				case we_newsletter_block::TEXT:
					$attribs = array(
						"wysiwyg" => "on",
						"width" => 430,
						"height" => 200,
						"rows" => 10,
						"cols" => 40,
						"cols" => 40,
						"style" => "width:440px;",
						"inlineedit" => "true",
						"bgcolor" => "white",
					);
					$blockHtml = preg_replace(
						array(
						'/(href=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/',
						'/(src=")(\\\\*&quot;)*(.+?)(\\\\*&quot;)*(")/'
						), '${1}${3}${5}', stripslashes($block->Html));


					$content.=we_html_tools::htmlFormElementTable(we_html_element::htmlTextArea(array("cols" => 40, "rows" => 10, "name" => "block" . $counter . "_Source", "onchange" => "top.content.hot=1;", "style" => "width:440px;"), oldHtmlspecialchars($block->Source)), g_l('modules_newsletter', '[block_plain]')) .
						we_html_element::jsScript(JS_DIR . "we_textarea.js") .
						we_html_tools::htmlFormElementTable(we_html_forms::weTextarea("block" . $counter . "_Html", $blockHtml, $attribs, "", "", true, "", true, true, false, true, $this->View->newsletter->Charset), g_l('modules_newsletter', '[block_html]')) .
						we_html_element::jsElement('
function extraInit(){
	if(typeof weWysiwygInitializeIt == "function"){
		weWysiwygInitializeIt();
	}
	loaded = 1;
}
window.onload=extraInit;');

					break;

				case we_newsletter_block::ATTACHMENT:
					$content.=we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "", "", $this->weAutoCompleter, implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH, we_base_ContentTypes::QUICKTIME))), g_l('modules_newsletter', '[block_attachment]'));
					break;

				case we_newsletter_block::URL:
					$content.=we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("block" . $counter . "_Field", 49, (is_numeric($block->Field) ? "" : $block->Field), "", "style='width:440px;'", "text", 0, 0, "top.content"), g_l('modules_newsletter', '[block_url]'));
					break;
			}

			$buttons = we_html_tools::getPixel(440, 1);

			$plus = we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('addBlock','" . $counter . "')");
			$trash = we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('delBlock','" . $counter . "')");

			$buttons.=(count($this->View->newsletter->blocks) > 1 ?
					we_html_button::position_yes_no_cancel($plus, $trash) :
					we_html_button::position_yes_no_cancel($plus)
				);

			$parts[] = array("headline" => sprintf(g_l('modules_newsletter', '[block]'), ($counter + 1)), "html" => $content, "space" => 140);
			$parts[] = array("headline" => "", "html" => $buttons, "space" => 140);

			$counter++;
		}

		return we_html_multiIconBox::getHTML("newsletter_header", "100%", $parts, 30, "", -1, "", "", false);
	}

	function getHTMLNewsletterGroups(){
		$count = count($this->View->newsletter->groups);

		$out = we_html_multiIconBox::getJS();

		for($i = 0; $i < $count; $i++){
			$parts = array();

			if(defined('CUSTOMER_TABLE')){
				$parts[] = array("headline" => g_l('modules_newsletter', '[customers]'), "html" => $this->getHTMLCustomer($i), "space" => 140);
			}

			$parts[] = array("headline" => g_l('modules_newsletter', '[file_email]'), "html" => $this->getHTMLExtern($i), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[emails]'), "html" => $this->getHTMLEmails($i), "space" => 140);


			$plus = ($i == $count - 1 ? we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('addGroup')") : null);
			$trash = ($count > 1 ? we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('delGroup'," . $i . ")") : null);

			$buttons = $plus . $trash;

			$wepos = weGetCookieVariable("but_newsletter_group_box_$i");

			$out.= we_html_multiIconBox::getHTML("newsletter_group_box_$i", "100%", $parts, 30, "", 0, "", "", (($wepos === "down") || ($count < 2 ? true : false)), sprintf(g_l('modules_newsletter', '[mailing_list]'), ($i + 1))) .
				we_html_element::htmlBr() . '<div style="margin-right:30px;">' . $buttons . '</div>';
		}

		return $out;
	}

	function formNewsletterDirChooser($width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $acObject = null){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . intval($IDValue), '', $this->db);
		}

		$wecmd1 = "document.we_form.elements['" . $IDName . "'].value";

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('openNewsletterDirselector'," . $wecmd1 . ",'" . we_base_request::encCmd($wecmd1) . "','" . we_base_request::encCmd("document.we_form.elements['" . $Pathname . "'].value") . "','" . we_base_request::encCmd(str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "')");
		if(is_object($acObject)){
			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId('PathGroup');
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput($Pathname, str_replace('\\', '/', $Pathvalue));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(true);
			$yuiSuggest->setResult($IDName, $IDValue);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(NEWSLETTER_TABLE);
			$yuiSuggest->setWidth($width);
			$yuiSuggest->setSelectButton($button);

			return $yuiSuggest->getHTML();
		}
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'top.content.hot=1; readonly id="yuiAcInputPathGroup"', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(trim($IDName), oldHtmlspecialchars($IDValue)), $button
		);
	}

	function getHTMLNewsletterHeader(){
		$table = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		$table->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", 37, stripslashes($this->View->newsletter->Text), "", 'onKeyUp="top.content.hot=1;" id="yuiAcInputPathName" onblur="parent.edheader.setPathName(this.value); parent.edheader.setTitlePath()"', 'text', self::def_width), g_l('modules_newsletter', '[name]')));
		$table->setCol(2, 0, array(), we_html_tools::htmlFormElementTable($this->formNewsletterDirChooser((self::def_width - 120), 0, "ParentID", $this->View->newsletter->ParentID, "Path", dirname($this->View->newsletter->Path), "opener.top.content.hot=1;", $this->weAutoCompleter), g_l('modules_newsletter', '[dir]')));

		//$table->setCol(2,0,array(),we_html_tools::htmlFormElementTable($this->formWeDocChooser(NEWSLETTER_TABLE,320,0,"ParentID",$this->View->newsletter->ParentID,"Path",dirname($this->View->newsletter->Path),"opener.top.content.hot=1;","folder"),g_l('modules_newsletter','[dir]')));
		$parts = array(
			array("headline" => "", "html" => "", "space" => 140, "noline" => 1),
			array("headline" => g_l('modules_newsletter', '[path]'), "html" => $table->getHtml(), "space" => 140),
		);

		if(!$this->View->newsletter->IsFolder){
			$table = new we_html_table(array('class' => 'default withSpace'), 4, 1);
			$table->setCol(0, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Subject", 37, stripslashes($this->View->newsletter->Subject), "", "onKeyUp='top.content.hot=1;'", 'text', self::def_width), g_l('modules_newsletter', '[subject]')));
			$table->setCol(1, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Sender", 37, $this->View->newsletter->Sender, "", "onKeyUp='top.content.hot=1;'", 'text', self::def_width), g_l('modules_newsletter', '[sender]')));

			$chk = ($this->View->newsletter->Sender == $this->View->newsletter->Reply ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "checked" => null, "name" => "reply_same", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value")) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 0, "name" => "reply_same", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value"))
				);
			$table->setCol(2, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Reply", 37, $this->View->newsletter->Reply, "", "onKeyUp='top.content.hot=1;'") . "&nbsp;&nbsp;" . $chk . "&nbsp;" . we_html_element::htmlLabel(array("class" => "defaultfont", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.reply_same.checked){document.we_form.reply_same.checked=false;}else{document.we_form.Reply.value=document.we_form.Sender.value;document.we_form.reply_same.checked=true;}"), g_l('modules_newsletter', '[reply_same]')), g_l('modules_newsletter', '[reply]')));
			$table->setCol(3, 0, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Test", 37, $this->View->newsletter->Test, "", "onKeyUp='top.content.hot=1;'"), g_l('modules_newsletter', '[test_email]')));

			$_embedImagesChk = ($this->View->newsletter->isEmbedImages ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "name" => "isEmbedImagesChk", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}", "checked" => null), g_l('modules_newsletter', '[isEmbedImages]')) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "name" => "isEmbedImagesChk", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}"), g_l('modules_newsletter', '[isEmbedImages]'))
				);
			$_embedImagesHid = we_html_element::htmlHidden("isEmbedImages", $this->View->newsletter->isEmbedImages);
			$_embedImagesLab = we_html_element::htmlLabel(array("class" => "defaultfont", "onclick" => $this->topFrame . ".hot=1;if(document.we_form.isEmbedImagesChk.checked){ document.we_form.isEmbedImagesChk.checked=false; document.we_form.isEmbedImages.value=0; }else{document.we_form.isEmbedImagesChk.checked=true;document.we_form.isEmbedImages.value=1;}"), g_l('modules_newsletter', '[isEmbedImages]'));

			$table->setCol(4, 0, array(), we_html_tools::htmlFormElementTable($_embedImagesHid . $_embedImagesChk . "&nbsp;" . $_embedImagesLab, ""));

			$parts[] = array("headline" => g_l('modules_newsletter', '[newsletter][text]'), "html" => $table->getHtml(), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[charset]'), "html" => $this->getHTMLCharsetTable(), "space" => 140);
			$parts[] = array("headline" => g_l('modules_newsletter', '[copy_newsletter]'), "html" => $this->getHTMLCopy(), "space" => 140, "noline" => 1);
		}

		return we_html_multiIconBox::getHTML("newsletter_header", "100%", $parts, 30, "", -1, "", "", false) .
			we_html_element::htmlBr();
	}

	/**
	 * Generates the body for modul frame
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @return unknown
	 */
	function getHTMLProperties(){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			$GLOBALS['we_print_not_htmltop'] = true;
			$GLOBALS['we_head_insert'] = $this->View->getJSProperty();
			$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->View->getHiddens(array('ncmd' => 'home')) . we_html_element::htmlHidden('home', 0));
			$GLOBALS['mod'] = 'newsletter';
			ob_start();
			include(WE_MODULES_PATH . 'home.inc.php');
			$out = ob_get_contents();
			ob_end_clean();
			return $out;
		}

		$js = $this->View->getJSProperty('setFocus();') .
			we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar.js') .
			we_html_element::jsScript(WE_INCLUDES_DIR . 'we_language/' . $GLOBALS["WE_LANGUAGE"] . '/calendar.js') .
			we_html_element::jsScript(LIB_DIR . 'additional/jscalendar/calendar-setup.js');

		$css = we_html_element::cssLink(LIB_DIR . "additional/jscalendar/skins/aqua/theme.css");


		$out = $this->View->getHiddens() .
			$this->View->newsletterHiddens() .
			$this->View->getHiddensProperty();

		switch($this->View->page){
			case 0:
				$out.=weSuggest::getYuiFiles() .
					we_html_element::htmlHiddens(array('home' => 0, "fromPage" => 0));

				if($this->View->newsletter->IsFolder == 0){
					$out.=$this->View->getHiddensMailingPage() .
						$this->View->getHiddensContentPage();
				}

				$out.=$this->getHTMLNewsletterHeader() .
					$this->weAutoCompleter->getYuiJs();
				break;
			case 1:
				$out.=$this->View->getHiddensPropertyPage() .
					$this->View->getHiddensContentPage() .
					we_html_element::htmlHiddens(array("fromPage" => 1, "ncustomer" => '', "nfile" => '', "ngroup" => '')) .
					$this->getHTMLNewsletterGroups();
				break;
			case 2:
				$out.=weSuggest::getYuiFiles() .
					$this->View->getHiddensMailingPage() .
					$this->View->getHiddensPropertyPage() .
					we_html_element::htmlHiddens(array("fromPage" => 2, "blockid" => 0)) .
					$this->getHTMLNewsletterBlocks() .
					$this->weAutoCompleter->getYuiJs();
				break;
			default:
				$out.=weSuggest::getYuiFiles() .
					$this->View->getHiddensPropertyPage() .
					$this->View->getHiddensMailingPage() .
					$this->View->getHiddensContentPage() .
					we_html_element::htmlHiddens(array("fromPage" => 3, "blockid" => 0)) .
					we_html_multiIconBox::getHTML('', "100%", $this->getHTMLReporting(), 30, '', -1, '', '', false) .
					$this->weAutoCompleter->getYuiJs();
		}

		$body = we_html_element::htmlBody(array("onload" => "self.loaded=1;if(self.doScrollTo){self.doScrollTo();}; setHeaderTitle();", "class" => "weEditorBody", "onunload" => "doUnload()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "onsubmit" => "return false;"), $out
				)
		);
//$this->getHTMLDocumentHeader();
		return $this->getHTMLDocument($body, $js . $css);
	}

	function getHTMLEmailEdit(){
		$type = we_base_request::_(we_base_request::INT, 'etyp', 0);
		$htmlmail = we_base_request::_(we_base_request::RAW, 'htmlmail', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name="default_htmlmail"', '', $this->db));
		$id = we_base_request::_(we_base_request::INT, 'eid', 0);
		$email = we_base_request::_(we_base_request::EMAIL, 'email', '');
		$group = we_base_request::_(we_base_request::STRING, 'grp', '');
		$salutation = rawurldecode(str_replace('[:plus:]', '+', we_base_request::_(we_base_request::STRING, 'salutation', '')));
		$title = rawurldecode(str_replace('[:plus:]', '+', we_base_request::_(we_base_request::STRING, 'title', '')));
		$firstname = rawurldecode(str_replace('[:plus:]', '+', we_base_request::_(we_base_request::STRING, 'firstname', '')));
		$lastname = rawurldecode(str_replace('[:plus:]', '+', we_base_request::_(we_base_request::STRING, 'lastname', '')));

		switch($type){
			case 2:
				$js = 'opener.setAndSave(document.we_form.id.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
					close();';
				break;
			case 1:
				$js = 'opener.editIt(document.we_form.group.value,document.we_form.id.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
				close();';
				break;
			default:
				$js = 'opener.add(document.we_form.group.value,document.we_form.emailfield.value,document.we_form.htmlmail.value,document.we_form.salutation.value,document.we_form.title.value,document.we_form.firstname.value,document.we_form.lastname.value);
				close();';
		}

		$js = we_html_element::jsElement('function save(){' . $js . '}');

		$row = 0;
		$table = new we_html_table(array('class' => 'default'), 12, 3);

		$table->setCol($row, 0, array("class" => "defaultgray"), g_l('modules_newsletter', '[email]'));
		$table->setCol($row++, 1, array('style' => "padding-left:15px;padding-bottom:2px;"), we_html_tools::htmlTextInput("emailfield", 32, $email, "", "", "text", 310));


		$table->setCol($row++, 2, array('style' => "padding-bottom:2px;"), we_html_forms::checkbox($htmlmail, (($htmlmail) ? true : false), "htmlmail", g_l('modules_newsletter', '[edit_htmlmail]'), false, "defaultfont", "if(document.we_form.htmlmail.checked) document.we_form.htmlmail.value=1; else document.we_form.htmlmail.value=0;"));

		$salut_select = new we_html_select(array("name" => "salutation", "style" => "width: 310px"));
		$salut_select->addOption("", "");
		if(!empty($this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], $this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD]);
		}
		if(!empty($this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], $this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD]);
		}
		$salut_select->selectOption($salutation);

		$table->setCol($row, 0, array("class" => "defaultgray", 'style' => "padding-bottom:2px;"), g_l('modules_newsletter', '[salutation]'));
		$table->setCol($row++, 1, array('style' => 'padding-left:15px;'), $salut_select->getHtml());


		$table->setCol($row, 0, array("class" => "defaultgray", 'style' => "padding-bottom:2px;"), g_l('modules_newsletter', '[title]'));
		$table->setCol($row++, 1, array('style' => 'padding-left:15px;'), we_html_tools::htmlTextInput("title", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($title) : $title), "", "", "text", 310));


		$table->setCol($row, 0, array("class" => "defaultgray", 'style' => "padding-bottom:2px;"), g_l('modules_newsletter', '[firstname]'));
		$table->setCol($row++, 1, array('style' => 'padding-left:15px;'), we_html_tools::htmlTextInput("firstname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($firstname) : $firstname), "", "", "text", 310));


		$table->setCol($row, 0, array("class" => "defaultgray", 'style' => "padding-bottom:2px;"), g_l('modules_newsletter', '[lastname]'));
		$table->setCol($row++, 1, array('style' => 'padding-left:15px;'), we_html_tools::htmlTextInput("lastname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($lastname) : $lastname), "", "", "text", 310));


		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");

		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => "document.we_form.emailfield.select();document.we_form.emailfield.focus();"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "save();return false;"), we_html_element::htmlHidden("group", $group) .
					($type ?
						we_html_element::htmlHidden("id", $id) :
						""
					) .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), g_l('modules_newsletter', $type ? '[edit_email]' : '[add_email]'), we_html_button::position_yes_no_cancel($save, $close)
					)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLPreview(){
		$gview = we_base_request::_(we_base_request::INT, "gview", 0);
		$hm = we_base_request::_(we_base_request::INT, "hm", 0);

		$content = '';
		$count = count($this->View->newsletter->blocks);
		for($i = 0; $i < $count; $i++){
			$content.=$this->View->getContent($i, $gview, $hm);
		}

		header("Pragma: no-cache;");
		header("Cache-Control: post-check=0, post-check=0, false");
		we_html_tools::headerCtCharset('text/html', ($this->View->newsletter->Charset ? : $GLOBALS['WE_BACKENDCHARSET']));


		if(!$hm){
			echo '<html><head></head><body><form>
							<textarea name="foo" style="width:100%;height:95%" cols="80" rows="40">' .
			oldHtmlspecialchars(trim($content)) .
			'</textarea></form></body></html>';
		} else {
			echo $content;
		}
	}

	function getHTMLBlackList(){
		$this->View->settings["black_list"] = we_base_request::_(we_base_request::STRING, "black_list", $this->View->settings["black_list"]);

		if(($ncmd = we_base_request::_(we_base_request::STRING, 'ncmd'))){
			if($ncmd === "save_black"){
				$this->View->processCommands();
			}
		}

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
function addBlack() {
	var p=document.we_form.elements.blacklist_sel;
	var newRecipient=prompt("' . g_l('modules_newsletter', '[add_email]') . '","");

	if (newRecipient != null) {
		if (newRecipient.length > 0) {
			if (newRecipient.length > 255 ) {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}

			if (!inSelectBox(p,newRecipient)) {
				addElement(p,"#",newRecipient,true);
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_exists]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}
	}
}

function deleteBlack() {
	var p=document.we_form.elements.blacklist_sel;

	if (p.selectedIndex >= 0) {
		if (confirm("' . g_l('modules_newsletter', '[email_delete]') . '")) {
			p.options[p.selectedIndex] = null;
		}
	}
}

function deleteallBlack() {
	var p=document.we_form.elements.blacklist_sel;

	if (confirm("' . g_l('modules_newsletter', '[email_delete_all]') . '")) {
		p.options.length = 0;
	}
}

function editBlack() {
	var p=document.we_form.elements.blacklist_sel;
	var index=p.selectedIndex;

	if (index >= 0) {
		var editRecipient=prompt("' . g_l('modules_newsletter', '[edit_email]') . '",p.options[index].text);

		if (editRecipient != null) {
			if (editRecipient != "") {
				if (editRecipient.length > 255 ) {
					' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[email_max_len]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					return;
				}
				p.options[index].text = editRecipient;
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[no_email]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
		}
	}
}

function set_import(val) {
	document.we_form.sib.value=val;

	if (val == 1) {
		document.we_form.seb.value=0;
	}

	PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
	submitForm("black_list");
}

function set_export(val) {
	document.we_form.seb.value=val;

	if (val == 1) {
		document.we_form.sib.value=0;
	}

	PopulateVar(document.we_form.blacklist_sel,document.we_form.black_list);
	submitForm("black_list");
}

self.focus();
');

		switch(we_base_request::_(we_base_request::STRING, "ncmd")){
			case "import_black":
				$filepath = we_base_request::_(we_base_request::FILE, "csv_file");
				$delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter");
				$col = we_base_request::_(we_base_request::INT, "csv_col", 0);

				if($col){
					$col--;
				}

				if(strpos($filepath, '..') !== false){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
				} else {
					$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, "rb");
					if($fh){
						while(($dat = fgetcsv($fh, 1000, $delimiter))){
							$_alldat = implode("", $dat);
							if(str_replace(" ", "", $_alldat) === ''){
								continue;
							}
							$row[] = $dat[$col];
						}

						fclose($fh);

						if(!empty($row)){
							if($this->View->settings["black_list"] === ''){
								$this->View->settings["black_list"] = implode(',', $row);
							} else {
								$this->View->settings["black_list"].="," . implode(',', $row);
							}
						}
					} else {
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
					}
				}
				break;
			case "export_black":
				$fname = rtrim(we_base_request::_(we_base_request::FILE, "csv_dir", ''), '/') . '/blacklist_export_' . time() . '.csv';
				we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $fname, str_replace(",", "\n", $this->View->settings["black_list"]));

				$js.=we_html_element::jsScript(JS_DIR . "windows.js") .
					we_html_element::jsElement('new jsWindow("' . $this->frameset . '?pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);');
				break;
		}


		$arr = makeArrayFromCSV($this->View->settings["black_list"]);


		$buttons_table = new we_html_table(array('class' => 'default withSpace'), 4, 1);
		$buttons_table->setCol(0, 0, array(), we_html_button::create_button(we_html_button::ADD, "javascript:addBlack();"));
		$buttons_table->setCol(1, 0, array(), we_html_button::create_button(we_html_button::EDIT, "javascript:editBlack();"));
		$buttons_table->setCol(2, 0, array(), we_html_button::create_button(we_html_button::DELETE, "javascript:deleteBlack()"));
		$buttons_table->setCol(3, 0, array(), we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:deleteallBlack()"));

		$table = new we_html_table(array('class' => 'default'), 5, 3);
		$table->setCol(0, 0, array('style' => 'vertical-align:middle;'), we_html_tools::htmlSelect("blacklist_sel", $arr, 10, "", false, array('style' => "width:388px"), "value", 600));
		$table->setCol(0, 1, array('style' => 'vertical-align:top;padding-left:15px;'), $buttons_table->getHtml());

		$importbut = we_html_button::create_button("import", "javascript:set_import(1)");
		$exportbut = we_html_button::create_button("export", "javascript:set_export(1)");

		$table->setCol(2, 0, array("colspan" => 3, 'style' => 'padding-top:10px;'), $importbut . $exportbut);

		$sib = we_base_request::_(we_base_request::RAW, "sib", 0);
		$seb = we_base_request::_(we_base_request::RAW, "seb", 0);

		if($sib){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:document.we_form.sib.value=0;we_cmd('import_black');");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:set_import(0);");

			$import_options = new we_html_table(array('class' => 'default withSpace'), 2, 2);

			$import_options->setCol(0, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, array(), we_html_tools::htmlTextInput("csv_delimiter", 1, ","));
			$import_options->setCol(1, 0, array("class" => "defaultfont"), g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(1, 1, array(), we_html_tools::htmlTextInput("csv_col", 2, 1));

			$import_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'padding-top:10px;'), 4, 1);
			$import_box->setCol(0, 0, array(), $this->formFileChooser(200, "csv_file", "/", ""));
			$import_box->setCol(1, 0, array(), we_html_button::create_button(we_html_button::UPLOAD, "javascript:we_cmd('upload_black')"));
			$import_box->setCol(2, 0, array(), $import_options->getHtml());
			$import_box->setCol(3, 0, array("nowrap" => null), $ok . $cancel);

			$table->setCol(3, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_import" => 1)) .
				$import_box->getHtml()
			);
		} elseif($seb){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:document.we_form.seb.value=0;we_cmd('export_black');");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:set_export(0);");

			$export_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'padding-top:10px;'), 2, 1);
			$export_box->setCol(0, 0, array(), $this->formFileChooser(200, "csv_dir", "/", "", "folder"));
			$export_box->setCol(1, 0, array("nowrap" => null), $ok . $cancel);

			$table->setCol(3, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_export" => 1)) .
				$export_box->getHtml()
			);
		}


		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_black')");


		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "onsubmit" => "save();return false;"), $this->View->getHiddens() .
					we_html_element::htmlHiddens(array("black_list" => $this->View->settings["black_list"],
						"sib" => $sib,
						"seb" => $seb)) .
					we_html_tools::htmlDialogLayout(
						$table->getHtml(), g_l('modules_newsletter', '[black_list]'), we_html_button::position_yes_no_cancel($save, null, $cancel)
					)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLUploadCsv($what){
		$weFileupload = new we_fileupload_include('we_File', '', '', 'we_form', 'upload_footer', true, "we_cmd('do_" . $what . "');", '', 330, true, false, 0);
		$weFileupload->setExternalProgress(true, 'progressbar', true, 120);
		$weFileupload->setAction($this->frameset . '?' . ($what === 'upload_csv' ? 'pnt=upload_csv&grp=0&ncmd=do_upload_csv' :
				($what === 'upload_black' ? 'pnt=upload_black&grp=undefined&ncmd=do_upload_black' : '')));

		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:" . $weFileupload->getJsBtnCmd('cancel'));
		$upload = we_html_button::create_button(we_html_button::UPLOAD, "javascript:" . $weFileupload->getJsBtnCmd('upload'), true, we_html_button::WIDTH, we_html_button::HEIGHT, '', '', false, false, '_footer');

		$buttons = $cancel . $upload;
		$footerTable = new we_html_table(array('class' => 'default', 'style' => 'width:100%;'), 1, 2);
		$footerTable->setCol(0, 0, $attribs = array(), we_html_element::htmlDiv(array('id' => 'progressbar', 'style' => 'display:none;padding-left:10px')));
		$footerTable->setCol(0, 1, $attribs = array('style' => 'text-align:right'), $buttons);

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
					self.focus();
		') . $weFileupload->getJs();

		$table = new we_html_table(array('class' => 'default withBigSpace'), 2, 1);
		$table->setCol(0, 0, array("style" => "padding-right:30px"), $weFileupload->getHtmlAlertBoxes());
		//$table->setCol(2, 0, array('style'=>'vertical-align:middle"), we_html_element::htmlInput(array('name' => 'we_File', 'TYPE' => 'file', 'size' => 35)));
		$table->setCol(1, 0, array('style' => 'vertical-align:middle;'), $weFileupload->getHTML());

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "enctype" => "multipart/form-data"), we_html_element::htmlCenter(
						$this->View->getHiddens() .
						(($grp = we_base_request::_(we_base_request::STRING, 'grp')) !== false ? we_html_element::htmlHiddens(array("group" => $grp)) : '') .
						we_html_element::htmlHiddens(array("MAX_FILE_SIZE" => $weFileupload->getMaxUploadSize())) .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[csv_upload]'), $footerTable->getHTML(), "100%", 30, "", "hidden")
					)
				)
		);

		return $this->getHTMLDocument($body, $weFileupload->getCSS() . $js);
	}

	private function getHTMLExportCsvMessage($allowClear = false){
		$link = we_base_request::_(we_base_request::URL, 'lnk');
		if($link === false){
			return;
		}

		$table = new we_html_table(array('class' => 'default'), 3, 1);

		$table->setCol(0, 0, array("class" => "defaultfont", 'style' => 'padding-top:5px;'), sprintf(g_l('modules_newsletter', '[csv_export]'), $link));
		$table->setCol(1, 0, array("class" => "defaultfont", 'style' => 'padding-top:5px;'), we_backup_wizard::getDownloadLinkText());
		$table->setCol(2, 0, array("class" => "defaultfont", 'style' => 'padding:5px 0px;'), we_html_element::htmlA(array("href" => getServerUrl(true) . $link, 'download' => basename($link)), g_l('modules_newsletter', '[csv_download]')));

		if($allowClear){
			$table->setCol(3, 0, array("class" => "defaultfont", 'style' => 'padding:5px 0px;'), we_html_element::htmlB(g_l('modules_newsletter', '[clearlog_note]')));
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:clearLog();");
		} else {
			$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		}

		$body = we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHidden("group", '') .
					($allowClear ?
						we_html_element::htmlHiddens(array("pnt" => "clear_log", "ncmd" => "do_clear_log")) .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[clear_log]'), we_html_button::position_yes_no_cancel($ok, null, $cancel), "100%", 30, "", "hidden") :
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[csv_download]'), we_html_button::position_yes_no_cancel(null, $close, null), "100%", 30, "", "hidden")
					) .
					we_html_element::jsElement("self.focus();")
				)
		);

		return ($allowClear ? $body : $this->getHTMLDocument($body));
	}

	/**
	 * Edit csv mail list
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @param String $open_file
	 * @return String
	 */
	function getHTMLEditFile($open_file = ""){
		$out = "";
		$content = array();

		$order = we_base_request::_(we_base_request::RAW, "order", "");
		for($i = 0; $i < 14; $i = $i + 2){
			$sorter_code[$i] = "<br/>" . ($order == $i ?
					we_html_element::htmlInput(array("type" => "radio", "value" => $i, "name" => "order", "checked" => true, "onclick" => "submitForm('edit_file')")) . "&darr;" :
					we_html_element::htmlInput(array("type" => "radio", "value" => $i, "name" => "order", "onclick" => "submitForm('edit_file')")) . "&darr;"
				);
			$sorter_code[$i + 1] = ($order == $i + 1 ?
					we_html_element::htmlInput(array("type" => "radio", "value" => $i + 1, "name" => "order", "checked" => true, "onclick" => "submitForm('edit_file')")) . "&uarr;" :
					we_html_element::htmlInput(array("type" => "radio", "value" => $i + 1, "name" => "order", "onclick" => "submitForm('edit_file')")) . "&uarr;"
				);
		}

		$headlines = array(
			array('dat' => 'ID' . $sorter_code[0] . $sorter_code[1], "width" => 20),
			array('dat' => g_l('modules_newsletter', '[email]') . $sorter_code[2] . $sorter_code[3], "width" => 50),
			array('dat' => g_l('modules_newsletter', '[edit_htmlmail]') . $sorter_code[4] . $sorter_code[5], "width" => 50),
			array('dat' => g_l('modules_newsletter', '[salutation]') . $sorter_code[6] . $sorter_code[7]),
			array('dat' => g_l('modules_newsletter', '[title]') . $sorter_code[8] . $sorter_code[9]),
			array('dat' => g_l('modules_newsletter', '[firstname]') . $sorter_code[10] . $sorter_code[11]),
			array('dat' => g_l('modules_newsletter', '[lastname]') . $sorter_code[12] . $sorter_code[13]),
			array('dat' => g_l('modules_newsletter', '[edit]')),
			array('dat' => g_l('modules_newsletter', '[status]')),
		);


		$csv_file = we_base_request::_(we_base_request::FILE, 'csv_file', '');
		$emails = array();
		$emailkey = array();
		if(strpos($csv_file, '..') === false){
			if($csv_file){
				$emails = we_newsletter_newsletter::getEmailsFromExtern2($csv_file, null, null, array(), we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), $emailkey);
			}
		} else {
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR));
		}

		$offset = max(we_base_request::_(we_base_request::INT, "offset", 0), 0);
		$numRows = we_base_request::_(we_base_request::INT, "numRows", 15);
		$anz = count($emails);
		$endRow = min($offset + $numRows, $anz);

		function cmp0($a, $b){
			return strnatcasecmp($a[0], $b[0]);
		}

		function cmp1($a, $b){
			return strnatcasecmp($a[1], $b[1]);
		}

		function cmp2($a, $b){
			return strnatcasecmp($a[2], $b[2]);
		}

		function cmp3($a, $b){
			return strnatcasecmp($a[3], $b[3]);
		}

		function cmp4($a, $b){
			return strnatcasecmp($a[4], $b[4]);
		}

		function cmp5($a, $b){
			return strnatcasecmp($a[5], $b[5]);
		}

		switch($order){
			case 2:
			case 3:
				uasort($emails, "cmp0");
				break;
			case 4:
			case 5:
				uasort($emails, "cmp1");
				break;
			case 6:
			case 7:
				uasort($emails, "cmp2");
				break;
			case 8:
			case 9:
				uasort($emails, "cmp3");
				break;
			case 10:
			case 11:
				uasort($emails, "cmp4");
				break;
			case 12:
			case 13:
				uasort($emails, "cmp5");
				break;
		}

		switch($order){
			case 0:
			case 2:
			case 4:
			case 6:
			case 8:
			case 10:
			case 12:
				$emails = array_reverse($emails, true);
			default:
				;
		}
		$counter = 0;
		foreach($emails as $k => $cols){
			if($k >= $offset && $k < $endRow){

				$edit = we_html_button::create_button(we_html_button::EDIT, "javascript:editEmailFile(" . $emailkey[$k] . ",'" . $cols[0] . "','" . $cols[1] . "','" . $cols[2] . "','" . $cols[3] . "','" . $cols[4] . "','" . $cols[5] . "')");
				$trash = we_html_button::create_button(we_html_button::TRASH, "javascript:delEmailFile(" . $emailkey[$k] . ",'" . $cols[0] . "')");

				$content[$counter] = array(
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), $k),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[0] ? : "&nbsp;")),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), g_l('modules_newsletter', ($cols[1] ? '[yes]' : '[no]'))),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[2] ? : "&nbsp;")),
						"height" => "",
						"align" => "right",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[3] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[4] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), ($cols[5] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), $edit . $trash),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array("class" => "middlefont"), we_html_element::htmlImg(array("src" => IMAGE_DIR . "icons/" . (we_check_email($cols[0]) ? "valid.gif" : "invalid.gif")))),
						"height" => "",
						"align" => "center",
					)
				);
				$counter++;
			}
		}

		$js = $this->View->getJSProperty() .
			we_html_element::jsElement('
self.focus();
function editEmailFile(eid,email,htmlmail,salutation,title,firstname,lastname){
	new jsWindow("' . $this->frameset . '?pnt=eemail&eid="+eid+"&etyp=2&email="+email+"&htmlmail="+htmlmail+"&salutation="+salutation+"&title="+title+"&firstname="+firstname+"&lastname="+lastname,"edit_email",-1,-1,430,270,true,true,true,true);
}

function setAndSave(eid,email,htmlmail,salutation,title,firstname,lastname){

	var fr=document.we_form;
	fr.nrid.value=eid;
	fr.email.value=email;
	fr.htmlmail.value=htmlmail;
	fr.salutation.value=salutation;
	fr.title.value=title;
	fr.firstname.value=firstname;
	fr.lastname.value=lastname;

	fr.ncmd.value="save_email_file";

	submitForm("edit_file");

}

function listFile(){
	var fr=document.we_form;
	fr.nrid.value="";
	fr.email.value="";
	fr.htmlmail.value="";
	fr.salutation.value="";
	fr.title.value="";
	fr.firstname.value="";
	fr.lastname.value="";
	fr.offset.value=0;

	submitForm("edit_file");
}

function delEmailFile(eid,email){
	var fr=document.we_form;
	if(confirm(sprintf("' . g_l('modules_newsletter', '[del_email_file]') . '",email))){
		fr.nrid.value=eid;
		fr.ncmd.value="delete_email_file";
		submitForm("edit_file");
	}
}

function postSelectorSelect(wePssCmd) {
	switch(wePssCmd) {
		case "selectFile":
			listFile();
			break;
	}
}
');


		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()");
		$edit = we_html_button::create_button(we_html_button::SAVE, "javascript:listFile()");


		$nextprev = new we_html_table(array('class' => 'default'), 1, 4);

		$colcontent = ($offset ?
				we_html_button::create_button(we_html_button::BACK, "javascript:document.we_form.offset.value=" . ($offset - $numRows) . ";submitForm('edit_file');") :
				we_html_button::create_button(we_html_button::BACK, "#", false, 100, 22, "", "", true));

		$nextprev->setCol(0, 0, array('style' => 'padding-right:10px;'), $colcontent);


		if(($anz - $offset) < $numRows){
			$colcontent = ( $anz ? $offset + 1 : 0 ) . "-" . $anz .
				we_html_tools::getPixel(5, 1) .
				g_l('global', '[from]') .
				we_html_tools::getPixel(5, 1) .
				$anz;
		} else {
			$colcontent = ( $anz ? $offset + 1 : 0 ) . "-" . $offset + $numRows .
				we_html_tools::getPixel(5, 1) .
				g_l('global', '[from]') .
				we_html_tools::getPixel(5, 1) .
				$anz;
		}

		$nextprev->setCol(0, 2, array("class" => "defaultfont", 'style' => 'padding-right:10px;'), we_html_element::htmlB($colcontent));


		$colcontent = (($offset + $numRows) < $anz ?
				we_html_button::create_button(we_html_button::NEXT, "javascript:document.we_form.offset.value=" . ($offset + $numRows) . ";submitForm('edit_file');") :
				we_html_button::create_button(we_html_button::NEXT, "#", false, 100, 22, "", "", true)
			);

		$nextprev->setCol(0, 3, array(), $colcontent);

		if(!empty($emails)){
			$add = we_html_button::create_button(we_html_button::PLUS, "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
			$end = $nextprev->getHtml();

			$nextprev->addCol(3);

			$nextprev->setCol(0, 5, array("class" => "defaultfont", 'style' => 'padding-left:20px;'), we_html_element::htmlB(g_l('modules_newsletter', '[show]')) . " " . we_html_tools::htmlTextInput("numRows", 5, $numRows)
			);
			$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", array("onchange" => 'listFile();'), "value", 150);
			$nextprev->setCol(0, 6, array("class" => "defaultfont", 'style' => 'padding-left:20px;'), $selectStatus);
			$nextprev->setCol(0, 7, array("class" => "defaultfont", 'style' => 'padding-left:20px;'), $add
			);

			$out = $nextprev->getHtml() .
				we_html_tools::getPixel(5, 5) .
				we_html_tools::htmlDialogBorder3(850, 300, $content, $headlines) .
				we_html_tools::getPixel(5, 5) .
				$end;
		} else {
			if(!$csv_file && empty($csv_file) && strlen($csv_file) < 4){
				$_nlMessage = g_l('modules_newsletter', '[no_file_selected]');
				$selectStatus2 = '';
			} else {
				if(we_base_request::_(we_base_request::INT, 'weEmailStatus') == 1){
					$_nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = "<br/>" . we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", array("onchange" => 'listFile();'), "value", 150);
				} else {
					$_nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = '';
				}
			}

			$out = we_html_element::htmlDiv(array("class" => "middlefontgray", 'style' => "text-align:center;padding-bottom:2em;"), "--&nbsp;" . $_nlMessage . "&nbsp;--" . $selectStatus2) .
				we_html_button::create_button(we_html_button::PLUS, "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
		}


		$body = we_html_element::htmlBody(array("class" => "weDialogBody", "onload" => ($open_file ? "submitForm('edit_file')" : "" )), we_html_element::htmlForm(array("name" => "we_form"), we_html_element::htmlHiddens(array(
						"ncmd" => "edit_file",
						"pnt" => "edit_file",
						"order" => $order,
						"offset" => $offset,
						"nrid" => "",
						"email" => "",
						"htmlmail" => "",
						"salutation" => "",
						"title" => "",
						"firstname" => "",
						"lastname" => "",
						"etyp" => "",
						"eid" => "")) .
					//we_button::create_button_table(array($close,$edit)).

					we_html_tools::htmlDialogLayout(
						we_html_element::htmlDiv(array('style' => 'margin-top:10px;'), $this->formFileChooser(420, "csv_file", ($open_file ? : ($csv_file ? : "/")), "", "", 'readonly="readonly" onchange="alert(100)"'))
						. '<br/>' . $out, g_l('modules_newsletter', '[select_file]'), $close . $edit, "100%", 30, 597)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLClearLog(){
		we_html_tools::protect();

		if(we_base_request::_(we_base_request::STRING, "ncmd") === "do_clear_log"){
			$this->View->db->query('TRUNCATE TABLE ' . NEWSLETTER_LOG_TABLE);
			return
				we_html_element::jsScript(JS_DIR . "we_showMessage.js") .
				we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('modules_newsletter', '[log_is_clear]'), we_message_reporting::WE_MESSAGE_NOTICE)
					. 'self.close();'
			);
		}

		$js = we_html_element::jsElement('
function clearLog(){
		var f = self.document.we_form;
		f.action = "' . $this->frameset . '";
		f.method = "post";
		f.submit();
}
');

		$csv = '';
		$this->View->db->query('SELECT ' . NEWSLETTER_TABLE . '.Text as NewsletterName, ' . NEWSLETTER_LOG_TABLE . '.* FROM ' . NEWSLETTER_TABLE . ' JOIN ' . NEWSLETTER_LOG_TABLE . ' ON ' . NEWSLETTER_TABLE . '.ID=' . NEWSLETTER_LOG_TABLE . '.NewsletterID');
		while($this->View->db->next_record()){
			$csv.=$this->View->db->f('NewsletterName') . "," . date(g_l('weEditorInfo', '[date_format]'), intval($this->View->db->f('LogTime'))) . ',' . (g_l('modules_newsletter', '[' . $this->View->db->f('Log') . ']') !== false ? (sprintf(g_l('modules_newsletter', '[' . $this->View->db->f('Log') . ']'), $this->View->db->f("Param"))) : $this->View->db->f('Log')) . "\n";
		}

		$link = BACKUP_DIR . 'download/log_' . time() . '.csv';
		if(!we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $link, $csv)){
			$link = '';
		}

		$_REQUEST['lnk'] = $link;

		return $this->getHTMLDocument($this->getHTMLExportCsvMessage(true), $js);
	}

	function getHTMLSendWait(){
		$nid = we_base_request::_(we_base_request::INT, 'nid', 0);
		$test = we_base_request::_(we_base_request::BOOL, 'test');


		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => 'weDialogBody', 'onload' => "self.focus();setTimeout(document.we_form.submit,200)"), we_html_element::htmlForm(array('name' => 'we_form'), we_html_element::htmlHiddens(array(
							"pnt" => "send_frameset",
							'nid' => $nid,
							'test' => $test)) .
						we_html_element::htmlCenter('<i class="fa fa-2x fa-spinner fa-pulse"></i>' .
							we_html_element::htmlBr() .
							we_html_element::htmlBr() .
							we_html_element::htmlDiv(array('class' => 'header_small'), g_l('modules_newsletter', '[prepare_newsletter]'))
						)
					)
				)
		);
	}

	function getHTMLSendFrameset(){
		$nid = we_base_request::_(we_base_request::INT, "nid", 0);

		$test = we_base_request::_(we_base_request::BOOL, "test");

		$this->View->newsletter = new we_newsletter_newsletter($nid);
		$ret = $this->View->cacheNewsletter();


		$_offset = ($this->View->newsletter->Offset) ? ($this->View->newsletter->Offset + 1) : 0;
		$_step = $this->View->newsletter->Step;

		if($this->View->settings['send_step'] <= $_offset){
			$_step++;
			$_offset = 0;
		}


		$head = we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsElement('
function yes(){
	doSend(' . $_offset . ',' . $_step . ');
}

function no(){
	doSend(0,0);
}
function cancel(){
	self.close();
}

function ask(start,group){
	new jsWindow("' . $this->View->frameset . '?pnt=qsend&start="+start+"&grp="+group,"send_question",-1,-1,400,200,true,true,true,false);
}

function doSend(start,group){
	self.focus();
	top.send_cmd.location="' . $this->frameset . '?pnt=send_cmd&nid=' . $nid . '&test=' . $test . '&blockcache=' . $ret["blockcache"] . '&emailcache=' . $ret["emailcache"] . '&ecount=' . $ret["ecount"] . '&gcount=' . $ret["gcount"] . '&start="+start+"&egc="+group;
}
self.focus();
');

		$body = we_html_element::htmlIFrame('send_body', $this->frameset . '?we_transaction=' . $this->transaction . '&pnt=msg_fv_headers', 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;', '', '', false) .
			we_html_element::htmlIFrame('send_cmd', $this->frameset . "?pnt=send_cmd", 'position:absolute;width:0px;height:0px;', '', '', false) .
			we_html_element::htmlIFrame('send_control', $this->frameset . "?pnt=send_control&nid=$nid&test=$test&blockcache=" . $ret["blockcache"] . "&emailcache=" . $ret["emailcache"] . "&ecount=" . $ret["ecount"] . "&gcount=" . $ret["gcount"], 'position:absolute;width:0px;height:0px;', '', '', false)
		;
		return $this->getHTMLDocument(we_html_element::htmlBody(array("onload" => (($this->View->newsletter->Step != 0 || $this->View->newsletter->Offset != 0) ? "ask(" . $this->View->newsletter->Step . "," . $this->View->newsletter->Offset . ");" : "no();")), $body), $head);
	}

	function getHTMLSendBody(){
		$pb = new we_progressBar(we_base_request::_(we_base_request::INT, "pro", 0));
		$pb->setStudLen(400);
		$pb->addText(g_l('modules_newsletter', '[sending]'), 0, "title");

		$_footer = '<table width="580" class="default"><tr><td style="text-align:left">' .
			$pb->getHTML() . '</td><td style="text-align:right">' .
			we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();") .
			'</td></tr></table>';

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array("class" => "weDialogBody"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), $pb->getJS('', true) .
						we_html_tools::htmlDialogLayout(we_html_element::htmlTextarea(array("name" => "details", "cols" => 60, "rows" => 15, "style" => "width:530px;height:300px;")), g_l('modules_newsletter', '[details]'), $_footer)
					) .
					we_html_element::jsElement('
									document.we_form.details.value="' . g_l('modules_newsletter', (we_base_request::_(we_base_request::BOOL, "test") ? '[test_no_mail]' : '[sending]')) . '";
									document.we_form.details.value=document.we_form.details.value+"\n"+"' . g_l('modules_newsletter', '[campaign_starts]') . '";
							')
		));
	}

	function getHTMLSendCmd(){
		$nid = we_base_request::_(we_base_request::INT, 'nid');
		if($nid === false){
			return;
		}

		$test = we_base_request::_(we_base_request::BOOL, "test");
		$start = we_base_request::_(we_base_request::INT, "start", 0);

		// to calc progress ------------------
		// total number of emails
		$ecount = we_base_request::_(we_base_request::INT, "ecount", 0);
		// counter
		$ecs = we_base_request::_(we_base_request::INT, "ecs", 0);
		//-----------------------------------

		$blockcache = we_base_request::_(we_base_request::RAW, "blockcache", 0);

		// emails cache -----------------------
		$emailcache = we_base_request::_(we_base_request::RAW, "emailcache", 0);
		//
		$egc = we_base_request::_(we_base_request::INT, "egc", 0);
		//
		$gcount = we_base_request::_(we_base_request::INT, "gcount", 0);
		//-----------------------------------

		$reload = we_base_request::_(we_base_request::BOOL, "reload");
		$retry = we_base_request::_(we_base_request::BOOL, "retry");


		$this->View->newsletter = new we_newsletter_newsletter($nid);
		if($retry){
			$egc = $this->View->newsletter->Step;
			$start = $this->View->newsletter->Offset;
			if($start){
				$start++;
			}
			$this->View->newsletter->addLog("retry");
			echo "RETRY $nid: $egc-$ecs<br/>";
			flush();
		}


		$js = we_html_element::jsElement('
function updateText(text){
	top.send_body.document.we_form.details.value=top.send_body.document.we_form.details.value+"\n"+text;
}

function checkTimeout(){
	return document.we_form.ecs.value;
}

function initControl(){
	if(top.send_control.init) top.send_control.init();
}

self.focus();');


		echo $this->getHTMLDocument(we_html_element::htmlBody(array("marginwidth" => 10, "marginheight" => 10, "leftmargin" => 10, "topmargin" => 10, "onload" => "initControl()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post"), we_html_element::htmlHiddens(array(
						"nid" => $nid,
						"pnt" => "send_cmd",
						"test" => $test,
						"blockcache" => $blockcache,
						"emailcache" => $emailcache,
						"ecount", "value" => $ecount,
						"gcount" => $gcount,
						"egc" => $egc + 1,
						"ecs" => $ecs,
						"reload" => 1))
				)
			), $js);
		flush();

		if($gcount <= $egc){
			$cc = 0;
			while(true){
				if(file_exists(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_p_" . $cc)){
					we_base_file::delete(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_p_" . $cc);
				} else {
					break;
				}
				if(file_exists(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc)){
					$_buffer = we_unserialize(we_base_file::load(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc));
					if(is_array($_buffer) && isset($_buffer['inlines'])){
						foreach($_buffer['inlines'] as $_fn){
							if(file_exists($_fn)){
								we_base_file::delete($_fn);
							}
						}
					}
					we_base_file::delete(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc);
				} else {
					break;
				}
				$cc++;
			}
			echo we_html_element::jsElement('
				top.send_control.location="about:blank";
				top.send_body.setProgress(100);
				top.send_body.setProgressText("title","<span style=\"color:#006699;text-weight:bold;\">' . g_l('modules_newsletter', '[finished]') . '",2);
				updateText("' . g_l('modules_newsletter', '[campaign_ends]') . '");
			');
			$this->View->db->query("UPDATE " . NEWSLETTER_TABLE . " SET Step=0,Offset=0 WHERE ID=" . $this->View->newsletter->ID);
			if(!$test){
				$this->View->newsletter->addLog("log_end_send");
			}
			return;
		}

		if($start && !$test && !$reload){
			$this->View->newsletter->addLog("log_continue_send");
		} else if(!$test && !$reload){
			$this->View->newsletter->addLog("log_start_send");
		}

		$content = "";

		$emails = $this->View->getFromCache($emailcache . "_" . $egc);
		$end = count($emails);

		for($j = $start; $j < $end; $j++){
			$email = trim($emails[$j][0]);

			$user_groups = explode(",", $emails[$j][6]);
			$user_blocks = $emails[$j][7];

			sort($user_blocks);
			$user_blocks = array_unique($user_blocks);

			$htmlmail = isset($emails[$j][1]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][1])) : "";
			$salutation = isset($emails[$j][2]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][2])) : "";
			$title = isset($emails[$j][3]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][3])) : "";
			$firstname = isset($emails[$j][4]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][4])) : "";
			$lastname = isset($emails[$j][5]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][5])) : "";
			$customerid = isset($emails[$j][8]) ? str_replace("\r", "", str_replace("\n", "", $emails[$j][8])) : "";
			$iscustomer = (isset($emails[$j][9]) && $emails[$j][9] === 'customer' ? 'C' : '');

			$contentDefault = $content_plainDefault = $contentF = $contentF_plain = $contentM = $contentM_plain = $contentTFL = $contentTFL_plain = $contentTL = $contentTL_plain = $contentFL = $contentFL_plain = $contentLN = $contentLN_plain = $contentFN = $contentFN_plain = '';

			$inlines = $atts = array();

			foreach($user_groups as $user_group){
				$atts = array_merge($atts, $this->View->getAttachments($user_group));
			}

			foreach($user_blocks as $user_block){

				$html_block = $this->View->getFromCache($blockcache . "_h_" . $user_block);
				$plain_block = $this->View->getFromCache($blockcache . "_p_" . $user_block);

				$contentDefault.=$html_block["default" . $iscustomer];
				$content_plainDefault.=$plain_block["default" . $iscustomer];

				$contentF.=$html_block["female" . $iscustomer];
				$contentF_plain.=$plain_block["female" . $iscustomer];

				$contentM.=$html_block["male" . $iscustomer];
				$contentM_plain.=$plain_block["male" . $iscustomer];

				$contentTFL.=$html_block["title_firstname_lastname" . $iscustomer];
				$contentTFL_plain.=$plain_block["title_firstname_lastname" . $iscustomer];

				$contentTL.=$html_block["title_lastname" . $iscustomer];
				$contentTL_plain.=$plain_block["title_lastname" . $iscustomer];

				$contentFL.=$html_block["firstname_lastname" . $iscustomer];
				$contentFL_plain.=$plain_block["firstname_lastname" . $iscustomer];

				$contentLN.=$html_block["lastname" . $iscustomer];
				$contentLN_plain.=$plain_block["lastname" . $iscustomer];

				$contentFN.=$html_block["firstname" . $iscustomer];
				$contentFN_plain.=$plain_block["firstname" . $iscustomer];

				foreach($html_block["inlines"] as $k => $v){
					if(!in_array($k, array_keys($inlines))){
						$inlines[$k] = $v;
					}
				}
			}

			if($salutation && $lastname && ($salutation == $this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD]) && ((!$this->View->settings["title_or_salutation"]) || (!$title))){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title,);
				$content = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentF) : $contentF), $rep);
				$content_plain = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentF_plain) : $contentF_plain), $rep);
			} else if($salutation && $lastname && ($salutation == $this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD]) && ((!$this->View->settings["title_or_salutation"]) || (!$title))){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);

				$content = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentM) : $contentM), $rep);
				$content_plain = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentM_plain) : $contentM_plain), $rep);
			} else if($title && $firstname && $lastname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);
				$content = strtr(preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentTFL), $rep);
				$content_plain = strtr(preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentTFL_plain), $rep);
			} else if($title && $lastname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);
				$content = strtr(preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentTL), $rep);
				$content_plain = strtr(preg_replace('|([^ ])###TITLE###|', '$1 ' . $title, $contentTL_plain), $rep);
			} else if($lastname && $firstname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
				);

				$content = strtr($contentFL, $rep);
				$content_plain = strtr($contentFL_plain, $rep);
			} else if($firstname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###CUSTOMERID###' => $customerid
				);
				$content = strtr($contentFN, $rep);
				$content_plain = strtr($contentFN_plain, $rep);
			} else if($lastname){
				$rep = array(
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid
				);

				$content = strtr($contentLN, $rep);
				$content_plain = strtr($contentLN_plain, $rep);
			} else {

				$content = $contentDefault;
				$content_plain = $content_plainDefault;
			}


			$content_plain = str_replace(we_newsletter_base::EMAIL_REPLACE_TEXT, $email, $content_plain);
			$content = str_replace(we_newsletter_base::EMAIL_REPLACE_TEXT, $email, $content);

			// damd: Newsletter Platzhalter ersetzten
			$this->replacePlaceholder($content, $content_plain, $emails[$j]);

			//$_clean = $this->View->getCleanMail($this->View->newsletter->Reply);

			$not_black = !$this->View->isBlack($email); //Bug #5791 Prüfung muss vor der aufbereitung der Adresse erfolgen
			if($lastname && $firstname || $title && $lastname){
				$emailName = ($title ? $title . ' ' : '') .
					($firstname ? $firstname . ' ' : '') .
					$lastname . '<' . $email . '>';
				//$email = $emailName;
			} else {
				$emailName = $email;
			}
			$phpmail = new we_util_Mailer($emailName, $this->View->newsletter->Subject, $this->View->newsletter->Sender, $this->View->newsletter->Reply, $this->View->newsletter->isEmbedImages);
			$phpmail->setCharSet($this->View->newsletter->Charset ? : $GLOBALS["_language"]["charset"]);

			if($htmlmail){
				$phpmail->addHTMLPart($content);
				$phpmail->addTextPart(trim($content_plain));
			} else {
				$phpmail->addTextPart(trim($content_plain));
			}

			if(!$this->View->settings["use_base_href"]){
				$phpmail->setIsUseBaseHref($this->View->settings["use_base_href"]);
			}

			foreach($atts as $att){
				$phpmail->doaddAttachment($att);
			}

			$domain = '';
			$not_malformed = ($this->View->settings["reject_malformed"]) ? $this->View->newsletter->check_email($email) : true;
			$verified = ($this->View->settings["reject_not_verified"]) ? $this->View->newsletter->check_domain($email, $domain) : true;

			if($verified && $not_malformed && $not_black){
				if(!$test){
					$phpmail->buildMessage();
					if($phpmail->Send()){
						if($this->View->settings["log_sending"]){
							$this->View->newsletter->addLog("mail_sent", $email);
						}
					} else {
						if($this->View->settings["log_sending"]){
							$this->View->newsletter->addLog("mail_failed", $email);
						}
						echo we_html_element::jsElement('updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[error]') . ": " . g_l('modules_newsletter', '[mail_failed]'), $email)) . '");');
						flush();
					}
					$this->View->db->query('UPDATE ' . NEWSLETTER_TABLE . ' SET Step=' . intval($egc) . ',Offset=' . intval($j) . ' WHERE ID=' . $this->View->newsletter->ID);
				}
			} elseif(!$not_malformed){
				if(!$test && $this->View->settings["log_sending"]){
					$this->View->newsletter->addLog("email_malformed", $email);
				}
				echo we_html_element::jsElement('
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[error]') . ": " . g_l('modules_newsletter', '[email_malformed]'), $email)) . '");
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");');
				flush();
			} elseif(!$verified){
				if(!$test && $this->View->settings["log_sending"]){
					$this->View->newsletter->addLog("domain_nok", $email);
				}
				echo we_html_element::jsElement('
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[warning]') . ": " . g_l('modules_newsletter', '[domain_nok]'), $domain)) . '");
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");');
				flush();
			} elseif(!$not_black){
				if(!$test && $this->View->settings["log_sending"]){
					$this->View->newsletter->addLog("email_is_black", $email);
				}
				echo we_html_element::jsElement('
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[warning]') . ": " . g_l('modules_newsletter', '[email_is_black]'), $email)) . '");
updateText("' . addslashes(sprintf(g_l('modules_newsletter', '[mail_not_sent]'), $email)) . '");
							');
				flush();
			}
			$ecs++;

			echo we_html_element::jsElement('
document.we_form.ecs.value=' . $ecs . ';
top.send_control.document.we_form.ecs.value=' . $ecs . ';');

			$pro = ($ecount ? ($ecs / $ecount) * 100 : 0);

			echo we_html_element::jsElement('top.send_body.setProgress(' . ((int) $pro) . ');');
			flush();
		}

		we_base_file::delete(WE_NEWSLETTER_CACHE_DIR . $emailcache . "_" . $egc);
		//$laststep = ceil(we_base_request::_(we_base_request::INT, "ecount", 0) / $this->View->settings["send_step"]);
		echo we_html_element::jsElement((!empty($this->View->settings["send_wait"]) && is_numeric($this->View->settings["send_wait"]) && $egc > 0 && isset($this->View->settings["send_step"]) && is_numeric($this->View->settings["send_step"]) && $egc < ceil($ecount / $this->View->settings["send_step"]) ?
				'setTimeout(document.we_form.submit,' . $this->View->settings["send_wait"] . ');' :
				'document.we_form.submit();'
		));
		flush();
	}

	function getHTMLSendControl(){
		$nid = we_base_request::_(we_base_request::INT, "nid", 0);
		$gcount = we_base_request::_(we_base_request::INT, "gcount", 0);
		$ecount = we_base_request::_(we_base_request::INT, "ecount", 0);
		$blockcache = we_base_request::_(we_base_request::RAW, "blockcache", 0);
		$ecs = we_base_request::_(we_base_request::RAW, "ecs", 0);
		$emailcache = we_base_request::_(we_base_request::RAW, "emailcache", 0);

		$to = is_numeric($this->View->settings["send_wait"]) ? $this->View->settings["send_wait"] : 0;
		$to += 40000;

		$js = we_html_element::jsElement('
var to=0;
var param=0;

function reinit(){
	top.send_body.document.we_form.details.value=top.send_body.document.we_form.details.value+"\n"+"' . g_l('modules_newsletter', '[retry]') . '...";
	document.we_form.submit();
	startTimeout();
}

function init(){
	document.we_form.ecs.value=top.send_cmd.document.we_form.ecs.value;
	startTimeout();
}

function startTimeout(){
	if(to) stopTimeout();
	to=setTimeout(reload,' . $to . ');
}

function stopTimeout(){
	clearTimeout(to);
}

function reload(){
	chk=document.we_form.ecs.value;
	if(parseInt(chk)>parseInt(param) && parseInt(chk)!=0){
		param=chk;
		startTimeout();
	}
	else{
		reinit();
	}
}

self.focus();');

		$body = we_html_element::htmlBody(array("style" => 'margin:10px', "onload" => "startTimeout()"), we_html_element::htmlForm(array("name" => "we_form", "method" => "post", "target" => "send_cmd", "action" => $this->frameset), we_html_element::htmlHiddens(array(
						"nid" => $nid,
						"pnt" => "send_cmd",
						"retry" => 1,
						"test" => 0,
						"blockcache" => $blockcache,
						"emailcache" => $emailcache,
						"ecount" => $ecount,
						"gcount" => $gcount,
						"ecs" => $ecs,
						"reload" => 0))
				)
		);
		echo $this->getHTMLDocument($body, $js);
		flush();
	}

	/**
	 * returns	a select menu within a html table. to ATTENTION this function is also used in classes object and objectFile !!!!
	 * 			when $withHeadline is true, a table with headline is returned, default is false
	 *
	 * ** @package none
	 * @subpackage Newsletter
	 * @return	select menue to determine charset
	 * @param	boolean
	 */
	function getHTMLCharsetTable(){
		$value = (isset($this->View->newsletter->Charset) ? $this->View->newsletter->Charset : "");

		$charsetHandler = new we_base_charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table(array(), 1, 2);
		$table->setCol(0, 0, null, we_html_tools::htmlTextInput("Charset", 15, $value, '', '', 'text', 100));
		$table->setCol(0, 1, null, we_html_tools::htmlSelect("CharsetSelect", $charsets, 1, $value, false, array("onblur" => "document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;", "onchange" => "document.forms[0].elements[\"Charset\"].value=this.options[this.selectedIndex].value;"), 'value', 'text', (self::def_width - 120), false));

		return $table->getHtml();
	}

	/**
	 * Ersetzt die Newsletter Platzthalter
	 *
	 * @author damd
	 * ** @package none
	 * @subpackage Newsletter
	 * @param String $content
	 * @param String $content_plain
	 * @param Array $customerInfos
	 */
	function replacePlaceholder(&$content, &$content_plain, $customerInfos){
		$placeholderfieldsmatches = array();
		preg_match_all("/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/", $content, $placeholderfieldsmatches);
		$placeholderfields = $placeholderfieldsmatches[1];
		unset($placeholderfieldsmatches);

		$fromCustomer = (is_array($customerInfos) && isset($customerInfos[8]) && isset($customerInfos[9]) && $customerInfos[9] === 'customer' ?
				array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customerInfos[8]), $this->View->db), we_customer_customer::getEncryptedFields()) :
				array());

		foreach($placeholderfields as $phf){
			$placeholderReplaceValue = $fromCustomer && isset($fromCustomer[$phf]) ? $fromCustomer[$phf] : "";
			$content = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $content);
			$content_plain = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $content_plain);
		}
	}

}
