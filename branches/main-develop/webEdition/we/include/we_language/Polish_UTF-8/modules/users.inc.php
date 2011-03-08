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
		'user_same' => "The owner cannot be deleted!", // TRANSLATE
		'grant_owners_ok' => "Udało się przenieść dane właściciela!",
		'grant_owners_notok' => "Wystąpił błąd przy przenoszeniu właściciela!",
		'grant_owners' => "Przenieś właściciela",
		'grant_owners_expl' => "Przenieś ustawionego wyżej właściciela i wszystkich użytkowników na wszystkie pliki i katalogi, które znajdują się w tym katalogu.",
		'make_def_ws' => "Standard",
		'user_saved_ok' => "Zapisano użytkownika '%s'",
		'group_saved_ok' => "Zapisano grupę '%s'",
		'alias_saved_ok' => "Zapisano alias '%s'",
		'user_saved_nok' => "Nie udało się zapisać użytkownika '%s'!",
		'nothing_to_save' => "Brak obiektów do zapisania!",
		'username_exists' => "Nazwa użytkownika już istnieje '%s'!",
		'username_empty' => "Nie wypełniono nazwy użytkownika!",
		'user_deleted' => "Usunięto użytkownika '%s'!",
		'nothing_to_delete' => "Brak obiektów do usunięcia!",
		'delete_last_user' => "Do zarządzania jest potrzebny przynajmniej administrator.\\nNie można usunąć ostatniego administratora.",
		'modify_last_admin' => "Do zarządzania jest potrzebny przynajmniej administrator.\\n Nie można zmienić uprawnień ostatniego administratora.",
		'user_path_nok' => "Nieprawidłowa ścieżka!",
		'user_data' => "Dane użytkowników",
		'first_name' => "Imię",
		'second_name' => "Nazwisko",
		'username' => "Nazwa użytkownika",
		'password' => "Hasło",
		'workspace_specify' => "Wyszczególnij obszar roboczy",
		'permissions' => "Uprawnienia",
		'user_permissions' => "Redaktor",
		'admin_permissions' => "Administrator",
		'password_alert' => "Hasło musi się składać z conajmniej 4 znaków",
		'delete_alert_user' => "Usunięcie wszystkich danych użytkownika '%s'.\\n Na pewno?",
		'delete_alert_alias' => "Usunięcie wszystkich danych aliasu dla aliasu '%s'.\\n Na pewno?",
		'delete_alert_group' => "Usunięcie wszystkich danych grupy i użytkowników grupy dla grupy '%s'.\\nNa pewno?",
		'created_by' => "Sporządził",
		'changed_by' => "Zmienił",
		'no_perms' => "Nie masz uprawnień do korzystania z tej opcji!",
		'publish_specify' => "Użytkownik może publikować",
		'work_permissions' => "Uprawnienia robocze",
		'control_permissions' => "Uprawnienia kontrolne",
		'log_permissions' => "Prawo do logowania",
		'file_locked' => array(
				FILE_TABLE => "Plik '%s' jest właśnie edytowany przez użytkownika '%s'!",
				TEMPLATES_TABLE => "Szablon '%s' jest właśnie edytowany przez użytkownika '%s'!",
		),
		'acces_temp_denied' => "Dostęp jest obecnie niemożliwy",
		'description' => "Opis",
		'group_data' => "Dane grupy",
		'group_name' => "Nazwa grupy",
		'group_member' => "Przynależność do grup",
		'group' => "Grupa",
		'address' => "Adres",
		'houseno' => "Numer domu",
		'state' => "Województwo",
		'PLZ' => "Kod pocztowy",
		'city' => "Miasto",
		'country' => "Kraj",
		'tel_pre' => "Nr kierunkowy telefonu",
		'fax_pre' => "Nr kierunkowy faksu",
		'telephone' => "Telefon",
		'fax' => "Faks",
		'mobile' => "Tel. komórkowy",
		'email' => "e-mail",
		'general_data' => "Dane ogólne",
		'workspace_documents' => "Obszar roboczy dokumentów",
		'workspace_templates' => "Obszar roboczy szablonów",
		'workspace_objects' => "Workspace Objects", // TRANSLATE
		'save_changed_user' => "Zmieniono użytkownika.\\nCzy chcesz zapisać zmiany?",
		'not_able_to_save' => " Nie zapisano danych, ponieważ są nieprawidłowe!",
		'cannot_save_used' => " Nie można zmienić statusu - trwa edycja!",
		'geaendert_von' => "Zmienił",
		'geaendert_am' => "Zmieniono dn.",
		'angelegt_am' => "Wprowadzono dn.",
		'angelegt_von' => "Wprowadził",
		'status' => "Status", // TRANSLATE
		'value' => " Wartość ",
		'gesperrt' => "zablokowano",
		'freigegeben' => "Zatwierdzone",
		'gelöscht' => "Usunięto",
		'ohne' => "Brak",
		'user' => "Użytkownik",
		'usertyp' => "Typ użytkownika",
		'search' => "Suche", // TRANSLATE
		'search_result' => "Ergebnis", // TRANSLATE
		'search_for' => "Suche nach", // TRANSLATE
		'inherit' => "Przejęcie uprawnień grupy nadrzędnej",
		'inherit_ws' => "Przejęcie obszaru roboczego grupy nadrzędnej",
		'inherit_wst' => "Przejęcie obszaru roboczego szablonów z grupy nadrzędnej",
		'inherit_wso' => "Inherit objects workspace from parent group", // TRANSLATE
		'organization' => "Organizacja",
		'give_org_name' => "Nazwa organizacji",
		'can_not_create_org' => "Nie można utworzyć organizacji",
		'org_name_empty' => "Nazwa organizacji jest pusta",
		'salutation' => "Tytuł",
		'sucheleer' => "Nie podano słowa do wyszukania.",
		'alias_data' => "Dane aliasów",
		'rights_and_workspaces' => "Uprawnienia i <br>obszary robocze",
		'workspace_navigations' => "Workspave Navigation", // TRANSLATE
		'inherit_wsn' => "Inherit navigation workspaces from parent group", // TRANSLATE
		'workspace_newsletter' => "Workspace Newsletter", // TRANSLATE
		'inherit_wsnl' => "Inherit newsletter workspaces from parent group", // TRANSLATE

		'delete_user_same' => "Sie können nicht Ihr eigenes Konto löschen.", // TRANSLATE
		'delete_group_user_same' => "Sie können nicht Ihre eigene Gruppe löschen.", // TRANSLATE

		'login_denied' => "Login denied", // TRANSLATE
		'workspaceFieldError' => "ERROR: Invalid workspace entry!", // TRANSLATE
		'noGroupError' => "Error: Invalid entry in field group!", // TRANSLATE
		'CreatorID' => "Created by: ", // TRANSLATE
		'CreateDate' => "Created at: ", // TRANSLATE
		'ModifierID' => "Modified by: ", // TRANSLATE
		'ModifyDate' => "Modified at: ", // TRANSLATE
		'lastPing' => "Last Ping: ", // TRANSLATE
		'lostID' => "ID: ", // TRANSLATE
		'lostID2' => " (deleted)", // TRANSLATE
);

if (defined("OBJECT_TABLE")) {
	$l_modules_users["file_locked"][OBJECT_TABLE] = "Klasa '%s' jest właśnie edytowana przez użytkownika '%s'!";
	$l_modules_users["file_locked"][OBJECT_FILES_TABLE] = "Obiekt '%s' jest właśnie edytowany przez użytkownika '%s'!";
}
