<?php

namespace PHPMD\Baseline;

class BaselineSet
{
    /** @var array<string, list<ViolationBaseline>> */
    private $violations = [];

    public function addEntry(ViolationBaseline $entry): void
    {
        $this->violations[$entry->getRuleName()][] = $entry;
    }

    /**
     * @param string      $ruleName
     * @param string      $fileName
     * @param string|null $methodName
     */
    public function contains($ruleName, $fileName, $methodName): bool
    {
        if (!isset($this->violations[$ruleName])) {
            return false;
        }

        // normalize slashes in file name
        $fileName = str_replace('\\', '/', $fileName);

        foreach ($this->violations[$ruleName] as $baseline) {
            if ($baseline->matches($fileName, $methodName)) {
                return true;
            }
        }

        return false;
    }
}
