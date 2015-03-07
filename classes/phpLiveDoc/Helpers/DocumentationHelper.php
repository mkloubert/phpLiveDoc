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
                  <th>Name</th>
                  <th>Description</th>
                </tr>
              </thead>
              
              <tbody>
                <?php
                    foreach ($tags as $t) {
                        ?>
                        <tr>
                          <td><?php echo htmlentities($t->name); ?></td>
                          <td><?php echo htmlentities($t->desc); ?></td>
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
}
