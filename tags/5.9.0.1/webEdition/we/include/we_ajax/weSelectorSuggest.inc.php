<?php
/**
 * webEdition CMS
 *
 * LICENSETEXT_CMS
 *
 *
 * @category   webEdition
 * @package    webEdition_base
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.living-e.de/licence     LICENSETEXT_CMS  TODO insert license type and url
 */

header('Content-type: text/plain');

include_once($_SERVER["DOCUMENT_ROOT"]."/webEdition/we/include/we_classes/weSelectorQuery.class.inc.php");

if (!isset($_REQUEST["we_cmd"][1]) || !isset($_REQUEST["we_cmd"][2])) exit();

$selectorSuggest = new weSelectorQuery();
$contentTypes = isset($_REQUEST["we_cmd"][3]) ? explode(",",$_REQUEST["we_cmd"][3]) : null;
$selectorSuggest->search($_REQUEST["we_cmd"][1],$_REQUEST["we_cmd"][2],$contentTypes);
$suggests = $selectorSuggest->getResult();
$return = "";
if (is_array($suggests)) {
	foreach ($suggests as $sug) {
		$return .= $sug['Path'] . "	" . $sug['ID'] . "\n";
	}
}
echo $return;
?>