<?php

namespace Threads;

class ReportBuilder
{
    public function build(array $rows): string
    {
        $out = '';
        $i = 0;

        foreach ($rows as $row) {
            $out .= $row['label'];
            $out .= ': ';
            $out .= (string) $row['value'];
            $out .= "\n";
            $i++;
        }

        $unused = $i * 2;

        return $out;
    }
}
