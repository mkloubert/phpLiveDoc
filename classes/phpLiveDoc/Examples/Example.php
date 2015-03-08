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


namespace phpLiveDoc\Examples;

use phpLiveDoc\Object as TM_Object;
use System\Linq\Enumerable;
use Zend\Db\Adapter\Platform\Oracle;


/**
 * Stores data of an example.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Example extends TM_Object {
    /**
     * @var array
     */
    private $_codes = array();
    /**
     * @var string
     */
    public $Title;
    
    
    private function __construct() {
    }
    
    
    private static function extractExamples(array &$examples, $xmlFile) {
        if (false === $xmlFile) {
            return;
        }
        
        try {
            $xml = @simplexml_load_file($xmlFile);
        
            if ($xml instanceof \SimpleXMLElement) {
                foreach ($xml->children() as $child) {
                    if ('example' != $child->getName()) {
                        continue;
                    }
        
                    try {
                        $examples[] = self::fromXml($child);
                    }
                    catch (\Exception $ex) {
                        // ignore here
                    }
                }
            }
        }
        catch (\Exception $ex) {
            // ignore here
        }
    }
    
    /**
     * Creates a list of example objects for a function.
     * 
     * @param  \ReflectionFunction|string $func The underlying function.
     * 
     * @return \System\Collections\Generic\IEnumerable The list of examples.
     */
    public static function fromFunc($func) {
        if (!$func instanceof \ReflectionFunction) {
            $func = new \ReflectionFunction($func);
        }
        
        $result = array();
        
        $examplesDir = realpath(PLD_DIR_EXAMPLES);
        if (false !== $examplesDir) {
            $funcsDir = realpath($examplesDir . DIRECTORY_SEPARATOR . 'funcs');
            if (false !== $funcsDir) {
                $xmlFile = realpath($funcsDir . DIRECTORY_SEPARATOR .
                                    sprintf('%s.xml',
                                            trim(strtolower($func->getName()))));
                
                self::extractExamples($result, $xmlFile);
            }
        }
        
        return Enumerable::fromArray($result);
    }
    
    /**
     * Creates a list of example objects for a method.
     * 
     * @param  \ReflectionMethod|string $method The underlying method.
     * 
     * @return \System\Collections\Generic\IEnumerable The list of examples.
     */
    public static function fromMethod($method) {
        if (!$method instanceof \ReflectionMethod) {
            $method = new \ReflectionMethod($method);
        }
        
        $result = array();
        
        $examplesDir = realpath(PLD_DIR_EXAMPLES);
        if (false !== $examplesDir) {
            $methodsDir = realpath($examplesDir . DIRECTORY_SEPARATOR . 'methods');
            if (false !== $methodsDir) {
                $type = $method->getDeclaringClass();
                
                $xmlFileName  = str_ireplace('\\', '.', $type->getName());
                $xmlFileName .= '.' . $method->getName();
                
                $xmlFile = realpath($methodsDir . DIRECTORY_SEPARATOR .
                                    sprintf('%s.xml',
                                            trim($xmlFileName)));
                
                self::extractExamples($result, $xmlFile);
            }
        }
        
        return Enumerable::fromArray($result);
    }
    
    /**
     * Creates a list of example objects for a type (class or interface).
     * 
     * @param  \ReflectionClass|string $type The underlying type.
     * 
     * @return \System\Collections\Generic\IEnumerable The list of examples.
     */
    public static function fromType($type) {
        if (!$type instanceof \ReflectionClass) {
            $type = new \ReflectionClass($type);
        }
        
        $result = array();
        
        $examplesDir = realpath(PLD_DIR_EXAMPLES);
        if (false !== $examplesDir) {
            $typesDir = realpath($examplesDir . DIRECTORY_SEPARATOR . 'types');
            if (false !== $typesDir) {
                $xmlFileName = str_ireplace('\\', '.', $type->getName());
                
                $xmlFile = realpath($typesDir . DIRECTORY_SEPARATOR .
                                    sprintf('%s.xml',
                                            trim($xmlFileName)));
                
                self::extractExamples($result, $xmlFile);
            }
        }
        
        return Enumerable::fromArray($result);
    }
    
    /**
     * Creates a new instance from XML data.
     * 
     * @param \SimpleXMLElement|string $xml The XML data.
     * 
     * @return \phpLiveDoc\Examples\Example The new instance.
     */
    public static function fromXml($xml) {
        if (!$xml instanceof \SimpleXMLElement) {
            $xml = trim($xml);
            
            if (file_exists($xml)) {
                $xml = simplexml_load_file($xml);
            }
            else {
                $xml = simplexml_load_string($xml);
            }
        }
        
        $result = new self();
        
        foreach ($xml->attributes() as $attrib) {
            switch ($attrib->getName()) {
                case 'title':
                    $result->Title = trim($attrib);
                    break;
            }
        }
        
        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'code':
                    try {
                        $result->_codes[] = Code::fromXml($result, $child);
                    }
                    catch (\Exception $ex) {
                    }
                    break;
            }
        }
        
        return $result;
    }
    
    /**
     * Returns the list of source codes.
     * 
     * @return \System\Collections\Generic\IEnumerable The list of codes.
     */
    public function getCodes() {
        return Enumerable::fromArray($this->_codes);
    }
}
