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
// generate ContentType JS-String
$_contentTypes = 'var _Contentypes = new Object();
	_Contentypes["cockpit"] = "icon_cockpit.gif";';
$ct = we_base_ContentTypes::inst();
foreach($ct->getContentTypes() as $ctype){
	$_contentTypes .= '_Contentypes["' . $ctype . '"] = "' . $ct->getIcon($ctype) . '";';
}

/*
 * Browser dependencies
 */

$browser = we_base_browserDetect::inst();
switch($browser->getBrowser()){
	case we_base_browserDetect::SAFARI:
		$heightPlus = 0;
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr><span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" id="###loadId###" title="" class="status"/>&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img id="###modId###" class="status modified"/><img src="' . IMAGE_DIR . 'multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span></nobr><span><img src="' . IMAGE_DIR . 'pixel.gif" height="0" /></span></div>';
		break;
	case we_base_browserDetect::IE:
		$heightPlus = 0;
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr>&nbsp;<span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" id="###loadId###" title="" class="status"/>&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img id="###modId###" class="status modified"/><img src="' . IMAGE_DIR . 'multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span></nobr></div>';

		echo we_html_element::cssElement('
div.tabOver{
		background-position:bottom;
	}
	span.text{
		vertical-align:middle;
	}
	span.spacer{
		vertical-align:middle;
	}
	img.close{
		vertical-align:middle;
		margin:0px;
	}
	span.status{
		vertical-align:middle;
		margin:0px;
	}
	img.status{
		vertical-align:middle;
		margin:0px;
	}');

		break;
	default:
		$heightPlus = 1;
		$tabDummy = '<div class="hidden" id="tabDummy" title="" name="" onclick="top.weMultiTabs.selectFrame(this)"><nobr>&nbsp;<span class="spacer">&nbsp;<img src="' . IMAGE_DIR . 'pixel.gif" id="###loadId###" title="" class="status"/>&nbsp;</span><span id="###tabTextId###" class="text"></span><span class="spacer"><img id="###modId###" class="status modified"/><img src="' . IMAGE_DIR . 'multiTabs/close.gif" id="###closeId###" border="0" vspace="0" hspace="0" onclick="top.weMultiTabs.onCloseTab(this)" onmouseover="this.src=\'' . IMAGE_DIR . 'multiTabs/closeOver.gif\'" onmouseout="this.src=\'' . IMAGE_DIR . 'multiTabs/close.gif\'" class="close" />&nbsp;</span></nobr></div>';
	/* 	switch($browser->getSystem()){
	  case we_base_browserDetect::SYS_MAC:
	  $tabContainerMargin = "0px";
	  break;
	  case we_base_browserDetect::SYS_UNIX:
	  $tabContainerMargin = "0px";
	  break;
	  default:
	  $tabContainerMargin = "-1px";
	  } */
}

echo we_html_element::jsElement($_contentTypes . '
	var contentTypeApp="' . we_base_ContentTypes::APPLICATION . '";
	var heightPlus=' . $heightPlus . ';
') . we_html_element::jsScript(JS_DIR . 'multiEditor/multiTabs.js');
?>

<div id="weMultiTabs">
	<div id="tabContainer" name="tabContainer">
	</div>
	<?php echo $tabDummy; ?>
</div>