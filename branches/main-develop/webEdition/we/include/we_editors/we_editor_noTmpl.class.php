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
class we_editor_noTmpl extends we_editor_base{

	public function show(){
		require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');
		if(!empty($GLOBALS['we_editmode'])){
			$foo = '<html><head>' .
					($this->we_doc->getElement('Keywords') ?
					we_html_element::htmlMeta(['name' => 'keywords', 'content' => $this->we_doc->getElement('Keywords')]) : '') .
					($this->we_doc->getElement('Charset') ?
					we_html_tools::htmlMetaCtCharset($this->we_doc->getElement('Charset')) : '') .
					($this->we_doc->getElement('Description') ?
					we_html_element::htmlMeta(['name' => 'description', 'content' => $this->we_doc->getElement('Description')]) : '');
		}

		return we_html_element::htmlDocType() . '
<html>
	<head>' .
				(!empty($GLOBALS['we_editmode']) ? STYLESHEET : '') .
				($this->we_doc->getElement('Charset') ? we_html_tools::htmlMetaCtCharset($this->we_doc->getElement('Charset')) : '') .
				($this->we_doc->getElement('Keywords') ? '<meta name="keywords" content="' . $this->we_doc->getElement('Keywords') . '">' : '') .
				($this->we_doc->getElement('Description') ? '<meta name="description" content="' . $this->we_doc->getElement('Description') . '">' : '') .
				'<title>' . $this->we_doc->getElement('Title') . '</title>' .
				(!empty($GLOBALS['we_editmode']) ?
				we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();') . we_editor_script::get() :
				we_tag('textarea', ['name' => 'HEAD'])
				) .
				'</head>' .
				(!empty($GLOBALS['we_editmode']) ? '
		<body style="margin:15px;">
			<form name="we_form" method="post">' . we_class::hiddenTrans() .
				we_html_tools::htmlMessageBox(667, 650, '<pre class="defaultfont">' . oldHtmlspecialchars($foo . we_html_element::htmlTitle($this->we_doc->getElement('Title'))) . '
</pre>
	' . we_tag('textarea', ['name' => 'HEAD', 'rows' => 8, 'cols' => 80, 'wrap' => 'virtual', 'style' => 'width: 600px;']) . '<br/>
<pre class="defaultfont">	&lt;/head&gt;
	&lt;body ' . we_tag('input', ['type' => 'text', 'size' => 60, 'name' => 'BODYTAG', 'style' => 'width: 480px;']) . '&gt;</pre>
' . we_tag('textarea', ['name' => 'BODY', 'rows' => 15, 'cols' => 80, 'wrap' => 'virtual', 'style' => 'width: 600px;']) . '
<pre class="defaultfont">	&lt;/body&gt;
&lt;/html&gt;</pre>') .
				we_html_element::htmlHidden("we_complete_request", 1) .
				'</form>
		</body>' :
				'<body ' . we_tag('input', ['name' => 'BODYTAG']) . '>' .
				printElement(we_tag('textarea', ['name' => 'BODY'], '')) .
				'</body>') .
				'</html>';
	}

}
