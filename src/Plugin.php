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

        $app->adext('page', 'dashboard.cards', 'pubvana.pages', [
            'label'    => 'Pages',
            'priority' => 10,
            'callable' => function (array $context) use ($app): array {
                $pages = $app->pages();

                return [
                    [
                        'id'          => 'published-pages',
                        'label'       => 'Published Pages',
                        'value'       => $pages->countByStatus('published'),
                        'icon'        => 'ti-file-text',
                        'tone'        => 'success',
                        'href'        => '/pages',
                        'description' => 'Live pages currently available on the site.',
                    ],
                    [
                        'id'          => 'draft-pages',
                        'label'       => 'Draft Pages',
                        'value'       => $pages->countByStatus('draft'),
                        'icon'        => 'ti-edit',
                        'tone'        => 'warning',
                        'href'        => '/pages',
                        'description' => 'Pages still waiting to be published.',
                    ],
                ];
            },
        ]);

        $app->adext('page', 'dashboard.sections', 'pubvana.pages', [
            'label'    => 'Pages',
            'priority' => 20,
            'callable' => function (array $context) use ($app): array {
                $items = [];
                foreach ($app->pages()->recentUpdated(5) as $page) {
                    $items[] = [
                        'label'    => $page->title,
                        'meta'     => ucfirst((string) $page->status) . ' · Updated ' . date('M j, Y g:ia', strtotime((string) $page->updated_at)),
                        'href'     => '/pages/' . (int) $page->id . '/edit',
                        'emphasis' => $page->status === 'published' ? 'success' : 'secondary',
                    ];
                }

                return [[
                    'id'          => 'recent-pages',
                    'title'       => 'Recently Updated Pages',
                    'type'        => 'list',
                    'icon'        => 'ti-files',
                    'href'        => '/pages',
                    'empty_state' => 'No pages have been updated yet.',
                    'items'       => $items,
                ]];
            },
        ]);
    }
}
