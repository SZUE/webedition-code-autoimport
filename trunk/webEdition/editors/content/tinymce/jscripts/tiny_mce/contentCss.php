<?php
/**
 * webEdition CMS
 *
 * $Rev: 5016 $
 * $Author: lukasimhof $
 * $Date: 2012-11-08 11:53:14 +0200 (Do, 8 Nov 2012) $
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
 * @package    webEdition_tinymce
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */

header("Content-type: text/css");

$bgcol = preg_match('/^[a-f0-9]{6}$/i', $_REQUEST['tinyMceBackgroundColor']) ? '#' . $_REQUEST['tinyMceBackgroundColor'] : $_REQUEST['tinyMceBackgroundColor'];
?>


/* css editor body */

body.mceContentBody {
	font-size: <?php print (we_base_browserDetect::isMAC()) ? "11px" : ((we_base_browserDetect::isUNIX()) ? "13px" : "12px"); ?>;
	background-color: <?php echo $bgcol; ?> !important; /* FF requires a important here */
}


/* css for plugin wevisialborders */

body.mceContentBody {
	//background: #ff0000;
	font-size:12px;
}

acronym.mceItemWeAcronym{
	border: 1px dotted green;
}

abbr.mceItemWeAbbr{ // not yet implemented in wevisualaid
	border: 1px dotted green;
}

abbr.mceItemWeLink{ // not yet implemented in wevisualaid
	border: 1px dotted blue;
}

span.mceItemWeLang{
	border: 1px dotted red;
}