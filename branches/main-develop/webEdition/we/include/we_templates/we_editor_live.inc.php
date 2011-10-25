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


include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_html_tools.inc.php");
htmlTop();
 print STYLESHEET;
 echo we_htmlElement::jsScript(JS_DIR.'windows.js');?>
<script  type="text/javascript"><!--
var w = new jsWindow("","live",100,100,350,220,true,false);
var d = w.wind.document;
d.open();
d.writeln("TEST");
d.close();

//-->
</script>
</head>
	<body>
	</body>
</html>