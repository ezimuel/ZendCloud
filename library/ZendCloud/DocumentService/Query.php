<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendCloud\DocumentService;

/**
 * Generic query object
 *
 * Aggregates operations in an array of clauses, where the first element
 * describes the clause type, and the next element describes the criteria.
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage DocumentService
 */
class Query implements QueryAdapter\QueryAdapterInterface
{
    /**
     * Known query types
     */
    const QUERY_SELECT  = 'select';
    const QUERY_FROM    = 'from';
    const QUERY_WHERE   = 'where';
    const QUERY_WHEREID = 'whereid'; // request element by ID
    const QUERY_LIMIT   = 'limit';
    const QUERY_ORDER   = 'order';

    /**
     * Clause list
     *
     * @var array
     */
    protected $_clauses = array();

    /**
     * Generic clause
     *
     * You can use any clause by doing $query->foo('bar')
     * but concrete adapters should be able to recognise it
     *
     * The call will be interpreted as clause 'foo' with argument 'bar'
     *
     * @param  string $name Clause/method name
     * @param  mixed $args
     * @return \ZendCloud\DocumentService\Query
     */
    public function __call($name, $args)
    {
        $this->_clauses[] = array(strtolower($name), $args);
        return $this;
    }

    /**
     * SELECT clause (fields to be selected)
     *
     * @param  null|string|array $select
     * @return \ZendCloud\DocumentService\Query
     */
    public function select($select)
    {
        if (empty($select)) {
            return $this;
        }
        if (!is_string($select) && !is_array($select)) {
            throw new Exception\InvalidArgumentException("SELECT argument must be a string or an array of strings");
        }
        $this->_clauses[] = array(self::QUERY_SELECT, $select);
        return $this;
    }

    /**
     * FROM clause
     *
     * @param string $name Field names
     * @return \ZendCloud\DocumentService\Query
     */
    public function from($name)
    {
        if(!is_string($name)) {
            throw new Exception\InvalidArgumentException("FROM argument must be a string");
        }
        $this->_clauses[] = array(self::QUERY_FROM, $name);
        return $this;
    }

    /**
     * WHERE query
     *
     * @param string $cond Condition
     * @param array $args Arguments to substitute instead of ?'s in condition
     * @param string $op relation to other clauses - and/or
     * @return \ZendCloud\DocumentService\Query
     */
    public function where($cond, $value = null, $op = 'and')
    {
        if (!is_string($cond)) {
            throw new Exception\InvalidArgumentException("WHERE argument must be a string");
        }
        $this->_clauses[] = array(self::QUERY_WHERE, array($cond, $value, $op));
        return $this;
    }

    /**
     * Select record or fields by ID
     *
     * @param  string|int $value Identifier to select by
     * @return \ZendCloud\DocumentService\Query
     */
    public function whereId($value)
    {
        if (!is_scalar($value)) {
            throw new Exception\InvalidArgumentException("WHEREID argument must be a scalar");
        }
        $this->_clauses[] = array(self::QUERY_WHEREID, $value);
        return $this;
    }

    /**
     * LIMIT clause (how many items to return)
     *
     * @param  int $limit
     * @return \ZendCloud\DocumentService\Query
     */
    public function limit($limit)
    {
        if ($limit != (int) $limit) {
            throw new Exception\InvalidArgumentException("LIMIT argument must be an integer");
        }
        $this->_clauses[] = array(self::QUERY_LIMIT, $limit);
        return $this;
    }

    /**
     * ORDER clause; field or fields to sort by, and direction to sort
     *
     * @param  string|int|array $sort
     * @param  string $direction
     * @return \ZendCloud\DocumentService\Query
     */
    public function order($sort, $direction = 'asc')
    {
        $this->_clauses[] = array(self::QUERY_ORDER, array($sort, $direction));
        return $this;
    }

    /**
     * "Assemble" the query
     *
     * Simply returns the clauses present.
     *
     * @return array
     */
    public function assemble()
    {
        return $this->getClauses();
    }

    /**
     * Return query clauses as an array
     *
     * @return array Clauses in the query
     */
    public function getClauses()
    {
         return $this->_clauses;
    }
}
