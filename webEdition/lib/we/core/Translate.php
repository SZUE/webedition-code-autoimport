<?php

/** Zend_Locale */
require_once 'Zend/Translate.php';

class we_core_Translate extends Zend_Translate
{


    /**
     * Adapter
     *
     * @var we_core_Translate_Adapter
     */
    private $_adapter;
    private static $_cache = null;


    /**
     * Generates the standard translation object
     *
     * @param  string              $adapter  Adapter to use
     * @param  array               $data     Translation source data for the adapter
     *                                       Depends on the Adapter
     * @param  string|Zend_Locale  $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL options for the adapter
     * @throws Zend_Translate_Exception
     */
    public function __construct($adapter, $data, $locale = null, array $options = array())
    {
        $this->setAdapter($adapter, $data, $locale, $options);
    }


    /**
     * Sets a new adapter
     *
     * @param  string              $adapter  Adapter to use
     * @param  string|array        $data     Translation data
     * @param  string|Zend_Locale  $locale   OPTIONAL locale to use
     * @param  array               $options  OPTIONAL Options to use
     * @throws Zend_Translate_Exception
     */
    public function setAdapter($adapter, $data, $locale = null, array $options = array())
    {
        switch (strtolower($adapter)) {
            case 'array':
                $adapter = 'Zend_Translate_Adapter_Array';
                break;
            case 'csv':
                $adapter = 'Zend_Translate_Adapter_Csv';
                break;
            case 'gettext':
                $adapter = 'Zend_Translate_Adapter_Gettext';
                break;
            case 'qt':
                $adapter = 'Zend_Translate_Adapter_Qt';
                break;
            case 'tbx':
                $adapter = 'Zend_Translate_Adapter_Tbx';
                break;
            case 'tmx':
                $adapter = 'Zend_Translate_Adapter_Tmx';
                break;
            case 'xliff':
                $adapter = 'Zend_Translate_Adapter_Xliff';
                break;
            case 'xmltm':
                $adapter = 'Zend_Translate_Adapter_XmlTm';
                break;
        }

        @Zend_Loader::loadClass($adapter);
        if (self::$_cache !== null) {
            call_user_func(array($adapter, 'setCache'), self::$_cache);
        }
        $this->_adapter = new $adapter($data, $locale, $options);
        if (!$this->_adapter instanceof Zend_Translate_Adapter) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("Adapter " . $adapter . " does not extend Zend_Translate_Adapter'");
        }
    }

    /**
     * Calls all methods from the adapter
     */
    public function __call($method, array $options)
    {
    	$charset = we_core_Local::getComputedUICharset();

        if (method_exists($this->_adapter, $method)) {
        	$text = call_user_func_array(array($this->_adapter, $method), $options);
        	if(strlen($method)==1 && $method==='_') {
    			if($charset!='UTF-8' && (!isset($options[2]) || (isset($options[2]) && !$options[2]))) {
    				$text = utf8_decode($text);
		    	}
		    }
		    return $text;

        }
        require_once 'Zend/Translate/Exception.php';
        throw new Zend_Translate_Exception("Unknown method '" . $method . "' called!");
    }
}