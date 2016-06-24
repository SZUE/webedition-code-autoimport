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
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
we_html_tools::protect();

$charset = '';

//FIXME: charset
echo we_html_tools::getHtmlTop('start wePlugin', '', '', we_html_element::jsScript(JS_DIR . 'weplugin.js'), we_html_element::htmlBody(['onload' => "to=window.setInterval(pingPlugin,5000);"], we_html_element::htmlHidden('hm', 0) .
		we_html_element::htmlApplet([
			'name' => 'WePlugin',
			'code' => 'EPlugin',
			'archive' => 'weplugin.jar',
			'codebase' => getServerUrl(true) . WEBEDITION_DIR . 'editors/content/eplugin/',
			'width' => 10,
			'height' => 10,
			' width' => 100, //keep html attributes
			' height' => 100,
			], '', [
			'permissions' => 'all-permissions',
			'param_list' => 'lan_main_dialog_title,lan_alert_noeditor_title,lan_alert_noeditor_text,lan_select_text,lan_select_button,lan_start_button,lan_close_button,lan_clear_button,lan_list_label,lan_showall_label,lan_edit_button,lan_default_for,lan_editor_name,lan_path,lan_args,lan_contenttypes,lan_defaultfor_label,lan_del_button,lan_save_button,lan_autostart_label,lan_settings_dialog_title,lan_alert_nodefeditor_text,lan_del_question,lan_clear_question,lan_encoding,lan_add_button',
			'host' => getServerUrl(true),
			'cmdentry' => getServerUrl(true) . WEBEDITION_DIR . 'editors/content/eplugin/weplugin_cmd.php',
			'lan_main_dialog_title' => g_l('eplugin', '[lan_main_dialog_title]'),
			'lan_settings_dialog_title' => g_l('eplugin', '[lan_settings_dialog_title]'),
			'lan_alert_noeditor_title' => g_l('eplugin', '[lan_alert_noeditor_title]'),
			'lan_alert_noeditor_text' => g_l('eplugin', '[lan_alert_noeditor_text]'),
			'lan_select_text' => g_l('eplugin', '[lan_select_text]'),
			'lan_select_button' => g_l('eplugin', '[lan_select_button]'),
			'lan_start_button' => g_l('eplugin', '[lan_start_button]'),
			'lan_close_button' => g_l('eplugin', '[lan_close_button]'),
			'lan_clear_button' => g_l('eplugin', '[lan_clear_button]'),
			'lan_list_label' => g_l('eplugin', '[lan_list_label]'),
			'lan_showall_label' => g_l('eplugin', '[lan_showall_label]'),
			'lan_edit_button' => g_l('eplugin', '[lan_edit_button]'),
			'lan_default_for' => g_l('eplugin', '[lan_default_for]'),
			'lan_editor_name' => g_l('eplugin', '[lan_editor_name]'),
			'lan_path' => g_l('eplugin', '[lan_path]'),
			'lan_args' => g_l('eplugin', '[lan_args]'),
			'lan_contenttypes' => g_l('eplugin', '[lan_contenttypes]'),
			'lan_defaultfor_label' => g_l('eplugin', '[lan_defaultfor_label]'),
			'lan_del_button' => g_l('eplugin', '[lan_del_button]'),
			'lan_save_button' => g_l('eplugin', '[lan_save_button]'),
			'lan_editor_prop' => g_l('eplugin', '[lan_editor_prop]'),
			'lan_autostart_label' => g_l('eplugin', '[lan_autostart_label]'),
			'lan_alert_nodefeditor_text' => g_l('eplugin', '[lan_alert_nodefeditor_text]'),
			'lan_del_question' => g_l('eplugin', '[lan_del_question]'),
			'lan_clear_question' => g_l('eplugin', '[lan_clear_question]'),
			'lan_encoding' => g_l('eplugin', '[lan_encoding]'),
			'lan_add_button' => g_l('eplugin', '[lan_add_button]'),
			'lan_add_button' => g_l('eplugin', '[lan_add_button]'),
			]
		) .
		we_html_element::htmlForm(['name' => 'we_form', 'target' => 'load', 'action' => WEBEDITION_DIR . 'editors/content/eplugin/weplugin_cmd.php', 'method' => 'post', 'accept-charset' => $charset], we_html_element::htmlHiddens([
			'we_cmd[0]' => '',
				'we_cmd[1]' => '',
				'we_cmd[2]' => '',
				'we_cmd[3]' => '',
				'we_cmd[4]' => ''])
		)
	)
);
