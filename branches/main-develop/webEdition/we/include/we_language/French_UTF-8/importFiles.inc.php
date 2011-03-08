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
 * Language file: import_files.inc.php
 * Provides language strings.
 * Language: English
 */
$l_importFiles = array(
		'destination_dir' => "Répertoire cible",
		'file' => "Fichier",
		'sameName_expl' => "Définissez le comportement de webEdition, s'il exitste un fichier avec le même nom.",
		'sameName_overwrite' => "Éffacer le fichier existant",
		'sameName_rename' => "Renommer le nouveau fichier",
		'sameName_nothing' => "Ne pas importer le fichier",
		'sameName_headline' => "En cas de<br>nom égal?",
		'step1' => "Import des fichiers local - étape 1 sur 2",
		'step2' => "Import des fichiers local - étape 2 sur 2",
		'step3' => "Import local files - Step 3 of 3", // TRANSLATE
		'import_expl' => "Avec un clic sur le bouton à coté du saisi de texte vous pouvez choisir un fichier sur votre disque dur local. Après la séléction un autre champ de saisi apparaîtra dans lequel vous pouvez choisir un autre fichier. Considérez que la taille par fichier ne doit pas dépasser %s à cause de restriction de PHP!<br><br>Cliquez \"Avancer\", pour démarrer l'import.",
		'import_expl_jupload' => "With the click on the button you can select more then one file from your harddrive. Alternatively the files can be selected per 'Drag and Drop' from the file manager.  Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Next\", to start the import.",
		'error' => "Erreur en important!\\n\\nLes fichiers suivant ne pouvait pas être importés:\\n%s",
		'finished' => "L&rsquo;import a été terminé avec succès!",
		'import_file' => "Import du fichier %s",
		'no_perms' => "Erreur: non authorisé",
		'move_file_error' => "Erreur: move_uploaded_file()",
		'read_file_error' => "Erreur: fread()",
		'php_error' => "Erreur: upload_max_filesize()",
		'same_name' => "Erreur: Datei existiert",
		'save_error' => "Erreur en enregistrant",
		'publish_error' => "Erreur en publiant",
		'root_dir_1' => "Vous avez choisi le répertoire racine du serveur web comme répertoire source. Êtes-vous sûr, que vous voulez importer le contenu du répertoire racine complètement?",
		'root_dir_2' => "Vous avez choisi le répertoire racine du serveur web comme répertoire cible. Êtes-vous sûr, que vous voulez importer tous directement dans le répertoire racine?",
		'root_dir_3' => "Vous avez choisi le répertoire racine du serveur web comme répertoire source et cible. Êtes-vous sûr, que vous voulez reimportez tous le contenu du répertoire racine dans le répertoire racien?",
		'thumbnails' => "Imagettes",
		'make_thumbs' => "Créer des<br>Imagettes",
		'image_options_open' => "Afficher les fonctions graphiques",
		'image_options_close' => "Cacher les les fonctions graphiques",
		'add_description_nogdlib' => "Pour que vous puisse profiter des fonctions des imagettes, il est nécéssaire que la GD Library soit installée!",
		'noFiles' => "No files exist in the specified source directory which correspond with the given import settings!", // TRANSLATE
		'emptyDir' => "The source directory is empty!", // TRANSLATE

		'metadata' => "Meta data", // TRANSLATE
		'import_metadata' => "Import meta data from file", // TRANSLATE
);