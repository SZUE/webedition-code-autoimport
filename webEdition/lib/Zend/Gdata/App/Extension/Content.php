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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_App_Extension_Text
 */
require_once 'Zend/Gdata/App/Extension/Text.php';

/**
 * Represents the atom:content element
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_App_Extension_Content extends Zend_Gdata_App_Extension_Text
{

    protected $_rootElement = 'content';
    protected $_src = null;

    public function __construct($text = null, $type = 'text', $src = null)
    {
        parent::__construct($text, $type);
        $this->_src = $src;
    }

    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_src != null) {
            $element->setAttribute('src', $this->_src);
        }
        return $element;
    }

    protected function takeAttributeFromDOM($attribute)
    {
        switch ($attribute->localName) {
        case 'src':
            $this->_src = $attribute->nodeValue;
            break;
        default:
            parent::takeAttributeFromDOM($attribute);
        }
    }

    /**
     * @return Zend_Gdata_App_Extension_Src
     */
    public function getSrc()
    {
        return $this->_src;
    }

    /**
     * @param Zend_Gdata_App_Extension_Src $value
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setSrc($value)
    {
        $this->_src = $value;
        return $this;
    }

}
