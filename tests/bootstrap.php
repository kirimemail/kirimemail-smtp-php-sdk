<?php

declare(strict_types=1);

// Autoload dependencies
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Set error reporting for tests
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set timezone for consistent datetime handling
date_default_timezone_set('UTC');