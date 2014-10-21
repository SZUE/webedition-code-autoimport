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
echo we_html_multiIconBox::getHTML('', '100%', array(
	array('icon' => 'path.gif', 'headline' => g_l('weClass', '[path]'), 'html' => $GLOBALS['we_doc']->formPath(), 'space' => 140),
	array('icon' => 'mastertemplate.gif', 'headline' => g_l('weClass', '[master_template]'), 'html' => $GLOBALS['we_doc']->formMasterTemplate(), 'space' => 140),
	array('icon' => 'doc.gif', 'headline' => g_l('weClass', '[documents]'), 'html' => $GLOBALS['we_doc']->formTemplateDocuments(), 'space' => 140),
	array('icon' => 'charset.gif', 'headline' => g_l('weClass', '[Charset]'), 'html' => $GLOBALS['we_doc']->formCharset(), 'space' => 140),
	array('icon' => 'copy.gif', 'headline' => g_l('weClass', '[copyTemplate]'), 'html' => $GLOBALS['we_doc']->formCopyDocument(), 'space' => 140)
	)
	, 20);
