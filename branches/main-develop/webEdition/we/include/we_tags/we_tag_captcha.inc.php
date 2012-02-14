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
function we_parse_tag_captcha($attribs, $content){
	eval('$attribs = ' . $attribs . ';');
	if(($foo = attributFehltError($attribs, 'width', 'captcha')) || ($foo = attributFehltError($attribs, 'height', 'captcha'))){
		return $foo;
	}

	$width = weTag_getParserAttribute('width', $attribs, 100);
	$height = weTag_getParserAttribute('height', $attribs, 25);
	$path = weTag_getParserAttribute('path', $attribs, '/');

	$maxlength = weTag_getParserAttribute('maxlength', $attribs, 5);
	$type = weTag_getParserAttribute('type', $attribs, 'gif');

	$font = weTag_getParserAttribute('font', $attribs);
	$fontpath = weTag_getParserAttribute('fontpath', $attribs, '');
	$fontsize = weTag_getParserAttribute('fontsize', $attribs, '14');
	$fontcolor = weTag_getParserAttribute('fontcolor', $attribs, '#000000');

	$angle = weTag_getParserAttribute('angle', $attribs, '0');

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
	$xml = weTag_getParserAttribute('xml', $attribs, '5,10');

	// writing the temporary document
	$file = $path . "we_captcha_" . $GLOBALS['we_doc']->ID . ".php";

	$fh = fopen($_SERVER['DOCUMENT_ROOT'] . $file, "w+");
	$php = '<?php' . "\n" . "\n" . 'require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captchaImage.class.php");
			require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captchaMemory.class.php");
				require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captcha.class.php");' . "\n" . "\n" . "\$image = new CaptchaImage(" . $width . ", " . $height . ", " . $maxlength . ");\n";
	if($fontpath != ""){
		$php .= "\$image->setFontPath('" . $fontpath . "');\n";
	}
	$php .= "\$image->setFont('" . $font . "', '" . $fontsize . "', '" . $fontcolor . "');\n" . "\$image->setCharacterSubset('" . $subset . "', '" . $case . "', '" . $skip . "');\n" . "\$image->setAlign('" . $align . "');\n" . "\$image->setVerticalAlign('" . $valign . "');\n";
	if(isset($bgcolor) && $transparent){
		$php .= "\$image->setBackground('" . $bgcolor . "', true);\n";
		$type = "gif";
	} else{
		$php .= "\$image->setBackground('" . $bgcolor . "');\n";
	}
	$php .= "\$image->setStyle('" . $style . "', '" . $stylecolor . "', '" . $stylenumber . "');\n" . "\$image->setAngleRange('" . $angle . "');\n" . "Captcha::display(\$image, '" . $type . "');\n" . "\n" . "?>";
	fputs($fh, $php);
	fclose($fh);

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

	$attribs['src'] = $file;
	return '<?php if(' . we_tag_tagParser::printTag('captcha', $attribs) . '){?>' . $content . '<?php } ?>';
}

function we_tag_captcha($attribs, $content){
	$attribs['src'] .= "?r=" . md5(md5(time()) . session_id());
	return getHtmlTag("img", $attribs);
}