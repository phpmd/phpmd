<?php

namespace PHPMD\Cache\Model;

class ResultCacheKey
{
    /** @var bool */
    private $strict;
    /** @var array<string, string> */
    private $rules;
    /** @var array<string, string> */
    private $composer;
    /** @var int */
    private $phpVersion;

    /**
     * @param bool                  $strict
     * @param array<string, string> $rules
     * @param array<string, string> $composer
     * @param int                   $phpVersion
     */
    public function __construct($strict, $rules, $composer, $phpVersion)
    {
        $this->strict     = $strict;
        $this->rules      = $rules;
        $this->composer   = $composer;
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
            'composer'   => $this->composer,
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
            && $this->composer === $other->composer
            && $this->phpVersion === $other->phpVersion;
    }
}
