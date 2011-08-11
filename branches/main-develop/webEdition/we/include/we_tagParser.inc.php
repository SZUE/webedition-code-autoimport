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
class we_tagParser {

	private $lastpos = 0;
	private $tags = array();
	private $ipos = 0;
	private $AppListviewItemsTags = array();

	public function __construct($content='') {
		//init Tags
		if($content!=''){
			$this->setAllTags($content);
		}
	}
	
	private function parseAppListviewItemsTags($tagname, $tag, $code, $attribs = "", $postName = "") {
		return $this->replaceTag($tag, $code, $php);
	}

	public static function getNames($tags) {
		$names = array();
		$ll = 0;
		$l = 0;
		$b = 0;
		for ($i = 0; $i < sizeof($tags); $i++) {
			if ($ll == 0 && $l == 0 && $b == 0) {
				if (eregi('name ?= ?"([^"]+)"', $tags[$i], $regs)) {
					if (!in_array($regs[1], $names))
						array_push($names, $regs[1]);
				}
			}
			if (preg_match('|< ?we:linklis t|i', $tags[$i])) {
				$ll++;
			} else
			if (preg_match('|< ?we:list |i', $tags[$i])) {
				$l++;
			} else
			if (preg_match('|< ?we:block |i', $tags[$i])) {
				$b++;
			} else
			if (preg_match('|< ?/ ?we:linklist |i', $tags[$i])) {
				$ll--;
			} else
			if (preg_match('|< ?/ ?we:list |i', $tags[$i])) {
				$l--;
			} else
			if (preg_match('|< ?/ ?we:block |i', $tags[$i])) {
				$b--;
			}
		}
		return $names;
	}

	public function getAllTags() {
		return $this->tags;
	}
	
