<?php

/**
 *  Website that displays live PHP documentation of classes and functions with the help of reflection.
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *  
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


namespace phpLiveDoc\Helpers;

use System\Linq\Enumerable;


/**
 * Helper class for reflection operations.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class ReflectionHelper {
	private function __construct() {
	}
	
	
	/**
	 * Creates a joing string list from a list of parameters.
	 * 
	 * @param array $params The list of parameters.
	 * 
	 * @return string The list of parameters as string.
	 */
	public static function createParameterList(array $params) {
		return Enumerable::fromArray($params)
    		             ->stringJoin(', ',
    		                          function(\ReflectionParameter $rp) {
    			                          $paramTypes = array();
    			                          
    			                          $pc = $rp->getClass();
    			                          if ($pc instanceof \ReflectionClass) {
    			                              $pcn      = $pc->getName();
    			                              $typeLink = null;
    			                              
    			                              if (class_exists($pcn) || interface_exists($pcn)) {
    			                                  $typeLink = self::tryGetLinkUrlFromType($pcn);
    			                              }
    			                              
    			                              if (is_null($typeLink)) {
    			                                  $paramTypes[] = htmlentities($pcn);
    			                              }
    			                              else {
    			                              	  $paramTypes[] = sprintf('<a href="%s" target="_blank">%s</a>',
    			                              	  		                  $typeLink,
    			                              	  		                  htmlentities($pcn));
    			                              }
    			                          }
    			                          else {
	    			                          if ($rp->isArray()) {
	    			                              $paramTypes[] = '<a href="http://php.net/manual/en/language.types.array.php" target="_blank">array</a>';
	    			                          }
	    			                          else if ($rp->isCallable()) {
	    			                              $paramTypes[] = '<a href="http://php.net/manual/en/language.types.callable.php" target="_blank">callable</a>';
	    			                          }
    			                          }
    			                                    
    			                          if (empty($paramTypes)) {
    			                              $paramTypes[] = 'mixed';
    			                          }
    			                                    
    			                          $suffix = '';
    			                          if ($rp->isOptional()) {
    			                              $suffix .= ' = ';
    			                                    	
    			                              if ($rp->isArray()) {
    			                                  $suffix .= 'array()';
    			                              }
    			                              else {
    			                                  $defVal = $rp->getDefaultValue();
    			                                  
    			                                  if (is_null($defVal)) {
    			                                      $suffix .= 'null';
    			                                  }
    			                                  else {
    			                                      $suffix .= var_export($defVal, true);
    			                                  }
    			                              }
    			                          }
    			                                    
    			                          return implode('|', $paramTypes) . 
                                                 ' $' . $rp->getName() .
    			                                 $suffix;
    		                          });
	}
	
	/**
	 * Tries to return a link to a documentated type. 
	 * 
	 * @param string|\ReflectionClass $type The type.
	 * 
	 * @return string The link or (null) if not found.
	 */
	public static function tryGetLinkUrlFromType($type) {
		if (!$type instanceof \ReflectionClass) {
			// handle as string
			$type = trim($type);
			
			if (!class_exists($type) &&
				!interface_exists($type)) {
				
				return null;
			}
			
			$type = new \ReflectionClass($type);
		}
		
		if ($type->isUserDefined()) {
			$knownType = \phpLiveDoc\Services::tryGetType($type->getName());
			
			if ($knownType instanceof \ReflectionClass) {
				return sprintf('index.php?m=typeDetails&t=%s',
						       urlencode($knownType->getName()));
			}
		}
		else {
			// PHP type
			
			return sprintf('http://php.net/manual/en/class.%s.php',
					       trim(strtolower($type->getName())));
		}
		
		return null;
	}
}
