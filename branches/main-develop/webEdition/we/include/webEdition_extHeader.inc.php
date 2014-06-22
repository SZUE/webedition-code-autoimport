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

//load EXT resources
echo we_html_element::cssLink(WEBEDITION_DIR . 'we_ext/extjs/resources/css/ext-all-gray.css') .
	we_html_element::cssLink(WEBEDITION_DIR . 'we_ext/resources/we_css.css') .
	we_html_element::jsScript(WEBEDITION_DIR . 'we_ext/extjs/ext-all.js') .
	we_html_element::jsScript(WEBEDITION_DIR . 'we_ext/fixes/fixTreeStore.js') .
	we_html_element::jsScript(WEBEDITION_DIR . 'we_ext/fixes/fixElementUpdate.js') .
	we_html_element::jsScript(WEBEDITION_DIR . 'we_ext/app/Conf.js') .

	//Set constants for Ext Backend using PHP
	we_html_element::jsElement('
Ext.apply(WE.Conf, {
	BACKEND_LANG : "' . $GLOBALS['WE_LANGUAGE'] . '",
	HYBRIDMODE: ' . (USE_EXT_EXTHYBRID ? 'true' : 'false') . ',
	TREE_DEL_DD: false,
	TREE_DEL_DD_FIX_SEL: false,
	FILE_TABLE: "' . FILE_TABLE . '",
	CATEGORY_TABLE: "' . CATEGORY_TABLE . '",
	TEMPLATES_TABLE: "' . TEMPLATES_TABLE . '"
});
	') .

	//load app
	we_html_element::jsScript(WEBEDITION_DIR . 'we_ext/app.js');