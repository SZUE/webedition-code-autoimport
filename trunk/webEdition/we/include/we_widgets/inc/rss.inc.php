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
if($aProps[3]){
	list($_rssUri, $_rssCont, $_rssNumItems, $_rssTb, $_rssTitle) = explode(',', $aProps[3]);
} else{//use default if data is corrupt
	list($_rssUri, $_rssCont, $_rssNumItems, $_rssTb, $_rssTitle) = array(
		0 => base64_encode('http://www.webedition.org/de/rss/webedition.xml'),
		1 => '111000',
		2 => '0',
		3 => '110000',
		4 => '1',
	);
}

list($bTbLabel, $bTbTitel, $bTbDesc, $bTbLink, $bTbPubDate, $bTbCopyright) = $_rssTb;
$aLabelPrefix = array();
#if ($bTbLabel)
#	$aLabelPrefix[] = g_l('cockpit','[rss_feed]');
if($bTbTitel && $_rssTitle){
	$_feed = (isset($aTrf)) ? $aTrf : $aTopRssFeeds;
	foreach($_feed as $iRssFeedIndex => $aFeed){
		if($_rssUri == $aFeed[1]){
			$aLabelPrefix[] = base64_decode($aFeed[0]);
			break;
		}
	}
}
$sTbPrefix = implode(' - ', $aLabelPrefix);
$aLang = array(
	$sTbPrefix, ''
);

$_iFrmRss = "
<script type=\"text/javascript\">

if ( window.addEventListener ) { // moz
	window.addEventListener(
		\"load\",
		function() {
			top.cockpitFrame.executeAjaxRequest('" . base64_decode(
		$_rssUri) . "', '" . $_rssCont . "', '" . $_rssNumItems . "', '" . $_rssTb . "', '" . $sTbPrefix . "', '" . 'm_' . $iCurrId . "');
		},
		true
	);

} else if ( window.attachEvent ) { // IE
	window.attachEvent( \"onload\", function(){
			top.cockpitFrame.executeAjaxRequest('" . base64_decode(
		$_rssUri) . "', '" . $_rssCont . "', '" . $_rssNumItems . "', '" . $_rssTb . "', '" . $sTbPrefix . "', '" . 'm_' . $iCurrId . "');
		}
	);
}

</script>
<div class=\"rssDiv\" id=\"m_" . $iCurrId . "_inline\" style=\"width: " . $iWidth . "px;height:287px ! important; overflow: auto;\"></div>
";

$oTblCont = new we_html_table(array(
		"cellpadding" => "0", "cellspacing" => "0", "border" => "0"
		), 1, 1);
$oTblCont->setCol(0, 0, null, $_iFrmRss);
?>