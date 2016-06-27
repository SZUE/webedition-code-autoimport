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
class we_import_wizard extends we_import_wizardBase{
	var $TemplateID = 0;

	public function __construct(){
		parent::__construct();
	}

	private function formCategory2($obj, $categories){
		$js = (defined('OBJECT_TABLE')) ? "opener.wizbody.document.we_form.elements[\\'v[import_type]\\'][0].checked=true;" : "";
		$addbut = we_html_button::create_button(we_html_button::ADD, "javascript:top.we_cmd('we_selector_category',0,'" . CATEGORY_TABLE . "','','','" . $js . "fillIDs();opener.top.we_cmd(\\'add_" . $obj . "Cat\\',top.allIDs);')", false, 100, 22, "", "", (!permissionhandler::hasPerm("EDIT_KATEGORIE")));
		$cats = new we_chooser_multiDirExtended(410, $categories, 'delete_' . $obj . 'Cat', $addbut, '', '"we/category"', CATEGORY_TABLE);
		$cats->setRowPrefix($obj);
		$cats->setCatField("self.document.we_form.elements['v[" . $obj . "Categories]']");
		return $cats->get();
	}

	/**
	 * @return array
	 * @param integer $classID
	 * @desc returns an array with all the fields of the class with the given $classID
	 */
	private static function getClassFields($classID){
		$db = new DB_WE();
		$dv = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classID), '', $db));
		$tableInfo_sorted = we_objectFile::getSortedTableInfo($classID, true, $db);
		$fields = [];
		$regs = [];
		foreach($tableInfo_sorted as $cur){
			// bugfix 8141
			if(preg_match('/(.+?)_(.*)/', $cur['name'], $regs)){
				$fields[] = array('name' => $regs[2], 'type' => $regs[1]);
			}
		}
		return $fields;
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is a text field
	 */
	private static function isTextField($type){
		switch($type){
			case 'input':
			case 'text':
			case 'meta':
			case 'checkbox': //Bugfix #4733
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is a text field
	 */
	private static function isDateField($type){
		return ($type === 'date');
	}

	/**
	 * @return boolean
	 * @param string $type
	 * @desc returns true if the field is numeric
	 */
	private static function isNumericField($type){
		switch($type){
			case 'int':
			case 'float':
				return true;
			default:
				return false;
		}
	}

	/**
	 * @return string
	 * @param array $v
	 * @desc returns a string of hidden fields
	 */
	protected function getHdns($v, $a, $ignore = []){
		$hdns = '';
		foreach($a as $key => $value){
			if(!in_array($key, $ignore)){
				$hdns .= we_html_element::htmlHidden($v . '[' . $key . ']', $value);
			}
		}
		return $hdns;
	}

	protected function getStep0(){
		$defaultVal = we_import_functions::TYPE_LOCAL_FILES;


		if(!permissionhandler::hasPerm('FILE_IMPORT')){
			$defaultVal = we_import_functions::TYPE_SITE;
			if(!permissionhandler::hasPerm('SITE_IMPORT')){
				$defaultVal = we_import_functions::TYPE_WE_XML;
				if(!permissionhandler::hasPerm('WXML_IMPORT')){
					$defaultVal = we_import_functions::TYPE_GENERIC_XML;
					if(!permissionhandler::hasPerm('GENERICXML_IMPORT')){
						$defaultVal = we_import_functions::TYPE_CSV;
						if(!permissionhandler::hasPerm('CSV_IMPORT')){
							$defaultVal = '';
						}
					}
				}
			}
		}

		$cmd = we_base_request::_(we_base_request::RAW, 'we_cmd', array('import', $defaultVal));
		$cmd[1] = empty($cmd[1]) ? we_import_functions::TYPE_LOCAL_FILES : $cmd[1];
		$expat = (function_exists('xml_parser_create')) ? true : false;

		$tblFiles = new we_html_table(array('class' => 'default withSpace'), 2, 1);
		$tblFiles->setCol(0, 0, [], we_html_forms::radiobutton('file_import', ($cmd[1] == we_import_functions::TYPE_LOCAL_FILES), 'type', g_l('import', '[file_import]'), true, 'defaultfont', '', !permissionhandler::hasPerm('FILE_IMPORT'), g_l('import', '[txt_file_import]'), 0, 384));
		$tblFiles->setCol(1, 0, [], we_html_forms::radiobutton('site_import', ($cmd[1] == we_import_functions::TYPE_SITE), 'type', g_l('import', '[site_import]'), true, 'defaultfont', '', !permissionhandler::hasPerm('SITE_IMPORT'), g_l('import', '[txt_site_import]'), 0, 384));
		$tblData = new we_html_table(array('class' => 'default withSpace'), 3, 1);
		$tblData->setCol(0, 0, [], we_html_forms::radiobutton(we_import_functions::TYPE_WE_XML, ($cmd[1] == we_import_functions::TYPE_WE_XML), 'type', g_l('import', '[wxml_import]'), true, 'defaultfont', '', (!permissionhandler::hasPerm('WXML_IMPORT') || !$expat), g_l('import', ($expat ? '[txt_wxml_import]' : '[add_expat_support]')), 0, 384));
		$tblData->setCol(1, 0, [], we_html_forms::radiobutton(we_import_functions::TYPE_GENERIC_XML, ($cmd[1] == we_import_functions::TYPE_GENERIC_XML), 'type', g_l('import', '[gxml_import]'), true, 'defaultfont', '', (!permissionhandler::hasPerm('GENERICXML_IMPORT') || !$expat), g_l('import', ($expat ? '[txt_gxml_import]' : '[add_expat_support]')), 0, 384));
		$tblData->setCol(2, 0, [], we_html_forms::radiobutton(we_import_functions::TYPE_CSV, ($cmd[1] == we_import_functions::TYPE_CSV), 'type', g_l('import', '[csv_import]'), true, 'defaultfont', '', !permissionhandler::hasPerm('CSV_IMPORT'), g_l('import', '[txt_csv_import]'), 0, 384));

		$parts = array(
			array(
				'headline' => g_l('import', '[import_file]'),
				'html' => $tblFiles->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1),
			array(
				'headline' => g_l('import', '[import_data]'),
				'html' => $tblData->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1),
		);
		return array(
			"function we_cmd() {
				//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//				var url = WE().util.getWe_cmdArgsUrl(args);
				parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
			}
			function set_button_state() {
				WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'disabled');
				WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
			}
			function handle_event(evt) {
				var f = self.document.we_form;
				switch(evt) {
					case 'previous':
						break;
					case 'next':
						for (var i = 0; i < f.type.length; i++) {
							if (f.type[i].checked == true) {
								switch(f.type[i].value) {
									case 'file_import':
										top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import_files';
										break;
									case 'site_import':
										top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=siteImport';
										break;
									default:
										f.type.value=f.type[i].value;
										f.step.value=1;
										f.mode.value=0;
										f.target='wizbody';
										f.action='" . $this->path . "';
										f.method='post';
										f.submit();
										break;
								}
							}
						}
						break;
					case 'cancel':
						top.close();
						break;
				}
			}",
			we_html_multiIconBox::getHTML("", $parts, 30, "", -1, "", "", false, g_l('import', '[title]'))
		);
	}

	protected function getWXMLImportStep1(){
		$v = we_base_request::_(we_base_request::STRING, 'v', []);
		$doc_root = get_def_ws();
		$tmpl_root = get_def_ws(TEMPLATES_TABLE);
		$nav_root = get_def_ws(NAVIGATION_TABLE);

		$hdns = we_html_element::htmlHiddens(array(
				'v[doc_dir_id]' => (isset($v['doc_dir_id']) ? $v['doc_dir_id'] : $doc_root),
				'v[tpl_dir_id]' => (isset($v['tpl_dir_id']) ? $v['tpl_dir_id'] : $tmpl_root),
				'v[doc_dir]' => (isset($v['doc_dir']) ? $v['doc_dir'] : id_to_path($doc_root)),
				'v[tpl_dir]' => (isset($v['tpl_dir']) ? $v['tpl_dir'] : id_to_path($tmpl_root, TEMPLATES_TABLE)),
				'v[import_from]' => (isset($v['import_from']) ? $v['import_from'] : 0),
				'v[navigation_dir_id]' => (isset($v['navigation_dir_id']) ? $v['navigation_dir_id'] : $nav_root),
				'v[navigation_dir]' => (isset($v['navigation_dir']) ? $v['navigation_dir'] : id_to_path($nav_root, NAVIGATION_TABLE)),
				'v[import_docs]' => (isset($v['import_docs'])) ? $v['import_docs'] : 0,
				'v[import_templ]' => (isset($v['import_templ'])) ? $v['import_templ'] : 0,
				'v[import_thumbnails]' => (isset($v['import_thumbnails'])) ? $v['import_thumbnails'] : 0,
				'v[import_objs]' => (isset($v['import_objs'])) ? $v['import_objs'] : 0,
				'v[import_classes]' => (isset($v['import_classes'])) ? $v['import_classes'] : 0,
				'v[restore_doc_path]' => (isset($v['restore_doc_path'])) ? $v['restore_doc_path'] : 1,
				'v[restore_tpl_path]' => (isset($v['restore_tpl_path'])) ? $v['restore_tpl_path'] : 1,
				'v[import_dt]' => (isset($v['import_dt'])) ? $v['import_dt'] : 0,
				'v[import_ct]' => (isset($v['import_ct'])) ? $v['import_ct'] : 0,
				'v[import_binarys]' => (isset($v['import_binarys'])) ? $v['import_binarys'] : 0,
				'v[import_owners]' => (isset($v['import_owners'])) ? $v['import_owners'] : 0,
				'v[owners_overwrite]' => (isset($v['owners_overwrite'])) ? $v['owners_overwrite'] : 0,
				'v[owners_overwrite_id]' => (isset($v['owners_overwrite_id'])) ? $v['owners_overwrite_id'] : 0,
				'v[owners_overwrite_path]' => (isset($v['owners_overwrite_path'])) ? $v['owners_overwrite_path'] : '/',
				'v[import_navigation]' => (isset($v['import_navigation'])) ? $v['import_navigation'] : 0,
				'v[rebuild]' => (isset($v['rebuild'])) ? $v['rebuild'] : 1,
				'v[mode]' => (isset($v['mode']) ? $v['mode'] : 0)));

		$functions = "
function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}
function set_button_state() {
	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}

