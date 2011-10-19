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
		'grant_owners_ok' => "L'utilisateur a été transmis avec succès!",
		'grant_owners_notok' => "Une erreur s'est produit en transmettant l'utilisateur!",
		'grant_owners' => "Transmettre l'utilisateur",
		'grant_owners_expl' => "Transmettez les propriétaires de tous les fichiers et répertoires, qui sont dans le répertoire actuel au propriétaire affiché en haut",
		'make_def_ws' => "Standard",
		'user_saved_ok' => "L'utilisateur '%s' a été enregistré avec succès",
		'group_saved_ok' => "Le groupe '%s' a été enregistré avec succès",
		'alias_saved_ok' => "Le pseudonym '%s' a été enregistré avec succès",
		'user_saved_nok' => "L'utilisateur '%s' ne peut pas être enregistré!",
		'nothing_to_save' => "Rien à enregister!",
		'username_exists' => "Le nom d'utilisateur '%s' existe déjà!",
		'username_empty' => "Le nom d'utilisateur n'est pas rempli!",
		'user_deleted' => "L'utilisateur '%s' a été supprimé avec succès!",
		'nothing_to_delete' => "Rien à supprimer!",
		'delete_last_user' => "Pour l'administration au moins un administrateur est nécessaire.\\nVous ne pouvez pas supprimer le dernier administrateur.",
		'modify_last_admin' => "Pour l'administration au moins un administrateur est nécessaire.\\nVous ne pouvez pas changer les droits du dernier administrateur.",
		'user_path_nok' => "Le chemin n'est pas correct!",
		'user_data' => "Donnée de l'utilisateur",
		'first_name' => "Prénom",
		'second_name' => "Nom",
		'username' => "Nom d'Utilisateur",
		'password' => "Mot de Passe",
		'workspace_specify' => "Définir un éspace de travail",
		'permissions' => "Droits",
		'user_permissions' => "Redacteur",
		'admin_permissions' => "Administrateur",
		'password_alert' => "Le mot de passe doit avoir au moins un longeur de 4 chiffres",
		'delete_alert_user' => "Tous les données du nom d'utilisateur '%s' seront supprimé.\\n Êtes-vous sûr?",
		'delete_alert_alias' => "Tous les données du pseudonyme '%s' seront supprimé.\\n Êtes-vous sûr?",
		'delete_alert_group' => "Tous les données de groupe et utilisateurs de groupe, du groupe '%s' seront supprimé.\\n Êtes-vous sûr?",
		'created_by' => "Créé par",
		'changed_by' => "Modifié par",
		'no_perms' => "Vous n'êtes pas authorisé, d'effectuer cette option!",
		'publish_specify' => "L'utilisateur a le droit de publier",
		'work_permissions' => "Droits de travail",
		'control_permissions' => "Droits de contrôle",
		'log_permissions' => "Droits pour le journal",
		'acces_temp_denied' => "L'accès n'est pas possible en ce moment",
		'description' => "Déscription",
		'group_data' => "Données de Groupe",
		'group_name' => "Nom du Groupe",
		'group_member' => "Affiliation au groupe",
		'group' => "Groupe",
		'address' => "Adresse",
		'houseno' => "Numéro",
		'state' => "Land",
		'PLZ' => "Code postal",
		'city' => "Ville",
		'country' => "Pays",
		'tel_pre' => "Prefix du téléphone",
		'fax_pre' => "Prefix du Fax",
		'telephone' => "Téléphone",
		'fax' => "Fax", // TRANSLATE
		'mobile' => "Portable",
		'email' => "E-Mail", // TRANSLATE
		'general_data' => "Données générales",
		'workspace_documents' => "Éspace de travail pour les documents",
		'workspace_templates' => "Éspace de travail pour les modèles",
		'workspace_objects' => "Workspace Objects", // TRANSLATE
		'save_changed_user' => "L'utilisateur a été modifié.\\nVoulez-vous enregistrer les modifications?",
		'not_able_to_save' => " Les données n'ont pas été enregistrées, parce qu'elles sont invalides!",
		'cannot_save_used' => " L'État ne peut pas être changé, parce que l'utilisateur est utilisér en ce moment!",
		'geaendert_von' => "Modifié  par",
		'geaendert_am' => "Modifié  le",
		'angelegt_am' => "Créé  le",
		'angelegt_von' => "Créé  par",
		'status' => "État",
		'value' => " Valeur ",
		'gesperrt' => "bloqué",
		'freigegeben' => "débloqué",
		'gelöscht' => "supprimé",
		'ohne' => "Sans",
		'user' => "Utilisateur",
		'usertyp' => "Type d'utilisateur",
		'search' => "Suche", // TRANSLATE
		'search_result' => "Ergebnis", // TRANSLATE
		'search_for' => "Suche nach", // TRANSLATE
		'inherit' => "Adopter les droits du goupe parental",
		'inherit_ws' => "Adopter l'éspace de travail du goupe parental",
		'inherit_wst' => "Adopter l'éspace de travail des modèles du goupe parentale",
		'inherit_wso' => "Inherit objects workspace from parent group", // TRANSLATE
		'organization' => "Organisation",
		'give_org_name' => "Le nom de l'Organisation",
		'can_not_create_org' => "L'Organisation ne peut pas être créé",
		'org_name_empty' => "Le nom de l'Organisation est vide",
		'salutation' => "Titre",
		'sucheleer' => "Il manque un termes de recherche.",
		'alias_data' => "Donnée du pseudonyme",
		'rights_and_workspaces' => "Droit et<br>éspaces de travail",
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
