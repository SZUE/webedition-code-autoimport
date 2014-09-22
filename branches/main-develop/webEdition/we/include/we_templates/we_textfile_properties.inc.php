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
	array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $GLOBALS['we_doc']->formPath(), 'space' => 120),
	($GLOBALS['we_doc']->ContentType == we_base_ContentTypes::CSS ? array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[document]'), 'html' => $GLOBALS['we_doc']->formParseFile(), 'space' => 140) : null),
	array('icon' => 'charset.gif', 'headline' => g_l('weClass', '[Charset]'), 'html' => $GLOBALS['we_doc']->formCharset(), 'space' => 120),
	array('icon' => 'user.gif', 'headline' => g_l('weClass', '[owners]'), 'html' => $GLOBALS['we_doc']->formCreatorOwners(), 'space' => 120),
	array('icon' => 'copy.gif', 'headline' => g_l('weClass', '[copy' . $GLOBALS['we_doc']->ContentType . ']'), 'html' => $GLOBALS['we_doc']->formCopyDocument(), 'space' => 120));

echo we_html_multiIconBox::getHTML('', '100%', $parts, 30);
