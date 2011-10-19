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
 * @package    webEdition_language
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/**
 * Language file: users.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_users = array(
		'user_same' => "Собственный пользователь не может быть удален!",
		'grant_owners_ok' => "Владельцы успешно назначены!",
		'grant_owners_notok' => "Ошибка при назначении владельцев!",
		'grant_owners' => "Назначить владельцев",
		'grant_owners_expl' => "Подчинить заданным выше владельцам и пользователям все файлы и директории, находящиеся в текущей директории",
		'make_def_ws' => "По умолчанию",
		'user_saved_ok' => "Пользователь '%s' успешно сохранен!",
		'group_saved_ok' => "Группа '%s' успешно сохранена",
		'alias_saved_ok' => "Алиас '%s' успешно сохранен",
		'user_saved_nok' => "Пользователь '%s' не может быть сохранен!",
		'nothing_to_save' => "Нет предмета для сохранения!",
		'username_exists' => "Имя пользователя '%s' уже существует!",
		'username_empty' => "Имя пользователя не заполнено!",
		'user_deleted' => "Пользователь '%s' удален!",
		'nothing_to_delete' => "Нет предмета удаления!",
		'delete_last_user' => "Для управления требуется по меньшей мере один администратор.\\nВы не можете удалить последнего администратора.",
		'modify_last_admin' => "Для управления требуется по меньшей мере один администратор.\\nВы не можете изменить права последнего адинистратора.",
		'user_path_nok' => "Путь не верен!",
		'user_data' => "Данные пользователя",
		'first_name' => "Имя",
		'second_name' => "Фамилия",
		'username' => "Имя пользователя",
		'password' => "Пароль",
		'workspace_specify' => "Установить рабочую область",
		'permissions' => "Права",
		'user_permissions' => "Полномочия пользователя/редактора",
		'admin_permissions' => "Полномочия администратора",
		'password_alert' => "Пароль должен состоять минимум из 4 знаков",
		'delete_alert_user' => "All user data for user '%s' will be deleted.\\n Are you sure that you wish to do this?", // TRANSLATE
		'delete_alert_alias' => "Все данные алиаса '%s' будут удалены.\\n Вы уверены?",
		'delete_alert_group' => "Все данные группы и пользователей группы '%s' будут удалены. Вы уверены?",
		'created_by' => "Создано пользователем:",
		'changed_by' => "Изменено пользователем:",
		'no_perms' => "У Вас нет полномочий на данную опцию!",
		'publish_specify' => "User is allowed to publish.", // TRANSLATE
		'work_permissions' => "Working permissions", // TRANSLATE
		'control_permissions' => "Control permissions", // TRANSLATE
		'log_permissions' => "Login permissions", // TRANSLATE
		'acces_temp_denied' => "Доступ временно отклонен",
		'description' => "Description", // TRANSLATE
		'group_data' => "Group data", // TRANSLATE
		'group_name' => "Group name", // TRANSLATE
		'group_member' => "Group membership", // TRANSLATE
		'group' => "Group", // TRANSLATE
		'address' => "Address", // TRANSLATE
		'houseno' => "House number/apartment", // TRANSLATE
		'state' => "State", // TRANSLATE
		'PLZ' => "Zip", // TRANSLATE
		'city' => "City", // TRANSLATE
		'country' => "Country", // TRANSLATE
		'tel_pre' => "Phone area code", // TRANSLATE
		'fax_pre' => "Fax area code", // TRANSLATE
		'telephone' => "Phone", // TRANSLATE
		'fax' => "Fax", // TRANSLATE
		'mobile' => "Mobile", // TRANSLATE
		'email' => "E-Mail", // TRANSLATE
		'general_data' => "General data", // TRANSLATE
		'workspace_documents' => "Документы рабочей области",
		'workspace_templates' => "Шаблоны рабочей области",
		'workspace_objects' => "Workspace Objects", // TRANSLATE
		'save_changed_user' => "User has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'not_able_to_save' => "Data has not been saved because of invalidity of data!", // TRANSLATE
		'cannot_save_used' => "Status cannot be changed because it is in processing!", // TRANSLATE
		'geaendert_von' => "Changed by", // TRANSLATE
		'geaendert_am' => "Changed at", // TRANSLATE
		'angelegt_am' => "Set up at", // TRANSLATE
		'angelegt_von' => "Set up by", // TRANSLATE
		'status' => "Status", // TRANSLATE
		'value' => " Value ", // TRANSLATE
		'gesperrt' => "restricted", // TRANSLATE
		'freigegeben' => "open", // TRANSLATE
		'gelöscht' => "deleted", // TRANSLATE
		'ohne' => "without", // TRANSLATE
		'user' => "Пользователь",
		'usertyp' => "Тип пользователя",
		'search' => "Suche",
		'search_result' => "Ergebnis",
		'search_for' => "Suche nach",
		'inherit' => "Inherit permissions from parent group.", // TRANSLATE
		'inherit_ws' => "Inherit documents workspace from parent group.", // TRANSLATE
		'inherit_wst' => "Inherit templates workspace from parent group.", // TRANSLATE
		'inherit_wso' => "Inherit objects workspace from parent group", // TRANSLATE
		'organization' => "Organization", // TRANSLATE
		'give_org_name' => "Organization name", // TRANSLATE
		'can_not_create_org' => "The organisation cannot be created", // TRANSLATE
		'org_name_empty' => "Organization name is empty", // TRANSLATE
		'salutation' => "Salutation", // TRANSLATE
		'sucheleer' => "Не введено ключевое слово для поиска",
		'alias_data' => "Alias data", // TRANSLATE
		'rights_and_workspaces' => "Права и<br>рабочие<br>области",
		'workspace_navigations' => "Workspave Navigation", // TRANSLATE
		'inherit_wsn' => "Inherit navigation workspaces from parent group", // TRANSLATE
		'workspace_newsletter' => "Workspace Newsletter",
		'inherit_wsnl' => "Inherit newsletter workspaces from parent group",
		'delete_user_same' => "You cannot delete your own account.", // TRANSLATE
		'delete_group_user_same' => "You cannot delete your own account.", // TRANSLATE

		'login_denied' => "Login denied",
		'workspaceFieldError' => "ERROR: Invalid workspace entry!",
		'noGroupError' => "Error: Invalid entry in field group!",
		'CreatorID' => "Created by: ", // TRANSLATE
		'CreateDate' => "Created at: ", // TRANSLATE
		'ModifierID' => "Modified by: ", // TRANSLATE
		'ModifyDate' => "Modified at: ", // TRANSLATE
		'lastPing' => "Last Ping: ", // TRANSLATE
		'lostID' => "ID: ", // TRANSLATE
		'lostID2' => " (deleted)", // TRANSLATE
);
