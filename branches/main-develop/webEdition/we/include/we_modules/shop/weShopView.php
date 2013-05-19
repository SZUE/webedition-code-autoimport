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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/* the parent class of storagable webEdition classes */


class weShopView{

	var $db;
	var $frameset;
	var $topFrame;
	var $raw;

	function __construct($frameset = '', $topframe = 'top.content'){
		$this->db = new DB_WE();
		$this->setFramesetName($frameset);
		$this->setTopFrame($topframe);
		//$this->raw = new weShop();
	}

	//----------- Utility functions ------------------

	function htmlHidden($name, $value = ''){
		return we_html_element::htmlHidden(array('name' => trim($name), 'value' => oldHtmlspecialchars($value)));
	}

	//-----------------Init -------------------------------

	function setFramesetName($frameset){
		$this->frameset = $frameset;
	}

	function setTopFrame($frame){
		$this->topFrame = $frame;
	}

	//------------------------------------------------


	function getCommonHiddens($cmds = array()){
		return $this->htmlHidden('cmd', (isset($cmds['cmd']) ? $cmds['cmd'] : '')) .
			$this->htmlHidden('cmdid', (isset($cmds['cmdid']) ? $cmds['cmdid'] : '')) .
			$this->htmlHidden('pnt', (isset($cmds['pnt']) ? $cmds['pnt'] : '')) .
			$this->htmlHidden('tabnr', (isset($cmds['tabnr']) ? $cmds['tabnr'] : ''));
	}

	function getJSTop_tmp(){//taken from old edit_shop_frameset.php

		// grep the last element from the year-set, wich is the current year
		$this->db->query('SELECT DATE_FORMAT(DateOrder,"%Y") AS DateOrd FROM ' . SHOP_TABLE . ' ORDER BY DateOrd');
		while($this->db->next_record()) {
			$strs = array($this->db->f("DateOrd"));
			$yearTrans = end($strs);
		}
		// print $yearTrans;
		/// config
		$feldnamen = explode('|', f('SELECT strFelder FROM ' . ANZEIGE_PREFS_TABLE . ' WHERE strDateiname="shop_pref"','strFelder',$this->db));
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
		$resultD = f('SELECT count(Name) as Anzahl FROM ' . LINK_TABLE . ' WHERE Name ="'.WE_SHOP_TITLE_FIELD_NAME.'"', 'Anzahl', $this->db);


		$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
		$title = '';
		foreach($GLOBALS['_we_available_modules'] as $modData){
			if($modData["name"] == $mod){
				$title = 'webEdition ' . g_l('global', "[modules]") . ' - ' . $modData["text"];
				break;
			}
		}

		$out = '
var hot = 0;

parent.document.title = "' . $title . '";

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd(){
	var args = "";

	var url = "' .WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]){
		case "new_shop":
			' . $this->topFrame . '.resize.shop_properties.location="<?php print WE_SHOP_MODULE_DIR; ?>edit_shop_editorFrameset.php";
			break;
		case "delete_shop":
			if (' . $this->topFrame . '.resize && ' . $this->topFrame . '.resize.shop_properties.edbody.hot && ' . $this->topFrame . '.resize.shop_properties.edbody.hot == 1 ) {
				if(confirm("' . g_l("modules_shop", "[del_shop]") . '")){
					' . $this->topFrame . '.resize.shop_properties.edbody.deleteorder();
				}
			} else {
				' . we_message_reporting::getShowMessageCall(g_l("modules_shop", "[nothing_to_delete]"), we_message_reporting::WE_MESSAGE_NOTICE) . '
			}
			break;
		case "new_article":
			if (' . $this->topFrame . '.resize && ' . $this->topFrame . '.resize.shop_properties.edbody.hot && ' . $this->topFrame . '.resize.shop_properties.edbody.hot == 1 ) {
				top.content.resize.shop_properties.edbody.neuerartikel();
			} else {
				' . we_message_reporting::getShowMessageCall(g_l("modules_shop", "[no_order_there]"), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			break;
		case "revenue_view":
			' . ($resultD > 0 ? $this->topFrame . '.resize.shop_properties.location=' . WE_SHOP_MODULE_DIR . 'edit_shop_editorFramesetTop.php?typ=document' :
			(!empty($resultO) ? $this->topFrame . '.resize.shop_properties.location=' . WE_SHOP_MODULE_DIR . 'edit_shop_editorFramesetTop.php?typ=object&ViewClass=$classid' :
			'')) . '
			break;
		';

		$yearshop = "2002";
		$z = 1;
		while($yearshop <= date("Y")) {
			$out .= '
		case "year' . $yearshop . '":
			' . $this->topFrame . '.location="' . WE_MODULES_DIR . 'show.php?mod=shop&year=' . $yearshop . '";
				break;
		';
			$yearshop++;
			$z++;
		}

		$out .= '
		case "pref_shop":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_pref.php","shoppref",-1,-1,470,600,true,true,true,false);
			break;

		case "edit_shop_vats":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_vats.php","edit_shop_vats",-1,-1,500,450,true,false,true,false);
			break;

		case "edit_shop_shipping":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_shipping.php","edit_shop_shipping",-1,-1,700,600,true,false,true,false);
			break;
		case "edit_shop_status":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_status.php","edit_shop_status",-1,-1,700,780,true,true,true,false);
			break;
		case "edit_shop_vat_country":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_vat_country.php","edit_shop_vat_country",-1,-1,700,780,true,true,true,false);
			break;
		case "payment_val":
			//var wind = new jsWindow("' . WE_SHOP_MODULE_DIR . 'edit_shop_payment.inc.php","shoppref",-1,-1,520,720,true,false,true,false);
			break;

		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("opener.top.content.we_cmd("+args+")");
			break;
	}
}
		';

