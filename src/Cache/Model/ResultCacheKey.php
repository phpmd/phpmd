<?php

namespace PHPMD\Cache\Model;

class ResultCacheKey
{
    /**
     * @param array<string, string> $rules
     * @param array<string, string> $composer
     */
    public function __construct(
        private readonly bool $strict,
        private readonly ?string $baselineHash,
        private readonly array $rules,
        private readonly array $composer,
        private readonly int $phpVersion
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'strict' => $this->strict,
            'baselineHash' => $this->baselineHash,
            'rules' => $this->rules,
            'composer' => $this->composer,
            'phpVersion' => $this->phpVersion,
        ];
    }

    public function isEqualTo(self $other): bool
    {
        return $this->strict === $other->strict
            && $this->baselineHash === $other->baselineHash
            && $this->rules === $other->rules
            && $this->composer === $other->composer
            && $this->phpVersion === $other->phpVersion;
    }
}
