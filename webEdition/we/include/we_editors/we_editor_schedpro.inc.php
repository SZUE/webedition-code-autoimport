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
if(we_base_moduleInfo::isActive(we_base_moduleInfo::SCHEDULER)){
	we_schedpro::trigger_schedule();
}

echo we_html_tools::getHtmlTop();
require_once(WE_INCLUDES_PATH . 'we_editors/we_editor_script.inc.php');
?>
</head>
<body  class="weEditorBody" onload="checkFooter();doScrollTo();" onunload="doUnload();">
	<form name="we_form" onsubmit="return false"><?php
		echo we_class::hiddenTrans();

		$parts = [];

		foreach($we_doc->schedArr as $i => $sched){
			$schedObj = new we_schedpro($sched, $i);

			$parts[] = ['headline' => '',
				'html' => $schedObj->getHTML($GLOBALS['we_doc']->Table == (defined('OBJECT_FILES_TABLE') ? OBJECT_FILES_TABLE : 'OBJECT_FILES_TABLE')),
			];
		}
		$parts[] = ['headline' => '',
			'html' => we_html_tools::htmlAlertAttentionBox(g_l('modules_schedule', '[descriptiontext]'), we_html_tools::TYPE_INFO, 700) . '<br/><br/>' . we_html_button::create_button('fa:btn_add_schedule,fa-plus,fa-lg fa-clock-o', "javascript:we_cmd('schedule_add')"),
		];
		echo we_html_multiIconBox::getJS() .
		we_schedpro::getMainJS($we_doc) .
		we_html_multiIconBox::getHTML('', $parts, 20) .
		we_html_element::htmlHidden("we_complete_request", 1);
		?>
	</form>
</body>
</html>