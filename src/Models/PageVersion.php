<?php

declare(strict_types=1);

namespace Pubvana\Pages\Models;

/**
 * @property int         $id
 * @property int         $page_id
 * @property string      $title
 * @property string|null $content
 * @property string      $status
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int         $version_number
 * @property int         $created_by
 * @property string      $created_at
 */
class PageVersion extends \flight\ActiveRecord
{
    public function __construct($pdo = null, array $config = [])
    {
        parent::__construct($pdo, 'page_versions', $config);
    }

    /**
     * Find a version by ID.
     */
    public function findById(int $id): ?self
    {
        $this->reset();
        $this->eq('id', $id)->find();
        return $this->isHydrated() ? $this : null;
    }

    /**
     * Get all versions for a page, newest first.
     */
    public function getForPage(int $pageId): array
    {
        return (new self($this->getDatabaseConnection()))
            ->eq('page_id', $pageId)
            ->order('version_number DESC')
            ->findAll();
    }

    /**
     * Get the next version number for a page.
     */
    public function nextVersionNumber(int $pageId): int
    {
        $result = (new self($this->getDatabaseConnection()))
            ->select('MAX(version_number) as max_ver')
            ->eq('page_id', $pageId)
            ->find();

        return ((int) ($result->max_ver ?? 0)) + 1;
    }

    /**
     * Create a version snapshot from a page's current state.
     */
    public function createFromPage(Page $page, int $userId): self
    {
        $version = new self($this->getDatabaseConnection());
        $version->page_id          = $page->id;
        $version->title            = $page->title;
        $version->content          = $page->content;
        $version->status           = $page->status;
        $version->meta_title       = $page->meta_title;
        $version->meta_description = $page->meta_description;
        $version->version_number   = $this->nextVersionNumber((int) $page->id);
        $version->created_by       = $userId;
        $version->created_at       = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $version->insert();

        return $version;
    }

    /**
     * Delete oldest versions for a page beyond the max count.
     */
    public function pruneForPage(int $pageId, int $max): void
    {
        $keep = (new self($this->getDatabaseConnection()))
            ->select('id')
            ->eq('page_id', $pageId)
            ->order('version_number DESC')
            ->limit($max)
            ->findAll();

        $keepIds = array_map(fn($v) => (int) $v->id, $keep);

        if (empty($keepIds)) {
            return;
        }

        $all = (new self($this->getDatabaseConnection()))
            ->eq('page_id', $pageId)
            ->notIn('id', $keepIds)
            ->findAll();

        foreach ($all as $old) {
            $old->delete();
        }
    }
}
