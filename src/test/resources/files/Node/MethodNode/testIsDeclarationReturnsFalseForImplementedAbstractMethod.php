<?php
class testIsDeclarationReturnsFalseForImplementedAbstractMethodClass
    extends testIsDeclarationReturnsFalseForImplementedAbstractMethodParentClass
{
    public function testIsDeclarationReturnsFalseForImplementedAbstractMethod($foo)
    {

    }
}

abstract class testIsDeclarationReturnsFalseForImplementedAbstractMethodParentClass
{
    public abstract function testIsDeclarationReturnsFalseForImplementedAbstractMethod($foo);
}
