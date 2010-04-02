<?php
/**
 * Simple test class
 */
class SourceWithoutViolations
{
    function doSomething()
    {
        if (time() % 42 === 0) {
            return 'foo';
        }
        return 'bar';
    }
}
