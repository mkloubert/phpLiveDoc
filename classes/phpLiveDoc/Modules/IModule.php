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

use phpLiveDoc\IObject as TM_IObject;


/**
 * Describes a module.
 * 
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
interface IModule extends TM_IObject {
	/**
	 * Executes that module.
	 * 
	 * @return mixed The output of the module.
	 */
    function execute();
    
    /**
     * Gets the name of that module.
     */
    function getName();
}
