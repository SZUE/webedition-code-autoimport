<?php

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
//TODO
function we_parse_tag_form($attribs, $content) {
	return '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){
		printElement(' . we_tagParser::printTag('form', $attribs) . ');}?>' .
	$content .
	'<?php if(!isset($GLOBALS[\'we_editmode\']) || !$GLOBALS[\'we_editmode\']){ echo \'</form>\';}$GLOBALS[\'WE_FORM\'] = \'\'; if (isset($GLOBALS[\'we_form_action\'])) {unset($GLOBALS[\'we_form_action\']);}?>';
}

function we_tag_form($attribs, $content) {
	$ret = '';

	$method = we_getTagAttribute("method", $attribs, "post");
	$id = we_getTagAttribute("id", $attribs);
	$action = we_getTagAttribute("action", $attribs);
	$classid = we_getTagAttribute("classid", $attribs);
	$parentid = we_getTagAttribute("parentid", $attribs);
	$doctype = we_getTagAttribute("doctype", $attribs);
	$type = we_getTagAttribute("type", $attribs);
	$tid = we_getTagAttribute("tid", $attribs);
	$categories = we_getTagAttribute("categories", $attribs);
	$onsubmit = we_getTagAttribute("onsubmit", $attribs);
	$onsubmit = we_getTagAttribute("onSubmit", $attribs, $onsubmit);
	$onsuccess = we_getTagAttribute("onsuccess", $attribs);
	$onerror = we_getTagAttribute("onerror", $attribs);
	$onmailerror = we_getTagAttribute("onmailerror", $attribs);
	$confirmmail = we_getTagAttribute("confirmmail", $attribs);
	$preconfirm = we_getTagAttribute("preconfirm", $attribs);
	$postconfirm = we_getTagAttribute("postconfirm", $attribs);
	$order = we_getTagAttribute("order", $attribs);
	$required = we_getTagAttribute("required", $attribs);
	$remove = we_getTagAttribute("remove", $attribs);
	$subject = we_getTagAttribute("subject", $attribs);
	$recipient = we_getTagAttribute("recipient", $attribs);
	$mimetype = we_getTagAttribute("mimetype", $attribs);
	$from = we_getTagAttribute("from", $attribs);
	$charset = we_getTagAttribute("charset", $attribs);
	$xml = we_getTagAttribute("xml", $attribs);
	$formname = we_getTagAttribute("name", $attribs, "we_global_form");
	if (array_key_exists('nameid', $attribs)) { // Bug #3153
		$formname = we_getTagAttribute("nameid", $attribs, "we_global_form");
		$attribs['pass_id'] = we_getTagAttribute("nameid", $attribs);
		unset($attribs['nameid']);
	}
	$onrecipienterror = we_getTagAttribute("onrecipienterror", $attribs);
	$forcefrom = we_getTagAttribute("forcefrom", $attribs, "", false);
	$captchaname = we_getTagAttribute("captchaname", $attribs);
	$oncaptchaerror = we_getTagAttribute("oncaptchaerror", $attribs);
	$enctype = we_getTagAttribute("enctype", $attribs);
	$target = we_getTagAttribute("target", $attribs);
	$formAttribs = removeAttribs($attribs, array(
			'onsubmit', 'onSubmit', 'name', 'method', 'xml', 'charset', 'id', 'action',
			'order', 'required', 'onsuccess', 'onerror', 'type', 'recipient', 'mimetype',
			'subject', 'onmailerror', 'preconfirm', 'postconfirm', 'from', 'confirmmail',
			'classid', 'doctype', 'remove', 'onrecipienterror', 'tid', 'forcefrom', 'categories'
					));

	$formAttribs['xml'] = $xml;
	$formAttribs['method'] = $method;

	if ($id) {
		$GLOBALS["we_form_action"] = ($id == "self" ? $_SERVER["SCRIPT_NAME"] : f("SELECT Path FROM " . FILE_TABLE . " WHERE ID=" . intval($id), "Path", $GLOBALS["DB_WE"]));
	} else {
		$GLOBALS["we_form_action"] = ($action ? $action : $_SERVER["SCRIPT_NAME"]);
	}
	if ($type != "search") {
		if (eregi('^(.*)return (.+)$', $onsubmit, $regs)) {
			$onsubmit = $regs[1] . ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return ' . $regs[2];
		} else {
			$onsubmit .= ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return true;';
		}
	}
	switch ($type) {
		case "shopliste" :
			$formAttribs['action'] = $GLOBALS["we_form_action"];
			$formAttribs['name'] = 'form' . (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1]) && strlen($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1])) ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1] : $we_doc->ID;
			if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
				$ret = getHtmlTag(
												'form', $formAttribs, '', false, true) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'type',
										'value' => ( isset($GLOBALS["lv"]->classID) ? 'o' : (isset($GLOBALS["lv"]->ID) ? 'w' : (isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID)) ? 'o' : 'w' )),
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'shop_artikelid',
										'value' => (isset($GLOBALS["lv"]->classID) || isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID) ? ((isset($GLOBALS["lv"]) && $GLOBALS["lv"]->DB_WE->Record["OF_ID"] != "") ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : (isset($we_doc->DB_WE->Record["OF_ID"]) ? $we_doc->DB_WE->Record["OF_ID"] : (isset($we_doc->OF_ID) ? $we_doc->OF_ID : $we_doc->ID))) : ((isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1]) && $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1] != "") ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count - 1] : $we_doc->ID)
										),
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'we_variant',
										'value' => (isset($GLOBALS["we_doc"]->Variant) ? $GLOBALS["we_doc"]->Variant : ''),
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 't',
										'value' => time(),
								));
			}
			break;
		case "object" :
		case "document" :
			if (!isset($_REQUEST['edit_' . $type])) {
				if (isset($GLOBALS["WE_SESSION_START"]) && $GLOBALS["WE_SESSION_START"]) {
					unset($_SESSION['we_' . $type . '_session_' . $formname]);
				}
			}

			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['name'] = $formname;
			$formAttribs['action'] = $GLOBALS["we_form_action"];

			if ($enctype) {
				$formAttribs['enctype'] = $enctype;
			}
			if ($target) {
				$formAttribs['target'] = $target;
			}
			if ($classid || $doctype) {
				$GLOBALS["WE_FORM"] = $formname;
				if (!$GLOBALS["we_doc"]->InWebEdition) {
					if ($type == "object") {

						initObject($classid, $formname, $categories, $parentid);
					} else {
						initDocument($formname, $tid, $doctype, $categories);
					}
				}
				$typetmp = (($type == "object") ? "Object" : "Document");

				if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
					$ret.=getHtmlTag(
													'form', $formAttribs, '', false, true) . getHtmlTag(
													'input', array(
											'type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml
									)) . getHtmlTag(
													'input', array(
											'type' => 'hidden',
											'name' => 'we_edit' . $typetmp . '_ID',
											'value' => isset($_REQUEST["we_edit' . $typetmp . '_ID"]) ? ($_REQUEST["we_edit' . $typetmp . '_ID"]) : 0,
											'xml' => $xml
									));
				}
			} else {
				if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
					$ret.=getHtmlTag('form', $formAttribs, '', false, true);
				}
			}
			break;
		case "formmail" :
			$successpage = $onsuccess ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onsuccess), "Path", $GLOBALS["DB_WE"]) : '';
			$errorpage = $onerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onerror), "Path", $GLOBALS["DB_WE"]) : '';
			$mailerrorpage = $onmailerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onmailerror), "Path", $GLOBALS["DB_WE"]) : '';
			$recipienterrorpage = $onrecipienterror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($onrecipienterror), "Path", $GLOBALS["DB_WE"]) : '';
			$captchaerrorpage = $oncaptchaerror ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($oncaptchaerror), "Path", $GLOBALS["DB_WE"]) : '';

			$confirmmail = ($confirmmail == 'true');
			$preconfirm = $confirmmail && $preconfirm ? str_replace("'", "\\'", $we_doc->getElement($preconfirm)) : '';
			$postconfirm = $confirmmail && $postconfirm ? str_replace("'", "\\'", $we_doc->getElement($postconfirm)) : '';
			if ($enctype) {
				$formAttribs['enctype'] = $enctype;
			}
			if ($target) {
				$formAttribs['target'] = $target;
			}

			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = WEBEDITION_DIR . 'we_formmail.php';
			if ($id) {
				$formAttribs['action'] = ($id == "self" ? $_SERVER["SCRIPT_NAME"] : f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id), "Path", $GLOBALS["DB_WE"]));
			}


			//  now prepare all needed hidden-fields:
			if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
				$ret.=getHtmlTag('form', $formAttribs, "", false, true);
				$_recipientString = $recipient;
				$_recipientArray = explode(",", $_recipientString);
				foreach ($_recipientArray as $_key => $_val) {
					$_recipientArray[$_key] = '"' . trim($_val) . '"';
				}
				$_recipientString = implode(",", $_recipientArray);

				$_ids = array();
				$GLOBALS["DB_WE"]->query('SELECT * FROM ' . RECIPIENTS_TABLE . ' WHERE Email IN(' . $_recipientString . ')');
				while ($GLOBALS["DB_WE"]->next_record()) {
					$_ids[] = $GLOBALS["DB_WE"]->f("ID");
				}

				$_recipientIdString = '';
				if (count($_ids)) {
					$_recipientIdString = implode(',', $_ids);
				}

				$ret.='<div class="weHide" style="display: none;">
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'order',
										'value' => $order,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'required',
										'value' => $required,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'subject',
										'value' => $subject,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient',
										'value' => $_recipientIdString,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mimetype',
										'value' => $mimetype,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'from',
										'value' => $from,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'error_page', 'value' => $errorpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mail_error_page',
										'value' => $mailerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient_error_page',
										'value' => $recipienterrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'ok_page', 'value' => $successpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'charset',
										'value' => $charset,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'confirm_mail',
										'value' => $confirmmail,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'pre_confirm',
										'value' => $preconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'post_confirm',
										'value' => $postconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'we_remove', 'value' => $remove, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'forcefrom', 'value' => $forcefrom, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captcha_error_page',
										'value' => $captchaerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captchaname',
										'value' => $captchaname,
										'xml' => $xml
								)) . '
			                 </div>';
			}
			break;
		default :
			if ($enctype) {
				$formAttribs['enctype'] = $enctype;
			}
			if ($target) {
				$formAttribs['target'] = $target;
			}
			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = $GLOBALS["we_form_action"];


			if (!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) {
				$ret.=getHtmlTag('form', $formAttribs, "", false, true);
			}
	}
	return $ret;
}

