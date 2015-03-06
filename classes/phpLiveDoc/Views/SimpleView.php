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


namespace phpLiveDoc\Views;

use \phpLiveDoc\Object as TM_Object;


/**
 * Simple way to render a view script.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class SimpleView extends TM_Object {
    private $_vars = array();
    
    
    /**
     * Gets a property value.
     * 
     * @param string $name The name of the property.
     * 
     * @return mixed The value of the property.
     */
    public function __get($name) {
        return $this->_vars[static::normalizeVarName($name)];
    }
    
    /**
     * Checks if a property is set.
     * 
     * @param string $name The name of the property.
     * 
     * @return boolean Is set or not.
     */
    public function __isset($name) {
        return isset($this->_vars[static::normalizeVarName($name)]);
    }
    
    /**
     * Sets a property value.
     *
     * @param string $name The name of the property.
     */
    public function __set($name, $value) {
        $this->_vars[static::normalizeVarName($name)] = $value;
    }
    
    /**
     * Unsets a property.
     * 
     * @param string $name The name of the property.
     */
    public function __unset($name) {
        unset($this->_vars[static::normalizeVarName($name)]);
    }
    
    
    /**
     * Normalizes a variable name.
     * 
     * @param string $name The input name.
     * 
     * @return string The normalized / parsed name.
     */
    protected static function normalizeVarName($name) {
        return trim(strtolower($name));
    }
    
    /**
     * Renders a view.
     * 
     * @param string $name The name of the view (script).
     *                     An empty value indicates that no script should
     *                     be executed.
     * 
     * @return mixed The rendered result of the view script.
     *               (false) indicates that there is no data to output.
     */
    public function render($name) {
        $result = false;

        $tpl = trim(\phpLiveDoc\Page\Settings::$View);
        if (!empty($tpl)) {
            // renderer
            $renderer = new \phpLiveDoc\Views\Renderer();
            $renderer->resolver()->addPaths(array(
                PLD_DIR_VIEWS,
            ));
            
            // ViewModel
            $vm = new \phpLiveDoc\Views\ViewModel();
            $vm->setTemplate($tpl);
             
            // set variables
            foreach ($this->_vars as $n => $v) {
                $vm->setVariable($n, $v);
            }
             
            $result = $renderer->render($vm);
        }
        
        return $result;
    }
    
    /**
     * Renders and outputs a view.
     * 
     * @param string $name The name of render script.
     * 
     * @return boolean Data was outputted or not.
     */
    public final function renderAndOutput($name) {
        $output = $this->render($name);
        if (false !== $output) {
            echo $output;
            return true;
        }
        
        // no data to output
        return false;
    }
}
