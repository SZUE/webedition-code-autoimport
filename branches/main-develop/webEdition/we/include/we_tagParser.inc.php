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

// Tag and TagBlock Cache
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tools/cache/weCacheHelper.class.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tools/cache/weCache.class.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tools/cache/weTagCache.class.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_tools/cache/weTagListviewCache.class.php");

class we_tagParser{

	var $DB_WE;

	var $lastpos = 0;

	var $tags = array();

	var $ipos = 0;

	var $ListviewItemsTags = array('object', 'customer', 'onlinemonitor', 'order', 'orderitem', 'metadata','languagelink');

	var $AppListviewItemsTags = array();

	function we_tagParser()	{
	}

	function parseAppListviewItemsTags($tagname,$tag, $code, $attribs = "", $postName = ""){

			$pre = $this->getStartCacheCode($tag, $attribs);

			return $this->replaceTag($tag, $code, $pre . $php);

	}

	function getNames($tags)
	{
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

	function getAllTags($code)
	{
		$tags = array();
		$foo = array();
		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $foo, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($foo); $i++) {
			if (substr($foo[$i][1], -1) == '<') {
				$foo[$i][1] = substr($foo[$i][1], 0, strlen($foo[$i][1]) - 1);
			}
			array_push($tags, $foo[$i][1]);

		}
		return $tags;
	}

	/**
	 * @return	array
	 * @param	string $tagname
	 * @param	string $code
	 * @param	bool   $hasEndtag
	 * @desc		function separates a complete XML tag in several pieces
	 *			returns array with this information
	 *			tagname without <> .. for example "we:hidePages"
	 *			[0][x] = complete Tag
	 *			[1][x] = start tag
	 *			[2][x] = parameter as string
	 */
	function itemize_we_tag($tagname, $code)
	{

		preg_match_all('/(<' . $tagname . '([^>]*)>)/U', $code, $_matches);
		return $_matches;
	}

