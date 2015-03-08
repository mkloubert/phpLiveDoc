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


\phpLiveDoc\Page\Settings::$Title = 'Home';


$namespace = null;
if (isset($_REQUEST['ns'])) {
    $namespace = trim($_REQUEST['ns']);
}

$namespaceFilter = function($r) use ($namespace) {
    if (is_null($namespace)) {
        return true;
    }
    
    return trim($r->getNamespaceName()) == $namespace;
};

$namespaceSorter = function($r) {
    return trim(strtolower(sprintf('%s %s',
                                   $r->getShortName(),
                                   $r->getNamespaceName())));
};

$outputNamespace = function($r) {
    $ns = trim($r->getNamespaceName());
    if (!empty($ns)) {
        ?>
        <br />
        <sub>declared in <a href="index.php?ns=<?php echo urlencode($ns); ?>"><?php echo htmlentities($ns); ?></a></sub>
        <?php
    }
};


if (is_null($namespace)) {
?>

<ul class="breadcrumbs">
  <li class="current"><a href="#">Home</a></li>
</ul>

<?php

}
else {

    $namespaceCaption = $namespace;
    if (empty($namespaceCaption)) {
        $namespaceCaption = '(root)';
    }
    
?>

<ul class="breadcrumbs">
  <a href="index.php">Home</a>
  <li class="current"><a href="#"><?php echo htmlentities($namespaceCaption); ?> namespace</a></li>
</ul>

<?php

}

?>

<a name="classesAndInterfaces"></a>
<h2>Classes and interfaces</h2><?php
$types = \phpLiveDoc\Services::getTypes()
                             ->where($namespaceFilter)
                             ->orderBy($namespaceSorter)
                             ->toArray();

if (!empty($types)) {
    ?>
    <table class="pdlFullWidth">
      <thead>
        <tr>
          <th class="pdlNameCol">Name</th>
          <th>Description</th>
        </tr>
      </thead>
      
      <tbody>
        <?php
          foreach($types as $t) {
              $doc = new \phpDocumentor\Reflection\DocBlock($t->getDocComment());
                
        ?>
        
        <tr>
          <td>
            <a href="index.php?m=typeDetails&t=<?php echo urlencode($t->getName()); ?>">
              <?php echo htmlentities($t->getShortName()); ?>
            </a>
            
            <?php $outputNamespace($t); ?>
          </td>
          <td>
            <?php echo htmlentities($doc->getShortDescription()); ?>
          </td>
        </tr>
        
        <?php } ?>
      </tbody>
    </table>
    <?php
    
    unset($types);
}
else {
    ?>
<div data-alert class="alert-box">No types defined.</div>
    <?php
}

?>

<a name="constants"></a>
<h2>Constants</h2><?php

$constants = \phpLiveDoc\Services::getConstants()
                                 ->toArray();
if (!empty($constants)) {
    ?>
    <table class="pdlFullWidth">
      <thead>
        <tr>
          <th class="pdlNameCol">Name</th>
          <th>Description</th>
        </tr>
      </thead>    
      
      <tbody>
    <?php    
    
    foreach ($constants as $cn) {
        ?>
        <tr>
          <td>
            <?php echo htmlentities($cn); ?>
          </td>
          
          <td>&nbsp;</td>
        </tr>
        <?php
    }
    
    ?>
      </tbody>
    </table>
    <?php
}
else {
?>
<div data-alert class="alert-box">No constants defined.</div>
<?php
}

?>

<a name="functions"></a>
<h2>Functions</h2><?php
$funcs = \phpLiveDoc\Services::getFuncs()
                             ->where($namespaceFilter)
                             ->orderBy($namespaceSorter)
                             ->toArray();
if (!empty($funcs)) {
    ?>
    <table class="pdlFullWidth">
      <thead>
        <tr>
          <th class="pdlNameCol">Name</th>
          <th>Description</th>
        </tr>
      </thead>
      
      <tbody>
        <?php
          foreach($funcs as $f) {
              $doc = new \phpDocumentor\Reflection\DocBlock($f->getDocComment());
                
        ?>
        
        <tr>
          <td>
            <a href="index.php?m=funcDetails&f=<?php echo urlencode($f->getName()); ?>">
              <?php
                  echo htmlentities($f->getShortName());
              ?>
              
              <?php $outputNamespace($f); ?>
            </a>
          </td>
          
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
<div data-alert class="alert-box">No functions defined.</div>
    <?php
}
