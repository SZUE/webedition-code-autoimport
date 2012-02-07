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
 * @package    webEdition_base
 * @license    http://www.gnu.org/copyleft/gpl.html  GPL
 */
if(!preg_match('|^([a-f0-9]){32}$|i', $_REQUEST['we_cmd'][1])){
	exit();
}

we_html_tools::protect();

we_html_tools::htmlTop();

$_we_cmd6 = "";
if(isset($_REQUEST['we_cmd'][6])){
	$_we_cmd6 = "&we_cmd[6]=" . $_REQUEST['we_cmd'][6];
}
?>
<script  type="text/javascript"
				 src="<?php print JS_DIR ?>windows.js"></script>
<script  type="text/javascript"><!--
	url = "<?php
print WEBEDITION_DIR;
?>we_cmd.php?we_cmd[0]=save_document&we_cmd[1]=<?php
print $_REQUEST['we_cmd'][1];
?>&we_cmd[2]=1&we_transaction=<?php
print $_REQUEST['we_cmd'][1];
?>&we_cmd[5]=<?php
print $_REQUEST['we_cmd'][5];


print $_we_cmd6;
?>";
		new jsWindow(url,"templateSaveQuestion",-1,-1,400,170,true,false,true);
		//-->
</script>
</head>
<body>
</body>
</html>
