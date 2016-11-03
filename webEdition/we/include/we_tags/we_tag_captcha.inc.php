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
function we_parse_tag_captcha($a, $c, array $attribs){
	if(($foo = attributFehltError($attribs, ['width' => false, 'height' => false], __FUNCTION__))){
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
	$file = 'we_captcha_' . $GLOBALS['we_doc']->ID . '.php';
	$realPath = rtrim(realpath($_SERVER['DOCUMENT_ROOT'] . $path), '/') . '/' . $file;
	we_base_file::deleteLocalFile($realPath);


	$GLOBALS['DB_WE']->query('REPLACE INTO ' . CAPTCHADEF_TABLE . ' SET ' . we_database_base::arraySetter([
		'ID' => $GLOBALS['we_doc']->ID,
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
			'transparent' => intval($bgcolor && $transparent),
			'style' => $style,
			'stylecolor' => $stylecolor,
			'stylenumber' => $stylenumber,
			'angle' => $angle,
			'type' => $bgcolor && $transparent ? 'gif' : $type
			]));

	// clean attribs
	$attribs = removeAttribs($attribs, ['path',
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
		'stylenumber',
		'src'
	]);

	return '<?php printElement(' . we_tag_tagParser::printTag('captcha', $attribs) . ');?>';
}

function we_tag_captcha(array $attribs){
	static $captcha = [];
	$tid = empty($GLOBALS['we_doc']) ? (empty($attribs['src']) ? 0 : intval($attribs['src'])) : $GLOBALS['we_doc']->TemplateID;

	if(!empty($GLOBALS['we_doc']) && !$GLOBALS['we_doc']->IsDynamic){
		//static documents link to dynamic source
		$attribs['src'] = WEBEDITION_DIR . 'showCaptcha.php?id=' . $tid;
		return getHtmlTag('img', $attribs);
	}

	if($tid){
		if(empty($captcha[$tid])){
			$attr = getHash('SELECT * FROM ' . CAPTCHADEF_TABLE . ' WHERE ID=' . $tid) ?: getHash('SELECT * FROM ' . CAPTCHADEF_TABLE . '  LIMIT 1');

			$image = new we_captcha_image($attr['width'], $attr['height'], $attr['maxlength']);
			if($attr['fontpath']){
				$image->setFontPath($attr['fontpath']);
			}
			$image->setFont($attr['font'], $attr['fontsize'], $attr['fontcolor']);
			$image->setCharacterSubset($attr['subset'], $attr['case'], $attr['skip']);
			$image->setAlign($attr['align']);
			$image->setVerticalAlign($attr['valign']);
			$image->setBackground($attr['bgcolor'], $attr['transparent']);
			$image->setStyle($attr['style'], $attr['stylecolor'], $attr['stylenumber']);
			$image->setAngleRange($attr['angle']);
			if(empty($GLOBALS['we_doc'])){ //called via we_showCaptcha
				return we_captcha_captcha::display($image, $attr['type']);
			}

			list($type, $data) = we_captcha_captcha::get($image, $attr['type']);
			$captcha[$tid] = $type . ';base64,' . base64_encode($data);
		}
		$attribs['src'] = 'data:' . $captcha[$tid];
		return getHtmlTag('img', $attribs);
	}
}
