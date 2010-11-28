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

function we_tag_linkToSeeMode($attribs, $content){
	$id = we_getTagAttribute('id', $attribs); //	if a document-id is selected go to that document
	$oid = we_getTagAttribute('oid', $attribs); //	if an object-id is selected go to that object
	$permission = we_getTagAttribute("permission", $attribs);
	$docAttr = we_getTagAttribute("doc", $attribs, "top");

	$xml = we_getTagAttribute("xml", $attribs, "");

	// check for value attribute
	$foo = attributFehltError($attribs, "value", "linkToSeeMode");
	if ($foo)
		return $foo;

	$value = we_getTagAttribute("value", $attribs);

	if (isset($id) && !empty($id)) {

		$type = 'document';
	} else
		if (isset($GLOBALS['we_obj']) || $oid) { // use object if possible


			$type = 'object';
			if ($oid) {
				$id = $oid;
			} else {
				if (isset($GLOBALS['we_obj'])) {
					$id = $GLOBALS['we_obj']->ID;
				}
			}
		} else {

			$type = 'document';
			$doc = we_getDocForTag($docAttr, true); // check if we should use the top document or the  included document
			$id = $doc->ID;
		}

	if (isset($_SESSION["webuser"]) && isset($_SESSION["webuser"]) && $_SESSION["webuser"]["registered"] && !isset(
			$_REQUEST["we_transaction"])) {
		if ($permission == "" || isset($_SESSION["webuser"][$permission]) && $_SESSION["webuser"][$permission]) { // Has webUser the right permissions??
			//	check if the customer is a user, too.
			$tmpDB = new DB_WE();

			$tmpDB->query(
					"SELECT ID FROM " . USER_TABLE . " WHERE username=\"" . $_SESSION["webuser"]["Username"] . "\" AND (UseSalt=0 AND passwd=\"" . md5(
							$_SESSION["webuser"]["Password"]) . "\") OR UseSalt=1 AND passwd=\"" . md5(
							$_SESSION["webuser"]["Password"] . md5($_SESSION["webuser"]["Username"])) . "\"");

			if ($tmpDB->num_rows() == 1) { // customer is also a user
				$retStr = getHtmlTag(
						'form',
						array(

								'method' => 'post',
								'name' => 'startSeeMode_' . $type . '_' . $id,
								'target' => '_parent',
								'action' => '/webEdition/loginToSuperEasyEditMode.php'
						),
								getHtmlTag(
								'input',
								array(

										'type' => 'hidden',
										'name' => 'username',
										'value' => $_SESSION["webuser"]["Username"],
										'xml' => $xml
								)) . getHtmlTag(
								'input',
								array(
									'type' => 'hidden', 'name' => 'type', 'value' => $type, 'xml' => $xml
								)) . getHtmlTag(
								'input',
								array(
									'type' => 'hidden', 'name' => 'id', 'value' => $id, 'xml' => $xml
								)) . getHtmlTag(
								'input',
								array(

										'type' => 'hidden',
										'name' => 'path',
										'value' => WE_SERVER_REQUEST_URI,
										'xml' => $xml
								))) . getHtmlTag(
						'a',
						array(

								'href' => 'javascript:document.forms[\'startSeeMode_' . $type . '_' . $id . '\'].submit();',
								'xml' => $xml
						),
						$value);
			} else { //	customer is no user
				$retStr = "<!-- ERROR: CUSTOMER IS NO USER! -->";
			}
			unset($tmpDB);
		} else { // User has not the right permissions.
			$retStr = "<!-- ERROR: USER DOES NOT HAVE REQUIRED PERMISSION! -->";
		}
	} else { //	webUser is not registered, show nothing
		$retStr = "<!-- ERROR: USER HAS NOT BEEN LOGGED IN! -->";
	}
	return $retStr;
}
