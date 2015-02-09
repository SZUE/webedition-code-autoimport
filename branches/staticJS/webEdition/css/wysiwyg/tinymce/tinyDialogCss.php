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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
header("Content-type: text/css");
echo (we_base_browserDetect::isMAC()) ? "
.mceActionPanel #replaceBtn{
position:absolute;
right: 325px;
}

.mceActionPanel #replaceAllBtn, .mceActionPanel #apply{
position:absolute;
right: 220px;
}

.mceActionPanel #insert{
position:absolute;
right: 10px;
}

.mceActionPanel #cancel{
position:absolute;
right: 115px;
}

.mceActionPanel #action{
position:absolute;
right: 230px;
}
" : "/* no special position for buttons on WIN/Linux */"
;
