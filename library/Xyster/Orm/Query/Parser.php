<?php
/**
 * Xyster Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to xyster@devweblog.org so we can send you a copy immediately.
 *
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id$
 */
/**
 * @see Xyster_String
 */
require_once 'Xyster/String.php';
/**
 * A data entity: the basic data unit of the ORM package
 *
 * @category  XysterColumn
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query_Parser
{
    /**
     * The mapper factory
     *
     * @var Xyster_Orm_Mapper_Factory_Interface
     */
    protected $_mapFactory;
    
    /**
     * A cache for runtime evaluations
     *
     * @var unknown_type
     */
    static protected $_runtime = array();

    /**
     * Creates a new query parser
     *
     * @param Xyster_Orm_Mapper_Factory_Interface $mapFactory
     */
    public function __construct( Xyster_Orm_Mapper_Factory_Interface $mapFactory )
    {
        $this->_mapFactory = $mapFactory;
    }
    
    /**
     * Asserts a field's presence in a class' members
     *
     * @param string $field
     * @param string $className
     * @throws Xyster_Orm_Query_Parser_Exception
     */
    public function assertValidFieldForClass( $field, $className )
    {
        require_once 'Xyster/Orm/Relation.php';
        require_once 'Xyster/Orm/Entity/Meta.php';
        
        require_once 'Xyster/Orm/Loader.php';
        Xyster_Orm_Loader::loadEntityClass($className);
        $map = $this->_mapFactory->get($className);
        $meta = $map->getEntityMeta();
        
        $field = ( $field instanceof Xyster_Data_Field ) ?
            trim($field->getName()) : trim($field);
            
        require_once 'Xyster/Data/Field/Aggregate.php';
        $matches = Xyster_Data_Field_Aggregate::match($field);
        if ( count($matches) ) {
            $field = trim($matches["field"]);
        }
            
        $calls = Xyster_String::smartSplit("->", $field);
        /*
            for composite references (i.e.  supervisor->name )
            - check each column exists in its container
        */
        if ( count($calls) > 1 ) {
            
            $container = $className;
            $currentMeta = $meta;
            foreach( $calls as $k=>$v ) {
                $this->assertValidFieldForClass($v, $container);
                if ( $meta->isRelation($v) ) {
                    $details = $meta->getRelation($v);
                    if ( !$details->isCollection() ) {
                        $container = $details->getTo();
                        $currentMeta = $this->_mapFactory->get($container)->getEntityMeta();
                    } else {
                        break;
                    }
                } else { 
                    break;
                }
            }
            
        } else {

            /*
                for method calls
                - check method exists in class
                - check any method parameters that may themselves be members
            */
            if ( preg_match("/^(?P<name>[a-z0-9_]+)(?P<meth>\((?P<args>[\w\W]*)\))$/i", $field, $matches) ) {
                if ( $className != "" && !in_array($matches['name'], $meta->getMembers()) ) {
                    require_once 'Xyster/Orm/Query/Parser/Exception.php';
                    throw new Xyster_Orm_Query_Parser_Exception($matches['name'] . ' is not a member of the ' . $className . ' class' );
                }
                $args = array();
                if ( strlen(trim($matches['args'])) ) {
                    foreach( Xyster_String::smartSplit(",", $matches['args']) as $v ) {
                        $v = trim($v);
                        $args[] = ( $this->isValidField($v) ) ?
                            $this->assertValidFieldForClass($v, $className) : $v;
                    }
                }
            /*
                for properties and relationships
                - check column exists in class
            */
            } else if ( $className != "" && !in_array($field, $meta->getMembers()) ) {
                require_once 'Xyster/Orm/Query/Parser/Exception.php';
                throw new Xyster_Orm_Query_Parser_Exception($field . ' is not a member of the ' . $className . ' class');
            }
        }
    }
    
    /**
     * Checks if a literal is a method call
     *
     * @param string $field
     * @return bool
     */
    public function isMethodCall( $field )
    {
        return (bool) preg_match("/^[a-z_][a-z0-9_]*\([\w\W]*\)$/i", $field);
    }
    
    /**
     * Verifies if a {@link Xyster_Data_Criterion} or {@link Xyster_Data_Field} is runtime
     * 
     * @param object $object 
     * @param string $class
     * @return bool
     */
    public function isRuntime( $object, $class ) 
    {
        if ( $object instanceof Xyster_Data_Criterion ) {
            
            foreach( Xyster_Data_Criterion::getFields($object) as $v ) {
                if ($this->isRuntime($v, $class)) {
                    return true;
                }
            }
            return false;
            
        } else if ( $object instanceof Xyster_Data_Field ) {
            
            $name = $object->getName();
            if ( !isset(self::$_runtime[$class][$name])) {
                self::$_runtime[$class][$name] = $this->_isRuntime($name,$class);
            }
            return self::$_runtime[$class][$name];
            
        } else if ( $object instanceof Xyster_Data_Sort ) {
            
            return $this->isRuntime($object->getField(), $class);
            
        }
        
        require_once 'Xyster/Orm/Query/Parser/Exception.php';
        throw new Xyster_Orm_Query_Parser_Exception('Unexpected type: ' . gettype($object));
    }
    
    /**
     * Checks a reference for syntactical correctness
     *
     * @param string $field
     * @return boolean
     */
    public function isValidField( $field )
    {
        $field = trim($field);

        $ok = true;
        if ( !preg_match("/^[a-z][a-z0-9_]*(->[a-z0-9_]+(\([\s]*\))?)*$/i", $field) ) {
            $mcs = Xyster_String::smartSplit("->", $field);
            foreach( $mcs as $mc ) {
                $matches = array();
                $match = preg_match( "/^[a-z][a-z0-9_]*(\((?P<params>[\w\W]*)\))?$/i", $mc, $matches );
                if ( ( $match && array_key_exists("params", $matches) && strlen(trim($matches['params']))
                && !$this->_checkMethodParameters($matches['params']) ) || !$match ) {
                    $ok = false;
                    break;
                }
            }
        }
        return $ok;
    }

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
        $groups = $this->_matchGroups($statement);
        if ( count($groups) == 1 && strlen($groups[0]) == strlen($statement) ) {
            $statement = trim(substr($statement,1,-1));
        }

        $crits = Xyster_String::smartSplit(" AND ", $statement, true);
        // in case it split the and of a "BETWEEN x AND y"
        if ( count($crits) == 2 && $this->_checkLiteral($crits[1]) ) {
            $crits = array($statement);
        }
        
        if ( count($crits) < 2 ) {
           
            $subcrits = Xyster_String::smartSplit(" OR ", $statement, true);
            if ( count($subcrits) < 2 ) {
                $groups = $this->_matchGroups(trim($subcrits[0]));
                $crit = ( count($groups) > 0 && strlen($groups[0]) > 2 ) ?
                    $this->parseCriterion($subcrits[0]) :
                    $this->parseExpression($subcrits[0]);
            } else {
                $crit = Xyster_Data_Junction::any( $this->parseCriterion($subcrits[0]),
                    $this->parseCriterion($subcrits[1]) );
                if ( count($subcrits) > 2 ) {
                    for ( $i=2; $i<count($subcrits); $i++ ) {
                        $crit->add( $this->parseCriterion( $subcrits[$i] ) );
                    }
                }
            }

        } else {
        
            $crit = Xyster_Data_Junction::all( $this->parseCriterion($crits[0]),
                $this->parseCriterion($crits[1]) );
                
            if ( count($crits) > 2 ) {
                for ( $i=2; $i<count($crits); $i++ ) {
                    $crit->add( $this->parseCriterion( $crits[$i] ) );
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
        $statement = Xyster_String::smartSplit(" ", preg_replace("/[\t\n\r]+/", " ", trim($statement)));
        
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

        if ( !Xyster_Data_Expression::isOperator($operator) ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid expression operator: ' . $operator);
        }

        array_shift($exp);

        if ( ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) && count($exp) != 3 ) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Invalid literal: ' . implode(" ",$exp));
        }

        if ( $operator == "IN" || $operator == "NOT IN" ) {
            $rightlit = trim(implode(" ",$exp));
            $matches = array();
            if ( !preg_match('/^\([\s]*(?P<choices>.*)[\s]*\)$/',$rightlit,$matches) ) {
                require_once 'Xyster/Orm/Query/Parser/Exception.php';
                throw new Xyster_Orm_Query_Parser_Exception('Invalid literal: ' . $rightlit);
            }
            else {
                $inChoices = Xyster_String::smartSplit(',',$matches['choices']);
                foreach( $inChoices as $k=>$choice ) {
                    $choice = trim($choice);
                    $this->_assertLiteral($choice);
                    if ( preg_match('/^"[^"]*"$/i',$choice) )
                        $inChoices[$k] = substr($choice,1,-1);
                }
                $rightlit = $inChoices;
            }
        } else {
            $rightlit = ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) ?
                array( $exp[0], $exp[2] ) : $exp[0];
            $this->_assertLiteral($rightlit);
        }

        if ( !is_array($rightlit) && preg_match('/^"[^"]*"$/i',$rightlit) ) {
            $rightlit = substr($rightlit,1,-1);
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
        $expecting = array('where','order');

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
        $expecting = array('select','where','group','having','order');

        $parts = $this->_baseParseQuery($query,$statement,$expecting);
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
    protected function _assertLiteral( $lit )
    {
        if ( is_array($lit) ) {
            foreach( $lit as $v ) {
                $this->_assertLiteral($v);
            }
        } else if ( !$this->_checkLiteral($lit) ) {
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
            $query->limit($limit,$offset);
        }
        
        $parts = array();
        $part = '';
        $split = Xyster_String::smartSplit(' ', trim($statement));

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
     * Checks a literal for syntactical correctness
     *
     * @param string $lit
     * @return boolean
     */
    protected function _checkLiteral( $lit )
    {
        return (
            // either a string or a number or the word "null"
            preg_match("/^(\"[^\"]*\"|[\d]+(.[\d]+)?|null)$/i", trim($lit))
            //  a string with escapes in it
            || preg_match('/^"[^"\\\\]*(\\\\.[^"\\\\]*)*"$/', trim($lit))
            // check to see if it's a field
            || $this->isValidField($lit)
        );
    }
    
    /**
     * Checks method parameters for syntactical correctness
     *
     * @param array $params
     * @return boolean
     */
    protected function _checkMethodParameters( $params )
    {
        $ps = Xyster_String::smartSplit(",", trim($params));
        $ok = true;
        foreach( $ps as $p ) {
            if ( !$this->_checkLiteral($p) ) {
                $ok = false;
                break;
            }
        }
        return $ok;
    }
    
    /**
     * Checks to see if a column can only be evaluated at runtime
     *
     * @param string $field
     * @param string $className
     * @return boolean
     */
    protected function _isRuntime( $field, $className )
    {
        require_once 'Xyster/Orm/Loader.php';
        Xyster_Orm_Loader::loadEntityClass($className);
        $meta = $this->_mapFactory->get($className)->getEntityMeta();

        $calls = Xyster_String::smartSplit('->',trim($field));

        if ( count($calls) == 1 ) {
            
            // the call isn't composite - could be a member or a relation
            return ( $this->isMethodCall($calls[0]) ) ? true :
                ( !in_array($field, $meta->getFieldNames())
                    && !$meta->isRelation($field) );
                    
        } else {
            
            // the call is composite - loop through to see if we can figure 
            // out the type bindings
            $container = $className;
            $currentMeta = $meta;
            foreach( $calls as $call ) {
                if ( $this->isMethodCall($call) ) {
                    return true;
                } else {
                    $isRel = $currentMeta->isRelation($call);
                    if ( !in_array($call, array_keys($currentMeta->getFields()))
                        && !$isRel ) {
                        return true;
                    } else if ( $isRel ) {
                        $container = $currentMeta->getRelation($call)->getTo();
                        $currentMeta = $this->_mapFactory->get($container)->getEntityMeta();
                    }
                }
            }
            return false;

        }
    }
    
    /**
     * Match nested parentheses groups
     *
     * @param string $string
     * @return array
     */
    protected function _matchGroups( $string )
    {
        if ( strpos($string, '(') === false ) {
            return array();
        }

        $groups = array();
        $buffer = "";
        $inParenth = 0;
        $inString = false;

        for ( $i=0; $i<strlen($string); $i++ ) {

            $curr = $string[$i];
            $last = ( $i ) ? $string[$i-1] : "";
            if ( $curr == '"' && ( ( $inString && $last != "\\") || !$inString ) ) {
                $inString = !$inString;
            }

            if ( !$inString ) {
                if ( $curr == '(' ) {
                    $inParenth++;
                } else if ( $curr == ')' ) {
                    if ( $inParenth == 1 ) {
                        $buffer .= $curr;
                    }
                    $inParenth--;
                }
            }

            if ( $inParenth ) {
                $buffer .= $curr;
            } else if ( strlen($buffer) ) {
                $groups[] = $buffer;
                $buffer = "";
            }
        }
        if ( strlen($buffer) ) {
            $groups[] = $buffer;
        }
        return $groups;
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
        if (!in_array($type, array('select', 'group', 'order'))) {
            require_once 'Xyster/Orm/Query/Parser/Exception.php';
            throw new Xyster_Orm_Query_Parser_Exception('Unknown clause type: ' . $type);
        }

        $call = array(
                'select'=>array($this, 'parseFieldAlias'),
                'order'=>array($this, 'parseSort'),
                'group'=>array('Xyster_Data_Field', 'group')
            );
        $method = array('select'=>'field', 'order'=>'order', 'group'=>'group');

        foreach( Xyster_String::smartSplit(",", $statement) as $item ) {
            $query->{$method[$type]}( call_user_func($call[$type], $item) );
        }
    }
}