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

if (isset($_SERVER['SCRIPT_NAME']) && str_replace(dirname($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']) == str_replace(__DIR__, '', __FILE__)) {
	exit();
}

function we_getModuleNameByContentType($ctype) {
	$_moduleDir = '';
	for ($i = 0; $i < sizeof($GLOBALS['_we_active_integrated_modules']); $i++) {

		if (strstr($ctype, $GLOBALS['_we_active_integrated_modules'][$i])) {
			$_moduleDir = $GLOBALS['_we_active_integrated_modules'][$i];
		}
	}
	return $_moduleDir;
}

function we_getIndexFileIDs($db) {
	return f('SELECT GROUP_CONCAT(ID) AS IDs FROM ' . FILE_TABLE . ' WHERE IsSearchable=1 AND ((Published > 0 AND (ContentType="text/html" OR ContentType="text/webedition")) OR (ContentType="application/*") )','IDs',$db);
}

function we_getIndexObjectIDs($db) {
	return f('SELECT GROUP_CONCAT(ID) AS IDs FROM ' . OBJECT_FILES_TABLE . ' WHERE Published > 0 AND Workspaces != ""','IDs',$db);
}

function correctUml($in) {
	return str_replace(array('ä','ö','ü','Ä','Ö','Ü','ß'), array('ae','oe','ue','Ae','Oe','Ue','ss'), $in);
}

function inWorkflow($doc) {
	if (!defined('WORKFLOW_TABLE')||!$doc->IsTextContentDoc)
		return false;

	return ($doc->ID?weWorkflowUtility::inWorkflow($doc->ID, $doc->Table):false);
}

function getAllowedClasses($db = '') {
	if (!$db) {
		$db = new DB_WE();
	}
	$out = array();
	if (defined('OBJECT_TABLE')) {
		$ws = get_ws();
		$ofWs = get_ws(OBJECT_FILES_TABLE);
		$ofWsArray = makeArrayFromCSV(id_to_path($ofWs, OBJECT_FILES_TABLE));

		if (intval($ofWs) == 0) {
			$ofWs = 0;
		}
		if (intval($ws) == 0) {
			$ws = 0;
		}
		$db->query('SELECT ID,Workspaces,Path FROM ' . OBJECT_TABLE . ' WHERE IsFolder=0');

		while ($db->next_record()) {
			$path = $db->f('Path');
			if (!$ws || $_SESSION['perms']['ADMINISTRATOR'] || (!$db->f('Workspaces')) ||
							in_workspace($db->f('Workspaces'),$ws,FILE_TABLE,'',true)) {

				$path2 = $path . '/';
				if (!$ofWs || $_SESSION['perms']['ADMINISTRATOR']) {
					array_push($out, $db->f('ID'));
				} else {

					// object Workspace check (New since Version 4.x)
					foreach ($ofWsArray as $w) {
						if ($w == $db->f('Path') || (strlen($w) >= strlen($path2) && substr($w, 0, strlen($path2)) == ($path2))) {
							array_push($out, $db->f('ID'));
							break;
						}
					}
				}
			}
		}
	}
	return $out;
}

function getObjectRootPathOfObjectWorkspace($classDir, $classId, $db = '') {
	if (!$db) {
		$db = new DB_WE();
	}
	$rootPath = '/';
	$rootId = $classId;
	if (defined('OBJECT_TABLE')) {
		$ws = get_ws(OBJECT_FILES_TABLE);
		if (intval($ws) == 0) {
			$ws = 0;
		}
		$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE IsFolder=1 AND Path LIKE "' . $db->escape($classDir) . '%"');
		while ($db->next_record()) {
			if (!$ws || in_workspace($db->f('ID'), $ws, OBJECT_FILES_TABLE, '', true)) {
				if ($rootPath == '/' || strlen($db->f('Path')) < strlen($rootPath)) {
					$rootPath = $db->f('Path');
					$rootId = $db->f('ID');
				}
			}
		}
	}
	return $rootId;
}

function weFileExists($id, $table = FILE_TABLE, $db = '') {
	$id=intval($id);
	if ($id == 0){
		return true;
	}
	return f('SELECT ID FROM '.$table.' WHERE ID=' . $id, 'ID', ($db?$db:new DB_WE()));
}

function makePIDTail($pid, $cid, $db = '', $table = FILE_TABLE) {
	if ($table != FILE_TABLE) {
		return '1';
	}

	$pid_tail = '';
	if (!$db){
		$db = new DB_WE();
	}
	$parentIDs = array();
	$pid=intval($pid);
	array_push($parentIDs, $pid);
	while ($pid != 0) {
		$pid = f('SELECT ParentID FROM ' . FILE_TABLE . ' WHERE ID=' . $pid, 'ParentID', $db);
		array_push($parentIDs, $pid);
	}
	$cid=intval($cid);
	$foo = f('SELECT DefaultValues FROM ' . OBJECT_TABLE . ' WHERE ID=' . $cid, 'DefaultValues', $db);
	$fooArr = unserialize($foo);
	$flag = (isset($fooArr['WorkspaceFlag'])?$fooArr['WorkspaceFlag']:1);
	$pid_tail = ($flag ? OBJECT_X_TABLE . $cid . '.OF_Workspaces="" OR ':'');
	foreach ($parentIDs as $pid){
		$pid_tail .= ' ' . OBJECT_X_TABLE . $cid . '.OF_Workspaces like "%,' . $pid . ',%" OR ' . OBJECT_X_TABLE . $cid . '.OF_ExtraWorkspacesSelected like "%,' . $pid . ',%" OR ';
	}
	$pid_tail = trim(preg_replace('/^(.*)OR /', '\1', $pid_tail));
	if ($pid_tail == ''){
		return '1';
	}

	return ' ('.$pid_tail.') ';
}

function we_getInputRadioField($name, $value, $itsValue, $atts) {
	//  This function replaced fnc: we_getRadioField
	$atts['type'] = 'radio';
	$atts['name'] = $name;
	$atts['value'] = htmlspecialchars($itsValue);
	if ($value == $itsValue) {
		$atts['checked'] = 'checked';
	}
	return getHtmlTag('input', $atts);
}

function we_getTextareaField($name, $value, $atts) {
	$atts['name'] = $name;
	$atts['rows'] = isset($atts['rows']) ? $atts['rows'] : 5;
	$atts['cols'] = isset($atts['cols']) ? $atts['cols'] : 20;

	return getHtmlTag('textarea', $atts, htmlspecialchars($value), true);
}

function we_getInputTextInputField($name, $value, $atts) {
	$atts['type'] = 'text';
	$atts['name'] = $name;
	$atts['value'] = htmlspecialchars($value);

	return getHtmlTag('input', $atts);
}

function we_getInputPasswordField($name, $value, $atts) {
	$atts['type'] = 'password';
	$atts['name'] = $name;
	$atts['value'] = htmlspecialchars($value);

	return getHtmlTag('input', $atts);
}

function we_getHiddenField($name, $value, $xml = false) {
	return '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($value) . '" ' . ($xml ? ' /' : '') . '>';
}

function we_getInputChoiceField($name, $value, $values, $atts, $mode, $valuesIsHash = false) {
	//  This function replaced we_getChoiceField
	//  we need input="text" and select-box
	//  First input='text'
	//<input type="text"'.($size ? ' size="'.$size.'"' : '').' name="'.$name.'" value="'.htmlspecialchars($value).'" '.$attr.($xml ? " /" :"").'>
	$textField = getHtmlTag('input',array_merge($atts, array('type' => 'text', 'name' => $name, 'value' => htmlspecialchars($value))));

	$opts = getHtmlTag('option', array('value' => ''), '', true) . "\n";
	$attsOpts=array();

	if ($valuesIsHash) {
		foreach ($values as $_val => $_text) {
			$attsOpts['value'] = htmlspecialchars($_val);
			$opts .= getHtmlTag('option', $attsOpts, htmlspecialchars($_text)) . "\n";
		}
	} else {
		// options of select Menu
		$options = makeArrayFromCSV($values);
		if (isset($atts['xml'])) {
			$attsOpts['xml'] = $atts['xml'];
		}

		foreach ($options as $option) {
			$attsOpts['value'] = htmlspecialchars($option);
			$opts .= getHtmlTag('option', $attsOpts, htmlspecialchars($option)) . "\n";
		}
	}

	// select menu
	$onchange = ($mode == 'add'
		?'this.form.elements[\'' . $name . '\'].value += ((this.form.elements[\'' . $name . '\'].value ? \' \' : \'\') + this.options[this.selectedIndex].value);'
		:'this.form.elements[\'' . $name . '\'].value=this.options[this.selectedIndex].value;');

	if (isset($atts['id'])) { //  use another ID!!!!
		$atts['id'] = 'tmp_' . $atts['id'];
	}
	$atts['onchange'] = $onchange . 'this.selectedIndex=0;';
	$atts['name'] = 'tmp_' . $name;
	$atts['size'] = isset($atts['size']) ? $atts['size'] : 1;
	$atts = removeAttribs($atts, array('size')); //  remove size for choice
	$selectMenue = getHtmlTag('select', $atts, $opts, true);
	return '<table border="0" cellpadding="0" cellspacing="0"><tr><td>' . $textField . '</td><td>' . $selectMenue . '</td></tr></table>';
}

function we_getInputCheckboxField($name, $value, $attr) {
	//  returns a checkbox with associated hidden-field

	$tmpname = md5(uniqid(time()));
	if ($value) {
		$attr['checked'] = 'checked';
	}
	$attr['type'] = 'checkbox';
	$attr['value'] = 1;
	$attr['name'] = $tmpname;
	$attr['onclick'] = 'this.form.elements[\'' . $name . '\'].value=(this.checked) ? 1 : 0';
	$_attsHidden=array();

	// hiddenField
	if (isset($attr['xml'])) {
		$_attsHidden['xml'] = $attr['xml'];
	}
	$_attsHidden['type'] = 'hidden';
	$_attsHidden['name'] = $name;
	$_attsHidden['value'] = htmlspecialchars($value);

	return getHtmlTag('input', $attr) . getHtmlTag('input', $_attsHidden);
}

function we_getSelectField($name, $value, $values, $attribs = array(), $addMissing = true) {

	$options = makeArrayFromCSV($values);
	$attribs['name'] = $name;
	$content = '';
	$isin = 0;
	foreach($options as $option) {
		if ($option == $value) {
			$content .= getHtmlTag('option', array('value' => $option, 'selected' => 'selected'), $option, true) . "\n";
			$isin = 1;
		} else {
			$content .= getHtmlTag('option', array('value' => $option), $option, true) . "\n";
		}
	}
	if ((!$isin) && $addMissing) {
		$content .= getHtmlTag('option', array(
								'value' => htmlspecialchars($value), 'selected' => 'selected'
										), htmlspecialchars($value), true) . "\n";
	}
	return getHtmlTag('select', $attribs, $content, true);
}

function we_getCatsFromDoc($doc, $tokken = ',', $showpath = false, $db = '', $rootdir = '/', $catfield = '', $onlyindir='') {
	return we_getCatsFromIDs(
					(isset($doc->Category) ? $doc->Category : ''),
					$tokken,
					$showpath,
					$db,
					$rootdir,
					$catfield,
					$onlyindir);
}

function we_getCatsFromIDs($catIDs, $tokken = ',', $showpath = false, $db = '', $rootdir = '/', $catfield = '', $onlyindir='') {
	if (!$db)
		$db = new DB_WE();
	if ($catIDs) {
		$foo = makeArrayFromCSV($catIDs);
		$cats = array();
		$field = $catfield ? $catfield : ($showpath ? 'Path' : 'Category');
		if ($catfield) {
			$showpath = false;
		}
		if (!isset($GLOBALS['WE_CATEGORY_CACHE'])) {
			$GLOBALS['WE_CATEGORY_CACHE'] = array();
		}
		for ($i = 0; $i < sizeof($foo); $i++) {
			if (!isset($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]])) {
				$GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]] = getHash(
												'SELECT ID,Path,Category,Catfields FROM ' . CATEGORY_TABLE . ' WHERE ID="' . $foo[$i] . '"',
												$db);
			}
			if ($field == 'Title' || $field == 'Description') {
				if ($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]]['Catfields']) {
					$_arr = unserialize($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]]['Catfields']);
					if (empty($onlyindir)) {
						array_push(
										$cats,
										($field == 'Description') ? parseInternalLinks($_arr['default'][$field], 0) : $_arr['default'][$field]);
					} else {
						$pos = strpos($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]]['Path'], $onlyindir);
						if (($pos !== false) AND ($pos == 0)) {
							array_push(
											$cats,
											($field == 'Description') ? parseInternalLinks($_arr['default'][$field], 0) : $_arr['default'][$field]);
						}
					}
				} else {
					if (empty($onlyindir)) {
						array_push($cats, '');
					} else {
						$pos = strpos($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]]['Path'], $onlyindir);
						if (($pos !== false) AND ($pos == 0)) {
							array_push($cats, '');
						}
					}
				}
			} else {
				if (empty($onlyindir)) {
					array_push($cats, $GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]][$field]);
				} else {
					$pos = strpos($GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]]['Path'], $onlyindir);
					if (($pos !== false) AND ($pos == 0)) {
						array_push($cats, $GLOBALS['WE_CATEGORY_CACHE'][$foo[$i]][$field]);
					}
				}
			}
		}
		if (($showpath || $catfield == 'Path') && strlen($rootdir)) {
			for ($i = 0; $i < sizeof($cats); $i++) {
				if (substr($cats[$i], 0, strlen($rootdir)) == $rootdir) {
					$cats[$i] = substr($cats[$i], strlen($rootdir));
				}
			}
		}
		return makeCSVFromArray($cats, false, $tokken);
	}
	return '';
}

