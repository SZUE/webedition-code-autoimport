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
/* last included stylesheet: visualaid- and body-background (if attibute not empty in we:textarea) must not be overwritten by document-css */

define('NO_SESS', 1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
header("Content-type: text/css");
$bgcol = we_base_request::_(we_base_request::STRING, 'tinyMceBackgroundColor');
$bgcol = preg_match('/^[a-f0-9]{6}$/i', $bgcol) ? '#' . $bgcol : $bgcol;
echo $bgcol ? '
body {
background-color: ' . $bgcol . ' !important;
background-image: none;
}
' : '';
?>


/* css for plugin wevisialborders */

acronym.mceItemWeAcronym{
border: 1px dotted gray;
}

abbr.mceItemWeAbbr{
border: 1px dotted gray;
}

span.mceItemWeLang{
border: 1px dotted gray;
}

wegallery{
background-image: url(/webEdition/images/wysiwyg/wegallery.gif);
background-repeat: no-repeat;
display: inline-block;
width: 65px !important;
height: 15px !important;
}