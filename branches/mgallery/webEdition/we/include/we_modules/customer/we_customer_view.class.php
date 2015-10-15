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
/* the parent class of storagable webEdition classes */

class we_customer_view extends we_modules_view{
	var $customer;
	var $settings;

	const ERR_SAVE_BRANCH = -10;
	const ERR_SAVE_FIELD_INVALID = -7;
	const ERR_SAVE_PROPERTY = -5;
	const ERR_SAVE_FIELD_EXISTS = -4;
	const ERR_SAVE_FIELD_NOT_EMPTY = -3;

	function __construct(){
		$frameset = WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php';
		$topframe = 'top.content';
		parent::__construct($frameset, $topframe);
		$this->customer = new we_customer_customer();
		$this->settings = new we_customer_settings();
		$this->settings->customer = & $this->customer;
		$this->settings->load();
	}

	function getCommonHiddens($cmds = array()){
		return we_html_element::htmlHiddens(array(
				'cmd' => (isset($cmds['cmd']) ? $cmds['cmd'] : ''),
				'pnt' => (isset($cmds['pnt']) ? $cmds['pnt'] : ''),
				'cmdid' => (isset($cmds['cmdid']) ? $cmds['cmdid'] : ''),
				'activ_sort' => (isset($cmds['activ_sort']) ? $cmds['activ_sort'] : ''),
				'branch' => we_base_request::_(we_base_request::STRING, 'branch', g_l('modules_customer', '[common]'))
		));
	}

	function getJSTop(){
		$mod = we_base_request::_(we_base_request::STRING, 'mod', '');
		$modData = we_base_moduleInfo::getModuleData($mod);
		$title = isset($modData['text']) ? 'webEdition ' . g_l('global', '[modules]') . ' - ' . $modData['text'] : '';

		return
			parent::getJSTop() .
			we_html_element::jsElement('
parent.document.title = "' . $title . '";
WE().consts.dirs.WE_CUSTOMER_MODULE_DIR="' . WE_CUSTOMER_MODULE_DIR . '";
WE().consts.g_l.customer.view={
	save_changed_customer:"' . g_l('modules_customer', '[save_changed_customer]') . '",
	delete_alert:"' . g_l('modules_customer', '[delete_alert]') . '",
	nothing_to_delete:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[nothing_to_delete]')) . '",
	nothing_to_save:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[nothing_to_save]')) . '"
};

var topFrame=top.content;
var frameUrl="' . $this->frameset . '";
') .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_top.js');
	}

