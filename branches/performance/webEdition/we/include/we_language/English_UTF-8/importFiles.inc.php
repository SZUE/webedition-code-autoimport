<?php

/**
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
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
/**
 * Language file: import_files.inc.php
 * Provides language strings.
 * Language: English
 */
$l_importFiles = array(
		'destination_dir' => "Destination directory",
		'file' => "File",
		'sameName_expl' => "If the filename already exists, what would you like webEdition to do?",
		'sameName_overwrite' => "Overwrite the existing file",
		'sameName_rename' => "Rename the new file",
		'sameName_nothing' => "Do not import the file",
		'sameName_headline' => "What to do<br>if a file exists?",
		'step1' => "Import local files - Step 1 of 2",
		'step2' => "Import local files - Step 2 of 3",
		'step3' => "Import local files - Step 3 of 3",
		'import_expl' => "Click on the button next to the input field to select a file from your harddrive. After the selection a new input field appears and you can select another file. Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Next\", to start the import.",
		'import_expl_jupload' => "With the click on the button you can select more then one file from your harddrive. Alternatively the files can be selected per 'Drag and Drop' from the file manager.  Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Upload Files\", to start the import.",
		'error' => "An error occured during the import process!\\n\\nThe following files could not be imported:\\n%s",
		'finished' => "The import was successful!",
		'import_file' => "Importing file %s",
		'no_perms' => "Error: no permission",
		'move_file_error' => "Error: move_uploaded_file()",
		'read_file_error' => "Error: fread()",
		'php_error' => "Error: upload_max_filesize()",
		'same_name' => "Error: file exists",
		'save_error' => "Error while saving",
		'publish_error' => "Error while publishing",
		'root_dir_1' => "You specified the root directory of the Web server as the source directory. Do you really want to import all contents of the root directory?",
		'root_dir_2' => "You specified the root directory of the Web server as the target directory. Do you really want to import directly into the root directory?",
		'root_dir_3' => "You specified the root directory of the Web server as both the source and the target directory. Do you really want to import the contents of the root directory directly into the root directory?",
		'thumbnails' => "Thumbnails",
		'make_thumbs' => "Create<br>Thumbnails",
		'image_options_open' => "Show image functions",
		'image_options_close' => "Hide image functions",
		'add_description_nogdlib' => "The GD Library has to be installed on your server for the graphic functions to work properly!",
		'noFiles' => "No files exist in the specified source directory which correspond with the given import settings!",
		'emptyDir' => "The source directory is empty!",
		'metadata' => "Meta data",
		'import_metadata' => "Import meta data from file",
);