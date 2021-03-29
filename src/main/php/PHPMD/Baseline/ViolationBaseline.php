<?php

namespace PHPMD\Baseline;

class ViolationBaseline
{
    /** @var string */
    private $ruleName;

    /** @var string */
    private $filename;

    /**
     * @param string $ruleName
     * @param string $filename
     */
    public function __construct($ruleName, $filename)
    {
        $this->ruleName = $ruleName;
        $this->filename = $filename;
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
    public function getFilename()
    {
        return $this->filename;
    }
}
