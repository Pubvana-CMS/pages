<?php

$now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

return [
    'install' => [
        [
            'table' => 'auth_permissions',
            'rows'  => [
                ['alias' => 'pages.manage', 'description' => 'Create, edit, and delete pages'],
                ['alias' => 'pages.system', 'description' => 'Mark pages as system (protected from deletion)'],
            ],
        ],
        [
            'table' => 'pages',
            'rows'  => [
                [
                    'title'            => 'Welcome to Pubvana CMS',
                    'slug'             => 'welcome-to-pubvana-cms',
                    'content'          => '<p>Congratulations, your new Pubvana CMS site is up and running! Pubvana CMS is a modern, lightweight content management system built on FlightPHP. It comes with a clean admin dashboard, plugin support, and everything you need to start publishing right away.</p>'
                                        . '<p>This is your first page. You can edit or delete it from the admin panel under Content -> Pages. From there you can create new pages and build out your site\'s structure. Take a look around the admin area to get familiar with all the tools at your disposal. Happy publishing!</p>',
                    'status'           => 'published',
                    'is_system'        => 0,
                    'created_by'       => 1,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
            ],
        ],
    ],
];
