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
/*
  / only used for direct expression input ( exp: ) from old search of slavko
 */

class we_search_exp extends we_search_base{

	var $Operators = array(
		'!=' => '<>', '=' => '=', '<>' => '<>', '<' => '<', '>' => '>'
	);
	var $FieldMap = array(
		'id' => 'ID',
		'path' => 'Path',
		'text' => 'Text',
		'doctype' => 'DocType',
		'category' => 'Category',
		'contenttype' => 'ContentType',
		'isfolder' => 'IsFolder',
		'templateid' => 'TemplateID',
		'parentid' => 'ParentID',
		'tableid' => 'TableID',
		'mastertemplateid' => 'MasterTemplateID'
	);

	function getSearchResults($keyword, $options, $res_num = 0){

		$exp_pos = strpos($keyword, 'exp:');
		$items = [];

		$keyword = trim($keyword);

		if($exp_pos !== false){
			$items = $this->evaluateExp(substr($keyword, $exp_pos + 4), $options, $res_num);
			$keyword = trim(substr($keyword, 0, $exp_pos));
		}

		return $items;
	}

	function evaluateExp($keyword, $options, $res_num = 0){
		$keyword = $this->normalize($keyword);
		$tokens = $this->translateOperators($this->tokenize($keyword));
		$tables = $options;
		$condition = $this->constructCondition($tokens);

		$result = [];

		foreach($tables as $table){
			$this->db->query('SELECT * FROM ' . $table . ' ' . $condition);

			while($this->next_record()){
				$result[] = array_merge(array(
					'Table' => $table
					), $this->db->Record);
			}
		}

		return $result;
	}

	function fixFieldNames($name){

		foreach($this->FieldMap as $k => $v){
			if(preg_match('%^' . $k . '$%i', $name)){
				return $v;
			}
		}

		return $name;
	}

	function normalize($keyword){
		foreach($this->Operators as $operator){
			$keyword = preg_replace('/[ ]*' . $operator . '[ ]*/i', $operator, $keyword);
		}
		return $keyword;
	}

	function tokenize($keyword){

		$array = [];

		$array['AND'] = [];
		$array['OR'] = [];
		$array['AND NOT'] = [];

		$ident = 'AND';
		$array[$ident][0] = '';

		$word = '';

		for($i = 0; $i < strlen($keyword); $i++){

			$word .= $keyword[$i];

			if($keyword[$i] === ' '){

				switch(strtolower(trim($word))){
					case 'and' :
						$ident = 'AND';
						$array[$ident][] = '';
						break;

					case 'or' :
						$ident = 'OR';
						$array[$ident][] = '';
						break;

					case 'not' :
						$ident = 'AND NOT';
						$array[$ident][] = '';
						break;
					default :
						$count = count($array[$ident]) - 1;
						$array[$ident][$count] .= $word;
				}
				$word = '';
			}
		}

		$array[$ident][count($array[$ident]) - 1] .= $word;

		return $array;
	}

	function translateOperators($tokens){

		$tokens = $tokens;
		foreach($tokens as $lop => $slots){
			foreach($slots as $key => $value){
				$tokens[$lop][$key] = $this->getExpression($value);
			}
		}
		return $tokens;
	}

	function replaceSpecChars($string){
		return trim(str_replace('*', '%', trim($string)), '"\'');
	}

	function getExpression($string){
		$arr = [];

		foreach($this->Operators as $k => $v){
			if(preg_match('_' . $k . '_', $string)){
				$arr = explode($k, $string);
				$expr = array(
					'operand1' => trim($this->fixFieldNames($arr[0])),
					'operator' => trim($v),
					'operand2' => trim($this->replaceSpecChars(stripslashes($arr[1])))
				);

				if($expr['operator'] === '=' && strpos($expr['operand2'], '%') !== false){
					$expr['operator'] = 'LIKE';
				}

				if(($expr['operator'] === '!=' || $expr['operator'] === '<>') && strpos($expr['operand2'], '%') !== false){
					$expr['operator'] = 'NOT LIKE';
				}

				$this->getTransaltedExpression($expr);

				if(!$this->isField($expr['operand1'])){
					$expr['operand1'] = implode('', $expr);
					unset($expr['operator']);
					unset($expr['operand2']);
				}

				break;
			}
		}

		if(!isset($expr)){
			$expr['operand1'] = $string;
		}

		return $expr;
	}

