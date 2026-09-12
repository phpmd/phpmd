<?php

namespace PHPMD\Utility;

use PDepend\Source\AST\ASTClosure;
use PDepend\Source\AST\ASTNode as PDependNode;
use PHPMD\AbstractNode;
use PHPMD\AbstractTestCase;
use PHPMD\Node\ASTNode;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

#[CoversClass(Seeker::class)]
class SeekerTest extends AbstractTestCase
{
    public function testGetOwningCallableReturnsEnclosingFunctionForFreeVariable(): void
    {
        $function = $this->getFunction();
        $variable = $this->findVariableWithClosureAncestor($function, '$a');

        $scope = Seeker::fromNode($variable)->getOwningCallable('$a');

        static::assertNotNull($scope);
        static::assertSame($function->getNode(), $scope->getNode());
    }

    public function testGetOwningCallableReturnsClosureWhenParameterShadowsOuterVariable(): void
    {
        $function = $this->getFunction();
        $variable = $this->findVariableWithClosureAncestor($function, '$b');

        $scope = Seeker::fromNode($variable)->getOwningCallable('$b');

        static::assertNotNull($scope);
        static::assertNotSame($function->getNode(), $scope->getNode());
        static::assertTrue($scope->isInstanceOf(ASTClosure::class));
    }

    public function testGetOwningCallableReturnsNullWhenNoEnclosingCallable(): void
    {
        $mock = $this->getMockBuilder(PDependNode::class)->getMock();
        $variable = new ASTNode($mock, __FILE__);

        static::assertNull(Seeker::fromNode($variable)->getOwningCallable('$a'));
    }

    /**
     * Finds the variable with the given image that is nested inside a
     * closure/arrow function, as opposed to its outer declaration.
     *
     * @param AbstractNode<PDependNode> $node
     * @return AbstractNode<PDependNode>
     * @throws RuntimeException
     */
    private function findVariableWithClosureAncestor(AbstractNode $node, string $image): AbstractNode
    {
        foreach ($node->findChildrenOfTypeVariable() as $variable) {
            if ($variable->getImage() === $image && $variable->getParentOfType(ASTClosure::class) !== null) {
                return $variable;
            }
        }

        throw new RuntimeException("Cannot locate variable $image nested in a closure.");
    }
}
