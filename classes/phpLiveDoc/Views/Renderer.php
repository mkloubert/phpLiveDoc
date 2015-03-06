<?php

/**
 *  Handles documents and correspondence.
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


/**
 * Extended renderer.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
class Renderer extends \Zend\View\Renderer\PhpRenderer {
	/**
	 * Initializes a new instance of that class.
	 * 
	 * @param array $config
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
	}
}
