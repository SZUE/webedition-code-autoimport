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
$appName = Zend_Controller_Front::getInstance()->getParam('appName');
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', 'toolfactory');


$activTab = we_base_request::_(we_base_request::STRING, 'activTab', 'idPropertyTab');

$this->inputWidth = 400;

$form = new we_ui_layout_Form(['name' => 'we_form', 'onsubmit' => 'return false', 'method' => 'post']);

$form->addHTML(we_ui_layout_Form::hidden('ID', $this->model->ID));
$form->addHTML(we_ui_layout_Form::hidden('activTab', $activTab));

$rowGeneral = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('General')]);

$labelName = new we_ui_controls_Label();
$labelName->setStyle('display:block;');
$labelName->setText($translate->_('Name') . ($this->model->isRequiredField('Text') ? ' [' . $translate->_('Mandatory field') . ']' : ''));
$inputName = new we_ui_controls_TextField();
$inputName->setName('Text');
if(!empty($this->model->ID)){
	$inputName->setDisabled(true);
}
$inputName->setValue($this->model->Text);
$inputName->setWidth($this->inputWidth);
$inputName->setOnChange('weEventController.fire("docChanged");setClassField(this.value);');
$inputName->setOnBlur('setClassField(this.value);');
$rowGeneral->addElement($labelName);
$rowGeneral->addElement($inputName);

$labelClass = new we_ui_controls_Label();
$labelClass->setStyle('margin-top:20px;display:block;');
$labelClass->setText($translate->_('Name of the model class') . ($this->model->isRequiredField('classname') ? ' [' . $translate->_('Mandatory field') . ']' : ''));
$inputClass = new we_ui_controls_TextField();
$inputClass->setName('classname');
$inputClass->setID('classname');

if(!empty($this->model->ID)){
	$inputClass->setDisabled(true);
}
$inputClass->setValue($this->model->classname);
$inputClass->setWidth($this->inputWidth);
$inputClass->setOnChange('weEventController.fire("docChanged");');
$rowGeneral->addElement($labelClass);
$rowGeneral->addElement($inputClass);

$labelDatasource = new we_ui_controls_Label();
$labelDatasource->setText($translate->_('Datasource') . ($this->model->isRequiredField('datasource') ? ' [' . $translate->_('Mandatory field') . ']' : ''));
$labelDatasource->setStyle('margin-top:20px;display:block;');
if(!empty($this->model->ID)){
	$labelDatasource->setHidden(true);
}
$selectDatasource = new we_ui_controls_Select();
$optDatasource = ['custom:' => 'custom',
	'table:' => $translate->_('Maintable')
];
$selectDatasource->setOptions($optDatasource);
$selectDatasource->setSelectedValue($this->model->datasource);
$selectDatasource->setName('datasource');
$selectDatasource->setWidth($this->inputWidth);
$selectDatasource->setOnChange('weEventController.fire("docChanged");if(this.value==\'table:\') document.getElementById(\'datasourceConf\').style.display=\'block\'; else  document.getElementById(\'datasourceConf\').style.display=\'none\';');
$selectDatasource->setWidth($this->inputWidth);
if(empty($this->model->ID)){
	$rowGeneral->addElement($labelDatasource);
	$rowGeneral->addElement($selectDatasource);
}

$divMaintable = new we_ui_layout_Div();
$divMaintable->setId('datasourceConf');
if(empty($this->model->ID)){
	$divMaintable->setStyle('margin-left: 10px;');
}
if(substr($this->model->datasource, 0, 6) != 'table:'){
	$divMaintable->setHidden(true);
}

$labelMaintable = new we_ui_controls_Label();
$labelMaintable->setText($translate->_('Maintable'));
$labelMaintable->setStyle('margin-top:20px;display:block;');
$inputMaintable = new we_ui_controls_TextField();
$inputMaintable->setName('maintable');
if(!empty($this->model->ID)){
	$inputMaintable->setDisabled(true);
}
$inputMaintable->setValue($this->model->maintable);
$inputMaintable->setWidth($this->inputWidth - 10);
if(!empty($this->model->ID)){
	$inputMaintable->setWidth($this->inputWidth);
}
$inputMaintable->setOnChange('weEventController.fire("docChanged")');
$divMaintable->addElement($labelMaintable);
$divMaintable->addElement($inputMaintable);


