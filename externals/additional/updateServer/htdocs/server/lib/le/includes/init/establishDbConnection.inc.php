<?php

/**
 * Establish connection to databases containing versioning and register
 * information
 */

// versioning database
$db_versioning_down = false;
$DB_Versioning = & DB::connect($DSN_versioning, $OPTIONS_versioning);
if (PEAR::isError($DB_Versioning)) {
    $db_versioning_down = true;

} else {
	$DB_Versioning->setFetchMode(DB_FETCHMODE_ASSOC);

}

// registration database (old)
$db_register_down = false;
/*
$DB_Register = & DB::connect($DSN_register, $OPTIONS_register);
if (PEAR::isError($DB_Register)) {
    $db_register_down = true;

} else {
	$DB_Register->setFetchMode(DB_FETCHMODE_ASSOC);

}
*/

// customer registration database (new)
/*
$db_customer_down = false;
$DB_Customer = & DB::connect($DSN_customer, $OPTIONS_customer);
if (PEAR::isError($DB_Customer)) {
    $db_customer_down = true;

} else {
	$DB_Customer->setFetchMode(DB_FETCHMODE_ASSOC);

}
*/
?>