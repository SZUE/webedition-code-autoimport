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

/**
 * @abstract class making the view for the document list
 */
abstract class doclistView{

	/**
	 * @abstract create javascript for document list
	 * @return javascript code
	 */
	public static function getSearchJS(){
		$h = 0;
		$addinputRows = "";
		if($GLOBALS['we_doc']->searchclassFolder->mode){
			$h += 30;
			//add height of each input row to calculate the scrollContent-height
			$addinputRows = 'for(i=0;i<newID;i++) {
                scrollheight = scrollheight + 26;
              }';
		}

		//workaround for z-index ans selects in ie6
		if(((we_base_browserDetect::isIE()) && we_base_browserDetect::getIEVersion() < 7)){
			$showHideSelects = 'var AnzahlSelects = document.getElementsByTagName("select");
                for (var k = 0; k <= AnzahlSelects.length; k++ ) {
                  var selectAnzahl = AnzahlSelects[k];
                  var sATop = absTop(selectAnzahl);
                  var sAHeight = selectAnzahl.offsetHeight;
                  var sABottom = eval(sATop+sAHeight);
                  var sALeft = absLeft(selectAnzahl);
                  var sAWidth = selectAnzahl.offsetWidth;
                  var sARight = eval(sALeft+sAWidth);

                  if(elem.offsetTop-20<sATop && eval(elem.offsetTop+elemHeight+50)>sABottom && elem.offsetLeft<sARight && eval(elem.offsetLeft+elemWidth)>sALeft) {
                    selectAnzahl.style.visibility = "hidden";
                  }
                  else {
                    selectAnzahl.style.visibility = "visible";
                  }
                }';

			$showSelects = 'var AnzahlSelects = document.getElementsByTagName("select");
              for (var k = 0; k <= AnzahlSelects.length; k++ ) {
                var selectAnzahl = AnzahlSelects[k];
                if(selectAnzahl.style.visibility == "hidden") {
                  selectAnzahl.style.visibility = "visible";
                }
              }';
		} else {
			$showHideSelects = '';
			$showSelects = '';
		}
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', 0);

		return we_html_element::jsElement('
var ajaxURL = "' . WEBEDITION_DIR . 'rpc/rpc.php";
var ajaxCallbackResultList = {
	success: function(o) {
		if(typeof(o.responseText) != "undefined" && o.responseText != "") {
			document.getElementById("scrollContent_doclist").innerHTML = o.responseText;
			makeAjaxRequestParametersTop();
			makeAjaxRequestParametersBottom();
		}
	},
	failure: function(o) {
		alert("Failure");
	}
}
var ajaxCallbackParametersTop = {
	success: function(o) {
		if(typeof(o.responseText) != "undefined" && o.responseText != "") {
			document.getElementById("parametersTop").innerHTML = o.responseText;
		}
	},
	failure: function(o) {
		alert("Failure");
	}
}
var ajaxCallbackParametersBottom = {
	success: function(o) {
		if(typeof(o.responseText) != "undefined" && o.responseText != "") {
			document.getElementById("parametersBottom").innerHTML = o.responseText;
		}
	},
	failure: function(o) {
		alert("Failure");
	}
}

var ajaxCallbackgetMouseOverDivs = {
	success: function(o) {
		if(typeof(o.responseText) != "undefined" && o.responseText != "") {
			document.getElementById("mouseOverDivs_doclist").innerHTML = o.responseText;
		}
	},
	failure: function(o) {
		alert("Failure");
	}
}

function search(newSearch) {
	if(' . intval(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()) . ') {
	' . we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
	}
	else {
		if(newSearch) {
			document.we_form.searchstart.value=0;
		}
		makeAjaxRequestDoclist();
}
}

function makeAjaxRequestDoclist() {
	getMouseOverDivs();
	var args = "";
	var newString = "";
	for(var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd["+encodeURI(newString)+"]="+encodeURI(document.we_form.elements[i].value);
	}
	var scroll = document.getElementById("scrollContent_doclist");
	scroll.innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td align=\'center\'><img src=' . IMAGE_DIR . 'logo-busy.gif /><div id=\'scrollActive\'></div></td></tr></table>";
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackResultList, "protocol=json&cns=doclist&cmd=GetSearchResult&classname=we_folder&id=' . $GLOBALS['we_doc']->ID . '&we_transaction=' . $we_transaction . '"+args+"");
}

function makeAjaxRequestParametersTop() {
	var args = "";
	var newString = "";
	for(var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd["+encodeURI(newString)+"]="+encodeURI(document.we_form.elements[i].value);
	}
		YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersTop, "protocol=json&cns=doclist&cmd=GetSearchParameters&position=top&classname=we_folder&id=' . $GLOBALS['we_doc']->ID . '&we_transaction=' . $we_transaction . '"+args+"");
}

function makeAjaxRequestParametersBottom() {
	var args = "";
	var newString = "";
	for(var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd["+encodeURI(newString)+"]="+encodeURI(document.we_form.elements[i].value);
	}
		YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackParametersBottom, "protocol=json&cns=doclist&cmd=GetSearchParameters&position=bottom&classname=we_folder&id=' . $GLOBALS['we_doc']->ID . '&we_transaction=' . $we_transaction . '"+args+"");
}

function getMouseOverDivs() {
	var args = "";
	var newString = "";
	for(var i = 0; i < document.we_form.elements.length; i++) {
		newString = document.we_form.elements[i].name;
		args += "&we_cmd["+encodeURI(newString)+"]="+encodeURI(document.we_form.elements[i].value);
	}
	YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackgetMouseOverDivs, "protocol=json&cns=doclist&cmd=GetMouseOverDivs&whichsearch=doclist&classname=we_folder&id=' . $GLOBALS['we_doc']->ID . '&we_transaction=' . $we_transaction . '"+args+"");
}

function switchSearch(mode) {
	document.we_form.mode.value=mode;
	var defSearch = document.getElementById("defSearch");
	var advSearch = document.getElementById("advSearch");
	var advSearch2 = document.getElementById("advSearch2");
	var advSearch3 = document.getElementById("advSearch3");
	var scrollContent = document.getElementById("scrollContent_doclist");

	scrollheight = 30;

	var elem = document.getElementById("filterTable");
	newID = elem.rows.length-1;

	for(i=0;i<newID;i++) {
		scrollheight = scrollheight + 26;
	}

	if (mode==1) {
		scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) +"px";
		defSearch.style.display = "none";
		advSearch.style.display = "block";
		advSearch2.style.display = "block";
		advSearch3.style.display = "block";
	}else {
		scrollContent.style.height = (scrollContent.offsetHeight + scrollheight) +"px";
		defSearch.style.display = "block";
		advSearch.style.display = "none";
		advSearch2.style.display = "none";
		advSearch3.style.display = "none";
	}
}

function openToEdit(tab,id,contentType){
	top.weEditorFrameController.openDocument(tab,id,contentType);
}


