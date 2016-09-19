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
	const TAB_PROPERTIES = 0; //make sure to keep 1
	const TAB_MAILING = 1;
	const TAB_EDIT = 2;
	const TAB_REPORTING = 3;

	private $weAutoCompleter;

	function __construct($frameset){
		parent::__construct($frameset);
		$this->module = 'newsletter';
		$this->View = new we_newsletter_view($frameset);
		$this->Tree = new we_tree_newsletter($this->frameset, 'top.content', 'top.content', 'top.content.cmd');
		$this->weAutoCompleter = &weSuggest::getInstance();
	}

	public function getHTMLDocumentHeader($what = '', $mode = ''){
		switch($what){
			case 'send':
			case 'send_body':
			case 'send_cmd':
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
				return parent::getHTMLDocumentHeader();
		}
	}

	function getHTML($what = '', $mode = 0, $step = 0){
		switch($what){
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
			case 'frameset':
				return $this->getHTMLFrameset($this->Tree->getJSTreeCode());
			default:
				return parent::getHTML($what, $mode, $step);
		}
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
			return parent::getHTMLEditorHeader(0);
		}

		$group = we_base_request::_(we_base_request::BOOL, "group");
		$page = ($group ? self::TAB_PROPERTIES : we_base_request::_(we_base_request::INT, 'page', self::TAB_PROPERTIES));

		$textPre = g_l('modules_newsletter', ($group ? '[group]' : '[newsletter][text]'));
		$textPost = we_base_request::_(we_base_request::STRING, "txt", g_l('modules_newsletter', ($group ? '[new_newsletter_group]' : '[new_newsletter]')));

		$js = '
