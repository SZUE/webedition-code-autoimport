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
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * @see Zend_Gdata_Gapps_Query
 */
require_once('Zend/Gdata/Gapps/Query.php');

/**
 * Assists in constructing queries for Google Apps owner entries.
 * Instances of this class can be provided in many places where a URL is
 * required.
 *
 * For information on submitting queries to a server, see the Google Apps
 * service class, Zend_Gdata_Gapps.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gapps
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Gapps_OwnerQuery extends Zend_Gdata_Gapps_Query
{

    /**
     * Group owner is refering to
     *
     * @var string
     */
    protected $_groupId = null;

    /**
     * The email of the owner
     *
     * @var string
     */
    protected $_ownerEmail = null;

    /**
     * Create a new instance.
     *
     * @param string $domain (optional) The Google Apps-hosted domain to use
     *          when constructing query URIs.
     * @param string $groupId (optional) Value for the groupId property.
     * @param string $ownerEmail (optional) Value for the OwnerEmail property.
     */
    public function __construct($domain = null, $groupId = null, $ownerEmail = null)
    {
        parent::__construct($domain);
        $this->setGroupId($groupId);
        $this->setOwnerEmail($ownerEmail);
    }

    /**
     * Set the group id to query for.
     *
     * @see getGroupId
     * @param string $value
     */
    public function setGroupId($value)
    {
        $this->_groupId = $value;
    }

    /**
     * Get the group id to query for.
     *
     * @return string
     *
     */
    public function getGroupId()
    {
        return $this->_groupId;
    }

    /**
     * Set the owner email to query for.
     *
     * @see getOwnerEmail
     * @param string $value
     */
    public function setOwnerEmail($value)
    {
        $this->_ownerEmail = $value;
    }

    /**
     * Get the owner email to query for.
     *
     * @return string
     *
     */
    public function getOwnerEmail()
    {
        return $this->_ownerEmail;
    }

    /**
     * Returns the query URL generated by this query instance.
     *
     * @return string The query URL for this instance.
     */
    public function getQueryUrl()
    {
        $uri = Zend_Gdata_Gapps::APPS_BASE_FEED_URI;
        $uri .= Zend_Gdata_Gapps::APPS_GROUP_PATH;
        $uri .= '/' . $this->_domain;
        if ($this->_groupId !== null) {
            $uri .= '/' . $this->_groupId;
        } else {
            require_once 'Zend/Gdata/App/InvalidArgumentException.php';
            throw new Zend_Gdata_App_InvalidArgumentException(
                    'groupId must not be null');
        }

        $uri .= '/owner';

        if ($this->_ownerEmail !== null) {
            $uri .= '/' . $this->_ownerEmail;
        }

        $uri .= $this->getQueryString();
        return $uri;
    }

}
