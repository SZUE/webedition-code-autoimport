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
 * Language: Deutsch
 */
$l_importFiles  = array(
		'destination_dir' => "Zielverzeichnis",
		'file' => "Datei",
		'sameName_expl' => "Bestimmen Sie hier das Verhalten von webEdition, wenn eine Datei mit gleichem Namen existiert.",
		'sameName_overwrite' => "Existierende Datei überschreiben",
		'sameName_rename' => "Neue Datei umbenennen",
		'sameName_nothing' => "Datei nicht importieren",
		'sameName_headline' => "Was tun bei<br>gleichem Namen?",
		'step1' => "Lokale Dateien importieren - Schritt 1 von 3",
		'step2' => "Lokale Dateien importieren - Schritt 2 von 3",
		'step3' => "Lokale Dateien importieren - Schritt 3 von 3",
		'import_expl' => "Durch Klick auf den Button neben dem Eingabefeld können Sie eine Datei auf Ihrer Festplatte auswählen. Nach der Auswahl erscheint ein neues Eingabefeld mit dem Sie eine weitere Datei auswählen können. Beachten Sie, daß pro Datei die maximale Größe von %s auf Grund von PHP-Einschränkungen nicht überschritten werden darf!<br><br>Klicken Sie auf \"Weiter\", um den Import zu starten.",
		'import_expl_jupload' => "Durch Klick auf den Button im Java-Applet können Sie mehrere Dateien auf Ihrer Festplatte auswählen. Alternativ können Sie die Dateien aus dem File Manager per 'Drag and Drop' in das Applet ziehen. Beachten Sie, daß pro Datei die maximale Größe von %s auf Grund von PHP-Einschränkungen nicht überschritten werden darf!<br><br>Klicken Sie auf \"Hochladen\" im Applet, um den Import zu starten.",
		'error' => "Es sind Fehler beim Import aufgetreten!\\n\\nFolgende Dateien konnten nicht importiert werden:\\n%s",
		'finished' => "Der Import wurde erfolgreich beendet!",
		'import_file' => "Importiere Datei %s",
		'no_perms' => "Fehler: keine Berechtigung",
		'move_file_error' => "Fehler: move_uploaded_file()",
		'read_file_error' => "Fehler: fread()",
		'php_error' => "Fehler: upload_max_filesize()",
		'same_name' => "Fehler: Datei existiert",
		'save_error' => "Fehler beim speichern",
		'publish_error' => "Fehler beim veröffentlichen",
		'root_dir_1' => "Sie haben als Quellverzeichnis das Root-Verzeichnis des Webservers angegeben. Sind Sie sicher, dass Sie sämtlichen Inhalt des Root-Verzeichnisses importieren möchten?",
		'root_dir_2' => "Sie haben als Zielverzeichnis das Root-Verzeichnis des Webservers angegeben. Sind Sie sicher, dass Sie alles direkt in das Root-Verzeichnis importieren möchten?",
		'root_dir_3' => "Sie haben als Quell- und Zielverzeichnis das Root-Verzeichnis des Webservers angegeben. Sind Sie sicher, dass Sie sämtlichen Inhalt des Root-Verzeichnisses wieder direkt in das Root-Verzeichnis importieren möchten?",
		'thumbnails' => "Miniaturansichten",
		'make_thumbs' => "Erzeuge<br>Miniaturansichten",
		'image_options_open' => "Grafikfunktionen einblenden",
		'image_options_close' => "Grafikfunktionen ausblenden",
		'add_description_nogdlib' => "Damit Ihnen die Grafikfunktionen zur Verfügung stehen, muß die GD Library auf Ihrem Server installiert sein!",
		'noFiles' => "Im angegebenen Quellverzeichnis existieren keine Dateien, welche den Importeinstellungen entsprechen!",
		'emptyDir' => "Das Quellverzeichnis ist leer!",
		'metadata' => "Metadaten",
		'import_metadata' => "Vorhandene Metadaten importieren",
);