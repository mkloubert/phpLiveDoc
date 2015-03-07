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

if ($type instanceof \ReflectionClass) {
	$method = null;
	if (isset($_REQUEST['tm'])) {
		$methodName = trim($_REQUEST['tm']);
		if (!empty($methodName)) {
			try {
				$method = $type->getMethod($methodName);
			}
			catch (\ReflectionException $ex) {
				$method = null;
			}
		}
	}
	
	if ($method instanceof \ReflectionMethod) {
		$methodDoc = new \phpDocumentor\Reflection\DocBlock($method->getDocComment());
		
		$methodName   = $method->getName();
		$methodDesc   = $methodDoc->getText();
		$methodParams = $method->getParameters();
		
?>

<style type="text/css">
    h3 {
        margin-top: 1em;
    }
</style>

<ul class="breadcrumbs">
  <li><a href="index.php">Home</a></li>
  <li><a href="index.php#classesAndInterfaces">Classes and interfaces</a></li>
  <li><a href="index.php?m=typeDetails&t=<?php echo urlencode($type->getName()); ?>"><?php echo htmlentities($type->getName()); ?></a></li>
  <li><a href="index.php?m=typeDetails&t=<?php echo urlencode($type->getName()); ?>#methods">Methods</a></li>
  <li class="current"><a href="#"><?php echo htmlentities($methodName); ?></a></li>
</ul>

    <h2><?php echo htmlentities($methodName); ?> method</h2>
    
    <p><?php echo htmlentities($methodDesc); ?></p>
    
    <h3>Syntax</h3>
    <pre><code class="php"><?php
    	$prefix = '';
    	if ($method->isPublic()) {
    		$prefix = 'public ';
    	}
    	else if ($method->isProtected()) {
    		$prefix = 'protected ';
    	}
    	else if ($method->isPrivate()) {
    		$prefix = 'private ';
    	}
    	
    	if ($method->isStatic()) {
    		$prefix .= 'static ';
    	}
    	
    	if ($method->isAbstract()) {
    		$prefix .= 'abstract ';
    	}
    	
    	$paramList = '';
    	if (!empty($methodParams)) {
    		$paramList = Enumerable::fromArray($methodParams)
    		                       ->stringJoin(', ',
    		                       		        function(\ReflectionParameter $rp) {
    			                                    $paramTypes = array();
    			                                    if ($rp->isArray()) {
    			                                    	$paramTypes[] = 'array';
    			                                    }
    			                                    if ($rp->isCallable()) {
    			                                    	$paramTypes[] = 'callable';
    			                                    }
    			                                    
    			                                    if (empty($paramTypes)) {
    			                                    	$paramTypes[] = 'mixed';
    			                                    }
    			                                    
    			                                    $suffix = '';
    			                                    if ($rp->isOptional()) {
    			                                    	$suffix .= ' = ';
    			                                    	
    			                                    	if ($rp->isArray()) {
    			                                    		$suffix .= 'array()';
    			                                    	}
    			                                    	else {
    			                                    		$defVal = $rp->getDefaultValue();
    			                                    		if (is_null($defVal)) {
    			                                    			$suffix .= 'null';
    			                                    		}
    			                                    		else {
    			                                    			$suffix .= var_export($defVal, true);
    			                                    		}
    			                                    	}
    			                                    }
    			                                    
    			                                    return implode('|', $paramTypes) . 
    			                                           ' $' .$rp->getName() .
    			                                           $suffix;
    		                                    });
    	}
    	
echo $prefix; ?>function <?php echo htmlentities($methodName); ?>(<?php echo htmlentities($paramList); ?>) {
    // code...
}
</code></pre>

	<h3>Parameters</h3>
<?php

    
    if (!empty($methodParams)) {
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
    	foreach ($methodParams as $p) {
    		$paramDesc = '';
    		
    		$tags = $methodDoc->getTags();
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
		?><div data-alert class="alert-box warning">Method not found!</div><?php
	}
}
else {
	?><div data-alert class="alert-box warning">Type not found!</div><?php
}