/*
	private function parseFormTag($tag, $code, $attribs = "") {
		eval('$arr = array(' . $attribs . ');');

		$method = we_getTagAttributeForParsingLater("method", $arr, "post");
		$id = weTag_getParserAttribute("id", $arr);
		$action = weTag_getParserAttribute("action", $arr);
		$classid = weTag_getParserAttribute("classid", $arr);
		$parentid = weTag_getParserAttribute("parentid", $arr);
		$doctype = weTag_getParserAttribute("doctype", $arr);
		$type = weTag_getParserAttribute("type", $arr);
		$tid = weTag_getParserAttribute("tid", $arr);
		$categories = weTag_getParserAttribute("categories", $arr);
		$onsubmit = weTag_getParserAttribute("onsubmit", $arr);
		$onsubmit = weTag_getParserAttribute("onSubmit", $arr, $onsubmit);
		$onsuccess = weTag_getParserAttribute("onsuccess", $arr);
		$onerror = weTag_getParserAttribute("onerror", $arr);
		$onmailerror = weTag_getParserAttribute("onmailerror", $arr);
		$confirmmail = weTag_getParserAttribute("confirmmail", $arr);
		$preconfirm = weTag_getParserAttribute("preconfirm", $arr);
		$postconfirm = weTag_getParserAttribute("postconfirm", $arr);
		$order = weTag_getParserAttribute("order", $arr);
		$required = weTag_getParserAttribute("required", $arr);
		$remove = weTag_getParserAttribute("remove", $arr);
		$subject = weTag_getParserAttribute("subject", $arr);
		$recipient = weTag_getParserAttribute("recipient", $arr);
		$mimetype = weTag_getParserAttribute("mimetype", $arr);
		$from = weTag_getParserAttribute("from", $arr);
		$charset = weTag_getParserAttribute("charset", $arr);
		$xml = weTag_getParserAttribute("xml", $arr);
		$formname = we_getTagAttributeForParsingLater("name", $arr, "we_global_form");
		if (array_key_exists('nameid', $arr)) { // Bug #3153
			$formname = we_getTagAttributeForParsingLater("nameid", $arr, "we_global_form");
			$arr['pass_id'] = we_getTagAttributeForParsingLater("nameid", $arr);
			unset($arr['nameid']);
		}
		$onrecipienterror = weTag_getParserAttribute("onrecipienterror", $arr);
		$forcefrom = weTag_getParserAttribute("forcefrom", $arr, "", false);
		$captchaname = weTag_getParserAttribute("captchaname", $arr);
		$oncaptchaerror = weTag_getParserAttribute("oncaptchaerror", $arr);
		$enctype = we_getTagAttributeForParsingLater("enctype", $arr);
		$target = we_getTagAttributeForParsingLater("target", $arr);
		$formAttribs = removeAttribs(
						$arr, array(
				'onsubmit',
				'onSubmit',
				'name',
				'method',
				'xml',
				'charset',
				'id',
				'action',
				'order',
				'required',
				'onsuccess',
				'onerror',
				'type',
				'recipient',
				'mimetype',
				'subject',
				'onmailerror',
				'preconfirm',
				'postconfirm',
				'from',
				'confirmmail',
				'classid',
				'doctype',
				'remove',
				'onrecipienterror',
				'tid',
				'forcefrom',
				'categories'
						));

		$formAttribs['xml'] = $xml;
		$formAttribs['method'] = $method;

		if ($id) {
			if ($id != "self") {
				$php = '<?php $__id__ = ' . $id . ';$GLOBALS["we_form_action"] = f("SELECT Path FROM ".FILE_TABLE." WHERE ID=".abs($__id__),"Path",$GLOBALS["DB_WE"]); ?>
';
			} else {
				$php = '<?php $GLOBALS["we_form_action"] = $_SERVER["SCRIPT_NAME"]; ?>
';
			}
		} else
		if ($action) {
			$php = '<?php $GLOBALS["we_form_action"] = "' . $action . '"; ?>
';
		} else {
			$php = '<?php $GLOBALS["we_form_action"] = $_SERVER["SCRIPT_NAME"]; ?>
';
		}
		if ($type != "search") {
			if (eregi('^(.*)return (.+)$', $onsubmit, $regs)) {
				$onsubmit = $regs[1] . ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return ' . $regs[2];
			} else {
				$onsubmit .= ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return true;';
			}
		}
		switch ($type) {
			case "shopliste" :
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';
				$formAttribs['name'] = 'form<?php print (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]) && strlen($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1])) ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1] : $we_doc->ID; ?>';
				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) { ?>' . getHtmlTag(
												'form', $formAttribs, '', false, true) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'type',
										'value' => '<?php if( isset($GLOBALS["lv"]->classID) ){ echo "o"; }else if( isset($GLOBALS["lv"]->ID) ){ echo "w"; }else if( (isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID) )){echo "o";}else if($GLOBALS["we_doc"]->ID){ echo "w"; } ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'shop_artikelid',
										'value' => '<?php if(isset($GLOBALS["lv"]->classID) || isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID)){ echo (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->DB_WE->Record["OF_ID"]!="") ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : (isset($we_doc->DB_WE->Record["OF_ID"]) ? $we_doc->DB_WE->Record["OF_ID"] : (isset($we_doc->OF_ID) ? $we_doc->OF_ID : $we_doc->ID)); }else { echo (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]) && $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]!="") ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1] : $we_doc->ID; } ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'we_variant',
										'value' => '<?php print (isset($GLOBALS["we_doc"]->Variant) ? $GLOBALS["we_doc"]->Variant : ""); ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 't',
										'value' => '<?php echo time(); ?>'
								)) . '<?php } ?>';
				break;
			case "object" :
			case "document" :
				$php .= '<?php if(!isset($_REQUEST["edit_' . $type . '"])){ if(isset($GLOBALS["WE_SESSION_START"]) && $GLOBALS["WE_SESSION_START"]){ unset($_SESSION["we_' . $type . '_session_' . $formname . '"] );}} ?>
';
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['name'] = $formname;
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';

				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}
				if ($classid || $doctype) {
					$php .= '<?php $GLOBALS["WE_FORM"] = "' . $formname . '"; ?>';
					$php .= '<?php
if (!$GLOBALS["we_doc"]->InWebEdition) {
';
					if ($type == "object") {

						$php .= 'initObject(' . $classid . ',"' . $formname . '","' . $categories . '","' . $parentid . '");
';
					} else {
						$php .= 'initDocument("' . $formname . '","' . $tid . '","' . $doctype . '","' . $categories . '");
';
					}
					$php .= '
}
?>
';
					$typetmp = (($type == "object") ? "Object" : "Document");

					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
													'form', $formAttribs, '', false, true) . getHtmlTag(
													'input', array(
											'type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml
									)) . getHtmlTag(
													'input', array(
											'type' => 'hidden',
											'name' => 'we_edit' . $typetmp . '_ID',
											'value' => '<?php print isset($_REQUEST["we_edit' . $typetmp . '_ID"]) ? ($_REQUEST["we_edit' . $typetmp . '_ID"]) : 0; ?>',
											'xml' => $xml
									)) . '<?php }?>';
				} else {
					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
													'form', $formAttribs, '', false, true) . '<?php }?>';
				}
				break;
			case "formmail" :
				$successpage = $onsuccess ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onsuccess . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$errorpage = $onerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$mailerrorpage = $onmailerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onmailerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$recipienterrorpage = $onrecipienterror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onrecipienterror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$captchaerrorpage = $oncaptchaerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $oncaptchaerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';

				if ($confirmmail == "true") {
					$confirmmail = true;
					$preconfirm = $preconfirm ? '<?php print str_replace("\'","\\\'",$we_doc->getElement("' . $preconfirm . '")); ?>' : '';
					$postconfirm = $postconfirm ? '<?php print str_replace("\'","\\\'",$we_doc->getElement("' . $postconfirm . '")); ?>' : '';
				} else {
					$confirmmail = false;
					$postconfirm = '';
					$preconfirm = '';
				}
				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}

				$formAttribs['name'] = $formname;
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['action'] = '<?php print WEBEDITION_DIR ?>we_formmail.php';
				if ($id) {
					if ($id != "self") {

						$formAttribs['action'] = '<?php print(f("SELECT Path FROM ".FILE_TABLE." WHERE ID=\'' . $id . '\'","Path",$GLOBALS["DB_WE"])); ?>';
					} else {
						$formAttribs['action'] = '<?php print $_SERVER["SCRIPT_NAME"]; ?>';
					}
				}


				//  now prepare all needed hidden-fields:
				$php = '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>
				            ' . getHtmlTag('form', $formAttribs, "", false, true) . '
				            <?php
				            	$_recipientString = "' . $recipient . '";
				            	$_recipientArray = explode(",", $_recipientString);
				            	foreach ($_recipientArray as $_key=>$_val) {
				            		$_recipientArray[$_key] = "\"" . trim($_val) . "\"";
				            	}
				            	$_recipientString = implode(",", $_recipientArray);

				            	$_ids = array();
				            	$GLOBALS["DB_WE"]->query("SELECT * FROM " . RECIPIENTS_TABLE . " WHERE Email IN(" . $_recipientString . ")");
				            	while ($GLOBALS["DB_WE"]->next_record()) {
				            		$_ids[] = $GLOBALS["DB_WE"]->f("ID");
				            	}

				            	$_recipientIdString = "";
				            	if (count($_ids)) {
				            		$_recipientIdString = implode(",", $_ids);
				            	}

				            ?>
				            <div class="weHide" style="display: none;">
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'order',
										'value' => '<?php print "' . $order . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'required',
										'value' => '<?php print "' . $required . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'subject',
										'value' => '<?php print "' . $subject . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient',
										'value' => '<?php print $_recipientIdString; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mimetype',
										'value' => '<?php print "' . $mimetype . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'from',
										'value' => '<?php print "' . $from . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'error_page', 'value' => $errorpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mail_error_page',
										'value' => $mailerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient_error_page',
										'value' => $recipienterrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'ok_page', 'value' => $successpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'charset',
										'value' => '<?php print "' . $charset . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'confirm_mail',
										'value' => '<?php print "' . $confirmmail . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'pre_confirm',
										'value' => $preconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'post_confirm',
										'value' => $postconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'we_remove', 'value' => $remove, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'forcefrom', 'value' => $forcefrom, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captcha_error_page',
										'value' => $captchaerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captchaname',
										'value' => $captchaname,
										'xml' => $xml
								)) . '
			                 </div>
				        <?php }?>';
				break;
			default :
				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}
				$formAttribs['name'] = $formname;
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';

				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
												'form', $formAttribs, "", false, true) . "<?php } ?>\n";
		}

		return $this->replaceTag($tag, $code, $php);
	}

 */