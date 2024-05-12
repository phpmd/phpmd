<?php

namespace PHPMD\Cache\Model;

class ResultCacheStrategy
{
    /** Determine the file cache freshness based on sha hash of the contents of the file */
    final public const CONTENT = 'content';

    /** Determine the file cache freshness based on the file modified timestamp */
    final public const TIMESTAMP = 'timestamp';
}
