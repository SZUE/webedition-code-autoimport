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
class we_tag_tagParser{

	private $lastpos = 0;
	private $tags = array();
	private static $CloseTags = 0;

	//private $AppListviewItemsTags = array();

	public function __construct($content=''){
		//init Tags
		if($content != ''){
			$this->setAllTags($content);
		}
		if(!is_array(self::$CloseTags)){
			self::$CloseTags = weTagWizard::getTagsWithEndTag();
		}
	}

	/* 	private function parseAppListviewItemsTags($tagname, $tag, $code, $attribs = "", $postName = ""){
	  return $this->replaceTag($tag, $code, $php);
	  }
	 */

	public static function getNames($tags){
		$names = array();
		$ll = 0;
		$l = 0;
		$b = 0;
		for($i = 0; $i < sizeof($tags); $i++){
			if($ll == 0 && $l == 0 && $b == 0){
				if(eregi('name ?= ?"([^"]+)"', $tags[$i], $regs)){
					if(!in_array($regs[1], $names))
						array_push($names, $regs[1]);
				}
			}
			if(preg_match('/< ?we:(list|linklist) /i', $tags[$i])){
				$ll++;
			} else
			if(preg_match('|< ?we:block |i', $tags[$i])){
				$b++;
			} else
			if(preg_match('-< ?/ ?we:(list|linklist) -i', $tags[$i])){
				$ll--;
			} else
			if(preg_match('|< ?/ ?we:block |i', $tags[$i])){
				$b--;
			}
		}
		return $names;
	}

	public function getAllTags(){
		return $this->tags;
	}

	private function setAllTags($code){
		$this->tags = array();
		$foo = array();
		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $foo, PREG_SET_ORDER);
		foreach($foo as $f){
			if(substr($f[1], -1) == '<'){
				$f[1] = substr($f[1], 0, strlen($f[1]) - 1);
			}
			array_push($this->tags, $f[1]);
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

		preg_match_all('/(<' . $tagname . '([^>]*)>)/U', $code, $_matches);
		return $_matches;
	}

	/**
	 * @return string of code with all required tags
	 * @param $code Src Code
	 * @desc Searches for all meta-tags in a given template (title, keyword, description, charset)
	 */
	public static function getMetaTags($code){
		$_tmpTags = array();
		$_foo = array();
		$_rettags = array();

		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $_foo, PREG_SET_ORDER);

		foreach($_foo as $f){
			if(substr($f[1], -1) == '<'){
				$f[1] = substr($f[1], 0, strlen($f[1]) - 1);
			}
			array_push($_tmpTags, $f[1]);
		}

		//	only Meta-tags, description, keywords, title and charset
		$_tags = array();
		foreach($_tmpTags as $t){
			if(strpos($t, 'we:title') || strpos($t, 'we:description') || strpos(
					$t, 'we:keywords') || strpos($t, 'we:charset')){
				$_tags[] = $t;
			}
		}
		//	now we need all between these tags - beware of selfclosing tags