function initObject($classID, $formname = 'we_global_form', $categories = '', $parentid = '') {
	$session = isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START'];

	if (!(isset($GLOBALS['we_object']) && is_array($GLOBALS['we_object']))){
		$GLOBALS['we_object'] = array();
	}
	$GLOBALS['we_object'][$formname] = new we_objectFile();
	if ((!$session) || (!isset($_SESSION['we_object_session_'.$formname]))) {
		if ($session) {
			$_SESSION['we_object_session_'.$formname] = array();
		}
		$GLOBALS['we_object'][$formname]->we_new();
		if (isset($_REQUEST['we_editObject_ID']) && $_REQUEST['we_editObject_ID']){
			$GLOBALS['we_object'][$formname]->initByID($_REQUEST['we_editObject_ID'], OBJECT_FILES_TABLE);
		} else {
			$GLOBALS['we_object'][$formname]->TableID = $classID;
			$GLOBALS['we_object'][$formname]->setRootDirID(true);
			$GLOBALS['we_object'][$formname]->resetParentID();
			$GLOBALS['we_object'][$formname]->restoreDefaults();
			if (strlen($categories)) {
				$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
				$GLOBALS['we_object'][$formname]->Category = $categories;
			}
		}

		// save parentid
		if ($parentid) {
			// check if parentid is in correct folder ...
			$parentfolder = new we_class_folder();
			$parentfolder->initByID($parentid, OBJECT_FILES_TABLE);

			if ($parentfolder) {

				if (($GLOBALS['we_object'][$formname]->ParentPath == $parentfolder->Path) || strpos(
												$parentfolder->Path . '/',
												$GLOBALS['we_object'][$formname]->ParentPath) === 0) {
					$GLOBALS['we_object'][$formname]->ParentID = $parentfolder->ID;
					$GLOBALS['we_object'][$formname]->Path = $parentfolder->Path . '/' . $GLOBALS['we_object'][$formname]->Filename;
				}
			}
		}

		if ($session) {
			$GLOBALS['we_object'][$formname]->saveInSession($_SESSION['we_object_session_'.$formname]);
		}
		$GLOBALS['we_object'][$formname]->DefArray = $GLOBALS['we_object'][$formname]->getDefaultValueArray();
	} else {
		if (isset($_REQUEST['we_editObject_ID']) && $_REQUEST['we_editObject_ID']){
			$GLOBALS['we_object'][$formname]->initByID($_REQUEST['we_editObject_ID'], OBJECT_FILES_TABLE);
		}else{
			if ($session){
				$GLOBALS['we_object'][$formname]->we_initSessDat($_SESSION['we_object_session_'.$formname]);
			}
		}
		if ($classID && ($GLOBALS['we_object'][$formname]->TableID != $classID)){
			$GLOBALS['we_object'][$formname]->TableID = $classID;
		}
		if (strlen($categories)) {
			$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
			$GLOBALS['we_object'][$formname]->Category = $categories;
		}
	}
	if (isset($_REQUEST['we_returnpage'])) {
		$GLOBALS['we_object'][$formname]->setElement('we_returnpage', $_REQUEST['we_returnpage']);
	}

	if (isset($_REQUEST['we_ui_'.$formname]) && is_array($_REQUEST['we_ui_'.$formname])) {
		$dates = array();

		foreach ($_REQUEST['we_ui_'.$formname] as $n => $v) {
			if (preg_match('/^we_date_([a-zA-Z0-9_]+)_(day|month|year|minute|hour)$/', $n, $regs)) {
				$dates[$regs[1]][$regs[2]] = $v;
			} else {
				$v = removePHP($v);
				$GLOBALS['we_object'][$formname]->i_convertElemFromRequest('', $v, $n);
				$GLOBALS['we_object'][$formname]->setElement($n, $v);
			}
		}

		foreach ($dates as $k => $v) {
			$GLOBALS['we_object'][$formname]->setElement(
							$k,
							mktime(
											$dates[$k]['hour'],
											$dates[$k]['minute'],
											0,
											$dates[$k]['month'],
											$dates[$k]['day'],
											$dates[$k]['year']));
		}
	}
	if (isset($_REQUEST['we_ui_' . $formname . '_categories'])) {
		$cats = $_REQUEST['we_ui_' . $formname . '_categories'];
		// Bug Fix #750
		if (is_array($cats)) {
			$cats = implode(',', $cats);
		}
		$cats = makeIDsFromPathCVS($cats, CATEGORY_TABLE);
		$GLOBALS['we_object'][$formname]->Category = $cats;
	}
	if (isset($_REQUEST['we_ui_'.$formname.'_Category'])){
		if(is_array($_REQUEST['we_ui_'.$formname.'_Category'])) {
			$_REQUEST['we_ui_'.$formname.'_Category'] = makeCSVFromArray($_REQUEST['we_ui_'.$formname.'_Category'],true);
		} else {
			$_REQUEST["we_ui_$formname"."_Category"] = makeCSVFromArray(makeArrayFromCSV($_REQUEST['we_ui_'.$formname.'_Category']), true);
		}
	}
	foreach ($GLOBALS["we_object"][$formname]->persistent_slots as $slotname) {
		if ($slotname != "categories" && isset($_REQUEST["we_ui_" . $formname . "_" . $slotname])) {
			$GLOBALS["we_object"][$formname]->$slotname=  $_REQUEST["we_ui_".$formname."_".$slotname];
		}
	}

	we_imageDocument::checkAndPrepare($formname, "we_object");
	we_flashDocument::checkAndPrepare($formname, "we_object");
	we_quicktimeDocument::checkAndPrepare($formname, "we_object");
	we_otherDocument::checkAndPrepare($formname, "we_object");

	if ($session) {
		$GLOBALS["we_object"][$formname]->saveInSession($_SESSION["we_object_session_$formname"]);
	}
	return $GLOBALS["we_object"][$formname];
}

function initDocument($formname = 'we_global_form', $tid = '', $doctype = '', $categories = '') {
	//  check if a <we:sessionStart> Tag was before
	$session = isset($GLOBALS['WE_SESSION_START']) && $GLOBALS['WE_SESSION_START'];

	if (!(isset($GLOBALS['we_document']) && is_array($GLOBALS['we_document'])))
		$GLOBALS['we_document'] = array();
	$GLOBALS['we_document'][$formname] = new we_webEditionDocument();
	if ((!$session) || (!isset($_SESSION["we_document_session_$formname"]))) {
		if ($session)
			$_SESSION["we_document_session_$formname"] = array();
		$GLOBALS['we_document'][$formname]->we_new();
		if (isset($_REQUEST['we_editDocument_ID']) && $_REQUEST['we_editDocument_ID']) {
			$GLOBALS['we_document'][$formname]->initByID($_REQUEST['we_editDocument_ID'], FILE_TABLE);
		} else {
			$dt = f('SELECT ID FROM ' . DOC_TYPES_TABLE . " WHERE DocType like '" . $GLOBALS['we_document'][$formname]->DB_WE->escape($doctype) . "'",
											'ID',
											$GLOBALS['we_document'][$formname]->DB_WE);
			$GLOBALS['we_document'][$formname]->changeDoctype($dt);
			if ($tid) {
				$GLOBALS['we_document'][$formname]->setTemplateID($tid);
			}
			if (strlen($categories)) {
				$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
				$GLOBALS['we_document'][$formname]->Category = $categories;
			}
		}
		if ($session)
			$GLOBALS['we_document'][$formname]->saveInSession($_SESSION["we_document_session_$formname"]);
	} else {
		if (isset($_REQUEST['we_editDocument_ID']) && $_REQUEST['we_editDocument_ID']){
			$GLOBALS['we_document'][$formname]->initByID($_REQUEST['we_editDocument_ID'], FILE_TABLE);
		}else{
			if ($session){
				$GLOBALS['we_document'][$formname]->we_initSessDat($_SESSION["we_document_session_$formname"]);
			}
		}
		if (strlen($categories)) {
			$categories = makeIDsFromPathCVS($categories, CATEGORY_TABLE);
			$GLOBALS['we_document'][$formname]->Category = $categories;
		}
	}

	if (isset($_REQUEST['we_returnpage'])){
		$GLOBALS['we_document'][$formname]->setElement('we_returnpage', $_REQUEST['we_returnpage']);
	}
	if (isset($_REQUEST["we_ui_$formname"]) && is_array($_REQUEST["we_ui_$formname"])) {
		$dates = array();
		foreach ($_REQUEST["we_ui_$formname"] as $n => $v) {
			if (preg_match('/^we_date_([a-zA-Z0-9_]+)_(day|month|year|minute|hour)$/', $n, $regs)) {
				$dates[$regs[1]][$regs[2]] = $v;
			} else {
				$v = removePHP($v);
				$GLOBALS['we_document'][$formname]->setElement($n, $v);
			}
		}

		foreach ($dates as $k => $v){
			$GLOBALS['we_document'][$formname]->setElement(
							$k,
							mktime(
											$dates[$k]['hour'],
											$dates[$k]['minute'],
											0,
											$dates[$k]['month'],
											$dates[$k]['day'],
											$dates[$k]['year']));
		}
	}

	if (isset($_REQUEST['we_ui_' . $formname . '_categories'])) {
		$cats = $_REQUEST['we_ui_' . $formname . '_categories'];
		// Bug Fix #750
		if (is_array($cats)) {
			$cats = implode(',', $cats);
		}
		$cats = makeIDsFromPathCVS($cats, CATEGORY_TABLE);
		$GLOBALS['we_document'][$formname]->Category = $cats;
	}
	if (isset($_REQUEST["we_ui_$formname".'_Category'])){
		if(is_array($_REQUEST["we_ui_$formname".'_Category'])) {
			$_REQUEST["we_ui_$formname".'_Category'] = makeCSVFromArray($_REQUEST["we_ui_$formname".'_Category'],true);
		} else {
			$_REQUEST["we_ui_$formname".'_Category'] = makeCSVFromArray(makeArrayFromCSV($_REQUEST["we_ui_$formname".'_Category']), true);
		}
	}
	foreach ($GLOBALS['we_document'][$formname]->persistent_slots as $slotname) {
		if ($slotname != 'categories' && isset($_REQUEST['we_ui_' . $formname . '_' . $slotname])) {
			$GLOBALS["we_document"][$formname]->$slotname =  $_REQUEST["we_ui_".$formname."_".$slotname];
		}
	}

	we_imageDocument::checkAndPrepare($formname, 'we_document');
	we_flashDocument::checkAndPrepare($formname, 'we_document');
	we_quicktimeDocument::checkAndPrepare($formname, 'we_document');
	we_otherDocument::checkAndPrepare($formname, 'we_document');

	if ($session) {
		$GLOBALS['we_document'][$formname]->saveInSession($_SESSION["we_document_session_$formname"]);
	}
	return $GLOBALS['we_document'][$formname];
}

