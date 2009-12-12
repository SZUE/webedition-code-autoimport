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
$l_metadata["none"] = "ei mit��n";
$l_metadata["filetype"] = "Tiedostotyyppi";

/*****************************************************************************
 * METADATA FIELD MAPPING
 *****************************************************************************/

$l_metadata["headline"] = "Metatieto kent�t";
$l_metadata["tagname"] = "Kent�n nimi";
$l_metadata["type"] = "Tyyppi";
$l_metadata["dummy"] = "esimerkki";

$l_metadata["save"] = "Metatietoja tallennetaan, odota pieni hetki...";
$l_metadata["save_wait"] = "Tallennetaan asetuksia";

$l_metadata["saved"] = "Metatietokent�t tallennettu.";
$l_metadata["saved_successfully"] = "Metatietokent�t tallennettu";

$l_metadata["properties"] = "Ominaisuudet";

$l_metadata["fields_hint"] = "M��rittele lis�kentti� metatiedolle. Liitetty data saatetaan automaattisesti muuttaa tuonnin yhteydess�. Lis�� sy�tt�kentt��n yksi tai useampi kentt� jotka tuodaan &quot;import from&quot; formaattiin &quot;[tyyppi]/[kentt�nimi]&quot;. Esimerkiksi &quot;exif/copyright,iptc/copyright&quot;. Useita kentti� voidaan laittaa erottamalla ne pilkulla. Tuonti etsii kaikki m��ritellyt kent�t kaikista kentist� joissa on jotain dataa.";
$l_metadata["import_from"] = "Tuo kohteesta";
$l_metadata["fields"] = "Kent�t";
$l_metadata['add'] = "lis��";

/*****************************************************************************
 * UPLOAD
 *****************************************************************************/

$l_metadata["import_metadata_at_upload"] = "Tuo metatieto tiedostosta";

/*****************************************************************************
 * ERROR MESSAGES
 *****************************************************************************/

$l_metadata['error_meta_field_empty_msg'] = "Kent�n nimi rivill� %s1 ei voi olla tyhj�!";
$l_metadata['meta_field_wrong_chars_messsage'] = "Kent�n nimi '%s1' on virheellinen! Sallitut kirjaimet ovat (a-z, A-Z, 0-9) ja alaviiva.";
$l_metadata['meta_field_wrong_name_messsage'] = "Kent�n nimi '%s1' on virheellinen! Se on sis�isesti webEditionin k�yt�ss�! Seuraavat nimet ovat virheellisi�, eik� voida k�ytt��: %s2";


/*****************************************************************************
 * INFO TAB
 *****************************************************************************/

$l_metadata['info_exif_data'] = "Exif data";
$l_metadata['info_iptc_data'] = "IPTC data";
$l_metadata['no_exif_data'] = "Ei Exif dataa saatavilla";
$l_metadata['no_iptc_data'] = "Ei IPTC dataa saatavilla";
$l_metadata['no_exif_installed'] = "PHP Exif -lis�osaa ei asennettu!";
$l_metadata['no_metadata_supported'] = "webEdition ei tue metadata formaatteja t�m�nkaltaisissa dokumenteissa.";

?>