	/**
	 * @return string of code with all required tags
	 * @param $code Src Code
	 * @desc Searches for all meta-tags in a given template (title, keyword, description, charset)
	 */
	function getMetaTags($code)
	{

		$_tmpTags = array();
		$_foo = array();
		$_rettags = array();

		preg_match_all("|(</?we:[^><]+[<>])|U", $code, $_foo, PREG_SET_ORDER);

		for ($_i = 0; $_i < sizeof($_foo); $_i++) {
			if (substr($_foo[$_i][1], -1) == '<') {
				$_foo[$_i][1] = substr($_foo[$_i][1], 0, strlen($_foo[$_i][1]) - 1);
			}
			array_push($_tmpTags, $_foo[$_i][1]);
		}

		//	only Meta-tags, description, keywords, title and charset
		$_tags = array();
		for ($_i = 0; $_i < sizeof($_tmpTags); $_i++) {

			if (strpos($_tmpTags[$_i], "we:title") || strpos($_tmpTags[$_i], "we:description") || strpos(
					$_tmpTags[$_i],
					"we:keywords") || strpos($_tmpTags[$_i], "we:charset")) {

				$_tags[] = $_tmpTags[$_i];
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

	function parseTags($tags, &$code, $postName = '', $ignore = array()){

		if (!defined('DISABLE_TEMPLATE_TAG_CHECK') || !DISABLE_TEMPLATE_TAG_CHECK) {
			if (!$this->checkOpenCloseTags($tags, $code)) {
				return;
			}
		}

		$this->lastpos = 0;
		$this->tags = $tags;
		$this->ipos = 0;
		while ($this->ipos < sizeof($this->tags)) {
			$this->lastpos = 0;

			if (in_array(substr(ereg_replace("[>/ ].*", '', $this->tags[$this->ipos]), 1), $ignore)) {
				$this->parseTag($code); //	dont add postname tagname in ignorearray
			} else {
				$this->parseTag($code, $postName);
			}
		}
		//remove unwanted/-needed start/stop tags
		$code=preg_replace("|;? *\?>\n?<\?php ?|si",";\n",$code);
	}

	function checkOpenCloseTags($TagsInTemplate, &$code){

		$CloseTags = array(
			'listview', 'listdir', 'block'
		);

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
				$ErrorMsg .= parseError(sprintf(g_l('parser','[missing_open_tag]'), 'we:' . $_tag));
				$isError = true;

			} else
				if ($_counter > 0) {
					$ErrorMsg .= parseError(sprintf(g_l('parser','[missing_close_tag]'), 'we:' . $_tag));
					$isError = true;

				}

		}
		if ($isError) {
			$code = $ErrorMsg;
		}
		return !$isError;

	}

	function searchEndtag($code, $tagPos){
		eregi("we:([^ >]+)", $this->tags[$this->ipos], $regs);
		$tagname = $regs[1];

		if ($tagname != 'back' && $tagname != 'next' && $tagname != 'printVersion' && $tagname != 'listviewOrder') {
			$tagcount = 0;
			$endtags = array();

			$endtagpos = $tagPos;

			for ($i = $this->ipos + 1; $i < sizeof($this->tags); $i++) {
				if (preg_match('|(< ?/ ?we ?: ?'.$tagname.'[^a-z])|i', $this->tags[$i], $regs)) {
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
				} else{
					if (preg_match('|(< ?we ?: ?'.$tagname.'[^a-z])|i', $this->tags[$i])) {
						$tagcount++;
					}
				}
			}
		}
		$this->ipos++;
		return -1;

	}

	function getNameAndAttribs($tag)
	{
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
	}

	function parseTag(&$code, $postName = '')	{
		$tag = $this->tags[$this->ipos];
		if (!$tag)
			return;
		$tagPos = -1;

		$endTag = false;
		eregi("<(/?)we:(.+)>?", $tag, $regs);
		if ($regs[1]) { ### its an end-tag
			$endTag = true;
		}
		$foo = $regs[2] . '/';
		ereg("([^ >/]+) ?(.*)", $foo, $regs);
		$tagname = $regs[1];
		$attr = trim(rtrim($regs[2],'/'));

		if (preg_match('|name="([^"]*)"|i', $attr, $regs)) {
			if (!$regs[1]) {
				print parseError(sprintf(g_l('parser','[name_empty]'), $tagname));
			} else
				if (strlen($regs[1]) > 255) {
					print parseError(sprintf(g_l('parser','[name_to_long]'), $tagname));
				}
		}

		$attribs = '';
		preg_match_all('/([^=]+)= *("[^"]*")/', $attr, $foo, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($foo); $i++) {
			$attribs .= '"' . trim($foo[$i][1]) . '"=>' . trim($foo[$i][2]) . ',';
		}

		if (!$endTag) {
			$arrstr = "array(" . rtrim($attribs,',') . ")";

			@eval('$arr = ' . ereg_replace('"\$([^"]+)"', '"$GLOBALS[\1]"', $arrstr) . ';');
			if (!isset($arr)) {
				$arr = array();
			}
					
			$parseFn = 'we_parse_tag_'.$tagname;
			if((we_include_tag_file($tagname)===true) && function_exists($parseFn)){
				$pre = $post = $content = '';
				$tagPos = strpos($code, $tag, $this->lastpos);
				$endeStartTag = $tagPos + strlen($tag);
				$endTagPos = $this->searchEndtag($code, $tagPos);

				if ($endTagPos > -1) {
					$endeEndTagPos = strpos(
							$code,
							">",
							$endTagPos) + 1;
					if ($endTagPos > $endeStartTag) {
						$content = substr(
								$code,
								$endeStartTag,
								($endTagPos - $endeStartTag));
					}}
					$attribs = str_replace('=>"\$', '=>"$', 'array(' . rtrim($attribs,',') . ')'); // workarround Bug Nr 6318
				/*call specific function for parsing this tag
				 * $attribs is the attribs string, $content is content of this tag
				 * return value is parsed again and inserted
				 */
				$content = $parseFn($attribs,$content);
				$tp = new we_tagParser();
				$tags = $tp->getAllTags($content);
				$tp->parseTags($tags, $content);
				$code = substr($code,0,$tagPos) . 
								$content.
								substr($code,$endeEndTagPos);
				return;
			}
			
			if (in_array($tagname,$this->AppListviewItemsTags) ){// for App-Tags of type listviewitems
				$code = $this->parseAppListviewItemsTags($tagname, $tag, $code, $attribs, $postName);
				$this->ipos++;
				$this->lastpos = 0;
			} else {
				switch ($tagname) {
					case "content" :
					case "master" :
						// don't parse it
						$code = str_replace($tag, '', $code);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "form" :
						$code = $this->parseFormTag($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "object" :
						$code = $this->parseObjectTag($tag, $code, $attribs, $postName);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "customer" :
						$code = $this->parseCustomerTag($tag, $code, $attribs, $postName);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "onlinemonitor" :
						$code = $this->parseOnlinemonitorTag($tag, $code, $attribs, $postName);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "order" :
						$code = $this->parseOrderTag($tag, $code, $attribs, $postName);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "orderitem" :
						$code = $this->parseOrderItemTag($tag, $code, $attribs, $postName);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "repeatShopItem" :
						$code = $this->parserepeatShopitem($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "addDelShopItem" :
						$code = $this->parseadddelShopitem($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "controlElement" :
					case "hidePages" :
						$code = $this->parseRemoveTags($tag, $code);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "xmlnode" :
						$code = $this->parseXMLNode($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "voting" :
						$code = $this->parseVotingTag($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					case "votingList" :
						$code = $this->parseVotingListTag($tag, $code, $attribs);
						$this->ipos++;
						$this->lastpos = 0;
						break;
					default :

						$attribs = "array(" . rtrim($attribs,',') . ")";
						$attribs = str_replace('=>"\$', '=>"$', $attribs); // workarround Bug Nr 6318
													if (substr($tagname, 0, 2) == "if" && $tagname != "ifNoJavaScript") {
														/*$code = str_replace($tag,'<?php echo \'<?php if(we_tag("'.$tagname.'", '.$attribs.')): ?>\'; ?>',$code);*/
														$code = str_replace(
																$tag,
																$this->parseCacheIfTag(
																		'<?php if(we_tag(\'' . $tagname . '\', ' . $attribs . ')): ?>'),
																$code);
														$this->ipos++;
														$this->lastpos = 0;
													} else
														if ($tagname == "condition") {
															$code = str_replace(
																	$tag,
																	'<?php we_tag(\'' . $tagname . '\', ' . $attribs . '); ?>',
																	$code);
															$this->ipos++;
															$this->lastpos = 0;
														} else {
															$tagPos = strpos($code, $tag, $this->lastpos);
															$endeStartTag = $tagPos + strlen($tag);
															$endTagPos = $this->searchEndtag($code, $tagPos);
															if ($endTagPos > -1) {
																$endeEndTagPos = strpos(
																		$code,
																		">",
																		$endTagPos) + 1;
																if ($endTagPos > $endeStartTag) {
																	$content = substr(
																			$code,
																			$endeStartTag,
																			($endTagPos - $endeStartTag));

																		if ($tagname != "noCache") {
																			$content = str_replace(
																					"\n",
																					"",
																					$content);
																			$content = trim(
																					str_replace(
																							"\r",
																							"",
																							$content));
																			$content = str_replace(
																					'"',
																					'\"',
																					$content);
																		}
																	$content = str_replace(
																			'we:',
																			'we_:_',
																			$content);
																	$content = str_replace(
																			'$GLOBALS[\"lv\"]',
																			'\$GLOBALS[\"lv\"]',
																			$content); //	this must be slashed inside blocks (for objects)!!!!
																	$content = str_replace(
																			'$GLOBALS[\"we_lv_array\"]',
																			'\$GLOBALS[\"we_lv_array\"]',
																			$content); //	this must be slashed inside blocks (for objects)!!!!
																	$content = str_replace(
																			'$GLOBALS[\"_we_listview_object_flag\"]',
																			'\$GLOBALS[\"_we_listview_object_flag\"]',
																			$content); //	this must be slashed inside blocks (for objects)!!!!  # 3479
																} else {
																	$content = "";
																}

{
																	// Tag besitzt Endtag
																	$we_tag = 'we_tag(\'' . $tagname . '\', ' . $attribs . ', "' . $content . '")';
																	$code = substr($code, 0, $tagPos) . '<?php printElement( ' . $we_tag . '); ?>' . substr(
																			$code,
																			$endeEndTagPos);
																	//neu


																}

															} else
																if ($tagname == "else") {
																	$code = substr($code, 0, $tagPos) . $this->parseCacheIfTag(
																			'<?php else: ?>') . substr(
																			$code,
																			$endeStartTag);

																} else
																	if (isset($GLOBALS["calculate"]) && $GLOBALS["calculate"] == 1) { //neu
																		$we_tag = 'we_tag(\'' . $tagname . '\', ' . $attribs . ')';
																		eval(
																				'$code = str_replace($tag,std_numberformat(' . $we_tag . '),$code);');
																		//neu
																	} else if ($tagname == 'include'){
																	$we_tag = 'we_tag(\'' . $tagname . '\', ' . $attribs . ')';
																	$code = substr($code, 0, $tagPos) . '<?php eval( ' . $we_tag . '); ?>' . substr(
																			$code,
																			$endeStartTag);
																}else {
																		$we_tag = 'we_tag(\'' . $tagname . '\', ' . $attribs . ', \'\')';
																		$code = substr($code, 0, $tagPos) . '<?php printElement( ' . $we_tag . '); ?>' . substr(
																				$code,
																				$endeStartTag);
																	}
															$this->lastpos = 0;
														}
						if ($postName) { //FIXME: will be obsolete

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
						}
				}
			}
		} else {

			$this->ipos++;
			if ($tagname == "ifHasEntries" || $tagname == "ifNotHasEntries" || $tagname == "ifHasCurrentEntry" || $tagname == "ifNotHasCurrentEntry" || $tagname == "ifshopexists" || $tagname == "ifobjektexists" || $tagname == "ifnewsletterexists" || $tagname == "ifcustomerexists" || $tagname == "ifbannerexists" || $tagname == "ifvotingexists") {
				$code = str_replace($tag, '<?php endif; ?>', $code);

			} else
				if (substr($tagname, 0, 2) == "if" && $tagname != "ifNoJavaScript") {
					/*$code = str_replace($tag,'<?php echo "<?php endif; ?>"; ?>',$code);*/
					$code = str_replace($tag, $this->parseCacheIfTag('<?php endif; ?>'), $code);
				} else
					if ($tagname == "printVersion") {
						$code = str_replace(
								$tag,
								'<?php if(isset($GLOBALS["we_tag_start_printVersion"]) && $GLOBALS["we_tag_start_printVersion"]){ $GLOBALS["we_tag_start_printVersion"]=0; ?></a><?php } ?>',
								$code);
					} else
						if ($tagname == "next") {
							if (isset($GLOBALS["_we_voting_list_active"]))
								$code = str_replace(
										$tag,
										'<?php if($GLOBALS["_we_voting_list"]->hasNextPage() ): ?></a><?php endif; ?>',
										$code);
							else
								$code = str_replace(
										$tag,
										'<?php if($GLOBALS["lv"]->hasNextPage() && $GLOBALS["lv"]->close_a() ) echo \'</a>\';?>',
										$code);
						} else
							if ($tagname == "back") {
								if (isset($GLOBALS["_we_voting_list_active"]))
									$code = str_replace(
											$tag,
											'<?php if($GLOBALS["_we_voting_list"]->hasPrevPage() ): ?></a><?php endif; ?>',
											$code);
								else
									$code = str_replace(
											$tag,
											'<?php if($GLOBALS["lv"]->hasPrevPage()  && $GLOBALS["lv"]->close_a() ) echo \'</a>\'; ?>',
											$code);
							} else
								if ($tagname == "form") {
									$code = str_replace(
											$tag,
											'<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]): ?></form><?php endif; $GLOBALS["WE_FORM"] = ""; if (isset($GLOBALS["we_form_action"])) {unset($GLOBALS["we_form_action"]);} ?>',
											$code);
								} else
										if ($tagname == "listview") {
											$code = preg_replace(
													'/'.preg_quote($tag, '/').'/',
													'<?php
if ( isset( $GLOBALS["we_lv_array"] ) ) {
	array_pop($GLOBALS["we_lv_array"]);
	if (count($GLOBALS["we_lv_array"])) {
		$GLOBALS["lv"] = clone($GLOBALS["we_lv_array"][count($GLOBALS["we_lv_array"])-1]);
	} else {
		unset($GLOBALS["lv"]);unset($GLOBALS["we_lv_array"]);
	}
}?>' . $this->getEndCacheCode($tag),
													$code,1);

										} else
											if (in_array($tagname,$this->ListviewItemsTags)) {
												$code = str_replace(
														$tag,
														'<?php endif;
if ( isset( $GLOBALS["we_lv_array"] ) ) {
	array_pop($GLOBALS["we_lv_array"]);
	if (count($GLOBALS["we_lv_array"])) {
		$GLOBALS["lv"] = clone($GLOBALS["we_lv_array"][count($GLOBALS["we_lv_array"])-1]);
	} else {
		unset($GLOBALS["lv"]);unset($GLOBALS["we_lv_array"]);
	}
} ?>' . $this->getEndCacheCode($tag), $code);

											} else
												if ($tagname == "listviewOrder") {
													$code = str_replace($tag, '</a>', $code);
												} else
													if ($tagname == "condition") {
														$code = str_replace(
																$tag,
																'<?php $GLOBALS["we_lv_conditionCount"]--;$GLOBALS[$GLOBALS["we_lv_conditionName"]] .= ")"; ?>',
																$code);
													} else
															if ($tagname == "repeatShopItem") {
																$code = str_replace(
																		$tag,
																		'<?php } unset($GLOBALS["lv"]); ?>',
																		$code);
															} else
																if ($tagname == "xmlnode") {
																	$code = str_replace(
																			$tag,
																			'<?php }} array_pop($GLOBALS["xstack"]); ?>',
																			$code);
																} else
																	if ($tagname == "voting") {
																		$code = str_replace(
																				$tag,
																				'<?php if(isset($GLOBALS[\'_we_voting\'])) unset($GLOBALS[\'_we_voting\']); ?>',
																				$code);
																	} else
																		if ($tagname == "votingList") {
																			unset(
																					$GLOBALS['_we_voting_list_active']);
																			$code = str_replace(
																					$tag,
																					'<?php unset($GLOBALS[\'_we_voting_list\']); ?>',
																					$code);
																		} else
																				if ($tagname == "content") {
																					$code = str_replace(
																							$tag,
																							'',
																							$code);
																				}

			$this->lastpos = 0;
		}
	}

	function getStartCacheCode($tag, $attribs) //FIXME: remove
	{
		return '';
	}

	function getEndCacheCode($tag)// FIXME: remove
	{
		return '';
	}


	function replaceTag($tag, $code, $str)
	{
		$tagPos = strpos($code, $tag, $this->lastpos);
		$endeEndTagPos = $tagPos + strlen($tag);
		return substr($code, 0, $tagPos) . $str . substr($code, $endeEndTagPos);
	}

	function parseObjectTag($tag, $code, $attribs = "", $postName = "")
	{

		if (defined("WE_OBJECT_MODULE_DIR")) {

			eval('$arr = array(' . $attribs . ');');

			$we_button = new we_button();

			$condition = we_getTagAttributeTagParser("condition", $arr, 0);
			$classid = we_getTagAttributeTagParser("classid", $arr);
			$we_oid = we_getTagAttributeTagParser("id", $arr, 0);
			$name = we_getTagAttributeTagParser("name", $arr) . $postName;
			$_showName = we_getTagAttributeTagParser("name", $arr);
			$size = we_getTagAttributeTagParser("size", $arr, 30);
			$triggerid = we_getTagAttributeTagParser("triggerid", $arr, "0");
			$searchable = we_getTagAttributeTagParser("searchable", $arr, "", true);
			if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "true", false);
			} else {
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "false", false);
			}
			if (defined('TAGLINKS_OBJECTSEOURLS') && TAGLINKS_OBJECTSEOURLS){
				$objectseourls = we_getTagAttributeTagParser("objectseourls", $arr, "true", false);
			} else {
				$objectseourls = we_getTagAttributeTagParser("objectseourls", $arr, "false", false);
			}

			$php = '<?php

if (!isset($GLOBALS["we_lv_array"])) {
	$GLOBALS["we_lv_array"] = array();
}

include_once(WE_OBJECT_MODULE_DIR . "we_objecttag.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
';
			if ($classid) {
				$php .= '$__id__='.$classid.';$classPath = f("SELECT Path FROM ".OBJECT_TABLE." WHERE ID=".abs($__id__),"Path",$GLOBALS["DB_WE"]);
$rootDirID = f("SELECT ID FROM ".OBJECT_FILES_TABLE." WHERE Path=\'$classPath\'","ID",$GLOBALS["DB_WE"]);
';
			} else {
				$php .= '$rootDirID = 0;
';
			}
			if ($name) {
				if (strpos($name, " ") !== false) {
					return parseError(sprintf(g_l('parser','[name_with_space]'), "object"));
				}

				$php .= '
		$we_doc = $GLOBALS["we_doc"];
		';
				if (strpos($we_oid,'$')===false ){//Bug 4848
					$php.='$we_oid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : ' . $we_oid . ';';
				} else {
					$php.='$we_oid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : isset('.$we_oid.') ? "'.$we_oid.'" : $GLOBALS["'.str_replace('$','', $we_oid). '"];';
				}

				$php .= '
		$path = f("SELECT Path FROM ".OBJECT_FILES_TABLE." WHERE ID=\'$we_oid\'","Path",$GLOBALS["DB_WE"]);
		$textname = \'we_\'.$we_doc->Name.\'_txt[' . $name . '_path]\';
		$idname = \'we_\'.$we_doc->Name.\'_txt[' . $name . ']\';
		$table = OBJECT_FILES_TABLE;
		$we_button = new we_button();
		//javascript:document.forms[0].elements[\'$idname\'].value=0;document.forms[0].elements[\'$textname\'].value=\'\';_EditorFrame.setEditorIsHot(false);we_cmd(\'reload_editpage\');
		$wecmdenc1= "WECMDENC_".base64_encode("document.forms[\'we_form\'].elements[\'$idname\'].value");
		$wecmdenc2= "WECMDENC_".base64_encode("document.forms[\'we_form\'].elements[\'$textname\'].value");
		$wecmdenc3= "WECMDENC_".base64_encode("opener.we_cmd(\'reload_editpage\');opener._EditorFrame.setEditorIsHot(true);");

		$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms[0].elements[\'$idname\'].value=0;document.forms[0].elements[\'$textname\'].value=\'\';_EditorFrame.setEditorIsHot(false);we_cmd(\'reload_editpage\');");
		$button    = $we_button->create_button("select", "javascript:we_cmd(\'openDocselector\',document.forms[0].elements[\'$idname\'].value,\'$table\',\'.$wecmdenc1.\',\'.$wecmdenc2.\',\'.$wecmdenc3.\',\'".session_id()."\',\'$rootDirID\',\'objectFile\',".(we_hasPerm("CAN_SELECT_OTHER_USERS_OBJECTS") ? 0 : 1).")");

if($GLOBALS["we_editmode"]): ?>
<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
	<tr>
		<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b>' . $_showName . '</b></span></td>
		<td><?php print hidden($idname,$we_oid) ?></td>
		<td><?php print htmlTextInput($textname,' . $size . ',$path,"",\' readonly\',"text",0,0); ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $button; ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $delbutton; ?></td>
	</tr>
</table><?php endif;
';
			} else {
				if (strpos($we_oid,'$')===false ){//Bug 4848
					$php .='$we_oid=' . $we_oid . '	;
					';
				} else {
					$php.='$we_oid =  isset('.$we_oid.') ? "'.$we_oid.'" : $GLOBALS["'.str_replace('$','', $we_oid). '"];';
				}

				$php .='

$we_oid = $we_oid ? $we_oid : (isset($_REQUEST["we_oid"]) ? $_REQUEST["we_oid"] : 0);
';
			}
			$searchable = empty($searchable) ? 'false' : $searchable;
			$php .= '$GLOBALS["lv"] = new we_objecttag("' . $classid . '",$we_oid,' . $triggerid . ',' . $searchable . ', "' . $condition . '",'.$hidedirindex.','.$objectseourls.');
$lv = clone($GLOBALS["lv"]); // for backwards compatibility
if(is_array($GLOBALS["we_lv_array"])) array_push($GLOBALS["we_lv_array"],clone($GLOBALS["lv"]));
if($GLOBALS["lv"]->avail): ?>';

			//	Add a sign for Super-Easy-Edit-Mode. to edit an Object.
			$php .= '<?php
		if(isset($_SESSION["we_mode"]) && $_SESSION["we_mode"] == "seem"){
			print "<a href=\"$we_oid\" seem=\"object\"></a>";
		}
		?>';

			if ($postName != "") {
				$content = str_replace('$', '\$', $php); //	to test with blocks ...
			}

			$pre = $this->getStartCacheCode($tag, $attribs);

			return $this->replaceTag($tag, $code, $pre . $php);
		} else { return str_replace($tag, modulFehltError('Object/DB','object'), $code); }
	}


	function parseCustomerTag($tag, $code, $attribs = "", $postName = "")
	{

		if (defined("WE_CUSTOMER_MODULE_DIR")) {

			eval('$arr = array(' . $attribs . ');');

			$we_button = new we_button();

			$condition = we_getTagAttributeTagParser("condition", $arr, 0);
			$we_cid = we_getTagAttributeTagParser("id", $arr, 0);
			$name = we_getTagAttributeTagParser("name", $arr) . $postName;
			$_showName = we_getTagAttributeTagParser("name", $arr);
			$size = we_getTagAttributeTagParser("size", $arr, 30);
			if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "true", false);
			} else {
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "false", false);
			}
			$php = '<?php

if (!isset($GLOBALS["we_lv_array"])) {
	$GLOBALS["we_lv_array"] = array();
}

include_once(WE_CUSTOMER_MODULE_DIR . "we_customertag.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
';

			if ($name) {
				if (strpos($name, " ") !== false) {
					return parseError(sprintf(g_l('parser','[name_with_space]'), "object"));
				}

				$php .= '
		$we_doc = $GLOBALS["we_doc"];
		';
				if (strpos($we_cid,'$')===false ){//Bug 4848
					$php.='$we_cid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : ' . $we_cid . ';';
				} else {
					$php.='$we_cid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : isset('.$we_cid.') ? "'.$we_cid.'" : $GLOBALS["'.str_replace('$','', $we_cid). '"];';
				}
			$php .='

		$we_cid = $we_cid ? $we_cid : (isset($_REQUEST["we_cid"]) ? $_REQUEST["we_cid"] : 0);
		$path = f("SELECT Path FROM ".CUSTOMER_TABLE." WHERE ID=".abs($we_cid),"Path",$GLOBALS["DB_WE"]);
		$textname = \'we_\'.$we_doc->Name.\'_txt[' . $name . '_path]\';
		$idname = \'we_\'.$we_doc->Name.\'_txt[' . $name . ']\';
		$table = CUSTOMER_TABLE;
		$we_button = new we_button();
		$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms[0].elements[\'$idname\'].value=0;document.forms[0].elements[\'$textname\'].value=\'\';_EditorFrame.setEditorIsHot(false);we_cmd(\'reload_editpage\');");
		$button    = $we_button->create_button("select", "javascript:we_cmd(\'openSelector\',document.forms[0].elements[\'$idname\'].value,\'$table\',\'document.forms[\\\'we_form\\\'].elements[\\\'$idname\\\'].value\',\'document.forms[\\\'we_form\\\'].elements[\\\'$textname\\\'].value\',\'opener.we_cmd(\\\'reload_editpage\\\');opener._EditorFrame.setEditorIsHot(true);\',\'".session_id()."\',0,\'\',1)");

if($GLOBALS["we_editmode"]): ?>
<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
	<tr>
		<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b>' . $_showName . '</b></span></td>
		<td><?php print hidden($idname,$we_cid) ?></td>
		<td><?php print htmlTextInput($textname,' . $size . ',$path,"",\' readonly\',"text",0,0); ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $button; ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $delbutton; ?></td>
	</tr>
</table><?php endif;
';
			} else {
				if (strpos($we_cid,'$')===false ){//Bug 4848
					$php .='$we_cid=' . $we_cid . '	;
					';
				} else {
					$php.='$we_cid =  isset('.$we_cid.') ? "'.$we_cid.'" : $GLOBALS["'.str_replace('$','', $we_cid). '"];';
				}


$php .='$we_cid = $we_cid ? $we_cid : (isset($_REQUEST["we_cid"]) ? $_REQUEST["we_cid"] : 0);
';
			}

			$php .= '$GLOBALS["lv"] = new we_customertag($we_cid,"' . $condition . '",'.$hidedirindex.');
$lv = clone($GLOBALS["lv"]); // for backwards compatibility
if(is_array($GLOBALS["we_lv_array"])) array_push($GLOBALS["we_lv_array"],clone($GLOBALS["lv"]));
if($GLOBALS["lv"]->avail): ?>';

			if ($postName != "") {
				$content = str_replace('$', '\$', $php); //	to test with blocks ...
			}

			$pre = $this->getStartCacheCode($tag, $attribs);

			return $this->replaceTag($tag, $code, $pre . $php);
		} else { return str_replace($tag, modulFehltError('Customer','customer'), $code); }
	}

	function parseOnlinemonitorTag($tag, $code, $attribs = "", $postName = "")
	{

		if (defined("WE_CUSTOMER_MODULE_DIR")) {

			eval('$arr = array(' . $attribs . ');');

			$condition = we_getTagAttributeTagParser("condition", $arr, 0);
			$we_omid = we_getTagAttributeTagParser("id", $arr, 0);

			$php = '<?php

if (!isset($GLOBALS["we_lv_array"])) {
	$GLOBALS["we_lv_array"] = array();
}

include_once(WE_CUSTOMER_MODULE_DIR . "we_onlinemonitortag.inc.php");
';


			if (strpos($we_omid,'$')===false ){//Bug 4848
					$php .='$we_omid=' . $we_oid . '	;
					';
			} else {
					$php.='$we_oimd =  isset('.$we_omid.') ? "'.$we_omid.'" : $GLOBALS["'.str_replace('$','', $we_omid). '"];';
			}

$php .='$we_omid = $we_omid ? $we_omid : (isset($_REQUEST["we_omid"]) ? $_REQUEST["we_omid"] : 0);
';


			$php .= '$GLOBALS["lv"] = new we_onlinemonitortag($we_omid,"' . $condition . '");
if(is_array($GLOBALS["we_lv_array"])) array_push($GLOBALS["we_lv_array"],clone($GLOBALS["lv"]));
if($GLOBALS["lv"]->avail): ?>';

			if ($postName != "") {
				$content = str_replace('$', '\$', $php); //	to test with blocks ...
			}

			return $this->replaceTag($tag, $code, $pre . $php);
		} else { return str_replace($tag, modulFehltError('Customer','customer'), $code); }
	}


	function parseOrderTag($tag, $code, $attribs = "", $postName = "")
	{

		if (defined("WE_SHOP_MODULE_DIR")) {

			eval('$arr = array(' . $attribs . ');');

			$we_button = new we_button();

			$condition = we_getTagAttributeTagParser("condition", $arr, 0);
			$we_orderid = we_getTagAttributeTagParser("id", $arr, 0);

			$name = we_getTagAttributeTagParser("name", $arr) . $postName;
			//$_showName = we_getTagAttributeTagParser("name", $arr);
			//$size = we_getTagAttributeTagParser("size", $arr, 30);
			if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "true", false);
			} else {
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "false", false);
			}

			$php = '<?php

if (!isset($GLOBALS["we_lv_array"])) {
	$GLOBALS["we_lv_array"] = array();
}

include_once(WE_SHOP_MODULE_DIR . "we_ordertag.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
';

			if ($name) {
				if (strpos($name, " ") !== false) {
					return parseError(sprintf(g_l('parser','[name_with_space]'), "object"));
				}

				$php .= '
		$we_doc = $GLOBALS["we_doc"];
		';
				if (strpos($we_orderid,'$')===false ){//Bug 4848
					$php.='$we_orderid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : ' . $we_orderid . ';';
				} else {
					$php.='$we_orderid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : isset('.$we_orderid.') ? "'.$we_orderid.'" : $GLOBALS["'.str_replace('$','', $we_orderid). '"];';
				}
				$php .= '

		$we_orderid = $we_orderid ? $we_orderid : (isset($_REQUEST["we_orderid"]) ? $_REQUEST["we_orderid"] : 0);
		$path = "/".$we_orderid;
		$textname = \'we_\'.$we_doc->Name.\'_txt[' . $name . '_path]\';
		$idname = \'we_\'.$we_doc->Name.\'_txt[' . $name . ']\';
		$table = SHOP_TABLE;
		$we_button = new we_button();
		$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms[0].elements[\'$idname\'].value=0;document.forms[0].elements[\'$textname\'].value=\'\';_EditorFrame.setEditorIsHot(false);we_cmd(\'reload_editpage\');");
		$button    = $we_button->create_button("select", "javascript:we_cmd(\'openSelector\',document.forms[0].elements[\'$idname\'].value,\'$table\',\'document.forms[\\\'we_form\\\'].elements[\\\'$idname\\\'].value\',\'document.forms[\\\'we_form\\\'].elements[\\\'$textname\\\'].value\',\'opener.we_cmd(\\\'reload_editpage\\\');opener._EditorFrame.setEditorIsHot(true);\',\'".session_id()."\',0,\'\',1)");

if($GLOBALS["we_editmode"]): ?>
<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
	<tr>
		<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b>' . $_showName . '</b></span></td>
		<td><?php print hidden($idname,$we_orderid) ?></td>
		<td><?php print htmlTextInput($textname,' . $size . ',$path,"",\' readonly\',"text",0,0); ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $button; ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $delbutton; ?></td>
	</tr>
</table><?php endif;
';
			} else {
				if (strpos($we_orderid,'$')===false ){//Bug 4848
					$php .='$we_orderid=' . $we_orderid . '	;
					';
				} else {
					$php.='$we_orderid =  isset('.$we_orderid.') ? "'.$we_orderid.'" : $GLOBALS["'.str_replace('$','', $we_orderid). '"];';
				}
				$php .= '$we_orderid = $we_orderid ? $we_orderid : (isset($_REQUEST["we_orderid"]) ? $_REQUEST["we_orderid"] : 0);
';
			}

			$php .= '$GLOBALS["lv"] = new we_ordertag($we_orderid,"' . $condition . '",'.$hidedirindex.');
$lv = clone($GLOBALS["lv"]); // for backwards compatibility
if(is_array($GLOBALS["we_lv_array"])) array_push($GLOBALS["we_lv_array"],clone($GLOBALS["lv"]));
if($GLOBALS["lv"]->avail): ?>';

			if ($postName != "") {
				$content = str_replace('$', '\$', $php); //	to test with blocks ...
			}

			$pre = $this->getStartCacheCode($tag, $attribs);

			return $this->replaceTag($tag, $code, $pre . $php);
		} else { return str_replace($tag, modulFehltError('Shop','"order"'), $code); }
	}

function parseOrderItemTag($tag, $code, $attribs = "", $postName = "")
	{

		if (defined("WE_SHOP_MODULE_DIR")) {

			eval('$arr = array(' . $attribs . ');');

			$we_button = new we_button();

			$condition = we_getTagAttributeTagParser("condition", $arr, 0);
			$we_orderitemid = we_getTagAttributeTagParser("id", $arr, 0);
			$we_orderid = we_getTagAttributeTagParser("orderid", $arr, 0);

			//$name = we_getTagAttributeTagParser("name", $arr) . $postName;
			//$_showName = we_getTagAttributeTagParser("name", $arr);
			//$size = we_getTagAttributeTagParser("size", $arr, 30);
			if ($condition) {
				$condition = $condition.' AND '."IntID = ".$we_orderitemid;
			} else {
				$condition = "IntID = ".$we_orderitemid;
			}
			if (defined('TAGLINKS_DIRECTORYINDEX_HIDE') && TAGLINKS_DIRECTORYINDEX_HIDE){
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "true", false);
			} else {
				$hidedirindex = we_getTagAttributeTagParser("hidedirindex", $arr, "false", false);
			}
			$php = '<?php

if (!isset($GLOBALS["we_lv_array"])) {
	$GLOBALS["we_lv_array"] = array();
}

include_once(WE_SHOP_MODULE_DIR . "we_orderitemtag.inc.php");
include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/html/we_button.inc.php");
';

			if ($name) {
				if (strpos($name, " ") !== false) {
					return parseError(sprintf(g_l('parser','[name_with_space]'), "object"));
				}

				$php .= '
		$we_doc = $GLOBALS["we_doc"];
		';
				if (strpos($we_orderitemid,'$')===false ){//Bug 4848
					$php.='$we_orderitemid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : ' . $we_orderitemid . ';';
				} else {
					$php.='$we_orderitemid = $we_doc->getElement("' . $name . '") ? $we_doc->getElement("' . $name . '") : isset('.$we_orderitemid.') ? "'.$we_orderitemid.'" : $GLOBALS["'.str_replace('$','', $we_orderitemid). '"];';
				}
				$php .= '
		$we_ordeitemrid = $we_orderitemid ? $we_orderitemid : (isset($_REQUEST["we_orderitemid"]) ? $_REQUEST["we_orderitemid"] : 0);
		$path = "/".$we_orderitemid;
		$textname = \'we_\'.$we_doc->Name.\'_txt[' . $name . '_path]\';
		$idname = \'we_\'.$we_doc->Name.\'_txt[' . $name . ']\';
		$table = SHOP_TABLE;
		$we_button = new we_button();
		$delbutton = $we_button->create_button("image:btn_function_trash", "javascript:document.forms[0].elements[\'$idname\'].value=0;document.forms[0].elements[\'$textname\'].value=\'\';_EditorFrame.setEditorIsHot(false);we_cmd(\'reload_editpage\');");
		$button    = $we_button->create_button("select", "javascript:we_cmd(\'openSelector\',document.forms[0].elements[\'$idname\'].value,\'$table\',\'document.forms[\\\'we_form\\\'].elements[\\\'$idname\\\'].value\',\'document.forms[\\\'we_form\\\'].elements[\\\'$textname\\\'].value\',\'opener.we_cmd(\\\'reload_editpage\\\');opener._EditorFrame.setEditorIsHot(true);\',\'".session_id()."\',0,\'\',1)");

if($GLOBALS["we_editmode"]): ?>
<table border="0" cellpadding="0" cellspacing="0" background="<?php print IMAGE_DIR ?>backgrounds/aquaBackground.gif">
	<tr>
		<td style="padding:0 6px;"><span style="color: black; font-size: 12px; font-family: Verdana, sans-serif"><b>' . $_showName . '</b></span></td>
		<td><?php print hidden($idname,$we_orderitemid) ?></td>
		<td><?php print htmlTextInput($textname,' . $size . ',$path,"",\' readonly\',"text",0,0); ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $button; ?></td>
		<td>' . getPixel(6, 4) . '</td>
		<td><?php print $delbutton; ?></td>
	</tr>
</table><?php endif;
';
			} else {
				if (strpos($we_orderitemid,'$')===false ){//Bug 4848
					$php .='$we_orderitemid=' . $we_orderitemid . '	;
					';
				} else {
					$php.='$we_orderitemid =  isset('.$we_orderitemid.') ? "'.$we_orderitemid.'" : $GLOBALS["'.str_replace('$','', $we_orderitemid). '"];';
				}
				$php .= '
$we_orderitemid = $we_orderitemid ? $we_orderitemid : (isset($_REQUEST["we_orderitemid"]) ? $_REQUEST["we_orderitemid"] : 0);
';
			}

			$php .= '$GLOBALS["lv"] = new we_orderitemtag($we_orderitemid,"' . $condition . '",'.$hidedirindex.');
$lv = clone($GLOBALS["lv"]); // for backwards compatibility
if(is_array($GLOBALS["we_lv_array"])) array_push($GLOBALS["we_lv_array"],clone($GLOBALS["lv"]));
if($GLOBALS["lv"]->avail): ?>';

			if ($postName != "") {
				$content = str_replace('$', '\$', $php); //	to test with blocks ...
			}

			$pre = $this->getStartCacheCode($tag, $attribs);

			return $this->replaceTag($tag, $code, $pre . $php);
		} else { return str_replace($tag, modulFehltError('Shop','"orderitem"'), $code); }
	}

	function parserepeatShopitem($tag, $code, $attribs = "")
	{
		if (defined("SHOP_TABLE")) {
			eval('$arr = array(' . $attribs . ');');

			$shopname = we_getTagAttributeTagParser("shopname", $arr);

			$php = '<?php
		include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/shop/we_conf_shop.inc.php");
		$_SESSION["we_shopname"]="' . $shopname . '";

		if (!isset($GLOBALS["' . $shopname . '"])||empty($GLOBALS["' . $shopname . '"])) {
			echo parseError(sprintf(g_l(\'parser\',\'[missing_createShop]\',\'repeatShopItem\'));
			return;
		}


		$GLOBALS["lv"] = new shop($GLOBALS["' . $shopname . '"]);

		while($GLOBALS["lv"]->next_record()) {
	?>';

			return $this->replaceTag($tag, $code, $php);
		} else { return str_replace($tag, modulFehltError('Shop','"repeatShopitem"'), $code); }
	}

	##########################################################################################
	function parseadddelShopitem($tag, $code, $attribs = "")
	{
	if (defined("SHOP_TABLE")) {

			$php = '';

			if (defined('SHOP_TABLE')) {

				eval('$arr = array(' . $attribs . ');');

				$shopname = we_getTagAttributeTagParser("shopname", $arr);
				$floatquantities = we_getTagAttributeTagParser("floatquantities", $arr,'',true);
				$floatquantities = empty($floatquantities) ? 'false' : $floatquantities;
				$php = '<?php
				$floatquantities='.$floatquantities.';
				include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_modules/shop/we_conf_shop.inc.php");
				$floatfilter = new Zend_Filter_LocalizedToNormalized();
				if((isset($_REQUEST["shopname"]) && $_REQUEST["shopname"]=="' . $shopname . '") || !isset($_REQUEST["shopname"]) || $_REQUEST["shopname"]==""){
					if ( isset($_REQUEST["shop_cart_id"]) && is_array($_REQUEST["shop_cart_id"]) ) {
						if($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
							if($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
								foreach ($_REQUEST["shop_cart_id"] as $cart_id => $cart_amount) {
									$' . $shopname . '->Set_Cart_Item($cart_id, $floatquantities ? $floatfilter->filter($cart_amount):$cart_amount);
									$_SESSION["' . $shopname . '_save"] = $' . $shopname . '->getCartProperties();
								}
							}
						}
					}
					else if(isset($_REQUEST["shop_anzahl_und_id"]) && is_array($_REQUEST["shop_anzahl_und_id"])) {
						if($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
							if($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
								//	reset the Array
								reset($_REQUEST["shop_anzahl_und_id"]);
								while(list($shop_articleid_variant,$shop_anzahl)=each($_REQUEST["shop_anzahl_und_id"])) {
									$articleInfo = explode("_",$shop_articleid_variant);
									$shop_artikelid = $articleInfo[0];
									$shop_artikeltype = $articleInfo[1];
									$shop_variant = (isset($articleInfo[2]) ? $articleInfo[2] : "");
									$' . $shopname . '->Set_Item($shop_artikelid,$floatquantities ? $floatfilter->filter($shop_anzahl):$shop_anzahl ,$shop_artikeltype, $shop_variant);
									$_SESSION["' . $shopname . '_save"] = $' . $shopname . '->getCartProperties();
									unset($articleInfo);
								}
							}
							$_SESSION["tb"]=$_REQUEST["t"];
						}
					}
					else if(isset($_REQUEST["shop_artikelid"]) && $_REQUEST["shop_artikelid"] != "" && isset($_REQUEST["shop_anzahl"]) && $_REQUEST["shop_anzahl"] != "0") {
						if($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0) ) {
							if($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0) ) {
								$' . $shopname . '->Add_Item($_REQUEST["shop_artikelid"],$floatquantities ? $floatfilter->filter($_REQUEST["shop_anzahl"]):$_REQUEST["shop_anzahl"], $_REQUEST["type"], (isset($_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"]) ? $_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"] : ""), ( ( isset($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) && is_array($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) ) ? $_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"] : array() ) );
								$_SESSION["' . $shopname . '_save"] = $' . $shopname . '->getCartProperties();
							}
							$_SESSION["tb"]=$_REQUEST["t"];
						}
					}
					else if(isset($_REQUEST["del_shop_artikelid"]) && $_REQUEST["del_shop_artikelid"] != "") {
						if($_REQUEST["t"] > (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
							if($_REQUEST["t"] != (isset($_SESSION["tb"]) ? $_SESSION["tb"] : 0 ) ) {
								$' . $shopname . '->Del_Item($_REQUEST["del_shop_artikelid"], $_REQUEST["type"], (isset($_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"]) ? $_REQUEST["' . WE_SHOP_VARIANT_REQUEST . '"] : ""), ( ( isset($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) && is_array($_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"]) ) ? $_REQUEST["' . WE_SHOP_ARTICLE_CUSTOM_FIELD . '"] : array() ) );
								$_SESSION["' . $shopname . '_save"] = $' . $shopname . '->getCartProperties();
							}
							$_SESSION["tb"]=$_REQUEST["t"];
						}
					}
				}
				?>';
			}

			return $this->replaceTag($tag, $code, $php);
		} else { return str_replace($tag, modulFehltError('Shop','"adddelShopitem"'), $code); }
	}

	function parseFormTag($tag, $code, $attribs = "")
	{
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
		if (array_key_exists ('nameid', $arr)) { // Bug #3153
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
				$arr,
				array(

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
				$php = '<?php $__id__ = ' . $id . ';$GLOBALS["we_form_action"] = f("SELECT Path FROM ".FILE_TABLE." WHERE ID=".abs($__id__),"Path",$GLOBALS["DB_WE"]); ?>
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
				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]) : ?>' . getHtmlTag(
						'form',
						$formAttribs,
						'',
						false,
						true) . getHtmlTag(
						'input',
						array(

								'xml' => $xml,
								'type' => 'hidden',
								'name' => 'type',
								'value' => '<?php if( isset($GLOBALS["lv"]->classID) ){ echo "o"; }else if( isset($GLOBALS["lv"]->ID) ){ echo "w"; }else if( (isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID) )){echo "o";}else if($GLOBALS["we_doc"]->ID){ echo "w"; } ?>'
						)) . getHtmlTag(
						'input',
						array(

								'xml' => $xml,
								'type' => 'hidden',
								'name' => 'shop_artikelid',
								'value' => '<?php if(isset($GLOBALS["lv"]->classID) || isset($GLOBALS["we_doc"]->ClassID) || isset($GLOBALS["we_doc"]->ObjectID)){ echo (isset($GLOBALS["lv"]) && $GLOBALS["lv"]->DB_WE->Record["OF_ID"]!="") ? $GLOBALS["lv"]->DB_WE->Record["OF_ID"] : (isset($we_doc->DB_WE->Record["OF_ID"]) ? $we_doc->DB_WE->Record["OF_ID"] : (isset($we_doc->OF_ID) ? $we_doc->OF_ID : $we_doc->ID)); }else { echo (isset($GLOBALS["lv"]) && isset($GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]) && $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1]!="") ? $GLOBALS["lv"]->IDs[$GLOBALS["lv"]->count-1] : $we_doc->ID; } ?>'
						)) . getHtmlTag(
						'input',
						array(

								'xml' => $xml,
								'type' => 'hidden',
								'name' => 'we_variant',
								'value' => '<?php print (isset($GLOBALS["we_doc"]->Variant) ? $GLOBALS["we_doc"]->Variant : ""); ?>'
						)) . getHtmlTag(
						'input',
						array(

								'xml' => $xml,
								'type' => 'hidden',
								'name' => 't',
								'value' => '<?php echo time(); ?>'
						)) . '<?php endif; ?>';
				break;
			case "object" :
			case "document" :
				$php .= '<?php if(!isset($_REQUEST["edit_' . $type . '"])): if(isset($GLOBALS["WE_SESSION_START"]) && $GLOBALS["WE_SESSION_START"]){ unset($_SESSION["we_' . $type . '_session_' . $formname . '"] );}  endif; ?>
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

					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]): ?>' . getHtmlTag(
							'form',
							$formAttribs,
							'',
							false,
							true) . getHtmlTag(
							'input',
							array(
								'type' => 'hidden', 'name' => 'edit_' . $type, 'value' => 1, 'xml' => $xml
							)) . getHtmlTag(
							'input',
							array(

									'type' => 'hidden',
									'name' => 'we_edit' . $typetmp . '_ID',
									'value' => '<?php print isset($_REQUEST["we_edit' . $typetmp . '_ID"]) ? ($_REQUEST["we_edit' . $typetmp . '_ID"]) : 0; ?>',
									'xml' => $xml
							)) . '<?php endif;?>';
				} else {
					$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]): ?>' . getHtmlTag(
							'form',
							$formAttribs,
							'',
							false,
							true) . '<?php endif;?>';
				}
				break;
			case "formmail" :
				$successpage = $onsuccess ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onsuccess . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$errorpage = $onerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$mailerrorpage = $onmailerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onmailerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$recipienterrorpage = $onrecipienterror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $onrecipienterror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';
				$captchaerrorpage = $oncaptchaerror ? '<?php print f("SELECT Path FROM ".FILE_TABLE." WHERE ID=' . $oncaptchaerror . '","Path",$GLOBALS["DB_WE"]); ?>' : '';

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

						$formAttribs['action'] = '<?php print(f("SELECT Path FROM ".FILE_TABLE." WHERE ID=\'' . $id . '\'","Path",$GLOBALS["DB_WE"])); ?>';
					} else {
						$formAttribs['action'] = '<?php print $_SERVER["SCRIPT_NAME"]; ?>';
					}
				}


				//  now prepare all needed hidden-fields:
				$php = '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]): ?>
				            ' . getHtmlTag('form', $formAttribs, "", false, true) . '
				            <?php
				            	$_recipientString = "' . $recipient . '";
				            	$_recipientArray = explode(",", $_recipientString);
				            	foreach ($_recipientArray as $_key=>$_val) {
				            		$_recipientArray[$_key] = "\"" . trim($_val) . "\"";
				            	}
				            	$_recipientString = implode(",", $_recipientArray);

				            	$_ids = array();
				            	$GLOBALS["DB_WE"]->query("SELECT * FROM " . RECIPIENTS_TABLE . " WHERE Email IN(" . $_recipientString . ")");
				            	while ($GLOBALS["DB_WE"]->next_record()) {
				            		$_ids[] = $GLOBALS["DB_WE"]->f("ID");
				            	}

				            	$_recipientIdString = "";
				            	if (count($_ids)) {
				            		$_recipientIdString = implode(",", $_ids);
				            	}

				            ?>
				            <div class="weHide" style="display: none;">
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'order',
								'value' => '<?php print "' . $order . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'required',
								'value' => '<?php print "' . $required . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'subject',
								'value' => '<?php print "' . $subject . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'recipient',
								'value' => '<?php print $_recipientIdString; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'mimetype',
								'value' => '<?php print "' . $mimetype . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'from',
								'value' => '<?php print "' . $from . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(
							'type' => 'hidden', 'name' => 'error_page', 'value' => $errorpage, 'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'mail_error_page',
								'value' => $mailerrorpage,
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'recipient_error_page',
								'value' => $recipienterrorpage,
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(
							'type' => 'hidden', 'name' => 'ok_page', 'value' => $successpage, 'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'charset',
								'value' => '<?php print "' . $charset . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'confirm_mail',
								'value' => '<?php print "' . $confirmmail . '"; ?>',
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'pre_confirm',
								'value' => $preconfirm,
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'post_confirm',
								'value' => $postconfirm,
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(
							'type' => 'hidden', 'name' => 'we_remove', 'value' => $remove, 'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(
							'type' => 'hidden', 'name' => 'forcefrom', 'value' => $forcefrom, 'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'captcha_error_page',
								'value' => $captchaerrorpage,
								'xml' => $xml
						)) . '
                                ' . getHtmlTag(
						'input',
						array(

								'type' => 'hidden',
								'name' => 'captchaname',
								'value' => $captchaname,
								'xml' => $xml
						)) . '
			                 </div>
				        <?php endif;?>';
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

				$php .= '<?php if(!isset($GLOBALS["we_editmode"]) || !$GLOBALS["we_editmode"]): ?>' . getHtmlTag(
						'form',
						$formAttribs,
						"",
						false,
						true) . "<?php endif; ?>\n";
		}

