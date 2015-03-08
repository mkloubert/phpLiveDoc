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


defined('PLD_INDEX') or die();

define('PLD_GLOBAL', true, false);


define('PLD_DIR_ROOT'    , '.' . DIRECTORY_SEPARATOR                      , false);
define('PLD_DIR_CLASSES' , PLD_DIR_ROOT . 'classes'  . DIRECTORY_SEPARATOR, false);
define('PLD_DIR_EXAMPLES', PLD_DIR_ROOT . 'examples' . DIRECTORY_SEPARATOR, false);
define('PLD_DIR_MODULES' , PLD_DIR_ROOT . 'modules'  . DIRECTORY_SEPARATOR, false);
define('PLD_DIR_VIEWS'   , PLD_DIR_ROOT . 'views'    . DIRECTORY_SEPARATOR, false);


// update include paths
set_include_path(get_include_path() . PATH_SEPARATOR
                                    . PLD_DIR_CLASSES);


/**
 * Autoloader
 *
 * @param string $clsName The name of the class to load.
 */
function __autoload($clsName) {
    require_once sprintf('%s%s%s',
                         PLD_DIR_CLASSES,
                         str_ireplace('\\', DIRECTORY_SEPARATOR, $clsName),
                         '.php');
}
