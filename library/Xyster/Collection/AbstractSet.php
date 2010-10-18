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
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace Xyster\Collection;
/**
 * Abstract class for no-duplicate collections
 *
 * @category  Xyster
 * @package   Xyster_Collection
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
abstract class AbstractSet extends AbstractCollection implements ISet
{
    /**
     * Adds an item to the set
     *
     * @param mixed $item The item to add
     * @return boolean Whether the set changed as a result of this method
     */
    public function add($item)
    {
        if (!$this->contains($item)) {
            return parent::add($item);
        }
        return false;
    }
}