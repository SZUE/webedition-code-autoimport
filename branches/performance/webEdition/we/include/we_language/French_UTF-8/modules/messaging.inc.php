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
 * Language file: messaging.inc.php
 * Provides language strings.
 * Language: English
 */
$l_modules_messaging = array(
		'done' => "Done", // TRANSLATE
		'new_messages' => "Nouveaux Messages",
		'new_tasks' => "Nouveaux Tâchesw",
		'wintitle' => "System Messagerie - Tâche",
		'updatestatus' => "État Mise à Jour",
		'search_todos' => "Fouiller les Tâches",
		'Mitteilungen' => "Messages", // TRANSLATE
		'Gesendet' => "Envoyé",
		'ToDo' => "Tâche",
		'Erledigt' => "Accompli",
		'Zurueckgewiesen' => "Rejeté",
		'type_todo' => "Task", // TRANSLATE
		'type_message' => "Message", // TRANSLATE

		'userid_not_found' => "ID-Utilisateur non existant",
		'username_not_found' => "Nom Utilisateur n'existe pas",
		'save_changed_folder' => "Folder has been changed.\\nDo you want to save your changes?", // TRANSLATE

		'no_inbox_folder' => "Manque d'un Reçue-Répertoire pour le destinataire",
		'no_done_folder' => "&quot;Non accompli&quot; Répertoire pour cet utilisateur n'est pas défini",
		'no_reject_folder' => "Manque d'un Repoussé-Répertoire pour le destinaire",
		'folder_settings' => "Préférences de Répertoire",
		'new_folder' => 'Nouveaux Repertoire',
		'change_folder_settings' => 'Changer les Préférences de Répertoire',
		'folder_name' => 'Nom de Répertoire',
		'parent_folder' => "Répertoire précédent",
		'update_successful' => 'Mis a jour effectué',
		'error_occured' => 'Erreur ',
		'type' => 'Type', // TRANSLATE
		'cant_paste' => "Il n'est pas possible de coller dans ce répertoire",
		'src_class_ne_trgt_class' => "Classe orginal est differnt de la classe destinataire",
		'no_selection' => 'Aucun message sélectionné',
		'rcpt_parse_error' => "L'adresse destinataire est inconnu",
		'addr_book_saved' => "Carnet d'adresses enregistré",
		'msg_type_not_found' => "Type de message n'existe pas",
		'children_same_name' => 'Nom de répertoire existe déjà.',
		'no_parent_folder' => "Répertoire précedent n'existe pas",
		'folder_created' => "Répertoire créé",
		'folder_create_error' => "Erreur en créant le Répertoire",
		'param_error' => 'Erreur de paramètre',
		'param_wrong_type' => "Le type de donné du paramètre est invalide",
		'parentfolder_invalid' => "Le répertoire precendent est invalide",
		'folderid_invalid' => 'ID-Répertoire invalide',
		'folder_modified' => 'Répertoire changé',
		'folder_change_failed' => 'Changement de Répertoire échoué',
		'mail_not_sent' => "Mail n'a pas été envoyé",
		'from' => 'De',
		'to' => 'To', // TRANSLATE
		'reject_to' => 'Repoussé à',
		'recipient' => 'Destinataire',
		'recipients' => 'Destinataires',
		'assigner' => 'Assignaire',
		'current_assignee' => 'Assigné à',
		'subject' => 'Sujet',
		'sender' => 'Expéditeur',
		'content' => 'Contenu',
		'deadline' => 'Date limite',
		'status' => 'État',
		'priority' => 'Priorité',
		'comment' => 'Commentaire',
		'message' => 'Messages',
		'forwarding_todo' => 'Transmettre la Tâche ...',
		'rejecting_todo' => 'Repousser la Tâche...',
		'creating_todo' => 'Créer une Tâche...',
		'todo_s_created' => 'Tâche créée avec succès',
		'todo_s_forwarded' => "Tâche transmit avec succès a",
		'todo_s_rejected' => "Tâche rejetée avec succès a",
		'todo_n_created' => "Tâche n'a pas été créé pour",
		'todo_n_forwarded' => "Tâche n'a pas été transmit à",
		'todo_n_rejected' => "Tâche n'a pas été rejetée à",
		'occured_errs' => 'Erreurs apparues',
		'nobody' => 'Personne',
		'new_todo' => 'Créer une Tâche',
		'forward_todo' => 'Transmettre la Tâche',
		'reject_todo' => 'Rejeter la Tâche',
		'update_todo' => "Mis a jour de l'etat de Tâche",
		'multi_sel_on' => 'Choix multiple activé',
		'multi_sel_off' => 'Choix multiple désactivé',
		'copy_todos' => 'Copier des Tâches',
		'cut_todos' => 'Couper des Tâches',
		'paste_todos' => 'Coller des Tâches',
		'rm_todos' => 'Supprimer les Tâches',
		'poll_msg' => 'Recherche de nouveaux Messages/Tâches',
		'addr_book' => 'Adresses',
		'selected' => 'Sélectioné',
		'settings' => 'Préférences',
		'saved' => 'Enregistré',
		'check_step' => 'Intervalle de Contrôle',
		'minutes' => 'Minute(s)', // TRANSLATE
		'message_send' => 'Envoyer des Messages',
		's_sent_to' => 'Envoyé avec succès à',
		'n_sent_to' => 'nicht versandt an',
		'q_rm_todos' => 'Supprimer les Tâches?',
		'search_messages' => 'Fouiller les Messages',
		'new_message' => 'New message',
		'reply_message' => 'Répondre à des Messages',
		'new_message' => 'Créer des Messages',
		'created_by' => 'Créé par',
		'assigned_by' => 'Assigné par',
		'creation_date' => 'Date de fabrication',
		'date' => 'Date', // TRANSLATE
		'q_rm_messages' => 'Supprimer les Messages?',
		'copy_messages' => 'Copier des Messages',
		'cut_messages' => 'Couper ',
		'paste_messages' => 'Coller des Messages',
		'rm_messages' => 'Supprimer des Messages',
		'is_read' => 'Lu',
		'attrib_line' => 'Vous écrivez',
		'comment_created' => 'Commentaire créé',
		'forwarded_to' => 'transmit à',
		'rejected_to' => 'rejeté à',
		'todo_no_changes' => 'Aucun Changement indiqué',
		'todo_none_selected' => 'Aucune Tâche sélectionée',
		'todo_move_error' => 'Erreur en déplacant',
		'todo_no_forward' => 'Cette Tâche ne peut pas être transmise',
		'todo_creator_is_assignee' => 'Créateur et Assignaire sont identiques',
		'todo_no_reject' => 'Cette Tâche ne peut pas être rejetée',
		'rm_folders' => "Supprimer le répertoire",
		'cant_create_folders' => "Erreur: Il n'était pas possible de créer un répertoire pour cet utiltisateur",
		'folders_created' => 'Répertoire créé',
		'deltext' => "Marquer les répertoires que vous voulez supprimer dans le Menue-Explorer, et appuyez ensuite sur le &quot;d'accord&quot;-Bouton",
		'sel_rcpts' => 'Choisir le Destinataire',
		'err_delete_folders' => 'Erreur en supprimant',
		'launch_todo' => 'Passer au System de Tâche',
		'launch_msg' => 'Passer au System de Messagerie',
		'folder_sent' => 'Envoyé',
		'folder_messages' => 'Messages', // TRANSLATE
		'folder_done' => 'Accompli',
		'folder_rejected' => 'Rejeté',
		'folder_todo' => 'Tâche',
		'search_advanced' => 'Préferences de recherche supplementaire',
		'to_search_fields' => 'Chercher dans les Champs',
		'to_search_folders' => 'Chercher dans les Répertoires',
		'todo_status_inv_input' => 'Entrée invalide (État-Tâche)',
		'todo_status_update' => "Actualisation de l'état de Tâche",
		'we_todo' => "webEdition Tâche",
		'nofolder' => "aucun",
);