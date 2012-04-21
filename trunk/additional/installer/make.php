<?php
if(substr(phpversion(),0,1) <=4) {
	die("PHP Version 5 or newer required.");
}

class le_OnlineInstaller_Make {
	/**
	 * PHP4 Constructor
	 *
	 * @return le_OnlineInstaller_Make
	 */
	function le_OnlineInstaller_Make() {
		$this->__construct();
	}

	/**
	 * PHP5 Constructor
	 *
	 */
	function __construct() {

	}

	/**
	 * Desctructor
	 *
	 */
	function __destruct() {

	}

	/**
	 * executes the main routine
	 * writes the installer file which includes all files located in
	 * $directory directory and save it to $saveTo directory with the
	 * name $saveTo/OnlineInstaller.php
	 *
	 * @param string $directory
	 * @param string $saveTo
	 * @param string $version
	 * @return unknown
	 */
	function execute($directory, $saveTo, $version) {
		if(is_null($directory)) {
			$directory = "./base";
		} else {
			$directory .= (!eregi("/$", $directory) ? "/" : "");
		}
		if(!is_null($saveTo) || !empty($saveTo)) {
			$saveTo .= !eregi("/$", $saveTo) ? "/" : "";
		}

		$lang['error'] = "An error occured!";
		$lang["file_permissions"] = '<br /><br />In order for webEdition to be installed, the root directory (DOCUMENT_ROOT) must be writable for the web server (Apache, IIS, ..) at least during installation.';
		$lang['dir_create'] = "Cannot create directory '\$directory'.<br />Please check your file access permissions.".$lang["file_permissions"];
		$lang['file_open'] = "Cannot open/create file '\$filename'.<br />Please check your file access permissions.".$lang["file_permissions"];
		$lang['file_write'] = "Cannot write file '\$filename'.<br />Please check your file access permissions.".$lang["file_permissions"];
		$lang['file_save'] = "Cannot save file '\$filename'.<br />Please check your file access permissions.".$lang["file_permissions"];
		$lang['change_perms'] = "Cannot change file permissions of file '\$filename'.";
		
		// first build array with all files
		$files = $this->getDirAsBase64EncodedString($directory,'files',$version);
		$cleanUpContent = $this->getCleanUp();
		$header = $this->getHeader($version);
		$content = <<<EOF
<?php
{$header}

// This array includes the complete installer
{$files}

// check which file extension is requested
\$pathinfo = pathinfo(\$_SERVER['SCRIPT_NAME']);

// file for cleaning up the installer file
\$cleanUp = array(
	'/DeleteMe.php' => '{$cleanUpContent}',
);
\$installerFiles = array_merge(\$cleanUp, \$files);
\$document_root = str_replace("\\\\", "/", dirname(__FILE__));
define("DOCUMENT_ROOT", \$document_root);

// Start the session
@session_start();
@session_destroy();

// write the installer files
foreach(\$installerFiles as \$filename => \$content) {
	
	// decode the content and change extensions
	\$content = str_replace(".php", "." . \$pathinfo['extension'], base64_decode(\$content));
	
	// get the filename with the correct extension
	\$filename = str_replace(".php", "." . \$pathinfo['extension'], '/OnlineInstaller' . \$filename);

	// create needed directories
	\$tmp = pathinfo(\$document_root . \$filename);
	\$directory = \$tmp['dirname'];

	if(!checkMakeDir(\$directory, 0755)) {
		echo getErrorScreen("{$lang['error']}", "{$lang['dir_create']}");
		die();
	}

	// open the file which have to be written
	\$fp = fopen(\$document_root . \$filename, "wb+");
	if(!\$fp) {
		echo getErrorScreen("{$lang['error']}", "{$lang['file_open']}");
		die();
	}

	// put the content into the file
	if(\$content != "") {
		if(!fputs(\$fp, \$content)) {
			@fclose(\$fp);
			echo getErrorScreen("{$lang['error']}", "{$lang['file_write']}");
			die();
		}
	}

	// save the file
	if(!fclose(\$fp)) {
		echo getErrorScreen("{$lang['error']}", "{$lang['file_save']}");
		die();
	}

	// chmod
	if(!chmod(\$document_root . "\$filename", 0755)) {
		echo getErrorScreen("{$lang['error']}", "{$lang['change_perms']}");
		die();
	}
}

function checkMakeDir(\$dirPath, \$mod=0755) {
	
	// open_base_dir - seperate document-root from rest
	if (strpos(\$dirPath, DOCUMENT_ROOT) === 0) {
		\$preDir = DOCUMENT_ROOT;
		\$dir = substr(\$dirPath, strlen(DOCUMENT_ROOT));
	} else {
		\$preDir = '';
		\$dir = \$dirPath;
	}

	\$pathArray = explode('/', \$dir);
	\$path = \$preDir;

	for(\$i=0; \$i<sizeof(\$pathArray); \$i++) {
		\$path .= \$pathArray[\$i];
		if(\$pathArray[\$i] != "" && !is_dir(\$path)) {
			if( !(file_exists(\$path) || mkdir(\$path, \$mod)) ) {
				return false;
			}
		}
		\$path .= "/";
	}
	
	if(!chmod(\$dirPath, \$mod)) {
		return false;
	}
	return true;
}

function getErrorScreen(\$headline, \$content) {
	\$errorScreen = <<<EOIF
<html>
<head>
	<title>Online Installer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<style type="text/css">
	body {
		margin-top			: 100px;
		text-align			: center;
	}
	h1 {
		padding				: 5px;
		width				: 500px;
		color				: #ff0000;
		font-size			: 12px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		font-weight			: bold;
		margin-top			: 4px;
		border-style		: none none solid none;
		border-width		: 1px;
		border-color		: #ff0000;
	}
	p {
		width				: 500px;
		font-size			: 10px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		line-height			: 16px;
	}
	</style>
</head>
<body>
<div align="center">
	<h1>{\$headline}</h1>
	<p>
		{\$content}
	</p>
</div>

</body>
</html>
EOIF;
	return \$errorScreen;
}
\$http = "http" . (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] =='on' ? "s" : "") . "://";
\$host = \$_SERVER['HTTP_HOST'];
\$cleanUp = (dirname(\$_SERVER['SCRIPT_NAME'])!=DIRECTORY_SEPARATOR?dirname(\$_SERVER['SCRIPT_NAME']):"") . "/OnlineInstaller/DeleteMe." . \$pathinfo['extension'];
\$parameters = "";

// Debug mode
if(isset(\$_REQUEST['debug'])) {
	\$parameters .= "debug=".\$_REQUEST['debug']."&";
}

\$pathinfo = pathinfo(__FILE__);
\$OnlineInstallerLog = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'OnlineInstaller.log.php';

// Check if an Installer Log exists --> redirected from binary Installer
if(file_exists(\$OnlineInstallerLog) && is_file(\$OnlineInstallerLog)) {
	require(\$OnlineInstallerLog);

	// if the mode was changed
	if(isset(\$ChangedModFrom)) {
		eval('\$ChangedModFromOctal = 0' . abs(\$ChangedModFrom) .';');
		\$ftpFile = __FILE__;
		\$apacheFile = str_replace(".php", "." . \$pathinfo['extension'], \$document_root . '/OnlineInstaller/DeleteMe.' . \$pathinfo['extension']);

		// check if the ftp user is the same as the apache user
		if(fileowner(\$ftpFile) == fileowner(\$apacheFile)) {
			if(!chmod(\$_SERVER['DOCUMENT_ROOT'], \$ChangedModFromOctal)) {
				echo getErrorScreen("{$lang['error']}", "{$lang['change_perms']}");
				die();
			}
		} else {
			\$_SESSION['leChangedMod'] = \$ChangedModFrom;
		}
	}
}

if(\$parameters != "") {
	\$parameters = "?" . \$parameters;
}
// all files are written, now redirect to the real installer
header("Location: " . \$http . \$host . \$cleanUp . \$parameters);
?>
EOF;
		if(is_null($saveTo)) {
			return $content;
		} else {
			$fp = fopen($saveTo . "OnlineInstaller.php", "wb+");
			if(!$fp) {
				return false;
			}
	
			// put the content into the file
			fputs($fp, $content);
	
			// save the file
			if(!fclose($fp)) {
				return false;
			}
			
			header ("Content-Type: application/octet-stream");
	        header ("Content-Length: " . filesize($saveTo . "OnlineInstaller.php"));
	        header ("Content-Disposition: attachment; filename=OnlineInstaller.php");
			readfile($saveTo . "OnlineInstaller.php");
			return true;
		}
	}

	/**
	 * read all files recursive
	 *
	 * @param string $dirname
	 * @param string $prefix
	 * @return array
	 * @access public
	 * @desc read the content of all files in this directory an return
	 * them as an array with the filename as key and content as value.
	 */
	function readFiles($dirname, $prefix = "",$version="") {
		$files = array();

		if(!file_exists($dirname) || !is_dir($dirname) || stristr($dirname,".svn")) {
			return $files;
		}
		$dirname .= !eregi("/$", $dirname) ? "/" : "";
		$d = dir($dirname);
		while (false !== ($entry = $d->read())) {
			//ignore Tempfiles
			if($entry != '.' && $entry != '..' && substr($entry,-1)!='~' ) {
				if(is_dir($dirname.$entry)) {
					$tmpfiles = self::readFiles($dirname.$entry.'/', $prefix.'/'.$entry,$version);
					$files = array_merge($files, $tmpfiles);
				} else {
					$fp = fopen($dirname.$entry, "rb");
					if($fp) {
						if(filesize($dirname.$entry)==0) {
							$files[$prefix.'/'.$entry] = "";
						} else {
							if($entry == "setup.php") {
								//$files[$prefix.'/'.$entry] = fread($fp, filesize($dirname.$entry));
								$files[$prefix.'/'.$entry] = str_replace("###VERSION###",$version,fread($fp, filesize($dirname.$entry)));
							} else {
								$files[$prefix.'/'.$entry] = fread($fp, filesize($dirname.$entry));
							}
						}
						fclose($fp);
					}
				}
			}
		}
		$d->close();
		return $files;
	}

	/**
	 * get all files as an base64encoded file
	 *
	 * @param string $dirname
	 * @param string $arrayname
	 * @return string
	 * @access public
	 * @desc get a array with all files as an base64encoded as a string
	 * The keys of the array represent the filenames and the values
	 * the base64encoded content
	 */
	function getDirAsBase64EncodedString($dirname, $arrayname = 'files',$version="") {
		$files = self::readFiles($dirname,"",$version);
		$encoded =	"\$$arrayname = array(\n";
		foreach ($files as $filename => $content) {
			$encoded .= "	'$filename' => '" . base64_encode($content) . "',\n";
		}
		$encoded .=		");\n";
		return $encoded;
	}

	/**
	 * get the code for the cleanUp file
	 *
	 * @return string
	 */
	function getCleanUp() {
		$lang['headline']['OnlineInstaller.php'] = "Security Hint!";
		$lang['content']['OnlineInstaller.php'] = "Cannot delete file 'OnlineInstaller.php'.<br />For security reasons delete this file manually.";
		$lang['headline']['OnlineInstaller.log.php'] = "Security Hint!";
		$lang['content']['OnlineInstaller.log.php'] = "Cannot move file 'OnlineInstaller.log.php'.";
		$lang['headline']['OnlineInstallerDeleteMe.php'] = "Security Hint!";
		$lang['content']['OnlineInstallerDeleteMe.php'] = "The file 'OnlineInstallerDeleteMe.php' is not used anymore.<br />For security reasons delete this file manually.";
		$lang['hint'] = "I have read this hint and will continue with the installation";

		$header = $this->getHeader();
		$cleanUp = <<<EOF
<?php
{$header}

// Start the session
session_start();

\$http = "http" . (isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] =='on' ? "s" : "") . "://";
\$host = \$_SERVER['HTTP_HOST'];
\$setup = (dirname(\$_SERVER['SCRIPT_NAME'])!=DIRECTORY_SEPARATOR?dirname(\$_SERVER['SCRIPT_NAME']):"") . "/setup.php";
\$parameters = "";
if(isset(\$_REQUEST['debug'])) {
	\$parameters .= "debug=".\$_REQUEST['debug']."&";
}
if(\$parameters != "") {
	\$parameters = "?" . \$parameters;
}
\$GLOBALS['redirect'] = \$http . \$host . \$setup . \$parameters;
if(file_exists("../OnlineInstaller.php")) {
	@chmod("../OnlineInstaller.php", 0777);

	// Cannot unlink installer file
	if(!@unlink("../OnlineInstaller.php")) {
		echo getErrorScreen("{$lang['headline']['OnlineInstaller.php']}", "{$lang['content']['OnlineInstaller.php']}", true);

	// Cannot unlink installer log file
	} elseif(file_exists("../OnlineInstaller.log.php") && !@rename("../OnlineInstaller.log.php", "../OnlineInstaller/OnlineInstaller.log.php")) {
		echo getErrorScreen("{$lang['headline']['OnlineInstaller.log.php']}", "{$lang['content']['OnlineInstaller.log.php']}", true);

	// all files are written, now redirect to the real installer
	} else {
		header("Location: " . \$http . \$host . \$setup . \$parameters);
	}
} else {
	echo getErrorScreen("{$lang['headline']['OnlineInstallerDeleteMe.php']}", "{$lang['content']['OnlineInstallerDeleteMe.php']}", true);
	die();
}