function setTab(tab) {
	top.content.editor.edbody.we_cmd("switchPage",tab);
}';

		$we_tabs = new we_tabs();

		$we_tabs->addTab(we_base_constants::WE_ICON_PROPERTIES, ($page == self::TAB_PROPERTIES), "self.setTab(" . self::TAB_PROPERTIES . ");", ['title' => g_l('modules_newsletter', '[property]')]);

		if(!$group){
			$we_tabs->addTab('<i class="fa fa-lg fa-list"></i>', ($page == self::TAB_MAILING), "self.setTab(" . self::TAB_MAILING . ");", ['title' => sprintf(g_l('modules_newsletter', '[mailing_list]'), "")]);
			$we_tabs->addTab(we_base_constants::WE_ICON_EDIT, ($page == self::TAB_EDIT), "self.setTab(" . self::TAB_EDIT . ");", ['title' => g_l('modules_newsletter', '[edit]')]);
			//if($this->View->newsletter->ID){ // zusaetzlicher tab fuer auswertung
			$we_tabs->addTab('<i class="fa fa-lg fa-pie-chart"></i>', ($page == self::TAB_REPORTING), "self.setTab(" . self::TAB_REPORTING . ");", ['title' => g_l('modules_newsletter', '[reporting][tab]')]);
			//}
		}

		$body = we_html_element::htmlBody(["onresize" => "weTabs.setFrameSize()", "onload" => "weTabs.setFrameSize()", "id" => "eHeaderBody"], '<div id="main"><div id="headrow"><b>' . oldHtmlspecialchars($textPre) . ':&nbsp;</b><span id="h_path" class="header_small"><b id="titlePath">' . oldHtmlspecialchars($textPost) . '</b></span></div>' .
				$we_tabs->getHTML() .
				'</div>'
		);
		return $this->getHTMLDocument($body, we_tabs::getHeader($js));
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
	protected function getHTMLEditorFooter(array $mode = [0], $extraHead = ''){
		if(we_base_request::_(we_base_request::BOOL, 'home')){
			return parent::getHTMLEditorFooter([]);
		}
		if(empty($mode)){
			$mode = [0];
		}
		$group = we_base_request::_(we_base_request::INT, "group", 0);

		$select = new we_html_select(['name' => 'gview']);

		$table2 = new we_html_table(array('class' => 'default', "width" => 300), 1, 5);
		if($mode[0] == 0){
			$table2->setRow(0, ['style' => 'vertical-align:middle;']);

			$table2->setCol(0, 0, [], ((permissionhandler::hasPerm(['NEW_NEWSLETTER', 'EDIT_NEWSLETTER'])) ?
					we_html_button::create_button(we_html_button::SAVE, "javascript:we_save()") :
					""
				)
			);

			if(!$group){
				$table2->setCol(0, 1, ['style' => 'padding-left:70px;'], $select->getHtml());
				$table2->setCol(0, 2, ['style' => 'padding-left:5px;'], we_html_forms::checkbox(0, false, "htmlmail_check", g_l('modules_newsletter', '[html_preview]'), false, "defaultfont", "if(document.we_form.htmlmail_check.checked) { document.we_form.hm.value=1;top.opener.top.nlHTMLMail=true; } else { document.we_form.hm.value=0;top.opener.top.nlHTMLMail=false; }"));
				$table2->setCol(0, 3, [], we_html_button::create_button(we_html_button::PREVIEW, "javascript:we_cmd('popPreview')"));
				$table2->setCol(0, 4, [], (permissionhandler::hasPerm("SEND_NEWSLETTER") ? we_html_button::create_button('send', "javascript:we_cmd('popSend')") : ""));
			}
		}

		$body = we_html_element::htmlBody(["id" => "footerBody", "onload" => "afterLoad();"], we_html_element::htmlForm([], we_html_element::htmlHidden("hm", 0) .
					$table2->getHtml()
				)
		);

		return $this->getHTMLDocument($body, we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/frames_footer.js'));
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
			$content.=we_html_element::htmlDiv(['class' => 'defaultfont'], $this->View->db->f("LogTime") . '&nbsp;' . ($param ? sprintf($log, $param) : $log));
		}

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('class' => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(array('name' => 'we_form', "method" => "post"), we_html_tools::htmlDialogLayout(
							we_html_element::htmlDiv(array('class' => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;margin:5px 10px 15px 10px;"), $content)
							, g_l('modules_newsletter', '[show_log]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
						)
					)
		));
	}

	/*
	 * Grafische Aufbereitung des Versandlogs im Tab "Auswertung"
	 */

	function getHTMLReporting(){
		$this->View->db->query('SELECT Log,stamp,DATE_FORMAT(stamp,"' . g_l('weEditorInfo', '[mysql_date_format]') . '") AS LogTime FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ' AND Log IN(\'log_start_send\', \'log_end_send\') ORDER BY stamp ASC');

		$newsletterMailOrders = [];
		$newsletterMailOrdersCnt = 0;

		while($this->View->db->next_record()){
			if($this->View->db->f('Log') === 'log_start_send'){
				$newsletterMailOrders[++$newsletterMailOrdersCnt]['start_send'] = $this->View->db->f('stamp');
				$newsletterMailOrders[$newsletterMailOrdersCnt]['startTime'] = $this->View->db->f('LogTime');
			} else {
				$newsletterMailOrders[$newsletterMailOrdersCnt]['end_send'] = $this->View->db->f('stamp');
			}
		}

		$parts = [];

		foreach($newsletterMailOrders as $key => $newsletterMailOrder){
			$table = new we_html_table(array('class' => 'defaultfont', 'style' => 'width: 588px'), 1, 5);
			$this->View->db->query('SELECT Log,COUNT(1) FROM ' . NEWSLETTER_LOG_TABLE . ' WHERE NewsletterID=' . $this->View->newsletter->ID . ' AND Log NOT IN (\'log_start_send\',\'log_end_send\') AND stamp BETWEEN "' . $newsletterMailOrder['start_send'] . '" AND "' . (isset($newsletterMailOrder['end_send']) ? $newsletterMailOrder['end_send'] : 'NOW()') . '" GROUP BY Log');

			$results = $this->View->db->getAllFirst(false);

			$allRecipients = array_sum($results);

			/* process bar blocked by blacklist */
			$allBlockedByBlacklist = (array_key_exists("email_is_black", $results) ? $results['email_is_black'] : 0);
			$percentBlockedByBlacklist = we_base_util::getPercent($allRecipients, $allBlockedByBlacklist, 2);

			$pbByB = new we_progressBar($percentBlockedByBlacklist);
			$pbByB->setName('blacklist' . $key);
			$pbByB->setStudWidth(10);
			$pbByB->setStudLen(150);

			$table->addRow();
			$table->setColContent(1, 0, we_html_element::htmlSpan(array('id' => 'blacklist_' . $key), g_l('modules_newsletter', '[reporting][mailing_emails_are_black]')));
			$table->setColContent(1, 1, $pbByB->getJSCode() . $pbByB->getHTML());
			$table->setCol(1, 2, array("style" => "padding: 0 5px 0 5px;"), we_html_element::htmlSpan(array('id' => 'blacklist_total', 'style' => 'color:' . (($allBlockedByBlacklist > 0) ? 'red' : 'green') . ';'), $allBlockedByBlacklist));
			$table->setCol(1, 3, array("style" => "padding: 0 5px 0 5px;"), '<i class="fa fa-lg ' . ($allBlockedByBlacklist == 0 ? "fa-check fa-ok" : "fa-close fa-cancel") . '"></i>');
			//todo: statt show black list, sollte show_log begrenzt auf Log=email_is_black + $start_send + start_end
			$table->setCol(1, 4, array('style' => 'width: 35px'), (($allBlockedByBlacklist == 0) ? '' : we_html_button::formatButtons(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('black_list');"))));

			/* process bar blocked by domain check */
			$allBlockedByDomainCheck = (array_key_exists("domain_nok", $results) ? $results['domain_nok'] : 0);
			$percentBlockedByDomain = we_base_util::getPercent($allRecipients, $allBlockedByDomainCheck, 2);

			$pbBbD = new we_progressBar($percentBlockedByDomain);
			$pbBbD->setName('domain' . $key);
			$pbBbD->setStudWidth(10);
			$pbBbD->setStudLen(150);

			$table->addRow();
			$table->setColContent(2, 0, we_html_element::htmlSpan(['id' => 'domain_' . $key], g_l('modules_newsletter', '[reporting][mailing_emails_nok]')));
			$table->setColContent(2, 1, $pbBbD->getJSCode() . $pbBbD->getHTML());
			$table->setCol(2, 2, ["style" => "padding: 0 5px 0 5px;"], we_html_element::htmlSpan(['id' => 'domain_total', 'style' => 'color:' . (($allBlockedByDomainCheck > 0) ? 'red' : 'green') . ';'], $allBlockedByDomainCheck));
			$table->setCol(2, 3, ["style" => "padding: 0 5px 0 5px;"], '<i class="fa fa-lg ' . ($allBlockedByDomainCheck == 0 ? "fa-check fa-ok" : "fa-close fa-cancel") . '"></i>');
			//todo: statt domain, sollte show_log begrenzt auf Log=domain_nok + $start_send + start_end
			$table->setCol(2, 4, ['style' => 'width: 35px'], (($allBlockedByDomainCheck == 0) ? '' : we_html_button::formatButtons(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('domain_check');"))));

			/* process bar all clear recipients */
			$allClearRecipients = (array_key_exists("mail_sent", $results) ? $results['mail_sent'] : 0);
			$percentClearRecipients = we_base_util::getPercent($allRecipients, $allClearRecipients, 2);

			$pbCR = new we_progressBar($percentClearRecipients);
			$pbCR->setName('recipients' . $key);
			$pbCR->setStudWidth(10);
			$pbCR->setStudLen(150);

			$table->addRow();
			$table->setColContent(3, 0, we_html_element::htmlSpan(['id' => 'recipients_' . $key], g_l('modules_newsletter', '[reporting][mailing_emails_success]')));
			$table->setColContent(3, 1, $pbCR->getJSCode() . $pbCR->getHTML());
			$table->setCol(3, 2, ["style" => "padding: 0 5px 0 5px;"], we_html_element::htmlSpan(['id' => 'recipients_total', 'style' => 'color:' . (($allClearRecipients <= 0) ? 'red' : 'green') . ';'], $allClearRecipients));
			$table->setCol(3, 3, ["style" => "padding: 0 5px 0 5px;"], '<i class="fa fa-lg ' . ($allClearRecipients == $allRecipients ? "fa-check fa-ok" : "fa-exclamation-triangle fa-cancel") . '" title="' . ($allClearRecipients < $allRecipients ? g_l('modules_newsletter', '[reporting][mailing_advice_not_success]') : '') . '"></i>');
			//todo: statt show_log, sollte show_log begrenzt auf Log=email_sent + $start_send + start_end
			$table->setCol(3, 4, ['style' => 'width: 35px'], we_html_button::formatButtons(we_html_button::create_button(we_html_button::VIEW, "javascript:top.opener.top.we_cmd('show_log')")));

			/* total recipients */
			$table->addRow();
			$table->setColContent(4, 0, we_html_element::htmlB(g_l('modules_newsletter', '[reporting][mailing_all_emails]')));
			$table->setCol(4, 2, ['colspan' => 2, "style" => "padding: 0 5px 0 5px;"], we_html_element::htmlB($allRecipients));

			$parts[] = [
				'headline' => g_l('modules_newsletter', '[reporting][mailing_send_at]') . '&nbsp;' . $newsletterMailOrder['startTime'],
				'html' => $table->getHTML() . we_html_element::htmlBr()
			];
		}

		return $parts? : [ [
				'headline' => g_l('modules_newsletter', '[reporting][mailing_not_done]'),
		]];
	}

	protected function getHTMLCmd(){
		if(($pid = we_base_request::_(we_base_request::INT, 'pid')) === false){
			return $this->getHTMLDocument(we_html_element::htmlBody());
		}

		return $this->getHTMLDocument(we_html_element::htmlBody([], we_html_element::htmlForm(['name' => 'we_form'], we_html_element::htmlHiddens([
							'mod' => 'newsletter',
							"pnt" => "cmd",
							"ncmd" => "",
							"nopt" => ""])
					)
				), we_html_element::jsElement(
					($pid ?
						'' :
						'top.content.treeData.clear();
top.content.treeData.add(top.content.node.prototype.rootEntry(\'' . $pid . '\',\'root\',\'root\'));'
					) . $this->Tree->getJSLoadTree(!$pid, we_tree_newsletter::getItemsFromDB($pid))));
	}

	function getHTMLSendQuestion(){
		$body = we_html_element::htmlBody(['class' => 'weEditorBody', "onblur" => "self.focus", "onunload" => "doUnload()"], we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[continue_camp]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "abbrechen", "opener.yes();self.close();", "opener.no();self.close();", "opener.cancel();self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLSaveQuestion1(){
		$body = we_html_element::htmlBody(['class' => 'weEditorBody', "onblur" => "self.focus", "onunload" => "doUnload()"], we_html_tools::htmlYesNoCancelDialog(g_l('modules_newsletter', '[ask_to_preserve]'), '<span class="fa-stack fa-lg" style="color:#F2F200;"><i class="fa fa-exclamation-triangle fa-stack-2x" ></i><i style="color:black;" class="fa fa-exclamation fa-stack-1x"></i></span>', "ja", "nein", "", "opener.document.we_form.ask.value=0;opener.we_cmd('save_newsletter');self.close();", "self.close();")
		);

		return $this->getHTMLDocument($body);
	}

	function getHTMLPrintLists(){
		$emails = [];
		$out = '';
		$count = count($this->View->newsletter->groups) + 1;

		$tab1 = '&nbsp;&nbsp;&nbsp;';
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;
		$c = 0;
		for($k = 1; $k < $count; $k++){
			$out.=we_html_element::htmlBr() .
				we_html_element::htmlDiv(['class' => 'defaultfont'], $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[mailing_list]'), $k)));
			$gc = 0;
			if(defined('CUSTOMER_TABLE')){
				$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab2 . g_l('modules_newsletter', '[customers]'));
				$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_CUSTOMER, 1);

				foreach($emails as $email){
					$gc++;
					$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab3 . $email);
				}
			}

			$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab2 . g_l('modules_newsletter', '[emails]'));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_EMAILS, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab3 . $email);
			}

			$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab2 . g_l('modules_newsletter', '[file_email]'));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_FILE, 1);
			foreach($emails as $email){
				$gc++;
				$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab3 . $email);
			}
			$c+=$gc;
			$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab1 . we_html_element::htmlB(sprintf(g_l('modules_newsletter', '[sum_group]'), $k) . ":" . $gc));
		}

		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(['class' => 'defaultfont'], $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[sum_all]') . ":" . $c)) .
			we_html_element::htmlBr();
		echo self::getHTMLDocument(we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "onload" => "self.focus()"], we_html_tools::htmlDialogLayout(
						we_html_element::htmlBr() .
						we_html_element::htmlDiv(['class' => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"], $out) .
						we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
					)
		)));
		flush();
	}

	function getHTMLDCheck(){
		$tab1 = "&nbsp;&nbsp;&nbsp;";
		$tab2 = $tab1 . $tab1;
		$tab3 = $tab1 . $tab1 . $tab1;

		$emails = [];
		$count = count($this->View->newsletter->groups) + 1;

		$out = we_html_element::htmlBr() .
			we_html_element::htmlDiv(['class' => 'defaultfont'], $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_begins]'))) .
			we_html_element::htmlBr();

		for($k = 1; $k < $count; $k++){

			$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab2 . sprintf(g_l('modules_newsletter', '[domain_check_list]'), $k));

			$emails = $this->View->getEmails($k, we_newsletter_view::MAILS_ALL, 1);

			foreach($emails as $email){
				if($this->View->newsletter->check_email($email)){
					$domain = "";

					if(!$this->View->newsletter->check_domain($email, $domain)){
						$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab3 . sprintf(g_l('modules_newsletter', '[domain_nok]'), $domain));
					}
				} else {
					$out.=we_html_element::htmlDiv(['class' => 'defaultfont'], $tab3 . sprintf(g_l('modules_newsletter', '[email_malformed]'), $email));
				}
			}
		}
		$out.=we_html_element::htmlBr() .
			we_html_element::htmlDiv(['class' => 'defaultfont'], $tab1 . we_html_element::htmlB(g_l('modules_newsletter', '[domain_check_ends]'))) .
			we_html_element::htmlBr();
		echo self::getHTMLDocument(we_html_element::htmlBody(['class' => 'weDialogBody'], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "onload" => "self.focus()"], we_html_tools::htmlDialogLayout(
						we_html_element::htmlBr() .
						we_html_element::htmlDiv(['class' => "blockWrapper", "style" => "width: 588px; height: 500px; border:1px #dce6f2 solid;"], $out) .
						we_html_element::htmlBr(), g_l('modules_newsletter', '[lists_overview]'), we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();")
					)
		)));
		flush();
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

		$table = new we_html_table(array('class' => 'default withSpace', 'style' => 'margin-bottom:10px'), 1, 2);
		$c = 0;

		foreach($texts as $text){
			if(!isset($settings[$text])){
				$this->View->putSetting($text, (isset($defaults[$text]) ? $defaults[$text] : 0));
				$settings = we_newsletter_view::getSettings();
			}

			$table->setCol($c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[' . $text . ']') . ":&nbsp;");
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlTextInput($text, 40, $settings[$text], "", "", "text", 308));

			$c++;
			$table->addRow();
		}

		if(defined('CUSTOMER_TABLE')){
			$custfields = [];

			foreach($this->View->customers_fields as $fv){
				$custfields[$fv] = $fv;
			}

			$table->setCol($c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_email_field]') . ":&nbsp;");
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect("customer_email_field", $custfields, 1, $settings["customer_email_field"], false, [], "value", 308));

			$table->setCol(++$c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_html_field]') . ':&nbsp;');
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect('customer_html_field', $custfields, 1, $settings['customer_html_field'], false, [], 'value', 308));

			$table->setCol(++$c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_salutation_field]') . ':&nbsp;');
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect('customer_salutation_field', $custfields, 1, $settings['customer_salutation_field'], false, [], 'value', 308));

			$table->setCol(++$c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_title_field]') . ':&nbsp;');
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect('customer_title_field', $custfields, 1, $settings['customer_title_field'], false, [], 'value', 308));

			$table->setCol(++$c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_firstname_field]') . ':&nbsp;');
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect('customer_firstname_field', $custfields, 1, $settings['customer_firstname_field'], false, [], 'value', 308));

			$table->setCol(++$c, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[customer_lastname_field]') . ':&nbsp;');
			$table->setCol($c, 1, ['class' => 'defaultfont'], we_html_tools::htmlSelect('customer_lastname_field', $custfields, 1, $settings['customer_lastname_field'], false, [], 'value', 308));
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
				$radios_code.= we_html_forms::checkbox($settings[$radio], (($settings[$radio] > 0) ? true : false), $radio . "_check", g_l('modules_newsletter', '[' . $radio . "_check]"), false, "defaultfont", "if(document.we_form." . $radio . "_check.checked){document.we_form." . $radio . ".value=" . (isset($defaults[$radio . "_check"]) ? $defaults[$radio . "_check"] : 0) . ";}else{document.we_form." . $radio . ".value=0;}");

				$radio_table = new we_html_table(['class' => 'default', 'style' => 'margin-left:25px;'], 1, 2);
				$radio_table->setCol(0, 0, ['class' => 'defaultfont'], oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')) . ":&nbsp;");
				$radio_table->setCol(0, 1, ['class' => 'defaultfont', 'style' => 'padding-left:5px;'], we_html_tools::htmlTextInput($radio, 5, $settings[$radio], "", "OnChange='if(document.we_form." . $radio . ".value!=0){document.we_form." . $radio . "_check.checked=true;}else{document.we_form." . $radio . "_check.checked=false;}'"));
				$radios_code.=$radio_table->getHtml();
			} else {
				$radios_code.=we_html_forms::checkbox($settings[$radio], (($settings[$radio] == 1) ? true : false), $radio, oldHtmlspecialchars(g_l('modules_newsletter', '[' . $radio . ']')), false, "defaultfont", "if(document.we_form." . $radio . ".checked){document.we_form." . $radio . ".value=1;}else{document.we_form." . $radio . ".value=0;}") . '<br/>';
			}
		}

		$deselect = we_html_button::create_button(we_html_button::TRASH, "javascript:document.we_form.global_mailing_list.value=''");

		$gml_table = new we_html_table(['class' => 'default withSpace', "style" => 'width:538px;margin:10px;'], 4, 2);
		$gml_table->setCol(0, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[global_mailing_list]'));
		$gml_table->setCol(2, 0, [], $this->formFileChooser(380, "global_mailing_list", $settings["global_mailing_list"]));
		$gml_table->setCol(2, 1, ['style' => 'text-align:right'], $deselect);

		$body = we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();'], we_html_element::htmlForm(['name' => 'we_form'], $this->View->getHiddens() .
					we_html_tools::htmlDialogLayout(
						$table->getHtml() .
						$radios_code .
						$gml_table->getHtml(), g_l('modules_newsletter', '[settings]'), we_html_button::position_yes_no_cancel($save, $close)
					)
				)
				. ($closeflag ? we_html_element::jsElement('top.close();') : "")
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLBlockType($name, $selected = 1){
		$values = [we_newsletter_block::DOCUMENT => g_l('modules_newsletter', '[newsletter_type_0]'),
			we_newsletter_block::DOCUMENT_FIELD => g_l('modules_newsletter', '[newsletter_type_1]'),
		];

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

	function getHTMLCopy(){
		return we_html_element::htmlHiddens([
				'copyid' => 0,
				'copyid_text' => ""]) .
			we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_file',document.we_form.elements.copyid.value,'" . NEWSLETTER_TABLE . "','copyid','copyid_text','copy_newsletter','','" . get_ws(NEWSLETTER_TABLE) . "')");
	}

	function getHTMLCustomer($group){
		$out = we_html_forms::checkbox($this->View->newsletter->groups[$group]->SendAll, (($this->View->newsletter->groups[$group]->SendAll == 0) ? false : true), "sendallcheck_$group", g_l('modules_newsletter', '[send_all]'), false, "defaultfont", "we_cmd('switch_sendall',$group);");

		if($this->View->newsletter->groups[$group]->SendAll == 0){

			$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_customers'," . $group . ")");
			$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('we_customer_selector','','" . CUSTOMER_TABLE . "','','','fillIDs();opener.we_cmd(\'add_customer\',top.allIDs.join(\',\')," . $group . ");','','','',1)");

			$cats = new we_chooser_multiDir(self::def_width, $this->View->newsletter->groups[$group]->Customers, "del_customer", $delallbut . $addbut, "", '"we/customer"', CUSTOMER_TABLE);
			$cats->extraDelFn = "document.we_form.ngroup.value=$group";
			$out.=$cats->get();
		}

		$out.=$this->getHTMLCustomerFilter($group);

		return $out;
	}

	function getHTMLExtern($group){
		$delallbut = we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:we_cmd('del_all_files'," . $group . ")");
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('browse_server','fileselect','" . we_base_ContentTypes::TEXT . "','/','add_file," . $group . "','',1);");


		$buttons = $delallbut . (permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES')) ? $addbut : '';

		$cats = new we_chooser_multiFile(self::def_width, $this->View->newsletter->groups[$group]->Extern, 'del_file', $buttons, 'edit_file');

		$cats->extraDelFn = 'document.we_form.ngroup.value=' . $group;
		return we_html_element::htmlHiddens(['fileselect' => '']) .
			$cats->get();
	}

	function getHTMLCustomerFilter($group){
		$custfields = [];
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

		$operators = [
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
		];
		$logic = ['AND' => g_l('modules_newsletter', '[logic][and]'), "OR" => g_l('modules_newsletter', '[logic][or]')];
		$hours = [];
		for($i = 0; $i < 24; $i++){
			$hours[] = ($i <= 9 ? '0' : '') . $i;
		}
		$minutes = [];
		for($i = 0; $i < 60; $i++){
			$minutes[] = ($i <= 9 ? '0' : '') . $i;
		}

		$filter = $this->View->newsletter->groups[$group]->getFilter();

		$table = new we_html_table(['class' => 'default'], 1, 7);
		$colspan = "7";
		$table->setCol(0, 0, (($filter) ? ["colspan" => $colspan] : []), we_html_forms::checkbox(($filter ? 1 : 0), ($filter ? true : false), "filtercheck_$group", g_l('modules_newsletter', '[filter]'), false, "defaultfont", "if(document.we_form.filtercheck_$group.checked) we_cmd('add_filter',$group); else we_cmd('del_all_filters',$group);"));

		$k = 0;
		$c = 1;
		if($filter){
			foreach($filter as $k => $v){
				if($k != 0){
					$table->addRow();
					$table->setCol($c, 0, ["colspan" => $colspan], we_html_tools::htmlSelect("filter_logic_" . $group . "_" . $k, $logic, 1, $v["logic"], false, [], "value", 70));
					$c++;
				}

				$table->addRow();
				$table->setCol($c, 0, [], we_html_tools::htmlSelect("filter_fieldname_" . $group . "_" . $k, $custfields, 1, $v["fieldname"], false, array('onchange' => 'top.content.hot=1;changeFieldValue(this.val,\'filter_fieldvalue_' . $group . '_' . $k . '\');'), "value", 170));
				$table->setCol($c, 1, [], we_html_tools::htmlSelect("filter_operator_" . $group . "_" . $k, $operators, 1, $v["operator"], false, array('onchange' => "top.content.hot=1;"), "value", 80));
				if($v['fieldname'] === "MemberSince" || $v['fieldname'] === "LastLogin" || $v['fieldname'] === "LastAccess"){
					$table->setCol($c, 2, ["id" => "td_value_fields_" . $group . "_" . $k], we_html_tools::getDateSelector("filter_fieldvalue_" . $group . "_" . $k, "_from_" . $group . "_" . $k, !empty($v["fieldvalue"]) ? !stristr($v["fieldvalue"], ".") ? date("d.m.Y", $v["fieldvalue"]) : $v["fieldvalue"] : ""));
					$table->setCol($c, 3, [], we_html_tools::htmlSelect("filter_hours_" . $group . "_" . $k, $hours, 1, isset($v["hours"]) ? $v["hours"] : "", false, array('onchange' => 'top.content.hot=1;')));
					$table->setCol($c, 4, ['class' => 'defaultfont'], "&nbsp;h :");
					$table->setCol($c, 5, [], we_html_tools::htmlSelect("filter_minutes_" . $group . "_" . $k, $minutes, 1, isset($v["minutes"]) ? $v["minutes"] : "", false, array('onchange' => "top.content.hot=1;")));
					$table->setCol($c, 6, ['class' => 'defaultfont'], "&nbsp;m");
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
			$table->setCol($c, 0, ["colspan" => $colspan, 'style' => 'padding-top:5px;'], $plus . $trash);
		}

		$js = we_html_element::jsElement("calendarSetup(" . $group . "," . $k . ");");

		return we_html_element::htmlHiddens(["filter_" . $group => count($filter)]) .
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
		$buttons_table = new we_html_table(['class' => 'default withSpace'], 4, 1);
		$buttons_table->setCol(0, 0, [], we_html_button::create_button(we_html_button::ADD, "javascript:we_cmd('add_email', " . $group . ");"));
		$buttons_table->setCol(1, 0, [], we_html_button::create_button(we_html_button::EDIT, "javascript:we_cmd('edit_email', " . $group . ");"));
		$buttons_table->setCol(2, 0, [], we_html_button::create_button(we_html_button::DELETE, "javascript:deleteit(" . $group . ")"));
		$buttons_table->setCol(3, 0, [], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:deleteall(" . $group . ")"));

		// Dialog table for the email block
		$table = new we_html_table(['class' => 'default withSpace'], 3, 3);

		// 1. ROW: select status
		$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", [g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')], "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", ['onchange' => "weShowMailsByStatus(this.value, $group);", 'id' => 'weViewByStatus'], "value", 150);
		$table->setCol(0, 0, ['style' => 'vertical-align:middle;', "colspan" => 3, 'class' => 'defaultfont'], $selectStatus);

		// 2. ROW: Mail list with handling buttons
		$table->setCol(1, 0, ['style' => 'vertical-align:top;'], $this->View->newsletter->htmlSelectEmailList("we_recipient" . $group, $arr, 10, "", false, 'style="width:' . (self::def_width - 110) . 'px; height:140px" id="we_recipient' . $group . '"', "value", 600));
		$table->setCol(1, 1, ['style' => 'vertical-align:middle;width:10px;']);
		$table->setCol(1, 2, ['style' => 'vertical-align:top;'], $buttons_table->getHtml());

		// 3. ROW: Buttons for email import and export
		$importbut = we_html_button::create_button('import', "javascript:we_cmd('set_import'," . $group . ")");
		$exportbut = we_html_button::create_button('export', "javascript:we_cmd('set_export'," . $group . ")");

		$table->setCol(2, 0, ["colspan" => 3], $importbut . $exportbut);

		// Import dialog
		if($this->View->getShowImportBox() == $group){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('import_csv')");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('reset_import');");

			$import_options = new we_html_table(['class' => 'default withSpace'], 7, 3);

			$import_options->setCol(0, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, [], we_html_tools::htmlTextInput("csv_delimiter" . $group, 1, ","));
			$import_options->setCol(1, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(1, 1, [], we_html_tools::htmlTextInput("csv_col" . $group, 2, 1));
			$import_options->setCol(2, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_hmcol]') . ":&nbsp;");
			$import_options->setCol(2, 1, [], we_html_tools::htmlTextInput("csv_hmcol" . $group, 2, 2));
			$import_options->setCol(2, 2, ['class' => "defaultfont lowContrast"], "&nbsp;" . g_l('modules_newsletter', '[csv_html_explain]'));
			$import_options->setCol(3, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_salutationcol]') . ":&nbsp;");
			$import_options->setColContent(3, 1, we_html_tools::htmlTextInput("csv_salutationcol" . $group, 2, 3));
			$import_options->setCol(3, 2, ['class' => "defaultfont lowContrast"], "&nbsp;" . g_l('modules_newsletter', '[csv_salutation_explain]'));
			$import_options->setCol(4, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_titlecol]') . ":&nbsp;");
			$import_options->setColContent(4, 1, we_html_tools::htmlTextInput("csv_titlecol" . $group, 2, 4));
			$import_options->setCol(4, 2, ['class' => "defaultfont lowContrastdefaultfont lowContrast"], "&nbsp;" . g_l('modules_newsletter', '[csv_title_explain]'));
			$import_options->setCol(5, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_firstnamecol]') . ":&nbsp;");
			$import_options->setColContent(5, 1, we_html_tools::htmlTextInput("csv_firstnamecol" . $group, 2, 5));
			$import_options->setCol(5, 2, ['class' => "defaultfont lowContrast"], "&nbsp;" . g_l('modules_newsletter', '[csv_firstname_explain]'));
			$import_options->setCol(6, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_lastnamecol]') . ":&nbsp;");
			$import_options->setColContent(6, 1, we_html_tools::htmlTextInput("csv_lastnamecol" . $group, 2, 6));
			$import_options->setCol(6, 2, ['class' => "defaultfont lowContrast"], "&nbsp;" . g_l('modules_newsletter', '[csv_lastname_explain]'));


			$import_box = new we_html_table(['class' => 'default withSpace', 'style' => 'margin-top:10px;'], 4, 1);
			$import_box->setColContent(0, 0, $this->formFileChooser(200, "csv_file" . $group, "/", ""));
			$import_box->setColContent(1, 0, we_html_button::create_button(we_html_button::UPLOAD, "javascript:we_cmd('upload_csv',$group)"));
			$import_box->setColContent(2, 0, $import_options->getHtml());
			$import_box->setColContent(3, 0, $ok . $cancel);

			$table->setCol(5, 0, ["colspan" => 3], we_html_element::htmlHiddens(["csv_import" => $group]) . $import_box->getHtml());
		}

		// Export dialog
		if($this->View->getShowExportBox() == $group){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:we_cmd('export_csv')");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:we_cmd('reset_import');");

			$export_box = new we_html_table(['class' => 'default withSpace', 'style' => 'margin-top:10px;'], 2, 1);

			$export_box->setCol(1, 0, [], $this->formFileChooser(200, "csv_dir" . $group, "/", "", "folder"));
			$export_box->setCol(2, 0, [], $ok . $cancel);

			$table->setCol(5, 0, ["colspan" => 3], we_html_element::htmlHiddens(["csv_export" => $group]) . $export_box->getHtml());
		}

		return $table->getHtml();
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $open_doc = '', $acObject = null, $contentType = ''){
		$Pathvalue = $Pathvalue? : f('SELECT Path FROM ' . $this->db->escape($table) . ' WHERE ID=' . intval($IDValue), '', $this->db);

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $IDName . "','" . $Pathname . "','" . we_base_request::encCmd(str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "','','" . $open_doc . "')");
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

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','" . $IDName . "','" . $Pathname . "','" . we_base_request::encCmd(str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "','" . $filter . "'," . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")");
		if(is_object($acObject)){
			$yuiSuggest = $acObject;
			$yuiSuggest->setAcId($IDName);
			$yuiSuggest->setContentType([we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH]);
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
			array("headline" => "", "html" => we_html_element::htmlHiddens(array("blocks" => count($this->View->newsletter->blocks))), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1)
		);

		foreach($this->View->newsletter->blocks as $block){
			$content = we_html_tools::htmlFormElementTable($this->getHTMLBlockType("block" . $counter . "_Type", $block->Type), g_l('modules_newsletter', '[name]'));

			$values = [];
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
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => "defaultfont lowContrast"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
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
								we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => "defaultfont lowContrast"), g_l('modules_newsletter', '[none]')), g_l('modules_newsletter', '[block_document_field]'))
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
						"height" => 400,
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
						we_html_element::jsScript(JS_DIR . 'we_textarea.js') .
						we_html_tools::htmlFormElementTable(we_html_forms::weTextarea("block" . $counter . "_Html", $blockHtml, $attribs, "", "", true, true, true, false, true, $this->View->newsletter->Charset), g_l('modules_newsletter', '[block_html]')) .
						we_html_element::jsElement('
function extraInit(){
	if(typeof weWysiwygInitializeIt == "function"){
		weWysiwygInitializeIt();
	}
	loaded = true;
}
window.onload=extraInit;');

					break;

				case we_newsletter_block::ATTACHMENT:
					$content.=we_html_tools::htmlFormElementTable($this->formWeChooser(FILE_TABLE, 320, 0, "block" . $counter . "_LinkID", $block->LinkID, "block" . $counter . "_LinkPath", "", "", "", $this->weAutoCompleter, implode(',', array(we_base_ContentTypes::FOLDER, we_base_ContentTypes::XML, we_base_ContentTypes::WEDOCUMENT, we_base_ContentTypes::IMAGE, we_base_ContentTypes::HTML, we_base_ContentTypes::APPLICATION, we_base_ContentTypes::FLASH))), g_l('modules_newsletter', '[block_attachment]'));
					break;

				case we_newsletter_block::URL:
					$content.=we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("block" . $counter . "_Field", 49, (is_numeric($block->Field) ? "" : $block->Field), "", "style='width:440px;'", "text", 0, 0, "top.content"), g_l('modules_newsletter', '[block_url]'));
					break;
			}

			$buttons = '<div style="margin-left:440px;">' .
				we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('addBlock','" . $counter . "')") .
				(count($this->View->newsletter->blocks) > 1 ?
					we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('delBlock','" . $counter . "')") :
					''
				) . '</div>';

			$parts[] = array("headline" => sprintf(g_l('modules_newsletter', '[block]'), ($counter + 1)), "html" => $content, 'space' => we_html_multiIconBox::SPACE_MED2);
			$parts[] = array("headline" => "", "html" => $buttons, 'space' => we_html_multiIconBox::SPACE_MED2);

			$counter++;
		}

		return we_html_multiIconBox::getHTML("newsletter_header", $parts, 30);
	}

	function getHTMLNewsletterGroups(){
		$count = count($this->View->newsletter->groups);
		$out = we_html_multiIconBox::getJS();

		for($i = 0; $i < $count; $i++){
			$parts = array(
				defined('CUSTOMER_TABLE') ? array("headline" => g_l('modules_newsletter', '[customers]'), "html" => $this->getHTMLCustomer($i), 'space' => we_html_multiIconBox::SPACE_MED2) : null,
				array("headline" => g_l('modules_newsletter', '[file_email]'), "html" => $this->getHTMLExtern($i), 'space' => we_html_multiIconBox::SPACE_MED2),
				array("headline" => g_l('modules_newsletter', '[emails]'), "html" => $this->getHTMLEmails($i), 'space' => we_html_multiIconBox::SPACE_MED2)
			);


			$buttons = ($i == $count - 1 ? we_html_button::create_button(we_html_button::PLUS, "javascript:we_cmd('addGroup')") : null) .
				($count > 1 ? we_html_button::create_button(we_html_button::TRASH, "javascript:we_cmd('delGroup'," . $i . ")") : null);

			$wepos = weGetCookieVariable("but_newsletter_group_box_$i");

			$out.= we_html_multiIconBox::getHTML("newsletter_group_box_$i", $parts, 30, "", 0, "", "", (($wepos === "down") || ($count < 2 ? true : false)), sprintf(g_l('modules_newsletter', '[mailing_list]'), ($i + 1))) .
				we_html_element::htmlBr() .
				'<div style="margin-right:30px;">' . $buttons . '</div>';
		}

		return $out;
	}

	private function formNewsletterDirChooser($width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = '', $acObject = null){
		if(!$Pathvalue){
			$Pathvalue = f('SELECT Path FROM ' . NEWSLETTER_TABLE . ' WHERE ID=' . intval($IDValue), '', $this->db);
		}

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_newsletter_dirSelector',document.we_form.elements['" . $IDName . "'].value,'" . $IDName . "','" . $Pathname . "','" . we_base_request::encCmd(str_replace('\\', '', $cmd)) . "','','" . $rootDirID . "')");
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
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'readonly="readonly" id="yuiAcInputPathGroup"', "text", $width, 0), "", "left", "defaultfont", we_html_element::htmlHidden(trim($IDName), oldHtmlspecialchars($IDValue)), $button
		);
	}

	function getHTMLNewsletterHeader(){
		$table = new we_html_table(['class' => 'default withSpace'], 2, 1);
		$table->setCol(0, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Text", 37, stripslashes($this->View->newsletter->Text), "", 'onKeyUp="top.content.hot=1;" id="yuiAcInputPathName" onblur="parent.edheader.weTabs.setTitlePath(this.value);"', 'text', self::def_width), g_l('modules_newsletter', '[name]')));
		$table->setCol(2, 0, [], we_html_tools::htmlFormElementTable($this->formNewsletterDirChooser((self::def_width - 120), 0, "ParentID", $this->View->newsletter->ParentID, "Path", dirname($this->View->newsletter->Path), "opener.top.content.hot=1;", $this->weAutoCompleter), g_l('modules_newsletter', '[dir]')));

		//$table->setCol(2,0,[],we_html_tools::htmlFormElementTable($this->formWeDocChooser(NEWSLETTER_TABLE,320,0,"ParentID",$this->View->newsletter->ParentID,"Path",dirname($this->View->newsletter->Path),"opener.top.content.hot=1;","folder"),g_l('modules_newsletter','[dir]')));
		$parts = array(
			array("headline" => "", "html" => "", 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1),
			array("headline" => g_l('modules_newsletter', '[path]'), "html" => $table->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2),
		);

		if(!$this->View->newsletter->IsFolder){
			$table = new we_html_table(['class' => 'default withSpace'], 4, 1);
			$table->setCol(0, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Subject", 37, stripslashes($this->View->newsletter->Subject), "", "onKeyUp='top.content.hot=1;'", 'text', self::def_width), g_l('modules_newsletter', '[subject]')));
			$table->setCol(1, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Sender", 37, $this->View->newsletter->Sender, "", "onKeyUp='top.content.hot=1;'", 'text', self::def_width), g_l('modules_newsletter', '[sender]')));

			$chk = ($this->View->newsletter->Sender == $this->View->newsletter->Reply ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "checked" => null, "name" => "reply_same", "onclick" => "top.content.hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value")) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 0, "name" => "reply_same", "onclick" => "top.content.hot=1;if(document.we_form.reply_same.checked) document.we_form.Reply.value=document.we_form.Sender.value"))
				);
			$table->setCol(2, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Reply", 37, $this->View->newsletter->Reply, "", "onKeyUp='top.content.hot=1;'") . "&nbsp;&nbsp;" . $chk . "&nbsp;" . we_html_element::htmlLabel(array('class' => 'defaultfont', "onclick" => "top.content.hot=1;if(document.we_form.reply_same.checked){document.we_form.reply_same.checked=false;}else{document.we_form.Reply.value=document.we_form.Sender.value;document.we_form.reply_same.checked=true;}"), g_l('modules_newsletter', '[reply_same]')), g_l('modules_newsletter', '[reply]')));
			$table->setCol(3, 0, [], we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("Test", 37, $this->View->newsletter->Test, "", "onKeyUp='top.content.hot=1;'"), g_l('modules_newsletter', '[test_email]')));

			$embedImagesChk = ($this->View->newsletter->isEmbedImages ?
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "name" => "isEmbedImagesChk", "onclick" => "top.content.hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}", "checked" => null), g_l('modules_newsletter', '[isEmbedImages]')) :
					we_html_element::htmlInput(array("type" => "checkbox", "value" => 1, "name" => "isEmbedImagesChk", "onclick" => "top.content.hot=1;if(document.we_form.isEmbedImagesChk.checked){document.we_form.isEmbedImages.value=1;}else{document.we_form.isEmbedImages.value=0;}"), g_l('modules_newsletter', '[isEmbedImages]'))
				);
			$embedImagesHid = we_html_element::htmlHidden("isEmbedImages", $this->View->newsletter->isEmbedImages);
			$embedImagesLab = we_html_element::htmlLabel(array('class' => 'defaultfont', "onclick" => "top.content.hot=1;if(document.we_form.isEmbedImagesChk.checked){ document.we_form.isEmbedImagesChk.checked=false; document.we_form.isEmbedImages.value=0; }else{document.we_form.isEmbedImagesChk.checked=true;document.we_form.isEmbedImages.value=1;}"), g_l('modules_newsletter', '[isEmbedImages]'));

			$table->setCol(4, 0, [], we_html_tools::htmlFormElementTable($embedImagesHid . $embedImagesChk . "&nbsp;" . $embedImagesLab, ""));

			$parts[] = array("headline" => g_l('modules_newsletter', '[newsletter][text]'), "html" => $table->getHtml(), 'space' => we_html_multiIconBox::SPACE_MED2);
			$parts[] = array("headline" => g_l('modules_newsletter', '[charset]'), "html" => $this->getHTMLCharsetTable(), 'space' => we_html_multiIconBox::SPACE_MED2);
			$parts[] = array("headline" => g_l('modules_newsletter', '[copy_newsletter]'), "html" => $this->getHTMLCopy(), 'space' => we_html_multiIconBox::SPACE_MED2, 'noline' => 1);
		}

		return we_html_multiIconBox::getHTML("newsletter_header", $parts, 30) .
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
			return $this->View->getHomeScreen();
		}

		$js = $this->View->getJSProperty('setFocus();') .
			we_html_tools::getCalendarFiles();

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
					we_html_multiIconBox::getHTML('', $this->getHTMLReporting(), 30) .
					$this->weAutoCompleter->getYuiJs();
		}

		$body = we_html_element::htmlBody(array("onload" => "self.loaded=true;if(self.doScrollTo){self.doScrollTo();}; setHeaderTitle();", "class" => "weEditorBody", "onunload" => "doUnload()"), we_html_element::htmlForm(array('name' => 'we_form', "method" => "post", "onsubmit" => "return false;"), $out
				)
		);
