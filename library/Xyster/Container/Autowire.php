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
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Container;

use Xyster\Enum\Enum;

/**
 * Autowiring modes
 *
 * @category  Xyster
 * @package   Xyster_Container
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Autowire extends Enum
{
    const None = 0;
    const ByName = 1;
    const ByType = 2;
    const Constructor = 3;

    /**
     * No autowiring
     *
     * @return Autowire
     */
    static public function None()
    {
        return Enum::_factory();
    }

    /**
     * Autowiring by property name
     *
     * @return Autowire
     */
    static public function ByName()
    {
        return Enum::_factory();
    }

    /**
     * Autowiring by property type
     *
     * @return Autowire
     */
    static public function ByType()
    {
        return Enum::_factory();
    }

    /**
     * Autowiring by constructor arguments
     *
     * @return Autowire
     */
    static public function Constructor()
    {
        return Enum::_factory();
    }
}