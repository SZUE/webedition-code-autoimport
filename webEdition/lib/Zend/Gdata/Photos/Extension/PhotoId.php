<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: PhotoId.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * @see Zend_Gdata_Extension
 */
require_once 'Zend/Gdata/Extension.php';

/**
 * @see Zend_Gdata_Photos
 */
require_once 'Zend/Gdata/Photos.php';

/**
 * Represents the gphoto:id element used by the Picasa API.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Photos
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Photos_Extension_PhotoId extends Zend_Gdata_Extension
{

    protected $_rootNamespace = 'gphoto';
    protected $_rootElement = 'id';

    /**
     * Constructs a new Zend_Gdata_Photos_Extension_PhotoId object.
     *
     * @param string $text (optional) The value to represent.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(Zend_Gdata_Photos::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
