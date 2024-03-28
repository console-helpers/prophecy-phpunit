<?php

if (PHP_VERSION_ID >= 70000) {
    require_once __DIR__ . '/ProphecyTrait7.php';
} else {
    require_once __DIR__ . '/ProphecyTrait5.php';
}
