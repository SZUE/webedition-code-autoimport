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
we_html_tools::protect();
$parts = array();

$we_doc = new we_docTypes();

// Initialize variables
$we_show_response = 0;

switch(($wecmd0 = we_base_request::_(we_base_request::STRING, 'we_cmd', '', 0))){
	case "save_docType":
		if(!permissionhandler::hasPerm("EDIT_DOCTYPE")){
			$we_responseText = g_l('weClass', '[no_perms]');
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			break;
		}
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if(preg_match('|[\'",]|', $we_doc->DocType)){
			$we_responseText = g_l('alert', '[doctype_hochkomma]');
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			$we_JavaScript = "";
			$we_show_response = 1;
		} else if(!$we_doc->DocType){
			$we_responseText = g_l('alert', '[doctype_empty]');
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			$we_JavaScript = "";
			$we_show_response = 1;
		} else {
			$GLOBALS['DB_WE']->query('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType="' . $GLOBALS['DB_WE']->escape($we_doc->DocType) . '"');
			if(($GLOBALS['DB_WE']->next_record()) && ($we_doc->ID != $GLOBALS['DB_WE']->f("ID"))){
				$we_responseText = sprintf(g_l('weClass', '[doctype_save_nok_exist]'), $we_doc->DocType);
				$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
				$we_JavaScript = "";
				$we_show_response = 1;
			} else {
				$we_JavaScript = 'opener.top.makefocus = self;' .
					we_main_headermenu::getMenuReloadCode();

				//$we_JavaScript .= "opener.top.header.document.location.reload();\n";
				if($we_doc->we_save()){
					$we_responseText = sprintf(g_l('weClass', '[doctype_save_ok]'), $we_doc->DocType);
					$we_response_type = we_message_reporting::WE_MESSAGE_NOTICE;
					$we_show_response = 1;
				} else {
					echo "ERROR";
				}
			}
		}
		break;
	case "newDocType":
		if(($dt = we_base_request::_(we_base_request::STRING, 'we_cmd', 0, 1))){
			$we_doc->DocType = urldecode($dt);
			$we_doc->we_save();
		}
		break;
	case "deleteDocTypeok":
		if(!permissionhandler::hasPerm("EDIT_DOCTYPE")){
			$we_responseText = g_l('alert', '[no_perms]');
			$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
			break;
		}
		$id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		$name = f('SELECT DocType FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . $id);
		$del = false;
		if($name){
			$GLOBALS['DB_WE']->query('SELECT 1 FROM ' . FILE_TABLE . ' WHERE DocType=' . $id . ' LIMIT 1');
			if(!$GLOBALS['DB_WE']->next_record()){
				$GLOBALS['DB_WE']->query('DELETE FROM ' . DOC_TYPES_TABLE . ' WHERE ID=' . $id);

				// Fast Fix for deleting entries from tblLangLink: #5840
				$GLOBALS['DB_WE']->query('DELETE FROM ' . LANGLINK_TABLE . " WHERE DocumentTable='tblDocTypes' AND (DID=" . $id . ' OR LDID=' . $id . ')');

				$we_show_response = 1;
				$we_response_type = we_message_reporting::WE_MESSAGE_NOTICE;
				$we_responseText = sprintf(g_l('weClass', '[doctype_delete_ok]'), $name);
				$del = true;
			} else {
				$we_show_response = 1;
				$we_response_type = we_message_reporting::WE_MESSAGE_ERROR;
				$we_responseText = sprintf(g_l('weClass', '[doctype_delete_nok]'), $name);
			}
			if($del){
				$id = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' ORDER BY DocType LIMIT 1');
				if($id){
					$we_doc->initByID($id, DOC_TYPES_TABLE);
				}
			} else {
				$we_doc->initByID(we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1), DOC_TYPES_TABLE);
			}
		}
		break;
	case 'add_dt_template':
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		$foo = makeArrayFromCSV($we_doc->Templates);
		$ids = makeArrayFromCSV(we_base_request::_(we_base_request::INTLIST, 'we_cmd', '', 1));
		foreach($ids as $id){
			if(!in_array($id, $foo)){
				$foo[] = $id;
			}
		}
		$we_doc->Templates = makeCSVFromArray($foo);
		break;
	case 'delete_dt_template':
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		$foo = makeArrayFromCSV($we_doc->Templates);
		$cmd1 = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1);
		if($cmd1 && (in_array($cmd1, $foo))){
			$pos = array_search($cmd1, $foo);
			if($pos !== false || $pos == '0'){
				array_splice($foo, $pos, 1);
			}
		}
		if($we_doc->TemplateID == $cmd1){
			$we_doc->TemplateID = ($foo ? $foo[0] : 0);
		}
		$we_doc->Templates = makeCSVFromArray($foo);
		break;
	case "dt_add_cat":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if(($id = we_base_request::_(we_base_request::INTLIST, 'we_cmd', 0, 1))){
			$we_doc->addCat($id);
		}
		break;
	case "dt_delete_cat":
		$we_doc->we_initSessDat($_SESSION['weS']['we_data'][$we_transaction]);
		if(($id = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1))){
			$we_doc->delCat($id);
		}
		break;
	default:
		$id = (($tmp = we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1)) ? $tmp :
				f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' ' . we_docTypes::getDoctypeQuery($GLOBALS['DB_WE']) . ' LIMIT 1'));

		if($id){
			$we_doc->initByID($id, DOC_TYPES_TABLE);
		}
}