function getErrorScreen(\$headline, \$content, \$showForm = true) {
	\$form = "";
	if(\$showForm) {
		\$form = <<<EOFORM
<form name="hint" action="{\$GLOBALS['redirect']}" method="get">
<table id="hint">
<tr>
	<td><input type="checkbox" id="read" name="read" value="read" onclick="checkCheckBox(this);"/></td>
	<td><label for="read">{$lang['hint']}</label></td>
</tr>
</table>
</form>
EOFORM;
		\$js = <<<EOFORM
	<script type="text/JavaScript">
	function checkCheckBox(field) {
		if(field.checked) {
			document.forms.hint.submit();
		}
	}
	</script>
EOFORM;
	}
	\$errorScreen = <<<EOIF
<html>
<head>
	<title>Online Installer</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta http-equiv="expires" content="0" />
	<meta http-equiv="pragma" content="no-cache" />
	<style type="text/css">
	body {
		margin-top			: 100px;
		text-align			: center;
	}
	h1 {
		padding				: 5px;
		width				: 500px;
		color				: #ff0000;
		font-size			: 12px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		font-weight			: bold;
		margin-top			: 4px;
		border-style		: none none solid none;
		border-width		: 1px;
		border-color		: #ff0000;
	}
	p {
		width				: 500px;
		font-size			: 10px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		line-height			: 16px;
	}
	#hint {
		width				: 490px;
		cellpadding			: 5px;
		cellspacing			: 5px;
		vertical-align		: middle;
		font-size			: 10px;
		font-family			: Verdana, Arial, Helvetica, sans-serif;
		line-height			: 16px;
	}
	</style>
{\$js}
</head>
<body>
<div align="center">
	<h1>{\$headline}</h1>
	<p>
		{\$content}
	</p>
{\$form}
</div>
</body>
</html>
EOIF;
	return \$errorScreen;
}
?>
EOF;
		return base64_encode($cleanUp);
	}

	/**
	 * get the comment code for the header of each file
	 *
	 * @return string
	 */
	function getHeader($version = "") {
		$HeaderCols = 69;
		$HeaderSpacer = str_repeat("-", $HeaderCols);

		$SoftwareName = "webEdition Online Installer";
		$SoftwareNameFiller = str_repeat(" ", $HeaderCols - strlen($SoftwareName));

		$SoftwareDate = date("r");
		$SoftwareDateFiller = str_repeat(" ", $HeaderCols - strlen($SoftwareDate));
		
		$SoftwareVersion = "Version ".$version;
		$SoftwareVersionFiller = str_repeat(" ", $HeaderCols - strlen($SoftwareVersion));
		
		$PHPVersion = "PHP version 5.2.4 or greater";
		$PHPVersionFiller = str_repeat(" ", $HeaderCols - strlen($PHPVersion));

		$AvailableSoftware = "Applications: webEdition, pageLogger";
		$AvailableSoftwareFiller = str_repeat(" ", $HeaderCols - strlen($AvailableSoftware));
		
		$DefaultSoftware = "Default Application: webEdition";
		$DefaultSoftwareFiller = str_repeat(" ", $HeaderCols - strlen($DefaultSoftware));

		$header = <<<EOF
// +-{$HeaderSpacer}+
// | {$SoftwareName}{$SoftwareNameFiller}|
// +-{$HeaderSpacer}+
// | {$SoftwareDate}{$SoftwareDateFiller}|
// +-{$HeaderSpacer}+
// | {$SoftwareVersion}{$SoftwareVersionFiller}|
// +-{$HeaderSpacer}+
// | {$PHPVersion}{$PHPVersionFiller}|
// +-{$HeaderSpacer}+
// | {$AvailableSoftware}{$AvailableSoftwareFiller}|
// | {$DefaultSoftware}{$DefaultSoftwareFiller}|
// +-{$HeaderSpacer}+
EOF;
		return $header;
	}
}

// code for standalone usage of this script, should be commented out if make.php is not called via http using a web server:
$le_OnlineInstaller = new le_OnlineInstaller_Make();
$le_OnlineInstaller->execute('./base', './out/', '2.7.0.0');
?>
