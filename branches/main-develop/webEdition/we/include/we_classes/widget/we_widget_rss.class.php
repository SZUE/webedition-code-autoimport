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
class we_widget_rss extends we_widget_base{
	private $rssDat = [];

	public function __construct($curID = '', $aProps = []){
		$this->rssDat = $aProps[3];
	}

	public function getInsertDiv($iCurrId, we_base_jsCmd $jsCmd){
		if(!empty($this->rssDat)){
			list($rssUri, $rssCont, $rssNumItems, $rssTb, $rssTitle) = explode(',', $this->rssDat);
		} else {//use default if data is corrupt
			list($rssUri, $rssCont, $rssNumItems, $rssTb, $rssTitle) = [base64_encode('http://www.webedition.org/de/rss/webedition.xml'), '111000', 0, '110000', 1];
		}

		list($bTbLabel, $bTbTitel, $bTbDesc, $bTbLink, $bTbPubDate, $bTbCopyright) = $rssTb;
		$aLabelPrefix = [];

		if($bTbTitel && $rssTitle){
			$feed = (isset($aTrf)) ? $aTrf : self::getTopFeeds();
			foreach($feed as $iRssFeedIndex => $aFeed){
				if($rssUri == $aFeed[1]){
					$aLabelPrefix[] = base64_decode($aFeed[0]);
					break;
				}
			}
		}
		$sTbPrefix = implode(' - ', $aLabelPrefix);
		$aLang = [$sTbPrefix, ''];

		$oTblDiv = '<div class="rssDiv middlefont" id="m_' . $iCurrId . '_inline" style="width:100%;height:287px ! important; overflow: auto;"></div>';
		$jsCmd->addCmd('loadRSS', [base64_decode($rssUri), $rssCont, $rssNumItems, $rssTb, $sTbPrefix, 'm_' . $iCurrId]);
		return [$oTblDiv, $aLang];
	}

	public static function getTopFeeds(){
		$aTopRssFeeds = g_l('topFeeds', '');
		foreach($aTopRssFeeds as &$curTopFeed){
			foreach($curTopFeed as &$cur){
				$cur = base64_encode($cur);
			}
			return $aTopRssFeeds;
		}
	}

	public static function getDefaultConfig(){
		return [
			'width' => self::WIDTH_SMALL,
			'expanded' => 0,
			'height' => 307,
			'res' => 0,
			'cls' => 'yellow',
			'csv' => base64_encode('http://www.webedition.org/de/feeds/aktuelles.xml') . ',111000,0,110000,1',
			'dlgHeight' => 480,
			'isResizable' => 1
		];
	}

	private static function htmlClipElement(array &$dynvar, $smalltext, $text, $content){
		static $unique = 0;
		$unique++;
		$dynvar['clip'][$unique] = [
			'state' => 0,
			'text' => $text,
			'textsmall' => $smalltext
		];

		$oClip = new we_html_table(['class' => 'default'], 1, 3);
		$oClip->setCol(0, 0, ['width' => 21, 'style' => 'vertical-align:top;text-align:right', "id" => "btn_" . $unique], we_html_button::create_button(we_html_button::DIRRIGHT, 'javascript:clip(' . $unique . ');'));
		$oClip->setCol(0, 1, ['width' => 10]);
		$oClip->setCol(0, 2, null, we_html_element::htmlSpan(["id" => 'clip_'.$unique,
				'class' => 'defaultfont',
				'style' => "cursor:pointer;",
				"onclick" => "clip(" . $unique . ");"
				], addslashes($smalltext)));

		return  $oClip->getHTML() . we_html_element::htmlDiv(["id" => "div_" . $unique, 'style' => "display:none;"], we_html_element::htmlBr() . $content);
	}

	public static function showDialog(){
		$dynVar = [];
		$oIptUri = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("ipt_uri", 55, "", 255, 'title=""', "text", 380, 0), g_l('cockpit', '[url]'), "left", "defaultfont");

		$oSctRss = new we_html_select(['name' => "sct_rss", 'class' => 'defaultfont', "onchange" => "onChangeSctRss(this);"]);
		$oSctRss->insertOption(0, "", "");
		$oTblSctRss = we_html_tools::htmlFormElementTable($oSctRss->getHTML(), g_l('cockpit', '[rss_top_feeds]'), 'left', 'defaultfont');