//$this->getHTMLDocumentHeader();
		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLEmailEdit(){
		$type = we_base_request::_(we_base_request::INT, 'etyp', 0);
		$htmlmail = we_base_request::_(we_base_request::STRING, 'htmlmail', f('SELECT pref_value FROM ' . SETTINGS_TABLE . ' WHERE tool="newsletter" AND pref_name="default_htmlmail"', '', $this->db));
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
		$table = new we_html_table(['class' => 'default'], 12, 3);

		$table->setCol($row, 0, ['class' => "defaultfont lowContrast"], g_l('modules_newsletter', '[email]'));
		$table->setCol($row++, 1, ['style' => "padding-left:15px;padding-bottom:2px;"], we_html_tools::htmlTextInput("emailfield", 32, $email, "", "", "text", 310));


		$table->setCol($row++, 1, ['style' => "padding-bottom:2px;"], we_html_forms::checkbox($htmlmail, (($htmlmail) ? true : false), "htmlmail", g_l('modules_newsletter', '[edit_htmlmail]'), false, "defaultfont", "if(document.we_form.htmlmail.checked) document.we_form.htmlmail.value=1; else document.we_form.htmlmail.value=0;"));

		$salut_select = new we_html_select(['name' => "salutation", "style" => "width: 310px"]);
		$salut_select->addOption("", "");
		if(!empty($this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD], $this->View->settings[we_newsletter_newsletter::FEMALE_SALUTATION_FIELD]);
		}
		if(!empty($this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD])){
			$salut_select->addOption($this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD], $this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD]);
		}
		$salut_select->selectOption($salutation);

		$table->setCol($row, 0, ['class' => "defaultfont lowContrast", 'style' => "padding-bottom:2px;"], g_l('modules_newsletter', '[salutation]'));
		$table->setCol($row++, 1, ['style' => 'padding-left:15px;'], $salut_select->getHtml());


		$table->setCol($row, 0, ['class' => "defaultfont lowContrastdefaultfont lowContrast", 'style' => "padding-bottom:2px;"], g_l('modules_newsletter', '[title]'));
		$table->setCol($row++, 1, ['style' => 'padding-left:15px;'], we_html_tools::htmlTextInput("title", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($title) : $title), "", "", "text", 310));


		$table->setCol($row, 0, ['class' => "defaultfont lowContrast", 'style' => "padding-bottom:2px;"], g_l('modules_newsletter', '[firstname]'));
		$table->setCol($row++, 1, ['style' => 'padding-left:15px;'], we_html_tools::htmlTextInput("firstname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($firstname) : $firstname), "", "", "text", 310));


		$table->setCol($row, 0, ['class' => "defaultfont lowContrast", 'style' => "padding-bottom:2px;"], g_l('modules_newsletter', '[lastname]'));
		$table->setCol($row++, 1, ['style' => 'padding-left:15px;'], we_html_tools::htmlTextInput("lastname", 32, ($GLOBALS['WE_BACKENDCHARSET'] != 'UTF-8' ? utf8_decode($lastname) : $lastname), "", "", "text", 310));


		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:save();");

		$body = we_html_element::htmlBody(['class' => "weDialogBody", "onload" => "document.we_form.emailfield.select();document.we_form.emailfield.focus();"], we_html_element::htmlForm(['name' => 'we_form', "onsubmit" => "save();return false;"], we_html_element::htmlHidden("group", $group) .
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
							<textarea name="foo" style="width:100%;height:95%">' .
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
			we_html_element::jsScript(WE_JS_MODULES_DIR . 'newsletter/newsletter_frames.js', 'self.focus();');

		switch(we_base_request::_(we_base_request::STRING, "ncmd")){
			case "import_black":
				$filepath = we_base_request::_(we_base_request::FILE, "csv_file");
				$delimiter = we_base_request::_(we_base_request::RAW_CHECKED, "csv_delimiter");
				$col = we_base_request::_(we_base_request::INT, "csv_col", 0);

				if($col){
					$col--;
				}

				if(strpos($filepath, '..') !== false){
					echo we_message_reporting::jsMessagePush(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
				} else {
					$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $filepath, "rb");
					if($fh){
						while(($dat = fgetcsv($fh, 1000, $delimiter))){
							$alldat = implode("", $dat);
							if(str_replace(" ", "", $alldat) === ''){
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
						echo we_message_reporting::jsMessagePush(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
					}
				}
				break;
			case "export_black":
				$fname = rtrim(we_base_request::_(we_base_request::FILE, "csv_dir", ''), '/') . '/blacklist_export_' . time() . '.csv';
				we_base_file::save($_SERVER['DOCUMENT_ROOT'] . $fname, str_replace(",", "\n", $this->View->settings["black_list"]));

				$js.= we_html_element::jsElement('new (WE().util.jsWindow)(window, WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=export_csv_mes&lnk=' . $fname . '","edit_email",-1,-1,440,250,true,true,true,true);');
				break;
		}


		$arr = makeArrayFromCSV($this->View->settings["black_list"]);


		$buttons_table = new we_html_table(['class' => 'default withSpace'], 4, 1);
		$buttons_table->setCol(0, 0, [], we_html_button::create_button(we_html_button::ADD, "javascript:addBlack();"));
		$buttons_table->setCol(1, 0, [], we_html_button::create_button(we_html_button::EDIT, "javascript:editBlack();"));
		$buttons_table->setCol(2, 0, [], we_html_button::create_button(we_html_button::DELETE, "javascript:deleteBlack()"));
		$buttons_table->setCol(3, 0, [], we_html_button::create_button(we_html_button::DELETE_ALL, "javascript:deleteallBlack()"));

		$table = new we_html_table(array('class' => 'default'), 5, 3);
		$table->setCol(0, 0, array('style' => 'vertical-align:middle;'), we_html_tools::htmlSelect("blacklist_sel", $arr, 10, "", false, array('style' => "width:388px"), "value", 600));
		$table->setCol(0, 1, array('style' => 'vertical-align:top;padding-left:15px;'), $buttons_table->getHtml());

		$importbut = we_html_button::create_button('import', "javascript:set_import(1)");
		$exportbut = we_html_button::create_button('export', "javascript:set_export(1)");

		$table->setCol(2, 0, array("colspan" => 3, 'style' => 'padding-top:10px;'), $importbut . $exportbut);

		$sib = we_base_request::_(we_base_request::RAW, "sib", 0);
		$seb = we_base_request::_(we_base_request::RAW, "seb", 0);

		if($sib){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:document.we_form.sib.value=0;we_cmd('import_black');");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:set_import(0);");

			$import_options = new we_html_table(['class' => 'default withSpace'], 2, 2);

			$import_options->setCol(0, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_delimiter]') . ":&nbsp;");
			$import_options->setCol(0, 1, [], we_html_tools::htmlTextInput("csv_delimiter", 1, ","));
			$import_options->setCol(1, 0, ['class' => 'defaultfont'], g_l('modules_newsletter', '[csv_col]') . ":&nbsp;");
			$import_options->setCol(1, 1, [], we_html_tools::htmlTextInput("csv_col", 2, 1));

			$import_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'padding-top:10px;'), 4, 1);
			$import_box->setCol(0, 0, [], $this->formFileChooser(200, "csv_file", "/", ""));
			$import_box->setCol(1, 0, [], we_html_button::create_button(we_html_button::UPLOAD, "javascript:we_cmd('upload_black')"));
			$import_box->setCol(2, 0, [], $import_options->getHtml());
			$import_box->setCol(3, 0, [], $ok . $cancel);

			$table->setCol(3, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_import" => 1)) .
				$import_box->getHtml()
			);
		} elseif($seb){
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:document.we_form.seb.value=0;we_cmd('export_black');");
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:set_export(0);");

			$export_box = new we_html_table(array('class' => 'default withSpace', 'style' => 'padding-top:10px;'), 2, 1);
			$export_box->setCol(0, 0, [], $this->formFileChooser(200, "csv_dir", "/", "", "folder"));
			$export_box->setCol(1, 0, [], $ok . $cancel);

			$table->setCol(3, 0, array("colspan" => 3), we_html_element::htmlHiddens(array("csv_export" => 1)) .
				$export_box->getHtml()
			);
		}


		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
		$save = we_html_button::create_button(we_html_button::SAVE, "javascript:we_cmd('save_black')");


		$body = we_html_element::htmlBody(array('class' => "weDialogBody"), we_html_element::htmlForm(array('name' => 'we_form', "onsubmit" => "save();return false;"), $this->View->getHiddens() .
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
		$weFileupload = new we_fileupload_ui_base('we_File');
		$weFileupload->setCallback("we_cmd('do_" . $what . "');");
		$weFileupload->setExternalProgress(['isExternalProgress' => true]);
		$weFileupload->setExternalUiElements(['btnUploadName' => 'upload_footer']);
		$weFileupload->setDimensions(['width' => 330, 'marginTop' => 6]);
		$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:" . $weFileupload->getJsBtnCmd('cancel'));
		$upload = we_html_button::create_button(we_html_button::UPLOAD, "javascript:" . $weFileupload->getJsBtnCmd('upload'), true, 0, 0, '', '', false, false, '_footer');

		$buttons = $cancel . $upload;
		$footerTable = new we_html_table(['class' => 'default', 'style' => 'width:100%;'], 1, 2);
		$footerTable->setCol(0, 0, [], we_html_element::htmlDiv(['id' => 'progressbar', 'style' => 'display:none;padding-left:10px']));
		$footerTable->setCol(0, 1, ['style' => 'text-align:right'], $buttons);

		$js = $this->View->getJSProperty() .
			$weFileupload->getJs();

		$table = new we_html_table(['class' => 'default withBigSpace'], 2, 1);
		$table->setCol(0, 0, ["style" => "padding-right:30px"], $weFileupload->getHtmlAlertBoxes());
		$table->setCol(1, 0, ['style' => 'vertical-align:middle;'], $weFileupload->getHTML());

		$body = we_html_element::htmlBody(['class' => "weDialogBody", 'onload' => 'self.focus();'], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "enctype" => "multipart/form-data"], we_html_element::htmlCenter(
						$this->View->getHiddens() .
						(($grp = we_base_request::_(we_base_request::STRING, 'grp')) !== false ? we_html_element::htmlHiddens(["group" => $grp]) : '') .
						we_html_element::htmlHiddens(["MAX_FILE_SIZE" => $weFileupload->getMaxUploadSize()]) .
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

		$table->setCol(0, 0, array('class' => 'defaultfont', 'style' => 'padding-top:5px;'), sprintf(g_l('modules_newsletter', '[csv_export]'), $link));
		$table->setCol(1, 0, array('class' => 'defaultfont', 'style' => 'padding-top:5px;'), we_backup_wizard::getDownloadLinkText());
		$table->setCol(2, 0, array('class' => 'defaultfont', 'style' => 'padding:5px 0px;'), we_html_element::htmlA(array("href" => getServerUrl(true) . $link, 'download' => basename($link)), g_l('modules_newsletter', '[csv_download]')));

		if($allowClear){
			$table->setCol(3, 0, array('class' => 'defaultfont', 'style' => 'padding:5px 0px;'), we_html_element::htmlB(g_l('modules_newsletter', '[clearlog_note]')));
			$cancel = we_html_button::create_button(we_html_button::CANCEL, "javascript:self.close();");
			$ok = we_html_button::create_button(we_html_button::OK, "javascript:clearLog();");
		} else {
			$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close();");
		}

		$body = we_html_element::htmlBody(array('class' => "weDialogBody", 'onload' => 'self.focus();'), we_html_element::htmlForm(array('name' => 'we_form', "method" => "post"), we_html_element::htmlHidden("group", '') .
					($allowClear ?
						we_html_element::htmlHiddens(array("pnt" => "clear_log", "ncmd" => "do_clear_log")) .
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[clear_log]'), we_html_button::position_yes_no_cancel($ok, null, $cancel), "100%", 30, "", "hidden") :
						we_html_tools::htmlDialogLayout($table->getHtml(), g_l('modules_newsletter', '[csv_download]'), we_html_button::formatButtons($close), "100%", 30, "", "hidden")
					)
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
		$out = '';
		$content = [];

		$order = we_base_request::_(we_base_request::STRING, "order", "");
		for($i = 0; $i < 14; $i = $i + 2){
			$sorter_code[$i] = "<br/>" . ($order == $i ?
					we_html_element::htmlInput(array('type' => "radio", "value" => $i, "name" => "order", "checked" => true, "onclick" => "submitForm('edit_file')")) . "&darr;" :
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
		$emails = [];
		$emailkey = [];
		if(strpos($csv_file, '..') === false){
			if($csv_file){
				$emails = we_newsletter_newsletter::getEmailsFromExtern2($csv_file, null, null, [], we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), $emailkey);
			}
		} else {
			echo we_message_reporting::jsMessagePush(g_l('modules_newsletter', '[path_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR);
		}

		$offset = max(we_base_request::_(we_base_request::INT, "offset", 0), 0);
		$numRows = we_base_request::_(we_base_request::INT, "numRows", 15);
		$anz = count($emails);
		$endRow = min($offset + $numRows, $anz);

		switch($order){
			case 2:
			case 3:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[0], $b[0]);
				});
				break;
			case 4:
			case 5:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[1], $b[1]);
				});
				break;
			case 6:
			case 7:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[2], $b[2]);
				});
				break;
			case 8:
			case 9:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[3], $b[3]);
				});
				break;
			case 10:
			case 11:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[4], $b[4]);
				}
				);
				break;
			case 12:
			case 13:
				uasort($emails, function ($a, $b){
					return strnatcasecmp($a[5], $b[5]);
				});
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
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), $k),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), ($cols[0] ? : "&nbsp;")),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), g_l('modules_newsletter', ($cols[1] ? '[yes]' : '[no]'))),
						"height" => "",
						"align" => "",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), ($cols[2] ? : "&nbsp;")),
						"height" => "",
						"align" => "right",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), ($cols[3] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), ($cols[4] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), ($cols[5] ? : "&nbsp;")),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), $edit . $trash),
						"height" => "",
						"align" => "left",
					),
					array(
						"dat" => we_html_element::htmlDiv(array('class' => "middlefont"), '<i class="fa fa-lg ' . (we_check_email($cols[0]) ? 'fa-ok fa-check' : 'fa-cancel fa-close' ) . '"></i>'),
						"height" => "",
						"align" => "center",
					)
				);
				$counter++;
			}
		}

		$js = $this->View->getJSProperty('self.focus();');

		$close = we_html_button::create_button(we_html_button::CLOSE, "javascript:self.close()");
		$edit = we_html_button::create_button(we_html_button::SAVE, "javascript:listFile()");
		$nextprev = new we_html_table(array('class' => 'default'), 1, 4);

		$nextprev->setCol(0, 0, array('style' => 'padding-right:10px;'), ($offset ?
				we_html_button::create_button(we_html_button::BACK, "javascript:document.we_form.offset.value=" . ($offset - $numRows) . ";submitForm('edit_file');") :
				we_html_button::create_button(we_html_button::BACK, "#", false, 100, 22, "", "", true)));

		$nextprev->setCol(0, 1, array('class' => 'defaultfont', 'style' => 'padding-right:10px;'), we_html_element::htmlB(( $anz ? $offset + 1 : 0 ) . "-" . (($anz - $offset) < $numRows ? $anz : $offset + $numRows) .
				g_l('global', '[from]') .
				$anz));

		$nextprev->setCol(0, 2, [], (($offset + $numRows) < $anz ?
				we_html_button::create_button(we_html_button::NEXT, "javascript:document.we_form.offset.value=" . ($offset + $numRows) . ";submitForm('edit_file');") :
				we_html_button::create_button(we_html_button::NEXT, "#", false, 100, 22, "", "", true)
		));

		if(!empty($emails)){
			$add = we_html_button::create_button(we_html_button::PLUS, "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
			$end = $nextprev->getHtml();

			$nextprev->addCol(3);

			$nextprev->setCol(0, 3, array('class' => 'defaultfont', 'style' => 'padding-left:20px;'), we_html_element::htmlB(g_l('modules_newsletter', '[show]')) . " " . we_html_tools::htmlTextInput("numRows", 5, $numRows)
			);
			$selectStatus = we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", array('onchange' => 'listFile();'), "value", 150);
			$nextprev->setCol(0, 4, array('class' => 'defaultfont', 'style' => 'padding-left:20px;'), $selectStatus);
			$nextprev->setCol(0, 5, array('class' => 'defaultfont', 'style' => 'padding-left:20px;'), $add);

			$out = $nextprev->getHtml() .
				we_html_tools::htmlDialogBorder3(850, $content, $headlines) .
				$end;
		} else {
			if(!$csv_file && empty($csv_file) && strlen($csv_file) < 4){
				$nlMessage = g_l('modules_newsletter', '[no_file_selected]');
				$selectStatus2 = '';
			} else {
				if(we_base_request::_(we_base_request::INT, 'weEmailStatus') == 1){
					$nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = "<br/>" . we_html_element::htmlB(g_l('modules_newsletter', '[status]')) . " " . we_html_tools::htmlSelect("weEmailStatus", array(g_l('modules_newsletter', '[statusAll]'), g_l('modules_newsletter', '[statusInvalid]')), "", we_base_request::_(we_base_request::RAW, 'weEmailStatus', 0), "", array('onchange' => 'listFile();'), "value", 150);
				} else {
					$nlMessage = g_l('modules_newsletter', '[file_all_ok]');
					$selectStatus2 = '';
				}
			}

			$out = we_html_element::htmlDiv(array('class' => "middlefont lowContrast", 'style' => "text-align:center;padding-bottom:2em;"), "--&nbsp;" . $nlMessage . "&nbsp;--" . $selectStatus2) .
				we_html_button::create_button(we_html_button::PLUS, "javascript:editEmailFile(" . count($emails) . ",'','','','','','')");
		}


		$body = we_html_element::htmlBody(array('class' => "weDialogBody", "onload" => ($open_file ? "submitForm('edit_file')" : "" )), we_html_element::htmlForm(array('name' => 'we_form'), we_html_element::htmlHiddens(array(
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
					we_html_tools::htmlDialogLayout(
						we_html_element::htmlDiv(array('style' => 'margin-top:10px;'), $this->formFileChooser(420, "csv_file", ($open_file ? : ($csv_file ? : "/")), "opener.postSelectorSelect('selectFile')", "", 'readonly="readonly" onchange="alert(100)"'))
						. '<br/>' . $out, g_l('modules_newsletter', '[select_file]'), $close . $edit, "100%", 30, 597)
				)
		);

		return $this->getHTMLDocument($body, $js);
	}

	function getHTMLClearLog(){
		if(we_base_request::_(we_base_request::STRING, "ncmd") === "do_clear_log"){
			$this->View->db->query('TRUNCATE TABLE ' . NEWSLETTER_LOG_TABLE);
			return
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
				we_html_element::htmlBody(array('class' => 'weDialogBody', 'onload' => "self.focus();setTimeout(function (doc) {doc.we_form.submit();},200,document);"), we_html_element::htmlForm(array('name' => 'we_form', 'action' => WEBEDITION_DIR . 'we_showMod.php', 'method' => 'post'), we_html_element::htmlHiddens(array(
							'mod' => 'newsletter',
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


		$offset = ($this->View->newsletter->Offset) ? ($this->View->newsletter->Offset + 1) : 0;
		$step = $this->View->newsletter->Step;

		if($this->View->settings['send_step'] <= $offset){
			$step++;
			$offset = 0;
		}


		$head = we_html_element::jsElement('
function yes(){
	doSend(' . $offset . ',' . $step . ');
}

function no(){
	doSend(0,0);
}
function cancel(){
	self.close();
}

function ask(start,group){
	new (WE().util.jsWindow)(window, "' . $this->View->frameset . '&pnt=qsend&start="+start+"&grp="+group,"send_question",-1,-1,400,200,true,true,true,false);
}

function doSend(start,group){
	self.focus();
	top.send_cmd.location=WE().consts.dirs.WEBEDITION_DIR + "we_showMod.php?mod=newsletter&pnt=send_cmd&nid=' . $nid . '&test=' . $test . '&blockcache=' . $ret["blockcache"] . '&emailcache=' . $ret["emailcache"] . '&ecount=' . $ret["ecount"] . '&gcount=' . $ret["gcount"] . '&start="+start+"&egc="+group;
}
self.focus();
');

		$body = we_html_element::htmlIFrame('send_body', WEBEDITION_DIR . 'we_showMod.php?mod=newsletter&pnt=send_body', 'position:absolute;top:0px;bottom:0px;left:0px;right:0px;', '', '', false) .
			we_html_element::htmlIFrame('send_cmd', WEBEDITION_DIR . 'we_showMod.php?mod=newsletter&pnt=send_cmd', 'position:absolute;width:0px;height:0px;', '', '', false) .
			we_html_element::htmlIFrame('send_control', WEBEDITION_DIR . 'we_showMod.php?mod=newsletter&pnt=send_control&nid=' . $nid . '&test=' . $test . '&blockcache=' . $ret["blockcache"] . '&emailcache=' . $ret["emailcache"] . '&ecount=' . $ret["ecount"] . '&gcount=' . $ret["gcount"], 'position:absolute;width:0px;height:0px;', '', '', false)
		;
		return $this->getHTMLDocument(we_html_element::htmlBody(array("onload" => (($this->View->newsletter->Step != 0 || $this->View->newsletter->Offset != 0) ? "ask(" . $this->View->newsletter->Step . "," . $this->View->newsletter->Offset . ");" : "no();")), $body), $head);
	}

	function getHTMLSendBody(){
		$pb = new we_progressBar(we_base_request::_(we_base_request::INT, "pro", 0));
		$pb->setStudLen(400);
		$pb->addText(g_l('modules_newsletter', '[sending]'), 0, "title");

		$footer = '<table style="width:580px;" class="default"><tr><td style="text-align:left">' .
			$pb->getHTML() . '</td><td style="text-align:right">' .
			we_html_button::create_button(we_html_button::CLOSE, "javascript:top.close();") .
			'</td></tr></table>';

		return $this->getHTMLDocument(
				we_html_element::htmlBody(array('class' => "weDialogBody"), we_html_element::htmlForm(array('name' => 'we_form', "method" => "post"), $pb->getJSCode() .
						we_html_tools::htmlDialogLayout(we_html_element::htmlTextarea(array('name' => "details", "cols" => 60, "rows" => 15, "style" => "width:530px;height:280px;")), g_l('modules_newsletter', '[details]'), $footer)
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


		echo $this->getHTMLDocument(we_html_element::htmlBody(['style' => 'margin:10px;', "onload" => "initControl()"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post"], we_html_element::htmlHiddens(['mod' => 'newsletter',
						"nid" => $nid,
						"pnt" => "send_cmd",
						"test" => $test,
						"blockcache" => $blockcache,
						"emailcache" => $emailcache,
						"ecount", "value" => $ecount,
						"gcount" => $gcount,
						"egc" => $egc + 1,
						"ecs" => $ecs,
						"reload" => 1])
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
					$buffer = we_unserialize(we_base_file::load(WE_NEWSLETTER_CACHE_DIR . $blockcache . "_h_" . $cc));
					if(is_array($buffer) && isset($buffer['inlines'])){
						foreach($buffer['inlines'] as $fn){
							if(file_exists($fn)){
								we_base_file::delete($fn);
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
			$this->View->db->query('UPDATE ' . NEWSLETTER_TABLE . ' SET Step=0,Offset=0 WHERE ID=' . $this->View->newsletter->ID);
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

			$inlines = $atts = [];

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
				$content = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentF) : $contentF), $rep);
				$content_plain = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentF_plain) : $contentF_plain), $rep);
			} else if($salutation && $lastname && ($salutation == $this->View->settings[we_newsletter_newsletter::MALE_SALUTATION_FIELD]) && ((!$this->View->settings["title_or_salutation"]) || (!$title))){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);

				$content = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentM) : $contentM), $rep);
				$content_plain = strtr(($title ? preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentM_plain) : $contentM_plain), $rep);
			} else if($title && $firstname && $lastname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);
				$content = strtr(preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentTFL), $rep);
				$content_plain = strtr(preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentTFL_plain), $rep);
			} else if($title && $lastname){
				$rep = array(
					'###FIRSTNAME###' => $firstname,
					'###LASTNAME###' => $lastname,
					'###CUSTOMERID###' => $customerid,
					'###TITLE###' => $title
				);
				$content = strtr(preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentTL), $rep);
				$content_plain = strtr(preg_replace('|([^ ])###TITLE###|', '${1} ' . $title, $contentTL_plain), $rep);
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

			//$clean = $this->View->getCleanMail($this->View->newsletter->Reply);

			$not_black = !$this->View->isBlack($email); //Bug #5791 Prüfung muss vor der aufbereitung der Adresse erfolgen
			if($lastname && $firstname || $title && $lastname){
				$emailName = ($title ? $title . ' ' : '') .
					($firstname ? $firstname . ' ' : '') .
					$lastname . '<' . $email . '>';
				//$email = $emailName;
			} else {
				$emailName = $email;
			}
			$phpmail = new we_mail_mail($emailName, $this->View->newsletter->Subject, $this->View->newsletter->Sender, $this->View->newsletter->Reply, $this->View->newsletter->isEmbedImages);
			//FIXME: where is $GLOBALS["language"]["charset"] set?
			$phpmail->setCharSet($this->View->newsletter->Charset ? : $GLOBALS["language"]["charset"]);

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
				'setTimeout(function (doc) {doc.we_form.submit();},' . $this->View->settings["send_wait"] . ',document);' :
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

		$to = (is_numeric($this->View->settings["send_wait"]) ? $this->View->settings["send_wait"] : 0) + 40000;

		echo $this->getHTMLDocument(we_html_element::htmlBody(["style" => 'margin:10px', "onload" => "startTimeout();"], we_html_element::htmlForm(['name' => 'we_form', "method" => "post", "target" => "send_cmd", "action" => $this->frameset], we_html_element::htmlHiddens(['mod' => 'newsletter',
						"nid" => $nid,
						"pnt" => "send_cmd",
						"retry" => 1,
						"test" => 0,
						"blockcache" => $blockcache,
						"emailcache" => $emailcache,
						"ecount" => $ecount,
						"gcount" => $gcount,
						"ecs" => $ecs,
						"reload" => 0])
				)
			), we_html_element::jsScript(WE_JS_MODULES_DIR . 'sendControl.js', 'self.focus();', ['id' => 'loadVarSendControl', 'data-control' => setDynamicVar([
					'to' => $to
		])]));
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
	private function getHTMLCharsetTable(){
		$value = (isset($this->View->newsletter->Charset) ? $this->View->newsletter->Charset : "");

		$charsetHandler = new we_base_charsetHandler();

		$charsets = $charsetHandler->getCharsetsForTagWizzard();
		asort($charsets);
		reset($charsets);

		$table = new we_html_table([], 1, 2);
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
		$placeholderfieldsmatches = [];
		preg_match_all('/####PLACEHOLDER:DB::CUSTOMER_TABLE:(.[^#]{1,200})####/', $content, $placeholderfieldsmatches);
		$placeholderfields = $placeholderfieldsmatches[1];
		unset($placeholderfieldsmatches);

		$fromCustomer = (is_array($customerInfos) && isset($customerInfos[8]) && isset($customerInfos[9]) && $customerInfos[9] === 'customer' ?
				array_merge(getHash('SELECT * FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($customerInfos[8]), $this->View->db), we_customer_customer::getEncryptedFields()) :
				[]);

		foreach($placeholderfields as $phf){
			$placeholderReplaceValue = $fromCustomer && isset($fromCustomer[$phf]) ? $fromCustomer[$phf] : "";
			$content = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $content);
			$content_plain = str_replace('####PLACEHOLDER:DB::CUSTOMER_TABLE:' . $phf . '####', $placeholderReplaceValue, $content_plain);
		}
	}

}
