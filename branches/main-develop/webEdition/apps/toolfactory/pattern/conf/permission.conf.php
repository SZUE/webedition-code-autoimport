
require_once ($_SERVER['DOCUMENT_ROOT']. '/webEdition/lib/we/core/autoload.inc.php');

$isUTF8 = ($GLOBALS['WE_BACKENDCHARSET'] === 'UTF-8');

$translate = we_core_Local::addTranslation('default.xml', '<?= $TOOLNAME; ?>');

$perm_group_name = "<?= $TOOLNAME; ?>";
$perm_group_title[$perm_group_name] = $isUTF8 ? $translate->_('<?= $TOOLNAME; ?>') : utf8_decode($translate->_('<?= $TOOLNAME; ?>'));

$perm_values[$perm_group_name] = array(
"USE_APP_<?= strtoupper($TOOLNAME); ?>", "NEW_APP_<?= strtoupper($TOOLNAME); ?>", "DELETE_APP_<?= strtoupper($TOOLNAME); ?>", "EDIT_APP_<?= strtoupper($TOOLNAME); ?>", "PUBLISH_APP_<?= strtoupper($TOOLNAME); ?>"
);

$perm_titles[$perm_group_name] = [];

$translated = array(
$translate->_('The user is allowed to use <?= $TOOLNAME; ?>'),
$translate->_('The user is allowed to create new items in <?= $TOOLNAME; ?>'),
$translate->_('The user is allowed to delete items from <?= $TOOLNAME; ?>'),
$translate->_('The user is allowed to edit items <?= $TOOLNAME; ?>'),
$translate->_('The user is allowed to publish items <?= $TOOLNAME; ?>')
);

foreach ($translated as $i => $value) {
$perm_titles[$perm_group_name][$perm_values[$perm_group_name][$i]] = $isUTF8 ? $value : utf8_decode($value);
}

$perm_defaults[$perm_group_name] = array(
"USE_APP_<?= strtoupper($TOOLNAME); ?>" => 1, "NEW_APP_<?= strtoupper($TOOLNAME); ?>" => 1, "DELETE_APP_<?= strtoupper($TOOLNAME); ?>" => 0, "EDIT_APP_<?= strtoupper($TOOLNAME); ?>" => 0, "PUBLISH_APP_<?= strtoupper($TOOLNAME); ?>" => 0
);
