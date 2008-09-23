<?php
/**
 * webEdition CMS
 *
 * LICENSETEXT_CMS
 *
 *
 * @category   webEdition
 * @package    webEdition_toolfactory
 * @copyright  Copyright (c) 2008 living-e AG (http://www.living-e.com)
 * @license    http://www.living-e.de/licence     LICENSETEXT_CMS  TODO insert license type and url
 */

$translate = we_core_Local::addTranslation('apps.xml');

$htmlPage = we_ui_layout_HTMLPage::getInstance();

$propertiesTitle = $translate->_('Properties');

$we_tabs = new we_ui_controls_Tabs(
	array(
		'contentFrame'=>'parent.edbody.',
		'tabs'=>array(
			array(
				'id'=>'idPropertyTab', 
				'text'=>$propertiesTitle, 
				'bottomline' => false,
				'reload'=>true,
				'active'=>true, 
				'title'=>$propertiesTitle,
				'hidden'=>false
			)
		)
	)
);
		
$htmlPage->addJSFile('/webEdition/lib/we/app/js/EditorHeader.js');

$htmlPage->setBodyAttributes(array('class'=>'weEditorHeader', 'onload'=>'setFrameSize()', 'onresize'=>'setFrameSize()'));

$titlePathGroup = htmlspecialchars($this->model->IsFolder ? $translate->_('Folder') : $translate->_('Entry'));
$titlePathName = htmlspecialchars($this->model->Text);

$htmlPage->addHTML(
	'<div id="main">
		<div style="margin:3px 0px 3px 0px;" id="headrow">
			&nbsp;<strong><span id="titlePathGroup">'.
			$titlePathGroup . '</span>:&nbsp;<span id="titlePathName">'. 
			$titlePathName . '</span><div id="mark" style="display: none;">*</div></strong>'.'
		</div>
	');			
		
$htmlPage->addElement($we_tabs);

$htmlPage->addHTML('</div>');

$js = <<<EOS

	weEventController.register("save", function(data, sender) {
		//self.setTitlePath("", data.model.Path);
		self.unmark();
	});

	weEventController.register("docChanged", function(data, sender) {
		var path = "";
		if(typeof(parent.edbody.document.we_form.ParentPath)!="undefined") {
			path = parent.edbody.document.we_form.ParentPath.value + "/";
		}

		path += parent.edbody.document.we_form.Text.value;
		self.setTitlePath("", path.replace(/\/\//,"/"));
		self.mark();
	});

	
EOS;

$htmlPage->addInlineJS($js);

echo $htmlPage->getHTML();