	function getJSProperty(){
		return we_html_element::jsElement('
var loaded=0;

function refreshForm(){
	if(document.we_form.cmd.value!="home"){
		we_cmd("switchPage",top.content.activ_tab);
		top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Username) . '";
	}
}' . $this->getJSSubmitFunction()) .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_property.js');
	}

	function getJSAdmin(){
		return we_html_element::jsElement('
var frameUrl="' . $this->frameset . '";
var g_l={
	del_fild_question:"' . g_l('modules_customer', '[del_fild_question]') . '",
	reset_edit_order_question:"' . g_l('modules_customer', '[reset_edit_order_question]') . '",
	other:"' . g_l('modules_customer', '[other]') . '",
	no_field: "' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[no_field]')) . '",
	no_branch:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[no_branch]')) . '",
	branch_no_edit:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[branch_no_edit]')) . '",
	we_fieldname_notValid:"' . we_message_reporting::prepareMsgForJS(g_l('modules_customer', '[we_fieldname_notValid]')) . '"
};' . $this->getJSSubmitFunction("customer_admin")
			) .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_admin.js');
	}

	function getJSTreeHeader(){
		return we_html_element::jsElement($this->getJSSubmitFunction('cmd', 'post', 'we_form_treeheader')) .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_treeHeader.js');
	}

	function getJSSearch(){
		return we_html_element::jsElement('
var frames={
	"set":"' . $this->frameset . '"
};
' .
				$this->getJSSubmitFunction("search")
			) .
			we_html_element::jsScript(WE_JS_CUSTOMER_MODULE_DIR . 'customer_search.js');
	}

	function getJSSettings(){
		return '
function doUnload() {
	WE().util.jsWindow.prototype.closeAll(window);
}

function we_cmd(){
	var args = "";
	var url = "' . $this->frameset . '?";
	for(var i = 0; i < arguments.length; i++){
		url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
		if(i < (arguments.length - 1)){
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "save_settings":
			document.we_form.cmd.value=arguments[0];
			submitForm();
		break;
		default:
	}
}' . $this->getJSSubmitFunction("customer_settings");
	}

	/* use parent
	  function getJSSubmitFunctionBack($def_target = 'edbody', $def_method = 'post'){}
	 */

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'new_customer':
				$this->customer = new we_customer_customer();
				$this->settings->initCustomerWithDefaults($this->customer);
				echo we_html_element::jsElement(
					'top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Username) . '";' .
					'top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
				);
				break;
			case 'customer_edit':
				$this->customer = new we_customer_customer(we_base_request::_(we_base_request::INT, "cmdid"));
				echo we_html_element::jsElement(
					'top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->customer->Username) . '";' .
					'top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";'
				);
				break;
			case 'save_customer':
				$js = '';
				$this->customer->Username = trim($this->customer->Username);
				if(!$this->customer->Username){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_customer', '[username_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->customer->filenameNotValid()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_customer', '[we_filename_notValid]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$newone = ($this->customer->ID ? false : true);

				$exists = f('SELECT ID FROM ' . CUSTOMER_TABLE . ' WHERE Username="' . $this->db->escape($this->customer->Username) . '"' . ($newone ? '' : ' AND ID!=' . $this->customer->ID), '', $this->db);
				if($exists){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[username_exists]'), $this->customer->Username), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				if($_SESSION['weS']['customer_session']->Password != $this->customer->Password || $this->customer->LoginDenied || $this->customer->AutoLoginDenied){
//delete autologins, if password is changed
					$this->db->query('DELETE FROM ' . CUSTOMER_AUTOLOGIN_TABLE . ' WHERE WebUserID=' . intval($this->customer->ID));
				}

				$saveOk = $this->customer->save();

				if($saveOk){
					$tt = strtr(addslashes(f('SELECT ' . $this->settings->treeTextFormatSQL . ' AS treeFormat FROM ' . CUSTOMER_TABLE . ' WHERE ID=' . intval($this->customer->ID), '', $this->db)), array('>' => '&gt;', '<' => '&lt;'));
					$js = ($newone ? '
var attribs = {
	id:"' . $this->customer->ID . '",
	typ:"item",
	parentid:"0",
	text:"' . $tt . '",
	disable:"0",
	tooltip:"' . (($this->customer->Forename != "" || $this->customer->Surname != "") ? $this->customer->Forename . "&nbsp;" . $this->customer->Surname : "") . '"
}
top.content.treeData.addSort(new top.content.node(attribs));
top.content.applySort();' :
							'top.content.updateEntry({id:' . $this->customer->ID . ',text:"' . $tt . '"});
							top.content.editor.edheader.document.getElementById("titlePath").innerText="' . $this->customer->Username . '";'
						);
				} else {
					$js = '';
				}

				echo we_html_element::jsElement(
					$js . ($saveOk ?
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[customer_saved_ok]'), addslashes($this->customer->Username)), we_message_reporting::WE_MESSAGE_NOTICE) :
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[customer_saved_nok]'), addslashes($this->customer->Username)), we_message_reporting::WE_MESSAGE_ERROR)
					)
				);
				break;
			case 'delete_customer':
				$oldid = $this->customer->ID;
				$this->customer->delete();
				$this->customer = new we_customer_customer();

				echo we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(g_l('modules_customer', '[customer_deleted]'), we_message_reporting::WE_MESSAGE_NOTICE) .
					'top.content.deleteEntry("' . $oldid . '");
top.content.editor.edheader.location="' . $this->frameset . '?home=1&pnt=edheader";
top.content.editor.edbody.location="' . $this->frameset . '?home=1&pnt=edbody"
top.content.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";'
				);

				break;
			case 'switchPage':
				break;
			case 'show_admin':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('
url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=customer_admin";
new (WE().util.jsWindow)(window, url,"customer_admin",-1,-1,600,420,true,true,true,false);');
				break;
			case 'save_field':
				$branch = we_base_request::_(we_base_request::STRING, 'branch');
				$field = we_base_request::_(we_base_request::STRING, 'field');
				$field_name = we_base_request::_(we_base_request::STRING, 'name');
				$field_type = we_base_request::_(we_base_request::STRING, 'field_type');
				$field_default = we_base_request::_(we_base_request::STRING, 'field_default');
				$field_encrypt = we_base_request::_(we_base_request::BOOL, 'field_encrypt');


				$saveret = $this->saveField($field, $branch, $field_name, $field_type, $field_default, $field_encrypt);

				switch($saveret){
					case self::ERR_SAVE_BRANCH:
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[branch_no_edit]'), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case self::ERR_SAVE_FIELD_INVALID:
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[we_fieldname_notValid]'), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case self::ERR_SAVE_PROPERTY:
						$js = we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[cannot_save_property]'), $field_name), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case self::ERR_SAVE_FIELD_EXISTS:
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[fieldname_exists]'), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					case self::ERR_SAVE_FIELD_NOT_EMPTY:
						$js = we_message_reporting::getShowMessageCall(g_l('modules_customer', '[field_not_empty]'), we_message_reporting::WE_MESSAGE_ERROR);
						break;
					default:
						$this->customer->loadPresistents();
						$sort = $this->settings->getEditSort();
						$sortarray = makeArrayFromCSV($sort);
						$orderedarray = $this->customer->persistent_slots;

						if(count($sortarray) != count($orderedarray)){
							if(count($sortarray) < count($orderedarray)){
								$sortarray[] = max($sortarray) + 1;
							}
							if(count($sortarray) < count($orderedarray)){
								$sortarray[] = max($sortarray) + 1;
							}
							if(count($sortarray) < count($orderedarray)){
								$sortarray[] = max($sortarray) + 1;
							}
							if(count($sortarray) != count($orderedarray)){
								$sortarray = range(0, count($orderedarray) - 1);
							}
						}
						$this->settings->setEditSort(implode(',', $sortarray));
						$this->settings->save();

						$js = '
opener.submitForm();
opener.opener.refreshForm();
close();';
				}
				echo we_html_element::jsElement($js);

				break;
			case 'delete_field':
				$field = we_base_request::_(we_base_request::STRING, 'fields_select');

				$sort = $this->settings->getEditSort();
				$sortarray = makeArrayFromCSV($sort);
				$orderedarray = $this->customer->persistent_slots;
				if(count($sortarray) != count($orderedarray)){
					$sortarray = range(0, count($orderedarray) - 1);
				}
				$orderedarray = array_combine($sortarray, $orderedarray);
				ksort($orderedarray);
				$curpos = array_search($field, $orderedarray);
				$curposS = array_search($curpos, $sortarray);
				unset($sortarray[$curposS]);
				foreach($sortarray as &$val){
					if($val >= $curpos){
						$val--;
					}
				}
				if($sortarray[count($sortarray) - 1] == ''){
					array_pop($sortarray);
				}
				$this->settings->setEditSort(implode(',', $sortarray));
				$this->settings->save();

				$ber = '';
				$fname = $this->customer->transFieldName($field, $ber);

				$this->deleteField(($ber == '' && preg_match('%' . g_l('modules_customer', '[other]') . '%i', $field) ? $fname : $field));

				$this->customer->loadPresistents();
				echo we_html_element::jsElement(
					we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[field_deleted]'), $fname, $ber), we_message_reporting::WE_MESSAGE_NOTICE) .
					'opener.refreshForm();'
				);
				break;
			case 'reset_edit_order':
				$orderedarray = $this->customer->persistent_slots;
				$sortarray = range(0, count($orderedarray) - 1);
				$this->settings->setEditSort(implode(',', $sortarray));
				$this->settings->save();
				break;
			case 'move_field_up':
				$field = we_base_request::_(we_base_request::STRING, 'fields_select');
				$sort = $this->settings->getEditSort();
				$sortarray = makeArrayFromCSV($sort);
				$orderedarray = $this->customer->persistent_slots;
				if(count($sortarray) != count($orderedarray)){
					if(count($sortarray) < count($orderedarray)){
						$sortarray[] = max($sortarray) + 1;
					}
					if(count($sortarray) != count($orderedarray)){
						$sortarray = range(0, count($orderedarray) - 1);
					}
				}
				$orderedarray = array_combine($sortarray, $orderedarray);
				ksort($orderedarray);

				$curpos = array_search($field, $orderedarray);
				$curpos1 = $curpos - 1;
				if($curpos != 0){
					$sort = str_replace(array(',' . $curpos . ',', ',' . $curpos1 . ','), array(',XX,', ',YY,'), $sort);
					$sort = str_replace(array(',XX,', ',YY,'), array(',' . $curpos1 . ',', ',' . $curpos . ','), $sort);

					$this->settings->setEditSort($sort);
					$this->settings->save();
					$this->customer->loadPresistents();
				}
				echo we_html_element::jsElement('opener.refreshForm();');

				break;
			case 'move_field_down':
				$field = we_base_request::_(we_base_request::STRING, 'fields_select');
				$sort = $this->settings->getEditSort();
				$sortarray = makeArrayFromCSV($sort);
				$orderedarray = $this->customer->persistent_slots;
				if(count($sortarray) != count($orderedarray)){
					if(count($sortarray) < count($orderedarray)){
						$sortarray[] = max($sortarray) + 1;
					}
					if(count($sortarray) != count($orderedarray)){
						$sortarray = range(0, count($orderedarray) - 1);
					}
				}
				$orderedarray = array_combine($sortarray, $orderedarray);
				ksort($orderedarray);

				$curpos = array_search($field, $orderedarray);
				$curpos1 = $curpos + 1;
				if($curpos != count($orderedarray) - 1){
					$sort = str_replace(array(',' . $curpos . ',', ',' . $curpos1 . ','), array(',XX,', ',YY,'), $sort);
					$sort = str_replace(array(',XX,', ',YY,'), array(',' . $curpos1 . ',', ',' . $curpos . ','), $sort);
					$this->settings->setEditSort($sort);
					$this->settings->save();
					$this->customer->loadPresistents();
				}
				echo we_html_element::jsElement('opener.refreshForm();');

				break;
			case 'save_branch':
				$branch_new = we_base_request::_(we_base_request::STRING, 'name', '');
				$branch_old = we_base_request::_(we_base_request::STRING, 'branch', '');

				if($branch_new == g_l('modules_customer', '[common]') || $branch_new == g_l('modules_customer', '[other]') || $branch_new == g_l('modules_customer', '[all]')){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('modules_customer', '[branch_no_edit]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}

				if($branch_new != $branch_old){
					$arr = $this->customer->getBranchesNames();

					if(in_array($branch_new, $arr)){
						echo we_html_element::jsElement(
							we_message_reporting::getShowMessageCall(g_l('modules_customer', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR)
						);
						return;
					}
				}

				if($this->saveBranch($branch_old, $branch_new) == -5){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(sprintf(g_l('modules_customer', '[cannot_save_property]'), $field), we_message_reporting::WE_MESSAGE_ERROR)
					);
				} else {
					$this->customer->loadPresistents();
					echo we_html_element::jsElement('
opener.document.we_form.branch.value="' . g_l('modules_customer', '[other]') . '";
opener.submitForm();
opener.opener.document.we_form.branch.value="' . g_l('modules_customer', '[common]') . '";
opener.opener.refreshForm();
close();');
				}

				break;
			case 'show_sort_admin':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=sort_admin";
new (WE().util.jsWindow)(window, url,"sort_admin",-1,-1,750,500,true,true,true,true);');

				break;
			case 'add_sort':
				$cout = 0;
				$found = false;
				while(!$found){//FIXME: might be an endless loop
					$cname = g_l('modules_customer', '[sort_name]') . $cout;
					if(!in_array($cname, array_keys($this->settings->SortView))){
						$found = true;
					}
					$cout++;
				}
				$this->settings->SortView[$cname] = array();

				break;
			case 'del_sort':
				if(($i = we_base_request::_(we_base_request::STRING, 'sortindex')) !== false){
					unset($this->settings->SortView[$i]);
				}
				break;
			case 'add_sort_field':
				if(($i = we_base_request::_(we_base_request::STRING, 'sortindex')) !== false){
					$this->settings->SortView[$i][] = array('branch' => '', 'field' => '', 'order' => '');
				}
				break;
			case 'del_sort_field':
				if(($i = we_base_request::_(we_base_request::STRING, 'sortindex')) !== false &&
					($j = we_base_request::_(we_base_request::INT, 'fieldindex')) !== false){

					unset($this->settings->SortView[$i][$j]);
				}
				break;
			case 'save_sort':

				$this->settings->save();
				$_sorting = 'opener.top.content.addSorting("' . g_l('modules_customer', '[no_sort]') . '");' . "\n";
				foreach(array_keys($this->settings->SortView) as $_sort){
					$_sorting .= 'opener.top.content.addSorting("' . $_sort . '");' . "\n";
				}

				echo we_html_element::jsScript(JS_DIR . "global.js", 'initWE();') .
				we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('modules_customer', '[sort_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
var selected = opener.top.content.document.we_form_treeheader.sort.selectedIndex;
opener.top.content.document.we_form_treeheader.sort.options.length=0;
' . $_sorting . '

if(selected<opener.top.content.document.we_form_treeheader.sort.options.length){
	opener.top.content.document.we_form_treeheader.sort.selectedIndex = selected;
} else {
	opener.top.content.document.we_form_treeheader.sort.selectedIndex = opener.top.content.document.we_form_treeheader.sort.options.length-1;
}

opener.top.content.applySort();
self.close();');
				break;
			case 'applySort':
				echo we_html_element::jsElement('top.content.clearTree();');
				break;
			case 'show_search':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=search&search=1&keyword=' . we_base_request::_(we_base_request::STRING, "keyword") . '";
						new (WE().util.jsWindow)(window, url,"search",-1,-1,650,600,true,true,true,false);');
				break;
			case 'show_customer_settings':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=settings";
						new (WE().util.jsWindow)(window, url,"customer_settings",-1,-1,550,250,true,true,true,false);');
				break;
			case 'import_customer':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=import";
						new (WE().util.jsWindow)(window, url,"import_customer",-1,-1,640,600,true,true,true,false);');
				break;
			case 'export_customer':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('url ="' . WE_CUSTOMER_MODULE_DIR . 'edit_customer_frameset.php?pnt=export";
						new (WE().util.jsWindow)(window, url,"export_customer",-1,-1,640,600,true,true,true,false);');
				break;
			case 'save_settings':
				foreach($this->settings->getAllSettings() as $k => $v){
					$set = we_base_request::_(we_base_request::STRING, $k);
					if($set !== false){
						$this->settings->setSettings($k, $set);
					}
				}
				foreach($this->settings->properties as $k => $v){
					$set = we_base_request::_(we_base_request::STRING, $k);
					if($set !== false){
						$this->settings->properties[$k] = $set;
					}
				}
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement(
					$this->settings->save() ?
						we_message_reporting::getShowMessageCall(g_l('modules_customer', '[settings_saved]'), we_message_reporting::WE_MESSAGE_NOTICE) . 'self.close();' :
						we_message_reporting::getShowMessageCall(g_l('modules_customer', '[settings_not_saved]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			default:
		}

//FIXME: this data is not deleted on close - fix this!
		$_SESSION['weS']['customer_session'] = $this->customer;
	}

	function processVariables(){
		if(isset($_SESSION['weS']['customer_session'])){
			$this->customer = $_SESSION['weS']['customer_session'];
		}
		if(($sid = we_base_request::_(we_base_request::INT, 'sid'))){
			$this->customer = new we_customer_customer($sid);
			$_SESSION['weS']['customer_session'] = $this->customer;
		}
		we_base_util::convertDateInRequest($_REQUEST);
		if(is_array($this->customer->persistent_slots)){
			foreach($this->customer->persistent_slots as $varname){
				switch($varname){
					case 'LoginDenied':
						if(we_base_request::_(we_base_request::BOOL, 'LoginDenied')){
							$this->customer->LoginDenied = 1;
						} elseif(we_base_request::_(we_base_request::STRING, 'Username')){
							$this->customer->LoginDenied = 0;
						}
						break;
					case 'Password':
						$pw = we_base_request::_(we_base_request::RAW_CHECKED, 'Password');
						if($pw && $pw != we_customer_customer::NOPWD_CHANGE){//keep old pwd
							$this->customer->Password = we_customer_customer::cryptPassword($pw);
						}
						break;
					default:
						if(($v = we_base_request::_(we_base_request::STRING, $varname)) !== false){
							$isEncField = $this->settings->retriveFieldAdd($varname, 'encrypt');
							if($isEncField){
								if($v != we_customer_customer::ENCRYPTED_DATA){
									//fixme: should we store bin data??
									$this->customer->{$varname} = we_customer_customer::cryptData($v, SECURITY_ENCRYPTION_KEY, false);
								}
							} else {
								$this->customer->{$varname} = $v;
							}
						}
				}
			}
		}
		if(we_base_request::_(we_base_request::STRING, 'pnt') === 'sort_admin'){
			$counter = we_base_request::_(we_base_request::INT, 'counter');

			if($counter !== false){
				$this->settings->SortView = array();

				for($i = 0; $i < $counter; $i++){
					$sort_name = we_base_request::_(we_base_request::STRING, 'sort_' . $i)? :
						g_l('modules_customer', '[sort_name]') . '_' . $i;


					$fcounter = we_base_request::_(we_base_request::INT, 'fcounter_' . $i, 1);

					if($fcounter > -1){
						$this->settings->SortView[$sort_name] = array();
					}
					for($j = 0; $j < $fcounter; $j++){
						$new = array();
						if(($b = we_base_request::_(we_base_request::STRING, 'branch_' . $i . '_' . $j))){
							$new['branch'] = $b;
						}
						if(($field = we_base_request::_(we_base_request::STRING, 'field_' . $i . '_' . $j))){
							$new['field'] = ($new['branch'] == g_l('modules_customer', '[common]') ?
									str_replace(g_l('modules_customer', '[common]') . '_', '', $field) :
									$field);
						}
						if(($func = we_base_request::_(we_base_request::STRING, 'function_' . $i . '_' . $j))){
							$new['function'] = $func;
						}
						if(($ord = we_base_request::_(we_base_request::STRING, 'order_' . $i . '_' . $j))){
							$new['order'] = $ord;
						}
						$this->settings->SortView[$sort_name][$j] = $new;
					}
				}
			}
		}
	}

	function getFieldProperties($field){
		$ret = array(
			'encrypt' => $this->settings->retriveFieldAdd($field, 'encrypt'),
			'default' => $this->settings->retriveFieldAdd($field, 'default', '')
		);

		$props = $this->customer->getFieldDbProperties($field);

		if(isset($props['Field'])){
			$branch = '';
			$ret['name'] = $this->customer->transFieldName($props['Field'], $branch);
		}
		if(isset($props['Type'])){
			$ret['type'] = $this->settings->getFieldType($props['Field']);
		}


		return $ret;
	}

	// field - contains full field name with branche
	// branch - branch name
	// field_name - field name without branch name
	// field_default - predefined values

	function saveField($field, $branch, $field_name, $field_type, $field_default, $encrypt){
		if($branch == g_l('modules_customer', '[common]')){
			return self::ERR_SAVE_BRANCH;
		}

		if($branch == g_l('modules_customer', '[other]')){
			$field = str_replace(g_l('modules_customer', '[other]') . '_', '', $field);
		}
		if(!$field_name){
			return self::ERR_SAVE_FIELD_NOT_EMPTY;
		}

		$h = $this->customer->getFieldDbProperties($field);

		$new_field_name = (($branch && $branch != g_l('modules_customer', '[other]')) ? $branch . '_' : '') . $field_name;

		if(preg_match('|[^a-z0-9\_]|i', $new_field_name)){
			return self::ERR_SAVE_FIELD_INVALID;
		}

		if($field != $new_field_name && count($this->customer->getFieldDbProperties($new_field_name))){
			return self::ERR_SAVE_FIELD_EXISTS;
		}

		if($this->customer->isProperty($field) ||
			$this->customer->isProtected($field) ||
			$this->customer->isProperty($new_field_name) ||
			$this->customer->isProtected($new_field_name) ||
			($branch == g_l('modules_customer', '[other]') && $this->settings->isReserved($new_field_name))){
			return self::ERR_SAVE_PROPERTY;
		}

		if($h){
			$this->settings->removeFieldAdd($field);
		}
		$this->settings->storeFieldAdd($new_field_name, 'default', $field_default);
		$this->settings->storeFieldAdd($new_field_name, 'type', $field_type);
		$this->settings->storeFieldAdd($new_field_name, 'encrypt', $encrypt);

		$this->db->query('ALTER TABLE ' . CUSTOMER_TABLE . ' ' . ((count($h)) ? 'CHANGE ' . $field : 'ADD') . ' ' . $new_field_name . ' ' . ($encrypt ? 'BLOB' : $this->settings->getDbType($field_type, $new_field_name)) . ' NOT NULL');

		$this->settings->save();
	}

	function deleteField($field){
		$h = $this->customer->getFieldDbProperties($field);

		if($h){
			$this->db->query('ALTER TABLE ' . $this->customer->table . ' DROP ' . $field);
		}

		$this->settings->removeFieldAdd($field);

		$this->settings->save();
	}

	function saveBranch($old_branch, $new_branch){
		$h = $this->customer->getFieldsDbProperties();
		foreach($h as $k => $v){
			if(strpos($k, $old_branch) !== false){
				$banche = '';
				$fieldname = $this->customer->transFieldName($k, $banche);
				if($banche == $old_branch && $fieldname != ''){
					$this->db->query('ALTER TABLE ' . $this->customer->table . ' CHANGE ' . $k . ' ' . $new_branch . '_' . $fieldname . ' ' . $v['Type'] . (!empty($v["Default"]) ? " DEFAULT '" . $v["Default"] . "'" : '') . ' NOT NULL');
				}
			}
		}

		$this->settings->renameFieldAdds($old_branch, $new_branch);
		$this->settings->save();
	}

	function getSearchResults($keyword, $res_num = 0){
		if(!$res_num){
			$res_num = $this->settings->getMaxSearchResults();
		}

		$arr = explode(' ', strToLower($keyword));
		$array = array(
			'AND' => array($arr[0]),
			'OR' => array(),
			'AND NOT' => array()
		);


		for($i = 1; $i < count($arr); $i++){
			switch($arr[$i]){
				case 'not':
					$i++;
					$array['AND NOT'][count($array['NOT'])] = $arr[$i];
					break;
				case 'and':
					$i++;
					$array['AND'][count($array['AND'])] = $arr[$i];
					break;
				case'or':
					$i++;
					$array['OR'][count($array['OR'])] = $arr[$i];
					break;
				default:
			}
		}
		$condition = '';

		foreach($array as $ak => $av){
			foreach($av as $value){
				$conditionarr = array();
				foreach($this->customer->persistent_slots as $field){
					if(!$this->customer->isProtected($field) && $field != "Password"){
						$conditionarr[] = "$field LIKE '%$value%'";
					}
				}
				$condition.=($condition ?
						' ' . $ak . ' (' . implode(' OR ', $conditionarr) . ')' :
						' (' . implode(' OR ', $conditionarr) . ')'
					);
			}
		}

		$this->db->query('SELECT ID,CONCAT(Username, " (",Forename," ",Surname,")") AS user FROM ' . $this->db->escape($this->customer->table) . ($condition ? ' WHERE ' . $condition : '') . ' ORDER BY Username' . " LIMIT 0,$res_num");
		return array_map('oldHtmlspecialchars', $this->db->getAllFirst(false));
	}

	function getHTMLBranchSelect($with_common = true, $with_other = true){
		$branches_names = $this->customer->getBranchesNames();

		$select = new we_html_select(array('name' => 'branch'));

		if($with_common){
			$select->addOption(g_l('modules_customer', '[common]'), g_l('modules_customer', '[common]'));
		}

		if($with_other){
			$select->addOption(g_l('modules_customer', '[other]'), g_l('modules_customer', '[other]'));
		}

		foreach($branches_names as $branch){
			$select->addOption($branch, $branch);
		}

		return $select;
	}

	function getHTMLSortSelect($include_no_sort = true){
		$sort = new we_html_select(array('name' => 'sort', 'class' => 'weSelect'));

		$sort_names = array_keys($this->settings->SortView);

		if($include_no_sort){
			$sort->addOption(g_l('modules_customer', '[no_sort]'), g_l('modules_customer', '[no_sort]'));
		}

		foreach($sort_names as $v){
			$sort->addOption(oldHtmlspecialchars($v), oldHtmlspecialchars($v));
		}

		return $sort;
	}

	function getHTMLFieldControl($field, $value = null, $isEncrypted = false){ //Code used, when data is in session, not intial/DB
		$props = $this->getFieldProperties($field);
		$hasEncContent = $isEncrypted && $value;
		switch($props['type']){
			case 'input':
				return we_html_tools::htmlTextInput($field, 32, ($hasEncContent ? we_customer_customer::ENCRYPTED_DATA : $value), '', "onchange=\"top.content.setHot();\" style='width:240px;'");
			case 'textarea':
				return we_html_element::htmlTextArea(array("name" => $field, "style" => "width:240px;", "class" => "wetextarea"), ($hasEncContent ? we_customer_customer::ENCRYPTED_DATA : $value));
			case 'number':
				return we_html_tools::htmlTextInput($field, 32, intval($value), '', "onchange=\"top.content.setHot();\" style='width:240px;'", 'number');
			case 'multiselect':
				if(!$this->customer->ID && $value == null){
					$value = $props['default'];
				}
				$out = we_html_element::htmlHidden($field, $value);
				$values = explode(',', $value);
				$defs = explode(',', $props['default']);
				$cnt = count($defs);
				$i = 0;
				foreach($defs as $def){
					$attribs = array('type' => 'checkbox', 'name' => $field . '_multi_' . ($i++), 'value' => $def, 'onclick' => 'setMultiSelectData(\'' . $field . '\',' . $cnt . ');');
					if(in_array($def, $values)){
						$attribs['checked'] = 'checked';
					}
					$out .= we_html_element::htmlInput($attribs) . $def . we_html_element::htmlBr();
				}

				return we_html_element::htmlDiv(array('style' => 'height: 80px;overflow: auto;width: 220px;border: 1px solid #000;padding: 3px;background: #FFFFFF;'), $out);
			case 'country':
				$langcode = array_search($GLOBALS['WE_LANGUAGE'], getWELangs());

				$countrycode = array_search($langcode, getWECountries());
				$countryselect = new we_html_select(array('name' => $field, 'size' => 1, 'style' => 'width:240px;', 'class' => 'wetextinput', 'id' => ($field === 'Gruppe' ? 'yuiAcInputPathGroupX' : ''), 'onchange' => ($field === 'Gruppe' ? 'top.content.setHot();' : 'top.content.setHot();')));

				$topCountries = array_flip(explode(',', WE_COUNTRIES_TOP));

				foreach($topCountries as $countrykey => &$countryvalue){
					$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
				}
				unset($countryvalue);
				$shownCountries = array_flip(explode(',', WE_COUNTRIES_SHOWN));
				foreach($shownCountries as $countrykey => &$countryvalue){
					$countryvalue = we_base_country::getTranslation($countrykey, we_base_country::TERRITORY, $langcode);
				}
				unset($countryvalue);
				$oldLocale = setlocale(LC_ALL, NULL);
				setlocale(LC_ALL, $langcode . '_' . $countrycode . '.UTF-8');
				asort($topCountries, SORT_LOCALE_STRING);
				asort($shownCountries, SORT_LOCALE_STRING);
				setlocale(LC_ALL, $oldLocale);

				if(WE_COUNTRIES_DEFAULT != ''){
					$countryselect->addOption('--', CheckAndConvertISObackend(WE_COUNTRIES_DEFAULT));
				}
				foreach($topCountries as $countrykey => &$countryvalue){
					$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
				}
				unset($countryvalue);
				if(!empty($topCountries) && !empty($shownCountries)){
					$countryselect->addOption('-', '----', array('disabled' => 'disabled'));
				}

				foreach($shownCountries as $countrykey => &$countryvalue){
					$countryselect->addOption($countrykey, CheckAndConvertISObackend($countryvalue));
				}
				unset($countryvalue);
				$countryselect->selectOption($value);
				return $countryselect->getHtml();

			case 'language':
				if(isset($GLOBALS['weFrontendLanguages']) && is_array($GLOBALS['weFrontendLanguages'])){
					$frontendL = $GLOBALS['weFrontendLanguages'];
					foreach($frontendL as &$lcvalue){
						$lccode = explode('_', $lcvalue);
						$lcvalue = $lccode[0];
					}
					unset($lcvalue);
					$languageselect = new we_html_select(array('name' => $field, 'size' => 1, 'style' => 'width:240px;', 'class' => 'wetextinput', "id" => ($field === "Gruppe" ? "yuiAcInputPathGroupX" : ''), "onchange" => ($field === "Gruppe" ? "top.content.setHot();" : "top.content.setHot();")));
					foreach(g_l('languages', '') as $languagekey => $languagevalue){
						if(in_array($languagekey, $frontendL)){
							$languageselect->addOption($languagekey, $languagevalue);
						}
					}
					$languageselect->selectOption($value);
					return $languageselect->getHtml();
				}
				return 'no FrontendLanguages defined';

			case 'select':
				if(!$this->customer->ID && $value == null){
					$value = $props['default'];
				}

				$defs = explode(',', $props['default']);
				if($this->customer->ID && !in_array($value, $defs)){
					$defs = array_merge(array($value), $defs);
				}

				$select = new we_html_select(array("name" => $field, "size" => 1, "style" => "width:240px;", "class" => "wetextinput", "id" => ($field === "Gruppe" ? "yuiAcInputPathGroupX" : ''), "onchange" => "top.content.setHot();"));
				foreach($defs as $def){
					$select->addOption($def, $def);
				}
				$select->selectOption($value);
				return $select->getHtml();
			case 'date':
				$date_format = DATE_ONLY_FORMAT;
				$format = g_l('weEditorInfo', '[date_only_format]');
			case 'dateTime':
				//$out = rray('name' => $field, 'value' => $value));
				try{
					$value = $value && $value != '0000-00-00' ? new DateTime($value /* ? $value : $this->settings->getSettings('start_year') . '-01-01' */) : 0;
				} catch (Exception $e){
					$value = 0;
				}
				$date_format = (isset($date_format) ? $date_format : DATE_FORMAT);

				$format = (isset($format) ? $format : g_l('weEditorInfo', '[date_format]'));

				return we_html_tools::getDateInput2('we_date_' . $field . '%s', $value, false, $format, '', "weSelect", false, $this->settings->getSettings('start_year'));
			case 'password':
				return we_html_tools::htmlTextInput($field, 32, $value, 32, 'onchange="top.content.setHot();" style="width:240px;" autocomplete="off" ', 'password');
			case 'img':
				$cmd1 = "document.we_form.elements['" . $field . "'].value";
				$wecmdenc3 = we_base_request::encCmd("opener.refreshForm()");
				$imgId = intval($value);
				$img = new we_imageDocument();

				$img->initByID($imgId, FILE_TABLE);
				return '
<table class="weEditTable">
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' . $img->getHtml() . we_html_element::htmlHidden($field, $imgId) . '</td>
	</tr>
	<tr>
		<td class="weEditmodeStyle" colspan="2" style="text-align:center">' .
					we_html_button::create_button('fa:btn_select_image,fa-lg fa-exchange,fa-lg fa-file-image-o', "javascript:we_cmd('we_selector_image', '" . $imgId . "', '" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','','" . $wecmdenc3 . "','', '', '" . we_base_ContentTypes::IMAGE . "', " . (permissionhandler::hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ")", true) . we_html_button::create_button(we_html_button::TRASH, "javascript:" . $cmd1 . "='';refreshForm();", true) .
					'</td>
	</tr>
</table>';
			default:
				return we_html_tools::htmlTextInput($field, 32, $value, '', "onchange=\"top.content.setHot();\" style='width:240px;'");
		}
		return null;
	}

	private function getCommonTable(array $common, $isAll){
		$table = new we_html_table(array('width' => 500, 'height' => 50, 'class' => 'customer'), 1, 2);
		$c = 0;
		$table->setRow(0, array('style' => 'vertical-align:top'));
		foreach($common as $pk => $pv){
			if($this->customer->isInfoDate($pk)){
				$pv = ($pv == '' || !is_numeric($pv)) ? 0 : $pv;
				$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(($pv ? we_html_element::htmlDiv(array('class' => 'defaultgray'), date(g_l('weEditorInfo', '[date_format]'), $pv)) : '-' . we_html_tools::getPixel(100, 5)), $this->settings->getPropertyTitle($pk)));
			} else {
				switch($pk){
					case 'ID':
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(($pv ? we_html_element::htmlDiv(array('class' => 'defaultgray'), $pv) : '-' . we_html_tools::getPixel(100, 5)), $this->settings->getPropertyTitle($pk)));
						++$c;
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), '');
						break;
					case 'LoginDenied':
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => 'defaultgray'), we_html_forms::checkbox(1, $pv, 'LoginDenied', g_l('modules_customer', '[login_denied]'), false, 'defaultfont', 'top.content.setHot();')), $this->settings->getPropertyTitle($pk)));
						break;
					case 'AutoLoginDenied':
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => 'defaultgray'), we_html_forms::checkbox(1, $pv, 'AutoLoginDenied', g_l('modules_customer', '[login_denied]'), false, 'defaultfont', 'top.content.setHot();')), $this->settings->getPropertyTitle($pk)));
						break;
					case 'AutoLogin':
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => 'defaultgray'), we_html_forms::checkbox(1, $pv, 'AutoLogin', g_l('modules_customer', '[autologin_request]'), false, 'defaultfont', 'top.content.setHot();')), $this->settings->getPropertyTitle($pk)));
						break;
					case 'Password':
						$table->setCol($c / 2, $c % 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($pk, 32, ($this->customer->ID ? we_customer_customer::NOPWD_CHANGE : ''), '', 'onchange="top.content.setHot();" autocomplete="off" ', 'password', "240px"), $this->settings->getPropertyTitle($pk)));
						break;
					case 'Username':
						$inputattribs = ' id="yuiAcInputPathName" onblur="parent.edheader.setTitlePath(this.value);"';
						$table->setCol($c / 2, $c % 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($pk, 32, $pv, '', 'onchange="top.content.setHot();" ' . $inputattribs, "text", "240px"), $this->settings->getPropertyTitle($pk)));
						break;
					case 'failedLogins':
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => 'defaultgray', 'id' => 'FailedCustomerLogins'), intval($common['failedLogins']) . ' / ' . SECURITY_LIMIT_CUSTOMER_NAME), sprintf(g_l('modules_customer', '[failedLogins]'), SECURITY_LIMIT_CUSTOMER_NAME_HOURS)));
						break;
					case 'resetFailed':
						$but = we_html_button::create_button('reset', 'javascript:resetLogins(' . $this->customer->ID . ')');
						$table->setCol($c / 2, $c % 2, array('class' => 'defaultfont'), we_html_tools::htmlFormElementTable(we_html_element::htmlDiv(array('class' => 'defaultgray'), $but), ''));
						break;
					default:
						$inputattribs = '';
						$table->setCol($c / 2, $c % 2, array(), we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($pk, 32, $pv, '', "onchange=\"top.content.setHot();\" " . $inputattribs, "text", "240px"), $this->settings->getPropertyTitle($pk)));
				}
			}
			if(++$c % 2 == 0){
				$table->addRow();
				$table->setRow($c / 2, array('style' => 'vertical-align:top'));
			}
		}
		return array(
			'headline' => g_l('modules_customer', ($isAll ? '[common]' : '[data]')),
			'html' => $table->getHtml(),
			'space' => 120
		);
	}

	private function getOtherTable(array $other, $isAll){
		$table = new we_html_table(array('width' => 500, 'height' => 50, 'class' => 'customer'), 1, 2);
		$c = 0;
		$table->setRow(0, array('style' => 'vertical-align:top'));
		foreach($other as $k => $v){
			$isEncField = $this->settings->retriveFieldAdd($k, 'encrypt');
			$control = $this->getHTMLFieldControl($k, $v, $isEncField);
			if($control){
				$table->setCol($c / 2, $c % 2, array(), we_html_tools::htmlFormElementTable($control, $k . ($isEncField ? $this->getEncryptionHandling($k, $v != '') : '')));
				if(++$c % 2 == 0){
					$table->addRow();
					$table->setRow($c / 2, array('style' => 'vertical-align:top'));
				}
			}
		}
		return array(
			'headline' => g_l('modules_customer', ($isAll ? '[other]' : '[data]')),
			'html' => $table->getHtml(),
			'space' => 120
		);
	}

	private function getEncryptionHandling($field, $hasContent){
		return '<span style="margin-left:1em;" class="fa fa-lock" onclick=""></span>';
	}

	function getHTMLProperties($preselect = ''){
		$other = $parts = $branches = array();
		$common = array(
			'ID' => $this->customer->ID,
		);

		$this->customer->getBranches($branches, $common, $other, $this->settings->getEditSort());

		$common['failedLogins'] = f('SELECT COUNT(1) FROM ' . FAILED_LOGINS_TABLE . ' WHERE UserTable="tblWebUser" AND Username="' . $GLOBALS['DB_WE']->escape($this->customer->Username) . '" AND isValid="true" AND LoginDate >DATE_SUB(NOW(), INTERVAL ' . intval(SECURITY_LIMIT_CUSTOMER_NAME_HOURS) . ' hour)');
		if($common['failedLogins'] >= intval(SECURITY_LIMIT_CUSTOMER_NAME)){
			$common['resetFailed'] = '';
		}

		switch($preselect){
			case g_l('modules_customer', '[common]'):
				$parts = array($this->getCommonTable($common, false));
				break;
			case g_l('modules_customer', '[other]'):
				$parts = array($this->getOtherTable($other, false));
				break;
			case g_l('modules_customer', '[orderTab]'):
				$parts = array(array(
						'html' => we_shop_functions::getCustomersOrderList($this->customer->ID, false),
						'space' => 0
					)
				);
				break;
			case g_l('modules_customer', '[objectTab]'):
				$DB_WE = new DB_WE();
				$DB_WE->query('SELECT ID,TableID,ContentType,Path,Text,ModDate,Published FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . OBJECT_FILES_TABLE . '.WebUserID = ' . $this->customer->ID . ' ORDER BY ' . OBJECT_FILES_TABLE . '.Path');
				$objectStr = '';
				if($DB_WE->num_rows()){
					$objectStr.='<table class="defaultfont" width="600">' .
						'<tr><td>&nbsp;</td> <td><b>' . g_l('modules_customer', '[ID]') . '</b></td><td><b>' . g_l('modules_object', '[class]') . '</b></td><td><b>' . g_l('modules_customer', '[filename]') . '</b></td><td><b>' . g_l('modules_customer', '[Aenderungsdatum]') . '</b></td>';
					while($DB_WE->next_record()){
						$objectStr.='<tr>
	<td>' . we_html_button::create_button(we_html_button::EDIT, "javascript: if(top.opener.top.doClickDirect){top.opener.top.doClickDirect(" . $DB_WE->f('ID') . ",'" . $DB_WE->f('ContentType') . "','" . OBJECT_FILES_TABLE . "'); }") . '</td>
	<td>' . $DB_WE->f('ID') . '</td>
	<td title="' . $DB_WE->f('Path') . '">' . $DB_WE->f('Text') . '</td>
	<td class="' . ($DB_WE->f('Published') ? ($DB_WE->f('ModDate') > $DB_WE->f('Published') ? 'changed defaultfont' : 'defaultfont') : 'npdefaultfont') . '">' . date('d.m.Y H:i', $DB_WE->f('ModDate')) . '</td>
</tr>';
					}
					$objectStr.='</table>';
				} else {
					$objectStr = g_l('modules_customer', '[NoObjects]');
				}

				$parts = array(
					array(
						"html" => $objectStr,
						"space" => 0
					)
				);
				break;
			case g_l('modules_customer', '[documentTab]'):
				$DB_WE = new DB_WE();
				$DB_WE->query('SELECT f.ID,f.Path,f.ContentType,f.Text,f.Published,f.ModDate,c1.Dat AS title,c2.Dat AS description' .
					' FROM ' .
					FILE_TABLE . ' f LEFT JOIN ' .
					LINK_TABLE . ' l1 ON (l1.DID=f.ID AND l1.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND l1.Name="Title") LEFT JOIN ' .
					CONTENT_TABLE . ' c1 ON l1.CID=c1.ID LEFT JOIN ' .
					LINK_TABLE . ' l2 ON (l2.DID=f.ID AND l2.DocumentTable="' . stripTblPrefix(FILE_TABLE) . '" AND l2.Name="Description") LEFT JOIN ' .
					CONTENT_TABLE . ' c2 ON l2.CID=c2.ID' .
					' WHERE f.WebUserID=' . intval($this->customer->ID) . ' ORDER BY f.Path');

				if($DB_WE->num_rows()){
					$documentStr = '<table class="defaultfont" width="600">' .
						'<tr><td>&nbsp;</td> <td><b>' . g_l('modules_customer', '[ID]') . '</b></td><td><b>' . g_l('modules_customer', '[filename]') . '</b></td><td><b>' . g_l('modules_customer', '[Aenderungsdatum]') . '</b></td><td><b>' . g_l('modules_customer', '[Titel]') . '</b></td>' .
						'</tr>';
					while($DB_WE->next_record()){
						$documentStr.='<tr>' .
							'<td>' . we_html_button::create_button(we_html_button::EDIT, "javascript: if(top.opener.top.doClickDirect){top.opener.top.doClickDirect(" . $DB_WE->f('ID') . ",'" . $DB_WE->f('ContentType') . "','" . FILE_TABLE . "'); }") . '</td>' .
							'<td>' . $DB_WE->f('ID') . '</td>' .
							'<td title="' . $DB_WE->f('Path') . '">' . $DB_WE->f('Text') . '</td>' .
							'<td class="' .
							($DB_WE->f('Published') ? ($DB_WE->f('ModDate') > $DB_WE->f('Published') ? 'changeddefaultfont' : 'defaultfont') : 'npdefaultfont')
							. '">' . date('d.m.Y H:i', $DB_WE->f('ModDate')) . '</td>' .
							'<td title="' . $DB_WE->f('description') . '">' . $DB_WE->f('title') . '</td>' .
							'</tr>';
					}
					$documentStr.='</table>';
				} else {
					$documentStr = g_l('modules_customer', '[NoDocuments]');
				}

				$parts = array(
					array(
						"html" => $documentStr,
						"space" => 0
					)
				);
				break;
			case g_l('modules_customer', '[all]'):
				$isAll = true;
				$parts = array(
					$this->getCommonTable($common, true),
					$this->getOtherTable($other, true)
				);
//no break;
			default:
				foreach($branches as $bk => $branch){
					if($preselect && $preselect != g_l('modules_customer', '[all]')){
						if($bk != $preselect){
							continue;
						}
					}

					$table = new we_html_table(array("width" => 500, "height" => 50, "class" => 'customer'), 1, 2);
					$c = 0;
					$table->setRow(0, array('style' => 'vertical-align:top;'));
					foreach($branch as $k => $v){
						$isEncField = $this->settings->retriveFieldAdd($bk . '_' . $k, 'encrypt');
						$control = $this->getHTMLFieldControl($bk . '_' . $k, $v, $isEncField);
						if(!$control){
							continue;
						}
						$table->setCol($c / 2, $c % 2, array(), we_html_tools::htmlFormElementTable($control, $k . ($isEncField ? $this->getEncryptionHandling($bk . '_' . $k, $v != '') : '')));

						if(++$c % 2 == 1){
							$table->addRow();
							$table->setRow($c / 2, array('style' => 'vertical-align:top;'));
						}
					}
					$parts[] = array(
						"headline" => (isset($isAll) ? $bk : g_l('modules_customer', '[data]')),
						"html" => $table->getHtml(),
						"space" => 120
					);
				}
		}

		return we_html_multiIconBox::getHTML('', $parts, 30);
	}

	public function getHomeScreen(){
		$hiddens['cmd'] = 'home';
		$GLOBALS['we_head_insert'] = $this->getJSProperty();
		$GLOBALS['we_body_insert'] = we_html_element::htmlForm(array('name' => 'we_form'), $this->getCommonHiddens($hiddens) . we_html_element::htmlHidden('home', 0));
		$content = we_html_button::create_button("fat:new_customer,fa-lg fa-user-plus", "javascript:top.opener.top.we_cmd('new_customer');", true, 0, 0, "", "", !permissionhandler::hasPerm("NEW_CUSTOMER"));

		return parent::getHomeScreen('customer', "customer.gif", $content);
	}

}
