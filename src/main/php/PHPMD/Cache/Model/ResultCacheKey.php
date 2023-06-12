<?php

namespace PHPMD\Cache\Model;

class ResultCacheKey
{
    /** @var string[] */
    private $rules;
    /** @var int */
    private $phpVersion;

    /**
     * @param string[] $rules
     * @param int      $phpVersion
     */
    public function __construct($rules, $phpVersion)
    {
        $this->rules      = $rules;
        $this->phpVersion = $phpVersion;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'rules'      => $this->rules,
            'phpVersion' => $this->phpVersion,
        );
    }

    /**
     * @return bool
     */
    public function isEqualTo(ResultCacheKey $other)
    {
        return $this->rules === $other->rules
            && $this->phpVersion === $other->phpVersion;
    }
}
