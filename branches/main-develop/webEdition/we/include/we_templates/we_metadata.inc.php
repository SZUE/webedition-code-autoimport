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
$parts = array(
	array("icon" => "upload.gif", "headline" => "", "html" => $GLOBALS['we_doc']->formUpload(), "space" => 140),
	array("icon" => "attrib.gif", "headline" => g_l('weClass', "[attribs]"), "html" => $GLOBALS['we_doc']->formProperties(), "space" => 140),
//array("icon"=>"meta.gif", "headline"=>g_l('weClass',"[metainfo]"),"html"=>$GLOBALS['we_doc']->formMetaInfos(),"space"=>140)
	array("icon" => "meta.gif", "headline" => g_l('weClass', "[metadata]"), "html" => $GLOBALS['we_doc']->formMetaInfos() . $GLOBALS['we_doc']->formMetaData(), "space" => 140),
);
echo we_html_multiIconBox::getJS() .
	we_html_multiIconBox::getHTML("weImgProp", "100%", $parts, 20);
