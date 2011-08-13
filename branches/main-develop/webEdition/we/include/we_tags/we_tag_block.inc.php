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

//define('oldBlock','1');
if(!defined('oldBlock')){

	/*
 * due to parser limits this does not work now
function we_parse_tag_blockControls($attribs,$content){
	eval('$arr = ' . $attribs . ';');
	if (($foo = attributFehltError($arr, 'name', 'blockControls')))	return $foo;
	$name = weTag_getParserAttribute("name", $arr);
	return '<?php if(we_tag(\'ifEditmode\')){echo we_tag_blockControls('.$name.'));}?>';
}
*/

function we_parse_tag_block($attribs,$content){
	eval('$arr = ' . $attribs . ';');
	if (($foo = attributFehltError($arr, 'name', 'block')))	return $foo;
	
	//cleanup content
	while(strpos($content,'\$')!==false){
		$content=str_replace('\$','$',$content);
	}
	
	$name = md5(weTag_getParserAttribute('name', $arr));
	$ctlPre='<?php if('.we_tagParser::printTag('ifEditmode').'){echo we_tag_blockControls(';
	$ctlPost=');}?>';
	
	//if(strpos($content,'blockControls')===false){
		if (preg_match('/^< ?(tr|td)/i', trim($content))) {
			$content = str_replace('=>', '#####PHPCALSSARROW####', 
							str_replace('?>', '#####PHPENDBRACKET####', $content));
			$content = preg_replace('|(< ?td[^>]*>)(.*< ?/ ?td[^>]*>)|si', '$1' . $ctlPre.'$block_'.$name.$ctlPost . '$2', $content,1);
			$content = str_replace('#####PHPCALSSARROW####', '=>', 
							str_replace('#####PHPENDBRACKET####', '?>', $content));
		}else{
			$content = '<p>'.$ctlPre.'$block_'.$name.$ctlPost.$content.'</p>';
		}
//	}
	return '<?php if(($block_'.$name.'='.we_tagParser::printTag('block',$attribs).')!==false){'."\n\t".
		'while(we_condition_tag_block($block_'.$name.')){?>'.$content.'<?php }}else{?>'.
		$ctlPre.'array(\'name\'=>\''.$name.'\'.(isset($GLOBALS[\'postTagName\'])?$GLOBALS[\'postTagName\']:\'\'),\'pos\'=>0,\'listSize\'=>0,'.
		'\'ctlShowSelect\'=>'.(weTag_getParserAttribute('showselect', $arr,true, true)?'true':'false').','.
		'\'ctlShow\'=>'.(int)weTag_getParserAttribute('limit', $arr, 10).')'.$ctlPost.
		'<?php }?>';
}

function we_condition_tag_block(&$block){
	//go to next element
	++$block['pos'];
	if($block['pos']>=$block['listSize']){
		//end of list
		//we need a last add button in editmode
		if ($GLOBALS['we_editmode']) {
			print printElement(we_tag_blockControls($block));
		}
		//reset data
		unset($GLOBALS['we_position']['block'][$block['name']]);
		$GLOBALS['postTagName'] = $block['lastPostName'];
		return false;
	}
	$blkPreName= 'blk_'.$block['name']. '_';
	$GLOBALS['postTagName']=$blkPreName . $block['list'][$block['pos']];
	
	$GLOBALS['we_position']['block'][$block['name']] = array(
			'position' => $block['pos']+1, 
			'size'=> $block['listSize']);
		return true;
}
}

