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
$l_modules_voting = array(
		'no_perms' => 'You do not have permission to use this option.',
		'delete_alert' => 'Supprimer le vote/groupe actuel.\\n Êtes-vous sûr?',
		'result_delete_alert' => 'Delete the current voting results.\\nAre you sure?', // TRANSLATE
		'nothing_to_delete' => 'Rien à supprimer!',
		'nothing_to_save' => 'Rien à enregistrer',
		'we_filename_notValid' => 'Le nom d&rsquo;utilisateur n&rsquo;est pas valide!\\nPermit sont les signe alpha-numerique, majuscule et minuscule, autant que le soulignage, le trait d&rsquo;union, le point et l&rsquo;éspace (a-z, A-Z, 0-9, _, -, ., )',
		'menu_new' => 'Nouveau',
		'menu_save' => 'Entregistrer',
		'menu_delete' => 'Supprimer',
		'menu_exit' => 'Fermer',
		'menu_info' => 'Info', // TRANSLATE
		'menu_help' => 'Aide',
		'headline' => 'Prénom et Noms',
		'headline_name' => 'Nom',
		'headline_publish_date' => 'Date de Création',
		'headline_data' => 'Données d&rsquo;enquête',
		'publish_date' => 'Date', // TRANSLATE
		'publish_format' => 'Format', // TRANSLATE

		'published_on' => 'Publié le',
		'total_voting' => 'Vote total',
		'reset_scores' => 'Remettre le score',
		'inquiry_question' => 'Question', // TRANSLATE
		'inquiry_answers' => 'Réponse',
		'question_empty' => 'La question est vide, s&rsquo;il vous plaît entrez une!',
		'answer_empty' => 'Une ou plusieurs réponses sont vide, s&rsquo;il vous plaît entrez les réponses!',
		'invalid_score' => 'La valeur du score doît être un numéro, essayez de nouveau s&rsquo;il vous plaît!',
		'headline_revote' => 'Contôle de revote',
		'headline_help' => 'Aide',
		'inquiry' => 'Enquête',
		'browser_vote' => 'Un navigateur ne peut pas voter de nouveau avant',
		'one_hour' => '1 heure',
		'feethteen_minutes' => '15 min.', // TRANSLATE
		'thirthty_minutes' => '30 min.', // TRANSLATE
		'one_day' => '1 jour',
		'never' => '--jamais--',
		'always' => '--toujours--',
		'cookie_method' => 'Par méthode Cookie',
		'ip_method' => 'Par méthode IP ',
		'time_after_voting_again' => 'Temps avant de voter de nouveau',
		'cookie_method_help' => 'Si vous pouvez utiliser la méthode IP, choisissez-la. Considerez, que quelques utilisateurs, on peut-être desactivés les cookies dans leurs navigateurs. Cependant, webEdition empêche la resoumission du même formulaire, bienque les cookies soient désactivés.',
		'ip_method_help' => 'Si votre site a seulement un accès d&rsquo;Intranet et vos utilisateurs ne connectent pas par un serveur proxy, choisissez cette méthode. Considerez, que quelques serveurs that some servers assignent des adresses IP addresses dynamiquement.',
		'time_after_voting_again_help' => 'Pour empêcher des votes multiples d&rsquo;un browser spécifique/IP dans une succession rapide, choisissez un temps approprié avant qu&rsquo;un vote peut-être éffectuer par ce navigateur. Si vous voulez qu&rsquo;un vote est éffectue qu&rsquo;une fois par un navigateur/ordinateur spécifié, choisissez \"jamais\".',
		'property' => 'Proprietés',
		'variant' => 'Version', // TRANSLATE
		'voting' => 'Vote',
		'result' => 'Résultat',
		'group' => 'Groupe',
		'name' => 'Nom',
		'newFolder' => 'Nouveau Groupe',
		'save_group_ok' => 'Le Groupe a été enregistré.',
		'save_ok' => 'Le Vote a été enregistré.',
		'path_nok' => 'Le chemin est incorrect!',
		'name_empty' => 'Le nom ne doit pas être vide!',
		'name_exists' => 'Le nom existe déjà!',
		'wrongtext' => 'Le nom n&rsquo;est pas valid!',
		'voting_deleted' => 'Le vote a été suprrimé avec succès.',
		'group_deleted' => 'Le groupe a été supprimé avec succès.',
		'access' => 'Accès',
		'limit_access' => 'Limiter l&rsquo;accès',
		'limit_access_text' => 'Permettre l&rsquo;access pour les utilisateur suivant',
		'variant_limit' => 'Il faut qu&rsquo;il y a au moins une version dans l&rsquo;enquête!',
		'answer_limit' => 'L&rsquo;enquête doit au moins contenir deux reponses!',
		'valid_txt' => 'La case à chocher "active" doit être coché, pour que le vote sur votre site est enregistré et "dépublié" à la fin de sa validité. Determinez avec le menu déroulant la date et le temps dans lequel le module de vote sera active. À partir de ce temps aucun vote sera accepté.',
		'active_till' => 'Active jusqu&rsquo;à',
		'valid' => 'Validité',
		'export' => 'Export', // TRANSLATE
		'export_txt' => 'Exporter les données du dans un fichier csv( valeurs séparées par des virgules).',
		'csv_download' => "Télécharger le fichier CSV",
		'csv_export' => "Le fichier '%s' a été enregistré.",
		'fallback' => 'Méthode IP Fallback',
		'save_user_agent' => 'Enregistrer/Comparer les données du agent-client',
		'save_changed_voting' => "Voting has been changed.\\nDo you want to save your changes?", // TRANSLATE
		'voting_log' => 'Journal de Vote',
		'forbid_ip' => 'Bloquer l\'adresse IP suivant',
		'until' => 'jusqu\'à',
		'options' => 'Options', // TRANSLATE
		'control' => 'Contrôle',
		'data_deleted_info' => 'Les données ont été supprimées!',
		'time' => 'Heure',
		'ip' => 'IP', // TRANSLATE
		'user_agent' => 'Agent-Client',
		'cookie' => 'Cookie', // TRANSLATE
		'delete_ipdata_question' => 'Vous voulez supprimer tous les données d\'IP enregistré. Êtes-vous sûr?',
		'delete_log_question' => 'Vous voulez supprimer tous les entrées de journal de vote. Êtes-vous sûr?',
		'delete_ipdata_text' => 'Les données d\'IP occupe %s octets de la mémoire. Supprimer-les, par utilisant le boutton \Supprimer\'. Considerez s\'il vous plaît, que tous les données d\'IP enregistrées seront effacées et pour cela des vote multiple seront possible.',
		'status' => 'État',
		'log_success' => 'Succès',
		'log_error' => 'Erreur',
		'log_error_active' => 'Erreur: non active',
		'log_error_revote' => 'Erreur: nouveau vote',
		'log_error_blackip' => 'Erreur: IP bloqué',
		'log_is_empty' => 'Le journal est vide!',
		'enabled' => 'Activé',
		'disabled' => 'Desactivé',
		'log_fallback' => 'Fallback', // TRANSLATE

		'new_ip_add' => 'S\'il vous plaît saisissez une nouvelle adresse IP!',
		'not_valid_ip' => 'Cette adresse IP n\'est pas valide',
		'not_active' => 'The entered date is in the past!', // TRANSLATE

		'headline_datatype' => 'Type of Inquiry', // TRANSLATE
		'AllowFreeText' => 'Allow free text', // TRANSLATE
		'AllowImages' => 'Allow images', // TRANSLATE
		'AllowSuccessor' => 'redirect to:', // TRANSLATE
		'AllowSuccessors' => 'allow individual redirects', // TRANSLATE
		'csv_charset' => "Export charset", // TRANSLATE
		'imageID_text' => "Image ID", // TRANSLATE
		'successorID_text' => "Successor ID", // TRANSLATE
		'mediaID_text' => "Media-ID", // TRANSLATE
		'AllowMedia' => 'Allow Media such as Audio or video files', // TRANSLATE

		'voting-id' => 'Voting ID', // TRANSLATE
		'voting-session' => 'Voting Session', // TRANSLATE
		'voting-successor' => 'successor', // TRANSLATE
		'voting-additionalfields' => 'add. data', // TRANSLATE
		'answerID' => 'answer ID', // TRANSLATE
		'answerText' => 'answer text', // TRANSLATE

		'userid_method' => 'For logged in Users (customer management), compare to saved customer ID (the log has to be active)', // TRANSLATE
		'IsRequired' => 'This is a required field', // TRANSLATE

		'answer_limit' => 'The inquiry must consist of at least two - in case free text answers are allowd one - answers!', // TRANSLATE
		'folder_path_exists' => "Folder already exists!", // TRANSLATE
);