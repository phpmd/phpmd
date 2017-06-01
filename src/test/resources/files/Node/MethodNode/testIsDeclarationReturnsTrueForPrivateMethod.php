<?php
class testIsDeclarationReturnsTrueForPrivateMethod extends testIsDeclarationReturnsTrueForPrivateMethod_parent
{
    private function testIsDeclarationReturnsTrueForPrivateMethod()
    {
        return false;
    }
}

class testIsDeclarationReturnsTrueForPrivateMethod_parent
{
    private function testIsDeclarationReturnsTrueForPrivateMethod()
    {
        return true;
    }
}
