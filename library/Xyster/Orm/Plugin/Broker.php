<?php
/**
 * Xyster Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A broker for plugins
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Plugin_Broker
{
    /**
     * Array of Xyster_Orm_Plugin_Abstract objects
     *
     * @var array
     */
    protected $_plugins = array();
    
    /**
     * Retrieve a plugin or plugins by class
     *
     * @param  string $class Class name of plugin(s) desired
     * @return Xyster_Orm_Plugin_Abstract|array|false False if none, the plugin if only one, and array of plugins if multiple of same class
     */
    public function getPlugin( $class )
    {
        $found = array();
        foreach ($this->_plugins as $plugin) {
            if ( $class == get_class($plugin) ) {
                $found[] = $plugin;
            }
        }

        switch (count($found)) {
            case 0:
                return false;
            case 1:
                return $found[0];
            default:
        }
        
        return $found;
    }

    /**
     * Retrieve all plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->_plugins;
    }
    
    /**
     * Checks whether a plugin of a particular class is registered
     *
     * @param string $class
     * @return boolean
     */
    public function hasPlugin( $class )
    {
        foreach ($this->_plugins as $plugin) {
            if ( $class == get_class($plugin) ) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Called prior to an entity being deleted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postDelete( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->postDelete($entity);
        }
    }
    
    /**
     * Called prior to an entity being inserted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postInsert( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->postInsert($entity);
        }
    }
    
    /**
     * Called after a new entity is loaded with values
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postLoad( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->postLoad($entity);
        }
    }
        
    /**
     * Called prior to an entity being updated
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function postUpdate( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->postUpdate($entity);
        }
    }
    
    /**
     * Called prior to an entity being deleted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preDelete( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->preDelete($entity);
        }
    }
    
    /**
     * Called prior to an entity being inserted
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preInsert( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->preInsert($entity);
        }
    }
    
    /**
     * Called prior to an entity being updated
     *
     * @param Xyster_Orm_Entity $entity
     */
    public function preUpdate( Xyster_Orm_Entity $entity )
    {
        foreach( $this->_plugins as $plugin ) {
            /* @var $plugin Xyster_Orm_Plugin_Abstract */
            $plugin->preUpdate($entity);
        }
    }
    
    /**
     * Register a plugin
     *
     * @param Xyster_Orm_Plugin_Abstract $plugin
     * @param int $stackIndex
     * @return Xyster_Orm_Plugin_Broker provides a fluent interface
     */
    public function registerPlugin( Xyster_Orm_Plugin_Abstract $plugin, $stackIndex = null )
    {
        if ( in_array($plugin, $this->_plugins, true) ) {
            require_once 'Xyster/Orm/Exception.php';
            throw new Xyster_Orm_Exception('Plugin already registered');
        }

        $stackIndex = (int) $stackIndex;

        if ($stackIndex) {
            if ( isset($this->_plugins[$stackIndex]) ) {
                require_once 'Xyster/Orm/Exception.php';
                throw new Xyster_Orm_Exception('Plugin with stackIndex "' . $stackIndex . '" already registered');
            }
            $this->_plugins[$stackIndex] = $plugin;
        } else {
            $stackIndex = count($this->_plugins);
            while ( isset($this->_plugins[$stackIndex]) ) {
                ++$stackIndex;
            }
            $this->_plugins[$stackIndex] = $plugin;
        }

        ksort($this->_plugins);

        return $this;
    }

    /**
     * Unregister a plugin.
     *
     * @param string|Xyster_Orm_Plugin_Abstract $plugin Plugin object or class name
     * @return Xyster_Orm_Plugin_Broker provides a fluent interface
     */
    public function unregisterPlugin( $plugin )
    {
        if ( $plugin instanceof Xyster_Orm_Plugin_Abstract ) {
            $key = array_search($plugin, $this->_plugins, true);
            if ( $key === false ) {
                require_once 'Xyster/Orm/Exception.php';
                throw new Xyster_Orm_Exception('Plugin not registered');
            }
            unset($this->_plugins[$key]);
        } else if ( is_string($plugin) ) {
            foreach( $this->_plugins as $key => $_plugin ) {
                $type = get_class($_plugin);
                if ($plugin == $type) {
                    unset($this->_plugins[$key]);
                }
            }
        }
        return $this;
    }
}