		$oRemTopFeeds = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_edit_rem]'), we_html_tools::TYPE_INFO, 380);
		$oIptNewTitle = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('ipt_newTitle', 55, "", 255, "", "text", 380, 0), g_l('cockpit', '[title]'), "left", "defaultfont");
		$oIptNewUri = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('ipt_newUri', 55, "", 255, "", "text", 380, 0), g_l('cockpit', '[url]'), "left", "defaultfont");
		$btnAddTopRssFeed = we_html_button::create_button(we_html_button::ADD, "javascript:handleTopRssFeed('add');", '', 0, 0, "", "", false, false);
		$btnOverwriteTopRssFeed = we_html_button::create_button('overwrite', "javascript:handleTopRssFeed('overwrite');", '', 0, 0, "", "", false, false);
		$btnDeleteTopRssFeed = we_html_button::create_button(we_html_button::DELETE, "javascript:handleTopRssFeed('delete');", '', 0, 0, "", "", false, false);

		$oBtnNewFeed = new we_html_table(['class' => 'default'], 1, 5);
		$oBtnNewFeed->setCol(0, 0, null, $btnAddTopRssFeed);
		$oBtnNewFeed->setCol(0, 1, ['style' => 'width:10px;']);
		$oBtnNewFeed->setCol(0, 2, null, $btnOverwriteTopRssFeed);
		$oBtnNewFeed->setCol(0, 3, ['style' => 'width:10px;']);
		$oBtnNewFeed->setCol(0, 4, null, $btnDeleteTopRssFeed);

		$oNewFeed = new we_html_table(['width' => 390, 'class' => 'default'], 3, 1);
		$oNewFeed->setCol(0, 0, null, $oRemTopFeeds . we_html_element::htmlBr() . $oIptNewTitle . we_html_element::htmlBr() . $oIptNewUri);
		$oNewFeed->setCol(1, 0, ['style' => 'width:5px;']);
		$oNewFeed->setCol(2, 0, ['style' => "text-align:right"], $oBtnNewFeed->getHTML());

		$rssUri = $oIptUri . we_html_element::htmlBr() . $oTblSctRss .
			we_html_element::htmlBr() .
			self::htmlClipElement($dynVar, g_l('cockpit', '[show_edit_toprssfeeds]'), g_l('cockpit', '[hide_edit_toprssfeeds]'), $oNewFeed->getHTML());

		$oRemRssConf = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_content_rem]'), we_html_tools::TYPE_INFO, 410);
		$oChbxContTitle = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[title]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxContLink = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[link]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxContDesc = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[desc]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxContEnc = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[content_encoded]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxContPubDate = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[pubdate]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxContCategory = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[category]'), true, "defaultfont", "", false, "", 0, 0);
		$oSctNumEntries = new we_html_select(['name' => "sct_conf", 'class' => 'defaultfont']);
		$oSctNumEntries->insertOption(0, 0, g_l('cockpit', '[no]'));

		for($iCurrEntry = 1; $iCurrEntry <= 50; $iCurrEntry++){
			$oSctNumEntries->insertOption($iCurrEntry, $iCurrEntry, $iCurrEntry);
			if($iCurrEntry >= 10){
				$iCurrEntry += ($iCurrEntry == 25) ? 24 : 4;
			}
		}

		$oRssContR = new we_html_table(["height" => "100%", 'class' => 'default'], 2, 3);
		$oRssContR->setCol(0, 0, ['style' => 'vertical-align:middle;', 'class' => 'defaultfont'], g_l('cockpit', '[limit_entries]'));
		$oRssContR->setCol(0, 1, ['style' => 'width:5px;']);
		$oRssContR->setCol(0, 2, ['style' => 'vertical-align:middle;'], $oSctNumEntries->getHTML());
		$oRssContR->setCol(1, 0, ["colspan" => 3, 'style' => 'vertical-align:bottom;'], $oChbxContPubDate . $oChbxContCategory);

		$oSelectRssCont = new we_html_table(['class' => 'default'], 1, 2);
		$oSelectRssCont->setCol(0, 0, ['width' => 165], $oChbxContTitle . $oChbxContLink . $oChbxContDesc . $oChbxContEnc);
		$oSelectRssCont->setCol(0, 1, ["height" => "100%", 'style' => 'vertical-align:top;'], $oRssContR->getHTML());

		$rssConf = $oRemRssConf . we_html_element::htmlBr() . self::htmlClipElement($dynVar, g_l('cockpit', '[show_select_rsscontent]'), g_l('cockpit', '[hide_select_rsscontent]'), $oSelectRssCont->getHTML());

		$oRemLabel = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_label_rem]'), we_html_tools::TYPE_INFO, 410);
		$oChbxTb[0] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[label_rssfeed]'), true, "defaultfont", "", false, "", 0, 0);
		$oChbxTb[1] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[title]'), true, "defaultfont", "onDisableRdoGroup('title');", false, "", 0, $width = 0);
		$oChbxTb[2] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[desc]'), true, "defaultfont", "", false, "", 0, $width = 0);
		$oChbxTb[3] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[link_url]'), true, "defaultfont", "", false, "", 0, $width = 0);
		$oChbxTb[4] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[pubdate]'), true, "defaultfont", "", false, "", 0, $width = 0);
		$oChbxTb[5] = we_html_forms::checkbox("", 0, "chbx_tb", g_l('cockpit', '[copyright]'), true, "defaultfont", "", false, "", 0, $width = 0);
		$oRdoTitle[0] = we_html_forms::radiobutton(1, 0, "rdo_title", g_l('cockpit', '[original_of_rssfeed]'), true, "defaultfont", "", false, "", 0, "");
		$oRdoTitle[1] = we_html_forms::radiobutton(0, 0, "rdo_title", g_l('cockpit', '[personalized]'), true, "defaultfont", "", false, "", 0, "");

		$oTitleTb = new we_html_table(['class' => 'default'], 2, 1);
		$oTitleTb->setCol(0, 0, ['width' => 165], $oRdoTitle[0]);
		$oTitleTb->setCol(1, 0, ['width' => 165], $oRdoTitle[1]);

		$oEditTb = new we_html_table(['class' => 'default'], 6, 2);
		$oEditTb->setCol(0, 0, ['width' => 165], $oChbxTb[0]);
		$oEditTb->setCol(1, 0, ['width' => 165, 'style' => 'vertical-align:top;'], $oChbxTb[1]);
		$oEditTb->setCol(1, 1, ['width' => 165], $oTitleTb->getHTML());
		$oEditTb->setCol(2, 0, ['width' => 165], $oChbxTb[2]);
		$oEditTb->setCol(3, 0, ['width' => 165], $oChbxTb[3]);
		$oEditTb->setCol(4, 0, ['width' => 165], $oChbxTb[4]);
		$oEditTb->setCol(5, 0, ['width' => 165], $oChbxTb[5]);

		$rssLabel = $oRemLabel . we_html_element::htmlBr() . self::htmlClipElement($dynVar, g_l('cockpit', '[show_edit_titlebar]'), g_l('cockpit', '[hide_edit_titlebar]'), $oEditTb->getHTML());

		list($jsFile, $oSelCls) = self::getDialogPrefs($dynVar);

		$buttons = we_html_button::position_yes_no_cancel(we_html_button::create_button(we_html_button::SAVE, 'javascript:save();'), we_html_button::create_button(we_html_button::PREVIEW, 'javascript:preview();'), we_html_button::create_button(we_html_button::CLOSE, 'javascript:exit_close();'));

		$sTblWidget = we_html_multiIconBox::getHTML('rssProps', [
				["html" => $rssUri,],
				["html" => $rssConf,],
				["html" => $rssLabel,],
				["html" => $oSelCls->getHTML(),]
				], 30, $buttons, -1, '', '', '', g_l('cockpit', '[rss_feed]'));

		echo we_html_tools::getHtmlTop(g_l('cockpit', '[rss_feed]'), '', '', $jsFile .
			we_html_element::jsScript(JS_DIR . 'widgets/rss.js'), we_html_element::htmlBody(
				['class' => 'weDialogBody', 'onload' => 'init();'], we_html_element::htmlForm("", $sTblWidget)
		));
	}

	public function showPreview(){
		echo we_html_tools::getHtmlTop('', '', '', we_base_jsCmd::singleCmd('loadRSS', we_base_request::_(we_base_request::STRING, 'we_cmd'))
			, we_html_element::htmlBody(['onload' => 'init()']));
	}

	public static function getRSSContent($sRssUri, $sCfgBinary, $iNumItems, $sTbBinary, $tmpTitle){

		$bCfgTitle = (bool) $sCfgBinary{0};
		$bCfgLink = (bool) $sCfgBinary{1};
		$bCfgDesc = (bool) $sCfgBinary{2};
		$bCfgContEnc = (bool) $sCfgBinary{3};
		$bCfgPubDate = (bool) $sCfgBinary{4};
		$bCfgCategory = (bool) $sCfgBinary{5};
		switch($iNumItems){
			case 11:
				$iNumItems = 15;
				break;
			case 12:
				$iNumItems = 20;
				break;
			case 13:
				$iNumItems = 25;
				break;
			case 14:
				$iNumItems = 50;
				break;
		}

		$bTbLabel = (bool) $sTbBinary{0};
		$bTbTitel = (bool) $sTbBinary{1};
		$bTbDesc = (bool) $sTbBinary{2};
		$bTbLink = (bool) $sTbBinary{3};
		$bTbPubDate = (bool) $sTbBinary{4};
		$bTbCopyright = (bool) $sTbBinary{5};

		//Bug 6119: Keine Unterstützung für curl in der XML_RSS Klasse
		//daher Umstellung den Inhalt des Feeds selbst zu holen
		$parsedurl = parse_url($sRssUri);
		$http_request = new we_http_request($parsedurl['path'], $parsedurl['host'], 'GET');
		$http_request->executeHttpRequest();
		$http_response = new we_http_response($http_request->getHttpResponseStr());
		while(isset($http_response->http_headers['Location'])){//eine Weiterleitung ist aktiv
			$parsedurl = parse_url($http_response->http_headers['Location']);
			$http_request = new we_http_request($parsedurl['path'], $parsedurl['host'], 'GET');
			$http_request->executeHttpRequest();
			$http_response = new we_http_response($http_request->getHttpResponseStr());
		}
		$feeddata = $http_response->http_body;

		$oRssParser = new we_xml_rss($feeddata, null, 'UTF-8'); // Umstellung in der XML_RSS-Klasse: den string, und nicht die url weiterzugeben
		$tmp = $oRssParser->parse();
		$sRssOut = "";

		$iCurrItem = 0;
		foreach($oRssParser->getItems() as $item){
			$bShowTitle = ($bCfgTitle && isset($item['title']));
			$bShowLink = ($bCfgLink && isset($item['link']));
			$bShowDesc = ($bCfgDesc && isset($item['description']));
			$bShowContEnc = ($bCfgContEnc && isset($item['content:encoded']));
			$bShowPubdate = ($bCfgPubDate && isset($item['pubdate']));
			$bShowCategory = ($bCfgCategory && isset($item['category']));

			$sLink = (($bCfgLink && isset($item['link'])) && !$bShowTitle) ? " &nbsp;" .
				we_html_element::htmlA(['href' => $item['link'], 'target' => '_blank'], g_l('cockpit', '[more]')) : "";
			if($bShowContEnc){
				$contEnc = new we_html_table(['class' => 'default'], 1, 1);
				$contEnc->setCol(0, 0, null, $item['content:encoded'] . ((!$bCfgDesc) ? $sLink : ""));
			}

			$sRssOut .= ($bShowTitle ?
				($bShowLink ? we_html_element::htmlA(["href" => $item['link'], "target" => "_blank"], we_html_element::htmlB($item['title'])) :
				we_html_element::htmlB($item['title'])) .
				($bShowPubdate ? ' ' : we_html_element::htmlBr()) :
				'') .
				($bShowPubdate ?
				g_l('cockpit', '[published]') . ': ' . date(g_l('date', '[format][default]'), strtotime($item['pubdate'])) :
				'') .
				($bShowCategory ?
				($bShowPubdate ? we_html_element::htmlBr() : "") .
				g_l('cockpit', '[category]') . ": " . $item['category'] :
				'') .
				($bShowPubdate || $bShowCategory ?
				we_html_element::htmlBr() :
				'') .
				($bShowDesc ?
				$item['description'] . $sLink . we_html_element::htmlBr() :
				"") .
				($bShowContEnc ?
				$contEnc->getHTML() :
				(!$bShowDesc ?
				$sLink . we_html_element::htmlBr() :
				'')
				) .
				($bShowDesc || $bShowContEnc ?
				we_html_element::htmlBr() :
				"");
			if($iNumItems){
				$iCurrItem++;
				if($iCurrItem == $iNumItems){
					break;
				}
			}
		}

		$aTb = [];
		if($bTbLabel){
			$aTb[] = g_l('cockpit', '[rss_feed]');
		}
		if($bTbTitel){
			$aTb[] = $tmpTitle ?: ((isset($oRssParser->channel["title"])) ? $oRssParser->channel["title"] : "");
		}
		if($bTbDesc){
			$aTb[] = (isset($oRssParser->channel["description"])) ? str_replace(["\n", "\r"], '', $oRssParser->channel["description"]) : '';
		}
		if($bTbLink){
			$aTb[] = (isset($oRssParser->channel["link"])) ? $oRssParser->channel["link"] : '';
		}
		if($bTbPubDate){
			$aTb[] = (isset($oRssParser->channel["pubdate"])) ? (date(g_l('date', '[format][default]'), strtotime($oRssParser->channel["pubdate"]))) : "";
		}
		if($bTbCopyright){
			$aTb[] = (isset($oRssParser->channel["copyright"])) ? $oRssParser->channel["copyright"] : "";
		}
		$title = implode(' - ', $aTb);
		return [$title, $sRssOut];
	}

}
