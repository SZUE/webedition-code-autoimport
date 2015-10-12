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

class we_navigation_view extends we_modules_view{
	var $navigation;
	var $editorBodyFrame;
	var $editorBodyForm;
	var $editorHeaderFrame;
	var $editorFooterFrame;
	var $icon_pattern = '';
	var $page = 1;
	var $Model;

	public function __construct($frameset = '', $topframe = 'top'){
		parent::__construct($frameset, $topframe);
		$this->Model = new we_navigation_navigation();
	}

	function setTopFrame($frame){
		parent::setTopFrame($frame);
		$this->editorBodyFrame = $frame . '.editor.edbody';
		$this->editorBodyForm = $this->editorBodyFrame . '.document.we_form';
		$this->editorHeaderFrame = $frame . '.editor.edheader';
		$this->editorFooterFrame = $frame . '.editor.edfooter';
	}

	function getCommonHiddens($cmds = array()){
		return
			parent::getCommonHiddens($cmds) .
			we_html_element::htmlHiddens(array(
				'vernr' => (isset($cmds['vernr']) ? $cmds['vernr'] : 0),
				'delayCmd' => (isset($cmds['delayCmd']) ? $cmds['delayCmd'] : ''),
				'delayParam' => (isset($cmds['delayParam']) ? $cmds['delayParam'] : '')
		));
	}

