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
function we_tag_ifIsActive($attribs){
	$name = weTag_getAttribute('_name_orig', $attribs);
	switch($name){
		case 'banner':
			return defined('BANNER_TABLE');
		case 'customer':
			return defined('CUSTOMER_TABLE');
		case 'glossary':
			return defined('GLOSSARY_TABLE');
		case 'messaging':
			return defined('MESSAGES_TABLE');
		case 'newsletter':
			return defined('NEWSLETTER_TABLE');
		case 'object':
			return defined('OBJECT_TABLE');
		case 'shop':
			return defined('SHOP_TABLE');
		case 'scheduler':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER);
		case 'voting':
			return defined('VOTING_TABLE');
		case 'workflow':
			return defined('WORKFLOW_TABLE');

		default:
			return false;
	}
}
