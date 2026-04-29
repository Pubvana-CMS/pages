[![Version](http://poser.pugx.org/pubvana/pages/version)](https://packagist.org/packages/pubvana/pages)
[![License](http://poser.pugx.org/pubvana/pages/license)](https://packagist.org/packages/pubvana/pages)
[![PHP Version Require](http://poser.pugx.org/pubvana/pages/require/php)](https://packagist.org/packages/pubvana/pages)

# Pubvana Pages

Static pages module for [Pubvana](https://pubvanacms.com) — CRUD for standalone pages with slug-based routing, version history, and view tracking. Built as a [Flight School](https://github.com/enlivenapp/flight-school) plugin with the headless service pattern.

## Features

- Create, edit, and soft-delete pages with slug-based URLs
- Version history with configurable max revisions and one-click restore
- Page view tracking with referrer domain
- HTML sanitization via HTMLPurifier on save (bypass per-save via admin checkbox)
- System page protection (cannot be deleted)
- Headless service on `$app->pages()` — usable from any controller or plugin
- Registers an `adext` menu contribution when admin is present

## Requirements

- PHP 8.1+
- `enlivenapp/flight-school`
- `enlivenapp/flight-shield`
- `enlivenapp/migrations`
- `flightphp/active-record`
- `ezyang/htmlpurifier`

## Recommends

- `pubvana/admin` (The head for Pubvana headless)

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
