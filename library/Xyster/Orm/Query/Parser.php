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
 * @see Xyster_Orm_Xsql
 */
require_once 'Xyster/Orm/Xsql.php';
/**
 * Parses an XSQL string into a Xyster_Orm_Query object
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007-2008 Irrational Logic (http://irrationallogic.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query_Parser
{    
    /**
     * Parse a statement into a Xyster_Data_Criterion
     *
     * @param string $statement
     * @return Xyster_Data_Criterion
     */
    public function parseCriterion( $statement )
    {
        require_once 'Xyster/Data/Junction.php';
        
        $crit = null;
        $statement = trim($statement);
        
        $groups = Xyster_Orm_Xsql::matchGroups($statement);
        if ( count($groups) == 1 && strlen($groups[0]) == strlen($statement) ) {
            $statement = trim(substr($statement, 1, -1));
        }

        $crits = Xyster_Orm_Xsql_Split::Custom(' AND ')->split($statement, true);
        // in case it split the and of a "BETWEEN x AND y"
        if ( count($crits) == 2 && Xyster_Orm_Xsql::isLiteral($crits[1]) ) {
            $crits = array($statement);
        }
        
        if ( count($crits) < 2 ) {
            $subcrits = Xyster_Orm_Xsql_Split::Custom(' OR ')->split($statement, true);
            if ( count($subcrits) < 2 ) {
                $groups = Xyster_Orm_Xsql::matchGroups(trim($subcrits[0]));
                $crit = ( count($groups) > 0 && strlen($groups[0]) ) ?
                    $this->parseCriterion($subcrits[0]) :
                    $this->parseExpression($subcrits[0]);
            } else {
                $criteria = array_map(array($this, 'parseCriterion'), $subcrits);
                $crit = Xyster_Data_Criterion::fromArray('OR', $criteria);
            }

        } else {
        
            $crit = Xyster_Data_Junction::all($this->parseCriterion($crits[0]),
                $this->parseCriterion($crits[1]));
                
            if ( count($crits) > 2 ) {
                for ( $i=2; $i<count($crits); $i++ ) {
                    $crit->add($this->parseCriterion($crits[$i]));
                }
            }
        }

        return $crit;
    }

    /**
     * Parse string statement as Expression
     *
     * @param string $statement
     * @return Xyster_Data_Expression
     * @throws Xyster_Orm_Query_Parser_Exception if the expression syntax is incorrect
     */
    public function parseExpression( $statement )
    {
        require_once 'Xyster/Data/Expression.php';
        
        // remove whitespace characters we don't like 
        $statement = Xyster_Orm_Xsql::splitSpace(preg_replace("/[\t\n\r]+/", " ", trim($statement)));
        
        $exp = array();
        foreach( $statement as $v ) {
            if( $v != "" ) {
                $exp[] = $v;
            }
        }
        
        $leftlit = $this->parseField($exp[0]);
        
        array_shift($exp);
        $upper0 = strtoupper($exp[0]);
        $upper1 = strtoupper($exp[1]);

        if ( $upper0 == "NOT" || ( $upper0 == "IS" && $upper1 == "NOT" ) ) {
            $operator = $upper0 . " " . $upper1;
            array_shift($exp);
        } else {
            $operator = $upper0;
        }

        require_once 'Xyster/Enum.php';
        if ( !in_array($operator, array_keys(Xyster_Enum::values('Xyster_Data_Operator_Expression'))) ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid expression operator: ' . $operator);
        }

        array_shift($exp);

        if ( ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) && count($exp) != 3 ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid literal: ' . implode(" ",$exp));
        }

        if ( $operator == "IN" || $operator == "NOT IN" ) {
            $rightlit = trim(implode(' ', $exp));
            $matches = array();
            if ( !preg_match('/^\([\s]*(?P<choices>.*)[\s]*\)$/', $rightlit, $matches) ) {
                require_once 'Xyster/Orm/Query/Parser/Exception.php';
                throw new Xyster_Orm_Query_Parser_Exception('Invalid literal: ' . $rightlit);
            } else {
            	$inChoices = Xyster_Orm_Xsql::splitComma($matches['choices']);
                foreach( $inChoices as $k=>$choice ) {
                    $choice = trim($choice);
                    $this->_checkLiteral($choice);
                    if ( preg_match('/^"[^"]*"$/i', $choice) )
                        $inChoices[$k] = substr($choice, 1, -1);
                }
                $rightlit = $inChoices;
            }
        } else {
            $rightlit = ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) ?
                array( $exp[0], $exp[2] ) : $exp[0];
            $this->_checkLiteral($rightlit);
        }

        if ( !is_array($rightlit) && preg_match('/^"[^"]*"$/i', $rightlit) ) {
            $rightlit = substr($rightlit, 1, -1);
        }

        $args = array( $leftlit );
        if ( in_array($operator, array("=","<>",">",">=","<","<=","LIKE","NOT LIKE",'IN','NOT IN')) ) {
            $args[] = $rightlit;
        } else if ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) {
            $args[] = $rightlit[0];
            $args[] = $rightlit[1];
        }

        return call_user_func_array(array("Xyster_Data_Expression",
            Xyster_Data_Expression::getMethodName($operator)), $args);
    }
    
    /**
     * Parse a string as Field
     *
     * @param string $name
     * @return Xyster_Data_Field
     */
    public function parseField( $name )
    {
        $name = trim($name);

        require_once 'Xyster/Data/Field/Aggregate.php';
        $match = Xyster_Data_Field_Aggregate::match($name);

        if ( $match ) {
            require_once 'Xyster/Enum.php';
            $function = Xyster_Enum::valueOf('Xyster_Data_Aggregate', strtoupper($match['function']));
            $field = Xyster_Data_Field::aggregate($function, trim($match['field']));
        } else {
            $field = Xyster_Data_Field::named($name);
        }

        return $field;
    }
    
    /**
     * Parse a string as Field with alias
     *
     * @param string $name
     * @return Xyster_Data_Field
     */
    public function parseFieldAlias( $statement )
    {
        $statement = trim($statement);
        $matches = array();
        $alias = $statement;
        $pattern = '/[\s]+(AS[\s]+(?P<aliasA>[a-z0-9_]+)|"(?P<aliasQ>[a-z0-9_]+)")/i';
        
        if (preg_match($pattern, $statement, $matches)) {
            if (!empty($matches['aliasA']) || !empty($matches['aliasQ'])) {
                $alias = (!empty($matches['aliasA'])) ?
                    $matches['aliasA'] : $matches['aliasQ'];
            }
            $statement = str_replace($matches[0], "", $statement);
        }

        $field = $this->parseField($statement);
        $field->setAlias($alias);
        return $field;
    }
    
    /**
     * Parses string statement as Xyster_Orm_Query
     *
     * @param Xyster_Orm_Query $query
     * @param string $statement
     */
    public function parseQuery( Xyster_Orm_Query $query, $statement )
    {
        $expecting = array('where', 'order');

        $parts = $this->_baseParseQuery($query, $statement, $expecting);
        if ( !empty($parts['where']) ) {
            $query->where($this->parseCriterion($parts['where']));
        }
        if ( !empty($parts['order']) ) {
            $this->_parseClause($query, 'order', $parts['order']);
        }
    }

    /**
     * Parses string statement as Xyster_Orm_Query_Report
     *
     * @param Xyster_Orm_Query_Report $query
     * @param string $statement
     */
    public function parseReportQuery( Xyster_Orm_Query_Report $query, $statement )
    {
        $expecting = array('select', 'where', 'group', 'having', 'order');

        $parts = $this->_baseParseQuery($query, $statement, $expecting);
        if ( empty($parts['select']) ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid statement: ' . $statement);
        }

        $matches = array();
        if (preg_match('/^distinct[[:space:]]+/i', $parts['select'], $matches)) {
            $query->distinct(true);
            $parts['select'] = str_replace($matches[0], '', $parts['select']);
        }

        $this->_parseClause($query, 'select', $parts['select']);
        if ( !empty($parts['where']) ) {
            $query->where($this->parseCriterion($parts['where']));
        }
        if ( !empty($parts['order']) ) {
            $this->_parseClause($query, 'order', $parts['order']);
        }
        if ( !empty($parts['group']) ) {
            $this->_parseClause($query, 'group', $parts['group']);
        }
        if ( !empty($parts['having']) ) {
            $query->having($this->parseCriterion($parts['having']));
        }
    }
    
    /**
     * Parse string statement as a {@link Xyster_Data_Sort}
     *
     * @param string $statement
     * @return Xyster_Data_Sort
     * @throws Xyster_Orm_Query_Parser_Exception  if the statement syntax is invalid
     */
    public function parseSort( $statement )
    {
        $statement = trim($statement);
        $matches = array();
        $dir = 'ASC';

        if ( preg_match('/\s+(?P<dir>ASC|DESC)$/i', $statement, $matches) ) {
            $dir = $matches["dir"];
            $statement = trim(str_replace($matches[0], "", $statement));
        }

        $field = $this->parseField($statement);
        return (!strcasecmp($dir, 'DESC')) ? $field->desc() : $field->asc();
    }
    
    /**
     * Asserts a literal for syntactical correctness
     *
     * @param string $lit
     * @throws Xyster_Orm_Query_Parser_Exception if the syntax is incorrect
     */
    protected function _checkLiteral( $lit )
    {
        if ( is_array($lit) ) {
            foreach( $lit as $v ) {
                $this->_checkLiteral($v);
            }
        } else if ( !Xyster_Orm_Xsql::isLiteral($lit) ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid literal: ' . $lit);
        }
    }
    
    /**
     * Removes the limit and offset clause from a statement
     *
     * @param Xyster_Orm_Query $query  The query into which the parts are set
     * @param string $statement  The statement to parse
     * @param array $expecting 
     * @return array  The statement split by parts
     */
    protected function _baseParseQuery( Xyster_Orm_Query $query, $statement, array $expecting )
    {
        $matches = array();
        if ( preg_match('/[\s]*LIMIT (?P<limit>[\d]+)( OFFSET (?P<offset>[\d]+))?$/i',$statement,$matches) ) {
            $statement = str_replace($matches[0], '', $statement);
        }
        $limit = ( isset($matches['limit']) ) ? $matches['limit'] : 0;
        $offset = ( isset($matches['offset']) ) ? $matches['offset'] : 0;
        if ( $limit ) { 
            $query->limit($limit, $offset);
        }
        
        $parts = array();
        $part = '';
        $split = Xyster_Orm_Xsql::splitSpace(trim($statement));

        foreach( $split as $v ) {
            if ( in_array($part, array('order','group')) && !strcasecmp($v, 'by') ) {
                continue;
            }
            foreach( $expecting as $epart ) {
                if ( !strcasecmp($v, $epart) ) {
                    $part = strtolower($v);
                }
            }
            if ( !in_array(strtolower($v), $expecting) ) {
                if ( !isset($parts[$part]) ) {
                    $parts[$part] = "";
                }
                $parts[$part] .= $v . " ";
            }
        }

        return $parts;
    }
    
    /**
     * Parses a string statement clause into its corresponding parts
     *
     * @param Xyster_Orm_Query $query The query into which the parts will be set
     * @param string $type  The type of clause (either select, group, or order)
     * @param string $statement  The actual clause to parse
     */
    protected function _parseClause( Xyster_Orm_Query $query, $type, $statement )
    {
        $call = array(
                'select'=>array($this, 'parseFieldAlias'),
                'order'=>array($this, 'parseSort'),
                'group'=>array('Xyster_Data_Field', 'group')
            );
        $method = array('select'=>'field', 'order'=>'order', 'group'=>'group');
        foreach( Xyster_Orm_Xsql::splitComma($statement) as $item ) {
            $query->{$method[$type]}( call_user_func($call[$type], $item) );
        }
    }
}