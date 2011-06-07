<?php
/**
 * reads all sql files of the current directory and writes them into one single file
 * for easier import into a database server
 * 
 * (c) 2008 by Living-E AG
 */

if(!defined("BASEPATH")) define("BASEPATH",dirname(__FILE__));
if(!defined("OUTDIR")) define("OUTDIR",'dump');
if(!defined("OUTFILE")) define("OUTFILE",'complete.sql');
if(!defined("OUTPATH")) define("OUTPATH",dirname(__FILE__).'/'.OUTDIR);

function getfiles() {
	$dir = scandir(BASEPATH);
	$files = array();
	foreach($dir as $entry) {
		if(is_readable(BASEPATH.'/'.$entry) && substr($entry,-4) == ".sql") {
			$files[] = $entry;
		}
	}
	return $files;
}

function dieWithError($text = "") {
	die('<font class="error"><b>ERROR:</b> '.$text.'</font></body></html>');
}

function dieWithWarning($text = "") {
	die('<font class="warning"><b>WARNING:</b> '.$text.'</font></body></html>');
}

?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>webEdition sql dump</title>
	<style type="text/css" media="all">
	body {
		font-family:sans-serif;
		font-size:8pt;
	}
	a {
		color:blue;
	}
	font.error {
		color:red;
		display:block;
		padding:3px;
		border:1px solid red;
	}
	font.warning {
		color:orange;
		display:block;
		padding:3px;
		border:1px solid orange;
	}
	div.list {
		margin-left:20px;
	}
	</style>
</head>

<body>
<h2>dump webEdition sql files into <a href="./<?php echo OUTDIR.'/'.OUTFILE; ?>">one single sql file</a></h2>
<?php

$files = getfiles();
if(empty($files)) {
	dieWithError('No source files found in directory <pre>'.BASEPATH.'</pre>');
}

$outfile = OUTPATH.'/'.OUTFILE;

if(file_exists($outfile) && (!isset($_REQUEST["verified"]) ||  $_REQUEST["verified"] != "yes")) {
	dieWithWarning('Target file <pre>'.$outfile.'</pre> already exists. <a href="?verified=yes">Click here</a>, to recreate this file ...');
}

if(is_file($outfile) && !is_writable($outfile)) {
	dieWithError('Target file <pre>'.$outfile.'</pre> is not writable.');
}

if(file_exists($outfile) && $_REQUEST["verified"] == "yes") {
	@unlink($outfile);
}

echo '<div class="list">';
foreach($files as $entry) {
	$tmpcontent = file_get_contents(BASEPATH."/".$entry);
	// query separator for webEdition sql dump, not needed for pageLogger:
	//$queryseparator = "\n/* query separator */\n";
	$queryseparator = "/* query separator */\n";
	//$queryseparator = "";
	if(!file_put_contents($outfile,$tmpcontent.$queryseparator,FILE_APPEND)) {
		echo('<font class="error">'.$entry.'</font> FAILED.<br />');
	} else {
		echo(''.$entry.' written.<br />');
	}
	$tmpcontent = "";
}
echo '</div>';
?>
<h3>Achtung!</h3>
Kode zur Übername "alter" Werte für das Liveupdate muss manuell aus der Datei entfernt werden.
Dies sind aktuelle die folgenden Zeilen:
<ul><li><pre>
CREATE TEMPORARY TABLE IF NOT EXISTS _newNewsPref(
  pref_name varchar(30) NOT NULL default '',
  pref_value longtext NOT NULL,
  PRIMARY KEY name (pref_name(30))
)ENGINE = MYISAM;
/* query separator */
INSERT IGNORE INTO _newNewsPref SELECT DISTINCT * FROM tblNewsletterPrefs GROUP BY pref_name;
/* query separator */
TRUNCATE tblNewsletterPrefs;
/* query separator */
INSERT INTO tblNewsletterPrefs SELECT * FROM _newNewsPref;
/* query separator */
DROP TEMPORARY TABLE IF EXISTS _newNewsPref;
/* query separator */
</pre></li>
<li><pre>
CREATE TEMPORARY TABLE IF NOT EXISTS _delKeys(
  ID bigint
)ENGINE = MEMORY;
/* query separator */
INSERT INTO _delKeys SELECT s.ID FROM tblsearchtool s, tblsearchtool t WHERE s.Path=t.Path AND s.ID>t.ID;
/* query separator */
DELETE FROM tblsearchtool WHERE ID IN (SELECT ID FROM _delKeys);
/* query separator */
DROP TEMPORARY TABLE IF EXISTS _delKeys;
/* query separator */
</pre></li>
</ul>
</body>
</html>