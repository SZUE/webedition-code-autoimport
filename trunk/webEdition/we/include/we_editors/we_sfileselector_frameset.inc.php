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
if(!$_SESSION['user']['Username']){
	session_id;
}

we_html_tools::protect(array('BROWSE_SERVER', 'SITE_IMPORT', 'ADMINISTRATOR'));
echo we_html_tools::getHtmlTop();

$docroot = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'], '/'));
we_cmd_dec(4);
we_cmd_dec(1);
$cmd1 = weRequest('raw', 'we_cmd', '', 1);


$filter = (isset($_REQUEST['we_cmd'][2]) && $_REQUEST['we_cmd'][2] != '') ? $_REQUEST['we_cmd'][2] : 'all_Types';
$currentDir = str_replace('\\', '/', ( isset($_REQUEST['we_cmd'][3]) ?
		($_REQUEST['we_cmd'][3] == '/' ? '' :
			( parse_url($_REQUEST['we_cmd'][3]) === FALSE && is_dir($docroot . $_REQUEST['we_cmd'][3]) ?
				$_REQUEST['we_cmd'][3] :
				dirname($_REQUEST['we_cmd'][3]))) :
		''));
$currentName = ($filter != 'folder' ? basename(weRequest('file', 'we_cmd', '', 3)) : '');
if(!file_exists($docroot . $currentDir . '/' . $currentName)){
	$currentDir = '';
	$currentName = '';
}

$currentID = $docroot . $currentDir . ($filter == 'folder' || $filter == 'filefolder' ? '' : (($currentDir != '') ? '/' : '') . $currentName);

$currentID = str_replace('\\', '/', $currentID);

$rootDir = ((isset($_REQUEST['we_cmd'][5]) && $_REQUEST['we_cmd'][5] != '') ? $_REQUEST['we_cmd'][5] : '');
?>
<script type="text/javascript"><!--
	var rootDir = "<?php echo $rootDir; ?>";
	var currentID = "<?php echo $currentID; ?>";
	var currentDir = "<?php echo str_replace($rootDir, '', $currentDir); ?>";
	var currentName = "<?php echo $currentName; ?>";
	var currentFilter = "<?php echo str_replace(' ', '%20', g_l('contentTypes', '[' . $filter . ']', true) !== false ? g_l('contentTypes', '[' . $filter . ']') : ''); ?>";
	var filter = '<?php echo $filter; ?>';
	var browseServer = <?php echo $cmd1 ? 'false' : 'true'; ?>

	var currentType = "<?php echo ($filter == 'folder') ? 'folder' : ''; ?>";
	var sitepath = "<?php echo $docroot; ?>";
	var dirsel = 1;
	var scrollToVal = 0;
	var allentries = new Array();

	function exit_close() {
<?php if($cmd1){ ?>
			var foo = (!currentID || (currentID === sitepath) ? "/" : currentID.substring(sitepath.length));

			opener.<?php echo $cmd1; ?> = foo;
			if (!!opener.postSelectorSelect) {
				opener.postSelectorSelect('selectFile');
			}

	<?php
}
if(isset($_REQUEST['we_cmd'][4]) && $_REQUEST['we_cmd'][4] != ""){
	echo $_REQUEST['we_cmd'][4] . ';';
}
?>
		close();
	}

	self.focus();

	function closeOnEscape() {
		return true;

	}
//-->
</script>
<?php
echo we_html_element::jsScript(JS_DIR . 'keyListener.js');
?>
</head>
<frameset rows="73,*,<?php echo (weRequest('bool', 'we_cmd', false, 2) ? 60 : 90); ?>,0" border="0" onload="top.fscmd.selectDir()">
  <frame src="we_sselector_header.php?ret=<?php echo ($cmd1 ? 1 : 0); ?>&filter=<?php echo $filter; ?>&currentDir=<?php echo $currentDir; ?>" name="fsheader" noresize scrolling="no">
	<frame src="<?php echo HTML_DIR; ?>white.html" name="fsbody" noresize scrolling="auto">
	<frame  src="we_sselector_footer.php?ret=<?php echo ($cmd1 ? 1 : 0); ?>&filter=<?php echo $filter; ?>&currentName=<?php echo $currentName; ?>" name="fsfooter" noresize scrolling="no">
	<frame src="we_sselector_cmd.php?ret=<?php echo ($cmd1 ? 1 : 0); ?>&filter=<?php echo $filter; ?>&currentName=<?php echo $currentName; ?>" name="fscmd" noresize scrolling="no">
</frameset>
<body>
</body>
</html>