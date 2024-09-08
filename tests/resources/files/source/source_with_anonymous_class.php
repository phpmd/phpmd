<?php

class TestCase
{
    public function getAnonymousClass()
    {
        return new class {
            public function get($a): string
            {
                return $a;
            }
        };
    }
}
