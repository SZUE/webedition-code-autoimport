<?php

/**
 * webEdition CMS
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
$l_users["user_same"] = "The owner cannot be deleted!";// TRANSLATE
$l_users["grant_owners_ok"] = "Owners have been successfully changed!";// TRANSLATE
$l_users["grant_owners_notok"] = "There was an error while changing the owners!";// TRANSLATE
$l_users["grant_owners"] = "Change owners";// TRANSLATE
$l_users["grant_owners_expl"] = "Change the owners of all files and directories which reside in the current directory to the owner setting above.";// TRANSLATE
$l_users["make_def_ws"] = "Default";// TRANSLATE
$l_users["user_saved_ok"] = "User '%s' has been successfully saved!";// TRANSLATE
$l_users["group_saved_ok"] = "Group '%s' has been successfully saved!";// TRANSLATE
$l_users["alias_saved_ok"] = "Alias '%s' has been successfully saved!";// TRANSLATE
$l_users["user_saved_nok"] = "User '%s' cannot be saved!";// TRANSLATE
$l_users["nothing_to_save"] = "Nothing to save!";// TRANSLATE
$l_users["username_exists"] = "User name '%s' already exists!";// TRANSLATE
$l_users["username_empty"] = "User name is empty!";// TRANSLATE
$l_users["user_deleted"] = "User '%s' is deleted!";// TRANSLATE
$l_users["nothing_to_delete"] = "Nothing to delete!";// TRANSLATE
$l_users["delete_last_user"] = "You are trying to delete the last user with administrator rights. Deleting would make the system unmanageable! Therefore deleting is not possible.";// TRANSLATE
$l_users["modify_last_admin"] = "There must be at least one administrator. You cannot change the rights of the last administrator.";// TRANSLATE
$l_users["user_path_nok"] = "The path is not correct!";// TRANSLATE
$l_users["user_data"] = "User data";// TRANSLATE
$l_users["first_name"] = "First name";// TRANSLATE
$l_users["second_name"] = "Last name";// TRANSLATE
$l_users["username"] = "User name";// TRANSLATE
$l_users["password"] = "Password";// TRANSLATE
$l_users["workspace_specify"] = "Specify workspace";// TRANSLATE
$l_users["permissions"] = "Permissions";// TRANSLATE
$l_users["user_permissions"] = "User permissions";// TRANSLATE
$l_users["admin_permissions"] = "Administrator permissions";// TRANSLATE
$l_users["password_alert"] = "Password must be at least 4 characters long.";// TRANSLATE
$l_users["delete_alert_user"] = "All user data for user '%s' will be deleted.\\nAre you sure that you wish to do this?";// TRANSLATE
$l_users["delete_alert_alias"] = "All alias data for alias '%s' will be deleted.\\nAre you sure that you wish to do this?";// TRANSLATE
$l_users["delete_alert_group"] = "All group data and group users of group '%s' will be deleted.\\nAre you sure that you wish to do this?";// TRANSLATE
$l_users["created_by"] = "Created by";// TRANSLATE
$l_users["changed_by"] = "Changed by:";// TRANSLATE
$l_users["no_perms"] = "You have no permission to use this option!";// TRANSLATE
$l_users["publish_specify"] = "User is allowed to publish.";// TRANSLATE
$l_users["work_permissions"] = "Working permissions";// TRANSLATE
$l_users["control_permissions"] = "Control permissions";// TRANSLATE
$l_users["log_permissions"] = "Login permissions";// TRANSLATE
$l_users["file_locked"][FILE_TABLE] = "The file '%s' is currently being used by '%s'!";// TRANSLATE
$l_users["file_locked"][TEMPLATES_TABLE] = "The template '%s' is currently being used by '%s'!";// TRANSLATE
if(defined("OBJECT_TABLE")){
	$l_users["file_locked"][OBJECT_TABLE] = "The class '%s' is currently being used by '%s'!";// TRANSLATE
	$l_users["file_locked"][OBJECT_FILES_TABLE] = "The object '%s' is currently being used by '%s'!";// TRANSLATE
}
$l_users["acces_temp_denied"] = "Access temporarily denied!";// TRANSLATE
$l_users["description"] = "Description";// TRANSLATE
$l_users["group_data"] = "Group data";// TRANSLATE
$l_users["group_name"] = "Group name";// TRANSLATE
$l_users["group_member"] = "Group membership";// TRANSLATE
$l_users["group"] = "Group";// TRANSLATE
$l_users["address"] = "Address";// TRANSLATE
$l_users["houseno"] = "House number/apartment";// TRANSLATE
$l_users["state"] = "State";// TRANSLATE
$l_users["PLZ"] = "Zip";// TRANSLATE
$l_users["city"] = "City";// TRANSLATE
$l_users["country"] = "Country";// TRANSLATE
$l_users["tel_pre"] = "Phone area code";// TRANSLATE
$l_users["fax_pre"] = "Fax area code";// TRANSLATE
$l_users["telephone"] = "Phone";// TRANSLATE
$l_users["fax"] = "Fax";// TRANSLATE
$l_users["mobile"] = "Mobile";// TRANSLATE
$l_users["email"] = "E-Mail";// TRANSLATE
$l_users["general_data"] = "General data";// TRANSLATE
$l_users["workspace_documents"] = "Workspace documents";// TRANSLATE
$l_users["workspace_templates"] = "Workspace templates";// TRANSLATE
$l_users["workspace_objects"] = "Workspace Objects";// TRANSLATE
$l_users["save_changed_user"] = "User has been changed.\\nDo you want to save your changes?";// TRANSLATE
$l_users["not_able_to_save"] = "Data has not been saved because of invalidity of data!";// TRANSLATE
$l_users["cannot_save_used"] = "Status cannot be changed because it is in processing!";// TRANSLATE
$l_users["geaendert_von"] = "Changed by";// TRANSLATE
$l_users["geaendert_am"] = "Changed at";// TRANSLATE
$l_users["angelegt_am"] = "Set up at";// TRANSLATE
$l_users["angelegt_von"] = "Set up by";// TRANSLATE
$l_users["status"] = "Status";// TRANSLATE
$l_users["value"] = " Value ";// TRANSLATE
$l_users["gesperrt"] = "restricted";// TRANSLATE
$l_users["freigegeben"] = "open";// TRANSLATE
$l_users["gelöscht"] = "deleted";// TRANSLATE
$l_users["ohne"] = "without";// TRANSLATE
$l_users["user"] = "User";// TRANSLATE
$l_users["usertyp"] = "User type";// TRANSLATE
$l_users["search"] = "Suche";// TRANSLATE
$l_users["search_result"] = "Ergebnis";// TRANSLATE
$l_users["search_for"] = "Suche nach";// TRANSLATE
$l_users["inherit"] = "Inherit permissions from parent group.";// TRANSLATE
$l_users["inherit_ws"] = "Inherit documents workspace from parent group.";// TRANSLATE
$l_users["inherit_wst"] = "Inherit templates workspace from parent group.";// TRANSLATE
$l_users["inherit_wso"] = "Inherit objects workspace from parent group";// TRANSLATE
$l_users["organization"] = "Organization";// TRANSLATE
$l_users["give_org_name"] = "Organization name";// TRANSLATE
$l_users["can_not_create_org"] = "The organisation cannot be created";// TRANSLATE
$l_users["org_name_empty"] = "Organization name is empty";// TRANSLATE
$l_users["salutation"] = "Salutation";// TRANSLATE
$l_users["sucheleer"] = "Search word is empty!";// TRANSLATE
$l_users["alias_data"] = "Alias data";// TRANSLATE
$l_users["rights_and_workspaces"] = "Permissions and<br>workspaces";// TRANSLATE
$l_users["workspace_navigations"] = "Workspave Navigation";// TRANSLATE
$l_users["inherit_wsn"] = "Inherit navigation workspaces from parent group";// TRANSLATE
$l_users["workspace_newsletter"] = "Workspace Newsletter";// TRANSLATE
$l_users["inherit_wsnl"] = "Inherit newsletter workspaces from parent group";// TRANSLATE

$l_users["delete_user_same"] = "You cannot delete your own account.";// TRANSLATE
$l_users["delete_group_user_same"] = "You cannot delete your own Group.";// TRANSLATE

$l_users["login_denied"] = "Login denied";// TRANSLATE
$l_users["workspaceFieldError"] = "ERROR: Invalid workspace entry!";// TRANSLATE
$l_users["noGroupError"] = "Error: Invalid entry in field group!";// TRANSLATE

?>