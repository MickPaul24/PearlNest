<?php
/** @var string $baseUrl */
/** @var callable $imgUrl */
/** @var array $property */
/** @var array $typeOptions */
/** @var array $flash */
require __DIR__ . '/layout.php';
?>

<div class="admin-page-header">
    <div>
        <h2>Edit Property</h2>
        <p class="admin-page-sub"><?= htmlspecialchars($property['title']) ?></p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="<?= $baseUrl ?>/property/detail/<?= $property['id'] ?>" target="_blank" class="btn btn-outline"><i data-lucide="eye"></i> View Public</a>
        <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-outline"><i data-lucide="arrow-left"></i> Back</a>
    </div>
</div>

<form id="prop-edit-form" action="<?= $baseUrl ?>/admin/edit/<?= $property['id'] ?>" method="POST" enctype="multipart/form-data">
    <div class="form-two-col">

        <!-- LEFT -->
        <div class="form-col">
            <div class="admin-card">
                <div class="admin-card-header"><h3>Basic Information</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label>Property Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-input"
                               value="<?= htmlspecialchars($property['title']) ?>" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Property Type</label>
                            <select name="type" class="form-input">
                                <?php foreach ($typeOptions as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $property['type'] === $val ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-input">
                                <?php foreach (['available','rented','sold','under_review'] as $s): ?>
                                <option value="<?= $s ?>" <?= $property['status'] === $s ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$s)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-input" rows="5"><?= htmlspecialchars($property['description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Amenities <small class="text-muted">(comma-separated)</small></label>
                        <input type="text" name="amenities" class="form-input"
                               value="<?= htmlspecialchars(is_array($property['amenities']) ? implode(', ', $property['amenities']) : ($property['amenities'] ?? '')) ?>">
                    </div>
                    <div class="form-group">
                        <label>Badge</label>
                        <select name="badge" class="form-input">
                            <option value="">None</option>
                            <?php foreach (['VERIFIED','POPULAR','LAST AVAILABLE','NEW'] as $b): ?>
                            <option value="<?= $b ?>" <?= $property['badge'] === $b ? 'selected' : '' ?>><?= $b ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1" <?= $property['is_featured'] ? 'checked' : '' ?>>
                            <strong>Feature this property</strong> (shows on homepage)
                        </label>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            <div class="admin-card mt-3">
                <div class="admin-card-header"><h3>Current Images</h3></div>
                <div class="admin-card-body">
                    <?php if (empty($property['images'])): ?>
                    <p class="text-muted">No images uploaded yet.</p>
                    <?php else: ?>
                    <div class="current-images-grid">
                        <?php foreach ($property['images'] as $img): ?>
                        <div class="current-img-wrap">
                            <img src="<?= htmlspecialchars($imgUrl($img['image_path'])) ?>" alt="property image">
                            <?php if ($img['is_primary']): ?>
                            <span class="primary-badge">Primary</span>
                            <?php endif; ?>
                            <form action="<?= $baseUrl ?>/admin/deleteimage/<?= $img['id'] ?>" method="POST"
                                  onsubmit="return confirm('Remove this image?')">
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <button type="submit" class="delete-img-btn"><i data-lucide="x"></i></button>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($property['videos'])): ?>
            <div class="admin-card mt-3">
                <div class="admin-card-header"><h3>Current Video</h3></div>
                <div class="admin-card-body">
                    <?php foreach ($property['videos'] as $vid): ?>
                    <video controls style="width:100%;border-radius:8px;max-height:200px;">
                        <source src="<?= $baseUrl . '/' . htmlspecialchars($vid['video_path']) ?>" type="video/mp4">
                    </video>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT -->
        <div class="form-col">
            <div class="admin-card">
                <div class="admin-card-header"><h3>Location &amp; Pricing</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label>Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" class="form-input"
                               value="<?= htmlspecialchars($property['location']) ?>" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>District</label>
                            <select name="district" class="form-input">
                                <option value="">Select district</option>
                                <?php foreach (['Kampala Central','Nakawa','Makindye','Rubaga','Kawempe','Wakiso','Mukono','Entebbe'] as $d): ?>
                                <option value="<?= $d ?>" <?= $property['district'] === $d ? 'selected' : '' ?>><?= $d ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Street / Address</label>
                            <input type="text" name="address" class="form-input"
                                   value="<?= htmlspecialchars($property['address'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label>Price (UGX) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-input"
                                   value="<?= (float)$property['price'] ?>" min="0" required>
                        </div>
                        <div class="form-group">
                            <label>Price Period</label>
                            <select name="price_period" class="form-input">
                                <?php foreach (['month','night','year'] as $p): ?>
                                <option value="<?= $p ?>" <?= $property['price_period'] === $p ? 'selected' : '' ?>>Per <?= ucfirst($p) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row-3">
                        <div class="form-group">
                            <label>Bedrooms</label>
                            <input type="number" name="bedrooms" class="form-input" value="<?= $property['bedrooms'] ?>" min="1">
                        </div>
                        <div class="form-group">
                            <label>Bathrooms</label>
                            <input type="number" name="bathrooms" class="form-input" value="<?= $property['bathrooms'] ?>" min="1">
                        </div>
                        <div class="form-group">
                            <label>Area (m²)</label>
                            <input type="number" name="area_sqm" class="form-input" value="<?= $property['area_sqm'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-card mt-3">
                <div class="admin-card-header"><h3>Add More Images</h3></div>
                <div class="admin-card-body">
                    <div class="form-group">
                        <label><small class="text-muted">JPEG/PNG/WEBP, max 5 MB each</small></label>
                        <div class="file-upload-area" id="imageDropZone2">
                            <i data-lucide="upload-cloud" style="width:36px;height:36px;margin-bottom:8px;"></i>
                            <p>Drag &amp; drop or click to upload</p>
                            <input type="file" name="images[]" id="imageInput2" multiple accept="image/*" class="file-input-hidden">
                        </div>
                        <div id="imagePreview2" class="image-preview-grid"></div>
                    </div>
                    <div class="form-group">
                        <label>Replace Video <small class="text-muted">(optional)</small></label>
                        <input type="text" name="video_title" class="form-input mb-2"
                               placeholder="Video title" value="<?= htmlspecialchars($property['videos'][0]['title'] ?? '') ?>">
                        <input type="file" name="video" accept="video/*" class="form-input">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div style="display:flex;gap:12px;margin-top:16px;">
    <button type="submit" form="prop-edit-form" class="btn btn-primary"><i data-lucide="save"></i> Update Property</button>
    <a href="<?= $baseUrl ?>/admin/properties" class="btn btn-outline">Cancel</a>
    <form action="<?= $baseUrl ?>/admin/delete/<?= $property['id'] ?>" method="POST" style="margin-left:auto;"
          onsubmit="return confirm('Permanently delete this property? This cannot be undone.')">
        <button type="submit" class="btn btn-danger"><i data-lucide="trash-2"></i> Delete Property</button>
    </form>
</div>

<script>
const imageInput2  = document.getElementById('imageInput2');
const previewGrid2 = document.getElementById('imagePreview2');
const dropZone2    = document.getElementById('imageDropZone2');
dropZone2.addEventListener('click', () => imageInput2.click());
dropZone2.addEventListener('dragover', e => { e.preventDefault(); dropZone2.classList.add('drag-over'); });
dropZone2.addEventListener('dragleave', () => dropZone2.classList.remove('drag-over'));
dropZone2.addEventListener('drop', e => { e.preventDefault(); dropZone2.classList.remove('drag-over'); showPreviews2(e.dataTransfer.files); });
imageInput2.addEventListener('change', () => showPreviews2(imageInput2.files));
function showPreviews2(files) {
    previewGrid2.innerHTML = '';
    Array.from(files).forEach(file => {
        const r = new FileReader();
        r.onload = e => { const d = document.createElement('div'); d.className = 'preview-thumb'; d.innerHTML = `<img src="${e.target.result}" alt="">`; previewGrid2.appendChild(d); };
        r.readAsDataURL(file);
    });
}
</script>

<?php require __DIR__ . '/layout-end.php'; ?>
