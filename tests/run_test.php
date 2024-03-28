<?php

namespace Prophecy\PhpUnit\Tests;

use PHPUnit\TextUI\Application;
use PHPUnit\TextUI\Command;
use PHPUnit_TextUI_Command;

require_once __DIR__ . '/xdebug_filter.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

function runTest($fixtureName)
{
    $filename = dirname(__DIR__) . '/fixtures/' . $fixtureName . '.php';
    if (!file_exists($filename)) {
        throw new \InvalidArgumentException('Unable to find test fixture at path ' . $filename);
    }

    if (class_exists(Application::class)) {
        // PHPUnit 10.x+.
        (new Application())->run(['phpunit', $filename, '--no-configuration']);
    } elseif (class_exists(Command::class)) {
        // PHPUnit 9.x-.
        (new Command())->run(['phpunit', $filename, '--verbose', '--no-configuration'], false);
    } else {
        // PHPUnit 5.x-.
        (new PHPUnit_TextUI_Command())->run(['phpunit', $filename, '--verbose', '--no-configuration'], false);
    }
}
