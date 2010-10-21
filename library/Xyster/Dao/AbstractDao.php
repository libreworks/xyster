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
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Dao;
/**
 * Base Data access object.
 *
 * Warning: If you're creating a DAO outside of the container, be sure to call
 * the init() method after all options are set.
 *
 * @category  Xyster
 * @package   Xyster_Dao
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractDao
{
    /**
     * @var boolean
     */
    protected $logging = false;
    /**
     * @var \Zend_Log
     */
    protected $logger;

    /**
     * Sets up the DAO.
     *
     * This method is final so that we can guarantee certain setup procedures.
     * @see AbstractDao::initDao
     */
    public final function init()
    {
        $this->checkConfig();
        $this->initDao();
    }

    /**
     * Allows implementing DAOs to check their configuration
     */
    protected abstract function checkConfig();

    /**
     * Meant to be overridden to do set up logic
     */
    protected function initDao()
    {
    }

    /**
     * Sets whether logging is enabled
     *
     * @param boolean $flag Whether logging is enabled
     * @return AbstractDao provides a fluent interface
     */
    public function setLogging($flag = true)
    {
        $this->logging = $flag;
        return $this;
    }

    /**
     * Sets the log used by the DAO
     *
     * @param \Zend_Log $log The logger instance
     * @return AbstractDao provides a fluent interface
     */
    public function setLogger(\Zend_Log $log)
    {
        $this->logger = $log;
        return $this;
    }

    /**
     * Sets up the logger
     */
    protected function setupLogger()
    {
        if ( $this->logging && !$this->logger ) {
            $this->logger = new \Zend_Log(new \Zend_Log_Writer_Null);
        }
    }
}