function we_tag_block($attribs, $content){
	if(!defined('oldBlock')){
	$name = weTag_getAttribute('name', $attribs);
	$showselect = weTag_getAttribute('showselect', $attribs, true, true);
	$start = weTag_getAttribute('start', $attribs);
	$limit = weTag_getAttribute('limit', $attribs);


	if (isset($GLOBALS['lv'])) {
		$list = $GLOBALS['lv']->f($name);
	} else {
		$list = $GLOBALS['we_doc']->getElement($name);
	}
	if($list){
		$list=unserialize($list);
	}else if ($start) {
		$list = array();
		if ($limit && $limit > 0 && $limit < $start) {
			$start = $limit;
		}
		for ($i = 1; $i <= $start; $i++) {
			$list[] = '_' . $i;
		}
	}
	
	$listlen=sizeof($list);
	if(!$list||$listlen==0){
		return false;
	}
	
	$show = 10;
	if (!$GLOBALS['we_editmode']) {
			if ($limit > 0 && $listlen > $limit) {
				$listlen = $limit;
			}
		}else{
			if ($limit && $limit > 0) {
				$diff = $limit - $listlen;
				if ($diff > 0) {
					$show = min($show, $diff);
				} else {
					$show = 0;
				}
			}
		}

		return array(
			'name'=>$name,
			'list'=>$list,
			'listSize'=>$listlen,
			'ctlShow'=>$show,
			'ctlShowSelect'=>$showselect,
			'pos'=>-1,
			'lastPostName'=>isset($GLOBALS['postTagName'])?$GLOBALS['postTagName']:'',
	);
	
	
	}
	if ($GLOBALS['we_editmode']) {
		$we_button = new we_button();
	}

	if (($foo = attributFehltError($attribs, 'name', 'block')))	return $foo;
	include_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_tagParser.inc.php');

	$name = weTag_getAttribute('name', $attribs);
	$showselect = weTag_getAttribute('showselect', $attribs, true, true);

	$isInListview = isset($GLOBALS['lv']);

	if ($isInListview) {
		$list = $GLOBALS['lv']->f($name);
		$GLOBALS['lv']->BlockInside = true;
	} else {
		$list = $GLOBALS['we_doc']->getElement($name);
	}

	// Bug Fix #1909 and #415
	$start = weTag_getAttribute('start', $attribs);
	$limit = weTag_getAttribute('limit', $attribs);
	if($list){
		$list=unserialize($list);
	}else if ($start) {
		$list = array();
		if ($limit && $limit > 0 && $limit < $start) {
			$start = $limit;
		}
		for ($i = 0; $i < $start; $i++) {
			$list[$i] = '_' . ($i + 1);
		}
	}
	// Bug Fix #1909 and #415


	$blkPreName = 'blk_' . $name . '_';

	$content = str_replace('<we:ref', '<we_:_ref', $content);

	$tp = new we_tagParser($content);

	$names = implode(',', we_tagParser::getNames($tags));

	if (strpos($content, '<we:object') === false && strpos($content, '<we:metadata') === false && strpos($content, '<we:listview') === false) { //	no we:object is used
		//	parse name of we:field
		$tp->parseTags($content, '<we_:_ref>', array());
	} else { //	we:object is used
		//	dont parse name of we:field !!!
		$tp->parseTags(
				$content,
				'<we_:_ref>',
				array(
					'we:field',
					'we:ifField',
					'we:ifNotField',
					'we:ifFieldEmpty',
					'we:ifFieldNotEmpty',
					'we:ifPageLanguage',
					'we:ifNotPageLanguage',
					'we:ifObjectLanguage',
					'we:ifNotObjectLanguage'
				));
	}
	$out = '';

	$tmpname = md5(uniqid(time()));

	$noButCode = '';
	if ($list) {

		$listlen = sizeof($list);

		if ($listlen != 0) {

			if (!$GLOBALS['we_editmode']) {
				if ($limit > 0 && $listlen > $limit) {
					$listlen = $limit;
				}
			}else{
				$show = 10;
				if ($limit && $limit > 0) {
					$diff = $limit - $listlen;
					if ($diff > 0) {
						$show = min($show, $diff);
					} else {
						$show = 0;
					}
				}
			}

			for ($i = 0; $i < $listlen; $i++) {
				$listRef = $blkPreName . $list[$i];

				$foo = $content;

				$foo = str_replace('<we_:_ref>', $listRef, $foo);

				//	handle we:ifPosition:
				if (strpos($foo, 'position') || strpos($foo, 'ifPosition') || strpos(
						$foo,
						'ifNotPosition')) { //	set information for ifPosition


					$foo = '<?php $GLOBALS[\'we_position\'][\'block\'][\'' . $name . '\'] = array(\'position\' => ' . ($i + 1) . ', \'size\'=>' . $listlen . '); ?>' . $foo . '<?php unset($GLOBALS[\'we_position\'][\'block\'][\'' . $name . '\']); ?>';
				}

				$noButCode .= $foo;
				if ($GLOBALS['we_editmode'] && !$isInListview) {


					$upbut = $we_button->create_button('image:btn_direction_up',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','$name','$i')");
					$upbutDis = $we_button->create_button('image:btn_direction_up', '', true, 21, 22, '', '', true);
					$downbut = $we_button->create_button('image:btn_direction_down',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','$name','$i')");
					$downbutDis = $we_button->create_button('image:btn_direction_down',
							'',true,21,22,'','',true);
					if ($showselect && $show > 0) {
						$selectb = '<select name="' . $tmpname . '_' . $i . '">';
						for ($j = 0; $j < $show; $j++) {
							$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
						}
						$selectb .= '</select>';
						$plusbut = $we_button->create_button('image:btn_add_listelement',
								"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','$name','$i',document.we_form.elements['" . $tmpname . "_" . $i . "'].options[document.we_form.elements['" . $tmpname . "_" . $i . "'].selectedIndex].text)",
								true,100,22,'','',($show > 0 ? false : true));
					} else {
						$plusbut = $we_button->create_button('image:btn_add_listelement',
								"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','$name','$i',1)",
								true,100,22,'','',($show > 0 ? false : true));
					}
					$trashbut = $we_button->create_button('image:btn_function_trash',
							"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','$name','$i','$names',1)");
					$buts = '';

					if ($showselect && $show > 0) {

						$buts = $we_button->create_button_table(
								array(

										$plusbut,
										$selectb,
										(($i > 0) ? $upbut : $upbutDis),
										(($i < ($listlen - 1)) ? $downbut : $downbutDis),
										$trashbut
								),
								5);
					} else {
						$buts = $we_button->create_button_table(
								array(

										$plusbut,
										(($i > 0) ? $upbut : $upbutDis),
										(($i < ($listlen - 1)) ? $downbut : $downbutDis),
										$trashbut
								),
								5);
					}
					if (preg_match('/^< ?(tr|td)/i', trim($foo))) {
						$foo = str_replace('=>', '#####PHPCALSSARROW####', $foo);
						$foo = str_replace('?>', '#####PHPENDBRACKET####', $foo);
						$foo = preg_replace('|(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)|si', '$1' . $buts . '$2$3', $foo,1);
						$foo = str_replace('#####PHPCALSSARROW####', '=>', $foo);
						$foo = str_replace('#####PHPENDBRACKET####', '?>', $foo);
					} else {
						$foo = $buts . $foo;
					}
				}
				$out .= $foo;
			}
		}
	}
	if ($GLOBALS['we_editmode']) {

		$show = 10;
		if ($limit && $limit > 0) {
			$diff = $limit - (isset($listlen) ? $listlen : 0);
			if ($diff > 0) {
				$show = min($show, $diff);
			} else {
				$show = 0;
			}
		}

		if ($show > 0 && !$isInListview) {
			if ($showselect) {
				$selectb = '<select name="' . $tmpname . '_00">';
				for ($j = 1; $j <= $show; $j++) {
					$selectb .= '<option value="' . $j . '">' . $j . '</option>';
				}
				$selectb .= '</select>';
				$plusbut = $we_button->create_button('image:btn_add_listelement',
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','$name',document.we_form.elements['" . $tmpname . "_00'].options[document.we_form.elements['" . $tmpname . "_00'].selectedIndex].text)",
						true,100,22,'','',($show > 0 ? false : true));
				$plusbut = $we_button->create_button_table(array(
					$plusbut, $selectb
				));
			} else {
				$plusbut = $we_button->create_button('image:btn_add_listelement',
						"javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','$name',1)",
						true,100,22,'','',($show > 0 ? false : true));
			}

			if (preg_match('/^< ?td/i', $content) || preg_match('/^< ?tr/i', $content)) {
				$foo = makeEmptyTable(rmPhp($content));
				$plusbut = preg_replace('|(< ?td[^>]*>)(.*)(< ?/ ?td[^>]*>)|i', '$1$2' . $plusbut . '$3', $foo,1);
			} else {
				$plusbut = '<p>' . $plusbut.'</p>';
			}
			$out .=  ('<input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . ']" value="' . htmlentities(
					serialize($list)) . '"><input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $name . '#content]" value="' . htmlspecialchars(
					$content) . '" />' . $plusbut);
		}

	}

	return $out;
}

