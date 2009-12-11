<?php
/**
 * webEdition CMS
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

include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we.inc.php");
include_once ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_html_tools.inc.php");

htmlTop();

print STYLESHEET . "\n";

?>
</head>
<body bgcolor="white" marginwidth="0" marginheight="0" leftmargin="0"
	topmargin="0" onBlur="self.close()" onClick="self.close()"
	onload="self.focus();">
<center>
			<?php
			include ($_SERVER["DOCUMENT_ROOT"] . "/webEdition/we/include/" . "we_templates/we_info.inc.php");
			?>
		</center>
</body>
</html>
