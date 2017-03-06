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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_parse_tag_form($attribs, $content){
	return '<?php printElement(' . we_tag_tagParser::printTag('form', $attribs) . ');
?>' . $content .
		'<?php printElement(' . we_tag_tagParser::printTag('form', array('_type' => 'stop')) . ');?>';
}

function we_tag_form(array $attribs){
	if(!empty($GLOBALS['we_editmode'])){
		return'';
	}
	if(weTag_getAttribute('_type', $attribs, '', we_base_request::STRING) === 'stop'){
		unset($GLOBALS['WE_FORM']);
		return we_tag('formToken') . '</form>';
	}
	$ret = '';
	$method = weTag_getAttribute('method', $attribs, 'post', we_base_request::STRING);
	$id = weTag_getAttribute('id', $attribs, 0, we_base_request::STRING);
	$action = weTag_getAttribute('action', $attribs, '', we_base_request::URL);
	$classid = weTag_getAttribute('classid', $attribs, 0, we_base_request::INT);
	$parentid = weTag_getAttribute('parentid', $attribs, 0, we_base_request::INT);
	$doctype = weTag_getAttribute('doctype', $attribs, '', we_base_request::STRING);
	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$tid = weTag_getAttribute('tid', $attribs, 0, we_base_request::INT);
	$categories = weTag_getAttribute('categories', $attribs, '', we_base_request::WEFILELIST);
	$onsubmit = weTag_getAttribute('onSubmit', $attribs, weTag_getAttribute('onsubmit', $attribs, '', we_base_request::JS), we_base_request::JS);
	$remove = weTag_getAttribute('remove', $attribs, '', we_base_request::RAW);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);
	$formname = weTag_getAttribute('name', $attribs, 'we_global_form', we_base_request::STRING);
	if(array_key_exists('nameid', $attribs)){ // Bug #3153
		$formname = weTag_getAttribute('nameid', $attribs, 'we_global_form', we_base_request::STRING);
		$attribs['pass_id'] = $formname;
		unset($attribs['nameid']);
	}
	$captchaname = weTag_getAttribute('captchaname', $attribs, '', we_base_request::STRING);
	$enctype = weTag_getAttribute('enctype', $attribs, '', we_base_request::STRING);
	$target = weTag_getAttribute('target', $attribs, '', we_base_request::STRING);
	$formAttribs = removeAttribs($attribs, array(
		'onsubmit', 'onSubmit', 'name', 'method', 'xml', 'charset', 'id', 'action',
		'order', 'required', 'onsuccess', 'onerror', 'type', 'recipient', 'mimetype',
		'subject', 'onmailerror', 'preconfirm', 'postconfirm', 'from', 'confirmmail',
		'classid', 'doctype', 'remove', 'onrecipienterror', 'tid', 'forcefrom', 'categories'
	));

	$formAttribs['xml'] = $xml;
	$formAttribs['method'] = $method;

	$we_form_action = ($id ?
		($id === 'self' || ($id == 0 && defined('WE_REDIRECTED_SEO')) ? (defined('WE_REDIRECTED_SEO') ? WE_REDIRECTED_SEO : $_SERVER['SCRIPT_NAME']) : f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id))) :
		($action ?: $_SERVER['SCRIPT_NAME']));

	if($type != 'search'){
		$regs = array();
		if(preg_match('/^(.*)return (.+)$/i', $onsubmit, $regs)){
			$onsubmit = $regs[1] . ';return ' . $regs[2];
		} else {
			$onsubmit .= ';return true;';
		}
	}
	switch($type){
		case 'shopliste' :
			$formAttribs['action'] = $we_form_action;
			$articleID = (isset($GLOBALS['lv']) && $GLOBALS['lv']->f('WE_ID')) ? $GLOBALS['lv']->f('WE_ID') : (isset($GLOBALS['we_obj']->ID) ? $GLOBALS['we_obj']->ID : $GLOBALS['we_doc']->ID);
			$formAttribs['name'] = 'form' . $articleID;
			if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$ret = getHtmlTag('form', $formAttribs, '', false, true) .
					getHtmlTag('input', array('xml' => $xml, 'type' => 'hidden', 'name' => 'type',
						'value' => (
						isset($GLOBALS['lv']->classID) ?
						we_shop_shop::OBJECT :
						($GLOBALS['lv'] instanceof we_listview_document ?
						we_shop_shop::DOCUMENT :
						(isset($GLOBALS['we_obj']->ID) ?
						we_shop_shop::OBJECT :
						we_shop_shop::DOCUMENT
						)
						)
						)
					)) .
					getHtmlTag('input', array('xml' => $xml, 'type' => 'hidden', 'name' => 'shop_artikelid',
						'value' => $articleID
					)) .
					getHtmlTag('input', array('xml' => $xml, 'type' => 'hidden', 'name' => 'we_variant',
						'value' => (isset($GLOBALS['we_doc']->Variant) ? $GLOBALS['we_doc']->Variant : '')
					)) .
					getHtmlTag('input', array('xml' => $xml, 'type' => 'hidden', 'name' => 't',
						'value' => time()
				));
			}
			break;
		case 'object' :
		case 'document' :
			if(!(isset($_REQUEST['edit_' . $type]) && empty($GLOBALS['WE_SESSION_START'])) && isset($_SESSION)){
				unset($_SESSION['weS']['we_' . $type . '_session_' . $formname]);
			}

			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['name'] = $formname;
			$formAttribs['action'] = $we_form_action;

			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}
			if($classid || $doctype){
				$GLOBALS['WE_FORM'] = $formname;
				if(!$GLOBALS['we_doc']->InWebEdition){
					if($type === 'object'){
						$id = we_base_request::_(we_base_request::INT, 'we_editObject_ID', 0);
						if(f('SELECT 1 FROM ' . OBJECT_TABLE . ' WHERE ID=' . intval($classid))){
							we_objectFile::initObject($classid, $formname, $categories, intval($parentid), $id);
						}
					} else {
						$id = we_base_request::_(we_base_request::INT, 'we_editDocument_ID', 0);
						we_webEditionDocument::initDocument($formname, $tid, $doctype, $categories, $id);
					}
				}
				$typetmp = ($type === 'object') ? 'Object' : 'Document';

				if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
					$ret .= getHtmlTag('form', $formAttribs, '', false, true) .
						getHtmlTag('input', array('type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml)) .
						getHtmlTag('input', array('type' => 'hidden', 'name' => 'we_edit' . $typetmp . '_ID', 'xml' => $xml,
							'value' => we_base_request::_(we_base_request::INT, 'we_edit' . $typetmp . '_ID', 0),
					));
				}
			} elseif(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$ret .= getHtmlTag('form', $formAttribs, '', false, true);
			}
			break;
		case 'formmail' :
			$confirmmail = weTag_getAttribute('confirmmail', $attribs, false, we_base_request::BOOL);
			$preconfirm = $confirmmail ? weTag_getAttribute('preconfirm', $attribs, '', we_base_request::STRING) : '';
			$postconfirm = $confirmmail ? weTag_getAttribute('postconfirm', $attribs, '', we_base_request::STRING) : '';
			$onsuccess = weTag_getAttribute('onsuccess', $attribs, 0, we_base_request::INT);
			$onerror = weTag_getAttribute('onerror', $attribs, 0, we_base_request::INT);
			$onmailerror = weTag_getAttribute('onmailerror', $attribs, 0, we_base_request::INT);
			$onrecipienterror = weTag_getAttribute('onrecipienterror', $attribs, 0, we_base_request::INT);
			$oncaptchaerror = weTag_getAttribute('oncaptchaerror', $attribs, 0, we_base_request::INT);
			$recipient = weTag_getAttribute('recipient', $attribs, '', we_base_request::EMAILLIST);

			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}

			$formAttribs['method'] = 'post'; //don't allow anything else
			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = ($id === 'self' ? (defined('WE_REDIRECTED_SEO') ? WE_REDIRECTED_SEO : $_SERVER['SCRIPT_NAME']) : ($id ? f('SELECT Path FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id)) : WEBEDITION_DIR . 'we_formmail.php'));


			//  now prepare all needed hidden-fields:
			if(!isset($GLOBALS['we_editmode']) || !$GLOBALS['we_editmode']){
				$recipientArray = explode(',', $recipient);
				foreach($recipientArray as $key => $val){
					$recipientArray[$key] = '"' . trim($val) . '"';
				}

				$GLOBALS['DB_WE']->query('SELECT ID FROM ' . RECIPIENTS_TABLE . ' WHERE Email IN(' . implode(',', $recipientArray) . ')');
				$ids = $GLOBALS['DB_WE']->getAll(true);

				$ret = getHtmlTag('form', $formAttribs, '', false, true);
				$data = array_filter(array(
					'order' => weTag_getAttribute('order', $attribs, '', we_base_request::STRING),
					'required' => weTag_getAttribute('required', $attribs, '', we_base_request::STRING),
					'subject' => weTag_getAttribute('subject', $attribs, '', we_base_request::STRING),
					'recipient' => ($ids ? implode(',', $ids) : ''),
					'mimetype' => weTag_getAttribute('mimetype', $attribs, '', we_base_request::STRING),
					'from' => weTag_getAttribute('from', $attribs, '', we_base_request::EMAIL),
					'error_page' => $onerror ? we_folder::getUrlFromID($onerror) : '',
					'mail_error_page' => $onmailerror ? we_folder::getUrlFromID($onmailerror) : '',
					'recipient_error_page' => $onrecipienterror ? we_folder::getUrlFromID($onrecipienterror) : '',
					'ok_page' => $onsuccess ? we_folder::getUrlFromID($onsuccess) : '',
					'charset' => weTag_getAttribute('charset', $attribs, '', we_base_request::STRING),
					'confirm_mail' => $confirmmail,
					'pre_confirm' => $GLOBALS['we_doc']->getElement($preconfirm),
					'post_confirm' => $GLOBALS['we_doc']->getElement($postconfirm),
					'we_remove' => $remove,
					'forcefrom' => weTag_getAttribute('forcefrom', $attribs, '', we_base_request::STRING),
					'captcha_error_page' => $oncaptchaerror ? we_folder::getUrlFromID($oncaptchaerror) : '',
					'captchaname' => $captchaname,
				));
				$ret .= getHtmlTag('input', array('type' => 'hidden', 'name' => 'data-jwt', 'value' => we_helpers_jwt::encode(we_serialize($data, SERIALIZE_JSON), sha1(SECURITY_ENCRYPTION_KEY)),
					'xml' => $xml));
				/* foreach($data as $name => $val){
				  if($val){
				  $ret .= getHtmlTag('input', array('type' => 'hidden', 'name' => $name, 'value' => $val, 'xml' => $xml));
				  }
				  } */
			}
			break;
		default :
			if($enctype){
				$formAttribs['enctype'] = $enctype;
			}
			if($target){
				$formAttribs['target'] = $target;
			}
			$formAttribs['name'] = $formname;
			$formAttribs['onsubmit'] = $onsubmit;
			$formAttribs['action'] = $we_form_action;


			if(empty($GLOBALS['we_editmode'])){
				$ret .= getHtmlTag('form', $formAttribs, '', false, true);
			}
	}
	return $ret;
}
