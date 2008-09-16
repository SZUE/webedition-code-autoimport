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
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Consumer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: File.php,v 1.1 2008/05/13 13:41:31 holger.meyer Exp $
 */

/**
 * @see Zend_OpenId_Consumer_Storage
 */
require_once "Zend/OpenId/Consumer/Storage.php";

/**
 * External storage implemmentation using serialized files
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend_OpenId_Consumer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_OpenId_Consumer_Storage_File extends Zend_OpenId_Consumer_Storage
{

    /**
     * Directory name to store data files in
     *
     * @var string $_dir
     */
    private $_dir;

    /**
     * Constructs storage object and creates storage directory
     *
     * @param string $dir directory name to store data files in
     * @throws Zend_OpenId_Exception
     */
    public function __construct($dir = null)
    {
        if (is_null($dir)) {
            $tmp = getenv('TMP');
            if (empty($tmp)) {
                $tmp = getenv('TEMP');
                if (empty($tmp)) {
                    $tmp = "/tmp";
                }
            }
            $user = get_current_user();
            if (is_string($user) && !empty($user)) {
                $tmp .= '/' . $user;
            }
            $dir = $tmp . '/openid/consumer';
        }
        $this->_dir = $dir;
        if (!is_dir($this->_dir)) {
            if (!@mkdir($this->_dir, 0700, 1)) {
                throw new Zend_OpenId_Exception(
                    'Cannot access storage directory ' . $dir,
                    Zend_OpenId_Exception::ERROR_STORAGE);
            }
        }
        if (($f = fopen($this->_dir.'/assoc.lock', 'w+')) === null) {
            throw new Zend_OpenId_Exception(
                'Cannot create a lock file in the directory ' . $dir,
                Zend_OpenId_Exception::ERROR_STORAGE);
        }
        fclose($f);
        if (($f = fopen($this->_dir.'/discovery.lock', 'w+')) === null) {
            throw new Zend_OpenId_Exception(
                'Cannot create a lock file in the directory ' . $dir,
                Zend_OpenId_Exception::ERROR_STORAGE);
        }
        fclose($f);
        if (($f = fopen($this->_dir.'/nonce.lock', 'w+')) === null) {
            throw new Zend_OpenId_Exception(
                'Cannot create a lock file in the directory ' . $dir,
                Zend_OpenId_Exception::ERROR_STORAGE);
        }
        fclose($f);
    }

    /**
     * Stores information about association identified by $url/$handle
     *
     * @param string $url OpenID server URL
     * @param string $handle assiciation handle
     * @param string $macFunc HMAC function (sha1 or sha256)
     * @param string $secret shared secret
     * @param long $expires expiration UNIX time
     * @return bool
     */
    public function addAssociation($url, $handle, $macFunc, $secret, $expires)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
        $lock = @fopen($this->_dir . '/assoc.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name1, 'w+');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $data = serialize(array($url, $handle, $macFunc, $secret, $expires));
        fwrite($f, $data);
        if (function_exists('symlink')) {
            symlink($name1, $name2);
        } else {
            $f2 = @fopen($name2, 'w+');
            if ($f2) {
                fwrite($f2, $data);
                fclose($f2);
            }
        }
        fclose($f);
        fclose($lock);
        return true;
    }

    /**
     * Gets information about association identified by $url
     * Returns true if given association found and not expired and false
     * otherwise
     *
     * @param string $url OpenID server URL
     * @param string &$handle assiciation handle
     * @param string &$macFunc HMAC function (sha1 or sha256)
     * @param string &$secret shared secret
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getAssociation($url, &$handle, &$macFunc, &$secret, &$expires)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        $lock = @fopen($this->_dir . '/assoc.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name1, 'r');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $ret = false;
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedUrl, $handle, $macFunc, $secret, $expires) = unserialize($data);
            if ($url === $storedUrl && $expires > time()) {
                $ret = true;
            } else {
                $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
                fclose($f);
                @unlink($name2);
                @unlink($name1);
                fclose($lock);
                return false;
            }
        }
        fclose($f);
        fclose($lock);
        return $ret;
    }

    /**
     * Gets information about association identified by $handle
     * Returns true if given association found and not expired and false
     * otherwise
     *
     * @param string $handle assiciation handle
     * @param string &$url OpenID server URL
     * @param string &$macFunc HMAC function (sha1 or sha256)
     * @param string &$secret shared secret
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getAssociationByHandle($handle, &$url, &$macFunc, &$secret, &$expires)
    {
        $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
        $lock = @fopen($this->_dir . '/assoc.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name2, 'r');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $ret = false;
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($url, $storedHandle, $macFunc, $secret, $expires) = unserialize($data);
            if ($handle === $storedHandle && $expires > time()) {
                $ret = true;
            } else {
                fclose($f);
                @unlink($name2);
                $name1 = $this->_dir . '/assoc_url_' . md5($url);
                @unlink($name1);
                fclose($lock);
                return false;
            }
        }
        fclose($f);
        fclose($lock);
        return $ret;
    }

    /**
     * Deletes association identified by $url
     *
     * @param string $url OpenID server URL
     * @return bool
     */
    public function delAssociation($url)
    {
        $name1 = $this->_dir . '/assoc_url_' . md5($url);
        $lock = @fopen($this->_dir . '/assoc.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name1, 'r');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedUrl, $handle, $macFunc, $secret, $expires) = unserialize($data);
            if ($url === $storedUrl) {
                $name2 = $this->_dir . '/assoc_handle_' . md5($handle);
                fclose($f);
                @unlink($name2);
                @unlink($name1);
                fclose($lock);
                return true;
            }
        }
        fclose($f);
        fclose($lock);
        return true;
    }

    /**
     * Stores information discovered from identity $id
     *
     * @param string $id identity
     * @param string $realId discovered real identity URL
     * @param string $server discovered OpenID server URL
     * @param float $version discovered OpenID protocol version
     * @param long $expires expiration UNIX time
     * @return bool
     */
    public function addDiscoveryInfo($id, $realId, $server, $version, $expires)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        $lock = @fopen($this->_dir . '/discovery.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name, 'w+');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $data = serialize(array($id, $realId, $server, $version, $expires));
        fwrite($f, $data);
        fclose($f);
        fclose($lock);
        return true;
    }

    /**
     * Gets information discovered from identity $id
     * Returns true if such information exists and false otherwise
     *
     * @param string $id identity
     * @param string &$realId discovered real identity URL
     * @param string &$server discovered OpenID server URL
     * @param float &$version discovered OpenID protocol version
     * @param long &$expires expiration UNIX time
     * @return bool
     */
    public function getDiscoveryInfo($id, &$realId, &$server, &$version, &$expires)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        $lock = @fopen($this->_dir . '/discovery.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name, 'r');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        $ret = false;
        $data = stream_get_contents($f);
        if (!empty($data)) {
            list($storedId, $realId, $server, $version, $expires) = unserialize($data);
            if ($id === $storedId && $expires > time()) {
                $ret = true;
            } else {
                fclose($f);
                @unlink($name);
                fclose($lock);
                return false;
            }
        }
        fclose($f);
        fclose($lock);
        return $ret;
    }

    /**
     * Removes cached information discovered from identity $id
     *
     * @param string $id identity
     * @return bool
     */
    public function delDiscoveryInfo($id)
    {
        $name = $this->_dir . '/discovery_' . md5($id);
        $lock = @fopen($this->_dir . '/discovery.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        @unlink($name);
        fclose($lock);
        return true;
    }

    /**
     * The function checks the uniqueness of openid.response_nonce
     *
     * @param  string $nonce openid.response_nonce field from authentication response
     * @return bool
     */
    public function isUniqueNonce($nonce)
    {
        $name = $this->_dir . '/nonce_' . md5($nonce);
        $lock = @fopen($this->_dir . '/nonce.lock', 'w+');
        if ($lock === false) {
            return false;
        }
        if (!flock($lock, LOCK_EX)) {
            fclose($lock);
            return false;
        }
        $f = @fopen($name, 'x');
        if ($f === false) {
            fclose($lock);
            return false;
        }
        fwrite($f, $nonce);
        fclose($f);
        fclose($lock);
        return true;
    }

    /**
     * Removes data from the uniqueness database that is older then given date
     *
     * @param mixed $date date of expired data
     */
    public function purgeNonces($date=null)
    {
        $lock = @fopen($this->_dir . '/nonce.lock', 'w+');
        if ($lock !== false) {
            flock($lock, LOCK_EX);
        }
        if (!is_int($date) && !is_string($date)) {
            foreach (glob($this->_dir . '/nonce_*') as $name) {
                @unlink($name);
            }
        } else {
            if (is_string($date)) {
                $time = time($date);
            } else {
                $time = $date;
            }
            foreach (glob($this->_dir . '/nonce_*') as $name) {
                if (filemtime($name) < $time) {
                    @unlink($name);
                }
            }
        }
        if ($lock !== false) {
            fclose($lock);
        }
    }
}
