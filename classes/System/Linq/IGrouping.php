<?php

/**
 *  LINQ concept for PHP
 *  Copyright (C) 2015  Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 *    
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 3.0 of the License, or (at your option) any later version.
 *    
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 *    Lesser General Public License for more details.
 *    
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library.
 */


namespace System\Linq;


/**
 * Describes a group of elements.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IGrouping extends \Countable, \IteratorAggregate {
    /**
     * Gets the items of that group.
     * 
     * @return IEnumerable The items of the group.
     */
    // function getIterator();
    
    /**
     * Gets the key of the group.
     * 
     * @return mixed The key.
     */
    function key();
}
