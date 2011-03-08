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
 * Language file: SEEM.inc.php
 * Provides language strings.
 * Language: English
 */
$l_SEEM = array(
		'ext_doc_selected' => "You have selected a link which points to a document that is not administered by webEdition. Continue?", // TRANSLATE
		'ext_document_on_other_server_selected' => "Ud ha seleccionado un vínculo que apunta a un documento en otro servidor Web.\\nEsto abrirá una nueva ventana del navegador. Continuar?",
		'ext_form_target_other_server' => "Ud está a punto de someter una forma a otro servidor Web.\\nEsto abrirá una nueva ventana del navegador. Continuar? ",
		'ext_form_target_we_server' => "El formulario enviará data a un documento, el cual no es administrado por webEdition.\\nContinuar?",
		'ext_doc' => "El documento actual: <b>%s</b> es <u>no</u> editable con webEdition.",
		'ext_doc_not_found' => "No se pudo encontrar la página selecioanda <b>%s</b>.",
		'ext_doc_tmp' => "Este documento no fue abierto correctamente por webEdition. Por favor, use la navegación normal del sitio Web para alcanzar su documento deseado.",
		'info_ext_doc' => "Sin vínculo webEdition",
		'info_doc_with_parameter' => "Vínculo con parámetro",
		'link_does_not_work' => "El vínculo está desactivado en el modo vista previa. Por favor, use la navegación principal para moverse por la página.",
		'info_link_does_not_work' => "Desactivado.",
		'open_link_in_SEEM_edit_include' => "UD está a punto de cambiar el contenido de la ventana principal de webEdition. Esta ventana se cerrará. Continuar?",
//  Used in we_info.inc.php
		'start_mode' => "Modo",
		'start_mode_normal' => "Normal", // TRANSLATE
		'start_mode_seem' => "seeMode", // TRANSLATE
//	When starting webedition in SEEMode
		'start_with_SEEM_no_startdocument' => "Ningún documento de inicio válido ha sido asignado.\nSu Administrador debe ajustar su documento de inicio.",
		'only_seem_mode_allowed' => "Ud no tiene los permisos requeridos para iniciar webEdition en modo normal.\\nIniciando seeMode ...",
//	Workspace - the SEEM startdocument
		'workspace_seem_startdocument' => "Documento inicio<br>para seeMode",
//	Desired document is locked by another user
		'try_doc_again' => "Intentelo nuevamente",
//	no permission to work with document
		'no_permission_to_work_with_document' => "Ud no tiene permiso para editar este documento.",
//	started seem with no startdocument - can select startdocument.
		'question_change_startdocument' => "Ningún documento de inicio válido ha sido asignado.\\nDesea Ud escoger ahora un documento de inicio en el diálogo Preferencias?",
//	started seem with no startdocument - can select startdocument.
		'no_permission_to_edit_document' => "Ud no tiene permiso para editar este documento.",
		'confirm' => array(
				'change_to_preview' => "Desea Ud cambiar a la vista previa?",
		),
		'alert' => array(
				'changed_include' => "Un archivo adjunto ha sido cambiado. El documento principal es recargado.",
				'close_include' => "Este archivo no es un documento webEdition. La ventana de adjunto es cerrada.",
		),
);