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
function we_tag_ifIsActive($attribs){
	switch(weTag_getAttribute('_name_orig', $attribs, '', we_base_request::STRING)){
		case 'banner':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::BANNER);
		case 'customer':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::CUSTOMER);
		case 'glossary':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::GLOSSARY);
		case 'messaging':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::MESSAGING);
		case 'newsletter':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::NEWSLETTER);
		case 'object':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::OBJECT);
		case 'shop':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::SHOP);
		case 'scheduler':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER);
		case 'voting':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::VOTING);
		case 'workflow':
			return we_base_moduleInfo::isActive(we_base_moduleInfo::WORKFLOW);

		default:
			return false;
	}
}
