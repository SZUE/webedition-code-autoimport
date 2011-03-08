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
		'destination_dir' => "Destination directory", // TRANSLATE
		'file' => "Archivo",
		'sameName_expl' => "Si el nombre del archivo ya existe, qué le gustaría que webEdition hiciese?.",
		'sameName_overwrite' => "Sobrescribir el archivo existente",
		'sameName_rename' => "Renombrar el nuevo archivo",
		'sameName_nothing' => "No importar el archivo",
		'sameName_headline' => "Qué hacer<br>si un archivo existe?",
		'step1' => "Importar archivos locales - Paso 1 de 2",
		'step2' => "Importar archivos locales - Paso 2 de 2",
		'step3' => "Import local files - Step 3 of 3", // TRANSLATE
		'import_expl' => "Clic en el botón proximo al campo de entrada para seleccionar un archivo en su disco duro. Después de la selección aparece un nuevo campo de entrada y Ud puede seleccionar otro archivo. Por favor, note que el tamaño maximo del archivo de  %s no debe ser excedido por las restricciones de PHP!<br><br>Clic en \"Siguiente\", para iniciar la importación.",
		'import_expl_jupload' => "With the click on the button you can select more then one file from your harddrive. Alternatively the files can be selected per 'Drag and Drop' from the file manager.  Please note that the maximum filesize of  %s is not to be exceeded because of restrictions by PHP!<br><br>Click on \"Next\", to start the import.",
		'error' => "Un error ocurre durante el proceso de importación!\\n\\nLos siguientes archivos no pudieron ser importados:\\n%s",
		'finished' => "La importación fue exitosa!",
		'import_file' => "Importando archivos %s",
		'no_perms' => "Error: sin permiso",
		'move_file_error' => "Error: move_uploaded_file()", // TRANSLATE
		'read_file_error' => "Error: fread()", // TRANSLATE
		'php_error' => "Error: upload_max_filesize()", // TRANSLATE
		'same_name' => "Error: el archivo ya existe",
		'save_error' => "Error mientras salvando",
		'publish_error' => "Error mientras publicando",
		'root_dir_1' => "Ud especificó el directorio raíz del servidor Web como el directorio original. Desea Ud realmente importar todo el contenido del directorio raíz?",
		'root_dir_2' => "Ud especificó el directorio raíz del servidor Web como el directorio objetivo. Desea Ud realmente importar directamente al directorio raíz?",
		'root_dir_3' => "Ud especificó el directorio raíz del servidor Web como ambos el directorio original y  el directorio objetivo. Desea Ud realmente importar todo el contenido del directorio raíz directamente al directorio raíz?",
		'thumbnails' => "Imagenes en miniatura",
		'make_thumbs' => "Crear<br>Imagenes en miniatura",
		'image_options_open' => "Mostrar opciones de imagen",
		'image_options_close' => "Ocultar opciones de imagen",
		'add_description_nogdlib' => "Para tener las funciónes gráficas a su disposición tiene que instalar la 'GD Library' en el servidor!",
		'noFiles' => "No files exist in the specified source directory which correspond with the given import settings!", // TRANSLATE
		'emptyDir' => "The source directory is empty!", // TRANSLATE

		'metadata' => "Meta data", // TRANSLATE
		'import_metadata' => "Import meta data from file", // TRANSLATE
);