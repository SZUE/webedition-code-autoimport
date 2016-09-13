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
include_once('we_tag_blockControls.inc.php');

function we_parse_tag_block($attribs, $content, array $arr){
	$GLOBALS['blkCnt'] = (isset($GLOBALS['blkCnt']) ? $GLOBALS['blkCnt'] + 1 : 0);
	if(($foo = attributFehltError($arr, 'name', __FUNCTION__))){
		return $foo;
	}

	//cleanup content
	while(strpos($content, '\\\\$') !== false){
		$content = str_replace('\\\\$', '$', $content);
	}
	//replace all \$ which are not inside we-tags
	$content = preg_replace('|([^\'"])\\\\\$|', '${1}\$', $content);

	$blockName = weTag_getParserAttribute('name', $arr);
	$name = str_replace(['$', '.', '/', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9], '', md5($blockName)) . $GLOBALS['blkCnt'];


	if(($content = str_replace('we_tag_blockControls("##blockControlsREPL##"', 'we_tag_blockControls($block_' . $name, $content, $count)) && $count){
		//nothing to do, we have a userdefined blockcontrol
	} else {
		$content = (preg_match('/< ?(tr|td)/i', $content) ?
				//table found
				strtr(preg_replace('|(< ?td[^>]*>)|si', '${1}' . '<?php we_tag_blockControls($block_' . $name . ');?>', strtr($content, ['=>' => '#####PHPCALSSARROW####', '?>' => '#####PHPENDBRACKET####']), 1), ['#####PHPCALSSARROW####' => '=>', '#####PHPENDBRACKET####' => '?>']) :
				//no tables found
				'<?php we_tag_blockControls($block_' . $name . ');?>' . $content
			);
	}
//	}
	//here postTagName is explicitly needed, because the control-element is not "inside" the block-tag (no block defined/first element) but controls its elements
	return '<?php
$block_' . $name . '=' . we_tag_tagParser::printTag('block', $attribs) . ';
while(we_condition_tag_block($block_' . $name . ')){
	?>' . $content . '<?php
}
unset($block_' . $name . '); ?>';
}

function we_condition_tag_block(&$block){
	if(!is_array($block)){
		return false;
	}
	//go to next element
	++$block['pos'];
	if($block['pos'] >= $block['listSize']){
		//end of list
		//we need a last add button in editmode
		if($GLOBALS['we_editmode']){
			we_tag_blockControls($block);
		}
		//reset data
		unset($GLOBALS['we_position']['block'][$block['name']]);
		$GLOBALS['postTagName'] = $block['lastPostName'];
		return false;
	}

	$GLOBALS['postTagName'] = 'blk_' . $block['name'] . '_' . $block['list'][$block['pos']];

	$GLOBALS['we_position']['block'][$block['name']] = ['position' => $block['pos'] + 1,
		'size' => $block['listSize']];
	return true;
}

function we_tag_block(array $attribs){
	$origName = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$name = weTag_getAttribute('name', $attribs, '', we_base_request::STRING);
	$start = weTag_getAttribute('start', $attribs, 0, we_base_request::INT);
	$limit = weTag_getAttribute('limit', $attribs, 0, we_base_request::INT);

	if(isset($GLOBALS['lv'])){
		if(!($list = $GLOBALS['lv']->f($name))){
			$list = $GLOBALS['lv']->f($origName);
			$name = $origName;
		}
	} else {
		$list = $GLOBALS['we_doc']->getElement($name);
	}

	if(($list = we_unserialize($list, [], true))){
		if(is_array($list) && count($list) && ((count($list) - 1) != max(array_keys($list)))){
			//reorder list!
			$list = array_values($list);
			$GLOBALS['we_doc']->setElement($name, we_serialize($list, SERIALIZE_JSON, true, 0, true));
		}
	} else if($start){
		$list = [];
		if($limit > 0){
			$start = min($start, $limit);
		}
		for($i = 1; $i <= $start; $i++){
			$list[] = '_' . $i;
		}
	}

	$listlen = count($list);
	$show = 10;
	if($limit > 0){
		if($GLOBALS['we_editmode']){
			$diff = $limit - $listlen;
			$show = ($diff > 0 ? min($show, $diff) : 0);
		} else {
			$listlen = min($listlen, $limit);
		}
	}
	return ['name' => $name,
		'list' => $list,
		'listSize' => $listlen,
		'ctlShow' => $show,
		'ctlShowSelect' => weTag_getAttribute('showselect', $attribs, true, we_base_request::BOOL),
		'pos' => -1,
		'lastPostName' => isset($GLOBALS['postTagName']) ? $GLOBALS['postTagName'] : '',
	];
}