function we_tag_blockControls($attribs, $content='') {
	//if in listview no Buttons are shown!
	if (!$GLOBALS['we_editmode'] || isset($GLOBALS['lv'])) {
		return '';
	}
	if (!isset($attribs['ctlName'])) {
		$attribs['ctlName'] = md5(uniqid(time()));
	}
	$we_button = new we_button();

	if ($attribs['pos'] < $attribs['listSize']) {
		$tabArray = array();
		if ($attribs['ctlShowSelect'] && $attribs['ctlShow'] > 0) {
			$tabArray[] = $we_button->create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "',document.we_form.elements['" . $attribs['ctlName'] . "_" . $attribs['pos'] . "'].options[document.we_form.elements['" . $attribs['ctlName'] . "_" . $attribs['pos'] . "'].selectedIndex].text)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
			$selectb = '<select name="' . $attribs['ctlName'] . '_' . $attribs['pos'] . '">';
			for ($j = 0; $j < $attribs['ctlShow']; $j++) {
				$selectb .= '<option value="' . ($j + 1) . '">' . ($j + 1) . '</option>';
			}
			$selectb .= '</select>';
			$tabArray[] = $selectb;
		} else {
			$tabArray[] = $we_button->create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('insert_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "',1)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
		}
		$tabArray[] = (($attribs['pos'] > 0) ?
										//enabled upBtn
										$we_button->create_button('image:btn_direction_up', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('up_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "')") :
										//disabled upBtn
										$we_button->create_button('image:btn_direction_up', '', true, 21, 22, '', '', true));
		$tabArray[] = (($attribs['pos'] == $attribs['listSize']) ?
										//disabled downBtn
										$we_button->create_button('image:btn_direction_down', '', true, 21, 22, '', '', true) :
										//enabled downBtn
										$we_button->create_button('image:btn_direction_down', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('down_entry_at_list','" . $attribs['name'] . "','" . $attribs['pos'] . "')"));
		$tabArray[] = $we_button->create_button('image:btn_function_trash', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('delete_list','" . $attribs['name'] . "','" . $attribs['pos'] . "','" . $attribs['name'] . "',1)");

		return $we_button->create_button_table($tabArray, 5);
	} else {

		if ($attribs['ctlShowSelect'] && $attribs['ctlShow'] > 0) {
			$selectb = '<select name="' . $attribs['ctlName'] . '_00">';
			for ($j = 1; $j <= $attribs['ctlShow']; $j++) {
				$selectb .= '<option value="' . $j . '">' . $j . '</option>';
			}
			$selectb .= '</select>';
			$plusbut = $we_button->create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list'," . $attribs['name'] . "',document.we_form.elements['" . $attribs['ctlName'] . "_00'].options[document.we_form.elements['" . $attribs['ctlName'] . "_00'].selectedIndex].text)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
			$plusbut = $we_button->create_button_table(array($plusbut, $selectb));
		} else {
			$plusbut = $we_button->create_button('image:btn_add_listelement', "javascript:setScrollTo();_EditorFrame.setEditorIsHot(true);we_cmd('add_entry_to_list','" . $attribs['name'] . "',1)", true, 100, 22, '', '', ($attribs['ctlShow'] > 0 ? false : true));
		}

		return '<input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $attribs['name'] . ']" value="' . htmlentities(
						serialize(isset($attribs['list'])?$attribs['list']:array())) . '"><input type="hidden" name="we_' . $GLOBALS["we_doc"]->Name . '_list[' . $attribs['name'] . '#content]" value="' .
		$content . '" />' . $plusbut;
	}
}
