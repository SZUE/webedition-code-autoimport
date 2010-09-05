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
 * Language file: thumbnails.inc.php
 * Provides language strings.
 * Language: English
 */

/*****************************************************************************
 * PRELOAD
 *****************************************************************************/

$l_thumbnails["preload"] = "Ladataan asetuksia, hetkinen ...";
$l_thumbnails["preload_wait"] = "Ladataan asetuksia";

/*****************************************************************************
 * SAVE
 *****************************************************************************/

$l_thumbnails["save"] = "Tallennetaan asetuksia, hetkinen ...";
$l_thumbnails["save_wait"] = "Tallennetaan asetuksia";

$l_thumbnails["saved"] = "Asetukset on tallennettu.";
$l_thumbnails["saved_successfully"] = "Asetukset tallennettu";

/*****************************************************************************
 * THUMBNAILS
 *****************************************************************************/

	/**
	 * JAVASCRIPT
	 */

	$l_thumbnails["new"] = "Kirjoita esikatselukuvan nimi!";
	$l_thumbnails["delete_prompt"] = "Poista esikatselukuva \'%s\'! Oletko varma?";

	/**
	 * CAPTION
	 */

	$l_thumbnails["thumbnails"] = "Esikatselukuvat";

	/**
	 * NAME
	 */

	$l_thumbnails["name"] = "Nimi";

	/**
	 * PROPERTIES
	 */

	$l_thumbnails["properties"] = "Ominaisuudet";
	$l_thumbnails["width"] = "Leveys";
	$l_thumbnails["height"] = "Korkeus";
	$l_thumbnails["ratio"] = "S�ilyt� kuvasuhde";
	$l_thumbnails["maximize"] = "Maksimoi koko jos tarvetta";
	$l_thumbnails["interlace"] = "Limitys (interlace) Kyll� / Ei";
	$l_thumbnails["quality"] = "Laatu";

	/**
	 * FORMAT
	 */

	$l_thumbnails["format"] = "Tallennustyyppi";
	$l_thumbnails["format_original"] = "Alkuper�inen tyyppi";
	$l_thumbnails["format_gif"] = "GIF -tiedosto";
	$l_thumbnails["format_jpg"] = "JPEG -tiedosto";
	$l_thumbnails["format_png"] = "PNG -tiedosto";

/*****************************************************************************
 * THUMBNAILS View
 *****************************************************************************/

	$l_thumbnails["add_descriptiontext"] = "Paina plus -painiketta lis�t�ksesi uusia esikatselukuvia.";
	$l_thumbnails["add_description_nogdlib"] = "K�ytt��ksesi esikatselukuva toimintoja gd -kirjasto on asennettava palvelimella. Ota yhteytt� j�rjestelm�n yll�pit�j��n asenuspyynt�� varten.";
	$l_thumbnails["format_not_supported"] = "Asennettu gd- kirjasto ei tue kuvatyypi�. Ota yhteytt� j�rjestelm�n yll�pit�j��n korjataksesi ongelmatilanteen.";
	$l_thumbnails["no_image_uploaded"] = "Kuva on ladattava palvelimelle k�ytt��ksesi esikatselukuva toimintoa.";

	$l_thumbnails["create_thumbnails"] = "Luo esikatselukuvia";
?>