$rowGeneral->addElement($divMaintable);

if(empty($this->model->ID)){
	$checkboxMakeTags = new we_ui_controls_Checkbox();
	$checkboxMakeTags->setId('makeTags');
	$checkboxMakeTags->setName('makeTags');
	$checkboxMakeTags->setOnClick('weEventController.fire("docChanged")');
	$checkboxMakeTags->setChecked(($this->model->makeTags) ? true : false);
	$checkboxMakeTags->setValue($this->model->makeTags);
	$checkboxMakeTags->setLabel($translate->_('Create Support for webEdition-Tags and the Pattern-Tag'));
	$checkboxMakeTags->setStyle('margin-top:20px;');
	$rowGeneral->addElement($checkboxMakeTags);

	$checkboxMakeServices = new we_ui_controls_Checkbox();
	$checkboxMakeServices->setId('makeServices');
	$checkboxMakeServices->setName('makeServices');
	$checkboxMakeServices->setOnClick('weEventController.fire("docChanged")');
	$checkboxMakeServices->setChecked(($this->model->makeServices) ? true : false);
	$checkboxMakeServices->setValue($this->model->makeServices);
	$checkboxMakeServices->setLabel($translate->_('Create Support for webEdition-Services and the Pattern-Service'));
	$rowGeneral->addElement($checkboxMakeServices);

	$checkboxMakePerms = new we_ui_controls_Checkbox();
	$checkboxMakePerms->setId('makePerms');
	$checkboxMakePerms->setName('makePerms');
	$checkboxMakePerms->setOnClick('weEventController.fire("docChanged")');
	$checkboxMakePerms->setChecked(($this->model->makePerms) ? true : false);
	$checkboxMakePerms->setValue($this->model->makePerms);
	$checkboxMakePerms->setLabel($translate->_('Create Support for webEdition-Permissions and the Pattern-Permission'));
	$rowGeneral->addElement($checkboxMakePerms);

	$checkboxMakeBackup = new we_ui_controls_Checkbox();
	$checkboxMakeBackup->setId('makeBackup');
	$checkboxMakeBackup->setName('makeBackup');
	$checkboxMakeBackup->setOnClick('weEventController.fire("docChanged")');
	$checkboxMakeBackup->setChecked(($this->model->makeBackup) ? true : false);
	$checkboxMakeBackup->setValue($this->model->makeBackup);
	$checkboxMakeBackup->setLabel($translate->_('Create Support for webEdition-Backupsystem'));
	$rowGeneral->addElement($checkboxMakeBackup);
}

// create div for content of property tab
$propertyTab = new we_ui_layout_Div(['id' => 'idPropertyTab']);

$tableGeneral = new we_ui_layout_HeadlineIconTable();
$tableGeneral->setId('tab_1');
$tableGeneral->setMarginLeft(30);
$tableGeneral->setRows([$rowGeneral]);

$propertyTab->addElement($tableGeneral);

