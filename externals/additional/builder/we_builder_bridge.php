#!/usr/local/bin/php5-56LATEST-CLI
<?php
$_SERVER['DOCUMENT_ROOT'] = '/kunden/343047_10825/sites/webedition.org/nightlybuilder';
define('NO_SESS', 1);
require($_SERVER['DOCUMENT_ROOT'] . '/conf/we_conf.inc.php');
require($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
require($_SERVER['DOCUMENT_ROOT'] . '/we_builder_configurations.class.php');
$GLOBALS['DB_WE']->query('DELETE FROM ' . ERROR_LOG_TABLE . ' WHERE `Date` < DATE_SUB(NOW(), INTERVAL ' . we_base_constants::ERROR_LOG_HOLDTIME . ' DAY)');

$arguments = getopt("b:t:p:v:");
$configurations = new we_builder_configurations($GLOBALS['DB_WE'], (isset($arguments['b']) ? $arguments['b'] : ''), (isset($arguments['t']) ? $arguments['t'] : ''), (isset($arguments['v']) ? $arguments['v'] : 0));

switch(trim($arguments['p'])){
	case 'isValidBranch':
		echo $configurations->getIsValidBranch() ? 1 : 0;
		break;
	case 'isValidType':
		echo $configurations->getIsValidType() ? 1 : 0;
		break;
	case 'isHotfix':
		echo $configurations->getIsHotfix() ? 1 : 0;
		break;
	case 'isValidHotfixSN':
		echo $configurations->getIsValidHotfixSN() ? 1 : 0;
		break;
	case 'configString':
		t_e('cs', $configurations->getConfigurationString());
		echo $configurations->getConfigurationString();
		break;
	case 'normalizedType':
		echo $configurations->getNormalizedType();
		break;
	case 'targetTakeSnapshot':
	case 'builderCreateTag':
		echo intval($configurations->get($arguments['p']));
	case 'builderVersionsToDelete':
		echo implode(',', $configurations->get($arguments['p']));
		break;
	default:
		echo $configurations->get($arguments['p']);
}