		for($i = 0; $i < sizeof($_tags);){

			if(preg_match("|<we:(.*)/(.*)>|i", $_tags[$i])){ //  selfclosing xhtml-we:tag
				$_start = strpos($code, $_tags[$i]);
				$_starttag = $_tags[$i];

				$_endtag = '';
				$i++;
			} else{ //  "normal" we:tag
				$_start = strpos($code, $_tags[$i]);
				$_starttag = $_tags[$i];
				$i++;

				$_end = strpos($code, $_tags[$i]) - $_start + strlen($_tags[$i]);
				$_endtag = isset($_tags[$i]) ? $_tags[$i] : '';
				$i++;
			}
			array_push($_rettags, array(
				array(
					$_starttag, $_endtag
				), $_endtag ? substr($code, $_start, $_end) : ''
			));
		}
		return $_rettags;
	}

	public function parseSpecificTags($tags, &$code, $postName = '', $ignore = array()){
		$this->tags = $tags;
		return $this->parseTags($code, ($postName==''?0:$postName), $ignore);
	}

	public function parseTags(&$code,$start=0,$ende=FALSE){
		if(is_string($start)){//old call
			$start=0;
			$ende=FALSE;
			t_e('Tagparser called with old API - please Update your tag!');
		}
		if($start==0 && ($tmp = self::checkOpenCloseTags($this->tags, $code)) !== true){
			return $tmp;
		}
		$this->lastpos = 0;
		$ende=$ende?$ende:sizeof($this->tags);
		for($ipos = $start; $ipos < $ende; ){
				if($this->tags[$ipos]){
					$tmp=$this->parseTag($code,$ipos); //	dont add postname tagname in ignorearray
					$this->tags[$ipos]='';
					$ipos+=$tmp;
				}else{
					$ipos++;
				}
		}
			$this->lastpos = 0;
		return true;
	}

	private static function checkOpenCloseTags($TagsInTemplate, &$code){
		include_once(WEBEDITION_INCLUDES_DIR . 'weTagWizard/classes/weTagWizard.class.php');
		if(!is_array(self::$CloseTags)){
			self::$CloseTags = weTagWizard::getTagsWithEndTag();
		}

		$Counter = array();

		foreach($TagsInTemplate as $_tag){
			if(preg_match_all("/<(\/|)we:([a-z]*)(.*)>/si", $_tag, $_matches)){
				if(!is_null($_matches[2][0]) && in_array($_matches[2][0], self::$CloseTags)){
					if(!isset($Counter[$_matches[2][0]])){
						$Counter[$_matches[2][0]] = 0;
					}
					if($_matches[1][0] == '/'){
						$Counter[$_matches[2][0]]--;
					} else{
						$Counter[$_matches[2][0]]++;
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
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_open_tag]'), 'we:' . $_tag));

				$isError = true;
			} else
			if($_counter > 0){
				$err.=sprintf(g_l('parser', '[missing_close_tag]'), 'we:' . $_tag);
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_close_tag]'), 'we:' . $_tag));
				$isError = true;
			}
		}
		if($isError){
			$code = $ErrorMsg;
		}
		return (!$isError ? true : $err);
	}

	private function searchEndtag($tagname,$code, $tagPos,$ipos){
		$tagcount = 0;
		$endtags = array();

		$endtagpos = $tagPos;
		for($i = $ipos + 1; $i < sizeof($this->tags); $i++){
			if(preg_match('|(< ?/ ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i], $regs)){
				array_push($endtags, $regs[1]);
				if($tagcount){
					$tagcount--;
				} else{
					// found endtag
					for($n = 0; $n < sizeof($endtags); $n++){
						$endtagpos = strpos($code, $endtags[$n], $endtagpos + 1);
					}
					$this->tags[$i]='';
					return array($endtagpos,$i);
				}
			} else{
				if(preg_match('|(< ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i])){
					$tagcount++;
				}
			}
		}
		return array(FALSE,FALSE);
	}

	/* 	function getNameAndAttribs($tag) {
	  if (preg_match('/<we:([^ ]+) ([^>]+)>/i', $tag, $_regs)) {
	  $_attribsString = $_regs[2];
	  $_tmpAttribs = '';
	  $_attribs = array();
	  if (preg_match_all('/([^=]+)= *("[^"]*")/', $_attribsString, $foo, PREG_SET_ORDER)) {
	  for ($i = 0; $i < sizeof($foo); $i++) {
	  $_tmpAttribs .= '"' . trim($foo[$i][1]) . '"=>' . trim($foo[$i][2]) . ',';
	  }
	  eval("\$_attribs = array(" . preg_replace('/(.+),$/', "\$1", $_tmpAttribs) . ");");
	  }
	  return array(
	  $_regs[1], $_attribs
	  );
	  }
	  return null;
	  } */

	private function parseTag(&$code,$ipos){
		$tag = $this->tags[$ipos];

		//$endTag = false;
		preg_match("|<(/?)we:(.+)(/?)>?|i", $tag, $regs);
		$endTag = (bool) ($regs[1]);
		if($endTag){
			//there should not be any endtags
			$code = str_replace($tag, '', $code);
			return 1;
		}

		$selfclose = (bool) ($regs[3]);
		ereg("([^ >/]+) ?(.*)", $regs[2] . '/', $regs);
		$tagname = $regs[1];
		$selfclose|=!in_array($tagname, self::$CloseTags);
		$attr = trim(rtrim($regs[2], '/'));

		//FIXME: remove?!
		if(preg_match('|name="([^"]*)"|i', $attr, $regs)){
			if(!$regs[1]){
				print parseError(sprintf(g_l('parser', '[name_empty]'), $tagname));
			} else
			if(strlen($regs[1]) > 255){
				print parseError(sprintf(g_l('parser', '[name_to_long]'), $tagname));
			}
		}

		$attribs = '';
		preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);

		//remove comment-attribute (should never be seen), and obsolete cachelifetime
		$attr = removeAttribs($foo, array('cachelifetime', 'comment'));

		foreach($foo as $f){
			$attribs .= '"' . trim($f[1]) . '"=>' . trim($f[2]) . ',';
		}

		$arrstr = 'array(' . rtrim($attribs, ',') . ')';

		@eval('$arr = ' . ereg_replace('"\$([^"]+)"', '"$GLOBALS[\1]"', $arrstr) . ';');
		if(!isset($arr)){
			$arr = array();
		}

		if(!function_exists('we_tag_' . $tagname)){
			we_include_tag_file($tagname);
		}
		$tagPos = strpos($code, $tag, $this->lastpos);
		$this->lastpos = $tagPos;
		$endeStartTag = $tagPos + strlen($tag);
		if($selfclose){
			$content = '';
		} else{
			list($endTagPos,$endTagNo) = $this->searchEndtag($tagname,$code, $tagPos,$ipos);

			if($endTagPos !== FALSE){
				$endeEndTagPos = strpos($code, '>', $endTagPos) + 1;
				$content = substr($code, $endeStartTag, ($endTagPos - $endeStartTag));
				//only 1 exception: comment tag should be able to contain partly invalid code (e.g. missing attributes etc)
				if(($tagname!='comment') && (($ipos+1)<$endTagNo)){
					$this->parseTags($content,($ipos+1),$endTagNo);
				}
			} else{
				t_e('Internal Parser Error: endtag for ' . $tag . ' not found', $code);
			}
		}
		$attribs = str_replace('=>"\$', '=>"$', 'array(' . rtrim($attribs, ',') . ')'); // workarround Bug Nr 6318
		//t_e($tag, $tagPos, $endeStartTag, $endTagPos, $ipos, $content,$this->tags);

		$parseFn = 'we_parse_tag_' . $tagname;
		if(function_exists($parseFn)){
			/* call specific function for parsing this tag
			 * $attribs is the attribs string, $content is content of this tag
			 * return value is parsed again and inserted
			 */
			$content = $parseFn($attribs, $content);

			$code = substr($code, 0, $tagPos) .
				$content .
				substr($code, (isset($endeEndTagPos) ? $endeEndTagPos : $endeStartTag));
		} else

		/* 			if(in_array($tagname, $this->AppListviewItemsTags)){// for App-Tags of type listviewitems
		  $code = $this->parseAppListviewItemsTags($tagname, $tag, $code, $attribs, $postName);
		  $this->lastpos = 0;
		  } else */
		if(substr($tagname, 0, 2) == "if" && $tagname != "ifNoJavaScript"){
			$code = substr($code, 0, $tagPos) .
				'<?php if(' . self::printTag($tagname, $attribs) . '){ ?>' .
				$content .
				'<?php } ?>' .
				substr($code, $endeEndTagPos);
		} else{

			// Tag besitzt Endtag
			if($content){
				$code = substr($code, 0, $tagPos) . '<?php printElement(' . self::printTag($tagname, $attribs, $content, true) . '); ?>' . substr(
						$code, $endeEndTagPos);
			} else{
				$code = substr($code, 0, $tagPos) . '<?php printElement(' . self::printTag($tagname, $attribs) . '); ?>' . substr(
						$code, $endeStartTag);
			}
		}
		return (isset($endTagNo)?($endTagNo-$ipos):1);
	}

	public static function printTag($name, $attribs='', $content='', $cslash=false){
		$attr = (is_array($attribs) ? self::printArray($attribs) : $attribs);
		return 'we_tag(\'' . $name . '\'' .
			($attr != '' ? ',' . $attr : ($content != '' ? ',array()' : '')) .
			($content != '' ? ',"' . ($cslash ? addcslashes($content, '"') : $content) . '"' : '') . ')';
	}

	public static function printArray($array){
		$ret = '';
		foreach($array as $key => $val){
			$ret.='\'' . $key . '\'=>\'' . $val . '\',';
		}
		return 'array(' . $ret . ')';
	}

}
