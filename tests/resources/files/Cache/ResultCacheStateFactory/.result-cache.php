<?php

return [
    'key'   =>
        [
            'strict'       => false,
            'baselineHash' => 'baseline',
            'rules'        =>
                [
                    'rule' => 'hash',
                ],
            'composer'     =>
                [
                    'composer.json' => 'hash1',
                    'composer.lock' => 'hash2',
                ],
            'phpVersion'   => 70000,
        ],
    'state' =>
        [
            'files' =>
                [
                    'file1' =>
                        [
                            'hash'       => 'file1-hash',
                            'violations' => [],
                        ],
                    'file2' =>
                        [
                            'hash'       => 'file2-hash',
                            'violations' => ['violations'],
                        ],
                ],
        ],
];
