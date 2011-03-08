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
$l_validation = array(
		'headline' => 'Validación en línea de este documento',
//  variables for checking html files.
		'description' => 'Puedes seleccionar un servicio de la web para chequear la validez/accesibilidad de este documento',
		'available_services' => 'Servicios Existentes',
		'category' => 'Categoría',
		'service_name' => 'Nombre del servicio',
		'service' => 'Servicio',
		'host' => 'Host', // TRANSLATE
		'path' => 'Path', // TRANSLATE
		'ctype' => 'Tipo del Contenido',
		'ctype' => 'Característica para el servidor destino para determinar el tipo del archivo enviado (texto/html o texto/css)',
		'fileEndings' => 'Extensiones',
		'fileEndings' => 'Insertar todas las extensiones que deben estar disponibles para este servicio. (.html,.css)',
		'method' => 'Método',
		'checkvia' => 'Vía del envío',
		'checkvia_upload' => 'Subir archivo',
		'checkvia_url' => 'Transferir URL',
		'varname' => 'Nombre de la variable',
		'varname' => 'Insertar nombre del identificador de campo del archivo/URL',
		'additionalVars' => 'Parámetros adicionales',
		'additionalVars' => 'Opcional: var1=wert1&var2=wert2&...',
		'result' => 'Resultado',
		'active' => 'Activo',
		'active' => 'Aquí puedes ocultar un servicio temporalmente',
		'no_services_available' => 'No existen servicios disponibles para este tipo de archivo aún.',
//  the different predefined services
		'adjust_service' => 'Ajustar el servicio de validación',
		'art_custom' => 'Servicios personalizados',
		'art_default' => 'Servicios predefinidos',
		'category_xhtml' => '(X)HTML', // TRANSLATE
		'category_links' => 'Vínculo',
		'category_css' => 'Hojas en Estilo de Cascada',
		'category_accessibility' => 'Accessibilidad',
		'edit_service' => array(
				'new' => 'Nuevo servicio',
				'saved_success' => 'El servicio fue guardado.',
				'saved_failure' => 'El servicio no pudo ser guardado.',
				'delete_success' => 'El servicio fue eliminado.',
				'delete_failure' => 'El servicio no pudo ser eliminado.',
				'servicename_already_exists' => 'A service with this name already exists.', // TRANSLATE
		),
//  services for html
		'service_xhtml_upload' => 'Validación (X)HTML de W3C por la vía de subida de archivos',
		'service_xhtml_url' => 'Validación (X)HTML de W3C por la vía de transferencia URL',
//  services for css
		'service_css_upload' => 'Validación del CSS por la vía de subida de archivos',
		'service_css_url' => 'Validación del CSS por la vía de transferencia URL',
		'connection_problems' => '<strong>Ha ocurrido un error mientras se conectaba a este servicio</strong><br /><br />Por favor notar: La opción "transferencia URL" está solamente disponible si su instalación de WebEdition está también accesible desde internet (fuera de su red local). Esto no es posible si WebEdition esta instalado localmente (servidor local).<br /><br />También, algunos problemas pueden ocurrir cuando se usan firewalls y servidores proxy. Por favor chequee su configuración en estos casos.<br /><br />HTTP responso: %s',
);