function makeIDsFromPathCVS($paths, $table = FILE_TABLE, $prePostKomma = true) {
	if (strlen($paths) == 0 || strlen($table) == 0)
		return "";
	$foo = makeArrayFromCSV($paths);
	$db = new DB_WE();
	$outArray = array();
	foreach ($foo as $path) {
		$path = trim($path);
		if (substr($path, 0, 1) != "/")
			$path = "/" . $path;
		$id = f("
			SELECT ID
			FROM $table
			WHERE Path='" . $db->escape($path) . "'", "ID", $db);
		if ($id)
			array_push($outArray, $id);
	}
	return makeCSVFromArray($outArray, $prePostKomma);
}

function getCatSQLTail($catCSV = '', $table = FILE_TABLE, $catOr = false, $db = "", $fieldName = "Category", $getParentCats = true, $categoryids = '') {
	$cat_tail = "";
	if (!$db)
		$db = new DB_WE();

	if ($categoryids) {

		$idarray = makeArrayFromCSV($categoryids);

		foreach ($idarray as $catId) {
			$catId = trim($catId);
			if ($catId) {
				$sql = getSQLForOneCatId($catId, $table, $db, $fieldName, $getParentCats);
				$cat_tail .= ( $sql . ($catOr ? " OR " : " AND "));
			}
		}

		$cat_tail = trim(preg_replace('#^(.*)'.($catOr ? 'OR':'AND').' $#', '\1', $cat_tail));

		if ($cat_tail == "") {
			$cat_tail = " AND " . $table . "." . $fieldName . " = '-1' ";
		} else {
			$cat_tail = " AND (" . $cat_tail . ") ";
		}
	} else
	if ($catCSV) {
		$foo = makeArrayFromCSV($catCSV);
		foreach ($foo as $cat) {
			$cat = trim($cat);
			if (strlen($cat) > 0 && substr($cat, -1) == "/") {
				$cat = substr($cat, 0, strlen($cat) - 1);
			}
			if (substr($cat, 0, 1) != "/") {
				$cat = "/" . $cat;
			}
			$sql = getSQLForOneCat($cat, $table, $db, $fieldName, $getParentCats);
			$cat_tail .= ( $sql . ($catOr ? " OR " : " AND "));
		}

		$cat_tail = trim(preg_replace('#^(.*)'.($catOr ? 'OR':'AND').' $#', '\1', $cat_tail));

		if ($cat_tail == "") {
			$cat_tail = " AND " . $table . "." . $fieldName . " = '-1' ";
		} else {
			$cat_tail = " AND (" . $cat_tail . ") ";
		}
	}

	return $cat_tail;
}

function getSQLForOneCatId($cat, $table = FILE_TABLE, $db = "", $fieldName = "Category", $getParentCats = true) {
	$db=($db?$db:new DB_WE());
	// 1st get path of id
	$catPath=f('SELECT Path FROM ' . CATEGORY_TABLE . ' WHERE ID = ' . intval($cat),'Path',$db);

	return ($catPath ?
					getSQLForOneCat($catPath, $table, $db, $fieldName, $getParentCats):
					'');
}

function getSQLForOneCat($cat, $table = FILE_TABLE, $db = "", $fieldName = "Category", $getParentCats = true) {
	$db=($db?$db:new DB_WE());
	$q = 'SELECT DISTINCT ID FROM ' . CATEGORY_TABLE . ' WHERE Path LIKE "' . $db->escape($cat) . '/%" OR Path="' . $db->escape($cat) . '"';

	$db->query($q);
	$sql = '';
	$z = 0;
	while ($db->next_record())
		$sql .= " " . $table . "." . $fieldName . " like '%," . intval($db->f("ID")) . ",%' OR ";
	$sql = preg_replace('#^(.*)OR $#', '\1', $sql);
	return ($sql?"( $sql )":'');
}

function getHttpOption() {
	if (ini_get('allow_url_fopen') != 1) {
		@ini_set('allow_url_fopen', '1');
		if (ini_get('allow_url_fopen') != 1) {
			return (function_exists('curl_init') ? 'curl':'none');
		}
	}
	return 'fopen';
}

function getCurlHttp($server, $path, $files = array(), $port = '', $protocol = 'http', $username = '', $password = '', $header = false) {
	$_response = array(
			'data' => '', // data if successful
			'status' => 0, // 0=ok otherwise error
			'error' => '' // error string
	);

	$server = str_replace($protocol.'://', '', $server);
	$port = defined('HTTP_PORT') ? HTTP_PORT : 80;
	$_pathA = explode('?', $path);
	$_url = $protocol . '://' . $server . ':' . $port . $_pathA[0];
	$_params = array();

	$_session = curl_init();
	curl_setopt($_session, CURLOPT_URL, $_url);
	curl_setopt($_session, CURLOPT_RETURNTRANSFER, 1);

	if ($username != '') {
		curl_setopt($_session, CURLOPT_USERPWD, $username . ':' . $password);
	}

	if (isset($_pathA[1]) && $_pathA[1] != '') {
		$_url_param = explode('&', $_pathA[1]);
		$_len = count($_url_param);
		for ($i = 0; $i < $_len; $i++) {
			$_param_split = explode('=', $_url_param[$i]);
			$_params[$_param_split[0]] = isset($_param_split[1]) ? $_param_split[1] : '';
		}
	}

	if (!empty($files)) {
		foreach ($files as $k => $v) {
			$_params[$k] = '@' . $v;
		}
	}

	if (!empty($_params)) {
		curl_setopt($_session, CURLOPT_POST, 1);
		curl_setopt($_session, CURLOPT_POSTFIELDS, $_params);
	}

	if ($header) {
		curl_setopt($_session, CURLOPT_HEADER, 1);
	}

	if (defined('WE_PROXYHOST') && WE_PROXYHOST != '') {

		$_proxyhost = defined('WE_PROXYHOST') ? WE_PROXYHOST : '';
		$_proxyport = (defined('WE_PROXYPORT') && WE_PROXYPORT) ? WE_PROXYPORT : '80';
		$_proxy_user = defined('WE_PROXYUSER') ? WE_PROXYUSER : '';
		$_proxy_pass = defined('WE_PROXYPASSWORD') ? WE_PROXYPASSWORD : '';

		if ($_proxyhost != '') {
			curl_setopt($_session, CURLOPT_PROXY, $_proxyhost . ':' . $_proxyport);
			if ($_proxy_user != '') {
				curl_setopt($_session, CURLOPT_PROXYUSERPWD, $_proxy_user . ':' . $_proxy_pass);
			}
			curl_setopt($_session, CURLOPT_SSL_VERIFYPEER, FALSE);
		}
	}

	$_data = curl_exec($_session);

	if (curl_errno($_session)) {
		$_response['status'] = 1;
		$_response['error'] = curl_error($_session);
		return false;
	} else {
		$_response['status'] = 0;
		$_response['data'] = $_data;
		curl_close($_session);
	}

	return $_response;
}

//FIXME: this function assumes strict Ports
//FIXME: this function SHOULD handle a given getServerUrl()!!! => e.g. weVersions.class.inc.php
function getHTTP($server, $url, $port = '', $username = '', $password = '') {
	$_opt = getHttpOption();

	switch($_opt){
	case 'fopen':

		if (!$port) {
			$port = defined('HTTP_PORT') ? HTTP_PORT : 80;
		}

		$foo = 'http'.($port==443?'s':'').'://' . (($username && $password) ? "$username:$password@" : '') . $server . ':' . $port . $url;
		$page = 'Server Error: Failed opening URL: '.$foo;
		$fh = @fopen($foo, 'rb');
		if (!$fh) {
			$fh = @fopen($_SERVER['DOCUMENT_ROOT'] . $url, 'rb');
		}
		if ($fh) {
			$page = '';
			while (!feof($fh))
				$page .= fgets($fh, 1024);
			fclose($fh);
		}
		return $page;
	case 'curl':
		$_response = getCurlHttp($server, $url, array(), $port, 'http', $username, $password);
		return ($_response['status'] != 0 ? $_response['error']:$_response['data']);
	default:
		return 'Server error: Unable to open URL (php configuration directive allow_url_fopen=Off)';
	}
}

function attributFehltError($attribs, $attr, $tag, $canBeEmpty = false) {
	if ($canBeEmpty) {
		if (!isset($attribs[$attr]))
			return parseError(sprintf(g_l('parser','[attrib_missing2]'), $attr, $tag));
	} else {
		if (!isset($attribs[$attr]) || $attribs[$attr] == '')
			return parseError(sprintf(g_l('parser','[attrib_missing]'), $attr, $tag));
	}
	return '';
}

function modulFehltError($modul, $tag) {
	return parseError(sprintf(g_l('parser','[module_missing]'), $modul, $tag));
}

function parseError($text) {
	trigger_error($text,E_USER_WARNING);
	return "<b>" . g_l('parser','[error_in_template]') . ":</b> $text<br>\n".'<?php trigger_error(\''.str_replace('\'', '"', $text).'\',E_USER_WARNING);?>';
}

function std_numberformat($content) {
	if (preg_match('#.*,[0-9]*$#', $content)) {
		// Deutsche Schreibweise
		$umschreib = preg_replace('#(.*),([0-9]*)$#', '\1.\2', $content);
		$pos = strrpos($content, ',');
		$vor = str_replace('.', '', substr($umschreib, 0, $pos));
		$content = $vor . substr($umschreib, $pos, strlen($umschreib) - $pos);
	} else
	if (preg_match('#.*\.[0-9]*$#', $content)) {
		// Englische Schreibweise
		$pos = strrpos($content, '.');
		$vor = substr($content, 0, $pos);
		$vor = str_replace(',','',str_replace('.','', $vor));
		$content = $vor . substr($content, $pos, strlen($content) - $pos);
	} else
		$content = str_replace(',','',str_replace('.','',$content));
	return $content;
}

function decode($in) {
	$out = '';
	for ($i = 0; $i < strlen($in); $i++)
		$out .= chr(ord(substr($in, $i, 1)) + 1);
	return $out;
}

function encode($in) {
	$out = '';
	for ($i = 0; $i < strlen($in); $i++)
		$out .= chr(ord(substr($in, $i, 1)) - 1);
	return $out;
}

/**
 *
 * @param type $id
 * @param type $table
 * @return bool true on success, or if not in DB
 */
function deleteContentFromDB($id, $table) {
	$DB_WE = new DB_WE();

	if(f('SELECT 1 AS cnt FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '" LIMIT 1','cnt',$DB_WE) !=1){
		return true;
	}

	$DB_WE->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE ID IN (
		SELECT CID FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '")');
	return $DB_WE->query('DELETE FROM ' . LINK_TABLE . ' WHERE DID=' . intval($id) . ' AND DocumentTable="' . $DB_WE->escape(stripTblPrefix($table)) . '"');
}

/**
 * Strips off the table prefix - this function is save of calling multiple times
 * @param string $table
 * @return string stripped tablename
 */
function stripTblPrefix($table){
	return TBL_PREFIX != '' && (strpos($table, TBL_PREFIX) !== FALSE) ? substr($table, strlen(TBL_PREFIX)) : $table;
}

function addTblPrefix($table){
	return TBL_PREFIX . $table;
}

function cleanTempFiles($cleanSessFiles = false) {
	$db2 = new DB_WE();
	$sess = $GLOBALS['DB_WE']->query('SELECT Date,Path FROM ' . CLEAN_UP_TABLE . ' WHERE Date <= ' . (time() - 300));
	while ($GLOBALS['DB_WE']->next_record()) {
		$p = $GLOBALS['DB_WE']->f('Path');
		if (file_exists($p)){
			we_util_File::deleteLocalFile($GLOBALS['DB_WE']->f('Path'));
		}
		$db2->query('DELETE LOW_PRIORITY FROM ' . CLEAN_UP_TABLE . ' WHERE DATE=' . intval($GLOBALS['DB_WE']->f('Date')) . ' AND Path="' . $GLOBALS['DB_WE']->f('Path') . '"');
	}
	if ($cleanSessFiles) {
		$seesID = session_id();
		$GLOBALS['DB_WE']->query('SELECT Date,Path FROM ' . CLEAN_UP_TABLE . " WHERE Path like '%" . $GLOBALS['DB_WE']->escape($seesID) . "%'");
		while ($GLOBALS['DB_WE']->next_record()) {
			$p = $GLOBALS['DB_WE']->f('Path');
			if (file_exists($p)){
				we_util_File::deleteLocalFile($GLOBALS['DB_WE']->f('Path'));
			}
			$db2->query('DELETE LOW_PRIORITY FROM ' . CLEAN_UP_TABLE . " WHERE Path like '%" . $GLOBALS['DB_WE']->escape($seesID) . "%'");
		}
	}
	$d = dir(TMP_DIR);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.' && $entry != '..') {
			$foo = TMP_DIR . '/' . $entry;
			if (filemtime($foo) <= (time() - 300)) {
				if (is_dir($foo))
					we_util_File::deleteLocalFolder($foo, 1);
				else
				if (file_exists($foo))
					we_util_File::deleteLocalFile($foo);
			}
		}
	}
	$d->close();
	$dstr = $_SERVER['DOCUMENT_ROOT'] . BACKUP_DIR . 'tmp/';
	$d = dir($dstr);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.' && $entry != '..') {
			$foo = $dstr . $entry;
			if (filemtime($foo) <= (time() - 300)) {
				if (is_dir($foo)){
					we_util_File::deleteLocalFolder($foo, 1);
				}else
				if (file_exists($foo) && is_writable($foo)){
					we_util_File::deleteLocalFile($foo);
				}
			}
		}
	}
	$d->close();

	// when a fragment task was stopped by the user, the tmp file will not be deleted! So we have to clean up
	$d = dir(WE_FRAGMENT_DIR);
	while (false !== ($entry = $d->read())) {
		if ($entry != '.' && $entry != '..') {
			$foo = WE_FRAGMENT_DIR . '/' . $entry;
			if (filemtime($foo) <= (time() - 3600 * 24)) {
				if (is_dir($foo))
					we_util_File::deleteLocalFolder($foo, 1);
				else
				if (file_exists($foo))
					we_util_File::deleteLocalFile($foo);
			}
		}
	}
	$d->close();
}


