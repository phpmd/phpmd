<?php
class testIsDeclarationReturnsFalseForImplementedInterfaceMethodClass
    implements testIsDeclarationReturnsFalseForImplementedInterfaceMethodInterface
{
    public function testIsDeclarationReturnsFalseForImplementedInterfaceMethod($foo)
    {

    }
}

interface testIsDeclarationReturnsFalseForImplementedInterfaceMethodInterface
{
    function testIsDeclarationReturnsFalseForImplementedInterfaceMethod($foo);
}
