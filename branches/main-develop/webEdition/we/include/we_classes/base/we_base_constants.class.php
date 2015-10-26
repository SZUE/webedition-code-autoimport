<?php

/**
 * These should be consts only used INSIDE WE, we should not need to load this class in frontend
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
 * @package constants
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
abstract class we_base_constants{

	const MODE_NORMAL = 'normal';
	const MODE_SEE = 'seem';
	const PING_TIME = 30; // 30 sec
	const PING_TOLERANZ = 170; // allows 4 Ping misses
//define how long Errors hold in DB
	const ERROR_LOG_HOLDTIME = 30; // in days
	const ERROR_LOG_MAX_ITEM_COUNT = 10000;
	const ERROR_LOG_MAX_ITEM_THRESH = 9800;
	const LOGIN_FAILED_TIME = 2; // in minutes
	const LOGIN_FAILED_NR = 3;
	const LOGIN_FAILED_HOLDTIME = 30; // in days
	const FILE_ONLY = 0;
	const FOLDER_ONLY = 1;
	const WE_EDITPAGE_PROPERTIES = 0;
	const WE_EDITPAGE_CONTENT = 1;
	const WE_EDITPAGE_INFO = 2;
	const WE_EDITPAGE_PREVIEW = 3;
	const WE_EDITPAGE_WORKSPACE = 4;
	const WE_EDITPAGE_METAINFO = 5;
	const WE_EDITPAGE_FIELDS = 6;
	const WE_EDITPAGE_SEARCH = 7;
	const WE_EDITPAGE_SCHEDULER = 8;
	const WE_EDITPAGE_THUMBNAILS = 9;
	const WE_EDITPAGE_VALIDATION = 10;
	const WE_EDITPAGE_VARIANTS = 11;
	const WE_EDITPAGE_PREVIEW_TEMPLATE = 12;
	const WE_EDITPAGE_CFWORKSPACE = 13;
	const WE_EDITPAGE_WEBUSER = 14;
	const WE_EDITPAGE_IMAGEEDIT = 15;
	const WE_EDITPAGE_DOCLIST = 16;
	const WE_EDITPAGE_VERSIONS = 17;
	const WE_EDITPAGE_COLLECTION = 18;
//Variants settings
	const WE_VARIANTS_PREFIX = 'we__intern_variant___';
	const WE_VARIANTS_ELEMENT_NAME = 'weInternVariantElement';
	const WE_VARIANT_REQUEST = 'we_variant';

}
