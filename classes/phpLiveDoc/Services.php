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


namespace phpLiveDoc;

use \System\Linq\Enumerable;
use \System\Collections\Generic\IEnumerable;


/**
 * Global services and data.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class Services {
    /**
     * @var array
     */
    private static $_constants = array();
    /**
     * @var array
     */
    private static $_conf      = null;
    /**
     * @var array
     */
    private static $_funcs     = array();
    /**
     * @var array
     */
    private static $_types     = array();
    
    
    private function __construct() {    
    }
    
    
    /**
     * Returns the app configuration.
     *
     * @return array The configuration data.
     */
    public static function conf() {
        if (is_null(self::$_conf)) {
            $jsonReader = new \Zend\Config\Reader\Json();
    
            self::$_conf = $jsonReader->fromFile(sprintf('%s%s',
                                                         PLD_DIR_ROOT, 'config.json'));
        }
    
        return self::$_conf;
    }
    
    /**
     * Returns the list of registrated constants.
     * 
     * @return \System\Collections\Generic\IEnumerable The list of constants.
     */
    public static function getConstants() {
        return Enumerable::fromArray(self::$_constants)
                         ->orderBy(function($cn) {
                                        return trim(strtolower($cn));
                                   });
    }
    
    /**
     * Returns the list of registrated functions.
     *
     * @return \System\Collections\Generic\IEnumerable The list of functions.
     */
    public static function getFuncs() {
        return Enumerable::fromArray(self::$_funcs)
                         ->orderBy(function(\ReflectionFunction $rf) {
                                       return trim(strtolower($rf->getName()));
                                   });
    }
    
    /**
     * Returns the list of registrated classes.
     *
     * @return \System\Collections\Generic\IEnumerable The list of classes.
     */
    public static function getTypes() {
        return Enumerable::fromArray(self::$_types)
        ->orderBy(function(\ReflectionClass $rc) {
            return trim(strtolower($rc->getName()));
        });
    }
    
    /**
     * Registers a constant.
     * 
     * @param string $cn The new of the constant.
     * 
     * @return string The name of the registrated constant.
     */
    public static function registerConstant($cn) {
        $cn = trim($cn);
        
        return self::$_constants[$cn] = $cn;
    }
    
    /**
     * Registers a defined function.
     * 
     * @param string $fn The name of the function.
     * 
     * @return \ReflectionFunction The underlying reflection object.
     */
    public static function registerFunc($fn) {
        $fn = trim($fn);
        $i  = trim(strtolower($fn));
    
        return self::$_funcs[$i] = new \ReflectionFunction($fn);
    }
    
    /**
     * Registers a declared type (class or interface, e.g.).
     *
     * @param string $tn The name of the type.
     *
     * @return \ReflectionClass The underlying reflection object.
     */
    public static function registerType($tn) {
        $tn = trim($tn);
    
        return self::$_types[$tn] = new \ReflectionClass($tn);
    }
    
    /**
     * Tries to return a function reflector object by name.
     *
     * @param string $fn The name of the function.
     *
     * @return \ReflectionFunction The object or (null) if not found.
     */
    public static function tryGetFunc($fn) {
        $fn = trim($fn);
        $i  = trim(strtolower($fn));
        
        if (isset(self::$_funcs[$i])) {
            return self::$_funcs[$i];
        }
        
        return null;
    }
    
    /**
     * Tries to return a type reflector object by name.
     * 
     * @param string $tn The name of the type.
     * 
     * @return \ReflectionClass The object or (null) if not found.
     */
    public static function tryGetType($tn) {
        $tn = trim($tn);
        
        if (isset(self::$_types[$tn])) {
            return self::$_types[$tn];
        }
        
        return null;
    }
}
