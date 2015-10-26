#!/usr/bin/php
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
 * @package none
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
/*
 * The script makes a rebuild like in the rebuild dialog of webEdition
 * webEdition must be installed
 */

require_once("cliConfig.php");

// CONFIGURATION BEGINS ---------------------------------------------------------

/**
 * Type of Rebuild. Possible Options are:
 *
 * all 			: rebuild all documents and templates
 * templates 	: rebuild all templates
 * static 		: rebuild static documents.
 * objects 		: rebuild all objects
 * navigation 	: rebuild the navigation
 * index		: rebuild the index table
 * thumbnails	: rebuild all thumbnails
 *
 * @var string
 */
$_REQUEST = array(
	'type' => 'all',
	/**
	 * When rebuild type is set to "all", it rewrites
	 * the maintable (tblFile) also.
	 * Only use this if the maintable is broken!
	 *
	 *  @var boolean
	 */
	'rewriteMaintable' => false,
	/**
	 * When rebuild type is set to "all", it rewrites the
	 * temporary table (tblTemporaryDocs) also.
	 * Only use this if the temporary table is broken!
	 *
	 * @var boolean
	 */
	'rewriteTmptable' => false,
	/**
	 * String with comma separated category ids.
	 * If this is set, only documents with the specified
	 * categories will be rebuilt
	 * This is only working when rebuild type is
	 * set to "static"
	 *
	 * @var string
	 */
	'categories' => "",
	/**
	 * Flag if should be an AND instead an OR operation
	 * between the categories.
	 * This is only working when rebuild type is
	 * set to "static"
	 *
	 * @var boolean
	 */
	'catAnd' => false,
	/**
	 * comma separated string with document type ids
	 * If this is set, only documents with the specified
	 * document types will be rebuilt
	 * This is only working when rebuild type is
	 * set to "static"
	 *
	 * @var string
	 */
	'doctypes' => "",
	/**
	 * comma separated string with directory ids.
	 * If this is set, only documents within the specified
	 * directories will be rebuilt
	 * This is only working when rebuild type is
	 * set to "static"
	 *
	 * @var string
	 */
	'directories' => "",
	/**
	 * comma separated string with thumb names to rebuild.
	 *
	 * This needs to be set when rebuild type is
	 * set to "thumbnails"
	 *
	 * @var string
	 */
	'thumbnails' => "",
	/**
	 * If you want to see the output of the script
	 * set this to true;
	 *
	 * @var boolean
	 */
	'verbose' => true,
);

//  END OF OPTIONS


/* #################################### Don't change anything below ############################ */




// we want to see errors
ini_set("display_errors", 1);
error_reporting(E_ALL);

