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
 * @see Xyster_Orm_Type_Boolean
 */
require_once 'Xyster/Orm/Type/Boolean.php';
/**
 * The boolean type
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class Xyster_Orm_Type_Boolean_TrueFalse extends Xyster_Orm_Type_Boolean_Character
{
    /**
     * Returns the abbreviated name of the type.
      *
      * @return string
     */
    public function getName()
    {
        return 'true_false';
    }

    /**
     * Returns the string representing a false value.
     *
     * @return string
     */
    protected function _getFalseString()
    {
        return 'false';
    }

    /**
     * Returns the string representing a true value.
     *
     * @return string
     */
    protected function _getTrueString()
    {
        return 'true';
    }
}