function setOrder(order){
	columns = new Array("Text", "SiteTitle", "CreationDate", "ModDate");
	for(var i=0;i<columns.length;i++) {
		if(order!=columns[i]) {
			deleteArrow = document.getElementById(""+columns[i]+"");
			deleteArrow.innerHTML = "";
		}
	}
	arrow = document.getElementById(""+order+"");
	foo = document.we_form.elements["order"].value;

	if(order+" DESC"==foo){
		document.we_form.elements["order"].value=order;
		arrow.innerHTML = "<img border=\"0\" width=\"11\" height=\"8\" src=\"' . IMAGE_DIR . 'arrow_sort_asc.gif\" />";
	}else{
		document.we_form.elements["order"].value=order+" DESC";
		arrow.innerHTML = "<img border=\"0\" width=\"11\" height=\"8\" src=\"' . IMAGE_DIR . 'arrow_sort_desc.gif\" />";
	}
	search(false);
}

function setview(setView){
	document.we_form.setView.value=setView;
	search(false);
}

elem = null;

function showImageDetails(picID){
	elem = document.getElementById(picID);
	elem.style.visibility = "visible";
}

function hideImageDetails(picID){
	elem = document.getElementById(picID);
	elem.style.visibility = "hidden";
	elem.style.left = "-9999px";

	' . $showSelects . '
}

document.onmousemove = updateElem;

function updateElem(e) {
	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var w = window.innerWidth ? window.innerWidth : document.body.offsetWidth;
	var x = (document.all) ? window.event.x + document.body.scrollLeft : e.pageX;
	var y = (document.all) ? window.event.y + document.body.scrollTop  : e.pageY;

	if (elem != null && elem.style.visibility == "visible") {

		elemWidth = elem.offsetWidth;
		elemHeight = elem.offsetHeight;
		elem.style.left = (x + 10) + "px";
		elem.style.top = (y - 120) + "px";

		if((w-x)<400 && (h-y)<250) {
			elem.style.left = (x - elemWidth - 10) + "px";
			elem.style.top = (y - elemHeight - 10) + "px";
		}
		else if((w-x)<400) {
			elem.style.left = (x - elemWidth - 10) + "px";
		}
		else if((h-y)<250) {
			elem.style.top = (y - elemHeight - 10) + "px";
		}
		' . $showHideSelects . '
	}
}

function absLeft(el) {
		 return (el.offsetParent)?
		el.offsetLeft+absLeft(el.offsetParent) : el.offsetLeft;
	}

 function absTop(el) {
		return (el.offsetParent)?
	el.offsetTop+absTop(el.offsetParent) : el.offsetTop;
	}


function sizeScrollContent() {
	var elem = document.getElementById("filterTable");
	newID = elem.rows.length-1;
	scrollheight = ' . $h . ';

	' . $addinputRows . '

	var h = window.innerHeight ? window.innerHeight : document.body.offsetHeight;
	var scrollContent = document.getElementById("scrollContent_doclist");

	var height = ' . (we_base_browserDetect::isIE() ? 200 : 180) . ';
	if((h - height)>0) {
		scrollContent.style.height=(h - height)+"px";
	}
	if((scrollContent.offsetHeight - scrollheight)>0){
		scrollContent.style.height = (scrollContent.offsetHeight - scrollheight) +"px";
	}
}

function init() {
	sizeScrollContent();
}

function reload() {
	top.we_cmd("reload_editpage");
}

function next(anzahl){
	var scrollActive = document.getElementById("scrollActive");
if(scrollActive==null) {
		document.we_form.elements[\'searchstart\'].value = parseInt(document.we_form.elements[\'searchstart\'].value) + anzahl;
		search(false);
	}
}

function back(anzahl){
	var scrollActive = document.getElementById("scrollActive");
if(scrollActive==null) {
		document.we_form.elements[\'searchstart\'].value = parseInt(document.we_form.elements[\'searchstart\'].value) - anzahl;
		search(false);
	}
}

var rows = ' . (isset($_REQUEST["searchFields"]) ? count($_REQUEST["searchFields"]) - 1 : 0) . ';

function newinput() {
	var searchFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('searchFields[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFields("__we_new_id__", "doclist"), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields[__we_new_id__]", 'onchange' => "changeit(this.value, __we_new_id__);")))) . '";
	var locationFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'id' => "location[__we_new_id__]")))) . '";
	var search = "' . addslashes(we_html_tools::htmlTextInput('search[__we_new_id__]', 24, "", "", " class=\"wetextinput\" id=\"search[__we_new_id__]\" ", "text", 190)) . '";

	var elem = document.getElementById("filterTable");
	newID = elem.rows.length-1;
	rows++;

	var scrollContent = document.getElementById("scrollContent_doclist");
	scrollContent.style.height = scrollContent.offsetHeight - 26 +"px";


	if(elem){
		var newRow = document.createElement("TR");
			newRow.setAttribute("id", "filterRow_" + rows);

			var cell = document.createElement("TD");
			cell.innerHTML=searchFields.replace(/__we_new_id__/g,rows)+"<input type=\"hidden\" value=\"\" name=\"hidden_searchFields["+rows+"]\"";
			newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_location["+rows+"]");
			cell.innerHTML=locationFields.replace(/__we_new_id__/g,rows);
			newRow.appendChild(cell);

		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search["+rows+"]");
			cell.innerHTML=search.replace(/__we_new_id__/g,rows);
			newRow.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rows+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rows+')") . '\';
			newRow.appendChild(cell);

		elem.appendChild(newRow);
	}
}


