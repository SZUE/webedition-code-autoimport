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
		'headline' => 'Online Validation of this document',
//  variables for checking html files.
		'description' => 'You can select a service from the web to check this document for validity/accessibility.',
		'available_services' => 'Existing services',
		'category' => 'Category',
		'service_name' => 'Name of the service',
		'service' => 'Service',
		'host' => 'Host',
		'path' => 'Path',
		'ctype' => 'Content type',
		'ctype' => 'Feature for the target server to determine the type of the submited file (text/html or text/css)',
		'fileEndings' => 'Extensions',
		'fileEndings' => 'Insert all extensions which should be available for this service. (.html,.css)',
		'method' => 'Method',
		'checkvia' => 'Submit via',
		'checkvia_upload' => 'File upload',
		'checkvia_url' => 'URL transfer',
		'varname' => 'Name of variable',
		'varname' => 'Insert name of fieldname of file/url',
		'additionalVars' => 'Additional Parameters',
		'additionalVars' => 'optional: var1=wert1&var2=wert2&...',
		'result' => 'Result',
		'active' => 'Aktive',
		'active' => 'Here you can hide a service temporary.',
		'no_services_available' => 'There are no services available for this filetype yet.',
//  the different predefined services
		'adjust_service' => 'Adjust validation service',
		'art_custom' => 'Custom services',
		'art_default' => 'Predefined services',
		'category_xhtml' => '(X)HTML',
		'category_links' => 'Links',
		'category_css' => 'Cascading Style Sheets',
		'category_accessibility' => 'Accessibility',
		'edit_service' => array(
				'new' => 'New service',
				'saved_success' => 'The service was saved.',
				'saved_failure' => 'The service could not be saved.',
				'delete_success' => 'The service was deleted.',
				'delete_failure' => 'The service could not be deleted.',
				'servicename_already_exists' => 'A service with this name already exists.',
		),
//  services for html
		'service_xhtml_upload' => '(X)HTML validation of W3C via file upload',
		'service_xhtml_url' => '(X)HTML valdiation of W3C via url transfer',
//  services for css
		'service_css_upload' => 'CSS Validation via file-upload',
		'service_css_url' => 'CSS Validation via url transfer',
		'connection_problems' => '<strong>An error occured while connecting to this service</strong><br /><br />Please note: The option "url transfer" is only available if your webEdition installation is also accessible from the internet (outside your local network). This is not possible if webEdition is locally installed (localhost).<br /><br />Also, some problems can occure when using firewalls and proxy-servers. Please check your configuration in such cases.<br /><br />HTTP-Response: %s',
);