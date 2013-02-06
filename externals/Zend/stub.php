< ?php
require_once dirname(__FILE__).'/Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);
__HALT_COMPILER();
