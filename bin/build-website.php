<?php

use Gregwar\RST\Parser;

/**
 * Remove a directory and all sub-directories and files inside.
 *
 * @param string $directory
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
 * Deep copy a directory with all content to another directory.
 *
 * @param string $source
 * @param string $destination
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
 * Create a HTML files from RST sources.
 *
 * @param string $dir
 * @param Parser $parser           RST file parser
 * @param string $websiteDirectory Output directory
 * @param string $changelogContent Content of the CHANGELOG file
 * @param string $rstDir           Directory containing .rst files
 * @param string $baseHref         Base of link to be used if website is deplpoyed in a folder URI
 * @param string $base             Base path for recursion
 *
 * @return void
 */
function buildWebsite($dir, $parser, $websiteDirectory, $changelogContent, $rstDir, $baseHref, $base = '')
{
    foreach (scandir($dir) as $item) {
        if (substr($item, 0, 1) === '.') {
            continue;
        }

        if (is_dir($dir.'/'.$item)) {
            buildWebsite($dir.'/'.$item, $parser, $websiteDirectory, $changelogContent, $rstDir, $baseHref, $base.'/'.$item);

            continue;
        }

        if (substr($item, -4) !== '.rst') {
            continue;
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
        $content = preg_replace_callback('/(<a id="[^"]+"><\/a>)\s*<h(?<level>[1-6])([^>]*>)(?<content>[\s\S]*)<\/h\\g<level>>/U', function ($match) {
            // Add one level to every title <h1> to <h2>, <h2> to <h3> etc.
            $level = $match['level'] + 1;
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
        $uri = $base.'/'.substr($item, 0, -4).'.html';

        $menu = buildMenu($uri, $rstDir, $baseHref);

        ob_start();
        include __DIR__.'/../src/site/resources/layout.php';
        $html = ob_get_contents();
        ob_end_clean();

        file_put_contents($websiteDirectory.$uri, $html);
    }
}

/**
 * Check if the node is index (that is skipped in the building of the menu)
 *
 * @param SimpleXMLElement $node menu item node
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
 * Check if the item is hidden (that is skipped in the building of the menu)
 *
 * @param SimpleXMLElement $node menu item node
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
 * Return the menu as HTML.
 *
 * @param string $uri      URI of the current page
 * @param string $rstDir   Directory containing .rst files
 * @param string $baseHref Base of link to be used if website is deplpoyed in a folder URI
 *
 * @return string
 */
function buildMenu($uri, $rstDir, $baseHref)
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
        $output .= '<li><a href="'.$baseHref.$href.'" title="'.$name.'">';
        $output .= $selected ? '<strong>'.$name.'</strong>' : $name;
        $output .= '</a>';

        if ($selected && $isDirectory && file_exists($file = $rstDir.'/'.$path.'.index.xml')) {
            $upperPath = $path;
            $subMenu = simplexml_load_file($file);

            $output .= '<ul>';

            foreach ($subMenu->children() as $subNode) {
                if (isIndex($subNode) || isHidden($subNode)) {
                    continue;
                }

                $isDirectory = $subNode->getName() === 'directory';
                $name = htmlspecialchars(strval($subNode->xpath('name')[0] ?? 'unknown'));
                $href = '/'.$upperPath.ltrim(strval($subNode->xpath('path')[0] ?? 'unknown'), '/');
                $root = $isDirectory ? $href : substr($href, 0, -4).'.html';
                $href = $isDirectory ? $href.'index.html' : $root;
                $output .= '<li><a href="'.$baseHref.$href.'" title="'.$name.'">';
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
$baseHref = ltrim(getenv('BASE_HREF') ?: '', ':');

$changelogContent = file_get_contents(__DIR__.'/../CHANGELOG');
removeDirectory($websiteDirectory);
@mkdir($websiteDirectory, 0777, true);
copyDirectory(__DIR__.'/../src/site/resources/web', $websiteDirectory);
buildWebsite($rstDir, $parser, $websiteDirectory, $changelogContent, $rstDir, $baseHref);
copy($websiteDirectory.'/about.html', $websiteDirectory.'/index.html');

if ($cname = getenv('CNAME')) {
    file_put_contents($websiteDirectory.'/CNAME', $cname);
}
