<?php
// Load the composer autoloader/
require_once __DIR__ . '/../../vendor/autoload.php';

// Call the bootstrap method of WP Mock.
WP_Mock::bootstrap();

/**
 * Now we include any plugin files that we need to be able to run the tests. This
 * should be files that define the functions and classes you're going to test.
 */
require_once __DIR__ . '/../../plugin.php';
