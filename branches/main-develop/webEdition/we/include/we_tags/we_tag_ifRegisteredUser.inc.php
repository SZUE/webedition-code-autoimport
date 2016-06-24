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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
function we_tag_ifRegisteredUser(array $attribs){
	if(isset($GLOBALS['WE_MAIN_DOC_REF']) && $GLOBALS['WE_MAIN_DOC_REF']->InWebEdition){
		return (bool) $GLOBALS['WE_MAIN_DOC_REF']->getEditorPersistent('registered');
	}

	$permission = weTag_getAttribute('permission', $attribs, '', we_base_request::STRING);
	$match = weTag_getAttribute('match', $attribs, [], we_base_request::STRING_LIST);
	$cfilter = weTag_getAttribute('cfilter', $attribs, false, we_base_request::BOOL);
	$allowNoFilter = weTag_getAttribute('allowNoFilter', $attribs, false, we_base_request::BOOL);
	$userid = weTag_getAttribute('userid', $attribs, [], we_base_request::INTLISTA);
	$matchType = weTag_getAttribute('matchType', $attribs, 'one', we_base_request::STRING);

	//return true only on registered users - or if cfilter is set to "no filter"
	if(!empty($_SESSION['webuser']['registered'])){
		$ret = true;

		if($ret && $userid){
			if(!isset($_SESSION['webuser']['ID'])){
				return false;
			}
			$ret &= ( in_array($_SESSION['webuser']['ID'], $userid));
		}
		if($ret && $permission){
			$ret &=!empty($_SESSION['webuser']['registered']) && isset($_SESSION['webuser'][$permission]);
			if(!$ret){
				return false;
			}
			if($match){
				$perm = explode(',', $_SESSION['webuser'][$permission]);
				switch($matchType){
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
						if($ret){
							$tmp = array_intersect($perm, $match);
							$ret &= count($tmp) == count($perm);
						}
						break;
				}
			} else {
				$ret &= (bool) $_SESSION['webuser'][$permission];
			}
		}

		if($ret && $cfilter && defined('CUSTOMER_TABLE')){
			if(!empty($GLOBALS['we_doc']->documentCustomerFilter)){
				$ret &= ( $GLOBALS['we_doc']->documentCustomerFilter->accessForVisitor($GLOBALS['we_doc']->ID, $GLOBALS['we_doc']->ContentType, true) === we_customer_documentFilter::ACCESS);
			} else {
				//access depends on $allowNoFilter
				return $allowNoFilter;
			}
		}

		return $ret;
	}
	//we are not logged in!
	if($cfilter && defined('CUSTOMER_TABLE')){
		if(isset($GLOBALS['we_doc']->documentCustomerFilter) && $GLOBALS['we_doc']->documentCustomerFilter){
			//not logged in - no filter can match
			return false;
		}
		//not logged in - but "allow all users" is set - return depends on allowNoFilter
		return $allowNoFilter;
	}

	return false;
}
