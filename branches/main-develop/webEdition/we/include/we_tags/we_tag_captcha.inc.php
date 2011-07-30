<?php

/**
 * webEdition CMS
 *
 * $Rev: 2633 $
 * $Author: mokraemer $
 * $Date: 2011-03-08 01:16:50 +0100 (Di, 08. Mär 2011) $
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

function we_parse_tag_captcha($attribs, $content) {
	eval('$attribs = ' . $attribs . ';');
	if (($foo = attributFehltError($arr, 'name', 'block')))
		return $foo;
	
		$width = we_getTagAttributeTagParser('width', $attribs, 100);
		$height = we_getTagAttributeTagParser('height', $attribs, 25);
		$path = we_getTagAttributeTagParser('path', $attribs, '/');

		$maxlength = we_getTagAttributeTagParser('maxlength', $attribs, 5);
		$type = we_getTagAttributeTagParser('type', $attribs, 'gif');

		$font = we_getTagAttributeTagParser('font', $attribs, '');
		$fontpath = we_getTagAttributeTagParser('fontpath', $attribs, '');
		$fontsize = we_getTagAttributeTagParser('fontsize', $attribs, '14');
		$fontcolor = we_getTagAttributeTagParser('fontcolor', $attribs, '#000000');

		$angle = we_getTagAttributeTagParser('angle', $attribs, '0');

		$subset = we_getTagAttributeTagParser('subset', $attribs, 'alphanum');
		$case = we_getTagAttributeTagParser('case', $attribs, 'mix');
		$skip = we_getTagAttributeTagParser('skip', $attribs, 'i,I,l,L,0,o,O,1,g,9');

		$valign = we_getTagAttributeTagParser('valign', $attribs, 'random');
		$align = we_getTagAttributeTagParser('align', $attribs, 'random');

		$bgcolor = we_getTagAttributeTagParser('bgcolor', $attribs, '#ffffff');
		$transparent = we_getTagAttributeTagParser('transparent', $attribs, false, true);

		$style = we_getTagAttributeTagParser('style', $attribs, '');
		$stylecolor = we_getTagAttributeTagParser('stylecolor', $attribs, '#cccccc');
		$stylenumber = we_getTagAttributeTagParser('stylenumber', $attribs, '5,10');
		$xml = we_getTagAttributeTagParser('xml', $attribs, '5,10');

		// writing the temporary document
		$file = $path . "we_captcha_" . $GLOBALS['we_doc']->ID . ".php";

		$fh = fopen($_SERVER['DOCUMENT_ROOT'] . $file, "w+");
		$php = '<?php' . "\n" . "\n" . 'require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captchaImage.class.php");
			require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captchaMemory.class.php");
				require_once($_SERVER[\'DOCUMENT_ROOT\']."' . WEBEDITION_DIR . 'we/include/we_classes/captcha/captcha.class.php");' . "\n" . "\n" . "\$image = new CaptchaImage(" . $width . ", " . $height . ", " . $maxlength . ");\n";
		if ($fontpath != "") {
			$php .= "\$image->setFontPath('" . $fontpath . "');\n";
		}
		$php .= "\$image->setFont('" . $font . "', '" . $fontsize . "', '" . $fontcolor . "');\n" . "\$image->setCharacterSubset('" . $subset . "', '" . $case . "', '" . $skip . "');\n" . "\$image->setAlign('" . $align . "');\n" . "\$image->setVerticalAlign('" . $valign . "');\n";
		if (isset($bgcolor) && $transparent) {
			$php .= "\$image->setBackground('" . $bgcolor . "', true);\n";
			$type = "gif";
		} else {
			$php .= "\$image->setBackground('" . $bgcolor . "');\n";
		}
		$php .= "\$image->setStyle('" . $style . "', '" . $stylecolor . "', '" . $stylenumber . "');\n" . "\$image->setAngleRange('" . $angle . "');\n" . "Captcha::display(\$image, '" . $type . "');\n" . "\n" . "?>";
		fputs($fh, $php);
		fclose($fh);

		// clean attribs
		$attribs = removeAttribs($attribs,array(
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
	return '<?php if(we_tag(\'captcha\',' . we_tagParser::printArray($attribs) . ')){' . $content . '} we_post_tag_listview();?>';
}

function we_tag_captcha($attribs, $content) {
	$attribs['src'] .= "?r=" . md5(md5(time()) . session_id());
	return  getHtmlTag("img", $attribs);
}