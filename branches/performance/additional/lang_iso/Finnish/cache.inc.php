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
 * Language file: enc_we_class.inc.php
 * Provides language strings.
 * Language: English
 */

$l_we_cache["cache"] = "Välimuisti";
$l_we_cache["cache_lifetime"] = "Välimuistin elinaika sekunneissa";
$l_we_cache["cache_type"] = "Välimuistin tyyppi";
$l_we_cache["cache_type_none"] = "ei välimuistia (välimuisti poistettu käytöstä)";
$l_we_cache["cache_type_wetag"] = "we:Tagit välimuistiin (we:Tagit ajetaan välimuistiin)";
$l_we_cache["cache_type_document"] = "Dokumentin oma välimuisti (include-dokumenteilla oma välimuistiasetus)";
$l_we_cache["cache_type_full"] = "Täysi välimuisti (koko dokumentti ajetaan välimuistiin)";

$l_we_cache["delete_cache"] = "Tyhjennetäänkö koko välimuisti?";
$l_we_cache["cache_deleted"] = "Välimuisti tyhjennettiin onnistuneesti!";
$l_we_cache["cache_not_deleted"] = "Välimuistia ei voitu tyhjentää.";

$l_we_cache['cacheLifeTimes'] = array();
$l_we_cache['cacheLifeTimes'][0] = "";
$l_we_cache['cacheLifeTimes'][60] = "1 minuutti";
$l_we_cache['cacheLifeTimes'][300] = "5 minuuttia";
$l_we_cache['cacheLifeTimes'][600] = "10 minuuttia";
$l_we_cache['cacheLifeTimes'][1800] = "30 minuuttia";
$l_we_cache['cacheLifeTimes'][3600] = "1 tunti";
$l_we_cache['cacheLifeTimes'][21600] = "6 tunti";
$l_we_cache['cacheLifeTimes'][43200] = "12 tunti";
$l_we_cache['cacheLifeTimes'][86400] = "1 vuorokausi";

?>