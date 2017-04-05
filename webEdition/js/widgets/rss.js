/* global WE, top, prefs */

/**
 * webEdition CMS
 *
 * webEdition CMS
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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
'use strict';
var aTopRssFeeds = opener.cockpit._trf;
var _iTopRssFeedsLen = aTopRssFeeds.length;
var _bIsHotTopRssFeeds = false;
var _sInitUri;
var _sLastPreviewUri = '';
var _sInitRssCfg = '';
var _iInitRssCfgNumEntries = 0;
var _sInitTbCfg = '';
var _iInitTbTitlePers = 0;

function isUrl(s) {
	var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
	return regexp.test(s);
}

function handleButtonState(enable) {
	for (var i = 1; i < arguments.length; i++) {
		WE().layout.button.switch_button_state(document, arguments[i], (enable ? 'enabled' : 'disabled'));
	}
}

function toggleRssTopFeed() {
	var _fo = document.forms[0];
	var oSctRss = _fo.elements.sct_rss;
	var sUri = oSctRss.options[oSctRss.selectedIndex].value;
	var sTitle = oSctRss.options[oSctRss.selectedIndex].text;
	var oIptNewUri = _fo.elements.ipt_newUri;
	var oIptNewTitle = _fo.elements.ipt_newTitle;
	oIptNewUri.value = sUri;
	oIptNewTitle.value = sTitle;
	handleButtonState(oSctRss.selectedIndex, 'overwrite', 'delete');
}

function init() {
	var _fo = document.forms[0];
	var sCsv_ = opener.document.getElementById(prefs._sObjId + '_csv').value;
	var aCsv = sCsv_.split(',');
	var sUri = window.atob(aCsv[0]);
	_sInitUri = sUri;
	_sInitRssCfg = aCsv[1];
	var oSctRss = _fo.elements.sct_rss;
	populateSct(oSctRss);
	var iSctRssLen = oSctRss.length;
	_fo.elements.ipt_uri.value = sUri;
	_fo.elements.ipt_uri.title = sUri;

	for (var i = iSctRssLen - 1; i >= 0; i--) {
		oSctRss.options[i].selected = (oSctRss.options[i].value == sUri) ? true : false;
	}
	toggleRssTopFeed();
	var oChbxConf = _fo.elements.chbx_conf;
	var iChbxConfLen = oChbxConf.length;
	for (i = iChbxConfLen - 1; i >= 0; i--) {
		oChbxConf[i].checked = (parseInt(_sInitRssCfg.charAt(i))) ? true : false;
	}
	var oSctConf = _fo.elements.sct_conf;
	_iInitRssCfgNumEntries = aCsv[2];
	oSctConf.options[_iInitRssCfgNumEntries].selected = true;
	_sInitTbCfg = aCsv[3];
	var oChbxTb = _fo.elements.chbx_tb;
	var iChbxTbLen = oChbxTb.length;
	for (i = iChbxTbLen - 1; i >= 0; i--) {
		oChbxTb[i].checked = (parseInt(aCsv[3].charAt(i))) ? true : false;
	}
	_iInitTbTitlePers = aCsv[4];
	var oRdoTitle = _fo.elements.rdo_title;
	oRdoTitle[aCsv[4]].checked = true;
	top.initPrefs();
}

function onChangeSctRss(obj) {
	var sUri = obj.options[obj.selectedIndex].value;
	var sTitle = obj.options[obj.selectedIndex].text;
	var _fo = document.forms[0];
	toggleRssTopFeed();
	if (sUri !== '') {
		var oIptUri = _fo.elements.ipt_uri;
		oIptUri.value = sUri;
		oIptUri.title = sUri;
	}
}

function onDisableRdoGroup(sId) {
	var _fo = document.forms[0];
	var oDisable = _fo.elements['rdo_' + sId];
	var iDisableLen = oDisable.length;
	for (var i = 0; iDisableLen > i; i++) {
		oDisable[i].disabled = (!oDisable[i].disabled) ? true : false;
	}
}

function getTbPersTitle(sUri) {
	var _fo = document.forms[0];
	var oRdoTitle = _fo.elements.rdo_title;
	var sTbTitle = '';
	if (oRdoTitle[1].checked) {
		var oSctRss = _fo.elements.sct_rss;
		for (var i = 1; _iTopRssFeedsLen > i; i++) {
			if (oSctRss.options[i].value == sUri) {
				sTbTitle = oSctRss.options[i].text;
				break;
			}
		}
	}
	return sTbTitle;
}

function displayRssFeed(sUri, bOnChange) {
	var _fo = document.forms[0];
	var sRssCfgBinary = getBinary('conf');
	var sRssCfgSelIdx = _fo.elements.sct_conf.selectedIndex;
	if (!bOnChange || (_sLastPreviewUri !== '' && sUri != _sLastPreviewUri) || (_sLastPreviewUri === '' && sUri != _sInitUri) ||
		_sInitRssCfg != sRssCfgBinary || _iInitRssCfgNumEntries != sRssCfgSelIdx) {
		_sLastPreviewUri = sUri;
		var sTbBinary = getBinary('tb');
		WE().layout.cockpitFrame.executeAjaxRequest(sUri, sRssCfgBinary, sRssCfgSelIdx, sTbBinary, getTbPersTitle(sUri), prefs._sObjId);
	}
}

function resetRssFeed() {
	var _fo = document.forms[0];
	var iSctConfSel = _fo.elements.sct_conf.selectedIndex;
	var iRdoTitleSel = (_fo.elements.rdo_title.checked) ? 0 : 1;
	if ((_sLastPreviewUri !== '' && _sInitUri != _sLastPreviewUri) ||
		(getBinary('conf') != _sInitRssCfg) ||
		(getBinary('tb') != _sInitTbCfg) ||
		(_iInitRssCfgNumEntries != iSctConfSel) ||
		(_iInitTbTitlePers != iRdoTitleSel)) {
		WE().layout.cockpitFrame.executeAjaxRequest(_sInitUri, _sInitRssCfg, _iInitRssCfgNumEntries, _sInitTbCfg, getTbPersTitle(_sInitUri), prefs._sObjId);
	}
}

function getBinary(postfix) {
	var sBinary = '';
	var _fo = document.forms[0];
	var oChbx = _fo.elements['chbx_' + postfix];
	var iChbxLen = oChbx.length;
	for (var i = 0; i < iChbxLen; i++) {
		sBinary += (oChbx[i].checked) ? '1' : '0';
	}
	return sBinary;
}


function exit_close() {
	resetRssFeed();
	top.exitPrefs();
	window.close();
}

function save() {
	var _fo = document.forms[0];
	var oIptUri = _fo.elements.ipt_uri;
	var sUri = oIptUri.value;
	if (!isUrl(sUri)) {
		//return;
	}
	var oSctConf = _fo.elements.sct_conf;
	var oCsv_ = opener.document.getElementById(prefs._sObjId + '_csv');
	var oRdoTitle = _fo.elements.rdo_title;
	oCsv_.value = window.btoa(sUri) + ',' + getBinary('conf') + ',' + oSctConf.selectedIndex +
		',' + getBinary('tb') + ',' + ((oRdoTitle[0].checked) ? 0 : 1);
	if (_bIsHotTopRssFeeds) {
		var oSctRss = _fo.elements.sct_rss;
		var aNewTopRssFeeds = [];
		for (var i = 0; _iTopRssFeedsLen > i; i++) {
			aNewTopRssFeeds[i] = [window.btoa(oSctRss.options[i + 1].text),
				window.btoa(oSctRss.options[i + 1].value)];
		}
		window.opener.cockpit._trf = aNewTopRssFeeds;
		window.opener._isHotTrf = true;
	}
	window.opener.saveSettings();
	//savePrefs();
	//displayRssFeed(sUri,true);
	WE().util.showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
	WE().layout.weNavigationHistory.navigateReload();
	window.close();
}

function preview() {
	var _fo = document.forms[0];
	var oIptUri = _fo.elements.ipt_uri;
	var sUri = oIptUri.value;
	if (!isUrl(sUri)) {
		WE().util.showMessage(WE().consts.g_l.cockpit.invalid_url, WE().consts.message.WE_MESSAGE_ERROR, window);
		//return;
	}
	top.previewPrefs();
	displayRssFeed(sUri, false);
}


function handleTopRssFeed(sAction) {
	var _fo = document.forms[0];
	var oIptUri = _fo.elements.ipt_uri;
	var oSctRss = _fo.elements.sct_rss;
	var iSelIdx = oSctRss.selectedIndex;
	var oIptNewTitle = _fo.elements.ipt_newTitle;
	var sNewTitle = oIptNewTitle.value;
	var oIptNewUri = _fo.elements.ipt_newUri;
	var sNewUri = oIptNewUri.value;
	switch (sAction) {
		case 'overwrite':
			oSctRss.options[iSelIdx].text = oIptNewTitle.value;
			oSctRss.options[iSelIdx].value = oIptNewUri.value;
			oIptUri.value = oSctRss.options[iSelIdx].value;
			break;
		case 'add':
			if (sNewTitle !== '' && sNewUri !== '') {
				if (oSctRss.length <= 1) {
					var newOpt1 = new Option(sNewTitle, sNewUri);
					oSctRss.options[1] = newOpt1;
					oSctRss.selectedIndex = 1;
				} else if (iSelIdx !== -1) {
					var aSctText = [];
					var aSctValues = [];
					var iCount = -1;
					var iNewSelected = -1;
					for (var i = 0; i < oSctRss.length; i++) {
						iCount++;
						if (iCount === iSelIdx) {
							aSctText[(iSelIdx === 0 && iCount === 0) ? 1 : iCount] = sNewTitle;
							aSctValues[(iSelIdx === 0 && iCount === 0) ? 1 : iCount] = sNewUri;
							iNewSelected = (iSelIdx === 0 && iCount === 0) ? 1 : iCount;
							iCount++;
						}
						aSctText[(iSelIdx === 0 && iCount === 1) ? 0 : iCount] = oSctRss.options[i].text;
						aSctValues[(iSelIdx === 0 && iCount === 1) ? 0 : iCount] = oSctRss.options[i].value;
					}
					for (i = 0; i <= iCount; i++) {
						var newOpt = new Option(aSctText[i], aSctValues[i]);
						oSctRss.options[i] = newOpt;
						oSctRss.options[i].selected = (i === iNewSelected) ? true : false;
					}
				}
				handleButtonState(1, 'overwrite', 'delete');
				_iTopRssFeedsLen++;
			} else {
				WE().util.showMessage(WE().consts.g_l.main.prefs_saved_successfully, WE().consts.message.WE_MESSAGE_NOTICE, window);
			}
			break;
		case 'delete':
			if (iSelIdx >= 1) {
				oSctRss.options[iSelIdx] = null;
				oSctRss.selectedIndex = 0;
				oIptNewTitle.value = oIptNewUri.value = '';
				handleButtonState(0, 'overwrite', 'delete');
				_iTopRssFeedsLen--;
			}
			break;
	}
	_bIsHotTopRssFeeds = true;
}

function populateSct(oSctRss) {
	for (var i = 0; _iTopRssFeedsLen > i; i++) {
		var sOptVal = window.atob(aTopRssFeeds[i][1]);
		var sOptTxt = window.atob(aTopRssFeeds[i][0]);
		oSctRss.options[oSctRss.options.length] = new Option(sOptTxt, sOptVal);
	}
}
