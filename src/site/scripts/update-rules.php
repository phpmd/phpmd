#!/usr/bin/env php
<?php
$input = __DIR__ . '/../../..';

// The output directory
$output = __DIR__ . '/../rst/rules';

if ( file_exists( $input ) === false )
{
    fwrite( STDOUT, 'Cannot locate rules, skipping here...' . PHP_EOL );
    exit( 1 );
}

$sets = array();

$files = glob( $input . '/src/main/resources/rulesets/*.xml' );
sort( $files );

$index = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL .
         '<index>' . PHP_EOL .
         '  <site index="true" display="false">' . PHP_EOL .
         '    <name>Index</name>' . PHP_EOL .
         '    <path>index.rst</path>' . PHP_EOL .
         '  </site>' . PHP_EOL;

foreach ( $files as $file )
{
    echo 'Processing: ', $file, PHP_EOL;

    $name = pathinfo( $file, PATHINFO_FILENAME );
    $path = $output . '/' . $name . '.rst';

    $cmd = sprintf(
        'xsltproc %s/pmd.xsl %s > %s',
        escapeshellarg( dirname( __FILE__ ) ),
        escapeshellarg( $file ),
        escapeshellarg( $path )
    );
    shell_exec( $cmd );

    $sxml   = simplexml_load_file( $file );
    $index .= '    <site>' . PHP_EOL .
              '        <name>' . $sxml['name'] . '</name>' . PHP_EOL .
              '        <path>' . $name . '.rst</path>' . PHP_EOL .
              '    </site>' . PHP_EOL;

    $rules = array();
    foreach ( $sxml->rule as $rule )
    {
        $rules[] = array(
            'name'  =>  normalize( $rule['name'] ),
            'desc'  =>  normalize( $rule->description ),
            'href'  =>  $name . '.html#' . strtolower( $rule['name'] ),
        );
    }

    $sets[] = array(
        'name'   =>  normalize( $sxml['name'] ),
        'desc'   =>  normalize( $sxml->description ),
        'rules'  =>  $rules,
    );
}

$index .= '</index>';

file_put_contents( $output . '/index.rst', generate_index( $sets ) );
file_put_contents( $output . '/.index.xml', $index );


exit( 0 );

function normalize( $elem )
{
    return preg_replace( '(\s+)s', ' ', trim( (string) $elem ) );
}

function generate_index( array $sets )
{
    $content = '================' . PHP_EOL
             . 'Current Rulesets' . PHP_EOL
             . '================' 
             . PHP_EOL . PHP_EOL
             . 'List of rulesets and rules contained in each ruleset.' 
             . PHP_EOL . PHP_EOL;

    foreach ( $sets as $set )
    {
        $content .= sprintf(
            '- `%s`__: %s%s',
            $set['name'],
            $set['desc'],
            PHP_EOL
        );
    }
    
    $content .= PHP_EOL;
    foreach ( $sets as $set )
    {
        $anchor = preg_replace( '([^a-z0-9]+)i', '-', $set['name'] );
        $anchor = strtolower( $anchor );

        $content .= '__ index.html#' . $anchor . PHP_EOL;
    }
    $content .= PHP_EOL;

    foreach ( $sets as $set )
    {
        $content .= $set['name'] . PHP_EOL;
        $content .= str_repeat( '=', strlen( $set['name' ] ) );
        $content .= PHP_EOL . PHP_EOL;

        foreach ( $set['rules'] as $rule )
        {
            $content .= sprintf(
                '- `%s`__: %s%s',
                $rule['name'],
                $rule['desc'],
                PHP_EOL
            );
        }

        $content .= PHP_EOL;
        foreach ( $set['rules'] as $rule )
        {
            $content .= '__ ' . $rule['href'] . PHP_EOL;
        }
        $content .= PHP_EOL;        
    }
    $content .= PHP_EOL;
    $content .= 'Remark' . PHP_EOL .
                '======' . PHP_EOL . PHP_EOL .
                '  This document is based on a ruleset xml-file, that ' .
                'was taken from the original source of the `PMD`__ ' .
                'project. This means that most parts of the content ' .
                'on this page are the intellectual work of the PMD ' .
                'community and its contributors and not of the PHPMD ' .
                'project.' . 
                PHP_EOL . PHP_EOL .
                '__ http://pmd.sourceforge.net/' .
                PHP_EOL;

    return $content;
}
