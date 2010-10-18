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
 * @package   Xyster_Db
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
namespace Xyster\Db;
use Xyster\Data\Symbol\ISymbol;
use Xyster\Data\Symbol\Field;
use Xyster\Data\Symbol\Sort;
use Xyster\Data\Symbol\Criterion;
use Xyster\Data\Symbol\Expression;
use Xyster\Data\Symbol\Junction;
use Xyster\Data\Symbol\IClause;
/**
 * Translates objects in the Xyster_Data package into SQL fragments
 *
 * @category  Xyster
 * @package   Xyster_Db
 * @copyright Copyright LibreWorks, LLC (http://libreworks.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Translator
{
    const QM = '?';
    /**
     * The quote character for database identifiers
     * 
     * @var string
     */
    protected $_identifierQuote = '"';
    /**
     * The callback for column renaming
     *
     * @var mixed
     */
    protected $_renameCallback;
    /**
     * A table name to prefix columns
     * 
     * @var mixed
     */
    protected $_table;

    /**
     * Creates a new translator for a given SQL connection
     *
     * @param string $identifierQuote
     */
    public function __construct($identifierQuote = '"')
    {
        if ( strlen(trim($identifierQuote)) ) {
            $this->_identifierQuote = $identifierQuote;
        }
    }

    /**
     * Sets the callback for column renaming
     *
     * This can be any valid PHP callback.  It's passed the column object.
     *
     * @param mixed $callback
     * @return Translator  Provides a fluent interface
     */
    public function setRenameCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new DbException('This is not a valid callback');
        }
        $this->_renameCallback = $callback;
        return $this;
    }

    /**
     * Sets a table name to prefix to columns
     * 
     * @param string $table
     * @return Translator  Provides a fluent interface
     */
    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Translates one of the Xyster Data objects into a SQL token
     *
     * @param ISymbol $object
     * @param boolean $quote Whether to quote field names
     * @return Token
     */
    public function translate(ISymbol $object, $quote = true)
    {
        if ($object instanceof Field) {
            return $this->translateField($object, $quote);
        } else if ($object instanceof Sort) {
            return $this->translateSort($object, $quote);
        } else if ($object instanceof Criterion) {
            return $this->translateCriterion($object, $quote);
        } else if ($object instanceof IClause) {
            return $this->translateClause($object, $quote);
        }
    }

    /**
     * Translates a clause
     *
     * @param IClause $clause
     * @param boolean $quote
     * @return Token
     */
    public function translateClause(IClause $clause, $quote = true)
    {
        $translated = null;
        if ($clause instanceof Junction) {
            $translated = $this->translateJunction($clause, $quote);
        } else {
            $sql = array();
            $binds = array();
            foreach ($clause as $k => $symbol) {
                $token = $this->translate($symbol, $quote);
                $sql[] = $token->getSql();
                $binds += $token->getBindValues();
            }
            $translated = new Token(implode(', ', $sql), $binds);
        }
        return $translated;
    }

    /**
     * Converts a field to SQL
     *
     * @param Field $tosql  The field to translate
     * @param boolean $quote Whether to quote field names
     * @return Token  The translated SQL syntax
     */
    public function translateField(Field $tosql, $quote = true)
    {
        $rename = $this->_getRenamedField($tosql);
        $tableName = $this->_getTableName($tosql);

        $q = $this->_identifierQuote;
        $field = ( $quote ) ? $q . str_replace($q, $q . $q, $rename) . $q : $rename;
        if ($tableName) {
            $field = "$tableName.$field";
        }

        return new Token($tosql instanceof \Xyster\Data\Symbol\AggregateField ?
                        $tosql->getFunction()->getValue() . '(' . $field . ')' : $field);
    }

    /**
     * Converts a sort to SQL
     *
     * @param Sort $tosql  The Sort to translate
     * @param boolean $quote Whether to quote field names
     * @return Token  The translated SQL syntax
     */
    public function translateSort(Sort $tosql, $quote = true)
    {
        return new Token($this->translateField($tosql->getField(),
                $quote)->getSql() . " " . $tosql->getDirection());
    }

    /**
     * Converts a criterion to SQL
     *
     * @param Criterion $tosql  The Criterion to translate
     * @param boolean $quote Whether to quote field names
     * @return Token  The translated SQL syntax
     */
    public function translateCriterion(Criterion $tosql, $quote = true)
    {
        $token = null;
        if ($tosql instanceof Expression) {
            $token = $this->translateExpression($tosql, $quote);
        } else if ($tosql instanceof Junction) {
            $token = $this->translateJunction($tosql, $quote);
        }
        return $token;
    }

    /**
     * Converts a Junction to SQL
     *
     * @param Junction $tosql  The Junction to translate
     * @param boolean $quote Whether to quote field names
     * @return Token  The translated SQL syntax
     */
    public function translateJunction(Junction $tosql, $quote = true)
    {
        $criteria = array();
        foreach ($tosql->getCriteria() as $v) {
            $loopToken = $this->translateCriterion($v, $quote);
            $criteria[$loopToken->getSql()] = $loopToken;
        }
        $token = new Token("( " .
                        implode(" " . $tosql->getOperator() . " ", array_keys($criteria)) .
                        " )");
        foreach ($criteria as $v) {
            $token->addBindValues($v);
        }
        return $token;
    }

    /**
     * Converts an expression to SQL
     *
     * @param Expression $tosql  The Expression to translate
     * @param boolean $quote Whether to quote field names
     * @return Token The translated SQL syntax
     */
    public function translateExpression(Expression $tosql, $quote = true)
    {
        $binds = array();

        $sql = $this->translateField($tosql->getLeft(), $quote)->getSql() . ' ';
        $val = $tosql->getRight();
        $operator = $tosql->getOperator()->getValue();
        if ($val === null || $val == "NULL") {
            $sql .= ( $operator == '=' ) ? 'IS' : 'IS NOT';
        } else {
            $sql .= $operator;
        }
        $sql .= ' ';

        if ($val == "NULL" || $val === null) {
            $sql .= 'NULL';
        } else if ($val instanceof Field) {
            $sql .= $this->translateField($val, $quote)->getSql();
        } else {
            if (is_array($val)) {
                if (substr($operator, -7) == 'BETWEEN') {
                    $sql .= self::QM . " AND " . self::QM;
                    $binds = array($val[0], $val[1]);
                } else if (substr($operator, -2) == 'IN') {
                    $binds = array_values($val);
                    $sql .= '(' . implode(',', array_fill(0, count($val), self::QM)) . ')';
                }
            } else {
                $sql .= self::QM;
                $binds[] = $val;
            }
        }
        return new Token($sql, $binds);
    }

    /**
     * Gets the renamed value of the field if appropriate
     *
     * This can be overridden to provide a custom renaming strategy
     *
     * @param Field $field
     * @return string
     */
    protected function _getRenamedField(Field $field)
    {
        $rename = $field->getName();
        if ($this->_renameCallback !== null) {
            $rename = call_user_func($this->_renameCallback, $rename);
        }
        return $rename;
    }

    /**
     * Gets the name of the table to use to prefix columns
     *
     * This can be extended to provide a custom table name
     *
     * @param Field $field
     * @return string
     */
    protected function _getTableName(Field $field)
    {
        return $this->_table;
    }
}