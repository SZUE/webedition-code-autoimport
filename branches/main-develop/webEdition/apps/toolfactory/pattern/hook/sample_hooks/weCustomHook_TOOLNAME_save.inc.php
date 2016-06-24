
/**
 * if hook execution is enabled this function will be executed
 * - but only if copied to the folder custom_hooks (use we:hookmanagement) -
 * when saving an entry or folder in the application <?= $TOOLNAME; ?>
 * The array $param has all information about the respective entry or folder.
 *
 * @param array $param
 */
function weCustomHook_<?= $TOOLNAME; ?>_save($param) {

	/**
	 * e.g.:
	 *
	 * ob_start("error_log");
	 * print_r($param);
	 * ob_end_clean();
	 */

}