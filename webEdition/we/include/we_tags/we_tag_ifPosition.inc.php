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

/**
 *
 * @param mixed $position position-value of position-Array (first,last,even,odd,#)
 * @param int $size size of position Array
 * @param string $operator operator (equal,less,greater,less|equal,greater|equal)
 * @param int $position position of comparable
 * @param int $size size of comparable
 * @return mixed (true,false,-1) -1 if no decission is made yet - pass next element of position array
 */
function _we_tag_ifPosition_op($position, $size, $operator, $position, $size){
	switch($position){
		case 'first' :
			if($size == 1 && $operator != ''){
				switch($operator){
					case 'equal':
						return $position == 1;
					case 'less':
						return $position < 1;
					case 'less|equal':
						return $position <= 1;
					case 'greater':
						return $position > 1;
					case 'greater|equal':
						return $position >= 1;
				}
			} else {
				if($position == 1){
					return true;
				}
			}
			break;
		case 'last' :
			if($size == 1 && $operator != ''){
				switch($operator){
					case 'equal':
						return $position == $size;
					case 'less':
						return $position < $size;
					case 'less|equal':
						return $position <= $size;
					case 'greater|equal':
						return $position >= $size;
				}
			} else {
				if($position == $size){
					return true;
				}
			}
			break;
		case 'odd' :
			if($position % 2 != 0){
				return true;
			}
			break;
		case 'even' :
			if($position % 2 == 0){
				return true;
			}
			break;

		default :
			$position = intval($position); // Umwandeln in integer
			if($size == 1 && $operator != ''){
				switch($operator){
					case 'equal':
						return $position == $position;
					case 'less':
						return $position < $position;
					case 'less|equal':
						return $position <= $position;
					case 'greater':
						return $position > $position;
					case 'greater|equal':
						return $position >= $position;
					case 'every':
						return ($position % $position == 0);
				}
			} else {
				if(($operator === 'every' && ($position % $position == 0)) || $position == $position){
					return true;
				}
			}
			break;
	}
	//no decission yet
	return -1;
}

function we_tag_ifPosition(array $attribs){
	//	content is not needed in this tag
	//Hack for linklist
	if(isset($GLOBALS['we']['ll'])){
		$attribs['type'] = 'linklist';
	}
	if(($missingAttrib = attributFehltError($attribs, array('type' => false, 'position' => false), __FUNCTION__))){
		echo $missingAttrib;
		return '';
	}


	$type = weTag_getAttribute('type', $attribs, '', we_base_request::STRING);
	$positionArray = explode(',', weTag_getAttribute('position', $attribs, '', we_base_request::STRING));
	$size = count($positionArray);
	$operator = weTag_getAttribute('operator', $attribs, '', we_base_request::STRING);

	switch($type){
		case 'listview' : //	inside a listview, we take direct global listview object
			foreach($positionArray as $position){
				$tmp = _we_tag_ifPosition_op($position, $size, $operator, $GLOBALS['lv']->count, $GLOBALS['lv']->anz);
				if($tmp !== -1){
					return $tmp;
				}
			}
			break;

		case 'linklist' :
			//	first we must get right array !!!
			$reference = $GLOBALS['we']['ll']->getName();

			$reference = $GLOBALS['we_position']['linklist'][$reference];

			if(is_array($reference) && isset($reference['position'])){
				foreach($positionArray as $position){
					$tmp = _we_tag_ifPosition_op($position, $size, $operator, $reference['position'] + 1, $reference['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}

			break;

		case 'block' : //	look in function we_tag_block for details
			$reference = substr($GLOBALS['postTagName'], 4, strrpos($GLOBALS['postTagName'], '__') - 4); //strip leading blk_ and trailing __NO
			$reference = $GLOBALS['we_position']['block'][$reference];

			if(is_array($reference) && isset($reference['position'])){
				foreach($positionArray as $position){
					$tmp = _we_tag_ifPosition_op($position, $size, $operator, $reference['position'], $reference['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}
			break;

		case 'listdir' : //	inside a listview
			if(isset($GLOBALS['we_position']['listdir'])){
				$content = $GLOBALS['we_position']['listdir'];
			}
			if(isset($content) && $content['position']){
				foreach($positionArray as $position){
					$tmp = _we_tag_ifPosition_op($position, $size, $operator, $content['position'], $content['size']);
					if($tmp !== -1){
						return $tmp;
					}
				}
			}
			break;
		default :
			return false;
	}
	return false;
}
