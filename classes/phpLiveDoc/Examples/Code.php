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


class Code extends TM_Object {
    /**
     * @var Example
     */
    private $_example;
    /**
     * @var string
     */
    private $_lang;
    /**
     * @var string
     */
    private $_sourceCode;
    /**
     * @var string
     */
    private $_title;
    
    
    private function __construct() {
    }
    
    
    /**
     * Creates a new instance from XML data.
     * 
     * @param Example $example The underlying example object.
     * @param \SimpleXMLElement|string $xml The XML data.
     * 
     * @return \phpLiveDoc\Examples\Code The new instance.
     */
    public static function fromXml(Example $example, $xml) {
        if (!$xml instanceof \SimpleXMLElement) {
            $xml = trim($xml);
                
            if (file_exists($xml)) {
                $xml = simplexml_load_file($xml);
            }
            else {
                $xml = simplexml_load_string($xml);
            }
        }
        
        $result              = new self();
        $result->_example    = $example;
        $result->_lang       = 'php';
        $result->_sourceCode = null;
        
        foreach ($xml->attributes() as $attrib) {
            switch ($attrib->getName()) {
                case 'lang':
                    $result->_lang = trim(strtolower($attrib));
                    break;
                
                case 'title':
                    $result->_title = trim($attrib);
                    break;
            }
        }
        
        foreach ($xml->children() as $child) {
            switch ($child->getName()) {
                case 'source':
                    $result->_sourceCode = strval($child);
                    break;
            }
        }
        
        $result->_lang = trim(strtolower($result->_lang));
        switch ($result->_lang) {
            case 'htm':
            case 'html':
            case 'javascript':
            case 'php':
                break;
                
            case 'js':
                $result->_lang = 'javascript';
                break;
                
            case '':
                $result->_lang = 'php';
                break;
            
            default:
                $result->_lang = null;
                break;
        }
        
        if ('' == trim($result->_sourceCode)) {
            $result->_sourceCode = null;
        }
        
        if (empty($result->_title)) {
            $result->_title = null;
        }
        
        return $result;
    }
    
    /**
     * Gets the underlying example instance.
     * 
     * @return \phpLiveDoc\Examples\Example
     */
    public function getExample() {
        return $this->_example;
    }
    
    public function getLang() {
        return $this->_lang;
    }
    
    public function getSourceCode() {
        return $this->_sourceCode;
    }
    
    public function getTitle() {
        return $this->_title;
    }
}
