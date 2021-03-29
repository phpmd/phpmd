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
     * @param string $filename
     * @return bool
     */
    public function contains($ruleName, $filename)
    {
        if (isset($this->violations[$ruleName]) === false) {
            return false;
        }

        foreach ($this->violations[$ruleName] as $baseline) {
            if ($baseline->getFilename() === $filename) {
                return true;
            }
        }

        return false;
    }
}
