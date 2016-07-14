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
list($pad_header_enc, $pad_csv) = explode(',', $aProps[3]);

$iFrmPadAtts['src'] = WEBEDITION_DIR . 'we_cmd.php?' . http_build_query(array(
		'mod' => 'pad',
		'we_cmd' => array(
			0 => 'widget_cmd',
			1 => 'reload',
			2 => $pad_csv,
			3 => '',
			4 => 'home',
			5 => $aProps[1],
			6 => $pad_header_enc,
			7 => 'm_' . $iCurrId,
	)));
$iFrmPadAtts['id'] = 'm_' . $iCurrId . '_inline';
$iFrmPadAtts['style'] = 'width:100%;height:287px';

$oTblDiv = str_replace('>', ' allowtransparency="true">', getHtmlTag('iframe', $iFrmPadAtts, '', true));

$aLang = array(
	g_l('cockpit', '[notes]') . " - " . base64_decode($pad_header_enc), ""
);
