<?php

declare(strict_types=1);

namespace Pubvana\Pages\Database\Migrations;

use Enlivenapp\Migrations\Services\Migration;

class CreatePageViewsTable extends Migration
{
    public function up(): void
    {
        $this->table('page_views')
            ->addColumn('id', 'primary', [])
            ->addColumn('page_id', 'integer', ['unsigned' => true])
            ->addColumn('referrer_domain', 'string', ['length' => 255, 'nullable' => true, 'default' => null])
            ->addColumn('viewed_at', 'datetime', [])
            ->addIndex(['page_id'])
            ->addIndex(['viewed_at'])
            ->addIndex(['referrer_domain'])
            ->create();
    }

    public function down(): void
    {
        $this->table('page_views')->drop();
    }
}
