<?php

namespace PHPMD\Baseline;

class ViolationBaseline
{
    /** @var string */
    private $ruleName;

    /** @var string */
    private $fileName;

    /** @var int */
    private $fileNameLength;

    /** @var string|null */
    private $methodName;

    /**
     * @param string      $ruleName
     * @param string      $fileName
     * @param string|null $methodName
     */
    public function __construct($ruleName, $fileName, $methodName)
    {
        $this->ruleName       = $ruleName;
        $this->fileName       = $fileName;
        $this->methodName     = $methodName;
        $this->fileNameLength = strlen($fileName);
    }

    /**
     * @return string
     */
    public function getRuleName()
    {
        return $this->ruleName;
    }

    /**
     * Test if the given filepath and method matches the baseline
     *
     * @param string      $filepath   the full filepath to match against
     * @param string|null $methodName the name of the method of the method, if any
     *
     * @return bool
     */
    public function matches($filepath, $methodName)
    {
        return $this->methodName === $methodName && substr($filepath, -$this->fileNameLength) === $this->fileName;
    }
}
