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
class we_glossary_settingControl{

	function processCommands(){
		switch(we_base_request::_(we_base_request::STRING, 'cmd')){
			case 'save_glossary_setting':
				$cmd = new we_base_jsCmd();
				if($this->saveSettings()){
					$cmd->addCmd('msg', ['msg' => g_l('modules_glossary', '[preferences_saved]'), 'prio' => we_message_reporting::WE_MESSAGE_NOTICE]);
					$cmd->addCmd('close');
				} else {
					$cmd->addCmd('msg', ['msg' => g_l('modules_glossary', '[preferences_not_saved]'), 'prio' => we_message_reporting::WE_MESSAGE_ERROR]);
				}
				echo we_html_tools::getHtmlTop(''/* FIXME: missing title */, '', '', '', '<body>' . $cmd->getCmds() . '</body>');

				break;
		}
	}

	function processVariables(){

	}

	function saveSettings($default = false){
		$db = new DB_WE();
		return $db->query('REPLACE INTO ' . SETTINGS_TABLE . ' SET tool="glossary",pref_name="GlossaryAutomaticReplacement",pref_value=' . intval($default ? 1 : we_base_request::_(we_base_request::BOOL, 'GlossaryAutomaticReplacement')));
	}

}