		$pre = $this->getStartCacheCode($tag, $attribs);

		return $this->replaceTag($tag, $code, $pre . $php);
	}

	/**
	 * @return string
	 * @param string $tag
	 * @param string $code
	 * @desc removes the complete tag from the template. Information is only saved in database
	 *		used to remove we:hidePages and we:controlElement
	 */
	function parseRemoveTags($tag, $code)
	{

		return $this->replaceTag($tag, $code, '');
	}

	function parseXMLNode($tag, $code, $attribs)
	{

		eval('$attr = array(' . $attribs . ');');

		$foo = attributFehltError($attr, "xpath", "xmlnode");
		if ($foo)
			return str_replace($tag, $foo, $code);

		$php = "<?php ";

		$unq = uniqid(rand());

		$feed_name = "feed_" . $unq;
		$got_name = "got_" . $unq;
		$c_name = "c_" . $unq;
		$otac_name = "otac_" . $unq;
		$nodes_name = "nodes_" . $unq;
		$out_name = "node_" . $unq;
		$ind_name = "ind_" . $unq;
		$node_name = "node_" . $unq;
		$parent_name = "parent_" . $unq;
		$pind_name = "pind_" . $unq;

		$php .= '
		$' . $out_name . '="";
		if(!isset($GLOBALS["xpaths"])) $GLOBALS["xpaths"]=array();
		if(!isset($GLOBALS["xstack"])) $GLOBALS["xstack"]=array();
		$' . $pind_name . '=count($GLOBALS["xstack"])-1;
		if($' . $pind_name . '<0){
			$' . $pind_name . '=0;
			$' . $parent_name . '="";
		}
		else{
			$' . $parent_name . '=$GLOBALS["xstack"][$' . $pind_name . '];
		}

		$' . $ind_name . '=count($GLOBALS["xpaths"])+1;
		$GLOBALS["xpaths"][$' . $ind_name . ']=array();
		$GLOBALS["xpaths"][$' . $ind_name . ']["xpath"]="' . $attr["xpath"] . '";
		$GLOBALS["xpaths"][$' . $ind_name . ']["parent"]=$' . $parent_name . ' ;

		$' . $got_name . '=false;

		';

		// find feed
		if (isset($attr["url"])) {
			include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/we_exim/weXMLBrowser.class.php");
			$php .= '
			$' . $feed_name . '=new weXMLBrowser("' . $attr["url"] . '");
			$GLOBALS["xpaths"][$' . $ind_name . ']["url"]="' . $attr["url"] . '";
			$' . $got_name . '=true;
			';
		} else
			if (isset($attr["feed"])) {
				$php .= '
			$' . $feed_name . '=$GLOBALS["xmlfeeds"]["' . $attr["feed"] . '"];
			$GLOBALS["xpaths"][$' . $ind_name . ']["feed"]="' . $attr["feed"] . '";
			$' . $got_name . '=true;
			';
			} else {
				$php .= '
			$' . $got_name . '=false;
			$' . $c_name . '=0;

			if(!empty($' . $parent_name . ')){
				for($' . $c_name . '=$' . $pind_name . ';$' . $c_name . '>-1;$' . $c_name . '--){
					$' . $otac_name . '=$GLOBALS["xstack"][$' . $c_name . '];
					if(isset($GLOBALS["xpaths"][$' . $otac_name . '])){
						if(isset($GLOBALS["xpaths"][$' . $otac_name . ']["url"]) && !empty($GLOBALS["xpaths"][$' . $otac_name . ']["url"])){
							$' . $feed_name . '=new weXMLBrowser($GLOBALS["xpaths"][$' . $otac_name . ']["url"]);
							$GLOBALS["xpaths"][$' . $ind_name . ']["url"]=$GLOBALS["xpaths"][$' . $otac_name . ']["url"];
							$' . $got_name . '=true;
						}
						if(isset($GLOBALS["xpaths"][$' . $otac_name . ']["feed"]) && !empty($GLOBALS["xpaths"][$' . $otac_name . ']["feed"])){
							$' . $feed_name . '=$GLOBALS["xmlfeeds"][$GLOBALS["xpaths"][$' . $otac_name . ']["feed"]];
							$GLOBALS["xpaths"][$' . $ind_name . ']["feed"]=$GLOBALS["xpaths"][$' . $otac_name . ']["feed"];
							$' . $got_name . '=true;
						}
					}
				}
			}
			';
			}

		$php .= '
		$' . $nodes_name . '=array();
		if($' . $got_name . '){
			if(isset($GLOBALS["xsuperparent"])){
				$' . $nodes_name . '=$' . $feed_name . '->evaluate($GLOBALS["xsuperparent"]."/".$GLOBALS["xpaths"][$' . $ind_name . ']["xpath"]);
			}
			if(count($' . $nodes_name . ')==0){
				$' . $nodes_name . '=$' . $feed_name . '->evaluate($GLOBALS["xpaths"][$' . $ind_name . ']["xpath"]);
			}
			if(count($' . $nodes_name . ')==0){
				if(!empty($' . $parent_name . ')){
					for($' . $c_name . '=$' . $pind_name . ';$' . $c_name . '>-1;$' . $c_name . '--){
						$' . $otac_name . '=$GLOBALS["xstack"][$' . $c_name . '];
						if(isset($GLOBALS["xpaths"][$' . $otac_name . '])){
							if(isset($GLOBALS["xpaths"][$' . $otac_name . ']["xpath"]) && !empty($GLOBALS["xpaths"][$' . $otac_name . ']["xpath"])){
								$GLOBALS["xpaths"][$' . $ind_name . ']["xpath"]=$GLOBALS["xpaths"][$' . $otac_name . ']["xpath"]."/".$GLOBALS["xpaths"][$' . $ind_name . ']["xpath"];
								$' . $nodes_name . '=$' . $feed_name . '->evaluate($GLOBALS["xpaths"][$' . $ind_name . ']["xpath"]);
							}
						}
					}
				}
			}
			if(count($' . $nodes_name . ')!=0) $' . $got_name . '=true;
			else  $' . $got_name . '=true;
		}

		array_push($GLOBALS["xstack"],$' . $ind_name . ');

		foreach ($' . $nodes_name . ' as $' . $node_name . '){
			if(!$' . $feed_name . '->hasChildNodes($' . $node_name . ')){
				print $' . $feed_name . '->getData($' . $node_name . ');
			}else{
				$GLOBALS["xsuperparent"]=$' . $node_name . ';

		';

		return $this->replaceTag($tag, $code, $php . ' ?>');

	}

	function parseVotingTag($tag, $code, $attribs)
	{
		if (defined("VOTING_TABLE")) {
			eval('$arr = array(' . $attribs . ');');

			$id = we_getTagAttributeTagParser("id", $arr, 0);
			$name = we_getTagAttributeTagParser("name", $arr, '');
			$version = we_getTagAttributeTagParser("version", $arr, 0);

			$foo = attributFehltError($arr, 'name', 'voting');
			if ($foo)
				return str_replace($tag, $foo, $code);



			$php = '<?php

						include_once($_SERVER["DOCUMENT_ROOT"] . \'/webEdition/we/include/we_modules/voting/weVoting.php\');
						$version = "' . $version . '";
						$version = ($version > 0) ? ($version - 1) : 0;
						$GLOBALS["_we_voting_namespace"] = "' . $name . '";
						$GLOBALS[\'_we_voting\'] = new weVoting();

						if(isset($GLOBALS[\'we_doc\']->elements[$GLOBALS[\'_we_voting_namespace\']][\'dat\'])) {
							$GLOBALS[\'_we_voting\'] = new weVoting($GLOBALS[\'we_doc\']->elements[$GLOBALS[\'_we_voting_namespace\']][\'dat\']);
						} else if(' . $id . '!=0) {
							$GLOBALS[\'_we_voting\'] = new weVoting(' . $id . ');
						} else {
							$__voting_matches = array();
							if(preg_match_all(\'/_we_voting_answer_([0-9]+)_?([0-9]+)?/\', implode(\',\',array_keys($_REQUEST)), $__voting_matches)){
								$GLOBALS[\'_we_voting\'] = new weVoting($__voting_matches[1][0]);
							}
						}
						if(isset($GLOBALS[\'_we_voting\'])) $GLOBALS[\'_we_voting\']->setDefVersion("$version");
					?>';

			return $this->replaceTag($tag, $code, $php);
		} else { return str_replace($tag, modulFehltError('Voting','"Voting"'), $code); }
	}

	function parseVotingListTag($tag, $code, $attribs)
	{
		if (defined("VOTING_TABLE")) {
			eval('$arr = array(' . $attribs . ');');

			$name = we_getTagAttributeTagParser('name', $arr, '');
			$groupid = we_getTagAttributeTagParser('groupid', $arr, 0);
			$rows = we_getTagAttributeTagParser('rows', $arr, 0);
			$desc = we_getTagAttributeTagParser('desc', $arr, "false");
			$order = we_getTagAttributeTagParser('order', $arr, 'PublishDate');
			$subgroup = we_getTagAttributeTagParser("subgroup", $arr, "false");
			$version = we_getTagAttributeTagParser("version", $arr, 1);
			$offset = we_getTagAttributeTagParser("offset", $arr, 0);

			$foo = attributFehltError($arr, 'name', 'votingList');
			if ($foo)
				return str_replace($tag, $foo, $code);

			$version = ($version > 0) ? ($version - 1) : 0;
			$GLOBALS['_we_voting_list_active'] = 1;

			$php = '<?php
				include_once($_SERVER["DOCUMENT_ROOT"] . \'/webEdition/we/include/we_modules/voting/weVotingList.php\');
				$GLOBALS[\'_we_voting_list\'] = new weVotingList(\'' . $name . '\',' . $groupid . ',' . $version . ',' . $rows . ', ' . $offset . ',' . $desc . ',"' . $order . '",' . $subgroup . ');
			?>';

			return $this->replaceTag($tag, $code, $php);
		} else { return str_replace($tag, modulFehltError('Voting','"VotingList"'), $code); }
	}

	function parseCacheIfTag($content)
	{

		$notInListview = !isset($GLOBALS["weListviewCacheActiveIf"]) || $GLOBALS["weListviewCacheActiveIf"] <= 0;

		// caching type
		if (isset($GLOBALS['we_doc']) && $notInListview && isset($GLOBALS['we_doc']->CacheType) && $GLOBALS['we_doc']->CacheType == "document" && $GLOBALS['we_doc']->CacheLifeTime > 0) {
			return "<?php echo '" . $content . "'; ?>";

		// caching disabled
		} else {
			return $content;

		}

	}

}
