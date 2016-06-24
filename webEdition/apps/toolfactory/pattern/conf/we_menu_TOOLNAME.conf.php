
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
include_once ('meta.conf.php');

$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', '<?= $TOOLNAME; ?>');

$controller = Zend_Controller_Front::getInstance();
$appName = $controller->getParam('appName');

$tool = we_tool_lookup::getToolProperties($appName);
$we_menu_<?= $TOOLNAME; ?>= array(
	100 => array(
		'text' => we_util_Strings::shortenPath($tool['text'], 40),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	),
	200 => array(
		'text' => $translate->_('New'),
		'parent' => 100,
		'perm' => '',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('New Entry'),
		'parent' => 200,
		'cmd' => 'app_' . $appName . '_new',
		'perm' => 'NEW_APP_<?= strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('New Folder'),
		'parent' => 200,
		'cmd' => 'app_' . $appName . '_new_folder',
		'perm' => 'NEW_APP_<?= strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('Save'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_save',
		'perm' => 'EDIT_APP_<?= strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('Delete'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_delete',
		'perm' => 'DELETE_APP_<?= strtoupper($TOOLNAME); ?> || ADMINISTRATOR',
		'enabled' => 1,
	),
	array(
		'parent' => 100, // separator
	),
	array(
		'text' => $translate->_('Close'),
		'parent' => 100,
		'cmd' => 'app_' . $appName . '_exit',
		'perm' => '',
		'enabled' => 1,
	),
	3000 => array(
		'text' => $translate->_('Help'),
		'parent' => 0,
		'perm' => '',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('Help') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_help',
		'perm' => '',
		'enabled' => 1,
	),
	array(
		'text' => $translate->_('Info') . '&hellip;',
		'parent' => 3000,
		'cmd' => 'app_' . $appName . '_info',
		'perm' => '',
		'enabled' => 1,
	)
);