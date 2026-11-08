<?php

function testIsStaticPropertyName($key, $object)
{
    return [
        Foo::$plain,
        Foo::$indexed[$key],
        Foo::$chained[0]->method(),
        $object::$onObject[0],
        $object->$dynamic[0],
        Foo::$first->$second[0],
    ];
}
