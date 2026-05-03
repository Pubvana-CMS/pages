<?php

/**
 * Public page routes.
 *
 * Auto-prefixed by Flight School. Default prefix: /pages
 *
 * Routes:
 *   GET /pages/@slug - Single published page
 *
 * @package Pubvana\Pages\Config
 */

use Pubvana\Pages\Controllers\PagesPublicController;

/** @var \flight\net\Router $router */
/** @var \flight\Engine $app */
/** @var string $configPrepend */

$router->get('/@slug', function (string $slug) use ($app, $configPrepend) {
    (new PagesPublicController($app, $configPrepend))->show($slug);
});
