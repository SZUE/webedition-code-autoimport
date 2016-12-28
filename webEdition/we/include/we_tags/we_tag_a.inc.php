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
 * @category	webEdition
 * @package 	none
 * @license		http://www.gnu.org/copyleft/gpl.html  GPL
 * @param		$attribs
 * @param		$content
 * @return		string
 */
function we_tag_a(array $attribs, $content){
	if(isset($GLOBALS['lv']) && $GLOBALS['lv'] instanceof stdClass){
		$id = $GLOBALS['lv']->ID;
	} else {
		// check for id attribute
		if(($foo = attributFehltError($attribs, 'id', __FUNCTION__))){
			return $foo;
		}

		// get attributes
		$id = weTag_getAttribute('id', $attribs, 0, we_base_request::STRING);
		if($id === 'self' && !defined('WE_REDIRECTED_SEO')){
			$id = $GLOBALS['WE_MAIN_DOC']->ID;
		}
	}
	$confirm = weTag_getAttribute('confirm', $attribs);
	$button = weTag_getAttribute('button', $attribs, false, we_base_request::BOOL);
	$hrefonly = weTag_getAttribute('hrefonly', $attribs, false, we_base_request::BOOL);
	$return = weTag_getAttribute('return', $attribs, false, we_base_request::BOOL);
	$target = weTag_getAttribute('target', $attribs);
	$hidedirindex = weTag_getAttribute('hidedirindex', $attribs, TAGLINKS_DIRECTORYINDEX_HIDE, we_base_request::BOOL);
	$shop = weTag_getAttribute('shop', $attribs, false, we_base_request::BOOL);
	$delarticle = weTag_getAttribute('delarticle', $attribs, false, we_base_request::BOOL);
	$delshop = weTag_getAttribute('delshop', $attribs, false, we_base_request::BOOL);
	$urladd = weTag_getAttribute('params', $attribs);
	$param = ($urladd ? [preg_replace('|^\?|', '', $urladd)] : []);

	$edit = weTag_getAttribute('edit', $attribs)? : ($shop || $delarticle || $delshop ? 'shop' : '');

	if($id === 'self' && defined('WE_REDIRECTED_SEO')){
		$url = WE_REDIRECTED_SEO;
	} else {
		// init variables
		$row = getHash('SELECT Path,IsFolder,IsDynamic FROM ' . FILE_TABLE . ' WHERE ID=' . intval($id));
		$url = (!$row ? '' : $row['Path'] . ($row['IsFolder'] ? '/' : ''));
		$path_parts = pathinfo($url);
		if($hidedirindex && TAGLINKS_DIRECTORYINDEX_HIDE && seoIndexHide($path_parts['basename'])){
			$url = ($path_parts['dirname'] != '/' ? $path_parts['dirname'] : '') . '/';
		}
	}

	if(!$url && !($GLOBALS['WE_MAIN_DOC'] instanceof we_template)){
		return ($GLOBALS['we_editmode'] ? parseError('in we:a attribute id not exists!') : '');
	}

	if($edit){
		$delete = weTag_getAttribute('delete', $attribs, false, true);
		$editself = weTag_getAttribute('editself', $attribs, false, true);
		$listview = isset($GLOBALS['lv']);

		switch($edit){
			case 'shop':
				if($delshop && (($foo = attributFehltError($attribs, 'shopname', __FUNCTION__)))){
					return $foo;
				}

				$amount = weTag_getAttribute('amount', $attribs, 1);
				$shopname = weTag_getAttribute('shopname', $attribs);
				$ifShopname = ($shopname ? '&shopname=' . $shopname : '');

				if($delshop){ //delete basket
					$param[] = 'deleteshop=1' . $ifShopname . '&t=' . time();
				} else {
					$customReq = '';
					if(isset($GLOBALS['lv'])){
						switch($GLOBALS['lv']){
							case ($GLOBALS['lv'] instanceof we_shop_shop):
								$articleID = $GLOBALS['lv']->ActItem['id'];
								$type = $GLOBALS['lv']->ActItem['type'];
								$customReq = $GLOBALS['lv']->getCustomFieldsAsRequest();
								break;
							case ($GLOBALS['lv']->Model instanceof we_objectFile):
							case (isset($GLOBALS['lv']->classID)):
								$articleID = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID');
								$type = we_shop_shop::OBJECT;
								break;
							case ($GLOBALS['lv'] instanceof we_listview_document):
							default:
								$articleID = $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID');
								$type = we_shop_shop::DOCUMENT;
								break;
						}
					} else {
						$articleID = isset($GLOBALS['we_obj']->ID) ? $GLOBALS['we_obj']->ID : $GLOBALS['we_doc']->ID;
						$type = isset($GLOBALS['we_obj']->ID) ? we_shop_shop::OBJECT : we_shop_shop::DOCUMENT;
					}

					// is it a variant ????
					$variant = // normal variant on document
						(isset($GLOBALS['we_doc']->Variant) ? // normal listView or document
							'&' . we_base_constants::WE_VARIANT_REQUEST . '=' . $GLOBALS['we_doc']->Variant :
							// variant inside shoplistview!
							(isset($GLOBALS['lv']) && $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'VARIANT') ?
								'&' . we_base_constants::WE_VARIANT_REQUEST . '=' . $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'VARIANT') :
								''));

					$trans = we_base_request::_(we_base_request::TRANSACTION, 'we_transaction');

					//	preview mode in seem
					if($trans && isset($_SESSION['weS']['we_data'][$trans][0]['ClassName']) && $_SESSION['weS']['we_data'][$trans][0]['ClassName'] === 'we_objectFile'){
						$type = we_shop_shop::OBJECT;
					}

					if($delarticle){ // delete article
						$param[] = 'del_shop_artikelid=' . $articleID . '&type=' . $type . '&t=' . time() . $variant . ($customReq ? : '') . $ifShopname;
					} else { // increase/decrease amount of articles
						$param[] = 'shop_artikelid=' . $articleID . '&shop_anzahl=' . $amount . '&type=' . $type . '&t=' . time() . $variant . ($customReq ? : '') . $ifShopname;
					}
				}
				break;

			case 'object':
				$oid = ($listview ?
						(!empty($GLOBALS['lv']) && $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID') ? $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID') : 0) :
						(!empty($GLOBALS['we_obj']) && isset($GLOBALS['we_obj']->ID) && $editself ? $GLOBALS['we_obj']->ID : 0));

				//FIXME: make sure only the selected object can be deleted - sth unique not user-known has to be added to prevent denial of service
				$param[] = ($oid ? 'we_' . ($delete ? 'del' : 'edit') . 'Object_ID=' . $oid : 'edit_object=1');
				break;
			case 'document':
				$did = ($listview ?
						(isset($GLOBALS['lv']) && $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID') ? $GLOBALS['lv']->f(we_listview_base::PROPPREFIX . 'ID') : 0) :
						(isset($GLOBALS['we_doc']) && isset($GLOBALS['we_doc']->ID) && $editself ? $GLOBALS['we_doc']->ID : 0));

				//FIXME: make sure only the selected document can be deleted - sth unique not user-known has to be added to prevent denial of service
				$param[] = ($did ? 'we_' . ($delete ? 'del' : 'edit') . 'Document_ID=' . $did : 'edit_document=1');
				break;
		}
	}

	if($return){
		$param[] = 'we_returnpage=' . rawurlencode($_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING']);
	}

	if($hrefonly){
		return $url . ($param ? '?' . implode('&', $param) : '');
	}

	//	remove unneeded attributes from array
	$attribs = removeAttribs($attribs, ['id',
		'shop',
		'amount',
		'delshop',
		'delarticle',
		'hidedirindex',
		'shopname',
		'return',
		'edit',
		'type',
		'button',
		'hrefonly',
		'confirm',
		'editself',
		'delete',
		'params'
	]);

	if($button){ //	show button
		$attribs['type'] = 'button';
		$attribs['onclick'] = ($target ? "var we_win=window.open('','" . $target . "');we_win" : 'self') .
			".document.location='" . $url . oldHtmlspecialchars(($param ? '?' . implode('&', $param) : '')) . "';";

		$attribs = removeAttribs($attribs, ['target']); //	not html - valid


		if($confirm){
			$confirm = str_replace("'", "\\'", $confirm);
			$attribs['onclick'] = 'if(window.confirm(\'' . $confirm . '\')){' . $attribs['onclick'] . '}';
		}
		return getHtmlTag('button', $attribs, $content, true);
	}
//	show normal link
	$attribs['href'] = $url . ($param ? oldHtmlspecialchars('?' . implode('&', $param)) : '');

	if($confirm){
		$attribs['onclick'] = 'if(window.confirm(\'' . $confirm . '\')){return true;}else{return false;}';
	}

	return getHtmlTag('a', $attribs, $content, true);
}
