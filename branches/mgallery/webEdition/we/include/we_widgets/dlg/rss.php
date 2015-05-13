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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once (WE_INCLUDES_PATH . 'we_widgets/dlg/prefs.inc.php');

we_html_tools::protect();

function htmlClipElement($smalltext, $text, $content){
	$unique = md5(uniqid(__FUNCTION__, true)); // #6590, changed from: uniqid(microtime())
	$js = we_html_element::jsElement('
		var state_' . $unique . '=0;
		function clip_' . $unique . '(){
			var text_' . $unique . '="' . addslashes($text) . '";
			var textsmall_' . $unique . ' = "' . addslashes($smalltext) . '";
			var oText=gel("' . $unique . '");
			var oDiv=gel("div_' . $unique . '");
			var oBtn=gel("btn_' . $unique . '");

			if(state_' . $unique . '==0){
				oText.innerHTML=text_' . $unique . ';
				oDiv.style.display="block";
				oBtn.innerHTML=\'<button class="weBtn" onclick="clip_' . $unique . '();">' .
			we_html_element::htmlImg(array(
				"src" => BUTTONS_DIR . "/icons/direction_down.gif", "border" => 0
			)) . '</button>\';
				state_' . $unique . '=1;
			}else{
				oText.innerHTML=textsmall_' . $unique . ';
				oDiv.style.display="none";
				oBtn.innerHTML=\'<button class="weBtn" onclick="clip_' . $unique . '();">' .
			we_html_element::htmlImg(array(
				"src" => BUTTONS_DIR . "/icons/direction_right.gif", "border" => 0
			)) . '</button>\';
				state_' . $unique . '=0;
			}
		}
	');

	$oClip = new we_html_table(array(
		"border" => 0, "cellpadding" => 0, "cellspacing" => 0
		), 1, 3);
	$oClip->setCol(
		0, 0, array(
		"width" => 21, "valign" => "top", "align" => "right", "id" => "btn_" . $unique
		), '<button class="weBtn" onclick="clip_' . $unique . '();">' .
		we_html_element::htmlImg(array(
			"src" => BUTTONS_DIR . "/icons/direction_right.gif", "border" => 0
		)) . '</button>');
	$oClip->setCol(0, 1, array(
		"width" => 10, "nowrap" => "nowrap"
		), we_html_tools::getPixel(10, 1));
	$oClip->setCol(
		0, 2, null, we_html_element::htmlSpan(
			array(
			"id" => $unique,
			"class" => "defaultfont",
			"style" => "cursor:pointer;",
			"onclick" => "clip_" . $unique . "();"
			), addslashes($smalltext)));

	return $js . $oClip->getHTML() . we_html_element::htmlDiv(array(
			"id" => "div_" . $unique, "style" => "display:none;"
			), we_html_tools::getPixel(1, 15) . we_html_element::htmlBr() . $content);
}

$oIptUri = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput("ipt_uri", 55, "", 255, 'title=""', "text", 380, 0), g_l('cockpit', '[url]'), "left", "defaultfont");

$oSctRss = new we_html_select(
	array(
	"name" => "sct_rss", "size" => 1, "class" => "defaultfont", "onchange" => "onChangeSctRss(this);"
	));
$oSctRss->insertOption(0, "", "");
$oTblSctRss = we_html_tools::htmlFormElementTable($oSctRss->getHTML(), g_l('cockpit', '[rss_top_feeds]'), 'left', 'defaultfont');

$oRemTopFeeds = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_edit_rem]'), we_html_tools::TYPE_INFO, 380);
$oIptNewTitle = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('ipt_newTitle', 55, "", 255, "", "text", 380, 0), g_l('cockpit', '[title]'), "left", "defaultfont");
$oIptNewUri = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('ipt_newUri', 55, "", 255, "", "text", 380, 0), g_l('cockpit', '[url]'), "left", "defaultfont");
$btnAddTopRssFeed = we_html_button::create_button(we_html_button::ADD, "javascript:handleTopRssFeed('add');", false, 0, 0, "", "", false, false);
$btnOverwriteTopRssFeed = we_html_button::create_button("overwrite", "javascript:handleTopRssFeed('overwrite');", false, 0, 0, "", "", false, false);
$btnDeleteTopRssFeed = we_html_button::create_button("delete", "javascript:handleTopRssFeed('delete');", false, 0, 0, "", "", false, false);

