<?php

use Gregwar\RST\Environment;
use Gregwar\RST\Parser;

class PhpMdEnvironment extends Environment
{
    public static $letters = array('=', '-', '`', '~', '*', '^', '"');

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

        $this->baseHref = ltrim($this->env('BASE_HREF') ?: '', ':');
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

    public function env(string $var)
    {
        static $settings = null;

        if ($settings === null) {
            $settings = file_exists('.env') ? parse_ini_file('.env') : [];
            echo (file_exists('.env') ? '.env file loaded: '.var_export(array_keys($settings), true) : 'no .env file.')."\n";
        }

        $value = getenv($var);

        if ($value === false) {
            return isset($settings[$var]) ? $settings[$var] : null;
        }

        return $value;
    }

    private function prefixRequest(string $prefix, string $url, string $repo = null, $data = null, bool $withToken = true, string $file = null)
    {
        $repo = $repo ?: 'phpmd/phpmd';

        return $this->request("$prefix$repo/$url", $data, $withToken, $file);
    }

    private function webRequest(string $url, string $repo = null, $data = null, bool $withToken = true, string $file = null)
    {
        return $this->prefixRequest('https://github.com/', $url, $repo, $data, $withToken, $file);
    }

    private function apiRequest(string $url, string $repo = null, $data = null, bool $withToken = true, string $file = null)
    {
        return $this->prefixRequest('https://api.github.com/repos/', $url, $repo, $data, $withToken, $file);
    }

    private function download(string $file, string $url, string $repo = null, $data = null, bool $withToken = true)
    {
        return $this->webRequest($url, $repo, $data, $withToken, $file);
    }

    private function request(string $url, $data = null, bool $withToken = false, string $file = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        if ($file !== null) {
            $file = fopen($file, 'w') ?: null;
            if ($file !== null) {
                curl_setopt($curl, CURLOPT_FILE, $file);
            }
        }

        if ($data !== null) {
            $payload = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        if ($data !== null || $withToken) {
            $token = $this->env('GITHUB_TOKEN');

            if (!$token) {
                throw new RuntimeException('No Github token provided.');
            }

            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: token '.$token,
            ]);
        }

        $content = curl_exec($curl);
        $error = null;

        if (!$content) {
            $error = curl_error($curl);
        }

        curl_close($curl);

        if ($file !== null) {
            fclose($file);
        }

        if ($error !== null) {
            throw new RuntimeException("$url failed:\n$error");
        }

        return $content;
    }

    private function json(string $url)
    {
        return json_decode($this->apiRequest($url, null, null, true));
    }

    public function publishPhar()
    {
        if (!$this->env('GITHUB_TOKEN')) {
            echo "PHAR publishing skipped as GITHUB_TOKEN is missing.\n";

            return;
        }

        // We get the releases from the GitHub API
        $releases = $this->json('releases');
        $releaseVersions = array_map(static function ($release) {
            return $release->tag_name;
        }, $releases);

        // we sort the releases with version_compare
        usort($releaseVersions, 'version_compare');

        // The total limit of all the phar files, size in bytes.
        // 94.371.840 B = 90 MB
        $totalLimitPharFiles = 94371840;

        // A counter for the total size for all the downloaded phar files.
        $totalPharSize = 0;

        // we iterate each version
        foreach ($releaseVersions as $version) {
            $pharUrl = 'releases/download/'.$version.'/phpmd.phar';
            $pharDestinationDirectory = $this->websiteDirectory.'/static/'.$version;
            @mkdir($pharDestinationDirectory, 0777, true);
            $this->download($pharDestinationDirectory.'/phpmd.phar', $pharUrl);
            $filesize = filesize($pharDestinationDirectory.'/phpmd.phar');

            echo $pharDestinationDirectory.'/phpmd.phar downloaded: '.number_format($filesize / 1024 / 1024, 2).' MB';

            if ($totalPharSize === 0) {
                echo ' (latest)';
                // the first one is the latest
                $latestPharDestinationDirectory = $this->websiteDirectory.'/static/latest';
                @mkdir($latestPharDestinationDirectory, 0777, true);
                copy($pharDestinationDirectory.'/phpmd.phar', $latestPharDestinationDirectory.'/phpmd.phar');
                $totalPharSize += $filesize;
            }

            echo "\n";

            $totalPharSize += $filesize;

            if ($totalPharSize > $totalLimitPharFiles) {
                // we have reached the limit
                break;
            }
        }
    }
}

$env = new PhpMdEnvironment;
$env->publishPhar();
$parser = new Parser($env);

return [
    'index' => 'about.html',
    'baseHref' => $env->getBaseHref(),
    'cname' => $env->env('CNAME'),
    'websiteDirectory' => $env->websiteDirectory,
    'sourceDirectory' => __DIR__.'/rst',
    'assetsDirectory' => __DIR__.'/resources/web',
    'layout' => __DIR__.'/resources/layout.php',
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
