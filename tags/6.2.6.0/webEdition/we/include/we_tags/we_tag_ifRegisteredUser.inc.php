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
function we_tag_ifRegisteredUser($attribs, $content) {

	$permission = we_getTagAttribute('permission', $attribs);
	$match = we_getTagAttribute('match', $attribs, '', false, false, true);
	$match = makeArrayFromCSV($match);
	$cfilter = we_getTagAttribute('cfilter', $attribs, '', true);
	$allowNoFilter = we_getTagAttribute('allowNoFilter', $attribs, '', true);
	$userid = we_getTagAttribute('userid', $attribs, '');
	$userid = makeArrayFromCSV($userid);
	$matchType = we_getTagAttribute('matchType', $attribs, 'one');

	if ($GLOBALS['we_doc']->InWebEdition || $GLOBALS['WE_MAIN_DOC']->InWebEdition) {
		return isset($_SESSION['we_set_registered']) && $_SESSION['we_set_registered'];
	} else {

		//return true only on registered users - or if cfilter is set to "no filter"
		if (isset($_SESSION['webuser']['registered']) && $_SESSION['webuser']['registered']) {
			$ret = true;

			if ($ret && sizeof($userid) > 0) {
				if (!isset($_SESSION['webuser']['ID'])) {
					return false;
				} else {
					$ret &= ( in_array($_SESSION['webuser']['ID'], $userid));
				}
			}

			if ($ret && $permission) {
				$ret &= isset($_SESSION['webuser']['registered']) && isset($_SESSION['webuser'][$permission]) && $_SESSION['webuser']['registered'];
				if (!$ret) {
					return false;
				}
				if (!empty($match)) {
					$perm = explode(',', $_SESSION['webuser'][$permission]);
					switch ($matchType) {
						case 'one':
							$tmp = array_intersect($perm, $match);
							$ret &= count($tmp) > 0;
							break;
						case 'contains':
							$tmp = array_intersect($perm, $match);
							$ret &= count($tmp) == count($match);
							break;
						default:
						case 'exact':
							$ret &= count($perm) == count($match);
							if ($ret) {
								$tmp = array_intersect($perm, $match);
								$ret &= count($tmp) == count($perm);
							}
							break;
					}
				} else {
					$ret &= (bool)$_SESSION['webuser'][$permission];
				}
			}

			if ($ret && $cfilter && defined('CUSTOMER_TABLE')) {
				if (isset($GLOBALS['we_doc']->documentCustomerFilter) && $GLOBALS['we_doc']->documentCustomerFilter) {
					$ret &= ( $GLOBALS['we_doc']->documentCustomerFilter->accessForVisitor($GLOBALS['we_doc'], array(), true) == WECF_ACCESS);
				} else {
					//access depends on $allowNoFilter
					return $allowNoFilter;
				}
			}

			return $ret;
		} else {
			//we are not logged in!
			if ($cfilter && defined('CUSTOMER_TABLE')) {
				if (isset($GLOBALS['we_doc']->documentCustomerFilter) && $GLOBALS['we_doc']->documentCustomerFilter) {
					//not logged in - no filter can match
					return false;
				} else {
					//not logged in - but "allow all users" is set - return depends on allowNoFilter
					return $allowNoFilter;
				}
			}
		}
		//this should never be reached
		return false;
	}
}
