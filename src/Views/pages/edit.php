<?php
/**
 * Edit page — admin form.
 *
 * @var string                      $pageTitle
 * @var \Pubvana\Pages\Models\Page $page
 */
?>

<div class="d-flex align-items-center justify-content-end mb-4">
    <div class="btn-list">
        <a href="/admin/pages/<?= (int) $page->id ?>/versions" class="btn btn-outline-secondary">Version History</a>
        <a href="/admin/pages" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<form method="POST" action="/admin/pages/<?= (int) $page->id ?>/update">
    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control"
                               value="<?= htmlspecialchars($page->title) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug <small class="text-secondary">(read-only)</small></label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($page->slug) ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="20"><?= htmlspecialchars($page->content ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">SEO</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="meta_title">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control"
                               value="<?= htmlspecialchars($page->meta_title ?? '') ?>">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control"
                                  rows="2"><?= htmlspecialchars($page->meta_description ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3 class="card-title">Publish</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="status">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="draft" <?= $page->status === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $page->status === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="purify_content" value="1" class="form-check-input" checked>
                            <span class="form-check-label">Sanitize HTML <i class="ti ti-info-circle ms-1 text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="When unchecked, content is saved as-is without removing potentially dangerous HTML such as scripts or event handlers."></i></span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Page</button>
                </div>
            </div>
        </div>
    </div>
</form>

<?= \Flight::media()->joditInit('#content') ?>