$oBtnNewFeed = new we_html_table(array("border" => 0, "cellpadding" => 0, "cellspacing" => 0), 1, 5);
$oBtnNewFeed->setCol(0, 0, null, $btnAddTopRssFeed);
$oBtnNewFeed->setCol(0, 1, null, we_html_tools::getPixel(10, 1));
$oBtnNewFeed->setCol(0, 2, null, $btnOverwriteTopRssFeed);
$oBtnNewFeed->setCol(0, 3, null, we_html_tools::getPixel(10, 1));
$oBtnNewFeed->setCol(0, 4, null, $btnDeleteTopRssFeed);

$oNewFeed = new we_html_table(array(
	"width" => 390, "border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 3, 1);
$oNewFeed->setCol(
	0, 0, null, $oRemTopFeeds . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oIptNewTitle . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oIptNewUri);
$oNewFeed->setCol(1, 0, null, we_html_tools::getPixel(1, 5));
$oNewFeed->setCol(2, 0, array(
	"align" => "right"
	), $oBtnNewFeed->getHTML());

$rssUri = $oIptUri . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . $oTblSctRss . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_edit_toprssfeeds]'), g_l('cockpit', '[hide_edit_toprssfeeds]'), $oNewFeed->getHTML());

$oRemRssConf = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_content_rem]'), we_html_tools::TYPE_INFO, 410);
$oChbxContTitle = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[title]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxContLink = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[link]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxContDesc = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[desc]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxContEnc = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[content_encoded]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxContPubDate = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[pubdate]'), true, "defaultfont", "", false, "", 0, 0);
$oChbxContCategory = we_html_forms::checkbox(0, 0, "chbx_conf", g_l('cockpit', '[category]'), true, "defaultfont", "", false, "", 0, 0);
$oSctNumEntries = new we_html_select(array("name" => "sct_conf", "size" => 1, "class" => "defaultfont"));
$oSctNumEntries->insertOption(0, 0, g_l('cockpit', '[no]'));

for($iCurrEntry = 1; $iCurrEntry <= 50; $iCurrEntry++){
	$oSctNumEntries->insertOption($iCurrEntry, $iCurrEntry, $iCurrEntry);
	if($iCurrEntry >= 10){
		$iCurrEntry += ($iCurrEntry == 25) ? 24 : 4;
	}
}

