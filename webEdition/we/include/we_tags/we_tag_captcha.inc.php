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
function we_parse_tag_captcha($a, $c, array $attribs){
	if(($foo = attributFehltError($attribs, array('width' => false, 'height' => false), __FUNCTION__))){
		return $foo;
	}

	$width = weTag_getParserAttribute('width', $attribs, 100);
	$height = weTag_getParserAttribute('height', $attribs, 25);
	$path = weTag_getParserAttribute('path', $attribs, '/');

	$maxlength = weTag_getParserAttribute('maxlength', $attribs, 5);
	$type = weTag_getParserAttribute('type', $attribs, 'gif');

	$font = weTag_getParserAttribute('font', $attribs);
	$fontpath = weTag_getParserAttribute('fontpath', $attribs, '');
	$fontsize = weTag_getParserAttribute('fontsize', $attribs, 14);
	$fontcolor = weTag_getParserAttribute('fontcolor', $attribs, '#000000');

	$angle = weTag_getParserAttribute('angle', $attribs, 0);

	$subset = weTag_getParserAttribute('subset', $attribs, 'alphanum');
	$case = weTag_getParserAttribute('case', $attribs, 'mix');
	$skip = weTag_getParserAttribute('skip', $attribs, 'i,I,l,L,0,o,O,1,g,9');

	$valign = weTag_getParserAttribute('valign', $attribs, 'random');
	$align = weTag_getParserAttribute('align', $attribs, 'random');

	$bgcolor = weTag_getParserAttribute('bgcolor', $attribs, '#ffffff');
	$transparent = weTag_getParserAttribute('transparent', $attribs, false, true);

	$style = weTag_getParserAttribute('style', $attribs, '');
	$stylecolor = weTag_getParserAttribute('stylecolor', $attribs, '#cccccc');
	$stylenumber = weTag_getParserAttribute('stylenumber', $attribs, '5,10');

	// writing the temporary document
	$file = 'we_captcha_' . $GLOBALS['we_doc']->ID . ".php";
	$realPath = rtrim(realpath($_SERVER['DOCUMENT_ROOT'] . $path), '/') . '/' . $file;

	we_base_file::save($realPath, '<?php
require_once($_SERVER[\'DOCUMENT_ROOT\'].\'' . WE_INCLUDES_DIR . 'we.inc.php\');
require_once(WE_INCLUDES_PATH . \'we_tag.inc.php\');
' . we_tag_tagParser::printTag('captcha', array('_internal' => true,
			'width' => $width,
			'height' => $height,
			'maxlength' => $maxlength,
			'fontpath' => $fontpath,
			'font' => $font,
			'fontsize' => $fontsize,
			'fontcolor' => $fontcolor,
			'subset' => $subset,
			'case' => $case,
			'skip' => $skip,
			'align' => $align,
			'valign' => $valign,
			'bgcolor' => $bgcolor,
			'transparent' => (bool) $bgcolor && $transparent,
			'style' => $style,
			'stylecolor' => $stylecolor,
			'stylenumber' => $stylenumber,
			'angle' => $angle,
			'type' => $bgcolor && $transparent ? 'gif' : $type
		)) . ';', 'w+');

	// clean attribs
	$attribs = removeAttribs($attribs, array(
		'path',
		'maxlength',
		'type',
		'font',
		'fontpath',
		'fontsize',
		'fontcolor',
		'angle',
		'subset',
		'case',
		'skip',
		'align',
		'valign',
		'bgcolor',
		'transparent',
		'style',
		'stylecolor',
		'stylenumber'
	));

	$attribs['src'] = rtrim($path, '/') . '/' . $file;
	return '<?php printElement(' . we_tag_tagParser::printTag('captcha', $attribs) . ');?>';
}

function we_tag_captcha(array $attribs){
	if(!isset($attribs['_internal'])){
		return getHtmlTag("img", $attribs);
	}
	$image = new we_captcha_image($attribs['width'], $attribs['height'], $attribs['maxlength']);
	if($attribs['fontpath']){
		$image->setFontPath($attribs['fontpath']);
	}
	$image->setFont($attribs['font'], $attribs['fontsize'], $attribs['fontcolor']);
	$image->setCharacterSubset($attribs['subset'], $attribs['case'], $attribs['skip']);
	$image->setAlign($attribs['align']);
	$image->setVerticalAlign($attribs['valign']);
	$image->setBackground($attribs['bgcolor'], $attribs['transparent']);
	$image->setStyle($attribs['style'], $attribs['stylecolor'], $attribs['stylenumber']);
	$image->setAngleRange($attribs['angle']);
	we_captcha_captcha::display($image, $attribs['type']);
}
