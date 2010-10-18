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
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Type\Property;
/**
 * A mediator for setting and getting values from a named field
 *
 * Implementors of this interface should specify a constructor that takes the
 * field name as an argument.
 *
 * @category  Xyster
 * @package   Xyster_Type
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
interface IProperty
{
    /**
     * Gets the value in the field of the target
     *
     * @param mixed $target
     * @return mixed
     */
    function get( $target );
    
    /**
     * Sets the value in the field of the target
     *
     * @param mixed $target
     * @param mixed $value
     */
    function set( $target, $value );
}