function getTemplatesOfTemplate($id, &$arr) {
	$foo=f('SELECT GROUP_CONCAT(ID) AS IDS FROM ' . TEMPLATES_TABLE . ' WHERE MasterTemplateID=' . intval($id) . " OR IncludedTemplates LIKE '%," . intval($id) . ",%'",'IDS',$GLOBALS['DB_WE']);

	if(!$foo){
		return;
	}

	$foo=explode(',',$foo);
	$arr=array_merge($arr,$foo);
	if (in_array($id, $arr)) {
		return;
	}

	foreach ($foo as $check) {
		getTemplatesOfTemplate($check, $arr);
	}
}

function getTemplAndDocIDsOfTemplate($id, $staticOnly = true, $publishedOnly = false, $PublishedAndTemp = false) {
	if (!$id){
		return 0;
	}

	$returnIDs = array();
	$returnIDs['templateIDs'] = array();
	$returnIDs['documentIDs'] = array();

	getTemplatesOfTemplate($id, $returnIDs['templateIDs']);

	// first we need to check if template is included within other templates
	//$GLOBALS['DB_WE']->query("SELECT ID FROM ".TEMPLATES_TABLE." WHERE MasterTemplateID=".intval($id)." OR IncludedTemplates LIKE '%,".intval($id).",%'");
	//while ($GLOBALS['DB_WE']->next_record()) {
	//	array_push($returnIDs["templateIDs"], $GLOBALS['DB_WE']->f("ID"));
	//}

	$id = intval($id);

	// Bug Fix 6615
	$tmpArray=$returnIDs['templateIDs'];
	$tmpArray[]=$id;
	$tmp=implode(',',$tmpArray);
	unset($tmpArray);
	$where=' ('.
	  ($PublishedAndTemp?'temp_template_id IN (' . $tmp . ') OR ':'').
		' TemplateID IN (' . $tmp.')'.
		')'.
	  ($staticOnly?' AND IsDynamic=0':'').
		($publishedOnly?' AND Published>0':'');

	$GLOBALS['DB_WE']->query('SELECT ID FROM ' . FILE_TABLE . ' WHERE '.$where);

	while ($GLOBALS['DB_WE']->next_record()) {
		array_push($returnIDs['documentIDs'], $GLOBALS['DB_WE']->f('ID'));
	}
	return $returnIDs;
}

function ObjectUsedByObjectFile($id) {
	if (!$id){
		return false;
	}
	return f('SELECT 1 AS cnt FROM ' . OBJECT_FILES_TABLE . ' WHERE TableID=' . intval($id).' LIMIT 0,1','cnt',$GLOBALS['DB_WE'])==1;
}

function dbDateToTimeStamp($date, $time = '') {
	list($y, $m, $d) = explode('-', $date);
	list($hr, $min, $sec) = $time ? explode(':', $time) : array(0, 0, 0);
	return mktime($hr, $min, $sec, $m, $d, $y);
}

function we_makeHiddenFields($filter = '') {
	$filterArr = explode(',', $filter);
	$hidden = '';
	if ($_REQUEST) {
		reset($_REQUEST);
		while (list($key, $val) = each($_REQUEST)){
			if (!in_array($key, $filterArr)) {
				if (is_array($val)) {
					foreach($val as $v){
						$hidden .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($v) . '" />';
					}
				} else
					$hidden .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($val) . '" />';
			}
		}
	}
	return $hidden;
}

function we_make_attribs($attribs, $doNotUse = '') {
	$attr = '';
	$fil = explode(',', $doNotUse);
	//array_push($fil,'xml');
	array_push($fil, 'user');
	array_push($fil, 'removefirstparagraph');
	if (is_array($attribs)) {
		reset($attribs);
		while (list($k, $v) = each($attribs))
			if (!in_array($k, $fil))
				$attr .= "$k=\"$v\" ";
		$attr = trim($attr);
	}
	return $attr;
}

function debug($text) {
	$fp = fopen(TMP_DIR . '/debug.txt', 'ab');
	fwrite($fp, $text);
	fclose($fp);
}

function we_hasPerm($perm) {
	return (isset($_SESSION['perms']['ADMINISTRATOR']) && $_SESSION['perms']['ADMINISTRATOR']) ||
					((isset($_SESSION['perms'][$perm]) && $_SESSION['perms'][$perm]) ||
					(!isset($_SESSION['perms'][$perm])));
}

function we_userCanEditModule($modName) {
	$one = false;
	$set = array();
	$enable = 1;
	if ($_SESSION['perms']['ADMINISTRATOR']) {
		return true;
	}
	foreach ($GLOBALS['_we_available_modules'] as $m)
		if ($m['name'] == $modName) {

			$p = isset($m['perm']) ? $m['perm'] : '';
			$or = explode('||', $p);
			foreach ($or as $k => $v) {
				$and = explode('&&', $v);
				$one = true;
				foreach ($and as $key => $val) {
					array_push($set, 'isset($_SESSION[\'perms\'][\'' . trim($val) . '\'])');
					$and[$key] = '$_SESSION[\'perms\'][\'' . trim($val) . '\']';
					$one = false;
				}
				$or[$k] = implode(' && ', $and);
				if ($one && !in_array('isset($_SESSION[\'perms\'][\'' . trim($v) . '\'])', $set))
					array_push($set, 'isset($_SESSION[\'perms\'][\'' . trim($v) . '\'])');
			}
			$set_str = implode(' || ', $set);
			$condition_str = implode(' || ', $or);
		 //FIXME: remove eval
			eval('if (' . $set_str . '){ if (' . $condition_str . ') { $enable=1; } else { $enable=0; } }');
			return $enable;
		}
	return $enable;
}

function makeOwnersSql($useCreatorID = true) {
	if (!$_SESSION['perms']['ADMINISTRATOR']) {
		$aliases = array(
				$_SESSION['user']['ID']
		);
		we_getAliases($_SESSION['user']['ID'], $aliases, $GLOBALS['DB_WE']);
		$q = $useCreatorID ? 'CreatorID IN (\'' . implode('\',\'', $aliases) . '\') OR ' : '';
		foreach ($aliases as $id)
			$q .= 'Owners like "%,' . intval($id) . ',%\' OR ';
		$groups = array(
				$_SESSION['user']['ID']
		);
		we_getParentIDs(USER_TABLE, $_SESSION['user']['ID'], $groups, $GLOBALS['DB_WE']);
		foreach ($aliases as $id)
			we_getParentIDs(USER_TABLE, $id, $groups, $GLOBALS['DB_WE']);
		foreach ($groups as $id)
			$q .= "Owners like '%," . intval($id) . ",%' OR ";
		$q = preg_replace('#^(.*) OR $#', '\1', $q);
		return ' AND ( RestrictOwners=0 OR (' . $q . ')) ';
	} else
		return '';
}

function we_getParentIDs($table, $id, &$ids, $db = '') {
	if (!$db)
		$db = new DB_WE();
	while (($pid = f('SELECT ParentID FROM '.$table.' WHERE ID=' . intval($id), 'ParentID', $db)) > 0) {
		$ids[]=$pid;
	}
}

function we_getAliases($id, &$ids, $db = '') {
	if (!$db)
		$db = new DB_WE();
	$foo=f('SELECT GROUP_CONCAT(ID) AS IDS FROM ' . USER_TABLE . ' WHERE Alias=' . intval($id), 'IDS', $db);
	if($foo){
		$ids=array_merge($ids,explode(',',$foo));
	}
}

function we_isOwner($csvOwners) {
	if ($_SESSION['perms']['ADMINISTRATOR']) {
		return true;
	}
	$ownersArray = makeArrayFromCSV($csvOwners);
	return (in_array($_SESSION['user']['ID'], $ownersArray)) || we_users_util::isUserInUsers($_SESSION['user']['ID'], $csvOwners);
}

function makeArrayFromCSV($csv) {
	$csv = trim(str_replace('\\,', '###komma###', $csv),',');

	if ($csv == '' && $csv != '0') {
		return array();
	}

		$foo = explode(',', $csv);
		foreach ($foo as &$f) {
			$f = str_replace('###komma###', ',', $f);
		}
		return $foo;
}

function makeCSVFromArray($arr, $prePostKomma = false, $sep = ',') {
	if (!sizeof($arr))
		return '';

	$replaceKomma = (count($arr) > 1) || ($prePostKomma == true);

	if ($replaceKomma) {
		foreach ($arr as &$a) {
			$a = str_replace($sep, '###komma###', $a);
		}
	}
	$out = implode($sep, $arr);
	if ($prePostKomma) {
		$out = $sep . $out . $sep;
	}
	if ($replaceKomma) {
		$out = str_replace('###komma###', '\\'.$sep, $out);
	}
	return $out;
}

function shortenPath($path, $len) {
	if (strlen($path) <= $len || strlen($path) < 10)
		return $path;
	$l = ($len / 2) - 2;
	return substr($path, 0, $l) . '....' . substr($path, $l * -1);
}

function shortenPathSpace($path, $len) {
	if (strlen($path) <= $len || strlen($path) < 10)
		return $path;
	$l = $len;
	return substr($path, 0, $l) . ' ' . shortenPathSpace(substr($path, $l), $len);
}

function in_parentID($id, $pid, $table = FILE_TABLE, $db = '') {
	if (intval($pid) != 0 && intval($id) == 0)
		return false;
	if (intval($pid) == 0 || $id == $pid || ($id == '' && $id != '0'))
		return true;
	if (!$db)
		$db = new DB_WE();
	$found = array();
	$p = intval($id);
	do {
		if ($p == $pid) {
			return true;
		}
		if (in_array($p, $found)) {
			return false;
		}
		array_push($found, $p);
		$p = f('SELECT ParentID FROM '.$table.' WHERE ID=' . intval($p), 'ParentID', $db);
	} while ($p);
	return false;
}

function in_workspace($IDs, $wsIDs, $table = FILE_TABLE, $db = '', $objcheck = false) {
	if (!$db) {
		$db = new DB_WE();
	}
	if (!is_array($IDs)) {
		$IDs = makeArrayFromCSV($IDs);
	}
	if (!is_array($wsIDs)) {
		$wsIDs = makeArrayFromCSV($wsIDs);
	}
	if (!sizeof($wsIDs)|| !sizeof($IDs) || (in_array(0, $wsIDs))) {
		return true;
	}
	if ((!$objcheck) && in_array(0, $IDs)) {
		return false;
	}
	foreach ($IDs as $id) {
		foreach ($wsIDs as $ws) {
			if (in_parentID($id, $ws, $table, $db) || ($id == $ws) || ($id == 0)) {
				return true;
			}
		}
	}
	return false;
}

function userIsOwnerCreatorOfParentDir($folderID, $tab) {
	if ($tab != FILE_TABLE && $tab != OBJECT_FILES_TABLE)
		return true;
	if ($_SESSION['perms']['ADMINISTRATOR'] || ($folderID == 0)){
		return true;
	}
	$db = new DB_WE();
	$db->query('SELECT RestrictOwners,Owners,CreatorID FROM '.$tab.' WHERE ID='.intval($folderID));
	if ($db->next_record())
		if ($db->f('RestrictOwners')) {
			$ownersArr = makeArrayFromCSV($db->f('Owners'));
			foreach ($ownersArr as $uid)
				we_users_util::addAllUsersAndGroups($uid, $ownersArr);
			array_push($ownersArr, $db->f('CreatorID'));
			$ownersArr = array_unique($ownersArr);
			if (in_array($_SESSION['user']['ID'], $ownersArr)) {
				return true;
			} else {
				return false;
			}
		} else {
			$pid = f('SELECT ParentID FROM '.$tab.' WHERE ID='.intval($folderID), 'ParentID', $db);
			return userIsOwnerCreatorOfParentDir($pid, $tab);
		}
	return true;
}

function path_to_id($path, $table = FILE_TABLE) {
	$db = new DB_WE();
	if ($path == '/') {
		return 0;
	}
	return intval(f("SELECT DISTINCT ID FROM $table WHERE Path='" . $db->escape($path) . "' LIMIT 1", "ID", $db));
}

function weConvertToIds($paths, $table) {
	if (!is_array($paths))
		return array();
	$paths = array_unique($paths);
	$ids = array();
	foreach ($paths as $p) {
		$ids[] = path_to_id($p, $table);
	}
	return $ids;
}

function path_to_id_ct($path, $table, &$contentType) {
	$db = new DB_WE();
	if ($path == '/') {
		return 0;
	}
	$res = getHash("SELECT ID,ContentType FROM $table WHERE Path='" . $db->escape($path) . "'", $db);
	$contentType = isset($res['ContentType']) ? $res['ContentType'] : null;

	return intval(isset($res['ID']) ? $res['ID'] : 0);
}

function id_to_path($IDs, $table = FILE_TABLE, $db = '', $prePostKomma = false, $asArray = false, $endslash = false) {
	if (!is_array($IDs) && !$IDs) {
		return '/';
	}
	if (!$db) {
		$db = new DB_WE();
	}
	if (!is_array($IDs)) {
		$IDs = makeArrayFromCSV($IDs);
	}
	$foo = array();
	foreach ($IDs as $id) {
		if ($id == 0) {
			array_push($foo, '/');
		} else {
			$foo2 = getHash("SELECT Path,IsFolder FROM $table WHERE ID=" . intval($id), $db);
			if (isset($foo2['Path'])) {
				if ($endslash && $foo2['IsFolder']) {
					$foo2['Path'] .= '/';
				}
				array_push($foo, $foo2['Path']);
			}
		}
	}
	if ($asArray) {
		return $foo;
	} else {
		return makeCSVFromArray($foo, $prePostKomma);
	}
}

