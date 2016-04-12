

$appDir = Zend_Controller_Front::getInstance()->getParam('appDir');
$appName = Zend_Controller_Front::getInstance()->getParam('appName');

$frameset = new we_ui_layout_Frameset();

$frameset->setRows('32,*,0');
	//$frameset->setOnLoad('start();');

$param = 	($this->tab ?
				'/tab/' . $this->tab :
				'') .
			($this->sid ?
				'/sid/' . $this->sid :
				'') .
			($this->modelId ?
				'/modelId/' . $this->modelId :
				'');

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/header/index',
	'name' => 'header',

	'noresize' => 'noresize'
));

$frameset->addFrame(array(
	'src' => $appDir . '/index.php/frameset/resize' . $param,
	'name' => 'resize',
));

$frameset->addFrame(array(
	'src' => 'about:blank',
	'name' => 'cmd_' . $appName,
	'noresize' => 'noresize'
));

$page = we_ui_layout_HTMLPage::getInstance();
$page->setIsTopFrame(true);
$page->setFrameset($frameset);
$page->addJSFile(LIB_DIR . 'we/core/JsonRpc.js');

$page->addInlineJS($this->getJSTop());

echo $page->getHTML();
