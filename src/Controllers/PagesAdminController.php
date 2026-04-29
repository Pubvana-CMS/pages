<?php

declare(strict_types=1);

namespace Pubvana\Pages\Controllers;

use Pubvana\Admin\Controllers\AdminController;

class PagesAdminController extends AdminController
{
    /**
     * Page listing.
     */
    public function index(): void
    {
        $request = $this->app->request();
        $page    = max(1, (int) ($request->query->page ?? 1));

        $result = $this->app->pages()->list($page);

        $this->render('pages/index', [
            'pageTitle' => 'Pages',
            'pages'     => $result['items'],
            'total'     => $result['total'],
            'page'      => $result['page'],
            'perPage'   => $result['per_page'],
        ]);
    }

    /**
     * Create page form.
     */
    public function create(): void
    {
        $this->render('pages/create', [
            'pageTitle' => 'New Page',
        ]);
    }

    /**
     * Store a new page.
     */
    public function store(): void
    {
        $post = $this->app->request()->data->getData();
        unset($post['_csrf_token']);

        $slug = $this->slugify($post['slug'] ?? '' ?: $post['title'] ?? '');

        if ($this->app->pages()->slugExists($slug)) {
            $this->app->redirect('/admin/pages/create');
            return;
        }

        $user = $this->app->auth()->user();

        $this->app->pages()->create([
            'title'            => $post['title'] ?? '',
            'slug'             => $slug,
            'content'          => $post['content'] ?? '',
            'status'           => $post['status'] ?? 'draft',
            'meta_title'       => $post['meta_title'] ?? null,
            'meta_description' => $post['meta_description'] ?? null,
            'is_system'        => 0,
            'purify_content'   => !empty($post['purify_content']),
        ], (int) $user->id);

        $this->app->redirect('/admin/pages');
    }

    /**
     * Edit page form.
     */
    public function edit(string $id): void
    {
        $page = $this->app->pages()->find((int) $id);

        if ($page === null) {
            $this->app->redirect('/admin/pages');
            return;
        }

        $this->render('pages/edit', [
            'pageTitle' => 'Edit Page',
            'page'      => $page,
        ]);
    }

    /**
     * Update a page.
     */
    public function update(string $id): void
    {
        $post = $this->app->request()->data->getData();
        unset($post['_csrf_token']);

        $user = $this->app->auth()->user();

        $this->app->pages()->update((int) $id, [
            'title'            => $post['title'] ?? '',
            'content'          => $post['content'] ?? '',
            'status'           => $post['status'] ?? 'draft',
            'meta_title'       => $post['meta_title'] ?? null,
            'meta_description' => $post['meta_description'] ?? null,
            'purify_content'   => !empty($post['purify_content']),
        ], (int) $user->id);

        $this->app->redirect('/admin/pages/' . $id . '/edit');
    }

    /**
     * Delete a page.
     */
    public function delete(string $id): void
    {
        $this->app->pages()->delete((int) $id);
        $this->app->redirect('/admin/pages');
    }

    /**
     * Version history for a page.
     */
    public function versions(string $id): void
    {
        $page = $this->app->pages()->find((int) $id);

        if ($page === null) {
            $this->app->redirect('/admin/pages');
            return;
        }

        $versions = $this->app->pages()->getVersions((int) $id);

        $this->render('pages/versions', [
            'pageTitle' => 'Version History: ' . $page->title,
            'page'      => $page,
            'versions'  => $versions,
        ]);
    }

    /**
     * Restore a page to a previous version.
     */
    public function restore(string $id, string $versionId): void
    {
        $user = $this->app->auth()->user();

        $this->app->pages()->restoreVersion((int) $id, (int) $versionId, (int) $user->id);

        $this->app->redirect('/admin/pages/' . $id . '/edit');
    }

    /**
     * Generate a URL-safe slug from a string.
     */
    private function slugify(string $text): string
    {
        $text = strtolower(trim($text));
        $text = str_replace('&', 'and', $text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s]+/', '-', $text);
        $text = preg_replace('/-+/', '-', $text);
        return trim($text, '-');
    }
}
