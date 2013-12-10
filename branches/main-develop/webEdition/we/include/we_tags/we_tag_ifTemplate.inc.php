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
function we_tag_ifTemplate($attribs){
	$id = weTag_getAttribute('id', $attribs);
	$TID = (isset($GLOBALS['we_doc']->TemplateID) ? $GLOBALS['we_doc']->TemplateID : ($GLOBALS['we_doc'] instanceof we_template && isset($GLOBALS['we_doc']->ID) ? $GLOBALS['we_doc']->ID : 0));

	if($TID && !empty($id)){
		return in_array($TID, makeArrayFromCSV($id));
	}
	$parentid = weTag_getAttribute('workspaceID', $attribs, weTag_getAttribute('parentid', $attribs));
	if(!empty($parentid)){
		$curTempPath = (isset($GLOBALS['we_doc']->TemplatePath) ? // in documents
				str_replace(TEMPLATES_PATH, '', $GLOBALS['we_doc']->TemplatePath) :
				// in templates
				$GLOBALS['we_doc']->Path);
		$path = f('SELECT DISTINCT Path FROM ' . TEMPLATES_TABLE . ' WHERE ID=' . intval($parentid) . ' LIMIT 1', 'Path', $GLOBALS['DB_WE']);
		return (!empty($path) && strpos($curTempPath, $path) === 0);
	}

	$path = weTag_getAttribute('path', $attribs);
	return (empty($path) ||
		(isset($GLOBALS['we_doc']->TemplatePath) && preg_match('|^' . TEMPLATES_PATH . str_replace('\\*', '.*', preg_quote($path, '|')) . '\$|', $GLOBALS['we_doc']->TemplatePath)));


	return false;
}
