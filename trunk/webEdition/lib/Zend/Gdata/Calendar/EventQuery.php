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
 * @subpackage Calendar
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: EventQuery.php 16971 2009-07-22 18:05:45Z mikaelkael $
 */

/**
 * Zend_Gdata_App_util
 */
require_once('Zend/Gdata/App/Util.php');

/**
 * Zend_Gdata_Query
 */
require_once('Zend/Gdata/Query.php');

/**
 * Assists in constructing queries for Google Calendar events
 *
 * @link http://code.google.com/apis/gdata/calendar/
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Calendar
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Calendar_EventQuery extends Zend_Gdata_Query
{

    const CALENDAR_FEED_URI = 'http://www.google.com/calendar/feeds';

    protected $_defaultFeedUri = self::CALENDAR_FEED_URI;
    protected $_comments = null;
    protected $_user = null;
    protected $_visibility = null;
    protected $_projection = null;
    protected $_event = null;

    /**
     * Create Gdata_Calendar_EventQuery object.  If a URL is provided,
     * it becomes the base URL, and additional URL components may be
     * appended.  For instance, if $url is 'http://www.foo.com', the
     * default URL constructed will be 'http://www.foo.com/default/public/full'
     *
     * @param string $url The URL to use as the base path for requests
     */
    public function __construct($url = null)
    {
        parent::__construct($url);
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setComments($value)
    {
        $this->_comments = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setEvent($value)
    {
        $this->_event = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setProjection($value)
    {
        $this->_projection = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setUser($value)
    {
        $this->_user = $value;
        return $this;
    }

    /**
     * @param bool $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setVisibility($value)
    {
        $this->_visibility = $value;
        return $this;
    }

    /**
     * @return string comments
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * @return string event
     */
    public function getEvent()
    {
        return $this->_event;
    }

    /**
     * @return string projection
     */
    public function getProjection()
    {
        return $this->_projection;
    }

    /**
     * @return string user
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return string visibility
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setStartMax($value)
    {
        if ($value != null) {
            $this->_params['start-max'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['start-max']);
        }
        return $this;
    }

    /**
     * @param int $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setStartMin($value)
    {
        if ($value != null) {
            $this->_params['start-min'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['start-min']);
        }
        return $this;
    }

    /**
     * @param string $value
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setOrderBy($value)
    {
        if ($value != null) {
            $this->_params['orderby'] = $value;
        } else {
            unset($this->_params['orderby']);
        }
        return $this;
    }

    /**
     * @return int start-max
     */
    public function getStartMax()
    {
        if (array_key_exists('start-max', $this->_params)) {
            return $this->_params['start-max'];
        } else {
            return null;
        }
    }

    /**
     * @return int start-min
     */
    public function getStartMin()
    {
        if (array_key_exists('start-min', $this->_params)) {
            return $this->_params['start-min'];
        } else {
            return null;
        }
    }

    /**
     * @return string orderby
     */
    public function getOrderBy()
    {
        if (array_key_exists('orderby', $this->_params)) {
            return $this->_params['orderby'];
        } else {
            return null;
        }
    }

    /**
     * @return string sortorder
     */
    public function getSortOrder()
    {
        if (array_key_exists('sortorder', $this->_params)) {
            return $this->_params['sortorder'];
        } else {
            return null;
        }
    }

    /**
     * @return string sortorder
     */
    public function setSortOrder($value)
    {
        if ($value != null) {
            $this->_params['sortorder'] = $value;
        } else {
            unset($this->_params['sortorder']);
        }
        return $this;
    }

    /**
     * @return string recurrence-expansion-start
     */
    public function getRecurrenceExpansionStart()
    {
        if (array_key_exists('recurrence-expansion-start', $this->_params)) {
            return $this->_params['recurrence-expansion-start'];
        } else {
            return null;
        }
    }

    /**
     * @return string recurrence-expansion-start
     */
    public function setRecurrenceExpansionStart($value)
    {
        if ($value != null) {
            $this->_params['recurrence-expansion-start'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['recurrence-expansion-start']);
        }
        return $this;
    }


    /**
     * @return string recurrence-expansion-end
     */
    public function getRecurrenceExpansionEnd()
    {
        if (array_key_exists('recurrence-expansion-end', $this->_params)) {
            return $this->_params['recurrence-expansion-end'];
        } else {
            return null;
        }
    }

    /**
     * @return string recurrence-expansion-end
     */
    public function setRecurrenceExpansionEnd($value)
    {
        if ($value != null) {
            $this->_params['recurrence-expansion-end'] = Zend_Gdata_App_Util::formatTimestamp($value);
        } else {
            unset($this->_params['recurrence-expansion-end']);
        }
        return $this;
    }

    /**
     * @param string $value Also accepts bools.
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function getSingleEvents()
    {
        if (array_key_exists('singleevents', $this->_params)) {
            $value = $this->_params['singleevents'];
            switch ($value) {
                case 'true':
                    return true;
                    break;
                case 'false':
                    return false;
                    break;
                default:
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new Zend_Gdata_App_Exception(
                            'Invalid query param value for futureevents: ' .
                            $value . ' It must be a boolean.');
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $value Also accepts bools. If using a string, must be either "true" or "false".
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setSingleEvents($value)
    {
        if ($value !== null) {
            if (is_bool($value)) {
                $this->_params['singleevents'] = ($value?'true':'false');
            } elseif ($value == 'true' | $value == 'false') {
                $this->_params['singleevents'] = $value;
            } else {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception(
                        'Invalid query param value for futureevents: ' .
                        $value . ' It must be a boolean.');
            }
        } else {
            unset($this->_params['singleevents']);
        }
        return $this;
    }

    /**
     * @return string futureevents
     */
    public function getFutureEvents()
    {
        if (array_key_exists('futureevents', $this->_params)) {
            $value = $this->_params['futureevents'];
            switch ($value) {
                case 'true':
                    return true;
                    break;
                case 'false':
                    return false;
                    break;
                default:
                    require_once 'Zend/Gdata/App/Exception.php';
                    throw new Zend_Gdata_App_Exception(
                            'Invalid query param value for futureevents: ' .
                            $value . ' It must be a boolean.');
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $value Also accepts bools. If using a string, must be either "true" or "false" or
     *                      an exception will be thrown on retrieval.
     * @return Zend_Gdata_Calendar_EventQuery Provides a fluent interface
     */
    public function setFutureEvents($value)
    {
        if ($value !== null) {
            if (is_bool($value)) {
                $this->_params['futureevents'] = ($value?'true':'false');
            } elseif ($value == 'true' | $value == 'false') {
                $this->_params['futureevents'] = $value;
            } else {
                require_once 'Zend/Gdata/App/Exception.php';
                throw new Zend_Gdata_App_Exception(
                        'Invalid query param value for futureevents: ' .
                        $value . ' It must be a boolean.');
            }
        } else {
            unset($this->_params['futureevents']);
        }
        return $this;
    }

    /**
     * @return string url
     */
    public function getQueryUrl()
    {
        if (isset($this->_url)) {
            $uri = $this->_url;
        } else {
            $uri = $this->_defaultFeedUri;
        }
        if ($this->getUser() != null) {
            $uri .= '/' . $this->getUser();
        } else {
            $uri .= '/default';
        }
        if ($this->getVisibility() != null) {
            $uri .= '/' . $this->getVisibility();
        } else {
            $uri .= '/public';
        }
        if ($this->getProjection() != null) {
            $uri .= '/' . $this->getProjection();
        } else {
            $uri .= '/full';
        }
        if ($this->getEvent() != null) {
            $uri .= '/' . $this->getEvent();
            if ($this->getComments() != null) {
                $uri .= '/comments/' . $this->getComments();
            }
        }
        $uri .= $this->getQueryString();
        return $uri;
    }

}