function changeit(value, rowNr){
	var setValue = document.getElementsByName("search["+rowNr+"]")[0].value;
	var from = document.getElementsByName("hidden_searchFields["+rowNr+"]")[0].value;

	var searchFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('searchFields[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFields("__we_new_id__", "doclist"), 1, "", false, array('class' => "defaultfont", 'id' => "searchFields[__we_new_id__]", 'onchange' => "changeit(this.value, __we_new_id__);")))) . '";
	var locationFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation(), 1, "", false, array('class' => "defaultfont", 'id' => "location[__we_new_id__]")))) . '";
	var search = "' . addslashes(we_html_tools::htmlTextInput('search[__we_new_id__]', 24, "", "", " class=\"wetextinput\" id=\"search[__we_new_id__]\" ", "text", 190)) . '";

	var row = document.getElementById("filterRow_"+rowNr);
	var locationTD = document.getElementById("td_location["+rowNr+"]");
	var searchTD = document.getElementById("td_search["+rowNr+"]");
	var delButtonTD = document.getElementById("td_delButton["+rowNr+"]");
	var location = document.getElementById("location["+rowNr+"]");

	if(value=="Content") {
		if (locationTD!=null) {
			location.disabled = true;
		}
		row.removeChild(searchTD);

		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}
		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=search.replace(/__we_new_id__/g,rowNr);
			row.appendChild(cell);

			cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);
	}
	else if(value=="temp_category") {
		if (locationTD!=null) {
			location.disabled = true;
		}
		row.removeChild(searchTD);

		var innerhtml= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td>\n"
				+ "<input class=\"wetextinput\" name=\"search["+rowNr+"]\" size=\"58\" value=\"\"  id=\"search["+rowNr+"]\" readonly=\"1\" style=\"width: 190px;\" type=\"text\" />\n"
				+ "</td><td><input value=\"\" name=\"searchParentID["+rowNr+"]\" type=\"hidden\" /></td><td>' . addslashes(we_html_tools::getPixel(5, 4)) . '</td><td>\n"
				+ "<table title=\"' . g_l('button', '[select][value]') . '\" class=\"weBtn\" style=\"width: 70px\" onmouseout=\"weButton.out(this);\" onmousedown=\"weButton.down(this);\" onmouseup=\"if(weButton.up(this)){we_cmd(\'openCatselector\',document.we_form.elements[\'searchParentID["+rowNr+"]\'].value,\'' . CATEGORY_TABLE . '\',\'document.we_form.elements[\\\\\'searchParentID["+rowNr+"]\\\\\'].value\',\'document.we_form.elements[\\\\\'search["+rowNr+"]\\\\\'].value\',\'\',\'\',\'0\',\'\',\'\');}\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
				+ "<tbody><tr><td class=\"weBtnLeft\"></td><td class=\"weBtnMiddle\" style=\"width: 58px\">\n"
				+ "' . g_l('button', '[select][value]') . '\n"
				+ "</td><td class=\"weBtnRight\"></td></tr></tbody></table></td></tr></tbody></table>\n";


		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=innerhtml;
			row.appendChild(cell);

		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);
	}
	else if(value=="temp_template_id" || value=="MasterTemplateID") {
		if (locationTD!=null) {
			location.disabled = true;
		}
		row.removeChild(searchTD);

		var innerhtml= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td>\n"
				+ "<input class=\"wetextinput\" name=\"search["+rowNr+"]\" size=\"58\" value=\"\"  id=\"search["+rowNr+"]\" readonly=\"1\" style=\"width: 190px;\" type=\"text\" />\n"
				+ "</td><td><input value=\"\" name=\"searchParentID["+rowNr+"]\" type=\"hidden\" /></td><td>' . addslashes(we_html_tools::getPixel(5, 4)) . '</td><td>\n"
				+ "<table title=\"' . g_l('button', '[select][value]') . '\" class=\"weBtn\" style=\"width: 70px\" onmouseout=\"weButton.out(this);\" onmousedown=\"weButton.down(this);\" onmouseup=\"if(weButton.up(this)){we_cmd(\'openDocselector\',document.we_form.elements[\'searchParentID["+rowNr+"]\'].value,\'' . TEMPLATES_TABLE . '\',\'document.we_form.elements[\\\\\'searchParentID["+rowNr+"]\\\\\'].value\',\'document.we_form.elements[\\\\\'search["+rowNr+"]\\\\\'].value\',\'\',\'\',\'0\',\'\',\'\');}\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
				+ "<tbody><tr><td class=\"weBtnLeft\"></td><td class=\"weBtnMiddle\" style=\"width: 58px\">\n"
				+ "' . g_l('button', '[select][value]') . '\n"
				+ "</td><td class=\"weBtnRight\"></td></tr></tbody></table></td></tr></tbody></table>\n";


		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=innerhtml;
			row.appendChild(cell);

		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);
	}
	else if(value=="Status") {
		if (locationTD!=null) {
			location.disabled = true;
		}
		row.removeChild(searchTD);
		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		search = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsStatus(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

		var cell = document.createElement("TD");
			cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=search.replace(/__we_new_id__/g,rowNr);
		row.appendChild(cell);

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);

	}
	else if(value=="Speicherart") {
		if (locationTD!=null) {
			location.disabled = true;
		}
		row.removeChild(searchTD);
		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		search = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('search[__we_new_id__]', $GLOBALS['we_doc']->searchclassFolder->getFieldsSpeicherart(), 1, "", false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[__we_new_id__]")))) . '";

		var cell = document.createElement("TD");
			cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=search.replace(/__we_new_id__/g,rowNr);
		row.appendChild(cell);

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);

	}
	else if(value=="Published" || value=="CreationDate" || value=="ModDate") {

		row.removeChild(locationTD);

		locationFields = "' . str_replace("\n", "\\n", addslashes(we_html_tools::htmlSelect('location[__we_new_id__]', we_search_search::getLocation("date"), 1, "", false, array('class' => "defaultfont", 'id' => "location[__we_new_id__]")))) . '";

		var cell = document.createElement("TD");
			cell.setAttribute("id", "td_location["+rowNr+"]");
			cell.innerHTML=locationFields.replace(/__we_new_id__/g,rowNr);
		row.appendChild(cell);

		row.removeChild(searchTD);

		var innerhtml= "<table id=\"search["+rowNr+"]_cell\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td></td><td></td><td>\n"
				+ "<input class=\"wetextinput\" name=\"search["+rowNr+"]\" size=\"55\" value=\"\" maxlength=\"10\" id=\"search["+rowNr+"]\" readonly=\"1\" style=\"width: 100px; \" type=\"text\" />"
				+ "</td><td>&nbsp;</td><td><a href=\"#\">\n"
				+ "<table id=\"date_picker_from"+rowNr+"\" class=\"weBtn\" onmouseout=\"weButton.out(this);\" onmousedown=\"weButton.down(this);\" onmouseup=\"if(weButton.up(this)){;}\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">\n"
				+ "<tbody><tr><td class=\"weBtnLeft\"></td><td class=\"weBtnMiddle\"">
				+ "<img src=\"' . BUTTONS_DIR . 'icons/date_picker.gif\" class=\"weBtnImage\" alt=\"\"/>"
				+ "</td><td class=\"weBtnRight\"></td></tr></tbody></table></a></td></tr></tbody></table>";


		cell = document.createElement("TD");
		cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=innerhtml;
			row.appendChild(cell);

			Calendar.setup({inputField:"search["+rowNr+"]",ifFormat:"%d.%m.%Y",button:"date_picker_from"+rowNr+"",align:"Tl",singleClick:true});

		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);

	}
	else {
		row.removeChild(searchTD);

		if (locationTD!=null) {
			row.removeChild(locationTD);
		}
		if (delButtonTD!=null) {
			row.removeChild(delButtonTD);
		}

		var cell = document.createElement("TD");
			cell.setAttribute("id", "td_location["+rowNr+"]");
			cell.innerHTML=locationFields.replace(/__we_new_id__/g,rowNr);
row.appendChild(cell);

		var cell = document.createElement("TD");
			cell.setAttribute("id", "td_search["+rowNr+"]");
			cell.innerHTML=search.replace(/__we_new_id__/g,rowNr);
		row.appendChild(cell);

		cell = document.createElement("TD");
			cell.setAttribute("id", "td_delButton["+rowNr+"]");
			cell.innerHTML=\'' . we_html_button::create_button("image:btn_function_trash", "javascript:delRow('+rowNr+')") . '\';
			row.appendChild(cell);
	}
	if(from=="temp_template_id" || from=="ContentType" || from=="temp_category" || from=="Status" || from=="Speicherart" || from=="Published" || from=="CreationDate" || from=="ModDate"
		 || value=="temp_template_id" || value=="ContentType" || value=="temp_category" || value=="Status" || value=="Speicherart" || value=="Published" || value=="CreationDate" || value=="ModDate") {
		document.getElementById("search["+rowNr+"]").value = "";
}
else {
	document.getElementById("search["+rowNr+"]").value = setValue;
}

	document.getElementsByName("hidden_searchFields["+rowNr+"]")[0].value = value;

}

function checkAllPubChecks() {

var checkAll = document.getElementsByName("publish_all");
var checkboxes = document.getElementsByName("publish_docs_doclist");
var check = false;

if(checkAll[0].checked) {
check = true;
}
for(var i = 0; i < checkboxes.length; i++) {
checkboxes[i].checked = check;
}

}

