<?php

/*
 * When running old PHPUnit version such as <= 5 with PHP >= 8.0,
 * new PHP tokens will be missing and generate errors.
 *
 * Creating simple PHP_Token for each of them is enough as none of them are affecting the coverage.
 * And as our code is PHP 5.3-compatible, running old PHPUnit version for coverage is no an issue
 * and with fixed tokens, it makes possible to run coverage with PHP 8.2 and xxebug 3.
 */

if (!class_exists('PHP_Token_NAME_QUALIFIED')) {
    class PHP_Token_NAME_QUALIFIED extends PHP_Token
    {
    }
}

if (!class_exists('PHP_Token_ATTRIBUTE')) {
    class PHP_Token_ATTRIBUTE extends PHP_Token
    {
    }
}

if (!class_exists('PHP_Token_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG')) {
    class PHP_Token_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG extends PHP_Token
    {
    }
}

if (!class_exists('PHP_Token_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG')) {
    class PHP_Token_AMPERSAND_FOLLOWED_BY_VAR_OR_VARARG extends PHP_Token
    {
    }
}

if (!class_exists('PHP_Token_NAME_FULLY_QUALIFIED')) {
    class PHP_Token_NAME_FULLY_QUALIFIED extends PHP_Token
    {
    }
}
