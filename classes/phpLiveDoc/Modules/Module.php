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


namespace phpLiveDoc\Modules;

use phpLiveDoc\Object as TM_Object;


/**
 * A module.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Module extends TM_Object implements IModule {
    private $_conf;
    private $_name;
    
    
    /**
     * Initailizes a new instance of that class.
     * 
     * @param string $name The name of the module.
     * 
     * @throws Exception Module does not exist.
     */
    public function __construct($name) {
        if (!static::exists($name)) {
            throw new \Exception('Module does not exist!');
        }
    
        $this->_name = static::normalizeName($name);
    }

    
    /**
     * (non-PHPdoc)
     * @see \phpLiveDoc\Modules\IModule::execute()
     */
    public function execute() {
        $result = false;
        
        ob_start();
        try {
            // define a closure that allows to
            // define custom output value / object
            // 
            // $setResult('My custom content')
            $hasCustomResult = false;
            $setResult = function($res) use (&$result, &$hasCustomResult) {
                $result = $res;
                $hasCustomResult = true;
            };
            
            require $this->getScriptPath();
            
            if (!$hasCustomResult) {
                $result = ob_get_contents();
            }

            ob_end_clean();
        }
        catch (\Exception $e) {
            ob_end_clean();
            
            $result = $e;
        }
        
        return $result;
    }
    
    /**
     * Checks if a module exists (by name).
     * 
     * @param string $name The name of the module.
     * 
     * @return boolean Module exists or not.
     */
    public static function exists($name) {
        $path = static::getScriptPathByName($name);
        if (empty($name)) {
            return false;
        }
        
        return file_exists($path);
    }
    
    /**
     * Gets the config for that module.
     * 
     * @return array The config data of that module.
     */
    public function getConfig() {
        if (!is_array($this->_conf)) {
            $file = realpath(sprintf('%s%s%s%s',
                                     PLD_DIR_MODULES,
                                     $this->getName(),
                                     DIRECTORY_SEPARATOR,
                                     'config.json'));
            
            if (false !== $file) {
                $jsonReader = new \Zend\Config\Reader\Json();
                
                $this->_conf = $jsonReader->fromFile($file);
            }
            else {
                $this->_conf = array();
            }
        }
        
        return $this->_conf;
    }
    
    /**
     * Returns a module safe, what means that if no module with a
     * specific name exists, an instance with the default module is
     * returned.
     * 
     * @param string $name The name of the module.
     * 
     * @return \PDM\Modules\Module The new module instance.
     */
    public static function getModuleSafe($name) {
        if (!static::exists($name)) {
            $name = 'index';
        }
        
        return new static($name);
    }
    
    /**
     * (non-PHPdoc)
     * @see \PDM\Modules\IModule::getName()
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Gets the root directory of that module.
     *
     * @return string The root directory of that module.
     */
    public function getPath() {
        return static::getModulePathByName($this->getName());
    }
    
    /**
     * Gets the root directory of a module by its name.
     * 
     * @param string $name The name of the module.
     *
     * @return string The root directory of the module or FALSE
     *                if an error occured.
     */
    public static function getPathByName($name) {
        $name = static::normalizeName($name);
        if (empty($name)) {
            // invalid name
            return false;
        }
         
        return PLD_DIR_MODULES . $name . DIRECTORY_SEPARATOR;
    }
    
    /**
     * Gets the full path of the module's script.
     * 
     * @return string The full path of the module's script.
     */
    protected function getScriptPath() {
        return static::getScriptPathByName($this->getName());      
    }
    
    /**
     * Returns the full path of a module by name.
     * 
     * @param string $name The name of the module.
     * 
     * @return boolean|string The script path of the module or
     *                           (false) if name is invalid.
     */
    protected static function getScriptPathByName($name) {
        $dir = static::getPathByName($name);
        if (empty($dir)) {
            // invalid directory
            return false;
        }
        
        return $dir . 'index.php';
    }
    
    /**
     * Normalizes a string that represents a module name.
     * 
     * @param string $name The input value.
     * 
     * @return string the normalized value.
     */
    protected static function normalizeName($name) {
        $name = trim($name);

        // path separators
        $name = str_ireplace('/'                , '', $name);
        $name = str_ireplace('\\'               , '', $name);
        $name = str_ireplace(DIRECTORY_SEPARATOR, '', $name);
        $name = str_ireplace(PATH_SEPARATOR     , '', $name);
        
        $name = str_ireplace('..', '', $name);
        
        // white spaces
        $name = str_ireplace("\t", '    ', $name);
        $name = str_ireplace("\r", ''    , $name);
        $name = str_ireplace("\n", ''    , $name);
        $name = str_ireplace(' ' , '_'   , $name);

        $name = trim($name);
        if (empty($name)) {
            $name = null;
        }
        
        return $name;
    }
}
