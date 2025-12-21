<?php

return array(
    'key'   =>
        array(
            'strict'       => false,
            'baselineHash' => 'baseline',
            'rules'        =>
                array(
                    'rule' => 'hash',
                ),
            'composer'     =>
                array(
                    'composer.json' => 'hash1',
                    'composer.lock' => 'hash2',
                ),
            'phpVersion'   => 70000,
        ),
    'state' =>
        array(
            'files' =>
                array(
                    'file1' =>
                        array(
                            'hash'       => 'file1-hash',
                            'violations' => array(),
                        ),
                    'file2' =>
                        array(
                            'hash'       => 'file2-hash',
                            'violations' => array('violations'),
                        ),
                ),
        ),
);
