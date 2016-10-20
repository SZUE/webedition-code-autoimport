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
require_once(WE_INCLUDES_PATH . 'we_tag.inc.php');
echo we_html_element::htmlDocType();
?><html>

	<head><?php
		if(!empty($GLOBALS['we_editmode'])){
			echo STYLESHEET;
		}
		if($we_doc->getElement('Charset')){
			echo we_html_tools::htmlMetaCtCharset($we_doc->getElement('Charset'));
		}
		if($we_doc->getElement('Keywords')){
			?>
			<meta name="keywords" content="<?= $we_doc->getElement('Keywords') ?>">
			<?php
		}
		if($we_doc->getElement('Description')){
			?>
			<meta name="description" content="<?= $we_doc->getElement('Description') ?>">
		<?php } ?>
		<title><?= $we_doc->getElement('Title') ?></title>
		<?php
		if(!empty($GLOBALS['we_editmode'])){
			echo we_html_element::jsScript(JS_DIR . 'global.js', 'initWE();');
			require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
		} else {
			echo we_tag('textarea', ['name' => 'HEAD']);
		}
		?>
	</head>
	<?php if(!empty($GLOBALS['we_editmode'])){ ?>
		<body style="margin:15px;">
			<form name="we_form" method="post"><?php
				echo we_class::hiddenTrans();
				$foo = '<html><head>' .
					($we_doc->getElement('Keywords') ?
						we_html_element::htmlMeta(array('name' => 'keywords', 'content' => $we_doc->getElement("Keywords"))) : '') .
					($we_doc->getElement('Charset') ?
						we_html_tools::htmlMetaCtCharset($we_doc->getElement('Charset')) : '') .
					($we_doc->getElement('Description') ?
						we_html_element::htmlMeta(array('name' => 'description', 'content' => $we_doc->getElement("Description"))) : '');

				$foo = '<pre class="defaultfont">' . oldHtmlspecialchars($foo . we_html_element::htmlTitle($we_doc->getElement('Title'))) . '
</pre>
	' . we_tag('textarea', ['name' => 'HEAD', 'rows' => 8, 'cols' => 80, 'wrap' => 'virtual', 'style' => 'width: 600px;']) . '<br/>
<pre class="defaultfont">	&lt;/head&gt;
	&lt;body ' . we_tag('input', ['type' => 'text', 'size' => 60, 'name' => 'BODYTAG', 'style' => 'width: 480px;']) . '&gt;</pre>
' . we_tag('textarea', ['name' => 'BODY', 'rows' => 15, 'cols' => 80, 'wrap' => 'virtual', 'style' => 'width: 600px;']) . '
<pre class="defaultfont">	&lt;/body&gt;
&lt;/html&gt;</pre>';
				echo we_html_tools::htmlMessageBox(667, 650, $foo) .
				we_html_element::htmlHidden("we_complete_request", 1);
				?>
			</form>
		</body>
	<?php } else { ?>
		<body <?= we_tag('input', ['name' => 'BODYTAG']); ?>>
			<?php printElement(we_tag('textarea', ['name' => 'BODY'], '')); ?>
		</body>
	<?php } ?>
</html>