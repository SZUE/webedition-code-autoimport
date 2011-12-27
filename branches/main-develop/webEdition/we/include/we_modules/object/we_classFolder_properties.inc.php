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

include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_delete_fn.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/webEdition/we/include/we_inc_min.inc.php');

$javascript = '';

if (isset($_REQUEST['do'])) {
	switch ($_REQUEST['do']) {
		case 'delete':
			$javascript .= $we_doc->deleteObjects();
			break;
		case 'unpublish':
			$javascript .= $we_doc->publishObjects(false);
			break;
		case 'publish':
			$javascript .= $we_doc->publishObjects();
			break;
		case 'unsearchable':
			$javascript .= $we_doc->searchableObjects(false);
			break;
		case 'searchable':
			$javascript .= $we_doc->searchableObjects();
			break;
		case 'copychar':
			$javascript .= $we_doc->copyCharsetfromClass();
			break;
		case 'copyws':
			$javascript .= $we_doc->copyWSfromClass();
			break;
		case 'copytid':
			$javascript .= $we_doc->copyTIDfromClass();
			break;
	}
}

we_html_tools::protect();

// Ausgabe beginnen
we_html_tools::htmlTop();

echo we_html_element::jsScript(JS_DIR.'windows.js');

echo $we_doc->getSearchJS();

if($javascript != '') {
	echo '<script  type="text/javascript">'.$javascript.'</script>';
}

include_once($_SERVER['DOCUMENT_ROOT']."/webEdition/we/include/we_editors/we_editor_script.inc.php");

print STYLESHEET;

echo '</head>

<body class="weEditorBody" onUnload="doUnload()">';


$_parts = array();
$_parts[] = array('html'=>$we_doc->getSearchDialog());
$_parts[] = array('html'=>$we_doc->searchProperties());

echo we_multiIconBox::getHTML('','100%',$_parts,'30','',-1,'','',false);


///////////////////////////////////////////////////////////////////////


echo '

</body>
</html>';