if(!empty($this->model->ID)){
	if($this->model->appconfig){
		if(!empty($this->model->appconfig->info->title)){
			$tableTitle = new we_ui_layout_HeadlineIconTable();
			$tableTitle->setId('tabTitle');
			$tableTitle->setMarginLeft(30);
			$rowsTitle = [];
			$rowTitle = new we_ui_layout_HeadlineIconTableRow(['title' => '']);
			$lang = we_core_Local::getLocale();

			if(!empty($this->model->appconfig->info->title->$lang)){
				$title = $this->model->appconfig->info->title->$lang;
			} else {
				$title = $this->model->appconfig->info->title->de;
			}
			if(!empty($this->model->appconfig->info->description->$lang)){
				$description = $this->model->appconfig->info->description->$lang;
			} else {
				$description = $this->model->appconfig->info->description->de;
			}
			$html = '<strong>' . $title . '</strong><br/>';
			$html .= $description;
			$rowTitle->addHTML($html);
			$rowsTitle[] = $rowTitle;
			$tableTitle->setRows($rowsTitle);
			$propertyTab->addElement($tableTitle);
		}
		if(!empty($this->model->appconfig->creator) || !empty($this->model->appconfig->maintainer)){
			$tableAuthor = new we_ui_layout_HeadlineIconTable();
			$tableAuthor->setId('tabAuthor');
			$tableAuthor->setMarginLeft(30);
			$rowsAuthor = [];
			if(!empty($this->model->appconfig->creator)){
				$cm = $this->model->appconfig->creator;
				$rowAuthor = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Author')]);
				$rowAuthor->setLine(0);
				$html = '';
				if(!empty($cm->company)){
					$html .= '<strong>' . $cm->company . '</strong><br/>';
				}
				if(!empty($cm->authors->author)){
					if(is_object($cm->authors->author)){
						$authornames = $cm->authors->author->toArray();
					} else {
						$authornames = $cm->authors->author;
					}
					if(!empty($cm->authorlinks->www) && is_object($cm->authorlinks->www)){
						$authorlinks = $cm->authorlinks->www->toArray();
					} else {
						$authorlinks = $cm->authorlinks->www;
					}
					if(is_array($authornames)){
						$authorentry = [];
						for($i = 0; $i < count($authornames); $i++){
							$htmla = '';
							if(!empty($authorlinks[$i])){
								$htmla .= '<a href="' . $authorlinks[$i] . '" target="_blank" >';
							}
							$htmla .= $authornames[$i];
							if(!empty($authorlinks[$i])){
								$htmla .= '</a>';
							}
							$authorentry[] = $htmla;
						}
						$html = implode(', ', $authorentry);
					} else {
						$html = '';
						if(!empty($authorlinks)){
							$html .= '<a href="' . $authorlinks . '" target="_blank" >';
						}
						$html .= $authornames;
						if(!empty($authorlinks)){
							$html .= '</a>';
						}
					}
				}
				if(!empty($cm->address)){
					$html .= '<br/>' . $cm->address;
				}
				if(!empty($cm->email)){
					$html .= '<br/><a href="mailto' . $cm->email . '">' . $cm->email . '</a>';
				}
				$rowAuthor->addHTML($html);
				$rowsAuthor[] = $rowAuthor;
			}
			if(!empty($this->model->appconfig->maintainer)){
				$cm = $this->model->appconfig->maintainer;
				$html = '';
				$rowMaintainer = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Maintainer')]);
				$rowMaintainer->setLine(0);
				if(!empty($cm->company)){
					$html .= '<strong>' . $cm->company . '</strong><br/>';
				}
				if(!empty($cm->authors->author)){
					if(is_array($cm->authors->author)){
						$authornames = $cm->authors->author->toArray();
					} else {
						$authornames = $cm->authors->author;
					}
					if(!empty($cm->authorlinks->www) && is_array($cm->authorlinks->www)){
						$authorlinks = $cm->authorlinks->www->toArray();
					} else {
						$authorlinks = $cm->authorlinks->www;
					}
					if(is_array($authornames)){
						$authorentry = [];
						for($i = 0; $i < count($authornames); $i++){
							$htmla = '';
							if(!empty($authorlinks[$i])){
								$htmla .= '<a href="' . $authorlinks[$i] . '" target="_blank" >';
							}
							$htmla .= $authornames[$i];
							if(!empty($authorlinks[$i])){
								$htmla .= '</a>';
							}
							$authorentry[] = $htmla;
						}
						$html .= implode(', ', $authorentry);
					} else {
						$html .= '';
						if(!empty($authorlinks)){
							$html .= '<a href="' . $authorlinks . '" target="_blank" >';
						}
						$html .= $authornames;
						if(!empty($authorlinks)){
							$html .= '</a>';
						}
					}
				}
				if(!empty($cm->address)){
					$html .= '<br/>' . $cm->address;
				}
				if(!empty($cm->email)){
					$html .= '<br/><a href="mailto' . $cm->email . '">' . $cm->email . '</a>';
				}

				$rowMaintainer->addHTML($html);

				$rowsAuthor[] = $rowMaintainer;
			}
			$tableAuthor->setRows($rowsAuthor);
			$propertyTab->addElement($tableAuthor);
		}



		if(!empty($this->model->appconfig->info->version) || !empty($this->model->appconfig->dependencies->version)){
			$rowVersion = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('AppStatus')]);
			$html = '';
			if(!empty($this->model->appconfig->info->version)){
				$html .= '<strong>' . $translate->_('Version') . ': ' . $this->model->appconfig->info->version . '</strong>';
				if(!empty($this->model->appconfig->info->copyright) || !empty($this->model->appconfig->info->copyrighturl)){
					$html .= ' &copy; ';
					if(!empty($this->model->appconfig->info->copyrighturl)){
						$html .= ' <a href="http://' . $this->model->appconfig->info->copyrighturl . '" target="_blank">';
					}
					$html .= $this->model->appconfig->info->copyright;
					if(!empty($this->model->appconfig->info->copyrighturl)){
						$html .= '</a>';
					}
				}
				$html .= '<br/>';
			}
			if(!empty($this->model->appconfig->dependencies->version)){
				$we_version = we_util_Strings::version2number(WE_VERSION, false);
				if($we_version < $this->model->appconfig->dependencies->version){
					$html .= $translate->_('MinWeVersion') . ': <strong><span style="color:red">' . $this->model->appconfig->dependencies->version . '</span></strong> ' . $translate->_('AktWeVersion') . ' <strong>' . WE_VERSION . '</strong>';
				} else {
					$html .= $translate->_('MinWeVersion') . ': <strong>' . $this->model->appconfig->dependencies->version . '</strong>';
				}
			}
			if(!empty($this->model->appconfig->dependencies->sdkversion)){
				$html .= '<br/>' . $translate->_('SdkVersion') . ': <strong>' . $this->model->appconfig->dependencies->sdkversion . '</strong>';
			}
			$html .= '<br/>' . ($this->model->appconfig ?
					$translate->_('The application manifest is available') :
					$translate->_('The application manifest is not available')
				) .
				'<br/>' . ($this->model->appconfig->info->deactivatable === 'true' ?
					$translate->_('The application can be deactivated.') :
					$translate->_('The application can not be deactivated!')
				) .
				'<br/>' . ($this->model->appconfig->info->deinstallable === 'true' ?
					$translate->_('The application is deletable.') :
					$translate->_('The application can not be deleted!')
				) .
				'<br/>' . ($this->model->appconfig->info->updatable === 'true' ?
					$translate->_('The application can be updated.') :
					$translate->_('The application can not be updated.')
				) .
				'<br/>' . $translate->_('AppStatus') . ': <strong>' .
				(!we_app_Common::isActive($this->model->classname) ?
					$translate->_('AppStatusDiabled') :
					$translate->_('AppStatusActive')
				) . '</strong>';

			//$html .= we_util_Strings::p_r($this->model->appconfig,true);
			$rowVersion->addHTML($html);
			$tableVersion = new we_ui_layout_HeadlineIconTable();
			$tableVersion->setId('tabVersion');
			$tableVersion->setMarginLeft(30);
			$tableVersion->setRows([$rowVersion]);
			$propertyTab->addElement($tableVersion);
		}
		if(!empty($this->model->appconfig->thirdparty)){
			$tableExTool = new we_ui_layout_HeadlineIconTable();
			$tableExTool->setId('tabExTool');
			$tableExTool->setMarginLeft(30);
			$rowsExTool = [];
			$html = '';
			$rowExTool = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('ExTool')]);
			if(!empty($this->model->appconfig->thirdparty->www)){
				$html .= ' <a href="' . $this->model->appconfig->thirdparty->www . '" target="_blank">';
				if(!empty($this->model->appconfig->thirdparty->name)){
					$html .= $this->model->appconfig->thirdparty->name;
				} else {
					$html .= $this->model->appconfig->thirdparty->www;
				}
				$html .= '</a>';
			}
			if(!empty($this->model->appconfig->thirdparty->version)){
				$html .= ', ' . $translate->_('Version') . ' ' . $this->model->appconfig->thirdparty->version;
			}
			if(!empty($this->model->appconfig->thirdparty->license)){
				$html .= '<br/> ' . $translate->_('LicenseType') . ' ';
				if(!empty($this->model->appconfig->thirdparty->licenseurl)){
					$html .= ' <a href="' . $this->model->appconfig->thirdparty->licenseurl . '" target="_blank">';
				}
				if(!empty($this->model->appconfig->thirdparty->license)){
					$html .= $this->model->appconfig->thirdparty->license;
				} else {
					$html .= $this->model->appconfig->thirdparty->licenseurl;
				}
				if(!empty($this->model->appconfig->thirdparty->licenseurl)){
					$html .= '</a>';
				}
			}
			$rowExTool->addHTML($html);
			$tableExTool->setRows([$rowExTool]);
			$propertyTab->addElement($tableExTool);
		}
	}
	$rowTags = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Tags')]);
	$html = '';
	foreach($this->model->tags as $tag => $incfile){
		$html .= '<strong>' . $tag . '</strong>' .
			'<br/>' .
			str_replace($_SERVER['DOCUMENT_ROOT'], '', $incfile) .
			'<br/><br/>';
	}
	$rowTags->addHTML($html);
	$tableTags = new we_ui_layout_HeadlineIconTable();
	$tableTags->setId('tabTags');
	$tableTags->setMarginLeft(30);
	$tableTags->setRows([$rowTags]);
	$propertyTab->addElement($tableTags);

	$rowServices = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Services')]);
	$html = '';
	foreach($this->model->services as $service => $incfile){
		$html .= '<strong>' . $service . '</strong>' .
			'<br/>' .
			str_replace($_SERVER['DOCUMENT_ROOT'], '', $incfile) .
			'<br/><br/>';
	}
	$rowServices->addHTML($html);
	$tableServices = new we_ui_layout_HeadlineIconTable();
	$tableServices->setId('tabServices');
	$tableServices->setMarginLeft(30);
	$tableServices->setRows([$rowServices]);
	$propertyTab->addElement($tableServices);

	$rowLanguage = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Language')]);
	$html = '';
	foreach($this->model->languages as $lan => $incfile){
		$html .= '<strong>' . $lan . '</strong>' .
			'<br/>' .
			str_replace($_SERVER['DOCUMENT_ROOT'], '', $incfile) .
			'<br/><br/>';
	}
	$rowLanguage->addHTML($html);
	$tableLanguage = new we_ui_layout_HeadlineIconTable();
	$tableLanguage->setId('tabLanguage');
	$tableLanguage->setMarginLeft(30);
	$tableLanguage->setRows([$rowLanguage]);
	$propertyTab->addElement($tableLanguage);

	$rowPermissions = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Permissions')]);
	$html = '';
	foreach($this->model->permissions as $key => $value){
		$html .= '<strong>' . $key . '</strong>' .
			'<br/>' .
			$translate->_('default') . ':&nbsp;' . $value .
			'<br/><br/>';
	}
	$rowPermissions->addHTML($html);
	$tablePermissions = new we_ui_layout_HeadlineIconTable();
	$tablePermissions->setId('tabPermissions');
	$tablePermissions->setMarginLeft(30);
	$tablePermissions->setRows([$rowPermissions]);
	$propertyTab->addElement($tablePermissions);

	$rowBackupTable = new we_ui_layout_HeadlineIconTableRow(['title' => $translate->_('Backup table')]);
	$html = '';
	foreach($this->model->backupTables as $table){
		$html .= $table .
			'<br/>';
	}
	$rowBackupTable->addHTML($html);
	$tableBackupTable = new we_ui_layout_HeadlineIconTable();
	$tableBackupTable->setId('tabBackupTable');
	$tableBackupTable->setMarginLeft(30);
	$tableBackupTable->setRows([$rowBackupTable]);
	$propertyTab->addElement($tableBackupTable);
}



