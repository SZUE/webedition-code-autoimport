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

function we_tag_a($attribs, $content){
	global $we_editmode;

	// check for id attribute
	$foo = attributFehltError($attribs, "id", "a");
	if ($foo)
		return $foo;

	// get attributes
	$id = we_getTagAttribute("id", $attribs);
	if ($id == "self") {
		$id = $GLOBALS["WE_MAIN_DOC"]->ID;
	}
	$confirm = we_getTagAttribute("confirm", $attribs);
	$button = we_getTagAttribute("button", $attribs, "", true);
	$hrefonly = we_getTagAttribute("hrefonly", $attribs, "", true);
	$return = we_getTagAttribute("return", $attribs, "", true);
	$target = we_getTagAttribute("target", $attribs, "");

	$shop = we_getTagAttribute("shop", $attribs, "", true);
	$amount = we_getTagAttribute("amount", $attribs, 1);
	$delarticle = we_getTagAttribute("delarticle", $attribs, "", true);
	$delshop = we_getTagAttribute("delshop", $attribs, "", true);

	$edit = we_getTagAttribute("edit", $attribs);

	if (!$edit && ($shop || $delarticle || $delshop)) {
		$edit = "shop";
	}

	if ($edit) {
		$delete = we_getTagAttribute("delete", $attribs, "", true);
		$editself = we_getTagAttribute("editself", $attribs, "", true);
		$listview = isset($GLOBALS["lv"]);
	}

	// init variables
	$db = new DB_WE();
	$row = getHash("SELECT Path,IsFolder,IsDynamic FROM " . FILE_TABLE . " WHERE ID=".abs($id)."", $db);
	$url = (isset($row["Path"]) ? $row["Path"] : "") . ((isset($row["IsFolder"]) && $row["IsFolder"]) ? "/" : "");
	$path_parts = pathinfo($url);
	if (show_SeoLinks() && defined('NAVIGATION_DIRECTORYINDEX_NAMES') && NAVIGATION_DIRECTORYINDEX_NAMES !='' && defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE  && in_array($path_parts['basename'],explode(',',NAVIGATION_DIRECTORYINDEX_NAMES)) ){
		$url = ($path_parts['dirname']!='/' ? $path_parts['dirname']:'').'/';
	} 

	$urladd = "";

	include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_tagParser.inc.php");
	$tp = new we_tagParser();
	$tags = $tp->getAllTags($content);
	$tp->parseTags($tags, $content);

	if ((!$url) && ($GLOBALS["WE_MAIN_DOC"]->ClassName != "we_template")) {
		if ($we_editmode) {
			return parseError("in we:a attribute id not exists!");
		} else {
			return "";
		}
	}

	if ($edit == "shop") {

		$amount = we_getTagAttribute("amount", $attribs, 1);

		if (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->ClassName !='we_listview_multiobject' ) {
			$foo = $GLOBALS["lv"]->count - 1;
		} else {
			$foo = -1;
		}
		 

		// get ID of element
		$customReq = '';
		if (isset($GLOBALS["lv"]) && get_class($GLOBALS["lv"]) == 'shop') {

			$idd = $GLOBALS["lv"]->ActItem['id'];
			$type = $GLOBALS["lv"]->ActItem['type'];
			$customReq = $GLOBALS["lv"]->getCustomFieldsAsRequest();

		} else {
			//Zwei Faelle werden abgedeckt, bei denen die Objekt-ID nicht gefunden wird: (a) bei einer listview ueber shop-objekte, darin eine listview über shop-varianten, hierin der we:a-link und (b) Objekt wird ueber den objekt-tag geladen #3538
			if ( (isset($GLOBALS["lv"]) && get_class($GLOBALS["lv"]) == 'we_listview_shopVariants' && isset($GLOBALS["lv"]->Model) && $GLOBALS["lv"]->Model->ClassName == 'we_objectFile') || isset($GLOBALS["lv"]) && get_class($GLOBALS["lv"]) == 'we_objecttag' ) {
				$type="o"; 
				if (get_class($GLOBALS["lv"]) == 'we_listview_shopVariants') {
					$idd = $GLOBALS["lv"]->Id;
				} else {
					$idd = $GLOBALS["lv"]->id;
				}
			} else {

				$idd = ((isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$foo])) && $GLOBALS["lv"]->IDs[$foo] != "") ? $GLOBALS["lv"]->IDs[$foo] : ((isset(
					$GLOBALS["lv"]->classID)) ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : ((isset(
					$GLOBALS["we_obj"]->ID)) ? $GLOBALS["we_obj"]->ID : $GLOBALS["WE_MAIN_DOC"]->ID));
				$type = (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$foo]) && $GLOBALS["lv"]->IDs[$foo] != "") ? ((isset(
					$GLOBALS["lv"]->classID) || isset($GLOBALS["lv"]->Record["OF_ID"])) ? "o" : "w") : ((isset(
					$GLOBALS["lv"]->classID)) ? "o" : ((isset($GLOBALS["we_obj"]->ID)) ? "o" : "w"));
			}
		}

		// is it a shopVariant ????
		$variant = '';
		// normal variant on document
		if (isset($GLOBALS['we_doc']->Variant)) { // normal listView or document
			$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['we_doc']->Variant;
		}
		// variant inside shoplistview!
		if (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->f('WE_VARIANT')) {
			$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS["lv"]->f('WE_VARIANT');
		}

		//	preview mode in seem
		if (isset($_REQUEST["we_transaction"]) && isset(
				$_SESSION["we_data"][$_REQUEST["we_transaction"]]["0"]["ClassName"]) && $_SESSION["we_data"][$_REQUEST["we_transaction"]]["0"]["ClassName"] == "we_objectFile") {
			$type = "o";
		}

		$shopname = we_getTagAttribute("shopname", $attribs, "");
		$ifShopname = $shopname == "" ? "" : "&shopname=" . $shopname;
		if ($delarticle) { // delarticle


			// is it a shopVariant ????
			$variant = '';
			// normal variant on document
			if (isset($GLOBALS['we_doc']->Variant)) { // normal listView or document
				$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS['we_doc']->Variant;
			}
			// variant inside shoplistview!
			if (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->f('WE_VARIANT')) {
				$variant = '&' . WE_SHOP_VARIANT_REQUEST . '=' . $GLOBALS["lv"]->f('WE_VARIANT');
			}

			$foo = $GLOBALS["lv"]->count - 1;

			$customReq = '';
			if (isset($GLOBALS["lv"]) && get_class($GLOBALS["lv"]) == 'shop') {

				$idd = $GLOBALS["lv"]->ActItem['id'];
				$type = $GLOBALS["lv"]->ActItem['type'];
				$customReq = $GLOBALS["lv"]->getCustomFieldsAsRequest();
			} else {
				$idd = (isset($GLOBALS["lv"]->IDs[$foo]) && $GLOBALS["lv"]->IDs[$foo] != "") ? $GLOBALS["lv"]->IDs[$foo] : ((isset(
						$GLOBALS["lv"]->classID)) ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : ((isset(
						$GLOBALS["we_obj"]->ID)) ? $GLOBALS["we_obj"]->ID : $GLOBALS["WE_MAIN_DOC"]->ID));
				$type = (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$foo]) && $GLOBALS["lv"]->IDs[$foo] != "") ? ((isset(
						$GLOBALS["lv"]->classID) || isset($GLOBALS["lv"]->Record["OF_ID"])) ? "o" : "w") : ((isset(
						$GLOBALS["lv"]->classID)) ? "o" : ((isset($GLOBALS["we_obj"]->ID)) ? "o" : "w"));
			}
			//	preview mode in seem
			if (isset($_REQUEST["we_transaction"]) && isset(
					$_SESSION["we_data"][$_REQUEST["we_transaction"]]["0"]["ClassName"]) && $_SESSION["we_data"][$_REQUEST["we_transaction"]]["0"]["ClassName"] == "we_objectFile") {
				$type = "o";
			}
			$urladd = ($urladd ? $urladd . "&" : '?') . 'del_shop_artikelid=' . $idd . '&type=' . $type . '&t=' . time() . $variant . $customReq . $ifShopname;

		} else
			if ($delshop) { // emptyshop


				$foo = attributFehltError($attribs, "shopname", "a");
				if ($foo)
					return $foo;
				$urladd = ($urladd ? $urladd . "&" : '?') . 'deleteshop=1' . $ifShopname . '&t=' . time();

			} else { // increase/decrease amount of articles


				$urladd = ($urladd ? $urladd . "&" : '?') . 'shop_artikelid=' . $idd . '&shop_anzahl=' . $amount . '&type=' . $type . '&t=' . time() . $variant . ($customReq ? $customReq : '') . $ifShopname;
			}

	} else
		if ($edit == "object") {
			if ($listview) {
				$oid = (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->f("WE_ID")) ? $GLOBALS["lv"]->f("WE_ID") : 0;
			} else {
				$oid = (isset($GLOBALS["we_obj"]) && isset($GLOBALS["we_obj"]->ID) && $editself) ? $GLOBALS["we_obj"]->ID : 0;
			}
			if ($delete) {
				if ($oid) {
					$urladd = ($urladd ? $urladd . "&" : '?') . "we_delObject_ID=" . $oid;
				}
			} else {
				if ($oid) {
					$urladd = ($urladd ? $urladd . "&" : '?') . "we_editObject_ID=" . $oid;
				} else {
					$urladd = ($urladd ? $urladd . "&" : '?') . "edit_object=1";
				}
			}
		} else
			if ($edit == "document") {

				if ($listview) {
					$did = (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->f("WE_ID")) ? $GLOBALS["lv"]->f("WE_ID") : 0;
				} else {
					$did = (isset($GLOBALS["we_doc"]) && isset($GLOBALS["we_doc"]->ID) && $editself) ? $GLOBALS["we_doc"]->ID : 0;
				}
				if ($delete) {
					if ($did) {
						$urladd = ($urladd ? $urladd . "&" : '?') . "we_delDocument_ID=" . $did;
					}
				} else {

					if ($did) {
						$urladd = ($urladd ? $urladd . "&" : '?') . "we_editDocument_ID=" . $did;
					} else {
						$urladd = ($urladd ? $urladd . "&" : '?') . "edit_document=1";
					}
				}
			}

	if ($return) {
		$urladd = ($urladd ? $urladd . "&" : '?') . "we_returnpage=" . rawurlencode(
				$_SERVER["SCRIPT_NAME"] . "?" . $_SERVER["QUERY_STRING"]);
	}

	if ($hrefonly) {
		return $url . $urladd;
	}

	//	remove unneeded attributes from array
	$attribs = removeAttribs(
			$attribs,
			array(

					"id",
					"shop",
					"amount",
					"delshop",
					"delarticle",
					"shopname",
					"return",
					"edit",
					"type",
					"button",
					"hrefonly",
					"confirm",
					"editself",
					"delete"
			));

	if ($button) { //	show button


		$attribs["type"] = "button";
		$attribs["value"] = htmlspecialchars($content);
		$attribs["onclick"] = ($target ? ("var wind=window.open('','$target');wind") : "self") . ".document.location='$url" . htmlspecialchars(
				$urladd) . "';";

		$attribs = removeAttribs($attribs, array(
			"target"
		)); //	not html - valid


		if ($confirm) {
			$confirm = str_replace("'", "\\'", $confirm);
			$attribs["onclick"] = "if(confirm('$confirm')){" . $attribs["onclick"] . "}";
			return getHtmlTag("input", $attribs);
		} else {
			return getHtmlTag("input", $attribs);
		}
	} else { //	show normal link


		$attribs["href"] = $url . ($urladd ? htmlspecialchars($urladd) : '');

		if ($confirm) {

			$attribs["onclick"] = "if(confirm('$confirm')){return true;}else{return false;}";
			return getHtmlTag("a", $attribs, $content, true);
		} else {
			return getHtmlTag("a", $attribs, $content, true);
		}
	}
}
