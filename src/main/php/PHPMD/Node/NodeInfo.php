<?php


namespace PHPMD\Node;

class NodeInfo
{
    /**
     * The full filepath of this violation
     *
     * @readonly
     * @var string|null
     */
    public $fileName;

    /**
     * Namespace of the owning/context class or interface of this violation.
     *
     * @readonly
     * @var string
     */
    public $namespaceName;

    /**
     * Name of the owning/context class or interface of this violation.
     *
     * @readonly
     * @var string|null
     */
    public $className;

    /**
     * The name of a method or <b>null</b> when this violation has no method
     * context.
     *
     * @readonly
     * @var string|null
     */
    public $methodName;

    /**
     * The name of a function or <b>null</b> when this violation has no function
     * context.
     *
     * @readonly
     * @var string|null
     */
    public $functionName;

    /**
     * The start line number of this violation
     *
     * @readonly
     * @var int
     */
    public $beginLine;

    /**
     * The end line number of this violation
     *
     * @readonly
     * @var int
     */
    public $endLine;

    /**
     * @param string|null $fileName
     * @param string $namespaceName
     * @param string|null $className
     * @param string|null $methodName
     * @param string|null $functionName
     * @param int $beginLine
     * @param int $endLine
     */
    public function __construct(
        $fileName,
        $namespaceName,
        $className,
        $methodName,
        $functionName,
        $beginLine,
        $endLine
    ) {
        $this->fileName      = $fileName;
        $this->namespaceName = $namespaceName;
        $this->className     = $className;
        $this->methodName    = $methodName;
        $this->functionName  = $functionName;
        $this->beginLine     = $beginLine;
        $this->endLine       = $endLine;
    }
}
