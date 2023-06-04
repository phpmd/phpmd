<?php

namespace PHPMD\Cache;

use PHPMD\RuleViolation;

class ResultCacheState
{
    /** @var array{files: array<string, array{hash: string, violations: array}>} */
    private $state;

    /**
     * @param array{files: array<string, array{hash: string, violations: array}>} $state
     */
    public function __construct($state = array())
    {
        $this->state = $state;
    }

    /**
     * @param string $filePath
     * @return array
     */
    public function getViolations($filePath)
    {
        if (isset($this->state['files'][$filePath]['violations']) === false) {
            return array();
        }

        return $this->state['files'][$filePath]['violations'];
    }

    /**
     * @param string $filePath
     */
    public function setViolations($filePath, array $violations)
    {
        $this->state['files'][$filePath]['violations'] = $violations;
    }

    /**
     * @param string $filePath
     */
    public function addViolation($filePath, RuleViolation $violation)
    {
        $this->state['files'][$filePath]['violations'][] = array(
            'rule'          => get_class($violation->getRule()),
            'namespaceName' => $violation->getNamespaceName(),
            'className'     => $violation->getClassName(),
            'methodName'    => $violation->getMethodName(),
            'functionName'  => $violation->getFunctionName(),
            'beginLine'     => $violation->getBeginLine(),
            'endLine'       => $violation->getEndLine(),
            'description'   => $violation->getDescription(),
            'args'          => $violation->getArgs(),
            'metric'        => $violation->getMetric()
        );
    }

    /**
     * @param string $filePath
     * @param string $hash
     * @return bool
     */
    public function isFileModified($filePath, $hash)
    {
        if (isset($this->state['files'][$filePath]) === false) {
            return true;
        }

        return $this->state['files'][$filePath]['hash'] !== $hash;
    }

    /**
     * @param string $filePath
     * @param string $hash
     */
    public function setFileState($filePath, $hash)
    {
        return $this->state['files'][$filePath]['hash'] = $hash;
    }

    /**
     * @return array{files: array<string, array{hash: string, violations: array}>}
     */
    public function getState()
    {
        return $this->state;
    }
}