function publishDocs() {

var checkAll = document.getElementsByName("publish_all");
var checkboxes = document.getElementsByName("publish_docs_doclist");
var check = false;

for(var i = 0; i < checkboxes.length; i++) {
if(checkboxes[i].checked) {
	check = true;
	break;
}
}

if(checkboxes.length==0) {
check = false;
}

if(check==false) {
' . we_message_reporting::getShowMessageCall(g_l('searchtool', '[notChecked]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
}
else {

Check = confirm("' . g_l('searchtool', '[publish_docs]') . '");
	if (Check == true) {
		publishDocsAjax();
	}
}
}

var ajaxCallbackPublishDocs = {
success: function(o) {
' . we_message_reporting::getShowMessageCall(g_l('searchtool', '[publishOK]'), we_message_reporting::WE_MESSAGE_NOTICE) . '

	// reload current document => reload all open Editors on demand

var _usedEditors =  top.weEditorFrameController.getEditorsInUse();
for (frameId in _usedEditors) {

	if ( _usedEditors[frameId].getEditorIsActive() ) { // reload active editor
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
		_usedEditors[frameId].setEditorIsActive(true);

	} else {
		_usedEditors[frameId].setEditorReloadAllNeeded(true);
	}
}
_multiEditorreload = true;

//reload tree
top.we_cmd("load", top.treeData.table ,0);

document.getElementById("resetBusy").innerHTML = "";

},
failure: function(o) {
 alert("Failure");
}
}

function publishDocsAjax() {

var args = "";
var check = "";
var checkboxes = document.getElementsByName("publish_docs_doclist");
for(var i = 0; i < checkboxes.length; i++) {
if(checkboxes[i].checked) {
		if(check!="") check += ",";
		check += checkboxes[i].value;
}
}
args += "&we_cmd[0]="+encodeURI(check);
var scroll = document.getElementById("resetBusy");
scroll.innerHTML = "<table border=\'0\' width=\'100%\' height=\'100%\'><tr><td align=\'center\'><img src=' . IMAGE_DIR . 'logo-busy.gif /></td></tr></table>";

YAHOO.util.Connect.asyncRequest("POST", ajaxURL, ajaxCallbackPublishDocs, "protocol=json&cns=tools/weSearch&cmd=PublishDocs&"+args+"");
}

function calendarSetup(x){
	for(i=0;i<x;i++) {
		if(document.getElementById("date_picker_from"+i+"") != null) {
			Calendar.setup({inputField:"search["+i+"]",ifFormat:"%d.%m.%Y",button:"date_picker_from"+i+"",align:"Tl",singleClick:true});
		}
	}
}

