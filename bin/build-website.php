<?php

use Gregwar\RST\Parser;

/**
 * Recursive remove a directory
 *
 * @param $directory
 *
 * @return void
 */
function removeDirectory($directory)
{
    if (!($dir = @opendir($directory))) {
        return;
    }

    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir($directory.'/'.$file)) {
            removeDirectory($directory.'/'.$file);

            continue;
        }

        unlink($directory.'/'.$file);
    }

    closedir($dir);

    @rmdir($directory);
}

/**
 * Recursive copy a directory with all content to another directory
 *
 * @param $source
 * @param $destination
 *
 * @return void
 */
function copyDirectory($source, $destination)
{
    $dir = opendir($source);
    @mkdir($destination);

    while (false !== ($file = readdir($dir))) {
        if (substr($file, 0, 1) === '.') {
            continue;
        }

        if (is_dir($source.'/'.$file)) {
            copyDirectory($source.'/'.$file, $destination.'/'.$file);

            continue;
        }

        copy($source.'/'.$file, $destination.'/'.$file);
    }

    closedir($dir);
}

/**
 * Create a cache of the directory
 *
 * @param string $dir
 * @param Parser $parser
 * @param string $websiteDirectory
 * @param string $changelogContent
 * @param string $rstDir
 * @param string $base
 *
 * @return void
 */
function cacheDirectory($dir, $parser, $websiteDirectory, $changelogContent, $rstDir, $base = '')
{
    foreach (scandir($dir) as $item) {
        if (substr($item, 0, 1) === '.') {
            continue;
        }

        if (is_dir($dir.'/'.$item)) {
            cacheDirectory($dir.'/'.$item, $parser, $websiteDirectory, $changelogContent, $rstDir, $base.'/'.$item);

            continue;
        }

        if (substr($item, -4) !== '.rst') {
            contine;
        }
        $directory = $websiteDirectory.$base;

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $content = file_get_contents($dir.'/'.$item);
        $content = str_replace(
            '.. include:: ../release/parts/latest.rst',
            $changelogContent,
            $content
        );
        $content = $parser->parse($content);
        // Add one level to every title <h1> to <h2>, <h2> to <h3> etc.
        $content = preg_replace_callback('/(<\/?h)([1-6])/', function ($match) {
            return $match[1].($match[2] + 1);
        }, $content);
        $content = preg_replace(
            '/phpmd-(\d+\.\S+)/',
            '<a href="https://github.com/phpmd/phpmd/releases/tag/$1" title="$0 release">$0</a>',
            $content
        );
        $uri = $base.'/'.substr($item, 0, -4).'.html';

        $menu = buildMenu($uri, $rstDir);

        ob_start();
        include __DIR__.'/../src/site/resources/layout.php';
        $html = ob_get_contents();
        ob_end_clean();

        file_put_contents($websiteDirectory.$uri, $html);
    }
}

/**
 * Check if the node is index (that is skiped in the building of the menu)
 *
 * @param SimpleXMLElement $node
 *
 * @return bool
 */
function isIndex($node)
{
    foreach ($node->attributes() as $name => $value) {
        if ($name === 'index' && strval($value[0]) === 'true') {
            return true;
        }
    }

    return false;
}

/**
 * Check if the item is hidden (that is skiped in the building of the menu)
 *
 * @param SimpleXMLElement $node
 *
 * @return bool
 */
function isHidden($node)
{
    foreach ($node->attributes() as $name => $value) {
        if ($name === 'display' && strval($value[0]) === 'false') {
            return true;
        }
    }

    return false;
}

/**
 * @param string $uri
 * @param string $rstDir
 *
 * @return string
 */
function buildMenu($uri, $rstDir)
{

    $output = '';
    $menu = simplexml_load_file($rstDir.'/.index.xml');

    foreach ($menu->children() as $node) {
        $path = $node->xpath('path');
        $name = $node->xpath('name');

        if (!isset($path[0], $name[0]) || isHidden($node)) {
            continue;
        }

        $isDirectory = $node->getName() === 'directory';
        $name = htmlspecialchars(strval($name[0]));
        $path = ltrim(strval($path[0]), '/');
        $href = '/'.$path;
        $root = $isDirectory ? $href : substr($href, 0, -4).'.html';
        $href = $isDirectory ? $href.'index.html' : $root;
        $selected = substr($uri, 0, strlen($root)) === $root;
        $output .= '<li><a href="'.$href.'" title="'.$name.'">';
        $output .= $selected ? '<strong>'.$name.'</strong>' : $name;
        $output .= '</a>';

        if ($selected && $isDirectory && file_exists($file = $rstDir.'/'.$path.'.index.xml')) {
            $upperPath = $path;
            $subMenu = simplexml_load_file($file);

            $output .= '<ul>';

            foreach ($subMenu->children() as $node) {
                if (isIndex($node) || isHidden($node)) {
                    continue;
                }

                $isDirectory = $node->getName() === 'directory';
                $name = htmlspecialchars(strval($node->xpath('name')[0] ?? 'unknown'));
                $href = '/'.$upperPath.ltrim(strval($node->xpath('path')[0] ?? 'unknown'), '/');
                $root = $isDirectory ? $href : substr($href, 0, -4).'.html';
                $href = $isDirectory ? $href.'index.html' : $root;
                $output .= '<li><a href="'.$href.'" title="'.$name.'">';
                $output .= substr($uri, 0, strlen($root)) === $root ? '<strong>'.$name.'</strong>' : $name;
                $output .= '</a>';
            }

            $output .= '</ul>';
        }

        $output .= '</li>';
    }

    return $output;
}

include __DIR__.'/../vendor/autoload.php';

$rstDir = __DIR__.'/../src/site/rst';
$websiteDirectory = __DIR__.'/../dist/website';

$parser = new Parser;

$changelogContent = file_get_contents(__DIR__.'/../CHANGELOG');
removeDirectory($websiteDirectory);
@mkdir($websiteDirectory, 0777, true);
copyDirectory(__DIR__.'/../src/site/resources/web', $websiteDirectory);
cacheDirectory($rstDir, $parser, $websiteDirectory, $changelogContent, $rstDir);
copy($websiteDirectory.'/about.html', $websiteDirectory.'/index.html');

if ($cname = getenv('CNAME')) {
    file_put_contents($websiteDirectory.'/CNAME', $cname);
}
