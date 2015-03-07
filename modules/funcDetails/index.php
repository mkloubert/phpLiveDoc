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


$func = null;
if (isset($_REQUEST['f'])) {
    $funcName = trim($_REQUEST['f']);
    if (!empty($funcName)) {
        $func = \phpLiveDoc\Services::tryGetFunc($funcName);
    }
}

//
{
    if ($func instanceof \ReflectionFunction) {
        $funcDoc = new \phpDocumentor\Reflection\DocBlock($func->getDocComment());
        
        $funcName   = $func->getName();
        $funcDesc   = $funcDoc->getText();
        $funcParams = $func->getParameters();
        
?>

<style type="text/css">
    h3 {
        margin-top: 1em;
    }
</style>

<ul class="breadcrumbs">
  <li><a href="index.php">Home</a></li>
  <li><a href="index.php#functions">Functions</a></li>
  <li class="current"><a href="#"><?php echo htmlentities($funcName); ?></a></li>
</ul>

    <h2><?php echo htmlentities($funcName); ?> method</h2>
    
    <p><?php echo htmlentities($funcDesc); ?></p>
    
    <h3>Syntax</h3>
    <pre><code class="php"><?php
        $prefix = '';

        $paramList = \phpLiveDoc\Helpers\ReflectionHelper::createParameterList($funcParams);
        
echo $prefix; ?>function <?php echo htmlentities($funcName); ?>(<?php echo $paramList; ?>) {
    // code...
}
</code></pre>

    <h3>Parameters</h3>
<?php

    
    if (!empty($funcParams)) {
?>
    <table class="pdlFullWidth">
      <thead>
        <tr>
          <th>Name</th>
          <th>Description</th>
        </tr>
      </thead>
      
      <tbody>
<?php
        foreach ($funcParams as $p) {
            $paramDesc = '';
            
            $tags = $funcDoc->getTags();
            foreach ($tags as $tag) {
                if (!$tag instanceof \phpDocumentor\Reflection\DocBlock\Tag\ParamTag) {
                    continue;
                }
                
                if ($tag->getVariableName() != ('$' . $p->getName())) {
                    continue;
                }
                
                $paramDesc = $tag->getDescription();
            }
            
            $paramPrefix = '';
            $paramSuffix = '';
            if ($p->isOptional()) {
                $paramPrefix = '[';
                $paramSuffix = ']';
            }

            ?>
            <tr>
              <td><?php echo htmlentities(sprintf('%s$%s%s',
                                                    $paramPrefix,
                                                    $p->getName(), 
                                                    $paramSuffix)); ?></td>
              <td><?php echo htmlentities($paramDesc); ?></td>
            </tr>
            <?php
        }
        
?>
      </tbody>
<?php
    }
    else {
        ?><div data-alert class="alert-box secondary">No parameters found.</div><?php
    }

?>

<?php
    }
    else {
        ?><div data-alert class="alert-box warning">Function not found!</div><?php
    }
}


