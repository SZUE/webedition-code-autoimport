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
$l_validation = array(
		'headline' => 'Validation online d\'un document.',
//  variables for checking html files.
		'description' => 'Vous pouvez vous servir des services de web, pour vérfifier la validité et accessibilité de votre site.',
		'available_services' => 'Service inscrit',
		'category' => 'Catégorie',
		'service_name' => 'Nom du Service',
		'service' => 'Service', // TRANSLATE
		'host' => 'Hôte',
		'path' => 'Chemin',
		'ctype' => 'Type-de-fichier',
		'ctype' => 'Caractéristique de reconnaissance pou le serveur cible, pour qu\'il puisse reconnaître de quel fichier il s\'agit. (text/html ou text/css)',
		'fileEndings' => 'Extension-de-fichier',
		'fileEndings' => 'Les extension pour lequelles ce service sera utilisé, peuvent être saisi ici. (.html,.css)',
		'method' => 'Methode',
		'checkvia' => 'Envoyer par',
		'checkvia_upload' => 'Datei-Upload',
		'checkvia_url' => 'Transmission d\'URL',
		'varname' => 'Nom de variable',
		'varname' => '(saisir le nom de la saisie-d\'HTML du fichier / URL)',
		'additionalVars' => 'Paramètre supplémentaire',
		'additionalVars' => 'optionnel: var1=valeur1&var2=valeur2&...',
		'result' => 'Résultat',
		'active' => 'Actif',
		'active' => 'Vous pouvez cacher/désactiver ces services.',
		'no_services_available' => 'Pour ce type de fichier aucun service a été enocre défini.',
//  the different predefined services
		'adjust_service' => 'Éditer les service de validation',
		'art_custom' => 'Benutzerdefinierte Dienste',
		'art_default' => 'Services allégué',
		'category_xhtml' => '(X)HTML', // TRANSLATE
		'category_links' => 'Liens',
		'category_css' => 'Cascading Style Sheets', // TRANSLATE
		'category_accessibility' => 'Accessibilité',
		'edit_service' => array(
				'new' => 'Nouveau Service',
				'saved_success' => 'Le service a été enregistré avec succès.',
				'saved_failure' => 'Le service n\'a pas pu être enregistré.',
				'delete_success' => 'Le service a été enregistré avec succès.',
				'delete_failure' => 'Le service n\'a pas pu être supprimé.',
				'servicename_already_exists' => 'A service with this name already exists.', // TRANSLATE
		),
//  services for html
		'service_xhtml_upload' => 'Validation d\'(X)HTML du W3C par téléchargement de fichier',
		'service_xhtml_url' => 'Validation d\'(X)HTML du W3C par transmission d\'URL',
//  services for css
		'service_css_upload' => 'Validation CSS par téléchargement de fichier',
		'service_css_url' => 'Validation CSS par transmission d\'URL',
		'connection_problems' => '<strong>Erreur en connectant au service choisi.</strong><br /><br />Considerez: L\'option "Transmission d\'URL" ne peut être utilisé, que si votre Installation de webEdition est accessible par l\'internet (alors en dehors de votre reseau local). Ce n\'est pas le cas avec installation local (localhost).<br /><br />Ainsi peuvent se produire des problèmes avec des serveur-proxy ou des pare-feux. Dans ce cas vérifiez votre configuration s\'il vous plaît.<br /><br />Réponse-HTTP: %s',
);