<?php

declare(strict_types=1);

namespace Pubvana\Pages\Models;

/**
 * @property int         $id
 * @property int         $page_id
 * @property string|null $referrer_domain
 * @property string      $viewed_at
 */
class PageView extends \flight\ActiveRecord
{
    public function __construct($pdo = null, array $config = [])
    {
        parent::__construct($pdo, 'page_views', $config);
    }

    /**
     * Record a page view.
     */
    public function record(int $pageId, ?string $referrerDomain = null): self
    {
        $view = new self($this->getDatabaseConnection());
        $view->page_id         = $pageId;
        $view->referrer_domain = $referrerDomain;
        $view->viewed_at       = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $view->insert();

        return $view;
    }

    /**
     * Count views for a page.
     */
    public function countForPage(int $pageId): int
    {
        $result = (new self($this->getDatabaseConnection()))
            ->select('COUNT(*) as cnt')
            ->eq('page_id', $pageId)
            ->find();

        return (int) $result->cnt;
    }
}
