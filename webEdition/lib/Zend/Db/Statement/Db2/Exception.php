<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Zend_Db_Statement_Exception
 */
require_once 'Zend/Db/Statement/Exception.php';

/**
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Joscha Feth <jffeth@de.ibm.com>
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
 */

class Zend_Db_Statement_Db2_Exception extends Zend_Db_Statement_Exception
{
    /**
     * @var string
     */
    protected $code = '00000';

    /**
     * @var string
     */
    protected $message = 'unknown exception';

    /**
     * @param string $msg
     * @param string $state
     */
    function __construct($msg = 'unknown exception', $state = '00000')
    {
        $this->message = $msg;
        $this->code = $state;
    }

}

