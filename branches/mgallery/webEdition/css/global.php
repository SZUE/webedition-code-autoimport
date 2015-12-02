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
$GLOBALS['show_stylesheet'] = true;
define('NO_SESS', 1); //no need for a session
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
//no protect, since login dialog is shown bad

header('Content-Type: text/css', true);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT', true);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT', true);
header('Cache-Control: max-age=86400, must-revalidate', true);
header('Pragma: ', true);

//FIXME: check if we can set style on body, & move the rest to static part => size relative to body
?>

.weSelect,
.wetextinput,
.wetextarea,
.defaultfont,
.npdefaultfont,
.changeddefaultfont,
.defaultgray,
.shopContentfont,
.shopContentfontAlert,
.shopContentfontR
.shopContentfontGR,
.npshopContentfont,
.pshopContentfont,
.pdefaultfont,
.pshopContentfontR,
.selector,
.tableHeader,
.weMultiIconBoxHeadlineThin,
.weMultiIconBoxHeadline,
.weDefaultStyle,
.weDialogHeadline,
.weObjectPreviewHeadline,
.weEditTable
{
font-size: <?php echo (we_base_browserDetect::isMAC()) ? 11 : ((we_base_browserDetect::isUNIX()) ? 13 : 12); ?>px;
font-family: <?php echo g_l('css', '[font_family]'); ?>;
}

.wetextinput,
.wetextarea{
<?php echo (we_base_browserDetect::isIE()) ? '' : 'line-height: 18px;'; ?>
}

.shopContentfontGreySmall,
.shopContentfontSmall {
font-size: <?php echo (we_base_browserDetect::isMAC()) ? 9 : ((we_base_browserDetect::isUNIX()) ? 11 : 10); ?>px;
font-family: <?php echo g_l('css', '[font_family]'); ?>;
}

.middlefont,
.middlefontgray,
.middlefontred{
font-size: <?php echo (we_base_browserDetect::isMAC()) ? 10 : ((we_base_browserDetect::isUNIX()) ? 12 : 11); ?>px;
}

.tree,
.small {
font-size: <?php echo (we_base_browserDetect::isGecko() && we_base_browserDetect::isWin() ? 10 : ((we_base_browserDetect::isUNIX()) ? 11 : 9)); ?>px;
}

.header_small {
font-size: <?php echo (we_base_browserDetect::isGecko() && we_base_browserDetect::isWin() ? 11 : ((we_base_browserDetect::isUNIX()) ? 10 : 10)); ?>px;
}

.header_shop,
.shop_th,
.shop_fontView,
.header,
.header_small,
.tree,
.small,
.middlefont,
.middlefontgray,
.middlefontred
{
font-family: <?php echo g_l('css', '[font_family]'); ?>;
}

.big,
.weDocListSearchHeadline,
.weDocListSearchHeadlineDivs{
font-size: <?php echo (we_base_browserDetect::isGecko() && we_base_browserDetect::isWin() ? 14 : ((we_base_browserDetect::isUNIX()) ? 15 : 13)); ?>px;
font-family: <?php echo g_l('css', '[font_family]'); ?>;
}

.weSidebarBody {
 background: #ffffff url(<?php echo IMAGE_DIR; ?>backgrounds/sidebarBackground.gif) no-repeat fixed bottom right;
}

select.defaultfont{
	border: #AAAAAA solid 1px;
}
select.defaultfont:focus{
	border: #888888 solid 1px;
}