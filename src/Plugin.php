<?php

declare(strict_types=1);

namespace Pubvana\Pages;

use Enlivenapp\FlightSchool\PluginInterface;
use Pubvana\Pages\Services\PagesService;
use flight\Engine;
use flight\net\Router;
use Flight;

/**
 * Flight School plugin registration for the Pages module.
 *
 * @package Pubvana\Pages
 */
class Plugin implements PluginInterface
{
    public function register(Engine $app, Router $router, array $config = []): void
    {
        $app->map('pages', function () use ($config) {
            static $instance = null;
            if ($instance === null) {
                $instance = new PagesService(Flight::db(), $config);
            }
            return $instance;
        });

        $app->adext('menu', 'content', 'pubvana.pages', [
            'label'    => 'Pages',
            'icon'     => 'ti-file-text',
            'url'      => '/pages',
            'priority' => 10,
        ]);
    }
}
