<?php

	/**
	 * Path Contants
	 *
	 */

	// Path to the living-e Installer
	define("LE_INSTALLER_PATH", str_replace("\\", "/", dirname(dirname(dirname(__FILE__)))));

	// URL to the living-e Installer
	define("LE_URL", ((isset($_SERVER["HTTPS"]) &&  $_SERVER["HTTPS"] == "on") ? "https" :  "http") . "://" . $_SERVER['HTTP_HOST'] . (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] != 80 && substr(strrchr($_SERVER['HTTP_HOST'],":"),1) == "" ? ':' . $_SERVER['SERVER_PORT'] : ''));

	// URL to the living-e Installer
	define("LE_INSTALLER_URL", LE_URL . dirname($_SERVER['SCRIPT_NAME']));

	// Path to the Online Installer
	define("LE_ONLINE_INSTALLER_PATH", LE_INSTALLER_PATH . "/OnlineInstaller");

	// URL to the living-e Installer
	define("LE_ONLINE_INSTALLER_URL", LE_INSTALLER_URL . "/OnlineInstaller");

	// Path to the Online Installer
	define("LE_APPLICATION_INSTALLER_PATH", LE_INSTALLER_PATH . "/ApplicationInstaller");

	// URL to the living-e Installer
	define("LE_APPLICATION_INSTALLER_URL", LE_INSTALLER_URL . "/ApplicationInstaller");

	// Path to the temp dir
	define("LE_INSTALLER_TEMP_PATH", LE_INSTALLER_PATH . "/temp");

	// Adapter URL
	define("LE_INSTALLER_ADAPTER_URL", LE_INSTALLER_URL . "/setup.php");


	/**
	 * Step Contants
	 *
	 */

	// call next step
	define("LE_STEP_NEXT", 1);

	// repeat step, when an error occured
	define("LE_STEP_ERROR", 2);

	// exit installer, no more action possible
	define("LE_STEP_FATAL_ERROR", 3);

	// repeat step - download files
	define("LE_STEP_ITERATE", 4);


	/**
	 * Wizard Type Constants
	 *
	 */

	// Wizard of the online installer
	define("LE_ONLINE_INSTALLER_WIZARD", 1);

	// Wizard of the application installer
	define("LE_APPLICATION_INSTALLER_WIZARD", 2);


?>