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
 * @category  Xyster
 * @package   Xyster_Orm
 * @copyright Copyright (c) 2007 Irrational Logic (http://devweblog.org)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
class Xyster_Orm_Query_Parser
{
    static protected $_runtime = array();

	/**
	 * Asserts a field's presence in a class' members
	 *
	 * @param string $field
	 * @param string $className
	 * @throws Xyster_Orm_Query_Parser_Exception
	 */
	static public function assertValidFieldForClass( $field, $className )
	{
	    require_once 'Xyster/Orm/Relation.php';
	    require_once 'Xyster/Orm/Entity/Meta.php';
	    
	    require_once 'Xyster/Orm/Loader.php';
		Xyster_Orm_Loader::loadEntityClass($className);
		
		$field = ( $field instanceof Field ) ?
			trim($field->getName()) : trim($field);
			
		$calls = Xyster_String::smartSplit("->",$field);
		/*
			for composite references (i.e.  supervisor->name )
			- check each column exists in its container
		*/
		if ( count($calls) > 1 ) {
		    
			$container = $className;
			foreach( $calls as $k=>$v ) {
				self::assertValidFieldForClass($v,$container);
				if ( Xyster_Orm_Relation::isValid($container,$v) ) {
					$details = Xyster_Orm_Relation::get($container,$v);
					if ( !$details->isCollection() ) {
						$container = $details->getTo();
					} else {
					    break;
					}
				} else { 
				    break;
				}
			}
			
		} else {
		    
			$matches = self::matchAggregate($field);
			if ( count($matches) ) {
				$field = trim($matches["col"]);
			}
			/*
				for method calls
				- check method exists in class
				- check any method parameters that may themselves be members
			*/
			if ( preg_match("/^(?P<name>[a-z0-9_]+)(?P<meth>\((?P<args>[\w\W]*)\))$/i", $field, $matches) ) {
				if ( $className != "" && !in_array($matches['name'], Xyster_Orm_Entity_Meta::getMembers($className)) ) {
					require_once 'Xyster/Orm/Query/Parser/Exception.php';
			        throw new Xyster_Orm_Query_Parser_Exception($matches['name'] . ' is not a member of the ' . $className . ' class' );
				}
				$args = array();
				if ( strlen(trim($matches['args'])) ) {
					foreach( Xyster_String::smartSplit(",", $matches['args']) as $v ) {
						$v = trim($v);
						$args[] = ( self::isValidField($v) ) ?
							self::assertValidFieldForClass($v, $className) : $v;
					}
				}
			/*
				for properties and relationships
				- check column exists in class
			*/
			} else if ( $className != "" && !in_array($field, Xyster_Orm_Entity_Meta::getMembers($className)) ) {
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
	static public function isMethodCall( $field )
	{
		return preg_match( "/^[a-z_][a-z0-9_]*\([\w\W]*\)$/i", $field );
	}
	
	/**
	 * Verifies if a {@link Criterion} or {@link Column} is runtime
	 * 
	 * @param object $object 
	 * @param string $class
	 * @return bool
	 */
	static public function isRuntime( $object, $class ) 
	{
		if ( $object instanceof Xyster_Data_Criterion ) {
		    
			foreach( Xyster_Data_Criterion::getFields($object) as $v ) {
				if ( self::isRuntime($v,$class) ) {
					return true;
				}
			}
			return false;
			
		} else if ( $object instanceof Xyster_Data_Field ) {
		    
			$name = $object->getName();
			if ( !isset(self::$_runtime[$class][$name])) {
				self::$_runtime[$class][$name] = self::_isRuntime($name,$class);
			}
			return self::$_runtime[$class][$name];
			
		} else if ( $object instanceof Xyster_Data_Sort ) {
		    
			return self::isRuntime($object->getField(),$class);
			
		}
		
		require_once 'Xyster/Orm/Query/Parser/Exception.php';
		throw new Xyster_Orm_Query_Parser_Exception('FRAMEWORK_BAD_TYPE',gettype($object));
	}
	
	/**
	 * Checks a reference for syntactical correctness
	 *
	 * @param string $field
	 * @return boolean
	 */
	static public function isValidField( $field )
	{
		$ok = true;
		if ( !preg_match("/^[a-z][a-z0-9_]*(->[a-z0-9_]+(\([\s]*\))?)*$/i",trim($field)) ) {
			$mcs = Xyster_String::smartSplit("->",trim($field));
			$ok = true;
			foreach( $mcs as $mc ) {
				$matches = array();
				$match = preg_match( "/^[a-z][a-z0-9_]*(\((?P<params>[\w\W]*)\))?$/i", $mc, $matches );
				if ( ( $match && array_key_exists("params", $matches) && strlen(trim($matches['params']))
				&& !self::_checkMethodParameters($matches['params']) ) || !$match ) {
					$ok = false;
					break;
				}
			}
		}
		return $ok;
	}
	
	/**
	 * Matches for aggregate functions
	 *
	 * @param string $haystack
	 * @return array
	 */
	static public function matchAggregate( $haystack )
	{
		$matches = array();
		preg_match('/^(?P<func>AVG|MAX|MIN|COUNT|SUM)\((?P<col>[\w\W]*)\)$/i', trim($haystack), $matches);
		return $matches;
	}

	/**
	 * Parse a statement into a Xyster_Data_Criterion
	 *
	 * @param string $statement
	 * @return Xyster_Data_Criterion
	 */
	static public function parseCriterion( $statement )
	{
	    require_once 'Xyster/Data/Junction.php';
	    
		$crit = null;
		$statement = trim($statement);
		$groups = self::_matchGroups($statement);
		if ( count($groups) == 1 && strlen($groups[0]) == strlen($statement) ) {
			$statement = trim(substr($statement,1,-2));
		}

		$crits = Xyster_String::smartSplit(" AND ",$statement);

		if ( count($crits) < 2 ) {
		   
			$subcrits = Xyster_String::smartSplit(" OR ",$statement);
			if ( count($subcrits) < 2 ) {
				$groups = self::_matchGroups(trim($subcrits[0]));
				$crit = ( count($groups) > 1 ) ?
					self::parseCriterion($subcrits[0]) :
					self::parseExpression($subcrits[0]);
			} else {
				$crit = Xyster_Data_Junction::any( self::parseCriterion($subcrits[0]),
					self::parseCriterion($subcrits[1]) );
				if ( count($subcrits) > 2 ) {
					for ( $i=2; $i<count($subcrits); $i++ ) {
						$crit->add( self::parseCriterion( $subcrits[$i] ) );
					}
				}
			}

		} else {
		    
			$crit = Xyster_Data_Junction::all( self::parseCriterion($crits[0]),
				self::parseCriterion($crits[1]) );
			if ( count($crits) > 2 ) {
				for ( $i=2; $i<count($crits); $i++ ) {
					$crit->add( self::parseCriterion( $crits[$i] ) );
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
	static public function parseExpression( $statement )
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
		
		$leftlit = self::parseField($exp[0]);
		
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
			throw new Xyster_Orm_Query_Parser_Exception('DATA_BAD_OPERATOR', $operator, 'EXPRESSION');
		}

		array_shift($exp);

		if ( ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) && count($exp) != 3 ) {
			require_once 'Xyster/Orm/Query/Parser/Exception.php';
			throw new Xyster_Orm_Query_Parser_Exception('DATA_BAD_LITERAL', implode(" ",$exp));
		}

		if ( $operator == "IN" || $operator == "NOT IN" ) {
			$rightlit = trim(implode(" ",$exp));
			$matches = array();
			if ( !preg_match('/^\([\s]*(?P<choices>.*)[\s]*\)$/',$rightlit,$matches) ) {
				require_once 'Xyster/Orm/Query/Parser/Exception.php';
				throw new Xyster_Orm_Query_Parser_Exception('DATA_BAD_LITERAL',$rightlit );
			}
			else {
				$inChoices = Xyster_String::smartSplit(',',$matches['choices']);
				foreach( $inChoices as $k=>$choice ) {
					self::_assertLiteral($choice);
					if ( preg_match('/^"[^"]*"$/i',$choice) )
						$inChoices[$k] = substr($choice,1,-1);
				}
				$rightlit = $inChoices;
			}
		} else {
			$rightlit = ( $operator == "BETWEEN" || $operator == "NOT BETWEEN" ) ?
				array( $exp[0], $exp[2] ) : $exp[0];
			self::_assertLiteral($rightlit);
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
	static public function parseField( $name )
	{
	    require_once 'Xyster/Enum.php';
	    require_once 'Xyster/Data/Field.php';
	    
		$name = trim($name);
		$function = null;
		$matches = self::matchAggregate($name);

		if ( count($matches) ) {
			$function = Xyster_Enum::valueOf('Xyster_Data_Aggregate', strtoupper($matches["func"]));
			$name = trim($matches["col"]);
		}

		return ( $function ) ? Xyster_Data_Field::aggregate($name, $function) :
		    Xyster_Data_Field::named($name);
	}
	
	/**
	 * Parse string statement as a {@link Xyster_Data_Sort}
	 *
	 * @param string $statement
	 * @return Xyster_Data_Sort
	 * @throws Xyster_Orm_Query_Parser_Exception  if the statement syntax is invalid
	 */
	static public function parseSort( $statement )
	{
		$statement = trim($statement);
		$matches = array();
		$dir = "ASC";

		if ( preg_match("/[\s]+(?P<dir>ASC|DESC)$/i",$statement,$matches) ) {
			$dir = $matches["dir"];
			$statement = trim(str_replace($matches[0],"",$statement));
		}
		$field = self::parseField($statement);
		
		require_once 'Xyster/Data/Sort.php';

		return ( !strcasecmp($dir,"DESC") ) ? Xyster_Data_Sort::desc($field) :
		    Xyster_Data_Sort::asc($field);
	}
	
	/**
	 * Asserts a literal for syntactical correctness
	 *
	 * @param string $lit
	 * @throws Xyster_Orm_Query_Parser_Exception if the syntax is incorrect
	 */
	static protected function _assertLiteral( $lit )
	{
		if ( is_array($lit) ) {
			foreach( $lit as $v ) {
				self::_assertLiteral($v);
			}
		} else if ( !self::_checkLiteral($lit) ) {
			require_once 'Xyster/Orm/Query/Parser/Exception.php';
			throw new Xyster_Orm_Query_Parser_Exception('DATA_BAD_LITERAL',$lit );
		}
	}
	
	/**
	 * Checks a literal for syntactical correctness
	 *
	 * @param string $lit
	 * @return boolean
	 */
	static protected function _checkLiteral( $lit )
	{
		return (
		    // either a string or a number or the word "null"
	    	preg_match("/^(\"[^\"]*\"|[\d]+(.[\d]+)?|null)$/i", trim($lit))
	    	//  a string with escapes in it
		    || preg_match('/^(?:"[^"]*\\(?:.[^"]*\\)*.[^"]*")|(?:"[^"]*")$/', trim($lit))
		    // check to see if it's a field
		    || self::isValidField($lit)
		);
	}
	
	/**
	 * Checks method parameters for syntactical correctness
	 *
	 * @param array $params
	 * @return boolean
	 */
	static protected function _checkMethodParameters( $params )
	{
		$ps = Xyster_String::smartSplit(",", trim($params));
		$ok = true;
		foreach( $ps as $p ) {
			if ( !self::_checkLiteral($p) ) {
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
	static protected function _isRuntime( $field, $className )
	{
	    require_once 'Xyster/Orm/Loader.php';
		Xyster_Orm_Loader::loadEntityClass($className);

		$calls = Xyster_String::smartSplit('->',trim($field));
		
		echo $field;

		if ( count($calls) == 1 ) {
		    
		    // the call isn't composite - could be a member or a relation
			return ( self::isMethodCall($calls[0]) ) ? true :
				( !in_array($field,array_keys(Xyster_Orm_Entity_Meta::getFields($className)))
					&& !Xyster_Orm_Relation::isValid($className, $field) );
					
		} else {
		    
		    // the call is composite - loop through to see if we can figure 
	        // out the type bindings
			$container = $className;
			foreach( $calls as $call ) {
				if ( self::isMethodCall($call) ) {
					return true;
				} else {
					$isRel = Xyster_Orm_Relation::isValid($container, $call);
					if ( !in_array($call, array_keys(Xyster_Orm_Entity_Meta::getFields($container)))
						&& !$isRel ) {
						return true;
					} else if ( $isRel ) { 
						$container = Xyster_Orm_Relation::get($container, $call)->getTo();
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
	static protected function _matchGroups( $string )
	{
		if ( strpos($string,'(') < 0 ) {
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
}