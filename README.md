[![Stable? Not Quite Yet](https://img.shields.io/badge/stable%3F-not%20quite%20yet-blue?style=for-the-badge)](https://packagist.org/packages/pubvana/pages)
[![License](https://img.shields.io/packagist/l/pubvana/pages?style=for-the-badge)](https://packagist.org/packages/pubvana/pages)
[![PHP Version](https://img.shields.io/packagist/php-v/pubvana/pages?style=for-the-badge)](https://packagist.org/packages/pubvana/pages)
[![Monthly Downloads](https://img.shields.io/packagist/dm/pubvana/pages?style=for-the-badge)](https://packagist.org/packages/pubvana/pages)
[![Total Downloads](https://img.shields.io/packagist/dt/pubvana/pages?style=for-the-badge)](https://packagist.org/packages/pubvana/pages)
[![GitHub Issues](https://img.shields.io/github/issues/Pubvana-CMS/pages?style=for-the-badge)](https://github.com/Pubvana-CMS/pages/issues)
[![Contributors](https://img.shields.io/github/contributors/Pubvana-CMS/pages?style=for-the-badge)](https://github.com/Pubvana-CMS/pages/graphs/contributors)
[![Latest Release](https://img.shields.io/github/v/release/Pubvana-CMS/pages?style=for-the-badge)](https://github.com/Pubvana-CMS/pages/releases)
[![Contributions Welcome](https://img.shields.io/badge/contributions-welcome-blue?style=for-the-badge)](https://github.com/Pubvana-CMS/pages/pulls)

# Pubvana Pages

**I noticed folks downloading some of these packages. I'm super grateful, Thank You!  I would like to let folks know until this notice disappears I'm doing a lot of breaking changes without worrying about them.  Once versions are up around 0.5.x things should settle down.**

Static pages module for [Pubvana](https://pubvanacms.com) — CRUD for standalone pages with slug-based routing, version history, and view tracking. Built as a [Flight School](https://github.com/enlivenapp/flight-school) plugin.

## Features

- Create, edit, and soft-delete pages with slug-based URLs
- Version history with configurable max revisions and one-click restore
- Page view tracking with referrer domain
- HTML sanitization via HTMLPurifier on save (bypass per-save via admin checkbox)
- System page protection (cannot be deleted)
- Page service on `$app->pages()` — usable from any controller or plugin
- Registers an `adext` menu contribution when admin is present

## Requirements

- PHP 8.1+
- `enlivenapp/flight-school`
- `enlivenapp/flight-shield`
- `enlivenapp/migrations`
- `flightphp/active-record`
- `ezyang/htmlpurifier`

## Recommends

- `pubvana/admin` (Admin UI for page management)

## Installation

```bash
composer require pubvana/pages
```

Enable in `app/config/config.php`:

```php
'plugins' => [
    'pubvana/pages' => [
        'enabled'  => true,
        'priority' => 43,
    ],
],
```

Migrations package creates the `pages`, `page_versions`, and `page_views` tables automatically on first load.

## Flight School config

This package uses Flight School's return-array config format. `src/Config/Config.php` returns the package defaults as an array, Flight School stores that array under `pubvana.pages` on `$app`, and the current public route prefix is defined there with `'routePrepend' => 'pages'`.

## Configuration

Defaults from `Config.php` — override in your plugin config block:

| Key | Default | Description |
|-----|---------|-------------|
| `max_revisions` | `15` | Maximum version snapshots kept per page |

HTML sanitization config is project-wide under the `html_purifier` key in `config.php`. See [PLUGIN-ARCHITECTURE.md](../../docs/PLUGIN-ARCHITECTURE.md) for details.

## Usage

```php
$pages = Flight::pages();

// Create a page
$page = $pages->create([
    'title'            => 'About Us',
    'slug'             => 'about-us',
    'content'          => '<p>Welcome.</p>',
    'status'           => 'published',
    'meta_title'       => 'About Us',
    'meta_description' => 'Learn more about us.',
], $userId);

// Update a page
$pages->update($id, [
    'title'   => 'About Us — Updated',
    'content' => '<p>New content.</p>',
    'status'  => 'published',
], $userId);

// Bypass HTML sanitization for a single save
$pages->create([
    'content'        => '<p>Trusted HTML</p>',
    'purify_content' => false,
    // ...
], $userId);

// Find by ID or slug
$page = $pages->find(5);
$page = $pages->findBySlug('about-us');

// List with pagination
$result = $pages->list(page: 1, perPage: 25);
// $result = ['items' => [...], 'total' => 12, 'page' => 1, 'per_page' => 25]

// Soft-delete (system pages are protected)
$pages->delete($id);

// Version history
$versions = $pages->getVersions($id);
$pages->restoreVersion($pageId, $versionId, $userId);

// Record a page view (referrer domain is optional, typically parsed from HTTP_REFERER)
$referrer = parse_url($_SERVER['HTTP_REFERER'] ?? '', PHP_URL_HOST) ?: null;
$pages->recordView($pageId, $referrer);
$count = $pages->getViewCount($pageId);
```

## Admin Routes

| Method | Route | Description |
|--------|-------|-------------|
| GET | `/admin/pages` | Page list |
| GET | `/admin/pages/create` | Create form |
| POST | `/admin/pages/store` | Store new page |
| GET | `/admin/pages/@id/edit` | Edit form |
| POST | `/admin/pages/@id/update` | Update page |
| POST | `/admin/pages/@id/delete` | Delete page |
| GET | `/admin/pages/@id/versions` | Version history |
| POST | `/admin/pages/@id/restore/@versionId` | Restore version |

All admin routes require session auth. Mutation routes (store, update, delete, restore) require CSRF.

## Seeds

Installs on first run:
- Two permissions: `pages.manage`, `pages.system`
- A default "Welcome to Pubvana CMS" page

## License

MIT
