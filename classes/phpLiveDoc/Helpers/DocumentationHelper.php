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


namespace phpLiveDoc\Helpers;

use System\Linq\Enumerable;


/**
 * Helper class for documentation operations.
 *
 * @author Marcel Joachim Kloubert <marcel.kloubert@gmx.net>
 */
final class DocumentationHelper {
    private function __construct() {
    }
    

    /**
     * Outputs a list of examples.
     * 
     * @param \Traversable|array $examples The list of examples to output.
     */
    public static function outputExamples($examples) {
        if (!is_array($examples)) {
            if ($examples instanceof \phpLiveDoc\Examples\Example) {
                // single instance
                $examples = array($examples);
            }
            else {
                // convert to array
                $arr = array();
                foreach ($examples as $ex) {
                    $arr[] = $ex;
                }
                
                $examples = $arr;
            }
        }
        
        if (!empty($examples)) {
            ?>
            <h3>Examples</h3>
            <ul class="tabs" data-tab>
                <?php 
                
                foreach ($examples as $i => $e) {
                    $elementId = 'funcExampleCode' . trim($i);
                    $isActive = $i < 1;
                    
                    $title = trim($e->Title);
                    if (empty($title)) {
                        $title = 'Example #' . trim($i + 1);
                    }
                    
                    ?>
                    <li class="tab-title<?php echo !$isActive ? '' : ' active'; ?>"><a href="#<?php echo $elementId; ?>"><?php echo htmlentities($title); ?></a></li>
                    <?php
                }
                
                ?>
            </ul>
            
            <div class="tabs-content">
                <?php 
                
                foreach ($examples as $i => $e) {
                    $elementId = 'funcExampleCode' . trim($i);
                    $isActive  = $i < 1;
                    
                    ?>
                    <div style="padding-left: 1em;" class="content<?php echo !$isActive ? '' : ' active'; ?>" id="<?php echo $elementId; ?>"><?php 
                        
                        foreach ($e->getCodes() as $code) {
                            ?>
                            <?php 
                            
                            $codeTitle = $code->getTitle();
                            if (!empty($codeTitle)) {
                                ?><strong><?php echo htmlentities($codeTitle); ?></strong><?php
                            }
                            
                            ?>
                            <p><pre><code class="<?php echo $code->getLang(); ?>"><?php
                                echo htmlentities(trim($code->getSourceCode()));
                            ?></code></pre></p>
                            <?php
                        }
                        
                        ?>
                    </div><?php
                }
                
                ?>
            </div>
            <?php
        }
    }
    
    /**
     * Outputs the list of documentation tags (if available).
     * 
     * @param mixed $doc The documentation data.
     * 
     * @return bool Table was outputted or not.
     */
    public static function outputTagListOfDocBlock($doc) {
        if (!$doc instanceof \phpDocumentor\Reflection\DocBlock) {
            $doc = new \phpDocumentor\Reflection\DocBlock($doc);
        }
        
        $tags = Enumerable::fromArray($doc->getTags())
                          ->where(function(\phpDocumentor\Reflection\DocBlock\Tag $t) {
                                      if ($t instanceof \phpDocumentor\Reflection\DocBlock\Tag\ParamTag) {
                                            // @param
                                            return false;
                                        }
                                  
                                        return true;
                                  })
                           ->select(function(\phpDocumentor\Reflection\DocBlock\Tag $t) {
                                          $result        = new \stdClass();
                                          $result->name  = '@' . $t->getName();
                                          $result->desc  = implode(' ', $t->getParsedDescription());

                                          return $result;
                                    })
                           ->orderBy(function (\stdClass $x) {
                                           return trim(strtolower($x->name));
                                     })
                           ->toArray();
        if (!empty($tags)) {
            ?>
            <a name="tags"></a>
            <h3>Tags</h3>
    
            <table class="pdlFullWidth">
              <thead>
                <tr>
                  <th class="pdlNameCol">Name</th>
                  <th>Description</th>
                </tr>
              </thead>
              
              <tbody>
                <?php
                    foreach ($tags as $t) {
                        ?>
                        <tr>
                          <td><?php echo htmlentities($t->name); ?></td>
                          <td><?php echo self::parseForHtml($t->desc); ?></td>
                        </tr>
                        <?php
                    }
                ?>
              </tbody>
            </table>
            <?php
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Parses a string for HTML output.
     * 
     * @param string $str The string to parse.
     * 
     * @return string The parsed string.
     */
    public static function parseForHtml($str) {
        $result = htmlentities($str);
        
        $result = str_ireplace("\n", '<br />'                  , $result);
        $result = str_ireplace("\r", ''                        , $result);
        $result = str_ireplace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $result);
        
        // email adresses
        $result = preg_replace('/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})/i',
                               '<a href="mailto:$0">$0</a>',
                               $result);
        
        return $result;
    }
}
