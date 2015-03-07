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


?>

<style text="text/css">

table.fullWidth {
   width: 100%;
   margin-left: auto;
   margin-right: auto;
   max-width: initial;
}

</style>

<h2>Classes and interfaces</h2><?php
$types = \phpLiveDoc\Services::getTypes()
                             ->toArray();
if (!empty($types)) {
	?>
	<table class="fullWidth">
	  <thead>
	    <tr>
	      <th>Name</th>
	      <th>Description</th>
	    </tr>
	  </thead>
	  
	  <tbody>
	    <?php
	      foreach($types as $t) {
	      	  $doc = new \phpDocumentor\Reflection\DocBlock($t->getDocComment());
	      	  
	    ?>
	    
	    <tr>
	      <td><?php echo htmlentities($t->getName()); ?></td>
	      <td><?php echo htmlentities($doc->getShortDescription()); ?></td>
	    </tr>
	    
	    <?php } ?>
	  </tbody>
	</table>
	<?php
	
	unset($types);
}
else {
	?>
<div data-alert class="alert-box warning">No types defined.</div>
	<?php
}

?>

<h2>Functions</h2><?php
$funcs = \phpLiveDoc\Services::getFuncs()
                             ->toArray();
if (!empty($funcs)) {
	?>
	<table class="fullWidth">
	  <thead>
	    <tr>
	      <th>Name</th>
	      <th>Description</th>
	    </tr>
	  </thead>
	  
	  <tbody>
	    <?php
	      foreach($funcs as $f) {
	      	  $doc = new \phpDocumentor\Reflection\DocBlock($f->getDocComment());
	      	  
	    ?>
	    
	    <tr>
	      <td><?php echo htmlentities($f->getName()); ?></td>
	      <td><?php echo htmlentities($doc->getShortDescription()); ?></td>
	    </tr>
	    
	    <?php } ?>
	  </tbody>
	</table>
	<?php
	
	unset($funcs);
}
else {
	?>
<div data-alert class="alert-box warning">No functions defined.</div>
	<?php
}
?>