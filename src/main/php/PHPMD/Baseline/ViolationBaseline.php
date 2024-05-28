<?php

namespace PHPMD\Baseline;

class ViolationBaseline
{
    private readonly int $fileNameLength;

    public function __construct(
        private readonly string $ruleName,
        private readonly string $fileName,
        private readonly ?string $methodName,
    ) {
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
