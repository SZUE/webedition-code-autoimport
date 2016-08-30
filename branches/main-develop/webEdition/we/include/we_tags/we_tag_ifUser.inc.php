<?php
/**
 * webEdition CMS
 *
 * $Rev$
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
 * This function returns true if given usernames (CSV) contain actual backend user or
 * given usergroups (CSV) contain group of actual backend user
 *
 * @author	$Author$
 * @param	$attribs array
 * @return	boolean
 *
 */

function we_tag_ifUser($attribs = array()) {
    if(empty($GLOBALS['we_editmode']) || !isset($_SESSION['user'])){
        return false;
    }

    if(($foo = attributFehltError($attribs, array('name' => false, 'match' => true), __FUNCTION__))){
        echo $foo;
        return false;
    }

    $match = weTag_getAttribute('match', $attribs, '', we_base_request::RAW);
    $operator = weTag_getAttribute('operator', $attribs, 'equal', we_base_request::STRING);

    $matchArray = [];
    if(is_bool($match)){
        $size = 1;
    } else {
        $matchArray = explode(',', $match);
        $size = count($matchArray);
    }

    /**
     * values: Username, groups = array()
     */
    switch(($name = weTag_getAttribute('name', $attribs, 'Username', we_base_request::STRING))){
        default:
        case (stripos($name, 'username') === 0): //we need this for <we:block>
            return ($size == 1 ?
                _we_tag_ifUser_op($operator, $_SESSION['user']['Username'], $match) :
                (isset($_SESSION['user']['Username']) && in_array($_SESSION['user']['Username'], $matchArray)));
        case (stripos($name, 'groups') === 0):
            $userGroups = isset($_SESSION['user']['groups']) ? $_SESSION['user']['groups'] : '';
            if(!is_array($userGroups) || count($userGroups) == 0){
                return false;
            }
            // get paths for actual users userGroups and check, if paths match with specified userGroups
            $query = "SELECT Path FROM " . USER_TABLE . " WHERE ID IN (" . implode("," , $userGroups) . ") AND `IsFolder` = 1";
            $db = new DB_WE();
            $db->query($query);
            while($db->next_record()) {
                if (in_array($db->f('Path'), explode(',', $match))) {
                    return true;
                }
            }
            return false;
    }
}

/**
 * @param $operator
 * @param $first
 * @param $match
 * @return bool
 */

function _we_tag_ifUser_op($operator, $first, $match){
    switch($operator){
        default:
        case 'equal':
            return $first == $match;
        case 'contains':
            return (strpos($first, $match) !== false);
        case 'isin':
            return (strpos($match, $first) !== false);
    }
}

