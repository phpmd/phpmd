<?php

namespace PHPMD\Node;

class NodeInfo
{
    /**
     * @param ?string $fileName The full filepath of this violation.
     * @param ?string $namespaceName Namespace of the owning/context class or interface of this violation.
     * @param ?string $className Name of the owning/context class or interface of this violation.
     * @param ?string $methodName The name of a method or <b>null</b> when this violation has no method context.
     * @param ?string $functionName The name of a function or <b>null</b> when this violation has no function context.
     * @param int $beginLine The start line number of this violation.
     * @param int $endLine The end line number of this violation.
     */
    public function __construct(
        public readonly ?string $fileName,
        public readonly ?string $namespaceName,
        public readonly ?string $className,
        public readonly ?string $methodName,
        public readonly ?string $functionName,
        public readonly int $beginLine,
        public readonly int $endLine,
    ) {
    }
}
