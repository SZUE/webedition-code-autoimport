<?php

include_once (dirname(dirname(__FILE__)) . '/../../we/core/autoload.php');

/**
 * @see we_net_Exception
 */
Zend_Loader::loadClass('we_net_Exception');

class we_net_LiveUpdate_Exception extends we_net_Exception
{}
