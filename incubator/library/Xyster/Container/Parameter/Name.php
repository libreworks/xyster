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
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * A simple parameter name
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Container_Parameter_Name
{   
    /**
     * @var ReflectionMethod
     */
    private $_member;
    /** 
     * @var int
     */
    private $_index;

    /**
     * Creates a new parameter name
     *
     * @param ReflectionMethod $member
     * @param int $index
     */
    public function __construct( ReflectionMethod $member, $index)
    {
        $this->_member = $member;
        $this->_index = $index;
    }

    /**
     * Gets the parameter name
     *
     * @return string
     */
    public function getName()
    {
        $params = $this->_member->getParameters();
        return $params[$this->_index]->getName();
    }
}