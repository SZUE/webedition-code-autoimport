<?php
/**
 * webEdition CMS
 *
 * $Rev: 12131 $
 * $Author: mokraemer $
 * $Date: 2016-05-20 01:10:39 +0200 (Fr, 20. Mai 2016) $
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
echo we_html_tools::getHtmlTop() .
 STYLESHEET;
$we_transaction = we_base_request::_(we_base_request::TRANSACTION, 'we_cmd', we_base_request::_(we_base_request::TRANSACTION, 'we_transaction'), 2);
?>
</head>
<body class="weEditorBody">
	<?php
	$tp = new we_tag_tagParser($GLOBALS['we_doc']->getTemplateCode(true));
	$relevantTags = array();
	//FIXME: we need to get the names of blocks
	$context = '';
	foreach($tp->getTagsWithAttributes() as $tag){
		if(!empty($tag['attribs']['name'])){
			$name = $tag['attribs']['name'];
			$nHash = md5($name);
			if(isset($relevantTags[$nHash])){
				$relevantTags[$nHash]['types'][$tag['name']] = 1;
			} else {
				$relevantTags[$nHash] = array(
					'name' => $name,
					'types' => array($tag['name'] => 1),
				);
			}
		}
	}

	$db = $GLOBALS['DB_WE'];
	$allFields = $db->getAllq('SELECT l.Type,l.Name,IF(c.BDID,c.BDID,c.Dat) AS content FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $GLOBALS['we_doc']->ID . ') AND l.Type!="attrib" GROUP BY l.nHash');

	$obsolete = $db->getAllq('SELECT l.Type,l.Name,IF(c.BDID,c.BDID,c.Dat) AS content FROM ' . LINK_TABLE . ' l JOIN ' . CONTENT_TABLE . ' c ON l.CID=c.ID WHERE DID IN (SELECT ID FROM ' . FILE_TABLE . ' WHERE TemplateID=' . $GLOBALS['we_doc']->ID . ') AND l.Type!="attrib" AND nHash NOT IN (x\'' . implode('\',x\'', array_keys($relevantTags)) . '\') GROUP BY l.nHash');

	$parts = array(
		array(
			'headline' => 'Gefundene Elemente',
			'html' => '<pre>' . print_r($relevantTags, true) . '</pre>',
			'space' => 140,
		),
		array(
			'headline' => 'Elemente in DB',
			'html' => '<pre>' . print_r($allFields, true) . '</pre>',
			'space' => 140,
		),
		array(
			'headline' => 'Obsolete Elemente',
			'html' => '<pre>' . print_r($obsolete, true) . '</pre>',
			'space' => 140,
		)
	);


	echo we_html_multiIconBox::getHTML('', $parts, 20, '', -1, '', '', false);
	?>
</body>
</html>
