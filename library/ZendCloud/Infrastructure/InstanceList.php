<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendCloud\Infrastructure;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * List of instances
 *
 * @package    Zend_Cloud
 * @subpackage Infrastructure
 */
class InstanceList implements
    ArrayAccess,
    Countable,
    Iterator
{
    /**
     * @var array Array of ZendCloud\Infrastructure\Instance
     */
    protected $instances = array();

    /**
     * @var int Iterator key
     */
    protected $iteratorKey = 0;

    /**
     * @var \ZendCloud\Infrastructure\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param  Adapter\AdapterInterface $adapter
     * @param  array $instances
     * @return void
     */
    public function __construct(Adapter\AdapterInterface $adapter, array $instances = null)
    {
        if (!($adapter instanceof Adapter\AdapterInterface)) {
            throw new Exception\InvalidArgumentException('You must pass a ZendCloud\Infrastructure\Adapter\AdapterInterface');
        }
        if (empty($instances)) {
            throw new Exception\InvalidArgumentException('You must pass an array of Instances');
        }

        $this->adapter = $adapter;
        $this->constructFromArray($instances);
    }

    /**
     * Transforms the Array to array of Instances
     *
     * @param  array $list
     * @return void
     */
    protected function constructFromArray(array $list)
    {
        foreach ($list as $instance) {
            $this->addInstance(new Instance($this->adapter,$instance));
        }
    }

    /**
     * Add an instance
     *
     * @param  Instance
     * @return InstanceList
     */
    protected function addInstance(Instance $instance)
    {
        $this->instances[] = $instance;
        return $this;
    }

    /**
     * Return number of instances
     *
     * Implement Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->instances);
    }

    /**
     * Return the current element
     *
     * Implement Iterator::current()
     *
     * @return Instance
     */
    public function current()
    {
        return $this->instances[$this->iteratorKey];
    }

    /**
     * Return the key of the current element
     *
     * Implement Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iteratorKey;
    }

    /**
     * Move forward to next element
     *
     * Implement Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        $this->iteratorKey++;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * Implement Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        $this->iteratorKey = 0;
    }

    /**
     * Check if there is a current element after calls to rewind() or next()
     *
     * Implement Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        $numItems = $this->count();
        if ($numItems > 0 && $this->iteratorKey < $numItems) {
            return true;
        }
        return false;
    }

    /**
     * Whether the offset exists
     *
     * Implement ArrayAccess::offsetExists()
     *
     * @param  int $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return ($offset < $this->count());
    }

    /**
     * Return value at given offset
     *
     * Implement ArrayAccess::offsetGet()
     *
     * @param  int $offset
     * @return Instance
     * @throws Exception\OutOfBoundsException
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new Exception\OutOfBoundsException('Illegal index');
        }
        return $this->instances[$offset];
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetSet()
     *
     * @param   int     $offset
     * @param   string  $value
     * @throws  Exception\InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception\InvalidArgumentException('You are trying to set read-only property');
    }

    /**
     * Throws exception because all values are read-only
     *
     * Implement ArrayAccess::offsetUnset()
     *
     * @param   int     $offset
     * @throws  Exception\InvalidArgumentException
     */
    public function offsetUnset($offset)
    {
        throw new Exception\InvalidArgumentException('You are trying to unset read-only property');
    }
}
