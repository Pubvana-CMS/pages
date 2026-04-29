<?php
/**
 * Pages listing — admin page.
 *
 * @var string                        $pageTitle
 * @var \Pubvana\Pages\Models\Page[] $pages
 * @var int                           $total
 * @var int                           $page
 * @var int                           $perPage
 */
?>

<div class="d-flex align-items-center justify-content-end mb-4">
    <a href="/admin/pages/create" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> New Page
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th>System</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">No pages found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pages as $p): ?>
                        <tr>
                            <td>
                                <a href="/admin/pages/<?= (int) $p->id ?>/edit">
                                    <?= htmlspecialchars($p->title) ?>
                                </a>
                            </td>
                            <td><code><?= htmlspecialchars($p->slug) ?></code></td>
                            <td>
                                <span class="badge bg-<?= $p->status === 'published' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($p->status) ?>
                                </span>
                            </td>
                            <td>
                                <?= (int) $p->is_system ? '<span class="badge bg-info">System</span>' : '—' ?>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <a href="/admin/pages/<?= (int) $p->id ?>/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="/admin/pages/<?= (int) $p->id ?>/versions" class="btn btn-sm btn-outline-secondary">Versions</a>
                                    <?php if (!(int) $p->is_system): ?>
                                        <form method="POST" action="/admin/pages/<?= (int) $p->id ?>/delete"
                                              class="d-inline" onsubmit="return confirm('Delete this page?')">
                                            <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $totalPages = (int) ceil($total / $perPage); ?>
<?php if ($totalPages > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="/admin/pages?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>
