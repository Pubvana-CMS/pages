<?php

declare(strict_types=1);

namespace Pubvana\Pages\Models;

/**
 * @property int         $id
 * @property string      $title
 * @property string      $slug
 * @property string|null $content
 * @property string      $status        draft|published
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property int         $is_system
 * @property int         $created_by
 * @property string      $created_at
 * @property string      $updated_at
 * @property string|null $deleted_at
 */
class Page extends \flight\ActiveRecord
{
    public function __construct($pdo = null, array $config = [])
    {
        parent::__construct($pdo, 'pages', $config);
    }

    /**
     * Find a single page by ID (not soft-deleted).
     */
    public function findById(int $id): ?self
    {
        $this->reset();
        $this->eq('id', $id)->isNull('deleted_at')->find();
        return $this->isHydrated() ? $this : null;
    }

    /**
     * Find a published page by slug.
     */
    public function findBySlug(string $slug): ?self
    {
        $this->reset();
        $this->eq('slug', $slug)
             ->eq('status', 'published')
             ->isNull('deleted_at')
             ->find();
        return $this->isHydrated() ? $this : null;
    }

    /**
     * Check if a slug already exists.
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = new self($this->getDatabaseConnection());
        $query->select('COUNT(*) as cnt')
              ->eq('slug', $slug)
              ->isNull('deleted_at');

        if ($excludeId !== null) {
            $query->notEq('id', $excludeId);
        }

        $result = $query->find();
        return ((int) $result->cnt) > 0;
    }

    /**
     * Paginated listing of all non-deleted pages.
     */
    public function paginate(int $page = 1, int $perPage = 25): array
    {
        return (new self($this->getDatabaseConnection()))
            ->isNull('deleted_at')
            ->order('id DESC')
            ->limit($perPage)
            ->offset(($page - 1) * $perPage)
            ->findAll();
    }

    /**
     * Count all non-deleted pages.
     */
    public function countAll(): int
    {
        $result = (new self($this->getDatabaseConnection()))
            ->select('COUNT(*) as cnt')
            ->isNull('deleted_at')
            ->find();

        return (int) $result->cnt;
    }

    /**
     * Count pages by status.
     */
    public function countByStatus(string $status): int
    {
        $result = (new self($this->getDatabaseConnection()))
            ->select('COUNT(*) as cnt')
            ->eq('status', $status)
            ->isNull('deleted_at')
            ->find();

        return (int) $result->cnt;
    }

    /**
     * Recently updated pages.
     *
     * @return self[]
     */
    public function recentUpdated(int $limit = 5): array
    {
        return (new self($this->getDatabaseConnection()))
            ->isNull('deleted_at')
            ->order('updated_at DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Create a new page record.
     */
    public function createRecord(array $data): self
    {
        $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $record = new self($this->getDatabaseConnection());

        foreach ($data as $key => $value) {
            $record->$key = $value;
        }

        $record->created_at = $now;
        $record->updated_at = $now;
        $record->insert();

        return $record;
    }

    /**
     * Update allowed fields on this record.
     */
    public function updateRecord(array $data): void
    {
        $allowed = ['title', 'content', 'status', 'meta_title', 'meta_description'];

        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $this->$field = $data[$field];
            }
        }

        $this->updated_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->save();
    }

    /**
     * Soft delete this record.
     */
    public function softDelete(): void
    {
        $this->deleted_at = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->save();
    }
}
