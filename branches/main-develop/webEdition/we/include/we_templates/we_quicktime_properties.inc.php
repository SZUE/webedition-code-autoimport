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

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_multiIconBox.class.inc.php");

$parts = array();

array_push($parts,array("icon"=>"path.gif", "headline"=>g_l('weClass',"[path]"),"html"=>$GLOBALS['we_doc']->formPath(),"space"=>120));
array_push($parts,array("icon"=>"default.gif", "headline"=>g_l('weClass',"[other]"),"html"=>$GLOBALS['we_doc']->formOther(),"space"=>120));

print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("weQuickProp","100%",$parts,20);