<?php

namespace PHPMD\Baseline;

class BaselineSet
{
    /** @var array<string, list<ViolationBaseline>> */
    private array $violations = [];

    public function addEntry(ViolationBaseline $entry): void
    {
        $this->violations[$entry->getRuleName()][] = $entry;
    }

    public function contains(string $ruleName, string $fileName, ?string $methodName): bool
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
