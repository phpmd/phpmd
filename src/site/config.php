<?php

use EasyDoc\Util\EnvVar;
use Gregwar\RST\Environment;
use Gregwar\RST\Parser;

class PhpMdEnvironment extends Environment
{
    public static $letters = ['=', '-', '`', '~', '*', '^', '"'];

    /**
     * @var string
     */
    protected $baseHref;

    public $websiteDirectory = __DIR__.'/../../dist/website';

    public function getBaseHref()
    {
        return $this->baseHref;
    }

    public function reset()
    {
        parent::reset();

        $this->baseHref = ltrim(EnvVar::toString('BASE_HREF') ?: '', ':');
        $this->titleLetters = [
            2 => '=',
            3 => '-',
            4 => '`',
            5 => '~',
            6 => '*',
            7 => '^',
            8 => '"',
        ];
    }

    public function relativeUrl($url)
    {
        $root = substr($url, 0, 1) === '/';

        return ($root ? $this->getBaseHref().'/' : '').parent::relativeUrl($url);
    }
}

$env = new PhpMdEnvironment;
$parser = new Parser($env);

return [
    'index' => 'about.html',
    'baseHref' => $env->getBaseHref(),
    'cname' => EnvVar::toString('CNAME'),
    'websiteDirectory' => $env->websiteDirectory,
    'sourceDirectory' => __DIR__.'/rst',
    'assetsDirectory' => __DIR__.'/resources/web',
    'layout' => __DIR__.'/resources/layout.php',
    'publishPhar' => 'phpmd/phpmd',
    'extensions' => [
        'rst' => function ($file) use ($parser) {
            $parser->getEnvironment()->setCurrentDirectory(dirname($file));
            $content = $parser->parseFile($file);
            // Rewrite links anchors
            $content = preg_replace_callback('/(<a id="[^"]+"><\/a>)\s*<h(?<level>[1-6])([^>]*>)(?<content>[\s\S]*)<\/h\\g<level>>/U', function ($match) {
                $level = $match['level'];
                $content = $match['content'];
                // Use content as anchor
                $hash = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($match['content'])));

                return "<a id=\"$hash\"></a>\n<h$level>$content</h$level>";
            }, $content);
            $content = preg_replace(
                '/phpmd-(\d+\.\S+)/',
                '<a href="https://github.com/phpmd/phpmd/releases/tag/$1" title="$0 release">$0</a>',
                $content
            );

            return $content;
        },
    ],
];
