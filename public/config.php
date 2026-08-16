<?php

use EasyDoc\Util\EnvVar;
use EasyDoc\Util\PharPublish;
use Gregwar\RST\Environment;
use Gregwar\RST\Parser;
use SimpleCli\Writer;

class PhpMdEnvironment extends Environment
{
    public static $letters = ['=', '-', '`', '~', '*', '^', '"'];

    /**
     * @var string
     */
    protected $baseHref;

    public $websiteDirectory = __DIR__.'/../dist/website';

    public function getBaseHref()
    {
        return $this->baseHref;
    }

    /**
     * Prevent Gregwar\RST\Span from resets the anonymous link stack so that
     * `__ url` targets at the end of the document can consume them.
     */
    public function resetAnonymousStack(): void
    {
    }

    public function reset(): void
    {
        parent::reset();

        $this->anonymous = [];
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
        $root = str_starts_with($url, '/');

        return ($root ? $this->getBaseHref().'/' : '').parent::relativeUrl($url);
    }
}

class PhpMdPharPublish extends PharPublish
{
    /**
     * Also publish the latest 2.x phar to static/latest-v2/ so users who
     * cannot upgrade yet keep a version agnostic download link.
     */
    public function publishPhar(?Writer $output = null, ?string $fileName = null): void
    {
        parent::publishPhar($output, $fileName);

        if (!EnvVar::toString('GITHUB_TOKEN')) {
            return;
        }

        $fileName = $fileName ?: 'phpmd.phar';
        // The releases endpoint returns 30 items per page by default; request
        // more so the last 2.x release stays visible once 3.x releases pile up.
        $versions = array_map(
            static fn ($release) => $release->tag_name,
            array_filter(
                $this->json('releases?per_page=100'),
                static fn ($release) => empty($release->draft)
                    && empty($release->prerelease)
                    && preg_match('/^2\./', $release->tag_name),
            ),
        );
        usort($versions, 'version_compare');
        $latestV2 = end($versions);

        if (!$latestV2) {
            return;
        }

        $directory = $this->downloadDirectory.'latest-v2';
        @mkdir($directory, 0777, true);
        $filePath = $directory.'/'.$fileName;
        $this->download($filePath, 'releases/download/'.$latestV2.'/'.$fileName);

        if (!is_file($filePath) || filesize($filePath) < $this->getPharMinimumSize()) {
            @unlink($filePath);
            @rmdir($directory);

            return;
        }

        $this->write("$filePath downloaded (latest-v2: $latestV2)\n", $output, 'light_green');
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
    'publishPhar' => [
        'repository' => 'phpmd/phpmd',
        'publisher' => PhpMdPharPublish::class,
    ],
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
