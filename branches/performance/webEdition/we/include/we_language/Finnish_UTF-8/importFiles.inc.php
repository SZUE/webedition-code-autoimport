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
		'destination_dir' => "Kohdehakemisto",
		'file' => "Tiedosto",
		'sameName_expl' => "Jos samanniminen tiedosto on jo olemassa, mitä haluaisit webEditionin tekevän?",
		'sameName_overwrite' => "Korvaa samannimisen tiedoston",
		'sameName_rename' => "Uudelleennimeää uuden tiedoston",
		'sameName_nothing' => "Ei tuo tiedostoa",
		'sameName_headline' => "Jos tiedosto<br>on olemassa?",
		'step1' => "Tuo paikallisia tiedostoja - Vaihe 1 / 3",
		'step2' => "Tuo paikallisia tiedostoja - Vaihe 2 / 3",
		'step3' => "Tuo paikallisia tiedostoja - Vaihe 3 / 3",
		'import_expl' => "Paina seuraava painiketta syöttökentässä valitaksesi tiedoston kovalevyltäsi. Valinnan jälkeen ilmestyy uusi syöttökenttä ja voit valita uuden tiedoston. Huomaa että tiedoston maksimikokoa %s ei voida ylittää johtuen PHP:n rajoituksesta!<br><br>Paina \"Seuraava\" -painiketta aloittaaksesi tuonnin.",
		'import_expl_jupload' => "Painiketta painamalla voit valita useampia tiedostoja kovalevyltäsi. Vaihtoehtoisesti voit valita tiedostoja 'raahaamalla' niitä koneesi tiedostojenhallinnasta.  Huomioi että PHP:n rajoittamaa tiedostojen maksimikokoa %s ei saa ylittää!<br><br>Klikkaa \"Seuraava\", aloittaaksesi tuonnin.",
		'error' => "Virhe tiedoston tuonnissa!\\n\\nSeuraavia tiedostoja ei voitu tuoda:\\n%s",
		'finished' => "Tuonti onnistui!",
		'import_file' => "Tuodaan tiedostoa %s",
		'no_perms' => "Virhe: ei oikeuksia",
		'move_file_error' => "Virhe: move_uploaded_file()",
		'read_file_error' => "Virhe: fread()",
		'php_error' => "Virhe: upload_max_filesize()",
		'same_name' => "Virhe: file exists",
		'save_error' => "Virhe tallennettaessa tiedostoa",
		'publish_error' => "Virhe julkaistaessa tiedostoa",
		'root_dir_1' => "Määritit www-palvelimen juurihakemiston lähdehakemistoksi. Oletko varma että haluat tuoda juurihakemiston sisällön?",
		'root_dir_2' => "Määritit www-palvelimen juurihakemiston kohdehakemistoksi. Oletko varma että haluat tuoda suoraan juurihakemistoon?",
		'root_dir_3' => "Määritit www-palvelimen juurihakemiston sekä lähde -että kohdehakemistoksi. Oletko varma että haluat tuoda juurihakemistoon?",
		'thumbnails' => "Esikatselukuvat",
		'make_thumbs' => "Luo<br>esikatselukivia",
		'image_options_open' => "Näytä kuvatoiminnot",
		'image_options_close' => "Piilota kuvatoiminnot",
		'add_description_nogdlib' => "GD -kirjasto pitää olla asennettuna palvelimelle, jotta voit käyttää kuvatoimintoja!",
		'noFiles' => "Lähdehakemistossa ei ole tuontiehtojen mukaisia tiedostoja!",
		'emptyDir' => "Lähdehakemisto on tyhjä!",
		'metadata' => "Metatiedot",
		'import_metadata' => "Tuo metatiedot tiedostosta",
);