function handle_event(evt) {
	var f = self.document.we_form;
	switch(evt) {
		case 'previous':
			f.step.value = 0;
			top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=" . we_import_functions::TYPE_WE_XML . "';
			break;
		case 'next':
			if(f.elements['v[rdofloc]'][1].checked===true){
				" . $this->fileUploader->getJsBtnCmd('upload') . "}
			else {
				handle_eventNext();
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}
function handle_eventNext(){
	var f = self.document.we_form,
		fs = f.elements['v[fserver]'].value,
		ext = '',
		fl = we_FileUpload !== undefined ? 'placeholder.xml' : f.elements.uploaded_xml_file.value;

	if (f.elements['v[rdofloc]'][0].checked==true && fs!=='/') {
		if (fs.match(/\.\./)=='..') { " . (we_message_reporting::getShowMessageCall(g_l('import', '[invalid_path]'), we_message_reporting::WE_MESSAGE_ERROR)) . "; return; }
		ext = fs.substr(fs.length-4,4);
		f.elements['v[import_from]'].value = fs;

	}
	else if (f.elements['v[rdofloc]'][1].checked==true && fl!=='') {
		ext = fl.substr(fl.length-4,4);
		f.elements['v[import_from]'].value = fl;
	}
	else if (fs=='/' || fl=='') {
		" . (we_message_reporting::getShowMessageCall(g_l('import', '[select_source_file]'), we_message_reporting::WE_MESSAGE_ERROR)) . "; return;
	}
	f.step.value = 2;
	// timing Problem with Safari
	setTimeout(we_submit_form,50,self.document.forms.we_form,'wizbody', '" . $this->path . "');
}
";

		$cmd1 = "top.wizbody.document.we_form.elements['v[fserver]'].value";
		$importFromButton = (permissionhandler::hasPerm("CAN_SELECT_EXTERNAL_FILES")) ? we_html_button::create_button(we_html_button::SELECT, "javascript: self.document.we_form.elements['v[rdofloc]'][0].checked=true;we_cmd('browse_server', '" . we_base_request::encCmd($cmd1) . "', '', " . $cmd1 . ")") : "";
		$inputLServer = we_html_tools::htmlTextInput("v[fserver]", 30, (isset($v["fserver"]) ? $v["fserver"] : "/"), 255, "readonly", "text", 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, "", "left", "defaultfont", $importFromButton, '', "", "", "", 0);

		$inputLLocal = $this->fileUploader->getHTML();
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, "", "left", "defaultfont", '', "", "", "", "", 0);
		$rdoLServer = we_html_forms::radiobutton("lServer", (isset($v["rdofloc"])) ? ($v["rdofloc"] === "lServer") : 1, "v[rdofloc]", g_l('import', '[fileselect_server]'));
		$rdoLLocal = we_html_forms::radiobutton("lLocal", (isset($v["rdofloc"])) ? ($v["rdofloc"] === "lLocal") : 0, "v[rdofloc]", g_l('import', '[fileselect_local]'));
		$importLocs = new we_html_table(array('class' => 'default'), 6, 1);
		$importLocs->setCol(0, 0, [], $rdoLServer);
		$importLocs->setCol(1, 0, [], $importFromServer);
		$importLocs->setCol(3, 0, array('style' => 'padding-top:4px;'), $rdoLLocal);
		$importLocs->setCol(4, 0, [], $this->fileUploader->getHtmlAlertBoxes());
		$importLocs->setCol(5, 0, [], $importFromLocal);
		$fn_colsn = new we_html_table(array('class' => 'default withSpace'), 4, 1);
		$fn_colsn->setCol(0, 0, [], we_html_tools::htmlAlertAttentionBox(g_l('import', '[collision_txt]'), we_html_tools::TYPE_ALERT, 410));
		$fn_colsn->setCol(1, 0, [], we_html_forms::radiobutton("replace", (isset($v["collision"])) ? ($v["collision"] === "replace") : true, "v[collision]", g_l('import', '[replace]'), true, "defaultfont", "", false, g_l('import', '[replace_txt]'), 0, 384));
		$fn_colsn->setCol(2, 0, [], we_html_forms::radiobutton("rename", (isset($v["collision"])) ? ($v["collision"] === "rename") : false, "v[collision]", g_l('import', '[rename]'), true, "defaultfont", "", false, g_l('import', '[rename_txt]'), 0, 384));
		$fn_colsn->setCol(3, 0, [], we_html_forms::radiobutton("skip", (isset($v["collision"])) ? ($v["collision"] === "skip") : false, "v[collision]", g_l('import', '[skip]'), true, "defaultfont", "", false, g_l('import', '[skip_txt]'), 0, 384));

		$parts = array(
			array(
				'headline' => g_l('import', '[import]'),
				'html' => $importLocs->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED),
			array(
				'headline' => g_l('import', '[file_collision]'),
				'html' => $fn_colsn->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED)
		);

		$wepos = weGetCookieVariable('but_wxml');
		$znr = -1;
		$content = $hdns . we_html_multiIconBox::getHTML(we_import_functions::TYPE_WE_XML, $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', '[wxml_import]'));
		return array($functions, $content);
	}

	protected function getWXMLImportStep2(){
		$v = we_base_request::_(we_base_request::STRING, 'v', []);
		$upload_error = false;

		if($v['rdofloc'] === 'lLocal'){
			if((!$v['import_from'] = $this->fileUploader->commitUploadedFile())){
				$upload_error = $this->fileUploader->getError();
			}
		}

		$we_valid = true;

		$event_handler = '
function handle_event(evt) {
	var we_form = self.document.we_form;
	switch(evt) {
		case "previous":
			we_form.step.value = 1;
			we_submit_form(we_form, "wizbody", "' . $this->path . '");
			break;
		case "next":
			we_form.elements.step.value=3;
			we_form.mode.value=1;
			we_form.elements["v[mode]"].value=1;
			we_submit_form(we_form,"wizbusy","' . $this->path . '&pnt=wizcmd");
			break;
		case "cancel":
			top.close();
			break;
	}
}
function we_submit_form(we_form, target, url) {
	we_form.target = target;
	we_form.action = url;
	we_form.method = "post";
	we_form.submit();
}';

		$hdns = we_html_element::htmlHiddens(array(
				'v[type]' => $v['type'],
				'v[mode]' => (isset($v['mode'])) ? $v['mode'] : 0,
				'v[fserver]' => $v['fserver'],
				'v[rdofloc]' => $v['rdofloc'],
				'v[import_from]' => $v['import_from'],
				'v[collision]' => isset($v['collision']) ? $v['collision'] : 0));


		$functions = "
function we_cmd() {
	var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	var url = WE().util.getWe_cmdArgsUrl(args);

	switch (args[0]) {" . '
		case "openNavigationDirselector":
				url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?we_cmd[]=we_navigation_dirSelector&";
				for(var i = 1; i < args.length; i++){
					url += "we_cmd[]="+encodeURI(args[i]);
					if(i < (args.length - 1)){ url += "&"; }
				}
				new (WE().util.jsWindow)(this, url,"we_navigation_dirselector",-1,-1,600,400,true,true,true);
			break;' . "
		case 'we_selector_file':
			new (WE().util.jsWindow)(this, url,'we_selector',-1,-1," . we_selector_file::WINDOW_SELECTOR_WIDTH . "," . we_selector_file::WINDOW_SELECTOR_HEIGHT . ",true,true,true,true);
			break;
		default:
			parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));

	}
}
function set_button_state() {
	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', " .
			(($we_valid) ? ((isset($v["mode"]) && $v["mode"] == 1) ? "'disabled'" : "'enabled'") : "'disabled'") . ");
}" . $event_handler . '
function toggle(name){
	var con = document.getElementById(name);
	con.style.display = (con.style.display == "none" ? "":"none");
}';

		$return = array('', '');
		if($upload_error){
			$return[1] = we_html_element::jsElement($functions . ' ' .
					we_message_reporting::getShowMessageCall($upload_error, we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
			return $return;
		}

		$import_file = $_SERVER['DOCUMENT_ROOT'] . $v['import_from'];
		if(we_backup_util::getFormat($import_file) != 'xml'){
			$return[1] = we_html_element::jsElement($functions . ' ' .
					we_message_reporting::getShowMessageCall(g_l('import', '[format_unknown]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
			return $return;
		}
		$xml_type = we_backup_util::getXMLImportType($import_file);
		switch($xml_type){
			case 'backup':
				$return[0] = '';

				if(permissionhandler::hasPerm('IMPORT')){
					$return[1] = we_html_element::jsElement('
							' . $functions . '
if(confirm("' . str_replace('"', '\'', g_l('import', '[backup_file_found]') . ' \n\n' . g_l('import', '[backup_file_found_question]')) . '")){
	top.opener.top.we_cmd("recover_backup");
	top.close();
}
handle_event("previous");');
				} else {
					$return[1] = we_html_element::jsElement(
							$functions .
							we_message_reporting::getShowMessageCall(g_l('import', '[backup_file_found]'), we_message_reporting::WE_MESSAGE_ERROR) .
							'handle_event("previous");');
				}
				return $return;
			case 'customer':
				$return[1] = we_html_element::jsElement($functions . '
							' . we_message_reporting::getShowMessageCall(g_l('import', '[customer_import_file_found]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
				return $return;
			case 'unreadble':
				$return[1] = we_html_element::jsElement($functions . '
							' . we_message_reporting::getShowMessageCall(g_l('backup', '[file_not_readable]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
				return $return;

			case 'unknown':
				$return[1] = we_html_element::jsElement($functions . '
							' . we_message_reporting::getShowMessageCall(g_l('import', '[format_unknown]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							handle_event("previous");');
				return $return;
		}


		$parts = [];
		if($we_valid){
			$tbl_extra = new we_html_table([], 5, 1);

			// import documents
			$tbl_extra->setCol(0, 0, null, we_html_forms::checkboxWithHidden((!empty($v['import_docs'])) ? true : false, 'v[import_docs]', g_l('import', '[import_docs]'), false, 'defaultfont', "toggle('doc_table')"));

			$rootDirID = get_def_ws();
			$cmd1 = "top.wizbody.document.we_form.elements['v[doc_dir_id]'].value";

			$btnDocDir = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[doc_dir]'].value") . "','','','" . $rootDirID . "')");
			$yuiSuggest = & weSuggest::getInstance();
			$yuiSuggest->setAcId("DocPath");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[doc_dir]", (isset($v["doc_dir"]) ? $v["doc_dir"] : id_to_path($rootDirID)), array("onfocus" => "self.document.we_form.elements['_v[restore_doc_path]'].checked=false;"));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[doc_dir_id]", (isset($v["doc_dir_id"]) ? $v["doc_dir_id"] : $rootDirID));
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(FILE_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);


			$docPath = weSuggest::getYuiFiles() . $yuiSuggest->getHTML();

			$dir_table = new we_html_table(array('id' => 'doc_table', 'style' => 'margin-left:20px;'), 3, 2);
			if((isset($v['import_docs']) && !$v['import_docs'])){
				$dir_table->setStyle('display', 'none');
			}
			$dir_table->setCol(0, 0, null, we_html_tools::htmlAlertAttentionBox(g_l('import', '[documents_desc]'), we_html_tools::TYPE_ALERT, 390, true, 50));
			$dir_table->setCol(1, 0, null, $docPath);
			$dir_table->setCol(2, 0, null, we_html_forms::checkboxWithHidden((!empty($v['restore_doc_path'])), 'v[restore_doc_path]', g_l('import', '[maintain_paths]'), false, "defaultfont", "self.document.we_form.elements['v[doc_dir]'].value='/';"));

			$tbl_extra->setCol(1, 0, null, $dir_table->getHtml());

			// --------------
			// import templates
			$rootDirID = get_def_ws(TEMPLATES_TABLE);
			$tbl_extra->setCol(2, 0, array('colspan' => 2), we_html_forms::checkboxWithHidden((!empty($v['import_templ'])), 'v[import_templ]', g_l('import', '[import_templ]'), false, 'defaultfont', "toggle('tpl_table')"));
			$cmd1 = "top.wizbody.document.we_form.elements['v[tpl_dir_id]'].value";
			$btnDocDir = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[tpl_dir]'].value") . "','','','" . $rootDirID . "')");

			$yuiSuggest->setAcId('TemplPath');
			$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
			$yuiSuggest->setInput('v[tpl_dir]', (isset($v['tpl_dir']) ? $v['tpl_dir'] : id_to_path($rootDirID, TEMPLATES_TABLE)), array('onFocus' => "self.document.we_form.elements['_v[restore_tpl_path]'].checked=false;"));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult('v[tpl_dir_id]', (isset($v['tpl_dir_id'])) ? $v['tpl_dir_id'] : $rootDirID);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(TEMPLATES_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);

			$docPath = $yuiSuggest->getHTML();

			$dir_table = new we_html_table(array('id' => 'tpl_table', 'style' => 'margin-left:20px;'), 3, 2);
			if((isset($v['import_templ']) && !$v['import_templ'])){
				$dir_table->setStyle('display', 'none');
			}
			$dir_table->setCol(0, 0, null, we_html_tools::htmlAlertAttentionBox(g_l('import', '[templates_desc]'), we_html_tools::TYPE_ALERT, 390, true, 50));
			$dir_table->setCol(1, 0, null, $docPath);
			$dir_table->setCol(2, 0, null, we_html_forms::checkboxWithHidden((!empty($v['restore_tpl_path'])) ? true : false, 'v[restore_tpl_path]', g_l('import', '[maintain_paths]'), false, 'defaultfont', "self.document.we_form.elements['v[tpl_dir]'].value='/';"));


			$tbl_extra->setCol(3, 0, null, $dir_table->getHtml());

			$tbl_extra->setCol(4, 0, array("colspan" => 2), we_html_forms::checkboxWithHidden((!empty($v["import_thumbnails"])) ? true : false, "v[import_thumbnails]", g_l('import', '[import_thumbnails]'), false, "defaultfont"));


			$parts[] = array(
				"headline" => g_l('import', '[handle_document_options]') . '<br/>' . g_l('import', '[handle_template_options]'),
				"html" => $tbl_extra->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			);


			if(defined('OBJECT_TABLE')){
				$tbl_extra = new we_html_table([], 2, 1);
				$tbl_extra->setCol(0, 0, null, we_html_forms::checkboxWithHidden((!empty($v["import_objs"])) ? true : false, "v[import_objs]", g_l('import', '[import_objs]')));
				$tbl_extra->setCol(1, 0, null, we_html_forms::checkboxWithHidden((!empty($v["import_classes"])) ? true : false, "v[import_classes]", g_l('import', '[import_classes]')));

				$parts[] = array(
					"headline" => g_l('import', '[handle_object_options]') . '<br/>' . g_l('import', '[handle_class_options]'),
					"html" => $tbl_extra->getHTML(),
					'space' => we_html_multiIconBox::SPACE_MED
				);
			}

			$tbl_extra = new we_html_table([], 4, 1);
			$tbl_extra->setCol(0, 0, null, we_html_forms::checkboxWithHidden((!empty($v["import_dt"])) ? true : false, "v[import_dt]", g_l('import', '[import_doctypes]')));
			$tbl_extra->setCol(1, 0, null, we_html_forms::checkboxWithHidden((!empty($v["import_ct"])) ? true : false, "v[import_ct]", g_l('import', '[import_cats]')));
			$tbl_extra->setCol(2, 0, null, we_html_forms::checkboxWithHidden((!empty($v["import_navigation"])) ? true : false, "v[import_navigation]", g_l('import', '[import_navigation]'), false, 'defaultfont', "toggle('navigation_table')"));

			// --

			$btnDocDir = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('openNavigationDirselector','" . we_base_request::encCmd("document.we_form.elements[\"v[navigation_dir_id]\"].value") . "','','" . we_base_request::encCmd("document.we_form.elements[\"v[navigation_dir]\"].value") . "');");

			$yuiSuggest->setAcId("NaviPath");
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[navigation_dir]", (isset($v["navigation_dir"]) ? $v["navigation_dir"] : id_to_path($rootDirID)));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[navigation_dir_id]", (isset($v["navigation_dir_id"])) ? $v["navigation_dir_id"] : $rootDirID);
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(NAVIGATION_TABLE);
			$yuiSuggest->setWidth(280);
			$yuiSuggest->setSelectButton($btnDocDir, 10);

			$docPath = $yuiSuggest->getHTML() . $yuiSuggest->getYuiJs();

			$dir_table = new we_html_table(array("id" => "navigation_table", 'style' => 'margin-left:20px;'), 2, 1);
			if((isset($v["import_navigation"]) && !$v["import_navigation"])){
				$dir_table->setStyle('display', 'none');
			}
			$dir_table->setCol(0, 0, null, we_html_tools::htmlAlertAttentionBox(g_l('import', '[navigation_desc]'), we_html_tools::TYPE_ALERT, 390));
			$dir_table->setCol(1, 0, null, $docPath);

			$tbl_extra->setCol(3, 0, null, $dir_table->getHtml());

			$xml_encoding = we_xml_parser::getEncoding($import_file);

			$parts[] = array(
				"headline" => g_l('import', '[handle_doctype_options]') . '<br/>' . g_l('import', '[handle_category_options]'),
				"html" => '<input type="hidden" name="v[import_XMLencoding]" value="' . $xml_encoding . '" />' . $tbl_extra->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			);


			if(DEFAULT_CHARSET != '' && (DEFAULT_CHARSET === 'ISO-8859-1' || DEFAULT_CHARSET === 'UTF-8' ) && ($xml_encoding === 'ISO-8859-1' || $xml_encoding === 'UTF-8' )){
				if(($xml_encoding != DEFAULT_CHARSET)){
					$parts[] = array(
						'headline' => g_l('import', '[encoding_headline]'),
						'html' => we_html_forms::checkboxWithHidden((!empty($v['import_ChangeEncoding'])) ? true : false, 'v[import_ChangeEncoding]', g_l('import', '[encoding_change]') . $xml_encoding . g_l('import', '[encoding_to]') . DEFAULT_CHARSET . g_l('import', '[encoding_default]')) . we_html_element::htmlHiddens(array("v[import_XMLencoding]" => $xml_encoding, "v[import_TARGETencoding]" => DEFAULT_CHARSET)),
						'space' => we_html_multiIconBox::SPACE_MED
					);
				}
			} else {
				$parts[] = array(
					'headline' => g_l('import', '[encoding_headline]'),
					'html' => we_html_forms::checkboxWithHidden((!empty($v['import_ChangeEncoding'])) ? true : false, 'v[import_ChangeEncoding]', g_l('import', '[encoding_noway]') . we_html_element::htmlHidden("v[import_XMLencoding]", $xml_encoding), false, "defaultfont", '', true),
					'space' => we_html_multiIconBox::SPACE_MED
				);
			}

			$parts[] = array(
				'headline' => g_l('import', '[handle_file_options]'),
				'html' => we_html_forms::checkboxWithHidden((!empty($v['import_binarys'])) ? true : false, 'v[import_binarys]', g_l('import', '[import_files]')),
				'space' => we_html_multiIconBox::SPACE_MED
			);

			$parts[] = array(
				'headline' => g_l('import', '[rebuild]'),
				'html' => we_html_forms::checkboxWithHidden((!empty($v['rebuild'])) ? true : false, 'v[rebuild]', g_l('import', '[rebuild_txt]')),
				'space' => we_html_multiIconBox::SPACE_MED
			);

			$header = we_base_file::loadPart($_SERVER['DOCUMENT_ROOT'] . $v['import_from'], 0, 512);

			if(empty($header)){
				$functions = '
					function set_button_state() {
						top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "enabled");
						top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "disabled");
					}
				' . $event_handler;
				$parts = array(
					array(
						'headline' => '',
						'html' => we_html_tools::htmlAlertAttentionBox(g_l('import', '[invalid_path]'), we_html_tools::TYPE_ALERT, 530),
					)
				);
				$content = $hdns . we_html_multiIconBox::getHTML(we_import_functions::TYPE_WE_XML, $parts, 30, '', -1, '', '', false, g_l('import', '[warning]'));
				return array($functions, $content);
			}

			$show_owner_opt = strpos($header, '<we:info>') !== false;

			if($show_owner_opt){
				$tbl_extra = new we_html_table([], 2, 1);
				$tbl_extra->setCol(0, 0, null, we_html_forms::checkboxWithHidden((!empty($v['import_owners'])) ? true : false, 'v[import_owners]', g_l('import', '[handle_owners]')));
				$tbl_extra->setCol(1, 0, null, we_html_forms::checkboxWithHidden((!empty($v['owners_overwrite'])) ? true : false, 'v[owners_overwrite]', g_l('import', '[owner_overwrite]')));

				$tbl_extra2 = we_html_element::htmlDiv(array('style' => 'margin:20px 20px 0 0;'), $this->formWeChooser(USER_TABLE, '', 0, 'v[owners_overwrite_id]', (isset($v['owners_overwrite_id']) ? $v['owners_overwrite_id'] : 0), 'v[owners_overwrite_path]', (isset($v['owners_overwrite_path']) ? $v['owners_overwrite_path'] : '/')));

				$parts[] = array(
					'headline' => g_l('import', '[handle_owners_option]'),
					'html' => we_html_tools::htmlAlertAttentionBox(g_l('import', '[notexist_overwrite]'), we_html_tools::TYPE_ALERT, 530) . $tbl_extra->getHTML() . $tbl_extra2,
					'space' => we_html_multiIconBox::SPACE_MED
				);
			} else {
				$hdns .= we_html_element::htmlHiddens(array(
						'v[import_owners]' => 0,
						'v[owners_overwrite]' => 0,
						'v[owners_overwrite_id]' => 0));
			}
		} else {
			$parts[] = array(
				'headline' => g_l('import', '[xml_file]'),
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('import', '[invalid_wxml]'), we_html_tools::TYPE_ALERT, 530),
				'space' => we_html_multiIconBox::SPACE_MED
			);
		}
		$wepos = weGetCookieVariable('but_wxml');
		$znr = -1;
		$content = $hdns . we_html_multiIconBox::getHTML(we_import_functions::TYPE_WE_XML, $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', ($we_valid ? '[import_options]' : '[wxml_import]')));
		return array($functions, $content);
	}

	protected function getWXMLImportStep3(){
		$functions = '
function addLog(text){
	document.getElementById("log").innerHTML+= text+"<br/>";
	document.getElementById("log").scrollTop = 50000;
}

function set_button_state() {
	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "back", "disabled");
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, "next", "disabled");
}

function handle_event(evt) {
	switch(evt) {
		case "cancel":
			top.close();
			break;
	}
}';

		$hdns = '';
		$parts = array(
			array(
				'headline' => '',
				'html' => we_html_element::htmlDiv(array('class' => 'blockWrapper', 'style' => 'width: 520px; height: 400px; border:1px #dce6f2 solid;', 'id' => 'log'), ''),
			)
		);
		$content = $hdns . we_html_multiIconBox::getHTML(we_import_functions::TYPE_WE_XML, $parts, 30, '', -1, '', '', false, g_l('import', '[log]'));

		return array($functions, $content);
	}

	/**
	 * Generic XML Import Step 1
	 *
	 * @return unknown
	 */
	protected function getGXMLImportStep1(){
		global $DB_WE;

		$v = we_base_request::_(we_base_request::STRING, 'v', []);

		if(isset($v['docType']) && $v['docType'] != -1 && we_base_request::_(we_base_request::BOOL, 'doctypeChanged')){
			$values = getHash('SELECT ParentID,Extension,IsDynamic,Category FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . intval($v["docType"]), $GLOBALS['DB_WE']);
			$v['store_to_id'] = $values['ParentID'];
			$v['store_to_path'] = id_to_path($v['store_to_id']);
			$v['we_Extension'] = $values['Extension'];
			$v['is_dynamic'] = $values['IsDynamic'];
			$v['docCategories'] = $values['Category'];
		}

		$hdns = we_html_element::htmlHiddens(array(
				'v[importDataType]' => '',
				'v[import_from]' => (isset($v['import_from']) ? $v['import_from'] : ''),
				'v[docCategories]' => (isset($v['docCategories']) ? $v['docCategories'] : ''),
				'v[objCategories]' => (isset($v['objCategories']) ? $v['objCategories'] : ''),
				//'v[store_to_id]', 'value' => (isset($v['store_to_id']) ? $v['store_to_id'] : 0))).
				'v[collision]' => (isset($v['collision']) ? $v['collision'] : 'rename'),
				'doctypeChanged' => 0,
				'v[we_TemplateID]' => 0,
				//'v[we_TemplateName]', 'value' => '/')).
				'v[is_dynamic]' => (isset($v['is_dynamic']) ? $v['is_dynamic'] : 0)));

		if(!defined('OBJECT_TABLE')){
			$hdns .= we_html_element::htmlHidden('v[import_type]', 'documents');
		}

		$DefaultDynamicExt = DEFAULT_DYNAMIC_EXT;
		$DefaultStaticExt = DEFAULT_STATIC_EXT;


		$functions = "
function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}
function set_button_state() {
	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function switchExt() {
	var a = self.document.we_form.elements;
	if (a['v[is_dynamic]'].value==1) var changeto='" . $DefaultDynamicExt . "'; else var changeto='" . $DefaultStaticExt . "';
	a['v[we_Extension]'].value=changeto;
}

function handle_event(evt) {
	var f = self.document.we_form;
	if(f.elements['v[docType]'].value == -1) {
		f.elements['v[we_TemplateID]'].value = f.elements.noDocTypeTemplateId.value;
	} else {
		f.elements['v[we_TemplateID]'].value = f.elements.docTypeTemplateId.value;
	}
	switch(evt) {
		case 'previous':
			f.step.value = 0;
			top.location.href=WE().consts.dirs.WEBEDITION_DIR+ 'we_cmd.php?we_cmd[0]=import&we_cmd[1]=" . we_import_functions::TYPE_GENERIC_XML . "';
			break;
		case 'next':
			if(f.elements['v[rdofloc]'][1].checked===true){
				" . $this->fileUploader->getJsBtnCmd('upload') . "}
			else {
				handle_eventNext();
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}
function handle_eventNext(){
	var f = self.document.we_form;
	f.elements['v[we_TemplateID]'].value = f.elements['v[docType]'].value == -1 ? f.elements.noDocTypeTemplateId.value : f.elements.docTypeTemplateId.value;

	var fs = f.elements['v[fserver]'].value;
	var fl = we_FileUpload !== undefined ? 'placeholder.xml' : f.elements.uploaded_xml_file.value;
	var ext = '';

	if ((f.elements['v[rdofloc]'][0].checked==true) && fs!='/') {
		if (fs.match(/\.\./)=='..') {
			" . we_message_reporting::getShowMessageCall(g_l('import', '[invalid_path]'), we_message_reporting::WE_MESSAGE_ERROR) . " return;
		}
		ext = fs.substr(fs.length-4,4);
		f.elements['v[import_from]'].value = fs;
	}else if (f.elements['v[rdofloc]'][1].checked==true && fl!='') {
		ext = fl.substr(fl.length-4,4);
		f.elements['v[import_from]'].value = fl;
	}else if (fs=='/' || fl=='') {
		" . we_message_reporting::getShowMessageCall(g_l('import', '[select_source_file]'), we_message_reporting::WE_MESSAGE_ERROR) . " return;
	}
	if(!f.elements['v[we_TemplateID]'].value ) f.elements['v[we_TemplateID]'].value =f.elements.noDocTypeTemplateId.value;" .
			(defined('OBJECT_TABLE') ?
				"if((f.elements['v[import_type]'][0].checked == true && f.elements['v[we_TemplateID]'].value != 0) || (f.elements['v[import_type]'][1].checked == true)) {\n" :
				"if(f.elements['v[we_TemplateID]'].value!=0) {\n"
			) . "
			f.step.value = 2;
			we_submit_form(f, 'wizbody', '" . $this->path . "');
	} else {" .
			(defined('OBJECT_TABLE') ?
				"				if(f.elements['v[import_type]'][0].checked == true) " . we_message_reporting::getShowMessageCall(g_l('import', '[select_docType]'), we_message_reporting::WE_MESSAGE_ERROR) :
				we_message_reporting::getShowMessageCall(g_l('import', '[select_docType]'), we_message_reporting::WE_MESSAGE_ERROR)
			) . "
	}
}
";
		$functions .= <<<HTS

function deleteCategory(obj,cat){
	if(document.we_form.elements['v['+obj+'Categories]'].value.indexOf(','+cat+',') != -1) {
		re = new RegExp(','+cat+',');
		document.we_form.elements['v['+obj+'Categories]'].value = document.we_form.elements['v['+obj+'Categories]'].value.replace(re,',');
		document.getElementById(obj+"Cat"+cat).parentNode.removeChild(document.getElementById(obj+"Cat"+cat));
		if(document.we_form.elements['v['+obj+'Categories]'].value == ',') {
			document.we_form.elements['v['+obj+'Categories]'].value = '';
			document.getElementById(obj+"CatTable").innerHTML = "<tr><td style='font-size:8px'>&nbsp;</td></tr>";
		}
	}
}
var ajaxUrl = "/webEdition/rpc.php";

var handleSuccess = function(o){
	if(o.responseText !== undefined){
		var elems = JSON.parse(o.responseText).elems;
		var node, prop;
		for(var i = 0; i < elems.length; i++){
			if((node = elems[i].type === 'formelement' ? self.document.we_form.elements[elems[i].name] : document.getElementById(elems[i].name))){
				for(var j = 0; j < elems[i].props.length; j++){
					prop = elems[i].props[j];
					switch(prop.type){
						case 'attrib':
							node.setAttribute(prop.name, prop.val);
							break;
						case 'style':
							node.style[prop.name] = prop.val;
							break;
						case 'innerHTML':
							node.innerHTML = prop.val;
							break;
					}
				}
			}
		}
		switchExt();
	}
}

var handleFailure = function(o){

}

var callback = {
  success: handleSuccess,
  failure: handleFailure,
  timeout: 1500
};


function weChangeDocType(f) {
	ajaxData = 'protocol=json&cmd=ChangeDocType&cns=importExport&docType='+f.value;
	_executeAjaxRequest('POST',ajaxUrl, callback, ajaxData);
}

function _executeAjaxRequest(aMethod, aUrl, aCallback, aData){
	return YAHOO.util.Connect.asyncRequest(aMethod, aUrl, aCallback, aData);
}

HTS;

		$v['import_type'] = isset($v['import_type']) ? $v['import_type'] : 'documents';
		$wecmdenc1 = we_base_request::encCmd("top.wizbody.document.we_form.elements['v[fserver]'].value");
		$importFromButton = (permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES')) ? we_html_button::create_button(we_html_button::SELECT, "javascript: self.document.we_form.elements['v[rdofloc]'][0].checked=true;we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.elements['v[fserver]'].value);") : "";
		$inputLServer = we_html_tools::htmlTextInput('v[fserver]', 30, (isset($v['fserver']) ? $v['fserver'] : '/'), 255, 'readonly', 'text', 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, '', 'left', 'defaultfont', $importFromButton, '', '', '', '', 0);

		//FIXME: still need condition?
		$inputLLocal = $this->fileUploader->getHTML();
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, '', 'left', 'defaultfont', '', '', '', '', '', 0);
		$rdoLServer = we_html_forms::radiobutton('lServer', (isset($v['rdofloc'])) ? ($v['rdofloc'] === 'lServer') : 1, 'v[rdofloc]', g_l('import', '[fileselect_server]'));
		$rdoLLocal = we_html_forms::radiobutton('lLocal', (isset($v['rdofloc'])) ? ($v['rdofloc'] === 'lLocal') : 0, 'v[rdofloc]', g_l('import', '[fileselect_local]'));
		$importLocs = new we_html_table(array('class' => 'default'), 7, 1);
		$tblRow = 0;
		$importLocs->setCol($tblRow++, 0, [], $rdoLServer);
		$importLocs->setCol($tblRow++, 0, [], $importFromServer);
		$importLocs->setCol($tblRow++, 0, array('style' => 'padding-top:4px;'), $rdoLLocal);
		//FIXME: still need condition?
		$importLocs->setCol($tblRow++, 0, [], $this->fileUploader->getHtmlAlertBoxes());
		$importLocs->setCol($tblRow++, 0, [], $importFromLocal);

		$DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt ORDER BY dt.DocType');
		$DTselect = new we_html_select(array(
			'name' => 'v[docType]',
			'class' => 'weSelect',
			'onclick' => (defined('OBJECT_TABLE')) ? "self.document.we_form.elements['v[import_type]'][0].checked=true;" : '',
			'onchange' => 'this.form.doctypeChanged.value=1; weChangeDocType(this);',
			'style' => 'width: 300px')
		);
		$optid = 0;
		$DTselect->insertOption($optid, -1, g_l('import', '[none]'));

		$v['docType'] = isset($v['docType']) ? $v['docType'] : -1;
		while($DB_WE->next_record()){
			$optid++;
			$DTselect->insertOption($optid, $DB_WE->f('ID'), $DB_WE->f('DocType'));
			if($v['docType'] == $DB_WE->f('ID')){
				$DTselect->selectOption($DB_WE->f('ID'));
			}
		}
		$doctypeElement = we_html_tools::htmlFormElementTable($DTselect->getHTML(), g_l('import', '[doctype]'), 'left', 'defaultfont');

		/*		 * * templateElement *************************************************** */
		/* $ueberschrift = (permissionhandler::hasPerm('CAN_SEE_TEMPLATES')?
		  '<a href="javascript:goTemplate(document.we_form.elements[\'' . $idname . '\'].value)">' . g_l('import', '[template]') . '</a>':
		  g_l('import', '[template]')); */

		$myid = (isset($v['we_TemplateID'])) ? $v['we_TemplateID'] : 0;
		//$path = f('SELECT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($myid), 'Path', $DB_WE);

		$cmd1 = "top.wizbody.document.we_form.elements['noDocTypeTemplateId'].value";
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[we_TemplateName]'].value") . "','" . we_base_request::encCmd("opener.top.we_cmd('reload_editpage');") . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)");
		/*		 * ******************************************************************** */
		$yuiSuggest = & weSuggest::getInstance();

		$TPLselect = new we_html_select(array(
			'name' => 'docTypeTemplateId',
			'class' => 'weSelect',
			'onclick' => (defined('OBJECT_TABLE')) ? "self.document.we_form.elements['v[import_type]'][0].checked=true;" : '',
			//'onchange'  => "we_submit_form(self.document.we_form, 'wizbody', '".$this->path."');",
			'style' => 'width: 300px')
		);

		if($v['docType'] != -1 && count($TPLselect->childs)){
			$displayDocType = 'display:block';
			$displayNoDocType = 'display:none';
			$foo = getHash('SELECT TemplateID,Templates FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($v['docType']), $DB_WE);
			$ids_arr = makeArrayFromCSV($foo['Templates']);
			$paths_arr = id_to_path($foo['Templates'], TEMPLATES_TABLE, null, true);

			$optid = 0;
			while(list(, $templateID) = each($ids_arr)){
				$TPLselect->insertOption($optid, $templateID, $paths_arr[$optid]);
				$optid++;
				if(isset($v['we_TemplateID']) && $v['we_TemplateID'] == $templateID){
					$TPLselect->selectOption($templateID);
				}
			}
		} else {
			$displayDocType = 'display:none';
			$displayNoDocType = 'display:block';
		}

		$templateElement = "<div id='docTypeLayer' style='" . $displayDocType . "'>" . we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), "left", "defaultfont") . "</div>";

		$yuiSuggest->setAcId('TmplPath');
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$yuiSuggest->setInput('v[we_TemplateName]', (isset($v['we_TemplateName']) ? $v['we_TemplateName'] : ''), array('onFocus' => "self.document.we_form.elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult('noDocTypeTemplateId', $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($button, 10);
		$yuiSuggest->setLabel(g_l('import', '[template]'));

		$templateElement .= "<div id='noDocTypeLayer' style='" . $displayNoDocType . "'>" . $yuiSuggest->getHTML() . "</div>";


		$docCategories = $this->formCategory2('doc', isset($v['docCategories']) ? $v['docCategories'] : '');
		$docCats = new we_html_table(array('class' => 'default'), 2, 2);
		$docCats->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', 'class' => 'defaultfont lowContrast'), g_l('import', '[categories]'));
		$docCats->setCol(0, 1, array('style' => 'width:150px;'), $docCategories);
		$cmd1 = "top.wizbody.document.we_form.elements['v[store_to_id]'].value";
		$storeToButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[store_to_path]'].value") . "','','','0')"
		);

		$yuiSuggest->setAcId('DirPath');
		$yuiSuggest->setContentType(we_base_ContentTypes::FOLDER);
		$yuiSuggest->setInput('v[store_to_path]', (isset($v['store_to_path']) ? $v['store_to_path'] : '/'), array('onFocus' => "self.document.we_form.elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult('v[store_to_id]', (isset($v['store_to_id']) ? $v['store_to_id'] : 0));
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($storeToButton, 10);
		$yuiSuggest->setLabel(g_l('import', '[import_dir]'));

		$storeTo = $yuiSuggest->getHTML();

		$radioDocs = we_html_forms::radiobutton('documents', ($v['import_type'] === 'documents'), 'v[import_type]', g_l('import', '[documents]'));
		$radioObjs = we_html_forms::radiobutton('objects', ($v['import_type'] === 'objects'), 'v[import_type]', g_l('import', '[objects]'), true, 'defaultfont', "self.document.we_form.elements['v[store_to_path]'].value='/'; YAHOO.autocoml.setValidById(self.document.we_form.elements['v[store_to_path]'].id); if(self.document.we_form.elements['v[we_TemplateName]']!==undefined) { self.document.we_form.elements['v[we_TemplateName]'].value=''; YAHOO.autocoml.setValidById(self.document.we_form.elements['v[we_TemplateName]'].id); }", (defined('OBJECT_TABLE') ? false : true));

		$v['classID'] = isset($v['classID']) ? $v['classID'] : -1;
		$CLselect = new we_html_select(array(
			'name' => 'v[classID]',
			'class' => 'weSelect',
			'onclick' => "self.document.we_form.elements['v[import_type]'][1].checked=true;",
			'style' => 'width: 150px')
		);
		$optid = 0;
		$ac = implode(',', we_users_util::getAllowedClasses($DB_WE));
		if($ac){
			$DB_WE->query('SELECT ID,Text FROM ' . OBJECT_TABLE . ' ' . ($ac ? ' WHERE ID IN(' . $ac . ') ' : '') . 'ORDER BY Text');
			while($DB_WE->next_record()){
				$optid++;
				$CLselect->insertOption($optid, $DB_WE->f('ID'), $DB_WE->f('Text'));
				if($DB_WE->f('ID') == $v['classID']){
					$CLselect->selectOption($DB_WE->f('ID'));
				}
			}
		} else {
			$CLselect->insertOption($optid, -1, g_l('import', '[none]'));
		}

		$objClass = new we_html_table(array('class' => 'default'), 2, 2);
		$objClass->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', 'class' => 'defaultfont lowContrast'), g_l('import', '[class]'));
		$objClass->setCol(0, 1, array('style' => 'width:150px;'), $CLselect->getHTML());

		$objCategories = $this->formCategory2('obj', isset($v['objCategories']) ? $v['objCategories'] : '');
		$objCats = new we_html_table(array('class' => 'default'), 2, 2);
		$objCats->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', 'class' => 'defaultfont lowContrast'), g_l('import', '[categories]'));
		$objCats->setCol(0, 1, array('style' => 'width:150px;'), $objCategories);

		$objects = new we_html_table(array('class' => 'default'), 3, 2);
		$objects->setCol(0, 0, array('colspan' => 3, 'class' => 'withBigSpace'), $radioObjs);
		$objects->setCol(1, 0, array('style' => 'width:50px;'));
		$objects->setCol(1, 1, [], $objClass->getHTML());
		$objects->setCol(2, 1, [], $objCats->getHTML());

		$specifyDoc = new we_html_table(array('class' => 'default'), 1, 2);
		$specifyDoc->setCol(0, 1, array('style' => 'vertical-align:bottom'), we_html_forms::checkbox(3, (isset($v['is_dynamic']) ? $v['is_dynamic'] : 0), 'chbxIsDynamic', g_l('import', '[isDynamic]'), true, 'defaultfont', "this.form.elements['v[is_dynamic]'].value=this.checked? 1 : 0; switchExt();"));
		$specifyDoc->setCol(0, 0, array('style' => 'padding-right:20px;'), we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup('v[we_Extension]', (isset($v['we_Extension']) ? $v['we_Extension'] : '.html'), we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT), 100), g_l('import', '[extension]')));

		$parts = array(
			array(
				'headline' => g_l('import', '[import]'),
				'html' => $importLocs->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED),
			array(
				'headline' => (defined('OBJECT_TABLE')) ? $radioDocs : g_l('import', '[documents]'),
				'html' => weSuggest::getYuiFiles() . $doctypeElement . ' ' . $templateElement . ' ' . $storeTo . $yuiSuggest->getYuiJs() . ' ' . $specifyDoc->getHTML() . ' ' .
				we_html_tools::htmlFormElementTable($docCategories, g_l('import', '[categories]'), 'left', 'defaultfont'),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1)
		);

		if(defined('OBJECT_TABLE')){
			$parts[] = array(
				'headline' => $radioObjs,
				'html' => (defined('OBJECT_TABLE')) ? we_html_tools::htmlFormElementTable($CLselect->getHTML(), g_l('import', '[class]'), 'left', 'defaultfont') . ' ' .
					we_html_tools::htmlFormElementTable($objCategories, g_l('import', '[categories]'), 'left', 'defaultfont') : '',
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1
			);
		}

		$wepos = weGetCookieVariable('but_xml');
		$znr = -1;

		$content = YAHOO_FILES .
			$hdns .
			we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML('xml', $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', '[gxml_import]'));

		return array($functions, $content);
	}

	/**
	 * Generic XML Import Step 2
	 *
	 */
	protected function getGXMLImportStep2(){
		$parts = [];
		$hdns = "\n";
		$v = we_base_request::_(we_base_request::STRING, 'v');
		$upload_error = false;

		if($v['rdofloc'] === 'lLocal'){
			if((!$v['import_from'] = $this->fileUploader->commitUploadedFile())){
				$upload_error = $this->fileUploader->getError();
			}
		}

		$vars = array('rdofloc', 'fserver', 'flocal', 'importDataType', 'docCategories', 'objCategories', 'store_to_id', 'is_dynamic', 'import_from', 'docType',
			'we_TemplateName', 'we_TemplateID', 'store_to_path', 'we_Extension', 'import_type', 'classID', 'sct_node', 'rcd', 'from_elem', 'to_elem', 'collision');
		foreach($vars as $var){
			$hdns.= we_html_element::htmlHidden('v[' . $var . ']', (isset($v[$var])) ? $v[$var] : '');
		}
		$hdns.= we_html_element::htmlHiddens(array(
				'v[mode]' => 0,
				'v[cid]', 'value' => -2));

		if((file_exists($_SERVER['DOCUMENT_ROOT'] . $v['import_from']) && is_readable($_SERVER['DOCUMENT_ROOT'] . $v['import_from']))){
			$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $v['import_from']);
			$xmlWellFormed = ($xp->parseError === '') ? true : false;

			if($xmlWellFormed){
				// Node-set with paths to the child nodes.
				$node_set = $xp->evaluate('*/child::*');
				$children = $xp->nodes[$xp->root]['children'];

				$recs = [];
				foreach($children as $key => $value){
					$flag = true;
					for($k = 1; $k < ($value + 1); $k++){
						if(!$xp->hasChildNodes($xp->root . '/' . $key . '[' . $k . ']')){
							$flag = false;
						}
					}
					if($flag){
						$recs[$key] = $value;
					}
				}
				$isSingleNode = (count($recs) == 1);
				$hasChildNode = (!empty($recs));
			}
			if($xmlWellFormed && $hasChildNode){
				$rcdSelect = new we_html_select(array(
					'name' => 'we_select',
					'class' => 'weSelect',
					(($isSingleNode) ? 'disabled' : 'style') => '',
					'onchange' => "this.form.elements['v[to_iElem]'].value=this.options[this.selectedIndex].value; this.form.elements['v[from_iElem]'].value=1;this.form.elements['v[sct_node]'].value=this.options[this.selectedIndex].text;" .
					"if(this.options[this.selectedIndex].value==1) {this.form.elements['v[from_iElem]'].disabled=true;this.form.elements['v[to_iElem]'].disabled=true;} else {this.form.elements['v[from_iElem]'].disabled=false;this.form.elements['v[to_iElem]'].disabled=false;}")
				);
				$optid = 0;
				foreach($recs as $value => $text){
					if($optid == 0){
						$firstOptVal = $text;
					}
					$rcdSelect->addOption($text, $value);
					if(isset($v['rcd'])){
						if($text == $v['rcd']){
							$rcdSelect->selectOption($value);
						}
					}
					$optid++;
				}

				$tblSelect = new we_html_table([], 1, 7);
				$tblSelect->setCol(0, 1, [], $rcdSelect->getHtml());
				$tblSelect->setCol(0, 2, array('width' => 20));
				$tblSelect->setCol(0, 3, array('class' => 'defaultfont'), g_l('import', '[num_data_sets]'));
				$tblSelect->setCol(0, 4, [], we_html_tools::htmlTextInput('v[from_iElem]', 4, 1, 5, 'align=right', 'text', 50, '', '', ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));
				$tblSelect->setCol(0, 5, array('class' => 'defaultfont'), g_l('import', '[to]'));
				$tblSelect->setCol(0, 6, [], we_html_tools::htmlTextInput('v[to_iElem]', 4, $firstOptVal, 5, 'align=right', 'text', 50, '', '', ($isSingleNode && ($firstOptVal == 1)) ? 1 : 0));

				$tblFrame = new we_html_table([], 3, 2);
				$tblFrame->setCol(0, 0, array('colspan' => 2, 'class' => 'defaultfont'), ($isSingleNode) ? we_html_tools::htmlAlertAttentionBox(g_l('import', '[well_formed]') . ' ' . g_l('import', '[select_elements]'), we_html_tools::TYPE_INFO, 530) :
						we_html_tools::htmlAlertAttentionBox(g_l('import', '[xml_valid_1]') . ' ' . $optid . ' ' . g_l('import', '[xml_valid_m2]'), we_html_tools::TYPE_INFO, 530));
				$tblFrame->setCol(1, 0, array('colspan' => 2));
				$tblFrame->setCol(2, 1, [], $tblSelect->getHtml());

				$parts[] = array('html' => $tblFrame->getHtml(), 'noline' => 1);
			} else {
				$parts[] = array('html' => we_html_tools::htmlAlertAttentionBox(g_l('import', (!$xmlWellFormed ? '[not_well_formed]' : '[missing_child_node]')), we_html_tools::TYPE_ALERT, 530), 'noline' => 1);
			}
		} else {
			$xmlWellFormed = $hasChildNode = false;

			if($upload_error){ // uploaded file nok: get error from uploader
				$parts[] = array('html' => we_html_tools::htmlAlertAttentionBox($upload_error, we_html_tools::TYPE_ALERT, 530), 'noline' => 1);
			} else { // file from server nok
				if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $v['import_from'])){
					$parts[] = array('html' => we_html_tools::htmlAlertAttentionBox(g_l('import', '[file_exists]') . $_SERVER['DOCUMENT_ROOT'] . $v['import_from'], we_html_tools::TYPE_ALERT, 530), 'noline' => 1);
				} elseif(!is_readable($_SERVER['DOCUMENT_ROOT'] . $v['import_from'])){
					$parts[] = array('html' => we_html_tools::htmlAlertAttentionBox(g_l('import', '[file_readable]'), we_html_tools::TYPE_ALERT, 530), 'noline' => 1);
				}
			}
		}

		$functions = "
