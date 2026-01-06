<?php

namespace PHPMD\Cache\Model;

enum ResultCacheStrategy: string
{
    /** Determine the file cache freshness based on sha hash of the contents of the file */
    case Content = 'content';

    /** Determine the file cache freshness based on the file modified timestamp */
    case Timestamp = 'timestamp';
}
