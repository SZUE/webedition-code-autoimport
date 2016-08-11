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
function we_tag_banner(array $attribs, $content){
	if(($foo = attributFehltError($attribs, 'name', __FUNCTION__))){
		return $foo;
	}

	$bannername = weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING);
	$paths = weTag_getAttribute('paths', $attribs, '', we_base_request::RAW);
	$type = weTag_getAttribute('type', $attribs, 'js', we_base_request::STRING);
	$target = weTag_getAttribute('target', $attribs, '', we_base_request::STRING);
	$width = weTag_getAttribute('width', $attribs, ($type === "pixel") ? 1 : '', we_base_request::UNIT);
	$height = weTag_getAttribute('height', $attribs, ($type === "pixel") ? 1 : '', we_base_request::UNIT);
	$link = weTag_getAttribute('link', $attribs, true, we_base_request::BOOL);
	$page = weTag_getAttribute('page', $attribs, '', we_base_request::RAW);
	$bannerclick = weTag_getAttribute('clickscript', $attribs, WEBEDITION_DIR . 'bannerclick.php', we_base_request::URL);
	$getbanner = weTag_getAttribute('getscript', $attribs, WEBEDITION_DIR . 'getBanner.php', we_base_request::URL);
	$xml = weTag_getAttribute('xml', $attribs, XHTML_DEFAULT, we_base_request::BOOL);

	$nocount = $GLOBALS["WE_MAIN_DOC"]->InWebEdition;
	$did = isset($GLOBALS['we_obj']->ID) ? $GLOBALS['we_obj']->TriggerID : $GLOBALS["WE_MAIN_DOC"]->ID; //Fix #10759

	if($type === "pixel"){

		$newAttribs['src'] = $getbanner . '?' .
			http_build_query(array(
				($nocount ? 'nocount' : '') => $nocount,
				'type' => 'pixel',
				'paths' => $paths,
				'bannername' => $bannername,
				'cats' => $GLOBALS['WE_MAIN_DOC']->Category,
				'dt' => (isset($GLOBALS['WE_MAIN_DOC']->DocType) ? $GLOBALS['WE_MAIN_DOC']->DocType : ""),
				($page ? 'page' : '') => $page,
				(!$page ? 'did' : '') => $did,
				'xml' => $xml ? "1" : "0",
		));
		$newAttribs['border'] = 0;
		$newAttribs['alt'] = '';
		$newAttribs['width'] = 1;
		$newAttribs['height'] = 1;

		return getHtmlTag('img', $newAttribs);
	}

	$uniq = md5(uniqid(__FUNCTION__, true));

	// building noscript
	// here build image with link(opt)
	$imgAtts['src'] = $getbanner . '?' .
		http_build_query(array(
			'c' => 1,
			'bannername' => $bannername,
			'cats' => isset($GLOBALS["WE_MAIN_DOC"]->Category) ? $GLOBALS["WE_MAIN_DOC"]->Category : "",
			'dt' => isset($GLOBALS["WE_MAIN_DOC"]->DocType) ? $GLOBALS["WE_MAIN_DOC"]->DocType : "",
			'paths' => $paths,
			($page ? 'page' : '') => $page,
			(!$page ? 'did' : '') => $did,
			'bannerclick' => $bannerclick,
			'xml' => ($xml ? "1" : "0")
	));
	$imgAtts['alt'] = '';
	$imgAtts['border'] = 0;
	if($width){
		$imgAtts['width'] = $width;
	}
	if($height){
		$imgAtts['height'] = $height;
	}
	$img = getHtmlTag('img', $imgAtts);

	if($link){ //  with link
		$linkAtts['href'] = $bannerclick . '?' . http_build_query(array(
				($nocount ? 'nocount' : '') => $nocount,
				'u' => $uniq,
				'bannername' => $bannername,
				'type' => $type,
				($page ? 'page' : '') => $page,
				(!$page ? 'did' : '') => $did
		));
		if($target){
			$linkAtts['target'] = $target;
		}
		$noscript = getHtmlTag('a', $linkAtts, $img);
	} else { //  only img
		$noscript = $img;
	}


	if($type === "iframe"){
		// stuff for iframe  and ilayer
		$newAttribs = removeAttribs($attribs, array('name', 'paths', 'type', 'target', 'link', 'clickscript', 'getscript', 'page'));
		$newAttribs['xml'] = $xml ? "true" : "false";
		$newAttribs['width'] = $width ? : 468;
		$newAttribs['height'] = $height ? : 60;
		$newAttribs['src'] = $getbanner . '?' . http_build_query(array(
				($nocount ? 'nocount' : '') => $nocount,
				'bannername' => $bannername,
				'cats' => $GLOBALS["WE_MAIN_DOC"]->Category,
				'link' => ($link ? 1 : 0),
				'type' => 'iframe',
				($page ? 'page' : '') => $page,
				(!$page ? 'did' : '') => $did,
				'paths' => $paths,
				'target' => $target,
				'bannerclick' => $bannerclick,
				'width' => $width,
				'height' => $height,
				'xml' => ($xml ? "1" : "0")
		));

		return getHtmlTag('iframe', $newAttribs, $noscript);
	}
	
	return ((isset($GLOBALS['we_obj']->ID) ? true : $GLOBALS['WE_MAIN_DOC']->IsDynamic) ?
			we_banner_banner::getBannerCode($did, $paths, $target, $width, $height, $GLOBALS["WE_MAIN_DOC"]->DocType, $GLOBALS["WE_MAIN_DOC"]->Category, $bannername, $link, "", $bannerclick, $getbanner, "", $page, $GLOBALS["WE_MAIN_DOC"]->InWebEdition, $xml) :
			($type === "cookie" ?
				$noscript :
				we_html_element::jsElement('r = Math.random();document.write ("<" + "script src=\"' . $getbanner . '?' . ($nocount ? 'nocount=' . $nocount . '&amp;' : '') . 'r="+r+"&amp;link=' . ($link ? 1 : 0) . '&amp;bannername=' . rawurlencode($bannername) . '&amp;type=js' . ($page ? ('&amp;page=' . rawurlencode($page)) : ('&amp;did=' . $did . '&amp;paths=' . rawurlencode($paths))) . '&amp;target=' . rawurlencode($target) . '&amp;bannerclick=' . rawurlencode($bannerclick) . '&amp;height=' . rawurlencode($height) . '&amp;width=' . rawurlencode($width) . '"+(document.referer ? ("&amp;referer="+encodeURI(document.referer)) : "")+"\"><" + "/script>");') . '<noscript>' . $noscript . '</noscript>'
			)
		);
}
