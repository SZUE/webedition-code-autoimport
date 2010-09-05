<?php

/**
 * webEdition CMS
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
 * Language file: metadata.inc.php
 * Provides language strings.
 * Language: Finnish
 */

/*****************************************************************************
 * DOCUMENT TAB
 *****************************************************************************/

$l_metadata["filesize"] = "Tiedostokoko";
$l_metadata["supported_types"] = "Metatieto formaatit";
$l_metadata["none"] = "ei mitään";
$l_metadata["filetype"] = "Tiedostotyyppi";

/*****************************************************************************
 * METADATA FIELD MAPPING
 *****************************************************************************/

$l_metadata["headline"] = "Metatieto kentät";
$l_metadata["tagname"] = "Kentän nimi";
$l_metadata["type"] = "Tyyppi";
$l_metadata["dummy"] = "esimerkki";

$l_metadata["save"] = "Metatietoja tallennetaan, odota pieni hetki...";
$l_metadata["save_wait"] = "Tallennetaan asetuksia";

$l_metadata["saved"] = "Metatietokentät tallennettu.";
$l_metadata["saved_successfully"] = "Metatietokentät tallennettu";

$l_metadata["properties"] = "Ominaisuudet";

$l_metadata["fields_hint"] = "Määrittele lisäkenttiä metatiedolle. Liitetty data saatetaan automaattisesti muuttaa tuonnin yhteydessä. Lisää syöttökenttään yksi tai useampi kenttä jotka tuodaan &quot;import from&quot; formaattiin &quot;[tyyppi]/[kenttänimi]&quot;. Esimerkiksi &quot;exif/copyright,iptc/copyright&quot;. Useita kenttiä voidaan laittaa erottamalla ne pilkulla. Tuonti etsii kaikki määritellyt kentät kaikista kentistä joissa on jotain dataa.";
$l_metadata["import_from"] = "Tuo kohteesta";
$l_metadata["fields"] = "Kentät";
$l_metadata['add'] = "lisää";

/*****************************************************************************
 * UPLOAD
 *****************************************************************************/

$l_metadata["import_metadata_at_upload"] = "Tuo metatieto tiedostosta";

/*****************************************************************************
 * ERROR MESSAGES
 *****************************************************************************/

$l_metadata['error_meta_field_empty_msg'] = "Kentän nimi rivillä %s1 ei voi olla tyhjä!";
$l_metadata['meta_field_wrong_chars_messsage'] = "Kentän nimi '%s1' on virheellinen! Sallitut kirjaimet ovat (a-z, A-Z, 0-9) ja alaviiva.";
$l_metadata['meta_field_wrong_name_messsage'] = "Kentän nimi '%s1' on virheellinen! Se on sisäisesti webEditionin käytössä! Seuraavat nimet ovat virheellisiä, eikä voida käyttää: %s2";
$l_metadata['file_size_0'] = 'The file size is 0 byte, please upload a document to the server before saving';// TRANSLATE

/*****************************************************************************
 * INFO TAB
 *****************************************************************************/

$l_metadata['info_exif_data'] = "Exif data";
$l_metadata['info_iptc_data'] = "IPTC data";
$l_metadata['no_exif_data'] = "Ei Exif dataa saatavilla";
$l_metadata['no_iptc_data'] = "Ei IPTC dataa saatavilla";
$l_metadata['no_exif_installed'] = "PHP Exif -lisäosaa ei asennettu!";
$l_metadata['no_metadata_supported'] = "webEdition ei tue metadata formaatteja tämänkaltaisissa dokumenteissa.";

?>