<?php

namespace PHPMD\Baseline;

class BaselineSet
{
    /** @var array<string, ViolationBaseline[]> */
    private $violations = array();

    public function addEntry(ViolationBaseline $entry)
    {
        $this->violations[$entry->getRuleName()][] = $entry;
    }

    /**
     * @param string $ruleName
     * @param string $fileName
     * @return bool
     */
    public function contains($ruleName, $fileName)
    {
        if (isset($this->violations[$ruleName]) === false) {
            return false;
        }

        foreach ($this->violations[$ruleName] as $baseline) {
            if ($baseline->getFileName() === $fileName) {
                return true;
            }
        }

        return false;
    }
}