	function getTransaltedExpression(&$expr){

		if(($expr['operand1'] === 'DocType')){
			if(strpos($expr['operand2'], '\*') !== false){
				$expr['operand2'] = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . str_replace("*", "%", $expr['operand2']) . '"', 'ID', new DB_WE());
			} else {
				$expr['operand2'] = f('SELECT ID FROM ' . DOC_TYPES_TABLE . ' WHERE DocType LIKE "' . $expr['operand2'] . '"', 'ID', new DB_WE());
			}
			// if operand2 is empty make some impossible condition
			if(empty($expr['operand2']) && ($expr['operator'] === 'LIKE' || $expr['operator'] === '=')){
				$expr['operand2'] = md5(uniqid(__FUNCTION__, true));
			}
		}

		if(($expr['operand1'] === 'Category')){
			$expr['operand2'] = ',' . f('SELECT ID FROM ' . CATEGORY_TABLE . ' WHERE Text="' . $expr['operand2'] . '"', 'ID', new DB_WE()) . ',';
			if($expr['operator'] === '='){
				$expr['operator'] = 'LIKE';
			}
			if($expr['operator'] === '!='){
				$expr['operator'] = 'NOT LIKE';
			}
			// if operand2 is empty make some impossible condition
			if(empty($expr['operand2']) && $expr['operator'] === 'LIKE'){
				$expr['operand2'] = md5(uniqid(__FUNCTION__, true));
			}
		}

		if(strpos($expr['operand2'], '\*') !== false){
			$expr['operator'] = 'LIKE';
			$expr['operand2'] = str_replace("*", "%", $expr['operand2']);
		}
	}

	function isField($field){
		return in_array($field, $this->FieldMap);
	}

	function getTables($options){
		$tables = [];
		foreach($options as $option => $value){
			if($value && $option == FILE_TABLE && permissionhandler::hasPerm('CAN_SEE_DOCUMENTS')){
				$tables[] = FILE_TABLE;
			}
			if($value && $option == TEMPLATES_TABLE && permissionhandler::hasPerm('CAN_SEE_TEMPLATES')){
				$tables[] = TEMPLATES_TABLE;
			}
			if(defined('OBJECT_FILES_TABLE') && $value && $option == OBJECT_FILES_TABLE && permissionhandler::hasPerm('CAN_SEE_OBJECTFILES')){
				$tables[] = OBJECT_FILES_TABLE;
			}
			if(defined('OBJECT_TABLE') && $value && $option == OBJECT_TABLE && permissionhandler::hasPerm('CAN_SEE_OBJECTS')){
				$tables[] = OBJECT_TABLE;
			}
		}
		return $tables;
	}

	function constructCondition(&$tokens){

		$condition = '';
		foreach($tokens as $log => $token){
			$word = [];
			$conditions = [];
			foreach($token as $op){
				if(count($op) < 3){
					$word[] = ' ' . $op['operand1'] . ' ';
				} else {
					$word[] = $op['operand1'] . ' ' . $op['operator'] . ' "' . addslashes($op['operand2']) . '"';
				}
			}
			if(!empty($word)){
				$conditions[] = implode(' ' . $log . ' ', $word);
			}

			if(!empty($conditions)){
				if(empty($condition)){
					$condition .= implode(' ' . $log . ' ', $conditions);
				} else {
					$condition .= ' ' . $log . ' ' . implode(' ' . $log . ' ', $conditions);
				}
			}
		}

		return ($condition ? ' WHERE ' . $condition . ' ' : '');
	}

}
