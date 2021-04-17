<?php

namespace PHPMD\Baseline;

class ViolationBaseline
{
    /** @var string */
    private $ruleName;

    /** @var string */
    private $fileName;

    /** @var string|null */
    private $methodName;

    /**
     * @param string      $ruleName
     * @param string      $fileName
     * @param string|null $methodName
     */
    public function __construct($ruleName, $fileName, $methodName)
    {
        $this->ruleName   = $ruleName;
        $this->fileName   = $fileName;
        $this->methodName = $methodName;
    }

    /**
     * @return string
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return string|null
     */
    public function getMethodName()
    {
        return $this->methodName;
    }
}
