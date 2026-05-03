<?php

declare(strict_types=1);

namespace Pubvana\Pages\Services;

use Pubvana\Pages\Models\Page;
use Pubvana\Pages\Models\PageVersion;
use Flight;

class PagesService
{
    private Page $model;
    private PageVersion $versionModel;
    private array $config;

    public function __construct(\PDO $pdo, array $config = [])
    {
        $this->model        = new Page($pdo);
        $this->versionModel = new PageVersion($pdo);
        $this->config       = $config;
    }

    /**
     * Paginated listing of all pages.
     */
    public function list(int $page = 1, int $perPage = 25): array
    {
        return [
            'items'    => $this->model->paginate($page, $perPage),
            'total'    => $this->model->countAll(),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }

    /**
     * Find a page by ID.
     */
    public function find(int $id): ?Page
    {
        return $this->model->findById($id);
    }

    /**
     * Find a published page by slug.
     */
    public function findBySlug(string $slug): ?Page
    {
        return $this->model->findBySlug($slug);
    }

    /**
     * Check if a slug exists.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        return $this->model->slugExists($slug, $excludeId);
    }

    /**
     * Get all published pages (for pickers/dropdowns).
     *
     * @return Page[]
     */
    public function listPublished(): array
    {
        return (new Page($this->model->getDatabaseConnection()))
            ->eq('status', 'published')
            ->isNull('deleted_at')
            ->order('title ASC')
            ->findAll();
    }

    /**
     * Count pages by status.
     */
    public function countByStatus(string $status): int
    {
        return $this->model->countByStatus($status);
    }

    /**
     * Recently updated pages.
     *
     * @return Page[]
     */
    public function recentUpdated(int $limit = 5): array
    {
        return $this->model->recentUpdated($limit);
    }

    /**
     * Create a new page and snapshot the initial version.
     */
    public function create(array $data, int $userId): Page
    {
        $purify = $data['purify_content'] ?? true;
        unset($data['purify_content']);

        if ($purify && !empty($data['content'])) {
            $data['content'] = $this->purifyContent($data['content']);
        }

        $data['created_by'] = $userId;
        $page = $this->model->createRecord($data);

        $this->versionModel->createFromPage($page, $userId);
        $this->pruneVersions((int) $page->id);

        return $page;
    }

    /**
     * Update a page and snapshot the new version.
     */
    public function update(int $id, array $data, int $userId): ?Page
    {
        $purify = $data['purify_content'] ?? true;
        unset($data['purify_content']);

        if ($purify && !empty($data['content'])) {
            $data['content'] = $this->purifyContent($data['content']);
        }

        $page = $this->model->findById($id);
        if ($page === null) {
            return null;
        }

        $page->updateRecord($data);

        $this->versionModel->createFromPage($page, $userId);
        $this->pruneVersions((int) $page->id);

        return $page;
    }

    /**
     * Soft-delete a page. System pages cannot be deleted.
     */
    public function delete(int $id): bool
    {
        $page = $this->model->findById($id);
        if ($page === null || (int) $page->is_system === 1) {
            return false;
        }

        $page->softDelete();
        return true;
    }

    /**
     * Get version history for a page.
     */
    public function getVersions(int $pageId): array
    {
        return $this->versionModel->getForPage($pageId);
    }

    /**
     * Restore a page to a previous version.
     */
    public function restoreVersion(int $pageId, int $versionId, int $userId): ?Page
    {
        $page = $this->model->findById($pageId);
        if ($page === null) {
            return null;
        }

        $version = $this->versionModel->findById($versionId);
        if ($version === null || (int) $version->page_id !== $pageId) {
            return null;
        }

        $page->updateRecord([
            'title'            => $version->title,
            'content'          => $version->content,
            'status'           => $version->status,
            'meta_title'       => $version->meta_title,
            'meta_description' => $version->meta_description,
        ]);

        // Snapshot the restored state as a new version
        $this->versionModel->createFromPage($page, $userId);
        $this->pruneVersions($pageId);

        return $page;
    }

    /**
     * Delete oldest versions beyond the configured max.
     */
    private function pruneVersions(int $pageId): void
    {
        $max = $this->config['max_revisions'] ?? 15;
        $this->versionModel->pruneForPage($pageId, $max);
    }

    private function purifyContent(string $html): string
    {
        $config = \HTMLPurifier_Config::create(Flight::get('html_purifier') ?? []);
        return (new \HTMLPurifier($config))->purify($html);
    }
}
