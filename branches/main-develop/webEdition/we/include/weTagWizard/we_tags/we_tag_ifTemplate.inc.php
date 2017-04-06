<?php
/**
 * //NOTE you are inside the constructor of weTagData.class.php
 * webEdition CMS
 *
 * $Rev$
 * $Author$
 * $Date$
*/
$this->NeedsEndTag = true;
$this->Groups[] = 'if_tags';
//$this->Module = '';
$this->Description = g_l('weTag', '[' . $tagName . '][description]', true);

$this->Attributes[] = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('id', TEMPLATES_TABLE, '', false, '') : null);
$this->Attributes[] = //new weTagData_textAttribute('path', false, '');
	(defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('path', TEMPLATES_TABLE, 'text/weTmpl', false, '', true) : null);
$this->Attributes[] = (defined('TEMPLATES_TABLE') ? new we_tagData_selectorAttribute('parentid', TEMPLATES_TABLE, we_tagData_selectorAttribute::FOLDER, false, '') : null);
