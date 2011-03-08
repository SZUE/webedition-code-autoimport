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
		'headline' => 'Online valideren van dit document',
//  variables for checking html files.
		'description' => 'U kunt een internet-dienst kiezen om dit document te testen op geldigheid/toegankelijkheid.',
		'available_services' => 'Bestaande diensten',
		'category' => 'Categorie',
		'service_name' => 'Naam van de dienst',
		'service' => 'Dienst',
		'host' => 'Host', // TRANSLATE
		'path' => 'Pad',
		'ctype' => 'Soort inhoud',
		'ctype' => 'Kenmerk voor de doel server bij het bepalen van het soort aangeboden bestand (tekst/html of tekst/css)',
		'fileEndings' => 'Extensies',
		'fileEndings' => 'Voeg alle extensies toe die beschikbaar zijn voor deze dienst. (.html,.css)',
		'method' => 'Methode',
		'checkvia' => 'Aanbieden via',
		'checkvia_upload' => 'Bestandsupload',
		'checkvia_url' => 'URL overdracht',
		'varname' => 'Variable naam',
		'varname' => 'Voer de veldnaam in van het bestand/url',
		'additionalVars' => 'Bijkomende Parameters',
		'additionalVars' => 'optioneel: var1=wert1&var2=wert2&...',
		'result' => 'Resultaat',
		'active' => 'Actief',
		'active' => 'Hier kunt u een dienst tijdelijk verbergen.',
		'no_services_available' => 'Er zijn nog geen diensten beschikbaar voor dit bestandstype.',
//  the different predefined services
		'adjust_service' => 'Stel validatie dienst in',
		'art_custom' => 'Vrije diensten',
		'art_default' => 'Vooraf gedefinieerde diensten',
		'category_xhtml' => '(X)HTML', // TRANSLATE
		'category_links' => 'Koppelingen',
		'category_css' => 'Cascading Style Sheets', // TRANSLATE
		'category_accessibility' => 'Toegankelijkheid',
		'edit_service' => array(
				'new' => 'Nieuwe dienst',
				'saved_success' => 'De dienst is bewaard.',
				'saved_failure' => 'De dienst kon niet bewaard worden.',
				'delete_success' => 'De dienst is verwijderd.',
				'delete_failure' => 'De dienst kon niet verwijderd worden.',
				'servicename_already_exists' => 'Er bestaat al een dienst met deze naam.',
		),
//  services for html
		'service_xhtml_upload' => '(X)HTML W3C valideren via bestandsupload',
		'service_xhtml_url' => '(X)HTML W3C valideren via url overdracht',
//  services for css
		'service_css_upload' => 'CSS valideren via bestandsupload',
		'service_css_url' => 'CSS valideren via url overdracht',
		'connection_problems' => '<strong>Er is een fout opgetreden tijdens het verbinden met deze dienst</strong><br /><br />Let op: De optie "url overdracht" is alleen beschikbaar als uw webEdition installatie ook bereikbaar is via het internet (buiten uw lokale netwerk). Dit is niet mogelijk wanneer webEdition lokaal is geïnstalleerd (localhost).<br /><br />Ook kunnen er problemen optreden wanneer u Firewalls en proxy-servers gebruikt. Controleer uw configuratie als dit het geval is.<br /><br />HTTP-Reactie: %s',
);