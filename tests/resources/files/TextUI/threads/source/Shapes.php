<?php

namespace Threads;

interface Shape
{
    public function area(): float;
}

trait Measurable
{
    public function describe(float $w, float $h): string
    {
        $a = $w * $h;

        if ($a > 100) {
            return 'large';
        }

        if ($a > 10) {
            return 'medium';
        }

        return 'small';
    }
}
