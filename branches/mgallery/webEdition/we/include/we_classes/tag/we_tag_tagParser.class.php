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
class we_tag_tagParser{

	private $lastpos = 0;
	private $tags = array();
	private static $CloseTags = array();
	private static $AllKnownTags = array();
	public static $curFile = '';

	//private $AppListviewItemsTags = array();

	public function __construct($content = '', $curFile = ''){
		self::$curFile = $curFile;
		//init Tags
		if($content != ''){
			$this->setAllTags($content);
		}
		/*
		 * if(!is_array(self::$CloseTags)){
		  self::$CloseTags = weTagWizard::getTagsWithEndTag();
		  self::$AllKnownTags = weTagWizard::getExistingWeTags();
		  } */
	}

	/* 	private function parseAppListviewItemsTags($tagname, $tag, $code, $attribs = "", $postName = ""){
	  return $this->replaceTag($tag, $code, $php);
	  }
	 */

	public function getAllTags(){
		return $this->tags;
	}

	private function setAllTags($code){
		$this->tags = array();
		$foo = array();
		preg_match_all('%</?we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*/?>?%i', $code, $foo, PREG_SET_ORDER);
		foreach($foo as $f){
			$this->tags[] = $f[0];
		}
	}

	/**
	 * @return	array
	 * @param	string $tagname
	 * @param	string $code
	 * @param	bool   $hasEndtag
	 * @desc		function separates a complete XML tag in several pieces
	 * 			returns array with this information
	 * 			tagname without <> .. for example "we:hidePages"
	 * 			[0][x] = complete Tag
	 * 			[1][x] = start tag
	 * 			[2][x] = parameter as string
	 */
	public static function itemize_we_tag($tagname, $code){
		$_matches = array();
		preg_match_all('/(<' . $tagname . '([^>]*)>)/U', $code, $_matches);
		return $_matches;
	}

	/* public function parseSpecificTags($tags, &$code, $postName = '', $ignore = array()){
	  $this->tags = $tags;
	  return $this->parseTags($code, ($postName == '' ? 0 : $postName), $ignore);
	  } */

	public function parseTags(&$code, $start = 0, $ende = FALSE){
		if(is_string($start)){//old call
			t_e('Tagparser called with old API - please Update your tag!');
			return;
		}
		if($start == 0 && ($tmp = $this->checkOpenCloseTags($code)) !== true){
			return $tmp;
		}
		$this->lastpos = 0;
		$ende = $ende ? : count($this->tags);
		for($ipos = $start; $ipos < $ende;){
			//t_e($ipos,$this->tags[$ipos],$ende);
			if($this->tags[$ipos]){
				$tmp = $this->parseTag($code, $ipos);
				if(!is_numeric($tmp)){
					//parser-error:
					return $tmp;
				}
				$this->tags[$ipos] = '';
				$ipos+=$tmp;
			} else {
				$ipos++;
			}
		}
		$this->lastpos = 0;
		return true;
	}

