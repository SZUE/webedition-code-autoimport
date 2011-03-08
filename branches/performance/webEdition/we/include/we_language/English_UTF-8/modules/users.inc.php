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
		'user_same' => "The owner cannot be deleted!",
		'grant_owners_ok' => "Owners have been successfully changed!",
		'grant_owners_notok' => "There was an error while changing the owners!",
		'grant_owners' => "Change owners",
		'grant_owners_expl' => "Change the owners of all files and directories which reside in the current directory to the owner setting above.",
		'make_def_ws' => "Default",
		'user_saved_ok' => "User '%s' has been successfully saved!",
		'group_saved_ok' => "Group '%s' has been successfully saved!",
		'alias_saved_ok' => "Alias '%s' has been successfully saved!",
		'user_saved_nok' => "User '%s' cannot be saved!",
		'nothing_to_save' => "Nothing to save!",
		'username_exists' => "User name '%s' already exists!",
		'username_empty' => "User name is empty!",
		'user_deleted' => "User '%s' is deleted!",
		'nothing_to_delete' => "Nothing to delete!",
		'delete_last_user' => "You are trying to delete the last user with administrator rights. Deleting would make the system unmanageable! Therefore deleting is not possible.",
		'modify_last_admin' => "There must be at least one administrator. You cannot change the rights of the last administrator.",
		'user_path_nok' => "The path is not correct!",
		'user_data' => "User data",
		'first_name' => "First name",
		'second_name' => "Last name",
		'username' => "User name",
		'password' => "Password",
		'workspace_specify' => "Specify workspace",
		'permissions' => "Permissions",
		'user_permissions' => "User permissions",
		'admin_permissions' => "Administrator permissions",
		'password_alert' => "Password must be at least 4 characters long.",
		'delete_alert_user' => "All user data for user '%s' will be deleted.\\nAre you sure that you wish to do this?",
		'delete_alert_alias' => "All alias data for alias '%s' will be deleted.\\nAre you sure that you wish to do this?",
		'delete_alert_group' => "All group data and group users of group '%s' will be deleted.\\nAre you sure that you wish to do this?",
		'created_by' => "Created by",
		'changed_by' => "Changed by:",
		'no_perms' => "You have no permission to use this option!",
		'publish_specify' => "User is allowed to publish.",
		'work_permissions' => "Working permissions",
		'control_permissions' => "Control permissions",
		'log_permissions' => "Login permissions",
		'file_locked' => array(
				FILE_TABLE => "The file '%s' is currently being used by '%s'!",
				TEMPLATES_TABLE => "The template '%s' is currently being used by '%s'!",
		),
		'acces_temp_denied' => "Access temporarily denied!",
		'description' => "Description",
		'group_data' => "Group data",
		'group_name' => "Group name",
		'group_member' => "Group membership",
		'group' => "Group",
		'address' => "Address",
		'houseno' => "House number/apartment",
		'state' => "State",
		'PLZ' => "Zip",
		'city' => "City",
		'country' => "Country",
		'tel_pre' => "Phone area code",
		'fax_pre' => "Fax area code",
		'telephone' => "Phone",
		'fax' => "Fax",
		'mobile' => "Mobile",
		'email' => "E-Mail",
		'general_data' => "General data",
		'workspace_documents' => "Workspace documents",
		'workspace_templates' => "Workspace templates",
		'workspace_objects' => "Workspace Objects",
		'save_changed_user' => "User has been changed.\\nDo you want to save your changes?",
		'not_able_to_save' => "Data has not been saved because of invalidity of data!",
		'cannot_save_used' => "Status cannot be changed because it is in processing!",
		'geaendert_von' => "Changed by",
		'geaendert_am' => "Changed at",
		'angelegt_am' => "Set up at",
		'angelegt_von' => "Set up by",
		'status' => "Status",
		'value' => " Value ",
		'gesperrt' => "restricted",
		'freigegeben' => "open",
		'gelöscht' => "deleted",
		'ohne' => "without",
		'user' => "User",
		'usertyp' => "User type",
		'search' => "Suche",
		'search_result' => "Ergebnis",
		'search_for' => "Suche nach",
		'inherit' => "Inherit permissions from parent group.",
		'inherit_ws' => "Inherit documents workspace from parent group.",
		'inherit_wst' => "Inherit templates workspace from parent group.",
		'inherit_wso' => "Inherit objects workspace from parent group",
		'organization' => "Organization",
		'give_org_name' => "Organization name",
		'can_not_create_org' => "The organisation cannot be created",
		'org_name_empty' => "Organization name is empty",
		'salutation' => "Salutation",
		'sucheleer' => "Search word is empty!",
		'alias_data' => "Alias data",
		'rights_and_workspaces' => "Permissions and<br>workspaces",
		'workspace_navigations' => "Workspave Navigation",
		'inherit_wsn' => "Inherit navigation workspaces from parent group",
		'workspace_newsletter' => "Workspace Newsletter",
		'inherit_wsnl' => "Inherit newsletter workspaces from parent group",
		'delete_user_same' => "You cannot delete your own account.",
		'delete_group_user_same' => "You cannot delete your own Group.",
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
if (defined("OBJECT_TABLE")) {
	$l_modules_users["file_locked"][OBJECT_TABLE] = "The class '%s' is currently being used by '%s'!";
	$l_modules_users["file_locked"][OBJECT_FILES_TABLE] = "The object '%s' is currently being used by '%s'!";
}
