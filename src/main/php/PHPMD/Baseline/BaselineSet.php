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
     * @param string      $ruleName
     * @param string      $fileName
     * @param string|null $methodName
     * @return bool
     */
    public function contains($ruleName, $fileName, $methodName)
    {
        if (isset($this->violations[$ruleName]) === false) {
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
