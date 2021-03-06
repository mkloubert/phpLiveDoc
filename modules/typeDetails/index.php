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


$type = null;
if (isset($_REQUEST['t'])) {
    $typeName = trim($_REQUEST['t']);
    if (!empty($typeName)) {
        $type = \phpLiveDoc\Services::tryGetType($typeName);
    }
}

$typeDoc = null;
if ($type instanceof \ReflectionClass) {
    $typeDoc = new \phpDocumentor\Reflection\DocBlock($type->getDocComment());
}

$getNameOfReflectorItem = function($r) {
    return $r->getName();
};

$typeLinkOrString = function($type) {
    $result = $type;
    
    $typeLink = \phpLiveDoc\Helpers\ReflectionHelper::tryGetLinkUrlFromType($type);
    if (!is_null($typeLink)) {
        $result = sprintf('<a href="%s" target="_blank">%s</a>',
                          $typeLink,
                          $type);
    }
    
    return $result;
};

$getNameOfReflectorItemWithLink = function($r) use ($getNameOfReflectorItem, $typeLinkOrString) {
    $result = $getNameOfReflectorItem($r);
    
    return $typeLinkOrString($result);
};

$getNameOfReflectorItemForSort = function($r) use ($getNameOfReflectorItem) {
    return trim(strtolower($getNameOfReflectorItem($r)));
};

?>

<style type="text/css">
    h3 {
        margin-top: 1em;
    }
</style>

<?php
    if ($typeDoc instanceof \phpDocumentor\Reflection\DocBlock) {
        $typeName = $type->getName();
        $typeDesc = $typeDoc->getText();
        
        $kindOfType = 'class';
        if ($type->isInterface()) {
            $kindOfType = 'interface';
        }
        
        \phpLiveDoc\Page\Settings::$Title = sprintf('%s %s',
                                                    $typeName, $kindOfType);
        
?>
    <ul class="breadcrumbs">
      <li><a href="index.php">Home</a></li>
      <li><a href="index.php#classesAndInterfaces">Classes and interfaces</a></li>
      <li class="current"><a href="#"><?php echo htmlentities($type->getName()); ?></a></li>
    </ul>

    <h2><?php echo htmlentities($type->getShortName() . ' ' . $kindOfType); ?></h2>
    
    <?php
    
    $typeNs = trim($type->getNamespaceName());
    
    $typeNsCaption = $typeNs;
    if (empty($typeNsCaption)) {
        $typeNsCaption = '(root)';
    }
    
    ?><h6 class="subheader">declared in namesapce <a href="index.php?ns=<?php echo urlencode($typeNs); ?>" target="_blank"><?php echo htmlentities($typeNsCaption); ?></a></h6><?php
    
    ?>
    
    <p style="margin-top: 2em;"><?php echo htmlentities($typeDesc); ?></p>
    
    <h3>Syntax</h3>
    <pre><code class="php"><?php 
    
    $prefix = '';
    $suffix = '';
    if ($type->isInterface()) {
        // interface
        
        $interfaces = Enumerable::fromArray($type->getInterfaces())
                                ->orderBy($getNameOfReflectorItemForSort)
                                ->toArray();
                                          
        if (!empty($interfaces)) {
            $suffix .= ' extends ' . Enumerable::fromArray($interfaces)
                                               ->select($getNameOfReflectorItemWithLink)
                                               ->stringJoin(', ');
        }
    }
    else {
        // class
        
        $parent = $type->getParentClass();
        if ($parent instanceof \ReflectionClass) {
            $suffix .= ' extends ' . $typeLinkOrString($parent->getName());
        }
        
        $interfaces = Enumerable::fromArray($type->getInterfaces())
                                ->orderBy($getNameOfReflectorItemForSort)
                                ->toArray();
              
        if (!empty($interfaces)) {
            $suffix .= ' implements ' . Enumerable::fromArray($interfaces)
                                                  ->select($getNameOfReflectorItemWithLink)
                                                  ->stringJoin(', ');
        }
    }
    
    if ($type->isFinal()) {
        $prefix = 'final ';
    }
    
    if ($type->isAbstract()) {
        $prefix = 'abstract ';
    }

?><?php echo $prefix; ?><?php echo htmlentities($kindOfType); ?> <?php echo htmlentities($typeName); ?><?php echo $suffix; ?> {
    // members...
}
<?php
    
    ?></code></pre>
    
    <?php 
    
    $examples = \phpLiveDoc\Examples\Example::fromType($typeName);
    \phpLiveDoc\Helpers\DocumentationHelper::outputExamples($examples);
    
    ?>

    <h3>Members</h3>
    
    <a name="constants"></a>
    <h4>Constants</h4>
    <?php 
    
    $contants = $type->getConstants();
    if (!empty($contants)) {
        uksort($contants, function($x, $y) {
            return strcmp(trim(strtolower($x)),
                          trim(strtolower($y)));
        });
        
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
          
          foreach ($contants as $constName => $constValue) {
                ?>
                <tr>
                  <td><?php echo htmlentities($constName); ?></td>
                  <td><?php echo htmlentities(var_export($constValue, true)); ?></td>
                </tr>
                <?php
          }
          
          ?>
          </tbody>
        </table>
        <?php
    }
    else {
        ?><div data-alert class="alert-box secondary">No constants found.</div><?php
    }
    
    ?>
    
    <a name="methods"></a>
    <h4>Methods</h4>
    <?php
    
    $methods = $type->getMethods();
    if (!empty($methods)) {
        ?>
          <table class="pdlFullWidth">
            <thead>
              <tr>
                <th class="pdlNameCol">Name</th>
                <th>Value</th>
              </tr>
            </thead>
              
            <tbody>
              <?php
                foreach(Enumerable::fromArray($methods)
                                  ->orderBy($getNameOfReflectorItemForSort) as $m) {

                    $methodDoc = new \phpDocumentor\Reflection\DocBlock($m->getDocComment());
                        
              ?>
                
              <tr>
                <td>
                  <a href="index.php?m=methodDetails&t=<?php echo urlencode($typeName); ?>&tm=<?php echo urlencode($m->getName()); ?>">
                    <?php echo htmlentities($m->getName()); ?>
                  </a>
                </td>
                <td><?php echo htmlentities($methodDoc->getShortDescription()); ?></td>
              </tr>
                
              <?php } ?>
              </tbody>
            </table>
        <?php
    }
    else {
        ?><div data-alert class="alert-box secondary">No methods found.</div><?php
    }
    
    ?>
    
    <a name="properties"></a>
    <h4>Properties</h4>
    <?php 
    
    $properties = Enumerable::fromArray($type->getProperties())
                            ->orderBy(function(\ReflectionProperty $rp) {
                                          return trim(strtolower($rp->getName()));
                                      })
                            ->toArray();
    
    if (!empty($properties)) {
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
          
          foreach ($properties as $p) {
                $propDoc = new \phpDocumentor\Reflection\DocBlock($p->getDocComment());
              
                ?>
                <tr>
                  <td><?php echo htmlentities($p->getName()); ?></td>
                  <td><?php echo htmlentities($propDoc->getText()); ?></td>
                </tr>
                <?php
          }
          
          ?>
          </tbody>
        </table>
        <?php
    }
    else {
        ?><div data-alert class="alert-box secondary">No properties found.</div><?php
    }
    
    phpLiveDoc\Helpers\DocumentationHelper::outputTagListOfDocBlock($typeDoc);
    
    }
    else {
  ?>
    <div data-alert class="alert-box warning">Type not found!</div>
<?php
    }
?>