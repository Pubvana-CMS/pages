<?php

declare(strict_types=1);

namespace Pubvana\Pages\Database\Migrations;

use Enlivenapp\Migrations\Services\Migration;

class CreatePagesTable extends Migration
{
    public function up(): void
    {
        $this->table('pages')
            ->addColumn('id', 'primary', [])
            ->addColumn('title', 'string', ['length' => 255])
            ->addColumn('slug', 'string', ['length' => 255])
            ->addColumn('content', 'longtext', ['nullable' => true])
            ->addColumn('status', 'enum', ['values' => ['draft', 'published'], 'default' => 'draft'])
            ->addColumn('meta_title', 'string', ['length' => 255, 'nullable' => true, 'default' => null])
            ->addColumn('meta_description', 'text', ['nullable' => true, 'default' => null])
            ->addColumn('is_system', 'integer', ['default' => 0])
            ->addColumn('created_by', 'integer', ['unsigned' => true])
            ->addColumn('created_at', 'datetime', ['nullable' => true, 'default' => null])
            ->addColumn('updated_at', 'datetime', ['nullable' => true, 'default' => null])
            ->addColumn('deleted_at', 'datetime', ['nullable' => true, 'default' => null])
            ->addIndex(['slug'], ['unique' => true])
            ->addIndex(['status'])
            ->addIndex(['created_by'])
            ->create();
    }

    public function down(): void
    {
        $this->table('pages')->drop();
    }
}
