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

function we_isNotEmpty($attribs){
	$docAttr = weTag_getAttribute('doc', $attribs);
	$type = weTag_getAttribute('type', $attribs);
	$match = weTag_getAttribute('match', $attribs);
	$doc = we_getDocForTag($docAttr, false);

	switch ($type) {
		case 'object' :
			return $doc->getElement($match);
		case 'binary' :
		case 'img' :
		case 'flashmovie' :
			return $doc->getElement($match, 'bdid');
		case 'href' :
			if (isset($doc->TableID) && $doc->TableID) {
				$hrefArr = $doc->getElement($match) ? unserialize($doc->getElement($match)) : array();
				if (!is_array($hrefArr))
					$hrefArr = array();
				$hreftmp = trim(we_document::getHrefByArray($hrefArr));
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
				return $hreftmp ? true : false;
			}
			$int = ($doc->getElement($match . '_we_jkhdsf_int') == '') ? 0 : $doc->getElement(
					$match . '_we_jkhdsf_int');
			if ($int) { // for type = href int
				$intID = $doc->getElement($match . '_we_jkhdsf_intID');
				if ($intID > 0) {
					return strlen(id_to_path($intID)) > 0;
				}
				return false;
			} else {
				$hreftmp = $doc->getElement($match);
				if (substr($hreftmp, 0, 1) == '/' && (!file_exists($_SERVER['DOCUMENT_ROOT'] . $hreftmp))) {
					return false;
				}
			}
		default :

			if (isset($doc)) {
				//   #3938 added this - some php version crashed, when unserialize started with a ?,?,?


				if ((substr($doc->getElement($match), 0, 2) == 'a:')) { //  only unserialize, when $match cluld be an array
					// Added @-operator in front of the unserialze function because there
					// were some PHP notices that had no effect on the output of the function
					// remark holeg: when it is a serialized array, the function looks if it is not empty
					if (is_array(
							$arr = @unserialize($doc->getElement($match)))) {
						return sizeof($arr) ? true : false;
					}
				}
				//   end of #3938
			}

	}
	return ($doc->getElement($match) != '') || $doc->getElement($match, 'bdid');
}

function we_tag_ifEmpty($attribs, $content){
	$foo = attributFehltError($attribs, 'match', 'ifEmpty');
	if ($foo) {
		print($foo);
		return '';
	}
	if (isset($GLOBALS['we_editmode']) && $GLOBALS['we_editmode']) {
		return true;
	}
	return !we_isNotEmpty($attribs);
}
