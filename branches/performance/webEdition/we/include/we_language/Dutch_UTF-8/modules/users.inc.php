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
		'user_same' => "De eigenaar kan niet verwijderd worden!",
		'grant_owners_ok' => "Eigenaren zijn succesvol gewijzigd!",
		'grant_owners_notok' => "Er is een fout opgetreden tijdens het wijzigen van de eigenaars!",
		'grant_owners' => "Wijzig eigenaars",
		'grant_owners_expl' => "Wijzig de eigenaars van alle bestanden en directories die zich bevinden in de huidige directory naar de bovenstaande eigenaar instellingen.",
		'make_def_ws' => "Standaard",
		'user_saved_ok' => "Gebruiker '%s' is succesvol bewaard!",
		'group_saved_ok' => "Groep '%s' X is succesvol bewaard!",
		'alias_saved_ok' => "Alias '%s' is succesvol bewaard!",
		'user_saved_nok' => "Gebruiker '%s' kan niet bewaard worden!",
		'nothing_to_save' => "Er is niks om te bewaren!",
		'username_exists' => "Gebruikersnaam '%s' bestaat al!",
		'username_empty' => "Gebruikersnaam is leeg!",
		'user_deleted' => "Gebruiker '%s' is verwijderd!",
		'nothing_to_delete' => "Er is niks om te verwijderen!",
		'delete_last_user' => "U probeert de laatste gebruiker met admin rechten te verwijderen. Hierdoor wordt het systeem onaanpasbaar! Daarom kunt u de gebruiker niet verwijderen.",
		'modify_last_admin' => "Er moet minimaal één admin zijn.\\n U kunt de rechten van de laatste admin niet wijzigen.",
		'user_path_nok' => "Het pad is niet correct!",
		'user_data' => "gebruikersdata",
		'first_name' => "Voornaam",
		'second_name' => "Achternaam",
		'username' => "Gebruikersnaam",
		'password' => "Wachtwoord",
		'workspace_specify' => "Specificeer werkgebied",
		'permissions' => "Rechten",
		'user_permissions' => "Gebruikersrechten",
		'admin_permissions' => "Administrator rechten",
		'password_alert' => "Het wachtwoord moet minimaal 4 karakters bevatten.",
		'delete_alert_user' => "Alle gebruikersdata voor gebruiker '%s' wordt verwijderd.\\n Weet u zeker dat u dit wilt doen?",
		'delete_alert_alias' => "Alle alias data voor alias '%s' wordt verwijderd.\\n Weet u zeker dat u dit wilt doen?",
		'delete_alert_group' => "Alle groep data en gebruikers van groep '%s' worden verwijderd. Weet u zeker dat u dit wilt doen?",
		'created_by' => "Aangemaakt door",
		'changed_by' => "Gewijzigd door:",
		'no_perms' => "U bent niet bevoegd om deze optie te gebruiken!",
		'publish_specify' => "Gebruiker is bevoegd te publiceren.",
		'work_permissions' => "Werk rechten",
		'control_permissions' => "Controle rechten",
		'log_permissions' => "Inlog rechten",
		'file_locked' => array(
				FILE_TABLE => "Het bestand '%s' wordt momenteel gebruikt door '%s'!",
				TEMPLATES_TABLE => "Het sjabloon '%s' wordt momenteel gebruikt door '%s'!",
		),
		'acces_temp_denied' => "Toegang tijdelijk geweigerd!",
		'description' => "Omschrijving",
		'group_data' => "Groep data",
		'group_name' => "Groepsnaam",
		'group_member' => "Groeps abonnement",
		'group' => "Groep",
		'address' => "Adres",
		'houseno' => "Huisnummer/appartement",
		'state' => "Provincie",
		'PLZ' => "Postcode",
		'city' => "Stad",
		'country' => "land",
		'tel_pre' => "Telefoon kerncijfer",
		'fax_pre' => "Fax kerncijfer",
		'telephone' => "Telefoon",
		'fax' => "Fax", // TRANSLATE
		'mobile' => "Mobiel",
		'email' => "Email",
		'general_data' => "Algemene data",
		'workspace_documents' => "Werkgebied documenten",
		'workspace_templates' => "Werkgebied sjablonen",
		'workspace_objects' => "Werkgebied Objecten",
		'save_changed_user' => "Gebruiker is gewijzigd.\\nWilt u de wijziging bewaren?",
		'not_able_to_save' => "Data is niet bewaard omdat deze niet geldig is!",
		'cannot_save_used' => "Status kan niet gewijzigd worden omdat deze bezig is!",
		'geaendert_von' => "Gewijzigd door",
		'geaendert_am' => "Gewijzigd bij",
		'angelegt_am' => "Opgezet bij",
		'angelegt_von' => "Opgezet door",
		'status' => "Status", // TRANSLATE
		'value' => " Waarde ",
		'gesperrt' => "Beperkt",
		'freigegeben' => "open", // TRANSLATE
		'gelöscht' => "verwijder",
		'ohne' => "zonder",
		'user' => "Gebruiker",
		'usertyp' => "Type gebruiker",
		'search' => "Search",
		'search_result' => "Ergebnis", // TRANSLATE
		'search_for' => "Search for",
		'inherit' => "Verkrijg rechten van hoofd groep.",
		'inherit_ws' => "Verkrijg documenten werkgebied van hoofd groep.",
		'inherit_wst' => "Verkrijg sjabloon werkgebied van hoofd groep.",
		'inherit_wso' => "Verkrijg objecten werkgebied van hoofd groep",
		'organization' => "Organisatie",
		'give_org_name' => "Naam organisatie",
		'can_not_create_org' => "De organisatie kan niet aangemaakt worden",
		'org_name_empty' => "Naam organisatie is leeg",
		'salutation' => "Aanhef",
		'sucheleer' => "Zoekterm is leeg!",
		'alias_data' => "Alias gegevens",
		'rights_and_workspaces' => "Rechten en<br>werkgebieden",
		'workspace_navigations' => "Workspave Navigatie",
		'inherit_wsn' => "Neem navigatie workspaces over van hoofdgroep",
		'workspace_newsletter' => "Workspace nieuwsbrief",
		'inherit_wsnl' => "Neem nieuwsbrief workspaces over van hoofdgroep",
		'delete_user_same' => "U kunt niet uw eigen account verwijderen.",
		'delete_group_user_same' => "U kunt niet uw eigen groep verwijderen.",
		'login_denied' => "Login denied", // TRANSLATE
		'workspaceFieldError' => "FOUT: Ongeldige workspace invoer!",
		'noGroupError' => "FOUT: Ongeldige invoer in field groep!",
		'CreatorID' => "Created by: ", // TRANSLATE
		'CreateDate' => "Created at: ", // TRANSLATE
		'ModifierID' => "Modified by: ", // TRANSLATE
		'ModifyDate' => "Modified at: ", // TRANSLATE
		'lastPing' => "Last Ping: ", // TRANSLATE
		'lostID' => "ID: ", // TRANSLATE
		'lostID2' => " (deleted)", // TRANSLATE
);
if (defined("OBJECT_TABLE")) {
	$l_modules_users[OBJECT_TABLE] = "De class '%s' wordt momenteel gebruikt door '%s'!";
	$l_modules_users[OBJECT_FILES_TABLE] = "Het object '%s' wordt momenteel gebruikt door '%s'!";
}