function set_button_state() {
	top.wizbusy.back_enabled=WE().layout.button.switch_button_state(top.wizbusy.document, 'back','enabled');
	top.wizbusy.next_enabled=WE().layout.button.switch_button_state(top.wizbusy.document, 'next','" . (($xmlWellFormed && $hasChildNode) ? "enabled" : "disabled") . "');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.we_form;
	switch(evt) {
	case 'previous':
		f.step.value = 1;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
		break;
	case 'next':
		f.elements['v[from_elem]'].value = f.elements['v[from_iElem]'].value;
		f.elements['v[to_elem]'].value = f.elements['v[to_iElem]'].value;
		iStart = isNaN(parseInt(f.elements['v[from_iElem]'].value))? 0 : f.elements['v[from_iElem]'].value;
		iEnd = isNaN(parseInt(f.elements['v[to_iElem]'].value))? 0 : f.elements['v[to_iElem]'].value;
		iElements = parseInt(f.elements.we_select.options[f.elements.we_select.selectedIndex].value);
		if ((iStart < 1) || (iStart > iElements) || (iEnd < 1) || (iEnd > iElements)) {
			msg = \"" . g_l('import', '[num_elements]') . "\" +iElements;" .
			we_message_reporting::getShowMessageCall("msg", we_message_reporting::WE_MESSAGE_ERROR, true) . "
		} else {
			f.elements['v[rcd]'].value = f.we_select.options[f.we_select.selectedIndex].text;
			f.step.value = 3;
			we_submit_form(f, 'wizbody', '" . $this->path . "');
		}
		break;
	case 'cancel':
		top.close();
		break;
	}
}";

		$wepos = weGetCookieVariable('but_xml');
		$znr = -1;

		$content = $hdns .
			we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML('xml', $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', '[select_data_set]'));

		return array($functions, $content);
	}

	protected function getGXMLImportStep3(){
		$v = we_base_request::_(we_base_request::STRING, 'v');
		if(isset($v['att_pfx'])){
			$v['att_pfx'] = base64_encode($v['att_pfx']);
		}
		$records = we_base_request::_(we_base_request::RAW, 'records', []);
		$we_flds = we_base_request::_(we_base_request::RAW, 'we_flds', []);
		$attrs = we_base_request::_(we_base_request::RAW, 'attrs', []);
		foreach($attrs as $name => $value){
			$attrs[$name] = base64_encode($value);
		}

		$hdns = $this->getHdns('v', $v) .
			($records ? $this->getHdns('records', $records) : '') .
			($we_flds ? $this->getHdns('we_flds', $we_flds) : '') .
			($attrs ? $this->getHdns('attributes', $attrs) : '') .
			//$hdns .= ' => 'v[cid]', 'value' => -2));
			we_html_element::htmlHiddens(array(
				'v[pfx_fn]' => ((!isset($v['pfx_fn'])) ? 0 : $v['pfx_fn']),
				(isset($v['rdo_timestamp']) ? 'v[sTimeStamp]' : '') => $v['rdo_timestamp']));

		$functions = "
function set_button_state() {
	top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', " . ((we_base_request::_(we_base_request::INT, 'mode') != 1) ? "'enabled'" : "'disabled'") . ");
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.we_form;
	switch(evt) {
		case 'previous':
			f.step.value = 2;
			we_submit_form(f, 'wizbody', '" . $this->path . "');
			break;
		case 'next':
			f.step.value=3;
			f.mode.value=1;
			f.elements['v[mode]'].value=1;
			top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'disabled');
			we_submit_form(f, 'wizbody', '" . $this->path . "&mode=1');
			break;
		case 'cancel':
			top.close();
			break;
	}
}";

		$db = new DB_WE();

		$records = $dateFields = [];

		if($v['import_type'] === 'documents'){
			$templateCode = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND l.DID=' . intval($v['we_TemplateID']) . ' AND l.nHash=x\'' . md5("completeData") . '\'', '', $db);
			$tp = new we_tag_tagParser($templateCode);
			$tags = $tp->getAllTags();
			$regs = [];

			foreach($tags as $tag){
				if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
					$tagname = $regs[1];
					if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != "var") && ($tagname != "field")){
						$name = $regs[1];
						switch($tagname){
							// tags with text content, links and hrefs
							case 'input':
								if(in_array('date', we_tag_tagParser::makeArrayFromAttribs($tag))){
									$dateFields[] = $name;
								}
							case 'textarea':
							case 'href':
							case 'link':
								$records[] = $name;
								break;
						}
					}
				}
			}
			$records[] = 'Title';
			$records[] = 'Description';
			$records[] = 'Keywords';
			$records[] = 'Charset';
			$records = array_unique($records);
		} else {
			$classFields = self::getClassFields($v['classID']);
			foreach($classFields as $classField){
				if(self::isTextField($classField['type']) || self::isNumericField($classField['type']) || self::isDateField($classField['type'])){
					$records[] = $classField['name'];
				}
				if(self::isDateField($classField['type'])){
					$dateFields[] = $classField['name'];
				}
			}
		}
		$xp = new we_xml_parser($_SERVER['DOCUMENT_ROOT'] . $v['import_from']);
		$nodeSet = $xp->evaluate($xp->root . '/' . $v['rcd'] . '[1]/child::*');
		$val_nodes = $val_attrs = [];

		foreach($nodeSet as $node){
			$nodeName = $xp->nodeName($node);
			$tmp_nodes = array($nodeName => $nodeName);
			$val_nodes = $val_nodes + $tmp_nodes;

			if($xp->hasAttributes($node)){
				$val_attrs = $val_attrs + array('@n:' => g_l('import', '[none]'));
				$attributes = $xp->getAttributes($node);

				foreach($attributes as $name => $value){
					$tmp_attrs = array($name => $name);
					$val_attrs = $val_attrs + $tmp_attrs;
				}
			}
		}
		if(empty($val_attrs)){
			$val_attrs = array('@n:' => g_l('import', '[none]'));
		}

		$th = array(array('dat' => g_l('import', '[we_flds]')), array('dat' => g_l('import', '[rcd_flds]')), array('dat' => g_l('import', '[attributes]')));
		$rows = [];

		reset($records);
		$i = 0;
		while(list(, $record) = each($records)){
			$hdns .= we_html_element::htmlHidden('records[' . $i . ']', $record);
			$sct_we_fields = new we_html_select(array(
				'name' => 'we_flds[' . $record . ']',
				'class' => 'weSelect',
				'onclick' => '',
				'style' => '')
			);

			reset($val_nodes);
			$sct_we_fields->addOption('', g_l('import', '[any]'));
			foreach($val_nodes as $value => $text){
				$sct_we_fields->addOption(oldHtmlspecialchars($value), $text);
				if(isset($we_flds[$record])){
					if($value == $we_flds[$record]){
						$sct_we_fields->selectOption($value);
					}
				} elseif($value == $record){
					$sct_we_fields->selectOption($value);
				}
			}
			switch($record){
				case 'Title':
					$new_record = g_l('import', '[we_title]');
					break;
				case 'Description':
					$new_record = g_l('import', '[we_description]');
					break;
				case 'Keywords':
					$new_record = g_l('import', '[we_keywords]');
					break;
				default:
					$new_record = '';
			}
			$rows[] = array(
				array('dat' => ($new_record != '') ? $new_record : $record), array('dat' => $sct_we_fields->getHTML()),
				array('dat' => we_html_tools::htmlTextInput('attrs[' . $record . ']', 30, (isset($attrs[$record]) ? base64_decode($attrs[$record]) : ''), 255, '', 'text', 100))
			);
			$i++;
		}

		// Associated prefix selector.
		$asocPfx = new we_html_table(array('class' => 'default'), 1, 1);
		$asocPfx->setCol(0, 0, array('class' => 'defaultfont'), g_l('import', '[pfx]') . '<br/><br/>' .
			we_html_tools::htmlTextInput('v[asoc_prefix]', 30, (isset($v['asoc_prefix']) ? $v['asoc_prefix'] : g_l('import', ($v['import_type'] === 'documents' ? '[pfx_doc]' : '[pfx_obj]'))), 255, "onclick=\"self.document.we_form.elements['v[rdo_filename]'][0].checked=true;\"", "text", 150));

		// Assigned record or attribute field selectors.
		$rcdPfxSelect = new we_html_select(array(
			'name' => 'v[rcd_pfx]',
			'class' => 'weSelect',
			'onclick' => "self.document.we_form.elements['v[pfx_fn]'].value=1;self.document.we_form.elements['v[rdo_filename]'][1].checked=true;",
			'style' => 'width: 150px')
		);

		foreach($val_nodes as $value => $text){
			$rcdPfxSelect->addOption(oldHtmlspecialchars($value), $text);
			if(isset($v['rcd_pfx'])){
				if($text == $v['rcd_pfx']){
					$rcdPfxSelect->selectOption($value);
				}
			}
		}

		$attPfxSelect = we_html_tools::htmlTextInput('v[att_pfx]', 30, (isset($v['att_pfx']) ? base64_decode($v['att_pfx']) : ''), 255, "onclick=\"self.document.we_form.elements['v[rdo_filename]'][1].checked=true;\"", "text", 100);

		$asgndFld = new we_html_table(array('class' => 'default'), 1, 3);
		$asgndFld->setCol(0, 0, array('class' => 'defaultfont'), g_l('import', '[rcd_fld]') . '<br/><br/>' . $rcdPfxSelect->getHTML());
		$asgndFld->setCol(0, 1, array('width' => 20), '');
		$asgndFld->setCol(0, 2, array('class' => 'defaultfont'), g_l('import', '[attributes]') . '<br/><br/>' . $attPfxSelect);

		// Filename selector.
		$fn = new we_html_table(array('class' => 'default'), 3, 2);
		$fn->setCol(0, 0, array('colspan' => 2), we_html_forms::radiobutton(0, (!isset($v['rdo_filename']) ? true : ($v['rdo_filename'] == 0) ? true : false), 'v[rdo_filename]', g_l('import', '[auto]'), true, 'defaultfont', "self.document.we_form.elements['v[pfx_fn]'].value=0;"));
		$fn->setCol(1, 0, array('style' => 'padding-left:25px;'), $asocPfx->getHTML());
		$fn->setCol(2, 0, array('colspan' => 2, 'style' => 'padding-top:5px;'), we_html_forms::radiobutton(1, (!isset($v['rdo_filename']) ? false : ($v['rdo_filename'] == 1) ? true : false), "v[rdo_filename]", g_l('import', '[asgnd]'), true, "defaultfont", "self.document.we_form.elements['v[pfx_fn]'].value=1;"));
		$fn->setCol(4, 0, array('style' => 'padding-left:25px;'), $asgndFld->getHTML());

		$parts = array(
			array(
				'html' => '<br/>' . we_html_tools::htmlDialogBorder3(510, $rows, $th, 'defaultfont'),
			)
		);
		if(!empty($dateFields)){
			// Timestamp
			$tStamp = new we_html_table(array('class' => 'default withSpace'), 4, 1);
			$tStamp->setCol(0, 0, array('colspan' => 2), we_html_forms::radiobutton('Unix', (!isset($v['rdo_timestamp']) ? 1 : ($v['rdo_timestamp'] === 'Unix') ? 1 : 0), 'v[rdo_timestamp]', g_l('import', '[uts]'), true, 'defaultfont', '', 0, g_l('import', '[unix_timestamp]'), 0, 384));
			$tStamp->setCol(1, 0, array('colspan' => 2), we_html_forms::radiobutton('GMT', (!isset($v['rdo_timestamp']) ? 0 : ($v['rdo_timestamp'] === 'GMT') ? 1 : 0), 'v[rdo_timestamp]', g_l('import', '[gts]'), true, 'defaultfont', '', 0, g_l('import', '[gmt_timestamp]'), 0, 384));
			$tStamp->setCol(2, 0, array('colspan' => 2), we_html_forms::radiobutton('Format', (!isset($v['rdo_timestamp']) ? 0 : ($v['rdo_timestamp'] === 'Format') ? 1 : 0), 'v[rdo_timestamp]', g_l('import', '[fts]'), true, 'defaultfont', '', 0, g_l('import', '[format_timestamp]'), 0, 384));
			$tStamp->setCol(3, 0, array('style' => 'padding-left:25px;'), we_html_tools::htmlTextInput('v[timestamp]', 30, (isset($v['timestamp']) ? $v['timestamp'] : ''), '', "onclick=\"self.document.we_form.elements['v[rdo_timestamp]'][2].checked=true;\"", "text", 150));

			$parts[] = array(
				'headline' => g_l('import', '[format_date]'),
				'html' => $tStamp->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED
			);
			if(!isset($v['dateFields'])){
				$hdns .= we_html_element::htmlHidden('v[dateFields]', implode(',', $dateFields));
			}
		}

		$parts[] = array(
			'headline' => g_l('import', '[name]'),
			'html' => $fn->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED,
			'noline' => 1
		);

		$conflict = isset($v['collision']) ? $v['collision'] : 'rename';
		$fn_colsn = new we_html_table(array('class' => 'default withSpace'), 3, 1);
		$fn_colsn->setCol(0, 0, [], we_html_forms::radiobutton('rename', $conflict === 'rename', 'nameconflict', g_l('import', '[rename]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='rename';"));
		$fn_colsn->setCol(1, 0, [], we_html_forms::radiobutton('replace', $conflict === 'replace', 'nameconflict', g_l('import', '[replace]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='replace';"));
		$fn_colsn->setCol(2, 0, [], we_html_forms::radiobutton('skip', $conflict === 'skip', 'nameconflict', g_l('import', '[skip]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='skip';"));

		$parts[] = array(
			'headline' => g_l('import', '[name_collision]'),
			'html' => $fn_colsn->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED2
		);

		$wepos = weGetCookieVariable('but_xml');
		$znr = -1;

		$content = $hdns .
			we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML('xml', $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', '[assign_record_fields]'));

		return array($functions, $content);
	}

	protected function getCSVImportStep1(){
		global $DB_WE;
		$v = we_base_request::_(we_base_request::STRING, 'v');

		$functions = "
function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
	//var url = WE().util.getWe_cmdArgsUrl(args);

	parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}
function set_button_state() {
	top.frames.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.frames.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}

function handle_event(evt) {
	var f = self.document.we_form;
	switch(evt) {
		case 'previous':
			f.step.value = 0;
			top.location.href='" . WEBEDITION_DIR . "we_cmd.php?we_cmd[0]=import&we_cmd[1]=" . we_import_functions::TYPE_CSV . "';
			break;
		case 'next':
			if(f.elements['v[rdofloc]'][1].checked===true){
				" . $this->fileUploader->getJsBtnCmd('upload') . "}
			else {
				handle_eventNext();
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}
function handle_eventNext(){
	var f = self.document.we_form,
		fvalid = true,
		fs = f.elements['v[fserver]'].value,
		fl = we_FileUpload !== undefined ? 'placeholder.xml' : f.elements['uploaded_csv_file'].value,
		ext = '';

	if ((f.elements['v[rdofloc]'][0].checked==true) && fs!='/') {
		if (fs.match(/\.\./)=='..') { " . we_message_reporting::getShowMessageCall(g_l('import', '[invalid_path]'), we_message_reporting::WE_MESSAGE_ERROR) . " return; }
		ext = fs.substr(fs.length-4,4);
		f.elements['v[import_from]'].value = fs;
	}else if (f.elements['v[rdofloc]'][1].checked==true && fl!='') {
		ext = fl.substr(fl.length-4,4);
		f.elements['v[import_from]'].value = fl;
	}else if (fs=='/' || fl=='') {" .
			(we_message_reporting::getShowMessageCall(g_l('import', '[select_source_file]'), we_message_reporting::WE_MESSAGE_ERROR)) . " return;
	}
	if (fvalid && f.elements['v[csv_seperator]'].value=='') { fvalid=false; " . we_message_reporting::getShowMessageCall(g_l('import', '[select_seperator]'), we_message_reporting::WE_MESSAGE_ERROR) . "}
	if (fvalid) {
		f.step.value = 2;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
	}
}
";

		$v['import_type'] = isset($v['import_type']) ? $v['import_type'] : 'documents';
		$wecmdenc1 = we_base_request::encCmd("top.wizbody.document.we_form.elements['v[fserver]'].value");
		$importFromButton = (permissionhandler::hasPerm('CAN_SELECT_EXTERNAL_FILES')) ? we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('browse_server', '" . $wecmdenc1 . "', '', document.we_form.elements['v[fserver]'].value)") : "";
		$inputLServer = we_html_tools::htmlTextInput('v[fserver]', 30, (isset($v['fserver']) ? $v['fserver'] : '/'), 255, "readonly onclick=\"self.document.we_form.elements['v[rdofloc]'][0].checked=true;\"", "text", 300);
		$importFromServer = we_html_tools::htmlFormElementTable($inputLServer, '', 'left', 'defaultfont', $importFromButton, '', "", "", "", 0);

		$inputLLocal = $this->fileUploader->getHTML();
		$importFromLocal = we_html_tools::htmlFormElementTable($inputLLocal, '', 'left', 'defaultfont', '', "", "", "", "", 0);
		$rdoLServer = we_html_forms::radiobutton('lServer', (isset($v['rdofloc'])) ? ($v['rdofloc'] === 'lServer') : 1, 'v[rdofloc]', g_l('import', '[fileselect_server]'));
		$rdoLLocal = we_html_forms::radiobutton('lLocal', (isset($v['rdofloc'])) ? ($v['rdofloc'] === 'lLocal') : 0, 'v[rdofloc]', g_l('import', '[fileselect_local]'));
		$importLocs = new we_html_table(array('class' => 'default'), 7, 1);
		$tblRow = 0;
		$importLocs->setCol($tblRow++, 0, [], $rdoLServer);
		$importLocs->setCol($tblRow++, 0, [], $importFromServer);
		$importLocs->setCol($tblRow++, 0, array('style' => 'padding-top:4px;'), $rdoLLocal);
		// FIXME: still need condition?
		$importLocs->setCol($tblRow++, 0, [], $this->fileUploader->getHtmlAlertBoxes());
		$importLocs->setCol($tblRow++, 0, [], $importFromLocal);

		$iptDel = we_html_tools::htmlTextInput('v[csv_seperator]', 2, (isset($v['csv_seperator']) ? (($v['csv_seperator'] != '') ? $v['csv_seperator'] : ' ') : ';'), 2, '', 'text', 20);
		$fldDel = new we_html_select(array('name' => 'v[sct_csv_seperator]', 'class' => 'weSelect', 'onchange' => "this.form.elements['v[csv_seperator]'].value=this.options[this.selectedIndex].innerHTML.substr(0,2);this.selectedIndex=options[0];", "style" => "width: 130px"));
		$fldDel->addOption('', '');
		$fldDel->addOption('semicolon', g_l('import', '[semicolon]'));
		$fldDel->addOption('comma', g_l('import', '[comma]'));
		$fldDel->addOption('colon', g_l('import', '[colon]'));
		$fldDel->addOption('tab', g_l('import', '[tab]'));
		$fldDel->addOption('space', g_l('import', '[space]'));
		if(isset($v['sct_csv_seperator'])){
			$fldDel->selectOption($v['sct_csv_seperator']);
		}

		$charSet = new we_html_select(array('name' => 'v[file_format]', 'class' => 'weSelect', 'onchange' => '', 'style' => ''));
		$charSet->addOption('win', 'Windows');
		$charSet->addOption('unix', 'Unix');
		$charSet->addOption('mac', 'Mac');
		if(isset($v['file_format'])){
			$charSet->selectOption($v['file_format']);
		}

		$txtDel = new we_html_select(array('name' => 'v[csv_enclosed]', 'class' => 'weSelect', 'onchange' => '', 'style' => 'width: 300px'));
		$txtDel->addOption('double_quote', g_l('import', '[double_quote]'));
		$txtDel->addOption('single_quote', g_l('import', '[single_quote]'));
		$txtDel->addOption('none', g_l('import', '[none]'));
		if(isset($v['csv_enclosed'])){
			$txtDel->selectOption($v['csv_enclosed']);
		}

		$rowDef = we_html_forms::checkbox('', (isset($v['csv_fieldnames']) ? $v['csv_fieldnames'] : true), 'checkbox_fieldnames', g_l('import', '[contains]'), true, 'defaultfont', "this.form.elements['v[csv_fieldnames]'].value=this.checked ? 1 : 0;");

		$csvSettings = new we_html_table(array('class' => 'default withSpace'), 4, 1);
		$csvSettings->setCol(0, 0, array('class' => 'defaultfont'), g_l('import', '[file_format]') . '<br/><br/>' . $charSet->getHtml());
		$csvSettings->setCol(1, 0, array('class' => 'defaultfont'), g_l('import', '[field_delimiter]') . '<br/><br/>' . $iptDel . ' ' . $fldDel->getHtml());
		$csvSettings->setCol(2, 0, array('class' => 'defaultfont'), g_l('import', '[text_delimiter]') . '<br/><br/>' . $txtDel->getHtml());
		$csvSettings->setCol(3, 0, [], $rowDef);

		$parts = array(
			array(
				'headline' => g_l('import', '[import]'),
				'html' => $importLocs->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED),
			array(
				'headline' => g_l('import', '[field_options]'),
				'html' => $csvSettings->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED,
				'noline' => 1)
		);

		$content = we_html_element::htmlHiddens(array(
				'v[csv_fieldnames]' => (isset($v['csv_fieldnames'])) ? $v['csv_fieldnames'] : 1,
				'v[import_from]' => (isset($v['import_from']) ? $v['import_from'] : ''),
				'v[csv_escaped]' => (isset($v['csv_escaped'])) ? $v['csv_escaped'] : '',
				'v[collision]' => (isset($v['collision'])) ? $v['collision'] : 'rename',
				'v[csv_terminated]' => (isset($v['csv_terminated'])) ? $v['csv_terminated'] : ''));

		$content.= we_html_multiIconBox::getHTML('csv', $parts, 30, '', -1, '', '', false, g_l('import', '[csv_import]'));

		return array($functions, $content);
	}

	protected function getCSVImportStep2(){
		global $DB_WE;
		$v = we_base_request::_(we_base_request::STRING, 'v');
		$upload_error = false;

		if($v['rdofloc'] === 'lLocal'){
			if((!$v['import_from'] = $this->fileUploader->commitUploadedFile())){
				$upload_error = $this->fileUploader->getError();
			}
		} else {
			$realPath = realpath($_SERVER['DOCUMENT_ROOT'] . $v['import_from']);
			if(strpos($realPath, $_SERVER['DOCUMENT_ROOT']) === FALSE){
				t_e('warning', 'Acess outside document_root forbidden!', $realPath);
			} else {
				$contents = we_base_file::load($fp, 'r');
				$v['import_from'] = TEMP_DIR . 'we_csv_' . we_base_file::getUniqueId() . '.csv';
				$replacement = str_replace("\r", "\n", $contents);
				we_base_file::save($fp, $replacement, 'w+');
			}
		}

		if(isset($v['docType']) && $v['docType'] != -1 && we_base_request::_(we_base_request::BOOL, 'doctypeChanged')){
			$values = getHash('SELECT ParentID,Extension,IsDynamic,Category FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($v['docType']));
			$v['store_to_id'] = $values['ParentID'];

			$v['store_to_path'] = id_to_path($v['store_to_id']);
			$v['we_Extension'] = $values['Extension'];
			$v['is_dynamic'] = $values['IsDynamic'];
			$v['docCategories'] = $values['Category'];
		}
		$hdns = we_html_element::htmlHiddens(array(
				'v[mode]' => (isset($v['mode']) ? $v['mode'] : ''),
				'v[import_from]' => $v['import_from'],
				'v[collision]' => $v['collision'],
				'v[rdofloc]' => $v['rdofloc'],
				'v[fserver]' => $v['fserver'],
				'v[csv_fieldnames]' => $v['csv_fieldnames'],
				'v[csv_seperator]' => trim($v['csv_seperator']),
				'v[csv_enclosed]' => $v['csv_enclosed'],
				'v[csv_escaped]' => $v['csv_escaped'],
				'v[csv_terminated]' => $v['csv_terminated'],
				'v[docCategories]' => (isset($v['docCategories']) ? $v['docCategories'] : ''),
				'v[objCategories]' => (isset($v['objCategories']) ? $v['objCategories'] : ''),
				//rray('name' => 'v[store_to_id]', 'value' => (isset($v['store_to_id']) ? $v['store_to_id'] : 0))).
				'v[we_TemplateID]' => (isset($v['we_TemplateID']) ? $v['we_TemplateID'] : 0),
				'v[is_dynamic]' => (isset($v['is_dynamic']) ? $v['is_dynamic'] : 0),
				'doctypeChanged' => 0,
				'v[file_format]' => $v['file_format'],
				(defined('OBJECT_TABLE') ? '' : 'v[import_type]') => 'documents')
		);

		$DefaultDynamicExt = DEFAULT_DYNAMIC_EXT;
		$DefaultStaticExt = DEFAULT_STATIC_EXT;


		$functions = "
function we_cmd() {
	//var args = WE().util.getWe_cmdArgsArray(Array.prototype.slice.call(arguments));
//	var url = WE().util.getWe_cmdArgsUrl(args);
	parent.we_cmd.apply(this, Array.prototype.slice.call(arguments));
}
function set_button_state() {
	top.frames.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
	top.frames.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'enabled');
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function switchExt() {
	var a = self.document.we_form.elements;
	if (a['v[is_dynamic]'].value==1) var changeto='" . $DefaultDynamicExt . "'; else var changeto='" . $DefaultStaticExt . "';
	a['v[we_Extension]'].value=changeto;
}
function handle_event(evt) {
	var f = self.document.we_form;
	if(f.elements['v[import_type]'].value == 'documents'){
		f.elements['v[we_TemplateID]'].value = f.elements['v[docType]'].value == -1 ? f.elements.noDocTypeTemplateId.value : f.elements.docTypeTemplateId.value;
	}

	switch(evt) {
		case 'previous':
			f.step.value = 1;
			we_submit_form(f, 'wizbody', '" . $this->path . "');
			break;
		case 'next':
			if(f.elements['v[import_type]'].value == 'documents'){
				if(!f.elements['v[we_TemplateID]'].value ) {
					f.elements['v[we_TemplateID]'].value =f.elements.DocTypeTemplateId.value;
				}
			}" . (defined('OBJECT_TABLE') ?
				"			if(f.elements['v[import_from]'].value != '/' && ((f.elements['v[import_type]'][0].checked == true && f.elements['v[we_TemplateID]'].value != 0) || (f.elements['v[import_type]'][1].checked == true)))" :
				"			if(f.elements['v[import_from]'].value != '/' && f.elements['v[we_TemplateID]'].value != 0)") . "
			{
				f.step.value = 3;
				we_submit_form(f, 'wizbody', '" . $this->path . "');
			} else {
				if(f.elements['v[import_from]'].value == '/') {
					" . we_message_reporting::getShowMessageCall(g_l('import', '[select_source_file]'), we_message_reporting::WE_MESSAGE_ERROR) .
			'}' .
			(defined('OBJECT_TABLE') ?
				"				else if(f.elements['v[import_type]'][0].checked == true) {" . we_message_reporting::getShowMessageCall(g_l('import', '[select_docType]'), we_message_reporting::WE_MESSAGE_ERROR) . '}' :
				"				else {" . we_message_reporting::getShowMessageCall(g_l('import', '[select_docType]'), we_message_reporting::WE_MESSAGE_ERROR)) . "
			}
			break;
		case 'cancel':
			top.close();
			break;
	}
}";

		$functions .= <<<HTS

function deleteCategory(obj,cat){
	if(document.we_form.elements['v['+obj+'Categories]'].value.indexOf(','+cat+',') != -1) {
		re = new RegExp(','+cat+',');
		document.we_form.elements['v['+obj+'Categories]'].value = document.we_form.elements['v['+obj+'Categories]'].value.replace(re,',');
		document.getElementById(obj+"Cat"+cat).parentNode.removeChild(document.getElementById(obj+"Cat"+cat));
		if(document.we_form.elements['v['+obj+'Categories]'].value == ',') {
			document.we_form.elements['v['+obj+'Categories]'].value = '';
			document.getzzElementById(obj+"CatTable").innerHTML = "<tr><td style='font-size:8px'>&nbsp;</td></tr>";
		}
	}
}
var ajaxUrl = "/webEdition/rpc.php";

var handleSuccess = function(o){
	if(o.responseText !== undefined){
		var elems = JSON.parse(o.responseText).elems;
		var node, prop;
		for(var i = 0; i < elems.length; i++){
			if((node = elems[i].type === 'formelement' ? self.document.we_form.elements[elems[i].name] : document.getElementById(elems[i].name))){
				for(var j = 0; j < elems[i].props.length; j++){
					prop = elems[i].props[j];
					switch(prop.type){
						case 'attrib':
							node.setAttribute(prop.name, prop.val);
							break;
						case 'style':
							node.style[prop.name] = prop.val;
							break;
						case 'innerHTML':
							node.innerHTML = prop.val;
							break;
					}
				}
			}
		}
		switchExt();
	}
}

var handleFailure = function(o){

}

var callback = {
  success: handleSuccess,
  failure: handleFailure,
  timeout: 1500
};


function weChangeDocType(f) {
	ajaxData = 'protocol=json&cmd=ChangeDocType&cns=importExport&docType='+f.value;
	_executeAjaxRequest('POST',ajaxUrl, callback, ajaxData);
}

function _executeAjaxRequest(aMethod, aUrl, aCallback, aData){
	return YAHOO.util.Connect.asyncRequest(aMethod, aUrl, aCallback, aData);
}

HTS;
		$v['import_type'] = isset($v['import_type']) ? $v['import_type'] : 'documents';
		$cmd1 = "top.wizbody.document.we_form.elements['v[store_to_id]'].value";

		$storeToButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory'," . $cmd1 . ",'" . FILE_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[store_to_path]'].value") . "','','','0')");

		$DTselect = new we_html_select(array(
			'name' => 'v[docType]',
			'class' => 'weSelect',
			'onclick' => (defined('OBJECT_TABLE')) ? "self.document.we_form.elements['v[import_type]'][0].checked=true;" : '',
			'onchange' => "this.form.doctypeChanged.value=1; weChangeDocType(this);",
			'style' => 'width: 300px')
		);
		$optid = 0;
		$DTselect->insertOption($optid, -1, g_l('import', '[none]'));

		$v['docType'] = isset($v['docType']) ? $v['docType'] : -1;
		$DB_WE->query('SELECT dt.ID,dt.DocType FROM ' . DOC_TYPES_TABLE . ' dt ORDER BY dt.DocType');
		while($DB_WE->next_record()){
			$optid++;
			$DTselect->insertOption($optid, $DB_WE->f('ID'), $DB_WE->f('DocType'));
			if($v['docType'] == $DB_WE->f('ID')){
				$DTselect->selectOption($DB_WE->f('ID'));
			}
		}

		$doctypeElement = we_html_tools::htmlFormElementTable($DTselect->getHTML(), g_l('import', '[doctype]'), "left", "defaultfont");

		/*		 * * templateElement *************************************************** */
		/* $ueberschrift = (permissionhandler::hasPerm("CAN_SEE_TEMPLATES") ?
		  '<a href="javascript:goTemplate(document.we_form.elements[\'' . $idname . '\'].value)">' . g_l('import', '[template]') . '</a>' :
		  g_l('import', '[template]')); */

		$myid = (isset($v["we_TemplateID"])) ? $v["we_TemplateID"] : 0;
		//$path = f('SELECT Path FROM ' . $DB_WE->escape(TEMPLATES_TABLE) . " WHERE ID=" . intval($myid), "Path", $DB_WE);
		$cmd1 = "top.wizbody.document.we_form.elements['noDocTypeTemplateId'].value";

		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_document'," . $cmd1 . ",'" . TEMPLATES_TABLE . "','" . we_base_request::encCmd($cmd1) . "','" . we_base_request::encCmd("top.wizbody.document.we_form.elements['v[we_TemplateName]'].value") . "','" . we_base_request::encCmd("opener.top.we_cmd('reload_editpage');") . "','','','" . we_base_ContentTypes::TEMPLATE . "',1)");

		$yuiSuggest = & weSuggest::getInstance();

		$TPLselect = new we_html_select(array(
			"name" => "docTypeTemplateId",
			"class" => "weSelect",
			"onclick" => "self.document.we_form.elements['v[import_type]'][0].checked=true;",
			"style" => "width: 300px")
		);

		if($v["docType"] != -1){
			$foo = f('SELECT Templates FROM ' . DOC_TYPES_TABLE . ' dt WHERE dt.ID=' . intval($v["docType"]), '', $DB_WE);
			$ids_arr = makeArrayFromCSV($foo);
			$paths_arr = id_to_path($foo, TEMPLATES_TABLE, null, true);


			$optid = 0;
			foreach($ids_arr as $templateID){
				$TPLselect->insertOption($optid, $templateID, $paths_arr[$optid]);
				++$optid;
				if(isset($v["we_TemplateID"]) && $v["we_TemplateID"] == $templateID){
					$TPLselect->selectOption($templateID);
				}
			}
		} else {
			$displayDocType = 'display:none';
			$displayNoDocType = 'display:block';
		}
		$yuiSuggest->setAcId("TmplPath");
		$yuiSuggest->setContentType('folder,' . we_base_ContentTypes::TEMPLATE);
		$yuiSuggest->setInput("v[we_TemplateName]", (isset($v["we_TemplateName"]) ? $v["we_TemplateName"] : ""), array("onFocus" => "self.document.we_form.elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(1);
		$yuiSuggest->setResult('noDocTypeTemplateId', $myid);
		$yuiSuggest->setSelector(weSuggest::DocSelector);
		$yuiSuggest->setTable(TEMPLATES_TABLE);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($button, 10);
		$yuiSuggest->setLabel(g_l('import', '[template]'));

		$templateElement = "<div id='docTypeLayer' style='" . $displayDocType . "'>" . we_html_tools::htmlFormElementTable($TPLselect->getHTML(), g_l('import', '[template]'), "left", "defaultfont") . "</div>
<div id='noDocTypeLayer' style='" . $displayNoDocType . "'>" . $yuiSuggest->getHTML() . "</div>";

		$yuiSuggest->setAcId("DirPath");
		$yuiSuggest->setContentType("folder");
		$yuiSuggest->setInput("v[store_to_path]", (isset($v["store_to_path"]) ? $v["store_to_path"] : "/"), array("onfocus" => "self.document.we_form.elements['v[import_type]'][0].checked=true;"));
		$yuiSuggest->setMaxResults(10);
		$yuiSuggest->setMayBeEmpty(0);
		$yuiSuggest->setResult("v[store_to_id]", (isset($v["store_to_id"]) ? $v["store_to_id"] : 0));
		$yuiSuggest->setSelector(weSuggest::DirSelector);
		$yuiSuggest->setWidth(300);
		$yuiSuggest->setSelectButton($storeToButton, 10);
		$yuiSuggest->setLabel(g_l('import', '[import_dir]'));

		$storeTo = $yuiSuggest->getHTML();

		$seaPu = new we_html_table(array('class' => 'default'), 2, 1);
		$seaPu->setCol(1, 0, [], we_html_forms::checkboxWithHidden(!empty($v["doc_search"]), 'v[doc_search]', g_l('weClass', '[IsSearchable]'), false, 'defaultfont'));
		$seaPu->setCol(0, 0, [], we_html_forms::checkboxWithHidden(isset($v["doc_publish"]) ? $v["doc_publish"] : true, 'v[doc_publish]', g_l('buttons_global', '[publish][value]'), false, 'defaultfont'));

		$docCategories = $this->formCategory2("doc", isset($v["docCategories"]) ? $v["docCategories"] : "");
		$docCats = new we_html_table(array('class' => 'default'), 1, 2);
		$docCats->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', "class" => "defaultfont lowContrast"), g_l('import', '[categories]'));
		$docCats->setCol(0, 1, array('style' => 'width:150px;'), $docCategories);

		$radioDocs = we_html_forms::radiobutton('documents', ($v["import_type"] === 'documents'), "v[import_type]", g_l('import', '[documents]'));
		$radioObjs = we_html_forms::radiobutton('objects', ($v["import_type"] === 'objects'), "v[import_type]", g_l('import', '[objects]'), true, "defaultfont", "self.document.we_form.elements['v[store_to_path]'].value='/'; YAHOO.autocoml.setValidById(self.document.we_form.elements['v[store_to_path]'].id); if(self.document.we_form.elements['v[we_TemplateName]']!==undefined) { self.document.we_form.elements['v[we_TemplateName]'].value=''; YAHOO.autocoml.setValidById(self.document.we_form.elements['v[we_TemplateName]'].id); }", (defined('OBJECT_TABLE') ? false : true));

		$optid = 0;
		if(defined('OBJECT_TABLE')){
			$v["classID"] = isset($v["classID"]) ? $v["classID"] : -1;
			$CLselect = new we_html_select(array(
				'id' => 'classID',
				"name" => "v[classID]",
				"class" => "weSelect",
				"onclick" => "self.document.we_form.elements['v[import_type]'][1].checked=true;",
				'onchange' => "var elem=document.we_form.elements['v[classID]'];document.we_form.elements['v[obj_path]'].value='/'+elem.options[elem.selectedIndex].text;"
				. "document.we_form.elements['v[obj_path_id]'].value=document.we_form.elements['v[classID]'].value.split('_')[1];",
				"style" => "width: 150px")
			);
			$ac = implode(',', we_users_util::getAllowedClasses($DB_WE));
			if($ac){
				$DB_WE->query('SELECT o.ID,o.Text,f.ID AS FID FROM ' . OBJECT_TABLE . ' o LEFT JOIN ' . OBJECT_FILES_TABLE . ' f ON o.Text=f.Text WHERE ' . ($ac ? '  o.ID IN(' . $ac . ') AND ' : '') . ' f.IsFolder=1 AND f.ParentID=0 ORDER BY o.Text');
				while($DB_WE->next_record()){
					if(!$optid){
						$first = '/' . $DB_WE->f("Text");
						$firstID = $DB_WE->f("FID");
					}
					$optid++;
					$CLselect->insertOption($optid, $DB_WE->f("ID") . '_' . $DB_WE->f("FID"), $DB_WE->f("Text"));
					if($DB_WE->f("ID") == $v["classID"]){
						$CLselect->selectOption($DB_WE->f("ID"));
					}
				}
			} else {
				$CLselect->insertOption($optid, -1, g_l('import', '[none]'));
			}

			$objClass = new we_html_table(array('class' => 'default'), 1, 2);
			$objClass->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', "class" => "defaultfont lowContrast"), g_l('import', '[class]'));
			$objClass->setCol(0, 1, array('style' => 'width:150px;'), $CLselect->getHTML());

			$wecmdenc1 = we_base_request::encCmd("top.wizbody.document.we_form.elements['v[obj_path_id]'].value");
			$wecmdenc2 = we_base_request::encCmd("top.wizbody.document.we_form.elements['v[obj_path]'].value");

			$objStoreToButton = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_directory',document.we_form.elements['v[obj_path]'].value,'" . OBJECT_FILES_TABLE . "','" . $wecmdenc1 . "','" . $wecmdenc2 . "','','',document.we_form.elements['v[classID]'].value.split('_')[1])");


			$yuiSuggest->setAcId('ObjPath');
			$yuiSuggest->setContentType("folder");
			$yuiSuggest->setInput("v[obj_path]", (isset($v["obj_path"]) ? $v["obj_path"] : isset($first) ? $first : '/'), array("onfocus" => "self.document.we_form.elements['v[import_type]'][1].checked=true;"));
			$yuiSuggest->setMaxResults(10);
			$yuiSuggest->setMayBeEmpty(0);
			$yuiSuggest->setResult("v[obj_path_id]", (isset($v["obj_path_id"]) ? $v["obj_path_id"] : (isset($firstID) ? $firstID : 0)));
			$yuiSuggest->setSelector(weSuggest::DirSelector);
			$yuiSuggest->setTable(OBJECT_FILES_TABLE);
			$yuiSuggest->setWidth(300);
			$yuiSuggest->setSelectButton($objStoreToButton, 10);
			$yuiSuggest->setLabel(g_l('import', '[import_dir]'));

			$objStoreTo = $yuiSuggest->getHTML();

			$objSeaPu = new we_html_table(array('class' => 'default'), 2, 1);
			$objSeaPu->setCol(1, 0, [], we_html_forms::checkboxWithHidden(!empty($v["obj_search"]), 'v[obj_search]', g_l('weClass', '[IsSearchable]'), false, 'defaultfont'));
			$objSeaPu->setCol(0, 0, [], we_html_forms::checkboxWithHidden(isset($v["obj_publish"]) ? $v["obj_publish"] : true, 'v[obj_publish]', g_l('buttons_global', '[publish][value]'), false, 'defaultfont'));
			$objCategories = $this->formCategory2("obj", isset($v["objCategories"]) ? $v["objCategories"] : "");
			$objCats = new we_html_table(array('class' => 'default'), 1, 2);
			$objCats->setCol(0, 0, array('style' => 'vertical-align:top;width:130px;', "class" => "defaultfont lowContrast"), g_l('import', '[categories]'));
			$objCats->setCol(0, 1, array('style' => 'width:150px;'), $objCategories);

			$objects = new we_html_table(array('class' => 'default withBigSpace'), 3, 2);
			$objects->setCol(0, 0, array("colspan" => 3, 'style' => 'width:50px;'), $radioObjs);
			$objects->setCol(1, 1, [], $objClass->getHTML());
			$objects->setCol(2, 1, [], $objCats->getHTML());
		}

		$specifyDoc = new we_html_table(array('class' => 'default'), 1, 2);
		$specifyDoc->setCol(0, 1, array('style' => 'vertical-align:bottom;'), we_html_forms::checkbox(3, (isset($v["is_dynamic"]) ? $v["is_dynamic"] : 0), "chbxIsDynamic", g_l('import', '[isDynamic]'), true, "defaultfont", "this.form.elements['v[is_dynamic]'].value=this.checked? 1 : 0; switchExt();"));
		$specifyDoc->setCol(0, 0, array('style' => 'padding-right:20px;'), we_html_tools::htmlFormElementTable(we_html_tools::getExtensionPopup("v[we_Extension]", (isset($v["we_Extension"]) ? $v["we_Extension"] : ".html"), we_base_ContentTypes::inst()->getExtension(we_base_ContentTypes::WEDOCUMENT), 100), g_l('import', '[extension]')));

		if((file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]) && is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"]))){
			$parts = array(
				array(
					"headline" => (defined('OBJECT_TABLE')) ? $radioDocs : g_l('import', '[documents]'),
					"html" => weSuggest::getYuiFiles() .
					$doctypeElement .
					$templateElement .
					$storeTo .
					$specifyDoc->getHTML() .
					$seaPu->getHtml() .
					we_html_tools::htmlFormElementTable($docCategories, g_l('import', '[categories]'), "left", "defaultfont") .
					(defined('OBJECT_TABLE') ? '' : $yuiSuggest->getYuiJs()),
					'space' => we_html_multiIconBox::SPACE_MED,
					'noline' => 1
				)
			);
			if(defined('OBJECT_TABLE')){
				$parts[] = array(
					"headline" => $radioObjs,
					"html" => we_html_tools::htmlFormElementTable($CLselect->getHTML(), g_l('import', '[class]'), "left", "defaultfont") .
					$objStoreTo .
					$objSeaPu->getHtml() .
					we_html_tools::htmlFormElementTable($objCategories, g_l('import', '[categories]'), "left", "defaultfont")
					. $yuiSuggest->getYuiJs(),
					'space' => we_html_multiIconBox::SPACE_MED,
					'noline' => 1
				);
			}
		} else {
			if(!file_exists($_SERVER['DOCUMENT_ROOT'] . $v["import_from"])){
				$parts = array(
					array(
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('import', '[file_exists]') . $_SERVER['DOCUMENT_ROOT'] . $v["import_from"], we_html_tools::TYPE_ALERT, 530),
						'noline' => 1));
				$functions.='WE().layout.button.switch_button_state(top.wizbusy.document, "next","disabled");';
			} else if(!is_readable($_SERVER['DOCUMENT_ROOT'] . $v["import_from"])){
				$parts = array(
					array(
						"html" => we_html_tools::htmlAlertAttentionBox(g_l('import', '[file_readable]'), we_html_tools::TYPE_ALERT, 530),
						'noline' => 1));
				$functions.='WE().layout.button.switch_button_state(top.wizbusy.document, "next","disabled");';
			} else {
				$parts = [];
			}
		}


		$content = YAHOO_FILES .
			$hdns .
			we_html_multiIconBox::getHTML('csv', $parts, 30, "", -1, "", "", false, g_l('import', '[csv_import]'));

		return array($functions, $content);
	}

	protected function getCSVImportStep3(){
		$tid = we_base_request::_(we_base_request::INT, 'v', 0, 'we_TemplateID');
		$tname = we_base_request::_(we_base_request::FILE, 'v', '', 'we_TemplateName');
		if($tname && !$tid){
			$_REQUEST["v"]['we_TemplateID'] = path_to_id($tname, TEMPLATES_TABLE, $GLOBALS['DB_WE']);
		}

		$v = we_base_request::_(we_base_request::STRING, 'v');

		$records = we_base_request::_(we_base_request::RAW, 'records', []);
		$we_flds = we_base_request::_(we_base_request::STRING, 'we_flds', []);
		$attrs = we_base_request::_(we_base_request::STRING, 'attrs', []);

		$csvFile = $_SERVER['DOCUMENT_ROOT'] . we_base_request::_(we_base_request::FILE, 'v', '', "import_from");
		if(file_exists($csvFile) && is_readable($csvFile)){
			$data = we_base_file::loadPart($csvFile);
			$encoding = mb_detect_encoding($data, 'UTF-8,ISO-8859-1,ISO-8859-15');
		}

		$hdns = $this->getHdns("v", we_base_request::_(we_base_request::STRING, "v")) .
			($records ? $this->getHdns("records", $records) : "") .
			($we_flds ? $this->getHdns("we_flds", $we_flds) : "") .
			($attrs ? $this->getHdns("attrs", $attrs) : "") .
			we_html_element::htmlHiddens(array(
				"v[startCSVImport]" => we_base_request::_(we_base_request::BOOL, 'v', false, "startCSVImport"),
				"v[cid]" => -2,
				"v[encoding]" => $encoding,
				"v[pfx_fn]" => we_base_request::_(we_base_request::STRING, 'v', 0, "pfx_fn"))) .
			(($tm = we_base_request::_(we_base_request::INT, 'rdo_timestamp')) !== false ? we_html_element::htmlHidden("v[sTimeStamp]", $tm) : '');


		$functions = "
function set_button_state() {
				top.wizbusy.back_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'back', 'enabled');
				top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', " . (we_base_request::_(we_base_request::INT, "mode") != 1 ? "'enabled'" : "'disabled'") . ");
}
function we_submit_form(f, target, url) {
	f.target = target;
	f.action = url;
	f.method = 'post';
	f.submit();
}
function handle_event(evt) {
	var f = self.document.we_form;
	switch(evt) {
	case 'previous':
		f.step.value = 1;
		we_submit_form(f, 'wizbody', '" . $this->path . "');
		break;
	case 'next':
		f.step.value=3;
		f.mode.value=1;
		f.elements['v[mode]'].value=1;
		f.elements['v[startCSVImport]'].value=1;
		top.wizbusy.next_enabled = WE().layout.button.switch_button_state(top.wizbusy.document, 'next', 'disabled');
		we_submit_form(f, 'wizbody', '" . $this->path . "&mode=1');
		break;
	case 'cancel':
		top.close();
		break;
	}
}";

		$db = new DB_WE();

		$records = $dateFields = [];

		if(we_base_request::_(we_base_request::STRING, 'v', '', "import_type") === "documents"){
			$templateCode = f('SELECT c.Dat FROM ' . CONTENT_TABLE . ' c JOIN ' . LINK_TABLE . ' l ON l.CID=c.ID  WHERE l.DocumentTable="' . stripTblPrefix(TEMPLATES_TABLE) . '" AND l.DID=' . we_base_request::_(we_base_request::INT, 'v', 0, 'we_TemplateID') . ' AND l.nHash=x\'' . md5("completeData") . '\'', '', $db);
			$tp = new we_tag_tagParser($templateCode);

			$tags = $tp->getAllTags();

			if($tags){
				$regs = [];
				foreach($tags as $tag){
					if(preg_match('|<we:([^> /]+)|i', $tag, $regs)){
						$tagname = $regs[1];
						if(preg_match('|name="([^"]+)"|i', $tag, $regs) && ($tagname != "var") && ($tagname != "field")){
							$name = $regs[1];
							switch($tagname){
								// tags with text content, links and hrefs
								case "input":
									if(in_array('date', we_tag_tagParser::makeArrayFromAttribs($tag))){
										$dateFields[] = $name;
									}
								case "textarea":
								case "href":
								case "link":
									$records[] = $name;
									break;
							}
						}
					}
				}
				$records = array_unique($records);
			} else {
				$records[] = "Title";
				$records[] = "Description";
				$records[] = "Keywords";
			}
		} else {
			list($class) = explode('_', we_base_request::_(we_base_request::STRING, 'v', 0, "classID"));
			$classFields = self::getClassFields($class);
			foreach($classFields as $classField){
				if(self::isTextField($classField["type"]) || self::isNumericField($classField["type"]) || self::isDateField($classField["type"])){
					$records[] = $classField["name"];
				}
				if(self::isDateField($classField["type"])){
					$dateFields[] = $classField["name"];
				}
			}
		}

		if(file_exists($csvFile) && is_readable($csvFile)){
			switch(we_base_request::_(we_base_request::STRING, 'v', '', 'csv_enclosed')){
				case 'double_quote':
					$encl = '"';
					break;
				case 'single_quote':
					$encl = "'";
					break;
				case 'none':
					$encl = '';
					break;
			}

			$cp = new we_import_CSV;

			$cp->setData($data);
			$cp->setDelim(we_base_request::_(we_base_request::RAW_CHECKED, 'v', '', 'csv_seperator'));
			$cp->setEnclosure($encl);
			$cp->setFromCharset($encoding);
			$cp->parseCSV();
			$num = count($cp->FieldNames);
			$recs = [];
			for($c = 0; $c < $num; $c++){
				$recs[$c] = $cp->CSVFieldName($c);
			}
			$val_nodes = [];
			for($i = 0; $i < count($recs); $i++){
				if(we_base_request::_(we_base_request::BOOL, 'v', false, 'csv_fieldnames') && $recs[$i] != ""){
					$val_nodes[$recs[$i]] = $recs[$i];
				} else {
					$val_nodes['f_' . $i] = g_l('import', '[record_field]') . ($i + 1);
				}
			}
		}

		$th = array(array("dat" => g_l('import', '[we_flds]')), array("dat" => g_l('import', '[rcd_flds]')));
		$rows = [];

		$i = 0;
		foreach($records as $record){
			$hdns .= we_html_element::htmlHidden("records[$i]", $record);
			$sct_we_fields = new we_html_select(array(
				"name" => 'we_flds[' . $record . ']',
				"class" => "weSelect",
				"onclick" => "",
				"style" => "")
			);
			$sct_we_fields->addOption("", g_l('import', '[any]'));
			foreach($val_nodes as $value => $text){
				$b64_value = we_base_request::_(we_base_request::BOOL, 'v', false, "startCSVImport") ? $value : base64_encode($value);
				$sct_we_fields->addOption($b64_value, oldHtmlspecialchars($text));
				if(isset($we_flds[$record])){
					if($value == base64_decode($we_flds[$record])){
						$sct_we_fields->selectOption($b64_value);
					}
				} elseif($value == $record){
					$sct_we_fields->selectOption($b64_value);
				}
			}

			switch($record){
				case "Title":
					$new_record = g_l('import', '[we_title]');
					break;
				case "Description":
					$new_record = g_l('import', '[we_description]');
					break;
				case "Keywords":
					$new_record = g_l('import', '[we_keywords]');
					break;
				default:
					$new_record = '';
			}
			$rows[] = array(
				array("dat" => ($new_record != "") ? $new_record : $record), array("dat" => $sct_we_fields->getHTML()),
			);
			++$i;
		}

		// Associated prefix selector.
		$asocPfx = new we_html_table(array('class' => 'default'), 1, 1);
		$asocPfx->setCol(0, 0, array("class" => "defaultfont"), g_l('import', '[pfx]') . "<br/><br/>" .
			we_html_tools::htmlTextInput("v[asoc_prefix]", 30, (isset($v["asoc_prefix"]) ? $v["asoc_prefix"] : g_l('import', ($v["import_type"] === "documents" ? '[pfx_doc]' : '[pfx_obj]'))), 255, "onclick=\"self.document.we_form.elements['v[rdo_filename]'][0].checked=true;\"", "text", 150));

		// Assigned record or attribute field selectors.
		$rcdPfxSelect = new we_html_select(array(
			"name" => "v[rcd_pfx]",
			"class" => "weSelect",
			"onclick" => "self.document.we_form.elements['v[pfx_fn]'].value=1;self.document.we_form.elements['v[rdo_filename]'][1].checked=true;",
			"style" => "width: 150px")
		);

		foreach($val_nodes as $value => $text){
			$rcdPfxSelect->addOption(oldHtmlspecialchars($value), $text);
			if($value == we_base_request::_(we_base_request::STRING, 'v', '', "rcd_pfx")){
				$rcdPfxSelect->selectOption($value);
			}
		}

		$asgndFld = new we_html_table(array('class' => 'default'), 1, 1);
		$asgndFld->setCol(0, 0, array("class" => "defaultfont"), g_l('import', '[rcd_fld]') . "<br/><br/>" . $rcdPfxSelect->getHTML());

		// Filename selector.
		$fn = new we_html_table(array('class' => 'default'), 5, 1);
		$fn->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton(0, (!isset($v["rdo_filename"]) ? true : ($v["rdo_filename"] == 0) ? true : false), "v[rdo_filename]", g_l('import', '[auto]'), true, "defaultfont", "self.document.we_form.elements['v[pfx_fn]'].value=0;"));
		$fn->setCol(1, 0, array('style' => 'padding-left:25px;'), $asocPfx->getHTML());
		$fn->setCol(2, 0, array("height" => 5), "");
		$fn->setCol(3, 0, array("colspan" => 2), we_html_forms::radiobutton(1, (!isset($v["rdo_filename"]) ? false : ($v["rdo_filename"] == 1) ? true : false), "v[rdo_filename]", g_l('import', '[asgnd]'), true, "defaultfont", "self.document.we_form.elements['v[pfx_fn]'].value=1;"));
		$fn->setCol(4, 0, array('style' => 'padding-left:25px;'), $asgndFld->getHTML());

		$parts = array(
			array(
				"html" => "<br/>" . we_html_tools::htmlDialogBorder3(510, $rows, $th, "defaultfont"),
			)
		);


		if(!empty($dateFields)){
			// Timestamp
			$tStamp = new we_html_table(array('class' => 'default withSpace'), 4, 1);
			$tStamp->setCol(0, 0, array("colspan" => 2), we_html_forms::radiobutton("Unix", (!isset($v["rdo_timestamp"]) ? 1 : ($v["rdo_timestamp"] === "Unix") ? 1 : 0), "v[rdo_timestamp]", g_l('import', '[uts]'), true, "defaultfont", "", 0, g_l('import', '[unix_timestamp]'), 0, 384));
			$tStamp->setCol(1, 0, array("colspan" => 2), we_html_forms::radiobutton("GMT", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] === "GMT") ? 1 : 0), "v[rdo_timestamp]", g_l('import', '[gts]'), true, "defaultfont", "", 0, g_l('import', '[gmt_timestamp]'), 0, 384));
			$tStamp->setCol(2, 0, array("colspan" => 2), we_html_forms::radiobutton("Format", (!isset($v["rdo_timestamp"]) ? 0 : ($v["rdo_timestamp"] === "Format") ? 1 : 0), "v[rdo_timestamp]", g_l('import', '[fts]'), true, "defaultfont", "", 0, g_l('import', '[format_timestamp]'), 0, 384));
			$tStamp->setCol(3, 0, array('style' => 'padding-left:25px;'), we_html_tools::htmlTextInput("v[timestamp]", 30, (isset($v["timestamp"]) ? $v["timestamp"] : ""), "", "onclick=\"self.document.we_form.elements['v[rdo_timestamp]'][2].checked=true;\"", "text", 150));

			$parts[] = array(
				"headline" => g_l('import', '[format_date]'),
				"html" => $tStamp->getHTML(),
				'space' => we_html_multiIconBox::SPACE_MED2
			);
			if(!isset($v["dateFields"])){
				$hdns .= we_html_element::htmlHidden("v[dateFields]", implode(',', $dateFields));
			}
		}

		$conflict = isset($v["collision"]) ? $v["collision"] : 'rename';
		$fn_colsn = new we_html_table(array('class' => 'default withSpace'), 3, 1);
		$fn_colsn->setCol(0, 0, [], we_html_forms::radiobutton("rename", $conflict === "rename", "nameconflict", g_l('import', '[rename]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='rename';"));
		$fn_colsn->setCol(1, 0, [], we_html_forms::radiobutton("replace", $conflict === "replace", "nameconflict", g_l('import', '[replace]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='replace';"));
		$fn_colsn->setCol(2, 0, [], we_html_forms::radiobutton("skip", $conflict === "skip", "nameconflict", g_l('import', '[skip]'), true, 'defaultfont', "self.document.we_form.elements['v[collision]'].value='skip';"));

		$parts[] = array(
			'headline' => g_l('import', '[name_collision]'),
			'html' => $fn_colsn->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED2
		);

		$parts[] = array(
			'headline' => g_l('import', '[name]'),
			'html' => $fn->getHTML(),
			'space' => we_html_multiIconBox::SPACE_MED2
		);

		$wepos = weGetCookieVariable('but_csv');
		$znr = -1;

		$content = $hdns .
			we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML('csv', $parts, 30, '', $znr, g_l('weClass', '[moreProps]'), g_l('weClass', '[lessProps]'), ($wepos === 'down'), g_l('import', '[assign_record_fields]'));

		return array($functions, $content);
	}

	private function formWeChooser($table = FILE_TABLE, $width = '', $rootDirID = 0, $IDName = 'ID', $IDValue = 0, $Pathname = 'Path', $Pathvalue = '/', $cmd = ''){
		$Pathvalue = (empty($Pathvalue) ? f('SELECT Path FROM ' . escape_sql_query($table) . ' WHERE ID=' . intval($IDValue), '', new DB_WE()) : $Pathvalue);
		$button = we_html_button::create_button(we_html_button::SELECT, "javascript:we_cmd('we_selector_file',document.we_form.elements['" . $IDName . "'].value,'" . $table . "','document.we_form.elements[\\'" . $IDName . "\\'].value','document.we_form.elements[\\'" . $Pathname . "\\'].value','" . $cmd . "','','" . $rootDirID . "')");
		return we_html_tools::htmlFormElementTable(we_html_tools::htmlTextInput($Pathname, 30, $Pathvalue, '', 'readonly', 'text', $width, 0), '', 'left', 'defaultfont', we_html_element::htmlHidden($IDName, $IDValue), $button);
	}

	public static function getFrameset(){
		$wizard = new self();

		$what = we_base_request::_(we_base_request::STRING, "pnt", 'wizframeset');
		$type = we_base_request::_(we_base_request::STRING, "type", '');
		$step = we_base_request::_(we_base_request::INT, "step", 0);
		$mode = we_base_request::_(we_base_request::INT, "mode", 0);

		if($type && ($step == 1 || $step == 2) && $what === 'wizbody'){
			$acceptedMime = $acceptedExt = [];
			switch($type){
				case we_import_functions::TYPE_GENERIC_XML:
					$name = 'uploaded_xml_file';
					$acceptedMime = array('text/xml');
					$acceptedExt = array('.xml');
					$genericFileNameTemp = TEMP_DIR . 'we_xml_' . we_fileupload::REPLACE_BY_UNIQUEID . '.xml';
					break;
				case we_import_functions::TYPE_WE_XML:
					$name = 'uploaded_xml_file';
					$acceptedMime = array('text/xml');
					$acceptedExt = array('.xml');
					$genericFileNameTemp = TEMP_DIR . we_fileupload::REPLACE_BY_UNIQUEID . '_w.xml';
					break;
				case we_import_functions::TYPE_CSV:
					$name = 'uploaded_csv_file';
					$acceptedExt = array('.csv', '.txt');
					$genericFileNameTemp = TEMP_DIR . 'we_csv_' . we_fileupload::REPLACE_BY_UNIQUEID . '.csv';
					break;
				default:
					break;
			}

			switch($step){
				case 2:
					$wizard->fileUploader = new we_fileupload_resp_base();
					break;
				default:
					$wizard->fileUploader = new we_fileupload_ui_base($name);
					$wizard->fileUploader->setCallback('top.wizbody.handle_eventNext()');
					$wizard->fileUploader->setExternalUiElements(array('contentName' => 'wizbody', 'btnUploadName' => 'next_btn'));
					$wizard->fileUploader->setFileSelectOnclick("self.document.we_form.elements['v[rdofloc]'][1].checked=true;");
					$wizard->fileUploader->setInternalProgress(array('isInternalProgress' => true, 'width' => 200));
					$wizard->fileUploader->setGenericFileName($genericFileNameTemp);
					$wizard->fileUploader->setDimensions(array('width' => 410, 'marginTop' => 12));
			}
			$wizard->fileUploader->setTypeCondition('accepted', $acceptedMime, $acceptedExt);
		}

		echo $wizard->getHTML($what, $type, $step, $mode);
	}

}
