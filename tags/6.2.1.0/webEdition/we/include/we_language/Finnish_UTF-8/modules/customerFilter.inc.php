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

$GLOBALS['l_customerFilter'] = array(
	'mode_off' 					=> 'Suodatin pois käytöstä (kaikilla vierailijoilla on pääsyoikeus)',
	'mode_none' 				=> 'Only not logged in users have access',
	'mode_all' 					=> 'Kaikilla sisäänkirjautuneilla asiakkailla on pääsyoikeus',
	'mode_specific' 			=> 'Vain valituilla asiakkailla on pääsyoikeus',
	'mode_filter' 				=> 'Vain tietyt kriteerit omaavilla asiakkailla on pääsyoikeus',
	'equal'						=> 'sama kuin (=)',
	'not_equal'					=> 'eri kuin (!=)',
	'less'						=> 'pienempi kuin (<)',
	'less_equal'				=> 'pienempi tai yhtäsuuri (<=)',
	'greater'					=> 'suurempi kuin (>)',
	'greater_equal'				=> 'suurempi tai yhtäsuuri (>=)',
	'starts_with'				=> 'alkaa',
	'ends_with'					=> 'päättyy',
	'contains'					=> 'sisältää',
	'in'						=> 'sisältyy',
	'AND'						=> 'JA',
	'OR'						=> 'TAI',
	'black_list'				=> 'Mustalista (näillä asiakkailla ei ole koskaan pääsyoikeutta):',
	'white_list'				=> 'Valkealista (näillä asiakkailla on aina pääsyoikeus):', 
	'documentNoLogin'			=> 'Virhesivu: asiakas ei ole kirjautunut sisään:',
	'documentNoAccess'			=> 'Virhesivu: asiakkaalla ei ole vaadittavia oikeuksia:',
	'accessControlOnErrorDoc' 	=> 'Käytä virhesivuja',
	'accessControlOnTemplate' 	=> 'Virheenkäsittely sivupohjan kautta',
	'accessControl'				=> 'Pääsyoikeuksien hallinta',
	'apply_filter_isHot'		=> 'Asiakassuodattimien käyttöönottamiseksi hakemisto on ensin tallennettava.',
	'apply_filter_info'			=> 'Ota tämän hakemiston suodattimet käyttöön kaikissa alihakemistoissa ja tiedostoissa.',
	'apply_filter'				=> 'Ota asiakassuodattimet käyttöön',
	'apply_filter_done'			=> 'Asiakassuodattimet ovat käytössä.',
	'apply_filter_cofirm_close'	=> 'Useampia alikohteita on auki. Asiakassuodatinten käyttöönottamiseksi kaikki alikohteet on suljettava. Jos jatkat, kaikki alikohteet suljetaan automaattisesti ja tallentamattomat muutokset menetetään.',
	'customerFilter'			=> 'Asiakassuodatin'
);


?>