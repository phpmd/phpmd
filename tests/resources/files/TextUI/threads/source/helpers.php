<?php

namespace Threads;

function normaliseTheIncomingPayloadForStorage(array $payload, bool $strict, int $depth): array
{
    $out = [];

    foreach ($payload as $key => $value) {
        if (is_array($value) && $depth > 0) {
            $out[$key] = normaliseTheIncomingPayloadForStorage($value, $strict, $depth - 1);

            continue;
        }

        $out[$key] = $strict ? trim((string) $value) : $value;
    }

    return $out;
}

function pick(array $a, string $k): mixed
{
    return $a[$k] ?? null;
}
