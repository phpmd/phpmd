<!DOCTYPE html>
<html
    xmlns="http://www.w3.org/1999/xhtml"
    xml:lang="en"
    lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta name="description" content="" />
    <meta name="keywords" content="PHPMD, PMD, Project Mess Detection, Design, Maintenance, Quality Assurence, Violations, Reporting" />
    <meta name="author" content="Manuel Pichler" />
    <meta name="language" content="en" />
    <meta name="date" content="<?php echo date('r'); ?>" />
    <meta name="robots" content="all" />

    <link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
    <meta name="DC.title" content="About" />
    <meta name="DC.creator" content="Manuel Pichler" />
    <meta name="DC.date" content="<?php echo date('r'); ?>" />
    <meta name="DC.rights" content="BSD 3-Clause" />

    <link rel="icon" href="<?php echo $baseHref ?? ''; ?>/favicon.ico" type="image/x-icon" />

    <link rel="Stylesheet" type="text/css" href="<?php echo $baseHref ?? ''; ?>/css/screen.css" media="screen" />
    <link rel="Stylesheet" type="text/css" href="<?php echo $baseHref ?? ''; ?>/css/print.css" media="print" />

    <title>PHPMD - PHP Mess Detector</title>
</head>
<body>
<h1>
    <a href="<?php echo $baseHref ?? ''; ?>/">PHPMD - PHP Mess Detector</a>
</h1>
<div id="content">
    <?php

    echo $content ?? '';

    ?>
</div>

<div id="navigation">
    <h2>Overview</h2>
    <ul>
        <?php

        echo $menu ?? '';

        ?>
    </ul>
</div>

<div id="footer">
    By <strong>Manuel Pichler</strong>
    licensed under <a href="https://opensource.org/licenses/bsd-license.php" title="BSD 3-Clause">BSD 3-Clause</a>

    <span class="follow">
        <a href="https://twitter.com/php_pmd" title="Follow on Twitter" class="twitter-follow" target="_blank">
            <i></i> Follow @PHP_PMD
        </a>
        <a rel="me" href="https://phpc.social/@phpmd" title="Follow on Mastodon" class="mastodon-follow" target="_blank">
            <i></i> Follow @phpmd@phpc.social
        </a>
    </span>
</div>

<div id="formats">
    <?php

    $uri ??= '';
    $sourceUri = ltrim(str_ends_with($uri, '.html') ? substr($uri, 0, -5).'.rst' : rtrim($uri, '/').'/index.rst', '/');

    ?>
    <a href="https://raw.githubusercontent.com/phpmd/phpmd/master/public/rst/<?php echo $sourceUri; ?>">Source</a>
    |
    <a href="https://github.com/phpmd/phpmd/edit/master/public/rst/<?php echo $sourceUri; ?>">Edit</a>
</div>

<script>
    [].forEach.call(document.querySelectorAll('pre > code'), function (code) {
        code.className += ' block';
    });
</script>
<?php echo getenv('FOOTER_HOOK') ?: ''; ?>
</body>
</html>
