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


error_reporting(E_ALL);

define('PLD_INDEX', true, false);

require_once './global.inc.php';


try {
	// get module name
	$moduleName = null;
	if (isset($_REQUEST['m'])) {
		$moduleName = $_REQUEST['m'];
	}
	
	

	// get module instance
	$module = \phpLiveDoc\Modules\Module::getModuleSafe($moduleName);
	
	// execute
	$output = $module->execute();
	if (!$output instanceof \Exception) {
		// output result of module
		{
			$view = new \phpLiveDoc\Views\SimpleView();
			 
			// set vars
			$view->content = $output;
			$view->title   = \phpLiveDoc\Page\Settings::$Title;
			 
			$view->renderAndOutput(\phpLiveDoc\Page\Settings::$View);
		}
	}
	else {
		throw $output;
	}
}
catch (\Exception $e) {
	die($e);
}
