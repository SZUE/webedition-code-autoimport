<?php


/*
 * Language-Limit
 */
define('LANGUAGELIMIT', '6300');

// set the shared template dir
define("SHARED_TEMPLATE_DIR", SHARED_DIR . '/includes/templates');

// set the shared language dir
define('SHARED_LANGUAGE_DIR', SHARED_DIR . '/includes/languages');

// set the shared language dir
define('SHARED_DOWNLOAD_DIR', SHARED_DIR . '/download');


/**
 * Names of tables
 */

// DB_Versioning
define('VERSION_TABLE', 'v6_versions');
define('SOFTWARE_TABLE', 'v6_changes');
define('SOFTWARE_LANGUAGE_TABLE', 'v6_changes_language');
define('UPDATELOG_TABLE','we_updatelog');
define('INSTALLLOG_TABLE','we_installlog');

// DB_Register
define('STOCK_TABLE', 'we');
define('INSTALLATION_TABLE', 'domains');
define('CUSTOMER_TABLE', 'tblCustomer');
define('INSTALLATIONLOG_TABLE', 'we6_installation_log');


/**
 * Special stuff for modules
 */
define('MODULES_INFORMATION', 'v5_modules_general');
define('MODULES_TRANSLATIONS', 'v5_modules');


/*
 * Needed constants and parameters
 */

define('VERSIONNUMBER_LENGTH', 4);

define("DOWNLOAD_KBYTES_PER_STEP", 750);

define('EXECUTE_QUERIES_PER_STEP', 15);

define('PREPARE_FILES_PER_STEP', 200);

?>