<?php

/**
 * Pages admin routes.
 *
 * Auto-prefixed by Flight School. Prefix: /admin
 *
 * Routes:
 *   GET    /admin/pages              - Page list
 *   GET    /admin/pages/create       - Create page form
 *   POST   /admin/pages/store        - Store new page
 *   GET    /admin/pages/@id/edit     - Edit page form
 *   POST   /admin/pages/@id/update   - Update page
 *   POST   /admin/pages/@id/delete   - Delete page
 *   GET    /admin/pages/@id/versions - Version history
 *   POST   /admin/pages/@id/restore/@versionId - Restore version
 *
 * @package Pubvana\Pages\Config
 */

use Enlivenapp\FlightCsrf\Middlewares\CsrfMiddleware;
use Enlivenapp\FlightShield\Middlewares\SessionAuthMiddleware;
use Pubvana\Pages\Controllers\PagesAdminController;

/** @var \flight\net\Router $router */
/** @var \flight\Engine $app */
/** @var string $configPrepend */

// List
$router->get('/pages', function () use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->index();
})->addMiddleware(new SessionAuthMiddleware($app));

// Create form
$router->get('/pages/create', function () use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->create();
})->addMiddleware(new SessionAuthMiddleware($app));

// Store
$router->post('/pages/store', function () use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->store();
})->addMiddleware(new SessionAuthMiddleware($app))
  ->addMiddleware(new CsrfMiddleware($app));

// Edit form
$router->get('/pages/@id/edit', function (string $id) use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->edit($id);
})->addMiddleware(new SessionAuthMiddleware($app));

// Update
$router->post('/pages/@id/update', function (string $id) use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->update($id);
})->addMiddleware(new SessionAuthMiddleware($app))
  ->addMiddleware(new CsrfMiddleware($app));

// Delete
$router->post('/pages/@id/delete', function (string $id) use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->delete($id);
})->addMiddleware(new SessionAuthMiddleware($app))
  ->addMiddleware(new CsrfMiddleware($app));

// Version history
$router->get('/pages/@id/versions', function (string $id) use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->versions($id);
})->addMiddleware(new SessionAuthMiddleware($app));

// Restore version
$router->post('/pages/@id/restore/@versionId', function (string $id, string $versionId) use ($app, $configPrepend) {
    (new PagesAdminController($app, $configPrepend))->restore($id, $versionId);
})->addMiddleware(new SessionAuthMiddleware($app))
  ->addMiddleware(new CsrfMiddleware($app));
