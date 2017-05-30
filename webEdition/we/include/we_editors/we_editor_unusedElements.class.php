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
class we_editor_unusedElements extends we_editor_base{

	private function doRemove(){
		$db = $GLOBALS['DB_WE'];

//$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 2);
		$remove = we_base_request::_(we_base_request::INT, 'weg');
		if(we_base_request::_(we_base_request::STRING, 'do') == 'delete' && !empty($remove)){
			$delS = $delB = [];
			foreach($remove as $rem => $blockcnt){
				if($blockcnt){
					$delB[] = $rem;
				} else {
					$delS[] = $rem;
				}
			}

			if($delS){
				$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $this->we_doc->ID . ') AND DocumentTable="tblFile" AND Type!="attrib" AND nHash IN (x\'' . implode('\',x\'', $delS) . '\')'
				);
			}
			if($delB){
				$strs = $db->getAllq('SELECT DISTINCT SUBSTRING_INDEX(l.Name,"__",1) FROM ' . CONTENT_TABLE . ' c WHERE c.DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $this->we_doc->ID . ') AND c.DocumentTable="tblFile" AND c.Type!="attrib" AND c.nHash IN (x\'' . implode('\',x\'', $delB) . '\')', true);
				$db->query('DELETE FROM ' . CONTENT_TABLE . ' WHERE DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $this->we_doc->ID . ') AND DocumentTable="tblFile" AND Type!="attrib" AND SUBSTRING_INDEX(Name,"__",1) IN ("' . implode('","', $strs) . '")'
				);
			}
		}
	}

	public function show(){

		$this->doRemove();
		$db = $GLOBALS['DB_WE'];

		$tp = new we_tag_tagParser($this->we_doc->getTemplateCode(true, true));
		$relevantTags = [
			'normal' => [],
			'block' => [],
		];
//FIXME: we need to get the names of blocks
		foreach($tp->getTagsWithAttributes(true) as $tag){
			if(!empty($tag['attribs']['name'])){
				$isBlock = !empty($tag['attribs']['weblock']);
				$type = !$isBlock ? 'normal' : 'block';
				$name = $tag['attribs']['name'] . ($isBlock ? implode('', $tag['attribs']['weblock']) : '');
				$nHash = $isBlock ? $name : md5($name);

				if(!isset($relevantTags[$type][$nHash])){
					$relevantTags[$type][$nHash] = $tag['attribs']['name'];
					if(!$isBlock){
						switch($tag['name']){
							case 'img':
								$relevantTags[$type][md5($tag['attribs']['name'] . we_imageDocument::ALT_FIELD)] = $tag['attribs']['name'];
								$relevantTags[$type][md5($tag['attribs']['name'] . we_imageDocument::THUMB_FIELD)] = $tag['attribs']['name'];
								$relevantTags[$type][md5($tag['attribs']['name'] . we_imageDocument::TITLE_FIELD)] = $tag['attribs']['name'];
								break;
							case 'href':
								$relevantTags[$type][md5($tag['attribs']['name'] . we_base_link::MAGIC_INT_LINK)] = $tag['attribs']['name'];
								$relevantTags[$type][md5($tag['attribs']['name'] . we_base_link::MAGIC_INT_LINK_ID)] = $tag['attribs']['name'];
								$relevantTags[$type][md5($tag['attribs']['name'] . we_base_link::MAGIC_INT_LINK_EXTPATH)] = $tag['attribs']['name'];
						}
					}
				}
			}
		}

		//$allFields = $db->getAllq('SELECT c.Type,c.Name,IF(c.BDID,c.BDID,c.Dat) AS content FROM ' . CONTENT_TABLE . ' c WHERE c.DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $this->we_doc->ID . ') AND l.DocumentTable="tblFile" AND c.Type!="attrib" AND l.nHash NOT IN (x\'' . md5('Title') . '\',x\'' . md5('Description') . '\',x\'' . md5('Keywords') . '\') GROUP BY c.nHash');

		if(!empty($relevantTags['normal']) || !empty($relevantTags['block'])){
			$obsolete = $db->getAllq('SELECT c.Type,c.Name,HEX(c.nHash) AS nHash,COUNT(1) AS no,IF(c.BDID,c.BDID, SUBSTR(c.Dat,1,150)) AS content FROM ' . CONTENT_TABLE . ' c WHERE c.DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $this->we_doc->ID . ') AND c.DocumentTable="tblFile" AND c.Type!="attrib" AND c.nHash NOT IN (x\'' . md5('Title') . '\',x\'' . md5('Description') . '\',x\'' . md5('Keywords') . '\') ' . (empty($relevantTags['normal']) ? '' : 'AND c.nHash NOT IN (x\'' . implode('\',x\'', array_keys($relevantTags['normal'])) . '\') ') . (empty($relevantTags['block']) ? '' : ' AND SUBSTRING_INDEX(c.Name,"__",1) NOT IN ("' . implode('","', array_keys($relevantTags['block'])) . '")') . ' GROUP BY c.nHash ORDER BY c.Name');

			foreach($obsolete as &$ob){
				$bl = explode('blk_', $ob['Name'], 2);
				$cnt = 0;
				$ob['real'] = $bl[0];
				$ob['block'] = isset($bl[1]) ? preg_replace('|(__\d+)+|', '', str_replace('blk_', ' -> ', $bl[1], $cnt)) : '';
				$ob['blockcnt'] = isset($bl[1]) ? $cnt + 1 : $cnt;
				$ob['content'] = oldHtmlspecialchars($ob['content']);
			}
			usort($obsolete, function($a, $b){
				return $a['block'] == $b['block'] ?
						strcmp($a['real'], $b['real']) :
						strcmp($a['block'], $b['block']);
			});
		} else {
			$obsolete = [];
		}

		$table = new we_html_table(['class' => 'default middlefont', 'width' => '100%'], count($obsolete) + 1, 6);
		$table->setRowAttributes(0, ['class' => 'boxHeader']);
		$table->setColContent(0, 0, '');
		$table->setColContent(0, 1, g_l('weEditor', '[unusedElements][block]'));
		$table->setColContent(0, 2, g_l('weEditor', '[unusedElements][Name]'));
		$table->setColContent(0, 3, g_l('weEditor', '[unusedElements][type]'));
		$table->setColContent(0, 4, g_l('weEditor', '[unusedElements][count]'));
		$table->setColContent(0, 5, g_l('weEditor', '[unusedElements][content]'));
		foreach($obsolete as $pos => $cur){
			$row = $pos + 1;
			$table->setRowAttributes($row, ['class' => 'htmlDialogBorder4Cell']);
			$table->setColContent($row, 0, '<input type="checkbox" name="weg[' . $cur['nHash'] . ']" value="' . $cur['blockcnt'] . '"/>');
			$table->setColContent($row, 1, $cur['block']);
			$table->setColContent($row, 2, $cur['real']);
			$table->setColContent($row, 3, $cur['Type']);
			$table->setColContent($row, 4, $cur['no']);
			$table->setColContent($row, 5, $cur['content']);
		}

		$parts = [
			['headline' => g_l('weClass', '[unusedElementsTab]'),
				'html' => we_html_tools::htmlAlertAttentionBox(g_l('weClass', '[unusedElements][description]'), we_html_tools::TYPE_ALERT, 850, false)
			],
			['html' => $table->getHtml() .
				($obsolete ? we_html_button::create_button(we_html_button::TRASH, "javascript: WE().util.showConfirm(window, '','" . g_l('weClass', '[unusedElements][delete]') . "',['setDoReload','delete']);") : ''),
			],
				/*
				  array(
				  'headline' => 'Obsolete Elemente',
				  'html' => '<pre>' . print_r($obsolete, true) . '</pre>',
				  ),
				  array(
				  'headline' => 'debug',
				  'html' => '<pre>' . print_r($tp->getTagsWithAttributes(true), true) . '</pre>',
				  ),
				  array(
				  'headline' => 'Gefundene Elemente',
				  'html' => '<pre>' . print_r($relevantTags, true) . '</pre>',
				  ),
				  array(
				  'headline' => 'Elemente in DB',
				  'html' => '<pre>' . print_r($allFields, true) . '</pre>',
				  ), */
		];

		return $this->getPage(we_html_multiIconBox::getHTML('', $parts, 20) .
						we_html_element::htmlHiddens([
							'do' => ''
						]), we_html_element::jsScript(JS_DIR . 'we_editor_script.js'));
	}

}
