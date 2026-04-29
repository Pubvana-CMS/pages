<?php
/**
 * Page version history — admin page.
 *
 * @var string                              $pageTitle
 * @var \Pubvana\Pages\Models\Page         $page
 * @var \Pubvana\Pages\Models\PageVersion[] $versions
 */
?>

<div class="d-flex align-items-center justify-content-end mb-4">
    <a href="/admin/pages/<?= (int) $page->id ?>/edit" class="btn btn-outline-secondary">Back to Edit</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($versions)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-secondary py-4">No versions found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($versions as $v): ?>
                        <tr>
                            <td>#<?= (int) $v->version_number ?></td>
                            <td><?= htmlspecialchars($v->title) ?></td>
                            <td>
                                <span class="badge bg-<?= $v->status === 'published' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($v->status) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($v->created_at) ?></td>
                            <td>
                                <form method="POST"
                                      action="/admin/pages/<?= (int) $page->id ?>/restore/<?= (int) $v->id ?>"
                                      onsubmit="return confirm('Restore this version? The current content will be saved as a new version first.')">
                                    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">
                                    <button class="btn btn-sm btn-outline-primary">Restore</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