echo we_html_tools::getHtmlTop(g_l('weClass', '[doctypes]'));
$yuiSuggest = & weSuggest::getInstance();
echo weSuggest::getYuiFiles() .
 we_html_element::jsScript(JS_DIR . "keyListener.js") .
 we_html_element::jsScript(JS_DIR . 'windows.js');
?>
<script type="text/javascript"><!--
<?php
if($we_show_response){
	echo $we_JavaScript . ';';
	if($we_responseText){
		?>
		opener.top.toggleBusy(0);
		<?php
		echo we_message_reporting::getShowMessageCall($we_responseText, $we_response_type);
	}
}
switch($wecmd0){
	case "deleteDocType":
		if(!permissionhandler::hasPerm("EDIT_DOCTYPE")){
			echo we_message_reporting::getShowMessageCall(g_l('alert', '[no_perms]'), we_message_reporting::WE_MESSAGE_ERROR);
		} else {
			?>
			if (confirm("<?php printf(g_l('weClass', '[doctype_delete_prompt]'), $we_doc->DocType); ?>")) {
				we_cmd("deleteDocTypeok", "<?php echo we_base_request::_(we_base_request::INT, 'we_cmd', 0, 1); ?>");
			}
			<?php
		}
		break;
	case "deleteDocTypeok":
		echo 'opener.top.makefocus = self;' .
		we_main_headermenu::getMenuReloadCode();
//							opener.top.header.document.location.reload();

		echo we_message_reporting::getShowMessageCall($we_responseText, we_message_reporting::WE_MESSAGE_NOTICE);
}
?>

var countSaveLoop = 0;

function we_save_docType(doc, url) {
	acStatus = '';
	invalidAcFields = false;
	if (YAHOO && YAHOO.autocoml) {
		acStatus = YAHOO.autocoml.checkACFields();
	} else {
		we_submitForm(doc, url);
		return;
	}
	acStatusType = typeof acStatus;
	if (countSaveLoop > 10) {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
		countSaveLoop = 0;
	} else if (acStatusType.toLowerCase() == 'object') {
		if (acStatus.running) {
			countSaveLoop++;
			setTimeout('we_save_docType(doc,url)', 100);
		} else if (!acStatus.valid) {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
			countSaveLoop = 0;
		} else {
			countSaveLoop = 0;
			we_submitForm(doc, url);
		}
	} else {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[save_error_fields_value_not_valid]'), we_message_reporting::WE_MESSAGE_ERROR) ?>;
	}
}

