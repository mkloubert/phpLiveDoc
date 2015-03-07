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

require PLD_DIR_MODULES . 'common_module_include.php';

use System\Linq\Enumerable;


\phpLiveDoc\Page\Settings::setupForJson();


$funcs     = array();
$types     = array();
$constants = array();


$addResult = function(&$items, $displayText, $link) {
    if (count($items) > 4) {
        return false;
    }
    
    $newEntry        = new \stdClass();
    $newEntry->label = $displayText;
    $newEntry->link  = $link;
    $newEntry->value = '';
    
    return $items[] = $newEntry;
};

$wasFound = function($expr, $str) {
    $str = trim(strtolower($str));

    $expr = Enumerable::fromArray(explode(' ', $expr))
                      ->select(function($str) {
                                     return trim(strtolower($str));
                               })
                      ->where(function($str) {
                                    return !empty($str);
                              })
                      ->distinct();
    
    return $expr->all(function($eStr) use ($str) {
                          return false !== stripos($str, $eStr);
                      });
};

if (isset($_REQUEST['e'])) {
    $expr = trim(strtolower($_REQUEST['e']));
    
    if (strlen($expr) > 1) {
        // types
        {
            foreach (\phpLiveDoc\Services::getTypes() as $t) {
                $typeName = $t->getName();
                
                $typeKindOf   = 'class';
                $typeKindOfPL = 'classes';
                if ($t->isInterface()) {
                    $typeKindOf   = 'interface';
                    $typeKindOfPL = 'interfaces';
                }
                
                $searchHere  = '';
                $searchHere .= $typeName;
                $searchHere .= $typeKindOfPL;
                $searchHere .= 'types';
        
                if ($wasFound($expr, $searchHere)) {
                    $addResult($types,
                               sprintf('%s() %s', $typeName
                                                , $typeKindOf),
                               sprintf('index.php?m=typeDetails&t=%s',
                                       urlencode($typeName)));
                }
            }
        }
        
        // functions
        {
            foreach (\phpLiveDoc\Services::getFuncs() as $f) {
                $funcName = $f->getName();
                
                $funcKindOf = 'function';
                
                $searchHere  = '';
                $searchHere .= $funcName;
                $searchHere .= 'functions';
                $searchHere .= 'funcs';
                
                if ($wasFound($expr, $searchHere)) { 
                    $addResult($funcs,
                               sprintf('%s() %s', $funcName
                                                , $funcKindOf),
                               sprintf('index.php?m=funcDetails&f=%s',
                                       urlencode($funcName)));
                }
            }
        }
    }
}

$setResult(Enumerable::fromArray(array_merge($types, $funcs, $constants))
                     ->orderBy(function(\stdClass $item) {
                                    return trim(strtolower($item->label));
                               })
                     ->toArray());