//use we-error handler; ignore if logging is disabled!
require_once ($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we_error_handler.inc.php');
if(!defined('WE_ERROR_SHOW')){
	define('WE_ERROR_SHOW', 1);
}
if(!defined('WE_ERROR_LOG')){
	define('WE_ERROR_LOG', 1);
}

we_error_handler(false);


// knock out identifiation and permissions
$_SESSION['perms'] = array('ADMINISTRATOR' => true);
$_SESSION['user']['Username'] = 1;


if(!isset($_SERVER['SERVER_NAME'])){
	$_SERVER['SERVER_NAME'] = $SERVER_NAME;
}

// include needed libraries
require_once($_SERVER['DOCUMENT_ROOT'] . '/webEdition/we/include/we.inc.php');
update_time_limit(0);
update_mem_limit(128);

// Define exit codes for errors
define('NO_ARGS', 10);
define('INVALID_OPTION', 11);

// Reading the incoming arguments - same as $argv
$args = Console_Getopt::readPHPArgv();


$_cliHelp = 'Usage: rebuild.php [options]
Options:
  -t TYPE, --type=TYPE       Type of rebuild. Possible TYPE values are:
                                * all         : rebuild all documents and templates
                                * templates   : rebuild all templates
                                * static      : rebuild static documents.
                                * objects     : rebuild all objects
                                * navigation  : rebuild the navigation
                                * thumbnails  : rebuild all thumbnails

  -v, --verbose              Verbosely list files processed
  --help                     Prints out this help

 Options to use when type is set to "all":

  --rewriteMaintable         When type is set to "all", it rewrites
                             the maintable (tblFile) also.
                             Only use this if the maintable is broken!

  --rewriteTmptable          When type is set to "all", it rewrites the
                             temporary table (tblTemporaryDocs) also.
                             Only use this if the temporary table is broken!

Options to use when type is set to "static":

  --categories=CATEGORIES    CATEGORIES is a string with comma separated
                             category ids.
                             If this is set, only documents with the specified
                             categories will be rebuilt

  --catAnd                   if set, all specified categories are ANDed.
                             Only documents which have all categories set are rebuilt.

  --doctypes=DOCTYPES        DOCTYPES is a string with comma separated document type ids
                             If this is set, only documents with the specified
                             document types will be rebuilt

  --directories=DIRECTORIES  DIRECTORIES is a string with comma separated directory ids.
                             If this is set, only documents within the specified
                             directories will be rebuilt

Options to use when type is set to "thumbnails":

  --thumbnails=THUMBNAILS    THUMBNAILS is a comma separated string with
                             thumb names to rebuild.
';

// Make sure we got them (for non CLI binaries)
if(PEAR::isError($args)){
	fwrite(STDERR, $args->getMessage() . "\n");
	exit(NO_ARGS);
}

// Short options
$short_opts = 'vt:';

// Long options
$long_opts = array(
	'type=',
	'rewriteMaintable=',
	'rewriteTmptable=',
	'categories=',
	'catAnd',
	'doctypes=',
	'directories=',
	'thumbnails=',
	'verbose',
	'help'
);

// Convert the arguments to options - check for the first argument
if($_SERVER['argv'] && realpath($_SERVER['argv'][0]) == __FILE__){
	$options = Console_Getopt::getOpt($args, $short_opts, $long_opts);
} else {
	$options = Console_Getopt::getOpt2($args, $short_opts, $long_opts);
}

// Check the options are valid
if(PEAR::isError($options)){
	fwrite(STDERR, $options->getMessage() . "\n" . $_cliHelp . "\n");
	exit(INVALID_OPTION);
}

if(!empty($args)){
	$_REQUEST['verbose'] = false;
	$_REQUEST['catAnd'] = false;
	$_REQUEST['rewriteMaintable'] = false;
	$_REQUEST['rewriteTmptable'] = false;
}

foreach($options[0] as $opt){
	switch($opt[0]){
		case '--type':
		case 't':
			$_REQUEST['type'] = $opt[1];
			break;

		case 'v':
		case '--verbose':
			$_REQUEST['verbose'] = true;
			break;

		case '--catAnd':
			$_REQUEST['catAnd'] = true;
			break;

		case '--rewriteMaintable':
			$_REQUEST['rewriteMaintable'] = true;
			break;

		case '--rewriteTmptable':
			$_REQUEST['rewriteTmptable'] = true;
			break;

		case '--help':
			print $_cliHelp;
			exit(0);
			break;

		default:
			$_REQUEST[preg_replace('/^--/', '', $opt[0])] = $opt[1];
	}
}

switch(($type = we_base_request::_(we_base_request::STRING, 'type'))){
	case 'static':
		$_REQUEST['type'] = "filter";
	case 'all':
	case 'templates':
		$data = we_rebuild_base::getDocuments(
				"rebuild_" . $type, $_REQUEST['categories'], $_REQUEST['catAnd'], $_REQUEST['doctypes'], $_REQUEST['directories'], $_REQUEST['rewriteMaintable'], $_REQUEST['rewriteTmptable']
		);
		break;

	case 'objects':
		$data = we_rebuild_base::getObjects();
		break;

	case 'navigation':
		$data = we_rebuild_base::getNavigation();
		break;

	case 'index':
		$data = we_rebuild_base::getIndex();
		break;

	case 'thumbnails':
		$_thumbNames = makeArrayFromCSV($_REQUEST['thumbnails']);
		$_thumbIds = array();
		$db = new DB_WE();
		foreach($_thumbNames as $_thumbName){
			$_thumbIds[] = f('SELECT ID FROM ' . THUMBNAILS_TABLE . " WHERE NAME='" . $db->escape($_thumbName) . "'", '', $db);
		}
		$_thumbIds = implode(',', $_thumbIds);
		$data = we_rebuild_base::getThumbnails($_thumbIds);
		break;

	default:
		print "ERROR: rebuild type is not set!";
}


// start rebuild

foreach($data as $d){
	we_rebuild_base::rebuild($d, $_REQUEST['verbose']);
}