function delRow(id) {
	var scrollContent = document.getElementById("scrollContent_doclist");
	scrollContent.style.height = scrollContent.offsetHeight + 26 +"px";
	var elem = document.getElementById("filterTable");

	if(elem){
		trows = elem.rows;
		rowID = "filterRow_" + id;

				for (i=0;i<trows.length;i++) {
					if(rowID == trows[i].id) {
						elem.deleteRow(i);
					}
				}
	}

}');
	}

	/**
	 * @abstract create search dialog-box
	 * @return html for search dialog box
	 */
	public static function getSearchDialog(){
		$out = '<table cellpadding="0" cellspacing="0" id="defSearch" border="0" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'none' : 'block') . ';">
        <tr>
        <td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '
        </td>
        <td>' . we_html_tools::getPixel(10, 2) . '
        </td>
        <td>' . we_html_tools::getPixel(40, 2) . '' . we_html_button::create_button("image:btn_direction_right", "javascript:switchSearch(1)", false) . '</td>
        <td width="100%">' . we_html_tools::getPixel(10, 2) . '
        </td>
        </tr>
        </table>
				<table cellpadding="0" cellspacing="0" border="0" id="advSearch" width="550" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
        <tr>
        <td class="weDocListSearchHeadline">' . g_l('searchtool', '[suchen]') . '
        </td>
        <td>' . we_html_tools::getPixel(10, 2) . '
        </td>
        <td>' . we_html_tools::getPixel(40, 2) . '' . we_html_button::create_button("image:btn_direction_down", "javascript:switchSearch(0)", false) . '</td>
        <td width="100%">' . we_html_tools::getPixel(10, 2) . '
        </td>
        </tr>
        </table>
				<table cellpadding="2" cellspacing="0"  id="advSearch2" border="0" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
        <tbody id="filterTable">
        <tr>
          <td>' . we_class::hiddenTrans() . '</td>
        </tr>';

		$r = $r2 = $r3 = array();
		if(isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search)){
			foreach($GLOBALS['we_doc']->searchclassFolder->search as $k => $v){
				$r[] = $GLOBALS['we_doc']->searchclassFolder->search [$k];
			}
		}
		if(isset($GLOBALS['we_doc']->searchclassFolder->searchFields) && is_array($GLOBALS['we_doc']->searchclassFolder->search)){
			foreach($GLOBALS['we_doc']->searchclassFolder->searchFields as $k => $v){
				$r2[] = $GLOBALS['we_doc']->searchclassFolder->searchFields [$k];
			}
		}
		if(($loc = we_base_request::_(we_base_request::STRING, 'location'))){
			foreach($_REQUEST['searchFields'] as $k => $v){
				$r3[] = (isset($loc[$k]) ? $loc[$k] : "disabled");
			}
		}

		$GLOBALS['we_doc']->searchclassFolder->search = $r;
		$GLOBALS['we_doc']->searchclassFolder->searchFields = $r2;
		$GLOBALS['we_doc']->searchclassFolder->location = $r3;

		for($i = 0; $i < $GLOBALS['we_doc']->searchclassFolder->height; $i++){
			$button = we_html_button::create_button("image:btn_function_trash", "javascript:delRow(" . $i . ");", true, "", "", "", "", false);

			$handle = "";

			$searchInput = we_html_tools::htmlTextInput("search[" . $i . "]", 30, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search[$i]) ? $GLOBALS['we_doc']->searchclassFolder->search[$i] : ''), "", " class=\"wetextinput\"  id=\"search['.$i.']\" ", "text", 190);

			switch(isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields[$i] : ''){
				case "Content":
				case "Status":
				case "Speicherart":
				case "temp_template_id":
				case "temp_category":
					$locationDisabled = 'disabled';
					break;
				default:
					$locationDisabled = '';
			}

			if(isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i])){
				if($GLOBALS['we_doc']->searchclassFolder->searchFields[$i] === "Status"){
					$searchInput = we_html_tools::htmlSelect("search[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFieldsStatus(), 1, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search [$i]) ? $GLOBALS['we_doc']->searchclassFolder->search [$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[' . $i . ']"));
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "Speicherart"){
					$searchInput = we_html_tools::htmlSelect("search[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFieldsSpeicherart(), 1, (isset($GLOBALS['we_doc']->searchclassFolder->search) && is_array($GLOBALS['we_doc']->searchclassFolder->search) && isset($GLOBALS['we_doc']->searchclassFolder->search [$i]) ? $GLOBALS['we_doc']->searchclassFolder->search [$i] : ""), false, array('class' => "defaultfont", 'style' => "width:190px;", 'id' => "search[' . $i . ']"));
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "Published" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "CreationDate" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "ModDate"){
					$handle = "date";
					$searchInput = we_html_tools::getDateSelector("search[" . $i . "]", "_from" . $i, $GLOBALS['we_doc']->searchclassFolder->search [$i]);
				}

				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "MasterTemplateID" || $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "temp_template_id"){
					$_linkPath = $GLOBALS['we_doc']->searchclassFolder->search [$i];

					$_rootDirID = 0;

					$wecmdenc1 = we_base_request::encCmd("document.we_form.elements['searchParentID[" . $i . "]'].value");
					$wecmdenc2 = we_base_request::encCmd("document.we_form.elements['search[" . $i . "]'].value");
					$_cmd = "javascript:we_cmd('openDocselector',document.we_form.elements['searchParentID[" . $i . "]'].value,'" . TEMPLATES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','','" . $_rootDirID . "','','" . we_base_ContentTypes::TEMPLATE . "')";
					$_button = we_html_button::create_button('select', $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly ', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(array('name' => 'searchParentID[' . $i . ']', "value" => "")), we_html_tools::getPixel(5, 4), $_button);

					$searchInput = $selector;
				}
				if($GLOBALS['we_doc']->searchclassFolder->searchFields [$i] === "temp_category"){
					$_linkPath = $GLOBALS['we_doc']->searchclassFolder->search [$i];

					$_rootDirID = 0;

					$_cmd = "javascript:we_cmd('openCatselector',document.we_form.elements['searchParentID[" . $i . "]'].value,'" . CATEGORY_TABLE . "','document.we_form.elements[\\'searchParentID[" . $i . "]\\'].value','document.we_form.elements[\\'search[" . $i . "]\\'].value','','','" . $_rootDirID . "','','')";
					$_button = we_html_button::create_button('select', $_cmd, true, 70, 22, '', '', false);
					$selector = we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput('search[' . $i . ']', 58, $_linkPath, '', 'readonly', 'text', 190, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden(array('name' => 'searchParentID[' . $i . ']', "value" => "")), we_html_tools::getPixel(5, 4), $_button);

					$searchInput = $selector;
				}
			}

			$out .= '
        <tr id="filterRow_' . $i . '">
          <td>' . we_html_tools::hidden("hidden_searchFields[" . $i . "]", isset($GLOBALS['we_doc']->searchclassFolder->searchFields[$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields[$i] : "" ) . '' . we_html_tools::htmlSelect("searchFields[" . $i . "]", $GLOBALS['we_doc']->searchclassFolder->getFields($i, "doclist"), 1, (isset($GLOBALS['we_doc']->searchclassFolder->searchFields) && is_array($GLOBALS['we_doc']->searchclassFolder->searchFields) && isset($GLOBALS['we_doc']->searchclassFolder->searchFields [$i]) ? $GLOBALS['we_doc']->searchclassFolder->searchFields [$i] : ""), false, array('class' => "defaultfont", 'id' => "searchFields[' . $i . ']", 'onchange' => "changeit(this.value, ' . $i . ');")) . '</td>
          <td id="td_location[' . $i . ']">' . we_html_tools::htmlSelect("location[" . $i . "]", we_search_search::getLocation($handle), 1, (isset($GLOBALS['we_doc']->searchclassFolder->location) && is_array($GLOBALS['we_doc']->searchclassFolder->location) && isset($GLOBALS['we_doc']->searchclassFolder->location [$i]) ? $GLOBALS['we_doc']->searchclassFolder->location [$i] : ""), false, array('class' => "defaultfont", $locationDisabled => $locationDisabled, 'id' => "location[' . $i . ']")) . '</td>
          <td id="td_search[' . $i . ']">' . $searchInput . '</td>
          <td id="td_delButton[' . $i . ']">' . $button . '</td>
        </tr>
        ';
		}

		$out .= '</tbody></table>
			<table cellpadding="0" cellspacing="0" id="advSearch3" border="0" style="margin-left:20px;display:' . ($GLOBALS['we_doc']->searchclassFolder->mode ? 'block' : 'none') . ';">
        <tr>
          <td colspan="4">' . we_html_tools::getPixel(20, 10) . '</td>
        </tr>
        <tr>
          <td width="215">' . we_html_button::create_button("add", "javascript:newinput();") . '</td>
          <td width="155"></td>
          <td width="188" align="right">' . we_html_button::create_button("search", "javascript:search(true);") . '</td>
          <td></td>
        </tr>

        </table>' .
			we_html_element::jsElement('calendarSetup(' . $GLOBALS['we_doc']->searchclassFolder->height . ');');

		return $out;
	}

	/**
	 * @abstract executes the search and writes the result into arrays
	 * @return array with search results
	 */
	public static function searchProperties($table = FILE_TABLE){
		$DB_WE = new DB_WE();
		$foundItems = 0;
		$content = $_result = $saveArrayIds = $searchText = array();
		$_SESSION['weS']['weSearch']['foundItems'] = 0;

		foreach($_REQUEST['we_cmd'] as $k => $v){
			if(stristr($k, 'searchFields[') && !stristr($k, 'hidden_')){
				$_REQUEST['we_cmd']['searchFields'][] = $v;
			}
			if(stristr($k, 'location[')){
				$_REQUEST['we_cmd']['location'][] = $v;
			}
			if(stristr($k, 'search[')){
				$_REQUEST['we_cmd']['search'][] = $v;
			}
		}

		$obj = (isset($GLOBALS['we_cmd_obj']) && is_object($GLOBALS['we_cmd_obj']) ?
				$GLOBALS['we_cmd_obj'] :
				$GLOBALS['we_doc']);

		$obj->searchclassFolder->searchstart = we_base_request::_(we_base_request::INT, "searchstart", 0);

		$searchFields = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->searchFields,'searchFields');
		$searchText = array_map('trim', we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->search, 'search'));
		$location = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->location, 'location');
		$_order = we_base_request::_(we_base_request::STRING, 'we_cmd', $obj->searchclassFolder->order, 'order');
		$_view = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->setView, 'setView');
		$_searchstart = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->searchstart, 'searchstart');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', $obj->searchclassFolder->anzahl, 'anzahl');

		$where = '';
		$op = ' AND ';
		$obj->searchclassFolder->settable($table);


		if(!we_search_search::checkRightTempTable() && !we_search_search::checkRightDropTable()){
			echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('searchtool', '[noTempTableRightsDoclist]'), we_message_reporting::WE_MESSAGE_NOTICE));
			return '';
		}
		if($obj->ID){
			$obj->searchclassFolder->createTempTable();

			foreach($searchFields as $i => $searchField){
				$w = "";
				if(isset($searchText[0])){
					$searchString = (isset($searchText[$i]) ? $searchText[$i] : $searchText[0]);
				}
				if(isset($searchString) && $searchString != ""){

					switch($searchField){
						default:
						case "Text":
							if(isset($searchField) && isset($location[$i])){
								$where .= $obj->searchclassFolder->searchfor($searchString, $searchField, $location[$i], $table);
							}
						case "Content":
						case "Status":
						case "Speicherart":
						case "CreatorName":
						case "WebUserName":
						case "temp_category":
							break;
					}

					switch($searchField){
						case "Content":
							$w = $obj->searchclassFolder->searchContent($searchString, $table);
							if(!$where){
								$where .= " AND " . ($w ? $w : '0');
							} elseif($w != ""){
								$where .= $op . " " . $w;
							}
							break;

						case 'Title':
							$w = $obj->searchclassFolder->searchInTitle($searchString, $table);
							if(!$where){
								$where = ' AND ' . ($w ? $w : '0');
							} elseif($w != ''){
								$where .= $op . ' ' . $w;
							}
							break;
						case "Status":
						case "Speicherart":
							if($searchString != ""){
								if($table == FILE_TABLE){
									$w = $obj->searchclassFolder->getStatusFiles($searchString, $table);
									$where .= $w;
								}
							}
							break;
						case "CreatorName":
						case "WebUserName":
							if($searchString != ""){
								$w = $obj->searchclassFolder->searchSpecial($searchString, $table, $searchField, $location[$i]);
								$where .= $w;
							}
							break;
						case "temp_category":
							$w = $obj->searchclassFolder->searchCategory($searchString, $table, $searchField);
							$where .= $w;
							break;
					}
				}
			}

			$where .= ' AND ParentID = ' . intval($obj->ID);

			$whereQuery = "1 " . $where;
			switch($table){
				case FILE_TABLE:
					$whereQuery .= ' AND ((RestrictOwners=0 OR RestrictOwners=' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners)))';
					break;
				case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE'):
					$whereQuery .= ' AND ((RestrictOwners=0 OR RestrictOwners=' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Owners)))';
					break;
				case (defined('OBJECT_TABLE') ? OBJECT_TABLE : OBJECT_TABLE):
					$whereQuery .= 'AND ((RestrictUsers=0 OR RestrictUsers= ' . intval($_SESSION["user"]["ID"]) . ') OR (FIND_IN_SET(' . intval($_SESSION["user"]["ID"]) . ',Users))) ';
					break;
			}

			$obj->searchclassFolder->setwhere($whereQuery);
			$obj->searchclassFolder->insertInTempTable($whereQuery, $table, $obj->Path . '/');

			$foundItems = $obj->searchclassFolder->countitems($whereQuery, $table);
			$_SESSION['weS']['weSearch']['foundItems'] = $foundItems;

			$obj->searchclassFolder->selectFromTempTable($_searchstart, $_anzahl, $_order);

			while($obj->searchclassFolder->next_record()){
				if(!isset($saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']])){
					$saveArrayIds[$obj->searchclassFolder->Record ['ContentType']][$obj->searchclassFolder->Record ['ID']] = $obj->searchclassFolder->Record ['ID'];
					$_result[] = array_merge(array('Table' => $table), $obj->searchclassFolder->Record);
				}
			}
		}

		if($_SESSION['weS']['weSearch']['foundItems']){
			$DB_WE->query('DROP TABLE IF EXISTS SEARCH_TEMP_TABLE');

			foreach($_result as $k => $v){
				$_result[$k]["Description"] = "";
				if($_result[$k]["Table"] == FILE_TABLE && $_result[$k]['Published'] >= $_result[$k]['ModDate'] && $_result[$k]['Published'] != 0){
					$_result[$k]["Description"] = f('SELECT c.Dat FROM (' . FILE_TABLE . ' a LEFT JOIN ' . LINK_TABLE . ' b ON (a.ID=b.DID)) LEFT JOIN ' . CONTENT_TABLE . ' c ON (b.CID=c.ID) WHERE a.ID=' . intval($_result[$k]["ID"]) . ' AND b.Name="Description" AND b.DocumentTable="' . FILE_TABLE . '"', '', $DB_WE);
				} else {
					if(($obj = f('SELECT DocumentObject FROM ' . TEMPORARY_DOC_TABLE . ' WHERE DocumentID=' . intval($_result[$k]["ID"]) . ' AND DocTable="tblFile" AND Active=1', '', $DB_WE))){
						$tempDoc = unserialize($obj);
						if(isset($tempDoc[0]['elements']['Description']) && $tempDoc[0]['elements']['Description']['dat']){
							$_result[$k]['Description'] = $tempDoc[0]['elements']['Description']['dat'];
						}
					}
				}
			}
			$content = self::makeContent($DB_WE, $_result, $_view);
		}
		return $content;
	}

	public static function makeHeadLines($table){
		return array(
			array("dat" => '<a href="javascript:setOrder(\'Text\');">' . g_l('searchtool', '[dateiname]') . '</a> <span id="Text" >' . self::getSortImage('Text') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'SiteTitle\');">' . ($table == TEMPLATES_TABLE ? g_l('weClass', '[path]') : g_l('searchtool', '[seitentitel]') ) . '</a> <span id="SiteTitle" >' . self::getSortImage('SiteTitle') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'CreationDate\');">' . g_l('searchtool', '[created]') . '</a> <span id="CreationDate" >' . self::getSortImage('CreationDate') . '</span>'),
			array("dat" => '<a href="javascript:setOrder(\'ModDate\');">' . g_l('searchtool', '[modified]') . '</a> <span id="ModDate" >' . self::getSortImage('ModDate') . '</span>'),
		);
	}

	private static function getSortImage($for){
		$order = we_base_request::_(we_base_request::RAW, 'order', $GLOBALS['we_doc']->searchclassFolder->order);

		if(strpos($order, $for) === 0){
			if(strpos($order, 'DESC')){
				return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_desc.gif" />';
			}
			return '<img border="0" width="11" height="8" src="' . IMAGE_DIR . 'arrow_sort_asc.gif" />';
		}
		return we_html_tools::getPixel(11, 8);
	}

	private function makeContent(we_database_base $DB_WE, $_result, $view){
		$we_PathLength = 30;

		$resultCount = count($_result);
		$content = array();

		for($f = 0; $f < $resultCount; $f++){
			$fontColor = '';
			$showPubCheckbox = true;
			if(isset($_result[$f]["Published"])){
				switch($_result[$f]["ContentType"]){
					case we_base_ContentTypes::HTML:
					case we_base_ContentTypes::WEDOCUMENT:
					case we_base_ContentTypes::OBJECT_FILE:
						$published = ((($_result[$f]["Published"] != 0) && ($_result[$f]["Published"] < $_result[$f]["ModDate"])) ? -1 : $_result[$f]["Published"]);
						if($published == 0){
							$fontColor = 'notpublished';
							$showPubCheckbox = false;
						} elseif($published == -1){
							$fontColor = 'changed';
							$showPubCheckbox = false;
						}
						break;
					default:
						$published = $_result[$f]["Published"];
				}
			} else {
				$published = 1;
			}

			$ext = isset($_result[$f]['Extension']) ? $_result[$f]['Extension'] : '';
			$Icon = we_base_ContentTypes::inst()->getIcon($_result[$f]['ContentType'], we_base_ContentTypes::FILE_ICON, $ext);

			if($view == 0){
				$publishCheckbox = (!$showPubCheckbox) ? (($_result[$f]['ContentType'] == we_base_ContentTypes::WEDOCUMENT || $_result[$f]['ContentType'] == we_base_ContentTypes::HTML || $_result[$f]['ContentType'] === 'objectFile') && permissionhandler::hasPerm('PUBLISH')) ? we_html_forms::checkbox($_result[$f]['docID'] . '_' . $_result[$f]['docTable'], 0, 'publish_docs_doclist', '', false, 'middlefont', '') : we_html_tools::getPixel(20, 10) : '';

				$content[$f] = array(
					array('dat' => $publishCheckbox),
					array('dat' => '<img src="' . TREE_ICON_DIR . $Icon . '" border="0" width="16" height="18" />'),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]['docTable'] . '\',\'' . $_result[$f]['docID'] . '\',\'' . $_result[$f]['ContentType'] . '\')" class="' . $fontColor . ' middlefont" title="' . $_result[$f]['Text'] . '"><u>' . we_util_Strings::shortenPath($_result[$f]['Text'], $we_PathLength)),
					//array("dat" => '<nobr>' . g_l('contentTypes', '[' . $_result[$f]['ContentType'] . ']') . '</nobr>'),
					array("dat" => '<nobr>' . we_util_Strings::shortenPath($_result[$f]["SiteTitle"], $we_PathLength) . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>')
				);
			} else {
				$fs = file_exists($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) ? filesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]) : 0;

				if($_result[$f]["ContentType"] == we_base_ContentTypes::IMAGE){
					$smallSize = 64;
					$bigSize = 140;

					if($fs){
						$imagesize = getimagesize($_SERVER['DOCUMENT_ROOT'] . $_result[$f]["Path"]);
						$imageView = "<img src='" . (file_exists($thumbpath = WE_THUMB_PREVIEW_DIR . $_result[$f]["docID"] . '_' . $smallSize . '_' . $smallSize . strtolower($_result[$f]['Extension'])) ?
								$thumbpath :
								WEBEDITION_DIR . 'thumbnail.php?id=' . $_result[$f]["docID"] . "&size=" . $smallSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]["Extension"]
							) . "' border='0' /></a>";

						$imageViewPopup = "<img src='" . (file_exists($thumbpathPopup = WE_THUMB_PREVIEW_DIR . $_result[$f]["docID"] . '_' . $bigSize . '_' . $bigSize . strtolower($_result[$f]["Extension"])) ?
								$thumbpathPopup :
								WEBEDITION_DIR . "thumbnail.php?id=" . $_result[$f]["docID"] . "&size=" . $bigSize . "&path=" . urlencode($_result[$f]["Path"]) . "&extension=" . $_result[$f]["Extension"]
							) . "' border='0' /></a>";
					} else {
						$imagesize = array(0, 0);
						$thumbpath = ICON_DIR . 'doclist/' . we_base_ContentTypes::IMAGE_ICON;
						$imageView = "<img src='" . $thumbpath . "' border='0' />";
						$imageViewPopup = "<img src='" . $thumbpath . "' border='0' />";
					}
				} else {
					$imagesize = array(0, 0);
					$imageView = '<img src="' . ICON_DIR . 'doclist/' . $Icon . '" border="0" width="64" height="64" />';
					$imageViewPopup = '<img src="' . ICON_DIR . 'doclist/' . $Icon . '" border="0" width="64" height="64" />';
				}

				$creator = $_result[$f]["CreatorID"] ? id_to_path($_result[$f]["CreatorID"], USER_TABLE, $DB_WE) : g_l('searchtool', '[nobody]');

				if($_result[$f]["ContentType"] == we_base_ContentTypes::WEDOCUMENT){
					$templateID = ($_result[$f]["Published"] >= $_result[$f]["ModDate"] && $_result[$f]["Published"] ?
							$_result[$f]["TemplateID"] :
							$_result[$f]["temp_template_id"]);

					$templateText = g_l('searchtool', '[no_template]');
					if($templateID){
						$DB_WE->query('SELECT ID, Text FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($templateID));
						while($DB_WE->next_record()){
							$templateText = we_util_Strings::shortenPath($DB_WE->f('Text'), 20) . " (ID=" . $DB_WE->f('ID') . ")";
						}
					}
				} else {
					$templateText = '';
				}

				$_defined_fields = we_metadata_metaData::getDefinedMetaDataFields();
				$metafields = array();
				$_fieldcount = count($_defined_fields);
				if($_fieldcount > 6){
					$_fieldcount = 6;
				}
				for($i = 0; $i < $_fieldcount; $i++){
					$_tagName = $_defined_fields[$i]["tag"];

					if(we_exim_contentProvider::isBinary($_result[$f]["docID"])){
						$DB_WE->query("SELECT a.ID, c.Dat FROM (" . FILE_TABLE . " a LEFT JOIN " . LINK_TABLE . " b ON (a.ID=b.DID)) LEFT JOIN " . CONTENT_TABLE . " c ON (b.CID=c.ID) WHERE b.DID=" . intval($_result[$f]["docID"]) . " AND b.Name='" . $DB_WE->escape($_tagName) . "' AND b.DocumentTable='" . FILE_TABLE . "'");
						$metafields[$_tagName] = "";
						while($DB_WE->next_record()){
							$metafields[$_tagName] = we_util_Strings::shortenPath($DB_WE->f('Dat'), 45);
						}
					}
				}

				$content[$f] = array(
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageView . '</a>'),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["SiteTitle"], 17)),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" class="' . $fontColor . '"  title="' . $_result[$f]["Text"] . '"><u>' . we_util_Strings::shortenPath($_result[$f]["Text"], 17) . '</u></a>'),
					array("dat" => '<nobr>' . ($_result[$f]["CreationDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["CreationDate"]) : "-") . '</nobr>'),
					array("dat" => '<nobr>' . ($_result[$f]["ModDate"] ? date(g_l('searchtool', '[date_format]'), $_result[$f]["ModDate"]) : "-") . '</nobr>'),
					array("dat" => '<a href="javascript:openToEdit(\'' . $_result[$f]["docTable"] . '\',\'' . $_result[$f]["docID"] . '\',\'' . $_result[$f]["ContentType"] . '\')" style="text-decoration:none;" class="middlefont" title="' . $_result[$f]["Text"] . '">' . $imageViewPopup . '</a>'),
					array("dat" => we_base_file::getHumanFileSize($fs)),
					array("dat" => $imagesize[0] . " x " . $imagesize[1]),
					array("dat" => we_util_Strings::shortenPath(g_l('contentTypes', '[' . ($_result[$f]['ContentType']) . ']'), 22)),
					array("dat" => '<span class="' . $fontColor . '">' . we_util_Strings::shortenPath($_result[$f]["Text"], 30) . '</span>'),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["SiteTitle"], 45)),
					array("dat" => we_util_Strings::shortenPath($_result[$f]["Description"], 100)),
					array("dat" => $_result[$f]['ContentType']),
					array("dat" => we_util_Strings::shortenPath($creator, 22)),
					array("dat" => $templateText),
					array("dat" => $metafields),
					array("dat" => $_result[$f]["docID"]),
				);
			}
		}

		return $content;
	}

	/**
	 * @abstract generates html for search result
	 * @return string, html search result
	 */
	public static function getSearchParameterTop($foundItems){
		$anzahl = array(10 => 10, 25 => 25, 50 => 50, 100 => 100);

		$order = we_base_request::_(we_base_request::STRING, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->order : '', 'order');
		$mode = we_base_request::_(we_base_request::BOOL, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->mode : '', 'mode');
		$setView = we_base_request::_(we_base_request::INT, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->setView : '', 'setView');
		$_anzahl = we_base_request::_(we_base_request::INT, 'we_cmd', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->searchclassFolder->anzahl : '', 'anzahl');
		$id = we_base_request::_(we_base_request::INT, 'id', isset($GLOBALS['we_doc']) ? $GLOBALS['we_doc']->ID : '');
		$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction', (isset($GLOBALS['we_transaction']) ? $GLOBALS['we_transaction'] : 0));

		return
			we_html_tools::hidden("we_transaction", $we_transaction) .
			we_html_tools::hidden("order", $order) .
			we_html_tools::hidden("todo", "") .
			we_html_tools::hidden("mode", $mode) .
			we_html_tools::hidden("setView", $setView) .
			'<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>' . we_html_tools::getPixel(19, 12) . '</td>
		<td style="font-size:12px;width:125px;">' . g_l('searchtool', '[eintraege_pro_seite]') . ':</td>
		<td class="defaultgray" style="width:60px;">' . we_html_tools::htmlSelect("anzahl", $anzahl, 1, $_anzahl, "", array('onchange' => 'this.form.elements[\'searchstart\'].value=0;search(false);')) . '</td>
		<td>' . self::getNextPrev($foundItems) . '</td>
		<td>' . we_html_tools::getPixel(10, 12) . '</td>
		<td style="width:50px;">' . we_html_button::create_button("image:btn_new_dir", "javascript:top.we_cmd('new_document','" . FILE_TABLE . "','','" . we_base_ContentTypes::FOLDER . "','','" . $id . "')", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("image:iconview", "javascript:setview(1);", true, 40, "", "", "", false) . '</td>
		<td>' . we_html_button::create_button("image:listview", "javascript:setview(0);", true, 40, "", "", "", false) . '</td>
	</tr>
	<tr><td colspan="12">' . we_html_tools::getPixel(1, 12) . '</td></tr>