$form->addElement($propertyTab);

$tabNr = we_base_request::_(we_base_request::INT, 'tabnr', 1);

$htmlPage = we_ui_layout_HTMLPage::getInstance();

$htmlPage->addJSFile(LIB_DIR . 'we/core/JsonRpc.js');


$filenameEmptyMessage = we_util_Strings::quoteForJSString($translate->_('The name must not be empty!'), false);
$filenameEmptyMessageCall = we_core_MessageReporting::getShowMessageCall(
		$filenameEmptyMessage, we_core_MessageReporting::kMessageWarning
);

$classnameEmptyMessage = we_util_Strings::quoteForJSString($translate->_('The name of the model class could not be empty!'), false);
$classnameEmptyMessageCall = we_core_MessageReporting::getShowMessageCall(
		$classnameEmptyMessage, we_core_MessageReporting::kMessageWarning
);

$noTablenameMessage = we_util_Strings::quoteForJSString($translate->_('The tablename is missing.'), false);
$noTablenameMessageCall = we_core_MessageReporting::getShowMessageCall(
		$noTablenameMessage, we_core_MessageReporting::kMessageWarning
);


$js = '

function submitForm(target, action, method) {

	var f = self.document.we_form;
	if (target) {
		f.target = target;
	}

	if (action) {
		f.action = action;
	}

	if (method) {
		f.method = method;
	}

	f.submit();
}


