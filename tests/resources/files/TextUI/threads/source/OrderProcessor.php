<?php

namespace Threads;

class OrderProcessor
{
    private array $lines = [];

    public function summarise(array $orders, bool $includeCancelled): array
    {
        $totals = [];

        foreach ($orders as $order) {
            if (!$includeCancelled && $order['cancelled']) {
                continue;
            }

            $key = $order['customer'];

            if (!isset($totals[$key])) {
                $totals[$key] = 0;
            }

            $totals[$key] += $order['amount'];
        }

        return $totals;
    }

    public function add(array $line): void
    {
        $this->lines[] = $line;
    }
}
