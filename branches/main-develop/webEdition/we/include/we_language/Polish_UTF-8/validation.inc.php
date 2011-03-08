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
		'headline' => 'Sprawdzanie online poprawności tego dokumentu.',
//  variables for checking html files.
		'description' => 'Tutaj można skorzystać z kilku usług, dzięki którym można sprawdzić swoją stronę pod względem poprawności czy też dostępności.',
		'available_services' => 'Wprowadzone usługi',
		'category' => 'Kategorie',
		'service_name' => 'Nazwa usługi',
		'service' => 'Usługa',
		'host' => 'Host', // TRANSLATE
		'path' => 'Ścieżka',
		'ctype' => 'Typ pliku',
		'ctype' => 'Identyfikator dla serwera stwierdzający o jaki typ dokumentu chodzi. (tekst/html lub tekst/css)',
		'fileEndings' => 'Zmiany pliku',
		'fileEndings' => 'Tutaj mogą zostać wprowadzone zmiany pliku dla tej usługi. (.html,.css)',
		'method' => 'Metoda',
		'checkvia' => 'Wysłać przez',
		'checkvia_upload' => 'Upload pliku',
		'checkvia_url' => 'podanie linku URL',
		'varname' => 'Nazwa zmiennej',
		'varname' => '(Nazwa pliku HTML/ podać URL)',
		'additionalVars' => 'Parametr dodatkowy',
		'additionalVars' => 'opcjonalnie: var1=wartosc1&var2=wartosc2&...',
		'result' => 'Wynik',
		'active' => 'Aktywny',
		'active' => 'Można wyświetlić usługi.',
		'no_services_available' => 'Dla tego typ pliku nie podano jeszcze żadnych usług.',
//  the different predefined services
		'adjust_service' => 'Edycja walidacji strony',
		'art_custom' => 'Usługi zdefiniowane przez użytkownika',
		'art_default' => 'Domyślnie ustawione usługi',
		'category_xhtml' => '(X)HTML', // TRANSLATE
		'category_links' => 'Linki',
		'category_css' => 'Cascading Style Sheets', // TRANSLATE
		'category_accessibility' => 'Dostępność',
		'edit_service' => array(
				'new' => 'Nowa usługa',
				'saved_success' => 'Usługa została zapisana.',
				'saved_failure' => 'Usługa nie mogła zostać zapisana.',
				'delete_success' => 'Usługa została usunięta.',
				'delete_failure' => 'Usługa nie mogła zostać usunięta.',
				'servicename_already_exists' => 'A service with this name already exists.', // TRANSLATE
		),
//  services for html
		'service_xhtml_upload' => 'Walidacja (X)HTML strony poprzez Upload pliku',
		'service_xhtml_url' => 'Walidacja (X)HTML strony poprzez podanie linku URL',
//  services for css
		'service_css_upload' => 'Walidacja CSS strony poprzez Upload pliku',
		'service_css_url' => 'Walidacja CSS strony poprzez podanie linku URL',
		'connection_problems' => '<strong>Wystšpił błšd podczas łšczenia się z wybranš usługš.</strong><br /><br />Pamiętaj: opcję "podanie linku URL" możesz użyć tylko wtedy, jeżeli Twoja instalacja webEdition jest osišgalna z Internetu (czyli spoza Twojej sieci lokalnej). W przypadku instalacji lokalnej (Localhost) nie ma dostępu do programu z zewnštrz.<br /><br />Przyczynš problemu mogš też być serwery zapór ogniowych (Firewall) i proxy. Sprawd pod tym kštem swojš konfigurację.<br /><br />Odpowied HTTP: %s',
);