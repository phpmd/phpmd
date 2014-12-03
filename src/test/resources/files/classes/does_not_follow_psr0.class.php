<?php
/**
 * @author Gerrit Addiks <gerrit@addiks.de>
 */

/**
 * Some class that stands as an example for classes not following PSR-0.
 */
class some_class_that_does_not_follow_psr0 extends \PHPMD\AbstractRule{
    
    /**
     * A method that returnes foo, bar and baz.
     *
     * @return string
     */
    public function getFooBarBaz(){
        return array('foo', 'bar', 'baz');
    }
    
    public function apply(\PHPMD\AbstractNode $node){
        
    }
    
}