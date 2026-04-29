<?php

declare(strict_types=1);

namespace Pubvana\Pages\Database\Migrations;

use Enlivenapp\Migrations\Services\Migration;

class CreatePageVersionsTable extends Migration
{
    public function up(): void
    {
        $this->table('page_versions')
            ->addColumn('id', 'primary', [])
            ->addColumn('page_id', 'integer', ['unsigned' => true])
            ->addColumn('title', 'string', ['length' => 255])
            ->addColumn('content', 'longtext', ['nullable' => true])
            ->addColumn('status', 'enum', ['values' => ['draft', 'published'], 'default' => 'draft'])
            ->addColumn('meta_title', 'string', ['length' => 255, 'nullable' => true, 'default' => null])
            ->addColumn('meta_description', 'text', ['nullable' => true, 'default' => null])
            ->addColumn('version_number', 'integer', ['unsigned' => true])
            ->addColumn('created_by', 'integer', ['unsigned' => true])
            ->addColumn('created_at', 'datetime', ['nullable' => true, 'default' => null])
            ->addIndex(['page_id'])
            ->addIndex(['page_id', 'version_number'])
            ->create();
    }

    public function down(): void
    {
        $this->table('page_versions')->drop();
    }
}