		return we_html_element::jsScript(JS_DIR . 'images.js') . we_html_element::jsScript(JS_DIR . 'windows.js') .
			we_html_element::jsElement($out);
	}

	function getJSTop(){//TODO: is this shop-code or a copy paste from another module?
		return we_html_element::jsScript(JS_DIR . 'windows.js') . we_html_element::jsElement('
var get_focus = 1;
var activ_tab = 1;
var hot= 0;
var scrollToVal=0;

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "new_raw":
			if(' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
				' . $this->topFrame . '.hot = 1;
				' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value = arguments[0];
				' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value = arguments[1];
				' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value = 1;
				' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
			} else {
				setTimeout(\'we_cmd("new_raw");\', 10);
			}
			break;

		case "delete_raw":
			if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;
			' . (!we_hasPerm("DELETE_RAW") ?
					( we_message_reporting::getShowMessageCall(g_l('modules_shop', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)) :
					('
					if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
						if (confirm("' . g_l('modules_shop', '[delete_alert]') . '")) {
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			')) . '
			break;

		case "save_raw":
			if(top.content.resize.right.editor.edbody.document.we_form.cmd.value=="home") return;


					if (' . $this->topFrame . '.resize.right.editor.edbody.loaded) {
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
							' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;

							' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('modules_shop', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			break;

		case "edit_raw":
			' . $this->topFrame . '.hot=0;
			' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmd.value=arguments[0];
			' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.cmdid.value=arguments[1];
			' . $this->topFrame . '.resize.right.editor.edbody.document.we_form.tabnr.value=' . $this->topFrame . '.activ_tab;
			' . $this->topFrame . '.resize.right.editor.edbody.submitForm();
		break;
		case "load":
			' . $this->topFrame . '.cmd.location="' . $this->frameset . '?pnt=cmd&pid="+arguments[1]+"&offset="+arguments[2]+"&sort="+arguments[3];
		break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("top.opener.top.we_cmd(" + args + ")");
	}
}');
	}

	function getJSProperty(){
		return we_html_element::jsScript(JS_DIR . "windows.js") .
			we_html_element::jsElement('
var loaded=0;

function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd() {
	var args = "";
	var url = "' . WEBEDITION_DIR . 'we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "switchPage":
			document.we_form.cmd.value=arguments[0];
			document.we_form.tabnr.value=arguments[1];
			submitForm();
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += "arguments["+i+"]" + ((i < (arguments.length-1)) ? "," : "");
			}
			eval("top.content.we_cmd("+args+")");
	}
}
' . $this->getJSSubmitFunction());
	}

	function getJSTreeHeader(){
		return '
function doUnload() {
	if (!!jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
}

function we_cmd(){
	var args = "";
	var url = "' . $this->frameset . '?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+escape(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += \'arguments[\'+i+\']\' + ((i < (arguments.length-1)) ? \',\' : \'\');
			}
			eval(\'top.content.we_cmd(\'+args+\')\');
	}
}' .
			$this->getJSSubmitFunction("cmd");
	}

	function getJSSubmitFunction($def_target = "edbody", $def_method = "post"){
		return '
function submitForm() {
	var f = self.document.we_form;

	f.target = (arguments[0]?arguments[0]:"' . $def_target . '");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"' . $def_method . '");

	f.submit();
}';
	}

	function processCommands(){
		if(isset($_REQUEST['cmd'])){
			switch($_REQUEST['cmd']){
				case 'new_raw':
					$this->raw = new weShop();
					print we_html_element::jsElement(
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
					);
					break;
				case 'edit_raw':
					$this->raw = new weShop($_REQUEST['cmdid']);
					print we_html_element::jsElement(
							$this->topFrame . '.resize.right.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->raw->Text) . '";' .
							$this->topFrame . '.resize.right.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
					);
					break;
				case 'save_raw':
					if($this->raw->filenameNotValid()){
						print we_html_element::jsElement(
								we_message_reporting::getShowMessageCall(g_l('modules_shop', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						break;
					}

					$newone = ($this->raw->ID ? false : true);

					$this->raw->save();

					//$ttrow = getHash('SELECT * FROM ' . RAW_TABLE . ' WHERE ID=' . intval($this->raw->ID), $this->db);
					$tt = addslashes($tt != '' ? $tt : $this->raw->Text);
					$js = ($newone ?
							'
var attribs = new Array();
attribs["icon"]="' . $this->raw->Icon . '";
attribs["id"]="' . $this->raw->ID . '";
attribs["typ"]="item";
attribs["parentid"]="0";
attribs["text"]="' . $tt . '";
attribs["disable"]=0;
attribs["tooltip"]="";' .
							$this->topFrame . '.treeData.addSort(new ' . $this->topFrame . '.node(attribs));' .
							$this->topFrame . '.drawTree();' :
							$this->topFrame . '.updateEntry(' . $this->raw->ID . ',"' . $tt . '");'
						);
					print we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_saved_ok]'), we_message_reporting::WE_MESSAGE_NOTICE)
					);
					break;
				case 'delete_raw':
					$js = '' . $this->topFrame . '.deleteEntry(' . $this->raw->ID . ');';

					$this->raw->delete();
					$this->raw = new weShop();

					print we_html_element::jsElement(
							$js .
							we_message_reporting::getShowMessageCall(g_l('modules_shop', '[raw_deleted]'), we_message_reporting::WE_MESSAGE_NOTICE)
					);
					break;
				case 'switchPage':
					break;
				default:
			}
		}

		$_SESSION['raw_session'] = serialize($this->raw);
	}

	function processVariables(){
		if(isset($_SESSION['raw_session'])){
			$this->raw = unserialize($_SESSION['raw_session']);
		}

		if(is_array($this->raw->persistent_slots)){
			foreach($this->raw->persistent_slots as $key => $val){
				$varname = $val;
				if(isset($_REQUEST[$varname])){
					$this->raw->{$val} = $_REQUEST[$varname];
				}
			}
		}

		if(isset($_REQUEST['page']))
			if(isset($_REQUEST['page'])){
				$this->page = $_REQUEST['page'];
			}
	}

	function new_array_splice(&$a, $start, $len = 1){
		$ks = array_keys($a);
		$k = array_search($start, $ks);
		if($k !== false){
			$ks = array_splice($ks, $k, $len);
			foreach($ks as $k)
				unset($a[$k]);
		}
	}

}