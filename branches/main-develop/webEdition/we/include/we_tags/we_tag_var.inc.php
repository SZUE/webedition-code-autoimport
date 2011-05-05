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

function we_tag_var($attribs, $content){
	if (($foo = attributFehltError($attribs, "name", "var")))
		return $foo;
	$docAttr = we_getTagAttribute("doc", $attribs);
	$name = we_getTagAttribute("name", $attribs);
	//$_name_orig=we_getTagAttribute("_name_orig", $attribs);
	$type = we_getTagAttribute("type", $attribs);
    $htmlspecialchars = we_getTagAttribute("htmlspecialchars", $attribs, "", true); // #3771

	$doc = we_getDocForTag($docAttr, false);

	switch ($type) {
		case "session" :
		    $return = (isset($_SESSION[$name])) ? $_SESSION[$name] : "";
		    if ($htmlspecialchars) $return = htmlspecialchars($return); // #3771
			return $return;
		case "request" :
			$return = removePHP(isset($_REQUEST[$name]) ? $_REQUEST[$name] : "");
		    if ($htmlspecialchars) $return = htmlspecialchars($return); // #3771
			return $return;
		case "post" :
			$return = removePHP(isset($_POST[$name]) ? $_POST[$name] : "");
		    if ($htmlspecialchars) $return = htmlspecialchars($return); // #3771
			return $return;
		case "get" :
			$return = removePHP(isset($_GET[$name]) ? $_GET[$name] : "");
		    if ($htmlspecialchars) $return = htmlspecialchars($return); // #3771
			return $return;
		case "global" :
		    $return = (isset($GLOBALS[$name])) ? $GLOBALS[$name] : "";
		    if ($htmlspecialchars) $return = htmlspecialchars($return); // #3771
			return $return;
		case 'multiobject' :
			$data = unserialize($doc->getField($attribs, $type, true));
			if (isset($data['objects']) && sizeof($data['objects']) > 0) {
				$out = implode(",", $data['objects']);
			} else {
				$out = "";
			}
			return $out;

		case "property" :
			if (isset($GLOBALS["we_obj"])) {
				eval('$var = $GLOBALS["we_obj"]->' . $name . ';');
			} else {
				eval('$var = $doc->' . $name . ';');
			}
			return $var;
		case 'shopVat' :
			if (defined('SHOP_TABLE')) {

				require_once (WE_SHOP_MODULE_DIR . 'weShopVats.class.php');
				$vatId = $GLOBALS['we_doc']->getElement(WE_SHOP_VAT_FIELD_NAME);
				return weShopVats::getVatRateForSite($vatId);
			}
		case 'link' : return $doc->getField($attribs, $type, false);break; // bugfix #3634
		default :
			$normVal = $doc->getField($attribs, $type, true);
			// bugfix 7557
			// wenn die Abfrage im Aktuellen Objekt kein Erg?bnis liefert
			// wird in den eingebundenen Objekten ?berpr?ft ob das Feld existiert
			if ($type == "select" && $normVal == "") {
				if (isset($doc->DefArray) && is_array($doc->DefArray)) {
					foreach ($doc->DefArray as $_glob_key => $_val) {

						if (substr($_glob_key, 0, 7) == "object_") {

							$normVal = we_document::getFieldByVal(
									$doc->getElement($name),
									$type,
									$attribs,
									false,
									$GLOBALS["we_doc"]->ParentID,
									$GLOBALS["we_doc"]->Path,
									$GLOBALS["DB_WE"],
									substr($_glob_key, 7));
						}

						if ($normVal != "")
							break;
					}
				} else {

					if (isset($doc->elements) && is_array($doc->elements)) {
						foreach ($doc->elements as $_glob_key => $_val) {

							if (substr($_glob_key, 0, 10) == "we_object_") {
								$normVal = we_document::getFieldByVal(
										$doc->getElement($name),
										$type,
										$attribs,
										false,
										$GLOBALS["we_doc"]->ParentID,
										$GLOBALS["we_doc"]->Path,
										$GLOBALS["DB_WE"],
										substr($_glob_key, 10));
							}
							if ($normVal != "")
								break;
						}
					}
				}
			}
			// EOF bugfix 7557


			return $normVal;
	}
	return $var;

}
