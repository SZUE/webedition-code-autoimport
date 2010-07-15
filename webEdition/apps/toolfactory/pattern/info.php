

// include autoload function
include_once('../../lib/we/core/autoload.php');
// include configuration

include_once('conf/meta.conf.php');

$appName = Zend_Controller_Front::getInstance()->getParam('appName');
$translate = we_core_Local::addTranslation('apps.xml');
we_core_Local::addTranslation('default.xml', $metaInfo['classname']);
we_core_Local::addTranslation('default.xml', 'toolfactory');

$htmlPage = we_ui_layout_Dialog::getInstance();
$htmlPage->addJSFile('/webEdition/js/windows.js');
$htmlPage->addJSFile('/webEdition/js/we_showMessage.js');
$htmlPage->addJSFile('/webEdition/js/images.js');
$htmlPage->addJSFile('/webEdition/js/libs/yui/yahoo-min.js');
$htmlPage->addJSFile('/webEdition/js/libs/yui/event-min.js');
$htmlPage->addJSFile('/webEdition/js/libs/yui/connection-min.js');
$htmlPage->addJSFile('/webEdition/js/libs/yui/json-min.js');
$htmlPage->addJSFile('/webEdition/lib/we/core/JsonRpc.js');


include_once($GLOBALS['__WE_BASE_PATH__']. DIRECTORY_SEPARATOR .'we'. DIRECTORY_SEPARATOR .'include'. DIRECTORY_SEPARATOR.'we_version.php');
$html = '<h2 style="text-align:center">'.$translate->_($metaInfo['name']).'</h2>';
$htmlPage->addHTML($html);
if(!empty($metaInfo['version']) || !empty($metaInfo['minWEversion'])){
		$rowVersion = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Version')));
		$rowVersion->setLeftWidth(100);
		$html = '';
		
		if(!empty($metaInfo['version'])){
			$html .= '<strong>'.we_util_Strings:: number2version($metaInfo['version'],true).'</strong>';
			if(!empty($metaInfo['copyright']) || !empty($metaInfo['copyrighturl'])){
				$html .= ' &copy;';
				if(!empty($metaInfo['copyrighturl'])){
					$html .= ' <a href="http://'.$metaInfo['copyrighturl'].'" target="_blank">';
					$html .=  $metaInfo['copyright'];
					$html .= '</a>';				
				}
			}
			$html .= '<br/>';
		}	
		if(!empty($metaInfo['minWEversion'])){
			$we_version = we_util_Strings::version2number(WE_VERSION,false);
			if ($we_version < $metaInfo['minWEversion']){
				$html .= $translate->_('MinWeVersion').': <strong><span style="color:red">'.we_util_Strings::number2version($metaInfo['minWEversion'],false).'</span></strong><br/> '.$translate->_('AktWeVersion').' <strong>' .WE_VERSION.'</strong>';
			} else {
				$html .= $translate->_('MinWeVersion').': <strong>'.we_util_Strings::number2version($metaInfo['minWEversion'],false).'</strong>';
			}
		}
		if(isset($metaInfo['appdisabled'])){
			$html .= '<br/>'.$translate->_('AppStatus').': <strong>';
			if($metaInfo['appdisabled']){
				$html .= $translate->_('AppStatusDiabled').'</strong>';
			} else {
				$html .= $translate->_('AppStatusActive').'</strong>';
			}
		}
		
		$rowVersion->addHTML($html);
		$tableVersion = new we_ui_layout_HeadlineIconTable();
		$tableVersion->setId('tabVersion');
		$tableVersion->setMarginLeft(30);
		$tableVersion->setRows(array($rowVersion));
		$htmlPage->addElement($tableVersion);
	}
	if (!empty($metaInfo['author']) || !empty($metaInfo['maintainer'])){
		$tableAuthor = new we_ui_layout_HeadlineIconTable();
		$tableAuthor->setId('tabAuthor');
		$tableAuthor->setMarginLeft(30);
		$rowsAuthor=array();	
		if(!empty($metaInfo['author'])){
			$rowAuthor = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Author')));
			$rowAuthor->setLeftWidth(100);
			$rowAuthor->setLine(0);
			$html = $metaInfo['author'];
			if(!empty($metaInfo['authorurl'])){
				$html .= ' <a href="http://'.$metaInfo['authorurl'].'" target="_blank">';
				if(!empty($metaInfo['authorurltext'])){$html .= $metaInfo['authorurltext'];} else {$html .= $metaInfo['authorurl'];}
				$html .= '</a>';
			}
			$rowAuthor->addHTML($html);
			$rowsAuthor[] = $rowAuthor;
		}
		if(!empty($metaInfo['maintainer'])){
			$rowMaintainer = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('Maintainer')));
			$rowMaintainer->setLine(0);
			$rowMaintainer->setLeftWidth(100);
			$html = $metaInfo['maintainer'];
			if(!empty($metaInfo['maintainerurl'])){
				$html .= ' <a href="http://'.$metaInfo['maintainerurl'].'" target="_blank">';
				if(!empty($metaInfo['maintainerurltext'])){$html .= $metaInfo['maintainerurltext'];} else {$html .= $metaInfo['maintainerurl'];}
				$html .= '</a>';
			}
			$rowMaintainer->addHTML($html);
			
			$rowsAuthor[] = $rowMaintainer;
		}
		$tableAuthor->setRows($rowsAuthor);
		$htmlPage->addElement($tableAuthor);
		

	}
	if (!empty($metaInfo['externaltool']) &&  $metaInfo['externaltool']){
		$tableExTool = new we_ui_layout_HeadlineIconTable();
		$tableExTool->setId('tabExTool');
		$tableExTool->setMarginLeft(30);
		$rowsExTool=array();
		$html = '';	
		$rowExTool = new we_ui_layout_HeadlineIconTableRow(array('title' => $translate->_('ExTool')));
		$rowExTool->setLeftWidth(100);
		if(!empty($metaInfo['externaltoolurl'])){
				$html .= ' <a href="http://'.$metaInfo['externaltoolurl'].'" target="_blank">';
				if(!empty($metaInfo['externaltoolname'])){$html .= $metaInfo['externaltoolname'];} else {$html .= $metaInfo['externaltoolurl'];}
				$html .= '</a>';
		}
		if(!empty($metaInfo['externaltoolversion'])){
			$html .= ', '.$translate->_('Version'). ' '.$metaInfo['externaltoolversion'];
		}
		if(!empty($metaInfo['externaltoollicensetype'])){
			$html .= '<br/> '.$translate->_('LicenseType');
			if(!empty($metaInfo['externaltoollicenseurl'])){
				$html .= ' <a href="http://'.$metaInfo['externaltoollicenseurl'].'" target="_blank">';
				if(!empty($metaInfo['externaltoollicensetype'])){$html .= $metaInfo['externaltoollicensetype'];} else {$html .= $metaInfo['externaltoollicenseurl'];}
				$html .= '</a>';		
			}
		}
		$rowExTool->addHTML($html);
		$tableExTool->setRows(array($rowExTool));
		$htmlPage->addElement($tableExTool);
	}


$button = new we_ui_controls_Button();
$button->setText($translate->_('Ok'));
$button->setType('onClick');
$button->setOnClick('top.close()');
$button->setStyle('margin-left:auto;margin-right:auto;');
$htmlPage->addElement($button);

echo $htmlPage->getHTML();


