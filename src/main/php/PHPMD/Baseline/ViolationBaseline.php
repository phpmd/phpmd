<?php

namespace PHPMD\Baseline;

class ViolationBaseline
{
    /** @var string */
    private $ruleName;

    /** @var string */
    private $fileName;

    /**
     * @param string $ruleName
     * @param string $fileName
     */
    public function __construct($ruleName, $fileName)
    {
        $this->ruleName = $ruleName;
        $this->fileName = $fileName;
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
}
