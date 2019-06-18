<?php

function removeDirectory($src)
{
    if (!($dir = @opendir($src))) {
        return;
    }

    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        if (is_dir($src.'/'.$file)) {
            removeDirectory($src.'/'.$file);

            continue;
        }

        unlink($src.'/'.$file);
    }

    closedir($dir);
}

function copyDirectory($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);

    while (false !== ($file = readdir($dir))) {
        if (substr($file, 0, 1) === '.') {
            continue;
        }

        if (is_dir($src.'/'.$file)) {
            copyDirectory($src.'/'.$file, $dst.'/'.$file);

            continue;
        }

        copy($src.'/'.$file, $dst.'/'.$file);
    }

    closedir($dir);
}

function cacheDirectory($dir, $base = '')
{
    global $parser, $websiteDirectory;

    foreach (scandir($dir) as $item) {
        if (substr($item, 0, 1) === '.') {
            continue;
        }

        if (is_dir("$dir/$item")) {
            cacheDirectory("$dir/$item", "$base/$item");

            continue;
        }

        if (substr($item, -4) === '.rst') {
            $directory = $websiteDirectory.$base;

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $content = $parser->parse(file_get_contents("$dir/$item"));
            $content = preg_replace_callback('/(<\/?h)([1-6])/', function ($match) {
                return $match[1].($match[2] + 1);
            }, $content);
            $uri = $base.'/'.substr($item, 0, -4).'.html';

            $menu = buildMenu($uri);

            ob_start();
            include __DIR__.'/../src/site/resources/layout.php';
            $html = ob_get_contents();
            ob_end_clean();

            file_put_contents($websiteDirectory.$uri, $html);
        }
    }
}

function isIndex($node)
{
    foreach ($node->attributes() as $name => $value) {
        if ($name === 'index' && strval($value[0]) === 'true') {
            return true;
        }
    }

    return false;
}

function buildMenu($uri)
{
    global $rstDir;

    $output = '';
    $menu = simplexml_load_file($rstDir.'/.index.xml');

    foreach ($menu->children() as $node) {
        $path = $node->xpath('path');
        $name = $node->xpath('name');

        if (!isset($path[0], $name[0])) {
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
                if (isIndex($node)) {
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

$parser = new Gregwar\RST\Parser;

removeDirectory($websiteDirectory);
@mkdir($websiteDirectory, 0777, true);
copyDirectory(__DIR__.'/../src/site/resources/web', $websiteDirectory);
cacheDirectory($rstDir);
copy($websiteDirectory.'/about.html', $websiteDirectory.'/index.html');