function getHashArrayFromCSV($csv, $firstEntry, $db='') {
	if (!$csv)
		return array();
	if (!$db)
		$db = new DB_WE();
	$IDArr = makeArrayFromCSV($csv);
	$out = $firstEntry ? array(
			'0' => $firstEntry
					) : array();
	foreach ($IDArr as $i => $id) {
		if (strlen($id) && ($path = id_to_path($id, FILE_TABLE, $db))) {
			$out[$id] = $path;
		}
	}
	return $out;
}

function getPathsFromTable($table = FILE_TABLE, $db = '', $type = FILE_ONLY, $wsIDs = '', $order = 'Path', $limitCSV = '', $first = '') {
	if (!$db)
		$db = new DB_WE();
	$limitCSV = trim($limitCSV, ',');
	$q = '';
	if ($wsIDs) {
		$idArr = makeArrayFromCSV($wsIDs);
		$wsPaths = makeArrayFromCSV(id_to_path($wsIDs, $table, $db));
		$qfoo = ' ( ';
		for ($i = 0; $i < sizeof($wsPaths); $i++)
			if ((!$limitCSV) || in_workspace($idArr[$i], $limitCSV, FILE_TABLE, $db))
				$qfoo .= " Path like '" . $db->escape($wsPaths[$i]) . "%' OR ";
		if ($qfoo == ' ( ')
			$qfoo = '';
		$qfoo = preg_replace('#^(.*)OR $#', '\1', $qfoo);
		if ($qfoo)
			$qfoo .= ' ) ';
		else
			return array();
		$q .= $qfoo;
	}
	$q2 = '';
	switch ($type) {
		case FILE_ONLY :
			$q2 = ' IsFolder=0 ';
			break;
		case FOLDER_ONLY :
			$q2 = ' IsFolder=1 ';
			break;
	}
	$q3 = '';
	$out = $first ? array(
			'0' => $first
					) : array();
	$db->query('SELECT ID,Path
		FROM '.$table  . (($q || $q2 || $q3) ? '
		WHERE ' : '') . $q . (($q && $q2) ? ' AND ' : '') . $q2 . ((($q || $q2) && $q3) ? ' AND ' : '') . $q3 . '
		ORDER BY '.$order);
	while ($db->next_record())
		$out[$db->f('ID')] = $db->f('Path');
	return $out;
}

function pushChildsFromArr(&$arr, $table = FILE_TABLE, $isFolder = '') {
	$tmpArr = $arr;
	$tmpArr2 = array();
	foreach ($arr as $id){
		pushChilds($tmpArr, $id, $table, $isFolder);
	}
	foreach (array_unique($tmpArr) as $id){
		$tmpArr2[]=$id;
	}
	return $tmpArr2;
}

function pushChilds(&$arr, $id, $table = FILE_TABLE, $isFolder = '') {
	$db = new DB_WE();
	$arr[]= $id;
	$db->query('SELECT ID FROM '.$table.' WHERE ParentID=' . intval($id) . (($isFolder != '' || $isFolder == '0') ? (' AND IsFolder="' . $db->escape($isFolder) . '"') : ''));
	while ($db->next_record()){
		pushChilds($arr, $db->f('ID'), $table, $isFolder);
	}
}

function uniqueCSV($csv, $prePost = false) {
	$arr = array_unique(makeArrayFromCSV($csv));
	$foo = array();
	foreach ($arr as $v){
		$foo[]= $v;
	}
	return makeCSVFromArray($foo, $prePost);
}

function get_ws($table = FILE_TABLE, $prePostKomma = false){
	switch($table){
		case FILE_TABLE :
			$type = 0;
			break;
		case TEMPLATES_TABLE :
			$type = 1;
			break;
		case NAVIGATION_TABLE :
			$type = 3;
			break;
		case (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : -1):
			$type = 2;
			break;
		case (defined('NEWSLETTER_TABLE') ? NEWSLETTER_TABLE : -2):
			$type = 4;
			break;
		default :
			return '';
	}

	if (isset($_SESSION) && isset($_SESSION['perms'])) {

		if ($_SESSION['perms']['ADMINISTRATOR']) {
			return '';
		}
		if ($_SESSION['user']['workSpace'] && $_SESSION['user']['workSpace'] != ';') {
			$a = explode(';', $_SESSION['user']['workSpace']);
			return makeCSVFromArray(makeArrayFromCSV($a[$type]), $prePostKomma);
		}
	}
	return '';
}

function we_readParents($id, &$parentlist, $tab, $match = 'ContentType', $matchvalue = 'folder') {
	$db_temp = new DB_WE();
	$db_temp1 = new DB_WE();
	$db_temp->query('SELECT ParentID FROM '.$tab.' WHERE ID=' . intval($id));
	while ($db_temp->next_record())
		if ($db_temp->f('ParentID') == 0) {
			array_push($parentlist, $db_temp->f('ParentID'));
			break;
		} else {
			$db_temp1->query('SELECT '.$match.' FROM '.$tab.' WHERE ID=' . intval($db_temp->f('ParentID')));
			if ($db_temp1->next_record())
				if ($db_temp1->f($match) == $matchvalue) {
					array_push($parentlist, $db_temp->f('ParentID'));
					we_readParents($db_temp->f('ParentID'), $parentlist, $tab, $match, $matchvalue);
				}
		}
}

function we_readChilds($pid, &$childlist, $tab, $folderOnly = true, $where = '', $match = 'ContentType', $matchvalue = 'folder') {
	$db_temp = new DB_WE();
	$db_temp->query('SELECT ID,'.$match.' FROM '.$tab.' WHERE ' . ($folderOnly ? ' IsFolder=1 AND ' : '') . 'ParentID=' . intval($pid) . $where);
	while ($db_temp->next_record()) {
		if ($db_temp->f($match) == $matchvalue) {
			we_readChilds($db_temp->f('ID'), $childlist, $tab, $folderOnly);
		}
		array_push($childlist, $db_temp->f('ID'));
	}
}

function getWsQueryForSelector($tab, $includingFolders = true) {
	$wsQuery = '';

	if ($_SESSION['perms']['ADMINISTRATOR']) {
		return '';
	}

	if (($ws = makeArrayFromCSV(get_ws($tab)))) {
		$paths = id_to_path($ws, $tab, '', false, true);
		$wsQuery .= ' AND (';
		foreach ($paths as $path) {
			$parts = explode('/', $path);
			array_shift($parts);
			$last = array_pop($parts);
			$path = '/';
			foreach ($parts as $part) {

				$path .= $part;
				if ($includingFolders) {
					$wsQuery .= ' (Path = "' . $GLOBALS['DB_WE']->escape($path) . '") OR ';
				} else {
					$wsQuery .= ' (Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%") OR ';
				}
				$path .= '/';
			}
			$path .= $last;
			if ($includingFolders) {
				$wsQuery .= ' (Path = "' . $GLOBALS['DB_WE']->escape($path) . '" OR Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%") OR ';
			} else {
				$wsQuery .= ' (Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%") OR ';
			}
			$wsQuery .= ' (Path LIKE "' . $GLOBALS['DB_WE']->escape($path) . '/%") OR ';
		}
		$wsQuery .= ' 0 )'; // end with "OR 0"
	}
	return $wsQuery;
}

function getWsFileList($table, $childsOnly = false) {
	if ($_SESSION['perms']['ADMINISTRATOR'] || ($table != FILE_TABLE && $table != TEMPLATES_TABLE)) {
		return '';
	}
	$db = new DB_WE();
	$wsFileList = '';

	$workspaces = makeArrayFromCSV(get_ws($table));
	if (sizeof($workspaces)) {
		$childList = array();
		foreach ($workspaces as $value) {
			array_push($childList, $value);
			$myPath = id_to_path($value, $table);
			$_query = "SELECT ID FROM $table WHERE 0 ";
			if (!$childsOnly) {
				$parts = explode('/', $myPath);
				array_shift($parts);
				array_pop($parts);
				$path = '/';
				foreach ($parts as $part) {
					$path .= $part;
					$_query .= "OR PATH = '" . $db->escape($path) . "' ";
					$path .= '/';
				}
			}
			$_query .= "OR PATH LIKE '$myPath/%' OR PATH = '" . $db->escape($myPath) . "' ";
			$db->query($_query);
			while ($db->next_record()) {
				array_push($childList, $db->f('ID'));
			}
		}
		if (sizeof($wsFileList)) {
			$wsFileList = implode(',', $childList);
		}
	}
	return $wsFileList;
}

function get_def_ws($table = FILE_TABLE, $prePostKomma = false) {
	if (!get_ws($table, $prePostKomma)) { // WORKARROUND
		return '';
	}
	if ($_SESSION['perms']['ADMINISTRATOR'])
		return '';
	$ws = '';

	$foo = f('SELECT workSpaceDef FROM ' . USER_TABLE . ' WHERE ID=' . intval($_SESSION['user']['ID']),'workSpaceDef',new DB_WE());
	$ws = makeCSVFromArray(makeArrayFromCSV($foo), $prePostKomma);

	if ($ws == '') {
		$wsA = makeArrayFromCSV(get_ws($table, $prePostKomma));
		return (sizeof($wsA)?$wsA[0]:'');
	} else
		return $ws;
}

function getArrayKey($needle, $haystack) {
	if (!is_array($haystack))
		return '';
	foreach ($haystack as $i => $val) {
		if ($val == $needle) {
			return $i;
		}
	}
	return '';
}

/**
 * This function is equivalent to print_r, except that it adds addtional "pre"-headers
 * @param * $val the variable to print
 */
function p_r($val) {
	print '<pre>';
	print_r($val);
	print '</pre>';
}

/**
 * This function triggers an error, which is logged to systemlog, and if enabled to we-log. This function can take any number of variables!
 * @param string $type (optional) define the type of the log; possible values are: warning (default), error, notice, deprecated
 * Note: type error causes we to stop execution, cause this is considered a major bug; but value is still logged.
 */
function t_e($type='warning'){
	$inc=false;
	$data=array();
	switch(is_string($type)?strtolower($type):-1){
		case 'error':
			$inc=true;
			$type=E_USER_ERROR;
			break;
		case 'notice':
			$inc=true;
			$type=E_USER_NOTICE;
			break;
		case 'deprecated':
			$inc=true;
			$type=E_USER_DEPRECATED;
			break;
		case 'warning':
			$inc=true;
		default:
			$type=E_USER_WARNING;
	}
	foreach (func_get_args() as $value){
		if($inc){
			$inc=false;
			continue;
		}
		if(is_array($value)||is_object($value)){
			$data[]=print_r($value,true);
		}else{
			$data[]=$value;
		}
	}

	if(count($data)>0){
		trigger_error(implode("\n---------------------------------------------------\n",$data),$type);
	}
}


function getHrefForObject($id, $pid, $path = '', $DB_WE = '',$hidedirindex=false,$objectseourls=false) {

	if (!$path)
		$path = $_SERVER['SCRIPT_NAME'];
	if (!$DB_WE)
		$DB_WE = new DB_WE();

	if (!$id) {
		return '';
	}
	$foo = getHash('SELECT Published,Workspaces, ExtraWorkspacesSelected FROM ' . OBJECT_FILES_TABLE . ' WHERE ID=' . intval($id),$DB_WE);

	if (!$GLOBALS['we_doc']->InWebEdition) {
		// check if object is published.
		if (!$foo['Published']) {
			$GLOBALS['we_link_not_published'] = 1;
			return '';
		}
	}

	if (count($foo) == 0)
		return '';
	$showLink = false;

	if ($foo['Workspaces']) {
		if (in_workspace($pid, $foo['Workspaces'], FILE_TABLE, $DB_WE))
			$showLink = true;
		else
		if ($foo['ExtraWorkspacesSelected']) {
			if (in_workspace($pid, $foo['ExtraWorkspacesSelected'], FILE_TABLE, $DB_WE))
				$showLink = true;
		}
	}
	if ($showLink) {

		$path = getNextDynDoc($path, $pid, $foo['Workspaces'], $foo['ExtraWorkspacesSelected'], $DB_WE);
		if (!$path)
			return '';

		if (!($GLOBALS['we_editmode'] || $GLOBALS['WE_MAIN_EDITMODE']) && $hidedirindex){
			$path_parts = pathinfo($path);
			if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
				$path= ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/';
			}
		}
		if (show_SeoLinks() && $objectseourls){

			$objectdaten=getHash('SELECT  Url,TriggerID FROM '.OBJECT_FILES_TABLE." WHERE ID=" . intval($id) . " LIMIT 1", $DB_WE);
			$objecturl=$objectdaten['Url'];$objecttriggerid= $objectdaten['TriggerID'];
			if ($objecttriggerid){$path_parts = pathinfo(id_to_path($objecttriggerid));}
		} else {$objecturl='';}
		$pidstr='';
		if ($pid){
			$pidstr='?pid='.intval($pid);
			}
		if ($objectseourls && $objecturl!=''){

			if($hidedirindex && show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES))){
				return ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$objecturl . $pidstr;
			} else {
				return ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/'.$path_parts['filename'].'/'.$objecturl . $pidstr;
			}
		} else {
			return $path . '?we_objectID=' . intval($id) . str_replace('?','&amp;',$pidstr);
		}
	} else {
		if ($foo['Workspaces']) {
			$fooArr = makeArrayFromCSV($foo['Workspaces']);
			$path = id_to_path($fooArr[0], FILE_TABLE, $DB_WE);
			$path = f('SELECT Path FROM ' . FILE_TABLE . " WHERE Published > 0 AND ContentType='text/webedition' AND IsDynamic=1 AND Path like '" . $DB_WE->escape($path) . "%'","Path",$DB_WE);
			return ($path ? $path . '?we_objectID=' . intval($id) . '&pid=' . intval($pid) :'');
		}
	}
	return '';
}

