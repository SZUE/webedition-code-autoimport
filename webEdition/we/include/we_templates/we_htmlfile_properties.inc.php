<?php
/**
 * webEdition CMS
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



include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_classes/html/we_multibox.inc.php");

$parts = array();

array_push($parts,array("icon"=>"path.gif", "headline"=>$GLOBALS["l_we_class"]["path"],"html"=>$GLOBALS['we_doc']->formPath(),"space"=>140));
array_push($parts,array("icon"=>"doc.gif", "headline"=>$GLOBALS["l_we_class"]["document"],"html"=>$GLOBALS['we_doc']->formDocTypeTempl(),"space"=>140));
array_push($parts,array("icon"=>"cat.gif", "headline"=>$GLOBALS["l_global"]["categorys"],"html"=>$GLOBALS['we_doc']->formCategory(),"space"=>140));
array_push($parts,array("icon"=>"navi.gif", "headline"=>$GLOBALS["l_global"]["navigation"],"html"=>$GLOBALS['we_doc']->formNavigation(),"space"=>140));
array_push($parts,array("icon"=>"copy.gif", "headline"=>$GLOBALS["l_we_class"]["copy".$we_doc->ContentType],"html"=>$GLOBALS['we_doc']->formCopyDocument(),"space"=>140));
array_push($parts,array("icon"=>"charset.gif", "headline"=>$GLOBALS["l_we_class"]["Charset"],"html"=>$GLOBALS['we_doc']->formCharset(),"space"=>140));
array_push($parts,array("icon"=>"user.gif", "headline"=>$GLOBALS["l_we_class"]["owners"],"html"=>$GLOBALS['we_doc']->formCreatorOwners(),"space"=>140));

$wepos = weGetCookieVariable("but_weHtmlDocProp");

print we_multiIconBox::getJS();
print we_multiIconBox::getHTML("weHtmlDocProp","100%",$parts,20);
