<?php
/**
 * Create page — admin form.
 *
 * @var string $pageTitle
 */
?>

<div class="d-flex align-items-center justify-content-end mb-4">
    <a href="/admin/pages" class="btn btn-outline-secondary">Back</a>
</div>

<form method="POST" action="/admin/pages/store">
    <input type="hidden" name="_csrf_token" value="<?= csrf_token() ?>">

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control"
                               placeholder="Auto-generated from title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="20"></textarea>
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
                        <input type="text" name="meta_title" id="meta_title" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="2"></textarea>
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
                            <option value="draft" selected>Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" name="purify_content" value="1" class="form-check-input" checked>
                            <span class="form-check-label">Sanitize HTML <i class="ti ti-info-circle ms-1 text-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="When unchecked, content is saved as-is without removing potentially dangerous HTML such as scripts or event handlers."></i></span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Create Page</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var titleEl = document.getElementById('title');
    var slugEl  = document.getElementById('slug');
    var slugEdited = false;

    slugEl.addEventListener('input', function () {
        slugEdited = this.value !== '';
    });

    titleEl.addEventListener('input', function () {
        if (slugEdited) return;
        slugEl.value = this.value.toLowerCase().trim()
            .replace(/&/g, 'and')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s]+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    });

});
</script>

<?= \Flight::media()->joditInit('#content') ?>