	function getJSTop(){
		return
			parent::getJSTop() .
			we_html_element::jsElement('
var activ_tab = "1";
var hot = 0;
var makeNewDoc = false;

function we_cmd() {
	var args = [];
	var url = WE().consts.dirs.WEBEDITION_DIR +"we_cmd.php?";
		for(var i = 0; i < arguments.length; i++){
						args.push(arguments[i]);

		url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
		if(i < (arguments.length - 1)){ url += "&"; }
		}
	if(top.content.hot && (args[0]=="module_navigation_edit" || args[0]=="module_navigation_new" || args[0]=="module_navigation_new_group" || args[0]=="exit_navigation")){
		top.content.editor.edbody.document.getElementsByName("delayCmd")[0].value = args[0];
		top.content.editor.edbody.document.getElementsByName("delayParam")[0].value = args[1];
		args[0] = "exit_doc_question";
	}
	switch (args[0]) {
		case "module_navigation_edit":
			if(top.content.editor.edbody.loaded) {
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.cmdid.value=args[1];
				top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
				top.content.editor.edbody.document.we_form.pnt.value="edbody";
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(\'we_cmd("module_navigation_edit",\'+args[1]+\');\', 10);
			}
		break;
		case "module_navigation_new":
		case "module_navigation_new_group":
			if(top.content.editor.edbody.loaded) {
				top.content.hot = 0;
				if(top.content.editor.edbody.document.we_form.presetFolder !== undefined) top.content.editor.edbody.document.we_form.presetFolder.value = false;
				top.content.editor.edbody.document.we_form.cmd.value = args[0];
				top.content.editor.edbody.document.we_form.pnt.value="edbody";
				top.content.editor.edbody.document.we_form.tabnr.value = 1;
				top.content.editor.edbody.submitForm();
			} else {
				setTimeout(\'we_cmd("\' + args[0] + \'");\', 10);
			}
			if((window.treeData!==undefined) && treeData){
				treeData.unselectnode();
			}
		break;
		case "module_navigation_save":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			if (top.content.editor.edbody.loaded) {
					if(top.content.editor.edbody.document.we_form.presetFolder) top.content.editor.edbody.document.we_form.presetFolder.value = makeNewDoc;
					var cont = true;
					if(top.content.editor.edbody.document.we_form.Selection!==undefined) {
						if(top.content.editor.edbody.document.we_form.Selection.options[top.content.editor.edbody.document.we_form.Selection.selectedIndex].value=="' . we_navigation_navigation::SELECTION_DYNAMIC . '" && top.content.editor.edbody.document.we_form.IsFolder.value=="1"){
							cont = confirm("' . g_l('navigation', '[save_populate_question]') . '");
						}
					}
					if(cont){
						top.content.editor.edbody.document.we_form.cmd.value=args[0];
						top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
						top.content.editor.edbody.document.we_form.pnt.value="edbody";
						top.content.editor.edbody.submitForm();
					}
			} else {
				' . we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_to_save]'), we_message_reporting::WE_MESSAGE_ERROR) . '
			}
			break;
		case "populate":
		case "depopulate":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home") return;
			if (top.content.editor.edbody.loaded) {
					if(args[0]=="populate") {
						q="' . g_l('navigation', '[populate_question]') . '";
					} else {
						q="' . g_l('navigation', '[depopulate_question]') . '";
					}
					if(confirm(q)){
						top.content.editor.edbody.document.we_form.pnt.value="edbody";
						top.content.editor.edbody.document.we_form.cmd.value=args[0];
						top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
						if(top.content.editor.edbody.document.we_form.pnt.value=="previewIframe") {
						top.content.editor.edbody.document.we_form.pnt.value="preview";
						}

						top.content.editor.edbody.submitForm();
					}
			}
		break;
		case "module_navigation_delete":
			if(top.content.editor.edbody.document.we_form.cmd.value=="home"){
				' . we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_selected]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			}
			if(top.content.editor.edbody.document.we_form.newone){
				if(top.content.editor.edbody.document.we_form.newone.value==1){
				' . we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
				return;
			} }
			' . (!permissionhandler::hasPerm('DELETE_NAVIGATION') ?
					(
					we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					) :
					('
					if (top.content.editor.edbody.loaded) {
						if (confirm("' . g_l('navigation', '[delete_alert]') . '")) {
							top.content.editor.edbody.document.we_form.cmd.value=args[0];
							top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
							top.content.editor.edheader.location="' . $this->frameset . '?home=1&pnt=edheader";
							top.content.editor.edfooter.location="' . $this->frameset . '?home=1&pnt=edfooter";
							top.content.editor.edbody.submitForm();
						}
					} else {
						' . we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR) . '
					}

			')) . '
		break;
		case "move_abs":
			top.content.cmd.location="' . $this->frameset . '?pnt=cmd&cmd="+args[0]+"&pos="+args[1];
		break;
		case "move_up":
		case "move_down":
			top.content.cmd.location="' . $this->frameset . '?pnt=cmd&cmd="+args[0];
		break;
		case "dyn_preview":
		case "create_template":
		case "populateWorkspaces":
		case "populateFolderWs":
		case "populateText":
			top.content.editor.edbody.document.we_form.cmd.value=args[0];
			top.content.editor.edbody.document.we_form.tabnr.value=top.content.activ_tab;
			top.content.editor.edbody.document.we_form.pnt.value="cmd";
			top.content.editor.edbody.submitForm("cmd");
		break;
		case "del_mode":
			top.content.treeData.setstate(treeData.tree_states["select"]);
			top.content.treeData.unselectnode();
			top.content.drawTree();
		case "move_mode":
			top.content.treeData.setstate(treeData.tree_states["selectitem"]);
			top.content.treeData.unselectnode();
			top.content.drawTree();
		break;
		case "exit_navigation":
		if (hot != 1) {
			top.opener.top.we_cmd("exit_modules");
			}
		break;
		case "exit_doc_question":
			url = "' . $this->frameset . '?pnt=exit_doc_question&delayCmd="+top.content.editor.edbody.document.getElementsByName("delayCmd")[0].value+"&delayParam="+top.content.editor.edbody.document.getElementsByName("delayParam")[0].value;
			new (WE().util.jsWindow)(top.window, url,"we_exit_doc_question",-1,-1,380,130,true,false,true);
		break;

		case "module_navigation_reset_customer_filter":
			if(confirm("' . g_l('navigation', '[reset_customerfilter_question]') . '")) {
				we_cmd("module_navigation_do_reset_customer_filter");
			}
		break;
		default:
			top.opener.top.we_cmd.apply(this, args);

	}
}

function mark() {
	hot=1;
	top.content.editor.edheader.mark();
}');
	}

	function getJSProperty(){
		$out = parent::getJSProperty();
		$_objFields = "\n";
		if($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS){
			if(defined('OBJECT_TABLE')){

				$_class = new we_object();
				$_class->initByID($this->Model->ClassID, OBJECT_TABLE);
				$_fields = $_class->getAllVariantFields();

				foreach($_fields as $_key => $val){
					$_objFields .= "\t\t\t" . 'weNavTitleField["' . substr($_key, strpos($_key, "_") + 1) . '"] = "' . $_key . '";' . "\n";
				}
			}
		}
		$js = '
var loaded=0;
function we_cmd() {
	var args = "";
	var url = WE().consts.dirs.WEBEDITION_DIR+"we_cmd.php?"; for(var i = 0; i < arguments.length; i++){ url += "we_cmd["+i+"]="+encodeURI(arguments[i]); if(i < (arguments.length - 1)){ url += "&"; }}
	switch (arguments[0]) {
		case "we_selector_image":
		case "we_selector_document":
			new (WE().util.jsWindow)(top.window, url,"we_docselector",-1,-1,WE().consts.size.docSelect.width,WE().consts.size.docSelect.height,true,true,true,true);
			break;
		case "we_selector_file":
			new (WE().util.jsWindow)(top.window, url,"we_selector",-1,-1,WE().consts.size.windowSelect.width,WE().consts.size.windowSelect.height,true,true,true,true);
			break;
		case "we_selector_directory":
			new (WE().util.jsWindow)(top.window, url,"we_selector",-1,-1,WE().consts.size.windowDirSelect.width,WE().consts.size.windowDirSelect.height,true,true,true,true);
			break;
		case "we_selector_category":
			new (WE().util.jsWindow)(top.window, url,"we_catselector",-1,-1,WE().consts.size.catSelect.width,WE().consts.size.catSelect.height,true,true,true,true);
			break;
		case "openNavigationDirselector":
			url = "' . WEBEDITION_DIR . 'we_cmd.php?we_cmd[0]=we_navigation_dirSelector&";
			for(var i = 1; i < arguments.length; i++){
				url += "we_cmd["+i+"]="+encodeURI(arguments[i]);
				if(i < (arguments.length - 1)){ url += "&"; }
			}
			new (WE().util.jsWindow)(top.window, url,"we_navigation_dirselector",-1,-1,600,400,true,true,true);
			break;
		case "openFieldSelector":
			url = "' . WE_INCLUDES_DIR . 'we_modules/navigation/edit_navigation_frameset.php?pnt=fields&cmd="+arguments[1]+"&type="+arguments[2]+"&selection="+arguments[3]+"&multi="+arguments[4];
			new (WE().util.jsWindow)(top.window, url,"we_navigation_field_selector",-1,-1,380,350,true,true,true);
			break;
		case "copyNaviFolder":
			folderPath = document.we_form.CopyFolderPath.value;
			folderID   = document.we_form.CopyFolderID.value;
			setTimeout(function(){copyNaviFolder(folderPath, folderID);},100);
			break;
		case "rebuildNavi":
			//new (WE().util.jsWindow)(top.window, \'' . WE_INCLUDES_PATH . 'we_cmd.php?we_cmd[0]=rebuild&step=2&type=rebuild_navigation&responseText=\',\'resave\',-1,-1,600,130,0,true);
			break;
		default:
					var args = [];
			for (var i = 0; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			top.content.we_cmd.apply(this, args);

	}
}

var copyNaviFolderUrl = "' . WEBEDITION_DIR . 'rpc/rpc.php";
function copyNaviFolder(folderPath,folderID) {
	var parentPos = selfNaviPath.indexOf(folderPath);
	if(parentPos==(-1) || selfNaviPath.indexOf(folderPath)>0) {
		cnfUrl = copyNaviFolderUrl+"?protocol=text&cmd=CopyNavigationFolder&cns=navigation&we_cmd[0]="+selfNaviPath+"&we_cmd[1]="+selfNaviId+"&we_cmd[2]="+folderPath+"&we_cmd[3]="+folderID;
		YAHOO.util.Connect.asyncRequest("GET", cnfUrl, copyNaviFolderAjaxCallback);
	} else {
		' . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}

var copyNaviFolderAjaxCallback = {
	success: function(o) {
		if(o.responseText != "") {
			' . we_message_reporting::getShowMessageCall(g_l('copyFolder', '[copy_success]'), we_message_reporting::WE_MESSAGE_NOTICE) . '
			//FIXME: add code for Tree reload!
			top.content.cmd.location.reload();
		} else {
			' . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
		}
	},
	failure: function(o) {
		' . we_message_reporting::getShowMessageCall(g_l('alert', '[copy_folder_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) . '
	}
}

function submitForm() {
	var f = self.document.we_form;
	populateVars();

	f.target =  (arguments[0]?arguments[0]:"edbody");
	f.action = (arguments[1]?arguments[1]:"' . $this->frameset . '");
	f.method = (arguments[2]?arguments[2]:"post");
	f.submit();
}

var table = "' . FILE_TABLE . '";
var log_counter=0;

function clearFields(){
	top.content.mark();
	var st = document.we_form.SelectionType;
	if(st.selectedIndex>-1){
		removeAllCats();
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "enabled");
		WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "enabled");
		if(st.options[st.selectedIndex].value=="' . we_navigation_navigation::STPYE_CLASS . '" && document.we_form.ClassID.options.length<1){
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "disabled");
			document.getElementById("yuiAcInputFolderPath").disabled=true;
		} else {
			WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_XFolder", "enabled");
			document.getElementById("yuiAcInputFolderPath").disabled=false;
		}
		if(st.options[st.selectedIndex].value=="' . we_navigation_navigation::STPYE_DOCTYPE . '"){
			setVisible("docFolder",true);
			setVisible("objFolder",false);
			setVisible("catFolder",false);
			if(' . $this->editorBodyForm . '.DocTypeID.options[' . $this->editorBodyForm . '.DocTypeID.selectedIndex].value==0){
				WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_TitleField", "disabled");
				WE().layout.button.switch_button_state(top.content.editor.edbody.document, "select_SortField", "disabled");
			}
		}
		if(st.options[st.selectedIndex].value=="' . we_navigation_navigation::STPYE_CLASS . '"){
			setVisible("docFolder",false);
			setVisible("objFolder",true);
			setVisible("catFolder",false);
		}
		' .
			(!$this->Model->IsFolder ? '
		document.we_form.LinkID.value="";
		document.we_form.LinkPath.value="";
		' : '') . '

		document.we_form.FolderID.value=0;
		document.we_form.FolderPath.value="/";
		document.we_form.TitleField.value="";
		document.we_form.__TitleField.value="";
		document.we_form.SortField.value="";
		document.we_form.__SortField.value="";
		weInputRemoveClass(document.we_form.__TitleField, "weMarkInputError");
		weInputRemoveClass(document.we_form.__SortField, "weMarkInputError");
		document.we_form.dynamic_Parameter.value="";
		if(document.we_form.IsFolder.value==0) {
			document.we_form.Parameter.value="";
			document.we_form.Url.value="http://";
		}

		if(st.options[st.selectedIndex].value=="' . we_navigation_navigation::STPYE_CATEGORY . '"){
			setVisible("docFolder",false);
			setVisible("objFolder",false);
			setVisible("catFolder",true);
			setVisible("fieldChooser",false);
			setVisible("catSort",false);
		} else {
			setVisible("fieldChooser",true);
			setVisible("catSort",true);
		}
	}
}

function setCustomerFilter(sel) {
' . ($this->Model->IsFolder ? '' : '
	var st = document.we_form.SelectionType;
	if (sel.options[sel.selectedIndex].value == "dynamic") {
	try{//FIXME
		document.we_form.elements._wecf_useDocumentFilter.checked = false;
		document.we_form.elements.wecf_useDocumentFilter.value = 0;
		document.we_form.elements._wecf_useDocumentFilter.disabled = true;
		document.getElementById("label__wecf_useDocumentFilter").style.color = "grey";
		}catch(e){
		}
		document.getElementById("MainFilterDiv").style.display = "block";
	} else {
	try{//FIXME
		document.we_form.elements._wecf_useDocumentFilter.disabled = false;
		document.getElementById("label__wecf_useDocumentFilter").style.color = "";
		}catch(e){
		}
	}') . '
}

function setPresentation(type) {
	top.content.mark();
	var st = document.we_form.SelectionType;
	st.options.length = 0;
	if(type=="' . we_navigation_navigation::SELECTION_DYNAMIC . '"){
		st.options[st.options.length] = new Option("' . g_l('navigation', '[documents]') . '","' . we_navigation_navigation::STPYE_DOCTYPE . '");
		' . (defined('OBJECT_TABLE') ? '
		st.options[st.options.length] = new Option("' . g_l('navigation', '[objects]') . '","' . we_navigation_navigation::STPYE_CLASS . '");
		' : '' ) . '
		st.options[st.options.length] = new Option("' . g_l('navigation', '[categories]') . '","' . we_navigation_navigation::STPYE_CATEGORY . '");
		setVisible("doctype",true);
		setVisible("classname",false);
		setVisible("docFolder",true);
		setVisible("objFolder",false);
		setVisible("catFolder",false);
		setStaticSelection("document");
	} else {
		st.options[st.options.length] = new Option("' . g_l('navigation', '[docLink]') . '","' . we_navigation_navigation::STPYE_DOCLINK . '");
		st.options[st.options.length] = new Option("' . g_l('navigation', '[urlLink]') . '","' . we_navigation_navigation::STYPE_URLLINK . '");
		' . (defined('OBJECT_TABLE') ? '
		st.options[st.options.length] = new Option("' . g_l('navigation', '[objLink]') . '","' . we_navigation_navigation::STPYE_OBJLINK . '");
		' : '' ) . '
		st.options[st.options.length] = new Option("' . g_l('navigation', '[catLink]') . '","' . we_navigation_navigation::STPYE_CATLINK . '");
		setVisible("classname",true);
		setVisible("doctype",false);
		setVisible("docFolder",false);
		setVisible("objFolder",true);
		setVisible("catFolder",true);
		setVisible("docLink",true);
		setStaticSelection("docLink");
	}
	clearFields();
}

function closeAllSelection(){
	setVisible("' . we_navigation_navigation::SELECTION_DYNAMIC . '",false);
	setVisible("' . we_navigation_navigation::SELECTION_STATIC . '",false);
}

function closeAllType(){
	setVisible("doctype",false);
	' . (defined('OBJECT_TABLE') ? '
	setVisible("classname",false);
	' : '') . '
}

function closeAllStats(){
	setVisible("docLink",false);
	' . (defined('OBJECT_TABLE') ? '
	setVisible("objLink",false);
	setVisible("objLinkWorkspace",false);
	' : '') . '
	setVisible("catLink",false);
	document.we_form.LinkID.value = "";
	document.we_form.LinkPath.value = "";
}

function putTitleField(field){
	top.content.mark();
	document.we_form.TitleField.value=field;
	document.we_form.__TitleField.value = document.we_form.SelectionType.value === "doctype" ? field : field.substring(field.indexOf("_")+1,field.length);
	weInputRemoveClass(document.we_form.__TitleField, "weMarkInputError");
}

function putSortField(field){
	top.content.mark();
	document.we_form.SortField.value = field;
	document.we_form.__SortField.value = document.we_form.SelectionType.value === "doctype" ? field : field.substring(field.indexOf("_")+1,field.length);
	weInputRemoveClass(document.we_form.__SortField, "weMarkInputError");
}

function setFocus() {
	if(document.we_form.Text!==undefined && top.content.activ_tab==1){
		document.we_form.Text.focus();
	}
}

function setWorkspaces(value) {
	setVisible("objLinkWorkspaceClass",false);
	setVisible("objLinkWorkspace",false);
	if(value=="' . we_navigation_navigation::STPYE_CLASS . '"){
		setVisible("objLinkWorkspaceClass",true);
	}
	if(value=="' . we_navigation_navigation::STPYE_OBJLINK . '"){
		setVisible("objLinkWorkspace",true);
	}
}

function setStaticSelection(value){
	if(value=="' . we_navigation_navigation::STPYE_CATEGORY . '"){
		setVisible("dynUrl",true);
		setVisible("dynamic_LinkSelectionDiv",true);
		dynamic_setLinkSelection("' . we_navigation_navigation::LSELECTION_INTERN . '");
	} else {

		setVisible("dynUrl",false);

		if(value=="' . we_navigation_navigation::STPYE_CATLINK . '"){
			setVisible("staticSelect",true);
			setVisible("staticUrl",true);
		} else if(value=="' . we_navigation_navigation::STYPE_URLLINK . '" || value=="' . we_navigation_navigation::STPYE_CATLINK . '"){
			setVisible("staticSelect",false);
			setVisible("staticUrl",true);
		} else {
			setVisible("staticSelect",true);
			setVisible("staticUrl",false);
		}

		if(value=="' . we_navigation_navigation::STPYE_DOCLINK . '" || value=="' . we_navigation_navigation::STPYE_OBJLINK . '" || value=="' . we_navigation_navigation::STPYE_CATLINK . '"){
			setVisible("docLink",false);
			setVisible("objLink",false);
			setVisible("catLink",false);
			setVisible(value,true);
		}

		if(value=="' . we_navigation_navigation::STYPE_URLLINK . '") {
			setVisible("LinkSelectionDiv",false);
			setLinkSelection("' . we_navigation_navigation::LSELECTION_EXTERN . '");
		} else if(value=="' . we_navigation_navigation::STPYE_CATLINK . '") {
			setVisible("LinkSelectionDiv",true);
			setLinkSelection("' . we_navigation_navigation::LSELECTION_INTERN . '");
		}
	}
}

function setFolderSelection(value){
		document.we_form.LinkID.value = "";
		document.we_form.LinkPath.value = "";
		document.we_form.FolderUrl.value = "http://";
		document.we_form.FolderWsID.value = -1;
		if(value=="' . we_navigation_navigation::STYPE_URLLINK . '"){
			setVisible("folderSelectionDiv",false);
			setVisible("docFolderLink",false);
			setVisible("objFolderLink",false);
			setVisible("objLinkFolderWorkspace",false);
			setVisible("folderUrlDiv",true);
		}else if(value=="' . we_navigation_navigation::STPYE_DOCLINK . '"){
			setVisible("folderSelectionDiv",true);
			setVisible("docFolderLink",true);
			setVisible("objFolderLink",false);
			setVisible("objLinkFolderWorkspace",false);
			setVisible("folderUrlDiv",false);

		} else {
			setVisible("folderSelectionDiv",true);
			setVisible("docFolderLink",false);
			setVisible("objFolderLink",true);
			setVisible("objLinkFolderWorkspace",true);
			setVisible("folderUrlDiv",false);
		}
}
var weNavTitleField = [];
' . $_objFields;

		return $out . we_html_element::jsElement($js) . we_html_element::jsScript(WE_JS_MODULES_DIR . 'navigation/navigation_view.js');
	}

	function getJSSubmitFunction(){
		return '';
	}

	function getEditNaviPosition(){
		$this->db->query('SELECT Ordn,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . $this->Model->ParentID . ' ORDER BY Ordn');
		$values = $this->db->getAllFirst(false);
		$values[-1] = g_l('navigation', '[end]');
		return $values;
	}

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'module_navigation_new':
			case 'module_navigation_new_group':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}
				$this->Model = new we_navigation_navigation();
				$this->Model->IsFolder = we_base_request::_(we_base_request::STRING, 'cmd') === 'module_navigation_new_group' ? 1 : 0;
				$this->Model->ParentID = we_base_request::_(we_base_request::INT, 'ParentID', 0);
				echo we_html_element::jsElement(
					'top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Model->Text) . '";
					top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";');
				break;
			case 'module_navigation_edit':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					break;
				}

				$this->Model = new we_navigation_navigation(we_base_request::_(we_base_request::INT, 'cmdid'));

				if(!$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = true;
					break;
				}
				echo we_html_element::jsElement(
					'top.content.editor.edheader.location="' . $this->frameset . '?pnt=edheader&text=' . urlencode($this->Model->Text) . '";
					top.content.editor.edfooter.location="' . $this->frameset . '?pnt=edfooter";
								if(top.content.treeData){
									top.content.treeData.unselectnode();
									top.content.treeData.selectnode(' . $this->Model->ID . ');
								}');
				break;
			case 'module_navigation_save':
				if(!permissionhandler::hasPerm('EDIT_NAVIGATION') && !permissionhandler::hasPerm('EDIT_NAVIGATION')){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$js = '';
				if($this->Model->filenameNotValid($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongtext]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if(!trim($this->Model->Text)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[name_empty]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				$oldpath = $this->Model->Path;
				// set the path and check it
				$this->Model->setPath();
				if($this->Model->pathExists($this->Model->Path)){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[name_exists]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->isSelf() || !$this->Model->isAllowedForUser()){
					echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[path_nok]'), we_message_reporting::WE_MESSAGE_ERROR));
					break;
				}

				if($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS && $this->Model->TitleField != ""){
					$_classFields = we_unserialize(f('SELECT DefaultValues FROM ' . OBJECT_TABLE . " WHERE ID=" . intval($this->Model->ClassID), "DefaultValues", $this->db));
					if(is_array($_classFields) && count($_classFields) > 0){
						$_fieldsByNamePart = array();
						foreach(array_keys($_classFields) as $_key){
							if(($_pos = strpos($_key, "_")) && (substr($_key, 0, $_pos) != "object")){
								$_fieldsByNamePart[substr($_key, $_pos + 1)] = $_key;
							}
						}
						if(!key_exists($this->Model->TitleField, $_fieldsByNamePart) && !key_exists($this->Model->TitleField, $_classFields)){
							echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR));
							break;
						}
					} else {
						echo we_html_element::jsElement(we_message_reporting::getShowMessageCall(g_l('navigation', '[wrongTitleField]'), we_message_reporting::WE_MESSAGE_ERROR));
						break;
					}
				}

				$js = '';

				$newone = $this->Model->ID == 0;

				$_dynamic = '';
				if($this->Model->ID && $this->Model->IsFolder){
					$_dynamic = f('SELECT Selection FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'Selection', $this->Model->db);
				}

				$this->Model->save();

				if($this->Model->IsFolder && $oldpath != '' && $oldpath != '/' && $oldpath != $this->Model->Path){
					$db_tmp = new DB_WE();
					$this->db->query('SELECT ID FROM ' . NAVIGATION_TABLE . ' WHERE Path LIKE \'' . $this->db->escape($oldpath) . '%\' AND ID!=' . intval($this->Model->ID));
					while($this->db->next_record()){
						$db_tmp->query('UPDATE ' . NAVIGATION_TABLE . ' SET Path="' . $this->db->escape($this->Model->evalPath($this->db->f("ID"))) . '" WHERE ID=' . intval($this->db->f("ID")));
					}
				}

				$js = ($newone ?
						'top.content.makeNewEntry({id:\'' . $this->Model->ID . '\',parentid:\'' . $this->Model->ParentID . '\',text:\'' . addslashes($this->Model->Text) . '\',open:0,contenttype:\'' . ($this->Model->IsFolder ? 'folder' : 'we/navigation') . '\',table:\'' . NAVIGATION_TABLE . '\',published:0,order:' . $this->Model->Ordn . '});' :
						'top.content.updateEntry({id:' . $this->Model->ID . ',text:\'' . addslashes($this->Model->Text) . '\',parentid:' . $this->Model->ParentID . ',order:\'' . $this->Model->Depended . '\',tooltip:' . $this->Model->ID . '});');

				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$_old_items = array();
					if($this->Model->hasDynChilds()){
						$_old_items = $this->Model->depopulateGroup();
						foreach($_old_items as $_id){
							$js .= 'top.content.deleteEntry(' . $_id['ID'] . ');';
						}
					}
					$_items = $this->Model->populateGroup($_old_items);
					foreach($_items as $_k => $_item){
						$js .= 'top.content.makeNewEntry({id:\'' . $_item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($_item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $_k . '});';
					}
				}
				if($this->Model->IsFolder && $this->Model->Selection == we_navigation_navigation::SELECTION_NODYNAMIC){
					$_old_items = array();
					if($this->Model->hasDynChilds()){
						$_old_items = $this->Model->depopulateGroup();
						foreach($_old_items as $_id){
							$js .= 'top.content.deleteEntry(' . $_id['ID'] . ');';
						}
					}
				}


				$js = we_html_element::jsElement($js . 'top.content.editor.edheader.location.reload();' .
						we_message_reporting::getShowMessageCall(g_l('navigation', ($this->Model->IsFolder == 1 ? '[save_group_ok]' : '[save_ok]')), we_message_reporting::WE_MESSAGE_NOTICE) .
						'top.content.hot=0;
						if(top.content.makeNewDoc) {
							setTimeout("top.content.we_cmd(\"module_navigation_' . (($this->Model->IsFolder == 1) ? 'new_group' : 'new') . '\",100)");
						}
					');

				if(we_base_request::_(we_base_request::BOOL, 'delayCmd')){
					$js .= we_html_element::jsElement(
							'top.content.we_cmd("' . we_base_request::_(we_base_request::JS, 'delayCmd') . '"' . (($dp = we_base_request::_(we_base_request::INT, 'delayParam')) ? ',"' . $dp . '"' : '' ) . ');
							'
					);
					$_REQUEST['delayCmd'] = '';
					$_REQUEST['delayParam'] = '';
				}

				echo $js;

				break;
			case 'module_navigation_delete':

				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();');

				if(!permissionhandler::hasPerm('DELETE_NAVIGATION')){
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
					return;
				}
				if($this->Model->delete()){
					echo we_html_element::jsElement('
									top.content.deleteEntry(' . $this->Model->ID . ');
									setTimeout(\'' . we_message_reporting::getShowMessageCall(g_l('navigation', ($this->Model->IsFolder == 1 ? '[group_deleted]' : '[navigation_deleted]')), we_message_reporting::WE_MESSAGE_NOTICE) . '\',500);

							');
					$this->Model = new we_navigation_navigation();
					$_REQUEST['home'] = 1;
					$_REQUEST['pnt'] = 'edbody';
				} else {
					echo we_html_element::jsElement(
						we_message_reporting::getShowMessageCall(g_l('navigation', '[nothing_to_delete]'), we_message_reporting::WE_MESSAGE_ERROR)
					);
				}
				break;
			case 'switchPage':

				break;
			case 'move_abs':
				if($this->Model->reorderAbs(we_base_request::_(we_base_request::INT, 'pos'))){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}

					echo we_html_element::jsElement(
						$this->editorBodyForm . '.Ordn.value=' . $this->Model->Ordn . ';
						top.content.reloadGroup(' . $this->Model->ParentID . ');
								WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
								WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");

								if(' . $this->editorBodyForm . '.Ordn.value==0){
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "disabled");
								} else {
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
								}' .
						$this->editorBodyForm . '.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'move_up' :
				if($this->Model->reorderUp()){
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement(
						$this->editorBodyForm . '.Ordn.value=' . $this->Model->Ordn . ';
						top.content.reloadGroup(' . $this->Model->ParentID . ');
								WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
								WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");

								if(' . $this->editorBodyForm . '.Ordn.value==1){
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "disabled");
								} else {
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
								}' .
						$this->editorBodyForm . '.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'move_down' :
				if($this->Model->reorderDown()){
					$_parentid = f('SELECT ParentID FROM ' . NAVIGATION_TABLE . ' WHERE ID=' . intval($this->Model->ID), 'ParentID', $this->db);
					$_num = f('SELECT MAX(Ordn) as OrdCount FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($_parentid), 'OrdCount', $this->db);
					$posVals = $this->getEditNaviPosition();
					$posText = '';
					foreach($posVals as $val => $text){
						$posText.='<option value="' . $val . '"' . ($val == $this->Model->Ordn ? ' selected="selected"' : '') . '>' . $text . '</option>';
					}
					echo we_html_element::jsElement(
						$this->editorBodyForm . '.Ordn.value=' . $this->Model->Ordn . ';
						top.content.reloadGroup(' . $this->Model->ParentID . ');
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
									WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_up", "enabled");
									if(' . $this->editorBodyForm . '.Ordn.value==' . ($_num + 1) . '){
										WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "disabled");
									} else {
										WE().layout.button.switch_button_state(top.content.editor.edbody.document, "direction_down", "enabled");
								}' .
						$this->editorBodyForm . '.Position.innerHTML=\'' . $posText . '\';'
					);
				}
				break;
			case 'populate':
				$_items = $this->Model->populateGroup();
				$_js = '';
				foreach($_items as $_k => $_item){
					$_js .= 'top.content.deleteEntry(' . $_item['id'] . ');
						top.content.makeNewEntry({id:\'' . $_item['id'] . '\',parentid:\'' . $this->Model->ID . '\',text:\'' . addslashes($_item['text']) . '\',open:0,contenttype:\'we/navigation\',table:\'' . NAVIGATION_TABLE . '\',published:1,order:' . $_k . '});';
				}
				echo we_html_element::jsElement(
					$_js .
					we_message_reporting::getShowMessageCall(g_l('navigation', '[populate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE)
				);
				break;
			case 'depopulate':
				$_items = $this->Model->depopulateGroup();
				$_js = '';
				foreach($_items as $_id){
					$_js .= 'top.content.deleteEntry(' . $_id . ');
						';
				}
				$_js .= we_message_reporting::getShowMessageCall(g_l('navigation', '[depopulate_msg]'), we_message_reporting::WE_MESSAGE_NOTICE);
				echo we_html_element::jsElement($_js);
				$this->Model->Selection = we_navigation_navigation::SELECTION_NODYNAMIC;
				$this->Model->saveField('Selection');
				break;
			case 'dyn_preview':
				echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') .
				we_html_element::jsElement('
						url = "' . WE_INCLUDES_DIR . 'we_modules/navigation/edit_navigation_frameset.php?pnt=dyn_preview";
						new (WE().util.jsWindow)(top.window, url,"we_navigation_dyn_preview",-1,-1,480,350,true,true,true);'
				);
				break;
			case 'create_template':
				echo we_html_element::jsElement(
					'top.content.opener.top.we_cmd("new","' . TEMPLATES_TABLE . '","","' . we_base_ContentTypes::TEMPLATE . '","","' . base64_encode($this->Model->previewCode) . '");
					');
				break;
			case 'populateFolderWs':
				$_prefix = '';
				$_values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				$_js = '';

				if($_values){

					foreach($_values as $_id => $_path){
						$_js .= $this->editorBodyForm . '.FolderWsID.options[' . $this->editorBodyForm . '.FolderWsID.options.length] = new Option("' . $_path . '",' . $_id . ');
							';
					}
					echo we_html_element::jsElement(
						'top.content.editor.edbody.setVisible("objLinkFolderWorkspace",true);
							' . $this->editorBodyForm . '.FolderWsID.options.length = 0;
							' . $_js . '
						');
				} elseif(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
					echo we_html_element::jsElement(
						$this->editorBodyForm . '.FolderWsID.options.length = 0;
								' . $this->editorBodyForm . '.FolderWsID.options[' . $this->editorBodyForm . '.FolderWsID.options.length] = new Option("/",0);
								' . $this->editorBodyForm . '.FolderWsID.selectedIndex = 0;
								top.content.editor.edbody.setVisible("objLinkFolderWorkspace",true);'
					);
				} else {
					echo we_html_element::jsElement(
						'top.content.editor.edbody.setVisible("objLinkFolderWorkspace' . $_prefix . '",false);
								' . $this->editorBodyForm . '.FolderWsID.options.length = 0;
								' . $this->editorBodyForm . '.FolderWsID.options[' . $this->editorBodyForm . '.FolderWsID.options.length] = new Option("-1",-1);
								' . $this->editorBodyForm . '.LinkID.value = "";
								' . $this->editorBodyForm . '.LinkPath.value = "";
								' . we_message_reporting::getShowMessageCall(g_l('navigation', '[no_workspace]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
				}

				break;
			case 'populateWorkspaces':

				$_objFields = "\n";
				if($this->Model->SelectionType == we_navigation_navigation::STPYE_CLASS){
					$__fields = array();
					if(defined('OBJECT_TABLE')){

						$_class = new we_object();
						$_class->initByID($this->Model->ClassID, OBJECT_TABLE);
						$_fields = $_class->getAllVariantFields();
						$_objFields = "\n";
						foreach($_fields as $_key => $val){
							$_objFields .= 'top.content.editor.edbody.weNavTitleField["' . substr($_key, strpos($_key, "_") + 1) . '"] = "' . $_key . '";' . "\n";
						}
					}
				}

				$_prefix = '';

				if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
					$_values = we_navigation_dynList::getWorkspacesForClass($this->Model->ClassID);
					$_prefix = 'Class';
				} else {
					$_values = we_navigation_dynList::getWorkspacesForObject($this->Model->LinkID);
				}

				$_js = '';

				if($_values){ // if the class has workspaces
					foreach($_values as $_id => $_path){
						$_js .= $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options[' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length] = new Option("' . $_path . '",' . $_id . ');
							';
					}
					echo we_html_element::jsElement(
						$_objFields .
						'top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",true);
							' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length = 0;
							' . $_js . '
						');
				} else { // if the class has no workspaces
					if(we_navigation_dynList::getWorkspaceFlag($this->Model->LinkID)){
						echo we_html_element::jsElement(
							$_objFields .
							$this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length = 0;
								' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options[' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length] = new Option("/",0);
								' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.selectedIndex = 0;
								//top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",false);'
						);
					} else {
						echo we_html_element::jsElement(
							$_objFields .
							'top.content.editor.edbody.setVisible("objLinkWorkspace' . $_prefix . '",false);
								' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length = 0;
								' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options[' . $this->editorBodyForm . '.WorkspaceID' . $_prefix . '.options.length] = new Option("-1",-1);
								' . $this->editorBodyForm . '.LinkID.value = "";
								' . $this->editorBodyForm . '.LinkPath.value = "";
								' . we_message_reporting::getShowMessageCall(g_l('navigation', '[no_workspace]'), we_message_reporting::WE_MESSAGE_ERROR) . '
							');
					}
				}
				break;
			case 'populateText':
				if(!$this->Model->Text && $this->Model->Selection == we_navigation_navigation::SELECTION_STATIC && $this->Model->SelectionType == we_navigation_navigation::STPYE_CATLINK){
					$_cat = new we_category();
					$_cat->load($this->Model->LinkID);

					if(isset($_cat->Title)){
						echo we_html_element::jsElement($this->editorBodyForm . '.Text.value = "' . addslashes($_cat->Title) . '";');
					}
				}
				break;
			default:
		}

		$_SESSION['weS']['navigation_session'] = $this->Model;
	}

	function processVariables(){
		if(isset($_SESSION['weS']['navigation_session']) && $_SESSION['weS']['navigation_session'] instanceof we_navigation_navigation){
			$this->Model = $_SESSION['weS']['navigation_session'];
		}

		if(defined('CUSTOMER_TABLE')){
			if(($mode = we_base_request::_(we_base_request::INT, 'wecf_mode')) !== false){
				we_navigation_customerFilter::translateModeToNavModel($mode, $this->Model);
			}
			$this->Model->Customers = we_customer_abstractFilter::getSpecificCustomersFromRequest();
			$this->Model->BlackList = we_customer_abstractFilter::getBlackListFromRequest();
			$this->Model->WhiteList = we_customer_abstractFilter::getWhiteListFromRequest();
			$this->Model->CustomerFilter = we_customer_abstractFilter::getFilterFromRequest();
			$this->Model->UseDocumentFilter = we_navigation_customerFilter::getUseDocumentFilterFromRequest();
		}

		$_categories = array();

		if(($name = we_base_request::_(we_base_request::STRING, 'CategoriesControl')) && ($cnt = we_base_request::_(we_base_request::INT, 'CategoriesCount')) !== false){
			for($i = 0; $i < $cnt; $i++){
				if(($cat = we_base_request::_(we_base_request::STRING, $name . '_variant0_' . $name . '_item' . $i)) !== false){
					$_categories[] = $cat;
				}
			}
			$this->Model->Categories = $_categories;
		}

		if(($field = we_base_request::_(we_base_request::STRING, 'SortField')) !== false){
			if($field){
				$this->Model->Sort = array(
					array(
						'field' => $field,
						'order' => we_base_request::_(we_base_request::STRING, 'SortOrder')
					)
				);
			} else {
				$this->Model->Sort = '';
			}
		}

		if(is_array($this->Model->persistent_slots)){
			foreach($this->Model->persistent_slots as $key => $type){
				if(($val = we_base_request::_($type, $key, '-1')) !== '-1'){
					$this->Model->$key = $val;
				}
			}
		}

		if($this->Model->Selection == we_navigation_navigation::SELECTION_DYNAMIC){
			if(($wid = we_base_request::_(we_base_request::INT, 'WorkspaceIDClass')) !== false){
				$this->Model->WorkspaceID = $wid;
			}

			if(($par = we_base_request::_(we_base_request::URL, 'dynamic_Parameter')) !== false){
				$this->Model->Parameter = $par;
			}

			if($this->Model->SelectionType == we_navigation_navigation::STPYE_CATEGORY && ($url = we_base_request::_(we_base_request::URL, 'dynamic_Url')) !== false){
				$this->Model->Url = $url;
				$this->Model->UrlID = we_base_request::_(we_base_request::INT, 'dynamic_UrlID', 0);
				$this->Model->LinkSelection = we_base_request::_(we_base_request::STRING, 'dynamic_LinkSelection');
				$this->Model->CatParameter = we_base_request::_(we_base_request::STRING, 'dynamic_CatParameter');
			}
		}


		if($this->Model->IsFolder == 0){
			$this->Model->Charset = $this->Model->findCharset($this->Model->ParentID);
		}

		if(($code = we_base_request::_(we_base_request::RAW_CHECKED, 'previewCode'))){
			$this->Model->previewCode = $code;
		}

		if(($page = we_base_request::_(we_base_request::INT, "page")) !== false){
			$this->page = ($this->Model->IsFolder && $page != 1 && $page != 3 ? 1 : $page);
		}
	}

	function getItems($id){
		$_db = new DB_WE();

		$_db->query('SELECT ID,Text FROM ' . NAVIGATION_TABLE . ' WHERE ParentID=' . intval($id) . ' AND Depended=1 ORDER BY Ordn;');
		return $_db->getAllFirst(false);
	}

}
