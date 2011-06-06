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

function we_tag_href($attribs, $content){
	// Define globals
	global $we_editmode;

	if ($we_editmode) {
		// Include files
		include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_button.inc.php");
		include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_classes/html/we_forms.inc.php");
	}

	$foo = attributFehltError($attribs, "name", "href");
	if ($foo)
		return $foo;
	$name = we_getTagAttribute("name", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$include = we_getTagAttribute("include", $attribs, '', true);
	$reload = we_getTagAttribute("reload", $attribs, '', true);
	$rootdir = we_getTagAttribute("rootdir", $attribs, '/');
	if (substr($rootdir, 0, 1) != "/") {
		$rootdirid = $rootdir;
		$rootdir = id_to_path($rootdir, FILE_TABLE);
	} else {
		if (strlen($rootdir) > 1) {
			$rootdir = rtrim($rootdir,'/');
		}
		$rootdirid = path_to_id($rootdir, FILE_TABLE);
	}
	// Bug Fix #7045
	if (strlen($rootdir) == 1 && $rootdir == "/") {
		$rootdir = "";
	}

	$file = we_getTagAttribute("file", $attribs, '', true, true);
	$directory = we_getTagAttribute("directory", $attribs, '', true);

	$attribs = removeAttribs($attribs, array(
		"rootdir"
	));

	if ($GLOBALS["we_doc"]->ClassName == "we_objectFile") {
		$hrefArr = $GLOBALS["we_doc"]->getElement($name) ? unserialize($GLOBALS["we_doc"]->getElement($name)) : array();
		if (!is_array($hrefArr)) {
			$hrefArr = array();
		}
		return sizeof($hrefArr) ? we_document::getHrefByArray($hrefArr) : "";
	}

	$nint = $name . "_we_jkhdsf_int";
	$nintID = $name . "_we_jkhdsf_intID";
	$nintPath = $name . "_we_jkhdsf_intPath";
	$extPath = $GLOBALS["we_doc"]->getElement($name);

	// we have to use a html_entity_decode first in case a user has set &amp, &uuml; by himself
	// as html_entity_decode is only available php > 4.3 we use a custom function
	$extPath = !empty($extPath) ? htmlspecialchars(html_entity_decode($extPath)) : $extPath;

	if ($we_editmode) {
		// Init we_button class
		$we_button = new we_button();

		$int_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nint . ']';
		$intPath_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nintPath . ']';
		$intID_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nintID . ']';
		$ext_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']';

		$trashbut = $we_button->create_button(
				"image:btn_function_trash",
				"javascript:document.we_form.elements['" . $intID_elem_Name . "'].value = ''; document.we_form.elements['" . $intPath_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);" . (($include || $reload) ? "setScrollTo(); top.we_cmd('reload_editpage');" : ""),
				true);
		$span = '<span style="color: black;font-size:' . (($GLOBALS["SYSTEM"] == "MAC") ? "11px" : (($GLOBALS["SYSTEM"] == "X11") ? "13px" : "12px")) . ';font-family:' . g_l('css','[font_family]') . ';">';
	}

	if (!$type || $type == "all") {

		$int = ($GLOBALS["we_doc"]->getElement($nint) == "") ? 0 : $GLOBALS["we_doc"]->getElement($nint);
		$intID = $GLOBALS["we_doc"]->getElement($nintID);
		if (!$intID && $rootdirid) {
			$intID = $rootdirid;
		}
		$intPath = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID='".abs($intID)."'", "Path", $GLOBALS["DB_WE"]);

		if ($int) {
			$href = $intPath;
			$include_path = $href ? $_SERVER["DOCUMENT_ROOT"] . "/" . $href : "";
		} else {
			//if (!$we_editmode) {
			//	$extPath = htmlspecialchars($extPath);
			//}
			$href = $extPath;
			$include_path = $href;
		}

		$int_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nint . ']';
		$intPath_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nintPath . ']';
		$intID_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $nintID . ']';
		$ext_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']';

		$attr = we_make_attribs($attribs, "name,value,type,onkeydown,onKeyDown");

		if ($we_editmode) {
			if (($directory && $file) || $file) {
				//javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value', 'document.forms[\\'we_form\\'].elements[\\'$intPath_elem_Name\\'].value', 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][0].checked = true;" . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd(\'reload_editpage\');" : "") . "', '" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");
				$wecmdenc1= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intPath_elem_Name'].value");
				$wecmdenc3= 'WECMDENC_'.base64_encode("opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements['$int_elem_Name'][0].checked = true;" . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : "") . "");
				$but = $we_button->create_button(
						"select",
						"javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
				$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $we_button->create_button(
						"select",
						"javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', '" . (($directory && $file) ? "filefolder" : "") . "', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][1].checked = true;','" . $rootdir . "')") : "";
			} else {
				//javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value', 'document.forms[\\'we_form\\'].elements[\\'$intPath_elem_Name\\'].value', 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][0].checked = true;" . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd(\'reload_editpage\');" : "") . "', '" . session_id() . "', '" . $rootdirid . "');
				$wecmdenc1= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intID_elem_Name'].value");
				$wecmdenc2= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intPath_elem_Name'].value");
				$wecmdenc3= 'WECMDENC_'.base64_encode("opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements['$int_elem_Name'][0].checked = true;" . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : "") . "");
				$but = $we_button->create_button(
						"select",
						"javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','" . session_id() . "', '" . $rootdirid . "');");
				$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $we_button->create_button(
						"select",
						"javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', 'folder', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true); opener.document.we_form.elements[\'$int_elem_Name\'][1].checked = true;','" . $rootdir . "')") : "";
			}
			$trashbut2 = $we_button->create_button(
					"image:btn_function_trash",
					"javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true);",
					true);
			$out = '
				<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle">
							' . we_forms::radiobutton(
					1,
					$int,
					$int_elem_Name,
					$span . g_l('tags',"[int_href]") . ":</span>") . '</td>
						<td class="weEditmodeStyle">
							<input type="hidden" name="' . $intID_elem_Name . '" value="' . $intID . '" />
							<input type="text" name="' . $intPath_elem_Name . '" value="' . $intPath . '" ' . $attr . ' readonly /></td>
						<td class="weEditmodeStyle">
							' . getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">
							' . $but . '</td>
						<td class="weEditmodeStyle">
							' . $trashbut . '</td>
					</tr>
					<tr>
						<td class="weEditmodeStyle">
							' . we_forms::radiobutton(
					0,
					!$int,
					$int_elem_Name,
					$span . g_l('tags',"[ext_href]") . ":</span>") . '</td>
						<td class="weEditmodeStyle">
							<input onchange="this.form.elements[\'' . $int_elem_Name . '\'][1].checked = true;" type="text" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $extPath . '" ' . $attr . ' /></td>
						<td class="weEditmodeStyle">
							' . getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">
							' . $but2 . '</td>
						<td class="weEditmodeStyle">
							' . $trashbut2 . '</td>
					</tr>
				</table>';

			if ($include) {
				$out .= $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
			}
			return $out;
		} else {
			if ($include) {
				return $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
			} else {
				return $href;
			}
		}
	} else
		if ($type == "int") {
			$intID = $GLOBALS["we_doc"]->getElement($nintID);
			$intPath = f("SELECT Path FROM " . FILE_TABLE . " WHERE ID='".abs($intID)."'", "Path", $GLOBALS["DB_WE"]);
			$href = $intPath;
			$include_path = $href ? $_SERVER["DOCUMENT_ROOT"] . "/" . $href : "";

			if ($we_editmode) {
				if (($directory && $file) || $file) {
					//javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value', 'document.forms[\\'we_form\\'].elements[\\'$intPath_elem_Name\\'].value', 'opener._EditorFrame.setEditorIsHot(true); " . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd(\'reload_editpage\');" : "") . "', '" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
					$wecmdenc1= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intID_elem_Name'].value");
					$wecmdenc2= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intPath_elem_Name'].value");
					$wecmdenc3= 'WECMDENC_'.base64_encode("opener._EditorFrame.setEditorIsHot(true); " . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : "") . "");
					$but = $we_button->create_button(
							"select",
							"javascript:we_cmd('openDocselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "','".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','" . session_id() . "', '" . $rootdirid . "', '', " . (we_hasPerm("CAN_SELECT_OTHER_USERS_FILES") ? 0 : 1) . ",''," . ($directory ? 1 : 0) . ");");
				} else {
					//javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', 'document.forms[\\'we_form\\'].elements[\\'$intID_elem_Name\\'].value', 'document.forms[\\'we_form\\'].elements[\\'$intPath_elem_Name\\'].value', 'opener._EditorFrame.setEditorIsHot(true); " . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd(\'reload_editpage\');" : "") . "', '" . session_id() . "', '" . $rootdirid . "');;
					$wecmdenc1= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intID_elem_Name'].value");
					$wecmdenc2= 'WECMDENC_'.base64_encode("document.forms['we_form'].elements['$intPath_elem_Name'].value");
					$wecmdenc3= 'WECMDENC_'.base64_encode("opener._EditorFrame.setEditorIsHot(true); " . (($include || $reload) ? "opener.setScrollTo(); opener.top.we_cmd('reload_editpage');" : "") . "");
					$but = $we_button->create_button(
							"select",
							"javascript:we_cmd('openDirselector', document.forms[0].elements['$intID_elem_Name'].value, '" . FILE_TABLE . "', '".$wecmdenc1."','".$wecmdenc2."','".$wecmdenc3."','" . session_id() . "', '" . $rootdirid . "');");
				}

				$attr = we_make_attribs($attribs, "name,value,type,onkeydown,onKeyDown");
				$out = '
				<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle defaultfont" nowrap="nowrap">
							<input type="hidden" name="' . $int_elem_Name . '" value="1" />
							' . $span . g_l('tags',"[int_href]") . ':</span></td>
						<td class="weEditmodeStyle">
							<input type="hidden" name="' . $ext_elem_Name . '" />
							<input type="hidden" name="' . $intID_elem_Name . '" value="' . $intID . '" />
							<input type="text" name="' . $intPath_elem_Name . '" value="' . $intPath . '" ' . $attr . ' readonly /></td>
						<td class="weEditmodeStyle">
							' . getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">
							' . $but . '</td>
						<td class="weEditmodeStyle">
							' . $trashbut . '</td>
					</tr>
				</table>';
				if ($include) {
					$out .= $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
				}
				return $out;
			} else {
				if ($include) {
					return $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
				} else {
					return $href;
				}
			}
		} else {
			//if (!$we_editmode) {
			//	$extPath = htmlspecialchars($extPath);
			//}
			$href = $extPath;
			$include_path = $href ? $_SERVER["DOCUMENT_ROOT"] . "/" . $href : "";

			if ($we_editmode) {
				$ext_elem_Name = 'we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']';

				$trashbut2 = $we_button->create_button(
						"image:btn_function_trash",
						"javascript:document.we_form.elements['" . $ext_elem_Name . "'].value = ''; _EditorFrame.setEditorIsHot(true)",
						true);

				if (($directory && $file) || $file) {
					$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $we_button->create_button(
							"select",
							"javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', '" . (($directory && $file) ? "filefolder" : "") . "', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true);', '" . $rootdir . "')") : "";
				} else {
					$but2 = we_hasPerm("CAN_SELECT_EXTERNAL_FILES") ? $we_button->create_button(
							"select",
							"javascript:we_cmd('browse_server', 'document.forms[0].elements[\\'$ext_elem_Name\\'].value', 'folder', document.forms[0].elements['$ext_elem_Name'].value, 'opener._EditorFrame.setEditorIsHot(true);', '" . $rootdir . "')") : "";
				}

				$attr = we_make_attribs($attribs, "name,value,type,onkeydown,onKeyDown");

				$out = '
				<table border="0" cellpadding="0" cellspacing="2" background="' . IMAGE_DIR . 'backgrounds/aquaBackground.gif" style="border: solid #006DB8 1px;">
					<tr>
						<td class="weEditmodeStyle defaultfont" nowrap="nowrap">
							<input type="hidden" name="' . $int_elem_Name . '" value="0" />
							' . $span . g_l('tags',"[ext_href]") . ':</span></td>
						<td class="weEditmodeStyle">
							<input type="text" name="we_' . $GLOBALS["we_doc"]->Name . '_txt[' . $name . ']" value="' . $extPath . '" ' . $attr . ' /></td>
						<td class="weEditmodeStyle">
							' . getPixel(8, 1) . '</td>
						<td class="weEditmodeStyle">
							' . $but2 . '</td>
						<td class="weEditmodeStyle">
							' . $trashbut2 . '</td>
					</tr>
				</table>';
				if ($include) {
					$out .= $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
				}
				return $out;
			} else {
				if ($include) {
					return $include_path ? '<?php if(file_exists("' . $include_path . '")) include("' . $include_path . '"); ?>' : '';
				} else {
					return $href;
				}
			}
		}
}
