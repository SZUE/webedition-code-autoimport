<?php

/**
 * These should be consts only used INSIDE WE, we should not need to load this class in frontend
 *
 * $Rev: 12578 $
 * $Author: mokraemer $
 * $Date: 2016-07-31 23:08:57 +0200 (So, 31. Jul 2016) $
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
 * @package constants
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_base_jsConstants{

	static function process($what){
		//first set header for all
		header('Content-Type: text/javascript', true);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT', true);
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime(__FILE__)) . ' GMT', true);
		header('Cache-Control: max-age=86400, must-revalidate', true);
		header('Pragma: ', true);

		switch($what){
			case 'selectors':
				return we_selector_file::getJSConsts();
			case 'g_l.fileselector':
				return we_selector_file::getJSLangConsts();
			case 'g_l.selectors.category':
				return we_category::getJSLangConsts();
			case 'g_l.weSearch':
				return we_search_search::getJSLangConsts();
			case 'g_l.versions':
				return we_versions_version::getJSLangConsts();
			case 'g_l.workflow':
				return we_workflow_workflow::getJSLangConsts();
			case 'g_l.voting':
				return we_voting_voting::getJSLangConsts();
			case 'g_l.users':
				return we_users_user::getJSLangConsts();
			case 'g_l.shop':
				return we_shop_shop::getJSLangConsts();
			case 'g_l.newsletter':
				return we_newsletter_newsletter::getJSLangConsts();
			case 'g_l.navigation':
				return we_navigation_navigation::getJSConsts();
			case 'g_l.glossary':
				return we_glossary_glossary::getJSConsts();
			case 'g_l.customer':
				return we_customer_customer::getJSLangConsts();
			case 'g_l.banner':
				return we_banner_banner::getJSLangConsts();
			case 'g_l.metadatafields':
				return we_metadata_metaData::getJSLangConsts();
			case 'g_l.prefs':
				return we_base_preferences::getJSLangConsts();
			case 'g_l.messaging':
				return we_messaging_messaging::getJSLangConsts();
			case 'g_l.backupWizard':
				return we_backup_wizard::getJSLangConsts();
			case 'g_l.exports':
				return we_export_export::getJSLangConsts();
			case 'g_l.rebuild':
				return we_rebuild_wizard::getJSLangConsts();
			case 'g_l.thumbnail':
				return we_thumbnail::getJSLangConsts();
			default:
				t_e('loading of JS consts ' . $what . ' failed');
		}
	}

}
