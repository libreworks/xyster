<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   UnitTests
 * @subpackage Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */

/**
 * Zend_Cache_Backend_Interface
 */
require_once 'Zend/Cache/Backend/Interface.php';

/**
 * A mock cache
 *
 */
class Xyster_Orm_CacheMock implements Zend_Cache_Backend_Interface
{
    /**
     * @var Xyster_Collection_Map_String
     */
    protected $_cache;
    
    protected $_returnOnSave = true;
    
    /**
     * Constructor
     *
     * @param boolean $returnOnSave
     */
    public function __construct( $returnOnSave = true )
    {
        $this->_returnOnSave = $returnOnSave;
        
        require_once 'Xyster/Collection/Map/String.php';
        $this->_cache = new Xyster_Collection_Map_String();
    }
    
    /**
     * Set the frontend directives
     * 
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives)
    {
    }
       
    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     * 
     * @param string $id cache id
     * @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
     * @return string cached datas (or false)
     */
    public function load($id, $doNotTestCacheValidity = false)
    {
        $value = $this->_cache->get($id);
        return ( is_object($value) ) ? clone $value : $value;
    }
    
    /**
     * Test if a cache is available or not (for the given id)
     * 
     * @param string $id cache id
     * @return mixed false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id)
    {
        return $this->_cache->containsKey($id);
    }
    
    /**
     * Save some string datas into a cache record
     *
     * @param string $data datas to cache
     * @param string $id cache id
     * @param array $tags array of strings, the cache record will be tagged by each string entry
     * @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)    
     * @return boolean true if no problem
     */
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $this->_cache->set($id, is_object($data) ? clone $data : $data);
        return $this->_returnOnSave;
    }
    
    /**
     * Remove a cache record
     * 
     * @param string $id cache id
     * @return boolean true if no problem
     */
    public function remove($id)
    {
        $this->_cache->remove($id);
        return true;
    }
    
    /**
     * Clean some cache records
     * 
     * @param string $mode clean mode
     * @param tags array $tags array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    { 
    }
    
    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }
}