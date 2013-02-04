<?php

	// Load library files from online installer
	if(file_exists(LE_ONLINE_INSTALLER_PATH . "/includes/library") && is_dir(LE_ONLINE_INSTALLER_PATH . "/includes/library")) {
		$_handle = opendir(LE_ONLINE_INSTALLER_PATH . "/includes/library");

		while(false !== ($_readdir = readdir($_handle)) ){
			if($_readdir != '.' && $_readdir != '..') {
				$_path = LE_ONLINE_INSTALLER_PATH . '/includes/library/'. $_readdir;
				if (is_file($_path)) {
					require_once($_path);

				}

			}

		}
		closedir($_handle);

	}

	// Load library files from application installer
	if(file_exists(LE_APPLICATION_INSTALLER_PATH . "/includes/library") && is_dir(LE_APPLICATION_INSTALLER_PATH . "/includes/library")) {
		$_handle = opendir(LE_APPLICATION_INSTALLER_PATH . "/includes/library");
		while(false !== ($_readdir = readdir($_handle))){
			if($_readdir != '.' && $_readdir != '..') {
				$_path = LE_ONLINE_INSTALLER_PATH . '/includes/library/'. $_readdir;
				if (is_file($_path)) {
					require_once($_path);

				}

			}

		}
		closedir($_handle);

	}

?>