/* update id hidden field */
function __updateIdEventHandler__(data, sender) {
	var form = document.we_form;
	form.ID.value = data.model.ID;
}
weEventController.register("save", __updateIdEventHandler__);


weCmdController.register("save_body", "app_' . $appName . '_save", null, self, function(cmdObj)
{

	var form = document.we_form;

	if (form.Text.value === "") {
		' . $filenameEmptyMessageCall . '
		form.Text.focus();
		form.Text.select();
		return false;
	}

	if (form.classname.value === "") {
		' . $classnameEmptyMessageCall . '
		form.classname.focus();
		form.classname.select();
		return false;
	}

	if (form.datasource.value=="table:" && form.maintable.value=="") {
		' . $noTablenameMessageCall . '
		form.maintable.focus();
		form.maintable.select();
		return false;
	}


	return true;
});

YAHOO.util.Event.addListener(window, "unload", function(e){
	weCmdController.unregister("save_body");
	weEventController.unregister("save", __updateIdEventHandler__);
});

function setClassField(classname) {
	var form = document.we_form;
	var newClassname = classname.toLowerCase();
	newClassname = newClassname.replace(/[^a-z0-9]/g, \'\');
	var firstCharIsNum = false;
	if(newClassname!="") {
		firstCharIsNum = firstCharNum(newClassname);
	}
	if(firstCharIsNum) {
		newClassname = newClassname.replace(/[^a-z]/g, \'\');
	}
	form.classname.value = newClassname;
}

function firstCharNum(str) {
   var numbers = "0123456789";
   var IsNumber=true;
   var char;

   char = str.charAt(0);
   if (numbers.indexOf(char) == -1) {
       IsNumber = false;
   }

   return IsNumber;
}


';

$cssLoadingWheel = '
.weLoadingWheelDiv {
	display:block;
	position:absolute;
	left:0px;
	top:0px;
	width:100%;
	height:100%;
	opacity:0.75;
	filter:alpha(opacity=75);
	background-color:#EDEDED;
	background-position:center center;
	background-repeat:no-repeat;
	text-align:center;
	margin:0px;
	padding:0px;
}
.weLoadingWheel {
	position:absolute;
	top:50%;
	left:50%;
	width:20px;
	height:19px;
}
';

$containerDiv = new we_ui_layout_Div();
$containerDiv->setId('containerDivBody');

$containerDiv->addElement($form);

$htmlPage->addElement($containerDiv);

$htmlPage->addInlineJS($js);
$htmlPage->setBodyAttributes(['class' => 'weEditorBody', 'onload' => 'loaded=1;']);

$htmlPage->addInlineCSS($cssLoadingWheel);


echo $htmlPage->getHTML();
