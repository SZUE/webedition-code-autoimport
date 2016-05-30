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
function we_parse_tag_xmlnode($attribs, $content, array $arr){
	$to = weTag_getParserAttribute('to', $arr, 'screen');
	$nameTo = weTag_getParserAttribute('nameto', $arr);

	$unq = '$_xmlnode' . md5(uniqid(__FUNCTION__, true));
	return '<?php ' . $unq . '=' . we_tag_tagParser::printTag('xmlnode', $attribs) . ';
while(' . $unq . '->next()){
	if(  ' . $unq . '->hasChild() ){
		$GLOBALS[\'xsuperparent\']=' . $unq . '->getNode();?>' . $content . '<?php
	}else{
		 echo we_redirect_tagoutput(' . $unq . '->getFeedData(),\'' . $nameTo . '\',\'' . $to . '\');
	}
	//array_pop($GLOBALS["xstack"]);  //ausgeblendet wegen 6339 und beobachtetem Verhalten, das immer maximal zwei Sachen angeziegt wurden
	// fix me
	// wann kann man was aus dem Array löschen?
}
unset(' . $unq . ');?>';
}

/**
 * @return string
 * @param array $attribs
 * @param string $content
 */
function we_tag_xmlnode($attribs){
	if(($foo = attributFehltError($attribs, "xpath", __FUNCTION__))){
		echo $foo;
		return false;
	}
	$feed = we_tag_getPostName(weTag_getAttribute('feed', $attribs, '', we_base_request::STRING));
	$url = weTag_getAttribute('url', $attribs, '', we_base_request::URL);

	if(!isset($GLOBALS["xpaths"])){
		$GLOBALS["xpaths"] = array();
	}
	if(!isset($GLOBALS["xstack"])){
		$GLOBALS["xstack"] = array();
	}
	$pind_name = count($GLOBALS["xstack"]) - 1;
	if($pind_name < 0){
		$pind_name = 0;
		$parent_name = '';
	} else {
		$parent_name = $GLOBALS["xstack"][$pind_name];
	}

	$ind_name = count($GLOBALS['xpaths']) + 1;
	$GLOBALS["xpaths"][$ind_name] = array(
		'xpath' => $attribs["xpath"],
		'parent' => $parent_name
	);

	// find feed
	if($url){
		$feed_name = new we_xml_browser($url);
		$GLOBALS["xpaths"][$ind_name]["url"] = $url;
		$got_name = true;
	} elseif($feed){
		$feed_name = $GLOBALS["xmlfeeds"][$feed];
		$GLOBALS["xpaths"][$ind_name]["feed"] = $feed;
		$got_name = true;
	} else {
		$got_name = false;
		$c_name = 0;

		if(!empty($parent_name)){
			for($c_name = $pind_name; $c_name > -1; $c_name--){
				$otac_name = $GLOBALS["xstack"][$c_name];
				if(isset($GLOBALS["xpaths"][$otac_name])){
					if(isset($GLOBALS["xpaths"][$otac_name]["url"]) && !empty($GLOBALS["xpaths"][$otac_name]["url"])){
						$feed_name = new we_xml_browser($GLOBALS["xpaths"][$otac_name]["url"]);
						$GLOBALS["xpaths"][$ind_name]["url"] = $GLOBALS["xpaths"][$otac_name]["url"];
						$got_name = true;
					}
					if(isset($GLOBALS["xpaths"][$otac_name]["feed"]) && !empty($GLOBALS["xpaths"][$otac_name]["feed"])){
						$feed_name = $GLOBALS["xmlfeeds"][$GLOBALS["xpaths"][$otac_name]["feed"]];
						$GLOBALS["xpaths"][$ind_name]["feed"] = $GLOBALS["xpaths"][$otac_name]["feed"];
						$got_name = true;
					}
				}
			}
		}
	}
	$nodes_name = array();
	if($got_name){
		if(isset($GLOBALS["xsuperparent"])){
			$nodes_name = $feed_name->evaluate($GLOBALS["xsuperparent"] . "/" . $GLOBALS["xpaths"][$ind_name]["xpath"]);
		}
		if(empty($nodes_name)){
			$nodes_name = $feed_name->evaluate($GLOBALS["xpaths"][$ind_name]["xpath"]);
		}
		if(empty($nodes_name)){
			if(!empty($parent_name)){
				for($c_name = $pind_name; $c_name > -1; $c_name--){
					$otac_name = $GLOBALS["xstack"][$c_name];
					if(isset($GLOBALS["xpaths"][$otac_name])){
						if(isset($GLOBALS["xpaths"][$otac_name]["xpath"]) && !empty($GLOBALS["xpaths"][$otac_name]["xpath"])){
							$GLOBALS["xpaths"][$ind_name]["xpath"] = $GLOBALS["xpaths"][$otac_name]["xpath"] . "/" . $GLOBALS["xpaths"][$ind_name]["xpath"];
							$nodes_name = $feed_name->evaluate($GLOBALS["xpaths"][$ind_name]["xpath"]);
						}
					}
				}
			}
		}
		//if(!empty($nodes_name)){
		$got_name = true;
	}

	$GLOBALS["xstack"][] = $ind_name; //war einfach ind_name und fehler undefinend konstant

	return new _we_tag_xmlnode_struct($nodes_name, $feed_name);
}

class _we_tag_xmlnode_struct{
	private $nodes_name;
	private $feed_name;
	private $init = false;

	function __construct($nodes_name, $feed_name){
		$this->nodes_name = $nodes_name;
		$this->feed_name = $feed_name;
	}

	function next(){
		if($this->init){
			return next($this->nodes_name) !== FALSE;
		}
		$this->init = true;
		return reset($this->nodes_name) !== FALSE;
	}

	function hasChild(){
		return $this->feed_name->hasChildNodes(current($this->nodes_name));
	}

	function getFeedData(){
		return $this->feed_name->getData(current($this->nodes_name));
	}

	function getNode(){
		return current($this->nodes_name);
	}

}