	private function checkOpenCloseTags(&$code){
		if(!is_array(self::$CloseTags)){
			self::$AllKnownTags = we_wizard_tag::getExistingWeTags();
			self::$CloseTags = we_wizard_tag::getTagsWithEndTag();
		}

		$Counter = array();

		foreach($this->tags as $_tag){
			$_matches = array();
			if(preg_match_all('|<(/?)we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*(/)?>?|smi', $_tag, $_matches)){
				if(!is_null($_matches[2][0]) && in_array($_matches[2][0], self::$CloseTags)){
					if(!isset($Counter[$_matches[2][0]])){
						$Counter[$_matches[2][0]] = 0;
					}

					if($_matches[1][0] === '/'){
						$Counter[$_matches[2][0]] --;
					} else {
						//selfclosing-Tag
						if($_matches[4][0] === '/'){
							continue;
						}
						$Counter[$_matches[2][0]] ++;
					}
				}
			}
		}

		$ErrorMsg = '';
		$err = '';
		$isError = false;
		foreach($Counter as $_tag => $_counter){
			if($_counter < 0){
				$err.=sprintf(g_l('parser', '[missing_open_tag]'), 'we:' . $_tag);
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_open_tag]') . ' (' . abs($_counter) . ')', 'we:' . $_tag));

				$isError = true;
			} elseif($_counter > 0){
				$err.=sprintf(g_l('parser', '[missing_close_tag]'), 'we:' . $_tag);
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_close_tag]') . ' (' . abs($_counter) . ')', 'we:' . $_tag));
				$isError = true;
			}
		}
		if($isError){
			$code = $ErrorMsg;
		}
		return (!$isError ? true : $err);
	}

	private function searchEndtag($tagname, $code, $tagPos, $ipos){
		$tagcount = 0;
		$endtags = array();

		$endtagpos = $tagPos;
		$regs = array();
		for($i = $ipos + 1; $i < count($this->tags); $i++){
			if(preg_match('|(< ?/ ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i], $regs)){
				$endtags[] = $regs[1];
				if($tagcount){
					$tagcount--;
				} else {
					// found endtag
					for($n = 0; $n < count($endtags); $n++){
						$endtagpos = strpos($code, $endtags[$n], $endtagpos + 1);
					}
					$this->tags[$i] = '';
					return array($endtagpos, $i);
				}
			} else {
				if(preg_match('|(< ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i])){
					$tagcount++;
				}
			}
		}
		return array(FALSE, FALSE);
	}

	public static function makeArrayFromAttribs($attr){
		$arr = array();
		@eval('$arr = array(' . self::parseAttribs($attr, false) . ');'); //FIXME: can we remove this eval?
		return $arr;
	}

	public static function parseAttribs($attr, $asArray){
		//remove comment-attribute (should never be seen), and obsolete cachelifetime
		$removeAttribs = array('cachelifetime', 'comment');
		$attribs = array();
		$regs = array();
		preg_match_all('/([^=]+)=[ \t]*"([^"]*)"/', $attr, $regs, PREG_SET_ORDER);

		if(!empty($regs)){
			foreach($regs as $f){
				if(!in_array($f[1], $removeAttribs)){
					$val = $f[2];
					if($asArray){
						$attribs[trim($f[1])] = $val;
					} else {
						$attribs[] = '"' . trim($f[1]) . '"=>' . ($val == 'true' || $val == 'false' || is_numeric($val) ? $val : '"' . $val . '"');
					}
				}
			}
		}
		return ($asArray ? $attribs : implode(',', $attribs));
	}

	public function getTagsWithAttributes(){
		$regs = array();
		$ret = array();
		foreach($this->tags as $tag){
			if(preg_match('%<we:([[:alnum:]_-]+)[ \t\n\r]*(.*)/?>?%msi', $tag, $regs)){
				$ret[] = array('name' => $regs[1], 'attribs' => (isset($regs[2]) ? self::parseAttribs($regs[2], true) : array()));
			}
		}
		return $ret;
	}

	private function parseTag(&$code, $ipos){
		$tag = $this->tags[$ipos];
		$regs = array();
		//$endTag = false;
		preg_match('%<(/?)we:([[:alnum:]_-]+)([ \t\n\r]*[[:alnum:]_-]+[ \t]*=[ \t]*"[^"]*")*[ \t\n\r]*(/?)(>?)%mi', $tag, $regs);
		$endTag = ($regs[1] === '/');
		if($endTag){
			//there should not be any endtags
			$code = str_replace($tag, '', $code);
			return 1;
		}

		$selfclose = ($regs[4] === '/');
		$gt = $regs[5];
		$tagname = $regs[2];

		//@Lukas: =>384
		if(!$gt){
			$data = '';
			for($i = $ipos - 2; $i < $ipos + 5; $i++){
				$data.="\n" . ($i == $ipos ? '!-> ' : '    ') . $this->tags[$i];
			}
			return parseError(sprintf(g_l('parser', '[incompleteTag]'), $tagname), 'Parsed tags around:' . $data);
		}
		//tags which need an endtag are not allowed to be selfclosing
		//FIXME: ok or not?
		//$selfclose&=!in_array($tagname, self::$CloseTags);
		preg_match('%</?we:[[:alnum:]_-]+[ \t\n\r]*(.*)' . $regs[4] . $regs[5] . '%msi', $regs[0], $regs);
		$attr = trim($regs[1]);

		$attribs = self::parseAttribs($attr, true);
		$attr = self::printArray($attribs);

		if(isset($attribs['name'])){
			$len = strlen($attribs['name']);
			if($len == 0){
				return parseError(sprintf(g_l('parser', '[name_empty]'), $tagname), $tag);
			} elseif($len > 255){
				return parseError(sprintf(g_l('parser', '[name_to_long]'), $tagname), $tag);
			}
		}

		if(!function_exists('we_tag_' . $tagname)){
			$ret = we_include_tag_file($tagname);
			if($ret !== true){
				return $ret;
			}
		}
		$tagPos = strpos($code, $tag, $this->lastpos);
		$this->lastpos = $tagPos;
		$endeStartTag = $tagPos + strlen($tag);
		if($selfclose){
			$content = '';
		} else {
			list($endTagPos, $endTagNo) = $this->searchEndtag($tagname, $code, $tagPos, $ipos);
			if($endTagPos !== FALSE){
				$endeEndTagPos = strpos($code, '>', $endTagPos) + 1;
				$content = substr($code, $endeStartTag, ($endTagPos - $endeStartTag));
				//only 1 exception: comment tag should be able to contain partly invalid code (e.g. missing attributes etc)
				if(($tagname != 'comment') && (($ipos + 1) < $endTagNo)){
					$tmp = $this->parseTags($content, ($ipos + 1), $endTagNo);
					if(is_string($tmp)){
						//parser-error:
						return $tmp;
					}
				}
			} else {
				//FIXME: remove in 6.4
				//if it is not a selfclosing tag (<we:xx/>),
				//there exists a tagWizzard file, and in this file this tag is stated to be selfclosing
				//NOTE: most custom tags don't have a wizzard file - so this tag must be selfclosing, or have a corresponding closing-tag!
				if(!$selfclose && in_array($tagname, self::$AllKnownTags) && !in_array($tagname, self::$CloseTags)){
					//for now we'll correct this error and keep parsing
					$selfclose = true;
					$content = '';
					unset($endeEndTagPos);
					unset($endTagPos);
					unset($endTagNo);
					//don't break for now.
					parseError(sprintf('Compatibility MODE of parser - Note this will soon be removed!' . "\n" . g_l('parser', '[start_endtag_missing]'), $tagname));
				} else {
					return parseError(sprintf(g_l('parser', '[start_endtag_missing]'), $tagname), $tag);
				}
			}
		}

		//t_e($tag, $tagPos, $endeStartTag, $endTagPos, $ipos, $content,$this->tags);
		$parseFn = 'we_parse_tag_' . $tagname;
		if(function_exists($parseFn)){
			/* call specific function for parsing this tag
			 * $attribs is the attribs string, $content is content of this tag
			 * return value is parsed again and inserted
			 */
			$content = $parseFn($attr, $content, $attribs);
			$code = substr($code, 0, $tagPos) .
					$content .
					substr($code, (isset($endeEndTagPos) ? $endeEndTagPos : $endeStartTag));
		} elseif(substr($tagname, 0, 2) === 'if' && $tagname !== 'ifNoJavaScript'){
			if(!isset($endeEndTagPos)){
				return parseError(sprintf(g_l('parser', '[selfclosingIf]'), $tagname), $tag);
			}
			$code = substr($code, 0, $tagPos) .
					'<?php if(' . self::printTag($tagname, $attr) . '){ ?>' .
					$content .
					'<?php } ?>' .
					substr($code, $endeEndTagPos);
		} else {

			$code = substr($code, 0, $tagPos) . '<?php printElement(' .
					($content ?
							// Tag besitzt Endtag
							self::printTag($tagname, $attr, $content, true) . '); ?>' :
							// Tag ohne Endtag
							self::printTag($tagname, $attr) . '); ?>'
					) . substr($code, (isset($endeEndTagPos) ? $endeEndTagPos : $endeStartTag));
		}
		return (isset($endTagNo) ? ($endTagNo - $ipos) : 1);
	}

	//FIXME: GLOBALS as "\$xx" should be set here directly? using isset??
	public static function printTag($name, $attribs = '', $content = '', $cslash = false){
		$attr = (is_array($attribs) ? self::printArray($attribs, false) : ($attribs === 'array()' || $attribs === '[]' ? '' : $attribs));
		return 'we_tag(\'' . $name . '\'' .
				($attr ? ',' . $attr : ($content ? ',array()' : '')) .
				($content ? ',"' . ($cslash ? addcslashes($content, '"') : $content) . '"' : '') . ')';
	}

	public static function printArray(array $array, $printEmpty = true){
		$ret = array();
		foreach($array as $key => $val){
			switch($key){
				case 'comment':
				case 'cachelifetime':
					continue;
				default:
					$quotes = ((strpos($val, '$') !== FALSE) || (strpos($val, '\'') !== FALSE) ? '"' : '\'');
					$ret[] = '\'' . $key . '\'=>' . ((is_numeric($val) && $val{0} != '+') || $val == 'true' || $val == 'false' ? $val : $quotes . $val . $quotes);
			}
		}
		$newTag = PHP_VERSION_ID >= 50400;
		return ($ret || (!$ret && $printEmpty) ? ($newTag ? '[' : 'array(') . implode(',', $ret) . ($newTag ? ']' : ')') : '');
	}

}
