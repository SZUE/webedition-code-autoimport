/**
 * webEdition CMS
 *
 * webEdition CMS
 * $Rev: 9621 $
 * $Author: mokraemer $
 * $Date: 2015-03-30 00:00:17 +0200 (Mo, 30. Mär 2015) $
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
var _aTopRssFeeds_ = opener._trf;
var _iTopRssFeedsLen = _aTopRssFeeds_.length;
var _bIsHotTopRssFeeds = false;
var _sInitUri;
var _sLastPreviewUri = '';
var _sInitRssCfg = '';
var _iInitRssCfgNumEntries = 0;
var _sInitTbCfg = '';
var _iInitTbTitlePers = 0;


function gel(id_) {
	return document.getElementById ? document.getElementById(id_) : null;
}

function isUrl(s) {
	var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
	return regexp.test(s);
}

function handleButtonState() {
	var iArgsLen = arguments.length;
	var sImplodeArgs = '';
	for (var i = 1; i < iArgsLen; i++) {
		sImplodeArgs += '\'' + arguments[i] + '\'' + ((i < iArgsLen - 1) ? ',' : '');
	}
	eval('var aDisable=[' + sImplodeArgs + ']');
	for (var i = 0; i < iArgsLen - 1; i++) {
		switch_button_state(aDisable[i], aDisable[i] + '_enabled', (arguments[0]) ? 'enabled' : 'disabled');
	}
}

function toggleRssTopFeed() {
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
	_fo = document.forms[0];
	var sCsv_ = opener.gel(_sObjId + '_csv').value;
	var aCsv = sCsv_.split(',');
	var sUri = opener.base64_decode(aCsv[0]);
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
	for (var i = iChbxConfLen - 1; i >= 0; i--) {
		oChbxConf[i].checked = (parseInt(_sInitRssCfg.charAt(i))) ? true : false;
	}
	var oSctConf = _fo.elements.sct_conf;
	_iInitRssCfgNumEntries = aCsv[2];
	oSctConf.options[_iInitRssCfgNumEntries].selected = true;
	_sInitTbCfg = aCsv[3];
	var oChbxTb = _fo.elements.chbx_tb;
	var iChbxTbLen = oChbxTb.length;
	for (var i = iChbxTbLen - 1; i >= 0; i--) {
		oChbxTb[i].checked = (parseInt(aCsv[3].charAt(i))) ? true : false;
	}
	_iInitTbTitlePers = aCsv[4];
	var oRdoTitle = _fo.elements.rdo_title;
	oRdoTitle[aCsv[4]].checked = true;
	initPrefs();
}

function onChangeSctRss(obj) {
	var sUri = obj.options[obj.selectedIndex].value;
	var sTitle = obj.options[obj.selectedIndex].text;
	toggleRssTopFeed();
	if (sUri != '') {
		var oIptUri = _fo.elements.ipt_uri;
		oIptUri.value = sUri;
		oIptUri.title = sUri;
	}
}

function onDisableRdoGroup(sId) {
	var oDisable = _fo.elements['rdo_' + sId];
	var iDisableLen = oDisable.length;
	for (var i = 0; iDisableLen > i; i++) {
		oDisable[i].disabled = (!oDisable[i].disabled) ? true : false;
	}
}

function getTbPersTitle(sUri) {
	var oRdoTitle = _fo.elements.rdo_title;
	var sTbTitle = '';
	if (oRdoTitle[1].checked == true) {
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
	var sRssCfgBinary = getBinary('conf');
	var sRssCfgSelIdx = _fo.elements.sct_conf.selectedIndex;
	if (!bOnChange || (_sLastPreviewUri != '' && sUri != _sLastPreviewUri) || (_sLastPreviewUri == '' && sUri != _sInitUri) ||
					_sInitRssCfg != sRssCfgBinary || _iInitRssCfgNumEntries != sRssCfgSelIdx) {
		_sLastPreviewUri = sUri;
		var sTbBinary = getBinary('tb');
		opener.rpc(escape(sUri), sRssCfgBinary, sRssCfgSelIdx, sTbBinary, getTbPersTitle(sUri), _sObjId);
	}
}

function resetRssFeed() {
	var iSctConfSel = _fo.elements.sct_conf.selectedIndex;
	var iRdoTitleSel = (_fo.elements.rdo_title.checked) ? 0 : 1;
	if ((_sLastPreviewUri != '' && _sInitUri != _sLastPreviewUri) ||
					(getBinary('conf') != _sInitRssCfg) ||
					(getBinary('tb') != _sInitTbCfg) ||
					(_iInitRssCfgNumEntries != iSctConfSel) ||
					(_iInitTbTitlePers != iRdoTitleSel)) {
		opener.rpc(escape(_sInitUri), _sInitRssCfg, _iInitRssCfgNumEntries, _sInitTbCfg, getTbPersTitle(_sInitUri), _sObjId);
	}
}

function getBinary(postfix) {
	var sBinary = '';
	var oChbx = _fo.elements['chbx_' + postfix];
	var iChbxLen = oChbx.length;
	for (var i = 0; i < iChbxLen; i++) {
		sBinary += (oChbx[i].checked) ? '1' : '0';
	}
	return sBinary;
}


function exit_close() {
	resetRssFeed();
	exitPrefs();
	self.close();
}

function save() {
	var oIptUri = _fo.elements.ipt_uri;
	var sUri = oIptUri.value;
	if (!isUrl(sUri)) {
		//return;
	}
	var oSctConf = _fo.elements.sct_conf;
	var oCsv_ = opener.gel(_sObjId + '_csv');
	var oRdoTitle = _fo.elements.rdo_title;
	oCsv_.value = opener.base64_encode(sUri) + ',' + getBinary('conf') + ',' + oSctConf.selectedIndex +
					',' + getBinary('tb') + ',' + ((oRdoTitle[0].checked) ? 0 : 1);
	if (_bIsHotTopRssFeeds) {
		var oSctRss = _fo.elements.sct_rss;
		var aNewTopRssFeeds = new Array();
		for (var i = 0; _iTopRssFeedsLen > i; i++) {
			aNewTopRssFeeds[i] = [opener.base64_encode(oSctRss.options[i + 1].text),
				opener.base64_encode(oSctRss.options[i + 1].value)];
		}
		opener._trf = aNewTopRssFeeds;
		opener._isHotTrf = true;
	}
	opener.saveSettings();
	//savePrefs();
	//displayRssFeed(sUri,true);
	top.we_showMessage(g_l.prefs_saved_successfully, WE_MESSAGE_NOTICE, window);
	opener.top.weNavigationHistory.navigateReload();
	self.close();
}

function preview() {
	var oIptUri = _fo.elements.ipt_uri;
	var sUri = oIptUri.value;
	if (!isUrl(sUri)) {
		top.we_showMessage(g_l.invalid_url, WE_MESSAGE_ERROR, window);
		//return;
	}
	previewPrefs();
	displayRssFeed(sUri, false);
}


function handleTopRssFeed(sAction) {
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
			if (sNewTitle != '' && sNewUri != '') {
				if (oSctRss.length <= 1) {
					var newOpt1 = new Option(sNewTitle, sNewUri);
					oSctRss.options[1] = newOpt1;
					oSctRss.selectedIndex = 1;
				} else if (iSelIdx != -1) {
					var aSctText = new Array();
					var aSctValues = new Array();
					var iCount = -1;
					var iNewSelected = -1;
					for (var i = 0; i < oSctRss.length; i++) {
						iCount++;
						if (iCount == iSelIdx) {
							aSctText[(iSelIdx == 0 && iCount == 0) ? 1 : iCount] = sNewTitle;
							aSctValues[(iSelIdx == 0 && iCount == 0) ? 1 : iCount] = sNewUri;
							iNewSelected = (iSelIdx == 0 && iCount == 0) ? 1 : iCount;
							iCount++;
						}
						aSctText[(iSelIdx == 0 && iCount == 1) ? 0 : iCount] = oSctRss.options[i].text;
						aSctValues[(iSelIdx == 0 && iCount == 1) ? 0 : iCount] = oSctRss.options[i].value;
					}
					for (var i = 0; i <= iCount; i++) {
						var newOpt = new Option(aSctText[i], aSctValues[i]);
						oSctRss.options[i] = newOpt;
						oSctRss.options[i].selected = (i == iNewSelected) ? true : false;
					}
				}
				handleButtonState(1, 'overwrite', 'delete');
				_iTopRssFeedsLen++;
			} else {
				top.we_showMessage(g_l.prefs_saved_successfully, WE_MESSAGE_NOTICE, window);
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
		var sOptVal = opener.base64_decode(_aTopRssFeeds_[i][1]);
		var sOptTxt = opener.base64_decode(_aTopRssFeeds_[i][0]);
		oSctRss.options[oSctRss.options.length] = new Option(sOptTxt, sOptVal);
	}
}