	private function setAllTags($code) {
		$this->tags = array();
		$foo = array();
		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $foo, PREG_SET_ORDER);
		foreach ($foo as $f) {
			if (substr($f[1], -1) == '<') {
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
	public static function itemize_we_tag($tagname, $code) {

		preg_match_all('/(<' . $tagname . '([^>]*)>)/U', $code, $_matches);
		return $_matches;
	}

	/**
	 * @return string of code with all required tags
	 * @param $code Src Code
	 * @desc Searches for all meta-tags in a given template (title, keyword, description, charset)
	 */
	public static function getMetaTags($code) {
		$_tmpTags = array();
		$_foo = array();
		$_rettags = array();

		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $_foo, PREG_SET_ORDER);

		foreach ($_foo as $f) {
			if (substr($f[1], -1) == '<') {
				$f[1] = substr($f[1], 0, strlen($f[1]) - 1);
			}
			array_push($_tmpTags, $f[1]);
		}

		//	only Meta-tags, description, keywords, title and charset
		$_tags = array();
		foreach ($_tmpTags as $t) {
			if (strpos($t, 'we:title') || strpos($t, 'we:description') || strpos(
											$t, 'we:keywords') || strpos($t, 'we:charset')) {
				$_tags[] = $t;
			}
		}
		//	now we need all between these tags - beware of selfclosing tags


		for ($i = 0; $i < sizeof($_tags);) {

			if (preg_match("|<we:(.*)/(.*)>|i", $_tags[$i])) { //  selfclosing xhtml-we:tag
				$_start = strpos($code, $_tags[$i]);
				$_starttag = $_tags[$i];

				$_endtag = '';
				$i++;
			} else { //  "normal" we:tag
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

	public function parseSpecificTags($tags,&$code, $postName = '', $ignore = array()) {
		$this->tags=$tags;
		return parseTags(&$code, $postName, $ignore);
	}
	
	public function parseTags(&$code, $postName = '', $ignore = array()) {

		if (!defined('DISABLE_TEMPLATE_TAG_CHECK') || !DISABLE_TEMPLATE_TAG_CHECK) {
			if (!self::checkOpenCloseTags($this->tags, $code)) {
				return;
			}
		}

		$this->lastpos = 0;
		$this->ipos = 0;
		while ($this->ipos < sizeof($this->tags)) {
			$this->lastpos = 0;

			if (in_array(substr(ereg_replace("[>/ ].*", '', $this->tags[$this->ipos]), 1), $ignore)) {
				$this->parseTag($code); //	dont add postname tagname in ignorearray
			} else {
				$this->parseTag($code, $postName);
			}
		}
	}

	private static function checkOpenCloseTags($TagsInTemplate, &$code) {

		$CloseTags = array('listview', 'listdir', 'block');

		$Counter = array();

		foreach ($TagsInTemplate as $_tag) {
			if (preg_match_all("/<(\/|)we:([a-z]*)(.*)>/si", $_tag, $_matches)) {
				if (!is_null($_matches[2][0]) && in_array($_matches[2][0], $CloseTags)) {
					if (!isset($Counter[$_matches[2][0]])) {
						$Counter[$_matches[2][0]] = 0;
					}
					if ($_matches[1][0] == '/') {
						$Counter[$_matches[2][0]]--;
					} else {
						$Counter[$_matches[2][0]]++;
					}
				}
			}
		}

		$ErrorMsg = '';
		$isError = false;
		foreach ($Counter as $_tag => $_counter) {
			if ($_counter < 0) {
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_open_tag]'), 'we:' . $_tag));
				$isError = true;
			} else
			if ($_counter > 0) {
				$ErrorMsg .= parseError(sprintf(g_l('parser', '[missing_close_tag]'), 'we:' . $_tag));
				$isError = true;
			}
		}
		if ($isError) {
			$code = $ErrorMsg;
		}
		return!$isError;
	}

	private function searchEndtag($code, $tagPos) {
		preg_match('|we:([^ >]+)|i', $this->tags[$this->ipos], $regs);
		$tagname = $regs[1];

		if ($tagname != 'back' && $tagname != 'next' && $tagname != 'printVersion' && $tagname != 'listviewOrder') {
			$tagcount = 0;
			$endtags = array();

			$endtagpos = $tagPos;

			for ($i = $this->ipos + 1; $i < sizeof($this->tags); $i++) {
				if (preg_match('|(< ?/ ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i], $regs)) {
					array_push($endtags, $regs[1]);
					if ($tagcount) {
						$tagcount--;
					} else {
						// found endtag
						$this->ipos = $i + 1;
						for ($n = 0; $n < sizeof($endtags); $n++) {
							$endtagpos = strpos($code, $endtags[$n], $endtagpos + 1);
						}
						$this->ipos = $i + 1;
						return $endtagpos;
					}
				} else {
					if (preg_match('|(< ?we ?: ?' . $tagname . '[^a-z])|i', $this->tags[$i])) {
						$tagcount++;
					}
				}
			}
		}
		$this->ipos++;
		return -1;
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

	private function parseTag(&$code, $postName = '') {
		$tag = $this->tags[$this->ipos];
		if (!$tag) {
			return;
		}
		$tagPos = -1;

		//$endTag = false;
		preg_match("|<(/?)we:(.+)(/?)>?|i", $tag, $regs);
		$endTag = (bool) ($regs[1]);
		$selfclose = (bool) ($regs[3]);
		$foo = $regs[2] . '/';
		ereg("([^ >/]+) ?(.*)", $foo, $regs);
		$tagname = $regs[1];
		$attr = trim(rtrim($regs[2], '/'));

		//FIXME: remove?!
		if (preg_match('|name="([^"]*)"|i', $attr, $regs)) {
			if (!$regs[1]) {
				print parseError(sprintf(g_l('parser', '[name_empty]'), $tagname));
			} else
			if (strlen($regs[1]) > 255) {
				print parseError(sprintf(g_l('parser', '[name_to_long]'), $tagname));
			}
		}

		$attribs = '';
		preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);

		//remove comment-attribute (should never be seen), and obsolete cachelifetime
		$attr = removeAttribs($foo, array('cachelifetime', 'comment'));

		foreach ($foo as $f) {
			$attribs .= '"' . trim($f[1]) . '"=>' . trim($f[2]) . ',';
		}

		if (!$endTag) {
			$arrstr = 'array(' . rtrim($attribs, ',') . ')';

			@eval('$arr = ' . ereg_replace('"\$([^"]+)"', '"$GLOBALS[\1]"', $arrstr) . ';');
			if (!isset($arr)) {
				$arr = array();
			}

			$parseFn = 'we_parse_tag_' . $tagname;
			if ((we_include_tag_file($tagname) === true) && function_exists($parseFn)) {
				$pre = $post = $content = '';
				if (!$selfclose) {
					$tagPos = strpos($code, $tag, $this->lastpos);
					$endeStartTag = $tagPos + strlen($tag);
					$endTagPos = $this->searchEndtag($code, $tagPos);

					if ($endTagPos > -1) {
						$endeEndTagPos = strpos(
														$code, ">", $endTagPos) + 1;
						if ($endTagPos > $endeStartTag) {
							$content = substr(
											$code, $endeStartTag, ($endTagPos - $endeStartTag));
						}
					}
				}
				$attribs = str_replace('=>"\$', '=>"$', 'array(' . rtrim($attribs, ',') . ')'); // workarround Bug Nr 6318
				/* call specific function for parsing this tag
				 * $attribs is the attribs string, $content is content of this tag
				 * return value is parsed again and inserted
				 */
				$content = $parseFn($attribs, $content);
//FIXME: make this linear -> modify $tags
				
				$tp = new we_tagParser($content);
				$tp->parseTags($content);
				$code = substr($code, 0, $tagPos) .
								$content .
								substr($code, (isset($endeEndTagPos)?$endeEndTagPos:$endeStartTag));
				return;
			}

			if (in_array($tagname, $this->AppListviewItemsTags)) {// for App-Tags of type listviewitems
				$code = $this->parseAppListviewItemsTags($tagname, $tag, $code, $attribs, $postName);
				$this->ipos++;
				$this->lastpos = 0;
			} else {
				switch ($tagname) {
					case "form" :
						$code = $this->parseFormTag($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					default :

						$attribs = "array(" . rtrim($attribs, ',') . ")";
						$attribs = str_replace('=>"\$', '=>"$', $attribs); // workarround Bug Nr 6318
						if (substr($tagname, 0, 2) == "if" && $tagname != "ifNoJavaScript") {
							$code = str_replace($tag, '<?php if(' . self::printTag($tagname, $attribs) . '){ ?>', $code);
							$this->ipos++;
							$this->lastpos = 0;
						} else {
							$tagPos = strpos($code, $tag, $this->lastpos);
							$endeStartTag = $tagPos + strlen($tag);
							$endTagPos = $this->searchEndtag($code, $tagPos);
							if ($endTagPos > -1) {
								$endeEndTagPos = strpos(
																$code, ">", $endTagPos) + 1;
								if ($endTagPos > $endeStartTag) {
									$content = substr(
													$code, $endeStartTag, ($endTagPos - $endeStartTag));

/*										$content = str_replace("\n", "", $content);
										$content = trim(str_replace("\r", "", $content));
										$content = str_replace('"', '\"', $content);
									$content = str_replace('we:', 'we_:_', $content);
									$content = str_replace('$GLOBALS[\"lv\"]', '\$GLOBALS[\"lv\"]', $content); //	this must be slashed inside blocks (for objects)!!!!
									$content = str_replace('$GLOBALS[\"we_lv_array\"]', '\$GLOBALS[\"we_lv_array\"]', $content); //	this must be slashed inside blocks (for objects)!!!!
									$content = str_replace('$GLOBALS[\"_we_listview_object_flag\"]', '\$GLOBALS[\"_we_listview_object_flag\"]', $content); //	this must be slashed inside blocks (for objects)!!!!  # 3479
	*/
									} else {
									$content = "";
								}

								// Tag besitzt Endtag
								$code = substr($code, 0, $tagPos) . '<?php printElement( ' . self::printTag($tagname, $attribs, $content, true) . '); ?>' . substr(
																$code, $endeEndTagPos);
								//neu
							} else
								$code = substr($code, 0, $tagPos) . '<?php printElement( ' . self::printTag($tagname, $attribs) . '); ?>' . substr(
																$code, $endeStartTag);
							$this->lastpos = 0;
						}
					/* if ($postName) { //FIXME: will be obsolete

					  $code = preg_replace(
					  '/("name"=>")(' . (isset($arr["name"]) ? $arr["name"] : "") . ')(")/i',
					  '\1\2' . $postName . '\3',
					  $code);
					  if ($tagname == 'setVar') {
					  if (isset($arr['from']) && $arr['from'] == "block") {
					  $code = preg_replace(
					  '/("namefrom"=>")(' . (isset($arr["namefrom"]) ? $arr["namefrom"] : "") . ')(")/i',
					  '\1\2' . $postName . '\3',
					  $code);
					  }
					  if (isset($arr['to']) && $arr['to'] == "block") {
					  $code = preg_replace(
					  '/("nameto"=>")(' . (isset($arr["nameto"]) ? $arr["nameto"] : "") . ')(")/i',
					  '\1\2' . $postName . '\3',
					  $code);
					  }
					  } elseif ($tagname == 'var') {  // #3558
					  if (isset($arr['type']) && in_array($arr['type'], array("global", "session", "request", "property"))) {
					  $code = preg_replace(
					  '/("name"=>")(.*)' . $postName . '(")/i',
					  '\1\2\3',
					  $code);
					  }
					  } else {
					  $code = preg_replace(
					  '/("namefrom"=>")(' . (isset($arr["namefrom"]) ? $arr["namefrom"] : "") . ')(")/i',
					  '\1\2' . $postName . '\3',
					  $code);
					  }
					  //$code = preg_replace('/("namefrom"=>")('. ( isset($arr["namefrom"]) ? $arr["namefrom"] : "" ) .')(")/i','\1\2'.$postName.'\3',$code);
					  if (!in_array($tagname, array(
					  'ifVar', 'ifNotVar'
					  ))) { // ifVar and ifNotVar contains a value, NO fieldname herefore don't change match!
					  $code = preg_replace(
					  '/("match"=>")(' . (isset($arr["match"]) ? $arr["match"] : "") . ')(")/i',
					  '\1\2' . $postName . '\3',
					  $code);
					  }
					  } */
				}
			}
		} else {

			$this->ipos++;
			if (substr($tagname, 0, 2) == 'if' && $tagname != 'ifNoJavaScript') {
				$code = str_replace($tag, '<?php } ?>', $code);
			} else
			if ($tagname == "printVersion") {
				$code = str_replace(
								$tag, '<?php if(isset($GLOBALS["we_tag_start_printVersion"]) && $GLOBALS["we_tag_start_printVersion"]){ $GLOBALS["we_tag_start_printVersion"]=0; ?></a><?php } ?>', $code);
			} else
			if ($tagname == "form") {
				$code = str_replace(
								$tag, '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?></form><?php } $GLOBALS["WE_FORM"] = ""; if (isset($GLOBALS["we_form_action"])) {unset($GLOBALS["we_form_action"]);} ?>', $code);
			} else
			if ($tagname == "listviewOrder") {
				$code = str_replace($tag, '</a>', $code);
			} else
			if ($tagname == "condition") {
				$code = str_replace(
								$tag, '<?php $GLOBALS["we_lv_conditionCount"]--;$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= ")"; ?>', $code);
			} else
			if ($tagname == "voting") {
				$code = str_replace(
								$tag, '<?php if(isset($GLOBALS[\'_we_voting\'])) unset($GLOBALS[\'_we_voting\']); ?>', $code);
			}

			$this->lastpos = 0;
		}
	}

	private function replaceTag($tag, $code, $str) {
		$tagPos = strpos($code, $tag, $this->lastpos);
		$endeEndTagPos = $tagPos + strlen($tag);
		return substr($code, 0, $tagPos) . $str . substr($code, $endeEndTagPos);
	}
/*
	private function parseFormTag($tag, $code, $attribs = "") {
		eval('$arr = array(' . $attribs . ');');

		$method = we_getTagAttributeForParsingLater("method", $arr, "post");
		$id = we_getTagAttributeTagParser("id", $arr);
		$action = we_getTagAttributeTagParser("action", $arr);
		$classid = we_getTagAttributeTagParser("classid", $arr);
		$parentid = we_getTagAttributeTagParser("parentid", $arr);
		$doctype = we_getTagAttributeTagParser("doctype", $arr);
		$type = we_getTagAttributeTagParser("type", $arr);
		$tid = we_getTagAttributeTagParser("tid", $arr);
		$categories = we_getTagAttributeTagParser("categories", $arr);
		$onsubmit = we_getTagAttributeTagParser("onsubmit", $arr);
		$onsubmit = we_getTagAttributeTagParser("onSubmit", $arr, $onsubmit);
		$onsuccess = we_getTagAttributeTagParser("onsuccess", $arr);
		$onerror = we_getTagAttributeTagParser("onerror", $arr);
		$onmailerror = we_getTagAttributeTagParser("onmailerror", $arr);
		$confirmmail = we_getTagAttributeTagParser("confirmmail", $arr);
		$preconfirm = we_getTagAttributeTagParser("preconfirm", $arr);
		$postconfirm = we_getTagAttributeTagParser("postconfirm", $arr);
		$order = we_getTagAttributeTagParser("order", $arr);
		$required = we_getTagAttributeTagParser("required", $arr);
		$remove = we_getTagAttributeTagParser("remove", $arr);
		$subject = we_getTagAttributeTagParser("subject", $arr);
		$recipient = we_getTagAttributeTagParser("recipient", $arr);
		$mimetype = we_getTagAttributeTagParser("mimetype", $arr);
		$from = we_getTagAttributeTagParser("from", $arr);
		$charset = we_getTagAttributeTagParser("charset", $arr);
		$xml = we_getTagAttributeTagParser("xml", $arr);
		$formname = we_getTagAttributeForParsingLater("name", $arr, "we_global_form");
		if (array_key_exists('nameid', $arr)) { // Bug #3153
			$formname = we_getTagAttributeForParsingLater("nameid", $arr, "we_global_form");
			$arr['pass_id'] = we_getTagAttributeForParsingLater("nameid", $arr);
			unset($arr['nameid']);
		}
		$onrecipienterror = we_getTagAttributeTagParser("onrecipienterror", $arr);
		$forcefrom = we_getTagAttributeTagParser("forcefrom", $arr, "", false);
		$captchaname = we_getTagAttributeTagParser("captchaname", $arr);
		$oncaptchaerror = we_getTagAttributeTagParser("oncaptchaerror", $arr);
		$enctype = we_getTagAttributeForParsingLater("enctype", $arr);
		$target = we_getTagAttributeForParsingLater("target", $arr);
		$formAttribs = removeAttribs(
						$arr, array(
				'onsubmit',
				'onSubmit',
				'name',
				'method',
				'xml',
				'charset',
				'id',
				'action',
				'order',
				'required',
				'onsuccess',
				'onerror',
				'type',
				'recipient',
				'mimetype',
				'subject',
				'onmailerror',
				'preconfirm',
				'postconfirm',
				'from',
				'confirmmail',
				'classid',
				'doctype',
				'remove',
				'onrecipienterror',
				'tid',
				'forcefrom',
				'categories'
						));

		$formAttribs['xml'] = $xml;
		$formAttribs['method'] = $method;

		if ($id) {
			if ($id != "self") {
				$php = '<?php $__id__ = ' . $id . ';$GLOBALS["we_form_action"] = f("SELECT Path FROM ".FILE_TABLE." WHERE ID=".abs($__id__),"Path",$GLOBALS['DB_WE']); ?>
';
			} else {
				$php = '<?php $GLOBALS["we_form_action"] = $_SERVER["SCRIPT_NAME"]; ?>
';
			}
		} else
		if ($action) {
			$php = '<?php $GLOBALS["we_form_action"] = "' . $action . '"; ?>
';
		} else {
			$php = '<?php $GLOBALS["we_form_action"] = $_SERVER["SCRIPT_NAME"]; ?>
';
		}
		if ($type != "search") {
			if (eregi('^(.*)return (.+)$', $onsubmit, $regs)) {
				$onsubmit = $regs[1] . ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return ' . $regs[2];
			} else {
				$onsubmit .= ';if(self.weWysiwygSetHiddenText){weWysiwygSetHiddenText();};return true;';
			}
		}
		switch ($type) {
			case "shopliste" :
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';
				$formAttribs['name'] = 'form<?php print (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]) && strlen($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1])) ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1] : $we_doc->ID; ?>';
				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) { ?>' . getHtmlTag(
												'form', $formAttribs, '', false, true) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'type',
										'value' => '<?php if( isset($GLOBALS["lv"]->classID) ){ echo "o"; }else if( isset($GLOBALS["lv"]->ID) ){ echo "w"; }else if( (isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID) )){echo "o";}else if($GLOBALS["we_doc"]->ID){ echo "w"; } ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'shop_artikelid',
										'value' => '<?php if(isset($GLOBALS["lv"]->classID) || isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID)){ echo (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->DB_WE->Record["OF_ID"]!="") ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : (isset($we_doc->DB_WE->Record["OF_ID"]) ? $we_doc->DB_WE->Record["OF_ID"] : (isset($we_doc->OF_ID) ? $we_doc->OF_ID : $we_doc->ID)); }else { echo (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]) && $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]!="") ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1] : $we_doc->ID; } ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 'we_variant',
										'value' => '<?php print (isset($GLOBALS["we_doc"]->Variant) ? $GLOBALS["we_doc"]->Variant : ""); ?>'
								)) . getHtmlTag(
												'input', array(
										'xml' => $xml,
										'type' => 'hidden',
										'name' => 't',
										'value' => '<?php echo time(); ?>'
								)) . '<?php } ?>';
				break;
			case "object" :
			case "document" :
				$php .= '<?php if(!isset($_REQUEST["edit_' . $type . '"])){ if(isset($GLOBALS["WE_SESSION_START"]) && $GLOBALS["WE_SESSION_START"]){ unset($_SESSION["we_' . $type . '_session_' . $formname . '"] );}} ?>
';
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['name'] = $formname;
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';

				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}
				if ($classid || $doctype) {
					$php .= '<?php $GLOBALS["WE_FORM"] = "' . $formname . '"; ?>';
					$php .= '<?php
if (!$GLOBALS["we_doc"]->InWebEdition) {
';
					if ($type == "object") {

						$php .= 'initObject(' . $classid . ',"' . $formname . '","' . $categories . '","' . $parentid . '");
';
					} else {
						$php .= 'initDocument("' . $formname . '","' . $tid . '","' . $doctype . '","' . $categories . '");
';
					}
					$php .= '
}
?>
';
					$typetmp = (($type == "object") ? "Object" : "Document");

					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
													'form', $formAttribs, '', false, true) . getHtmlTag(
													'input', array(
											'type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml
									)) . getHtmlTag(
													'input', array(
											'type' => 'hidden',
											'name' => 'we_edit' . $typetmp . '_ID',
											'value' => '<?php print isset($_REQUEST["we_edit' . $typetmp . '_ID"]) ? ($_REQUEST["we_edit' . $typetmp . '_ID"]) : 0; ?>',
											'xml' => $xml
									)) . '<?php }?>';
				} else {
					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
													'form', $formAttribs, '', false, true) . '<?php }?>';
				}
				break;
			case "formmail" :
				$successpage = $onsuccess ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onsuccess . '","Path",$GLOBALS['DB_WE']); ?>' : '';
				$errorpage = $onerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onerror . '","Path",$GLOBALS['DB_WE']); ?>' : '';
				$mailerrorpage = $onmailerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onmailerror . '","Path",$GLOBALS['DB_WE']); ?>' : '';
				$recipienterrorpage = $onrecipienterror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onrecipienterror . '","Path",$GLOBALS['DB_WE']); ?>' : '';
				$captchaerrorpage = $oncaptchaerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $oncaptchaerror . '","Path",$GLOBALS['DB_WE']); ?>' : '';

				if ($confirmmail == "true") {
					$confirmmail = true;
					$preconfirm = $preconfirm ? '<?php print str_replace("\'","\\\'",$we_doc->getElement("' . $preconfirm . '")); ?>' : '';
					$postconfirm = $postconfirm ? '<?php print str_replace("\'","\\\'",$we_doc->getElement("' . $postconfirm . '")); ?>' : '';
				} else {
					$confirmmail = false;
					$postconfirm = '';
					$preconfirm = '';
				}
				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}

				$formAttribs['name'] = $formname;
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['action'] = '<?php print WEBEDITION_DIR ?>we_formmail.php';
				if ($id) {
					if ($id != "self") {

						$formAttribs['action'] = '<?php print(f("SELECT Path FROM ".FILE_TABLE." WHERE ID=\'' . $id . '\'","Path",$GLOBALS['DB_WE'])); ?>';
					} else {
						$formAttribs['action'] = '<?php print $_SERVER["SCRIPT_NAME"]; ?>';
					}
				}


				//  now prepare all needed hidden-fields:
				$php = '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>
				            ' . getHtmlTag('form', $formAttribs, "", false, true) . '
				            <?php
				            	$_recipientString = "' . $recipient . '";
				            	$_recipientArray = explode(",", $_recipientString);
				            	foreach ($_recipientArray as $_key=>$_val) {
				            		$_recipientArray[$_key] = "\"" . trim($_val) . "\"";
				            	}
				            	$_recipientString = implode(",", $_recipientArray);

				            	$_ids = array();
				            	$GLOBALS['DB_WE']->query("SELECT * FROM " . RECIPIENTS_TABLE . " WHERE Email IN(" . $_recipientString . ")");
				            	while ($GLOBALS['DB_WE']->next_record()) {
				            		$_ids[] = $GLOBALS['DB_WE']->f("ID");
				            	}

				            	$_recipientIdString = "";
				            	if (count($_ids)) {
				            		$_recipientIdString = implode(",", $_ids);
				            	}

				            ?>
				            <div class="weHide" style="display: none;">
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'order',
										'value' => '<?php print "' . $order . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'required',
										'value' => '<?php print "' . $required . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'subject',
										'value' => '<?php print "' . $subject . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient',
										'value' => '<?php print $_recipientIdString; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mimetype',
										'value' => '<?php print "' . $mimetype . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'from',
										'value' => '<?php print "' . $from . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'error_page', 'value' => $errorpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'mail_error_page',
										'value' => $mailerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'recipient_error_page',
										'value' => $recipienterrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'ok_page', 'value' => $successpage, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'charset',
										'value' => '<?php print "' . $charset . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'confirm_mail',
										'value' => '<?php print "' . $confirmmail . '"; ?>',
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'pre_confirm',
										'value' => $preconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'post_confirm',
										'value' => $postconfirm,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'we_remove', 'value' => $remove, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden', 'name' => 'forcefrom', 'value' => $forcefrom, 'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captcha_error_page',
										'value' => $captchaerrorpage,
										'xml' => $xml
								)) . '
                                ' . getHtmlTag(
												'input', array(
										'type' => 'hidden',
										'name' => 'captchaname',
										'value' => $captchaname,
										'xml' => $xml
								)) . '
			                 </div>
				        <?php }?>';
				break;
			default :
				if ($enctype) {
					$formAttribs['enctype'] = $enctype;
				}
				if ($target) {
					$formAttribs['target'] = $target;
				}
				$formAttribs['name'] = $formname;
				$formAttribs['onsubmit'] = $onsubmit;
				$formAttribs['action'] = '<?php print $GLOBALS["we_form_action"]; ?>';

				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]){ ?>' . getHtmlTag(
												'form', $formAttribs, "", false, true) . "<?php } ?>\n";
		}

		return $this->replaceTag($tag, $code, $php);
	}*/

	public static function printTag($name, $attribs='', $content='',$cslash=false) {
		$attr = (is_array($attribs) ? self::printArray($attribs) : $attribs);
		return 'we_tag(\'' . $name . '\'' .
		($attr != '' ? ',' . $attr : ($content != '' ? ',array()' : '')) .
		($content != '' ? ',"' . ($cslash?addcslashes($content, '"'):$content) . '"' : '') . ')';
		//addcslashes($content, '"')
	}

	public static function printArray($array) {
		$ret = '';
		foreach ($array as $key => $val) {
			$ret.='\'' . $key . '\'=>\'' . $val . '\',';
		}
		return 'array(' . $ret . ')';
	}

}
