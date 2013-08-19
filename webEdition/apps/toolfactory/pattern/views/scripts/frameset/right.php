
$appDir = Zend_Controller_Front::getInstance()->getParam('appDir');

$client = we_ui_Client::getInstance();

$frameset = new we_ui_layout_Frameset();

$params = 	($this->tab ?
				'/tab/' . $this->tab :
				'') .
			($this->sid ?
				'/sid/' . $this->sid :
				'') .
			($this->modelId ?
				'/modelId/' . $this->modelId :
				'');

$controller = $this->modelId ? 'editor/index' . $params : 'home/index/';


	$frameset->setCols('*');
	$frameset->addFrame(array(
		'src' => $appDir . '/index.php/' . $controller,
		'name' => 'editor',
		'noresize' => 'noresize',
		'scrolling' => 'no'
	));

$page = we_ui_layout_HTMLPage::getInstance();
$page->setFrameset($frameset);

echo $page->getHTML();