function we_cmd() {
	var args = "";
	var url = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?";
	for (var i = 0; i < arguments.length; i++) {
		url += "we_cmd[" + i + "]=" + encodeURIComponent(arguments[i]);
		if (i < (arguments.length - 1)) {
			url += "&";
		}
	}
	switch (arguments[0]) {
		case "openImgselector":
		case "openDocselector":
		case "openDirselector":
			new jsWindow(url, "we_fileselector", -1, -1,<?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>, true, true, true, true);
			break;
		case "openCatselector":
			new jsWindow(url, "we_catselector", -1, -1,<?php echo we_selector_file::WINDOW_DOCSELECTOR_WIDTH . ',' . we_selector_file::WINDOW_DOCSELECTOR_HEIGHT; ?>, true, true, true, true);
			break;
		case "add_dt_template":
		case "delete_dt_template":
		case "dt_add_cat":
		case "dt_delete_cat":
		case "save_docType":
			we_save_docType(self.name, url)
			break;
		case "newDocType":
<?php
$GLOBALS['DB_WE']->query('SELECT CONCAT("\'",REPLACE(DocType,"\'","\\\\\'"),"\'") FROM ' . DOC_TYPES_TABLE . ' ORDER BY DocType');
$dtNames = implode(',', $GLOBALS['DB_WE']->getAll(true));
echo 'var docTypeNames = new Array(' . $dtNames . ');';
?>

			var name = prompt("<?php echo g_l('weClass', '[newDocTypeName]'); ?>", "");
			if (name != null) {
				if ((name.indexOf("<") != -1) || (name.indexOf(">") != -1)) {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[name_nok]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
					return;
				}
				if (name.indexOf("'") != -1 || name.indexOf('"') != -1 || name.indexOf(',') != -1) {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[doctype_hochkomma]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
				}
				else if (name == "") {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[doctype_empty]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
				}
				else if (in_array(docTypeNames, name)) {
<?php echo we_message_reporting::getShowMessageCall(g_l('alert', '[doctype_exists]'), we_message_reporting::WE_MESSAGE_ERROR); ?>
				}
				else {
<?php
echo we_main_headermenu::getMenuReloadCode();
?>
					/*						if (top.opener.top.header) {
					 top.opener.top.header.location.reload();
					 }*/
					self.location = "<?php echo WEBEDITION_DIR; ?>we_cmd.php?we_cmd[0]=newDocType&we_cmd[1]=" + encodeURIComponent(name);
				}
			}
			break;
		case "change_docType":
		case "deleteDocType":
		case "deleteDocTypeok":
			self.location = url;
			break;
		default:
			for (var i = 0; i < arguments.length; i++) {
				args += 'arguments[' + i + ']' + ((i < (arguments.length - 1)) ? ',' : '');
			}
			eval('opener.top.we_cmd(' + args + ')');
	}
}


function we_submitForm(target, url) {
	var f = self.document.we_form;
	f.target = target;
	f.action = url;
	f.method = "post";
	f.submit();
}

function doUnload() {
	if (jsWindow_count) {
		for (i = 0; i < jsWindow_count; i++) {
			eval("jsWindow" + i + "Object.close()");
		}
	}
	opener.top.dc_win_open = false;
}

function in_array(haystack, needle) {
	for (var i = 0; i < haystack.length; i++) {
		if (haystack[i] == needle) {
			return true;
		}
	}
	return false;
}

function makeNewEntry(icon, id, pid, txt, offen, ct, tab) {
	opener.top.makeNewEntry(icon, id, pid, txt, offen, ct, tab);
}

function updateEntry(id, text, pid, tab) {
	opener.top.updateEntry(id, text, pid, tab);
}

function disableLangDefault(allnames, allvalues, deselect) {
	var arr = allvalues.split(",");

	for (var v in arr) {
		w = allnames + '[' + arr[v] + ']';
		e = document.getElementById(w);
		e.disabled = false;
	}
	w = allnames + '[' + deselect + ']';
	e = document.getElementById(w);
	e.disabled = true;


}
//-->
</script>
<?php echo STYLESHEET; ?>
</head>

<body class="weDialogBody" style="overflow:hidden;" onunload="doUnload()" onload="self.focus();">
	<form name="we_form" action="" method="post" onsubmit="return false">
		<?php
		echo we_class::hiddenTrans();

		if($we_doc->ID){

			$parts[] = array("headline" => g_l('weClass', '[doctypes]'),
				"html" => $GLOBALS['we_doc']->formDocTypeHeader(),
				"space" => 120
			);

			$parts[] = array("headline" => g_l('weClass', '[name]'),
				"html" => $GLOBALS['we_doc']->formName(),
				"space" => 120
			);

			$parts[] = array("headline" => g_l('global', '[templates]'),
				"html" => $GLOBALS['we_doc']->formDocTypeTemplates(),
				"space" => 120
			);

			$parts[] = array("headline" => g_l('weClass', '[defaults]'),
				"html" => $GLOBALS['we_doc']->formDocTypeDefaults(),
				"space" => 120
			);
		} else {
			$parts[] = array("headline" => "",
				"html" => $GLOBALS['we_doc']->formNewDocType(),
				"space" => 0
			);
		}

		$cancelbut = we_html_button::create_button("close", "javascript:self.close();if(top.opener.we_cmd){top.opener.we_cmd('switch_edit_page',0);}");

		$buttons = ($we_doc->ID ?
				we_html_button::position_yes_no_cancel(we_html_button::create_button("save", "javascript:we_cmd('save_docType', '" . $we_transaction . "')"), "", $cancelbut) :
				'<div align="right">' . $cancelbut . '</div>');


		echo we_html_multiIconBox::getJS() .
			we_html_multiIconBox::getHTML("", "100%", $parts, 30, $buttons, -1, "", "", false, "", "", 630) .
			$yuiSuggest->getYuiCss() .
			$yuiSuggest->getYuiJs();
		?>
	</form>
</body>

</html>

<?php
$we_doc->saveInSession($_SESSION['weS']['we_data'][$we_transaction]);
