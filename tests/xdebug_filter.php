<?php
if (function_exists('xdebug_set_filter')
    && defined('XDEBUG_FILTER_CODE_COVERAGE')
    && defined('XDEBUG_PATH_INCLUDE')
    && defined('XDEBUG_PATH_EXCLUDE')
) {
    xdebug_set_filter(
        XDEBUG_FILTER_CODE_COVERAGE,
        XDEBUG_PATH_INCLUDE,
        [ dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR ]
    );
    xdebug_set_filter(
        XDEBUG_FILTER_CODE_COVERAGE,
        XDEBUG_PATH_EXCLUDE,
        [ dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR ]
    );
}
