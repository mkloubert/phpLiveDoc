<?php

use System\Linq\Enumerable;
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


defined('PLD_GLOBAL') or die();

define('PLD_BOOT', true, false);


$conf = \phpLiveDoc\Services::conf();

// load functions
if (isset($conf['functions'])) {
    $getDefinedFuncs = function() {
        $df = get_defined_functions();
        $result = array_merge($df['internal'], $df['user']);      
        
        unset($df);
        return $result;
    };
    
    $prevLoadedFuncs = $getDefinedFuncs();
    
    foreach ($conf['functions'] as $entry) {
        if (!isset($entry['source'])) {
            continue;
        }
        
        switch (trim(strtolower($entry['source']))) {
            case 'include_file':
                if (isset($entry['path'])) {
                    require_once realpath($entry['path']);
                }
                break;
        }
        
        $loadedFuncs = $getDefinedFuncs();

        $diffLoadedFuncs = array_diff($loadedFuncs, $prevLoadedFuncs);

        foreach ($diffLoadedFuncs as $funcName) {
            \phpLiveDoc\Services::registerFunc($funcName);
        }
        
        $prevLoadedFuncs = $loadedFuncs;
        unset($loadedFuncs);
    }
    
    unset($prevLoadedFuncs);
}

// load classes
if (isset($conf['classes'])) {
    $getDeclaredTypes = function() {
        return array_merge(get_declared_classes(),
                           get_declared_interfaces());
    };
    
    $prevLoadedClasses = $getDeclaredTypes();
    
    foreach ($conf['classes'] as $entry) {
        if (!isset($entry['source'])) {
            continue;
        }
        
        switch (trim(strtolower($entry['source']))) {
            case 'include_file':
                if (isset($entry['path'])) {
                    require_once realpath($entry['path']);
                }
                break;
                
            case 'include_files':
                if (isset($entry['paths'])) {
                    foreach ($entry['paths'] as $p) {
                        require_once realpath($p);
                    }
                }
                break;
        }
        
        $loadedClasses     = $getDeclaredTypes();
        $diffLoadedClasses = array_diff($loadedClasses, $prevLoadedClasses);
        
        foreach ($diffLoadedClasses as $typeName) {
            \phpLiveDoc\Services::registerType($typeName);
        }
        
        $prevLoadedClasses = $loadedClasses;
        unset($loadedClasses);
    }
    
    unset($prevLoadedClasses);
}

// load constants
if (isset($conf['constants'])) {
    $getDefinedConstants = function() {
        return array_keys(get_defined_constants());
    };
    
    $prevLoadedConstants = $getDefinedConstants();
    
    foreach ($conf['constants'] as $entry) {
        if (!isset($entry['source'])) {
            continue;
        }
        
        switch (trim(strtolower($entry['source']))) {
            case 'include_file':
                if (isset($entry['path'])) {
                    require_once realpath($entry['path']);
                }
                break;
        }
        
        $loadedConstants     = $getDefinedConstants();
        $diffLoadedConstants = array_diff($loadedConstants, $prevLoadedConstants);

        foreach ($diffLoadedConstants as $constName) {
            \phpLiveDoc\Services::registerConstant($constName);
        }
        
        $prevLoadedClasses = $loadedConstants;
        unset($diffLoadedConstants);
    }
    
    unset($prevLoadedConstants);
}

