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

$_iFrmPadAtts['src'] = WE_INCLUDES_DIR . 'we_widgets/mod/pad.php?' . http_build_query(array(
			'we_cmd' => array(
				0 => $pad_csv,
				2 => 'home',
				3 => $aProps[1],
				4 => $pad_header_enc,
				5 => $iCurrId,
				6 => $aProps[1],
				7 => 'home')));
$_iFrmPadAtts['id'] = 'm_' . $iCurrId . '_inline';
$_iFrmPadAtts['style'] = 'width:100%;height:287px';
$_iFrmPadAtts['scrolling'] = 'no';
$_iFrmPadAtts['marginheight'] = 0;
$_iFrmPadAtts['marginwidth'] = 0;
$_iFrmPadAtts['frameborder'] = 0;

$oTblDiv = str_replace('>', ' allowtransparency="true">', getHtmlTag('iframe', $_iFrmPadAtts, '', true));

$aLang = array(
	g_l('cockpit', '[notes]') . " - " . base64_decode($pad_header_enc), ""
);