$oRssContR = new we_html_table(array(
	"height" => "100%", "border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 2, 3);
$oRssContR->setCol(0, 0, array(
	"valign" => "middle", "class" => "defaultfont"
	), g_l('cockpit', '[limit_entries]'));
$oRssContR->setCol(0, 1, null, we_html_tools::getPixel(5, 1));
$oRssContR->setCol(0, 2, array(
	"valign" => "middle"
	), $oSctNumEntries->getHTML());
$oRssContR->setCol(1, 0, array(
	"colspan" => 3, "valign" => "bottom"
	), $oChbxContPubDate . $oChbxContCategory);

$oSelectRssCont = new we_html_table(array(
	"border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 1, 2);
$oSelectRssCont->setCol(0, 0, array(
	"width" => 165
	), $oChbxContTitle . $oChbxContLink . $oChbxContDesc . $oChbxContEnc);
$oSelectRssCont->setCol(0, 1, array(
	"height" => "100%", "valign" => "top"
	), $oRssContR->getHTML());

$rssConf = $oRemRssConf . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_select_rsscontent]'), g_l('cockpit', '[hide_select_rsscontent]'), $oSelectRssCont->getHTML());

$oRemLabel = we_html_tools::htmlAlertAttentionBox(g_l('cockpit', '[rss_label_rem]'), we_html_tools::TYPE_INFO, 410);
$oChbxTb[0] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[label_rssfeed]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[1] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[title]'), $uniqid = true, $class = "defaultfont", $onClick = "onDisableRdoGroup('title');", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[2] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[desc]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[3] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[link_url]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[4] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[pubdate]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oChbxTb[5] = we_html_forms::checkbox($value = "", $checked = 0, $name = "chbx_tb", $text = g_l('cockpit', '[copyright]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $width = 0);
$oRdoTitle[0] = we_html_forms::radiobutton($value = 1, $checked = 0, $name = "rdo_title", $text = g_l('cockpit', '[original_of_rssfeed]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");
$oRdoTitle[1] = we_html_forms::radiobutton($value = 0, $checked = 0, $name = "rdo_title", $text = g_l('cockpit', '[personalized]'), $uniqid = true, $class = "defaultfont", $onClick = "", $disabled = false, $description = "", $type = 0, $onMouseUp = "");

$oTitleTb = new we_html_table(array(
	"border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 2, 1);
$oTitleTb->setCol(0, 0, array(
	"width" => 165
	), $oRdoTitle[0]);
$oTitleTb->setCol(1, 0, array(
	"width" => 165
	), $oRdoTitle[1]);

$oEditTb = new we_html_table(array(
	"border" => 0, "cellpadding" => 0, "cellspacing" => 0
	), 6, 2);
$oEditTb->setCol(0, 0, array(
	"width" => 165
	), $oChbxTb[0]);
$oEditTb->setCol(1, 0, array(
	"width" => 165, "valign" => "top"
	), $oChbxTb[1]);
$oEditTb->setCol(1, 1, array(
	"width" => 165
	), $oTitleTb->getHTML());
$oEditTb->setCol(2, 0, array(
	"width" => 165
	), $oChbxTb[2]);
$oEditTb->setCol(3, 0, array(
	"width" => 165
	), $oChbxTb[3]);
$oEditTb->setCol(4, 0, array(
	"width" => 165
	), $oChbxTb[4]);
$oEditTb->setCol(5, 0, array(
	"width" => 165
	), $oChbxTb[5]);

$rssLabel = $oRemLabel . we_html_tools::getPixel(1, 5) . we_html_element::htmlBr() . htmlClipElement(
		g_l('cockpit', '[show_edit_titlebar]'), g_l('cockpit', '[hide_edit_titlebar]'), $oEditTb->getHTML());

$parts = array(
	array(
		"headline" => "", "html" => $rssUri, "space" => 0
	),
	array(
		"headline" => "", "html" => $rssConf, "space" => 0
	),
	array(
		"headline" => "", "html" => $rssLabel, "space" => 0
	),
	array(
		"headline" => "", "html" => $oSelCls->getHTML(), "space" => 0
	)
);

$save_button = we_html_button::create_button('save', 'javascript:save();', false, 0, 0);
$preview_button = we_html_button::create_button('preview', 'javascript:preview();', false, 0, 0);
$cancel_button = we_html_button::create_button('close', 'javascript:exit_close();');
$buttons = we_html_button::position_yes_no_cancel($save_button, $preview_button, $cancel_button);

$sTblWidget = we_html_multiIconBox::getHTML('rssProps', '100%', $parts, 30, $buttons, -1, '', '', '', g_l('cockpit', '[rss_feed]'), '', 439);

echo we_html_element::htmlDocType() .
 we_html_element::htmlHtml(
	we_html_element::htmlHead(
		we_html_tools::getHtmlInnerHead(g_l('cockpit', '[rss_feed]')) .
		STYLESHEET .
		we_html_element::jsScript(JS_DIR . 'we_showMessage.js') .
		we_html_element::jsElement($jsPrefs . "
var g_l={
	'prefs_saved_successfully': '" . we_message_reporting::prepareMsgForJS(g_l('cockpit', '[prefs_saved_successfully]')) . "',
	'invalid_url': '" . we_message_reporting::prepareMsgForJS(g_l('cockpit', '[invalid_url]')) . "'
};") .
		we_html_element::jsScript(JS_DIR . 'widgets/rss.js')
	) .
	we_html_element::htmlBody(
		array('class' => 'weDialogBody', 'onload' => 'init();'), we_html_element::htmlForm("", $sTblWidget)
));
