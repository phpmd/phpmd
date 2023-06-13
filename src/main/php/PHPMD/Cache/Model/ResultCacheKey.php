<?php

namespace PHPMD\Cache\Model;

class ResultCacheKey
{
    /** @var string[] */
    private $rules;
    /** @var int */
    private $phpVersion;
    /** @var bool */
    private $strict;

    /**
     * @param bool     $strict
     * @param string[] $rules
     * @param int      $phpVersion
     */
    public function __construct($strict, $rules, $phpVersion)
    {
        $this->strict     = $strict;
        $this->rules      = $rules;
        $this->phpVersion = $phpVersion;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'strict'     => $this->strict,
            'rules'      => $this->rules,
            'phpVersion' => $this->phpVersion,
        );
    }

    /**
     * @return bool
     */
    public function isEqualTo(ResultCacheKey $other)
    {
        return $this->strict === $other->strict
            && $this->rules === $other->rules
            && $this->phpVersion === $other->phpVersion;
    }
}
