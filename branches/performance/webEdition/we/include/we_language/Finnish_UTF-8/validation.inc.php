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
		'headline' => 'Dokumentin validointi internetin välityksellä',
//  variables for checking html files.
		'description' => 'Voit valita palvelun verkosta tarkistaaksesi dokumentin validiteetin/käytettävyyden.',
		'available_services' => 'Olemassaolevat palvelut',
		'category' => 'Kategoria',
		'service_name' => 'Palvelun nimi',
		'service' => 'Palvelu',
		'host' => 'Palvelin',
		'path' => 'Polku',
		'ctype' => 'Sisällön tyyppi',
		'ctype' => 'Lähetettävän tiedoston tyypin tarkistuksen toiminto kohdepalvelimelle (text/html oder text/css)',
		'fileEndings' => 'Tiedoston päätteet',
		'fileEndings' => 'Syötä kaikki päätteet jotka on käytettävissä tälle palvelulle. (.html,.css)',
		'method' => 'Siirtotapa',
		'checkvia' => 'Lähetä käyttäen',
		'checkvia_upload' => 'Tiedoston latausta',
		'checkvia_url' => 'URL -siirtoa',
		'varname' => 'Muuttujan nimi',
		'varname' => 'Syötä tiedoston/url -kentän nimi',
		'additionalVars' => 'Lisäparametrit',
		'additionalVars' => 'valinneinen: var1=wert1&var2=wert2&...',
		'result' => 'Tulos',
		'active' => 'Aktiivinen',
		'active' => 'Piilota palvelu väliaikaisesti.',
		'no_services_available' => 'Ei palveluita käytettävissä tälle tiedostotyypille.',
//  the different predefined services
		'adjust_service' => 'Muuta validointipalvelua',
		'art_custom' => 'Räätälöidyt palvelut',
		'art_default' => 'Esimääritetyt palvelut',
		'category_xhtml' => '(X)HTML',
		'category_links' => 'Linkit',
		'category_css' => 'CSS -tyylitiedostot',
		'category_accessibility' => 'Käytettävyys',
		'edit_service' => array(
				'new' => 'Uusi palvelu',
				'saved_success' => 'Palvelu on tallennettu.',
				'saved_failure' => 'Palvelua ei voitu tallentaa.',
				'delete_success' => 'Palvelu on poistettu.',
				'delete_failure' => 'Palvelua ei voitu poistaa.',
				'servicename_already_exists' => 'Tämän niminen palvelu on jo olemassa.',
		),
//  services for html
		'service_xhtml_upload' => '(X)HTML W3C -validointi tiedostolatauksen kautta',
		'service_xhtml_url' => '(X)HTML W3C -validointi URL -siirron kautta',
//  services for css
		'service_css_upload' => 'CSS -validointi tiedostolatauksen kautta',
		'service_css_url' => 'CSS -validointi URL -siirron kautta',
		'connection_problems' => '<strong>Virhe yhteydenmuodostuksessa palveluun<(/trong><br /><br /><br />Huomioi: valinta "url siirto" on käytettävissä vain, jos webEdition järjestelmän pääsy on sallittu internetiin.Siirto ei ole mahdollinen paikallisasennuksessa.<br /><br />Ongelmia voi myös esiintyä käytettäessä palomuureja tai proxy-palvelimia. Tarkista asetukset tässä tapauksessa.<br /><br />HTTP-Response: %s',
);