</table>';
	}

	public static function getSearchParameterBottom($table, $foundItems){
		switch($table){
			case TEMPLATES_TABLE:
				$publishButton = $publishButtonCheckboxAll = "";
				break;
			default:
				if(permissionhandler::hasPerm('PUBLISH')){
					$publishButtonCheckboxAll = we_html_forms::checkbox(1, 0, "publish_all", "", false, "middlefont", "checkAllPubChecks()");
					$publishButton = we_html_button::create_button("publish", "javascript:publishDocs();", true, 100, 22, "", "");
				} else {
					$publishButton = $publishButtonCheckboxAll = "";
				}
		}

		return
			'<table border="0" cellpadding="0" cellspacing="0" style="margin-top:20px;">
	<tr>
	 <td>' . $publishButtonCheckboxAll . '</td>
	 <td style="font-size:12px;width:125px;">' . $publishButton . '</td>
	 <td class="defaultgray" style="width:60px;" id="resetBusy">' . we_html_tools::getPixel(30, 12) . '</td>
	 <td style="width:370px;">' . self::getNextPrev($foundItems, false) . '</td>
	</tr>
</table>';
	}

	/**
	 * @abstract generates html for paging GUI
	 * @return string, html for paging GUI
	 */
	private static function getNextPrev($we_search_anzahl, $isTop = true){
		if(($obj = we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'obj'))){
			$anzahl = $_SESSION['weS']['weSearch']['anzahl'];
			$searchstart = $_SESSION['weS']['weSearch']['searchstart'];
		} else {
			$obj = $GLOBALS['we_doc'];
			$anzahl = $obj->searchclassFolder->anzahl;
			$searchstart = $obj->searchclassFolder->searchstart;
		}

		$out = '<table cellpadding="0" cellspacing="0" border="0"><tr><td>' .
			($searchstart ?
				we_html_button::create_button("back", "javascript:back(" . $anzahl . ");") :
				we_html_button::create_button("back", "", true, 100, 22, "", "", true)
			) .
			'</td><td>' . we_html_tools::getPixel(10, 2) . '</td>
        <td class="defaultfont"><b>' . (($we_search_anzahl) ? $searchstart + 1 : 0) . '-' .
			(($we_search_anzahl - $searchstart) < $anzahl ? $we_search_anzahl : $searchstart + $anzahl) .
			' ' . g_l('global', '[from]') . ' ' . $we_search_anzahl . '</b></td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>' .
			(($searchstart + $anzahl) < $we_search_anzahl ?
				we_html_button::create_button("next", "javascript:next(" . $anzahl . ");") :
				we_html_button::create_button("next", "", true, 100, 22, "", "", true)
			) .
			'</td><td>' . we_html_tools::getPixel(10, 2) . '</td><td>';

		$pages = array();
		if($anzahl){
			for($i = 0; $i < ceil($we_search_anzahl / $anzahl); $i++){
				$pages[($i * $anzahl)] = ($i + 1);
			}
		}

		$page = ($anzahl ? ceil($searchstart / $anzahl) * $anzahl : 0);

		$select = we_html_tools::htmlSelect("page", $pages, 1, $page, false, array("onchange" => "this.form.elements['searchstart'].value = this.value; search(false);"));

		if(!we_base_request::_(we_base_request::BOOL, 'we_cmd', false, 'setInputSearchstart') && !defined('searchstart') && $isTop){
			define("searchstart", true);
			$out .= we_html_tools::hidden("searchstart", $searchstart);
		}

		$out .= $select .
			'</td></tr></table>';

		return $out;
	}

	/**
	 * @abstract writes the complete html code
	 * @return string, html
	 */
	public static function getHTMLforDoclist($content){
		$out = '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%;">
<tr>
	<td class="defaultfont">';

		foreach($content as $i => $c){
			$_forceRightHeadline = (isset($c["forceRightHeadline"]) && $c["forceRightHeadline"]);
			$icon = (isset($c["icon"]) && $c["icon"]) ? ('<img src="' . ICON_DIR . $c["icon"] . '" width="64" height="64" alt="" style="margin-left:20px;" />') : "";
			$headline = (isset($c["headline"]) && $c["headline"]) ? ('<div class="weMultiIconBoxHeadline" style="margin-bottom:10px;">' . $c["headline"] . '</div>') : "";
			$mainContent = (isset($c["html"]) && $c["html"]) ? $c["html"] : "";
			$leftWidth = (isset($c["space"]) && $c["space"]) ? abs($c["space"]) : 0;
			$leftContent = $icon ? : (($leftWidth && (!$_forceRightHeadline)) ? $headline : "");
			$rightContent = '<div class="defaultfont">' . ((($icon && $headline) || ($leftContent === "") || $_forceRightHeadline) ? ($headline . '<div>' . $mainContent . '</div>') : '<div>' . $mainContent . '</div>') . '</div>';

			$out .= '<div style="margin-left:0px" >';

			if($leftContent || $leftWidth){
				if((!$leftContent) && $leftWidth){
					$leftContent = "&nbsp;";
				}
				$out .= '<div style="float:left;width:' . $leftWidth . 'px">' . $leftContent . '</div>';
			}

			$out .= $rightContent .
				'</div>' . ((we_base_browserDetect::isIE()) ? we_html_element::htmlBr() : '');

			if($i < (count($content) - 1) && (!isset($c["noline"]))){
				$out .= '<div style="border-top: 1px solid #AFB0AF;margin:10px 0 10px 0;clear:both;"></div>';
			}
		}

		return $out . '</td></tr></table>';
	}

}
