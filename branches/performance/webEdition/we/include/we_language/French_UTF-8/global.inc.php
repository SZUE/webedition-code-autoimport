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
 * Language file: global.inc.php
 * Provides language strings.
 * Language: English
 */
$l_global = array(
		'new_link' => "New Link", // TRANSLATE // It is important to use the GLOBALS ARRAY because in linklists, the file is included in a function.
		'load_menu_info' => "Chargement des données en cours!<br>Cela peut nécessiter du temps si le menu a beaucoup d'entrées ...",
		'text' => "Texte",
		'yes' => "oui",
		'no' => "non",
		'checked' => "Active",
		'max_file_size' => "Taille de fichier max. (en Octet)",
		'default' => "Préréglage",
		'values' => "Valeurs",
		'name' => "Nom",
		'type' => "Type", // TRANSLATE
		'attributes' => "Attributs",
		'formmailerror' => "Le formulaire n'a pas été transmis pour la raison suivant:",
		'email_notallfields' => "Vous n'avez pas rempli tous les champs!",
		'email_ban' => "Vous n'êtes pas autorisé de utiliser ce script!",
		'email_recipient_invalid' => "L'adresse destinataire est invalide!",
		'email_no_recipient' => "L'adresse destinataire n'existe pas!",
		'email_invalid' => "Votre <b>Adresse E-Mail</b> n'est pas valide!",
		'captcha_invalid' => "The entered security code is wrong!", // TRANSLATE
		'question' => "Question", // TRANSLATE
		'warning' => "Avertissement",
		'we_alert' => "Cette fonction n'est pas disponible dans la Version Démo de webEdition!",
		'index_table' => "Tableau Index",
		'cannotconnect' => "Il n'était pas possible de connecter au serveur de webEdition!",
		'recipients' => "Destinataire-Formmail",
		'recipients_txt' => "Insérer ici tous les adresses e-mail, aux quelles des formulaires avec la fonction-formmail  (&lt;we:form type=\"formmail\" ..&gt;) sont être envoyés.<br><br>Si aucun d'adresse e-mail est saisie ici, il n'est pas possible d'envoyer des formulaires avec la fonction-Formmail!",
		'std_mailtext_newObj' => "Un nouveau object %s a été créé dans la classe %s!",
		'std_subject_newObj' => "Nouveau Object",
		'std_subject_newDoc' => "Nouveau Document",
		'std_mailtext_newDoc' => "Un nouveau Object %s a été créé!",
		'std_subject_delObj' => "Object supprimé",
		'std_mailtext_delObj' => "L'object %s a été supprimé!",
		'std_subject_delDoc' => "Document supprimé",
		'std_mailtext_delDoc' => "Le document %s a été supprimé!",
		'we_make_same' => array(
				'text/html' => "Après l'enregistrement nouveau Site",
				'text/webedition' => "Après l'enregistrement nouveau Site",
				'objectFile' => "New object after saving",
		),
		'no_entries' => "Aucune Entrée!",
		'save_temporaryTable' => "Récrire les documents de travail temporaires",
		'save_mainTable' => "Récrire le tableau principal de la base de donnée",
		'add_workspace' => "Ajouter un éspace de travail",
		'folder_not_editable' => "Ce répertoire ne doit pas être choisi!",
		'modules' => "Modules", // TRANSLATE
		'modules_and_tools' => "Modules and Tools", // TRANSLATE
		'center' => "Centrer",
		'jswin' => "Fenêtre Popup",
		'open' => "Ouvrir",
		'posx' => "Position x",
		'posy' => "Position y",
		'status' => "État",
		'scrollbars' => "Barres de défilement",
		'menubar' => "Barre de menue",
		'toolbar' => "Toolbar", // TRANSLATE
		'resizable' => "Taille modifiable",
		'location' => "Lieu",
		'title' => "Titre",
		'description' => "Déscription",
		'required_field' => "Champ obligatoire",
		'from' => "de",
		'to' => "à",
		'search' => "Search", // TRANSLATE
		'in' => "in", // TRANSLATE
		'we_rebuild_at_save' => "Rebuild automatique",
		'we_publish_at_save' => "Publier en enregistrant",
		'we_new_doc_after_save' => "New Document after saving", // TRANSLATE
		'we_new_folder_after_save' => "New folder after saving", // TRANSLATE
		'we_new_entry_after_save' => "New entry after saving", // TRANSLATE
		'wrapcheck' => "Passage à ligne",
		'static_docs' => "Documents statiques",
		'save_templates_before' => "Enregistrer les modèles avant",
		'specify_docs' => "Documents avec les critères suivant",
		'object_docs' => "Tous les Objects",
		'all_docs' => "Tous les Documents",
		'ask_for_editor' => "dabord demander quel editeur doit être utilisé",
		'cockpit' => "Cockpit", // TRANSLATE
		'introduction' => "Introduction", // TRANSLATE
		'doctypes' => "Type-de-Documents",
		'content' => "Contenu",
		'site_not_exist' => "Ce site n'existe pas!",
		'site_not_published' => "Ce site ne pas encore publié!",
		'required' => "Entrée nécéssaire",
		'all_rights_reserved' => "Tous droits réservés",
		'width' => "Largeur",
		'height' => "Hauteur",
		'new_username' => "Nouveau nom d'utilisateur",
		'username' => "Nom d'Utilisateur",
		'password' => "Mot de Passe",
		'documents' => "Documents", // TRANSLATE
		'templates' => "Modèles",
		'objects' => "Objects", // TRANSLATE
		'licensed_to' => "Licencié",
		'left' => "gauche",
		'right' => "droite",
		'top' => "en haut",
		'bottom' => "en bas",
		'topleft' => "en haut à gauche",
		'topright' => "en haut à droite",
		'bottomleft' => "en bas à gauche",
		'bottomright' => "en bas à droite",
		'true' => "oui",
		'false' => "non",
		'showall' => "afficher tous",
		'noborder' => "sans bordure",
		'border' => "bordure",
		'align' => "alignement",
		'hspace' => "distance horiz.",
		'vspace' => "distance vert.",
		'exactfit' => "exactfit",
		'select_color' => "Choisir une couleur",
		'changeUsername' => "Changer le nom d'utilisateur",
		'changePass' => "Changer le mot de passe",
		'oldPass' => "Ancien mot de passe",
		'newPass' => "Nouveau mot de passe",
		'newPass2' => "Nouveau mot de passe répétition",
		'pass_not_confirmed' => "La répétition du nouveau mot de passe ne correspond pas au nouveau mot de passe!",
		'pass_not_match' => "L'ancien mot de passe n'est pas correcte!",
		'passwd_not_match' => "Le mot de passe n'est pas correcte!",
		'pass_to_short' => "Le mot de passe doit au moins avoir une longeur de 4 chiffres!",
		'pass_changed' => "Le mot de passe a été changé avec succès!",
		'pass_wrong_chars' => "Le mot de passe doit seulement contenir des lettres (a-z et A-Z) et des chiffres (0 à 9)",
		'username_wrong_chars' => "Username may only contain alpha-numeric characters (a-z, A-Z and 0-9) and '.', '_' or '-'!", // TRANSLATE
		'all' => "Tous",
		'selected' => "Choisi",
		'username_to_short' => "Le nom d'utilisateur doit au moins avoir une longeur de 4 chiffres!",
		'username_changed' => "Le nom d'utilisateur à été changé avec succès!",
		'published' => "Publié",
		'help_welcome' => "Bienvenue dans le system d'aide de webEdition",
		'edit_file' => "Éditer le fichier",
		'docs_saved' => "Documents enregistrés avec succès!",
		'preview' => "Prévision",
		'close' => "Fermer la fenêtre",
		'loginok' => "<strong>Autentifacation ok!</strong><br>webEdition devrait s'ouvrir dans une nouvelle fenêtre.<br>Si ce n'est pas le cas, vous avez probablement bloquez les popups dans votre navigateur!",
		'apple' => "&#x2318;", // TRANSLATE
		'shift' => "SHIFT", // TRANSLATE
		'ctrl' => "CTRL", // TRANSLATE
		'required_fields' => "Champs obligatoires",
		'no_file_uploaded' => "<p class=\"defaultfont\">Aucun document à été téléchargé.</p>",
		'openCloseBox' => "Ouvrir/Fermer",
		'rebuild' => "Rebuild", // TRANSLATE
		'unlocking_document' => "débloque le Document",
		'variant_field' => "Champs de variantes",
		'redirect_to_login_failed' => "Please press the following link, if you are not redirected within the next 30 seconds ", // TRANSLATE
		'redirect_to_login_name' => "webEdition login", // TRANSLATE
		'untitled' => "Untitled", // TRANSLATE
		'no_document_opened' => "There is no document opened!", // TRANSLATE
		'credits_team' => "webEdition Team", // TRANSLATE
		'developed_further_by' => "developed further by", // TRANSLATE
		'with' => "with the", // TRANSLATE
		'credits_translators' => "Translations", // TRANSLATE
		'credits_thanks' => "Thanks to", // TRANSLATE
		'unable_to_call_ping' => "Connection to server is lost - RPC: Ping!", // TRANSLATE
		'unable_to_call_setpagenr' => "Connection to server is lost - RPC: setPageNr!", // TRANSLATE
		'nightly-build' => "nightly Build", // TRANSLATE
		'alpha' => "Alpha", // TRANSLATE
		'beta' => "Beta", // TRANSLATE
		'rc' => "RC", // TRANSLATE
		'preview' => "preview", // TRANSLATE
		'release' => "official release", // TRANSLATE

		'categorys' => "Categories", // TRANSLATE
		'navigation' => "Navigation", // TRANSLATE
);
