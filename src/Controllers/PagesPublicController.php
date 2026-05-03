<?php

declare(strict_types=1);

namespace Pubvana\Pages\Controllers;

use Pubvana\Admin\Controllers\PublicController;

/**
 * Public-facing pages controller — renders published pages by slug or ID.
 */
class PagesPublicController extends PublicController
{
    /**
     * Single published page by slug.
     */
    public function show(string $slug): void
    {
        $page = $this->app->pages()->findBySlug($slug);
        $this->renderPage($page);
    }

    /**
     * Single published page by ID (used by homepage dispatch).
     */
    public function showById(int $id): void
    {
        $page = $this->app->pages()->find($id);
        $this->renderPage($page);
    }

    private function renderPage(?object $page): void
    {
        if ($page === null || ($page->status ?? '') !== 'published') {
            $this->app->halt(404, 'Page not found');
            return;
        }

        $this->render('page', [
            'title'          => $page->title,
            'content'        => $page->content,
            'featured_image' => $this->publicAssetUrl($page->featured_image ?? null),
        ]);
    }
}