function getNextDynDoc($path, $pid, $ws1, $ws2, $DB_WE = '') {
	if (!$DB_WE)
		$DB_WE = new DB_WE();
	if (f('
		SELECT IsDynamic
		FROM ' . FILE_TABLE . "
		WHERE Path='" . $DB_WE->escape($path) . "'", 'IsDynamic', $DB_WE)) {
		return $path;
	}
	$arr1 = makeArrayFromCSV(id_to_path($ws1, FILE_TABLE, $DB_WE));
	$arr2 = makeArrayFromCSV(id_to_path($ws2, FILE_TABLE, $DB_WE));
	$arr3 = makeArrayFromCSV($ws1);
	$arr4 = makeArrayFromCSV($ws2);
	foreach ($arr1 as $i => $ws)
		if (in_workspace($pid, $arr3[$i])) {
			$path = f(
											'
				SELECT Path
				FROM ' . FILE_TABLE . "
				WHERE Published > 0 AND ContentType='text/webedition' AND IsDynamic=1 AND Path like '" . $DB_WE->escape($ws) . "%'",
											'Path',
											$DB_WE);
			if ($path)
				return $path;
		}
	foreach ($arr2 as $i => $ws)
		if (in_workspace($pid, $arr4[$i])) {
			return f('SELECT Path FROM ' . FILE_TABLE . ' WHERE Published > 0 AND ContentType="text/webedition" AND IsDynamic=1 AND Path like "' . $DB_WE->escape($ws) . '%"','Path',$DB_WE);
		}
	return '';
}

function parseInternalLinks(&$text, $pid, $path = '') {
	$DB_WE = new DB_WE();

	if (preg_match_all('/(href|src)="document:(\d+)("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)) {

		foreach ($regs as $reg) {

			$_path = f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($reg[2]).(isset($GLOBALS['we_doc']->InWebEdition) && $GLOBALS['we_doc']->InWebEdition ? '':' AND Published > 0'), 'Path',$DB_WE);

			if ($_path) {
				$path_parts = pathinfo($_path);
				if(show_SeoLinks() && defined('WYSIWYGLINKS_DIRECTORYINDEX_HIDE') && WYSIWYGLINKS_DIRECTORYINDEX_HIDE && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
					$_path = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/';
				}
				$text = str_replace($reg[1] . '="document:' . $reg[2] . $reg[3],
							$reg[1] . '="' . $_path . $reg[3],
							$text);
			} else {
				$text = preg_replace('|<a [^>]*href="document:' . $reg[2] . '"[^>]*>(.*)</a>|Ui', '\1', $text);
				$text = preg_replace('|<a [^>]*href="document:' . $reg[2] . '"[^>]*>|Ui', '', $text);
				$text = preg_replace('|<img [^>]*src="document:' . $reg[2] . '"[^>]*>|Ui', '', $text);
			}
		}
	}
	if (preg_match_all('/src="thumbnail:([^" ]+)"/i', $text, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			list($imgID, $thumbID) = explode(",", $reg[1]);
			$thumbObj = new we_thumbnail();
			if ($thumbObj->initByImageIDAndThumbID($imgID, $thumbID)) {
				$text = str_replace('src="thumbnail:' . $reg[1] . '"',
												'src="' . $thumbObj->getOutputPath() . '"',
												$text);
			} else {
				$text = preg_replace('|<img[^>]+src="thumbnail:' . $reg[1] . '[^>]+>|Ui', '', $text);
			}
		}
	}
	if (defined('OBJECT_TABLE')) {
		if (preg_match_all('/href="object:(\d+)(\??)("|[^"]+")/i', $text, $regs, PREG_SET_ORDER)) {
			$hidedirindex = defined('WYSIWYGLINKS_DIRECTORYINDEX_HIDE') && WYSIWYGLINKS_DIRECTORYINDEX_HIDE;
			$objectseourls = defined('WYSIWYGLINKS_OBJECTSEOURLS') && WYSIWYGLINKS_OBJECTSEOURLS;
			foreach ($regs as $reg) {
				$href = getHrefForObject($reg[1], $pid, $path,"",$hidedirindex,$objectseourls);
				if (isset($GLOBALS["we_link_not_published"])) {
					unset($GLOBALS["we_link_not_published"]);
				}
				if ($href) {
					if ($reg[2] == "?") {
						$text = str_replace('href="object:' . $reg[1] . "?",'href="' . $href . "&amp;",$text);
					} else {
						$text = str_replace('href="object:' . $reg[1] . $reg[2] . $reg[3],
														'href="' . $href . $reg[2] . $reg[3],
														$text);
					}
				} else {
					$text = preg_replace('|<a [^>]*href="object:' . $reg[1] . '"[^>]*>(.*)</a>|Ui','\1',$text);
					$text = preg_replace('|<a [^>]*href="object:' . $reg[1] . '"[^>]*>|Ui', '', $text);
				}
			}
		}
	}
	$suchmuster = '/\<a>(.*)\<\/a>/siU';
	$ersetzung = '\1';

	$text = preg_replace($suchmuster, $ersetzung, $text);

	return $text;
}

function removeHTML($val) {
	$val = preg_replace('%<br ?/?>%i', '###BR###',
					str_replace(array('<?','?>'),
						array('###?###','###/?###'),$val));
	$val=preg_replace('/<[^><]+>/', '',$val);
	return str_replace(array('###BR###','###?###','###/?###'),
		array('<br/>','<?','?>'), $val);
}

function removePHP($val) {
	$val = str_replace(array('<?','?>'), '', $val);
	return preg_replace('|<script +language[^p]+php[^>]*>|si', '', $val);
}

function getMysqlVer($nodots = true) {
	$DB_WE = new DB_WE();
	$res=f('SELECT VERSION() AS Version','Version',$DB_WE);

	if ($res) {
		$res = explode('-', $res);
	} else {
		$res=f('SHOW VARIABLES LIKE "version"','Value',$DB_WE);
		if ($res) {
			$res = explode('-', $res);
		}
	}
	if (isset($res)) {
		if ($nodots) {
			$strver = substr(str_replace('.', '', $res[0]), 0, 4);

			$ver = (int) $strver;
			if (strlen($ver) < 4) {
				$ver = sprintf('%04d', $ver);
				if (substr($ver, 0, 1) == '0')
					$ver = (int) (substr($ver, 1) . '0');
			}

			return $ver;
		} else {
			return $res[0];
		}
	}
	return '';
}

function we_mail($recipient, $subject, $txt, $from = '') {
	if (runAtWin () && $txt){
			$txt = str_replace("\n", "\r\n", $txt);
	}

	$phpmail = new we_util_Mailer($recipient, $subject, $from);
	$phpmail->setCharSet($GLOBALS['WE_BACKENDCHARSET']);
	$phpmail->addTextPart(trim($txt));
	$phpmail->buildMessage();
	$phpmail->Send();
}

function runAtWin() {
	return stripos(PHP_OS,'win')!==false && (stripos(PHP_OS,'darwin')===false);
}

function weMemDebug() {
	print("Mem usage " . round(((memory_get_usage() / 1024)  / 1024), 3) . ' MiB');
}

function weGetCookieVariable($name) {
	$c = isset($_COOKIE['we' . session_id()]) ? $_COOKIE['we' . session_id()] : '';
	$vals = array();
	if ($c) {
		$parts = explode('&', $c);
		foreach ($parts as $p) {
			$foo = explode('=', $p);
			$vals[rawurldecode($foo[0])] = rawurldecode($foo[1]);
		}
		return (isset($vals[$name]) ? $vals[$name] : '');
	}
	return '';
}

function getContentTypeFromFile($dat) {
	if (is_dir($dat)) {
		return 'folder';
	} else {
		$ext = strtolower(preg_replace('#^.*(\..+)$#', '\1', $dat));
		if ($ext) {
			$ct= new we_base_ContentTypes();
			$type=$ct->getTypeForExtension($ext);
			if($type){
				return $type;
			}
		}
	}
	return 'application/*';
}

function getUploadMaxFilesize($mysql = false, $db = '') {

	$post_max_size = we_convertIniSizes(ini_get('post_max_size'));
	$upload_max_filesize = we_convertIniSizes(ini_get('upload_max_filesize'));

	if (!defined('WE_MAX_UPLOAD_SIZE') || WE_MAX_UPLOAD_SIZE == 0) {

		if ($mysql) {
			return min($post_max_size, $upload_max_filesize, getMaxAllowedPacket($db));
		} else {
			return min($post_max_size, $upload_max_filesize);
		}
	} else {
		return WE_MAX_UPLOAD_SIZE * 1024 * 1024;
	}
}

function getMaxAllowedPacket($db = '') {
	if (!$db) {
		$db = new DB_WE();
	}
	return f('SHOW VARIABLES LIKE "max_allowed_packet"','Value',$db);
}

function we_convertIniSizes($in) {
	if (preg_match('#^([0-9]+)M$#i', $in, $regs)) {
		return 1024 * 1024 * intval($regs[1]);
	} else
	if (preg_match('#^([0-9]+)K$#i', $in, $regs)) {
		return 1024 * intval($regs[1]);
	} else {
		return intval($in);
	}
}

function we_getDocumentByID($id, $includepath = '', $db = '', $charset = '') {
	if (!$db) {
		$db = new DB_WE();
	}
	// look what document it is and get the className
	$clNm = f('SELECT ClassName FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), 'ClassName', $db);

	// init Document
	if (isset($GLOBALS['we_doc'])) {
		$backupdoc = $GLOBALS['we_doc'];
	}

	$GLOBALS['we_doc'] = new $clNm();

	$GLOBALS['we_doc']->initByID($id, FILE_TABLE, we_class::LOAD_MAID_DB);
	$content = $GLOBALS['we_doc']->i_getDocument($includepath);
	$charset = $GLOBALS['we_doc']->getElement('Charset');
	if (!$charset) {
		$charset = 'UTF-8';
	}

	if (isset($backupdoc)) {
		$GLOBALS['we_doc'] = $backupdoc;
	}
	return $content;
}

function we_getObjectFileByID($id, $includepath = '') {
	$mydoc = new we_objectFile();
	$mydoc->initByID($id, OBJECT_FILES_TABLE, we_class::LOAD_MAID_DB);
	return $mydoc->i_getDocument($includepath);
}

/**
 * @return str
 * @param bool $slash
 * @desc returns the protocol, the webServer is running, http or https, when slash is true - :// is added to protocol
 */
function getServerProtocol($slash = false) {
	return (we_isHttps()?'https':'http').($slash?'://':'');
}

function getServerAuth(){
		$pwd = rawurlencode(defined('HTTP_USERNAME') ? HTTP_USERNAME : (isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:'')) . ':' .
			rawurlencode(defined('HTTP_PASSWORD') ? HTTP_PASSWORD : (isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:'')) . '@';
		return (strlen($pwd)>3)?$pwd:'';
}

function getServerUrl($useUserPwd=false){
	$port='';
	if(isset($_SERVER['SERVER_PORT'])){
		if((we_isHttps() && $_SERVER['SERVER_PORT']!=443) || ($_SERVER['SERVER_PORT']!=80)){
			$port=':'.$_SERVER['SERVER_PORT'];
		}
	}
	if($useUserPwd){
		$pwd = getServerAuth();
	}
	return getServerProtocol(true) . ($useUserPwd && strlen($pwd) > 3 ? $pwd : '') . $_SERVER['SERVER_NAME'] . $port;
}

function we_check_email($email) {	 // Zend validates only the pure address
	$email = html_entity_decode($email);
	$namePart[0] = '';
	if (preg_match('/<(.)*>/', $email, $_email)) {
		$namePart = substr($email, 0, strpos($email, '<'));
		$namePart = preg_replace('/"(.)*"/', "x", $namePart);
		$namePart = preg_replace('/\\\\(.)/', "y", $namePart);
		if (strpos($namePart, '"'))
			return false;
		$email = substr($_email[0], 1, strlen($_email[0]) - 2);
	}

	$validator = new Zend_Validate_EmailAddress();
	return $validator->isValid($email);
}

function getRequestVar($name, $default, $yescode = '', $nocode = '') {
	if (isset($_REQUEST[$name])) {
		if ($yescode != ''){
			eval($yescode);
		}
		return $_REQUEST[$name];
	} else {
		if ($nocode != ''){
			eval($nocode);
		}
		return $default;
	}
}

/**
 * Converts a given number in a via array specified system.
 * as default a number is converted in the matching chars 0->^,1->a,2->b, ...
 * other systems can simply set via the parameter $chars for example -> array(0,1)
 * for bin-system
 *
 * @return string
 * @param int $value
 * @param array[optional] $chars
 * @param string[optional] $str
 */
function number2System($value, $chars = array(), $str = '') {

	if (!(is_array($chars) && sizeof($chars) > 1)) { //	in case of error take default-array
		$chars = array('^','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
	}
	$base = sizeof($chars);

	//	get some information about the numbers:
	$_rest = $value % $base;
	$_result = ($value - $_rest) / $base;

	//	1. Deal with the rest
	$str = $chars[$_rest] . $str;

	//	2. Deal with remaining result
	return ($_result > 0 ? number2System($_result, $chars, $str) : $str);
}


/**
 * This function returns preference for given name; Checks first the users preferences and then global
 *
 * @param          string                                  $name
 *
 * @see            getAllGlobalPrefs()
 *
 * @return         string
 */
function getPref($name) {
	if (isset($_SESSION['prefs'][$name])) {
		return $_SESSION['prefs'][$name];
	} else {
		$file_name = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_global.inc.php';
		$parser = weConfParser::getConfParserByFile($file_name);
		$all = $parser->getData();
		return isset($all[$name]) ? $all[$name] : '';
	}
}

/**
 * The function saves the user pref in the session and the database; The function works with user preferences only
 *
 * @param          string                                  $name
 * @param          string                                  $value
 *
 * @see            setUserPref()
 *
 * @return         boolean
 */
function setUserPref($name, $value) {
	if (isset($_SESSION['prefs'][$name]) && isset($_SESSION['prefs']['userID']) && $_SESSION['prefs']['userID']) {
		$_SESSION['prefs'][$name] = $value;
		$_db = new DB_WE();
		$_db->query('UPDATE ' . PREFS_TABLE . ' SET ' . $name . '="' . $_db->escape($value) . '" WHERE userId=' . intval($_SESSION['prefs']['userID']));
		return true;
	}
	return false;
}

/**
 * This function creates the given path in the repository and returns the id of the last created folder
 *
 * @param          string				$path
 * @param          string				$table
 * @param          array				$pathids
 *
 * @return         string
 */
function makePath($path, $table, &$pathids, $owner = 0) {
	$path = str_replace('\\', '/', $path);
	$patharr = explode('/', $path);
	$mkpath = '';
	$pid = 0;
	$ids = array();
	foreach ($patharr as $elem) {
		if ($elem != '' && $elem != '/') {
			$mkpath .= '/' . $elem;
			$id = path_to_id($mkpath, $table);
			if (!$id) {
				$new = new we_folder();
				$new->Text = $elem;
				$new->Filename = $elem;
				$new->ParentID = $pid;
				$new->Path = $mkpath;
				$new->Table = $table;
				$new->CreatorID = $owner;
				$new->ModifierID = $owner;
				$new->Owners = ',' . $owner . ',';
				$new->OwnersReadOnly = serialize(array(
										$owner => 0
								));
				$new->we_save();
				$id = $new->ID;
				$pathids[] = $id;
			}
			$pid = $id;
		}
	}

	return $pid;
}

/**
 * This function clears path from double slashes and back slashes
 *
 * @param          string                                  $path
 *
 *
 * @return         string
 */
function clearPath($path) {
	return preg_replace('#/+#', '/', str_replace('\\', '/', $path));
}

/**
 * @return	string
 * @param	string $element
 * @param	[opt]array $attribs
 * @param	[opt]string $content
 * @param	[opt]boolean $forceEndTag=false
 * @desc	returns the html element with the given attribs.attr[pass_*] is replaced by "*" to loop some
 *          attribs through the tagParser.
 */
function getHtmlTag($element, $attribs = array(), $content = '', $forceEndTag = false, $onlyStartTag = false) {

	//	default at the moment is xhtml-style
	$_xmlClose = false;

	//	take values given from the tag - later from preferences.
	$xhtml = (defined('XHTML_DEFAULT') && XHTML_DEFAULT == 1) ? true : false;

	if (isset($attribs['xml']) && $attribs['xml']) {
		$xhtml = ($attribs['xml'] == 'true' || $attribs['xml'] == 'on' || $attribs['xml'] == 'xml' || $attribs['xml'] == 1) ? true : false;
	}

	// at the moment only transitional is supported
	$xhtmlType = (isset($attribs['xmltype']) ? $attribs['xmltype'] : 'transitional');

	//	remove x(ht)ml-attributs
	$attribs = removeAttribs($attribs, array(
							'xml', 'xmltype','to','nameto'
					));
	if ($element =='img' && defined('HIDENAMEATTRIBINWEIMG_DEFAULT') && HIDENAMEATTRIBINWEIMG_DEFAULT && !$GLOBALS['WE_MAIN_DOC']->InWebEdition){
		$attribs = removeAttribs($attribs, array('name'));
	}
	if ($element =='form' && defined('HIDENAMEATTRIBINWEFORM_DEFAULT') && HIDENAMEATTRIBINWEFORM_DEFAULT && !$GLOBALS['WE_MAIN_DOC']->InWebEdition){
		$attribs = removeAttribs($attribs, array('name'));
	}
	if ($xhtml) { //	xhtml, check if and what we shall debug
		$_xmlClose = true;

		if (defined('XHTML_DEBUG') && XHTML_DEBUG) { //  check if XHTML_DEBUG is activated - system pref
			include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/validation/xhtml.inc.php');

			$showWrong = (isset($_SESSION['prefs']['xhtml_show_wrong']) && $_SESSION['prefs']['xhtml_show_wrong'] && isset(
											$GLOBALS['we_doc']) && $GLOBALS['we_doc']->InWebEdition); //  check if XML_SHOW_WRONG is true (user) - only in webEdition
			$removeWrong = (defined('XHTML_REMOVE_WRONG') && XHTML_REMOVE_WRONG); //  check if XML_REMOVE_WRONG is true (constant)


			validateXhtmlAttribs($element, $attribs, $xhtmlType, $showWrong, $removeWrong);
		}
	}

	$_tag = '<'.$element;

	foreach ($attribs as $k => $v) {
		if ($k == 'link_attribute') {// Bug #3741
			$_tag .= ' ' . $v;
		} else {
			$_tag .= ' ' . str_replace('pass_', '', $k) . "=\"$v\"";
		}
	}
	if ($content != '' || $forceEndTag) { //	use endtag
		$_tag .= '>'.$content.'</'.$element.'>';
	} else { //	xml style or not
		$_tag .= ( ($_xmlClose && !$onlyStartTag) ? ' />' : '>');
	}
	return $_tag;
}

/**
 * @return array
 * @param array $attribs
 * @param array $remove
 * @desc removes all entries of $attribs, where the key from attribs is in values of $remove
 */
function removeAttribs($attribs, $remove = array()) {
	array_push($remove, 'user');

	foreach ($remove as $r) {
		if (array_key_exists($r, $attribs)) {
			unset($attribs[$r]);
		}
	}
	return $attribs;
}

/**
 * @return array
 * @param array $atts
 * @param array $ignore
 * @desc Removes all empty values from assoc array without the in $ignore given
 */
function removeEmptyAttribs($atts, $ignore = array()) {
	foreach ($atts as $k => $v) {
		if ($v == '' && !in_array($k, $ignore)) {
			unset($atts[$k]);
		}
	}
	return $atts;
}

/**
 * @return array
 * @param array $atts
 * @param array $ignore
 * @desc only uses the attribs given in the array use
 */
function useAttribs($atts, $use = array()) {

	foreach ($atts as $k => $v) {
		if (!in_array($k, $use)) {
			unset($atts[$k]);
		}
	}
	return $atts;
}

/**
 * This function works in very same way as the standard array_splice function
 * except the second parametar is the array index and not just offset
 * The functions modifies the array that has been passed by reference as the first function parametar
 *
 * @param          array                                  $a
 * @param          interger                                $start
 * @param          integer                                 $len
 *
 *
 * @return         none
 */
function new_array_splice(&$a, $start, $len = 1) {
	$ks = array_keys($a);
	$k = array_search($start, $ks);
	if ($k !== false) {
		$ks = array_splice($ks, $k, $len);
		foreach ($ks as $k)
			unset($a[$k]);
	}
}

/**
 * This function works oposit to htmlentities function
 *
 * @param          array                                  $code
 *
 *
 * @return         string
 */
function rhtmlentities($code) {
	$table = get_html_translation_table(HTML_ENTITIES);
	$rtable = array_flip($table);
	return strtr($code, $rtable);
}

/**
 * Returns number od days for given month
 *
 * @param          int                                  $month
 * @param          int                                  $year
 *
 *
 * @return         int
 */
function getNumberOfDays($month, $year) {
	switch ($month) {
		case 1:
		case 3:
		case 5:
		case 7:
		case 8:
		case 10:
		case 12:
			return '31';
		case 2:
			return ($year % 4) == 0 ? '29' : '28';
		default:
			return '30';
	}
}

/**
 * Returns "where query" for Doctypes depending on which workspace the user have
 *
 * @param	object	$db
 *
 *
 * @return         string
 */
function getDoctypeQuery($db = '') {
	if (!$db) {
		$db = new DB_WE();
	}

	$paths = array();
	$ws = get_ws(FILE_TABLE);
	if ($ws) {
		$b = makeArrayFromCSV($ws);
		if ((!defined('WE_DOCTYPE_WORKSPACE_BEHAVIOR')) || WE_DOCTYPE_WORKSPACE_BEHAVIOR == 0) {
			foreach ($b as $k => $v) {
				$db->query('SELECT ID,Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($v));
				while ($db->next_record()) {
					array_push(
									$paths,
									"(ParentPath = '" . $db->escape($db->f("Path")) . "' || ParentPath like '" . $db->escape($db->f("Path")) . "/%')");
				}
			}
			if (is_array($paths) && count($paths) > 0) {
				return 'WHERE (' . implode(' OR ', $paths) . ' OR ParentPath="") ORDER BY DocType';
			}
		} else {
			foreach ($b as $k => $v) {
				$_tmp_path = id_to_path($v);
				while ($_tmp_path && $_tmp_path != '/') {
					array_push($paths, '"' . $db->escape($_tmp_path) . '"');
					$_tmp_path = dirname($_tmp_path);
				}
			}
			if (is_array($paths) && count($paths) > 0) {
				return 'WHERE ParentPath IN (' . implode(',', $paths) . ',"")  ORDER BY DocType';
			}
		}
	}
	if (is_array($paths) && count($paths) > 0) {
		$q = "WHERE ((" . implode(" OR ", $paths) . ") OR ParentPath='') ORDER BY DocType";
	} else $q=' ORDER BY DocType';

	return '';
}

/**
 * Makes a relative path from an absolute path
 *
 * @param	string	$docpath Absolute Path of document
 * @param	string	$linkpath Absolute Path of link (href or src)
 *
 * @return         string
 */
function makeRelativePath($docpath, $linkpath) {
	$parentPath = $docpath;
	$newLinkPath = '';

	while ($parentPath != substr($linkpath, 0, strlen($parentPath))) {
		$parentPath = dirname($parentPath);
		$newLinkPath .= '../';
	}
	$rest = substr($linkpath, strlen($parentPath));
	if (substr($rest, 0, 1) == '/') {
		$rest = substr($rest, 1);
	}
	return $newLinkPath . $rest;
}

function we_loadLanguageConfig() {

	$file = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php';
	if (!file_exists($file) || !is_file($file)) {
		if (WE_LANGUAGE == 'Deutsch' || WE_LANGUAGE == 'Deutsch_UTF-8') {
			we_writeLanguageConfig('de_DE', array(
					'de_DE', 'en_GB'
			));
		} else {
			we_writeLanguageConfig('en_GB', array(
					'de_DE', 'en_GB'
			));
		}
	}
	include_once ($file);
}
function getWeFrontendLanguagesForBackend(){
	$la = array();
	$targetLang = we_core_Local::weLangToLocale($GLOBALS['WE_LANGUAGE']);
	foreach($GLOBALS["weFrontendLanguages"] as $Locale){
		$temp = explode('_', $Locale);
		if (sizeof($temp) == 1) {
			$la[$Locale] =  CheckAndConvertISObackend(Zend_Locale::getTranslation($temp[0],'language',$targetLang) . ' '.$Locale) ;
		} else {
			$la[$Locale] =  CheckAndConvertISObackend(Zend_Locale::getTranslation($temp[0],'language',$targetLang).' ('.Zend_Locale::getTranslation($temp[1],'territory',$targetLang).') ' .$Locale) ;
		}
	}
	return $la;
}
function we_writeLanguageConfig($default, $available = array()) {

	$locales = '';
	sort($available);
	foreach ($available as $Locale) {
		$temp = explode('_', $Locale);
		if (sizeof($temp) == 1) {
			$locales .= "	'" . $Locale . "',\n";
		} else {
			$locales .= "	'" . $Locale . "',\n";
		}
	}

	$code = '<?php

/**
 * webEdition CMS
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

$GLOBALS["weFrontendLanguages"] = array(
' . $locales . '
);

$GLOBALS["weDefaultFrontendLanguage"] = "' . $default . '";
';

	$file = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/conf/we_conf_language.inc.php';
	$fh = fopen($file, 'w+');
	if (!$fh) {
		return false;
	}
	fputs($fh, $code);
	return fclose($fh);
}

function getObjectsForDocWorkspace($id) {
	$ids = (is_array($id))? $id : array($id);
	$db = new DB_WE();

	if (!defined('OBJECT_FILES_TABLE')) {
		return array();
	}

	$where = array();
	foreach ($ids as $id) {
		$where[] = 'Workspaces LIKE "%,' . $id . ',%"';
		$where[] = 'ExtraWorkspaces LIKE "%,' . $id . ',%"';
	}

	$out = array();
	$db->query('SELECT ID,Path FROM ' . OBJECT_FILES_TABLE . ' WHERE ' . implode(' OR ', $where));

	while ($db->next_record()) {
		$out[$db->f('ID')] = $db->f('Path');
	}

	return $out;
}

function we_filenameNotValid($filename) {
	if (substr($filename, 0, 2) === '..') {
		return true;
	}
	return preg_match('#[^a-z0-9._-]#i', $filename);
}

function we_isHttps() {
	return isset($_SERVER['HTTPS']) && (strtoupper($_SERVER['HTTPS']) == 'ON' || $_SERVER['HTTPS'] == 1);
}

//check if number is positive
function pos_number($val) {
	return abs($val)==$val && $val>0;
}

function convertCharsetEncoding($fromC, $toC, $string) {
	if ($fromC != '' && $toC != '') {
		if (function_exists('iconv')) {
			$string = iconv($fromC, $toC . '//TRANSLATE', $string);
		} elseif ($fromC == 'UTF-8' && $toC == 'ISO-8859-1') {
			$string = utf8_decode($string);
		} elseif ($fromC == 'ISO-8859-1' && $toC == 'UTF-8') {
			$string = utf8_encode($string);
		}
	}
	return $string;
}

function isSerialized($str) {
	return ($str == serialize(false) || @unserialize($str) !== false);
}

function AAcorrectSerDataISOtoUTF($serialized) {
	return preg_replace_callback('!(?<=^|;)s:(\d+)(?=:"(.*?)";(?:}|a:|s:|b:|i:|o:|N;))!s', 'serialize_fix_callback', $serialized);
}

function serialize_fix_callback($match) {
	return 's:' . strlen($match[2]);
}

function correctSerDataISOtoUTF($serial_str) {
	$out = preg_replace('!s:(\d+):"(.*?)";!se', '"s:".strlen("$2").":\"$2\";"', $serial_str);
	return $out;
}

function convertExactCharsetString($fromC, $toC, $string) {
	return ($string == $fromC ? $toC : $string);
}

function convertCharsetString($fromC, $toC, $string) {
	return str_replace($fromC, $toC, $string);
}



function getVarArray($arr, $string) {
	if (!isset($arr)) {
		return false;
	}
	preg_match_all('/\[([^\]]*)\]/', $string, $arr_matches, PREG_PATTERN_ORDER);
	$return = $arr;
	foreach ($arr_matches[1] as $dimension) {
		if (isset($return[$dimension])) {
			$return = $return[$dimension];
		} else {
			return false;
		}
	}
	return $return;
}


function CheckAndConvertISOfrontend($utf8data) {
	if (isset($GLOBALS['CHARSET']) && $GLOBALS['CHARSET'] != '' && $GLOBALS['CHARSET'] != 'UTF-8') {
		return iconv('UTF-8', $GLOBALS['CHARSET'] , $utf8data);
	} else {
		return $utf8data;
	}
}

function CheckAndConvertISObackend($utf8data) {
	if ($GLOBALS["WE_BACKENDCHARSET"]!='UTF-8') {
		return mb_convert_encoding($utf8data, $GLOBALS["WE_BACKENDCHARSET"], 'UTF-8');
	} else {
		return $utf8data;
	}
}

/**internal function - do not call */
function g_l_encodeArray($tmp){
	$charset = (isset($_SESSION['user'])&&isset($_SESSION['user']['isWeSession']) ? $GLOBALS['WE_BACKENDCHARSET'] : (isset($GLOBALS['CHARSET'])? $GLOBALS['CHARSET']:$GLOBALS['WE_BACKENDCHARSET']));
	return (is_array($tmp)?
					array_map('g_l_encodeArray',$tmp):
					mb_convert_encoding($tmp, $charset, 'UTF-8'));
}

/**
 * getLanguage property
 *  Note: underscores in name are used as directories - modules_workflow is searched in subdir modules
 * usage example: echo g_l('modules_workflow','[test][new]');
 *
 * @param $name name of the variable, without 'l_', this name is also used for inclusion
 * @param $specific the array element to access
 * @param $useEntities if set, return the string as html-entities
 */
function g_l($name, $specific, $omitErrors=false) {
	$charset = (isset($_SESSION['user'])&&isset($_SESSION['user']['isWeSession']) ? $GLOBALS['WE_BACKENDCHARSET'] : (isset($GLOBALS['CHARSET'])? $GLOBALS['CHARSET']:$GLOBALS['WE_BACKENDCHARSET']) );
	//cache last accessed lang var
	static $cache = array();
	//echo $name.$specific;
	if(isset($cache["l_$name"])){
		$tmp = getVarArray($cache["l_$name"], $specific);
		if (!($tmp === false)) {
			return ($charset != 'UTF-8'?
								(is_array($tmp)?
									array_map('g_l_encodeArray',$tmp):
									mb_convert_encoding($tmp, $charset, 'UTF-8')
								):
							$tmp);
		}
	}else{
		//FIXME: decide if in we - then turn off, else turn on
/*		if(isset($GLOBALS['WE_MAIN_DOC']) && (!$GLOBALS['WE_MAIN_DOC']->InWebEdition) && isset($cache)){
			unset($cache);
		}*/
	}
	$file = $_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_language/' . $GLOBALS['WE_LANGUAGE'] . '/'.str_replace('_','/',$name).'.inc.php';
	if (file_exists($file)) {
		include($file);
		$tmp = (isset(${"l_$name"})?getVarArray(${"l_$name"}, $specific):false);
		//get local variable
		if($tmp !== false){
			$cache["l_$name"]=${"l_$name"};
			return ($charset!='UTF-8'?
								(is_array($tmp)?
									array_map('g_l_encodeArray',$tmp):
									mb_convert_encoding($tmp, $charset, 'UTF-8')
									):
								$tmp);
		}else{
				if(!$omitErrors){
					trigger_error('Requested lang entry l_'.$name.$specific.' not found in '.$file.' !',E_USER_WARNING);
				}
			return false;
		}
	}
	//t_e('warning','Requested lang file '.$file.' not found!');
	return '';
}

function we_templateInit(){
	if(!isset($GLOBALS['DB_WE'])){
		$GLOBALS['DB_WE'] = new DB_WE;
	}
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/lib/we/core/autoload.php');
	if($GLOBALS['we_doc']){
		$GLOBALS['WE_DOC_ID'] = $GLOBALS['we_doc']->ID;
		if(!isset($GLOBALS['WE_MAIN_ID'])) $GLOBALS['WE_MAIN_ID'] = $GLOBALS['we_doc']->ID;
		if(!isset($GLOBALS['WE_MAIN_DOC'])) $GLOBALS['WE_MAIN_DOC'] = clone($GLOBALS['we_doc']);
		if(!isset($GLOBALS['WE_MAIN_DOC_REF'])) $GLOBALS['WE_MAIN_DOC_REF'] = &$GLOBALS['we_doc'];
		if(!isset($GLOBALS['WE_MAIN_EDITMODE'])) $GLOBALS['WE_MAIN_EDITMODE'] = isset($GLOBALS['we_editmode']) ? $GLOBALS['we_editmode'] : '';
		$GLOBALS['WE_DOC_ParentID'] = $GLOBALS['we_doc']->ParentID;
		$GLOBALS['WE_DOC_Path'] = $GLOBALS['we_doc']->Path;
		$GLOBALS['WE_DOC_IsDynamic'] = $GLOBALS['we_doc']->IsDynamic;
		$GLOBALS['WE_DOC_FILENAME'] = $GLOBALS['we_doc']->Filename;
		$GLOBALS['WE_DOC_Category'] = isset($GLOBALS['we_doc']->Category) ? $GLOBALS['we_doc']->Category : '';
		$GLOBALS['WE_DOC_EXTENSION'] = $GLOBALS['we_doc']->Extension;
		$GLOBALS['TITLE'] = $GLOBALS['we_doc']->getElement('Title');
		$GLOBALS['KEYWORDS'] = $GLOBALS['we_doc']->getElement('Keywords');
		$GLOBALS['DESCRIPTION'] = $GLOBALS['we_doc']->getElement('Description');
		$GLOBALS['CHARSET'] = $GLOBALS['we_doc']->getElement('Charset');
		list($__lang) = explode('_',$GLOBALS['we_doc']->Language);
		if ($__lang) {
			$__parts = explode('_', $GLOBALS['WE_LANGUAGE']);
			$__last = array_pop($__parts);
			// Charset of page is not UTF-8 but languge files of page are UTF-8
			// Then change language files to non UTF-8 pedant if available
			if (count($__parts) && $__last === 'UTF-8' && $GLOBALS['CHARSET'] !== 'UTF-8') {
				$__lang = $__parts[0];
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_language/'.$__lang)) {
					$GLOBALS['WE_LANGUAGE'] = $__lang;
				}

				// Charset of page is  UTF-8 but languge files of page are not UTF-8
			// Then change language files to UTF-8 pedant if available
			} else if ($__last !== 'UTF-8' && $GLOBALS['CHARSET'] === 'UTF-8') {
				$__lang = $GLOBALS['WE_LANGUAGE'] . '_UTF-8';
				if (file_exists($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_language/'.$__lang)) {
					$GLOBALS['WE_LANGUAGE'] = $__lang;
				}
			}
		}
	}
}

function we_templateHead(){
	if(isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'] ){
		print STYLESHEET_BUTTONS_ONLY . SCRIPT_BUTTONS_ONLY;
		print we_html_element::jsScript(JS_DIR.'windows.js');
		include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_editors/we_editor_script.inc.php');
	} else if(defined('WE_ECONDA_STAT') && defined('WE_ECONDA_PATH') && WE_ECONDA_STAT  && WE_ECONDA_PATH !='' && !$GLOBALS['we_doc']->InWebEdition) {
		include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/weTracking/econda/weEcondaImplementHeader.inc.php');
	}
}

function we_templatePreContent(){
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		print '<form name="we_form" method="post" onsubmit="return false;">';
		print $GLOBALS['we_doc']->pHiddenTrans();
	}
}

function we_templatePostContent(){
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		print '</form>';
	} else if(defined('WE_ECONDA_STAT') && defined('WE_ECONDA_PATH') && WE_ECONDA_STAT  && WE_ECONDA_PATH !='' && !$GLOBALS['we_doc']->InWebEdition) {
		include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/weTracking/econda/weEcondaImplement.inc.php');
	}
}

function we_templatePost(){
	if(isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode'] ){
		print '<script  type="text/javascript">setTimeout("doScrollTo();",100);</script>';
	}
	if(defined('DEBUG_MEM')){
		weMemDebug();
	}
}

function show_SeoLinks(){
	if (defined('SEOINSIDE_HIDEINWEBEDITION') && SEOINSIDE_HIDEINWEBEDITION && $GLOBALS['WE_MAIN_DOC']->InWebEdition){
		return false;
	} else if (defined('SEOINSIDE_HIDEINEDITMODE') && SEOINSIDE_HIDEINEDITMODE && (isset($GLOBALS['we_editmode']) && ($GLOBALS['we_editmode']) || (isset($GLOBALS['WE_MAIN_EDITMODE']) && $GLOBALS['WE_MAIN_EDITMODE']))){
		return false;
	}
	return true;

}

function we_cmd_enc($str) {
	return ($str==''?'':'WECMDENC_' . urlencode(base64_encode($str)));
}

function we_cmd_dec($no,$default='') {
	if (isset($_REQUEST['we_cmd'][$no])){
		if(strpos($_REQUEST['we_cmd'][$no], 'WECMDENC_') !== false) {
			$_REQUEST['we_cmd'][$no] = base64_decode(urldecode(substr($_REQUEST['we_cmd'][$no], 9)));
		}
		return $_REQUEST['we_cmd'][$no];
	}
	return $default;
}
